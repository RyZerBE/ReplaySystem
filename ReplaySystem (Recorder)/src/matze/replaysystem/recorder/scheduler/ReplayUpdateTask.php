<?php

namespace matze\replaysystem\recorder\scheduler;

use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\scheduler\Task;

class ReplayUpdateTask extends Task {

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick): void {
        foreach(ReplayManager::getInstance()->getReplays() as $replay) {
            if(!$replay->isRunning()) continue;
            $replay->onUpdate();
        }
    }
}