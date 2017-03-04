<?php

namespace GildedRose;

/**
 * Hi and welcome to team Gilded Rose.
 *
 * As you know, we are a small inn with a prime location in a prominent city
 * ran by a friendly innkeeper named Allison. We also buy and sell only the
 * finest goods. Unfortunately, our goods are constantly degrading in quality
 * as they approach their sell by date. We have a system in place that updates
 * our inventory for us. It was developed by a no-nonsense type named Leeroy,
 * who has moved on to new adventures. Your task is to add the new feature to
 * our system so that we can begin selling a new category of items. First an
 * introduction to our system:
 *
 * - All items have a SellIn value which denotes the number of days we have to sell the item
 * - All items have a Quality value which denotes how valuable the item is
 * - At the end of each day our system lowers both values for every item
 *
 * Pretty simple, right? Well this is where it gets interesting:
 *
 * - Once the sell by date has passed, Quality degrades twice as fast
 * - The Quality of an item is never negative
 * - "Aged Brie" actually increases in Quality the older it gets
 * - The Quality of an item is never more than 50
 * - "Sulfuras", being a legendary item, never has to be sold or decreases in Quality
 * - "Backstage passes", like aged brie, increases in Quality as it's SellIn
 *   value approaches; Quality increases by 2 when there are 10 days or less and
 *   by 3 when there are 5 days or less but Quality drops to 0 after the concert
 *
 * We have recently signed a supplier of conjured items. This requires an
 * update to our system:
 *
 * - "Conjured" items degrade in Quality twice as fast as normal items
 *
 * Feel free to make any changes to the UpdateQuality method and add any new
 * code as long as everything still works correctly. However, do not alter the
 * Item class or Items property as those belong to the goblin in the corner who
 * will insta-rage and one-shot you as he doesn't believe in shared code
 * ownership (you can make the UpdateQuality method and Items property static
 * if you like, we'll cover for you).
 *
 * Just for clarification, an item can never have its Quality increase above
 * 50, however "Sulfuras" is a legendary item and as such its Quality is 80 and
 * it never alters.
 */
class Program
{
    private $items = array();

    public static function Main($days = 1)
    {
        echo "OMGHAI!\n";

        $app = new Program(array(
              new Item(array( 'name' => "+5 Dexterity Vest",'sellIn' => 10,'quality' => 20)),
              new Item(array( 'name' => "Aged Brie",'sellIn' => 2,'quality' => 0)),
              new Item(array( 'name' => "Elixir of the Mongoose",'sellIn' => 5,'quality' => 7)),
              new Item(array( 'name' => "Sulfuras, Hand of Ragnaros",'sellIn' => 0,'quality' => 80)),
              new Item(array(
                     'name' => "Backstage passes to a TAFKAL80ETC concert",
                     'sellIn' => 15,
                     'quality' => 20
              )),
              new Item(array('name' => "Conjured Mana Cake",'sellIn' => 3,'quality' => 6)),
        ));

        for ($i = 1; $i <= $days; $i++) {
            $app->UpdateQuality();
            echo "-------- day $i --------\n";
            echo sprintf("%50s - %7s - %7s\n", "Name", "SellIn", "Quality");
            foreach ($app->items as $item) {
                echo sprintf("%50s - %7d - %7d\n", $item->name, $item->sellIn, $item->quality);
            }
        }
    }

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function UpdateQuality()
    {
        for ($i = 0; $i < count($this->items); $i++) {
            switch ($this->items[$i]->name) {
                case "Aged Brie":
                    (new AgedItem($this->items[$i]))->update();
                    return;
                case "Sulfuras, Hand of Ragnaros":
                    (new SulfurasItem($this->items[$i]))->update();
                    return;
                case "Backstage passes to a TAFKAL80ETC concert":
                    (new BackstagePassItem($this->items[$i]))->update();
                    return;
                default:
                    (new NormalItem($this->items[$i]))->update();
                    return;
            }
        }
    }
}

class AgedItem
{
    private $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    public function update()
    {
        $this->item->sellIn -= 1;

        if ($this->item->quality == 50) {
            return;
        }

        $this->item->quality += 1;

        if ($this->item->sellIn < 0 && $this->item->quality < 50) {
            $this->item->quality += 1;
        }
    }
}

class SulfurasItem
{
    public function update()
    {
        // Sulfuras never has to be sold and never changes quality
    }
}

class BackstagePassItem
{
    private $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    public function update()
    {
        $this->item->sellIn -= 1;

        if ($this->item->sellIn < 0) {
            $this->item->quality = 0;
            return;
        }

        if ($this->item->quality == 50) {
            return;
        }

        $this->item->quality += 1;

        if ($this->item->sellIn < 10 && $this->item->quality < 50) {
            $this->item->quality += 1;
        }

        if ($this->item->sellIn < 5 && $this->item->quality < 50) {
            $this->item->quality += 1;
        }
    }
}

class NormalItem
{
    private $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    public function update()
    {
        $this->item->sellIn -= 1;

        if ($this->item->quality == 0) {
            return;
        }

        $this->item->quality -= 1;

        if ($this->item->sellIn < 0) {
            $this->item->quality -= 1;
        }
    }
}
