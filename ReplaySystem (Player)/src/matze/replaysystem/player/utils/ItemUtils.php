<?php

namespace matze\replaysystem\player\utils;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\StringTag;
use function explode;
use function implode;

class ItemUtils {

    /**
     * @param string $item
     * @return Item
     */
    public static function fromString(string $item): Item {
        $item = explode(":", $item);
        $result = ItemFactory::get(($item[0] ?? 0), ($item[1] ?? 0), ($item[3] ?? 1));
        $enchanted = (bool)($item[2] ?? false);
        if($enchanted) {
            $result->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING)));
        }
        return $result;
    }

    /**
     * @param Item $item
     * @return string
     */
    public static function toString(Item $item): string {
        return implode(":", [$item->getId(), $item->getDamage(), (int)$item->hasEnchantments(), $item->getCount()]);
    }

    /**
     * @param Item $item
     * @param string $tag
     * @param string $tagName
     * @return Item
     */
    public static function addItemTag(Item $item, string $tag, string $tagName): Item {
        $nbt = $item->getNamedTag();
        $nbt->setString($tagName, $tag, true);
        $item->setCompoundTag($nbt);
        return $item;
    }

    /**
     * @param Item $item
     * @param array $tags
     * @return Item
     */
    public static function addItemTags(Item $item, array $tags): Item {
        foreach ($tags as $key => $value) {
            $item = self::addItemTag($item, $value, $key);
        }
        return $item;
    }

    /**
     * @param Item $item
     * @param string $tagName
     * @return bool
     */
    public static function hasItemTag(Item $item, string $tagName): bool {
        $nbt = $item->getNamedTag();
        return $nbt->hasTag($tagName, StringTag::class);
    }

    /**
     * @param Item $item
     * @param string $tagName
     * @return string
     */
    public static function getItemTag(Item $item, string $tagName): string {
        $nbt = $item->getNamedTag();
        return $nbt->getString($tagName);
    }

    /**
     * @param Item $item
     * @param string $tagName
     * @return Item
     */
    public static function removeItemTag(Item $item, string $tagName): Item {
        if(!self::hasItemTag($item, $tagName)) {
            return $item;
        }
        $nbt = $item->getNamedTag();
        $nbt->removeTag($tagName);
        $item->setCompoundTag($nbt);
        return $item;
    }

    /**
     * @param Item $item
     * @param array $enchantments
     * @return Item
     */
    public static function addEnchantments(Item $item, array $enchantments): Item {
        foreach ($enchantments as $enchantmentId => $enchantmentLevel) {
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($enchantmentId), $enchantmentLevel));
        }
        return $item;
    }
}