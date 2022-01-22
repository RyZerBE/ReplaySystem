<?php

namespace matze\replaysystem\recorder\provider;

use pocketmine\Player;
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
    public static function saveReplay(string $path, string $replayId, array $actions, array $chunks, array $extraData): void
    {
        @mkdir($path);
        $data = json_encode([
            "ReplayId" => $replayId,
            "Actions" => $actions,
            "Chunks" => $chunks,
            "ExtraData" => $extraData
        ]);
        $compressed = gzdeflate($data, 9);
        $compressed = gzdeflate($compressed, 9);
        file_put_contents($path . $replayId . ".dat", $compressed);
    }

    /**
     * @param array $playerName
     * @param string $id
     */
    public static function addIdToPlayer(array $playerName, string $id)
    {
        $playerNames = [];
        foreach ($playerName as $player) {
            if ($player instanceof Player)
                $playerNames[] = $player->getName();
            else if (is_string($player))
                $playerNames[] = $player;
        }
    }
}