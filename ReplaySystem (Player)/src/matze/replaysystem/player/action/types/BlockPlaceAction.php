<?php

namespace matze\replaysystem\player\action\types;

use matze\replaysystem\player\action\Action;
use matze\replaysystem\player\action\ActionIds;
use matze\replaysystem\player\replay\Replay;

class BlockPlaceAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "BlockPlaceAction";
    }

    public function getId(): int{
        return ActionIds::BLOCK_PLACE_ACTION;
    }

    /** @var int */
    public $x;
    /** @var int */
    public $y;
    /** @var int */
    public $z;
    /** @var int */
    public $blockPlacedId;
    /** @var int */
    public $blockPlacedDamage;
    /** @var int */
    public $blockReplacedId;
    /** @var int */
    public $blockReplacedDamage;

    /**
     * @param array $data
     * @return $this
     */
    public function decode(array $data): Action{
        $action = new BlockPlaceAction();
        $action->x = $data["X"];
        $action->y = $data["Y"];
        $action->z = $data["Z"];
        $action->blockPlacedId = $data["BlockPlacedId"];
        $action->blockPlacedDamage = $data["BlockPlacedDamage"];
        $action->blockReplacedId = $data["BlockReplacedId"];
        $action->blockReplacedDamage = $data["BlockReplacedDamage"];
        return $action;
    }

    /**
     * @param Replay $replay
     * @param Action|BlockPlaceAction $action
     * @param int $playType
     */
    public function handle(Replay $replay, Action $action, int $playType): void{
        switch($playType) {
            case Replay::PLAY_TYPE_FORWARD: {
                $replay->getLevel()->setBlockIdAt($action->x, $action->y, $action->z, $action->blockPlacedId);
                $replay->getLevel()->setBlockDataAt($action->x, $action->y, $action->z, $action->blockPlacedDamage);
                break;
            }
            case Replay::PLAY_TYPE_BACKWARDS: {
                $replay->getLevel()->setBlockIdAt($action->x, $action->y, $action->z, $action->blockReplacedId);
                $replay->getLevel()->setBlockDataAt($action->x, $action->y, $action->z, $action->blockReplacedDamage);
                break;
            }
        }
    }
}