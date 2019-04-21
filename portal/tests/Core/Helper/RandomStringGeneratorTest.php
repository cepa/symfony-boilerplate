<?php

namespace Core\Tests\Helper;

use Core\Helper\RandomStringGenerator;
use PHPUnit\Framework\TestCase;

class RandomStringGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $x = RandomStringGenerator::generate(16);
        $this->assertEquals(16, strlen($x));
    }
}