<?php

namespace matze\replaysystem\player;

use matze\replaysystem\player\action\ActionManager;
use matze\replaysystem\player\entity\ReplayArrow;
use matze\replaysystem\player\entity\ReplayEgg;
use matze\replaysystem\player\entity\ReplayEnderPearl;
use matze\replaysystem\player\entity\ReplayHuman;
use matze\replaysystem\player\entity\ReplayItemEntity;
use matze\replaysystem\player\entity\ReplaySnowball;
use matze\replaysystem\player\listener\EntityExplodeListener;
use matze\replaysystem\player\replay\Replay;
use matze\replaysystem\player\replay\ReplayManager;
use matze\replaysystem\player\scheduler\ReplayUpdateTask;
use matze\replaysystem\player\utils\Timings;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use function is_null;

class Loader extends PluginBase {

    /** @var Loader|null */
    private static $instance = null;
    /** @var Config|null */
    private static $settings = null;

    public function onEnable(): void {
        self::$instance = $this;

        $this->saveResource("/settings.yml");
        self::$settings = new Config($this->getDataFolder() . "/settings.yml");

        ActionManager::getInstance();

        $this->initListener();
        $this->initEntities();

        $this->getScheduler()->scheduleRepeatingTask(new ReplayUpdateTask(), 1);
    }

    public function onDisable(): void {
        foreach(Timings::getTimings() as $timing => $timings) {
            Server::getInstance()->getLogger()->info($timing . " > " . Timings::getAverageTimings($timing));
        }
    }

    /**
     * @return Loader|null
     */
    public static function getInstance(): ?Loader{
        return self::$instance;
    }

    /**
     * @return Config|null
     */
    public static function getSettings(): ?Config{
        return self::$settings;
    }

    private function initListener(): void {
        $listeners = [
            new EntityExplodeListener()
        ];
        foreach($listeners as $listener) {
            Server::getInstance()->getPluginManager()->registerEvents($listener, $this);
        }
    }

    private function initEntities(): void {
        $entities = [
            ReplayHuman::class,
            ReplayItemEntity::class,
            ReplayArrow::class,
            ReplayEnderPearl::class,
            ReplaySnowball::class,
            ReplayEgg::class
        ];
        foreach($entities as $entity) {
            Entity::registerEntity($entity, true);
        }
    }

    /**
     * @param CommandSender $sender
     * @param Command $command
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) return false;
        switch($command->getName()) {
            case "replay": {
                if(!isset($args[0])) break;

                $replayId = $args[0];
                $replay = ReplayManager::getInstance()->getReplay($replayId);
                if(!is_null($replay)) {
                    if(isset($args[1])) {
                        $replay->setTicksPerTick((int)$args[1]);
                        break;
                    }
                    $replay->setPlayType(($replay->getPlayType() === Replay::PLAY_TYPE_FORWARD ? Replay::PLAY_TYPE_BACKWARDS : Replay::PLAY_TYPE_FORWARD));
                    break;
                }
                $sender = $sender->getName();
                ReplayManager::getInstance()->playReplay($replayId, function(Replay $replay) use ($sender): void {
                    $sender = Server::getInstance()->getPlayerExact($sender);
                    if(is_null($sender)) return;
                    $sender->teleport($replay->getSpawn());
                });
                break;
            }
        }
        return true;
    }
}