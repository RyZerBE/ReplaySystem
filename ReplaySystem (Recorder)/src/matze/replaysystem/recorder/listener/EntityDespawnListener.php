<?php

namespace matze\replaysystem\recorder\listener;

use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\Listener;
use function is_null;

class EntityDespawnListener implements Listener {

    /**
     * @param EntityDespawnEvent $event
     */
    public function onDespawn(EntityDespawnEvent $event): void {
        $entity = $event->getEntity();
        $level = $entity->getLevel();
        $replay = ReplayManager::getInstance()->getReplayByLevel($level);
        if(is_null($replay)) return;

        $replay->removeEntity($entity);
    }
}