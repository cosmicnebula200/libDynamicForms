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
     * @param string      $content        // Visible text label to tell the player what this item is for.
     * @param string|null $identifier   // Internal identifier used in form response to idententify the specific content.
     *
     * @deprecated
     * @see CustomForm::addNonInputContentArea()
     */
    public function addLabel(string $content, ?string $identifier = null) : void {
        $this->addNonInputContentArea($content, $identifier);
    }

    /**
     * Adds a text only area to the form, intended to add instructions or context.
     * Client response will always return this section as null.
     *
     * @param string      $content        // Visible text to that the player can read.
     * @param string|null $identifier   // Internal identifier used in form response to idententify the specific content.
     */
    public function addNonInputContentArea(string $content, ?string $identifier = null) : void{
        $this->addContent(["type" => "label", "text" => $content]);
        $this->labelMap[] = $identifier ?? count($this->labelMap);
        $this->validationMethods[] = static function($v) : bool {
            return $v === null;
        };
    }

    /***
     * @param string      $label        // Visible text label to tell the player what this item is for.
     * @param string|null $identifier   // Internal identifier used in form response to idententify the specific content.
     * @param bool|null   $default      // The default option that will be pre-selected for the player.
     */
    public function addToggle(string $label, ?string $identifier = null, bool $default = null) : void {
        $content = ["type" => "toggle", "text" => $label];
        if($default !== null) {
            $content["default"] = $default;
        }
        $this->addContent($content);
        $this->labelMap[] = $identifier ?? count($this->labelMap);
        $this->validationMethods[] = static function($v) : bool {
            return is_bool($v);
        };
    }

    /**
     * @param string      $label        // Visible text label to tell the player what this item is for.
     * @param int         $min          // The lowest selectable value.
     * @param int         $max          // The higest selectable value.
     * @param string|null $identifier   // Internal identifier used in form response to idententify the specific content.
     * @param int         $step         // Total difference between each step from $min to $max.
     * @param int         $default      // Default starting value that will be pre-selected for the player.
     */
    public function addSlider(string $label, int $min, int $max, ?string $identifier = null, int $step = -1, int $default = -1) : void {
        $content = ["type" => "slider", "text" => $label, "min" => $min, "max" => $max];
        if($step !== -1) {
            $content["step"] = $step;
        }
        if($default !== -1) {
            $content["default"] = $default;
        }
        $this->addContent($content);
        $this->labelMap[] = $identifier ?? count($this->labelMap);
        $this->validationMethods[] = static function($v) use($min, $max) : bool {
            return (is_float($v) || is_int($v)) && $v >= $min && $v <= $max;
        };
    }

    /**
     * @param string      $label        // Visible text label to tell the player what this item is for.
     * @param string|null $identifier   // Internal identifier used in form response to idententify the specific content.
     * @param array       $steps        // Array of valid selections.  Array indexes must be integers.
     * @param int         $defaultIndex // Default step value that will be pre-selected for the player.
     */
    public function addStepSlider(string $label, ?string $identifier = null, array $steps, int $defaultIndex = -1) : void {
        $content = ["type" => "step_slider", "text" => $label, "steps" => $steps];
        if($defaultIndex !== -1) {
            $content["default"] = $defaultIndex;
        }
        $this->addContent($content);
        $this->labelMap[] = $identifier ?? count($this->labelMap);
        $this->validationMethods[] = static function($v) use($steps) : bool {
            return is_int($v) && isset($steps[$v]);
        };
    }

    /**
     * @param string      $label        // Visible text label to tell the player what this item is for.
     * @param string|null $identifier   // Internal identifier used in form response to idententify the specific content.
     * @param array       $options      // Array of valid selections for the player to choose from.  Indexes must be integers.
     * @param int|null    $default      // Default index of the pre-selected value for the player.
     */
    public function addDropdown(string $label, ?string $identifier = null, array $options, int $default = null) : void {
        $this->addContent(["type" => "dropdown", "text" => $label, "options" => $options, "default" => $default]);
        $this->labelMap[] = $identifier ?? count($this->labelMap);
        $this->validationMethods[] = static function($v) use($options) : bool {
            return is_int($v) && isset($options[$v]);
        };
    }

    /**
     * @param string      $label        // Visible text label to tell the player what this item is for.
     * @param string|null $identifier   // Internal identifier used in form response to idententify the specific content.
     * @param string      $placeholder  // Text that will be displayed when the input box is empty.  Typically used for context and examples.  This value will not be returned if the input box is empty when submitted.
     * @param string|null $default      // Default return value.
     */
    public function addInput(string $label, ?string $identifier = null, string $placeholder = "", string $default = null) : void {
        $this->addContent(["type" => "input", "text" => $label, "placeholder" => $placeholder, "default" => $default]);
        $this->labelMap[] = $identifier ?? count($this->labelMap);
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
