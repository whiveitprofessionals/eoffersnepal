<?php

if( isset( $_GET['token'] ) && isset( $_POST['register'] ) && check_ajax_token( 'register', $_GET['token'] ) ) {

    $response = array();

    $pd = \site\utils::validate_user_data( $_POST['register'] );

    try {

        $session = \user\main::register( $pd );

        $_SESSION['session'] = $session;

        $response['state'] = 'success';
        $response['message'] = t( 'register_success', "You have successfully registered." );
        $response['session'] = $GLOBALS['siteURL'] . 'setSession.php';

        unset( $_SESSION['csrf']['ajax_register'] );

    }

    catch( Exception $e ){
        $response['state'] = 'error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode( $response );

    die;

}

echo json_encode( array( 'state' => 'error', 'message' => t( 'unexpected', 'Unexpected' ) ) );