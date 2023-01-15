<?php

namespace main;

/** */

class load extends template {

private $languages = [];
private $current_lang = [];

function __construct() {

    global $GET, $db, $LANG, $queryLastLog;

    require_once DIR . '/' . IDIR . '/main/init.php';
    require_once DIR . '/' . IDIR . '/functions/functions_basic.php';

    date_default_timezone_set( \query\main::get_option( 'timezone' ) );
    $db->query( "SET time_zone='" . date( 'P' ) . "'" );

    // website's url
    $GLOBALS['siteURL'] = \site\utils::site_url();

    // information about logged user
    $GLOBALS['me'] = \user\main::is_logged();

    // website's theme
    $this->template = \query\main::get_option( 'theme' );

    // current viewing page
    $this->page_type = isset( $GET['loc'] ) ? $GET['loc'] : 'index';

    // get id for current viewing page
    $this->id = ( isset( $GET['id'] ) ? $GET['id'] : '' );

    // include all load files from plugins
    $load_plugins = \query\main::user_plugins( '', 'loader,' );
    if( !empty( $load_plugins ) ) {
        foreach( $load_plugins as $plugin ) {
            if( file_exists( DIR . '/' . UPDIR . '/' . $plugin->load_file ) ) {
                require_once DIR . '/' . UPDIR . '/' . $plugin->load_file;
            }
        }
    }
 
    // include functions from the current theme
    if( file_exists( DIR . '/' . THEMES_LOC . '/' . $this->template . '/' . 'functions.php' ) ) {
        require_once DIR . '/' . THEMES_LOC . '/' . $this->template . '/' . 'functions.php';
        do_action( 'after_theme_functions_loaded' );
    }

    $this->languages = $this->languages();

    if( is_admin_panel() ) {
        $LANG = $this->set_back_end_language();
    } else {
        $LANG = $this->set_front_end_language();   
    }

}

private function set_back_end_language() {
    
    global $add_translation;
    $translations = value_with_filter( 'added_translations', $add_translation );

    $language = \query\main::get_option( 'adminpanel_lang' );

    if( !in_array( $language, array_keys( $this->languages ) ) ) {
        $language = 'english';
    }

    // include main translation file
    require_once $this->languages[$language]['location'];

    // include theme translation if exists
    if( function_exists( 'theme_languages_location' ) && file_exists( ( $theme_language = theme_location( true ) . '/' . rtrim( theme_languages_location(), '/' ) . '/' . $language . '.php' ) ) ) {   
        require_once $theme_language;
    }

    // include custom translations
    if( !empty( $translations ) && is_array( $translations ) ) {
        foreach( $translations as $translation ) {
            $user_lang = DIR . '/' . $translation . $language . '.php';
            if( file_exists( $user_lang ) ) {
                require_once $user_lang;
            }
        }
    }

    $LANG['$current'] = array_merge( ['id' => $language], $this->languages[$language] );
    $LANG['$languages'] = $this->languages;

    return $LANG;

}

private function set_front_end_language() {

    global $add_translation;
    $translations = value_with_filter( 'added_translations', $add_translation );

    $language = \query\main::get_option( 'sitelang' );

    if( (boolean) \query\main::get_option( 'allow_select_lang' ) && isset( $_COOKIE['language'] ) && in_array( strtolower( $_COOKIE['language'] ), array_keys( $this->languages ) ) ) {
        $language = strtolower( $_COOKIE['language'] );
    }

    if( !in_array( $language, array_keys( $this->languages ) ) ) {
        $language = 'english';
    }

    // include main translation file
    require_once $this->languages[$language]['location'];

    // include theme translation if exists
    if( function_exists( 'theme_languages_location' ) && file_exists( ( $theme_language = theme_location( true ) . '/' . rtrim( theme_languages_location(), '/' ) . '/' . $language . '.php' ) ) ) {   
        require_once $theme_language;
    }

    // include custom translations
    if( !empty( $translations ) && is_array( $translations ) ) {
        foreach( $translations as $translation ) {
            $user_lang = DIR . '/' . $translation . $language . '.php';
            if( file_exists( $user_lang ) ) {
                require_once $user_lang;
            }
        }
    }

    $LANG['$current'] = array_merge( ['id' => $language], $this->languages[$language] );
    $LANG['$languages'] = $this->languages;

    return $LANG;

}

public function page_load_after() {

    $load_pages = get( 'pages-load-after' );
    if( is_array( $load_pages ) ) {
        asort( $load_pages );
        foreach( $load_pages as $file => $order ) {
            if( file_exists( $file ) ) {
                require_once $file;
            }
        }
    }

}

private function plugin( $id = '' ) {

    require_once IDIR . '/functions/functions_global.php';

    $this->template_header( false );
    $this->template_plugin( $id );

}

private function ajax( $id = '' ) {

    require_once IDIR . '/functions/functions_global.php';

    $this->template_header( false );
    $this->template_ajax( $id );

}

private function cron( $id = '' ) {

    require_once IDIR . '/functions/functions_global.php';

    $this->template_header( false );
    $this->template_cron( $id );

}

private function page_tpage( $id = '' ) {

    require_once IDIR . '/functions/functions_template_page.php';
    require_once IDIR . '/functions/functions_global.php';

    ob_start();
    $this->template_tpage( $id );
    $tpage_page = ob_get_contents();
    ob_end_clean();

    $this->template_header();
    echo $tpage_page;
    $this->template_footer();

}

private function page_page() {

    require_once IDIR . '/functions/functions_page.php';
    require_once IDIR . '/functions/functions_global.php';

    ob_start();
    $this->template_page();
    $page_page = ob_get_contents();
    ob_end_clean();

    $this->template_header();
    echo $page_page;
    $this->template_footer();

}

private function page_single() {

    require_once IDIR . '/functions/functions_single.php';
    require_once IDIR . '/functions/functions_global.php';

    ob_start();
    $this->template_single();
    $single_page = ob_get_contents();
    ob_end_clean();

    $this->template_header();
    echo $single_page;
    $this->template_footer();

}

private function page_product() {

    require_once IDIR . '/functions/functions_product.php';
    require_once IDIR . '/functions/functions_global.php';

    ob_start();
    $this->template_product();
    $product_page = ob_get_contents();
    ob_end_clean();

    $this->template_header();
    echo $product_page;
    $this->template_footer();

}

private function page_category() {

    require_once IDIR . '/functions/functions_category.php';
    require_once IDIR . '/functions/functions_global.php';

    ob_start();
    $this->template_category();
    $category_page = ob_get_contents();
    ob_end_clean();

    $this->template_header();
    echo $category_page;
    $this->template_footer();

}

private function page_search() {

    require_once IDIR . '/functions/functions_search.php';
    require_once IDIR . '/functions/functions_global.php';

    ob_start();
    $this->template_search();
    $search_page = ob_get_contents();
    ob_end_clean();

    $this->template_header();
    echo $search_page;
    $this->template_footer();

}

private function page_store() {

    require_once IDIR . '/functions/functions_store.php';
    require_once IDIR . '/functions/functions_global.php';

    ob_start();
    $this->template_store();
    $store_page = ob_get_contents();
    ob_end_clean();

    $this->template_header();
    echo $store_page;
    $this->template_footer();

}

private function page_stores() {

    require_once IDIR . '/functions/functions_stores.php';
    require_once IDIR . '/functions/functions_global.php';

    ob_start();
    $this->template_stores();
    $stores_page = ob_get_contents();
    ob_end_clean();

    $this->template_header();
    echo $stores_page;
    $this->template_footer();

}

private function page_reviews() {

    require_once IDIR . '/functions/functions_reviews.php';
    require_once IDIR . '/functions/functions_global.php';

    ob_start();
    $this->template_reviews();
    $reviews_page = ob_get_contents();
    ob_end_clean();

    $this->template_header();
    echo $reviews_page;
    $this->template_footer();

}

private function page_user( $id = '' ) {

    require_once IDIR . '/functions/functions_user.php';
    require_once IDIR . '/functions/functions_global.php';

    ob_start();
    $this->template_user( $id );
    $user_page = ob_get_contents();
    ob_end_clean();

    $this->template_header();
    echo $user_page;
    $this->template_footer();

}

private function page_whf( $id = '' ) {

    require_once IDIR . '/functions/functions_global.php';

    $this->template_whf( $id );

}

private function page_maintenance() {

    require_once IDIR . '/functions/functions_global.php';

    $this->template_maintenance();

}

private function page_index() {

    require_once IDIR . '/functions/functions_global.php';

    ob_start();
    $this->template_index();
    $index_page = ob_get_contents();
    ob_end_clean();

    $this->template_header();
    echo $index_page;
    $this->template_footer();

}

public function execute() {

    if( ( $redirect_to = \user\main::banned() ) ) {
        if( !filter_var( $redirect_to, FILTER_VALIDATE_URL ) ) {
            header( 'HTTP/1.0 403 Forbidden' );
        } else {
            header( 'Location: ' . $redirect_to );
        }
        die;
    }

    if( isset( $_GET['ref'] ) ) {
        setcookie ( 'referrer', (int) $_GET['ref'], strtotime( '+' . \query\main::get_option( 'refer_cookie' ) . ' days' ), '/' );
    }

    if( isset( $_GET['set_language'] ) ) {
        setcookie( 'language', $_GET['set_language'], strtotime( '+1 month' ), '/' );
        header( 'Location: ' . get_remove( array( 'set_language' ) ) );
        die;
    }

    if( (boolean) \query\main::get_option( 'maintenance' ) && ( !$GLOBALS['me'] || !$GLOBALS['me']->is_subadmin ) ) {
        $this->page_maintenance();
        die;
    }

    switch( $this->page_type ) {

        case 'page': $this->page_page(); break;
        case 'single': $this->page_single(); break;
        case 'product': $this->page_product(); break;
        case 'category': $this->page_category(); break;
        case 'search': $this->page_search(); break;
        case 'store': $this->page_store(); break;
        case 'stores': $this->page_stores(); break;
        case 'reviews': $this->page_reviews(); break;
        case 'user': $this->page_user( $this->id ); break;
        case 'tpage': $this->page_tpage( $this->id ); break;
        case 'ajax': $this->ajax( $this->id );break;
        case 'cron': $this->cron( $this->id );break;
        case 'plugin': $this->plugin( $this->id );break;
        case 'whf': $this->page_whf( $this->id );break;
        default: $this->page_index(); break;

    }

}

}