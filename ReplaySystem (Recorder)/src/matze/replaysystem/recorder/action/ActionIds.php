<?php

namespace matze\replaysystem\recorder\action;

final class ActionIds {

    public const BLOCK_BREAK_ACTION = 0x01;
    public const BLOCK_EVENT_ACTION = 0x02;
    public const BLOCK_PLACE_ACTION = 0x03;

    public const ENTITY_ANIMATION_ACTION = 0x04;
    public const ENTITY_CONTENT_UPDATE_ACTION = 0x05;
    public const ENTITY_DESPAWN_ACTION = 0x06;
    public const ENTITY_EVENT_ACTION = 0x07;
    public const ENTITY_MOVE_ACTION = 0x08;
    public const ENTITY_SNEAK_ACTION = 0x09;
    public const ENTITY_SPAWN_ACTION = 0x10;
    public const ENTITY_UPDATE_ACTION = 0x11;

    public const LEVEL_EVENT_ACTION = 0x12;
    public const LEVEL_SOUND_EVENT_ACTION = 0x13;
    public const SET_ACTOR_DATA_ACTION = 0x14;
}