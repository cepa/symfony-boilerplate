<?php

namespace  Core\Entity;

use Doctrine\ORM\Mapping as ORM;

trait IdTrait
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return __CLASS__ . '#' . $this->getId();
    }
}
