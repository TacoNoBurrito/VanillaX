<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\items\types\ShieldItem;
use CLADevs\VanillaX\network\gamerules\GameRule;
use CLADevs\VanillaX\VanillaX;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;

class EntityListener implements Listener{

    public function onDamage(EntityDamageEvent $event): void{
        if(!$event->isCancelled()){
            $entity = $event->getEntity();

            VanillaX::getInstance()->getEnchantmentManager()->handleDamage($event); /** Enchantment Damage */
            switch($event->getCause()){
                case EntityDamageEvent::CAUSE_FALL:
                    if($this->handleFallDamage($entity)) $event->setCancelled();
                    break;
                case EntityDamageEvent::CAUSE_DROWNING:
                    /** If gamerule 'drowningDamage' is not on then cancel it */
                    if(!GameRule::getGameRuleValue(GameRule::DROWNING_DAMAGE, $entity->getLevel())){
                        $event->setCancelled();
                    }
                    break;
                case EntityDamageEvent::CAUSE_FIRE:
                    /** If gamerule 'fireDamage' is not on then cancel it */
                    if(!GameRule::getGameRuleValue(GameRule::FIRE_DAMAGE, $entity->getLevel())){
                        $event->setCancelled();
                    }
                    break;
                case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
                    if($event instanceof EntityDamageByEntityEvent && $entity instanceof Player){
                        /** If gamerule 'pvp' is not on then cancel it */
                        if(!GameRule::getGameRuleValue(GameRule::PVP, $entity->getLevel())){
                            $event->setCancelled();
                            return;
                        }
                        $item = $entity->getInventory()->getItemInHand();
                        if($item instanceof ShieldItem && $entity->isSneaking() && $this->handleShieldDamage($event->getDamager(), $entity, $item)){
                            $event->setCancelled();
                        }
                    }
                    break;
            }
        }
    }

    public function onRegenerateHealth(EntityRegainHealthEvent $event): void{
        if(!$event->isCancelled() && !GameRule::getGameRuleValue(GameRule::NATURAL_REGENERATION, $event->getEntity()->getLevel())){
            $event->setCancelled();
        }
    }

    public function onEntitySpawn(EntitySpawnEvent $event): void{
        $entity = $event->getEntity();

        if($entity instanceof PrimedTNT && !GameRule::getGameRuleValue(GameRule::TNT_EXPLODES, $entity->getLevel())){
            $entity->flagForDespawn();
        }
    }

    private function handleShieldDamage(Entity $damager, Entity $entity, Item &$item): bool{
        if($damager instanceof Living && $damager->getDirection() !== $entity->getDirection()){
            $item->applyDamage(1);
            $entity->getInventory()->setItemInHand($item);
            $entity->getLevel()->broadcastLevelSoundEvent($entity, LevelSoundEventPacket::SOUND_ITEM_SHIELD_BLOCK);
            $damager->knockBack($entity, 0, $damager->x - $entity->x, $damager->z - $entity->z, 0.5);
            return true;
        }
        return false;
    }

    private function handleFallDamage(Entity $entity): bool{
        $value = false;

        /** If gamerule 'fallDamage' is not on then cancel it */
        if(!GameRule::getGameRuleValue(GameRule::FALL_DAMAGE, $entity->getLevel())){
            $value = true;
        }

        /** Elytra */
        if($entity instanceof Player){
            $session = VanillaX::getInstance()->getSessionManager()->get($entity);

            if($session->isGliding()){
                $value = true;
            }else{
                if(($end = $session->getEndGlideTime()) !== null && ($start = $session->getStartGlideTime()) !== null){
                    if(($end - $start) < 3){
                        $value = true;
                    }
                }
            }
        }
        return $value;
    }
}