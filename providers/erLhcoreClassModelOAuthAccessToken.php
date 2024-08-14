<?php

namespace LiveHelperChatExtension\ssoprovider\providers;
class erLhcoreClassModelOAuthAccessToken
{
    use \erLhcoreClassDBTrait;

    public static $dbTable = 'sso_provider_oauth_access_tokens';

    public static $dbSessionHandler = '\LiveHelperChatExtension\ssoprovider\providers\erLhcoreClassOAuthHelper::getSession';

    public static $dbTableId = 'id';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'client_id' => $this->client_id,
            'name' => $this->name,
            'scopes' => $this->scopes,
            'revoked' => $this->revoked,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'expires_at' => $this->expires_at
        );
    }
    public function beforeSave()
    {
        if ($this->created_at === null) {
            $this->created_at = time();
        }

        $this->updated_at = time();
    }

    public function __toString()
    {
        return $this->name;
    }

    public $id = null;
    public $user_id = 0;
    public $client_id = 1;
    public $name = '';
    public $scopes = '';
    public $revoked = '';
    public $created_at = null;
    public $updated_at = null;
    public $expires_at = null;
}

?>