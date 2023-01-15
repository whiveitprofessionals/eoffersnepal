<?php

if( \user\main::banned( 'login' ) || \user\main::banned( 'register' ) ) {
    header( 'Location: ' . $GLOBALS['siteURL'] );
    die;
} else if( \query\main::get_option( 'facebook_appID' ) === '' || \query\main::get_option( 'facebook_secret' ) === '' ) {
    die( 'This service is unavailable for the moment.' );
}

require_once DIR . '/' . LBDIR . '/facebook-sdk-5.5/autoload.php';

$fb = new Facebook\Facebook([
    'app_id' => \query\main::get_option( 'facebook_appID' ), // Replace {app-id} with your app id
    'app_secret' => \query\main::get_option( 'facebook_secret' ),
    'default_graph_version' => 'v5.0'
]);

$helper = $fb->getRedirectLoginHelper();

if( empty( $_GET['code'] ) ) {
    header( 'Location:' . $helper->getLoginUrl( $GLOBALS['siteURL'] . '?plugin=facebook_login', [ 'email' ] ) );
    die;
}

try {
    $token = $helper->getAccessToken( $GLOBALS['siteURL'] . '?plugin=facebook_login' );
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    die( 'Graph returned an error: ' . $e->getMessage() );
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    die( 'Facebook SDK returned an error: ' . $e->getMessage() );
}

try {
    $response = $fb->get( '/me?fields=id,name,email', $token );
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    die( 'Graph returned an error: ' . $e->getMessage() );
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    die( 'Facebook SDK returned an error: ' . $e->getMessage() );
}

$me = $response->getGraphUser();

if( !isset( $me['email'] ) || !filter_var( $me['email'], FILTER_VALIDATE_EMAIL ) ) {
    die( 'Your facebook account is not associated with a valid email address.' );
}

$session = \user\main::insert_user( array( 'username' => $me['name'], 'email' => $me['email'] ), true, true );

$_SESSION['session'] = $session;

header( 'Location: ' . $GLOBALS['siteURL'] . 'setSession.php' );