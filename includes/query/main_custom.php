<?php

namespace query;

/** This class should be used only to extend features */

class main_custom {

/* CHECK IF COUPON EXIST */

public static function item_exists( $clause = '' ) {

    if( empty( $clause ) ) {
        return false;
    }

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare( 'SELECT COUNT(*) FROM ' . DB_TABLE_PREFIX . 'coupons ' . $clause );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    return ( !empty( $count ) ? $count : false );

}

/* GET INFORMATION ABOUT A COUPON */

public static function item_info( $clause = '', $multiple = false ) {

    if( empty( $clause ) ) {
        return false;
    }

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT * FROM " . DB_TABLE_PREFIX . "coupons " . $clause );
    $stmt->execute();
    $result = $stmt->get_result();
    if( $multiple ) {
        $row = array();
        while( $line = $result->fetch_assoc() ) {
            $row[] = (object) $line;
        }
    } else {
        $row = (object) $result->fetch_assoc();
    }
    $stmt->free_result();
    $stmt->close();

    return ( !empty( $row ) ? (object) $row : false );

}

/* CHECK IF PRODUCT EXIST */

public static function product_exists( $clause = '' ) {

    if( empty( $clause ) ) {
        return false;
    }

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products " . $clause );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    return ( !empty( $count ) ? $count : false );

}

/* GET INFORMATION ABOUT A PRODUCT */

public static function product_info( $clause = '', $multiple = false ) {

    if( empty( $clause ) ) {
        return false;
    }

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT * FROM " . DB_TABLE_PREFIX . "products " . $clause );
    $stmt->execute();
    $result = $stmt->get_result();
    if( $multiple ) {
        $row = array();
        while( $line = $result->fetch_assoc() ) {
            $row[] = (object) $line;
        }
    } else {
        $row = (object) $result->fetch_assoc();
    }
    $stmt->free_result();
    $stmt->close();

    return ( !empty( $row ) ? (object) $row : false );

}

/* CHECK IF STORE EXIST */

public static function store_exists( $clause = '' ) {

    if( empty( $clause ) ) {
        return false;
    }

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores " . $clause );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    return ( !empty( $count ) ? $count : false );

}

/* GET INFORMATION ABOUT A STORE */

public static function store_info( $clause = '', $multiple = false ) {

    if( empty( $clause ) ) {
        return false;
    }

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT * FROM " . DB_TABLE_PREFIX . "stores " . $clause );
    $stmt->execute();
    $result = $stmt->get_result();
    if( $multiple ) {
        $row = array();
        while( $line = $result->fetch_assoc() ) {
            $row[] = (object) $line;
        }
    } else {
        $row = (object) $result->fetch_assoc();
    }
    $stmt->free_result();
    $stmt->close();

    return ( !empty( $row ) ? $row : false );

}

/* CHECK IF PAGE EXIST */

public static function page_exists( $clause = '' ) {

    if( empty( $clause ) ) {
        return false;
    }

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "pages " . $clause );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    return ( !empty( $count ) ? $count : false );

}

/* GET INFORMATION ABOUT A PAGE */

public static function page_info( $clause = '', $multiple = false ) {

    if( empty( $clause ) ) {
        return false;
    }

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT * FROM " . DB_TABLE_PREFIX . "pages " . $clause );
    $stmt->execute();
    $result = $stmt->get_result();
    if( $multiple ) {
        $row = array();
        while( $line = $result->fetch_assoc() ) {
            $row[] = (object) $line;
        }
    } else {
        $row = (object) $result->fetch_assoc();
    }
    $stmt->free_result();
    $stmt->close();

    return ( !empty( $row ) ? $row : false );

}

}