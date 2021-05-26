<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use function json_encode;

class BlockEventAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "BlockEventAction";
    }

    /** @var int */
    public $x;
    /** @var int */
    public $y;
    /** @var int */
    public $z;
    /** @var int */
    public $eventType;
    /** @var int */
    public $eventData;

    /**
     * @return string
     */
    public function encode(): string{
        return json_encode([
            "X" => $this->x,
            "Y" => $this->y,
            "Z" => $this->z,
            "EventType" => $this->eventType,
            "EventData" => $this->eventData
        ]);
    }
}