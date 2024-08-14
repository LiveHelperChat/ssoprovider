<?php

use Laminas\Diactoros\Stream;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use LiveHelperChatExtension\ssoprovider\providers\Entities\UserEntity;
use LiveHelperChatExtension\ssoprovider\providers\Repositories\AccessTokenRepository;
use LiveHelperChatExtension\ssoprovider\providers\Repositories\AuthCodeRepository;
use LiveHelperChatExtension\ssoprovider\providers\Repositories\ClientRepository;
use LiveHelperChatExtension\ssoprovider\providers\Repositories\RefreshTokenRepository;
use LiveHelperChatExtension\ssoprovider\providers\Repositories\ScopeRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

$app = new App([
    'settings'    => [
        'displayErrorDetails' => true,
    ],
    AuthorizationServer::class => function () {
        // Init our repositories
        $clientRepository = new ClientRepository();
        $scopeRepository = new ScopeRepository();
        $accessTokenRepository = new AccessTokenRepository();
        $refreshTokenRepository = new RefreshTokenRepository();
        $authCodeRepository = new AuthCodeRepository();
        $settingsList = include 'extension/ssoprovider/settings/settings.ini.php';

        $settings = $settingsList[$_POST['client_id']];
        $privateKeyPath = $settingsList['private_key'];

        // Setup the authorization server
        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKeyPath,
            $settings['client_secret']
        );

        // Enable the refresh token grant on the server
        $grant = new RefreshTokenGrant($refreshTokenRepository);
        $grant->setRefreshTokenTTL(new DateInterval('P1M')); // The refresh token will expire in 1 month
        $server->enableGrantType(
            $grant,
            new DateInterval('PT1H') // The new access token will expire after 1 hour
        );

        // Enable the authentication code grant on the server with a token TTL of 1 hour
        $server->enableGrantType(
            new AuthCodeGrant(
                $authCodeRepository,
                $refreshTokenRepository,
                new \DateInterval('PT10M')
            ),
            new \DateInterval('PT1H')
        );

        return $server;
    },
]);

$app->post('/site_admin/ssoprovider/token', function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
    /* @var \League\OAuth2\Server\AuthorizationServer $server */
    $server = $app->getContainer()->get(AuthorizationServer::class);
    try {
        return $server->respondToAccessTokenRequest($request, $response);
    } catch (OAuthServerException $exception) {
        return $exception->generateHttpResponse($response);
    } catch (\Exception $exception) {
        $body = new Stream('php://temp', 'r+');
        $body->write($exception->getMessage());
        return $response->withStatus(500)->withBody($body);
    }
});

$app->run();
exit;
?>