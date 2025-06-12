<?php

use League\OAuth2\Server\ResourceServer;
use LiveHelperChatExtension\ssoprovider\providers\Repositories\AccessTokenRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Factory\AppFactory;

$settings = include 'extension/ssoprovider/settings/settings.ini.php';

$resourceServer = new ResourceServer(
    new AccessTokenRepository(),// instance of AccessTokenRepositoryInterface
    $settings['public_key']     // the authorization server's public key
);

$app = AppFactory::create();

// An example endpoint secured with OAuth 2.0
$app->get(
    '/site_admin/ssoprovider/userinfo',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($resourceServer) {
        try {
            // Manually validate the request using the resource server
            $request = $resourceServer->validateAuthenticatedRequest($request);
            
            $user = erLhcoreClassModelUser::fetch($request->getAttribute('oauth_user_id'));

            if (!($user instanceof erLhcoreClassModelUser)) {
                throw new Exception('User not found!');
            }

            $user = [
                'id' => $user->id,
                'displayName' => $user->name_official,
                'email'=> $user->email,
            ];

            $response->getBody()->write(\json_encode($user));

            return $response->withStatus(200);

        } catch (\Exception $exception) {
            $response->getBody()->write($exception->getMessage());
            return $response->withStatus(500);
        }
    }
);

$app->run();
exit;
?>