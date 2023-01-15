<?php

namespace query;

/** */

class gallery {

/* GET NUMBER OF COUPONS CLAIMED */

public static function images( $categories = array() ) {
    return self::have_images( $categories, array( 'only_count' => true ) );
}

/* CHECK IF AN IMAGE EXISTS */

public static function exists( $image_id = 0 ) {

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT id FROM " . DB_TABLE_PREFIX . "gallery WHERE id = ?" );
    $stmt->bind_param( "i", $image_id );
    $stmt->execute();
    $stmt->bind_result( $id );
    $stmt->fetch();
    $stmt->close();

    if( $id !== NULL ) {
        return $id;
    }

    return false;

}

/* GET INFORMATION ABOUT IMAGE */

public static function image_info( $image_id = 0 ) {

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, title, cat_id, sizes, date FROM " . DB_TABLE_PREFIX . "gallery WHERE id = ?");
    $stmt->bind_param( "i", $image_id );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $title, $cat_id, $sizes, $date );
    $stmt->fetch();
    $stmt->close();

    return (object) value_with_filter( 'image_info_values', array( 'ID' => $id, 'userID' => $user, 'title' => esc_html( $title ), 'catID' => esc_html( $cat_id ), 'sizes' => value_with_filter( 'image_info_sizes', @unserialize( $sizes ) ), 'date' => $date ) );

}

/* NUMBER OF IMAGES */

public static function have_images( $category = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['ids'] ) && strcasecmp( $categories['ids'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['ids'] ) ));
        if( !empty( $arr ) )
        $where[] = 'id IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
        if( !isset( $categories['orderby'] ) ) {
            $orderby[] = 'field(id,' . \site\utils::dbp( implode(',', $arr) ) . ')';
        }
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'title REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( !empty( $categories['category'] ) ) {
        $where[] = 'cat_id = "' . \site\utils::dbp( $categories['category'] ) . '"';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "gallery" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();


    $pags = array();
    $pags['results'] = $count;
    $pags['per_page'] = ( !empty( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
    $pags['pages'] = ceil( $pags['results'] / $pags['per_page'] );
    $page = ( !empty( $categories['page'] ) ? (int) $categories['page'] : ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 ) );
    if( $page < 1 ) $page = 1;
    if( $page > $pags['pages'] ) $page = $pags['pages'];
    $pags['page'] =    $page;
    if( $pags['pages'] > $pags['page'] ) $pags['next_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']+1) ) );
    if( $pags['pages'] > 1 && $pags['page'] > 1 ) $pags['prev_page'] = \site\utils::update_uri( '', array( 'page' => ($pags['page']-1) ) );

    return $pags;

}

/* FETCH THE IMAGES */

public static function fetch_images( $category = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = $limit = $orderby = array();

    if( isset( $categories['max'] ) ) {
        if( !empty( $categories['max'] ) ) {
            $limit[] = $categories['max'];
        }
    } else {
        $page = ( !empty( $categories['page'] ) ? (int) $categories['page'] : ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 ) );
        $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
        $offset = isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0;

        $limit[] = $offset;
        $limit[] = $per_page;
    }

    /* WHERE / ORDER BY */

    if( !empty( $categories['ids'] ) && strcasecmp( $categories['ids'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['ids'] ) ));
        if( !empty( $arr ) )
        $where[] = 'id IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
        if( !isset( $categories['orderby'] ) ) {
            $orderby[] = 'field(id,' . \site\utils::dbp( implode(',', $arr) ) . ')';
        }
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'title REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( !empty( $categories['category'] ) ) {
        $where[] = 'cat_id = "' . \site\utils::dbp( $categories['category'] ) . '"';
    }

    if( isset( $categories['orderby'] ) ) {
        $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
        foreach( $order as $v ) {
            switch( $v ) {
                case 'rand': $orderby[] = 'RAND()'; break;
                case 'name': $orderby[] = 'title'; break;
                case 'name desc': $orderby[] = 'title DESC'; break;
                case 'date': $orderby[] = 'date'; break;
                case 'date desc': $orderby[] = 'date DESC'; break;
            }
        }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, title, cat_id, sizes, date FROM " . DB_TABLE_PREFIX . "gallery" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $title, $cat_id, $sizes, $date );

    $data = array();
    while( $stmt->fetch() ) {

        $data[] = (object) value_with_filter( 'image_info_values', array( 'ID' => $id, 'userID' => $user, 'title' => esc_html( $title ), 'catID' => esc_html( $cat_id ), 'sizes' => value_with_filter( 'image_info_sizes', @unserialize( $sizes ) ), 'date' => $date ) );

    }

    $stmt->close();

    return $data;

}

}