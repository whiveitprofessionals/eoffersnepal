<?php

/* PUT THE OBJECT INTO GLOBAL VARIABLES */

$GLOBALS['item'] = \query\main::item_info( 0, array( 'update_views' => '' ) );
$GLOBALS['exists'] = \query\main::item_exists( 0, array( 'user_view' => '' ) );

/* CHECK IF COUPON EXISTS */

function exists() {
    return $GLOBALS['exists'];
}

/* INFORMATION ABOUT COUPON */

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

/* PERSONALIZED META TAGS ONLY IF THE COUPON EXISTS */

if( $GLOBALS['exists'] > 0 ) {

    /* METATAGS - TITLE */

    function meta_title() {

    if( !empty( $GLOBALS['item']->meta_title ) ) {
        return meta_default( '', $GLOBALS['item']->meta_title );
    } else

    $desc = \query\main::get_option( 'meta_coupon_title' );
    $repl = array( '%NAME%' => $GLOBALS['item']->title, '%STORE_NAME%' => $GLOBALS['item']->store_name, '%EXPIRATION%' => date( 'Y/m/d', strtotime( $GLOBALS['item']->expiration_date ) ), '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

    return str_replace( array_keys( $repl ), array_values( $repl ), esc_html( $desc ) );

    }

    /* METATAGS - KEYWORDS */

    function meta_keywords() {

    if( !empty( $GLOBALS['item']->meta_keywords ) ) {
        return meta_default( '', $GLOBALS['item']->meta_keywords );
    } else

    $desc = \query\main::get_option( 'meta_coupon_keywords' );
    $repl = array( '%NAME%' => $GLOBALS['item']->title, '%STORE_NAME%' => $GLOBALS['item']->store_name, '%EXPIRATION%' => date( 'Y/m/d', strtotime( $GLOBALS['item']->expiration_date ) ), '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

    return str_replace( array_keys( $repl ), array_values( $repl ), esc_html( $desc ) );

    }

    /* METATAGS - DESCRIPTION */

    function meta_description() {

    if( !empty( $GLOBALS['item']->meta_description ) ) {
        return meta_default( '', $GLOBALS['item']->meta_description );
    } else

    $desc = \query\main::get_option( 'meta_coupon_desc' );
    $repl = array( '%NAME%' => $GLOBALS['item']->title, '%STORE_NAME%' => $GLOBALS['item']->store_name, '%EXPIRATION%' => date( 'Y/m/d', strtotime( $GLOBALS['item']->expiration_date ) ), '%YEAR%' => date('Y'), '%MONTH%' => date('F') );

    return str_replace( array_keys( $repl ), array_values( $repl ), esc_html( $desc ) );

    }

    /* METATAGS - IMAGE */

    function meta_image( $image = '' ) {

        return \query\main::store_avatar( $GLOBALS['item']->store_img );

    }

    /* IS CURRENT PAGE */

    function this_is_coupon( $identifier = 0 ) {

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

    do_action( 'before_coupon_page', $GLOBALS['item'] );

} else {

    /* THIS IS 404 PAGE */

    function this_is_404_page() {

        return true;

    }

}