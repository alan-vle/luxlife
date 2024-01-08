<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_REF = 'admin-user';
    public const DIRECTOR_REF = 'director-user';
    public const AGENT_REF = 'agent-user';
    public const CUSTOMER_REF = 'customer-user';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $agencyOfMarseille = $this->getReference(AgencyFixtures::AGENCY_MARSEILLE_REFERENCE);

        if (!$agencyOfMarseille instanceof Agency) {
            throw new \Exception("La référence n'est pas une instance de App\Entity\Agency.");
        }

        $users = [
            [ // Admin
                'first_name' => 'Jhhony',
                'last_name' => 'Punisher',
                'email' => 'jhhony-punisher@luxlife.com',
                'password' => 'azs!aAAz4a1s24e1sa',
                'address' => 'Sans adresse.',
                'birth_date' => '1998/02/15',
                'roles' => ['ADMIN'],
                'ref' => self::ADMIN_REF,
            ],
            [ // Director
                'first_name' => 'Bruce',
                'last_name' => 'Douglas',
                'email' => 'bruce-douglas@marseille.luxlife.com',
                'password' => 'ezZ?ez21d!zezda',
                'address' => $agencyOfMarseille->getAddress(),
                'birth_date' => '1987/08/24',
                'agency' => $agencyOfMarseille,
                'roles' => ['DIRECTOR'],
                'ref' => self::DIRECTOR_REF,
            ],
            [ // Agent
                'first_name' => 'Louise',
                'last_name' => 'Hersine',
                'email' => 'louise-hersine@luxlife.com',
                'password' => 'ez4d0d21zer!zdzedzaerr?AA',
                'address' => $agencyOfMarseille->getAddress(),
                'birth_date' => '1997/06/14',
                'agency' => $agencyOfMarseille,
                'roles' => ['AGENT'],
                'ref' => self::AGENT_REF,
            ],
            [ // Customer
                'first_name' => 'Lucas',
                'last_name' => 'Jones',
                'email' => 'lucas-jones@gmail.com',
                'password' => 'ezezae&@a?zmAzemM12123',
                'address' => '2 rue du tiroir, Marseille',
                'birth_date' => '1975/06/30',
                'roles' => ['CUSTOMER'],
                'ref' => self::CUSTOMER_REF,
            ],
        ];

        $faker = Faker\Factory::create('fr_FR');

        foreach ($users as $userData) {
            $user = new User();

            $user
                ->setFirstName($userData['first_name'])
                ->setLastName($userData['last_name'])
                ->setEmail($userData['email'])
                ->setPassword($this->passwordHasherFnc($user, $userData['password']))
                ->setAddress($userData['address'] ?: '')
                ->setBirthDate(new \DateTime($userData['birth_date']))
                /* @phpstan-ignore-next-line | Because he can't detect methods of FR faker provider */
                ->setPhoneNumber($this->phoneNumberStandardization($faker->mobileNumber()))
                ->setAgency(array_key_exists('agency', $userData) ? $userData['agency'] : null)
                ->setRoles($userData['roles'])
            ;

            if (in_array('ADMIN', $userData['roles']) || in_array('DIRECTOR', $userData['roles']) || in_array('AGENT', $userData['roles'])) {
                $user
                    ->setVerifiedEmail(true)
                    ->setVerifiedPhoneNumber(true)
                ;
            }
            $manager->persist($user);

            $this->addReference($userData['ref'], $user);
            $manager->flush();
        }
    }

    /**
     * Remove prefix of number : 0, +33, (0).
     */
    private function phoneNumberStandardization(string $phoneNumber): ?string
    {
        return preg_replace('/^\+33|^0|\s+|\(0\)/', '', $phoneNumber);
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

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            AgencyFixtures::class,
        ];
    }
}
