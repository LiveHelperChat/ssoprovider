# SSO Provider

This is SSO provider based on [oauth2-server](https://github.com/thephpleague/oauth2-server). At the moment this integration was tested with https://js.wiki

Implementation is based on example given there https://github.com/thephpleague/oauth2-server/tree/master/examples

## Settings for third party SSO

Change domain where needed


Authorization Endpoint URL:
```
https://chat.example.com/site_admin/ssoprovider/authorize
```

Token Endpoint URL
```
https://chat.example.com/site_admin/ssoprovider/token
```

User Info Endpoint URL
```
https://chat.example.com/site_admin/ssoprovider/userinfo
```

ID Claim
```
id
```

Display Name Claim
```
displayName
```

Email Claim
```
email
```

## Install instructions

* Clone repository and put it in `extension/ssoprovider`
* Modify main settings file `lhc_web/settings/settings.ini.php` and activate extensions
```
...
'extensions' =>
    array (   
    'ssoprovider'
    ),
...
```
* Being in `extension/ssoprovider` do `composer install`
* Copy `extension/ssoprovider/settings/settings.ini.default.php` to `extension/ssoprovider/settings/settings.ini.php`
* Generate private and public keys
```shell
openssl genrsa -out private.key 2048
openssl rsa -in private.key -pubout > public.key
```
* Put generated files content in `private_key` and `public_key` content.
* Put your preferred `client_id`, `client_secret`, `client_name` values.
* Modify `url_login` value. It's full login URL for third party.
* Modify `redirect_url` value. This value was taken from js.wiki back office.

## https://js.wiki integration

At this moment goal of this extension was to have SSO login directly to https://js.wiki in Live Helper Chat. Configuration screenshot.

![image info](https://github.com/LiveHelperChat/ssoprovider/blob/main/doc/js.wiki.png?raw=true)