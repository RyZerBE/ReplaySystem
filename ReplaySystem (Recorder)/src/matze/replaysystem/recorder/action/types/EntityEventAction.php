<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use function json_encode;

class EntityEventAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntityEventAction";
    }

    /** @var int */
    public $entityId;
    /** @var int */
    public $event;
    /** @var int */
    public $data = 0;

    /**
     * @return string
     */
    public function encode(): string{
        return json_encode([
            "EntityId" => $this->entityId,
            "Event" => $this->event,
            "Data" => $this->data
        ]);
    }
}