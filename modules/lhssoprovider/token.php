<?php

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
use Slim\Factory\AppFactory;

// Create authorization server factory function
function createAuthorizationServer($clientId) {
    // Init our repositories
    $clientRepository = new ClientRepository();
    $scopeRepository = new ScopeRepository();
    $accessTokenRepository = new AccessTokenRepository();
    $refreshTokenRepository = new RefreshTokenRepository();
    $authCodeRepository = new AuthCodeRepository();
    $settingsList = include 'extension/ssoprovider/settings/settings.ini.php';

    $settings = $settingsList[$clientId];
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
}

$app = AppFactory::create();

$app->post('/site_admin/ssoprovider/token', function (ServerRequestInterface $request, ResponseInterface $response) {
    try {
        // Parse the POST data to get client_id
        $parsedBody = $request->getParsedBody();
        $clientId = $parsedBody['client_id'] ?? null;
        
        if (!$clientId) {
            throw new \Exception('client_id is required');
        }
        
        // Create authorization server with the client_id
        $server = createAuthorizationServer($clientId);
        
        return $server->respondToAccessTokenRequest($request, $response);
    } catch (OAuthServerException $exception) {
        return $exception->generateHttpResponse($response);
    } catch (\Exception $exception) {
        $response->getBody()->write($exception->getMessage());
        return $response->withStatus(500);
    }
});

$app->run();
exit;
?>