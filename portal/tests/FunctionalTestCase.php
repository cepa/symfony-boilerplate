<?php

namespace Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class FunctionalTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->client->disableReboot();

        $this->em = $this->get('doctrine.orm.entity_manager');
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

    public function get($name)
    {
        return $this->client->getContainer()->get($name);
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
