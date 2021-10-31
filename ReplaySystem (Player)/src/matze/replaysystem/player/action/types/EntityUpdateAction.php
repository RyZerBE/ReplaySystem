<?php

namespace matze\replaysystem\player\action\types;

use matze\replaysystem\player\action\Action;
use matze\replaysystem\player\action\ActionIds;
use matze\replaysystem\player\replay\Replay;
use function is_null;

class EntityUpdateAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntityUpdateAction";
    }

    public function getId(): int{
        return ActionIds::ENTITY_UPDATE_ACTION;
    }

    /** @var int */
    public $entityId;
    /** @var string */
    public $nametag;
    /** @var bool */
    public $nametagVisible;
    /** @var bool */
    public $nametagAlwaysVisible;
    /** @var string */
    public $scoretag;
    /** @var float */
    public $scale;

    /**
     * @param array $data
     * @return $this
     */
    public function decode(array $data): Action{
        $action = new EntityUpdateAction();
        $action->entityId = $data["EntityId"];
        $action->nametag = $data["Nametag"];
        $action->scoretag = $data["Scoretag"];
        $action->scale = $data["Scale"];
        $action->nametagVisible = $data["NametagVisible"];
        $action->nametagAlwaysVisible = $data["NametagAlwaysVisible"];
        return $action;
    }

    /**
     * @param Replay $replay
     * @param Action|EntityUpdateAction $action
     * @param int $playType
     */
    public function handle(Replay $replay, Action $action, int $playType): void{
        $entity = $replay->findEntity($action->entityId);
        if(is_null($entity)) return;
        $entity->setNameTag($action->nametag);
        $entity->setScoreTag(($action->scoretag ?? ""));
        $entity->setScale($action->scale);
        $entity->setNameTagVisible((bool)($action->nametagVisible ?? true));
        $entity->setNameTagAlwaysVisible((bool)($action->nametagAlwaysVisible ?? true));
    }
}