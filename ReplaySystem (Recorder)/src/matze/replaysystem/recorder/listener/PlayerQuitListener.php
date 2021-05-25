<?php

namespace matze\replaysystem\recorder\listener;

use matze\replaysystem\recorder\action\types\EntityDespawnAction;
use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use function is_null;

class PlayerQuitListener implements Listener {

    /**
     * @param PlayerQuitEvent $event
     * @priority MONITOR
     */
    public function onQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $level = $player->getLevel();
        $replay = ReplayManager::getInstance()->getReplayByLevel($level);
        if(is_null($replay)) return;

        $action = new EntityDespawnAction();
        $action->entityId = $player->getId();
        $replay->addAction($action);
    }
}