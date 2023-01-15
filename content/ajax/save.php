<?php

if( isset( $_GET['token'] ) && isset( $_POST['item'] ) && isset( $_POST['type'] ) && check_ajax_token( 'save', $_GET['token'] ) ) {

    $response = array();

    if( !$GLOBALS['me'] ) {

        $response['state'] = 'not_logged';
        $response['message'] = t( 'msg_require_login', 'To perform this action you must be logged in.' );

    } else {

        $favorite = \user\main::save( $GLOBALS['me']->ID, $_POST['item'], $_POST['type'] );

        if( $favorite && in_array( $favorite, array( 'saved',  'unsaved' ) ) ) {

            if( $favorite == 'saved' ) {

                $response['state'] = 'success';
                $response['message'] = ( isset( $_POST['added_message'] ) ? $_POST['added_message'] : t( 'msg_added_to_favorites', 'Added to favorites' ) );

            } else if( $favorite == 'unsaved' ) {

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