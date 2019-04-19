<?php

namespace Fixtures\DataFixtures\Prod;

use Doctrine\Common\Persistence\ObjectManager;
use Fixtures\AbstractFixture;

class AdminFixtures extends AbstractFixture
{
    public function load(ObjectManager $om)
    {
        $service = $this->container->get('core.admin');

        $admin = $service->create('admin@domain.tld', 's3cr3t')
            ->setName('System Admin')
        ;

        $service->save($admin);
    }

    public function getOrder()
    {
        return 1;
    }
}
