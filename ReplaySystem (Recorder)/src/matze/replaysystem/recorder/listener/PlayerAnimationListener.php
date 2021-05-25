<?php

namespace matze\replaysystem\recorder\listener;

use matze\replaysystem\recorder\action\types\EntityAnimationAction;
use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerAnimationEvent;
use function is_null;

class PlayerAnimationListener implements Listener {

    /**
     * @param PlayerAnimationEvent $event
     * @priority MONITOR
     */
    public function onAnimation(PlayerAnimationEvent $event): void {
        if($event->isCancelled()) return;
        $player = $event->getPlayer();

        $replay = ReplayManager::getInstance()->getReplayByLevel($player->getLevel());
        if(is_null($replay)) return;

        $action = new EntityAnimationAction();
        $action->entityId = $player->getId();
        $action->action = $event->getAnimationType();
        $replay->addAction($action);
    }
}