<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use function json_encode;

class BlockBreakAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "BlockBreakAction";
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