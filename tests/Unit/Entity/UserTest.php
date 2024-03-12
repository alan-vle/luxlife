<?php

namespace App\Tests\Entity;

use App\Entity\User\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function getEntity(): User
    {
        return (new User())
            ->setFullName('Test FullName')
            ->setEmail('test2@example.com')
            ->setAddress('1 rue du tricot, 13003 Marseille')
            ->setPhoneNumber('679847568')
            ->setBirthDate(\DateTime::createFromFormat('d/m/Y', '28/01/2001'))
        ;

    }

    public function testEntityIsValid(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $user = $this->getEntity();

        $errors = $container->get('validator')->validate($user);
        $this->assertCount(0, $errors);
    }

    public function testInvalidEmail(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $user = $this->getEntity();
        $user->setEmail('email_invalid');

        $errors = $container->get('validator')->validate($user);
        $this->assertCount(1, $errors);
    }
}
