<?php

namespace matze\replaysystem\recorder\listener;

use matze\replaysystem\recorder\action\types\BlockBreakAction;
use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\block\Block;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use function is_null;

class EntitySpawnListener implements Listener {

    /**
     * @param EntitySpawnEvent $event
     * @priority MONITOR
     */
    public function onEntitySpawn(EntitySpawnEvent $event): void {
        $entity = $event->getEntity();
        $level = $entity->getLevel();
        $replay = ReplayManager::getInstance()->getReplayByLevel($level);
        if(is_null($replay)) return;
        $replay->addEntity($entity);

        if($entity instanceof PrimedTNT && $entity->getLevel()->getBlock($entity)->getId() === Block::TNT) {
            $action = new BlockBreakAction();
            $action->x = $entity->getFloorX();
            $action->y = $entity->getFloorY();
            $action->z = $entity->getFloorZ();
            $action->blockId = Block::TNT;
            $action->blockDamage = 0;
            $replay->addAction($action);
        }
    }
}