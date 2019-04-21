<?php

namespace Core\Event;

use Core\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserAccountEvent extends Event
{
    const REGISTERED         = 'user_account.registered';
    const ACTIVATED          = 'user_account.activated';
    const LOGIN              = 'user_account.login';
    const LOGOUT             = 'user_account.logout';
    const MODIFIED           = 'user_account.modified';
    const PASSWORD_CHANGED   = 'user_account.password_changed';
    const PASSWORD_REQUESTED = 'user_account.password_requested';

    /**
     * @var User
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
