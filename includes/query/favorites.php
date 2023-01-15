<?php

namespace query;

/** */

class favorites {

/* GET NUMBER OF FAVORITE STORES */

public static function stores( $categories = array() ) {
    return self::have_favorites( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF FAVORITE COUPONS */

public static function items( $categories = array() ) {
    return self::have_favorite_items( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF FAVORITE PRODUCTS */

public static function products( $categories = array() ) {
    return self::have_favorite_products( $categories, '', array( 'only_count' => true ) );
}

/* NUMBER OF FAVORITE STORES */

public static function have_favorites( $category = array(), $special = array() ) {

    if( $GLOBALS['me'] ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    $where[] = 'f.user = "' . (int) $GLOBALS['me']->ID . '" AND s.visible > 0';

    if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]{1}$|^0-9$)/', $categories['firstchar'] ) ) {
        $where[] = 's.name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(s.name, s.tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        foreach( $show as $v ) {
            switch( $v ) {
                case 'all': break;
                case 'physical': $where[] = 's.physical > 0'; break;
                case 'online':  $where[] = 's.physical = 0'; break;
                case 'popular': $where[] = 's.popular > 0'; break;
                case 'feed': $where[] = 's.feedID > 0'; break;
                case 'notvisible': $where[] = 's.visible = 0'; break;
                default: $where[] = 's.visible > 0'; break;
            }
        }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "favorite f LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = f.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

    return false;

}

/* FETCH THE FAVORITE STORES */

public static function fetch_favorites( $category = array(), $special = array() ) {

    if( $GLOBALS['me'] ) {

    global $db;

    /** make or not seo links */
    $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
    list( $seo_link_store, $seo_link_reviews, $extension ) = array( \query\main::get_option( 'seo_link_store' ), \query\main::get_option( 'seo_link_reviews' ), \query\main::get_option( 'extension' ) );

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

    $where[] = 'f.user = "' . (int) $GLOBALS['me']->ID . '" AND s.visible > 0';

    if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]{1}$|^0-9$)/', $categories['firstchar'] ) ) {
        $where[] = 's.name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(s.name, s.tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        foreach( $show as $v ) {
            switch( $v ) {
                case 'all': break;
                case 'physical': $where[] = 's.physical > 0'; break;
                case 'online':  $where[] = 's.physical = 0'; break;
                case 'popular': $where[] = 's.popular > 0'; break;
                case 'feed': $where[] = 's.feedID > 0'; break;
                case 'notvisible': $where[] = 's.visible = 0'; break;
                default: $where[] = 's.visible > 0'; break;
            }
        }
    }

    if( isset( $categories['orderby'] ) ) {
        $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
        foreach( $order as $v ) {
            switch( $v ) {
                case 'rand': $orderby[] = 'RAND()'; break;
                case 'name': $orderby[] = 's.name'; break;
                case 'name desc': $orderby[] = 's.name DESC'; break;
                case 'update': $orderby[] = 's.lastupdate'; break;
                case 'update desc': $orderby[] = 's.lastupdate DESC'; break;
                case 'rating': $orderby[] = 's.rating'; break;
                case 'rating desc': $orderby[] = 's.rating DESC'; break;
                case 'votes': $orderby[] = 's.votes'; break;
                case 'votes desc': $orderby[] = 's.votes DESC'; break;
                case 'views': $orderby[] = 's.views'; break;
                case 'views desc': $orderby[] = 's.views DESC'; break;
                case 'date': $orderby[] = 's.date'; break;
                case 'date desc': $orderby[] = 's.date DESC'; break;
                case 'added_date': $orderby[] = 'f.date'; break;
                case 'added_date desc': $orderby[] = 'f.date DESC'; break;
            }
        }
    }

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : (boolean) \query\main::get_option( 'smilies_stores' );
    $usesh = ( isset( $special['no_shortcodes'] ) && $special['no_shortcodes'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT s.id, s.feedID, s.user, s.category, s.popular, s.physical, s.name, s.link, s.description, s.tags, s.image, s.hours, s.phoneno, s.sellonline, s.visible, s.views, s.url_title, s.extra, s.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons WHERE store = s.id AND visible > 0), (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products WHERE store = s.id AND visible > 0) FROM " . DB_TABLE_PREFIX . "favorite f LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (f.store = s.id)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $feed_id, $user, $cat, $popular, $physical, $name, $link, $description, $tags, $image, $hours, $phone, $sellonline, $visible, $views, $url_title, $extra, $date, $reviews, $stars, $coupons, $products );

    $data = array();
    while( $stmt->fetch() ) {

        $data[] = (object) value_with_filter( 'store_info_values', array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'catID' => $cat, 'name' => \site\content::title( 'stores_name_list', $name, $useem, $useec, $usefl ), 'url' => esc_html( $link ), 'description' => \site\content::content( 'stores_list', $description, $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'hours' => @unserialize( $hours ), 'phone_no' => esc_html( $phone ), 'sellonline' => (boolean) ( !$physical ? 1 : $sellonline ), 'sellonline2' => (boolean) $sellonline, 'extra' => @unserialize( $extra ), 'date' => $date, 'visible' => (boolean) $visible, 'views' => $views, 'reviews' => $reviews, 'stars' => $stars, 'coupons' => $coupons, 'products' => $products, 'is_popular' => (boolean) $popular, 'is_physical' => (boolean) $physical, 'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $name, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?store=' . $id ), 'reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $name, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $id ) ) );

    }

    $stmt->close();

    return $data;

    }

    return array();

}

/* NUMBER OF FAVORITE ITEMS */

public static function have_items( $category = array() ) {

    if( $GLOBALS['me'] ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    $where[] = 'f.user = "' . (int) $GLOBALS['me']->ID . '" AND c.visible > 0 AND s.visible > 0';

    if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]{1}$|^0-9$)/', $categories['firstchar'] ) ) {
        $where[] = 's.name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
    }

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
                default: $where[] = 'c.visible > 0 AND s.visible > 0'; break;
            }
        }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "favorite f ON (f.store = c.store) LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = f.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

/* FETCH THE FAVORITE ITEMS */

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

    $where[] = 'f.user = "' . (int) $GLOBALS['me']->ID . '" AND c.visible > 0 AND s.visible > 0';

    if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]{1}$|^0-9$)/', $categories['firstchar'] ) ) {
        $where[] = 's.name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
    }

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
                default: $where[] = 'c.visible > 0 AND s.visible > 0'; break;
            }
        }
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
                case 'date': $orderby[] = 'c.date'; break;
                case 'date desc': $orderby[] = 'c.date DESC'; break;
                case 'active': $orderby[] = 'c.expiration'; break;
                case 'active desc': $orderby[] = 'c.expiration DESC'; break;
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
    $stmt->prepare( "SELECT c.id, c.feedID, c.user, c.store, c.category, c.popular, c.exclusive, c.printable, c.show_in_store, c.available_online, c.title, c.link, c.description, c.tags, c.image, c.code, c.source, c.claim_limit, c.claims, c.visible, c.views, c.clicks, c.start, c.expiration, c.cashback, c.url_title, c.votes, c.votes_percent, c.verified, c.last_verif, c.paid_until, c.extra, c.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.physical, s.category, s.name, s.link, s.image, s.phoneno, s.sellonline, s.url_title, s.extra FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "favorite f ON (f.store = c.store) LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = f.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $popular, $exclusive, $printable, $show_in_store, $avab_online, $title, $link, $description, $tags, $image, $code, $source, $claim_limit, $claims, $visible, $views, $clicks, $start, $expiration, $cashback, $url_title, $votes, $votes_percent, $verified, $last_verif, $paid_until, $extra, $date, $reviews, $stars, $store_type, $store_cat, $store_name, $store_link, $store_img, $store_phone, $store_sellonline, $store_url_title, $store_extra );

    $data = array();
    while( $stmt->fetch() ) {

        $data[] = (object) value_with_filter( 'coupon_info_values', array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'code' => esc_html( $code ), 'title' => \site\content::title( 'items_name_list', $title, $useem, $useec, $usefl ), 'url' => ( preg_match( '/^http(s)?/i', $link ) ? esc_html( $link ) : esc_html( $store_link ) ), 'original_url' => $link, 'description' => \site\content::content( 'items_list', $description, $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'image2' => ( !empty( $image ) ? ( !filter_var( $image, FILTER_VALIDATE_URL ) ? $GLOBALS['siteURL'] . esc_html( $image ) : esc_html( $image ) ) : '' ), 'source' => ( empty( $source ) ? '' : ( filter_var( $source, FILTER_VALIDATE_URL ) ? esc_html( $source ) : $GLOBALS['siteURL'] . esc_html( $source ) ) ), 'visible' => (boolean) $visible, 'views' => $views, 'clicks' => $clicks, 'start_date' => $start, 'is_coupon' => ( !empty( $code ) ? true : false ), 'is_deal' => ( empty( $code ) ? true : false ), 'is_running' => ( strtotime( $start ) < time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_show_in_store' => ( (boolean) $store_type ? (boolean) $show_in_store : false ), 'is_printable' => ( (boolean) $store_type ? (boolean) $printable : false ), 'is_available_online' => ( ! (boolean) $store_type || (boolean) $avab_online ? true : false ), 'is_local_source' => ( empty( $source ) || filter_var( $source, FILTER_VALIDATE_URL ) ? false : true ), 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'is_popular' => (boolean) $popular, 'is_exclusive' => (boolean) $exclusive, 'is_verified' => (boolean) $verified, 'last_check' => $last_verif, 'votes' => $votes, 'votes_percent' => $votes_percent, 'claim_limit' => $claim_limit, 'claims' => $claims, 'paid_until' => $paid_until, 'extra' => @unserialize( $extra ), 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_is_physical' => (boolean) $store_type, 'store_img' => esc_html( $store_img ), 'store_catID' => $store_cat, 'store_name' => esc_html( $store_name ), 'store_url' => esc_html( $store_link ), 'store_phone_no' => esc_html( $store_phone ), 'store_sellonline' => (boolean) $store_sellonline, 'store_extra' => @unserialize( $store_extra ), 'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_coupon, $title, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?id=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store_url_title, $store, $extension ) : $GLOBALS['siteURL'] . '?store=' . $store ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store_url_title, $store, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $store ) ) );

    }

    $stmt->close();

    return $data;

    }

    return array();

}

/* NUMBER OF FAVORITE PRODUCTS */

public static function have_products( $category = array() ) {

    if( $GLOBALS['me'] ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    $where[] = 'f.user = "' . (int) $GLOBALS['me']->ID . '" AND p.visible > 0 AND s.visible > 0';

    if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]{1}$|^0-9$)/', $categories['firstchar'] ) ) {
        $where[] = 's.name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(p.title, p.tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        foreach( $show as $v ) {
            switch( $v ) {
                case 'all': break;
                case 'expired': $where[] = 'p.expiration <= NOW()'; break;
                case 'active':    $where[] = 'p.expiration > NOW()'; break;
                case 'popular':    $where[] = 'p.popular > 0'; break;
                case 'cashback': $where[] = 'p.cashback > 0'; break;
                case 'feed':    $where[] = 'p.feedID > 0'; break;
                case 'visible':    $where[] = 'p.visible > 0 AND s.visible > 0'; break;
                case 'notvisible': $where[] = 'p.visible = 0'; break;
                default: $where[] = 'p.visible > 0 AND s.visible > 0'; break;
            }
        }
    } else {
        $where[] = 'p.visible > 0 AND s.visible > 0';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "favorite f ON (f.store = p.store) LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = f.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

/*  FETCH THE FAVORITE PRODUCTS */

public static function fetch_products( $category = array(), $special = array() ) {

    if( $GLOBALS['me'] ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = $limit = $orderby = array();

    /** make or not seo links */
    $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
    list( $seo_link_product, $seo_link_store, $seo_link_reviews, $extension ) = array( \query\main::get_option( 'seo_link_product' ), \query\main::get_option( 'seo_link_store' ), \query\main::get_option( 'seo_link_reviews' ), \query\main::get_option( 'extension' ) );

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

    $where[] = 'f.user = "' . (int) $GLOBALS['me']->ID . '" AND p.visible > 0 AND s.visible > 0';

    if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]{1}$|^0-9$)/', $categories['firstchar'] ) ) {
        $where[] = 's.name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(p.title, p.tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        foreach( $show as $v ) {
            switch( $v ) {
                case 'all': break;
                case 'expired': $where[] = 'p.expiration <= NOW()'; break;
                case 'active':    $where[] = 'p.expiration > NOW()'; break;
                case 'popular':    $where[] = 'p.popular > 0'; break;
                case 'cashback': $where[] = 'p.cashback > 0'; break;
                case 'feed':    $where[] = 'p.feedID > 0'; break;
                case 'visible':    $where[] = 'p.visible > 0 AND s.visible > 0'; break;
                case 'notvisible': $where[] = 'p.visible = 0'; break;
                default: $where[] = 'p.visible > 0 AND s.visible > 0'; break;
            }
        }
    } else {
        $where[] = 'p.visible > 0 AND s.visible > 0';
    }

    if( isset( $categories['orderby'] ) ) {
        $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
        foreach( $order as $v ) {
            switch( $v ) {
                case 'rand': $orderby[] = 'RAND()'; break;
                case 'name': $orderby[] = 'p.title'; break;
                case 'name desc': $orderby[] = 'p.title DESC'; break;
                case 'update': $orderby[] = 'p.lastupdate'; break;
                case 'update desc': $orderby[] = 'p.lastupdate DESC'; break;
                case 'rating': $orderby[] = 'rating'; break;
                case 'rating desc': $orderby[] = 'rating DESC'; break;
                case 'votes': $orderby[] = 'votes'; break;
                case 'votes desc': $orderby[] = 'votes DESC'; break;
                case 'views': $orderby[] = 'p.views'; break;
                case 'views desc': $orderby[] = 'p.views DESC'; break;
                case 'price': $orderby[] = 'p.price'; break;
                case 'price desc': $orderby[] = 'p.price DESC'; break;
                case 'discount': $orderby[] = '(p.old_price - p.price)'; break;
                case 'discount desc': $orderby[] = '(p.old_price - p.price) DESC'; break;
                case 'expiration': $orderby[] = 'p.expiration'; break;
                case 'expiration desc': $orderby[] = 'p.expiration DESC'; break;
                case 'date': $orderby[] = 'p.date'; break;
                case 'date desc': $orderby[] = 'p.date DESC'; break;
                case 'active': $orderby[] = 'p.expiration'; break;
                case 'active desc': $orderby[] = 'p.expiration DESC'; break;
            }
        }
    }

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : (boolean) \query\main::get_option( 'smilies_products' );
    $usesh = ( isset( $special['no_shortcodes'] ) && $special['no_shortcodes'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT p.id, p.feedID, p.user, p.store, p.category, p.popular, p.title, p.link, p.description, p.tags, p.image, p.price, p.old_price, p.currency, p.visible, p.views, p.start, p.expiration, p.cashback, p.url_title, p.paid_until, p.extra, p.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.id, s.category, s.physical, s.name, s.image, s.link, s.phoneno, s.sellonline, s.url_title, s.extra FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "favorite f ON (f.store = p.store) LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = f.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $popular, $title, $link, $description, $tags, $image, $price, $old_price, $currency, $visible, $views, $start, $expiration, $cashback, $url_title, $paid_until, $extra, $date, $reviews, $stars, $store_id, $store_cat, $store_type, $store_name, $store_img, $store_link, $store_phone, $store_sellonline, $store_url_title, $store_extra );

    $data = array();
    while( $stmt->fetch() ) {

        $data[] = (object) value_with_filter( 'product_info_values', array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'title' => \site\content::title( 'products_name_list', $title, $useem, $useec, $usefl ), 'url' => ( preg_match( '/^http(s)?/i', $link ) ? esc_html( $link ) : esc_html( $store_link ) ), 'original_url' => $link, 'description' => \site\content::content( 'products_list', $description, $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'price' => $price, 'old_price' => ( $old_price > 0 && $old_price > $price ? $old_price : 0 ), 'currency' => esc_html( $currency ), 'visible' => (boolean) $visible, 'views' => $views, 'start_date' => $start, 'is_running' => ( strtotime( $start ) < time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_available_online' => ( ! (boolean) $store_type || $store_sellonline ? true : false ), 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'paid_until' => $paid_until, 'extra' => @unserialize( $extra ), 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_is_physical' => (boolean) $store_type, 'store_img' => esc_html( $store_img ), 'storeID' => $store_id, 'store_catID' => $store_cat, 'store_name' => esc_html( $store_name ), 'store_url' => esc_html( $store_link ), 'store_sellonline' => (boolean) $store_sellonline, 'store_extra' => @unserialize( $store_extra ), 'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_product, $title, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?product=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store_url_title, $store_id, $extension ) : $GLOBALS['siteURL'] . '?store=' . $store_id ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store_url_title, $store_id, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $store_id ) ) );

    }

    $stmt->close();

    return $data;

    }

    return array();

}

}