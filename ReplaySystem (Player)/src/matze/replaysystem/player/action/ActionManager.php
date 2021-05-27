<?php

namespace matze\replaysystem\player\action;

use matze\replaysystem\player\action\types\BlockBreakAction;
use matze\replaysystem\player\action\types\BlockEventAction;
use matze\replaysystem\player\action\types\BlockPlaceAction;
use matze\replaysystem\player\action\types\EntityAnimationAction;
use matze\replaysystem\player\action\types\EntityContentUpdateAction;
use matze\replaysystem\player\action\types\EntityDespawnAction;
use matze\replaysystem\player\action\types\EntityEventAction;
use matze\replaysystem\player\action\types\EntityMoveAction;
use matze\replaysystem\player\action\types\EntitySneakAction;
use matze\replaysystem\player\action\types\EntitySpawnAction;
use matze\replaysystem\player\action\types\EntityUpdateAction;
use matze\replaysystem\player\action\types\LevelEventAction;
use matze\replaysystem\player\action\types\LevelSoundEventAction;
use matze\replaysystem\player\utils\InstantiableTrait;

class ActionManager {
    use InstantiableTrait;

    /** @var array */
    private $actions = [];

    public function __construct(){
        $actions = [
            new EntitySpawnAction(),
            new EntityMoveAction(),
            new EntitySneakAction(),
            new EntityDespawnAction(),
            new EntityAnimationAction(),
            new EntityEventAction(),
            new LevelEventAction(),
            new LevelSoundEventAction(),
            new BlockPlaceAction(),
            new BlockBreakAction(),
            new EntityContentUpdateAction(),
            //new SetActorDataAction(),
            new BlockEventAction(),
            new EntityUpdateAction()
        ];
        foreach($actions as $action) {
            $this->actions[$action->getName()] = $action;
        }
    }

    /**
     * @return Action[]
     */
    public function getActions(): array{
        return $this->actions;
    }

    /**
     * @param string $name
     * @return Action|null
     */
    public function getAction(string $name): ?Action {
        return $this->actions[$name] ?? null;
    }
}