<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace LiveHelperChatExtension\ssoprovider\providers\Repositories;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use LiveHelperChatExtension\ssoprovider\providers\Entities\ClientEntity;

class ClientRepository implements ClientRepositoryInterface
{
    const CLIENT_NAME = 'Live Helper Chat';
    const REDIRECT_URI = '';

    /**
     * {@inheritdoc}
     */
    public function getClientEntity($clientIdentifier)
    {
        $settings = include 'extension/ssoprovider/settings/settings.ini.php';

        $client = new ClientEntity();

        $client->setIdentifier($clientIdentifier);
        $client->setName($settings['client_name']);
        $client->setRedirectUri($settings['redirect_url']);
        $client->setConfidential();

        return $client;
    }

    /**
     * {@inheritdoc}
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        $settings = include 'extension/ssoprovider/settings/settings.ini.php';

        $clients = [
            $settings['client_id'] => [
                'secret'          => \password_hash($settings['client_secret'], PASSWORD_BCRYPT),
                'name'            => $settings['client_name'],
                'redirect_uri'    => $settings['redirect_url'],
                'is_confidential' => true,
            ],
        ];

        // Check if client is registered
        if (\array_key_exists($clientIdentifier, $clients) === false) {
            return false;
        }

        if (
            $clients[$clientIdentifier]['is_confidential'] === true
            && \password_verify($clientSecret, $clients[$clientIdentifier]['secret']) === false
        ) {
            return false;
        }

        return true;
    }
}