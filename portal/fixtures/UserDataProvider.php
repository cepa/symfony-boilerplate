<?php

namespace Fixtures;

use Core\Entity\User;
use Core\Helper\RandomIdGenerator;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class UserDataProvider
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function userPasswordEncoder( $password)
    {
        /** @var PasswordEncoderInterface $encoder */
        $encoder = $this->container->get('security.password_encoder');
        return $encoder->encodePassword(new User(), $password);
    }

    public function userUniqueId()
    {
        return RandomIdGenerator::generate();
    }
}
