<?php

namespace App\Entity\Enum\Rental;

enum DeliveryStatusEnum: int
{
    case PREPARATION = 0;
    case SHIPPING = 1;
    case ON_DELIVERY = 2;
    case DELIVERED = 3;
}
