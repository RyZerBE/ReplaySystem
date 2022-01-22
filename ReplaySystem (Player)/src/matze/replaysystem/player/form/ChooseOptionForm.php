<?php


namespace matze\replaysystem\player\form;


use BauboLP\Cloud\CloudBridge;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;

class ChooseOptionForm
{

    public static function open(Player $player): void {
        $form = new SimpleForm(function (Player $player, $data): void{
            if($data === null){
                CloudBridge::getCloudProvider()->dispatchProxyCommand($player->getName(), "hub");
                return;
            }

            if($data === "search") {
                PlayReplayForm::open($player);
            }
        });
        $form->setTitle("§lReplay");
        $form->addButton("Search Replay", 0, "textures/ui/magnifyingGlass.png", "search");
        $form->addButton("Last Replays", 0, "textures/ui/book_edit_hover.png", "choose");
        $player->sendForm($form);
    }
}