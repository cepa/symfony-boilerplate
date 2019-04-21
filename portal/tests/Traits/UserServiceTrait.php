<?php

namespace Tests\Traits;

use Core\Entity\User;
use Core\Service\UserService;

trait UserServiceTrait
{
    public function getUserService(): UserService
    {
        /** @var UserService $service */
        $service = $this->get('core.user');
        $this->assertInstanceOf(UserService::class, $service);
        return $service;
    }

    protected function createFakeUser(): User
    {
        $service = $this->getUserService();
        $user = $service->create(Constants::FAKE_USER_EMAIL, Constants::FAKE_USER_PASS);
        $user->setName(Constants::FAKE_USER_NAME);
        $service->save($user);
        return $user;
    }
}
