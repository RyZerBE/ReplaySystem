<?php

namespace matze\replaysystem\player\action\types;

use matze\replaysystem\player\action\Action;
use matze\replaysystem\player\action\ActionIds;
use matze\replaysystem\player\replay\Replay;
use pocketmine\math\Vector3;

class LevelSoundEventAction extends Action {


    /**
     * @return string
     */
    public function getName(): string{
        return "LevelSoundEventAction";
    }

    public function getId(): int{
        return ActionIds::LEVEL_SOUND_EVENT_ACTION;
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
     * @param array $data
     * @return $this
     */
    public function decode(array $data): Action{
        $action = new LevelSoundEventAction();
        $action->sound = $data["Sound"];
        $action->x = $data["X"];
        $action->y = $data["Y"];
        $action->z = $data["Z"];
        $action->extraData = $data["ExtraData"];
        $action->entityType = $data["EntityType"];
        $action->isBabyMob = $data["IsBabyMob"];
        $action->disableRelativeVolume = $data["DisableRelativeVolume"];
        return $action;
    }

    /**
     * @param Replay $replay
     * @param Action|LevelSoundEventAction $action
     * @param int $playType
     */
    public function handle(Replay $replay, Action $action, int $playType): void{
        $replay->getLevel()->broadcastLevelSoundEvent(
            new Vector3($action->x, $action->y, $action->z),
            $action->sound,
            $action->extraData,
            -1,
            $action->isBabyMob,
            $action->disableRelativeVolume
        );
    }
}