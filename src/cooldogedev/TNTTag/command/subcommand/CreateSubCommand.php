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

namespace cooldogedev\TNTTag\command\subcommand;

use cooldogedev\TNTTag\async\directory\AsyncDirectoryClone;
use cooldogedev\TNTTag\game\data\GameData;
use cooldogedev\TNTTag\permission\PermissionsList;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
Use pocketmine\Server;

final class CreateSubCommand extends BaseSubCommand
{
    public function __construct()
    {
        parent::__construct("create", "Create a new arena", ["c"]);
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $name = $args[GameData::GAME_DATA_NAME];
        $world = $args[GameData::GAME_DATA_WORLD];
        $countdown = $args[GameData::GAME_DATA_COUNTDOWN];
        $maxPlayers = $args[GameData::GAME_DATA_MAX_PLAYERS];
        $minPlayers = $args[GameData::GAME_DATA_MIN_PLAYERS];
        $roundDuration = $args[GameData::GAME_DATA_ROUND_DURATION];
        $graceDuration = $args[GameData::GAME_DATA_GRACE_DURATION];
        $endDuration = $args[GameData::GAME_DATA_END_DURATION];
        $lobby = $args[GameData::GAME_DATA_LOBBY] ?? null;

        if ($this->getOwningPlugin()->getGameManager()->isMapExists($name)) {
            $sender->sendMessage(TextFormat::RED . "There's already existing map with the name " . TextFormat::WHITE . $name);
            return;
        }

        if (!Server::getInstance()->getWorldManager()->isWorldGenerated($world) || $lobby !== null && !$this->getOwningPlugin()->getServer()->getWorldManager()->isWorldGenerated($lobby)) {
            $sender->sendMessage(TextFormat::RED . "There's no existing worlds with the name " . TextFormat::WHITE . $world);
            return;
        }

        $data = [
            GameData::GAME_DATA_NAME => $name,
            GameData::GAME_DATA_COUNTDOWN => (int)$countdown,
            GameData::GAME_DATA_MIN_PLAYERS => (int)$minPlayers,
            GameData::GAME_DATA_MAX_PLAYERS => (int)$maxPlayers,
            GameData::GAME_DATA_ROUND_DURATION => (int)$roundDuration,
            GameData::GAME_DATA_GRACE_DURATION => (int)$graceDuration,
            GameData::GAME_DATA_END_DURATION => (int)$endDuration,
        ];

        $dataPath = $this->getOwningPlugin()->getDataFolder() . "maps" . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
        @mkdir($dataPath);

        file_put_contents($dataPath . "data.json", json_encode($data));

        $this->getOwningPlugin()->getGameManager()->addMap($name, $data);

        $directories = [];

        $directories[$this->getOwningPlugin()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . $world] = $dataPath . DIRECTORY_SEPARATOR . GameData::GAME_DATA_WORLD;

        if ($lobby !== null && strtolower($lobby) !== strtolower($world)) {
            $directories[$this->getOwningPlugin()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . $lobby] = $dataPath . DIRECTORY_SEPARATOR . GameData::GAME_DATA_LOBBY;
        }

        Server::getInstance()->getAsyncPool()->submitTask(new AsyncDirectoryClone($directories));

        $sender->sendMessage(TextFormat::GREEN . "Successfully created a map with the name " . TextFormat::WHITE . $name);
    }

    protected function prepare(): void
    {
        $this->setPermission(PermissionsList::TNTTAG_SUBCOMMAND_CREATE);

        $this->registerArgument(0, new RawStringArgument(GameData::GAME_DATA_NAME));
        $this->registerArgument(1, new RawStringArgument(GameData::GAME_DATA_WORLD));
        $this->registerArgument(2, new RawStringArgument(GameData::GAME_DATA_COUNTDOWN));
        $this->registerArgument(3, new RawStringArgument(GameData::GAME_DATA_MIN_PLAYERS));
        $this->registerArgument(4, new RawStringArgument(GameData::GAME_DATA_MAX_PLAYERS));
        $this->registerArgument(5, new RawStringArgument(GameData::GAME_DATA_ROUND_DURATION));
        $this->registerArgument(6, new RawStringArgument(GameData::GAME_DATA_GRACE_DURATION));
        $this->registerArgument(7, new RawStringArgument(GameData::GAME_DATA_END_DURATION));
        $this->registerArgument(8, new RawStringArgument(GameData::GAME_DATA_LOBBY, true));
    }
}
