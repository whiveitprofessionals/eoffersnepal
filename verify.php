<?php

/** START SESSION */

session_start();

/** REPORT ALL PHP ERRORS */

error_reporting( E_ALL );

/** REQUIRE SETTINGS */

require_once 'settings.php';

/** CONNECT TO DB */

require_once IDIR . '/site/db.php';

if( ( !isset( $db ) || ( $db_conn = $db->connect_errno ) ) && is_dir( 'install' ) ) {

    require_once 'install/index.php';
    die;

} else if( $db_conn ) {

    die('Failed to connect to MySQL (' . $db->connect_errno . ') ' . $db->connect_error);

}

$db->set_charset( DB_CHARSET );

/** */

spl_autoload_register(function ( $cn ) {

    $type = strstr( $cn, '\\', true );

    if( $type == 'plugin' ) {
        $cn = str_replace( '\\', '/', $cn );
        if( file_exists( ( $file = DIR . '/' . UPDIR . '/' . substr( $cn, strpos( $cn, '/' )+1 ) . '.php' ) ) )
        require_once $file;
    } else {
        if( file_exists( ( $file = DIR . '/' . IDIR . '/' . str_replace( '\\', '/', $cn ) . '.php' ) ) )
        require_once $file;
    }

});

/** */

$load = new \main\load;

if( isset( $_GET['action'] ) && $_GET['action'] == 'unsubscribe' ) {

    echo '<!DOCTYPE html>

    <html>
        <head>

            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
            <meta name="robots" content="noindex, nofollow">
            <title>' . t( 'uunsubscr_metatitle', "Unsubscribe" ) . '</title>
            <link href="//fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900" rel="stylesheet" />
            <link href="' . MISCDIR . '/verify.css" media="all" rel="stylesheet" />

        </head>

    <body>
        <section class="msg">';

        if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

            if( isset( $_POST['token'] ) && isset( $_POST['email'] ) && \site\utils::check_csrf( $_POST['token'], 'sendunsubscr_csrf' ) ) {

                try {

                    $type = \user\main::unsubscribe( array( 'email' => $_POST['email'] ) );
                    if( $type == 1 ) echo '<div class="success">' . sprintf( t( 'uunsubscr_reqsent', "Please check your inbox (%s) and confirm the unsubscription." ), $_POST['email'] ) . '</div>';
                    else echo '<div class="success">' . t( 'uunsubscr_ok', "You had been unsubscribed." ) . '</div>';

                }

                catch ( Exception $e ) {
                    echo '<div class="error">' . $e->getMessage() . '</div>';
                }

            }

        }

        $csrf = $_SESSION['sendunsubscr_csrf'] = \site\utils::str_random(10);

        echo '<h2 style="color: #000;">' . t( 'uunsubscr_title', "Unsubscribe" ) . '</h2>
        ' . sprintf( t( 'uunsubscr_body', 'If you are subscribed to our newsletter and you wish to unsubscribe, please insert your email address and then click on "Unsubscribe me".' ), '<span id="seconds">5</span>' ) . ' <br /><br />
        <form method="POST" action="#" autocomplete="off">
        <input type="email" name="email" value="' . ( isset( $_GET['email'] ) ? esc_html( $_GET['email'] ) : '' ) . '" required />
        <input type="hidden" name="token" value="' . $csrf . '" />
        <button>' . t( 'uunsubscr_button', "Unsubscribe me" ) . '</button>
        </form> <br />
        <a href="index.php">' . t( 'cancel', "Cancel" ) . '</a>
        </section>
    </body>
    </html>';

    die;

} else if( isset( $_GET['action'] ) && isset( $_GET['email'] ) && isset( $_GET['token'] ) && $_GET['action'] == 'unsubscribe2' && \user\mail_sessions::check( 'unsubscription', array( 'email' => $_GET['email'], 'session' => $_GET['token'] ) ) ) {

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "newsletter WHERE email = ?" );
    $stmt->bind_param( "s", $_GET['email'] );
    if( $stmt->execute() ) {
        do_action( 'user-unsubscribe-confirmed', array( 'email' => $post['email'] ) );
    }
    $stmt->close();

    \user\mail_sessions::clear( 'unsubscription', array( 'email' => $_GET['email'] ) );

    echo '<!DOCTYPE html>

    <html>
        <head>

            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
            <meta name="robots" content="noindex, nofollow">
            <meta http-equiv="Refresh" content="5; url=index.php" />

            <title>' . t( 'uunsubscr2_metatitle', "Unsubscribed" ) . '</title>

            <link href="' . MISCDIR . '/verify.css" media="all" rel="stylesheet" />

            <script type="text/javascript">

            var i = 5;

            var interval = setInterval(function(){

            var tag = document.getElementById("seconds");
            tag.innerHTML = i;
            i--;

            if( i == 0 ) {
                clearInterval(interval);
            }

            }, 1000);

            </script>

        </head>

    <body>
        <section class="msg">
        <h2>' . t( 'uunsubscr2_title', "You are now unsubscribed" ) . '</h2>
        ' . sprintf( t( 'uunsubscr2_body', "You had been successfully unsubscribed, you will be redirected in %s seconds." ), '<span id="seconds">5</span>' ) . ' <br /><br />
        <a href="index.php">' . t( 'verify_clickhere', "click here" ) . '</a>
        </section>
    </body>
    </html>';

    die;

} else if( isset( $_GET['action'] ) && isset( $_GET['email'] ) && isset( $_GET['token'] ) && $_GET['action'] == 'subscribe' && \user\mail_sessions::check( 'subscription', array( 'email' => $_GET['email'], 'session' => $_GET['token'] ) ) ) {

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "newsletter SET econf = 1 WHERE email = ?" );
    $stmt->bind_param( "s", $_GET['email'] );
    if( $stmt->execute() ) {
        do_action( 'user-subscribe-confirmed', array( 'email' => $_GET['email'] ) );
    }
    $stmt->close();

    \user\mail_sessions::clear( 'subscription', array( 'email' => $_GET['email'] ) );

    echo '<!DOCTYPE html>

    <html>
        <head>

            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
            <meta name="robots" content="noindex, nofollow">
            <meta http-equiv="Refresh" content="5; url=index.php" />

            <title>' . t( 'usubscr_metatitle', "Subscribed" ) . '</title>

            <link href="' . MISCDIR . '/verify.css" media="all" rel="stylesheet" />

            <script type="text/javascript">

            var i = 5;

            var interval = setInterval(function(){

            var tag = document.getElementById("seconds");
            tag.innerHTML = i;
            i--;

            if( i == 0 ) {
                clearInterval(interval);
            }

            }, 1000);

            </script>

        </head>

    <body>
        <section class="msg">
        <h2>' . t( 'usubscr_title', "Thank you!" ) . '</h2>
        ' . sprintf( t( 'usubscr_body', "You had been successfully subscribed, you will be redirected in %s seconds." ), '<span id="seconds">5</span>' ) . ' <br /><br />
        <a href="index.php">' . t( 'verify_clickhere', "click here" ) . '</a>
        </section>
    </body>
    </html>';

    die;

} else if( isset( $_GET['user'] ) && isset( $_GET['token'] ) && \user\mail_sessions::check( 'confirmation', array( 'user' => (int) $_GET['user'], 'session' => $_GET['token'] ) ) ) {

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET valid = 1 WHERE id = ?" );
    $stmt->bind_param( "i", $_GET['user'] );
    $stmt->execute();
    @$stmt->close();

    \user\mail_sessions::clear( 'confirmation', array( 'user' => (int) $_GET['user'] ) );

    // check if user has been refered

    $uinfo = \query\main::user_info( $_GET['user'] );
    if( !empty( $uinfo->refid ) ) {
        \user\update::add_points( $uinfo->refid, \query\main::get_option( 'u_points_refer' ) );
    }

    echo '<!DOCTYPE html>

    <html>
        <head>

            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
            <meta name="robots" content="noindex, nofollow">
            <meta http-equiv="Refresh" content="5; url=index.php" />

            <title>' . t( 'uverify_metatitle', "Account Verified" ) . '</title>

            <link href="' . MISCDIR . '/verify.css" media="all" rel="stylesheet" />

            <script type="text/javascript">

            var i = 5;

            var interval = setInterval(function(){

            var tag = document.getElementById("seconds");
            tag.innerHTML = i;
            i--;

            if( i == 0 ) {
                    clearInterval(interval);
            }

            }, 1000);

            </script>

        </head>

    <body>
        <section class="msg">
        <h2>' . t( 'uverify_title', "Thank you" ) . '</h2>
        ' . sprintf( t( 'uverify_body', "Your account had been successfully verified, you will be redirected in %s seconds." ), '<span id="seconds">5</span>' ) . ' <br /><br />
        <a href="index.php">' . t( 'verify_clickhere', "click here" ) . '</a>
        </section>
    </body>
    </html>';

    die;

}

$load->page_load_after();

$db->close();

header( 'Location: index.php' );