<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use matze\replaysystem\recorder\action\ActionIds;
use function json_encode;

class EntityContentUpdateAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntityContentUpdateAction";
    }

    public function getId(): int{
        return ActionIds::ENTITY_CONTENT_UPDATE_ACTION;
    }

    /** @var int */
    public $entityId;
    /** @var string */
    public $item;
    /** @var string */
    public $boots;
    /** @var string */
    public $leggings;
    /** @var string */
    public $chestplate;
    /** @var string */
    public $helmet;

    /**
     * @return string
     */
    public function encode(): string{
        return json_encode([
            "EntityId" => $this->entityId,
            "Item" => $this->item,
            "Boots" => $this->boots,
            "Leggings" => $this->leggings,
            "Chestplate" => $this->chestplate,
            "Helmet" => $this->helmet
        ]);
    }
}