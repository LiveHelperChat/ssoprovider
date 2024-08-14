<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace LiveHelperChatExtension\ssoprovider\providers\Repositories;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use LiveHelperChatExtension\ssoprovider\providers\Entities\AccessTokenEntity;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    /*
     *
     *https://github.com/spring-attic/spring-security-oauth/blob/main/spring-security-oauth2/src/test/resources/schema.sql
     * https://admin.qu.lt/sqlbud/?username=root&db=pavlo_dice&table=oauth_access_tokens
     * */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        // https://oauth2.thephpleague.com/access-token-repository-interface/
        /*\erLhcoreClassLog::write(print_r($accessTokenEntity->getIdentifier(),true));
        \erLhcoreClassLog::write(print_r($accessTokenEntity->getExpiryDateTime(),true));
        \erLhcoreClassLog::write(print_r($accessTokenEntity->getUserIdentifier(),true));
        \erLhcoreClassLog::write(print_r($accessTokenEntity->getClient()->getIdentifier(),true));
        \erLhcoreClassLog::write(print_r('persistNewAccessToken - ' . $accessTokenEntity->getIdentifier(),true));*/


        $accessToken = new \LiveHelperChatExtension\ssoprovider\providers\erLhcoreClassModelOAuthAccessToken();
        $accessToken->id = $accessTokenEntity->getIdentifier();
        $accessToken->client_id = $accessTokenEntity->getClient()->getIdentifier();
        $accessToken->user_id = $accessTokenEntity->getUserIdentifier();
        $accessToken->expires_at = $accessTokenEntity->getExpiryDateTime()->getTimestamp();
        $accessToken->scopes = json_encode($accessTokenEntity->getScopes());
        $accessToken->saveThis();
        // Some logic here to save the access token to a database
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAccessToken($tokenId)
    {
        // \erLhcoreClassLog::write(print_r('revokeAccessToken',true));

        $accessToken = \LiveHelperChatExtension\ssoprovider\providers\erLhcoreClassModelOAuthAccessToken::fetch($tokenId);

        if (is_object($accessToken)) {
            $accessToken->revoked = 1;
            $accessToken->saveThis();
        }

        // Some logic here to revoke the access token
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenRevoked($tokenId)
    {
        // \erLhcoreClassLog::write(print_r('isAccessTokenRevoked - ' . $tokenId,true));

        $accessToken = \LiveHelperChatExtension\ssoprovider\providers\erLhcoreClassModelOAuthAccessToken::findOne(['filter' => ['id' => $tokenId]]);

        // Refresh token does not exists
        if (!is_object($accessToken)) {
            return true;
        }

        return !($accessToken->revoked === 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);

        return $accessToken;
    }
}