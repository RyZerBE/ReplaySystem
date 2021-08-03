<?php


namespace matze\replaysystem\player\form;


use BauboLP\Cloud\CloudBridge;
use BauboLP\Core\Provider\AsyncExecutor;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\Server;

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
            }else if($data == "choose") {
                $playerName = $player->getName();
                AsyncExecutor::submitMySQLAsyncTask("RyzerCore", function (\mysqli $mysqli) use ($playerName){
                    $res = $mysqli->query("SELECT * FROM `replayList` WHERE playername='$playerName'");
                    if($res->num_rows > 0) {
                        while ($data = $res->fetch_assoc()) {
                            $ids = explode(";", $data["replays"]);
                            return $ids;
                        }
                    }

                    return [];
                }, function (Server $server, $result) use ($playerName) {
                    if(($player = $server->getPlayerExact($playerName)) != null){
                        ChooseReplayForm::open($player, (is_array($result) == true) ? $result : []);
                    }
                });
            }
        });
        $form->setTitle("§lReplay");
        $form->addButton("Search Replay", 0, "textures/ui/magnifyingGlass.png", "search");
        $form->addButton("Last Replays", 0, "textures/ui/book_edit_hover.png", "choose");
        $player->sendForm($form);
    }
}