<?php

namespace App\EntityListener;

use App\Entity\Agency;
use App\Entity\Rental\Rental;
use App\Entity\Review;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Review::class)]
class ReviewEntityListener
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function prePersist(Review $review): void
    {
        if (null !== $review->getCustomer()) {
            return;
        }

        $loggedUser = $this->security->getUser();

        if (!$loggedUser instanceof User || !$this->security->isGranted('ROLE_CUSTOMER')) {
            throw new BadRequestException();
        }

        $customerRentals = $loggedUser->getMyRentals();
        $rentalsFilteredByAgency = $this->rentalsFilteredByAgency($customerRentals, $review->getAgency());

        // Check if the customer has already rented with this agency
        if (!is_array($rentalsFilteredByAgency) || 0 === count($rentalsFilteredByAgency)) {
            throw new BadRequestException();
        }

        $review->setCustomer($loggedUser);
    }

    /**
     * @param Collection<int, Rental> $customerRentals
     *
     * @return array<Rental>
     */
    private function rentalsFilteredByAgency(Collection $customerRentals, ?Agency $reviewAgency): array
    {
        return array_filter($customerRentals->toArray(), fn ($customerRental) => $customerRental->getAgency() === $reviewAgency);
    }
}
