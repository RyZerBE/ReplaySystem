<?php

namespace matze\replaysystem\player\utils;

use pocketmine\math\Vector3;
use function explode;
use function str_replace;

class Vector3Utils {

    /**
     * @param string $vector
     * @return Vector3
     */
    public static function fromString(string $vector): Vector3 {
        $vector = str_replace(["Vector3(x=", "y=", "z=", ")"], "", $vector);
        $vector = explode(":", $vector);
        return new Vector3((float)$vector[0], (float)$vector[1], (float)$vector[2]);
    }

    /**
     * @param Vector3 $vector3
     * @return string
     */
    public static function toString(Vector3 $vector3): string {
        return $vector3->x . ":" . $vector3->y . ":" . $vector3->z;
    }
}