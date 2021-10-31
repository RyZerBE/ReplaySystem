<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use matze\replaysystem\recorder\action\ActionIds;
use function json_encode;
use function serialize;

class SetActorDataAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "SetActorDataAction";
    }

    public function getId(): int{
        return ActionIds::SET_ACTOR_DATA_ACTION;
    }

    /** @var int */
    public $entityId;
    /** @var mixed */
    public $metadata;

    /**
     * @return string
     */
    public function encode(): string{
        return json_encode([
            "EntityId" => $this->entityId,
            "Metadata" => serialize($this->metadata)
        ]);
    }
}