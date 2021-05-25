<?php

namespace matze\replaysystem\player\entity;

use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageEvent;

class ReplayHuman extends Human {

    /** @var int  */
    public $drag = 0;
    /** @var int  */
    public $gravity = 0;

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool{
        if($this->isInvisible()) $this->despawnFromAll();
        if(!$this->forceMovementUpdate) return true;
        return parent::onUpdate($currentTick);
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void{}
}