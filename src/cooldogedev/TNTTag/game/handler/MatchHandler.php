<?php

/**
 *
 * Copyright (c) 2022 cooldogedev
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @auto-license
 */

declare(strict_types=1);

namespace cooldogedev\TNTTag\game\handler;

use cooldogedev\TNTTag\game\Game;
use cooldogedev\TNTTag\session\Session;
use cooldogedev\TNTTag\utility\message\KnownMessages;
use cooldogedev\TNTTag\utility\message\LanguageManager;
use cooldogedev\TNTTag\utility\message\TranslationKeys;
use pocketmine\world\Position;

final class MatchHandler extends IHandler
{
    protected int $timeLeft;

    public function __construct(Game $game)
    {
        parent::__construct($game);

        $this->timeLeft = $this->getGame()->getData()->getRoundDuration();
        $this->startRound();
    }

    public function startRound(): void
    {
        $this->game->incrementRound();

        $sessions = $this->game->getPlayerManager()->getSessions();
        shuffle($sessions);
        $runnersCount = $this->getTaggedCount(count($sessions));

        if (count($sessions) <= 6 && !$this->game->isDeathMatch()) {
            $this->game->broadcastTitle(LanguageManager::getMessage(KnownMessages::TOPIC_DEATHMATCH, KnownMessages::DEATHMATCH_TITLE), LanguageManager::getMessage(KnownMessages::TOPIC_DEATHMATCH, KnownMessages::DEATHMATCH_SUBTITLE));
            $this->game->broadcastMessage(LanguageManager::getMessage(KnownMessages::TOPIC_DEATHMATCH, KnownMessages::DEATHMATCH_MESSAGE));
            $this->game->setDeathMatch(true);
        }

        foreach ($sessions as $session) {
            $session->getPlayer()->setMovementSpeed($this->game->getPlugin()->getConfig()->get("behaviour")["speed"]);

            if ($runnersCount > count($sessions)) {
                $runnersCount = $this->getTaggedCount(count($sessions)) - count($this->game->getTagged());
            }

            if ($runnersCount > 0) {
                $session->setTagged(true);
                $session->getPlayer()->sendMessage(LanguageManager::getMessage(KnownMessages::TOPIC_TAGGED, KnownMessages::TAGGED_START));
                $this->game->broadcastMessage(LanguageManager::translate(LanguageManager::getMessage(KnownMessages::TOPIC_TAGGED, KnownMessages::TAGGED_TAGGED), [
                    TranslationKeys::PLAYER => $session->getPlayer()->getDisplayName()
                ]));
                $runnersCount--;
            }

            $session->getPlayer()->teleport(Position::fromObject($this->game->getWorld()->getSpawnLocation()->add(0.5, 0, 0.5), $this->game->getWorld()));
        }
    }

    public function getTaggedCount(int $playersCount): int
    {
        if ($playersCount >= 20) {
            return 8;
        } elseif ($playersCount > 10) {
            return 4;
        } elseif ($playersCount >= 8) {
            return 2;
        }
        return 1;
    }

    public function handleTicking(): void
    {
        if ($this->timeLeft > 0) {
            $this->timeLeft--;
        } else {
            $this->handleRoundEnd();
        }
    }

    protected function handleRoundEnd(): void
    {
        foreach ($this->game->getPlayerManager()->getSessions() as $session) {
            $session->getPlayer()->setMovementSpeed(0.10);
        }

        foreach ($this->game->getTagged() as $session) {
            $this->game->explodePlayer($session);
        }

        $this->game->setHandler(new GraceHandler($this->game));
    }

    public function handleScoreboardUpdates(): void
    {
        if ($this->timeLeft < 1) {
            return;
        }

        foreach ($this->game->getPlayerManager()->getSessions(null) as $session) {
            if (!$session->getPlayer()->isOnline()) {
                continue;
            }

            $translations = [
                TranslationKeys::MAP => $this->game->getData()->getName(),
                TranslationKeys::PLAYERS_COUNT => count($this->game->getPlayerManager()->getSessions()),
                TranslationKeys::ROUND => $this->game->getRound(),
                TranslationKeys::GOAL => $session->getState() === Session::PLAYER_STATE_ALIVE ? $session->getGoal() : LanguageManager::getMessage(KnownMessages::TOPIC_GOALS, KnownMessages::GOALS_NONE),
                TranslationKeys::EXPLOSION => $this->timeLeft,
            ];

            $lines = array_map(fn($line) => $line !== "" ? LanguageManager::translate($line, $translations) : $line, $this->getScoreboardBody());

            $session->getScoreboard()->setLines($lines);
            $session->getScoreboard()->onUpdate();
        }
    }

    protected function getScoreboardBody(): array
    {
        $scoreboardData = LanguageManager::getArray(KnownMessages::TOPIC_SCOREBOARD, KnownMessages::SCOREBOARD_BODY);

        return $scoreboardData["match"];
    }
}
