<?php

namespace Core\Helper;

use Generic\Collection\Collection;
use Generic\Entity\PopulateInterface;
use Generic\Entity\ToArrayInterface;

class ListContainer implements PopulateInterface, ToArrayInterface
{
    /**
     * @var Collection
     */
    protected $items;

    /**
     * @var integer
     */
    protected $total;

    public function __construct($items = [], $total = 0)
    {
        $this->items = new Collection($items);
        $this->total = ($this->items->count() > $total ? $this->items->count() : $total);
    }

    public function addItem($item): ListContainer
    {
        $this->items->add($item);
        return $this;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function getCount(): int
    {
        return (int) $this->items->count();
    }

    public function getTotal(): int
    {
        return (int) $this->total;
    }

    public function setTotal(int $total): ListContainer
    {
        if ($this->items->count() > $total) {
            throw new \InvalidArgumentException("Total slice count must be bigger than or equal to item's count");
        }
        $this->total = $total;
        return $this;
    }

    public function populate($items): ListContainer
    {
        $this->items->populate($items);
        return $this;
    }

    public function toArray(): array
    {
        return [
            'items' => $this->items->toArray(),
            'count' => $this->items->count(),
            'total' => $this->total
        ];
    }
}
