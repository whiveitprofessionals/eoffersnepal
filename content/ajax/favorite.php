<?php

if( isset( $_GET['token'] ) && isset( $_POST['store'] ) && check_ajax_token( 'favorite', $_GET['token'] ) ) {

    $response = array();

    if( !$GLOBALS['me'] ) {

        $response['state'] = 'not_logged';
        $response['message'] = t( 'msg_require_login', 'To perform this action you must be logged in.' );

    } else {

        $favorite = \user\main::favorite( $GLOBALS['me']->ID, $_POST['store'] );

        if( $favorite && in_array( $favorite, array( 'added',  'removed' ) ) ) {

            if( $favorite == 'added' ) {

                $response['state'] = 'success';
                $response['message'] = ( isset( $_POST['added_message'] ) ? $_POST['added_message'] : t( 'msg_added_to_favorites', 'Added to favorites' ) );

            } else if( $favorite == 'removed' ) {

                $response['state'] = 'success';
                $response['message'] = ( isset( $_POST['removed_message'] ) ? $_POST['removed_message'] : t( 'msg_removed_from_favorites', 'Removed from favorites' ) );

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