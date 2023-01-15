<?php

session_start();

if( isset( $_SESSION['session'] ) ) {
    setcookie( 'user-session', $_SESSION['session'], time() + 3600 * (24 * 60), '/' );
    header( 'Location: ' . ( !empty( $_GET['back'] ) ? base64_decode( $_GET['back'] ) : 'index.php' ) );
    unset( $_SESSION['session'] );
    die;
}

header( 'Location: index.php' );