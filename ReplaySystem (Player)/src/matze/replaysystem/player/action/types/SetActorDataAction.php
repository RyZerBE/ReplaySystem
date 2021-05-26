<?php

namespace matze\replaysystem\player\action\types;

use matze\replaysystem\player\action\Action;
use matze\replaysystem\player\replay\Replay;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use function is_null;
use function unserialize;

class SetActorDataAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "SetActorDataAction";
    }

    /** @var int */
    public $entityId;
    /** @var mixed */
    public $metadata;

    /**
     * @param array $data
     * @return $this
     */
    public function decode(array $data): Action{
        $action = new SetActorDataAction();
        $action->entityId = $data["EntityId"];
        $action->metadata = unserialize($data["Metadata"]);
        return $action;
    }

    /**
     * @param Replay $replay
     * @param Action|SetActorDataAction $action
     * @param int $playType
     */
    public function handle(Replay $replay, Action $action, int $playType): void{
        $entity = $replay->findEntity($action->entityId);
        if(is_null($entity)) return;

        $pk = new SetActorDataPacket();
        $pk->entityRuntimeId = $entity->getId();
        $pk->metadata = $action->metadata;
        $replay->getLevel()->broadcastPacketToViewers($entity, $pk);
    }
}