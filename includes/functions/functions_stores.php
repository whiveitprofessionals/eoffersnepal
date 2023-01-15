<?php

/* NAVIGATION */

function navigation() {
    return $GLOBALS['have_items'];
}

/* CHECK IF HAVE ITEMS */

function have_items( $category = array(), $special = array() ) {

    $GLOBALS['have_items'] = \query\main::have_stores( $category, '', $special );

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

/* SHOW STORES */

function items( $category = array(), $special = array() ) {
    return \query\main::while_stores( $category, '', $special );
}

/* ACTION BEFORE DISPLAYING THE CONTENT */

do_action( 'before_stores_page' );

/* IS CURRENT PAGE */

function this_is_stores_page() {

    return true;

}