<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use function json_encode;

class LevelSoundEventAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "LevelSoundEventAction";
    }

    /** @var int */
    public $sound;
    /** @var float */
    public $x;
    /** @var float */
    public $y;
    /** @var float */
    public $z;
    /** @var int */
    public $extraData;
    /** @var string */
    public $entityType;
    /** @var bool */
    public $isBabyMob;
    /** @var bool */
    public $disableRelativeVolume;

    /**
     * @return string
     */
    public function encode(): string{
        return json_encode([
            "Sound" => $this->sound,
            "X" => $this->x,
            "Y" => $this->y,
            "Z" => $this->z,
            "ExtraData" => $this->extraData,
            "EntityType" => $this->entityType,
            "IsBabyMob" => $this->isBabyMob,
            "DisableRelativeVolume" => $this->disableRelativeVolume
        ]);
    }
}