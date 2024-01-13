<?php

namespace App\Entity\Enum;

enum AgencyStatusEnum: int
{
    case CLOSED_TEMPORARY = 0;
    case ACTIVE = 1;
    case CLOSED_DEFINITIVELY = 2;
}
