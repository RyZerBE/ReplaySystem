<?php

namespace matze\replaysystem\recorder\provider;

use function file_put_contents;
use function json_encode;
use function mkdir;

class ReplayProvider {

    /**
     * @param string $path
     * @param string $replayId
     * @param array $actions
     * @param array $chunks
     * @param array $extraData
     */
    public static function saveReplay(string $path, string $replayId, array $actions, array $chunks, array $extraData): void {
        @mkdir($path);
        $data = json_encode([
            "ReplayId" => $replayId,
            "Actions" => $actions,
            "Chunks" => $chunks,
            "ExtraData" => $extraData
        ]);
        file_put_contents($path . $replayId . ".dat", $data);
    }
}