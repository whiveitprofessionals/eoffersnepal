<?php

/** START SESSION */

session_start();

/** REPORT ALL PHP ERRORS */

error_reporting( E_ALL );

/** require_once FILES */

require_once '../settings.php';

/** OTHER INIT FUNCTIONS */

define( 'IS_ADMIN_PANEL', true );

/** CONNECT TO DB */

require_once DIR . '/' . IDIR . '/site/db.php';

if( !isset( $db ) || $db->connect_errno ) {
  header( 'Location: ../index.php' );
  die;
}

$db->set_charset( DB_CHARSET );

/** */

spl_autoload_register( function ( $cn ) {

    $type = strstr( $cn, '\\', true );

    if( $type == 'plugin' ) {
        $cn = str_replace( '\\', '/', $cn );
        if( file_exists( ( $file = DIR . '/' . UPDIR . '/' . substr( $cn, strpos( $cn, '/' )+1 ) . '.php' ) ) )
        require_once $file;
    } else if( $type == 'admin' ) {
        $cn = str_replace( '\\', '/', $cn );
        if( file_exists( ( $file = DIR . '/' . ADMINDIR . '/includes/' . substr( $cn, strpos( $cn, '/' )+1 ) . '.php' ) ) )
        require_once $file;
    } else {
        if( file_exists( ( $file = DIR . '/' . IDIR . '/' . str_replace( '\\', '/', $cn ) . '.php' ) ) )
        require_once $file;
    }

} );

/** */

$load =  new \main\load;

require_once 'includes/functions.php';

$GLOBALS['admin_main_class'] = new admin\main;

if( $GLOBALS['me'] && $GLOBALS['me']->is_subadmin ) {

    // this it's not mandatory, but good to clear information in real time
    admin\actions::cleardata( array(
        'coupons' => array( 'status' => true, 'interval' => (int) \query\main::get_option( 'delete_old_coupons' ) ),
        'products' => array( 'status' => true, 'interval' => (int) \query\main::get_option( 'delete_old_products' ) ) )
    );

    if( isset( $_GET['ajax'] ) && file_exists( 'ajax/' . $_GET['ajax'] ) ) {
        require_once 'ajax/' . $_GET['ajax'];
        die;
    } else if( isset( $_GET['download'] ) && file_exists( 'etc/download/' . $_GET['download'] ) ) {
        require_once 'etc/download/' . $_GET['download'];
        die;
    }

    if( !isset( $_GET['action'] ) ) $_GET['action'] = '';

    ob_start();

    if( !empty( $_GET['plugin'] ) && file_exists( DIR . '/' . IDIR . '/user_plugins/' . $_GET['plugin'] ) ) {

        $plugin = admin\plugin::info();

        if( !empty( $plugin->ID ) && ( ( $GLOBALS['me']->is_subadmin && $plugin->subadmin_view ) || $GLOBALS['me']->is_admin ) ) {
            require_once DIR . '/' . IDIR . '/user_plugins/' . $_GET['plugin'];
        }

    } else if( isset( $_GET['route'] ) && file_exists( $_GET['route'] ) ) {

        require_once $_GET['route'];

    } else {

        require_once 'dashboard.php';

    }

    $content = ob_get_contents();
    ob_end_clean();

    require_once 'html/header.php';
    require_once 'html/nav.php';
    require_once 'html/logged.php';

    new admin\importer;

    echo $content;

} else if( isset( $_GET['action'] ) && $_GET['action'] == 'password_recovery' ) {

    require_once 'html/header.php';
    require_once 'password_recovery.php';

} else {

    require_once 'html/header.php';
    require_once 'signin.php';

}

require_once 'html/footer.php';

$load->page_load_after();

$db->close();