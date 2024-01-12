<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use App\Entity\Enum\AgencyStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AgencyFixtures extends Fixture
{
    public const AGENCY_MARSEILLE_REFERENCE = 'agency-marseille-user';

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        $agency = new Agency();
        $agency->setAddress('2 rue du Clusety');
        $agency->setCity('Marseille, 13013');
        $agency->setEmail('contact-marseille@luxlife.com');
        $agency->setOpeningHours(new \DateTime('08:00'));
        $agency->setClosingHours(new \DateTime('21:00'));
        $agency->setStatus(AgencyStatusEnum::ACTIVE);

        $this->addReference(self::AGENCY_MARSEILLE_REFERENCE, $agency);

        $manager->persist($agency);
        $manager->flush();
    }
}
