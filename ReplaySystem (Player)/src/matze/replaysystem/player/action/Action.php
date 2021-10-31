<?php

namespace matze\replaysystem\player\action;

use matze\replaysystem\player\replay\Replay;

abstract class Action {
    abstract public function getName(): string;
    abstract public function getId(): int;
    abstract public function decode(array $data): self;
    abstract public function handle(Replay $replay, self $action, int $playType): void;
}