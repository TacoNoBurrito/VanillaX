<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\Entity;;

class LingeringPotionEntity extends Entity{

    public $width = 0.25;
    public $height = 0.25;

    const NETWORK_ID = self::LINGERING_POTION;
}