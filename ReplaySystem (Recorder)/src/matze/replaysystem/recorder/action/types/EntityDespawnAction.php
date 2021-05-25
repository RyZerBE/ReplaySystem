<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use function json_encode;

class EntityDespawnAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntityDespawnAction";
    }

    /** @var int */
    public $entityId;

    /**
     * @return string
     */
    public function encode(): string{
        return json_encode([
            "EntityId" => $this->entityId
        ]);
    }
}