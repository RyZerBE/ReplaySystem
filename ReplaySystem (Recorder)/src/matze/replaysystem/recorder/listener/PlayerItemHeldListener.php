<?php

namespace matze\replaysystem\recorder\listener;

use matze\replaysystem\recorder\action\types\EntityContentUpdateAction;
use matze\replaysystem\recorder\replay\ReplayManager;
use matze\replaysystem\recorder\utils\ItemUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemHeldEvent;
use function is_null;

class PlayerItemHeldListener implements Listener {

    /**
     * @param PlayerItemHeldEvent $event
     * @priority MONITOR
     */
    public function onItemHeld(PlayerItemHeldEvent $event): void {
        if($event->isCancelled()) return;
        $player = $event->getPlayer();

        $replay = ReplayManager::getInstance()->getReplayByLevel($player->getLevel());
        if(is_null($replay)) return;

        $armorInventory = $player->getArmorInventory();
        $action = new EntityContentUpdateAction();
        $action->entityId = $player->getId();
        $action->item = ItemUtils::toString($player->getInventory()->getItemInHand());
        $action->boots = ItemUtils::toString($armorInventory->getBoots());
        $action->leggings = ItemUtils::toString($armorInventory->getLeggings());
        $action->chestplate = ItemUtils::toString($armorInventory->getChestplate());
        $action->helmet = ItemUtils::toString($armorInventory->getHelmet());
        $replay->addAction($action);
    }
}