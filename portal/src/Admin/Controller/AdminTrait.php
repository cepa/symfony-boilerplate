<?php

namespace Admin\Controller;

use Core\Entity\Admin;
use Core\Service\AdminService;

trait AdminTrait
{
    protected function createNewAdminEntity()
    {
        return $this->getAdminService()->create('', '');
    }

    protected function persistAdminEntity(Admin $user)
    {
        $this->encodeAdminPassword($user);
        $this->persistEntity($user);
    }

    protected function updateAdminEntity(Admin $user)
    {
        $this->encodeAdminPassword($user);
        $this->updateEntity($user);
    }

    protected function encodeAdminPassword(Admin $user)
    {
        $user->setPasswordHash($this->getAdminService()->encodePassword($user, $user->getPasswordHash()));
    }

    protected function getAdminService(): AdminService
    {
        return $this->get('core.admin');
    }
}
