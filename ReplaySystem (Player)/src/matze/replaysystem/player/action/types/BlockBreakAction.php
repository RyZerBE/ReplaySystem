<?php

namespace matze\replaysystem\player\action\types;

use matze\replaysystem\player\action\Action;
use matze\replaysystem\player\action\ActionIds;
use matze\replaysystem\player\replay\Replay;

class BlockBreakAction extends Action {

    public function getName(): string{
        return "BlockBreakAction";
    }

    public function getId(): int{
        return ActionIds::BLOCK_BREAK_ACTION;
    }

    /** @var int */
    public $x;
    /** @var int */
    public $y;
    /** @var int */
    public $z;
    /** @var int */
    public $blockId;
    /** @var int */
    public $blockDamage;

    /**
     * @param array $data
     * @return $this
     */
    public function decode(array $data): Action{
        $action = new BlockBreakAction();
        $action->x = $data["X"];
        $action->y = $data["Y"];
        $action->z = $data["Z"];
        $action->blockId = $data["BlockId"];
        $action->blockDamage = $data["BlockDamage"];
        return $action;
    }

    /**
     * @param Replay $replay
     * @param Action|BlockBreakAction $action
     * @param int $playType
     */
    public function handle(Replay $replay, Action $action, int $playType): void{
        switch($playType) {
            case Replay::PLAY_TYPE_FORWARD: {
                $replay->getLevel()->setBlockIdAt($action->x, $action->y, $action->z, 0);
                $replay->getLevel()->setBlockDataAt($action->x, $action->y, $action->z, 0);
                break;
            }
            case Replay::PLAY_TYPE_BACKWARDS: {
                $replay->getLevel()->setBlockIdAt($action->x, $action->y, $action->z, $action->blockId);
                $replay->getLevel()->setBlockDataAt($action->x, $action->y, $action->z, $action->blockDamage);
                break;
            }
        }
    }
}