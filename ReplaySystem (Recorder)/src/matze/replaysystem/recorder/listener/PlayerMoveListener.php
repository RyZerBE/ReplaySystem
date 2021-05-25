<?php

namespace matze\replaysystem\recorder\listener;

use matze\replaysystem\recorder\action\types\EntityMoveAction;
use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use function is_null;

class PlayerMoveListener implements Listener {

    /**
     * @param PlayerMoveEvent $event
     * @priority MONITOR
     */
    public function onMove(PlayerMoveEvent $event): void {
        if($event->isCancelled()) return;
        $player = $event->getPlayer();
        $to = $event->getTo();
        $level = $event->getPlayer()->getLevel();
        $replay = ReplayManager::getInstance()->getReplayByLevel($level);
        if(is_null($replay)) return;

        $action = new EntityMoveAction();
        $action->entityID = $player->getId();
        $action->x = $to->x;
        $action->y = $to->y;
        $action->z = $to->z;
        $action->yaw = $to->yaw;
        $action->pitch = $to->pitch;
        $replay->addAction($action);
    }
}