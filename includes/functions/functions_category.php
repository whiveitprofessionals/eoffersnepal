<?php

/* SHOWING COUPONS OR PRODUCTS */

function searched_type() {
    if( isset( $_GET['type'] ) ) {
        if( strtolower( $_GET['type'] ) === 'products' ) {
            return 'products';
        } else if( strtolower( $_GET['type'] ) === 'stores' ) {
            return 'stores';
        }
    }
    return 'coupons';
}

/* PUT THE OBJECT INTO GLOBAL VARIABLES */

$GLOBALS['searched_type'] = searched_type();
$GLOBALS['item'] = \query\main::category_info();
$GLOBALS['exists'] = \query\main::category_exists();

/* CHECK IF CATEGORY EXISTS */

function exists() {
    return $GLOBALS['exists'];
}

/* INFORMATION ABOUT CATEGORY */

function the_item() {
    return $GLOBALS['item'];
}

/* GET EXTRA FIELD DATA */

function extra( $id = '' ) {
    if( empty( $id ) ) return false;
    if( isset( $GLOBALS['item']->extra[$id] ) ) {
        return $GLOBALS['item']->extra[$id];
    }
    return false;
}

/* NAVIGATION */

function navigation() {
    return $GLOBALS['have_items'];
}

/* CHECK IF HAVE COUPONS/PRODUCTS */

function have_items( $category = array(), $special = array() ) {

    if( $GLOBALS['searched_type'] === 'products' ) {
        $GLOBALS['have_items'] = \query\main::have_products( value_with_filter( 'category_view_products_args', $category ), 'category', $special );
    } else if( $GLOBALS['searched_type'] === 'stores' ) {
        $GLOBALS['have_items'] = \query\main::have_stores( value_with_filter( 'category_view_stores_args', $category ), 'category', $special );
    } else {
        $GLOBALS['have_items'] = \query\main::have_items( value_with_filter( 'category_view_items_args', $category ), 'category', $special );
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

/* SHOW COUPONS/PRODUCTS/STORES */

function items( $category = array(), $special = array() ) {

    if( $GLOBALS['searched_type'] === 'products' ) {
        return \query\main::while_products( value_with_filter( 'category_view_products_args', $category ), 'category', $special );
    } else if( $GLOBALS['searched_type'] === 'stores' ) {
        return \query\main::while_stores( value_with_filter( 'category_view_stores_args', $category ), 'category', $special );
    } else {
        return \query\main::while_items( value_with_filter( 'category_view_items_args', $category ), 'category', $special );
    }

}

/* PERSONALIZED META TAGS ONLY IF THE CATEGORY EXISTS */

if( $GLOBALS['exists'] > 0 ) {

    /* METATAGS - TITLE */

    function meta_title() {

        if( !empty( $GLOBALS['item']->meta_title ) ) {
            return meta_default( '', $GLOBALS['item']->meta_title );
        } else

        $desc = \query\main::get_option( 'meta_category_title' );
        $repl = array( '%NAME%' => $GLOBALS['item']->name, '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

        return str_replace( array_keys( $repl ), array_values( $repl ), esc_html( $desc ) );

    }

    /* METATAGS - KEYWORDS */

    function meta_keywords() {

        if( !empty( $GLOBALS['item']->meta_keywords ) ) {
            return meta_default( '', $GLOBALS['item']->meta_keywords );
        } else

        $desc = \query\main::get_option( 'meta_category_keywords' );
        $repl = array( '%NAME%' => $GLOBALS['item']->name, '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

        return str_replace( array_keys( $repl ), array_values( $repl ), esc_html( $desc ) );

    }

    /* METATAGS - DESCRIPTION */

    function meta_description() {

        if( !empty( $GLOBALS['item']->meta_description ) ) {
            return meta_default( '', $GLOBALS['item']->meta_description );
        } else

        $desc = \query\main::get_option( 'meta_category_desc' );
        $repl = array( '%NAME%' => $GLOBALS['item']->name, '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

        return str_replace( array_keys( $repl ), array_values( $repl ), esc_html( $desc ) );

    }

    /* IS CURRENT PAGE */

    function this_is_category( $identifier = 0 ) {

        if( !empty( $identifier ) ) {

            global $GET;

            if( isset( $GET['id'] ) && strcasecmp( $GET['id'], $identifier ) == 0 ) {   
                return true;
            }
            return false;

        }

        return true;

    }

    /* ACTION BEFORE DISPLAYING THE CONTENT */

    do_action( 'before_category_page', $GLOBALS['item'] );

} else {

    /* THIS IS 404 PAGE */

    function this_is_404_page() {

        return true;

    }

}