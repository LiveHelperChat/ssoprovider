<?php

namespace LiveHelperChatExtension\ssoprovider\providers;

class erLhcoreClassOAuthHelper
{
    public static function getSession()
    {
        if (!isset (self::$persistentSession)) {
            self::$persistentSession = new \ezcPersistentSession (\ezcDbInstance::get(), new \ezcPersistentCodeManager ('./extension/ssoprovider/pos'));
        }

        return self::$persistentSession;
    }

    public static function validateBearer($jwt)
    {
        $settings = include 'extension/ssoprovider/settings/settings.ini.php';

        $jwtConfiguration = \Lcobucci\JWT\Configuration::forSymmetricSigner(
            new \Lcobucci\JWT\Signer\Rsa\Sha256(),
            \Lcobucci\JWT\Signer\Key\InMemory::plainText('empty', 'empty')
        );

        $clock = new \Lcobucci\Clock\SystemClock(new \DateTimeZone(\date_default_timezone_get()));
        $jwtConfiguration->setValidationConstraints(
            new \Lcobucci\JWT\Validation\Constraint\LooseValidAt($clock, null),
            new \Lcobucci\JWT\Validation\Constraint\SignedWith(
                new \Lcobucci\JWT\Signer\Rsa\Sha256(),
                \Lcobucci\JWT\Signer\Key\InMemory::plainText($settings['public_key'], '')
            )
        );

        try {
            // Attempt to parse the JWT
            $token = $jwtConfiguration->parser()->parse($jwt);
        } catch (\Lcobucci\JWT\Exception $exception) {
            throw \League\OAuth2\Server\Exception\OAuthServerException::accessDenied($exception->getMessage(), null, $exception);
        }

        try {
            // Attempt to validate the JWT
            $constraints = $jwtConfiguration->validationConstraints();
            $jwtConfiguration->validator()->assert($token, ...$constraints);
        } catch (\Lcobucci\JWT\Validation\RequiredConstraintsViolated $exception) {
            throw \League\OAuth2\Server\Exception\OAuthServerException::accessDenied('Access token could not be verified', null, $exception);
        }

        $claims = $token->claims();

        $accessTokenRepository = new \LiveHelperChatExtension\ssoprovider\providers\Repositories\AccessTokenRepository();

        // Check if token has been revoked
        if ($accessTokenRepository->isAccessTokenRevoked($claims->get('jti'))) {
            throw \League\OAuth2\Server\Exception\OAuthServerException::accessDenied('Access token has been revoked');
        }

        return [
            'status' => \erLhcoreClassChatEventDispatcher::STOP_WORKFLOW,
            'user_id' => $claims->get('sub'),
        ];
    }

    private static $persistentSession;
}

?>