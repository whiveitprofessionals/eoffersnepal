<?php
 
if( class_exists( 'mysqli' ) ) {
    try {
        $db = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
    }

    catch( \Exception $e ) {
        if( !isset( $db ) ) {
            if( is_dir( 'install' ) ) {
                require_once 'install/index.php';
                die;
            }
            die( 'Failed to connect to MySQL' );
        } else if( ( !( $db_conn = $db->connect_errno ) ) && is_dir( 'install' ) ) {
            require_once 'install/index.php';
            die;
        } else if( $db_conn ) {
            die('Failed to connect to MySQL (' . $db->connect_errno . ') ' . $db->connect_error);
        }
    }
}