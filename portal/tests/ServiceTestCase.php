<?php

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class ServiceTestCase extends KernelTestCase
{
    protected $ed;
    protected $em;

    public function setUp(): void
    {
        parent::setUp();
        static::bootKernel();
        $this->ed = self::$kernel->getContainer()->get('event_dispatcher');
        $this->em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->em->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);
    }

    public function tearDown(): void
    {
        if ($this->em->getConnection()->isTransactionActive()) {
            $this->em->rollback();
        }
        parent::tearDown();
    }

    protected function get($id)
    {
        return self::$kernel->getContainer()->get($id);
    }

    /**
     * TODO: For some reason KERNEL_CLASS from phpunit.xml.dist is ignored...
     * @return string
     */
    protected static function getKernelClass()
    {
        return 'Core\Kernel';
    }
}
