<?php

// tests/AuthenticationTest.php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User\User;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AuthenticationTest extends ApiTestCase
{
    private ?Client $client = null;

    private ?User $user = null;
    private array $requestParams = [];

    private ?string $apiUrl = null;

    private ?string $token = null;

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testLogin(): void
    {
        $this->client = self::createClient();
        $container = self::getContainer();

        $this->createUser($container);

        // retrieve a token

        $this->apiUrl = $container->getParameter('api_url');
        $this->requestParams = [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'test@example.com',
                'password' => '$3CR3T',
            ],
        ];

        // test not verified email
        $this->accountNotVerified($container);

        // test success
        $this->accountVerified();

        $getUserUrl = sprintf('/users/%s', $this->user->getUuid());
        // test not authorized
        $this->notAuthorized($getUserUrl);

        // test authorized
        $this->authorized($getUserUrl);
    }

    private function createUser(Container $container): void
    {
        $user = new User();
        $user
            ->setFullName('Test FullName')
            ->setEmail('test@example.com')
            ->setPassword(
                $container->get('security.user_password_hasher')->hashPassword($user, '$3CR3T')
            )
            ->setAddress('1 rue du tricot, 13003 Marseille')
            ->setPhoneNumber('679847568')
            ->setBirthDate(\DateTime::createFromFormat('d/m/Y', '28/01/2001'))
        ;

        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();

        $this->user = $user;
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function accountNotVerified(Container $container): void
    {
        $this->client->request('POST', sprintf('%s/login', $this->apiUrl), $this->requestParams);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains(['message' => 'Your email is not verified.']);

        $this->user->setVerifiedEmail(true);
        $manager = $container->get('doctrine')->getManager();
        $manager->flush();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function accountVerified(): void
    {
        $response = $this->client->request('POST', sprintf('%s/login', $this->apiUrl), $this->requestParams);
        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        $this->token = $json['token'];
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function notAuthorized(string $url): void
    {
        $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function authorized(string $url): void
    {
        $this->client->request('GET', $url, ['auth_bearer' => $this->token]);
        $this->assertResponseIsSuccessful();
    }
}
