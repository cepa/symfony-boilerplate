<?php

namespace Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Generic\Entity\Entity;

abstract class AbstractUser extends Entity
{
    use IdTrait;
    use NameTrait;
    use IsActiveTrait;
    use IsDeletedTrait;
    use CreatedAtTrait;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=128, nullable=false, unique=true)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(name="password_hash", type="string", length=128, nullable=false)
     */
    protected $passwordHash;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_login_at", type="datetime", nullable=true)
     */
    protected $lastLoginAt;

    /**
     * @var string
     * @ORM\Column(name="last_login_ip", type="string", length=32, nullable=true)
     */
    protected $lastLoginIp;

    /**
     * @var string
     * @ORM\Column(name="last_user_agent", type="string", length=255, nullable=true)
     */
    protected $lastUserAgent;

    public function setEmail(string $email)
    {
        $this->email = mb_strtolower($email);
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setPasswordHash(string $hash)
    {
        $this->passwordHash = $hash;
        return $this;
    }

    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    public function setLastLoginAt($datetime)
    {
        if ($datetime instanceof \DateTime) {
            $this->lastLoginAt = $datetime;
        } else {
            $this->lastLoginAt = new \DateTime($datetime);
        }
        return $this;
    }

    public function getLastloginAt()
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginIp($ip)
    {
        $this->lastLoginIp = (string) $ip;
    }

    public function getLastLoginIp()
    {
        return $this->lastLoginIp;
    }

    public function setLastUserAgent($userAgent)
    {
        $this->lastUserAgent = (string) $userAgent;
        return $this;
    }

    public function getLastUserAgent()
    {
        return $this->lastUserAgent;
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }
}
