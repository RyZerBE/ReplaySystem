<?php

namespace matze\replaysystem\recorder\listener;

use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\Player;
use function is_null;

class PlayerGameModeChangeListener implements Listener {

    /**
     * @param PlayerGameModeChangeEvent $event
     * @priority MONITOR
     */
    public function onGameModeChange(PlayerGameModeChangeEvent $event): void {
        if($event->isCancelled()) return;
        $player = $event->getPlayer();

        $replay = ReplayManager::getInstance()->getReplayByLevel($player->getLevel());
        if(is_null($replay)) return;

        if($event->getNewGamemode() === Player::SPECTATOR) {
            $replay->removeEntity($player);
            return;
        }
        $replay->addEntity($player);
    }
}