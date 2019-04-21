<?php

namespace Core\Tests\Helper;

use Core\Helper\RandomIdGenerator;
use PHPUnit\Framework\TestCase;

class RandomIdGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $x = RandomIdGenerator::generate();
        $this->assertEquals(10, strlen($x));
    }
}