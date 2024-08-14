<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "sso_provider_oauth_access_tokens";
$def->class = '\LiveHelperChatExtension\ssoprovider\providers\erLhcoreClassModelOAuthAccessToken';

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentManualGenerator' );

foreach (['user_id', 'revoked', 'created_at', 'updated_at', 'expires_at'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

foreach (['client_id', 'name', 'scopes'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

return $def;

?>