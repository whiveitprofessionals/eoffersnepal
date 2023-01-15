<?php

/* PUT THE OBJECT INTO GLOBAL VARIABLES */

$GLOBALS['item'] = \query\main::page_info( 0, array( 'update_views' => '' ) );
$GLOBALS['exists'] = \query\main::page_exists( 0, array( 'user_view' => '' ) );

/* CHECK IF PAGE EXISTS */

function exists() {
  return $GLOBALS['exists'];
}

/* INFORMATION ABOUT PAGE */

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

/* GET THE ID */

function ID() {
    return $GLOBALS['item']->ID;
}

/* GET THE TITLE */

function title() {
    return $GLOBALS['item']->name;
}

/* GET THE CONTENT */

function content( $escape = false, $use_shortcodes = true, $allow_filters = true ) {
    $html = nl2br( $GLOBALS['item']->html );
    return \site\content::content( 'page_single', esc_html( $html ), (boolean) \query\main::get_option( 'smilies_pages' ), $use_shortcodes, false, $escape, $allow_filters );
}

/* PERSONALIZED META TAGS ONLY IF THE PAGE EXISTS */

if( $GLOBALS['exists'] > 0 ) {

    /* METATAGS - TITLE */

    function meta_title() {

        if( !empty( $GLOBALS['item']->meta_title ) ) {
            return meta_default( '', $GLOBALS['item']->meta_title );
        } else
            return meta_default( '', \query\main::get_option( 'sitetitle' ) );

    }

    /*  METATAGS - KEYWORDS */

    function meta_keywords() {

        if( !empty( $GLOBALS['item']->meta_keywords ) ) {
          return meta_default( '', $GLOBALS['item']->meta_keywords );
        } else
          return meta_default( '', \query\main::get_option( 'meta_keywords' ) );

    }

    /*  METATAGS - DESCRIPTION */

    function meta_description() {

        if( !empty( $GLOBALS['item']->meta_description ) ) {
          return meta_default( '', $GLOBALS['item']->meta_description );
        } else
          return meta_default( '', \query\main::get_option( 'meta_description' ) );

    }

    /* IS CURRENT PAGE */

    function this_is_page( $identifier = 0 ) {

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

    do_action( 'before_page_page', $GLOBALS['item'] );

} else {

    /* THIS IS 404 PAGE */

    function this_is_404_page() {

        return true;

    }

}