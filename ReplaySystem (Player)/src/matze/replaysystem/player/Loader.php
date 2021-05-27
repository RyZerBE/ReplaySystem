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
use matze\replaysystem\player\listener\PlayerInteractListener;
use matze\replaysystem\player\listener\PlayerJoinListener;
use matze\replaysystem\player\scheduler\ReplayUpdateTask;
use matze\replaysystem\player\utils\Timings;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

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
            new EntityExplodeListener(),
            new PlayerJoinListener(),
            new PlayerInteractListener()
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
}