<?php

namespace App\DataFixtures\Car;

use App\DataFixtures\AgencyFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Car\Manufacturer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ManufacturerFixtures extends Fixture implements DependentFixtureInterface
{
    public const TESLA_REF = 'tesla-manufacturer';
    public const AUDI_REF = 'audi-manufacturer';

    public function load(ObjectManager $manager): void
    {
        $manufacturers = [self::TESLA_REF, self::AUDI_REF];

        foreach ($manufacturers as $manufacturerData) {
            $manufacturer = new Manufacturer();
            $manufacturerName = strstr($manufacturerData, '-', true);

            $manufacturer->setName($manufacturerName ?: '');

            $manager->persist($manufacturer);

            $this->addReference($manufacturerData, $manufacturer);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AgencyFixtures::class,
            UserFixtures::class,
        ];
    }
}
