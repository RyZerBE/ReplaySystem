<?php

namespace matze\replaysystem\player\action\types;

use matze\replaysystem\player\action\Action;
use matze\replaysystem\player\replay\Replay;
use pocketmine\level\particle\DustParticle;
use pocketmine\math\Vector3;
use function is_null;

class EntityMoveAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntityMoveAction";
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
     * @param array $data
     * @return $this
     */
    public function decode(array $data): Action{
        $action = new EntityMoveAction();
        $action->entityID = $data["EntityId"];
        $action->x = $data["X"];
        $action->y = $data["Y"];
        $action->z = $data["Z"];
        $action->yaw = $data["Yaw"];
        $action->pitch = $data["Pitch"];
        return $action;
    }

    /**
     * @param Replay $replay
     * @param Action|EntityMoveAction $action
     * @param int $playType
     */
    public function handle(Replay $replay, Action $action, int $playType): void{
        $entity = $replay->findEntity($action->entityID);
        if(is_null($entity)) return;
        $entity->setRotation($action->yaw, $action->pitch);
        $entity->setMotion(new Vector3(
            $action->x - $entity->x,
            $action->y - $entity->y,
            $action->z - $entity->z
        ));
        $entity->setForceMovementUpdate();
        $vector3 = new Vector3($action->x, $action->y, $action->z);
        if($entity->distance($vector3) >= 4) $entity->teleport($vector3);
    }
}