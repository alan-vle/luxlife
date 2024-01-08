<?php

namespace App\DataFixtures\Rental;

use App\Entity\Rental\Rental;
use App\Entity\Rental\RentalArchived;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RentalArchivedFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $rental = $this->isInstanceOfRental(RentalFixtures::RENTAL_RETURNED_MUST_BE_ARCHIVED_REF);

        $rentalArchived = RentalArchived::convertToRentalArchived($rental);

        $manager->persist($rentalArchived);

        $manager->remove($rental);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RentalFixtures::class,
            DeliveryFixtures::class,
        ];
    }

    private function isInstanceOfRental(string $ref): Rental
    {
        $ref = $this->getReference($ref);

        return $ref instanceof Rental ? $ref : new Rental();
    }
}
