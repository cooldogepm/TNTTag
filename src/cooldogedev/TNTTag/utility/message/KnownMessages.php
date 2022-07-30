<?php

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

    public const TOPIC_CHAT = "chat";
    public const CHAT_ALIVE = "alive";
    public const CHAT_SPECTATOR = "spectator";

    public const TOPIC_SCOREBOARD = "scoreboard";
    public const SCOREBOARD_TITLE = "title";
    public const SCOREBOARD_BODY = "body";
}
