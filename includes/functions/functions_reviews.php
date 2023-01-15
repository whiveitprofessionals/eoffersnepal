<?php

/* PUT THE OBJECT INTO GLOBAL VARIABLES */

$GLOBALS['item'] = \query\main::store_info();
$GLOBALS['exists'] = \query\main::store_exists();

global $GET;

$GET['id'] = $GLOBALS['exists'];

/* CHECK IF STORE EXISTS */

function exists() {
    return $GLOBALS['exists'];
}

/* INFORMATION ABOUT STORE */

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

/* CHECK IF HAVE ITEMS */

function have_items( $category = array(), $special = array() ) {

    $GLOBALS['have_items'] = \query\main::have_reviews( $category, 'store', $special );

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

/* SHOW REVIEWS */

function items( $category = array(), $special = array() ) {
    return \query\main::while_reviews( $category, 'store', $special );
}

/* PERSONALIZED META TAGS ONLY IF THE STORE EXISTS */

if( $GLOBALS['exists'] > 0 ) {

    /* METATAGS - TITLE */

    function meta_title() {

        if( !empty( $GLOBALS['item']->meta_title ) ) {
          return meta_default( '', $GLOBALS['item']->meta_title );
        } else

        $desc = \query\main::get_option( 'meta_reviews_title' );
        $repl = array( '%NAME%' => $GLOBALS['item']->name, '%COUPONS%' => $GLOBALS['item']->coupons, '%REVIEWS%' => $GLOBALS['item']->reviews, '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

        return str_replace( array_keys( $repl ), array_values( $repl ), esc_html( $desc ) );

    }

    /* METATAGS - KEYWORDS */

    function meta_keywords() {

        if( !empty( $GLOBALS['item']->meta_keywords ) ) {
          return meta_default( '', $GLOBALS['item']->meta_keywords );
        } else

        $desc = \query\main::get_option( 'meta_reviews_keywords' );
        $repl = array( '%NAME%' => $GLOBALS['item']->name, '%COUPONS%' => $GLOBALS['item']->coupons, '%REVIEWS%' => $GLOBALS['item']->reviews, '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

        return str_replace( array_keys( $repl ), array_values( $repl ), esc_html( $desc ) );

    }

    /* METATAGS - DESCRIPTION */

    function meta_description() {

        if( !empty( $GLOBALS['item']->meta_description ) ) {
          return meta_default( '', $GLOBALS['item']->meta_description );
        } else

        $desc = \query\main::get_option( 'meta_reviews_desc' );
        $repl = array( '%NAME%' => $GLOBALS['item']->name, '%COUPONS%' => $GLOBALS['item']->coupons, '%REVIEWS%' => $GLOBALS['item']->reviews, '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

        return str_replace( array_keys( $repl ), array_values( $repl ), esc_html( $desc ) );

    }

    /* METATAGS - IMAGE */

    function meta_image( $image = '' ) {

      return \query\main::store_avatar( $GLOBALS['item']->image );

    }

    /* THIS IS REVIEWS PAGE */

    function this_is_reviews_page( $identifier = 0 ) {

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

    do_action( 'before_reviews_page', $GLOBALS['item'] );

} else {

    /* THIS IS 404 PAGE */

    function this_is_404_page() {

        return true;

    }

}