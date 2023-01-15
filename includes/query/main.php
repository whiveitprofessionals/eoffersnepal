<?php

namespace query;

/** */

class main {

/* GET NUMBER OF STORES */

public static function stores( $categories = array() ) {
    return self::have_stores( $categories, '', array( 'only_count' => true ) );
}

/* GET NUMBER OF CATEGORIES */

public static function categories( $categories = array() ) {
    return self::have_categories( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF COUPONS */

public static function items( $categories = array() ) {
    return self::have_items( $categories, '', array( 'only_count' => true ) );
}

public static function coupons( $categories = array() ) {
    return self::have_items( $categories, '', array( 'only_count' => true ) );
}

/* GET NUMBER OF PRODUCTS */

public static function products( $categories = array() ) {
    return self::have_products( $categories, '', array( 'only_count' => true ) );
}

/* GET NUMBER OF PAGES */

public static function pages( $categories = array() ) {
    return self::have_pages( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF USERS */

public static function users( $categories = array() ) {
    return self::have_users( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF REVIEWS */

public static function reviews( $categories = array() ) {
    return self::have_reviews( $categories, '', array( 'only_count' => true ) );
}

/* GET NUMBER OF REWARDS */

public static function rewards( $categories = array() ) {
    return self::have_rewards( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF CLAIM REWARD REQUESTS */

public static function rewards_reqs( $categories = array() ) {
    return self::have_rewards_reqs( $categories, array( 'only_count' => true ) );
}

/* GET NUMBER OF FAVORITE STORES */

public static function favorites( $categories = array() ) {
    return self::have_favorites( $categories, array( 'only_count' => true ) );
}

/* GET OPTIONS */

public static function get_option( $option = '', $unserialize = false ) {

    global $db;

    $cache = new \cache\main;

    if( $show_from_cache = $cache->check( 'options_' . $option ) ) {

        return ( $unserialize ? @unserialize( $show_from_cache ) : $show_from_cache );

    } else {

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT option_value FROM " . DB_TABLE_PREFIX . "options WHERE option_name = ?");
    $stmt->bind_param( "s", $option );
    $stmt->execute();
    $stmt->bind_result( $value );
    $stmt->fetch();

    $stmt->close();

    $cache->add( 'options_' . $option, $value );

    return ( $unserialize ? @unserialize( $value ) : $value );

    }

}

/* ADD OPTION */

public static function add_option( $option = '', $value = '', $serialize = false ) {

    global $db;

    $value = $serialize ? @serialize( $value ) : $value;

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "options (option_name, option_value) VALUES (?, ?)" );
    $stmt->bind_param( "ss", $option, $value );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        $cache = new \cache\main;
        $cache->add( 'options_' . $option, $value );

        return $value;
    }

    return false;

}

/* SHOW USER PLUGINS */

public static function user_plugins( $scope = '', $view = '' ) {

    global $db;

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $scope ) ) {
        $where[] = 'scope = "' . \site\utils::dbp( $scope ) . '"';
    }

    $show = array_map( 'trim', explode( ',', strtolower( $view ) ) );
    foreach( $show as $v ) {
        switch( $v ) {
            case 'all': break;
            case 'menu': $where[] = 'menu = 1 AND visible > 0'; break;
            case 'loader': $where[] = 'loader != ""'; break;
            default:    $where[] = 'visible > 0'; break;
        }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT id, user, name, image, scope, main, loader, menu, menu_icon, subadmin_view, extend_vars, visible, date FROM " . DB_TABLE_PREFIX . "plugins" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $name, $image, $scope, $main, $loader, $menu, $menu_icon, $subadmin_view, $vars, $visible, $date );

    $data = array();
    while( $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'user' => $user, 'name' => esc_html( $name ), 'image' => esc_html( $image ), 'scope' => esc_html( $scope ), 'main_file' => esc_html( $main ), 'load_file' => esc_html( $loader ), 'menu_icon' => $menu_icon, 'subadmin_view' => (boolean) $subadmin_view, 'vars' => @unserialize( $vars ), 'in_menu' => (boolean) $menu, 'is_active' => ( $visible !== 0 ? true : false ), 'date' => $date );
    }

    $stmt->close();

    return $data;

}

/* SHOW USER AVATAR */

public static function user_avatar( $text ) {

    if( filter_var( $text, FILTER_VALIDATE_URL ) ) {
        return $text;
    } else if( empty( $text ) ) {
        return $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/' . \query\main::get_option( 'default_user_avatar' );
    }
    return $GLOBALS['siteURL'] . $text;

}

/* SHOW STORE AVATAR */

public static function store_avatar( $text ) {

    if( filter_var( $text, FILTER_VALIDATE_URL ) ) {
        return $text;
    } else if( empty( $text ) ) {
        return $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/' . \query\main::get_option( 'default_store_avatar' );
    }
    return $GLOBALS['siteURL'] . $text;

}

/* SHOW PRODUCT AVATAR */

public static function product_avatar( $text ) {

    if( filter_var( $text, FILTER_VALIDATE_URL ) ) {
        return $text;
    } else if( empty( $text ) ) {
        return $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/product_avatar_aa.png';
    }
    return $GLOBALS['siteURL'] . $text;

}

/* SHOW REWARD AVATAR */

public static function reward_avatar( $text ) {

    if( filter_var( $text, FILTER_VALIDATE_URL ) ) {
        return $text;
    } else if( empty( $text ) ) {
        return $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/' . \query\main::get_option( 'default_reward_avatar' );
    }
    return $GLOBALS['siteURL'] . $text;

}

/* SHOW THEME AVATAR */

public static function theme_avatar( $text ) {

    if( empty( $text ) ) {
        return $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/theme_aa.png';
    }
    return $GLOBALS['siteURL'] . $text;

}

/* SHOW PAYMENT PLAN AVATAR */

public static function payment_plan_avatar( $text ) {

    if( empty( $text ) ) {
        return $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/payplan_aa.png';
    }
    return $GLOBALS['siteURL'] . $text;

}

/* CHECK IF CATEGORY EXISTS */

public static function category_exists( $id = 0 ) {

    global $db, $GET;

    $id = empty( $id ) ? $GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "categories WHERE id = ? OR url_title = ?" );
    $stmt->bind_param( "is", $id, $id );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    if( $count > 0 ) {
        return true;
    }

    return false;

}

/* GET INFORMATION ABOUT CATEGORY */

public static function category_info( $id = 0, $special = array() ) {

    global $db, $GET;

    $id = empty( $id ) ? $GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, subcategory, user, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = c.user), name, description, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores WHERE category = c.id), url_title, meta_title, meta_keywords, meta_desc, extra, date FROM " . DB_TABLE_PREFIX . "categories c WHERE id = ? OR url_title = ?" );
    $stmt->bind_param( "is", $id, $id );
    $stmt->execute();
    $stmt->bind_result( $id, $subcategory, $user, $user_name, $name, $description, $stores, $url_title, $meta_title, $meta_keywords, $meta_desc, $extra, $date );
    $stmt->fetch();
    $stmt->close();

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : (boolean) \query\main::get_option( 'smilies_categories' );
    $usesh = ( isset( $special['no_shortcodes'] ) && $special['no_shortcodes'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    return (object) value_with_filter( 'category_info_values', array( 'ID' => $id, 'subcatID' => $subcategory, 'user' => $user, 'user_name' => esc_html( $user_name ), 'name' => \site\content::title( 'category_name_single', $name, $useem, $useec, $usefl ), 'description' => \site\content::content( 'category_single', $description, $useem, $usesh, false, $useec, $usefl ), 'stores' => $stores, 'meta_title' => esc_html( $meta_title ), 'meta_keywords' => esc_html( $meta_keywords ), 'meta_description' => esc_html( $meta_desc ), 'extra' => @unserialize( $extra ), 'date' => $date, 'is_subcat' => ( $subcategory !== 0 ? true : false ), 'url_title' => esc_html( $url_title ), 'link' => ( defined( 'SEO_LINKS' ) && SEO_LINKS ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_category' ), $name, $url_title, $id, \query\main::get_option( 'extension' ) ) : $GLOBALS['siteURL'] . '?cat=' . $id ) ) );

}

/* CHECK IF WIDGET EXISTS */

public static function widget_exists( $id = 0 ) {

    global $db, $GET;

    $id = empty( $id ) ? $GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "widgets WHERE id = ?" );
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

/* GET INFORMATION ABOUT WIDGET */

public static function widget_info( $id = 0 ) {

    global $db, $GET;

    $id = empty( $id ) ? $GET['id'] : $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, widget_id, sidebar, title, stop, type, orderby, position, text, extra, html, mobile_view, date FROM " . DB_TABLE_PREFIX . "widgets WHERE id = ?" );
    $stmt->bind_param( "i", $id );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $widget, $sidebar, $title, $stop, $type, $orderby, $position, $text, $extra, $html, $mobile_view, $date );
    $stmt->fetch();
    $stmt->close();

    return (object)array( 'id' => $id, 'user' => $user, 'widget_id' => $widget, 'zone' => esc_html( $sidebar ), 'title' => esc_html( $title ), 'limit' => $stop, 'type' => $type, 'orderby' => $orderby, 'position' => $position, 'text' => esc_html( $text ), 'extra' => @unserialize( $extra ), 'html' => (boolean) $html, 'mobile_view' => (boolean) $mobile_view, 'date' => $date );

}

/* SHOW WIDGETS */

public static function show_widgets( $id ) {

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, widget_id, location, title, stop, type, orderby, text, extra, html, mobile_view FROM " . DB_TABLE_PREFIX . "widgets WHERE theme = ? AND sidebar = ? ORDER BY position, last_update DESC" );
    $theme = \query\main::get_option( 'theme' );
    $zone = trim( $id );
    $stmt->bind_param( "ss", $theme, $zone );
    $stmt->execute();
    $stmt->bind_result( $id, $widget, $location, $title, $limit, $type, $orderby, $text, $extra, $html, $mobile_view );

    $data = array();
    while( $stmt->fetch() ) {
        if( file_exists( DIR . '/' . $location ) ) {
            $data[] = array( 'ID' => $id, 'widget_id' => $widget, 'title' => esc_html( $title ), 'limit' => $limit, 'type' => $type, 'orderby' => $orderby, 'content' => \site\content::content( 'content', $text, true, true, false, !$html ), 'extra' => @unserialize( $extra ), 'mobile_view' => (boolean) $mobile_view, 'file' => $location );
        }
    }

    $stmt->close();

    return $data;

}

/* CHECK IF USER EXISTS */

public static function user_exists( $user_id = 0 ) {

    global $db, $GET;

    $user_id = empty( $user_id ) ? $GET['id'] : $user_id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "users WHERE id = ? OR name = ?" );
    $stmt->bind_param( "is", $user_id, $user_id );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    if( $count > 0 ) {
        return true;
    }

    return false;

}

/* GET INFORMATION ABOUT USER */

public static function user_info( $user_id = 0, $special = array() ) {

    global $db, $GET;

    $user_id = empty( $user_id ) ? $GET['id'] : $user_id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, name, email, avatar, points, credits, ipaddr, privileges, erole, subscriber, last_login, last_action, visits, valid, ban, refid, extra, date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE user = u.id), (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores WHERE user = u.id), (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons WHERE user = u.id), (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products WHERE user = u.id) FROM " . DB_TABLE_PREFIX . "users u WHERE id = ? OR name = ?" );
    $stmt->bind_param( "is", $user_id, $user_id );
    $stmt->execute();
    $stmt->bind_result( $id, $name, $email, $avatar, $points, $credits, $ip, $privileges, $erole, $subscriber, $last_login, $last_action, $visits, $valid, $ban, $refid, $extra, $date, $reviews, $stores, $coupons, $products );
    $stmt->fetch();
    $stmt->close();

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    return (object) value_with_filter( 'user_info_values', array( 'ID' => $id, 'name' => \site\content::title( 'user_name_single', $name, $useem, $useec, $usefl ), 'email' => esc_html( $email ), 'avatar' => esc_html( $avatar ), 'points' => $points, 'credits' => $credits, 'IP' => esc_html( $ip ), 'privileges' => $privileges, 'erole' => @unserialize( $erole ), 'is_subscribed' => (boolean) $subscriber, 'is_confirmed' => (boolean) $valid, 'is_banned' => (strtotime( $ban ) > time() ? true : false) , 'is_subadmin' => ( $privileges === 1 ? true : false ), 'is_admin' => ( $privileges > 1 ? true : false ), 'last_login' => $last_login, 'last_action' => $last_action, 'visits' => $visits, 'ban' => $ban, 'refid' => $refid, 'extra' => @unserialize( $extra ), 'date' => $date, 'reviews' => $reviews, 'stores' => $stores, 'coupons' => $coupons, 'products' => $products ) );

}

/* CHECK IF REVIEW EXISTS */

public static function review_exists( $review_id = 0 ) {

    global $db, $GET;

    $review_id = empty( $review_id ) ? $GET['id'] : $review_id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE id = ?" );
    $stmt->bind_param( "i", $review_id );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    if( $count > 0 ) {
        return true;
    }

    return false;

}

/* GET INFORMATION ABOUT REVIEW */

public static function review_info( $review_id = 0, $special = array() ) {

    global $db, $GET;

    $review_id = empty( $review_id ) ? $GET['id'] : $review_id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT r.id, r.user, r.store, s.name, s.link, s.url_title, r.text, r.stars, r.valid, r.lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = r.lastupdate_by), r.lastupdate, r.date, u.name, u.avatar FROM " . DB_TABLE_PREFIX . "reviews r LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = r.store) LEFT JOIN " . DB_TABLE_PREFIX . "users u ON (u.id = r.user) WHERE r.id = ?" );
    $stmt->bind_param( "i", $review_id );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $store, $store_name, $store_url, $url_title, $text, $stars, $valid, $lastupdate_by, $lastupdate_by_name, $last_update, $date, $user_name, $user_avatar );
    $stmt->fetch();
    $stmt->close();

    $store_url_tit = !empty( $url_title ) ? $url_title : $store_name;

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : (boolean) \query\main::get_option( 'smilies_reviews' );
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    return (object) array( 'ID' => $id, 'userID' => $user, 'storeID' => $store, 'user_name' => esc_html( $user_name ), 'store_name' => esc_html( $store_name ), 'store_url' => $store_url, 'text' => \site\content::content( 'review_single', $text, $useem, false, $useec, $usefl ), 'stars' => $stars, 'valid' => (boolean) $valid, 'date' => $date, 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => esc_html( $lastupdate_by_name ), 'last_update' => $last_update, 'user_avatar' => esc_html( $user_avatar ), 'store_link' => ( defined( 'SEO_LINKS' ) && SEO_LINKS ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_store' ), $store_url_tit, $store, \query\main::get_option( 'extension' ) ) : $GLOBALS['siteURL'] . '?store=' . $store ) );

}

/* CHECK IF PAGE EXISTS */

public static function page_exists( $page_id = 0, $special = array() ) {

    global $db, $GET;

    $page_id = empty( $page_id ) ? $GET['id'] : $page_id;

    $where = array();

    if( isset( $special['user_view'] ) ) {
        $where[] = 'visible > 0';
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "pages WHERE (id = ? OR url_title = ?)" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->bind_param( "is", $page_id, $page_id );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    if( $count > 0 ) {
        return true;
    }

    return false;

}

/* GET INFORMATION ABOUT PAGE */

public static function page_info( $page_id = 0, $special = array() ) {

    global $db, $GET;

    $page_id = empty( $page_id ) ? $GET['id'] : $page_id;

    $stmt = $db->stmt_init();

    if( isset( $special['update_views'] ) ) {
        $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "pages SET views = views + 1 WHERE id = ? OR name = ?" );
        $stmt->bind_param( "is", $page_id, $page_id );
        $stmt->execute();
    }

    $stmt->prepare( "SELECT id, user, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = p.user), name, text, visible, views, url_title, meta_title, meta_keywords, meta_desc, lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = p.lastupdate_by), lastupdate, extra, date FROM " . DB_TABLE_PREFIX . "pages p WHERE id = ? OR url_title = ?" );
    $stmt->bind_param( "is", $page_id, $page_id );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $user_name, $name, $text, $visible, $views, $url_title, $meta_title, $meta_keywords, $meta_desc, $lastupdate_by, $lastupdate_by_name, $last_update, $extra, $date );
    $stmt->fetch();
    $stmt->close();

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : (boolean) \query\main::get_option( 'smilies_pages' );
    $usesh = ( isset( $special['no_shortcodes'] ) && $special['no_shortcodes'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    return (object) value_with_filter( 'page_info_values', array( 'ID' => $id, 'user' => $user, 'name' => \site\content::title( 'page_name_single', $name, $useem, $useec, $usefl ), 'user_name' => esc_html( $user_name ), 'html' => $text, 'text' => \site\content::content( 'page_single', $text, $useem, $usesh, false, $useec, $usefl ), 'visible' => (boolean) $visible, 'views' => $views, 'meta_title' => esc_html( $meta_title ), 'meta_keywords' => esc_html( $meta_keywords ), 'meta_description' => esc_html( $meta_desc ), 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => esc_html( $lastupdate_by_name ), 'last_update' => $last_update, 'extra' => @unserialize( $extra ), 'date' => $date, 'url_title' => esc_html( $url_title ), 'link' => ( defined( 'SEO_LINKS' ) && SEO_LINKS ? \site\utils::make_seo_link( '', $name, $url_title, $id, \query\main::get_option( 'extension' ) ) : $GLOBALS['siteURL'] . '?p=' . $id ) ) );

}

/* CHECK IF COUPON EXISTS */

public static function item_exists( $item_id = 0, $special = array() ) {

    global $db, $GET;

    $item_id = empty( $item_id ) ? $GET['id'] : $item_id;

    $where = array();

    if( isset( $special['user_view'] ) ) {
        $where[] = 'visible > 0';
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "coupons WHERE (id = ? OR url_title = ?)" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->bind_param( "is", $item_id, $item_id );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    if( !empty( $count ) ) {
        return $count;
    }

    return false;

}

/* GET INFORMATION ABOUT A COUPON */

public static function item_info( $item_id = 0, $special = array() ) {

    global $db, $GET;

    /** make or not seo links */
    $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;

    $item_id = empty( $item_id ) ? $GET['id'] : $item_id;

    $stmt = $db->stmt_init();

    if( isset( $special['update_views'] ) ) {
        $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET views = views + 1 WHERE id = ? OR title = ?" );
        $stmt->bind_param( "is", $item_id, $item_id );
        $stmt->execute();
    }

    $stmt->prepare( "SELECT c.id, c.feedID, c.user, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = c.user), c.store, c.category, c.popular, c.exclusive, c.printable, c.show_in_store, c.available_online, c.title, c.link, c.description, c.tags, c.image, c.code, c.source, c.claim_limit, c.claims, c.visible, c.views, c.clicks, c.start, c.expiration, c.cashback, c.url_title, c.meta_title, c.meta_keywords, c.meta_desc, c.lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = c.lastupdate_by), c.lastupdate, c.votes, c.votes_percent, c.verified, c.last_verif, c.paid_until, c.extra, c.date, s.physical, s.category, s.name, s.link, s.image, s.phoneno, s.sellonline, s.url_title, s.extra, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0), (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store) WHERE c.id = ? OR c.url_title = ?" );
    $stmt->bind_param( "is", $item_id, $item_id );
    $stmt->execute();
    $stmt->bind_result( $id, $feed_id, $user, $user_name, $store, $cat, $popular, $exclusive, $printable, $show_in_store, $avab_online, $title, $link, $description, $tags, $image, $code, $source, $claim_limit, $claims, $visible, $views, $clicks, $start, $expiration, $cashback, $url_title, $meta_title, $meta_keywords, $meta_desc, $lastupdate_by, $lastupdate_by_name, $last_update, $votes, $votes_percent, $verified, $last_verif, $paid_until, $extra, $date, $store_type, $store_cat, $store_name, $store_link, $store_img, $store_phone, $store_sellonline, $store_url_title, $store_extra, $reviews, $stars );
    $stmt->fetch();
    $stmt->close();

    $extension = \query\main::get_option( 'extension' );

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : (boolean) \query\main::get_option( 'smilies_coupons' );
    $usesh = ( isset( $special['no_shortcodes'] ) && $special['no_shortcodes'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    return (object) value_with_filter( 'coupon_info_values', array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'user_name' => esc_html( $user_name ), 'code' => esc_html( $code ), 'title' => \site\content::title( 'item_name_single', $title, $useem, $useec, $usefl ), 'original_url' => $link, 'url' => ( preg_match( '/^http(s)?/i', $link )    ? esc_html( $link ) : esc_html( $store_link ) ), 'description' => \site\content::content( 'item_single', $description, $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'image2' => ( !empty( $image ) ? ( !filter_var( $image, FILTER_VALIDATE_URL ) ? $GLOBALS['siteURL'] . esc_html( $image ) : esc_html( $image ) ) : '' ), 'source' => ( empty( $source ) ? '' : ( filter_var( $source, FILTER_VALIDATE_URL ) ? esc_html( $source ) : $GLOBALS['siteURL'] . esc_html( $source ) ) ), 'visible' => (boolean) $visible, 'views' => $views, 'clicks' => $clicks, 'start_date' => $start, 'is_coupon' => ( !empty( $code ) ? true : false ), 'is_deal' => ( empty( $code ) ? true: false ), 'is_running' => ( strtotime( $start ) < time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_printable' => ( (boolean) $store_type ? (boolean) $printable : false ), 'is_show_in_store' => ( (boolean) $store_type ? (boolean) $show_in_store : false ), 'is_available_online' => ( ! (boolean) $store_type || (boolean) $avab_online ? true : false ), 'is_local_source' => ( empty( $source ) || filter_var( $source, FILTER_VALIDATE_URL ) ? false : true ), 'claim_limit' => $claim_limit, 'claims' => $claims, 'meta_title' => esc_html( $meta_title ), 'meta_keywords' => esc_html( $meta_keywords ), 'meta_description' => esc_html( $meta_desc ), 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => esc_html( $lastupdate_by_name ), 'last_update' => $last_update, 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'is_popular' => (boolean) $popular, 'is_exclusive' => (boolean) $exclusive, 'is_verified' => (boolean) $verified, 'last_check' => $last_verif, 'votes' => $votes, 'votes_percent' => $votes_percent, 'paid_until' => $paid_until, 'extra' => @unserialize( $extra ), 'date' => $date, 'store_is_physical' => (boolean) $store_type, 'store_img' => esc_html( $store_img ), 'storeID' => $store, 'store_catID' => $store_cat, 'store_name' => esc_html( $store_name ), 'store_url' => esc_html( $store_link ), 'store_phone_no' => esc_html( $store_phone ), 'store_sellonline' => (boolean) $store_sellonline, 'reviews' => $reviews, 'stars' => $stars, 'url_title' => esc_html( $url_title ), 'store_extra' => @unserialize( $store_extra ), 'link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_coupon' ), $title, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?id=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_store' ), $store_name, $store_url_title, $store, $extension ) : $GLOBALS['siteURL'] . '?store=' . $store ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_reviews' ), $store_name, $store_url_title, $store, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $store ) ) );

}

/* CHECK IF PRODUCT EXISTS */

public static function product_exists( $product_id = 0, $special = array() ) {

    global $db, $GET;

    $product_id = empty( $product_id ) ? $GET['id'] : $product_id;

    $where = array();

    if( isset( $special['user_view'] ) ) {
        $where[] = 'visible > 0';
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "products WHERE (id = ? OR url_title = ?)" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->bind_param( "is", $product_id, $product_id );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    if( !empty( $count ) ) {
        return $count;
    }

    return false;

}

/* GET INFORMATION ABOUT A PRODUCT */

public static function product_info( $product_id = 0, $special = array() ) {

    global $db, $GET;

    /** make or not seo links */
    $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;

    $product_id = empty( $product_id ) ? $GET['id'] : $product_id;

    $stmt = $db->stmt_init();

    if( isset( $special['update_views'] ) ) {
        $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "products SET views = views + 1 WHERE id = ? OR title = ?" );
        $stmt->bind_param( "is", $product_id, $product_id );
        $stmt->execute();
    }

    $stmt->prepare( "SELECT p.id, p.feedID, p.user, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = p.user), p.store, p.category, p.popular, p.title, p.link, p.description, p.tags, p.image, p.price, p.old_price, p.currency, p.visible, p.views, p.start, p.expiration, p.cashback, p.url_title, p.meta_title, p.meta_keywords, p.meta_desc, p.lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = p.lastupdate_by), p.lastupdate, p.paid_until, p.extra, p.date, s.physical, s.image, s.id, s.name, s.link, s.category, s.sellonline, s.url_title, s.extra, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0), (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store) WHERE p.id = ? OR p.url_title = ?" );
    $stmt->bind_param( "is", $product_id, $product_id );
    $stmt->execute();
    $stmt->bind_result( $id, $feed_id, $user, $user_name, $store, $cat, $popular, $title, $link, $description, $tags, $image, $price, $old_price, $currency, $visible, $views, $start, $expiration, $cashback, $url_title, $meta_title, $meta_keywords, $meta_desc, $lastupdate_by, $lastupdate_by_name, $last_update, $paid_until, $extra, $date, $store_type, $store_img, $store_id, $store_name, $store_link, $store_cat, $store_sellonline, $store_url_title, $store_extra, $reviews, $stars );
    $stmt->fetch();
    $stmt->close();

    $extension = \query\main::get_option( 'extension' );

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : (boolean) \query\main::get_option( 'smilies_products' );
    $usesh = ( isset( $special['no_shortcodes'] ) && $special['no_shortcodes'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    return (object) value_with_filter( 'product_info_values', array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'user_name' => esc_html( $user_name ), 'title' => \site\content::title( 'product_name_single', $title, $useem, $useec, $usefl ), 'original_url' => $link, 'url' => ( $link && preg_match( '/^http(s)?/i', $link ) ? esc_html( $link ) : esc_html( $store_link ) ), 'description' => \site\content::content( 'product_single', $description, $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'price' => $price, 'old_price' => ( $old_price > 0 && $old_price > $price ? $old_price : 0 ), 'currency' => esc_html( $currency ), 'visible' => (boolean) $visible, 'views' => $views, 'start_date' => $start, 'is_running' => ( ( !$start || strtotime( $start ) < time() ) && ( !$expiration || strtotime( $expiration ) > time() ) ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_available_online' => ( ! (boolean) $store_type || $store_sellonline ? true : false ), 'meta_title' => esc_html( $meta_title ), 'meta_description' => esc_html( $meta_desc ), 'meta_keywords' => esc_html( $meta_keywords ), 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => esc_html( $lastupdate_by_name ), 'last_update' => $last_update, 'is_popular' => $popular, 'is_started' => ( !$start || strtotime( $start ) > time() ? false : true ), 'is_expired' => ( !$expiration || strtotime( $expiration ) > time() ? false : true ), 'is_popular' => (boolean) $popular, 'paid_until' => $paid_until, 'extra' => @unserialize( $extra ), 'date' => $date, 'store_is_physical' => (boolean) $store_type, 'store_img' => esc_html( $store_img ), 'storeID' => $store_id, 'store_catID' => $store_cat, 'store_name' => esc_html( $store_name ), 'store_url' => esc_html( $store_link ), 'reviews' => $reviews, 'stars' => $stars, 'store_sellonline' => (boolean) $store_sellonline, 'url_title' => esc_html( $url_title ), 'store_extra' => @unserialize( $store_extra ), 'link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_product' ), $title, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?product=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_store' ), $store_name, $store_url_title, $store_id, $extension ) : $GLOBALS['siteURL'] . '?store=' . $store_id ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_reviews' ), $store_name, $store_url_title, $store_id, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $store_id ) ) );

}

/* CHECK IF STORE EXISTS */

public static function store_exists( $store_id = 0, $special = array() ) {

    global $db, $GET;

    $store_id = empty( $store_id ) ? $GET['id'] : $store_id;

    $where = array();

    if( isset( $special['user_view'] ) ) {
        $where[] = 'visible > 0';
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "stores WHERE (id = ? OR url_title = ?)" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->bind_param( "is", $store_id, $store_id );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    if( !empty( $count ) ) {
        return $count;
    }

    return false;

}

/* GET INFORMATION ABOUT A STORE */

public static function store_info( $store_id = 0, $special = array() ) {

    global $db, $GET;

    /** make or not seo links */
    $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;

    $store_id = empty( $store_id ) ? $GET['id'] : $store_id;

    $stmt = $db->stmt_init();

    if( isset( $special['update_views'] ) ) {
        $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET views = views + 1 WHERE id = ? OR url_title = ?" );
        $stmt->bind_param( "is", $store_id, $store_id );
        $stmt->execute();
    }

    $stmt->prepare( "SELECT s.id, s.feedID, s.user, s.category, s.popular, s.physical, s.name, s.link, s.description, s.tags, s.image, s.hours, s.phoneno, s.sellonline, s.visible, s.views, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons WHERE store = s.id AND visible > 0), s.url_title, s.meta_title, s.meta_keywords, s.meta_desc, s.lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = s.lastupdate_by), s.lastupdate, s.extra, s.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0), (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0), (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products WHERE store = s.id AND visible > 0), u.name FROM " . DB_TABLE_PREFIX . "stores s LEFT JOIN " . DB_TABLE_PREFIX . "users u ON (u.id = s.user) WHERE s.id = ? OR s.url_title = ?" );
    $stmt->bind_param( "is", $store_id, $store_id );
    $stmt->execute();
    $stmt->bind_result( $id, $feed_id, $user, $cat, $popular, $physical, $name, $link, $description, $tags, $image, $hours, $phone, $sellonline, $visible, $views, $coupons, $url_title, $meta_title, $meta_keywords, $meta_desc, $lastupdate_by, $lastupdate_by_name, $last_update, $extra, $date, $reviews, $stars, $products, $user_name);
    $stmt->fetch();
    $stmt->close();

    $extension = \query\main::get_option( 'extension' );

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : (boolean) \query\main::get_option( 'smilies_stores' );
    $usesh = ( isset( $special['no_shortcodes'] ) && $special['no_shortcodes'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    return (object) value_with_filter( 'store_info_values', array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'user_name' => esc_html( $user_name ), 'catID' => $cat, 'name' => \site\content::title( 'store_name_single', $name, $useem, $useec, $usefl ), 'url' => esc_html( $link ), 'description' => \site\content::content( 'store_single', $description,  $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'hours' => @unserialize( $hours ), 'phone_no' => esc_html( $phone ), 'sellonline' => (boolean) ( !$physical ? 1 : $sellonline ), 'sellonline2' => (boolean) $sellonline, 'visible' => (boolean) $visible, 'views' => $views, 'coupons' => $coupons, 'extra' => @unserialize( $extra ), 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'products' => $products, 'meta_title' => esc_html( $meta_title ), 'meta_keywords' => esc_html( $meta_keywords ), 'meta_description' => esc_html( $meta_desc ), 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => esc_html( $lastupdate_by_name ), 'last_update' => $last_update, 'is_popular' => (boolean) $popular, 'is_physical' => (boolean) $physical, 'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_store' ), $name, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?store=' . $id ), 'reviews_link' => ( $seo_link ? \site\utils::make_seo_link( \query\main::get_option( 'seo_link_reviews' ), $name, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $id ) ) );

}

/* CHECK IF A REWARD EXISTS */

public static function reward_exists( $reward_id = 0 ) {

    global $db, $GET;

    $reward_id = empty( $reward_id ) ? $GET['id'] : $reward_id;

    $where = array();

    if( isset( $special['user_view'] ) ) {
        $where[] = 'visible > 0';
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "rewards WHERE id = ?" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->bind_param( "i", $reward_id );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    if( $count > 0 ) {
        return true;
    }

    return false;

}

/* GET INFORMATION ABOUT A REWARD */

public static function reward_info( $reward_id = 0 ) {

    global $db, $GET;

    $reward_id = empty( $reward_id ) ? $GET['id'] : $reward_id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = r.user), points, title, description, image, fields, lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = r.lastupdate_by), lastupdate, visible, date FROM " . DB_TABLE_PREFIX . "rewards r WHERE id = ?" );
    $stmt->bind_param( "i", $reward_id );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $user_name, $points, $title, $description, $image, $fields, $lastupdate_by, $lastupdate_by_name, $last_update, $visible, $date );
    $stmt->fetch();
    $stmt->close();

    return (object) array( 'ID' => $id, 'user' => $user, 'user_name' => esc_html( $user_name ), 'points' => $points, 'title' => esc_html( $title ), 'description' => esc_html( $description ), 'image' => esc_html( $image ), 'fields' => @unserialize( $fields ), 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => esc_html( $lastupdate_by_name ), 'last_update' => $last_update, 'visible' => (boolean) $visible, 'date' => $date );

}

/* CHECK IF A REWARD REQUEST EXISTS */

public static function reward_req_exists( $request_id = 0 ) {

    global $db, $GET;

    $request_id = empty( $request_id ) ? $GET['id'] : $request_id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "rewards_reqs WHERE id = ?" );
    $stmt->bind_param( "i", $request_id );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    if( $count > 0 ) {
        return true;
    }

    return false;

}

/* GET INFORMATION ABOUT A REWARD REQUEST */

public static function reward_req_info( $request_id = 0 ) {

    global $db, $GET;

    $request_id = empty( $request_id ) ? $GET['id'] : $request_id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, name, user, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = r.user), points, reward,    (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "rewards WHERE id = r.reward), fields, lastupdate_by, (SELECT name FROM " . DB_TABLE_PREFIX . "users WHERE id = r.lastupdate_by), lastupdate, claimed, date FROM " . DB_TABLE_PREFIX . "rewards_reqs r WHERE id = ?" );
    $stmt->bind_param( "i", $request_id );
    $stmt->execute();
    $stmt->bind_result( $id, $name, $user, $user_name, $points, $reward, $reward_exists, $fields, $lastupdate_by, $lastupdate_by_name, $last_update, $claimed, $date );
    $stmt->fetch();
    $stmt->close();

    return (object) array( 'ID' => $id, 'name' => esc_html( $name ), 'user' => $user, 'user_name' => esc_html( $user_name ), 'points' => $points, 'reward' => $reward, 'reward_exists' => ( $reward_exists > 0 ? true : false ), 'fields' => @unserialize( $fields ), 'lastupdate_by' => $lastupdate_by, 'lastupdate_by_name' => esc_html( $lastupdate_by_name ), 'last_update' => $last_update, 'claimed' => (boolean) $claimed, 'date' => $date );

}

/* CHECK IF AN USER HAVE A STORE */

public static function have_store( $id, $user ) {

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores WHERE id = ? AND user = ?" );
    $stmt->bind_param( "ii", $id, $user );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();
    $stmt->close();

    if( $count > 0 ) {
        return true;
    }

    return false;

}

/* NUMBER OF CATEGORIES */

public static function have_categories( $category = array(), $special = array() ) {

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

    if( !empty( $categories['parent'] ) ) {
        $where[] = 'subcategory = ' . (int) $categories['parent'];
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(name, description) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        $custom_where_clause = value_with_filter( 'categories_where_clause', array(
            'cats'      => 'subcategory = 0',
            'subcats'   => 'subcategory > 0'
        ) );
        foreach( $show as $v ) {
            if( !empty( $custom_where_clause ) && in_array( $v, array_keys( $custom_where_clause ) ) ) {
                $where[] = $custom_where_clause[$v];
            }
        }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "categories" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

/* FETCH THE CATEGORIES */

public static function while_categories( $category = array(), $special = array() ) {

    global $db;

    /** make or not seo links */
    $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
    list( $seo_link_category, $extension ) = array( \query\main::get_option( 'seo_link_category' ), \query\main::get_option( 'extension' ) );

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

    if( !empty( $categories['parent'] ) ) {
        $where[] = 'subcategory = ' . (int) $categories['parent'];
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(name, description) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        $custom_where_clause = value_with_filter( 'categories_where_clause', array(
            'cats'      => 'subcategory = 0',
            'subcats'   => 'subcategory > 0'
        ) );
        foreach( $show as $v ) {
            if( !empty( $custom_where_clause ) && in_array( $v, array_keys( $custom_where_clause ) ) ) {
                $where[] = $custom_where_clause[$v];
            }
        }
    }

    if( isset( $categories['orderby'] ) ) {
        $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
        $custom_orderby_clause = value_with_filter( 'categories_orderby_clause', array(
            'rand'          => 'RAND()',
            'name'          => 'c.name',
            'name desc'     => 'c.name DESC',
            'date'          => 'c.date',
            'date desc'     => 'c.date DESC'
        ) );

        foreach( $order as $v ) {
            if( !empty( $custom_orderby_clause ) && in_array( $v, array_keys( $custom_orderby_clause ) ) ) {
                $orderby[] = $custom_orderby_clause[$v];
            }
        }
    }

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : (boolean) \query\main::get_option( 'smilies_categories' );
    $usesh = ( isset( $special['no_shortcodes'] ) && $special['no_shortcodes'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, subcategory, user, name, description, url_title, extra, date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores WHERE category = c.id) FROM " . DB_TABLE_PREFIX . "categories c" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $subcategory, $user, $name, $description, $url_title, $extra, $date, $stores );

    $data = array();
    while( $stmt->fetch() ) {

        $data[] = (object) value_with_filter( 'category_info_values', array( 'ID' => $id, 'subcatID' => $subcategory, 'user' => $user, 'name' => \site\content::title( 'categories_name_list', $name, $useem, $useec, $usefl ), 'description' => \site\content::content( 'categories_list', $description, $useem, $usesh, false, $useec, $usefl ), 'extra' => @unserialize( $extra ), 'date' => $date, 'stores' => $stores, 'is_subcat' => ( $subcategory !== 0 ? true : false ), 'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_category, $name, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?cat=' . $id ) ) );

    }

    $stmt->close();

    return $data;

}

/* GET THE CATEGORIES */

public static function group_categories( $category = array(), $special = array() ) {

    $array = array();

    foreach( \query\main::while_categories( $category, $special ) as $c ) {
        if( $c->is_subcat ) {
            $array['cat_' . $c->subcatID]['subcats'][] = $c;
        } else {
            $array['cat_' . $c->ID]['info'] = $c;
        }
    }

    return $array;

}

/* NUMBER OF USERS */

public static function have_users( $category = array(), $special = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(name, email, ipaddr) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['ip'] ) ) {
        $where[] = 'ipaddr = "' . \site\utils::dbp( $categories['ip'] ) . '"';
    }

    if( !empty( $categories['referrer'] ) ) {
        $where[] = 'refid = "' . (int) $categories['referrer'] . '"';
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        $custom_where_clause = value_with_filter( 'users_where_clause', array() );
        foreach( $show as $v ) {
            switch( $v ) {
                case 'members': $where[] = 'privileges = 0'; break;
                case 'subadmins':    $where[] = 'privileges = 1'; break;
                case 'admins':    $where[] = 'privileges >= 2'; break;
                case 'verified': $where[] = 'valid >= 1'; break;
                case 'notverified': $where[] = 'valid = 0'; break;
                case 'banned': $where[] = 'ban >= NOW()';
                case 'referred': $where[] = 'refid > 0'; break;
                default: 
                if( !empty( $custom_where_clause ) && in_array( $v, array_keys( $custom_where_clause ) ) ) {
                    $where[] = $custom_where_clause[$v];
                }
            }
        }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "users" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

/* FETCH THE USERS */

public static function while_users( $category = array(), $special = array() ) {

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
        $where[] = 'CONCAT(name, email, ipaddr) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['ip'] ) ) {
        $where[] = 'ipaddr = "' . \site\utils::dbp( $categories['ip'] ) . '"';
    }

    if( !empty( $categories['referrer'] ) ) {
        $where[] = 'refid = "' . (int) $categories['referrer'] . '"';
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        $custom_where_clause = value_with_filter( 'users_where_clause', array() );
        foreach( $show as $v ) {
            switch( $v ) {
                case 'members': $where[] = 'privileges = 0'; break;
                case 'subadmins': $where[] = 'privileges = 1'; break;
                case 'admins': $where[] = 'privileges >= 2'; break;
                case 'verified': $where[] = 'valid >= 1'; break;
                case 'notverified': $where[] = 'valid = 0'; break;
                case 'banned': $where[] = 'ban >= NOW()';
                case 'referred': $where[] = 'refid > 0'; break;
                default: 
                if( !empty( $custom_where_clause ) && in_array( $v, array_keys( $custom_where_clause ) ) ) {
                    $where[] = $custom_where_clause[$v];
                }
            }
        }
    }

    if( isset( $categories['orderby'] ) ) {
        $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
        $custom_orderby_clause = value_with_filter( 'users_orderby_clause', array() );
        foreach( $order as $v ) {
            switch( $v ) {
                case 'rand': $orderby[] = 'RAND()'; break;
                case 'name': $orderby[] = 'name'; break;
                case 'name desc': $orderby[] = 'name DESC'; break;
                case 'date': $orderby[] = 'date'; break;
                case 'date desc': $orderby[] = 'date DESC'; break;
                case 'action': $orderby[] = 'last_action'; break;
                case 'action desc': $orderby[] = 'last_action DESC'; break;
                case 'points': $orderby[] = 'points'; break;
                case 'points desc': $orderby[] = 'points DESC'; break;
                case 'credits': $orderby[] = 'credits'; break;
                case 'credits desc': $orderby[] = 'credits DESC'; break;
                case 'visits': $orderby[] = 'visits'; break;
                case 'visits desc': $orderby[] = 'visits DESC'; break;
                default: 
                if( !empty( $custom_orderby_clause ) && in_array( $v, array_keys( $custom_orderby_clause ) ) ) {
                    $orderby[] = $custom_orderby_clause[$v];
                }
            }
        }
    }

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, name, email, avatar, points, credits, ipaddr, privileges, subscriber, last_login, last_action, visits, valid, ban, refid, extra, date FROM " . DB_TABLE_PREFIX . "users" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $name, $email, $avatar, $points, $credits, $ip, $privileges, $subscriber, $last_login, $last_action, $visits, $valid, $ban, $refid, $extra, $date );

    $data = array();
    while( $stmt->fetch() ) {

        $data[] = (object) value_with_filter( 'user_info_values', array( 'ID' => $id, 'name' => \site\content::title( 'users_name_list', $name, $useem, $useec, $usefl ), 'email' => esc_html( $email ), 'avatar' => esc_html( $avatar ), 'points' => $points, 'credits' => $credits, 'IP' => esc_html( $ip ), 'privileges' => $privileges, 'is_subscribed' => (boolean) $subscriber, 'is_confirmed' => (boolean) $valid, 'is_banned' => (strtotime( $ban ) > time() ? true : false) , 'is_subadmin' => ( $privileges === 1 ? true : false ), 'is_admin' => ( $privileges > 1 ? true : false ), 'last_login' => $last_login, 'last_action' => $last_action, 'visits' => $visits, 'ban' => $ban, 'refid' => $refid, 'extra' => @unserialize( $extra ), 'date' => $date ) );

    }

    $stmt->close();

    return $data;

}

/* NUMBER OF PAGES */

public static function have_pages( $category = array(), $special = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(name, text) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        $custom_where_clause = value_with_filter( 'pages_where_clause', array(
            'visible'   => 'visible > 0'
        ) );
        foreach( $show as $v ) {
            if( !empty( $custom_where_clause ) && in_array( $v, array_keys( $custom_where_clause ) ) ) {
                $where[] = $custom_where_clause[$v];
            }
        }
    } else {
        $where[] = 'visible > 0';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "pages" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

/* FETCH THE PAGES */

public static function while_pages( $category = array(), $special = array() ) {

    global $db;

    /** make or not seo links */
    $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
    list( $seo_link_page, $extension ) = array( \query\main::get_option( 'seo_link_page' ), \query\main::get_option( 'extension' ) );

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
        $where[] = 'CONCAT(name, text) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( !empty( $categories['update'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['update'] ) );
        $where[] = 'lastupdate >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'lastupdate <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        $custom_where_clause = value_with_filter( 'pages_where_clause', array(
            'visible'   => 'visible > 0'
        ) );
        foreach( $show as $v ) {
            if( !empty( $custom_where_clause ) && in_array( $v, array_keys( $custom_where_clause ) ) ) {
                $where[] = $custom_where_clause[$v];
            }
        }
    } else {
        $where[] = 'visible > 0';
    }

    if( isset( $categories['orderby'] ) ) {
        $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
        $custom_orderby_clause = value_with_filter( 'pages_orderby_clause', array(
            'rand'          => 'RAND()',
            'name'          => 'name',
            'name desc'     => 'name DESC',
            'update'        => 'lastupdate',
            'update desc'   => 'lastupdate DESC',
            'date'          => 'date',
            'date desc'     => 'date DESC',
            'views'         => 'views',
            'views desc'    => 'views DESC'
        ) );

        foreach( $order as $v ) {
            if( !empty( $custom_orderby_clause ) && in_array( $v, array_keys( $custom_orderby_clause ) ) ) {
                $orderby[] = $custom_orderby_clause[$v];
            }
        }
    }

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : (boolean) \query\main::get_option( 'smilies_pages' );
    $usesh = ( isset( $special['no_shortcodes'] ) && $special['no_shortcodes'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, name, text, visible, url_title, extra, date FROM " . DB_TABLE_PREFIX . "pages" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $name, $text, $visible, $url_title, $extra, $date );

    $data = array();
    while( $stmt->fetch() ) {

        $data[] = (object) value_with_filter( 'page_info_values', array( 'ID' => $id, 'user' => $user, 'name' => \site\content::title( 'pages_name_list', $name, $useem, $useec, $usefl ), 'html' => $text, 'text' => \site\content::content( 'pages_list', $text, $useem, $usesh, false, $useec, $usefl ), 'visible' => (boolean) $visible, 'extra' => @unserialize( $extra ), 'date' => $date, 'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_page, $name, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?p=' . $id ) ) );

    }

    $stmt->close();

    return $data;

}

/* NUMBER OF ITEMS - COUPONS */

public static function have_items( array $category = [], string $place = '', array $special = [], array $options = [] ) {

    global $db, $GET;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['categories'] ) && strcasecmp( $categories['categories'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['categories'] ) ));
        if( !empty( $arr ) )
        $where[] = 'c.category IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['update'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['update'] ) );
        $where[] = 'c.lastupdate >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'c.lastupdate <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'c.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'c.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        $custom_where_clause = value_with_filter( 'items_where_clause', array(
            'expired'   => '(c.expiration IS NOT NULL AND c.expiration <= NOW())',
            'active'    => '(c.expiration IS NULL OR c.expiration > NOW())',
            'popular'   => 'c.popular > 0',
            'sponsored' => 'c.paid_until > NOW()',
            'exclusive' => 'c.exclusive > 0',
            'codes'     => "c.code != ''",
            'deals'     => "c.code = ''",
            'printable' => 'c.printable = 1',
            'cashback'  => 'c.cashback > 0',
            'verified'  => 'c.verified > 0',
            'feed'      => 'c.feedID > 0',
            'visible'   => 'c.visible > 0 AND s.visible > 0',
            'notvisible'=> 'c.visible = 0',
            'show_in_store' => 'c.show_in_store > 0'
        ) );
        foreach( $show as $v ) {
            if( !empty( $custom_where_clause ) && in_array( $v, array_keys( $custom_where_clause ) ) ) {
                $where[] = $custom_where_clause[$v];
            }
        }
    } else {
        $where[] = 'c.visible > 0 AND s.visible > 0';
    }

    /* */

    switch( $place ) {

    case 'category':

    if( !empty( $categories['active'] ) ) {
        $where[] = 'c.expiration > NOW()';
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE (? > 0 AND (id = ? OR subcategory = ?)) OR (url_title IS NOT NULL AND url_title = ?)" );
    $stmt->bind_param( "iiis", $GET['id'], $GET['id'], $GET['id'], $GET['id'] );
    $stmt->execute();
    $stmt->bind_result( $id );

    $ids = array();
    while( $stmt->fetch() ) {
        $ids[] = $id;
    }

    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (c.store = s.id) WHERE c.category IN(" . implode( ',', $ids ) . ") AND c.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();

    break;

    case 'search':

    if( !empty( $categories['active'] ) )
    $where[] = 'c.expiration > NOW()';

    $stmt = $db->stmt_init();

    $ids = array();

    if( !empty( $options['category'] ) ) {
        $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE subcategory = ?" );
        $stmt->bind_param( "i", $options['category'] );
        $stmt->execute();
        $stmt->bind_result( $id );

        $ids[] = (int) $options['category'];
        while( $stmt->fetch() ) {
            $ids[] = $id;
        }

        $where[] = 'c.category IN(' . implode( ',', $ids ) . ')';
    }

    $query  = "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store)  WHERE (MATCH(c.title) AGAINST (? IN BOOLEAN MODE) OR MATCH(c.tags) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.name) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.tags) AGAINST (? IN BOOLEAN MODE)) AND c.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
    $bind_t = 'ssss';

    if( gettype( $GET['id'] ) === 'string' ) {
        $search = implode( '+', explode( ' ', trim( $GET['id'] ) ) );
        $search = substr( $search, 0, 50 );
    } else {
        $search = '';
    }

    $params = [ $search, $search, $search, $search ];
    $dist   = false;

    if( !empty( $options['loc'] ) ) {
        if( preg_match( '/([\d\.\-]+)\s([\d\.\-]+)\s?.*/', urldecode( $options['loc'] ), $fcoords ) ) {
            $coords = [ 'lat' => $fcoords[1], 'lng' => $fcoords[2] ];
        } else if( !( $coords = \site\utils::get_coords_from_str( $options['loc'] ) ) ) {
            setQueryLastLog( t( 'cant_get_coords', "We couldn't find this location, please try a new one." ) );
        } else {
            $_GET['loc'] = implode( ' ', $coords ); 
        }

        if( !empty( $coords ) ) {
            $query  = "SELECT COUNT(*), (6378 * acos(cos(radians(?)) * cos(radians(lat)) * cos( radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store) RIGHT JOIN  " . DB_TABLE_PREFIX . "store_locations sl ON (sl.store = c.store) WHERE MBRContains(ST_Buffer(LineString(Point(? + ? / (111.111 / COS(RADIANS(?))), ? + ? / 111.111), Point(? - ? / ( 111.1 / COS(RADIANS(?))), ? - ? / 111.111)), 1), sl.point)";
            $distance = $options['distance'] ?? 10;
            $distance = (int) $distance;
            if( $distance < 1 || $distance > 10 )
            $distance = 10;

            if( !empty( $search ) ) {
                $query .= " AND (MATCH(c.title) AGAINST (? IN BOOLEAN MODE) OR MATCH(c.tags) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.name) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.tags) AGAINST (? IN BOOLEAN MODE))";
                $query .= " AND c.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
                $query .= ' GROUP BY distance HAVING distance <= ?';
                $bind_t = 'dddddddddddddssssd';
                $params = [ $coords['lat'], $coords['lng'], $coords['lat'], $distance, $distance, $coords['lng'], $coords['lng'], $distance, $coords['lat'], $distance, $coords['lng'], $coords['lng'], $distance, $search, $search, $search, $search, $distance ];
                $dist   = true;
            } else {
                $query .= " AND c.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
                $query .= ' GROUP BY distance HAVING distance <= ?';
                $bind_t = 'dddddddddddddd';
                $params = [ $coords['lat'],  $coords['lng'], $coords['lat'], $distance, $distance, $coords['lng'], $coords['lng'], $distance, $coords['lat'], $distance, $coords['lng'], $coords['lng'], $distance, $distance ];
                $dist   = true;
            }

        }
    }

    $stmt->prepare( $query );
    $stmt->bind_param( $bind_t, ...$params );
    $stmt->execute();
    echo $stmt->error;
    if( $dist )
    $stmt->bind_result( $count, $distance );
    else 
    $stmt->bind_result( $count );
    $stmt->fetch();

    break;

    default:

    /* WHERE / ORDER BY */

    if( !empty( $categories['ids'] ) && strcasecmp( $categories['ids'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ) {
            return (int) $w;
        }, explode( ',', $categories['ids'] ) ));
        if( !empty( $arr ) )
        $where[] = 'c.id IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['store'] ) && strcasecmp( $categories['store'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ) {
            return (int) $w;
        }, explode( ',', $categories['store'] ) ));
        if( !empty( $arr ) )
        $where[] = 'c.store IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['user'] ) ) {
        $where[] = 'c.user = "' . (int) $categories['user'] . '"';
    }

    if( !empty( $categories['store_owner'] ) ) {
        $where[] = 's.user = "' . (int) $categories['store_owner'] . '"';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(c.title, c.tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();

    break;

    }

    $stmt->close();

    if( isset( $categories['limit'] ) && $categories['limit'] < $count ) {
        $count = $categories['limit'];
    }

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

/* FETCH THE ITEMS - COUPONS */

public static function while_items( array $category = [], string $place = '', array $special = [], array $options = [] ) {
    global $db, $GET;

    /** make or not seo links */
    $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
    list( $seo_link_coupon, $seo_link_store, $seo_link_reviews, $extension ) = array( \query\main::get_option( 'seo_link_coupon' ), \query\main::get_option( 'seo_link_store' ), \query\main::get_option( 'seo_link_reviews' ), \query\main::get_option( 'extension' ) );

    $categories = \site\utils::validate_user_data( $category );

    $where = $orderby = $limit = array();

    if( isset( $categories['max'] ) ) {
        if( !empty( $categories['max'] ) ) {
            $limit[] = $categories['max'];
        }
    } else {
        $page = ( !empty( $categories['page'] ) ? (int) $categories['page'] : ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 ) );
        $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
        $offset = ( isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0 );

        $limit[] = $offset;
        $limit[] = isset( $categories['limit'] ) && ( $offset + $per_page ) > $categories['limit'] ? ( ( $limit2 = ( $categories['limit'] - $per_page ) ) > 0 ? $limit2 : $categories['limit'] ) : $per_page;
    }

    /* WHERE / ORDER BY */

    if( !empty( $categories['categories'] ) && strcasecmp( $categories['categories'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['categories'] ) ));
        if( !empty( $arr ) )
        $where[] = 'c.category IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['update'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['update'] ) );
        $where[] = 'c.lastupdate >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'c.lastupdate <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'c.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'c.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        $custom_where_clause = value_with_filter( 'items_where_clause', array(
            'expired'   => '(c.expiration IS NOT NULL AND c.expiration <= NOW())',
            'active'    => '(c.expiration IS NULL OR c.expiration > NOW())',
            'popular'   => 'c.popular > 0',
            'sponsored' => 'c.paid_until > NOW()',
            'exclusive' => 'c.exclusive > 0',
            'codes'     => "c.code != ''",
            'deals'     => "c.code = ''",
            'printable' => 'c.printable = 1',
            'cashback'  => 'c.cashback > 0',
            'verified'  => 'c.verified > 0',
            'feed'      => 'c.feedID > 0',
            'visible'   => 'c.visible > 0 AND s.visible > 0',
            'notvisible'=> 'c.visible = 0',
            'show_in_store' => 'c.show_in_store > 0'
        ) );
        foreach( $show as $v ) {
            if( !empty( $custom_where_clause ) && in_array( $v, array_keys( $custom_where_clause ) ) ) {
                $where[] = $custom_where_clause[$v];
            }
        }
    } else {
        $where[] = 'c.visible > 0 AND s.visible > 0';
    }

    if( isset( $categories['orderby'] ) ) {
        $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
        $custom_orderby_clause = value_with_filter( 'items_orderby_clause', array(
            'rand'          => 'RAND()',
            'name'          => 'c.title',
            'name desc'     => 'c.title DESC',
            'update'        => 'c.lastupdate',
            'update desc'   => 'c.lastupdate DESC',
            'rating'        => 'rating',
            'rating desc'   => 'rating DESC',
            'votes'         => 'votes',
            'votes desc'    => 'votes DESC',
            'cvotes'        => 'c.votes',
            'cvotes desc'   => 'c.votes DESC',
            'views'         => 'c.views',
            'views desc'    => 'c.views DESC',
            'clicks'        => 'c.clicks',
            'clicks desc'   => 'c.clicks DESC',
            'popular'       => 'c.popular DESC',
            'sponsored'     => 'sponsored DESC',
            'start'         => 'c.start',
            'start desc'    => 'c.start DESC',
            'expiration'    => 'c.expiration',
            'expiration desc'=> 'c.expiration DESC',
            'date'          => 'c.date',
            'date desc'     => 'c.date DESC',
            'active'        => 'c.expiration',
            'active desc'   => 'c.expiration DESC',
            'distance'      => 'distance',
            'distance desc' => 'distance DESC'
        ) );

        foreach( $order as $v ) {
            if( !empty( $custom_orderby_clause ) && in_array( $v, array_keys( $custom_orderby_clause ) ) ) {
                $orderby[] = $custom_orderby_clause[$v];
            }
        }
    }

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : (boolean) \query\main::get_option( 'smilies_coupons' );
    $usesh = ( isset( $special['no_shortcodes'] ) && $special['no_shortcodes'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    /* */

    switch( $place ) {

    case 'category':

    if( !empty( $categories['active'] ) ) {
        $where[] = 'c.expiration > NOW()';
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE (? > 0 AND (id = ? OR subcategory = ?)) OR (url_title IS NOT NULL AND url_title = ?)" );
    $stmt->bind_param( "iiis", $GET['id'], $GET['id'], $GET['id'], $GET['id'] );
    $stmt->execute();
    $stmt->bind_result( $id );

    $ids = array();
    while( $stmt->fetch() ) {
        $ids[] = $id;
    }

    $stmt->prepare( "SELECT c.id, c.feedID, c.user, c.store, c.category, c.popular, c.exclusive, c.printable, c.show_in_store, c.available_online, c.title, c.link, c.description, c.tags, c.image, c.code, c.source, c.claim_limit, c.claims, c.visible, c.views, c.clicks, c.start, c.expiration, c.cashback, c.url_title, c.votes, c.votes_percent, c.verified, c.last_verif, c.lastupdate, c.paid_until, c.extra, c.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.physical, s.category, s.name, s.link, s.image, s.phoneno, s.sellonline, s.url_title, s.extra, IF(c.paid_until > NOW(), 1, 0) as sponsored FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store) WHERE c.category IN(" . implode( ',', $ids ) . ") AND c.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $popular, $exclusive, $printable, $show_in_store, $avab_online, $title, $link, $description, $tags, $image, $code, $source, $claim_limit, $claims, $visible, $views, $clicks, $start, $expiration, $cashback, $url_title, $votes, $votes_percent, $verified, $last_verif, $last_update, $paid_until, $extra, $date, $reviews, $stars, $store_type, $store_cat, $store_name, $store_link, $store_img, $store_phone, $store_sellonline, $store_url_title, $store_extra );

    $data = array();
    while( $stmt->fetch() ) {

        $data[] = (object) value_with_filter( 'coupon_info_values', array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'code' => esc_html( $code ), 'title' => \site\content::title( 'items_name_list', $title, $useem, $useec, $usefl ), 'url' => ( preg_match( '/^http(s)?/i', $link ) ? esc_html( $link ) : esc_html( $store_link ) ), 'original_url' => $link, 'description' => \site\content::content( 'items_list', $description, $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'image2' => ( !empty( $image ) ? ( !filter_var( $image, FILTER_VALIDATE_URL ) ? $GLOBALS['siteURL'] . esc_html( $image ) : esc_html( $image ) ) : '' ), 'source' => ( empty( $source ) ? '' : ( filter_var( $source, FILTER_VALIDATE_URL ) ? esc_html( $source ) : $GLOBALS['siteURL'] . esc_html( $source ) ) ), 'visible' => (boolean) $visible, 'views' => $views, 'clicks' => $clicks, 'start_date' => $start, 'is_coupon' => ( !empty( $code ) ? true : false ), 'is_deal' => ( empty( $code ) ? true : false ), 'is_running' => ( strtotime( $start ) < time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_printable' => ( (boolean) $store_type ? (boolean) $printable : false ), 'is_show_in_store' => ( (boolean) $store_type ? (boolean) $show_in_store : false ), 'is_available_online' => ( ! (boolean) $store_type || (boolean) $avab_online ? true : false ), 'is_local_source' => ( empty( $source ) || filter_var( $source, FILTER_VALIDATE_URL ) ? false : true ), 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'is_popular' => (boolean) $popular, 'is_exclusive' => (boolean) $exclusive, 'is_verified' => (boolean) $verified, 'last_check' => $last_verif, 'votes' => $votes, 'votes_percent' => $votes_percent, 'claim_limit' => $claim_limit, 'claims' => $claims, 'paid_until' => $paid_until, 'extra' => @unserialize( $extra ), 'last_update' => $last_update, 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_is_physical' => (boolean) $store_type, 'store_img' => esc_html( $store_img ), 'store_catID' => $store_cat, 'store_name' => esc_html( $store_name ), 'store_url' => esc_html( $store_link ), 'store_phone_no' => esc_html( $store_phone ), 'store_sellonline' => (boolean) $store_sellonline, 'store_extra' => @unserialize( $store_extra ), 'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_coupon, $title, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?id=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store_url_title, $store, $extension ) : $GLOBALS['siteURL'] . '?store=' . $store ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store_url_title, $store, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $store ) ) );

    }

    $stmt->close();

    return $data;

    break;

    case 'search':

    if( !empty( $categories['active'] ) )
    $where[] = 'c.expiration > NOW()';

    $stmt = $db->stmt_init();

    $ids = array();

    if( !empty( $options['category'] ) ) {
        $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE subcategory = ?" );
        $stmt->bind_param( "i", $options['category'] );
        $stmt->execute();
        $stmt->bind_result( $id );

        $ids[] = (int) $options['category'];
        while( $stmt->fetch() ) {
            $ids[] = $id;
        }

        $where[] = 'c.category IN(' . implode( ',', $ids ) . ')';
    }
    
    $query  = "SELECT c.id, c.feedID, c.user, c.store, c.category, c.popular, c.exclusive, c.printable, c.show_in_store, c.available_online, c.title, c.link, c.description, c.tags, c.image, c.code, c.source, c.claim_limit, c.claims, c.visible, c.views, c.clicks, c.start, c.expiration, c.cashback, c.url_title, c.votes, c.votes_percent, c.verified, c.last_verif, c.lastupdate, c.paid_until, c.extra, c.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.physical, s.category, s.name, s.link, s.image, s.phoneno, s.sellonline, s.url_title, s.extra, IF(c.paid_until > NOW(), 1, 0) as sponsored FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store) WHERE (MATCH(c.title) AGAINST (? IN BOOLEAN MODE) OR MATCH(c.tags) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.name) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.tags) AGAINST (? IN BOOLEAN MODE)) AND c.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
    $query .= ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) );
    $bind_t = 'ssss';

    if( isset( $GET['id'] ) && gettype( $GET['id'] ) === 'string' ) {
        $search = implode( '+', explode( ' ', trim( $GET['id'] ) ) );
        $search = substr( $search, 0, 50 );
    } else {
        $search = '';
    }

    $params = [ $search, $search, $search, $search ];
    $dist   = false;

    if( !empty( $options['loc'] ) ) {
        if( preg_match( '/([\d\.\-]+)\s([\d\.\-]+)\s?.*/', urldecode( $options['loc'] ), $fcoords ) ) {
            $coords = [ 'lat' => $fcoords[1], 'lng' => $fcoords[2] ];
        } else if( !( $coords = \site\utils::get_coords_from_str( $options['loc'] ) ) ) {
            setQueryLastLog( t( 'cant_get_coords', "We couldn't find this location, please try a new one." ) );
        } else {
            $_GET['loc'] = implode( ' ', $coords ); 
        }

        if( !empty( $coords ) ) {
            $query  = "SELECT c.id, c.feedID, c.user, c.store, c.category, c.popular, c.exclusive, c.printable, c.show_in_store, c.available_online, c.title, c.link, c.description, c.tags, c.image, c.code, c.source, c.claim_limit, c.claims, c.visible, c.views, c.clicks, c.start, c.expiration, c.cashback, c.url_title, c.votes, c.votes_percent, c.verified, c.last_verif, c.lastupdate, c.paid_until, c.extra, c.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.physical, s.category, s.name, s.link, s.image, s.phoneno, s.sellonline, s.url_title, s.extra, IF(c.paid_until > NOW(), 1, 0) as sponsored, (6378 * acos(cos(radians(?)) * cos(radians(lat)) * cos( radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store) RIGHT JOIN  " . DB_TABLE_PREFIX . "store_locations sl ON (sl.store = c.store) WHERE MBRContains(ST_Buffer(LineString(Point(? + ? / (111.111 / COS(RADIANS(?))), ? + ? / 111.111), Point(? - ? / ( 111.1 / COS(RADIANS(?))), ? - ? / 111.111)), 1), sl.point) ";
            
            $distance = $options['distance'] ?? 10;
            $distance = (int) $distance;
            if( $distance < 1 || $distance > 10 )
            $distance = 10;

            if( !empty( $search ) ) {
                $query .= " AND (MATCH(c.title) AGAINST (? IN BOOLEAN MODE) OR MATCH(c.tags) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.name) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.tags) AGAINST (? IN BOOLEAN MODE))";
                $query .= " AND c.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
                $query .= ' HAVING distance <= ?';
                $query .= ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) );
                $bind_t = 'dddddddddddddssssd';
                $params = [ $coords['lat'], $coords['lng'], $coords['lat'], $distance, $distance, $coords['lng'], $coords['lng'], $distance, $coords['lat'], $distance, $coords['lng'], $coords['lng'], $distance,  $search, $search, $search, $search, $distance ];
                $dist   = true;
            } else {
                $query .= " AND c.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
                $query .= ' HAVING distance <= ?';
                $query .= ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) );
                $bind_t = 'dddddddddddddd';
                $params = [ $coords['lat'],  $coords['lng'], $coords['lat'], $distance, $distance, $coords['lng'], $coords['lng'], $distance, $coords['lat'], $distance, $coords['lng'], $coords['lng'], $distance, $distance ];
                $dist   = true;
            }

        }
    }

    $stmt->prepare( $query );
    $stmt->bind_param( $bind_t, ...$params );
    $stmt->execute();
    if( $dist )
    $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $popular, $exclusive, $printable, $show_in_store, $avab_online, $title, $link, $description, $tags, $image, $code, $source, $claim_limit, $claims, $visible, $views, $clicks, $start, $expiration, $cashback, $url_title, $votes, $votes_percent, $verified, $last_verif, $last_update, $paid_until, $extra, $date, $reviews, $stars, $store_type, $store_cat, $store_name, $store_link, $store_img, $store_phone, $store_sellonline, $store_url_title, $store_extra, $distance, $sponsored );
    else 
    $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $popular, $exclusive, $printable, $show_in_store, $avab_online, $title, $link, $description, $tags, $image, $code, $source, $claim_limit, $claims, $visible, $views, $clicks, $start, $expiration, $cashback, $url_title, $votes, $votes_percent, $verified, $last_verif, $last_update, $paid_until, $extra, $date, $reviews, $stars, $store_type, $store_cat, $store_name, $store_link, $store_img, $store_phone, $store_sellonline, $store_url_title, $store_extra, $sponsored );
    $data = array();
    while( $stmt->fetch() ) {

        $vals   = array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'code' => esc_html( $code ), 'title' => \site\content::title( 'items_name_list', $title, $useem, $useec, $usefl ), 'url' => ( preg_match( '/^http(s)?/i', $link ) ? esc_html( $link ) : esc_html( $store_link ) ), 'original_url' => $link, 'description' => \site\content::content( 'items_list', $description, $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'image2' => ( !empty( $image ) ? ( !filter_var( $image, FILTER_VALIDATE_URL ) ? $GLOBALS['siteURL'] . esc_html( $image ) : esc_html( $image ) ) : '' ), 'source' => ( empty( $source ) ? '' : ( filter_var( $source, FILTER_VALIDATE_URL ) ? esc_html( $source ) : $GLOBALS['siteURL'] . esc_html( $source ) ) ), 'visible' => (boolean) $visible, 'views' => $views, 'clicks' => $clicks, 'start_date' => $start, 'is_coupon' => ( !empty( $code ) ? true : false ), 'is_deal' => ( empty( $code ) ? true : false ), 'is_running' => ( strtotime( $start ) < time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_printable' => ( (boolean) $store_type ? (boolean) $printable : false ), 'is_show_in_store' => ( (boolean) $store_type ? (boolean) $show_in_store : false ), 'is_available_online' => ( ! (boolean) $store_type || (boolean) $avab_online ? true : false ), 'is_local_source' => ( empty( $source ) || filter_var( $source, FILTER_VALIDATE_URL ) ? false : true ), 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'is_popular' => (boolean) $popular, 'is_exclusive' => (boolean) $exclusive, 'is_verified' => (boolean) $verified, 'last_check' => $last_verif, 'votes' => $votes, 'votes_percent' => $votes_percent, 'claim_limit' => $claim_limit, 'claims' => $claims, 'paid_until' => $paid_until, 'extra' => @unserialize( $extra ), 'last_update' => $last_update, 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_is_physical' => (boolean) $store_type, 'store_img' => esc_html( $store_img ), 'store_catID' => $store_cat, 'store_name' => esc_html( $store_name ), 'store_url' => esc_html( $store_link ), 'store_phone_no' => esc_html( $store_phone ), 'store_sellonline' => (boolean) $store_sellonline, 'store_extra' => @unserialize( $store_extra ),  'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_coupon, $title, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?id=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store_url_title, $store, $extension ) : $GLOBALS['siteURL'] . '?store=' . $store ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store_url_title, $store, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $store ) );
        if( $dist )
        $vals['distance'] = $distance;
        $data[] = (object) value_with_filter( 'coupon_info_values', $vals );

    }

    $stmt->close();

    return $data;

    break;

    default:

    /* WHERE / ORDER BY */

    if( !empty( $categories['ids'] ) && strcasecmp( $categories['ids'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['ids'] ) ));
        if( !empty( $arr ) )
        $where[] = 'c.id IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
        if( !isset( $categories['orderby'] ) ) {
            $orderby[] = 'field(c.id,' . \site\utils::dbp( implode(',', $arr) ) . ')';
        }
    }

    if( !empty( $categories['store'] ) && strcasecmp( $categories['store'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['store'] ) ));
        if( !empty( $arr ) )
        $where[] = 'c.store IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['user'] ) ) {
        $where[] = 'c.user = "' . (int) $categories['user'] . '"';
    }

    if( !empty( $categories['store_owner'] ) ) {
        $where[] = 's.user = "' . (int) $categories['store_owner'] . '"';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(c.title, c.tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT c.id, c.feedID, c.user, c.store, c.category, c.popular, c.exclusive, c.printable, c.show_in_store, c.available_online, c.title, c.link, c.description, c.tags, c.image, c.code, c.source, c.claim_limit, c.claims, c.visible, c.views, c.clicks, c.start, c.expiration, c.cashback, c.url_title, c.votes, c.votes_percent, c.verified, c.last_verif, c.lastupdate, c.paid_until, c.extra, c.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.physical, s.category, s.name, s.link, s.image, s.phoneno, s.sellonline, s.url_title, s.extra, IF(c.paid_until > NOW(), 1, 0) as sponsored FROM " . DB_TABLE_PREFIX . "coupons c LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = c.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $popular, $exclusive, $printable, $show_in_store, $avab_online, $title, $link, $description, $tags, $image, $code, $source, $claim_limit, $claims, $visible, $views, $clicks, $start, $expiration, $cashback, $url_title, $votes, $votes_percent, $verified, $last_verif, $last_update, $paid_until, $extra, $date, $reviews, $stars, $store_type, $store_cat, $store_name, $store_link, $store_img, $store_phone, $store_sellonline, $store_url_title, $store_extra, $sponsored );

    $data = array();
    while( $stmt->fetch() ) {

        $data[] = (object) value_with_filter( 'coupon_info_values', array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'code' => esc_html( $code ), 'title' => \site\content::title( 'items_name_list', $title, $useem, $useec, $usefl ), 'url' => ( preg_match( '/^http(s)?/i', (string) $link ) ? esc_html( $link ) : esc_html( $store_link ) ), 'original_url' => $link, 'description' => \site\content::content( 'items_list', $description, $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'image2' => ( !empty( $image ) ? ( !filter_var( $image, FILTER_VALIDATE_URL ) ? $GLOBALS['siteURL'] . esc_html( $image ) : esc_html( $image ) ) : '' ), 'source' => ( empty( $source ) ? '' : ( filter_var( $source, FILTER_VALIDATE_URL ) ? esc_html( $source ) : $GLOBALS['siteURL'] . esc_html( $source ) ) ), 'visible' => (boolean) $visible, 'views' => $views, 'clicks' => $clicks, 'start_date' => $start, 'is_coupon' => ( !empty( $code ) ? true : false ), 'is_deal' => ( empty( $code ) ? true : false ), 'is_running' => ( strtotime( $start ) < time() && strtotime( $expiration ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_printable' => ( (boolean) $store_type ? (boolean) $printable : false ), 'is_show_in_store' => ( (boolean) $store_type ? (boolean) $show_in_store : false ), 'is_available_online' => ( ! (boolean) $store_type || (boolean) $avab_online ? true : false ), 'is_local_source' => ( empty( $source ) || filter_var( $source, FILTER_VALIDATE_URL ) ? false : true ), 'is_started' => ( strtotime( $start ) > time() ? false : true ), 'is_expired' => ( strtotime( $expiration ) > time() ? false : true ), 'is_popular' => (boolean) $popular, 'is_exclusive' => (boolean) $exclusive, 'is_verified' => (boolean) $verified, 'last_check' => $last_verif, 'votes' => $votes, 'votes_percent' => $votes_percent, 'claim_limit' => $claim_limit, 'claims' => $claims, 'paid_until' => $paid_until, 'extra' => @unserialize( $extra ), 'last_update' => $last_update, 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_is_physical' => (boolean) $store_type, 'store_img' => esc_html( $store_img ), 'store_catID' => $store_cat, 'store_name' => esc_html( $store_name ), 'store_url' => esc_html( $store_link ), 'store_phone_no' => esc_html( $store_phone ), 'store_sellonline' => (boolean) $store_sellonline, 'store_extra' => @unserialize( $store_extra ), 'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_coupon, $title, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?id=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store_url_title, $store, $extension ) : $GLOBALS['siteURL'] . '?store=' . $store ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store_url_title, $store, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $store ) ) );

    }

    $stmt->close();

    return $data;

    break;

    }

}

/* NUMBER OF PRODUCTS */

public static function have_products( array $category = [], string $place = '', array $special = [], array $options = [] ) {

    global $db, $GET;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['categories'] ) && strcasecmp( $categories['categories'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['categories'] ) ));
        if( !empty( $arr ) )
        $where[] = 'p.category IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['update'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['update'] ) );
        $where[] = 'p.lastupdate >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'p.lastupdate <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'p.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'p.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( !empty( $categories['currency'] ) ) {
        $where[] = 'p.currency = "' . \site\utils::dbp( $categories['currency'] ) . '"';
    }

    if( !empty( $categories['price_from'] ) ) {
        $where[] = 'p.price > 0 AND p.price >= ' . \site\utils::dbp( $categories['price_from'] );
    }

    if( !empty( $categories['price_up_to'] ) ) {
        $where[] = 'p.price > 0 AND p.price <= ' . \site\utils::dbp( $categories['price_up_to'] );
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        $custom_where_clause = value_with_filter( 'products_where_clause', array(
            'expired'   => 'p.expiration <= NOW()',
            'active'    => 'p.expiration > NOW()',
            'popular'   => 'p.popular > 0',
            'cashback'  => 'p.cashback > 0',
            'feed'      => 'p.feedID > 0',
            'visible'   => 'p.visible > 0 AND s.visible > 0',
            'notvisible'=> 'p.visible = 0'
        ) );
        foreach( $show as $v ) {
            if( !empty( $custom_where_clause ) && in_array( $v, array_keys( $custom_where_clause ) ) ) {
                $where[] = $custom_where_clause[$v];
            }
        }
    } else {
        $where[] = 'p.visible > 0 AND s.visible > 0';
    }

    /*  */

    switch( $place ) {

    case 'category':

    if( !empty( $categories['active'] ) ) {
        $where[] = 'p.expiration > NOW()';
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE (? > 0 AND (id = ? OR subcategory = ?)) OR (url_title IS NOT NULL AND url_title = ?)" );
    $stmt->bind_param( "iiis", $GET['id'], $GET['id'], $GET['id'], $GET['id'] );
    $stmt->execute();
    $stmt->bind_result( $id );

    $ids = array();
    while( $stmt->fetch() ) {
        $ids[] = $id;
    }

    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (p.store = s.id) WHERE p.category IN(" . implode( ',', $ids ) . ")" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();

    break;

    case 'search':

    if( !empty( $categories['active'] ) )
    $where[] = 'p.expiration > NOW()';

    $stmt = $db->stmt_init();

    $ids = array();

    if( !empty( $options['category'] ) ) {
        $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE subcategory = ?" );
        $stmt->bind_param( "i", $options['category'] );
        $stmt->execute();
        $stmt->bind_result( $id );

        $ids[] = (int) $options['category'];
        while( $stmt->fetch() ) {
            $ids[] = $id;
        }

        $where[] = 'p.category IN(' . implode( ',', $ids ) . ')';
    }

    $query  = "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store)  WHERE (MATCH(p.title) AGAINST (? IN BOOLEAN MODE) OR MATCH(p.tags) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.name) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.tags) AGAINST (? IN BOOLEAN MODE)) AND p.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
    $bind_t = 'ssss';

    if( gettype( $GET['id'] ) === 'string' ) {
        $search = implode( '+', explode( ' ', trim( $GET['id'] ) ) );
        $search = substr( $search, 0, 50 );
    } else {
        $search = '';
    }

    $params = [ $search, $search, $search, $search ];
    $dist   = false;

    if( !empty( $options['loc'] ) ) {
        if( preg_match( '/([\d\.\-]+)\s([\d\.\-]+)\s?.*/', urldecode( $options['loc'] ), $fcoords ) ) {
            $coords = [ 'lat' => $fcoords[1], 'lng' => $fcoords[2] ];
        } else if( !( $coords = \site\utils::get_coords_from_str( $options['loc'] ) ) ) {
            setQueryLastLog( t( 'cant_get_coords', "We couldn't find this location, please try a new one." ) );
        } else {
            $_GET['loc'] = implode( ' ', $coords ); 
        }

        if( !empty( $coords ) ) {
            $query  = "SELECT COUNT(*), (6378 * acos(cos(radians(?)) * cos(radians(lat)) * cos( radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store) RIGHT JOIN  " . DB_TABLE_PREFIX . "store_locations sl ON (sl.store = p.store) WHERE MBRContains(ST_Buffer(LineString(Point(? + ? / (111.111 / COS(RADIANS(?))), ? + ? / 111.111), Point(? - ? / ( 111.1 / COS(RADIANS(?))), ? - ? / 111.111)), 1), sl.point)";
            
            $distance = $options['distance'] ?? 10;
            $distance = (int) $distance;
            if( $distance < 1 || $distance > 10 )
            $distance = 10;

            if( !empty( $search ) ) {
                $query .= " AND (MATCH(p.title) AGAINST (? IN BOOLEAN MODE) OR MATCH(p.tags) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.name) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.tags) AGAINST (? IN BOOLEAN MODE))";
                $query .= " AND p.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
                $query .= ' GROUP BY distance HAVING distance <= ?';
                $bind_t = 'dddddddddddddssssd';
                $params = [ $coords['lat'], $coords['lng'], $coords['lat'], $distance, $distance, $coords['lng'], $coords['lng'], $distance, $coords['lat'], $distance, $coords['lng'], $coords['lng'], $distance, $search, $search, $search, $search, $distance ];
                $dist   = true;
            } else {
                $query .= " AND p.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
                $query .= ' GROUP BY distance HAVING distance <= ?';
                $bind_t = 'dddddddddddddd';
                $params = [ $coords['lat'],  $coords['lng'], $coords['lat'], $distance, $distance, $coords['lng'], $coords['lng'], $distance, $coords['lat'], $distance, $coords['lng'], $coords['lng'], $distance, $distance ];
                $dist   = true;
            }

        }
    }

    $stmt->prepare( $query );
    $stmt->bind_param( $bind_t, ...$params );
    $stmt->execute();
    if( $dist )
    $stmt->bind_result( $count, $distance );
    else 
    $stmt->bind_result( $count );
    $stmt->fetch();

    break;

    default:

    /* WHERE / ORDER BY */

    if( !empty( $categories['ids'] ) && strcasecmp( $categories['ids'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['ids'] ) ));
        if( !empty( $arr ) )
        $where[] = 'p.id IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['store'] ) && strcasecmp( $categories['store'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['store'] ) ));
        if( !empty( $arr ) )
        $where[] = 'p.store IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['user'] ) ) {
        $where[] = 'p.user = "' . (int) $categories['user'] . '"';
    }

    if( !empty( $categories['store_owner'] ) ) {
        $where[] = 's.user = "' . (int) $categories['store_owner'] . '"';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(p.title, p.tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();

    break;

    }

    $stmt->close();

    if( isset( $categories['limit'] ) && $categories['limit'] < $count ) {
        $count = $categories['limit'];
    }

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

/* FETCH THE PRODUCTS */

public static function while_products( array $category = [], string $place = '', array $special = [], array $options = [] ) {

    global $db, $GET;

    /** make or not seo links */
    $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
    list( $seo_link_product, $seo_link_store, $seo_link_reviews, $extension ) = array( \query\main::get_option( 'seo_link_product' ), \query\main::get_option( 'seo_link_store' ), \query\main::get_option( 'seo_link_reviews' ), \query\main::get_option( 'extension' ) );

    $categories = \site\utils::validate_user_data( $category );

    $where = $orderby = $limit = array();

    if( isset( $categories['max'] ) ) {
        if( !empty( $categories['max'] ) ) {
            $limit[] = $categories['max'];
        }
    } else {
        $page = ( !empty( $categories['page'] ) ? (int) $categories['page'] : ( !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1 ) );
        $per_page = ( isset( $categories['per_page'] ) ? (int) $categories['per_page'] : \query\main::get_option( 'items_per_page' ) );
        $offset = ( isset( $page ) && $page > 1 ? ( $page - 1 ) * $per_page : 0 );

        $limit[] = $offset;
        $limit[] = isset( $categories['limit'] ) && ( $offset + $per_page ) > $categories['limit'] ? ( ( $limit2 = ( $categories['limit'] - $per_page ) ) > 0 ? $limit2 : $categories['limit'] ) : $per_page;
    }

    /* WHERE / ORDER BY */

    if( !empty( $categories['categories'] ) && strcasecmp( $categories['categories'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['categories'] ) ));
        if( !empty( $arr ) )
        $where[] = 'p.category IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['update'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['update'] ) );
        $where[] = 'p.lastupdate >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'p.lastupdate <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'p.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'p.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( !empty( $categories['currency'] ) ) {
        $where[] = 'p.currency = "' . \site\utils::dbp( $categories['currency'] ) . '"';
    }

    if( !empty( $categories['price_from'] ) ) {
        $where[] = 'p.price > 0 AND p.price >= ' . \site\utils::dbp( $categories['price_from'] );
    }

    if( !empty( $categories['price_up_to'] ) ) {
        $where[] = 'p.price > 0 AND p.price <= ' . \site\utils::dbp( $categories['price_up_to'] );
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        $custom_where_clause = value_with_filter( 'products_where_clause', array(
            'expired'   => 'p.expiration <= NOW()',
            'active'    => '(p.expiration IS NULL OR p.expiration > NOW())',
            'popular'   => 'p.popular > 0',
            'sponsored' => 'p.paid_until > NOW()',
            'cashback'  => 'p.cashback > 0',
            'feed'      => 'p.feedID > 0',
            'visible'   => 'p.visible > 0 AND s.visible > 0',
            'notvisible'=> 'p.visible = 0'
        ) );
        foreach( $show as $v ) {
            if( !empty( $custom_where_clause ) && in_array( $v, array_keys( $custom_where_clause ) ) ) {
                $where[] = $custom_where_clause[$v];
            }
        }
    } else {
        $where[] = 'p.visible > 0 AND s.visible > 0';
    }

    if( isset( $categories['orderby'] ) ) {
        $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
        $custom_orderby_clause = value_with_filter( 'products_orderby_clause', array(
            'rand'          => 'RAND()',
            'name'          => 'p.title',
            'name desc'     => 'p.title DESC',
            'update'        => 'p.lastupdate',
            'update desc'   => 'p.lastupdate DESC',
            'rating'        => 'rating',
            'rating desc'   => 'rating DESC',
            'votes'         => 'votes',
            'votes desc'    => 'votes DESC',
            'views'         => 'p.views',
            'views desc'    => 'p.views DESC',
            'price'         => 'p.price',
            'price desc'    => 'p.price DESC',
            'discount'      => '(p.old_price - p.price)',
            'discount desc' => '(p.old_price - p.price) DESC',
            'popular'       => 'p.popular DESC',
            'sponsored'     => 'sponsored DESC',
            'expiration'    => 'p.expiration',
            'expiration desc'=> 'p.expiration DESC',
            'date'          => 'p.date',
            'date desc'     => 'p.date DESC',
            'active'        => 'p.expiration',
            'active desc'   => 'p.expiration DESC',
            'distance'      => 'distance',
            'distance desc' => 'distance DESC'
        ) );

        foreach( $order as $v ) {
            if( !empty( $custom_orderby_clause ) && in_array( $v, array_keys( $custom_orderby_clause ) ) ) {
                $orderby[] = $custom_orderby_clause[$v];
            }
        }
    }

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : (boolean) \query\main::get_option( 'smilies_products' );
    $usesh = ( isset( $special['no_shortcodes'] ) && $special['no_shortcodes'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    /* */

    switch( $place ) {

    case 'category':

    if( !empty( $categories['active'] ) ) {
        $where[] = 'p.expiration > NOW()';
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE (? > 0 AND (id = ? OR subcategory = ?)) OR (url_title IS NOT NULL AND url_title = ?)" );
    $stmt->bind_param( "iiis", $GET['id'], $GET['id'], $GET['id'], $GET['id'] );
    $stmt->execute();                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
    $stmt->bind_result( $id );

    $ids = array();
    while( $stmt->fetch() ) {
        $ids[] = $id;
    }

    $stmt->prepare( "SELECT p.id, p.feedID, p.user, p.store, p.category, p.popular, p.title, p.link, p.description, p.tags, p.image, p.price, p.old_price, p.currency, p.visible, p.views, p.start, p.expiration, p.cashback, p.url_title, p.lastupdate, p.paid_until, p.extra, p.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.id, s.category, s.physical, s.name, s.image, s.link, s.sellonline, s.url_title, s.extra, IF(p.paid_until > NOW(), 1, 0) as sponsored FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store) WHERE p.category IN(" . implode( ',', $ids ) . ")" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $popular, $title, $link, $description, $tags, $image, $price, $old_price, $currency, $visible, $views, $start, $expiration, $cashback, $url_title, $last_update, $paid_until, $extra, $date, $reviews, $stars, $store_id, $store_cat, $store_type, $store_name, $store_img, $store_link, $store_sellonline, $store_url_title, $store_extra, $sponsored );

    $data = array();
    while( $stmt->fetch() ) {

        $data[] = (object) value_with_filter( 'product_info_values', array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'title' => \site\content::title( 'products_name_list', $title, $useem, $useec, $usefl ), 'url' => ( $link && preg_match( '/^http(s)?/i', $link ) ? esc_html( $link ) : esc_html( $store_link ) ), 'original_url' => $link, 'description' => \site\content::content( 'products_list', $description, $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'price' => $price, 'old_price' => ( $old_price > 0 && $old_price > $price ? $old_price : 0 ), 'currency' => esc_html( $currency ), 'visible' => (boolean) $visible, 'views' => $views, 'start_date' => $start, 'is_running' => ( ( !$start || strtotime( $start ) < time() ) && ( !$expiration || strtotime( $expiration ) ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_popular' => $popular, 'is_available_online' => ( ! (boolean) $store_type || $store_sellonline ? true : false ), 'is_started' => ( !$start || strtotime( $start ) > time() ? false : true ), 'is_expired' => ( !$expiration || strtotime( $expiration ) > time() ? false : true ), 'paid_until' => $paid_until, 'extra' => @unserialize( $extra ), 'last_update' => $last_update, 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_is_physical' => (boolean) $store_type, 'store_img' => esc_html( $store_img ), 'storeID' => $store_id, 'store_catID' => $store_cat, 'store_name' => esc_html( $store_name ), 'store_url' => esc_html( $store_link ), 'store_sellonline' => (boolean) $store_sellonline, 'store_extra' => @unserialize( $store_extra ), 'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_product, $title, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?product=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store_url_title, $store_id, $extension ) : $GLOBALS['siteURL'] . '?store=' . $store_id ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store_url_title, $store_id, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $store_id ) ) );

    }

    $stmt->close();

    return $data;

    break;

    case 'search':

    if( !empty( $categories['active'] ) ) {
        $where[] = 'p.expiration > NOW()';
    }

    $stmt = $db->stmt_init();

    $ids = array();

    if( !empty( $options['category'] ) ) {
        $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE subcategory = ?" );
        $stmt->bind_param( "i", $options['category'] );
        $stmt->execute();
        $stmt->bind_result( $id );

        $ids[] = (int) $options['category'];
        while( $stmt->fetch() ) {
            $ids[] = $id;
        }

        $where[] = 'p.category IN(' . implode( ',', $ids ) . ')';
    }

    $query  = "SELECT p.id, p.feedID, p.user, p.store, p.category, p.popular, p.title, p.link, p.description, p.tags, p.image, p.price, p.old_price, p.currency, p.visible, p.views, p.start, p.expiration, p.cashback, p.url_title, p.lastupdate, p.paid_until, p.extra, p.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.id, s.category, s.physical, s.name, s.image, s.link, s.sellonline, s.url_title, s.extra, IF(p.paid_until > NOW(), 1, 0) as sponsored FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store) WHERE (MATCH(p.title) AGAINST (? IN BOOLEAN MODE) OR MATCH(p.tags) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.name) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.tags) AGAINST (? IN BOOLEAN MODE)) AND p.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
    $query .= ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) );
    $bind_t = 'ssss';

    if( isset( $GET['id'] ) && gettype( $GET['id'] ) === 'string' ) {
        $search = implode( '+', explode( ' ', trim( $GET['id'] ) ) );
        $search = substr( $search, 0, 50 );
    } else {
        $search = '';
    }

    $params = [ $search, $search, $search, $search ];
    $dist   = false;

    if( !empty( $options['loc'] ) ) {
        if( preg_match( '/([\d\.\-]+)\s([\d\.\-]+)\s?.*/', urldecode( $options['loc'] ), $fcoords ) ) {
            $coords = [ 'lat' => $fcoords[1], 'lng' => $fcoords[2] ];
        } else if( !( $coords = \site\utils::get_coords_from_str( $options['loc'] ) ) ) {
            setQueryLastLog( t( 'cant_get_coords', "We couldn't find this location, please try a new one." ) );
        } else {
            $_GET['loc'] = implode( ' ', $coords ); 
        }

        if( !empty( $coords ) ) {
            $query  = "SELECT p.id, p.feedID, p.user, p.store, p.category, p.popular, p.title, p.link, p.description, p.tags, p.image, p.price, p.old_price, p.currency, p.visible, p.views, p.start, p.expiration, p.cashback, p.url_title, p.lastupdate, p.paid_until, p.extra, p.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.id, s.category, s.physical, s.name, s.image, s.link, s.sellonline, s.url_title, s.extra, IF(p.paid_until > NOW(), 1, 0) as sponsored, (6378 * acos(cos(radians(?)) * cos(radians(lat)) * cos( radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store) RIGHT JOIN  " . DB_TABLE_PREFIX . "store_locations sl ON (sl.store = p.store) WHERE MBRContains(ST_Buffer(LineString(Point(? + ? / (111.111 / COS(RADIANS(?))), ? + ? / 111.111), Point(? - ? / ( 111.1 / COS(RADIANS(?))), ? - ? / 111.111)), 1), sl.point) ";
            
            $distance = $options['distance'] ?? 10;
            $distance = (int) $distance;
            if( $distance < 1 || $distance > 10 )
            $distance = 10;

            if( !empty( $search ) ) {
                $query .= " AND (MATCH(p.title) AGAINST (? IN BOOLEAN MODE) OR MATCH(p.tags) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.name) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.tags) AGAINST (? IN BOOLEAN MODE))";
                $query .= " AND p.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
                $query .= ' HAVING distance <= ?';
                $query .= ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) );
                $bind_t = 'dddddddddddddssssd';
                $params = [ $coords['lat'], $coords['lng'], $coords['lat'], $distance, $distance, $coords['lng'], $coords['lng'], $distance, $coords['lat'], $distance, $coords['lng'], $coords['lng'], $distance,  $search, $search, $search, $search, $distance ];
                $dist   = true;
            } else {
                $query .= " AND p.visible > 0 AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
                $query .= ' HAVING distance <= ?';
                $query .= ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) );
                $bind_t = 'dddddddddddddd';
                $params = [ $coords['lat'],  $coords['lng'], $coords['lat'], $distance, $distance, $coords['lng'], $coords['lng'], $distance, $coords['lat'], $distance, $coords['lng'], $coords['lng'], $distance, $distance ];
                $dist   = true;
            }

        }
    }

    $stmt->prepare( $query );
    $stmt->bind_param( $bind_t, ...$params );
    $stmt->execute();
    if( $dist )
    $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $popular, $title, $link, $description, $tags, $image, $price, $old_price, $currency, $visible, $views, $start, $expiration, $cashback, $url_title, $last_update, $paid_until, $extra, $date, $reviews, $stars, $store_id, $store_cat, $store_type, $store_name, $store_img, $store_link, $store_sellonline, $store_url_title, $store_extra, $sponsored, $distance );
    else 
    $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $popular, $title, $link, $description, $tags, $image, $price, $old_price, $currency, $visible, $views, $start, $expiration, $cashback, $url_title, $last_update, $paid_until, $extra, $date, $reviews, $stars, $store_id, $store_cat, $store_type, $store_name, $store_img, $store_link, $store_sellonline, $store_url_title, $store_extra, $sponsored );
    $data = array();
    while( $stmt->fetch() ) {

        $vals   = array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'title' => \site\content::title( 'products_name_list', $title, $useem, $useec, $usefl ), 'url' => ( $link && preg_match( '/^http(s)?/i', $link ) ? esc_html( $link ) : esc_html( $store_link ) ), 'original_url' => $link, 'description' => \site\content::content( 'products_list', $description, $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'price' => $price, 'old_price' => ( $old_price > 0 && $old_price > $price ? $old_price : 0 ), 'currency' => esc_html( $currency ), 'visible' => (boolean) $visible, 'views' => $views, 'start_date' => $start, 'is_running' => ( ( !$start || strtotime( $start ) < time() ) && ( !$expiration || strtotime( $expiration ) ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_popular' => $popular, 'is_available_online' => ( ! (boolean) $store_type || $store_sellonline ? true : false ), 'is_started' => ( !$start || strtotime( $start ) > time() ? false : true ), 'is_expired' => ( !$expiration || strtotime( $expiration ) > time() ? false : true ), 'paid_until' => $paid_until, 'extra' => @unserialize( $extra ), 'last_update' => $last_update, 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_is_physical' => (boolean) $store_type, 'store_img' => esc_html( $store_img ), 'storeID' => $store_id, 'store_catID' => $store_cat, 'store_name' => esc_html( $store_name ), 'store_url' => esc_html( $store_link ), 'store_sellonline' => (boolean) $store_sellonline, 'store_extra' => @unserialize( $store_extra ), 'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_product, $title, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?product=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store_url_title, $store_id, $extension ) : $GLOBALS['siteURL'] . '?store=' . $store_id ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store_url_title, $store_id, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $store_id ) );
        if( $dist )
        $vals['distance'] = $distance;
        $data[] = (object) value_with_filter( 'product_info_values', $vals );
    
    }

    $stmt->close();

    return $data;

    break;

    default:

    /* WHERE / ORDER BY */

    if( !empty( $categories['ids'] ) && strcasecmp( $categories['ids'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['ids'] ) ));
        if( !empty( $arr ) )
        $where[] = 'p.id IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
        if( !isset( $categories['orderby'] ) ) {
            $orderby[] = 'field(p.id,' . \site\utils::dbp( implode(',', $arr) ) . ')';
        }
    }

    if( !empty( $categories['store'] ) && strcasecmp( $categories['store'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['store'] ) ));
        if( !empty( $arr ) )
        $where[] = 'p.store IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['user'] ) ) {
        $where[] = 'p.user = "' . (int) $categories['user'] . '"';
    }

    if( !empty( $categories['store_owner'] ) ) {
        $where[] = 's.user = "' . (int) $categories['store_owner'] . '"';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(p.title, p.tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT p.id, p.feedID, p.user, p.store, p.category, p.popular, p.title, p.link, p.description, p.tags, p.image, p.price, p.old_price, p.currency, p.visible, p.views, p.start, p.expiration, p.cashback, p.url_title, p.lastupdate, p.paid_until, p.extra, p.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, s.id, s.category, s.physical, s.name, s.image, s.link, s.sellonline, s.url_title, s.extra FROM " . DB_TABLE_PREFIX . "products p LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = p.store)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $feed_id, $user, $store, $cat, $popular, $title, $link, $description, $tags, $image, $price, $old_price, $currency, $visible, $views, $start, $expiration, $cashback, $url_title, $last_update, $paid_until, $extra, $date, $reviews, $stars, $store_id, $store_cat, $store_type, $store_name, $store_img, $store_link, $store_sellonline, $store_url_title, $store_extra );

    $data = array();
    while( $stmt->fetch() ) {

        $data[] = (object) value_with_filter( 'product_info_values', array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'storeID' => $store, 'catID' => $cat, 'title' => \site\content::title( 'products_name_list', $title, $useem, $useec, $usefl ), 'url' => ( $link && preg_match( '/^http(s)?/i', $link ) ? esc_html( $link ) : esc_html( $store_link ) ), 'original_url' => $link, 'description' => \site\content::content( 'products_list', $description, $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'price' => $price, 'old_price' => ( $old_price > 0 && $old_price > $price ? $old_price : 0 ), 'currency' => esc_html( $currency ), 'visible' => (boolean) $visible, 'views' => $views, 'start_date' => $start, 'is_running' => ( ( !$start || strtotime( $start ) < time() ) && ( !$expiration || strtotime( $expiration ) ) > time() ? true : false ), 'expiration_date' => $expiration, 'cashback' => $cashback, 'is_popular' => $popular, 'is_available_online' => ( ! (boolean) $store_type || $store_sellonline ? true : false ), 'is_started' => ( !$start || strtotime( $start ) > time() ? false : true ), 'is_expired' => ( !$expiration || strtotime( $expiration ) > time() ? false : true ), 'paid_until' => $paid_until, 'extra' => @unserialize( $extra ), 'last_update' => $last_update, 'date' => $date, 'reviews' => $reviews, 'stars' => $stars, 'store_is_physical' => (boolean) $store_type, 'store_img' => esc_html( $store_img ), 'storeID' => $store_id, 'store_catID' => $store_cat, 'store_name' => esc_html( $store_name ), 'store_url' => esc_html( $store_link ), 'store_sellonline' => (boolean) $store_sellonline, 'store_extra' => @unserialize( $store_extra ), 'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_product, $title, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?product=' . $id ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_name, $store_url_title, $store_id, $extension ) : $GLOBALS['siteURL'] . '?store=' . $store_id ), 'store_reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $store_name, $store_url_title, $store_id, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $store_id ) ) );

    }

    $stmt->close();

    return $data;

    break;

    }

}

/* NUMBER OF REVIEWS */

public static function have_reviews( $category = array(), $place = '', $special = array() ) {

    global $db, $GET;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'r.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'r.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        $custom_where_clause = value_with_filter( 'reviews_where_clause', array(
            'notvalid'  => 'r.valid = 0',
            'valid'     => 'r.valid > 0'
        ) );
        foreach( $show as $v ) {
            if( !empty( $custom_where_clause ) && in_array( $v, array_keys( $custom_where_clause ) ) ) {
                $where[] = $custom_where_clause[$v];
            }
        }
    } else {
        $where['valid'] = 'r.valid > 0';
    }

    /* */

    switch( $place ) {

    case 'store':

    $stmt = $db->stmt_init();
    $stmt->prepare("SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews r WHERE store = ? AND valid > 0");
    $stmt->bind_param( "i", $GET['id'] );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();

    break;

    default:

    /* WHERE / ORDER BY */

    if( !empty( $categories['store'] ) && strcasecmp( $categories['store'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['store'] ) ));
        if( !empty( $arr ) )
        $where[] = 'r.store IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['user'] ) ) {
        $where[] = 'r.user = "' . (int) $categories['user'] . '"';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'r.text REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews r" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();

    break;

    }

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

/* FETCH THE REVIEWS */

public static function while_reviews( $category = array(), $place = '', $special = array() ) {

    global $db, $GET;

    /** make or not seo links */
    $seo_link = defined( 'SEO_LINKS' ) && SEO_LINKS ? true : false;
    list( $seo_link_store, $extension ) = array( \query\main::get_option( 'seo_link_store' ), \query\main::get_option( 'extension' ) );

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

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'r.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'r.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        $custom_where_clause = value_with_filter( 'reviews_where_clause', array(
            'notvalid'  => 'r.valid = 0',
            'valid'     => 'r.valid > 0'
        ) );
        foreach( $show as $v ) {
            if( !empty( $custom_where_clause ) && in_array( $v, array_keys( $custom_where_clause ) ) ) {
                $where[] = $custom_where_clause[$v];
            }
        }
    } else {
        $where['valid'] = 'r.valid > 0';
    }

    if( isset( $categories['orderby'] ) ) {
        $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
        $custom_orderby_clause = value_with_filter( 'reviews_orderby_clause', array(
            'rand'          => 'RAND()',
            'date'          => 'r.date',
            'date desc'     => 'r.date DESC'
        ) );

        foreach( $order as $v ) {
            if( !empty( $custom_orderby_clause ) && in_array( $v, array_keys( $custom_orderby_clause ) ) ) {
                $orderby[] = $custom_orderby_clause[$v];
            }
        }
    }

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : (boolean) \query\main::get_option( 'smilies_reviews' );
    $usesh = ( isset( $special['no_shortcodes'] ) && $special['no_shortcodes'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    /* */

    switch( $place ) {

    case 'store':

    unset( $where['valid'] );

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT r.id, r.user, r.store, r.text, r.stars, r.valid, r.date, u.name, u.avatar FROM " . DB_TABLE_PREFIX . "reviews r LEFT JOIN " . DB_TABLE_PREFIX . "users u ON (u.id = r.user) WHERE r.store = ? AND r.valid > 0" . ( !empty( $where ) ? ' AND ' : implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->bind_param( "i", $GET['id'] );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $store, $text, $stars, $valid, $date, $user_name, $user_avatar );

    $data = array();
    while( $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'userID' => $user, 'storeID' => $store, 'user_name' => esc_html( $user_name ), 'text' => \site\content::content( 'reviews_list', $text, $useem, $usesh, false, $useec, $usefl ), 'stars' => $stars, 'valid' => (boolean) $valid, 'date' => $date, 'user_avatar' => esc_html( $user_avatar ) );
    }

    $stmt->close();

    return $data;

    break;

    default:

    /* WHERE / ORDER BY */

    if( !empty( $categories['store'] ) && strcasecmp( $categories['store'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['store'] ) ));
        if( !empty( $arr ) )
        $where[] = 'r.store IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['user'] ) ) {
        $where[] = 'r.user = "' . (int) $categories['user'] . '"';
    }


    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'r.text REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT r.id, r.user, r.store, r.text, r.stars, r.valid, r.date, u.name, u.avatar, s.name, s.link, s.url_title FROM " . DB_TABLE_PREFIX . "reviews r LEFT JOIN " . DB_TABLE_PREFIX . "stores s ON (s.id = r.store) LEFT JOIN " . DB_TABLE_PREFIX . "users u ON (u.id = r.user)" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $store, $text, $stars, $valid, $date, $user_name, $user_avatar, $store_name, $store_url, $store_url_title );

    $data = array();
    while( $stmt->fetch() ) {

        $store_url_tit = !empty( $store_url_title ) ? $store_url_title : $store_name;

        $data[] = (object)array( 'ID' => $id, 'userID' => $user, 'storeID' => $store, 'user_name' => esc_html( $user_name ), 'store_url' => $store_url, 'text' => \site\content::content( 'reviews_list', $text, $useem, $usesh, false, $useec, $usefl ), 'stars' => $stars, 'valid' => (boolean) $valid, 'date' => $date, 'user_avatar' => esc_html( $user_avatar ), 'store_name' => esc_html( $store_name ), 'store_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $store_url_tit, $store, $extension ) : $GLOBALS['siteURL'] . '?store=' . $store ) );

    }

    $stmt->close();

    return $data;

    break;

    }

}

/* NUMBER OF STORES */

public static function have_stores( array $category = [], string $place = '', array $special = [], array $options = [] ) {

    global $db, $GET;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]$|^[0-9]$)/u', $categories['firstchar'] ) ) {
        $where[] = 's.name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
    }

    if( !empty( $categories['categories'] ) && strcasecmp( $categories['categories'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['categories'] ) ));
        if( !empty( $arr ) )
        $where[] = 's.category IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['update'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['update'] ) );
        $where[] = 's.lastupdate >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 's.lastupdate <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 's.date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 's.date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        $custom_where_clause = value_with_filter( 'stores_where_clause', array(
            'physical'  => 's.physical > 0',
            'online'    => 's.physical = 0',
            'popular'   => 's.popular > 0',
            'feed'      => 's.feedID > 0',
            'visible'   => 's.visible > 0',
            'notvisible'=> 's.visible = 0'
        ) );
        foreach( $show as $v ) {
            if( !empty( $custom_where_clause ) && in_array( $v, array_keys( $custom_where_clause ) ) ) {
                $where[] = $custom_where_clause[$v];
            }
        }
    } else {
        $where[] = 's.visible > 0';
    }

    /* */

    switch( $place ) {

    case 'category':

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE (? > 0 AND (id = ? OR subcategory = ?)) OR (url_title IS NOT NULL AND url_title = ?)" );
    $stmt->bind_param( "iiis", $GET['id'], $GET['id'], $GET['id'], $GET['id'] );
    $stmt->execute();
    $stmt->bind_result( $id );

    $ids = array();
    while( $stmt->fetch() ) {
        $ids[] = $id;
    }

    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores s WHERE category IN(" . implode( ',', $ids ) . ") AND visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();

    break;

    case 'search':

    $stmt = $db->stmt_init();

    $ids = array();

    if( !empty( $options['category'] ) ) {
        $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE subcategory = ?" );
        $stmt->bind_param( "i", $_GET['category'] );
        $stmt->execute();
        $stmt->bind_result( $id );

        $ids[] = (int) $options['category'];
        while( $stmt->fetch() ) {
            $ids[] = $id;
        }

        $where[] = 's.category IN(' . implode( ',', $ids ) . ')';
    }

    
    $query  = "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores s WHERE (MATCH(s.name) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.tags) AGAINST (? IN BOOLEAN MODE))" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
    $bind_t = 'ss';

    if( isset( $GET['id'] ) && gettype( $GET['id'] ) === 'string' ) {
        $search = implode( '+', explode( ' ', trim( $GET['id'] ) ) );
        $search = substr( $search, 0, 50 );
    } else {
        $search = '';
    }

    $params = [ $search, $search ];
    $dist   = false;

    if( !empty( $options['loc'] ) ) {
        if( preg_match( '/([\d\.\-]+)\s([\d\.\-]+)\s?.*/', urldecode( $options['loc'] ), $fcoords ) ) {
            $coords = [ 'lat' => $fcoords[1], 'lng' => $fcoords[2] ];
        } else if( !( $coords = \site\utils::get_coords_from_str( $options['loc'] ) ) ) {
            setQueryLastLog( t( 'cant_get_coords', "We couldn't find this location, please try a new one." ) );
        } else {
            $_GET['loc'] = implode( ' ', $coords ); 
        }

        if( !empty( $coords ) ) {
            $query  = "SELECT COUNT(*), (6378 * acos(cos(radians(?)) * cos(radians(lat)) * cos( radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance FROM " . DB_TABLE_PREFIX . "stores s RIGHT JOIN  " . DB_TABLE_PREFIX . "store_locations sl ON (sl.store = s.id) WHERE MBRContains(ST_Buffer(LineString(Point(? + ? / (111.111 / COS(RADIANS(?))), ? + ? / 111.111), Point(? - ? / ( 111.1 / COS(RADIANS(?))), ? - ? / 111.111)), 1), sl.point)";
            
            $distance = $options['distance'] ?? 10;
            $distance = (int) $distance;
            if( $distance < 1 || $distance > 10 )
            $distance = 10;

            if( !empty( $search ) ) {
                $query .= " AND (MATCH(s.name) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.tags) AGAINST (? IN BOOLEAN MODE))";
                $query .= " AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
                $query .= ' GROUP BY distance HAVING distance <= ?';
                $bind_t = 'dddddddddddddssd';
                $params = [ $coords['lat'], $coords['lng'], $coords['lat'], $distance, $distance, $coords['lng'], $coords['lng'], $distance, $coords['lat'], $distance, $coords['lng'], $coords['lng'], $distance, $search, $search, $distance ];
                $dist   = true;
            } else {
                $query .= " AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
                $query .= ' GROUP BY distance HAVING distance <= ?';
                $bind_t = 'dddddddddddddd';
                $params = [ $coords['lat'],  $coords['lng'], $coords['lat'], $distance, $distance, $coords['lng'], $coords['lng'], $distance, $coords['lat'], $distance, $coords['lng'], $coords['lng'], $distance, $distance ];
                $dist   = true;
            }

        }
    }

    $stmt->prepare( $query );
    $stmt->bind_param( $bind_t, ...$params );
    $stmt->execute();
    if( $dist )
    $stmt->bind_result( $count, $distance );
    else 
    $stmt->bind_result( $count );
    $stmt->fetch();

    break;

    default:

    /* WHERE / ORDER BY */

    if( !empty( $categories['user'] ) ) {
        $where[] = 'user = "' . (int) $categories['user'] . '"';
    }

    if( !empty( $categories['ids'] ) && strcasecmp( $categories['ids'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['ids'] ) ));
        if( !empty( $arr ) )
        $where[] = 'id IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(name, tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores s" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();

    break;

    }

    $stmt->close();

    if( isset( $categories['limit'] ) && $categories['limit'] < $count ) {
        $count = $categories['limit'];
    }

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

/* FETCH THE STORES */

public static function while_stores( array $category = [], string $place = '', array $special = [], array $options = [] ) {

    global $db, $GET;

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
        $limit[] = isset( $categories['limit'] ) && ( $offset + $per_page ) > $categories['limit'] ? ( ( $limit2 = ( $categories['limit'] - $per_page ) ) > 0 ? $limit2 : $categories['limit'] ) : $per_page;
    }

    /* WHERE / ORDER BY */

    if( isset( $categories['firstchar'] ) && preg_match( '/(^[\p{L}]$|^[0-9]$)/u', $categories['firstchar'] ) ) {
        $where[] = 'name REGEXP "^' . ( is_numeric( $categories['firstchar'][0] ) ? '[0-9]' : \site\utils::dbp( $categories['firstchar'] ) ) . '"';
    }

    if( !empty( $categories['categories'] ) && strcasecmp( $categories['categories'], 'all' ) != 0 ) {
        $arr = array_filter( array_map( function( $w ){
            return (int) $w;
        }, explode( ',', $categories['categories'] ) ));
        if( !empty( $arr ) )
        $where[] = 'category IN(' . \site\utils::dbp( implode(',', $arr) ) . ')';
        if( !isset( $categories['orderby'] ) ) {
            $orderby[] = 'field(id,' . \site\utils::dbp( implode(',', $arr) ) . ')';
        }
    }

    if( !empty( $categories['update'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['update'] ) );
        $where[] = 'lastupdate >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'lastupdate <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( !empty( $categories['date'] ) ) {
        $date = array_map( 'trim', explode( ',', $categories['date'] ) );
        $where[] = 'date >= FROM_UNIXTIME(' . \site\utils::dbp( $date[0] ) . ')';
        if( isset( $date[1] ) ) {
            $where[] = 'date <= FROM_UNIXTIME(' . \site\utils::dbp( $date[1] ) . ')';
        }
    }

    if( isset( $categories['show'] ) ) {
        $show = array_map( 'trim', explode( ',', strtolower( $categories['show'] ) ) );
        $custom_where_clause = value_with_filter( 'stores_where_clause', array(
            'physical'  => 'physical > 0',
            'online'    => 'physical = 0',
            'popular'   => 'popular > 0',
            'feed'      => 'feedID > 0',
            'visible'   => 'visible > 0',
            'notvisible'=> 'visible = 0'
        ) );
        foreach( $show as $v ) {
            if( !empty( $custom_where_clause ) && in_array( $v, array_keys( $custom_where_clause ) ) ) {
                $where[] = $custom_where_clause[$v];
            }
        }
    } else {
        $where[] = 'visible > 0';
    }

    if( isset( $categories['orderby'] ) ) {
        $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
        $custom_orderby_clause = value_with_filter( 'stores_orderby_clause', array(
            'rand'          => 'RAND()',
            'name'          => 'name',
            'name desc'     => 'name DESC',
            'update'        => 'lastupdate',
            'update desc'   => 'lastupdate DESC',
            'rating'        => 'rating',
            'rating desc'   => 'rating DESC',
            'votes'         => 'votes',
            'votes desc'    => 'votes DESC',
            'views'         => 'views',
            'views desc'    => 'views DESC',
            'popular'       => 'popular DESC',
            'date'          => 'date',
            'date desc'     => 'date DESC',
            'distance'      => 'distance',
            'distance desc' => 'distance DESC'
        ) );

        foreach( $order as $v ) {
            if( !empty( $custom_orderby_clause ) && in_array( $v, array_keys( $custom_orderby_clause ) ) ) {
                $orderby[] = $custom_orderby_clause[$v];
            }
        }
    }

    // special
    $useem = ( isset( $special['no_emoticons'] ) && $special['no_emoticons'] ) ? false : (boolean) \query\main::get_option( 'smilies_stores' );
    $usesh = ( isset( $special['no_shortcodes'] ) && $special['no_shortcodes'] ) ? false : true;
    $useec = ( isset( $special['no_escape'] ) && $special['no_escape'] ) ? false : true;
    $usefl = ( isset( $special['no_filters'] ) && $special['no_filters'] ) ? false : true;

    /* */

    switch( $place ) {

    case 'category':

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE (? > 0 AND (id = ? OR subcategory = ?)) OR (url_title IS NOT NULL AND url_title = ?)" );
    $stmt->bind_param( "iiis", $GET['id'], $GET['id'], $GET['id'], $GET['id'] );
    $stmt->execute();
    $stmt->bind_result( $id );

    $ids = array();
    while( $stmt->fetch() ) {
        $ids[] = $id;
    }

    $stmt->prepare( "SELECT id, feedID, user, category, popular, physical, name, link, description, tags, image, hours, phoneno, sellonline, visible, views, url_title, lastupdate, extra, date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons WHERE store = s.id AND visible > 0), (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products WHERE store = s.id AND visible > 0) FROM " . DB_TABLE_PREFIX . "stores s  WHERE category IN(" . implode( ',', $ids ) . ") AND visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $feed_id, $user, $cat, $popular, $physical, $name, $link, $description, $tags, $image, $hours, $phone, $sellonline, $visible, $views, $url_title, $last_update, $extra, $date, $reviews, $stars, $coupons, $products );

    $data = array();
    while( $stmt->fetch() ) {

        $data[] = (object) value_with_filter( 'store_info_values', array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'catID' => $cat, 'name' => \site\content::title( 'stores_name_list', $name, $useem, $useec, $usefl ), 'url' => esc_html( $link ), 'description' => \site\content::content( 'stores_list', $description, $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'hours' => @unserialize( $hours ), 'phone_no' => esc_html( $phone ), 'sellonline' => (boolean) ( !$physical ? 1 : $sellonline ), 'sellonline2' => (boolean) $sellonline, 'extra' => @unserialize( $extra ), 'last_update' => $last_update, 'date' => $date, 'visible' => (boolean) $visible, 'views' => $views, 'reviews' => $reviews, 'stars' => $stars, 'coupons' => $coupons, 'products' => $products, 'is_popular' => (boolean) $popular, 'is_physical' => (boolean) $physical, 'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $name, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?store=' . $id ), 'reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $name, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $id ) ) );

    }

    $stmt->close();

    return $data;

    break;

    case 'search':

    $stmt = $db->stmt_init();

    $ids = array();

    if( !empty( $options['category'] ) ) {
        $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "categories WHERE subcategory = ?" );
        $stmt->bind_param( "i", $options['category'] );
        $stmt->execute();
        $stmt->bind_result( $id );

        $ids[] = (int) $options['category'];
        while( $stmt->fetch() ) {
            $ids[] = $id;
        }

        $where[] = 's.category IN(' . implode( ',', $ids ) . ')';
    }

    $query  = "SELECT s.id, s.feedID, s.user, s.category, s.popular, s.physical, s.name, s.link, s.description, s.tags, s.image, s.hours, s.phoneno, s.sellonline, s.visible, s.views, s.url_title, s.lastupdate, s.extra, s.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons WHERE store = s.id AND visible > 0), (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products WHERE store = s.id AND visible > 0) FROM " . DB_TABLE_PREFIX . "stores s WHERE (MATCH(s.name) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.tags) AGAINST (? IN BOOLEAN MODE))" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
    $query .= ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) );
    $bind_t = 'ss';

    if( isset( $GET['id'] ) && gettype( $GET['id'] ) === 'string' ) {
        $search = implode( '+', explode( ' ', trim( $GET['id'] ) ) );
        $search = substr( $search, 0, 50 );
    } else {
        $search = '';
    }

    $params = [ $search, $search ];
    $dist   = false;

    if( !empty( $options['loc'] ) ) {
        if( preg_match( '/([\d\.\-]+)\s([\d\.\-]+)\s?.*/', urldecode( $options['loc'] ), $fcoords ) ) {
            $coords = [ 'lat' => $fcoords[1], 'lng' => $fcoords[2] ];
        } else if( !( $coords = \site\utils::get_coords_from_str( $options['loc'] ) ) ) {
            setQueryLastLog( t( 'cant_get_coords', "We couldn't find this location, please try a new one." ) );
        } else {
            $_GET['loc'] = implode( ' ', $coords ); 
        }

        if( !empty( $coords ) ) {
            $query  = "SELECT s.id, s.feedID, s.user, s.category, s.popular, s.physical, s.name, s.link, s.description, s.tags, s.image, s.hours, s.phoneno, s.sellonline, s.visible, s.views, s.url_title, s.lastupdate, s.extra, s.date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons WHERE store = s.id AND visible > 0), (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products WHERE store = s.id AND visible > 0), (6378 * acos(cos(radians(?)) * cos(radians(lat)) * cos( radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance FROM " . DB_TABLE_PREFIX . "stores s RIGHT JOIN  " . DB_TABLE_PREFIX . "store_locations sl ON (sl.store = s.id) WHERE MBRContains(ST_Buffer(LineString(Point(? + ? / (111.111 / COS(RADIANS(?))), ? + ? / 111.111), Point(? - ? / ( 111.1 / COS(RADIANS(?))), ? - ? / 111.111)), 1), sl.point) ";
            
            $distance = $options['distance'] ?? 10;
            $distance = (int) $distance;
            if( $distance < 1 || $distance > 10 )
            $distance = 10;

            if( !empty( $search ) ) {
                $query .= " AND (MATCH(s.name) AGAINST (? IN BOOLEAN MODE) OR MATCH(s.tags) AGAINST (? IN BOOLEAN MODE))";
                $query .= " AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
                $query .= ' HAVING distance <= ?';
                $query .= ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) );
                $bind_t = 'dddddddddddddssd';
                $params = [ $coords['lat'], $coords['lng'], $coords['lat'], $distance, $distance, $coords['lng'], $coords['lng'], $distance, $coords['lat'], $distance, $coords['lng'], $coords['lng'], $distance,  $search, $search, $distance ];
                $dist   = true;
            } else {
                $query .= " AND s.visible > 0" . ( empty( $where ) ? '' : ' AND ' . implode( ' AND ', array_filter( $where ) ) );
                $query .= ' HAVING distance <= ?';
                $query .= ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) );
                $bind_t = 'dddddddddddddd';
                $params = [ $coords['lat'],  $coords['lng'], $coords['lat'], $distance, $distance, $coords['lng'], $coords['lng'], $distance, $coords['lat'], $distance, $coords['lng'], $coords['lng'], $distance, $distance ];
                $dist   = true;
            }

        }
    }

    $stmt->prepare( $query );
    $stmt->bind_param( $bind_t, ...$params );
    $stmt->execute();
    if( $dist )
    $stmt->bind_result( $id, $feed_id, $user, $cat, $popular, $physical, $name, $link, $description, $tags, $image, $hours, $phone, $sellonline, $visible, $views, $url_title, $last_update, $extra, $date, $reviews, $stars, $coupons, $products, $distance );
    else 
    $stmt->bind_result( $id, $feed_id, $user, $cat, $popular, $physical, $name, $link, $description, $tags, $image, $hours, $phone, $sellonline, $visible, $views, $url_title, $last_update, $extra, $date, $reviews, $stars, $coupons, $products );
    
    $data = array();

    while( $stmt->fetch() ) {

        $vals   = array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'catID' => $cat, 'name' => \site\content::title( 'stores_name_list', $name, $useem, $useec, $usefl ), 'url' => esc_html( $link ), 'description' => \site\content::content( 'stores_list', $description, $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'hours' => @unserialize( $hours ), 'phone_no' => esc_html( $phone ), 'sellonline' => (boolean) ( !$physical ? 1 : $sellonline ), 'sellonline2' => (boolean) $sellonline, 'extra' => @unserialize( $extra ), 'last_update' => $last_update, 'date' => $date, 'visible' => (boolean) $visible, 'views' => $views, 'reviews' => $reviews, 'stars' => $stars, 'coupons' => $coupons, 'products' => $products, 'is_popular' => (boolean) $popular, 'is_physical' => (boolean) $physical, 'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $name, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?store=' . $id ), 'reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $name, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $id ) );
        if( $dist )
        $vals['distance'] = $distance;
        $data[] = (object) value_with_filter( 'store_info_values', $vals );

    }

    $stmt->close();

    return $data;

    break;

    default:

    /* WHERE / ORDER BY */

    if( !empty( $categories['user'] ) ) {
        $where[] = 'user = "' . (int) $categories['user'] . '"';
    }

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
        $where[] = 'CONCAT(name, tags) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    /* */
    
    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, feedID, user, category, popular, physical, name, link, description, tags, image, hours, phoneno, sellonline, visible, views, url_title, lastupdate, extra, date, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as votes, (SELECT AVG(stars) FROM " . DB_TABLE_PREFIX . "reviews WHERE store = s.id AND valid > 0) as rating, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons WHERE store = s.id AND visible > 0), (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products WHERE store = s.id AND visible > 0) FROM " . DB_TABLE_PREFIX . "stores s" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $feed_id, $user, $cat, $popular, $physical, $name, $link, $description, $tags, $image, $hours, $phone, $sellonline, $visible, $views, $url_title, $last_update, $extra, $date, $reviews, $stars, $coupons, $products );

    $data = array();
    while( $stmt->fetch() ) {

        $data[] = (object) value_with_filter( 'store_info_values', array( 'ID' => $id, 'feedID' => $feed_id, 'userID' => $user, 'catID' => $cat, 'name' => \site\content::title( 'stores_name_list', $name, $useem, $useec, $usefl ), 'url' => esc_html( $link ), 'description' => \site\content::content( 'stores_list', $description, $useem, $usesh, false, $useec, $usefl ), 'tags' => esc_html( $tags ), 'image' => esc_html( $image ), 'hours' => @unserialize( $hours ), 'phone_no' => esc_html( $phone ), 'sellonline' => (boolean) ( !$physical ? 1 : $sellonline ), 'sellonline2' => (boolean) $sellonline, 'extra' => @unserialize( $extra ), 'last_update' => $last_update, 'date' => $date, 'visible' => (boolean) $visible, 'views' => $views, 'reviews' => $reviews, 'stars' => $stars, 'coupons' => $coupons, 'products' => $products, 'is_popular' => (boolean) $popular, 'is_physical' => (boolean) $physical, 'url_title' => esc_html( $url_title ), 'link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_store, $name, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?store=' . $id ), 'reviews_link' => ( $seo_link ? \site\utils::make_seo_link( $seo_link_reviews, $name, $url_title, $id, $extension ) : $GLOBALS['siteURL'] . '?reviews=' . $id ) ) );

    }

    $stmt->close();

    return $data;

    break;

    }

}

/* NUMBER OF REWARDS */

public static function have_rewards( $category = array(), $special = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'CONCAT(title, description) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = strtolower( $categories['show'] );
        switch( $show ) {
            case 'all': break;
            case 'active': $where[] = 'visible > 0'; break;
        }
    } else {
        $where[] = 'visible > 0';
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "rewards" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

/*  FETCH THE REWARDS */

public static function while_rewards( $category = array() ) {

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
        $where[] = 'CONCAT(title, description) REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = strtolower( $categories['show'] );
        switch( $show ) {
            case 'all': break;
            case 'active': $where[] = 'visible > 0'; break;
        }
    } else {
        $where[] = 'visible > 0';
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
                case 'points': $orderby[] = 'points'; break;
                case 'points desc': $orderby[] = 'points DESC'; break;
            }
        }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, user, points, title, description, image, fields, visible, date FROM " . DB_TABLE_PREFIX . "rewards" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $user, $points, $title, $description, $image, $fields, $visible, $date );

    $data = array();
    while( $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'user' => $user, 'points' => $points, 'title' => esc_html( $title ), 'description' => esc_html( $description ), 'image' => esc_html( $image ), 'fields' => @unserialize( $fields ), 'visible' => (boolean) $visible, 'date' => $date );
    }

    $stmt->close();

    return $data;

}

/*  NUMBER OF REWARD REQUESTS */

public static function have_rewards_reqs( $category = array(), $special = array() ) {

    global $db;

    $categories = \site\utils::validate_user_data( $category );

    $where = array();

    /* WHERE / ORDER BY */

    if( !empty( $categories['user'] ) ) {
        $where[] = 'user = "' . (int) $categories['user'] . '"';
    }

    if( !empty( $categories['reward'] ) ) {
        $where[] = 'reward = "' . (int) $categories['reward'] . '"';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'fields REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = strtolower( $categories['show'] );
        switch( $show ) {
            case 'valid': $where[] = 'claimed = 1'; break;
            case 'notvalid': $where[] = 'claimed = 0'; break;
        }
    }

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "rewards_reqs" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) );
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

/*  FETCH THE REWARDS REQUESTS */

public static function while_rewards_reqs( $category = array() ) {

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

    if( !empty( $categories['user'] ) ) {
        $where[] = 'user = "' . (int) $categories['user'] . '"';
    }

    if( !empty( $categories['reward'] ) ) {
        $where[] = 'reward = "' . (int) $categories['reward'] . '"';
    }

    if( !empty( $categories['search'] ) ) {
        $search = implode( '.*', explode( ' ', trim( $categories['search'] ) ) );
        $where[] = 'fields REGEXP "' . \site\utils::dbp( $search ) . '"';
    }

    if( isset( $categories['show'] ) ) {
        $show = strtolower( $categories['show'] );
        switch( $show ) {
            case 'valid': $where[] = 'claimed = 1'; break;
            case 'notvalid': $where[] = 'claimed = 0'; break;
        }

    }

    if( isset( $categories['orderby'] ) ) {
        $order = array_map( 'trim', explode( ',', strtolower( $categories['orderby'] ) ) );
        foreach( $order as $v ) {
            switch( $v ) {
                case 'rand': $orderby[] = 'RAND()'; break;
                case 'date': $orderby[] = 'date'; break;
                case 'date desc': $orderby[] = 'date DESC'; break;
                case 'points': $orderby[] = 'points'; break;
                case 'points desc': $orderby[] = 'points DESC'; break;
            }
        }
    }

    /*
    */

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, name, user, points, reward, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "rewards WHERE id = r.reward), fields, claimed, date FROM " . DB_TABLE_PREFIX . "rewards_reqs r" . ( empty( $where ) ? '' : ' WHERE ' . implode( ' AND ', array_filter( $where ) ) ) . ( empty( $orderby ) ? '' : ' ORDER BY ' . implode( ', ', array_filter( $orderby ) ) ) . ( empty( $limit ) ? '' : ' LIMIT ' . implode( ',', $limit ) ) );
    $stmt->execute();
    $stmt->bind_result( $id, $name, $user, $points, $reward, $reward_exists, $fields, $claimed, $date );

    $data = array();
    while( $stmt->fetch() ) {
        $data[] = (object) array( 'ID' => $id, 'name' => esc_html( $name ), 'user' => $user, 'points' => $points, 'reward' => $reward, 'reward_exists' => ( $reward_exists > 0 ? true : false ), 'fields' => @unserialize( $fields ), 'claimed' => (boolean) $claimed, 'date' => $date );
    }

    $stmt->close();

    return $data;

}

}