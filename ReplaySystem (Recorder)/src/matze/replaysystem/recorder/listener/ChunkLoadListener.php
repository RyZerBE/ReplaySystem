<?php

namespace matze\replaysystem\recorder\listener;

use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\event\level\ChunkLoadEvent;
use pocketmine\event\Listener;
use function is_null;

class ChunkLoadListener implements Listener {

    /**
     * @param ChunkLoadEvent $event
     * @priority MONITOR
     */
    public function onChunkLoad(ChunkLoadEvent $event): void {
        $chunk = $event->getChunk();
        $level = $event->getLevel();
        $replay = ReplayManager::getInstance()->getReplayByLevel($level);
        if(is_null($replay)) return;
        $replay->addChunk($chunk);
    }
}