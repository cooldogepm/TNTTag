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

namespace cooldogedev\TNTTag\game\player;

use cooldogedev\TNTTag\constant\ItemConstants;
use cooldogedev\TNTTag\game\Game;
use cooldogedev\TNTTag\game\handler\EndHandler;
use cooldogedev\TNTTag\session\Session;
use cooldogedev\TNTTag\utility\message\KnownMessages;
use cooldogedev\TNTTag\utility\message\LanguageManager;
use cooldogedev\TNTTag\utility\message\TranslationKeys;
use pocketmine\item\VanillaItems;
use pocketmine\player\GameMode;
use pocketmine\world\Position;

final class PlayerManager
{
    protected array $sessions = [];
    protected array $queues = [];

    public function __construct(protected Game $game)
    {
        $this->sessions = [];
        $this->queues = [];
    }

    public function getSession(string $uuid): ?Session
    {
        return $this->sessions[$uuid] ?? null;
    }

    public function removeFromGame(?Session $session, bool $left = false, bool $spectate = false): bool
    {
        if (
            !$session ||
            !$session->inGame() ||
            $session->getGame()->getData()->getId() !== $this->game->getData()->getId()
        ) {
            return false;
        }
        $session->setTagged(false, false);
        $session->getPlayer()->getCursorInventory()->clearAll();
        $session->getPlayer()->getInventory()->clearAll();
        $session->getPlayer()->getArmorInventory()->clearAll();
        $session->getPlayer()->getOffHandInventory()->clearAll();
        $session->getPlayer()->getEffects()->clear();
        $session->getPlayer()->setHealth($session->getPlayer()->getMaxHealth());
        $session->getPlayer()->getHungerManager()->setFood($session->getPlayer()->getHungerManager()->getMaxFood());
        $session->getPlayer()->setMovementSpeed(0.10);

        if ($spectate) {
            $teleporterItem = VanillaItems::COMPASS()->setCustomName(LanguageManager::getMessage(KnownMessages::TOPIC_ITEMS, KnownMessages::ITEMS_TELEPORTER));
            $playAgainItem = VanillaItems::PAPER()->setCustomName(LanguageManager::getMessage(KnownMessages::TOPIC_ITEMS, KnownMessages::ITEMS_PLAY_AGAIN));
            $backToLobbyItem = VanillaItems::RED_BED()->setCustomName(LanguageManager::getMessage(KnownMessages::TOPIC_ITEMS, KnownMessages::ITEMS_BACK_TO_LOBBY));

            $teleporterItem->getNamedTag()->setInt(ItemConstants::ITEM_TYPE_IDENTIFIER, ItemConstants::ITEM_TELEPORTER);
            $playAgainItem->getNamedTag()->setInt(ItemConstants::ITEM_TYPE_IDENTIFIER, ItemConstants::ITEM_PLAY_AGAIN);
            $backToLobbyItem->getNamedTag()->setInt(ItemConstants::ITEM_TYPE_IDENTIFIER, ItemConstants::ITEM_BACK_TO_LOBBY);

            $session->getPlayer()->getInventory()->setItem(0, $teleporterItem);
            $session->getPlayer()->getInventory()->setItem(7, $playAgainItem);
            $session->getPlayer()->getInventory()->setItem(8, $backToLobbyItem);

            $session->setState(Session::PLAYER_STATE_SPECTATOR);
            $session->getPlayer()->setGamemode(GameMode::SPECTATOR());
            $session->getPlayer()->teleport(Position::fromObject($this->game->getWorld()->getSpawnLocation()->add(0.5, 20, 0.5), $this->game->getWorld()));
        } else {
            $session->getScoreboard()->reset();
            $session->setGame(null);
            $session->setState(Session::PLAYER_STATE_DEFAULT);
            unset($this->sessions[$session->getPlayer()->getUniqueId()->getBytes()]);
            if ($this->game->getPlugin()->getConfig()->get("behaviour")["queue-on-game-end"]) {
                $this->game->getPlugin()->getGameManager()->queueToGame([$session], null, true, true);
            } else {
                $session->getPlayer()->setGamemode($this->game->getPlugin()->getServer()->getGamemode());
                $session->getPlayer()->teleport(Position::fromObject($this->game->getPlugin()->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation()->add(0.5, 0, 0.5), $this->game->getPlugin()->getServer()->getWorldManager()->getDefaultWorld()));
            }
        }

        if ($left && !$this->game->getHandler() instanceof EndHandler) {
            $this->game->broadcastMessage(LanguageManager::getMessage(KnownMessages::TOPIC_PLAYER, KnownMessages::PLAYER_QUIT), [
                TranslationKeys::PLAYER => $session->getPlayer()->getDisplayName(),
                TranslationKeys::PLAYERS_COUNT => count($this->getSessions()),
                TranslationKeys::PLAYERS_MAX => $this->game->getData()->getMaxPlayers()
            ]);
        }

        return true;
    }

    /**
     * @param int|null $state
     * @return Session[]
     */
    public function getSessions(?int $state = Session::PLAYER_STATE_ALIVE): array
    {
        if ($state === null) {
            return $this->sessions;
        }

        $sessions = [];
        foreach ($this->sessions as $session) {
            if ($session->getState() !== $state) {
                continue;
            }
            $sessions[] = $session;
        }
        return $sessions;
    }

    public function clearQueue(): void
    {
        foreach ($this->getQueues() as $key => $session) {
            if ($session->getPlayer()->isConnected()) {
                $this->addToGame($session, true);
            }

            unset($this->queues[$key]);
        }
    }

    /**
     * @return Session[]
     */
    public function getQueues(): array
    {
        return $this->queues;
    }

    public function addToGame(Session $session, bool $fromQueue = false): bool
    {
        if (!$this->game->isFree($fromQueue) || $session->inGame()) {
            return false;
        }

        if ($this->game->isLoading()) {
            $this->queues[$session->getPlayer()->getUniqueId()->getBytes()] = $session;
            $session->setQueued(true);
            return true;
        }

        $session->getPlayer()->getInventory()->clearAll();
        $session->getPlayer()->getCursorInventory()->clearAll();
        $session->getPlayer()->getOffHandInventory()->clearAll();
        $session->getPlayer()->getArmorInventory()->clearAll();

        $quitItem = VanillaItems::RED_BED()->setCustomName(LanguageManager::getMessage(KnownMessages::TOPIC_ITEMS, KnownMessages::ITEMS_QUIT_GAME));
        $quitItem->getNamedTag()->setInt(ItemConstants::ITEM_TYPE_IDENTIFIER, ItemConstants::ITEM_QUIT_GAME);

        $session->getPlayer()->getInventory()->setItem(8, $quitItem);

        $session->getPlayer()->getEffects()->clear();
        $session->getPlayer()->setMovementSpeed(0.10);

        $session->getPlayer()->setHealth($session->getPlayer()->getMaxHealth());
        $session->getPlayer()->getHungerManager()->setFood($session->getPlayer()->getHungerManager()->getMaxFood());

        $session->getPlayer()->setGamemode(GameMode::ADVENTURE());

        $session->getScoreboard()->setActive(true);
        $session->getScoreboard()->setTitle(LanguageManager::getMessage(KnownMessages::TOPIC_SCOREBOARD, KnownMessages::SCOREBOARD_TITLE));

        $session->setState(Session::PLAYER_STATE_ALIVE);
        $session->setQueued(false);
        $session->setGame($this->game);

        $session->getPlayer()->teleport($this->game->getLobby()->getSafeSpawn());

        $this->sessions[$session->getPlayer()->getUniqueId()->getBytes()] = $session;
        $this->game->broadcastMessage(LanguageManager::getMessage(KnownMessages::TOPIC_PLAYER, KnownMessages::PLAYER_JOIN), [
            TranslationKeys::PLAYER => $session->getPlayer()->getDisplayName(),
            TranslationKeys::PLAYERS_COUNT => count($this->getSessions()),
            TranslationKeys::PLAYERS_MAX => $this->game->getData()->getMaxPlayers()
        ]);

        return true;
    }
}
