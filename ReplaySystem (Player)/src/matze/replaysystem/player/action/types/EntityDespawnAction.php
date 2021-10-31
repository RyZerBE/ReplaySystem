<?php

namespace matze\replaysystem\player\action\types;

use matze\replaysystem\player\action\Action;
use matze\replaysystem\player\action\ActionIds;
use matze\replaysystem\player\replay\Replay;
use function is_null;

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
     * @param array $data
     * @return $this
     */
    public function decode(array $data): Action{
        $action = new EntityDespawnAction();
        $action->entityId = $data["EntityId"];
        return $action;
    }

    /**
     * @param Replay $replay
     * @param Action|EntityDespawnAction $action
     * @param int $playType
     */
    public function handle(Replay $replay, Action $action, int $playType): void{
        $entity = $replay->findEntity($action->entityId);
        if(is_null($entity)) return;
        switch($playType) {
            case Replay::PLAY_TYPE_FORWARD: {
                $entity->setInvisible(true);
                $entity->despawnFromAll();
                break;
            }
            case Replay::PLAY_TYPE_BACKWARDS: {
                $entity->setInvisible(false);
                $entity->spawnToAll();
                break;
            }
        }
    }
}