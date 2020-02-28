<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyCustomEnchants\enchants\tools\axes;

use DaPigGuy\PiggyCustomEnchants\enchants\CustomEnchant;
use DaPigGuy\PiggyCustomEnchants\enchants\ReactiveEnchantment;
use DaPigGuy\PiggyCustomEnchants\enchants\traits\tools\BlockBreakingTrait;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Event;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\player\Player;

/**
 * Class LumberjackEnchant
 * @package DaPigGuy\PiggyCustomEnchants\enchants\tools\axes
 */
class LumberjackEnchant extends ReactiveEnchantment
{
    use BlockBreakingTrait;

    /** @var string */
    public $name = "Lumberjack";
    /** @var int */
    public $maxLevel = 1;

    /**
     * @param Player $player
     * @param Item $item
     * @param Inventory $inventory
     * @param int $slot
     * @param Event $event
     * @param int $level
     * @param int $stack
     */
    public function breakBlocks(Player $player, Item $item, Inventory $inventory, int $slot, Event $event, int $level, int $stack): void
    {
        if ($event instanceof BlockBreakEvent) {
            $block = $event->getBlock();
            if ($player->isSneaking()) {
                if ($block->getId() == BlockLegacyIds::LOG || $block->getId() == BlockLegacyIds::LOG2) {
                    $this->breakTree($block, $player);
                }
            }
            $event->setInstaBreak(true);
        }
    }

    /**
     * @param Block $block
     * @param Player $player
     * @param int $mined
     */
    public function breakTree(Block $block, Player $player, int $mined = 0): void
    {
        $item = $player->getInventory()->getItemInHand();
        for ($i = 0; $i <= 5; $i++) {
            if ($mined > 800) {
                break;
            }
            $side = $block->getSide($i);
            if ($side->getId() !== BlockLegacyIds::LOG && $side->getId() !== BlockLegacyIds::LOG2) {
                continue;
            }
            $this->setCooldown($player, 1);
            $player->getWorld()->useBreakOn($side->getPos(), $item, $player);
            $mined++;
            $this->breakTree($side, $player, $mined);
        }
    }

    /**
     * @return int
     */
    public function getItemType(): int
    {
        return CustomEnchant::ITEM_TYPE_AXE;
    }
}