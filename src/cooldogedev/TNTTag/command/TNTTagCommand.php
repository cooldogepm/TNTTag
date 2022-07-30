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

namespace cooldogedev\TNTTag\command;

use cooldogedev\TNTTag\command\subcommand\CreateSubCommand;
use cooldogedev\TNTTag\command\subcommand\DeleteSubCommand;
use cooldogedev\TNTTag\command\subcommand\JoinSubCommand;
use cooldogedev\TNTTag\command\subcommand\ListSubCommand;
use cooldogedev\TNTTag\command\subcommand\QuitSubCommand;
use cooldogedev\TNTTag\TNTTag;
use cooldogedev\TNTTag\utility\message\KnownMessages;
use cooldogedev\TNTTag\utility\message\LanguageManager;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;

final class TNTTagCommand extends BaseCommand
{
    protected TNTTag $plugin;

    public function __construct(TNTTag $plugin)
    {
        parent::__construct($plugin, LanguageManager::getMessage(KnownMessages::TOPIC_COMMAND, KnownMessages::COMMAND_NAME), LanguageManager::getMessage(KnownMessages::TOPIC_COMMAND, KnownMessages::COMMAND_DESCRIPTION), LanguageManager::getArray(KnownMessages::TOPIC_COMMAND, KnownMessages::COMMAND_ALIASES));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (count($args) === 0) {
            $this->sendUsage();
        }
    }

    protected function prepare(): void
    {
        // Administrative commands
        $this->registerSubCommand(new CreateSubCommand());
        $this->registerSubCommand(new DeleteSubCommand());
        $this->registerSubCommand(new ListSubCommand());

        $this->registerSubCommand(new JoinSubCommand());
        $this->registerSubCommand(new QuitSubCommand());
    }
}
