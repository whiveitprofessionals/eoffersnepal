<?php

namespace admin;

/** */

class admin_query {

/* GET NUMBER OF SUGGETSIONS */

public static function suggestions( $categories = array() ) {
    return self::have_suggestions( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF BANNED IPS */

public static function banned( $categories = array() ) {
    return self::have_banned( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF NEWS */

public static function news( $categories = array() ) {
    return self::have_news( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF USER SESSIONS */

public static function user_sessions( $categories = array() ) {
    return self::have_usessions( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF SUBSCRIBERS */

public static function subscribers( $categories = array() ) {
    return self::have_subscribers( $categories, array( 'only_count' => true ) );
}


/* GET NUMBER OF CHAT MESSAGES */

public static function chat_messages( $categories = array() ) {
    return self::have_chat_messages( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF INSTALLED PLUGINS */

public static function plugins( $categories = array() ) {
    return self::have_plugins( $categories, array( 'only_count' => true ) );
}

/* CHECK IF SUGGESTON EXIST */

public static function suggestion_exists( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "suggestions WHERE id = ?");
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

/* GET INFORMATION ABOUT SUGGESTION */

public static function suggestion_info( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT id, user, type, viewed, name, url, description, message, date FROM " . DB_TABLE_PREFIX . "suggestions WHERE id = ?");
    $stmt->bind_param( "i", $id );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $type, $read, $name, $url, $description, $message, $date );
    $stmt->fetch();
    $stmt->close();

    return (object) array( 'ID' => $id, 'user' => $user, 'type' => $type, 'read' => $read, 'name' => esc_html( $name ), 'url' => esc_html( $url ), 'description' => esc_html( $description ), 'message' => esc_html( $message ), 'date' => $date );

}

/* CHECK IF SUBSCRIBER EXIST */

public static function subscriber_exists( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "newsletter WHERE id = ?");
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

/* GET INFORMATION ABOUT SUBSCRIBER */

public static function subscriber_info( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT id, email, ipaddr, econf, date FROM " . DB_TABLE_PREFIX . "newsletter WHERE id = ?");
    $stmt->bind_param( "i", $id );
    $stmt->execute();
    $stmt->bind_result( $id, $email, $ipaddr, $verified, $date );
    $stmt->fetch();
    $stmt->close();

    return (object) array( 'ID' => $id, 'email' => esc_html( $email ), 'IP' => esc_html( $ipaddr ), 'verified' => (boolean) $verified, 'date' => $date );

}

/* CHECK IF BANNED IP EXIST */

public static function banned_exists( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "banned WHERE id = ?");
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

/* GET INFORMATION ABOUT A BANNED IP */

public static function banned_info( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT id, ipaddr, registration, login, site, redirect_to, expiration, expiration_date, date FROM " . DB_TABLE_PREFIX . "banned WHERE id = ?");
    $stmt->bind_param( "i", $id );
    $stmt->execute();
    $stmt->bind_result( $id, $ip, $regs, $login, $site, $redirect, $expiration, $expiration_date, $date );
    $stmt->fetch();
    $stmt->close();

    return (object) array( 'ID' => $id, 'IP' => $ip, 'registration' => (boolean) $regs, 'login' => (boolean) $login, 'site' => (boolean) $site, 'redirect_to' => $redirect, 'expiration' => $expiration, 'expiration_date' => $expiration_date, 'date' => $date );

}

/* CHECK IF A STORE HAS BEEN IMPORTED */

public static function store_imported( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT id, category FROM " . DB_TABLE_PREFIX . "stores WHERE feedID = ?");
    $stmt->bind_param( "i", $id );
    $stmt->execute();
    $stmt->bind_result( $sid, $cat );
    $stmt->fetch();
    $stmt->close();

    if( !empty( $sid ) ) {
        return (object) array( 'ID' => $sid, 'catID' => $cat );
    }

    return false;

}

/* CHECK IF A COUPON HAS BEEN IMPORTED */

public static function coupon_imported( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT id, category FROM " . DB_TABLE_PREFIX . "coupons WHERE feedID = ?");
    $stmt->bind_param( "i", $id );
    $stmt->execute();
    $stmt->bind_result( $cid, $cat );
    $stmt->fetch();
    $stmt->close();

    if( !empty( $cid ) ) {
        return (object) array( 'ID' => $cid, 'catID' => $cat );
    }

    return false;

}

/* CHECK IF A PRODUCT HAS BEEN IMPORTED */

public static function product_imported( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT id, category FROM " . DB_TABLE_PREFIX . "products WHERE feedID = ?");
    $stmt->bind_param( "i", $id );
    $stmt->execute();
    $stmt->bind_result( $pid, $cat );
    $stmt->fetch();
    $stmt->close();

    if( !empty( $pid ) ) {
        return (object) array( 'ID' => $pid, 'catID' => $cat );
    }

    return false;

}

/* CHECK IF PLUGIN EXIST */

public static function plugin_exists( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "plugins WHERE id = ?");
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

/* GET INFORMATION ABOUT A PLUGIN */

public static function plugin_info( $id = 0 ) {

    global $db;

    $id = empty( $id ) ? $_GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, name, image, scope, main, options, menu, menu_ready, menu_icon, subadmin_view, extend_vars, description, visible, version, update_checker, uninstall, date FROM " . DB_TABLE_PREFIX . "plugins WHERE id = ? OR main REGEXP ?" );
    $stmt->bind_param( "is", $id, $id );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $name, $image, $scope, $main_file, $options_file, $menu, $menu_ready, $menu_icon, $subadmin_view, $vars, $description, $visible, $version, $update_checker, $uninstall, $date );
    $stmt->fetch();
    $stmt->close();

    return (object) array( 'ID' => $id, 'user' => $user, 'name' => esc_html( $name ), 'image' => esc_html( $image ), 'scope' => esc_html( $scope ), 'main_file' => esc_html( $main_file ), 'options_file' => esc_html( $options_file ), 'menu' => (boolean) $menu, 'menu_ready' => (boolean) $menu_ready, 'menu_icon' => $menu_icon, 'subadmin_view' => $subadmin_view, 'vars' => @unserialize( $vars ), 'description' => esc_html( $description ), 'update_checker' => esc_html( $update_checker ), 'version' => $version, 'uninstall_preview' => @unserialize( $uninstall ), 'visible' => (boolean) $visible, 'date' => $date );

}

/* NUMBER OF SUGGESTIONS */

public static function have_suggestions( $category = array(), $special = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /*  WHERE / ORDER BY */

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(name, url, description, message) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = strtolower( $categories['show'] );
        switch( $show ) {
            case 'read': $where[] = 'viewed = 1'; break;
            case 'notread': $where[] = 'viewed = 0'; break;
        }
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "suggestions" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

/* FETCH THE SUGGESTIONS */

public static function while_suggestions( $category = array() ) {

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
        $where[] = 'CONCAT(name, url, description, message) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = strtolower( $categories['show'] );
        switch( $show ) {
            case 'read': $where[] = 'viewed = 1'; break;
            case 'notread': $where[] = 'viewed = 0'; break;
        }
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( isset( $categories['orderby'] ) ) {
    $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
    foreach( $order as $v ) {
        switch( $v ) {
            case 'date': $orderby[] = 'date'; break;
            case 'date desc': $orderby[] = 'date DESC'; break;
        }
    }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, type, viewed, name, url, description, message, date FROM " . DB_TABLE_PREFIX . "suggestions" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $type, $read, $name, $url, $description, $message, $date );

    $data = array();
    while( $info = $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'user' => $user, 'type' => $type, 'read' => (boolean) $read, 'name' => esc_html( $name ), 'url' => esc_html( $url ), 'description' => esc_html( $description ), 'message' => esc_html( $message ), 'date' => $date );
    }

    $stmt->close();

    return $data;

}

/* NUMBER OF BANNED IPS */

public static function have_banned( $category = array(), $special = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'ipaddr REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( !empty( $categories['date'] ) ) {

        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }

    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "banned" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

/* FETCH BANNED IPS */

public static function while_banned( $category = array() ) {

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
        $where[] = 'ipaddr REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( isset( $categories['orderby'] ) ) {
    $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
    foreach( $order as $v ) {
        switch( $v ) {
            case 'date': $orderby[] = 'date'; break;
            case 'date desc': $orderby[] = 'date DESC'; break;
        }
    }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, ipaddr, registration, login, site, redirect_to, date FROM " . DB_TABLE_PREFIX . "banned" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $ip, $regs, $login, $site, $redirect, $date );

    $data = array();
    while( $info = $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'IP' => $ip, 'registration' => (boolean) $regs, 'login' => (boolean) $login, 'site' => (boolean) $site, 'redirect_to' => $redirect, 'date' => $date );
    }

    $stmt->close();

    return $data;

}

/* NUMBER OF NEWS */

public static function have_news( $category = array(), $special = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'title REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "news" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

/* FETCH THE NEWS */

public static function while_news( $category = array() ) {

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
        $where[] = 'title REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['orderby'] ) ) {
    $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
    foreach( $order as $v ) {
        switch( $v ) {
            case 'date': $orderby[] = 'date'; break;
            case 'date desc': $orderby[] = 'date DESC'; break;
        }
    }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT newsID, title, url, date FROM " . DB_TABLE_PREFIX . "news" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $title, $url, $date );

    $data = array();
    while( $info = $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'title' => esc_html( $title ), 'url' => esc_html( $url ), 'date' => $date );
    }

    $stmt->close();

    return $data;

}

/* NUMBER OF USER SESSIONS */

public static function have_usessions( $category = array(), $special = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'u.name REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "sessions s LEFT JOIN " . DB_TABLE_PREFIX . "users u ON (u.id = s.user)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

/* FETCH THE USER SESSIONS */

public static function while_usessions( $category = array() ) {

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
        $where[] = 'u.name REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['orderby'] ) ) {
    $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
    foreach( $order as $v ) {
        switch( $v ) {
            case 'date': $orderby[] = 's.date'; break;
            case 'date desc': $orderby[] = 's.date DESC'; break;
            case 'name': $orderby[] = 'u.name'; break;
            case 'name desc': $orderby[] = 'u.name DESC'; break;        }
    }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT s.id, s.user, u.name, u.avatar, s.expiration, s.date FROM " . DB_TABLE_PREFIX . "sessions s LEFT JOIN " . DB_TABLE_PREFIX . "users u ON (u.id = s.user)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $name, $avatar, $expiration, $date );

    $data = array();
    while( $info = $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'userID' => $user, 'name' => esc_html( $name ), 'avatar' => esc_html( $avatar ), 'expiration' => $expiration, 'date' => $date );
    }

    $stmt->close();

    return $data;

}

/* NUMBER OF SUBSRBERS */

public static function have_subscribers( $category = array(), $special = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $w_user = $w_newsletter = '';

    /* WHERE / ORDER BY */

    $where['users'][] = 'subscriber = 1';

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where['users'][] = 'CONCAT(name, email, ipaddr) REGEXP "' . \site\utils::dbp( $search ) . '"';
        $where['newsletter'][] = 'CONCAT(email, ipaddr) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = strtolower( $categories['show'] );
        switch( $show ) {
            case 'verified': $where['users'][] = 'valid >= 1'; $where['newsletter'][] = 'econf >= 1'; break;
            case 'notverified': $where['users'][] = 'valid = 0'; $where['newsletter'][] = 'econf = 0'; break;
        }
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM (SELECT id FROM " . DB_TABLE_PREFIX . "users " . ( empty( $where['users'] ) ? '' : ' WHERE ' . implode( ' AND ', $where['users'] ) ) . " UNION ALL SELECT id FROM " . DB_TABLE_PREFIX . "newsletter " . ( empty( $where['newsletter'] ) ? '' : ' WHERE ' . implode( ' AND ', $where['newsletter'] ) ) . ") AS count" );
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

/* FETCH THE SUBSCRIBERS */

public static function while_subscribers( $category = array() ) {

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

    $where['users'][] = 'subscriber = 1';

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where['users'][] = 'CONCAT(name, email, ipaddr) REGEXP "' . \site\utils::dbp( $search ) . '"';
        $where['newsletter'][] = 'CONCAT(email, ipaddr) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = strtolower( $categories['show'] );
        switch( $show ) {
            case 'verified': $where['users'][] = 'valid >= 1'; $where['newsletter'][] = 'econf >= 1'; break;
            case 'notverified': $where['users'][] = 'valid = 0'; $where['newsletter'][] = 'econf = 0'; break;
        }
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where['users'][] = $where['newsletter'][] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where['users'][] = $where['newsletter'][] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( isset( $categories['orderby'] ) ) {
    $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
    foreach( $order as $v ) {
        switch( $v ) {
            case 'date': $orderby[] = 'date'; break;
            case 'date desc': $orderby[] = 'date DESC'; break;
            case 'email': $orderby[] = 'email'; break;
            case 'email desc': $orderby[] = 'email DESC'; break;        }
    }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "(SELECT 1, id, email, name, avatar, valid, date FROM " . DB_TABLE_PREFIX . "users" . ( empty( $where['users'] ) ? '' : ' WHERE ' . implode( ' AND ', $where['users'] ) ) . ") UNION ALL (SELECT 0, id, email, '', '', econf, date FROM " . DB_TABLE_PREFIX . "newsletter" . ( empty( $where['newsletter'] ) ? '' : ' WHERE ' . implode( ' AND ', $where['newsletter'] ) ) . ")" . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $user, $id, $email, $name, $avatar, $verified, $date );

    $data = array();
    while( $info = $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'email' => esc_html( $email ), 'is_user' => (boolean) $user, 'verified' => (boolean) $verified, 'user_name' => esc_html( $name ), 'user_avatar' => esc_html( $avatar ), 'date' => $date );
    }

    $stmt->close();

    return $data;

}

/* NUMBER OF CLICKS */

public static function have_clicks( $category = array(), $special = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['store'] ) ) {
        $where[] = 'c.store = "' . (int) $categories['store'] . '"';
    }

    if( !empty( $categories['coupon'] ) ) {
        $where[] = 'c.coupon = "' . (int) $categories['coupon'] . '"';
    }

    if( !empty( $categories['product'] ) ) {
        $where[] = 'c.product = "' . (int) $categories['product'] . '"';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(c.country1, c.country2, c.browser, c.ipaddr, s.name) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( !empty( $categories['date'] ) ) {

        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'c.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'c.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }

    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "click c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

/* FETCH THE CLICKS */

public static function while_clicks( $category = array() ) {

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
        $where[] = 'c.store = "' . (int) $categories['store'] . '"';
    }

    if( !empty( $categories['coupon'] ) ) {
        $where[] = 'c.coupon = "' . (int) $categories['coupon'] . '"';
    }

    if( !empty( $categories['product'] ) ) {
        $where[] = 'c.product = "' . (int) $categories['product'] . '"';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(c.country1, c.country2, c.browser, c.ipaddr, s.name) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'c.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'c.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( isset( $categories['orderby'] ) ) {
    $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
    foreach( $order as $v ) {
        switch( $v ) {
            case 'date': $orderby[] = 'c.date'; break;
            case 'date desc': $orderby[] = 'c.date DESC'; break;
        }
    }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT c.id, c.store, c.coupon, c.product, c.user, c.ipaddr, c.browser, c.country1, c.country2, c.date, s.name, s.image FROM " . DB_TABLE_PREFIX . "click c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $store, $coupon, $product, $user, $IP, $browser, $country_code, $country_name, $date, $store_name, $store_img );

    $data = array();
    while( $info = $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'storeID' => $store, 'couponID' => $coupon, 'productID' => $product, 'user' => $user, 'IP' => esc_html( $IP ), 'browser' => esc_html( $browser ), 'country' => esc_html( $country_code ), 'country_full' => esc_html( $country_name ), 'date' => $date, 'store_name' => esc_html( $store_name ), 'store_img' => esc_html( $store_img ) );
    }

    $stmt->close();

    return $data;

}

/* NUMBER OF CHAT MESSAGES */

public static function have_chat_messages( $category = array(), $special = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'text REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "chat" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

/* FETCH THE CLICKS */

public static function while_chat_messages( $category = array() ) {

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
        $search = implode( '|', explode( ',', trim( $categories['search'] ) ) );
        $where[] = 'c.text REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( isset( $categories['orderby'] ) ) {
    $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );

    foreach( $order as $v ) {
        switch( $v ) {
            case 'date': $orderby[] = 'c.date'; break;
            case 'date desc': $orderby[] = 'c.date DESC'; break;
        }
    }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT c.id, c.user, u.name, u.avatar, c.text, c.date FROM " . DB_TABLE_PREFIX . "chat c LEFT JOIN " . DB_TABLE_PREFIX . "users u ON (u.id = c.user)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $user_name, $user_avatar, $text, $date );

    $data = array();
    while( $info = $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'userID' => $user, 'user_name' => esc_html( $user_name ), 'user_avatar' => esc_html( $user_avatar ), 'text' => \site\content::content( 'admin_chat_message', $text, true, false, true ), 'date' => $date );
    }

    $stmt->close();

    return $data;

}

/* NUMBER OF PLUGINS */

public static function have_plugins( $category = array(), $special = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(name, description) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
    $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
    foreach( $show as $v ) {
        switch( $v ) {
            case 'languages': $where[] = 'scope = "language"'; break;
            case 'payment_gateways': $where[] = 'scope = "pay_gateway"'; break;
            case 'feed_servers': $where[] = 'scope = "feed_server"'; break;
            case 'applications': $where[] = 'scope = ""'; break;
        }
    }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "plugins" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

/* FETCH THE PLUGINS */

public static function while_plugins( $category = array() ) {

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
        $where[] = 'CONCAT(name, description) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
    $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
    foreach( $show as $v ) {
        switch( $v ) {
            case 'languages': $where[] = 'scope = "language"'; break;
            case 'payment_gateways': $where[] = 'scope = "pay_gateway"'; break;
            case 'feed_servers': $where[] = 'scope = "feed_server"'; break;
            case 'applications': $where[] = 'scope = ""'; break;
        }
    }
    }

    if( isset( $categories['orderby'] ) ) {
    $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
    foreach( $order as $v ) {
        switch( $v ) {
            case 'name': $orderby[] = 'name'; break;
            case 'name desc': $orderby[] = 'name DESC'; break;
            case 'date': $orderby[] = 'date'; break;
            case 'date desc': $orderby[] = 'date DESC'; break;
        }
    }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, name, image, scope, main, options, menu, menu_ready, menu_icon, extend_vars, description, version, update_checker, uninstall, visible, date FROM " . DB_TABLE_PREFIX . "plugins" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $name, $image, $scope, $main_file, $options_file, $menu, $menu_ready, $menu_icon, $vars, $description, $version, $update_checker, $uninstall, $visible, $date );

    $data = array();
    while( $info = $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'user' => $user, 'name' => esc_html( $name ), 'image' => esc_html( $image ), 'scope' => esc_html( $scope ), 'main_file' => esc_html( $main_file ), 'options_file' => esc_html( $options_file ), 'menu' => (boolean) $menu, 'menu_ready' => (boolean) $menu_ready, 'menu_icon' => $menu_icon, 'vars' => @unserialize( $vars ), 'description' => esc_html( $description ), 'update_checker' => esc_html( $update_checker ), 'version' => $version, 'uninstall_preview' => @unserialize( $uninstall ), 'visible' => (boolean) $visible, 'date' => $date );
    }

    $stmt->close();

    return $data;

}

}