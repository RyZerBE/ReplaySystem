<?php

namespace matze\replaysystem\player\utils;

use function array_shift;
use function count;
use function microtime;

class Timings {
    use InstantiableTrait;

    /** @var array */
    private static $timings = [];
    /** @var array */
    private static $timings_session = [];

    public function reset(): void{
        self::$timings = [];
    }

    /**
     * @return array
     */
    public static function getTimings(): array{
        return self::$timings;
    }

    /**
     * @param string $name
     */
    public static function startTiming(string $name): void{
        self::$timings_session[$name] = microtime(true);
    }

    /**
     * @param string $name
     */
    public static function stopTiming(string $name): void{
        if(!isset(self::$timings_session[$name])) return;
        if(!isset(self::$timings[$name])) self::$timings[$name] = [];
        $duration = microtime(true) - self::$timings_session[$name];
        self::$timings[$name][] = $duration;

        if(count(self::$timings[$name]) > 50){
            array_shift(self::$timings[$name]);
        }
    }

    /**
     * @param string $name
     * @return float
     */
    public static function getAverageTimings(string $name): float{
        if(!isset(self::$timings[$name])) return -1;
        $average = 0;
        foreach(self::$timings[$name] as $timing){
            $average += $timing;
        }
        $average /= count(self::$timings[$name]);
        return $average;
    }
}