<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use matze\replaysystem\recorder\action\ActionIds;
use function json_encode;

class EntityAnimationAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntityAnimationAction";
    }

    public function getId(): int{
        return ActionIds::ENTITY_ANIMATION_ACTION;
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