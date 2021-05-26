<?php

namespace matze\replaysystem\recorder\action\types;

use matze\replaysystem\recorder\action\Action;
use function json_encode;
use function serialize;

class SetActorDataAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "SetActorDataAction";
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