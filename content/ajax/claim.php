<?php

if( isset( $_GET['token'] ) && isset( $_POST['item'] ) && check_ajax_token( 'claim', $_GET['token'] ) ) {

    $response = array();

    if( !$GLOBALS['me'] ) {

        $response['state'] = 'not_logged';
        $response['message'] = t( 'msg_require_login', 'To perform this action you must be logged in.' );

    } else {

        $claim = \user\main::claim_coupon( $_POST['item'], $GLOBALS['me']->ID );

        if( $claim ) {

            if( $claim == 'claimed' ) {

                $response['state'] = 'success';
                $response['message'] = ( isset( $_POST['claimed_message'] ) ? $_POST['claimed_message'] : t( 'msg_claimed', 'Claimed' ) );

            }

        } else {

            $response['state'] = 'error';
            $response['message'] = t( 'unexpected', 'Unexpected' );

        }

    }

    echo json_encode( $response );

    die;

}

echo json_encode( array( 'state' => 'error', 'message' => t( 'unexpected', 'Unexpected' ) ) );