<?php

namespace matze\replaysystem\player\listener;

use matze\replaysystem\player\replay\ReplayManager;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;
use function is_null;

class EntityExplodeListener implements Listener {

    /**
     * @param EntityExplodeEvent $event
     */
    public function onEntityExplode(EntityExplodeEvent $event): void {
        $level = $event->getEntity()->getLevelNonNull();
        $replay = ReplayManager::getInstance()->getReplayByLevel($level);
        if(is_null($replay)) return;
        $event->setCancelled();
    }
}