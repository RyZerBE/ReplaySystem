<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use function json_encode;

class LevelEventAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "LevelEventAction";
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
     * @return string
     */
    public function encode(): string{
        return json_encode([
            "EvId" => $this->evid,
            "X" => $this->x,
            "Y" => $this->y,
            "Z" => $this->z,
            "Data" => $this->data
        ]);
    }
}