<?php

namespace App\EntityListener\Rental;

use App\Entity\Car\Car;
use App\Entity\Enum\Rental\RentalStatusEnum;
use App\Entity\Rental\Delivery;
use App\Entity\Rental\Rental;
use App\Entity\User\User;
use App\Service\Utils\DeliveryUtils;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Rental::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Rental::class)]
// #[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Rental::class)]
// #[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Rental::class)]
class RentalEntityListener
{
    public function __construct(
        private readonly Security $security,
        private readonly DeliveryUtils $deliveryUtils
    ) {
    }

    public function prePersist(Rental $rental): void
    {
        if ($rental->isFixtures) {
            return;
        }

        $this->setStatusAccordingToCase($rental);

        $this->isAgencyOrOnlineRental($rental);
    }

    public function preUpdate(Rental $rental): void
    {
        $this->updateStatusAccordingToCase($rental);
    }

    /**
     * Draft rental is defined, so the status is Draft.
     * Delivery is defined, so the status is Delivery.
     * By default, Reserved is the status.
     */
    private function setStatusAccordingToCase(Rental $rental): void
    {
        if ($rental->draftRental) {
            $rental->setStatus(RentalStatusEnum::DRAFT);
        } elseif (null !== $rental->getDelivery()) {
            $rental->setStatus(RentalStatusEnum::DELIVERY);
        } else {
            $rental->setStatus(RentalStatusEnum::RESERVED);
        }
    }

    private function isAgencyOrOnlineRental(Rental $rental): void
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new BadRequestException();
        }

        // If the connected user is an agent, this means it's an agency rental.
        if ($this->security->isGranted('ROLE_AGENT') && null !== $rental->getCustomer()) {
            $rental->setEmployee($user);
        } else { // Otherwise, it's an online rental.
            $rental->setCustomer($user);
        }
    }

    private function updateStatusAccordingToCase(Rental $rental): void
    {
        $car = $rental->getCar() ?: new Car();

        if ('Problem' === $car->getStatus() || ('Rented' === $rental->getStatus() && null !== $rental->getUsedKilometers())) {
            $rental->setStatus(RentalStatusEnum::RETURNED);
        } elseif ('Delivery' === $rental->getStatus()) {
            $delivery = $rental->getDelivery() ?: new Delivery();
            $deliveryTrackNumber = $delivery->getTrackNumber() ?: throw new InternalErrorException();
            $deliveryStatus = $this->deliveryUtils->checkStatusOfDelivery($deliveryTrackNumber);

            if ('Delivered' !== $deliveryStatus) {
                return;
            }

            $rental->setStatus(RentalStatusEnum::RENTED);
        } elseif ('Draft' === $rental->getStatus()) {
            if ($rental->draftRental) {
                return;
            }

            $newStatus = $rental->getDelivery() ? RentalStatusEnum::DELIVERY : RentalStatusEnum::RESERVED;

            $rental->setStatus($newStatus);
        }
    }
}
