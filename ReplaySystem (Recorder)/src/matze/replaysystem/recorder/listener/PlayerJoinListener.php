<?php

namespace matze\replaysystem\recorder\listener;

use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use function is_null;

class PlayerJoinListener implements Listener {

    /**
     * @param PlayerJoinEvent $event
     * @priority MONITOR
     */
    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $level = $player->getLevel();
        $replay = ReplayManager::getInstance()->getReplayByLevel($level);
        if(is_null($replay)) return;

        $replay->addEntity($player);
    }
}