<?php

if( isset( $_GET['token'] ) && isset( $_POST['write_review_form'] ) && check_ajax_token( 'write_review', $_GET['token'] ) ) {

    $response = array();

    $pd = \site\utils::validate_user_data( $_POST['write_review_form'] );

    if( !isset( $pd['store_id'] ) || !store_exists( $pd['store_id'] ) )
    die( json_encode( array( 'state' => 'error', 'message' => t( 'unexpected', 'Unexpected' ) ) ) );
    
    try {

        $session = \user\main::write_review( $pd['store_id'], $GLOBALS['me']->ID, $pd );

        $_SESSION['session'] = $session;

        $response['state'] = 'success';
        $response['message'] = t( 'write_review_success', "Your review has been added" );

    }

    catch( Exception $e ) {
        $response['state'] = 'error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode( $response );

    die;

}

echo json_encode( array( 'state' => 'error', 'message' => t( 'unexpected', 'Unexpected' ) ) );