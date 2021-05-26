<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use function json_encode;

class EntitySpawnAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntitySpawnAction";
    }

    /** @var int */
    public $networkID;
    /** @var string */
    public $entityID;
    /** @var float */
    public $x;
    /** @var float */
    public $y;
    /** @var float */
    public $z;
    /** @var float */
    public $yaw;
    /** @var float */
    public $pitch;
    /** @var string */
    public $nametag;
    /** @var string */
    public $scoreTag;
    /** @var mixed|null */
    public $skin;
    /** @var string|null */
    public $item;
    /** @var bool */
    public $isPlayer;

    /**
     * @return string
     */
    public function encode(): string{
        return json_encode([
            "NetworkID" => $this->networkID,
            "EntityId" => $this->entityID,
            "X" => $this->x,
            "Y" => $this->y,
            "Z" => $this->z,
            "Yaw" => $this->yaw,
            "Pitch" => $this->pitch,
            "Nametag" => $this->nametag,
            "ScoreTag" => $this->scoreTag,
            "Skin" => $this->skin,
            "Item" => $this->item,
            "IsPlayer" => $this->isPlayer
        ]);
    }
}