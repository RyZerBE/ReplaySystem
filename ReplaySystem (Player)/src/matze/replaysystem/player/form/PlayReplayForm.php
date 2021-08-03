<?php

namespace matze\replaysystem\player\form;

use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Provider\CloudProvider;
use baubolp\core\Ryzer;
use jojoe77777\FormAPI\CustomForm;
use matze\replaysystem\player\Loader;
use matze\replaysystem\player\replay\Replay;
use matze\replaysystem\player\replay\ReplayManager;
use matze\replaysystem\player\scheduler\ReplayLoadTask;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\Player;
use pocketmine\Server;
use function is_null;

class PlayReplayForm {

    /**
     * @param Player $player
     * @param string|null $errorMessage
     */
    public static function open(Player $player, ?string $errorMessage = null): void {
        $form = new CustomForm(function(Player $player, $data): void {
            if($data === null) {
                ChooseOptionForm::open($player);
                return;
            }
            $replayId = $data["replayId"];
            $name = $player->getName();
            if(!ReplayManager::getInstance()->playReplay($replayId, function(Replay $replay) use ($name): void {
                $player = Server::getInstance()->getPlayerExact($name);
                if(is_null($player)) return;
                Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new ReplayLoadTask($player, $replay), 2);
            })) {
                ChooseOptionForm::open($player);
                return;
            }
            $player->sendTitle("§a§lLoad replay...", "", 20, 40, 0);
            $player->getLevel()->addSound(new AnvilFallSound($player));
        });
        $form->setTitle("§lReplay");
        if(!is_null($errorMessage)) $form->addLabel($errorMessage);
        $form->addInput("Replay Id", "", "", "replayId");
        $form->sendToPlayer($player);
    }
}