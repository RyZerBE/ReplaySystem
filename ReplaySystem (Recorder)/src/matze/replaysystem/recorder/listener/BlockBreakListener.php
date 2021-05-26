<?php

namespace matze\replaysystem\recorder\listener;

use matze\replaysystem\recorder\action\types\BlockBreakAction;
use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use function is_null;

class BlockBreakListener implements Listener {

    /**
     * @param BlockBreakEvent $event
     * @priority MONITOR
     */
    public function onBreak(BlockBreakEvent $event): void {
        if($event->isCancelled()) return;
        $originBlock = $event->getBlock();

        $replay = ReplayManager::getInstance()->getReplayByLevel($originBlock->getLevel());
        if(is_null($replay)) return;

        foreach($originBlock->getAffectedBlocks() as $block) {
            $action = new BlockBreakAction();
            $action->x = $block->x;
            $action->y = $block->y;
            $action->z = $block->z;
            $action->blockId = $block->getId();
            $action->blockDamage = $block->getDamage();
            $replay->addAction($action);
        }
    }
}