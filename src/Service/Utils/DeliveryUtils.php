<?php

namespace App\Service\Utils;

use App\Repository\Rental\DeliveryRepository;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\Uid\Uuid;

class DeliveryUtils
{
    public function __construct(
        private readonly DeliveryRepository $deliveryRepository
    ) {
    }

    public function checkStatusOfDelivery(Uuid $deliveryUuid): string
    {
        $delivery = $this->deliveryRepository->findOneBy(['uuid' => $deliveryUuid]);

        if (!$delivery) {
            throw new InternalErrorException();
        }

        return $delivery->getStatus();
    }
}
