<?php

namespace Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="Core\Repository\AdminRepository")
 * @ORM\Table(name="admin")
 */
class Admin extends AbstractUser implements UserInterface
{
    public function getUsername()
    {
        return $this->getEmail();
    }

    public function getPassword()
    {
        return $this->getPasswordHash();
    }

    public function getSalt()
    {
        // Do nothing.
    }

    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    public function eraseCredentials()
    {
        // Do nothing.
    }
}
