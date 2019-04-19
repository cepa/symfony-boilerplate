<?php

namespace Core\Entity;

use Doctrine\ORM\Mapping as ORM;

trait IsActiveTrait
{
    /**
     * @var bool
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    protected $isActive = true;


    public function setIsActive(bool $bool)
    {
        $this->isActive = $bool;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
