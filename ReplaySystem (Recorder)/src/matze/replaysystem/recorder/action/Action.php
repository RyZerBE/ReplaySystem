<?php

namespace matze\replaysystem\recorder\action;

abstract class Action {
    abstract public function getId(): int;
    abstract public function getName(): string;
    abstract public function encode(): string;
}