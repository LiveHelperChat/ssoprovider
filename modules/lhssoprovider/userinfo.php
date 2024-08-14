<?php

use League\OAuth2\Server\ResourceServer;
use LiveHelperChatExtension\ssoprovider\providers\Repositories\AccessTokenRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

$app = new App([
    // Add the resource server to the DI container
    ResourceServer::class => function () {

        $settings = include 'extension/ssoprovider/settings/settings.ini.php';

        $server = new ResourceServer(
            new AccessTokenRepository(),// instance of AccessTokenRepositoryInterface
            $settings['public_key']     // the authorization server's public key
        );

        return $server;
    },
]);

// Add the resource server middleware which will intercept and validate requests
$app->add(
    new \League\OAuth2\Server\Middleware\ResourceServerMiddleware(
        $app->getContainer()->get(ResourceServer::class)
    )
);

// An example endpoint secured with OAuth 2.0
$app->get(
    '/site_admin/ssoprovider/userinfo',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
        try {
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
            $body = new Stream('php://temp', 'r+');
            $body->write($exception->getMessage());
            return $response->withStatus(500)->withBody($body);
        }
    }
);

$app->run();
exit;
?>