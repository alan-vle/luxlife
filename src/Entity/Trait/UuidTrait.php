<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

trait UuidTrait
{
    #[Assert\Uuid(versions: [4])]
    #[Column(type: 'uuid')]
    protected ?Uuid $uuid = null;

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    #[PrePersist]
    public function setUuidValue(): void
    {
        $this->uuid = Uuid::v4();
    }
}
