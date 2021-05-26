<?php

namespace matze\replaysystem\recorder\listener;

use matze\replaysystem\recorder\action\types\BlockBreakAction;
use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\block\Block;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\event\Listener;
use function is_null;

class EntityExplodeListener implements Listener {

    /**
     * @param EntityExplodeEvent $event
     * @priority MONITOR
     */
    public function onExplode(EntityExplodeEvent $event): void {
        if($event->isCancelled()) return;

        $replay = ReplayManager::getInstance()->getReplayByLevel($event->getEntity()->getLevel());
        if(is_null($replay)) return;

        foreach($event->getBlockList() as $block) {
            $action = new BlockBreakAction();
            $action->x = $block->x;
            $action->y = $block->y;
            $action->z = $block->z;
            $action->blockId = $block->getId();
            $action->blockDamage = $block->getDamage();
            $replay->addAction($action);
        }
    }

    /**
     * @param ExplosionPrimeEvent $event
     * @priority MONITOR
     */
    public function onExplosionPrime(ExplosionPrimeEvent $event): void {
        if($event->isCancelled()) return;
        $entity = $event->getEntity();

        $replay = ReplayManager::getInstance()->getReplayByLevel($entity->getLevel());
        if(is_null($replay)) return;

        $action = new BlockBreakAction();
        $action->x = $entity->getFloorX();
        $action->y = $entity->getFloorY();
        $action->z = $entity->getFloorZ();
        $action->blockId = Block::TNT;
        $action->blockDamage = 0;
        $replay->addAction($action);
    }
}