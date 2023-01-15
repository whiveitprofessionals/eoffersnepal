<?php

/** START SESSION */

session_start();

/** REPORT ALL PHP ERRORS */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


/** REQUIRE SETTINGS */

require_once 'settings.php';

/** CONNECT TO DB */

require_once IDIR . '/site/db.php';

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


if( !empty( $_GET ) ) {

    if( defined( 'SEO_LINKS' ) && SEO_LINKS && isset( $_GET['lcp'] ) ) {

        $available_pages    = array(
                                'ajax'                                          => 'ajax',
                                'cron'                                          => 'cron',
                                'whf'                                           => 'whf',
                                \query\main::get_option( 'seo_link_coupon' )    => 'id',
                                \query\main::get_option( 'seo_link_product' )   => 'product',
                                \query\main::get_option( 'seo_link_category' )  => 'cat',
                                \query\main::get_option( 'seo_link_search' )    => 's',
                                \query\main::get_option( 'seo_link_store' )     => 'store',
                                \query\main::get_option( 'seo_link_stores' )    => 'stores',
                                \query\main::get_option( 'seo_link_reviews' )   => 'reviews',
                                \query\main::get_option( 'seo_link_user' )      => 'user',
                                \query\main::get_option( 'seo_link_plugin' )    => 'plugin'
                            );

        $path               = substr_count( $_GET['lcp'], '/' ) ? dirname( $_GET['lcp'] ) : $_GET['lcp'];

        if( in_array( $path, array_keys( $available_pages ) ) ) {

            $k  = $available_pages[$path];

            if( $k == 's' ) {
                $v = ( isset( $_GET['s'] ) ? $_GET['s'] : '' );
            } else {
                $v  = ( !empty( $_GET['lcp_id'] ) ? (int) $_GET['lcp_id'] : basename( $_GET['lcp'] ) );
            }

        } else {

            if( substr_count( $_GET['lcp'], '/' ) == 0 && \query\main::page_exists( ( $user_page_id = ( !empty( $_GET['lcp_id'] ) ? (int) $_GET['lcp_id'] : $_GET['lcp'] ) ) ) ) {
                $k = 'p';
                $v = $user_page_id;
            } else {
                $k  = 'tpage';
                $v  = $_GET['lcp'];
            }

        }

    } else {
        $k = key( $_GET );
        $v = current( $_GET );
    }

    $GET = array();

    switch( $k ) {

        case 'p':
        $GET = array( 'loc' => 'page', 'id' => $v );
        break;

        case 'id':
        $GET = array( 'loc' => 'single', 'id' => $v );
        break;

        case 'product':
        $GET = array( 'loc' => 'product', 'id' => $v );
        break;

        case 'cat':
        $GET = array( 'loc' => 'category', 'id' => $v );
        break;

        case 's':
        $GET = array( 'loc' => 'search', 'id' => $v );
        break;

        case 'store':
        $GET = array( 'loc' => 'store', 'id' => $v );
        break;

        case 'stores':
        $GET = array( 'loc' => 'stores');
        break;

        case 'reviews':
        $GET = array( 'loc' => 'reviews', 'id' => $v );
        break;

        case 'user':
        $GET = array( 'loc' => 'user', 'id' => $v );
        break;

        case 'tpage':
        $GET = array( 'loc' => 'tpage', 'id' => str_replace( \query\main::get_option( 'extension' ), '', $v ) );
        break;

        case 'ajax':
        $GET = array( 'loc' => 'ajax', 'id' => $v );
        break;

        case 'cron':
        $GET = array( 'loc' => 'cron', 'id' => $v );
        break;

        case 'plugin':
        $GET = array( 'loc' => 'plugin', 'id' => $v );
        break;

        case 'whf':
        $GET = array( 'loc' => 'whf', 'id' => $v );
        break;

    }

}

$load = new \main\load;
$load->execute();
$load->page_load_after();

$db->close();