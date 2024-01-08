<?php

namespace App\Entity\Enum\Car;

enum CarStatusEnum: int
{
    case RESERVED = 0;
    case RENTED = 1;
    case AVAILABLE = 2;
    case PROBLEM = 3;
}
