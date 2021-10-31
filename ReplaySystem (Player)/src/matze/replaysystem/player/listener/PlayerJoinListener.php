<?php

namespace matze\replaysystem\player\listener;

use baubolp\core\provider\AsyncExecutor;
use matze\replaysystem\player\form\ChooseOptionForm;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class PlayerJoinListener implements Listener {

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event): void {
        $event->setJoinMessage("");
        $player = $event->getPlayer();

        $player->setGamemode(2);
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->removeAllEffects();
        $player->noDamageTicks = PHP_INT_MAX;
        $player->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 9999999, 1, false));
        AsyncExecutor::submitClosureTask(60, function(int $tick) use ($player): void {
            if(!$player->isConnected()) return;
            $player->setImmobile();
            ChooseOptionForm::open($player);
        });
    }
}