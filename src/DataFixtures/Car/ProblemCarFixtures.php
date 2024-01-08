<?php

namespace App\DataFixtures\Car;

use App\DataFixtures\Rental\RentalFixtures;
use App\Entity\Car\Car;
use App\Entity\Car\ProblemCar;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProblemCarFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $problemsCars = self::getProblemsCarsData();

        foreach ($problemsCars as $problemCarData) {
            $problemCar = new ProblemCar();

            $problemCar
                ->setCar(
                    $this->isInstanceOfCar(
                        is_string($problemCarData['car']) ? $problemCarData['car'] : ''
                    )
                )
                ->setDescription(
                    is_string($problemCarData['description']) ? $problemCarData['description'] : ''
                )
                ->setType(!(0 === $problemCarData['type']))
                ->setProblemDate(
                    new \DateTime(is_string($problemCarData['problem_date']) ? $problemCarData['problem_date'] : '')
                )
            ;

            $manager->persist($problemCar);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RentalFixtures::class,
        ];
    }

    private function isInstanceOfCar(string $ref): Car
    {
        $ref = $this->getReference($ref);

        return $ref instanceof Car ? $ref : new Car();
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    private function getProblemsCarsData(): array
    {
        return [
            [
                'car' => CarFixtures::AUDI_Q3_REF,
                'description' => 'Panne moteur, ramenée au concessionaire (pendant une location).',
                'type' => 0,
                'problem_date' => '2023-01-09',
            ],
            [
                'car' => CarFixtures::TESLA_MODEL_S_REF,
                'description' => 'Defaillance électronique',
                'type' => 0,
                'problem_date' => '2022-11-04',
            ],
            [
                'car' => CarFixtures::AUDI_R8_REF,
                'description' => 'Accident, sur un parking.',
                'type' => 1,
                'problem_date' => '2022-07-08',
            ],
        ];
    }
}
