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

abstract class ModalForm extends Form {

    /** @var string */
    private $content = "";


    public function __construct(string $title = "", ?Form $previousForm = null){
        parent::__construct(self::TYPE_MODAL ,$title, $previousForm);
        $this->data["content"] = $this->content;
        $this->data["button1"] = "";
        $this->data["button2"] = "";
    }

    public function processData(&$data) : void {
        if(!is_bool($data)) {
            throw new FormValidationException("Expected a boolean response, got " . gettype($data));
        }
    }
    
    /**
     * @return string
     */
    public function getContent() : string {
        return $this->data["content"];
    }

    /**
     * @param string $content
     */
    public function setContent(string $content) : void {
        $this->data["content"] = $content;
    }

    /**
     * @param string $text
     * @deprecated
     * @see ModalForm::setTrueButtonLabel()
     */
    public function setButton1(string $text) : void {
        $this->setTrueButtonLabel($text);
    }

    /**
     * Used to set the label for the button that will return a true value when clicked.
     *
     * @param string $text
     */
    public function setTrueButtonLabel(string $text) : void{
        $this->data["button1"] = $text;
    }

    /**
     * @return string
     * @deprecated
     * @see ModalForm::getTrueButtonLabel()
     */
    public function getButton1() : string {
        return $this->getTrueButtonLabel();
    }

    /**
     * Returns the label for the button that returns a response of true.
     *
     * @return string
     */
    public function getTrueButtonLabel() : string{
        return $this->data["button1"];
    }

    /**
     * @param string $text
     * @deprecated
     * @see ModalForm::setFalseButtonLabel()
     */
    public function setButton2(string $text) : void {
        $this->setFalseButtonLabel($text);
    }

    /**
     * Used to set the label for the button that will return a false value when clicked.
     *
     * @param string $text
     */
    public function setFalseButtonLabel(string $text) : void{
        $this->data["button2"] = $text;
    }

    /**
     * @return string
     * @deprecated
     * @see ModalForm::getFalseButtonLabel()
     */
    public function getButton2() : string {
        return $this->getFalseButtonLabel();
    }

    /**
     * Returns the label for the button that returns a response of false.
     *
     * @return string
     */
    public function getFalseButtonLabel() : string{
        return $this->data["button2"];
    }
}
