<?php


namespace CoralMedia\Bundle\SecurityBundle\Test\Application;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use CoralMedia\Bundle\SecurityBundle\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiSecurityControllerTest extends ApiTestCase
{
    use RecreateDatabaseTrait;

    protected function setUpUser(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword(
            static::getContainer()->get('security.user_password_hasher')
                ->hashPassword($user, '$3CR3T')
        );

        $manager = static::getContainer()->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testApiLogout(): void
    {
        $client = static::createClient()
            ->withOptions(
                ['base_uri' => 'https://localhost:8000/api/']
            );

        $this->setUpUser();

        // retrieve a token
        $response = $client->request('POST', 'security/token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'user' => 'test@example.com',
                'password' => '$3CR3T',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test authorized
        $json = $client->request('GET', 'security/logout', ['auth_bearer' => $json['token']])
            ->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('message', $json);
    }
}