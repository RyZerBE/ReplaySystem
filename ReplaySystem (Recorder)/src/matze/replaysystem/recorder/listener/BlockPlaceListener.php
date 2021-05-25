<?php

namespace matze\replaysystem\recorder\listener;

use matze\replaysystem\recorder\action\types\BlockPlaceAction;
use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use function is_null;

class BlockPlaceListener implements Listener {

    /**
     * @param BlockPlaceEvent $event
     * @priority MONITOR
     */
    public function onPlace(BlockPlaceEvent $event): void {
        if($event->isCancelled()) return;
        $block = $event->getBlock();
        $blockReplaced = $event->getBlockReplaced();

        $replay = ReplayManager::getInstance()->getReplayByLevel($block->getLevel());
        if(is_null($replay)) return;

        $action = new BlockPlaceAction();
        $action->x = $block->getFloorX();
        $action->y = $block->getFloorY();
        $action->z = $block->getFloorZ();
        $action->blockPlacedId = $block->getId();
        $action->blockPlacedDamage = $block->getDamage();
        $action->blockReplacedId = $blockReplaced->getId();
        $action->blockReplacedDamage = $blockReplaced->getDamage();
        $replay->addAction($action);
    }
}