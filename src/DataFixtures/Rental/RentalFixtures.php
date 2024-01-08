<?php

namespace App\DataFixtures\Rental;

use App\DataFixtures\UserFixtures;
use App\Entity\Car\Car;
use App\Entity\Enum\Rental\RentalStatusEnum;
use App\Entity\Rental\Rental;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RentalFixtures extends Fixture implements DependentFixtureInterface
{
    public const RENTAL_DELIVERY_REF = 'rental-delivery';

    public function load(ObjectManager $manager): void
    {
        $rentals = self::getRentalsData();

        foreach ($rentals as $rentalData) {
            $rental = new Rental();
            $rental
                ->setCustomer(is_string($rentalData['customer']) ? $this->isInstanceOfUser($rentalData['customer']) : throw new \Exception())
                ->setEmployee(is_string($rentalData['employee']) ? $this->isInstanceOfUser($rentalData['employee']) : throw new \Exception())
                ->setCar(is_string($rentalData['car']) ? $this->isInstanceOfCar($rentalData['car']) : throw new \Exception())
                ->setContract(!(0 === $rentalData['contract']))
                ->setMileageKilometers(is_int($rentalData['mileage_kilometers']) ? $rentalData['mileage_kilometers'] : 0)
                ->setFromDate($this->stringToDateTime($rentalData['from_date']))
                ->setToDate($this->stringToDateTime($rentalData['to_date']))
                ->setPrice(is_int($rentalData['price']) ? (string) $rentalData['price'] : '')
                ->setStatus($rentalData['status'] instanceof RentalStatusEnum ? $rentalData['status'] : RentalStatusEnum::DRAFT)
            ;

            $manager->persist($rental);

            if (RentalStatusEnum::DELIVERY === $rental->getStatus()) {
                $this->addReference(self::RENTAL_DELIVERY_REF, $rental);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    private function isInstanceOfUser(string $ref): User
    {
        $ref = $this->getReference($ref);

        return $ref instanceof User ? $ref : new User();
    }

    private function isInstanceOfCar(string $ref): Car
    {
        $ref = $this->getReference($ref);

        return $ref instanceof Car ? $ref : new Car();
    }

    private function stringToDateTime(mixed $date): \DateTime
    {
        return is_string($date) ? new \DateTime($date) : new \DateTime();
    }

    /**
     * @return array<int, array<string, RentalStatusEnum|int|string>>
     */
    private function getRentalsData(): array
    {
        return [
            [
                'customer' => UserFixtures::CUSTOMER_REF,
                'employee' => UserFixtures::AGENT_REF,
                'car' => '',
                'contract' => 1,
                'mileage_kilometers' => 42000,
                'from_date' => '2024-01-18',
                'to_date' => '2026-01-25',
                'price' => 80000,
                'status' => RentalStatusEnum::DRAFT,
            ],
            [
                'customer' => UserFixtures::CUSTOMER_REF,
                'employee' => UserFixtures::AGENT_REF,
                'car' => '',
                'contract' => 0,
                'mileage_kilometers' => 1000,
                'from_date' => '2024-01-09',
                'to_date' => '2024-01-19',
                'price' => 3000,
                'status' => RentalStatusEnum::RENTED,
            ],
            [
                'customer' => UserFixtures::CUSTOMER_REF,
                'employee' => UserFixtures::AGENT_REF,
                'car' => '',
                'contract' => 0,
                'mileage_kilometers' => 200,
                'from_date' => '2024-01-14',
                'to_date' => '2024-01-17',
                'price' => 1800,
                'status' => RentalStatusEnum::DELIVERY,
            ],
            [
                'customer' => UserFixtures::CUSTOMER_REF,
                'employee' => UserFixtures::AGENT_REF,
                'car' => '',
                'contract' => 1,
                'mileage_kilometers' => 7000,
                'from_date' => '2024-01-09',
                'to_date' => '2025-01-19',
                'price' => 22000,
                'status' => RentalStatusEnum::RENTED,
            ],
            [
                'customer' => UserFixtures::CUSTOMER_REF,
                'employee' => UserFixtures::AGENT_REF,
                'car' => '',
                'contract' => 0,
                'mileage_kilometers' => 2000,
                'from_date' => '2023-11-10',
                'to_date' => '2023-12-02',
                'price' => 3000,
                'status' => RentalStatusEnum::RETURNED,
            ],
        ];
    }
}
