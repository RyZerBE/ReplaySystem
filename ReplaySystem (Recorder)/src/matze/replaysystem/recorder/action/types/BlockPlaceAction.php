<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use function json_encode;

class BlockPlaceAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "BlockPlaceAction";
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
     * @return string
     */
    public function encode(): string{
        return json_encode([
            "X" => $this->x,
            "Y" => $this->y,
            "Z" => $this->z,
            "BlockPlacedId" => $this->blockPlacedId,
            "BlockPlacedDamage" => $this->blockPlacedDamage,
            "BlockReplacedId" => $this->blockReplacedId,
            "BlockReplacedDamage" => $this->blockReplacedDamage
        ]);
    }
}