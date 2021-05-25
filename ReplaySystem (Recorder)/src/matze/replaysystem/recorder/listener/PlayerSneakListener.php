<?php

namespace matze\replaysystem\recorder\listener;

use matze\replaysystem\recorder\action\types\EntitySneakAction;
use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerToggleSneakEvent;
use function is_null;

class PlayerSneakListener implements Listener {

    /**
     * @param PlayerToggleSneakEvent $event
     * @priority MONITOR
     */
    public function onSneak(PlayerToggleSneakEvent $event): void {
        if($event->isCancelled()) return;
        $player = $event->getPlayer();
        $level = $player->getLevel();
        $replay = ReplayManager::getInstance()->getReplayByLevel($level);
        if(is_null($replay)) return;

        $action = new EntitySneakAction();
        $action->entityId = $player->getId();
        $action->sneaking = $event->isSneaking();
        $replay->addAction($action);
    }
}