<?php

if( isset( $_GET['token'] ) && isset( $_POST['subscribe'] ) && check_ajax_token( 'subscribe', $_GET['token'] ) ) {

    $response = array();
                                 
    $pd = \site\utils::validate_user_data( $_POST['subscribe'] );

    try {

        $id = $GLOBALS['me'] ? $GLOBALS['me']->ID : 0;

        $type = \user\main::subscribe( $id, $pd );
        $response['state'] = 'success';
        $response['message'] = ( $type == 1 ? sprintf( t( 'newsletter_reqconfirm', "Please check your inbox (%s) and confirm your subscription. (please check Spam directory also)" ), $pd['email'] ) : t( 'newsletter_success', "You had been successfully subscribed, thank you!" ) );

        unset( $_SESSION['csrf']['ajax_subscribe'] );

    }

    catch( Exception $e ){
        $response['state'] = 'error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode( $response );

    die;

}

echo json_encode( array( 'state' => 'error', 'message' => t( 'unexpected', 'Unexpected' ) ) );