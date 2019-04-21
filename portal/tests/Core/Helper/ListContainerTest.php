<?php

namespace Core\Tests\Helper;

use Core\Helper\ListContainer;
use PHPUnit\Framework\TestCase;

class ListContainerTest extends TestCase
{
    public function testList()
    {
        $list = new ListContainer();
        $this->assertEquals(0, $list->getCount());
        $this->assertEquals(0, $list->getTotal());
        $this->assertEquals(0, $list->getItems()->count());

        $list = new ListContainer(['aaa', 'bbb', 'ccc']);
        $this->assertEquals(3, $list->getCount());
        $this->assertEquals(3, $list->getTotal());
        $this->assertEquals(3, $list->getItems()->count());
    }

    public function testThrowExceptionWhenTotalIsSmallerThanCount()
    {
        $this->expectException(\InvalidArgumentException::class);
        $list = new ListContainer([1, 2, 3]);
        $list->setTotal(0);
    }
}