<?php

namespace matze\replaysystem\player\replay;

use matze\replaysystem\player\Loader;
use matze\replaysystem\player\provider\ReplayProvider;
use matze\replaysystem\player\utils\AsyncExecuter;
use matze\replaysystem\player\utils\InstantiableTrait;
use matze\replaysystem\player\utils\Vector3Utils;
use pocketmine\level\biome\Biome;
use pocketmine\level\format\Chunk;
use pocketmine\level\generator\Flat;
use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\LevelChunkPacket;
use pocketmine\Player;
use pocketmine\Server;
use function base64_decode;
use function is_file;
use function is_null;
use function uniqid;

class ReplayManager {
    use InstantiableTrait;

    /** @var array  */
    private $replays = [];

    /**
     * @return Replay[]
     */
    public function getReplays(): array{
        return $this->replays;
    }

    /**
     * @param Level $level
     * @param bool $onlyRunning
     * @return Replay|null
     */
    public function getReplayByLevel(Level $level, bool $onlyRunning = true): ?Replay {
        foreach($this->getReplays() as $replay) {
            if($replay->getLevel()->getId() == $level->getId()){
                if($onlyRunning && !$replay->isRunning()) return null;
                return $replay;
            }
        }
        return null;
    }

    /**
     * @param Replay $replay
     */
    public function addReplay(Replay $replay): void {
        $this->replays[] = $replay;
    }

    /**
     * @param string $replayId
     * @param callable $callable
     * @return bool
     */
    public function playReplay(string $replayId, callable $callable): bool {
        $path = Loader::getSettings()->get("path");
        $file = $path . $replayId . ".dat";
        if(!is_file($file)) return false;
        $level = uniqid();
        Server::getInstance()->generateLevel($level, 0, Flat::class, ["preset" => "2;256*minecraft:air;" . Biome::PLAINS . ";"]);
        AsyncExecuter::submitAsyncTask(function() use ($replayId, $path): ?array {
            return ReplayProvider::loadReplay($path, $replayId);
        }, function(Server $server, ?array $result) use ($replayId, $callable, $level): void {
            $replay = new Replay($replayId);
            $replay->setActions($result["Actions"]);
            $replay->setChunks($result["Chunks"]);
            $replay->setExtraData($result["ExtraData"]);
            $replay->setDuration($replay->getExtraData()["Duration"]);

            $level = Server::getInstance()->getLevelByName($level);
            if(is_null($level) || $replay->getExtraData()["Version"] !== Loader::VERSION){
                $server->getLogger()->error("Something went wrong with " . $replayId . "...");
                return;
            }
            foreach($replay->getChunks() as $chunkXZ => $chunk) {
                $chunk = Chunk::fastDeserialize(base64_decode($chunk));
                $tiles = $chunk->getTiles();
                $level->setChunk($chunk->getX(), $chunk->getZ(), $chunk);
                foreach ($tiles as $tile) {
                    $tile->closed = false;
                    $tile->setLevel($level);
                    $level->addTile($tile);
                }

                foreach ($level->getChunkLoaders($chunk->getX(), $chunk->getZ()) as $chunkLoader) {
                    if ($chunkLoader instanceof Player) {
                        $chunkLoader->dataPacket(LevelChunkPacket::withoutCache($chunk->getX(), $chunk->getZ(), $chunk->getSubChunkSendCount(), $chunk->networkSerialize()));
                    }
                }
            }
            $level->stopTime();

            $replay->setLevel($level);
            $replay->setSpawn(Vector3Utils::fromString($replay->getExtraData()["Spawn"]));
            ReplayManager::getInstance()->addReplay($replay);

            ($callable)($replay);
        });
        return true;
    }
}