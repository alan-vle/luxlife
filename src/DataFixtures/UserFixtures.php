<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setFirstName('Jhhony');
        $admin->setLastName('Punisher');
        $admin->setEmail('jhhony-punisher@luxlife.com');
        $hashedAdminPassword = $this->passwordHasherFnc($admin, 'azs!aAAz4a1s24e1sa');
        $admin->setPassword($hashedAdminPassword);
        $admin->setAddress('Sans adresse.');
        $admin->setBirthDate(new \DateTime('1998/02/15'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setActive(true);

        $agencyDirector = new User();
        $agencyDirector->setFirstName('Bruce');
        $agencyDirector->setLastName('Douglas');
        $agencyDirector->setEmail('bruce-douglas@luxlife.com');
        $hashedAgencyDirectorPassword = $this->passwordHasherFnc($agencyDirector, 'ezZ?ez21d!zezda');
        $agencyDirector->setPassword($hashedAgencyDirectorPassword);
        $agencyDirector->setAddress('Agence de Marseille (13013), Marseille');
        $agencyDirector->setBirthDate(new \DateTime('1987/08/24'));
        $agencyDirector->setRoles(['ROLE_DIRECTOR']);
        $agencyDirector->setActive(true);

        $receptionAgent = new User();
        $receptionAgent->setFirstName('Louise');
        $receptionAgent->setLastName('Hersine');
        $receptionAgent->setEmail('louise-hersine@luxlife.com');
        $hashedReceptionAgentPassword = $this->passwordHasherFnc($receptionAgent, 'ez4d0d21zer!zdzedzaerr?AA');
        $receptionAgent->setPassword($hashedReceptionAgentPassword);
        $receptionAgent->setAddress('Agence de Marseille (13013), Marseille');
        $receptionAgent->setBirthDate(new \DateTime('1997/06/14'));
        $receptionAgent->setRoles(['ROLE_AGENT']);
        $receptionAgent->setActive(true);

        $customer = new User();
        $customer->setFirstName('Lucas');
        $customer->setLastName('Jones');
        $customer->setEmail('lucas-jones@gmail.com');
        $hashedCustomerPassword = $this->passwordHasherFnc($customer, 'ezezae&@a?zmAzemM12123');
        $customer->setPassword($hashedCustomerPassword);
        $customer->setAddress('2 rue du tiroir, Marseille');
        $customer->setBirthDate(new \DateTime('1975/06/30'));
        $customer->setRoles(['ROLE_CUSTOMER']);
        $customer->setActive(true);

        $manager->persist($admin);
        $manager->persist($agencyDirector);
        $manager->persist($receptionAgent);
        $manager->persist($customer);
        $manager->flush();
    }

    /**
     * Hash user password.
     */
    private function passwordHasherFnc(User $user, string $plaintextPassword): string
    {
        return $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
    }
}
