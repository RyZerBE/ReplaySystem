<?php

namespace matze\replaysystem\player\scheduler;

use matze\replaysystem\player\Loader;
use matze\replaysystem\player\replay\Replay;
use matze\replaysystem\player\utils\ItemUtils;
use pocketmine\item\Item;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use function array_rand;
use function array_search;
use function mt_rand;

class ReplayLoadTask extends Task {

    /** @var Player */
    private $player;
    /** @var Replay */
    private $replay;
    /** @var bool */
    private $willFail;
    /** @var int  */
    private $progress = 0;

    /**
     * ReplayLoadTask constructor.
     * @param Player $player
     * @param Replay $replay
     */
    public function __construct(Player $player, Replay $replay){
        $this->player = $player;
        $this->replay = $replay;
        $this->willFail = (mt_rand(1, 3) === 1);
    }

    /**
     * @param int $currentTick
     *
     * Do not mind this trash :)
     */
    public function onRun(int $currentTick): void {
        $player = $this->player;
        $replay = $this->replay;
        if(!$player->isConnected()) {
            $this->cancel();
            return;
        }
        switch($this->progress) {
            case 1: {
                if($this->willFail) {
                    $player->sendTitle("§c§lERROR", "", 20, 100, 0);
                } else {
                    $player->sendTitle("§a§lLoaded Replay", "", 20, 100, 0);
                }
                $player->getLevel()->addSound(new AnvilFallSound($player));
                break;
            }
            case 20: {
                if($this->willFail) {
                    $player->sendTitle("§a§lJust joking...", "", 20, 100, 0);
                    $player->getLevel()->addSound(new AnvilFallSound($player));
                }
                break;
            }
            case 30: {
                if($this->willFail) {
                    $this->willFail = false;
                    $this->progress = 0;
                } else {
                    $player->sendTitle("§a§lGenerate level...", "", 20, 100, 0);
                    $player->getLevel()->addSound(new AnvilFallSound($player));
                }
                break;
            }
            case 50: {
                $texts = [//Todo: Add more texts
                    "Drink coffee...",
                    "Take a break...",
                    "Feed the ducks...",
                    "Clean the garbage...",
                    "Subscribe to Chillihero...",
                    "Hug trees...",
                    "Follow @ryzerbe\n on Twitter..."
                ];
                $player->sendTitle("§a§l" . $texts[array_rand($texts)], "", 20, 100, 0);
                $player->getLevel()->addSound(new AnvilFallSound($player));
                break;
            }
            case 90: {
                $player->sendTitle("§a§lDone?", "", 20, 100, 0);
                $player->getLevel()->addSound(new AnvilFallSound($player));
                break;
            }
            case 100: {
                $player->sendTitle("§a§lEnjoy!", "", 20, 40, 0);
                $player->getLevel()->addSound(new AnvilFallSound($player));
                $player->removeAllEffects();
                $player->setImmobile(false);

                $player->getInventory()->setContents([
                    0 => ItemUtils::addItemTag(Item::get(Item::ARROW)->setCustomName("§r§aPlay Backwards"), "play_backwards", "replay_item"),
                    1 => ItemUtils::addItemTag(Item::get(Item::MAGMA_CREAM)->setCustomName("§r§aPause Replay"), "pause_replay", "replay_item"),
                    2 => ItemUtils::addItemTag(Item::get(Item::ARROW)->setCustomName("§r§aPlay Forward"), "play_forward", "replay_item"),
                    7 => ItemUtils::addItemTag(Item::get(Item::MAGMA_CREAM)->setCustomName("§r§aSlowmode (§cOFF§a)"), "slowmode", "replay_item"),
                    8 => ItemUtils::addItemTag(Item::get(Item::COMPASS)->setCustomName("§r§aTeleporter"), "teleporter", "replay_item"),
                ]);

                $player->teleport($replay->getSpawn());
                $this->cancel();

                $replay->setRunning(true);
                break;
            }
        }

        if(!$this->willFail) $player->sendTip("§r§aProgress§7: §6" . $this->progress . "%");
        $this->progress++;
    }

    private function cancel(): void {
        Loader::getInstance()->getScheduler()->cancelTask($this->getTaskId());
    }
}