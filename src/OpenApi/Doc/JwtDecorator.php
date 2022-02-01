<?php

namespace CoralMedia\Bundle\SecurityBundle\OpenApi\Doc;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;
use ArrayObject;

class JwtDecorator implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Token-Authentication'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);
        $schemas['Token-Credentials'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'user' => [
                    'type' => 'string',
                    'example' => 'johndoe@example.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'my password',
                ],
            ],
        ]);

        $schemas['Token-Refresh'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'your refresh token here',
                ],
            ],
        ]);

        $pathItem = $this->getToken();
        $openApi->getPaths()->addPath('/api/security/token', $pathItem);

        $pathItem = $this->refreshToken();
        $openApi->getPaths()->addPath('/api/security/token/refresh', $pathItem);

        return $openApi;
    }

    private function getToken(): Model\PathItem
    {
        return new Model\PathItem(
            'JWT Token',
            null,
            null,
            null,
            null,
            new Model\Operation(
                'postCredentialsItem',
                ['Token'],
                [
                    '200' => [
                        'description' => 'Get JWT token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token-Authentication',
                                ],
                            ],
                        ],
                    ],
                ],
                'Get JWT token.',
                '',
                null,
                [],
                new Model\RequestBody(
                    $description = 'Generate new JWT Token',
                    $content = new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Token-Credentials',
                            ],
                        ],
                    ]),
                ),
            ),
        );
    }

    private function refreshToken(): Model\PathItem
    {
        return new Model\PathItem(
            'JWT Refresh Token',
            null,
            null,
            null,
            null,
            new Model\Operation(
                'postRefreshTokenItem',
                ['Token'],
                [
                    '200' => [
                        'description' => 'Get new JWT token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token-Authentication',
                                ],
                            ],
                        ],
                    ],
                ],
                'Get JWT token.',
                '',
                null,
                [],
                new Model\RequestBody(
                    $description = 'Generate new JWT Token',
                    $content = new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Token-Refresh',
                            ],
                        ],
                    ]),
                ),
            ),
        );
    }
}
