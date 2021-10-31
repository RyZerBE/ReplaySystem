<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use matze\replaysystem\recorder\action\ActionIds;
use function json_encode;

class EntityUpdateAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntityUpdateAction";
    }

    public function getId(): int{
        return ActionIds::ENTITY_UPDATE_ACTION;
    }

    /** @var int */
    public $entityId;
    /** @var string */
    public $nametag;
    /** @var bool */
    public $nametagVisible;
    /** @var bool */
    public $nametagAlwaysVisible;
    /** @var string */
    public $scoretag;
    /** @var float */
    public $scale;

    /**
     * @return string
     */
    public function encode(): string{
        return json_encode([
            "EntityId" => $this->entityId,
            "Nametag" => $this->nametag,
            "Scoretag" => $this->scoretag,
            "Scale" => $this->scale,
            "NametagVisible" => $this->nametagVisible,
            "NametagAlwaysVisible" => $this->nametagAlwaysVisible
        ]);
    }
}