<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use function json_encode;

class EntityAnimationAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntityAnimationAction";
    }

    /** @var int */
    public $entityId;
    /** @var int */
    public $action;

    /**
     * @return string
     */
    public function encode(): string{
        return json_encode([
            "EntityId" => $this->entityId,
            "Action" => $this->action
        ]);
    }
}