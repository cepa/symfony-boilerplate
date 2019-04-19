<?php

namespace Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractFixture extends Fixture implements OrderedFixtureInterface
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getEnvironment(): string
    {
        return $this->container->get('kernel')->getEnvironment();
    }

    public function isAllowedEnvironment(array $environments): bool
    {
        return in_array($this->getEnvironment(), $environments);
    }

    public function getOrder()
    {
        return 50;
    }
}
