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

namespace cooldogedev\TNTTag\utility\form;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class NormalForm extends FormInterface
{
    public const IMAGE_TYPE_URL = 0;
    public const IMAGE_TYPE_PATH = 1;

    public function __construct(string $title = "", string $content = "")
    {
        $this->data["title"] = $title;
        $this->data["content"] = $content;
        $this->data["type"] = "form";
        $this->data["buttons"] = [];
    }

    public function setContent(string $content): void
    {
        $this->data["content"] = $content;
    }

    public function getContent(): string
    {
        return $this->data["content"];
    }

    public function addButton(string $text, int $imageType = -1, string $imagePath = ""): void
    {
        $content = ["text" => $text];
        if ($imageType !== -1) {
            $content["image"]["type"] = $imageType === 0 ? "path" : "url";
            $content["image"]["data"] = $imagePath;
        }
        $this->data["buttons"][] = $content;
    }

    final public function handleResponse(Player $player, $data): void
    {
        parent::handleResponse($player, TextFormat::clean($this->getButton($data) ?? ""));
    }

    public function getButton(?int $index): ?string
    {
        return isset($this->data["buttons"][$index]) ? TextFormat::clean($this->data["buttons"][$index]["text"]) : null;
    }
}
