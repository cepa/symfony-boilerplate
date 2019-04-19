<?php

namespace Tests\Core\Service;

use Core\Entity\Admin;
use Symfony\Component\Security\Core\User\UserInterface;
use Tests\ServiceTestCase;
use Tests\Traits\AdminServiceTrait;

class AdminServiceTest extends ServiceTestCase
{
    use AdminServiceTrait;

    const EMAIL = 'test.admin@corp.tld';
    const PASSWORD = 's3cr3t';
    const NAME = 'Test Admin';

    public function testFetchByIdFail()
    {
        $this->expectException(\RuntimeException::class);
        $this->getAdminService()->fetchById(-1);
    }

    public function testFetchByEmailFail()
    {
        $this->expectException(\RuntimeException::class);
        $this->getAdminService()->fetchByEmail('non@existing.tld');
    }

    public function testCrud()
    {
        $service = $this->getAdminService();

        /** @var Admin $admin */
        $admin = $service->create(self::EMAIL, self::PASSWORD);
        $this->assertInstanceOf(Admin::class, $admin);
        $this->assertInstanceOf(UserInterface::class, $admin);
        $this->assertEquals(self::EMAIL, $admin->getEmail());
        $this->assertEquals(self::EMAIL, $admin->getUsername());

        $admin->setName(self::NAME);
        $service->save($admin);
        $this->assertNotNull($admin->getId());
        $id = $admin->getId();

        $admin = $service->fetchById($admin->getId());
        $this->assertEquals(self::EMAIL, $admin->getEmail());
        $this->assertEquals(self::NAME, $admin->getName());
        $this->assertContains('ROLE_ADMIN', $admin->getRoles());

        $admin = $service->fetchByEmail(self::EMAIL);
        $this->assertEquals($id, $admin->getId());
        $this->assertEquals(self::EMAIL, $admin->getEmail());
        $this->assertEquals(self::NAME, $admin->getName());
        $this->assertContains('ROLE_ADMIN', $admin->getRoles());

        $this->assertTrue($service->validatePassword($admin, self::PASSWORD));
        $this->assertFalse($service->validatePassword($admin, 'foo'));

        $service->delete($admin);

        try {
            $service->fetchById($id);
            $this->fail('Exception has not been thrown');
        } catch (\RuntimeException $e) {
            // Ok
        }

        try {
            $service->fetchByEmail(self::EMAIL);
            $this->fail('Exception has not been thrown');
        } catch (\RuntimeException $e) {
            // Ok
        }
    }
}
