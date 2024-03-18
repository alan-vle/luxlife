<?php

namespace App\DataFixtures\Car;

use App\DataFixtures\AgencyFixtures;
use App\Entity\Agency;
use App\Entity\Car\Car;
use App\Entity\Car\Manufacturer;
use App\Entity\Enum\Car\CarStatusEnum;
use App\Utils\ImageUploader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\HttpFoundation\File\File;

class CarFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly ImageUploader $imageUploader
    ) {
    }

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
            $car->filePath = $this->imageUploader->upload($carData['image']);
            $car
                ->setManufacturer(
                    $this->isInstanceOfManufacturer(
                        is_string($carData['manufacturer']) ? $carData['manufacturer'] : throw new \Exception()
                    )
                )
                ->setAgency($this->isInstanceOfAgency(AgencyFixtures::AGENCY_MARSEILLE_REF))
                ->setModel(is_string($carData['model']) ? $carData['model'] : '')
                ->setPricePerKilometer(is_int($carData['price_per_km']) ? (string) $carData['price_per_km'] : '')
                ->setKilometers(is_int($carData['kilometers']) ? $carData['kilometers'] : 0)
                ->setStatus($carData['status'] instanceof CarStatusEnum ? $carData['status'] : CarStatusEnum::AVAILABLE)
            ;

            $manager->persist($car);

            $this->addReference(is_string($carData['ref']) ? $carData['ref'] : '', $car);
        }

        $manufacturers = [ManufacturerFixtures::TESLA_REF, ManufacturerFixtures::AUDI_REF];
        $carStatus = [CarStatusEnum::RESERVED, CarStatusEnum::RENTED, CarStatusEnum::AVAILABLE, CarStatusEnum::PROBLEM];
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 5; ++$i) {
            $car = new Car();
            $uploadedFileName = $faker->image('/srv/luxlife/public/uploads/cars', 360, 360, 'cars');
            $car->filePath = substr(strstr($uploadedFileName, 'cars/'), strlen('cars/'));

            $car
                ->setManufacturer($this->isInstanceOfManufacturer($manufacturers[array_rand($manufacturers)]))
                ->setAgency($this->isInstanceOfAgency(AgencyFixtures::AGENCY_MARSEILLE_REF))
                ->setModel($faker->word())
                ->setPricePerKilometer((string) $faker->randomNumber(3, true))
                ->setKilometers($faker->randomNumber(5, true))
                ->setStatus($carStatus[array_rand($carStatus)])
            ;

            $manager->persist($car);
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
     * @return array<int, array<string, CarStatusEnum|int|string|File>>
     */
    private function getCarsData(): array
    {
        $imagesDir = __DIR__.'/Images';

        return [
            [
                'manufacturer' => ManufacturerFixtures::TESLA_REF,
                'model' => 'Model S',
                'image' => sprintf('%s/background-car.png', $imagesDir),
                'price_per_km' => 7,
                'kilometers' => 10000,
                'status' => CarStatusEnum::RESERVED,
                'ref' => self::TESLA_MODEL_S_REF,
            ],
            [
                'manufacturer' => ManufacturerFixtures::AUDI_REF,
                'model' => 'TT',
                'image' => sprintf('%s/audi.png', $imagesDir),
                'price_per_km' => 10,
                'kilometers' => 40000,
                'status' => CarStatusEnum::RENTED,
                'ref' => self::AUDI_TT_REF,
            ],
            [
                'manufacturer' => ManufacturerFixtures::AUDI_REF,
                'model' => 'R8',
                'image' => sprintf('%s/Audi-PNG-Clipart.png', $imagesDir),
                'price_per_km' => 11,
                'kilometers' => 74000,
                'status' => CarStatusEnum::AVAILABLE,
                'ref' => self::AUDI_R8_REF,
            ],
            [
                'manufacturer' => ManufacturerFixtures::AUDI_REF,
                'model' => 'Q2',
                'image' => sprintf('%s/audi_s5_sportback.jpg', $imagesDir),
                'price_per_km' => 4,
                'kilometers' => 28000,
                'status' => CarStatusEnum::RENTED,
                'ref' => self::AUDI_Q2_REF,
            ],
            [
                'manufacturer' => ManufacturerFixtures::AUDI_REF,
                'model' => 'Q3',
                'image' => sprintf('%s/audi_18a7.png', $imagesDir),
                'price_per_km' => 5,
                'kilometers' => 170000,
                'status' => CarStatusEnum::PROBLEM,
                'ref' => self::AUDI_Q3_REF,
            ],
        ];
    }
}
