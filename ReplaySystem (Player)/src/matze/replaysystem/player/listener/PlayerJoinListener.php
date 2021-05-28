<?php

namespace matze\replaysystem\player\listener;

use matze\replaysystem\player\form\PlayReplayForm;
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

        $player->setGamemode(3);
        $player->setImmobile();
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->removeAllEffects();
        $player->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 9999999, 1, false));
        PlayReplayForm::open($player);
    }
}