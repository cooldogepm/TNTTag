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

namespace cooldogedev\TNTTag\utility\message;

final class KnownMessages
{
    public const TOPIC_PLAYER = "player";
    public const PLAYER_JOIN = "join";
    public const PLAYER_QUIT = "quit";

    public const TOPIC_LOSE = "lose";
    public const LOSE_TITLE = "title";
    public const LOSE_SUBTITLE = "subtitle";
    public const LOSE_MESSAGE = "message";

    public const TOPIC_WIN = "win";
    public const WIN_TITLE = "title";
    public const WIN_SUBTITLE = "subtitle";
    public const WIN_MESSAGE = "message";

    public const TOPIC_END = "end";
    public const END_TITLE = "title";
    public const END_SUBTITLE = "subtitle";
    public const END_MESSAGE = "message";

    public const TOPIC_TAGGED = "tagged";
    public const TAGGED_START = "start";
    public const TAGGED_TAGGED = "tagged";
    public const TAGGED_EXPLODE = "explode";

    public const TOPIC_COUNTDOWN = "countdown";
    public const COUNTDOWN_START = "start";
    public const COUNTDOWN_STOP = "stop";
    public const COUNTDOWN_DECREMENT = "decrement";

    public const TOPIC_START = "start";
    public const START_TITLE = "title";
    public const START_SUBTITLE = "subtitle";
    public const START_MESSAGE = "message";

    public const TOPIC_DEATHMATCH = "deathmatch";
    public const DEATHMATCH_TITLE = "title";
    public const DEATHMATCH_SUBTITLE = "subtitle";
    public const DEATHMATCH_MESSAGE = "message";

    public const TOPIC_QUEUE_ERROR = "queue-error";
    public const QUEUE_ERROR_MESSAGE = "message";
    public const QUEUE_ERROR_KICK = "kick";

    public const TOPIC_GOALS = "goals";
    public const GOALS_RUNNER = "runner";
    public const GOALS_TAGGED = "tagged";
    public const GOALS_NONE = "none";

    public const TOPIC_COMMAND = "command";
    public const COMMAND_NAME = "name";
    public const COMMAND_ALIASES = "aliases";
    public const COMMAND_DESCRIPTION = "description";

    public const TOPIC_ITEMS = "items";
    public const ITEMS_QUIT_GAME = "quit-game";
    public const ITEMS_TELEPORTER = "teleporter";
    public const ITEMS_PLAY_AGAIN = "play-again";
    public const ITEMS_BACK_TO_LOBBY = "back-to-lobby";

    public const TOPIC_SCOREBOARD = "scoreboard";
    public const SCOREBOARD_TITLE = "title";
    public const SCOREBOARD_BODY = "body";
}
