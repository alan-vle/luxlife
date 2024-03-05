<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use App\Entity\Enum\AgencyStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AgencyFixtures extends Fixture
{
    public const AGENCY_MARSEILLE_REF = 'agency-marseille';
    public const AGENCY_AIX_EN_PROVENCE_REF = 'agency-aix';

    public function load(ObjectManager $manager): void
    {
        $marseilleAgency = new Agency();
        $marseilleAgency
            ->setAddress('2 rue du Clusety')
            ->setCity('Marseille, 13013')
            ->setEmail('contact-marseille@luxlife.com')
            ->setOpeningHours(new \DateTime('08:00'))
            ->setClosingHours(new \DateTime('21:00'))
            ->setStatus(AgencyStatusEnum::ACTIVE)
        ;

        $this->addReference(self::AGENCY_MARSEILLE_REF, $marseilleAgency);
        $manager->persist($marseilleAgency);

        $aixAgency = new Agency();
        $aixAgency
            ->setAddress('138 avenue du camarade')
            ->setCity('Aix-en-Provence, 13080')
            ->setEmail('contact-aix@luxlife.com')
            ->setOpeningHours(new \DateTime('08:00'))
            ->setClosingHours(new \DateTime('21:00'))
            ->setStatus(AgencyStatusEnum::ACTIVE)
        ;

        $this->addReference(self::AGENCY_AIX_EN_PROVENCE_REF, $aixAgency);
        $manager->persist($aixAgency);

        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 5; ++$i) {
            $fakeAgency = new Agency();
            $fakeAgency
                ->setAddress(sprintf('%s,', $faker->streetAddress()))
                ->setCity($faker->city())
                ->setEmail($faker->email())
                ->setOpeningHours(new \DateTime('08:00'))
                ->setClosingHours(new \DateTime('21:00'))
                ->setStatus(AgencyStatusEnum::ACTIVE)
            ;

            $manager->persist($fakeAgency);
        }

        $manager->flush();
    }
}
