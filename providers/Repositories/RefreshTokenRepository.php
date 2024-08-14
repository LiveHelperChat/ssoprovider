<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace LiveHelperChatExtension\ssoprovider\providers\Repositories;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use LiveHelperChatExtension\ssoprovider\providers\Entities\RefreshTokenEntity;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        // Some logic to persist the refresh token in a database
        $accessToken = new \LiveHelperChatExtension\ssoprovider\providers\erLhcoreClassModelOAuthRefreshToken();
        $accessToken->id = $refreshTokenEntity->getIdentifier();
        $accessToken->access_token_id = $refreshTokenEntity->getAccessToken()->getIdentifier();
        $accessToken->expires_at = $refreshTokenEntity->getExpiryDateTime()->getTimestamp();
        $accessToken->saveThis();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeRefreshToken($tokenId)
    {
        // \erLhcoreClassLog::write(print_r("revokeRefreshToken - " . $tokenId,true));

        $refreshToken = \LiveHelperChatExtension\ssoprovider\providers\erLhcoreClassModelOAuthRefreshToken::fetch($tokenId);

        if (is_object($refreshToken)) {
            $refreshToken->revoked = 1;
            $refreshToken->saveThis();
        }

        // Some logic to revoke the refresh token in a database
    }

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        $refreshToken = \LiveHelperChatExtension\ssoprovider\providers\erLhcoreClassModelOAuthRefreshToken::findOne(['filter' => ['id' => $tokenId]]);

        // Refresh token does not exists
        if (!is_object($refreshToken)) {
            return true;
        }

        return !($refreshToken->revoked === 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }
}