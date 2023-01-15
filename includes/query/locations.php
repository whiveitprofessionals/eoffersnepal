<?php

namespace query;

/** */

class locations {

/* GET NUMBER OF COUNTRIES */

public static function countries( $categories = array() ) {
    return self::have_countries( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF STATES */

public static function states( $categories = array() ) {
    return self::have_states( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF CITIES */

public static function cities( $categories = array() ) {
    return self::have_cities( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF LOCATIONS FOR A STORE */

public static function store_locations( $categories = array() ) {
    return self::have_store_locations( $categories, array( 'only_count' => true ) );
}

/* CHECK IF A COUNTRY EXIST */

public static function country_exists( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, name FROM " . DB_TABLE_PREFIX . "countries WHERE (id = ? OR name = ?)" );
    $stmt->bind_param( "is", $id, $id );
    $stmt->execute();
    $stmt->bind_result( $cid, $name );
    $stmt->fetch();
    $stmt->close();

    if( !empty( $cid ) ) {
        return array( 'ID' => $cid, 'name' => $name );
    }

    return false;

}

/* GET INFORMATION ABOUT A COUNTRY */

public static function country_info( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, name, visible, lat, lng, lastupdate_by, lastupdate, date FROM " . DB_TABLE_PREFIX . "countries WHERE id = ? OR name = ?");
    $stmt->bind_param( "is", $id, $id );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $name, $visible, $lat, $lng, $lastupdate_by, $last_update, $date );
    $stmt->fetch();
    $stmt->close();

    return (object) array( 'ID' => $id, 'user' => $user, 'name' => esc_html( $name ), 'visible' => (boolean) $visible, 'lat' => $lat, 'lng' => $lng, 'lastupdate_by' => $lastupdate_by, 'last_update' => $last_update, 'date' => $date );

}

/* CHECK IF A STATE EXIST */

public static function state_exists( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, name FROM " . DB_TABLE_PREFIX . "states WHERE (id = ? OR name = ?)" );
    $stmt->bind_param( "is", $id, $id );
    $stmt->execute();
    $stmt->bind_result( $sid, $name );
    $stmt->fetch();
    $stmt->close();

    if( !empty( $sid ) ) {
        return array( 'ID' => $sid, 'name' => $name );
    }

    return false;

}

/* GET INFORMATION ABOUT A STATE */

public static function state_info( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, name, country, visible, lat, lng, lastupdate_by, lastupdate, date FROM " . DB_TABLE_PREFIX . "states WHERE id = ? OR name = ?" );
    $stmt->bind_param( "is", $id, $id );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $name, $country, $visible, $lat, $lng, $lastupdate_by, $last_update, $date );
    $stmt->fetch();
    $stmt->close();

    return (object) array( 'ID' => $id, 'user' => $user, 'country' => $country, 'name' => esc_html( $name ), 'visible' => (boolean) $visible, 'lat' => $lat, 'lng' => $lng, 'lastupdate_by' => $lastupdate_by, 'last_update' => $last_update, 'date' => $date );

}

/* CHECK IF A CITY EXIST */

public static function city_exists( $id = 0 ) {

    global $db, $GET;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, name FROM " . DB_TABLE_PREFIX . "cities WHERE (id = ? OR name = ?)" );
    $stmt->bind_param( "is", $id, $id );
    $stmt->execute();
    $stmt->bind_result( $cid, $name );
    $stmt->fetch();
    $stmt->close();

    if( !empty( $cid ) ) {
        return array( 'ID' => $cid, 'name' => $name );
    }

    return false;

}

/* GET INFORMATION ABOUT A CITY */

public static function city_info( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, name, country, state, visible, lat, lng, lastupdate_by, lastupdate, date FROM " . DB_TABLE_PREFIX . "cities WHERE id = ? OR name = ?" );
    $stmt->bind_param( "is", $id, $id );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $name, $country, $state, $visible, $lat, $lng, $lastupdate_by, $last_update, $date );
    $stmt->fetch();
    $stmt->close();

    return (object) array( 'ID' => $id, 'user' => $user, 'country' => $country, 'state' => $state, 'name' => esc_html( $name ), 'visible' => (boolean) $visible, 'lat' => $lat, 'lng' => $lng, 'lastupdate_by' => $lastupdate_by, 'last_update' => $last_update, 'date' => $date );

}

/* CHECK IF A STORE LOCATION EXIST */

public static function store_location_exists( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "store_locations WHERE id = ?" );
    $stmt->bind_param( "i", $id );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    if( $count > 0 ) {
        return true;
    }

    return false;

}

/* GET INFORMATION ABOUT A STORE LOCATION */

public static function store_location_info( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT id, user, store, country, countryID, state, stateID, city, cityID, zip, address, lat, lng, lastupdate_by, lastupdate, date FROM " . DB_TABLE_PREFIX . "store_locations WHERE id = ?");
    $stmt->bind_param( "i", $id );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $store, $country, $countryID, $state, $stateID, $city, $cityID, $zip, $address, $lat, $lng, $lastupdate_by, $last_update, $date );
    $stmt->fetch();
    $stmt->close();

    return (object) array( 'ID' => $id, 'userID' => $user, 'storeID' => $store, 'country' => esc_html( $country ), 'countryID' => $countryID, 'state' => esc_html( $state ), 'stateID' => $stateID, 'city' => esc_html( $city ), 'cityID' => $cityID, 'zip' => esc_html( $zip ), 'address' => esc_html( $address ), 'lat' => $lat, 'lng' => $lng, 'lastupdate_by' => $lastupdate_by, 'last_update' => $last_update, 'date' => $date );

}

/* NUMBER OF COUNTRIES */

public static function have_countries( $category = array(), $special = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'name REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = strtolower( $categories['show'] );
        switch( $show ) {
            case 'all': break;
            default: $where[] = 'visible > 0'; break;
        }
    } else {
        $where[] = 'visible > 0';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "countries" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    if( !empty( $special['only_count'] ) ) {
        return $count;
    }


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

/* GET THE COUNTRIES */

public static function while_countries( $category = array() ) {

global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = $orderby = $limit = array();

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

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'name REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = strtolower( $categories['show'] );
        switch( $show ) {
            case 'all': break;
            default: $where[] = 'visible > 0'; break;
        }
    } else {
        $where[] = 'visible > 0';
    }

    if( isset( $categories['orderby'] ) ) {
    $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
    foreach( $order as $v ) {
        switch( $v ) {
            case 'rand': $orderby[] = 'RAND()'; break;
            case 'name': $orderby[] = 'name'; break;
            case 'name desc': $orderby[] = 'name DESC'; break;
            case 'update': $orderby[] = 'lastupdate'; break;
            case 'update desc': $orderby[] = 'lastupdate DESC'; break;
            case 'date': $orderby[] = 'date'; break;
            case 'date desc': $orderby[] = 'date DESC'; break;
        }
    }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, name, visible, lat, lng, lastupdate_by, lastupdate, date FROM " . DB_TABLE_PREFIX . "countries" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $name, $visible, $lat, $lng, $lastupdate_by, $last_update, $date );

    $data = array();
    while( $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'user' => $user, 'name' => esc_html( $name ), 'visible' => (boolean) $visible, 'lat' => $lat, 'lng' => $lng, 'lastupdate_by' => $lastupdate_by, 'last_update' => $last_update, 'date' => $date );
    }

    $stmt->close();

    return $data;

}

/* NUMBER OF STATES */

public static function have_states( $category = array(), $special = array() ) {

global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['country'] ) && strcasecmp( $categories['country'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
                return (int) $w;
        }, explode( ',', $categories['country'] ) ));
        if( !empty( $arr ) )
        $where[] = 'country IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'name REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = strtolower( $categories['show'] );
        switch( $show ) {
            case 'all': break;
            default: $where[] = 'visible > 0'; break;
        }
    } else {
        $where[] = 'visible > 0';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "states" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    if( !empty( $special['only_count'] ) ) {
        return $count;
    }


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

/* GET THE STATES */

public static function while_states( $category = array() ) {

global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = $orderby = $limit = array();

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

    if( !empty( $categories['country'] ) && strcasecmp( $categories['country'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['country'] ) ));
        if( !empty( $arr ) )
        $where[] = 'country IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'name REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = strtolower( $categories['show'] );
        switch( $show ) {
            case 'all': break;
            default: $where[] = 'visible > 0'; break;
        }
    } else {
        $where[] = 'visible > 0';
    }

    if( isset( $categories['orderby'] ) ) {
    $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
    foreach( $order as $v ) {
        switch( $v ) {
            case 'rand': $orderby[] = 'RAND()'; break;
            case 'name': $orderby[] = 'name'; break;
            case 'name desc': $orderby[] = 'name DESC'; break;
            case 'update': $orderby[] = 'lastupdate'; break;
            case 'update desc': $orderby[] = 'lastupdate DESC'; break;
            case 'date': $orderby[] = 'date'; break;
            case 'date desc': $orderby[] = 'date DESC'; break;
        }
    }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, name, country, visible, lat, lng, lastupdate_by, lastupdate, date FROM " . DB_TABLE_PREFIX . "states" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $name, $country, $visible, $lat, $lng, $lastupdate_by, $last_update, $date );

    $data = array();
    while( $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'user' => $user, 'name' => esc_html( $name ), 'country' => $country, 'visible' => (boolean) $visible, 'lat' => $lat, 'lng' => $lng, 'lastupdate_by' => $lastupdate_by, 'last_update' => $last_update, 'date' => $date );
    }

    $stmt->close();

    return $data;

}

/* NUMBER OF CITIES */

public static function have_cities( $category = array(), $special = array() ) {

global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['country'] ) && strcasecmp( $categories['country'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
                return (int) $w;
        }, explode( ',', $categories['country'] ) ));
        if( !empty( $arr ) )
        $where[] = 'country IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['state'] ) && strcasecmp( $categories['state'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
                return (int) $w;
        }, explode( ',', $categories['state'] ) ));
        if( !empty( $arr ) )
        $where[] = 'state IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'name REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = strtolower( $categories['show'] );
        switch( $show ) {
            case 'all': break;
            default: $where[] = 'visible > 0'; break;
        }
    } else {
        $where[] = 'visible > 0';
    }

    /*

    */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "cities" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    if( !empty( $special['only_count'] ) ) {
        return $count;
    }


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

/* GET THE CITIES */

public static function while_cities( $category = array() ) {

global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = $orderby = $limit = array();

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

    if( !empty( $categories['state'] ) && strcasecmp( $categories['state'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
                return (int) $w;
        }, explode( ',', $categories['state'] ) ));
        if( !empty( $arr ) )
        $where[] = 'state IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'name REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = strtolower( $categories['show'] );
        switch( $show ) {
            case 'all': break;
            default: $where[] = 'visible > 0'; break;
        }
    } else {
        $where[] = 'visible > 0';
    }

    if( isset( $categories['orderby'] ) ) {
    $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
    foreach( $order as $v ) {
        switch( $v ) {
            case 'rand': $orderby[] = 'RAND()'; break;
            case 'name': $orderby[] = 'name'; break;
            case 'name desc': $orderby[] = 'name DESC'; break;
            case 'date': $orderby[] = 'date'; break;
            case 'date desc': $orderby[] = 'date DESC'; break;
        }
    }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, name, state, visible, lat, lng, lastupdate_by, lastupdate, date FROM " . DB_TABLE_PREFIX . "cities" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $name, $state, $visible, $lat, $lng, $lastupdate_by, $last_update, $date );

    $data = array();
    while( $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'user' => $user, 'name' => esc_html( $name ), 'state' => $state, 'visible' => (boolean) $visible, 'lat' => $lat, 'lng' => $lng, 'lastupdate_by' => $lastupdate_by, 'last_update' => $last_update, 'date' => $date );
    }

    $stmt->close();

    return $data;

}

/* NUMBER OF STORE LOCATIONS */

public static function have_store_locations( $category = array(), $special = array() ) {

global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['store'] ) ) {
        $where[] = 'store = "' . (int) $categories['store'] . '"';
    }

    if( !empty( $categories['stores'] ) && strcasecmp( $categories['stores'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
                return (int) $w;
        }, explode( ',', $categories['stores'] ) ));
        if( !empty( $arr ) )
        $where[] = 'store IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(country, state, city, address) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "store_locations" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    if( !empty( $special['only_count'] ) ) {
        return $count;
    }


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

/* GET THE LOCATIONS FOR A STORE */

public static function while_store_locations( $category = array() ) {

global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = $orderby = $limit = array();

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

    if( !empty( $categories['store'] ) ) {
        $where[] = 'store = "' . (int) $categories['store'] . '"';
    }

    if( !empty( $categories['stores'] ) && strcasecmp( $categories['stores'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
                return (int) $w;
        }, explode( ',', $categories['stores'] ) ));
        if( !empty( $arr ) )
        $where[] = 'store IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(country, state, city, address) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['orderby'] ) ) {
    $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
    foreach( $order as $v ) {
        switch( $v ) {
            case 'rand': $orderby[] = 'RAND()'; break;
            case 'name': $orderby[] = 'name'; break;
            case 'name desc': $orderby[] = 'name DESC'; break;
            case 'date': $orderby[] = 'date'; break;
            case 'date desc': $orderby[] = 'date DESC'; break;
        }
    }
    }

    /*  */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, store, country, state, city, zip, address, lat, lng, lastupdate_by, lastupdate, date FROM " . DB_TABLE_PREFIX . "store_locations" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $store, $country, $state, $city, $zip, $address, $lat, $lng, $lastupdate_by, $last_update, $date );

    $data = array();
    while( $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'userID' => $user, 'storeID' => $store, 'country' => esc_html( $country ), 'state' => esc_html( $state ), 'city' => esc_html( $city ), 'address' => esc_html( $address ), 'zip' => esc_html( $zip ), 'lat' => $lat, 'lng' => $lng, 'lastupdate_by' => $lastupdate_by, 'last_update' => $last_update, 'date' => $date );
    }

    $stmt->close();

    return $data;

}

}