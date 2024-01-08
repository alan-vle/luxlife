<?php

namespace App\Entity\Enum;

enum AgencyStatusEnum: int
{
    case CLOSED = 0;
    case OPEN = 1;
    case DEFINITIVELY_CLOSED = 2;
}
