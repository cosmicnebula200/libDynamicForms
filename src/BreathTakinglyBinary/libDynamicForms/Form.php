<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types = 1);

namespace BreathTakinglyBinary\libDynamicForms;

use pocketmine\form\Form as IForm;
use pocketmine\Player;

abstract class Form implements IForm{

    /** @var array */
    protected $data = [];

    public function __construct(string $title = ""){
        $this->data["title"] = $title;
    }


    /**
     * @param string $title
     */
    public function setTitle(string $title) : void {
        $this->data["title"] = $title;
    }

    /**
     * @return string
     */
    public function getTitle() : string {
        return (isset($this->data["title"]) and is_string($this->data["title"])) ? (string) $this->data["title"] : "";
    }

    public function handleResponse(Player $player, $data) : void {
        $this->processData($data);
        if($data === null) {
            $this->onClose($player);
            return;
        }
        $this->onResponse($player, $data);
    }

    public function processData(&$data) : void {
    }

    public function jsonSerialize(){
        return $this->data;
    }


    /**
     * Children classes should implement this method to properly
     * deal with non-null player responses.
     *
     * @param Player $player
     * @param        $data
     */
    public abstract function onResponse(Player $player, $data) : void;

    /**
     * This method is called when a player closes the form without sending an response.
     *
     * @param Player $player
     */
    public function onClose(Player $player) : void {

    }
}
