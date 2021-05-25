<?php

namespace matze\replaysystem\player\replay;

use matze\replaysystem\player\action\ActionManager;
use matze\replaysystem\player\utils\Timings;
use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use function is_null;
use function json_decode;
use function round;

class Replay {
    public const PLAY_TYPE_FORWARD = 0;
    public const PLAY_TYPE_BACKWARDS = 1;

    /** @var string */
    private $id;

    /** @var array  */
    private $actions = [];
    /** @var array  */
    private $chunks = [];
    /** @var array  */
    private $extraData = [];

    /** @var Vector3|null */
    private $spawn;

    /** @var int  */
    private $tick = 0;
    /** @var int  */
    private $duration = 0;
    /** @var bool  */
    private $running = false;
    /** @var bool  */
    private $paused = false;
    /** @var Level|null */
    private $level = null;
    /** @var int  */
    private $playType = self::PLAY_TYPE_FORWARD;

    /** @var array  */
    private $actionCache = [];

    /**
     * Replay constructor.
     * @param string $id
     */
    public function __construct(string $id){
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string{
        return $this->id;
    }

    /**
     * @return array
     */
    public function getActions(): array{
        return $this->actions;
    }

    /**
     * @return array
     */
    public function getChunks(): array{
        return $this->chunks;
    }

    /**
     * @return array
     */
    public function getExtraData(): array{
        return $this->extraData;
    }

    /**
     * @return Vector3|null
     */
    public function getSpawn(): ?Vector3{
        return $this->spawn;
    }

    /**
     * @return bool
     */
    public function isRunning(): bool{
        return $this->running;
    }

    /**
     * @return bool
     */
    public function isPaused(): bool{
        return $this->paused;
    }

    /**
     * @return int
     */
    public function getTick(): int{
        return $this->tick;
    }

    /**
     * @return Level|null
     */
    public function getLevel(): ?Level{
        return $this->level;
    }

    /**
     * @return int
     */
    public function getPlayType(): int{
        return $this->playType;
    }

    /**
     * @return int
     */
    public function getDuration(): int{
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration(int $duration): void{
        $this->duration = $duration;
    }

    /**
     * @param Vector3 $spawn
     */
    public function setSpawn(Vector3 $spawn): void{
        $this->spawn = new Position($spawn->x, $spawn->y, $spawn->z, $this->getLevel());
    }

    /**
     * @param bool $paused
     */
    public function setPaused(bool $paused): void{
        $this->paused = $paused;
    }

    /**
     * @param Level|null $level
     */
    public function setLevel(?Level $level): void{
        $this->level = $level;
    }

    /**
     * @param array $actions
     */
    public function setActions(array $actions): void{
        $this->actions = $actions;
    }

    /**
     * @param array $chunks
     */
    public function setChunks(array $chunks): void{
        $this->chunks = $chunks;
    }

    /**
     * @param array $extraData
     */
    public function setExtraData(array $extraData): void{
        $this->extraData = $extraData;
    }

    /**
     * @param bool $running
     */
    public function setRunning(bool $running): void{
        $this->running = $running;
    }

    /**
     * @param int $playType
     */
    public function setPlayType(int $playType): void{
        $this->playType = $playType;
    }

    public function onUpdate(): void {
        $tip = "§8[§r§a" . round($this->tick / 20) . "§7/§a" . round($this->getDuration() / 20) . "§8]";
        foreach($this->getLevel()->getPlayers() as $player) $player->sendTip($tip);
        if($this->isPaused()) return;
        if(isset($this->actions[$this->tick])) {
            Timings::startTiming("Actions");
            foreach($this->actions[$this->tick] as $actionName => $actions) {
                $action = ActionManager::getInstance()->getAction($actionName);
                if(is_null($action)) continue;
                foreach($actions as $key => $actionData) {
                    if(!isset($this->actionCache[$this->tick][$actionName][$key])) {
                        $json = json_decode($actionData, true);
                        if(!$json) continue;
                        Timings::startTiming($actionName . "_decoding");
                        $this->actionCache[$this->tick][$actionName][$key] = $action->decode($json);
                        Timings::stopTiming($actionName . "_decoding");
                    }
                    Timings::startTiming($actionName . "_handling");
                    $action->handle($this, $this->actionCache[$this->tick][$actionName][$key], $this->getPlayType());
                    Timings::stopTiming($actionName . "_handling");
                }
            }
            Timings::stopTiming("Actions");
        }

        switch($this->getPlayType()) {
            case self::PLAY_TYPE_FORWARD: {
                if($this->tick >= $this->getDuration()) break;
                $this->tick++;
                break;
            }
            case self::PLAY_TYPE_BACKWARDS: {
                if($this->tick <= 0) break;
                $this->tick--;
                break;
            }
        }
    }

    /**
     * @param int $entityID
     * @return Entity|null
     */
    public function findEntity(int $entityID): ?Entity {
        foreach($this->getLevel()->getEntities() as $entity) {
            if((int)$entity->namedtag->getInt("EntityId", -1) === $entityID) return $entity;
        }
        return null;
    }
}