<?php

namespace matze\replaysystem\player\entity;

use pocketmine\entity\projectile\Egg;
use pocketmine\event\entity\EntityDamageEvent;

class ReplayEgg extends Egg {

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

    public function attack(EntityDamageEvent $source): void{}
    public function move(float $dx, float $dy, float $dz): void{}
}