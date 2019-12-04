<?php

namespace Tests\Core\Service;

use Core\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Tests\ServiceTestCase;
use Tests\Traits\UserServiceTrait;

class UserServiceTest extends ServiceTestCase
{
    use UserServiceTrait;

    const EMAIL         = 'test.user@corp.tld';
    const PASSWORD      = 's3cr3t';
    const NAME          = 'Test User';
    const FACEBOOK_ID   = 'abcdefgh12345678';

    public function testFetchByIdFail()
    {
        $this->expectException(\RuntimeException::class);
        $this->getUserService()->fetchById(-1);
    }

    public function testFetchByUniqueIdFail()
    {
        $this->expectException(\RuntimeException::class);
        $this->getUserService()->fetchByUniqueId('xyz');
    }

    public function testFetchByEmailFail()
    {
        $this->expectException(\RuntimeException::class);
        $this->getUserService()->fetchByEmail('non@existing.tld');
    }

    public function testCrud()
    {
        $service = $this->getUserService();

        /** @var User $user */
        $user = $service->create(self::EMAIL, self::PASSWORD);
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals(self::EMAIL, $user->getEmail());
        $this->assertEquals(self::EMAIL, $user->getUsername());

        $user->setName(self::NAME);
        $service->save($user);
        $this->assertNotNull($user->getId());
        $id = $user->getId();
        $uniqueId = $user->getUniqueId();

        $user = $service->fetchById($user->getId());
        $this->assertEquals(self::EMAIL, $user->getEmail());
        $this->assertEquals(self::NAME, $user->getName());
        $this->assertTrue(in_array('ROLE_USER', $user->getRoles()));

        $user = $service->fetchByUniqueId($uniqueId);
        $this->assertEquals($id, $user->getId());

        $user = $service->fetchByEmail(self::EMAIL);
        $this->assertEquals($id, $user->getId());
        $this->assertEquals(self::EMAIL, $user->getEmail());
        $this->assertEquals(self::NAME, $user->getName());
        $this->assertTrue(in_array('ROLE_USER', $user->getRoles()));

        $this->assertTrue($service->validatePassword($user, self::PASSWORD));
        $this->assertFalse($service->validatePassword($user, 'foo'));

        $service->delete($user);

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

    public function testRegisterRegularAndActivate()
    {
        $service = $this->getUserService();

        $user = $service->register([
            'name' => self::NAME,
            'email' => self::EMAIL,
            'password' => self::PASSWORD,
        ]);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(self::NAME, $user->getName());
        $this->assertEquals(self::EMAIL, $user->getEmail());
        $this->assertFalse($user->isActive());
        $this->assertNotEmpty($user->getToken());

        $service->activate($user);
        $this->assertTrue($user->isActive());
    }

    public function testRegisterFailDueToMissingRequiredFields()
    {
        $service = $this->getUserService();
        $this->expectException(\RuntimeException::class);
        $service->register([]);
    }

    public function testUpdate()
    {
        $service = $this->getUserService();

        $user = $service->findByEmail('test@test.tld');
        $oldId = $user->getId();
        $oldEmail = $user->getEmail();
        $oldUniqueId = $user->getUniqueId();
        $oldToken = $user->getToken();
        $oldPasswordHash = $user->getPasswordHash();

        $service->update($user, [
            'name'         => 'xxx',
            'id'           => 123,
            'email'        => 'phishing@test.tld',
            'uniqueId'     => 'xyz123',
            'token'        => 'abc123',
            'passwordHash' => 'xyz666'
        ]);

        $this->assertEquals('xxx', $user->getName());
        $this->assertEquals($oldId, $user->getId());
        $this->assertEquals($oldEmail, $user->getEmail());
        $this->assertEquals($oldUniqueId, $user->getUniqueId());
        $this->assertEquals($oldToken, $user->getToken());
        $this->assertEquals($oldPasswordHash, $user->getPasswordHash());
    }

    public function testChangePassword()
    {
        $service = $this->getUserService();
        $user = $service->findByEmail('test@test.tld');
        $oldPass = $user->getPasswordHash();
        $oldToken = $user->getToken();
        $service->changePassword($user, 'xxx');
        $this->assertNotEquals($oldPass, $user->getPasswordHash());
        $this->assertNotEquals($oldToken, $user->getToken());
    }

    public function testRequestPassword()
    {
        $service = $this->getUserService();
        $user = $service->findByEmail('test@test.tld');
        $oldToken = $user->getToken();
        $service->requestPassword($user);
        $this->assertNotEquals($oldToken, $user->getToken());
    }
}
