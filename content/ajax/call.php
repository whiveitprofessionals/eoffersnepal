<?php

if( isset( $_GET['token'] ) && isset( $_GET['type'] ) &&
( $callback = ajax_callback_value( $_GET['type'] ) ) &&
( ( $callback = \site\utils::check_callback( $callback ) ) ) &&
check_ajax_token( $_GET['type'], $_GET['token'] ) ) {
   
    echo call_user_func( $callback );

}