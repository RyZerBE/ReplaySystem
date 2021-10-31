<?php

namespace matze\replaysystem\player\action\types;

use matze\replaysystem\player\action\Action;
use matze\replaysystem\player\action\ActionIds;
use matze\replaysystem\player\entity\ReplayArrow;
use matze\replaysystem\player\entity\ReplayEgg;
use matze\replaysystem\player\entity\ReplayEnderPearl;
use matze\replaysystem\player\entity\ReplayHuman;
use matze\replaysystem\player\entity\ReplayItemEntity;
use matze\replaysystem\player\entity\ReplaySnowball;
use matze\replaysystem\player\replay\Replay;
use matze\replaysystem\player\utils\ItemUtils;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use function base64_decode;
use function is_null;
use function json_decode;

class EntitySpawnAction extends Action {

    /**
     * @return string
     */
    public function getName(): string{
        return "EntitySpawnAction";
    }

    public function getId(): int{
        return ActionIds::ENTITY_SPAWN_ACTION;
    }

    /** @var int */
    public $networkID;
    /** @var string */
    public $entityID;
    /** @var float */
    public $x;
    /** @var float */
    public $y;
    /** @var float */
    public $z;
    /** @var float */
    public $yaw;
    /** @var float */
    public $pitch;
    /** @var string */
    public $nametag;
    /** @var string */
    public $scoreTag;
    /** @var mixed|null */
    public $skin;
    /** @var string */
    public $item;
    /** @var bool */
    public $isPlayer;

    /**
     * @param array $data
     * @return $this
     */
    public function decode(array $data): Action{
        $action = new EntitySpawnAction();
        $action->networkID = $data["NetworkID"];
        $action->entityID = $data["EntityId"];
        $action->x = $data["X"];
        $action->y = $data["Y"];
        $action->z = $data["Z"];
        $action->yaw = $data["Yaw"];
        $action->pitch = $data["Pitch"];
        $action->nametag = $data["Nametag"];
        $action->scoreTag = $data["ScoreTag"];
        $action->skin = ($data["Skin"] !== "null" ? json_decode($data["Skin"], true) : null);
        $action->item = $data["Item"];
        $action->isPlayer = $data["IsPlayer"];
        return $action;
    }

    /**
     * @param Replay $replay
     * @param Action|EntitySpawnAction $action
     * @param int $playType
     */
    public function handle(Replay $replay, Action $action, int $playType): void{
        $entity = $replay->findEntity($action->entityID);
        switch($playType) {
            case Replay::PLAY_TYPE_FORWARD: {
                if(!is_null($entity)){
                    $entity->setInvisible(false);
                    $entity->spawnToAll();
                    return;
                }
                $nbt = Entity::createBaseNBT(new Vector3($action->x, $action->y, $action->z), null, $action->yaw, $action->pitch);
                $nbt->setInt("EntityId", $action->entityID);
                $nbt->setInt("IsPlayer", (int)$action->isPlayer);
                $entity = null;
                switch($action->networkID) {
                    case -1: {}
                    case EntityIds::PLAYER: {
                        if(is_null($action->skin)) break;
                        $nbt->setTag(new CompoundTag("Skin", [
                            new StringTag("Name", $action->skin["SkinId"]),
                            new ByteArrayTag("Data", base64_decode($action->skin["SkinData"])),
                            new ByteArrayTag("CapeData", base64_decode($action->skin["CapeData"])),
                            new StringTag("GeometryName", $action->skin["GeometryName"]),
                            new ByteArrayTag("GeometryData", base64_decode($action->skin["GeometryData"]))
                        ]));
                        $entity = new ReplayHuman($replay->getLevel(), $nbt);
                        break;
                    }
                    case EntityIds::ARROW: {
                        $entity = new ReplayArrow($replay->getLevel(), $nbt);
                        break;
                    }
                    case EntityIds::ENDER_PEARL: {
                        $entity = new ReplayEnderPearl($replay->getLevel(), $nbt);
                        break;
                    }
                    case EntityIds::SNOWBALL: {
                        $entity = new ReplaySnowball($replay->getLevel(), $nbt);
                        break;
                    }
                    case EntityIds::EGG: {
                        $entity = new ReplayEgg($replay->getLevel(), $nbt);
                        break;
                    }
                    case EntityIds::ITEM: {
                        $item = ItemUtils::fromString($action->item);
                        $itemTag = $item->nbtSerialize();
                        $itemTag->setName("Item");
                        if(is_null($itemTag)) break;
                        $nbt->setTag($itemTag);
                        $entity = new ReplayItemEntity($replay->getLevel(), $nbt);
                        break;
                    }
                    default: {
                        //$entity = Entity::createEntity($action->networkID, $replay->getLevel(), $nbt);
                    }
                }
                if(is_null($entity)) return;
                $entity->setNameTag($action->nametag);
                $entity->setScoreTag(($action->scoreTag ?? ""));
                $entity->spawnToAll();
                $entity->setInvisible(false);
                break;
            }
            case Replay::PLAY_TYPE_BACKWARDS: {
                if(is_null($entity)) return;
                $entity->setInvisible(true);
                $entity->despawnFromAll();
                break;
            }
        }
    }
}