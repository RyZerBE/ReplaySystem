<?php

namespace matze\replaysystem\recorder\listener;

use matze\replaysystem\recorder\action\types\EntityDespawnAction;
use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use function is_null;

class EntityLevelChangeListener implements Listener {

    /**
     * @param EntityLevelChangeEvent $event
     * @priority MONITOR
     */
    public function onLevelChange(EntityLevelChangeEvent $event): void {
        if($event->isCancelled()) return;
        $originLevel = $event->getOrigin();
        $targetLevel = $event->getTarget();
        $entity = $event->getEntity();

        $replay = ReplayManager::getInstance()->getReplayByLevel($originLevel);
        if(!is_null($replay)) {
            $action = new EntityDespawnAction();
            $action->entityId = $entity->getId();
            $replay->addAction($action);
        }

        $replay = ReplayManager::getInstance()->getReplayByLevel($targetLevel);
        if(!is_null($replay)) $replay->addEntity($entity);
    }
}