<?php

namespace matze\replaysystem\player\action\types;

use matze\replaysystem\player\action\Action;
use matze\replaysystem\player\replay\Replay;
use matze\replaysystem\player\utils\ItemUtils;
use pocketmine\entity\Human;
use pocketmine\item\Item;
use pocketmine\Server;
use function is_null;

class EntityContentUpdateAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntityContentUpdateAction";
    }

    /** @var int */
    public $entityId;
    /** @var Item */
    public $item;
    /** @var Item */
    public $boots;
    /** @var Item */
    public $leggings;
    /** @var Item */
    public $chestplate;
    /** @var Item */
    public $helmet;

    /**
     * @param array $data
     * @return $this
     */
    public function decode(array $data): Action{
        $action = new EntityContentUpdateAction();
        $action->entityId = $data["EntityId"];
        $action->item = ItemUtils::fromString($data["Item"]);
        $action->boots = ItemUtils::fromString($data["Boots"]);
        $action->leggings = ItemUtils::fromString($data["Leggings"]);
        $action->chestplate = ItemUtils::fromString($data["Chestplate"]);
        $action->helmet = ItemUtils::fromString($data["Helmet"]);
        return $action;
    }

    /**
     * @param Replay $replay
     * @param Action|EntityContentUpdateAction $action
     * @param int $playType
     */
    public function handle(Replay $replay, Action $action, int $playType): void{
        $entity = $replay->findEntity($action->entityId);
        if(is_null($entity) || !$entity instanceof Human) return;
        $entity->getInventory()->setItemInHand($action->item);
        $entity->getArmorInventory()->setHelmet($action->helmet);
        $entity->getArmorInventory()->setLeggings($action->leggings);
        $entity->getArmorInventory()->setChestplate($action->chestplate);
        $entity->getArmorInventory()->setBoots($action->boots);

        $entity->getInventory()->sendHeldItem(Server::getInstance()->getOnlinePlayers());
        $entity->getArmorInventory()->sendContents(Server::getInstance()->getOnlinePlayers());
    }
}