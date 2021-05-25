<?php

namespace matze\replaysystem\player\entity;

use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\EntityDamageEvent;

class ReplayItemEntity extends ItemEntity {

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