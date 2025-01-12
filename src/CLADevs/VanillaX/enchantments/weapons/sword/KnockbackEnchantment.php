<?php

namespace CLADevs\VanillaX\enchantments\weapons\sword;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\KnockbackEnchantment as PMKnockbackEnchantment;

class KnockbackEnchantment extends PMKnockbackEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::KNOCKBACK, "%enchantment.knockback", self::RARITY_UNCOMMON, self::SLOT_SWORD, self::SLOT_NONE, 2);
    }
}