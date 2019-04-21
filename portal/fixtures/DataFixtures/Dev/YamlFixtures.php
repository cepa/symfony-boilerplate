<?php

namespace Fixtures\DataFixtures\Dev;

use Fixtures\AbstractFixture;
use Fixtures\CustomNativeLoader;
use Doctrine\Common\Persistence\ObjectManager;

class YamlFixtures extends AbstractFixture
{
    public function load(ObjectManager $om)
    {
        if (!$this->isAllowedEnvironment(['dev'])) return;

        $files = [
            __DIR__ . '/../../data/dev/random_admins.yml',
            __DIR__ . '/../../data/dev/random_users.yml',
            __DIR__ . '/../../data/dev/test_user.yml',
        ];
        $loader = new CustomNativeLoader(null, $this->container);
        $objects = $loader->loadFiles($files)->getObjects();
        foreach ($objects as $object) {
            $om->persist($object);
        }
        $om->flush();
    }
}
