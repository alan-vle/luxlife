<?php

namespace App\Entity\Rental;

use App\Repository\Rental\RentalArchivedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentalArchivedRepository::class)]
class RentalArchived extends Rental
{
}
