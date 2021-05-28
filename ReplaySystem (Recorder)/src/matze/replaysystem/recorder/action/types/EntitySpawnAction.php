<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use function json_decode;
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

    /**
     * @param array $data
     * @return $this
     */
    public static function decode(array $data): Action{
        $action = new EntitySpawnAction();
        $action->networkID = $data["NetworkID"];
        $action->entityID = $data["EntityId"];
        $action->x = $data["X"];
        $action->y = $data["Y"];
        $action->z = $data["Z"];
        $action->yaw = $data["Yaw"];
        $action->pitch = $data["Pitch"];
        $action->nametag = $data["Nametag"];
        $action->scoreTag = $data["ScoreTag"];
        $action->skin = ($data["Skin"] !== "null" ? json_decode($data["Skin"], true) : null);
        $action->item = $data["Item"];
        $action->isPlayer = $data["IsPlayer"];
        return $action;
    }
}