<?php

namespace matze\replaysystem\recorder;

use matze\replaysystem\recorder\listener\BlockBreakListener;
use matze\replaysystem\recorder\listener\BlockPlaceListener;
use matze\replaysystem\recorder\listener\ChunkLoadListener;
use matze\replaysystem\recorder\listener\DataPacketSendListener;
use matze\replaysystem\recorder\listener\EntityDespawnListener;
use matze\replaysystem\recorder\listener\EntityExplodeListener;
use matze\replaysystem\recorder\listener\EntityLevelChangeListener;
use matze\replaysystem\recorder\listener\EntitySpawnListener;
use matze\replaysystem\recorder\listener\InventoryTransactionListener;
use matze\replaysystem\recorder\listener\PlayerAnimationListener;
use matze\replaysystem\recorder\listener\PlayerGameModeChangeListener;
use matze\replaysystem\recorder\listener\PlayerItemHeldListener;
use matze\replaysystem\recorder\listener\PlayerJoinListener;
use matze\replaysystem\recorder\listener\PlayerMoveListener;
use matze\replaysystem\recorder\listener\PlayerQuitListener;
use matze\replaysystem\recorder\listener\PlayerSneakListener;
use matze\replaysystem\recorder\replay\Replay;
use matze\replaysystem\recorder\replay\ReplayManager;
use matze\replaysystem\recorder\scheduler\ReplayUpdateTask;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use function is_null;

class Loader extends PluginBase {
    public const VERSION = "1.1.0";

    /** @var Loader|null */
    private static $instance = null;
    /** @var Config|null */
    private static $settings = null;

    public function onEnable(): void {
        self::$instance = $this;

        $this->saveResource("/settings.yml");
        self::$settings = new Config($this->getDataFolder() . "/settings.yml");

        ReplayManager::getInstance();

        $this->initListener();

        $this->getScheduler()->scheduleRepeatingTask(new ReplayUpdateTask(), 1);
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
            new ChunkLoadListener(),
            new DataPacketSendListener(),
            new PlayerMoveListener(),
            new EntitySpawnListener(),
            new PlayerSneakListener(),
            new EntityDespawnListener(),
            new PlayerJoinListener(),
            new PlayerQuitListener(),
            new EntityLevelChangeListener(),
            new BlockPlaceListener(),
            new BlockBreakListener(),
            new EntityExplodeListener(),
            new PlayerAnimationListener(),
            new InventoryTransactionListener(),
            new PlayerItemHeldListener(),
            new PlayerGameModeChangeListener()
        ];
        foreach($listeners as $listener) {
            Server::getInstance()->getPluginManager()->registerEvents($listener, $this);
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
        if(!$sender instanceof Player || !$sender->isOp()) return false;
        switch($command->getName()) {
            case "replaytest": {
                $replay = ReplayManager::getInstance()->getReplayByLevel($sender->getLevelNonNull());
                if(is_null($replay)) {
                    $replay = new Replay($sender->getLevelNonNull());
                    $replay->startRecording();
                    $replay->setSpawn($sender);
                    break;
                }
                $replay->stopRecording(true, ($replay->getTick() - (20 * 30)));
                break;
            }
        }
        return true;
    }
}