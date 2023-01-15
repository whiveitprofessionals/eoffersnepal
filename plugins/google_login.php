<?php

if( \user\main::banned( 'login' ) || \user\main::banned( 'register' ) ) {
    header( 'Location: ' . $GLOBALS['siteURL'] );
    die;
} else if( \query\main::get_option( 'google_clientID' ) === '' || \query\main::get_option( 'google_secret' ) === '' || \query\main::get_option( 'google_ruri' ) === '' ) {
    die( 'This service is unavailable for the moment.' );
}

require_once DIR . '/' . LBDIR . '/google-api-php-client-master/vendor/autoload.php';

$client = new Google_Client();
$client->setApplicationName('Login to ' . \query\main::get_option( 'sitename' ));
$client->setClientId( \query\main::get_option( 'google_clientID' ) );
$client->setClientSecret( \query\main::get_option( 'google_secret' ) );
$client->setRedirectUri( \query\main::get_option( 'google_ruri' ) );
$client->setScopes( 'email' );
$client->addScope( 'profile' );

if ( !empty( $_GET['code'] ) ) {

    try {

        $client->authenticate( $_GET['code'] );
        $_SESSION['access_token'] = $client->getAccessToken();

    } catch( Exception $e ) {
        echo $e->getMessage();
        die;
    }

}

if ( isset( $_SESSION['access_token'] ) ) {
    $client->setAccessToken( $_SESSION['access_token'] );
}

if ( $client->getAccessToken() ) {

    $_SESSION['access_token'] = $client->getAccessToken();
    $token_data = $client->verifyIdToken();

}

if( isset( $token_data ) ) {

    $me = (new Google_Service_Oauth2( $client ))->userinfo_v2_me->get();

    if( !isset( $me->email ) || !filter_var( $me->email, FILTER_VALIDATE_EMAIL ) ) {

    echo 'Your Google+ account it\'s not associated with a valid email address.';

    die;

    }

    $session = \user\main::insert_user( array( 'username' => $me->name, 'email' => $me->email ), true, true );

    $_SESSION['session'] = $session;

    header( 'Location: ' . $GLOBALS['siteURL'] . 'setSession.php' );

} else {
    header( 'Location: ' . $client->createAuthUrl() );
}