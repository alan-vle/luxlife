<?php

namespace App\DataFixtures;

use App\DataFixtures\Rental\RentalFixtures;
use App\Entity\Agency;
use App\Entity\Review;
use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $review = new Review();

        $review
            ->setCustomer($this->isInstanceOfUser(UserFixtures::CUSTOMER_REF))
            ->setAgency($this->isInstanceOfAgency(AgencyFixtures::AGENCY_MARSEILLE_REFERENCE))
            ->setStar('4.5')
            ->setDetails('La meilleure agence de Marseille !')
        ;

        $manager->persist($review);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RentalFixtures::class,
        ];
    }

    private function isInstanceOfAgency(string $ref): Agency
    {
        $ref = $this->getReference($ref);

        return $ref instanceof Agency ? $ref : new Agency();
    }

    private function isInstanceOfUser(string $ref): User
    {
        $ref = $this->getReference($ref);

        return $ref instanceof User ? $ref : new User();
    }
}
