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

namespace cooldogedev\TNTTag\utility\form;

use pocketmine\form\Form;
use pocketmine\player\Player;

abstract class FormInterface implements Form
{
    protected array $data = [];
    protected ?FormInterface $previous = null;
    /**
     * @var callable|null
     */
    protected $callback = null;

    public function setTitle(string $title): void
    {
        $this->data["title"] = $title;
    }

    public function getTitle(): string
    {
        return $this->data["title"];
    }

    public function getPrevious(): ?FormInterface
    {
        return $this->previous;
    }

    public function setPrevious(?FormInterface $form): void
    {
        $this->previous = $form;
    }

    public function getCallback(): ?callable
    {
        return $this->callback;
    }

    public function setCallback(?callable $callback): void
    {
        $this->callback = $callback;
    }

    public function handleResponse(Player $player, $data): void
    {
        $this->onResponse($player, $data);
    }

    public function onResponse(Player $player, $data)
    {
        $callback = $this->callback;
        if ($callback) {
            $callback($player, $data);
        }
    }

    public function sendPrevious(Player $player): void
    {
        if ($this->previous !== null) {
            $player->sendForm($this->previous);
        }
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}
