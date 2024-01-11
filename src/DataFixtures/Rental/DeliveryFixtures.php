<?php

namespace App\DataFixtures\Rental;

use App\DataFixtures\UserFixtures;
use App\Entity\Enum\Rental\DeliveryStatusEnum;
use App\Entity\Rental\Delivery;
use App\Entity\Rental\Rental;
use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DeliveryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $delivery = new Delivery();

        $customer = $this->isInstanceOfUser(UserFixtures::CUSTOMER_REF);

        $delivery
            ->setRental($this->isInstanceOfRental(RentalFixtures::RENTAL_DELIVERY_REF))
            ->setStatus(DeliveryStatusEnum::PREPARATION)
            ->setAddress($customer->getAddress() ?: '')
            ->setDeliveryDate((new \DateTime())->modify('+1 week'))
        ;

        $manager->persist($delivery);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RentalFixtures::class,
        ];
    }

    private function isInstanceOfUser(string $ref): User
    {
        $ref = $this->getReference($ref);

        return $ref instanceof User ? $ref : new User();
    }

    private function isInstanceOfRental(string $ref): Rental
    {
        $ref = $this->getReference($ref);

        return $ref instanceof Rental ? $ref : new Rental();
    }
}
