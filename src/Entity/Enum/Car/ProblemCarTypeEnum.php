<?php

namespace App\Entity\Enum\Car;

enum ProblemCarTypeEnum: int
{
    case FAILURE = 0;
    case ACCIDENT = 1;
}
