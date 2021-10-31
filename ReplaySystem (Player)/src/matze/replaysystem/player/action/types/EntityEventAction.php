<?php

namespace matze\replaysystem\player\action\types;

use matze\replaysystem\player\action\Action;
use matze\replaysystem\player\action\ActionIds;
use matze\replaysystem\player\replay\Replay;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use function is_null;

class EntityEventAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntityEventAction";
    }

    public function getId(): int{
        return ActionIds::ENTITY_EVENT_ACTION;
    }

    /** @var int */
    public $entityId;
    /** @var int */
    public $event;
    /** @var int */
    public $data = 0;

    /**
     * @param array $data
     * @return $this
     */
    public function decode(array $data): Action{
        $action = new EntityEventAction();
        $action->entityId = $data["EntityId"];
        $action->event = $data["Event"];
        $action->data = $data["Data"];
        return $action;
    }

    /**
     * @param Replay $replay
     * @param Action|EntityEventAction $action
     * @param int $playType
     */
    public function handle(Replay $replay, Action $action, int $playType): void{
        $entity = $replay->findEntity($action->entityId);
        if(is_null($entity)) return;
        $pk = new ActorEventPacket();
        $pk->entityRuntimeId = $entity->getId();
        $pk->event = $action->event;
        $pk->data = $action->data;
        $entity->getLevel()->broadcastPacketToViewers($entity, $pk);
    }
}