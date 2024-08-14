<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace LiveHelperChatExtension\ssoprovider\providers\Repositories;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use LiveHelperChatExtension\ssoprovider\providers\Entities\AuthCodeEntity;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $authCode = new \LiveHelperChatExtension\ssoprovider\providers\erLhcoreClassModelOAuthCodes();
        $authCode->id = $authCodeEntity->getIdentifier();
        $authCode->client_id = $authCodeEntity->getClient()->getIdentifier();
        $authCode->scopes = json_encode($authCodeEntity->getScopes());
        $authCode->user_id = $authCodeEntity->getUserIdentifier();
        $authCode->expires_at = $authCodeEntity->getExpiryDateTime()->getTimestamp();
        $authCode->saveThis();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAuthCode($codeId)
    {
        $authCode = \LiveHelperChatExtension\ssoprovider\providers\erLhcoreClassModelOAuthCodes::fetch($codeId);

        if (is_object($authCode)) {
            $authCode->revoked = 1;
            $authCode->saveThis();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthCodeRevoked($codeId)
    {
        $authCode = \LiveHelperChatExtension\ssoprovider\providers\erLhcoreClassModelOAuthCodes::findOne(['filter' => ['id' => $codeId]]);

        // Refresh token does not exists
        if (!is_object($authCode)) {
            return true;
        }

        return !($authCode->revoked === 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }
}