<?php

namespace Core\Entity;

use Doctrine\ORM\Mapping as ORM;

trait CreatedAtTrait
{
    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    public function setCreatedAt($datetime)
    {
        if ($datetime instanceof \DateTime) {
            $this->createdAt = $datetime;
        } else {
            $this->createdAt = new \DateTime($datetime);
        }
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
