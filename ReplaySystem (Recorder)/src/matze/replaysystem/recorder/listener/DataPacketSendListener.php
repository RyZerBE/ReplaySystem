<?php

namespace matze\replaysystem\recorder\listener;

use Generator;
use matze\replaysystem\recorder\action\types\BlockEventAction;
use matze\replaysystem\recorder\action\types\EntityAnimationAction;
use matze\replaysystem\recorder\action\types\EntityEventAction;
use matze\replaysystem\recorder\action\types\EntityMoveAction;
use matze\replaysystem\recorder\action\types\EntityUpdateAction;
use matze\replaysystem\recorder\action\types\LevelEventAction;
use matze\replaysystem\recorder\action\types\LevelSoundEventAction;
use matze\replaysystem\recorder\replay\Replay;
use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\Player;
use pocketmine\Server;
use function is_null;

class DataPacketSendListener implements Listener {

    /**
     * @param DataPacketSendEvent $event
     */
    public function onDataPacketSend(DataPacketSendEvent $event): void {
        $level = $event->getPlayer()->getLevel();
        $packet = $event->getPacket();
        $replay = ReplayManager::getInstance()->getReplayByLevel($level);
        if(is_null($replay)) return;

        if($packet instanceof BatchPacket) {
            $packet->decode();
            foreach($this->getPackets($packet) as $buffer) {
                $pk = PacketPool::getPacket($buffer);
                if(!$pk->canBeBatched()) continue;
                $pk->decode();
                $this->handlePacket($pk, $replay);
            }
            return;
        }
        $this->handlePacket($packet, $replay);
    }

    /**
     * @param DataPacket $packet
     * @param Replay $replay
     */
    private function handlePacket(DataPacket $packet, Replay $replay): void {
        if($packet instanceof MoveActorAbsolutePacket || $packet instanceof MovePlayerPacket) {
            $entity = Server::getInstance()->findEntity($packet->entityRuntimeId);
            if(is_null($entity) || $entity instanceof Player) return;

            $action = new EntityMoveAction();
            $action->entityID = $packet->entityRuntimeId;
            $action->x = $packet->position->x;
            $action->y = $packet->position->y;
            $action->z = $packet->position->z;
            if($packet instanceof MoveActorAbsolutePacket) {
                $action->yaw = $packet->zRot;
                $action->pitch = $packet->xRot;
            } else {
                $action->yaw = $packet->yaw;
                $action->pitch = $packet->pitch;
            }
            $replay->addAction($action);
            return;
        }

        if($packet instanceof AnimatePacket) {
            $action = new EntityAnimationAction();
            $action->entityId = $packet->entityRuntimeId;
            $action->action = $packet->action;
            $replay->addAction($action);
            return;
        }

        if($packet instanceof ActorEventPacket) {
            $action = new EntityEventAction();
            $action->entityId = $packet->entityRuntimeId;
            $action->event = $packet->event;
            $action->data = $packet->data;
            $replay->addAction($action);
            return;
        }

        if($packet instanceof LevelEventPacket) {
            $action = new LevelEventAction();
            $action->evid = $packet->evid;
            $action->data = $packet->data;
            $action->x = $packet->position->x;
            $action->y = $packet->position->y;
            $action->z = $packet->position->z;
            $replay->addAction($action);
            return;
        }

        if($packet instanceof LevelSoundEventPacket) {
            $action = new LevelSoundEventAction();
            $action->sound = $packet->sound;
            $action->x = $packet->position->x;
            $action->y = $packet->position->y;
            $action->z = $packet->position->z;
            $action->extraData = $packet->extraData;
            $action->entityType = $packet->entityType;
            $action->isBabyMob = $packet->isBabyMob;
            $action->disableRelativeVolume = $packet->disableRelativeVolume;
            $replay->addAction($action);
            return;
        }

        if($packet instanceof SetActorDataPacket) {
            $entity = Server::getInstance()->findEntity($packet->entityRuntimeId);
            if(is_null($entity)) return;

            $action = new EntityUpdateAction();
            $action->entityId = $packet->entityRuntimeId;
            $action->nametag = $entity->getNameTag();
            $action->scoretag = $entity->getScoreTag();
            $action->scale = $entity->getScale();
            $action->nametagVisible = $entity->isNameTagVisible();
            $action->nametagAlwaysVisible = $entity->isNameTagAlwaysVisible();
            $replay->addAction($action);
            return;
        }

        if($packet instanceof BlockEventPacket) {
            $action = new BlockEventAction();
            $action->x = $packet->x;
            $action->y = $packet->y;
            $action->z = $packet->z;
            $action->eventData = $packet->eventData;
            $action->eventType = $packet->eventType;
            $replay->addAction($action);
            return;
        }
    }

    /**
     * @param BatchPacket $packet
     * @return Generator
     */
    private function getPackets(BatchPacket $packet) {
        $stream = new NetworkBinaryStream($packet->payload);
        while(!$stream->feof()){
            yield $stream->getString();
        }
    }
}