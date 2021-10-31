<?php

namespace matze\replaysystem\player\action\types;

use matze\replaysystem\player\action\Action;
use matze\replaysystem\player\action\ActionIds;
use matze\replaysystem\player\replay\Replay;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;

class LevelEventAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "LevelEventAction";
    }

    public function getId(): int{
        return ActionIds::LEVEL_EVENT_ACTION;
    }

    /** @var int */
    public $evid;
    /** @var float */
    public $x;
    /** @var float */
    public $y;
    /** @var float */
    public $z;
    /** @var int */
    public $data;

    /**
     * @param array $data
     * @return $this
     */
    public function decode(array $data): Action{
        $action = new LevelEventAction();
        $action->evid = $data["EvId"];
        $action->x = $data["X"];
        $action->y = $data["Y"];
        $action->z = $data["Z"];
        $action->data = $data["Data"];
        return $action;
    }

    /**
     * @param Replay $replay
     * @param Action|LevelEventAction $action
     * @param int $playType
     */
    public function handle(Replay $replay, Action $action, int $playType): void{
        $pk = new LevelEventPacket();
        $pk->evid = $action->evid;
        $pk->data = $action->data;
        $pk->position = new Vector3($action->x, $action->y, $action->z);
        $replay->getLevel()->broadcastPacketToViewers($pk->position, $pk);
    }
}