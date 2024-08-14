<?php

namespace LiveHelperChatExtension\ssoprovider\providers;
class erLhcoreClassModelOAuthCodes
{
    use \erLhcoreClassDBTrait;

    public static $dbTable = 'sso_provider_oauth_auth_codes';

    public static $dbSessionHandler = '\LiveHelperChatExtension\ssoprovider\providers\erLhcoreClassOAuthHelper::getSession';

    public static $dbTableId = 'id';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'client_id' => $this->client_id,
            'scopes' => $this->scopes,
            'revoked' => $this->revoked,
            'expires_at' => $this->expires_at
        );
    }

    public $id = null;
    public $user_id = 0;
    public $client_id = '';
    public $scopes = '';
    public $revoked = 0;
    public $expires_at = null;
}

?>