<?php

/**
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

namespace cooldogedev\TNTTag\session;

use cooldogedev\libSQL\context\ClosureContext;
use cooldogedev\TNTTag\constant\DatabaseConstants;
use cooldogedev\TNTTag\game\Game;
use cooldogedev\TNTTag\query\QueryManager;
use cooldogedev\TNTTag\session\data\CacheData;
use cooldogedev\TNTTag\session\data\SessionData;
use cooldogedev\TNTTag\session\scoreboard\Scoreboard;
use cooldogedev\TNTTag\TNTTag;
use pocketmine\player\Player;

final class Session
{
    public const PLAYER_STATE_DEFAULT = -1;
    public const PLAYER_STATE_ALIVE = 0;
    public const PLAYER_STATE_SPECTATOR = 1;

    use CacheData, SessionData;

    protected TNTTag $plugin;
    protected Player $player;
    protected Scoreboard $scoreboard;
    protected bool $loading;

    public function __construct(TNTTag $plugin, Player $player)
    {
        $this->plugin = $plugin;
        $this->player = $player;
        $this->scoreboard = new Scoreboard($this->player);
        $this->game = null;
        $this->loading = true;
        $this->queued = false;

        $this->plugin->getConnectionPool()->submit(QueryManager::getPlayerRetrieveQuery($player->getXuid()), DatabaseConstants::TABLE_TNTTAG_PLAYERS, ClosureContext::create(
            function (?array $result): void {
                if ($result !== null) {
                    $this->wins = $result["wins"];
                    $this->winStreak = $result["win_streak"];
                    $this->losses = $result["losses"];
                }

                $this->unregistered = $result === null;
                $this->loading = false;
            }
        ));
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getPlugin(): TNTTag
    {
        return $this->plugin;
    }

    public function isLoading(): bool
    {
        return $this->loading;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function save(): void
    {
        if (!$this->unregistered && $this->updated) {
            $this->plugin->getConnectionPool()->submit(QueryManager::getPlayerUpdateQuery($this->player->getXuid(), $this->toArray()), DatabaseConstants::TABLE_TNTTAG_PLAYERS);
        }

        if ($this->isUnregistered()) {
            $this->plugin->getConnectionPool()->submit(QueryManager::getPlayerCreateQuery($this->player->getXuid(), $this->player->getName(), $this->toArray()), DatabaseConstants::TABLE_TNTTAG_PLAYERS);
        }
    }

    public function getScoreboard(): Scoreboard
    {
        return $this->scoreboard;
    }
}
