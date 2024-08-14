<?php

namespace LiveHelperChatExtension\ssoprovider\providers;
class erLhcoreClassModelOAuthRefreshToken
{
    use \erLhcoreClassDBTrait;

    public static $dbTable = 'sso_provider_oauth_refresh_tokens';

    public static $dbSessionHandler = '\LiveHelperChatExtension\ssoprovider\providers\erLhcoreClassOAuthHelper::getSession';
    
    public static $dbTableId = 'id';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'access_token_id' => $this->access_token_id,
            'revoked' => $this->revoked,
            'expires_at' => $this->expires_at,
        );
    }

    public $id = null;
    public $access_token_id = 0;
    public $revoked = 0;
    public $expires_at = null;

}

?>