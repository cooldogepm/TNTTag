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
use cooldogedev\TNTTag\utility\message\KnownMessages;
use cooldogedev\TNTTag\utility\message\LanguageManager;
use cooldogedev\TNTTag\utility\message\TranslationKeys;

/**
 * This handler is responsible for handling the end phase of the game.
 */
final class EndHandler extends IHandler
{
    protected int $timeLeft;

    public function __construct(Game $game)
    {
        parent::__construct($game);

        $this->timeLeft = $this->getGame()->getData()->getEndDuration();
    }

    public function handleTicking(): void
    {

        if ($this->timeLeft > 0) {
            $this->timeLeft--;
        } else {
            $this->getGame()->startDestruction();
        }
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
                TranslationKeys::END => $this->timeLeft,
                TranslationKeys::WINNER => $this->game->getWinner() ? $this->game->getWinner()->getName() : "N/A",
                TranslationKeys::PLAYERS_COUNT => 1, // Meaningless, but looks nice in the scoreboard.
                TranslationKeys::TOTAL_ROUNDS => $this->game->getRound(),
            ];

            $lines = array_map(fn($line) => $line !== "" ? LanguageManager::translate($line, $translations) : $line, $this->getScoreboardBody());

            $session->getScoreboard()->setLines($lines);
            $session->getScoreboard()->onUpdate();
        }
    }

    protected function getScoreboardBody(): array
    {
        $scoreboardData = LanguageManager::getArray(KnownMessages::TOPIC_SCOREBOARD, KnownMessages::SCOREBOARD_BODY);

        return $scoreboardData["end"];
    }
}
