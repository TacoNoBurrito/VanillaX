<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\Entity;

class ShulkerBulletEntity extends Entity{

    public $width = 0.625;
    public $height = 0.625;

    const NETWORK_ID = self::SHULKER_BULLET;
}