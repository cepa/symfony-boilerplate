<?php

namespace Core\Helper;

class ListParams
{
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    protected $sortings = [];
    protected $page = 0;
    protected $size = 10;

    public function addSorting(string $column, string $order = self::ORDER_ASC): ListParams
    {
        if (!in_array($order, [self::ORDER_ASC, self::ORDER_DESC])) {
            throw new \UnexpectedValueException("Sorting order must either be ASC or DESC");
        }
        $this->sortings[$column] = $order;
        return $this;
    }

    public function addSortings(array $sortings): ListParams
    {
        foreach ($sortings as $column => $order) {
            $this->addSorting($column, $order);
        }
        return $this;
    }

    public function getSortings(): array
    {
        return $this->sortings;
    }

    public function setPage(int $page): ListParams
    {
        $this->page = $page;
        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setSize(int $size): ListParams
    {
        $this->size = $size;
        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
