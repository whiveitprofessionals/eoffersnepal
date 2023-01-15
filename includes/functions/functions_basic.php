<?php

/* PUT THE OBJECT INTO GLOBAL VARIABLES */

$GLOBALS['get_theme_options'] = option( 'theme_options_' . strtolower( theme() ), true );

/* SETTINGS: META CHARSET */

function meta_charset() {
    return \query\main::get_option( 'meta_charset' );
}

/* SETTINGS: SITE NAME */

function site_name() {
    return \query\main::get_option( 'sitename' );
}

/* SETTINGS: SITE DESCRIPTION */

function description() {
    return \query\main::get_option( 'sitedescription' );
}

/* GET OPTION */

function option( $option = '', $unserialize = false ) {
    return \query\main::get_option( $option, $unserialize );
}

/* GET THEME OPTIONS */

function get_theme_options() {
    return array_map( 'esc_html', $GLOBALS['get_theme_options'] );
}

/* GET THEME OPTION */

function get_theme_option( $opt = '' ) {
    return is_array( $GLOBALS['get_theme_options'] ) && in_array( $opt, array_keys( $GLOBALS['get_theme_options'] ) ) ? $GLOBALS['get_theme_options'][$opt] : false;
}

/* USER INFO ( can be used as me() or $GLOBALS['me'] ) */

function me() {
    if( !$GLOBALS['me'] ) {
        return false;
    }

    return $GLOBALS['me'];
}

/* MY POINTS */

function my_points() {
    if( !$GLOBALS['me'] ) {
        return false;
    }
    return $GLOBALS['me']->Points;
}

/* MY CREDITS */

function my_credits() {
    if( !$GLOBALS['me'] ) {
        return false;
    }
    return $GLOBALS['me']->Credits;
}

/* IS OR NOT A STORE OWNER */

function is_store_owner() {
    if( !$GLOBALS['me'] ) {
        return false;
    }
    return (boolean) $GLOBALS['me']->Stores;
}

/* SETTINGS: SITE THEME */

function theme() {
    return \query\main::get_option( 'theme' );
}

/* SITE URL */

function site_url( $path = array() ) {
    return $GLOBALS['siteURL'] . ( !empty( $path ) ? ( is_array( $path ) ? implode( '/', $path ) : $path ) : '' ) ;
}

/* THEME LOCATION */

function theme_location( $local = false ) {
    return ( $local ? DIR . '/' : $GLOBALS['siteURL'] ) . rtrim( THEMES_LOC, '/' ) . '/' . theme();
}

/*  THEME LOCATION 2 */

function theme_location2() {
    return rtrim( THEMES_LOC, '/' ) . '/' . theme();
}

/* SITE LANGUAGES */

function site_languages() {
    global $LANG;
    return value_with_filter( 'front-end-languages', $LANG['$languages'] );
}

/* REMOVE PARAMETERS FROM GET QUERY STRING */

function get_update( $array = array(), $url = '' ) {
    return \site\utils::update_uri( $url, $array );
}

/* REMOVE PARAMETER FROM GET QUERY STRING */

function get_remove( $array = array(), $url = '' ) {
    return \site\utils::update_uri( $url, $array, 'remove' );
}

/* VALUE WITH FILTER */

function value_with_filter( $filter = '', $default = '', $extra = '' ) {
    if( !is_array( $filter ) ) {
        $filters = get( 'filters', $filter );

        if( !empty( $filters ) ) {
            foreach( $filters as $current_filter ) {
                if( ( $callback = \site\utils::check_callback( $current_filter ) ) ) {
                    $default = call_user_func( $callback, $default, $extra );
                }
            }
        }
    } else {
        foreach( $filter as $filter2 ) {
            $default = value_with_filter( $filter2, $default, $extra );
        }
    }

    return $default;
}

/* DO ACTION */

function do_action( $action = '', $extra = '' ) {
    if( !is_array( $action ) ) {
        $actions = get( 'actions', $action );

        if( !empty( $actions ) && is_array( $actions )  ) {

            asort( $actions );

            $hooks = '';

            foreach( $actions as $action ) {
                if( ( $callback = \site\utils::check_callback( $action ) ) ) {
                    $hooks .= call_user_func( $callback, $extra );
                }
            }

            return $hooks;

        }
    } else {
        $hooks = '';
        foreach( $action as $act ) {
            $hooks .= do_action( $act, $extra );
        }
        return $hooks;
    }

    return false;
}

/* IT'S NUMBER ONE? */

function is_First( $num ) {
    if( (int) $num === 1 ) return true;
        return false;
}

/* TIME AGO */

function timeago( $time, $type = '' ) {
    if( $type == 'seconds' ) {
        $time =    time() - $time;
    } else {
        $time = $time - time();
    }

    if( $time > 31536000 ) {
        $y = floor( $time / 31536000 );
        return $y . ' ' . ( is_First( $y ) ? strtolower( t( 'year', 'Year' ) ) : strtolower( t( 'years', 'Years' ) ) );
    } else if( $time > 2592000 ) {
        $m = floor( $time / 2592000 );
        return $m    . ' ' . ( is_First( $m ) ? strtolower( t( 'month', 'Month' ) ) : strtolower( t( 'months', 'Months' ) ) );
    } else if( $time > 86400 ) {
        $d = floor( $time / 86400 );
        return $d    . ' ' . ( is_First( $d ) ? strtolower( t( 'day', 'Day' ) ) : strtolower( t( 'days', 'Days' ) ) );
    } else if( $time > 3600 ) {
        $h = floor( $time / 3600 );
        return $h . ' ' . ( is_First( $h ) ? strtolower( t( 'hour', 'Hour' ) ) : strtolower( t( 'hours', 'Hours' ) ) );
    } else if( $time > 60 ) {
        $m = floor( $time / 60 );
        return $m . ' ' . ( is_First( $m ) ? strtolower( t( 'minute', 'Minute' ) ) : strtolower( t( 'minutes', 'Minutes' ) ) );
    } else {
        return $time . ' ' . ( is_First( $time ) ? strtolower( t( 'second', 'Second' ) ) : strtolower( t( 'seconds', 'Seconds' ) ) );
    }
}

/* MAINTENANCE MODE */

function is_maintenance_mode() {
    return (boolean) \query\main::get_option( 'maintenance' );
}

/* READ A PART OF TEMPLATE */

function read_template_part( $part ) {
    $theme_location = rtrim( THEMES_LOC, '/' ) . '/' . \query\main::get_option( 'theme' );

    switch( $part ) {
        case '404': include( $theme_location . '/404.php' ); break;
    }
}

/* GET THEME LINK */

function tlink( $place, $q = array(), $backTo = '' ) {

    $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;

    $page = substr( strstr( $place, '/' ), 1 );
    $path =    strstr( $place, '/', true);
    if( empty( $path ) ) {
        $path = $place;
    }

    if( !empty( $backTo ) ) {
        if( $backTo == 'this' ) {
            $backTo = $_SERVER['REQUEST_URI'];
        }
        $q = !empty( $q ) ? $q . '&amp;backto=' . $backTo : 'backto=' . $backTo;
    }

    $extension = \query\main::get_option( 'extension' );

    switch( $path ) {

        case 'index':
            return $GLOBALS['siteURL'];
        break;

        case 'page':
            return ( $seo_link ? $GLOBALS['siteURL'] . ( !empty( $q['seo'] ) ? $q['seo'] : '' ) : $GLOBALS['siteURL'] . ( !empty( $q['notseo'] ) ? '?' . $q['notseo'] : '' ) );
        break;

        case 'stores':
            return ( $seo_link ? \site\utils::make_seo_link( '', \query\main::get_option( 'seo_link_stores' ), '', '', $extension ) . ( !empty( $q ) ? '?' . (string) $q : '' ) : $GLOBALS['siteURL'] . '?stores' . ( !empty( $q ) ? '&amp;' . (string) $q : '' ) );
        break;

        case 'search':
            return ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_search' ) ) . ( !empty( $q ) ? '?' . (string) $q : '' ) : $GLOBALS['siteURL'] . ( !empty( $q ) ? '?' . (string) $q : '' ) );
        break;

        case 'user':
            return ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_user' ) ) . $page . $extension . ( !empty( $q ) ? '?' . (string) $q : '' ) : $GLOBALS['siteURL'] . '?user=' . $page . ( !empty( $q ) ? '&amp;' . (string) $q : '' ) );
        break;

        case 'ajax':
            return ( $seo_link ? \site\utils::make_seo_link( 'ajax' ) . $page . $extension . ( !empty( $q ) ? '?' . (string) $q : '' ) : $GLOBALS['siteURL'] . '?ajax=' . $page . ( !empty( $q ) ? '&amp;' . (string) $q : '' ) );
        break;

        case 'plugin':
            return ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_plugin' ) ) . $page . $extension . ( !empty( $q ) ? '?' . (string) $q : '' ) : $GLOBALS['siteURL'] . '?plugin=' . $page . ( !empty( $q ) ? '&amp;' . (string) $q : '' ) );
        break;

        case 'whf':
            return ( $seo_link ? \site\utils::make_seo_link( 'whf' ) . $page . $extension . ( !empty( $q ) ? '?' . (string) $q : '' ) : $GLOBALS['siteURL'] . '?whf=' . $page . ( !empty( $q ) ? '&amp;' . (string) $q : '' ) );
        break;

        case 'tpage':
            return ( $seo_link ? $GLOBALS['siteURL'] . $page . $extension . ( !empty( $q ) ? '?' . (string) $q : '' ) : $GLOBALS['siteURL'] . '?tpage=' . $page . ( !empty( $q ) ? '&amp;' . (string) $q : '' ) );
        break;

        case 'pay':
            return $GLOBALS['siteURL'] . 'payment.php' . ( !empty( $q ) ? '?' . (string) $q : '' );
        break;

    }

    return $GLOBALS['siteURL'];

}

/* GET LINK PAGE */

function get_link( $type = '', $id = '', $return_item = false ) {

    switch( $type ) {

        case 'page':
            if( \query\main::page_exists( $id ) ) {
                $page = \query\main::page_info( $id );
                if( $return_item ) {
                    return array( $page->link, 'info' => $page );
                } else return $page->link;
            }
            return false;
        break;

        case 'store':
            if( \query\main::store_exists( $id ) ) {
                $item = \query\main::store_info( $id );
                if( $return_item ) {
                    return array( $item->link, 'info' => $item );
                } else return $item->link;
            }
            return false;
        break;

        case 'item':
            if( \query\main::item_exists( $id ) ) {
                $item = \query\main::item_info( $id );
                if( $return_item ) {
                    return array( $item->link, 'info' => $item );
                } else return $item->link;
            }
            return false;
        break;

        case 'product':
            if( \query\main::product_exists( $id ) ) {
                $item = \query\main::product_info( $id );
                if( $return_item ) {
                    return array( $item->link, 'info' => $item );
                } else return $item->link;
            }
            return false;
        break;

    }

    return false;

}

/* GET PAGE LINK */

function get_page_link( $id, $return_item = false ) {
    return get_link( 'page', $id, $return_item );
}

/* GET STORE LINK */

function get_store_link( $id, $return_item = false ) {
    return get_link( 'store', $id, $return_item );
}

/* GET ITEM/COUPON LINK */

function get_item_link( $id, $return_item = false ) {
    return get_link( 'item', $id, $return_item );
}

/* GET PRODUCT LINK */

function get_product_link( $id, $return_item = false ) {
    return get_link( 'product', $id, $return_item );
}

/* TARGET LINKS */

function get_target_link( $type = '', $id = '', $query = '' ) {
    return tlink( 'plugin/click', $type . '=' . $id . ( !empty( $query ) ? '&amp;' . http_build_query( $query ) : '' ) );
}

/* DO CONTENT */

function do_content( $text = '', $escape = false, $use_emoticons = true, $use_shortcodes = true, $allow_filters = true, $filter = 'content' ) {
    return \site\content::content( $filter, $text, $use_emoticons, $use_shortcodes, false, $escape, $allow_filters );
}

/* SHOW DAYS OF A WEEK */

function days_of_week() {
    return \site\utils::days_of_week();
}

/* SHOW PRICE AS SPECIFIED FORMAT */

function price( $price ) {
    return sprintf( PRICE_FORMAT, $price );
}

/* SHOW PRICE IN DESIRED FORMAT */

function price_format( $price, $currency = '' ) {
    return ( !empty( $currency ) ? 
    value_with_filter( 'price_format_currency_position', sprintf( '%s %s', \site\utils::money_format( $price ), $currency ), 
        array( 
            'price' => \site\utils::money_format( $price ), 
            'currency' => $currency 
        ) 
    ) : \site\utils::money_format( $price ) );
}

/* ADD STORE PRICE */

function store_price( $format = false ) {
    $price = \query\main::get_option( 'price_store' );
    return ( $format ? price_format( $price ) : (double) $price );
}

/* ADD/EDIT COUPON PRICE */

function coupon_price( $format = false ) {
    $price = \query\main::get_option( 'price_coupon' );
    return ( $format ? price_format( $price ) : (double) $price );
}

/* ADD/EDIT PRODUCT PRICE */

function product_price( $format = false ) {
    $price = \query\main::get_option( 'price_product' );
    return ( $format ? price_format( $price ) : (double) $price );
}

/* MAXIMUM NUMBER OF DAYS WHICH CAN BE PURCHASED FOR A SINGLE PAYMENT */

function coupon_price_days() {
    return (int) \query\main::get_option( 'price_max_days' );
}

function product_price_days() {
    return (int) \query\main::get_option( 'price_product_max_days' );
}