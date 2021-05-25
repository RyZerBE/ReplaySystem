<?php

namespace matze\replaysystem\player\action\types;

use matze\replaysystem\player\action\Action;
use matze\replaysystem\player\replay\Replay;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use function is_null;

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
     * @param array $data
     * @return $this
     */
    public function decode(array $data): Action{
        $action = new EntityAnimationAction();
        $action->entityId = $data["EntityId"];
        $action->action = $data["Action"];
        return $action;
    }

    /**
     * @param Replay $replay
     * @param Action|EntityAnimationAction $action
     * @param int $playType
     */
    public function handle(Replay $replay, Action $action, int $playType): void{
        $entity = $replay->findEntity($action->entityId);
        if(is_null($entity)) return;
        $pk = new AnimatePacket();
        $pk->entityRuntimeId = $entity->getId();
        $pk->action = $action->action;
        $entity->getLevel()->broadcastPacketToViewers($entity, $pk);
    }
}