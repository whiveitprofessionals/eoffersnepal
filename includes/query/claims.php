<?php

namespace query;

/** */

class claims {

/* GET NUMBER OF COUPONS CLAIMED */

public static function items( $categories = array() ) {
    return self::have_items( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF CLAIMS */

public static function claims( $categories = array() ) {
    return self::have_claims( $categories, array( 'only_count' => true ) );
}

/* CHECK IF A COUPON CODE EXISTS */

public static function coupon_code_exists( $code ) {

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT cc.id, cc.used, cc.used_date FROM " . DB_TABLE_PREFIX . "coupon_claims cc LEFT JOIN " . DB_TABLE_PREFIX . "coupons c ON (c.id = cc.coupon) LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store) WHERE cc.code = ? AND s.user = ?" );
    $stmt->bind_param( "si", $code, $GLOBALS['me']->ID );
    $stmt->execute();
    $stmt->bind_result( $id, $used, $used_date );
    $stmt->fetch();
    $stmt->close();

    if( $id !== NULL ) {
        return [ 'ID' => $id, 'used' => $used, 'used_date' => $used_date ];
    }

    return false;

}

/* NUMBER OF ITEMS CLAIMED */

public static function have_items( $category = array() ) {

    if( $GLOBALS['me'] ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    $where[] = 'cc.user = "' . (int) $GLOBALS['me']->ID . '" AND c.visible > 0 AND s.visible > 0';

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(c.title, c.tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        foreach( $show as $v ) {
            switch( $v ) {
                case 'all': break;
                case 'expired': $where[] = 'c.expiration <= NOW()'; break;
                case 'active': $where[] = 'c.expiration > NOW()'; break;
                case 'popular': $where[] = 'c.popular > 0'; break;
                case 'exclusive': $where[] = 'c.exclusive > 0'; break;
                case 'codes': $where[] = "c.code != ''"; break;
                case 'printable': $where[] = "c.printable = 1"; break;
                case 'cashback': $where[] = 'c.cashback > 0'; break;
                case 'verified': $where[] = 'c.verified > 0'; break;
                case 'feed':    $where[] = 'c.feedID > 0'; break;
                case 'visible':    $where[] = 'c.visible > 0 AND s.visible > 0'; break;
                case 'notvisible': $where[] = 'c.visible = 0'; break;
            }
        }
    } else {
        $where[] = 'c.visible > 0 AND s.visible > 0';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "coupon_claims cc ON (cc.coupon = c.id) LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

    return false;

}

/* FETCH THE ITEMS CLAIMED */

public static function fetch_items( $category = array(), $special = array() ) {

    if( $GLOBALS['me'] ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = $limit = $orderby = array();

    /** make or not seo links */
    $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
    list( $seo_link_coupon, $seo_link_store, $seo_link_reviews, $extension ) = array( \query\main::get_option( 'seo_link_coupon' ), \query\main::get_option( 'seo_link_store' ), \query\main::get_option( 'seo_link_reviews' ), \query\main::get_option( 'extension' ) );

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

    $where[] = 'cc.user = "' . (int) $GLOBALS['me']->ID . '" AND c.visible > 0 AND s.visible > 0';

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(c.title, c.tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        foreach( $show as $v ) {
            switch( $v ) {
                case 'all': break;
                case 'expired': $where[] = 'c.expiration <= NOW()'; break;
                case 'active': $where[] = 'c.expiration > NOW()'; break;
                case 'popular': $where[] = 'c.popular > 0'; break;
                case 'sponsored': $where[] = 'c.paid_until > NOW()'; break;
                case 'exclusive': $where[] = 'c.exclusive > 0'; break;
                case 'codes': $where[] = "c.code != ''"; break;
                case 'printable': $where[] = "c.printable = 1"; break;
                case 'cashback': $where[] = 'c.cashback > 0'; break;
                case 'verified': $where[] = 'c.verified > 0'; break;
                case 'feed':    $where[] = 'c.feedID > 0'; break;
                case 'visible':    $where[] = 'c.visible > 0 AND s.visible > 0'; break;
                case 'notvisible': $where[] = 'c.visible = 0'; break;
            }
        }
    } else {
        $where[] = 'c.visible > 0 AND s.visible > 0';
    }

    if( isset( $categories['orderby'] ) ) {
        $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
        foreach( $order as $v ) {
            switch( $v ) {
                case 'rand': $orderby[] = 'RAND()'; break;
                case 'name': $orderby[] = 'c.title'; break;
                case 'name desc': $orderby[] = 'c.title DESC'; break;
                case 'update': $orderby[] = 'c.lastupdate'; break;
                case 'update desc': $orderby[] = 'c.lastupdate DESC'; break;
                case 'rating': $orderby[] = 'rating'; break;
                case 'rating desc': $orderby[] = 'rating DESC'; break;
                case 'votes': $orderby[] = 'votes'; break;
                case 'votes desc': $orderby[] = 'votes DESC'; break;
                case 'cvotes': $orderby[] = 'c.votes'; break;
                case 'cvotes desc': $orderby[] = 'c.votes DESC'; break;
                case 'views': $orderby[] = 'c.views'; break;
                case 'views desc': $orderby[] = 'c.views DESC'; break;
                case 'clicks': $orderby[] = 'c.clicks'; break;
                case 'clicks desc': $orderby[] = 'c.clicks DESC'; break;
                case 'popular': $orderby[] = 'c.popular'; break;
                case 'sponsored': $orderby[] = 'sponsored DESC'; break;
                case 'date': $orderby[] = 'c.date'; break;
                case 'date desc': $orderby[] = 'c.date DESC'; break;
                case 'active': $orderby[] = 'c.expiration'; break;
                case 'active desc': $orderby[] = 'c.expiration DESC'; break;
                case 'added_date': $orderby[] = 'f.date'; break;
                case 'added_date desc': $orderby[] = 'f.date DESC'; break;
            }
        }
    }

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : (boolean) \query\main::get_option( 'smilies_coupons' );
    $usesh = ( isset( $special['no_shortcodes'] ) && $special['no_shortcodes'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT c.id, c.feedID, c.user, c.store, c.category, c.popular, c.exclusive, c.printable, c.show_in_store, c.available_online, c.title, c.link, c.description, c.tags, c.image, c.code, c.source, c.claim_limit, c.claims, c.visible, c.views, c.clicks, c.start, c.expiration, c.cashback, c.url_title, c.votes, c.votes_percent, c.verified, c.last_verif, c.paid_until, c.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.physical, s.category, s.name, s.link, s.image, s.phoneno, s.sellonline, s.url_title, s.extra, IF(c.paid_until > NOW(), 1, 0) as sponsored FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "coupon_claims cc ON (cc.coupon = c.id) LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $popular, $exclusive, $printable, $show_in_store, $avab_online, $title, $link, $description, $tags, $image, $code, $source, $claim_limit, $claims, $visible, $views, $clicks, $start, $expiration, $cashback, $url_title, $votes, $votes_percent, $verified, $last_verif, $paid_until, $date, $reviews, $stars, $store_type, $store_cat, $store_name, $store_link, $store_img, $store_phone, $store_sellonline, $store_url_title, $store_extra, $sponsored );

    $data = array();
    while( $stmt->fetch() ) {

        $data[] = (object) value_with_filter( 'coupon_info_values', array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'code' => esc_html( $code ), 'title' => \site\content::title( 'items_name_list', $title, $useem, $useec, $usefl ), 'url' => ( preg_match( '/^http(s)?/i', $link ) ? esc_html( $link ) : esc_html( $store_link ) ), 'original_url' => $link, 'description' => \site\content::content( 'items_list', $description, $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'image2' => ( !empty( $image ) ? ( !filter_var( $image, FILTER_VALIDATE_URL ) ? $GLOBALS['siteURL'] . esc_html( $image ) : esc_html( $image ) ) : '' ), 'source' => ( empty( $source ) ? '' : ( filter_var( $source, FILTER_VALIDATE_URL ) ? esc_html( $source ) : $GLOBALS['siteURL'] . esc_html( $source ) ) ), 'visible' => (boolean) $visible, 'views' => $views, 'clicks' => $clicks, 'start_date' => $start, 'is_coupon' => ( !empty( $code ) ? true : false ), 'is_deal' => ( empty( $code ) ? true : false ), 'is_running' => ( strtotime( $start ) < time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_printable' => ( (boolean) $store_type ? (boolean) $printable : false ), 'is_show_in_store' => ( (boolean) $store_type ? (boolean) $show_in_store : false ), 'is_available_online' => ( ! (boolean) $store_type || (boolean) $avab_online ? true : false ), 'is_local_source' => ( empty( $source ) || filter_var( $source, FILTER_VALIDATE_URL ) ? false : true ), 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'is_popular' => (boolean) $popular, 'is_exclusive' => (boolean) $exclusive, 'is_verified' => (boolean) $verified, 'last_check' => $last_verif, 'votes' => $votes, 'votes_percent' => $votes_percent, 'claim_limit' => $claim_limit, 'claims' => $claims, 'paid_until' => $paid_until, 'extra' => @unserialize( $extra ), 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_is_physical' => (boolean) $store_type, 'store_img' => esc_html( $store_img ), 'store_catID' => $store_cat, 'store_name' => esc_html( $store_name ), 'store_url' => esc_html( $store_link ), 'store_phone_no' => esc_html( $store_phone ), 'store_sellonline' => (boolean) $store_sellonline, 'store_extra' => @unserialize( $store_extra ), 'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_coupon, $title, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?id=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store_url_title, $store, $extension ) : $GLOBALS['siteURL'] . '?store=' . $store ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store_url_title, $store, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $store ) ) );

    }

    $stmt->close();

    return $data;

    }

    return array();

}

/* NUMBER OF CLAIMS */

public static function have_claims( $category = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        foreach( $show as $v ) {
            switch( $v ) {
                case 'all': break;
                case 'used': $where[] = 'used = 1'; break;
                case 'not_used':    $where[] = 'used = 0'; break;
            }
        }
    }

    if( !empty( $categories['coupon'] ) ) {
        $where[] = 'coupon = "' . (int) $categories['coupon'] . '"';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupon_claims" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

/* FETCH THE CLAIMS */

public static function fetch_claims( $category = array(), $special = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = $limit = $orderby = array();

    /** make or not seo links */
    $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
    list( $seo_link_coupon, $seo_link_store, $seo_link_reviews, $extension ) = array( \query\main::get_option( 'seo_link_coupon' ), \query\main::get_option( 'seo_link_store' ), \query\main::get_option( 'seo_link_reviews' ), \query\main::get_option( 'extension' ) );

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

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        foreach( $show as $v ) {
            switch( $v ) {
                case 'all': break;
                case 'used': $where[] = 'used = 1'; break;
                case 'not_used':    $where[] = 'used = 0'; break;
            }
        }
    }

    if( !empty( $categories['coupon'] ) ) {
        $where[] = 'coupon = "' . (int) $categories['coupon'] . '"';
    }

    if( isset( $categories['orderby'] ) ) {
        $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
        foreach( $order as $v ) {
            switch( $v ) {
                case 'rand': $orderby[] = 'RAND()'; break;
                case 'date': $orderby[] = 'date'; break;
                case 'date desc': $orderby[] = 'date DESC'; break;
                case 'used_date': $orderby[] = 'used_date'; break;
                case 'used_date desc': $orderby[] = 'used_date DESC'; break;
            }
        }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, coupon, user, code, used, used_date, date FROM " . DB_TABLE_PREFIX . "coupon_claims" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $coupon, $user, $code, $used, $used_date, $date );

    $data = array();
    while( $stmt->fetch() ) {

        $data[] = (object) array( 'ID' => $id, 'couponID' => $coupon, 'get_coupon' => new info_class_coupon( $coupon ), 'userID' => $user, 'get_user' => new info_class_user( $user ), 'code' => $code, 'is_used' => $used, 'used_date' => $used_date, 'date' => $date );

    }

    $stmt->close();

    return $data;

}

}