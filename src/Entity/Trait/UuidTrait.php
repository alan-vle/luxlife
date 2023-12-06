<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\Uid\Uuid;

trait UuidTrait
{
    #[Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[PrePersist]
    public function setUuidWhenPersist(): void
    {
        $uuid = Uuid::v4();
        $this->setUuid($uuid);
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    /**
     * @return $this
     */
    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }
}
