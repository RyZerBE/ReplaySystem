<?php

namespace matze\replaysystem\player\scheduler;

use matze\replaysystem\player\replay\ReplayManager;
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