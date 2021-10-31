<?php

namespace matze\replaysystem\player\action\types;

use matze\replaysystem\player\action\Action;
use matze\replaysystem\player\action\ActionIds;
use matze\replaysystem\player\replay\Replay;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\BlockEventPacket;

class BlockEventAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "BlockEventAction";
    }

    public function getId(): int{
        return ActionIds::BLOCK_EVENT_ACTION;
    }

    /** @var int */
    public $x;
    /** @var int */
    public $y;
    /** @var int */
    public $z;
    /** @var int */
    public $eventType;
    /** @var int */
    public $eventData;

    /**
     * @param array $data
     * @return $this
     */
    public function decode(array $data): Action{
        $action = new BlockEventAction();
        $action->x = $data["X"];
        $action->y = $data["Y"];
        $action->z = $data["Z"];
        $action->eventType = $data["EventType"];
        $action->eventData = $data["EventData"];
        return $action;
    }

    /**
     * @param Replay $replay
     * @param Action|BlockEventAction $action
     * @param int $playType
     */
    public function handle(Replay $replay, Action $action, int $playType): void{
        $packet = new BlockEventPacket();
        $packet->x = (int)$action->x;
        $packet->y = (int)$action->y;
        $packet->z = (int)$action->z;
        $packet->eventType = $action->eventType;
        $packet->eventData = $action->eventData;
        $replay->getLevel()->broadcastPacketToViewers(new Vector3($action->x, $action->y, $action->z), $packet);
    }
}