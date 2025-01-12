<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interferces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class GuardianEntity extends VanillaEntity{

    const NETWORK_ID = self::GUARDIAN;

    public $width = 0.85;
    public $height = 0.85;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Guardian";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $prismarine_shard = ItemFactory::get(ItemIds::PRISMARINE_SHARD, 0, 1);
        ItemHelper::applySetCount($prismarine_shard, 0, 2);
        ItemHelper::applyLootingEnchant($this, $prismarine_shard);
         
        $fish = ItemFactory::get(ItemIds::RAW_FISH, 0, 1);
        ItemHelper::applyLootingEnchant($this, $fish);
         
        $prismarine_crystals = ItemFactory::get(ItemIds::PRISMARINE_CRYSTALS, 0, 1);
        ItemHelper::applyLootingEnchant($this, $prismarine_crystals);
        return [$prismarine_shard, $fish, $prismarine_crystals];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 10 : 0;
    }

    public function getClassification(): int{
        return EntityClassification::AQUATIC;
    }
}