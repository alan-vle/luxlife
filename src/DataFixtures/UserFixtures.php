<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
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

        $users = self::getUsersData($agencyOfMarseille);

        $faker = Faker\Factory::create('fr_FR');

        foreach ($users as $userData) {
            $user = new User();

            $user->isFixtures = true;
            $user
                ->setFullName(self::isString($userData['first_name']).' '.self::isString($userData['last_name']))
                ->setEmail(self::isString($userData['email']))
                ->setPassword($this->passwordHasherFnc($user, self::isString($userData['password'])))
                ->setAddress(self::isString($userData['address']))
                ->setBirthDate(new \DateTime(self::isString($userData['birth_date'])))
                /* @phpstan-ignore-next-line | Because he can't detect methods of FR faker provider */
                ->setPhoneNumber($faker->randomNumber(9, true))
                ->setAgency(array_key_exists('agency', $userData) ? self::isInstanceOfAgency($userData['agency']) : null)
                ->setRoles(is_array($userData['roles']) ? $userData['roles'] : [])
            ;

            if (in_array('ADMIN', self::isArray($userData['roles']))
                || in_array('DIRECTOR', self::isArray($userData['roles']))
                || in_array('AGENT', self::isArray($userData['roles']))
            ) {
                $user
                    ->setVerifiedEmail(true)
                    ->setVerifiedPhoneNumber(true)
                ;
            }

            $manager->persist($user);

            $this->addReference(self::isString($userData['ref']), $user);
            $manager->flush();
        }
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

    /**
     * @return array<string>
     */
    private static function isArray(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    private static function isInstanceOfAgency(mixed $agency): Agency
    {
        return $agency instanceof Agency ? $agency : new Agency();
    }

    private static function isString(mixed $value): string
    {
        return is_string($value) ? $value : '';
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
     * @return array<int, array<string, Agency|array<int, string>|string|null>>
     */
    private function getUsersData(Agency $agencyOfMarseille): array
    {
        return [
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
                'password' => 'eAzZ?ez21d!zezda',
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
    }
}
