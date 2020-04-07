<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types = 1);

namespace BreathTakinglyBinary\libDynamicForms;

use pocketmine\form\FormValidationException;

abstract class CustomForm extends Form {

    private $labelMap = [];
    private $validationMethods = [];


    public function __construct(string $title = "", ?Form $previousForm = null){
        parent::__construct(self::TYPE_CUSTOM, $title, $previousForm);
        $this->data["content"] = [];
    }

    public function processData(&$data) : void {
        if($data !== null && !is_array($data)) {
            throw new FormValidationException("Expected an array response, got " . gettype($data));
        }
        if(is_array($data)) {
            if(count($data) !== count($this->validationMethods)) {
                throw new FormValidationException("Expected an array response with the size " . count($this->validationMethods) . ", got " . count($data));
            }
            $new = [];
            foreach($data as $i => $v){
                $validationMethod = $this->validationMethods[$i] ?? null;
                if($validationMethod === null) {
                    throw new FormValidationException("Invalid element " . $i);
                }
                if(!$validationMethod($v)) {
                    throw new FormValidationException("Invalid type given for element " . $this->labelMap[$i]);
                }
                $new[$this->labelMap[$i]] = $v;
            }
            $data = $new;
        }
    }

    /**
     * @param string $text
     * @param string|null $label
     */
    public function addLabel(string $text, ?string $label = null) : void {
        $this->addContent(["type" => "label", "text" => $text]);
        $this->labelMap[] = $label ?? count($this->labelMap);
        $this->validationMethods[] = static function($v) : bool {
            return $v === null;
        };
    }

    /***
     * @param string      $text
     * @param string|null $label
     * @param bool|null   $default
     */
    public function addToggle(string $text, ?string $label = null, bool $default = null) : void {
        $content = ["type" => "toggle", "text" => $text];
        if($default !== null) {
            $content["default"] = $default;
        }
        $this->addContent($content);
        $this->labelMap[] = $label ?? count($this->labelMap);
        $this->validationMethods[] = static function($v) : bool {
            return is_bool($v);
        };
    }

    /**
     * @param string      $text
     * @param int         $min
     * @param int         $max
     * @param string|null $label
     * @param int         $step
     * @param int         $default
     */
    public function addSlider(string $text, int $min, int $max, ?string $label = null, int $step = -1, int $default = -1) : void {
        $content = ["type" => "slider", "text" => $text, "min" => $min, "max" => $max];
        if($step !== -1) {
            $content["step"] = $step;
        }
        if($default !== -1) {
            $content["default"] = $default;
        }
        $this->addContent($content);
        $this->labelMap[] = $label ?? count($this->labelMap);
        $this->validationMethods[] = static function($v) use($min, $max) : bool {
            return (is_float($v) || is_int($v)) && $v >= $min && $v <= $max;
        };
    }

    /**
     * @param string      $text
     * @param string|null $label
     * @param array       $steps
     * @param int         $defaultIndex
     */
    public function addStepSlider(string $text, ?string $label = null, array $steps, int $defaultIndex = -1) : void {
        $content = ["type" => "step_slider", "text" => $text, "steps" => $steps];
        if($defaultIndex !== -1) {
            $content["default"] = $defaultIndex;
        }
        $this->addContent($content);
        $this->labelMap[] = $label ?? count($this->labelMap);
        $this->validationMethods[] = static function($v) use($steps) : bool {
            return is_int($v) && isset($steps[$v]);
        };
    }

    /**
     * @param string      $text
     * @param string|null $label
     * @param array       $options
     * @param int|null    $default
     */
    public function addDropdown(string $text, ?string $label = null, array $options, int $default = null) : void {
        $this->addContent(["type" => "dropdown", "text" => $text, "options" => $options, "default" => $default]);
        $this->labelMap[] = $label ?? count($this->labelMap);
        $this->validationMethods[] = static function($v) use($options) : bool {
            return is_int($v) && isset($options[$v]);
        };
    }

    /**
     * @param string      $text
     * @param string|null $label
     * @param string      $placeholder
     * @param string|null $default
     */
    public function addInput(string $text, ?string $label = null, string $placeholder = "", string $default = null) : void {
        $this->addContent(["type" => "input", "text" => $text, "placeholder" => $placeholder, "default" => $default]);
        $this->labelMap[] = $label ?? count($this->labelMap);
        $this->validationMethods[] = static function($v) : bool {
            return is_string($v);
        };
    }

    /**
     * @param array $content
     */
    private function addContent(array $content) : void {
        $this->data["content"][] = $content;
    }

}
