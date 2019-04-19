<?php

namespace Core\Entity;

use Doctrine\ORM\Mapping as ORM;

trait IsDeletedTrait
{
    /**
     * @var bool
     * @ORM\Column(name="is_deleted", type="boolean", nullable=false)
     */
    protected $isDeleted = false;


    public function setIsDeleted(bool $bool)
    {
        $this->isDeleted = $bool;
        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }
}
