<?php

namespace matze\replaysystem\player\form;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\Server;
use function is_null;

class TeleporterForm {

    /**
     * @param Player $player
     */
    public static function open(Player $player): void {
        $form = new SimpleForm(function(Player $player, $data): void {
            if(is_null($data) || $data === "close") return;
            $entity = Server::getInstance()->findEntity($data);
            if(is_null($entity) || $entity->isInvisible()) return;
            $player->teleport($entity);
        });
        $form->setTitle("§lTeleporter");
        foreach($player->getLevel()->getEntities() as $entity) {
            if(!(bool)$entity->namedtag->getInt("IsPlayer", 0) || $entity->isInvisible()) continue;
            $form->addButton($entity->getNameTag(), -1, "", $entity->getId());
        }
        $form->addButton("§lClose", -1, "", "close");
        $form->sendToPlayer($player);
    }
}