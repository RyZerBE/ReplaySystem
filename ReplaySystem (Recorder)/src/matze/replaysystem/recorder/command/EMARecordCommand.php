<?php

namespace matze\replaysystem\recorder\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class EMARecordCommand extends Command {

    public function __construct(){
        $this->setPermission("ryzer.admin");
        parent::__construct("ema", "EMA", "", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$this->testPermission($sender)) return;
        if(!isset($args[0])) return;

        $player = Server::getInstance()->getPlayer($args[0]);
        if($player === null) return;

        $player->setNameTag(TextFormat::BLUE."Mysterious Player");
        $player->setDisplayName(TextFormat::BLUE."Mysterious Player");
    }
}