<?php

namespace main;

/** */

class template extends \site\language {

protected $template = 'default';

protected function template_header( $show_header = true ) {

    if( $show_header && file_exists( theme_location( true ) . '/' . 'site_header.php' ) ) {
        require_once theme_location( true ) . '/' . 'site_header.php';
    }

    return true;

}

protected function template_footer() {

    if( file_exists( theme_location( true ) . '/' . 'site_footer.php' ) ) {
        require_once theme_location( true ) . '/' . 'site_footer.php';
    }

    return true;

}

protected function template_tpage( $id ) {

    global $add_theme_page;

    if( !this_is_template_page() ) {
        header( 'HTTP/1.0 404 Not Found' ); 
        require_once theme_location( true ) . '/' . '404.php';
        return false;
    }

    $template_page = theme_location( true ) . '/' . $id . '.php';

    if( file_exists( $template_page ) ) {
        require_once $template_page;
    } else {

        $path = strtolower( \site\utils::file_path( $id ) );

        // check if callback is valid
        if( \site\utils::check_callback( $add_theme_page[$path] ) ) {

            ob_start();
            call_user_func_array( $add_theme_page[$path], array( basename( $id ), ( preg_match( '/\-([0-9]+)\.?([a-z0-9]+)?$/', $id, $r ) ? $r[1] : '' ), $path ) );
            $cb = ob_get_contents();
            ob_end_clean();

            // if callback returns content, echo content
            if( $cb ) {
                echo $cb;
            } else {
                header( 'HTTP/1.0 404 Not Found' );
                require_once theme_location( true ) . '/' . '404.php';
                return false;
            }

        // if callback is not valid, display 404 template
        } else {
            header( 'HTTP/1.0 404 Not Found' );
            require_once theme_location( true ) . '/' . '404.php';
            return false;
        }

    }

    return true;

}

protected function template_plugin( $id ) {

    global $db;

    if( file_exists( PDIR . '/' . $id . '.php' ) )
    require_once PDIR . '/' . $id . '.php';

    return true;

}

protected function template_whf( $id ) {

    global $db;

    if( file_exists( theme_location( true ) . '/' . $id . '.php' ) )
    ob_start();
    require_once theme_location( true ) . '/' . $id . '.php';
    if( !defined( 'IS_PWHF' ) || !IS_PWHF ) {
        ob_end_clean();
        return false;
    }
    return true;

}

protected function template_ajax( $id ) {

    global $db;
    
    if( file_exists( AJAX_LOCATION . '/' . $id . '.php' ) )
    require_once AJAX_LOCATION . '/' . $id . '.php';

    return true;

}

protected function template_cron( $id ) {

    global $db;

    if( file_exists( CRONDIR . '/' . 'tasks' . '/' . ( $name = strtok( $id, '.' ) ) . '.php' ) )
    require_once CRONDIR . '/' . 'tasks' . '/' . $name . '.php';

}

protected function template_page() {

    if( !this_is_page() ) {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
        return false;
    }

    if( file_exists( theme_location( true ) . '/' . 'page.php' ) )
    require_once theme_location( true ) . '/' . 'page.php';
    else {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
    }

    return true;

}

protected function template_single() {

    if( !this_is_coupon() ) {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
        return false;
    }

    if( file_exists( theme_location( true ) . '/' . 'single.php' ) )
    require_once theme_location( true ) . '/' . 'single.php';
    else {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
    }

    return true;

}

protected function template_product() {

    if( !this_is_product() ) {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
        return false;
    }

    if( file_exists( theme_location( true ) . '/' . 'product.php' ) )
    require_once theme_location( true ) . '/' . 'product.php';
    else {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
    }

    return true;

}

protected function template_category() {

    if( !this_is_category() ) {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
        return false;
    }

    if( file_exists( theme_location( true ) . '/' . 'category.php' ) )
    require_once theme_location( true ) . '/' . 'category.php';
    else {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
    }

    return true;

}

protected function template_search() {
    if( file_exists( theme_location( true ) . '/' . 'search.php' ) )
    require_once theme_location( true ) . '/' . 'search.php';
    else {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
    }

    return true;

}

protected function template_user( $id ) {

    if( !this_is_user_section() ) {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
        return false;
    }

    if( file_exists( theme_location( true ) . '/user/' . $id . '.php' ) )
    require_once theme_location( true ) . '/user/' . $id . '.php';
    else {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
    }

    return true;

}

protected function template_store() {

    if( !this_is_store() ) {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
        return false;
    }

    if( file_exists( theme_location( true ) . '/' . 'store.php' ) )
    require_once theme_location( true ) . '/' . 'store.php';
    else {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
    }

    return true;

}

protected function template_stores() {

    if( file_exists( theme_location( true ) . '/' . 'stores.php' ) )
    require_once theme_location( true ) . '/' . 'stores.php';
    else {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
    }

    return true;

}

protected function template_reviews() {

    if( !this_is_reviews_page() ) {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
        return false;
    }

    if( file_exists( theme_location( true ) . '/' . 'reviews.php' ) )
    require_once theme_location( true ) . '/' . 'reviews.php';
    else {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
    }

    return true;

}

protected function template_maintenance() {

    if( file_exists( theme_location( true ) . '/' . 'maintenance.php' ) )
    require_once theme_location( true ) . '/' . 'maintenance.php';
    else {

        $maintenance_page = value_with_filter( 'maintenance-page', PDIR . '/maintenance.php' );
        if( file_exists( $maintenance_page ) ) {
            require_once $maintenance_page;
        }

    }

    return true;

}

protected function template_index() {
    $index = value_with_filter( 'index-page', theme_location( true ) . '/index.php' );

    if( file_exists( $index ) )
    require_once $index;
    else {
        header( 'HTTP/1.0 404 Not Found' );
        require_once theme_location( true ) . '/' . '404.php';
    }

    return true;

}

}