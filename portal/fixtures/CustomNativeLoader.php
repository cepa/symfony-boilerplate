<?php

namespace Fixtures;

use Faker\Generator as FakerGenerator;
use Nelmio\Alice\Loader\NativeLoader;
use Psr\Container\ContainerInterface;

class CustomNativeLoader extends NativeLoader
{
    private $container;

    public function __construct(FakerGenerator $fakerGenerator = null, ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct($fakerGenerator);
    }

    protected function createFakerGenerator(): FakerGenerator
    {
        $generator = parent::createFakerGenerator();
        $generator->addProvider(new UserDataProvider($this->container));
        return $generator;
    }
}
