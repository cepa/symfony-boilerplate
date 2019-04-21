<?php

namespace Core\Entity;

trait UniqueIdTrait
{
    /**
     * @var string
     * @ORM\Column(name="unique_id", type="string", length=64, nullable=false, unique=true)
     */
    protected $uniqueId;


    public function setUniqueId(string $uniqueId)
    {
        $this->uniqueId = $uniqueId;
        return $this;
    }

    public function getUniqueId()
    {
        return $this->uniqueId;
    }
}
