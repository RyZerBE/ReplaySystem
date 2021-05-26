<?php

namespace matze\replaysystem\recorder\replay;

use matze\replaysystem\recorder\action\Action;
use matze\replaysystem\recorder\action\types\EntityContentUpdateAction;
use matze\replaysystem\recorder\action\types\EntityDespawnAction;
use matze\replaysystem\recorder\action\types\EntitySpawnAction;
use matze\replaysystem\recorder\Loader;
use matze\replaysystem\recorder\provider\ReplayProvider;
use matze\replaysystem\recorder\utils\AsyncExecuter;
use matze\replaysystem\recorder\utils\ItemUtils;
use matze\replaysystem\recorder\utils\Vector3Utils;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\object\ItemEntity;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;
use function array_search;
use function base64_encode;
use function count;
use function implode;
use function in_array;
use function is_null;
use function json_encode;
use function microtime;
use function mt_rand;
use function round;
use function serialize;
use function unserialize;

class Replay {
    public const CHUNKS_PER_TICK = 40;

    /** @var Level */
    private $level;
    /** @var string */
    private $id;

    /** @var array  */
    private $chunkQueue = [];
    /** @var array  */
    private $chunks = [];
    /** @var array  */
    private $actions = [];
    /** @var array  */
    private $entities = [];
    /** @var Vector3|null */
    private $spawn = null;

    /** @var int  */
    private $tick = 0;
    /** @var bool  */
    private $running = false;

    /**
     * Replay constructor.
     * @param Level $level
     */
    public function __construct(Level $level){
        $this->level = $level;
        $this->id = mt_rand(1000, 9999);//Todo
    }

    /**
     * @return Level
     */
    public function getLevel(): Level{
        return $this->level;
    }

    /**
     * @return string
     */
    public function getId(): string{
        return $this->id;
    }

    /**
     * @return int
     */
    public function getTick(): int{
        return $this->tick;
    }

    /**
     * @return Vector3
     */
    public function getSpawn(): Vector3{
        if(is_null($this->spawn)) $this->spawn = new Vector3(0, 100, 0);
        return $this->spawn;
    }

    /**
     * @return bool
     */
    public function isRunning(): bool{
        return $this->running;
    }

    /**
     * @param bool $running
     */
    public function setRunning(bool $running): void{
        $this->running = $running;
    }

    /**
     * @param Vector3 $spawn
     */
    public function setSpawn(Vector3 $spawn): void{
        $this->spawn = $spawn;
    }

    public function startRecording(): void {
        ReplayManager::getInstance()->addReplay($this);
        $this->setRunning(true);
        $level = $this->getLevel();
        foreach($level->getChunks() as $chunk) {
            $this->queueChunk($chunk);
        }
        foreach($level->getEntities() as $entity) {
            $this->addEntity($entity);
        }
    }

    public function stopRecording(): void {
        ReplayManager::getInstance()->removeReplay($this);
        $this->setRunning(false);
        foreach($this->getQueuedChunks(count($this->chunkQueue)) as $chunk) {
            $this->addChunk($chunk);
        }

        $replayId = $this->getId();
        $path = Loader::getSettings()->get("path");
        $actions = serialize($this->actions);
        $chunks = serialize($this->chunks);
        $extraData = serialize(["Duration" => $this->tick, "Server" => Server::getInstance()->getMotd(), "Spawn" => Vector3Utils::toString($this->getSpawn())]);
        AsyncExecuter::submitAsyncTask(function() use ($actions, $chunks, $extraData, $replayId, $path): float {
            $microtime = microtime(true);
            ReplayProvider::saveReplay($path, $replayId, unserialize($actions), unserialize($chunks), unserialize($extraData));
            return round(microtime(true) - $microtime, 4);
        }, function(Server $server, float $microtime) use ($replayId): void {
            $server->getLogger()->info("Saved Replay " . $replayId . ". Took " . $microtime . "s");
        });
    }

    public function onUpdate(): void {
        $this->tick++;
        foreach($this->getQueuedChunks() as $chunk) {
            $this->addChunk($chunk);
        }
    }

    /**
     * @param Chunk $chunk
     */
    public function queueChunk(Chunk $chunk): void {
        $this->chunkQueue[] = $chunk;
    }

    /**
     * @param int $amount
     * @return array
     */
    public function getQueuedChunks(int $amount = self::CHUNKS_PER_TICK): array{
        $chunks = [];
        foreach($this->chunkQueue as $key => $chunk) {
            if(--$amount <= 0) break;
            unset($this->chunkQueue[$key]);
            $chunks[] = $chunk;
        }
        return $chunks;
    }

    /**
     * @param Chunk $chunk
     */
    public function addChunk(Chunk $chunk): void {
        $sChunk = implode(":", [$chunk->getX(), $chunk->getZ()]);
        if(isset($this->chunks[$sChunk])) return;
        $this->chunks[$sChunk] = base64_encode($chunk->fastSerialize());
    }

    /**
     * @param Action $action
     */
    public function addAction(Action $action): void {
        $this->actions[$this->getTick()][$action->getName()][] = $action->encode();
    }

    /**
     * @param Entity $entity
     */
    public function addEntity(Entity $entity): void {
        if(in_array($entity->getId(), $this->entities)) return;
        $this->entities[] = $entity->getId();

        $action = new EntitySpawnAction();
        $action->networkID = $entity::NETWORK_ID;
        $action->entityID = $entity->getId();
        $action->x = $entity->getX();
        $action->y = $entity->getY();
        $action->z = $entity->getZ();
        $action->yaw = $entity->getYaw();
        $action->pitch = $entity->getPitch();
        $action->nametag = $entity->getNameTag();
        $action->scoreTag = $entity->getScoreTag();
        $action->skin = ($entity instanceof Human ? json_encode([
            "SkinId" => $entity->getSkin()->getSkinId(),
            "SkinData" => base64_encode($entity->getSkin()->getSkinData()),
            "CapeData" => base64_encode($entity->getSkin()->getCapeData()),
            "GeometryName" => $entity->getSkin()->getGeometryName(),
            "GeometryData" => base64_encode($entity->getSkin()->getGeometryData())
        ]) : "null");
        $action->item = ($entity instanceof ItemEntity ? ItemUtils::toString($entity->getItem()) : "null");
        $action->isPlayer = ($entity instanceof Player);
        $this->addAction($action);

        if($entity instanceof Human) {
            $armorInventory = $entity->getArmorInventory();
            $action = new EntityContentUpdateAction();
            $action->entityId = $entity->getId();
            $action->item = ItemUtils::toString($entity->getInventory()->getItemInHand());
            $action->boots = ItemUtils::toString($armorInventory->getBoots());
            $action->leggings = ItemUtils::toString($armorInventory->getLeggings());
            $action->chestplate = ItemUtils::toString($armorInventory->getChestplate());
            $action->helmet = ItemUtils::toString($armorInventory->getHelmet());
            $this->addAction($action);
        }
    }

    /**
     * @param Entity $entity
     */
    public function removeEntity(Entity $entity): void {
        if(in_array($entity->getId(), $this->entities)) unset($this->entities[array_search($entity->getId(), $this->entities)]);
        $action = new EntityDespawnAction();
        $action->entityId = $entity->getId();
        $this->addAction($action);
    }
}