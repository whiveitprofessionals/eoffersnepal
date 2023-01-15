<?php

/* SHOWING COUPONS OR PRODUCTS */

function searched_type() {
    if( isset( $_GET['type'] ) && strtolower( $_GET['type'] ) === 'products' ) {
      return 'products';
    }
    return 'coupons';
}

/* PUT THE OBJECT INTO GLOBAL VARIABLES */

$GLOBALS['searched_type'] = searched_type();
$GLOBALS['item'] = \query\main::store_info( 0, array( 'update_views' => '' ) );
$GLOBALS['exists'] = \query\main::store_exists( 0, array( 'user_view' => '' ) );

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

/* CHECK IF STORE HAVE COUPONS */

function have_items( $category = array(), $special = array() ) {
    global $GET;
    return \query\main::have_items( array_merge( $category, array( 'store' => $GLOBALS['item']->ID ) ), '', $special );
}

/* SHOW STORE COUPONS */

function items( $category = array(), $special = array() ) {
    global $GET;
    return \query\main::while_items( array_merge( $category, array( 'store' => $GLOBALS['item']->ID ) ), '', $special );
}

/* CHECK IF STORE HAVE PRODUCTS */

function have_products( $category = array(), $special = array() ) {
    global $GET;
    return \query\main::have_products( array_merge( $category, array( 'store' => $GLOBALS['item']->ID ) ), '', $special );
}

/* SHOW STORE PRODUCTS */

function products( $category = array(), $special = array() ) {
    global $GET;
    return \query\main::while_products( array_merge( $category, array( 'store' => $GLOBALS['item']->ID ) ), '', $special );
}

/* CHECK IF STORE HAVE REVIEWS */

function have_reviews( $category = array(), $special = array() ) {
    return \query\main::have_reviews( $category, 'store', $special );
}

/* PERSONALIZED META TAGS ONLY IF THE COUPON EXISTS */

if( $GLOBALS['exists'] > 0 ) {

    /* METATAGS - TITLE */

    function meta_title() {

    if( !empty( $GLOBALS['item']->meta_title ) ) {
        return meta_default( '', $GLOBALS['item']->meta_title );
    } else

    $desc = \query\main::get_option( 'meta_store_title' );
    $repl = array( '%NAME%' => $GLOBALS['item']->name, '%COUPONS%' => $GLOBALS['item']->coupons, '%REVIEWS%' => $GLOBALS['item']->reviews, '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

    return str_replace( array_keys( $repl ), array_values( $repl ), esc_html( $desc ) );

    }

    /* METATAGS - DESCRIPTION */

    function meta_keywords() {

    if( !empty( $GLOBALS['item']->meta_keywords ) ) {
        return meta_default( '', $GLOBALS['item']->meta_keywords );
    } else

    $desc = \query\main::get_option( 'meta_store_keywords' );
    $repl = array( '%NAME%' => $GLOBALS['item']->name, '%COUPONS%' => $GLOBALS['item']->coupons, '%REVIEWS%' => $GLOBALS['item']->reviews, '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

    return str_replace( array_keys( $repl ), array_values( $repl ), esc_html( $desc ) );

    }

    /* METATAGS - DESCRIPTION */

    function meta_description() {

    if( !empty( $GLOBALS['item']->meta_description ) ) {
        return meta_default( '', $GLOBALS['item']->meta_description );
    } else

    $desc = \query\main::get_option( 'meta_store_desc' );
    $repl = array( '%NAME%' => $GLOBALS['item']->name, '%COUPONS%' => $GLOBALS['item']->coupons, '%REVIEWS%' => $GLOBALS['item']->reviews, '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

    return str_replace( array_keys( $repl ), array_values( $repl ), esc_html( $desc ) );

    }

    /* METATAGS - IMAGE */

    function meta_image( $image = '' ) {

        return \query\main::store_avatar( $GLOBALS['item']->image );

    }

    /* IS CURRENT PAGE */

    function this_is_store( $identifier = 0 ) {

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

    do_action( 'before_store_page', $GLOBALS['item'] );

} else {

    /* THIS IS 404 PAGE */

    function this_is_404_page() {

        return true;

    }

}

/* ADD TO HISTORY */

$_SESSION['history'][current( $_GET )] = time();
arsort( $_SESSION['history'] );

if( count( $_SESSION['history'] ) > 30 ) {
    foreach( array_slice( array_keys( $_SESSION['history'] ), 30 ) as $id ) {
        unset( $_SESSION['history'][$id] );
    }
}