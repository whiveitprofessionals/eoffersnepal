<?php

if( isset( $_GET['token'] ) && isset( $_POST['item'] ) && isset( $_POST['vote'] ) && check_ajax_token( 'vote', $_GET['token'] ) ) {

    $response = array();

    if( \user\main::check_vote_ip( $_POST['item'], '', true ) ) {

        $response['state'] = 'success';
        $response['message'] = ( isset( $_POST['already_voted_message'] ) ? $_POST['already_voted_message'] : t( 'msg_already_voted', 'Already voted' ) );

    } else {

        try {

            $vote = \user\main::vote( $_POST['item'], $_POST['vote'] );

            $response['state'] = 'success';
            $response['message'] = ( isset( $_POST['voted_message'] ) ? $_POST['voted_message'] : t( 'msg_voted', 'Voted' ) );

        }

        catch( Exception $e ) {

            // get the exception but assume that this is a multi voting request

            $response['state'] = 'error';
            $response['message'] = $e->getMessage();

        }

    }

    echo json_encode( $response );

}