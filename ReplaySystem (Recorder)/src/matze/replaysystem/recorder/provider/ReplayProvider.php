<?php

namespace matze\replaysystem\recorder\provider;

use BauboLP\Core\Provider\AsyncExecutor;
use pocketmine\Player;
use pocketmine\Server;
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

        AsyncExecutor::submitMySQLAsyncTask("RyzerCore", function (\mysqli $mysqli) use ($playerNames, $id) {
            if (is_array($playerNames)) {
                foreach ($playerNames as $pName) {
                    $res = $mysqli->query("SELECT * FROM `replayList` WHERE playername='$pName'");
                    if ($res->num_rows > 0) {
                        while ($data = $res->fetch_assoc()) {
                            $oldList = explode(";", $data["replays"]);
                            $oldList[] = $id;
                            $newList = implode(";", $oldList);
                            $mysqli->query("UPDATE `replayList` SET replays='$newList' WHERE playername='$pName'");
                        }
                    } else {
                        $mysqli->query("INSERT INTO `replayList`(`playername`, `replays`) VALUES ('$pName', '$id')");
                    }
                }
            }
        });
    }
}