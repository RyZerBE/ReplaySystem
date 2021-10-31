<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use matze\replaysystem\recorder\action\ActionIds;
use function json_encode;

class EntityMoveAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntityMoveAction";
    }

    public function getId(): int{
        return ActionIds::ENTITY_MOVE_ACTION;
    }

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

    /**
     * @return string
     */
    public function encode(): string{
        return json_encode([
            "EntityId" => $this->entityID,
            "X" => $this->x,
            "Y" => $this->y,
            "Z" => $this->z,
            "Yaw" => $this->yaw,
            "Pitch" => $this->pitch
        ]);
    }
}