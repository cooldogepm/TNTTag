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

namespace cooldogedev\TNTTag\session\data;

use cooldogedev\TNTTag\game\Game;
use cooldogedev\TNTTag\session\Session;
use cooldogedev\TNTTag\utility\message\KnownMessages;
use cooldogedev\TNTTag\utility\message\LanguageManager;
use pocketmine\block\VanillaBlocks;

trait SessionData
{
    protected ?Game $game = null;
    protected bool $queued = false;
    protected bool $tagged = false;
    protected int $state = Session::PLAYER_STATE_DEFAULT;

    public function getState(): int
    {
        return $this->state;
    }

    public function setState(int $state): void
    {
        $this->state = $state;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): void
    {
        $this->game = $game;
    }

    public function isQueued(): bool
    {
        return $this->queued;
    }

    public function setQueued(bool $queued): void
    {
        $this->queued = $queued;
    }

    public function inGame(): bool
    {
        return $this->game !== null;
    }

    public function isTagged(): bool
    {
        return $this->tagged;
    }

    public function setTagged(bool $tagged, bool $updateInventory = true): void
    {
        $this->tagged = $tagged;

        if (!$updateInventory) {
            return;
        }

        $inv = $this->player->getInventory();
        if ($tagged) {
            $item = VanillaBlocks::TNT()->asItem();
            for ($i = 0; $i < $inv->getSize(); $i++) {
                $inv->setItem($i, $item);
            }
        } else {
            $inv->clearAll();
        }
    }

    public function getGoal(): string
    {
        return $this->tagged ? LanguageManager::getMessage(KnownMessages::TOPIC_GOALS, KnownMessages::GOALS_TAGGED) : LanguageManager::getMessage(KnownMessages::TOPIC_GOALS, KnownMessages::GOALS_RUNNER);
    }
}
