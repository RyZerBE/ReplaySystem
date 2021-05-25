<?php

namespace matze\replaysystem\recorder\listener;

use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use function is_null;

class EntitySpawnListener implements Listener {

    /**
     * @param EntitySpawnEvent $event
     * @priority MONITOR
     */
    public function onEntitySpawn(EntitySpawnEvent $event): void {
        $entity = $event->getEntity();
        $level = $entity->getLevel();
        $replay = ReplayManager::getInstance()->getReplayByLevel($level);
        if(is_null($replay)) return;
        $replay->addEntity($entity);
    }
}