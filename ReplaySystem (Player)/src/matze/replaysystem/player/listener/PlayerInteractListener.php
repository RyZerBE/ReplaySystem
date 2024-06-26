<?php

namespace matze\replaysystem\player\listener;

use matze\replaysystem\player\form\TeleporterForm;
use matze\replaysystem\player\replay\Replay;
use matze\replaysystem\player\replay\ReplayManager;
use matze\replaysystem\player\utils\ItemUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\level\sound\ClickSound;
use function is_null;

class PlayerInteractListener implements Listener {

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $replay = ReplayManager::getInstance()->getReplayByLevel($player->getLevel());

        if(!ItemUtils::hasItemTag($item, "replay_item") || is_null($replay) || $player->hasItemCooldown($item)) return;
        $player->resetItemCooldown($item, 10);
        switch(ItemUtils::getItemTag($item, "replay_item")) {
            case "play_backwards": {
                $replay->setPlayType(Replay::PLAY_TYPE_BACKWARDS);
                $player->getLevel()->addSound(new ClickSound($player));
                break;
            }
            case "pause_replay": {
                $replay->setPaused(!$replay->isPaused());
                $player->getLevel()->addSound(new ClickSound($player));
                break;
            }
            case "play_forward": {
                $replay->setPlayType(Replay::PLAY_TYPE_FORWARD);
                $player->getLevel()->addSound(new ClickSound($player));
                break;
            }
            case "slowmode": {
                $player->getLevel()->addSound(new ClickSound($player));
                if($replay->getTickInterval() === 1) {
                    $replay->setTickInterval(2);
                    $player->getInventory()->setItemInHand(ItemUtils::addItemTag(Item::get(Item::MAGMA_CREAM)->setCustomName("§r§aSlowmode (§6ON§a)"), "slowmode", "replay_item"));
                } else {
                    $replay->setTickInterval(1);
                    $player->getInventory()->setItemInHand(ItemUtils::addItemTag(Item::get(Item::MAGMA_CREAM)->setCustomName("§r§aSlowmode (§cOFF§a)"), "slowmode", "replay_item"));
                }
                break;
            }
            case "teleporter": {
                TeleporterForm::open($player);
                break;
            }
        }
    }
}