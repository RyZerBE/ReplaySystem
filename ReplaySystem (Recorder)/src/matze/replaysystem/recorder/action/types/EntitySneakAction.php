<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use function json_encode;

class EntitySneakAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntitySneakAction";
    }

    /** @var int */
    public $entityId;
    /** @var bool */
    public $sneaking;

    /**
     * @return string
     */
    public function encode(): string{
        return json_encode([
            "EntityId" => $this->entityId,
            "Sneaking" => $this->sneaking
        ]);
    }
}