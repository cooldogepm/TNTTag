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

use pocketmine\form\FormValidationException;
use pocketmine\player\Player;

class CustomForm extends FormInterface
{
    protected array $labelMap;

    public function __construct(string $title = "")
    {
        $this->data["type"] = "custom_form";
        $this->data["title"] = $title;
        $this->data["content"] = [];
        $this->labelMap = [];
    }

    public function addLabel(string $text, ?string $label = null): void
    {
        $this->addContent(["type" => "label", "text" => $text]);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    protected function addContent(array $content): void
    {
        $this->data["content"][] = $content;
    }

    public function addToggle(string $text, bool $default = null, ?string $label = null): void
    {
        $content = ["type" => "toggle", "text" => $text];
        if ($default !== null) {
            $content["default"] = $default;
        }
        $this->addContent($content);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    public function addSlider(string $text, int $min, int $max, int $step = -1, int $default = -1, ?string $label = null): void
    {
        $content = ["type" => "slider", "text" => $text, "min" => $min, "max" => $max];
        if ($step !== -1) {
            $content["step"] = $step;
        }
        if ($default !== -1) {
            $content["default"] = $default;
        }
        $this->addContent($content);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    public function addStepSlider(string $text, array $steps, int $defaultIndex = -1, ?string $label = null): void
    {
        $content = ["type" => "step_slider", "text" => $text, "steps" => $steps];
        if ($defaultIndex !== -1) {
            $content["default"] = $defaultIndex;
        }
        $this->addContent($content);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    public function addDropdown(string $text, array $options, int $default = null, ?string $label = null): void
    {
        $this->addContent(["type" => "dropdown", "text" => $text, "options" => $options, "default" => $default]);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    public function addInput(string $text, string $placeholder = "", string $default = null, ?string $label = null): void
    {
        $this->addContent(["type" => "input", "text" => $text, "placeholder" => $placeholder, "default" => $default]);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    public function handleResponse(Player $player, $data): void
    {
        if ($data !== null && !is_array($data)) {
            throw new FormValidationException("Expected an array response, got " . gettype($data));
        }
        if (is_array($data)) {
            $new = [];
            foreach ($data as $i => $v) {
                $new[$this->labelMap[$i]] = $v;
            }
            $data = $new;
        }
        parent::handleResponse($player, $data);
    }
}
