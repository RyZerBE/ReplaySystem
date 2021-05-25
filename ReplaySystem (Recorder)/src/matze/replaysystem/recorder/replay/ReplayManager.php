<?php

namespace matze\replaysystem\recorder\replay;

use matze\replaysystem\recorder\utils\InstantiableTrait;
use pocketmine\level\Level;
use function is_null;

class ReplayManager {
    use InstantiableTrait;

    /** @var array  */
    private $replays = [];

    /**
     * @return Replay[]
     */
    public function getReplays(): array{
        return $this->replays;
    }

    /**
     * @param Replay $replay
     */
    public function addReplay(Replay $replay): void {
        $this->replays[$replay->getId()] = $replay;
    }

    /**
     * @param Replay $replay
     */
    public function removeReplay(Replay $replay): void {
        if(is_null($this->getReplay($replay->getId()))) return;
        unset($this->replays[$replay->getId()]);
    }

    /**
     * @param string $id
     * @return Replay|null
     */
    public function getReplay(string $id): ?Replay {
        return $this->replays[$id] ?? null;
    }

    /**
     * @param Level $level
     * @param bool $onlyRunning
     * @return Replay|null
     */
    public function getReplayByLevel(Level $level, bool $onlyRunning = true): ?Replay {
        foreach($this->getReplays() as $replay) {
            if($replay->getLevel()->getId() == $level->getId()){
                if($onlyRunning && !$replay->isRunning()) return null;
                return $replay;
            }
        }
        return null;
    }
}