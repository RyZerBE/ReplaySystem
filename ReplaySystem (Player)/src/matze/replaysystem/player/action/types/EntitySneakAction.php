<?php

namespace matze\replaysystem\player\action\types;

use matze\replaysystem\player\action\Action;
use matze\replaysystem\player\action\ActionIds;
use matze\replaysystem\player\replay\Replay;
use function is_null;

class EntitySneakAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntitySneakAction";
    }

    public function getId(): int{
        return ActionIds::ENTITY_SNEAK_ACTION;
    }

    /** @var int */
    public $entityId;
    /** @var bool */
    public $sneaking;

    /**
     * @param array $data
     * @return $this
     */
    public function decode(array $data): Action{
        $action = new EntitySneakAction();
        $action->entityId = $data["EntityId"];
        $action->sneaking = $data["Sneaking"];
        return $action;
    }

    /**
     * @param Replay $replay
     * @param Action|EntitySneakAction $action
     * @param int $playType
     */
    public function handle(Replay $replay, Action $action, int $playType): void{
        $entity = $replay->findEntity($action->entityId);
        if(is_null($entity)) return;
        switch($playType) {
            case Replay::PLAY_TYPE_FORWARD: {
                $entity->setSneaking($action->sneaking);
                break;
            }
            case Replay::PLAY_TYPE_BACKWARDS: {
                $entity->setSneaking(!$action->sneaking);
                break;
            }
        }
    }
}