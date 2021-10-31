<?php


namespace matze\replaysystem\player\form;


use jojoe77777\FormAPI\SimpleForm;
use matze\replaysystem\player\Loader;
use matze\replaysystem\player\replay\Replay;
use matze\replaysystem\player\replay\ReplayManager;
use matze\replaysystem\player\scheduler\ReplayLoadTask;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class ChooseReplayForm
{

    public static function open(Player $player, array $ids)
    {
        $form = new SimpleForm(function (Player $player, $data): void{
            if($data === null) {
                ChooseOptionForm::open($player);
                return;
            }

            $replayId = $data;
            $name = $player->getName();
            if(!ReplayManager::getInstance()->playReplay($replayId, function(Replay $replay) use ($name): void {
                $player = Server::getInstance()->getPlayerExact($name);
                if(is_null($player)) return;
                $player->setGamemode(3);
                Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new ReplayLoadTask($player, $replay), 2);
            })) {
                PlayReplayForm::open($player, "§cReplay does not exist!");
                return;
            }
        });

        $path = Loader::getSettings()->get("path");
        foreach ($ids as $id) {
            if(is_file($path.$id.".dat"))
            $form->addButton(TextFormat::RED.$id."\n".TextFormat::DARK_GRAY."[".TextFormat::GOLD.date("Y-m-d H:i:s", filemtime($path.$id.".dat")).TextFormat::DARK_GRAY."]", -1, "", $id);
        }

        $form->sendToPlayer($player);
    }
}