<?php

namespace Core\Tests\Helper;

use Core\Helper\ListParams;
use PHPUnit\Framework\TestCase;

class ListParamsTest extends TestCase
{
    public function testListParams()
    {
        $params = new ListParams();
        $this->assertEquals(0, $params->getPage());
        $this->assertEquals(10, $params->getSize());
        $this->assertEquals(0, count($params->getSortings()));

        $params
            ->setPage(123)
            ->setSize(678)
            ->addSortings([
                'foo' => ListParams::ORDER_ASC,
                'bar' => ListParams::ORDER_DESC
            ])
            ;
        $this->assertEquals(123, $params->getPage());
        $this->assertEquals(678, $params->getSize());
        $this->assertEquals(2, count($params->getSortings()));
    }

    public function testListParamsInvalidOrder()
    {
        $this->expectException(\UnexpectedValueException::class);
        $params = new ListParams();
        $params->addSorting('xxx', 'yyy');
    }
}