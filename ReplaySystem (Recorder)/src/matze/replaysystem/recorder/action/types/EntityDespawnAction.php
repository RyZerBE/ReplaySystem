<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use matze\replaysystem\recorder\action\ActionIds;
use function json_encode;

class EntityDespawnAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntityDespawnAction";
    }

    public function getId(): int{
        return ActionIds::ENTITY_DESPAWN_ACTION;
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

    /**
     * @param array $data
     * @return Action|EntityDespawnAction
     */
    public static function decode(array $data): Action{
        $action = new EntityDespawnAction();
        $action->entityId = $data["EntityId"];
        return $action;
    }
}