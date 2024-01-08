<?php

namespace App\Entity\Enum\Rental;

enum RentalStatusEnum: int
{
    case DRAFT = 0;
    case RESERVED = 1;
    case DELIVERY = 2;
    case RENTED = 3;
    case RETURNED = 4;
}
