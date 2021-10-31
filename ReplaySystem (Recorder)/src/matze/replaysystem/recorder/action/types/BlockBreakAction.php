<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use matze\replaysystem\recorder\action\ActionIds;
use function json_encode;

class BlockBreakAction extends Action {

    /**
     * @return string
     */
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
     * @return string
     */
    public function encode(): string{
        return json_encode([
            "X" => $this->x,
            "Y" => $this->y,
            "Z" => $this->z,
            "BlockId" => $this->blockId,
            "BlockDamage" => $this->blockDamage,
        ]);
    }
}