<?php

use Laminas\Diactoros\Stream;
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


$app = new App([
    'settings'    => [
        'displayErrorDetails' => true,
    ],
    AuthorizationServer::class => function () {
        // Init our repositories
        $clientRepository = new ClientRepository();
        $scopeRepository = new ScopeRepository();
        $accessTokenRepository = new AccessTokenRepository();
        $authCodeRepository = new AuthCodeRepository();
        $refreshTokenRepository = new RefreshTokenRepository();

        $settingsList = include 'extension/ssoprovider/settings/settings.ini.php';
        $settings = $settingsList[$_GET['client_id']];

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
    },
]);

$app->get('/site_admin/ssoprovider/authorize',function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
    /* @var \League\OAuth2\Server\AuthorizationServer $server */
    $server = $app->getContainer()->get(AuthorizationServer::class);

    try {

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
        $body = new Stream('php://temp', 'r+');
        $body->write($exception->getMessage());
        return $response->withStatus(500)->withBody($body);
    }
});

$app->run();
exit;
?>