<?php

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
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
    $authCodeRepository = new AuthCodeRepository();
    $refreshTokenRepository = new RefreshTokenRepository();
    
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

$app->get('/site_admin/ssoprovider/authorize',function (ServerRequestInterface $request, ResponseInterface $response) {
    try {
        // Parse the query parameters to get client_id
        $queryParams = $request->getQueryParams();
        $clientId = $queryParams['client_id'] ?? null;
        
        if (!$clientId) {
            throw new \Exception('client_id is required');
        }
        
        // Create authorization server with the client_id
        $server = createAuthorizationServer($clientId);

        // Validate the HTTP request and return an AuthorizationRequest object.
        // The auth request object can be serialized into a user's session
        $authRequest = $server->validateAuthorizationRequest($request);

        $userEntity = new UserEntity();
        $userEntity->setUser(erLhcoreClassUser::instance()->getUserData());

        // Once the user has logged in set the user on the AuthorizationRequest
        $authRequest->setUser($userEntity);

        // Once the user has approved or denied the client update the status
        // (true = approved, false = denied)
        $authRequest->setAuthorizationApproved(true);

        // Return the HTTP redirect response
        return $server->completeAuthorizationRequest($authRequest, $response);
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