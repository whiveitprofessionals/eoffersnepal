<?php

/* SHOWING COUPONS, PRODUCTS OR STORES */

function searched_type() {
    if( isset( $_GET['type'] ) ) {
        if( strtolower( $_GET['type'] ) === 'products' ) {
            return 'products';
        } else if( strtolower( $_GET['type'] ) === 'stores' ) {
            return 'stores';
        } else if( strtolower( $_GET['type'] ) === 'coupon-locations' ) {
            return 'coupon-locations';
        } else if( strtolower( $_GET['type'] ) === 'product-locations' ) {
            return 'product-locations';
        } else if( strtolower( $_GET['type'] ) === 'locations' ) {
            return 'locations';
        }
    }
    return 'coupons';
}

/* PUT THE OBJECT INTO GLOBAL VARIABLES */

$GLOBALS['searched_type'] = searched_type();

/* NAVIGATION */

function navigation() {
    return $GLOBALS['have_items'];
}

/* CHECK IF HAVE ITEMS */

function have_items( $category = array(), $special = array() ) {

    if( $GLOBALS['searched_type'] === 'products' ) {
        $GLOBALS['have_items'] = \query\main::have_products( value_with_filter( 'search_view_products_args', $category ), 'search', $special, $_GET );
    } else if( $GLOBALS['searched_type'] === 'stores' ) {
        $GLOBALS['have_items'] = \query\main::have_stores( value_with_filter( 'search_view_stores_args', $category ), 'search', $special, $_GET );
    } else if( $GLOBALS['searched_type'] === 'coupon-locations' ) {
        $GLOBALS['have_items'] = \query\items_by_location::have_items( value_with_filter( 'search_view_items_by_location_args', $category ), 'search', $special, $_GET );
    } else if( $GLOBALS['searched_type'] === 'product-locations' ) {
        $GLOBALS['have_items'] = \query\items_by_location::have_products( value_with_filter( 'search_view_products_by_location_args', $category ), 'search', $special, $_GET );
    } else if( $GLOBALS['searched_type'] === 'locations' ) {
        $GLOBALS['have_items'] = \query\items_by_location::have_stores( value_with_filter( 'search_view_stores_by_location_args', $category ), 'search', $special, $_GET );
    } else {
        $GLOBALS['have_items'] = \query\main::have_items( value_with_filter( 'search_view_items_args', $category ), 'search', $special, $_GET );
    }

    /* ACTIVATE PAGES INFORMATION IF FUNCTION have_items() IS CALLED */

    /* NUMBER OF RESULTS */

    function results() {
        return $GLOBALS['have_items']['results'];
    }

    /* THIS PAGE IS */

    function page() {
        return $GLOBALS['have_items']['page'];
    }

    /* NUMBER OF PAGES */

    function pages() {
        return $GLOBALS['have_items']['pages'];
    }

    /* NEXT PAGE */

    function next_page() {
        if( !empty( $GLOBALS['have_items']['next_page'] ) ) {
            return $GLOBALS['have_items']['next_page'];
        }
        return false;
    }

    /* PREVIEW PAGE */

    function prev_page() {
        if( !empty( $GLOBALS['have_items']['prev_page'] ) ) {
            return $GLOBALS['have_items']['prev_page'];
        }
        return false;
    }

    return $GLOBALS['have_items']['results'];

}

/* RETURN THE NUMBER OF COUPONS/PRODUCTS/STORES */

/* SHOW COUPONS/PRODUCTS/STORES */

function items( $category = array(), $special = array() ) {
    if( $GLOBALS['searched_type'] === 'products' ) {
        return \query\main::while_products( value_with_filter( 'search_view_products_args', $category ), 'search', $special, $_GET );
    } else if( $GLOBALS['searched_type'] === 'stores' ) {
        return \query\main::while_stores( value_with_filter( 'search_view_stores_args', $category ), 'search', $special, $_GET );
    } else if( $GLOBALS['searched_type'] === 'coupon-locations' ) {
        return \query\items_by_location::while_items( value_with_filter( 'search_view_items_by_location_args', $category ), 'search', $special, $_GET );
    } else if( $GLOBALS['searched_type'] === 'product-locations' ) {
        return \query\items_by_location::while_products( value_with_filter( 'search_view_products_by_location_args', $category ), 'search', $special, $_GET );
    } else if( $GLOBALS['searched_type'] === 'locations' ) {
        return \query\items_by_location::while_stores( value_with_filter( 'search_view_stores_by_location_args', $category ), 'search', $special, $_GET );
    } else {
        return \query\main::while_items( value_with_filter( 'search_view_items_args', $category ), 'search', $special, $_GET );
    }
}

/* SEARCHED TEXT */

function searched( $v = 'text' ) {

    global $GET;

    $text = '';

    switch( $v ) {
        case 'text':
        if( gettype( $GET['id'] ) === 'string' ) {
            $text = substr( esc_html( $GET['id'] ), 0, 50 );
        }
        break;
    }

    return $text;

}

/* ACTION BEFORE DISPLAYING THE CONTENT */

do_action( 'before_search_page' );

/* THIS IS SEARCH PAGE */

function this_is_search_page() {

    return true;

}