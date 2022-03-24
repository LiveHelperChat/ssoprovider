<?php

$Module = array( "name" => "SSO Provider");

$ViewList = array();

// Authorization Endpoint URL:
$ViewList['authorize'] = array(
    'params' => array(),
    'functions' => array('use_admin')
);

// Token Endpoint URL
$ViewList['token'] = array(
    'params' => array()
);

// User Info Endpoint URL
$ViewList['userinfo'] = array(
    'params' => array()
);

$FunctionList['use_admin'] = array('explain' => 'Allow operator to login third party apps using Live Helper Chat');

?>