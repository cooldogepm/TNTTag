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

namespace cooldogedev\TNTTag\game\data;

use cooldogedev\TNTTag\game\Game;

final class GameData
{
    public const GAME_DATA_NAME = "name";
    public const GAME_DATA_WORLD = "world";
    public const GAME_DATA_LOBBY = "lobby";
    public const GAME_DATA_COUNTDOWN = "countdown";
    public const GAME_DATA_MIN_PLAYERS = "min_players";
    public const GAME_DATA_MAX_PLAYERS = "max_players";
    public const GAME_DATA_ROUND_DURATION = "round_duration";
    public const GAME_DATA_GRACE_DURATION = "grace_duration";
    public const GAME_DATA_END_DURATION = "end_duration";

    public function __construct(protected int $id, protected string $name, protected int $countdown, protected int $minPlayers, protected int $maxPlayers, protected int $roundDuration, protected int $endDuration, protected int $graceDuration)
    {
    }

    public function getGraceDuration(): int
    {
        return $this->graceDuration;
    }

    public function getLobby(): string
    {
        return Game::GAME_LOBBY_IDENTIFIER . $this->id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEndDuration(): int
    {
        return $this->endDuration;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMaxPlayers(): int
    {
        return $this->maxPlayers;
    }

    public function getMinPlayers(): int
    {
        return $this->minPlayers;
    }

    public function getCountdown(): int
    {
        return $this->countdown;
    }

    public function getRoundDuration(): int
    {
        return $this->roundDuration;
    }

    public function getWorld(): string
    {
        return Game::GAME_WORLD_IDENTIFIER . $this->id;
    }
}
