<?php

namespace App\Service\Utils;

use App\Entity\Agency;
use App\Entity\Car\Car;
use App\Entity\Car\ProblemCar;
use App\Entity\Enum\Car\CarStatusEnum;
use App\Repository\AgencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class ProblemCarUtils
{
    public function __construct(
        private readonly AgencyRepository $agencyRepository,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function problemCarGenerator(): void
    {
        $agencies = $this->agencyRepository->findAll();

        if (empty($agencies)) {
            throw new InternalErrorException();
        }

        $car = self::getRandomCarInRandomAgency($agencies);
        $car = $car instanceof Car ? $car : new Car();

        $problemCar = self::createProblemCar($car);

        $car->setStatus(CarStatusEnum::PROBLEM);
        $this->em->persist($problemCar);
        $this->em->flush();
    }

    /**
     * @param array<Agency> $agencies
     */
    private static function getRandomCarInRandomAgency(array $agencies): ?Car
    {
        $randomAgency = $agencies[array_rand($agencies)]; // Get agency by a random key
        // Check if agency is an Instance of Agency and get its cars
        $cars = $randomAgency->getCars();

        // If the agency has cars, select one at random and create a problem or recall this function
        return !$cars->isEmpty() ? $cars[array_rand($cars->toArray())] : self::getRandomCarInRandomAgency($agencies);
    }

    private static function createProblemCar(Car $car): ProblemCar
    {
        $faker = Faker\Factory::create('fr_FR');
        $fakeDescriptions = self::getFakeDescriptions();
        $randomDescription = array_rand($fakeDescriptions); // Get a random description from $fakeDescriptions

        $problemCar = new ProblemCar();

        $problemCar
            ->setCar($car)
            ->setDescription((string) $randomDescription)
            ->setType($fakeDescriptions[$randomDescription]) // Get value of key description to get the type
            ->setProblemDate($faker->dateTimeBetween('-3 hours', '-3 minutes'))
        ;

        return $problemCar;
    }

    /**
     * Get Fake descriptions with its type (false|0: failure or true|1: accident).
     *
     * @return array<string, boolean>
     */
    private static function getFakeDescriptions(): array
    {
        return [
            'Capteur défectueux' => false,
            'Pression des pneus' => false,
            'Triangle à changer' => false,
            'Entretien nécessaire' => false,
            'Pare choc cassé suite à un accident' => true,
            'Bris de glace' => true,
            'Accrochage sur un parking' => true,
        ];
    }
}
