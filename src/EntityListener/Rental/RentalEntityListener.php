<?php

namespace App\EntityListener\Rental;

use App\Entity\Car\Car;
use App\Entity\Enum\Car\CarStatusEnum;
use App\Entity\Enum\Rental\DeliveryStatusEnum;
use App\Entity\Enum\Rental\RentalStatusEnum;
use App\Entity\Rental\Delivery;
use App\Entity\Rental\Rental;
use App\Entity\User\User;
use App\Exception\CustomException;
use App\Utils\DeliveryUtils;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
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

    /**
     * @throws InternalErrorException
     */
    public function prePersist(Rental $rental): void
    {
        if ($rental->isFixtures) {
            return;
        }

        $car = $rental->getCar() instanceof Car ? $rental->getCar() : throw new BadRequestException();

        if (CarStatusEnum::AVAILABLE !== $car->getBrutStatus()) {
            throw new CustomException('Car unavailable.', 400);
        }

        // Check when car will be available
        //        if (CarStatusEnum::RENTED === $car->getBrutStatus()) {
        //            $carInRented = array_filter($car->getRentals()->toArray(), fn ($carRental) => $carRental->getToDate() >= $rental->getFromDate());
        //            if (count($carInRented) > 0) {
        //                throw new BadRequestException();
        //            }
        //        }

        $rental->setAgency($car->getAgency() ?: throw new BadRequestException());
        $this->isAgencyOrOnlineRental($rental);
        if (null !== $rental->getDelivery()) {
            $rental
                ->getDelivery()
                ->setRental($rental)
                ->setStatus(DeliveryStatusEnum::PREPARATION)
            ;

            $rental->setStatus(RentalStatusEnum::DELIVERY);
        }

        if (null !== $rental->getBrutStatus()) {
            $this->updateCarStatus($rental->getBrutStatus(), $car);

            return;
        }
        // Set rental status depending on case
        $this->setStatusAccordingToCase($rental);

        // Set car status depending on case
        $this->updateCarStatus($rental->getBrutStatus(), $car);
    }

    /**
     * @throws InternalErrorException
     */
    public function preUpdate(Rental $rental, PreUpdateEventArgs $args): void
    {
        $car = $rental->getCar();
        dd('eea');
        // Status of rental is draft and logged user is a customer
        if (RentalStatusEnum::DRAFT === $rental->getBrutStatus() && $this->security->isGranted('ROLE_CUSTOMER')) {
            if ($rental->getFromDate() === new \DateTime()) {
                $rental->setStatus(RentalStatusEnum::RENTED);
                $car->setStatus(CarStatusEnum::RENTED);

                return;
            }

            $rental->setStatus(RentalStatusEnum::RESERVED);
            $car->setStatus(CarStatusEnum::RESERVED);

            return;
        }
        if ($args->hasChangedField('status') && $this->security->isGranted('ROLE_AGENT')) {
            $this->updateCarStatus($rental->getBrutStatus(), $car);

            return;
        }

        // Set rental status depending on case
        $this->updateStatusAccordingToCase($rental);

        // Set car status depending on case
        $this->updateCarStatus($rental->getBrutStatus(), $car);
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

    /**
     * Update car status.
     * If Rental status is RESERVED so car too.
     * Else if rental status is RENTED so car too.
     * Else if rental status is RETURNED so car status will become available.
     */
    private function updateCarStatus(?RentalStatusEnum $rentalBrutStatus, ?Car $car): void
    {
        if (!$car instanceof Car || !$rentalBrutStatus instanceof RentalStatusEnum) {
            throw new InternalErrorException();
        }
        if (RentalStatusEnum::RESERVED === $rentalBrutStatus) {
            $car->setStatus(CarStatusEnum::RESERVED);
        } elseif (RentalStatusEnum::RENTED === $rentalBrutStatus) {
            $car->setStatus(CarStatusEnum::RENTED);
        } elseif (RentalStatusEnum::RETURNED === $rentalBrutStatus) {
            $car->setStatus(CarStatusEnum::AVAILABLE);
        }
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
        } else {
            $rental->setStatus(RentalStatusEnum::RESERVED);
        }
    }

    private function updateStatusAccordingToCase(Rental $rental): void
    {
        $car = $rental->getCar() instanceof Car ? $rental->getCar() : throw new InternalErrorException();
        dd('eea');
        if (
            (RentalStatusEnum::RENTED === $rental->getBrutStatus() && null !== $rental->getUsedKilometers())
            || CarStatusEnum::PROBLEM === $car->getBrutStatus()
        ) {
            $rental->setStatus(RentalStatusEnum::RETURNED);
        } elseif (RentalStatusEnum::DELIVERY === $rental->getBrutStatus()) {
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
            dd('eaa');
            $newStatus = $rental->getDelivery() ? RentalStatusEnum::DELIVERY : RentalStatusEnum::RESERVED;

            $rental->setStatus($newStatus);
        }
    }
}
