<?php

namespace App\DataFixtures\Rental;

use App\DataFixtures\Car\CarFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Car\Car;
use App\Entity\Enum\Rental\RentalContractEnum;
use App\Entity\Enum\Rental\RentalStatusEnum;
use App\Entity\Rental\Rental;
use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RentalFixtures extends Fixture implements DependentFixtureInterface
{
    public const RENTAL_DELIVERY_REF = 'delivery-rental';
    public const RENTAL_RETURNED_REF = 'returned-rental';
    public const RENTAL_RETURNED_MUST_BE_ARCHIVED_REF = 'returned-must-be-archived-rental';

    public function load(ObjectManager $manager): void
    {
        $rentals = self::getRentalsData();

        foreach ($rentals as $rentalData) {
            $rental = new Rental();
            $rental->isFixtures = true;
            $car = is_string($rentalData['car']) ? $this->isInstanceOfCar($rentalData['car']) : throw new \Exception();
            $rental
                ->setCustomer(is_string($rentalData['customer']) ? $this->isInstanceOfUser($rentalData['customer']) : throw new \Exception())
                ->setEmployee(
                    array_key_exists('employee', $rentalData) && is_string($rentalData['employee'])
                        ? $this->isInstanceOfUser($rentalData['employee']) : null)
                ->setCar($car)
                ->setContract(
                    $rentalData['contract'] instanceof RentalContractEnum ? $rentalData['contract'] : throw new \Exception()
                )
                ->setMileageKilometers(
                    is_int($rentalData['mileage_kilometers']) ? $rentalData['mileage_kilometers'] : 0
                )
                ->setFromDate($this->stringToDateTime($rentalData['from_date']))
                ->setToDate($this->stringToDateTime($rentalData['to_date']))
                ->setStatus(
                    $rentalData['status'] instanceof RentalStatusEnum ? $rentalData['status'] : RentalStatusEnum::DRAFT
                )
                ->setAgency($car->getAgency())
            ;

            $manager->persist($rental);

            if (RentalStatusEnum::DELIVERY === $rental->getBrutStatus()) {
                $this->addReference(self::RENTAL_DELIVERY_REF, $rental);
            } elseif (RentalStatusEnum::RETURNED === $rental->getBrutStatus()) {
                $rental->setUsedKilometers(
                    is_int($rentalData['used_kilometers']) ? $rentalData['used_kilometers'] : 0
                );

                $car = $rental->getCar();

                if ($car instanceof Car) {
                    $carKilometers = (int) $car->getKilometers();

                    $adjustedKilometers = $carKilometers + $rental->getUsedKilometers();

                    $car->setKilometers($adjustedKilometers);
                }

                $this->addReference(is_string($rentalData['ref']) ? $rentalData['ref'] : '', $rental);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CarFixtures::class,
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
     * @return array<int, array<string, RentalStatusEnum|RentalContractEnum|int|string>>
     */
    private function getRentalsData(): array
    {
        return [
            [
                'customer' => UserFixtures::CUSTOMER_REF,
                'employee' => UserFixtures::AGENT_REF,
                'car' => CarFixtures::AUDI_R8_REF,
                'contract' => RentalContractEnum::LLD,
                'mileage_kilometers' => 42000,
                'from_date' => '2024-01-18',
                'to_date' => '2026-01-25',
                'status' => RentalStatusEnum::DRAFT,
            ],
            [
                'customer' => UserFixtures::CUSTOMER_REF,
                'car' => CarFixtures::TESLA_MODEL_S_REF,
                'contract' => RentalContractEnum::CLASSIC,
                'mileage_kilometers' => 1000,
                'from_date' => '2024-01-09',
                'to_date' => '2024-01-19',
                'status' => RentalStatusEnum::RESERVED,
            ],
            [
                'customer' => UserFixtures::CUSTOMER_REF,
                'employee' => UserFixtures::AGENT_REF,
                'car' => CarFixtures::AUDI_TT_REF,
                'contract' => RentalContractEnum::CLASSIC,
                'mileage_kilometers' => 200,
                'from_date' => '2024-01-14',
                'to_date' => '2024-01-17',
                'status' => RentalStatusEnum::DELIVERY,
            ],
            [
                'customer' => UserFixtures::CUSTOMER_REF,
                'car' => CarFixtures::AUDI_Q2_REF,
                'contract' => RentalContractEnum::LLD,
                'mileage_kilometers' => 7000,
                'from_date' => '2024-01-09',
                'to_date' => '2025-01-19',
                'status' => RentalStatusEnum::RENTED,
            ],
            [
                'customer' => UserFixtures::CUSTOMER_REF,
                'employee' => UserFixtures::AGENT_REF,
                'car' => CarFixtures::AUDI_Q3_REF,
                'contract' => RentalContractEnum::CLASSIC,
                'mileage_kilometers' => 2000,
                'used_kilometers' => 1850,
                'from_date' => '2023-11-10',
                'to_date' => '2023-12-02',
                'status' => RentalStatusEnum::RETURNED,
                'ref' => self::RENTAL_RETURNED_REF,
            ],
            [
                'customer' => UserFixtures::CUSTOMER_REF,
                'employee' => UserFixtures::AGENT_REF,
                'car' => CarFixtures::AUDI_Q3_REF,
                'contract' => RentalContractEnum::CLASSIC,
                'mileage_kilometers' => 4000,
                'used_kilometers' => 3740,
                'from_date' => '2023-01-01',
                'to_date' => '2023-02-01',
                'status' => RentalStatusEnum::RETURNED,
                'ref' => self::RENTAL_RETURNED_MUST_BE_ARCHIVED_REF,
            ],
        ];
    }
}
