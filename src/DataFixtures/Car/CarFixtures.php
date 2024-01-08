<?php

namespace App\DataFixtures\Car;

use App\DataFixtures\AgencyFixtures;
use App\Entity\Agency;
use App\Entity\Car\Car;
use App\Entity\Car\Manufacturer;
use App\Entity\Enum\Car\CarStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CarFixtures extends Fixture implements DependentFixtureInterface
{
    public const TESLA_MODEL_S_REF = 'tesla-model-s-car-ref';
    public const AUDI_TT_REF = 'audi-tt-car-ref';
    public const AUDI_R8_REF = 'audi-r8-car-ref';

    public const AUDI_Q2_REF = 'audi-q2-car-ref';
    public const AUDI_Q3_REF = 'audi-q3-car-ref';

    public function load(ObjectManager $manager): void
    {
        $cars = self::getCarsData();

        foreach ($cars as $carData) {
            $car = new Car();

            $car
                ->setManufacturer(
                    $this->isInstanceOfManufacturer(
                        is_string($carData['manufacturer']) ? $carData['manufacturer'] : throw new \Exception()
                    )
                )
                ->setAgency($this->isInstanceOfAgency(AgencyFixtures::AGENCY_MARSEILLE_REFERENCE))
                ->setModel(is_string($carData['model']) ? $carData['model'] : '')
                ->setKilometers(is_int($carData['kilometers']) ? $carData['kilometers'] : 0)
                ->setStatus($carData['status'] instanceof CarStatusEnum ? $carData['status'] : CarStatusEnum::AVAILABLE)
            ;

            $manager->persist($car);

            $this->addReference(is_string($carData['ref']) ? $carData['ref'] : '', $car);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ManufacturerFixtures::class,
            AgencyFixtures::class,
        ];
    }

    private function isInstanceOfManufacturer(string $ref): Manufacturer
    {
        $ref = $this->getReference($ref);

        return $ref instanceof Manufacturer ? $ref : new Manufacturer();
    }

    private function isInstanceOfAgency(string $ref): Agency
    {
        $ref = $this->getReference($ref);

        return $ref instanceof Agency ? $ref : new Agency();
    }

    /**
     * @return array<int, array<string, CarStatusEnum|int|string>>
     */
    private function getCarsData(): array
    {
        return [
            [
                'manufacturer' => ManufacturerFixtures::TESLA_REF,
                'model' => 'Model S',
                'kilometers' => 10000,
                'status' => CarStatusEnum::RESERVED,
                'ref' => self::TESLA_MODEL_S_REF,
            ],
            [
                'manufacturer' => ManufacturerFixtures::AUDI_REF,
                'model' => 'TT',
                'kilometers' => 40000,
                'status' => CarStatusEnum::RENTED,
                'ref' => self::AUDI_TT_REF,
            ],
            [
                'manufacturer' => ManufacturerFixtures::AUDI_REF,
                'model' => 'R8',
                'kilometers' => 74000,
                'status' => CarStatusEnum::AVAILABLE,
                'ref' => self::AUDI_R8_REF,
            ],
            [
                'manufacturer' => ManufacturerFixtures::AUDI_REF,
                'model' => 'Q2',
                'kilometers' => 28000,
                'status' => CarStatusEnum::RENTED,
                'ref' => self::AUDI_Q2_REF,
            ],
            [
                'manufacturer' => ManufacturerFixtures::AUDI_REF,
                'model' => 'Q3',
                'kilometers' => 170000,
                'status' => CarStatusEnum::PROBLEM,
                'ref' => self::AUDI_Q3_REF,
            ],
        ];
    }
}
