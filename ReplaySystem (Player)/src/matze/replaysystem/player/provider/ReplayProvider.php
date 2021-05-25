<?php

namespace matze\replaysystem\player\provider;

use function file_get_contents;
use function is_file;
use function json_decode;

class ReplayProvider {

    /**
     * @param string $path
     * @param string $replayId
     * @return array|null
     */
    public static function loadReplay(string $path, string $replayId): ?array {
        $file = $path . $replayId . ".dat";
        if(!is_file($file)) return null;
        return json_decode(file_get_contents($file), true);
    }
}