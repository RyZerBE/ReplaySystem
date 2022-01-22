<?php

namespace matze\replaysystem\recorder\command;

use matze\replaysystem\recorder\replay\Replay;
use matze\replaysystem\recorder\replay\ReplayManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use ryzerbe\core\RyZerBE;
use function implode;
use function is_null;

class ReplayCommand extends Command {

    public function __construct(){
        parent::__construct("replay", "Replay admin command", "", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player) return;

        if(empty($args[0])){
            $replay = ReplayManager::getInstance()->getReplayByLevel($sender->getLevel());
            if($replay === null) {
                $sender->sendMessage(RyZerBE::PREFIX.TextFormat::RED."No Replay recording!");
                return;
            }
            $sender->sendMessage(TextFormat::GOLD."ReplayID§7: ".TextFormat::WHITE.$replay->getId());
            return;
        }

        if(!$sender->hasPermission("replay.admin")) return;

        switch($args[0]) {
            case "record":
                $replay = new Replay($sender->getLevel());
                $replay->startRecording();
                $replay->setSpawn($sender->asVector3());
                $sender->sendMessage(RyZerBE::PREFIX.TextFormat::GREEN."Replay recording started!");
                $sender->sendMessage(TextFormat::GOLD."ReplayID: ".TextFormat::WHITE.$replay->getId());
                break;
            case "pause":
                $replay = ReplayManager::getInstance()->getReplayByLevel($sender->getLevel());
                if(is_null($replay)){
                    $sender->sendMessage(RyZerBE::PREFIX.TextFormat::RED."No Replay recording!");
                    return;
                }
                $replay->setRunning(false);
                $sender->sendMessage(RyZerBE::PREFIX.TextFormat::RED."Replay recording paused!");
                break;
            case "continue":
                $replay = ReplayManager::getInstance()->getReplayByLevel($sender->getLevel());
                if(is_null($replay)){
                    $sender->sendMessage(RyZerBE::PREFIX.TextFormat::RED."No Replay recording!");
                    return;
                }
                $replay->setRunning(true);
                $sender->sendMessage(RyZerBE::PREFIX.TextFormat::GREEN."Replay recording continued!");
                break;
            case "save":
                $replay = ReplayManager::getInstance()->getReplayByLevel($sender->getLevel());
                if(is_null($replay)){
                    $sender->sendMessage(RyZerBE::PREFIX.TextFormat::RED."No Replay recording!");
                    return;
                }
                $replay->stopRecording();
                $sender->sendMessage(RyZerBE::PREFIX.TextFormat::GREEN."Replay saved!");
                $sender->sendMessage(TextFormat::GOLD."ReplayID: ".TextFormat::WHITE.$replay->getId());
                break;
            default:
                $sender->sendMessage(implode("\n", [
                    TextFormat::GOLD."/replay ".TextFormat::RED."record".TextFormat::GRAY." - ".TextFormat::WHITE."Start replay recording",
                    TextFormat::GOLD."/replay ".TextFormat::RED."pause".TextFormat::GRAY." - ".TextFormat::WHITE."Pause replay recording",
                    TextFormat::GOLD."/replay ".TextFormat::RED."continue".TextFormat::GRAY." - ".TextFormat::WHITE."Continue paused replay recording",
                    TextFormat::GOLD."/replay ".TextFormat::RED."save".TextFormat::GRAY." - ".TextFormat::WHITE."Save recorded replay"
                ]));
                break;
        }
    }
}