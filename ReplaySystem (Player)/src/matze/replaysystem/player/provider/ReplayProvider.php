<?php

namespace matze\replaysystem\player\provider;

use matze\replaysystem\player\Loader;
use function date;
use function file_exists;
use function file_get_contents;
use function filemtime;
use function gzinflate;
use function is_file;
use function json_decode;
use function strtotime;
use function time;
use function unlink;
use function var_dump;

class ReplayProvider {

    /**
     * @param string $path
     * @param string $replayId
     * @return array|null
     */
    public static function loadReplay(string $path, string $replayId): ?array {
        $file = $path . $replayId . ".dat";
        if(!is_file($file)) return null;
        return json_decode(gzinflate(gzinflate(file_get_contents($file))), true);
    }

    public static function expiredReplay(string $replayId): bool {
        $path = Loader::getSettings()->get("path").$replayId.".dat";
        $days = Loader::getSettings()->get("expire_days");

        if(!file_exists($path)) return false;

        $creationDate = filemtime($path);
        $deleteDate = strtotime(date("Y-m-d", $creationDate) . " + {$days} day");
        #var_dump(date("Y-m-d H:i:s", $deleteDate)." delete date");

        if($deleteDate < time()) {
            unlink($path);
            var_dump("deleted replay with the id ".$replayId);
        }else
            return false;

        return true;
    }
}