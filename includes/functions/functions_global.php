<?php

/* SETTINGS: ITEMS PER PAGE */

function items_per_page() {
    return \query\main::get_option( 'items_per_page' );
}

/* SETTINGS: ALLOW REVIEWS */

function allow_reviews() {
    return (boolean) \query\main::get_option( 'allow_reviews' );
}

/* CHECK IF HAVE ITEMS */

function have_items_cat( $cat ) {
    return \query\main::have_items( $cat );
}

/* SHOW GROUPED CATEGORIES */

function all_grouped_categories() {
    return \query\main::group_categories( array( 'max' => 0 ) );
}

/* CHECK IF HAVE CUSTOM CATEGORIES */

function have_categories_custom( $category = array(), $special = array() ) {
    return \query\main::have_categories( $category, $special );
}

/* CHECK IF HAVE CUSTOM CATEGORIES */

function categories_custom( $category = array(), $special = array() ) {
    return \query\main::while_categories( $category, $special );
}

/* CHECK IF CATEGORY EXISTS */

function category_exists( $id = 0 ) {
    return \query\main::category_exists( $id );
}

/* INFO CATEGORY */

function category_info( $id = 0, $special = array() ) {
    return \query\main::category_info( $id, $special );
}

/* SHOW CUSTOM CATEGORIES */

function grouped_categories_custom( $category = array() ) {
    return \query\main::group_categories( $category );
}

/* SHOW PAGES */

function all_pages() {
    return \query\main::while_pages( array( 'max' => 0 ) );
}

/* CHECK IF HAVE CUSTOM PAGES */

function have_pages_custom( $category = array() ) {
    return \query\main::have_pages( $category );
}

/* SHOW CUSTOM PAGES */

function pages_custom( $category = array() ) {
    return \query\main::while_pages( $category );
}

/* CHECK IF PAGE EXISTS */

function page_exists( $id = 0, $special = array() ) {
    return \query\main::page_exists( $id, $special );
}

/* INFO PAGE */

function page_info( $id = 0, $special = array() ) {
    return \query\main::page_info( $id, $special );
}

/* CHECK IF HAVE CUSTOM ITEMS */

function have_items_custom( array $category = [], string $type = '', array $special = [], array $options = [] ) {
    return \query\main::have_items( $category, $type, $special, $options );
}

/* SHOW CUSTOM ITEMS */

function items_custom( array $category, string $type = '', array $special = [], array $options = [] ) {
    return \query\main::while_items( $category, $type, $special, $options );
}

/* CHECK IF ITEM EXISTS */

function item_exists( $id = 0, $special = array() ) {
    return \query\main::item_exists( $id, $special );
}

/* INFO ITEM */

function item_info( $id = 0, $special = array() ) {
    return \query\main::item_info( $id, $special );
}

/* CHECK IF HAVE CUSTOM PRODUCTS */

function have_products_custom( array $category = [], string $type = '', array $special = [], array $options = [] ) {
    return \query\main::have_products( $category, $type, $special, $options );
}

/* SHOW CUSTOM PRODUCTS */

function products_custom( array $category = [], string $type = '', array $special = [], array $options = [] ) {
    return \query\main::while_products( $category, $type, $special, $options );
}

/* CHECK IF PRODUCT EXISTS */

function product_exists( $id = 0, $special = array() ) {
    return \query\main::product_exists( $id, $special );
}

/* INFO PRODUCT */

function product_info( $id = 0, $special = array() ) {
    return \query\main::product_info( $id, $special );
}

/* CHECK IF HAVE CUSTOM STORES */

function have_stores_custom( array $category = [], string $type = '', array $special = [], array $options = [] ) {
    return \query\main::have_stores( $category, $type, $special, $options );
}

/* SHOW CUSTOM STORES */

function stores_custom( array $category = [], string $type = '', array $special = [], array $options = [] ) {
    return \query\main::while_stores( $category, $type, $special, $options );
}

/* CHECK IF HAVE STORES BY LOCATION */

function have_stores_by_location( $category = array(), $special = array() ) {
    return \query\items_by_location::have_stores( $category, '', $special );
}

/* SHOW CUSTOM STORES BY LOCATION */

function stores_by_location( $category = array(), $special = array() ) {
    return \query\items_by_location::while_stores( $category, '', $special );
}

/* CHECK IF HAVE COUPONS BY LOCATION */

function have_items_by_location( $category = array(), $special = array() ) {
    return \query\items_by_location::have_items( $category, '', $special );
}

/* SHOW CUSTOM COUPONS BY LOCATION */

function items_by_location( $category = array(), $special = array() ) {
    return \query\items_by_location::while_items( $category, '', $special );
}

/* CHECK IF HAVE PRODUCTS BY LOCATION */

function have_products_by_location( $category = array(), $special = array() ) {
    return \query\items_by_location::have_products( $category, '', $special );
}

/* SHOW CUSTOM PRODUCTS BY LOCATION */

function products_by_location( $category = array(), $special = array() ) {
    return \query\items_by_location::while_products( $category, '', $special );
}

/* CHECK IF STORE EXISTS */

function store_exists( $id = 0, $special = array() ) {
    return \query\main::store_exists( $id, $special );
}

/* INFO STORE */

function store_info( $id = 0, $special = array() ) {
    return \query\main::store_info( $id, $special );
}

/* SHOW STORE LOCATIONS */

function store_locations( $id, $category = array() ) {
    return \query\locations::while_store_locations( array_merge( array( 'max' => 0, 'store' => $id ), $category ) );
}

/* CHECK IF HAVE CUSTOM REVIEWS */

function have_reviews_custom( $category = array()    ) {
    return \query\main::have_reviews( $category );
}

/* SHOW CUSTOM REVIEWS */

function reviews_custom( $category = array() ) {
    return \query\main::while_reviews( $category );
}

/* CHECK IF HAVE CUSTOM USERS */

function have_users_custom( $category = array()    ) {
    return \query\main::have_users( $category );
}

/* SHOW CUSTOM USERS */

function users_custom( $category = array() ) {
    return \query\main::while_users( $category );
}

/* CHECK IF USER EXISTS */

function user_exists( $id = 0 ) {
    return \query\main::user_exists( $id );
}

/* INFO USER */

function user_info( $id = 0 ) {
    return \query\main::user_info( $id );
}

/* CHECK IF HAVE REWARDS */

function have_rewards( $category = array()    ) {
    return \query\main::have_rewards( $category );
}

/* SHOW REWARDS */

function rewards( $category = array() ) {
    return \query\main::while_rewards( $category );
}

/* CHECK IF HAVE PAYMENT PLANS */

function have_payment_plans( $category = array()    ) {
    return \query\payments::have_plans( $category );
}

/* SHOW PAYMENT PLANS */

function payment_plans( $category = array() ) {
    return \query\payments::while_plans( $category );
}

/* SHOW USER AVATAR */

function user_avatar( $text ) {
    return \query\main::user_avatar( $text );
}

/* SHOW STORE AVATAR */

function store_avatar( $text ) {
    return \query\main::store_avatar( $text );
}

/* DISPLAY IMAGE */

function image( $text ) {
    if( filter_var( $text, FILTER_VALIDATE_URL ) ) {
        return $text;
    }
    return $GLOBALS['siteURL'] . $text;
}

/* SHOW PRODUCT AVATAR */

function product_avatar( $text ) {
    return \query\main::product_avatar( $text );
}

/* SHOW REWARD AVATAR */

function reward_avatar( $text ) {
    return \query\main::reward_avatar( $text );
}

/* PAYMENT PLAN AVATAR */

function payment_plan_avatar( $text ) {
    return \query\main::payment_plan_avatar( $text );
}

/* SITE LOGO */

function site_logo( $fallback = '' ) {
    $logo = \query\main::get_option( 'site_logo' );
    if( !empty( $logo ) ) {
        return esc_html( $logo );
    }

    return $fallback;
}

/** SET QUERY LAST LOG */

function setQueryLastLog( string $log ) {
    global $queryLastLog;
    $queryLastLog = $log;
}

function getQueryLastLog() {
    global $queryLastLog;
    return $queryLastLog;
}

/* ADD HEAD */

function add_head() {

    $cache = new \cache\main;

    if( $show_from_cache = $cache->check( 'theme_head' ) ) {

        return $show_from_cache;

    } else {

    global $add_styles, $add_scripts, $add_inline_style, $add_to_head;

    $head = '<meta http-equiv="Content-Type" content="text/html; charset=' . meta_charset(). '" />' . "\r\n";
    $head .= '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />' . "\r\n";
    $ico = value_with_filter( 'site-favicon-tag', option( 'site_favicon' ) );
    if( !empty( $ico ) ) {
        $head .= '<link rel="icon" href="' . esc_html( $ico ) . '" type="image/x-icon" />' . "\r\n";
    }
    $head .= "<title>" . value_with_filter( 'site-title-tag', meta_title() ) . "</title>\r\n";
    $head .= '<meta name="description" content="' . value_with_filter( 'meta-description', meta_description() ) . '" />' . "\r\n";
    $head .= '<meta name="keywords" content="' . value_with_filter( 'meta-keywords', meta_keywords() ) . '" />' . "\r\n";
    $head .= '<meta property="og:title" content="' . value_with_filter( 'og-meta-title', meta_title() ) . '" />' . "\r\n";
    $head .= '<meta property="og:description" content="' . value_with_filter( 'og-meta-description', meta_description() ) . '" />' . "\r\n";
    $head .= '<meta property="og:image" content="' . value_with_filter( 'og-meta-image', meta_image( option( 'site_logo' ) ) ) . '" />' . "\r\n";
    $head .= '<meta name="robots" content="' .    ( (boolean) option( 'site_indexfollow' ) ? 'index, follow' : 'noindex, nofollow' ) . '" />' . "\r\n";

    if( is_array( $add_styles ) ) {
        foreach( $add_styles as $style => $options ) {
            $head .= '<link href="' . $style . '"' . ( !empty( $options ) ? \site\utils::build_atts( $options, ' ' ) : '' ) . ' />';
            $head .= "\r\n";
        }
    }

    if( is_array( $add_scripts ) ) {
        foreach( $add_scripts as $script => $options ) {
            if( filter_var( $script, FILTER_VALIDATE_URL ) )
            $head .= '<script src="' . $script . '"' . ( !empty( $options ) ? \site\utils::build_atts( $options, ' ' ) : '' ) . '></script>';
            else {
                $head .= $script;
            }
            $head .= "\r\n";
        }
    }

    if( file_exists( COMMON_LOCATION . '/head.html' ) ) {
        $head .= $common_content = @file_get_contents( COMMON_LOCATION . '/head.html' );
        if( !empty( $common_content ) ) $head .= "\r\n";
    }

    if( is_array( $add_inline_style ) ) {
        $head .= "<style>\r\n";
        foreach( $add_inline_style as $inline_style ) {
            $head .= $inline_style . "\r\n";
        }
        $head .= "</style>\r\n";
    }

    if( is_array( $add_to_head ) ) {
        foreach( $add_to_head as $custom_head_text ) {
            if( is_callable( $custom_head_text ) ) {
                $head .= call_user_func( $custom_head_text );
            } else {
                if( !empty( $custom_head_text ) )
                $head .= $custom_head_text . "\r\n";
            }
        }
    }

    $cache->add( 'theme_head', $head );

    return $head;

    }

}

/* BODY CLASSES */

function body_classes() {
    global $add_body_class;

    return ( !empty( $add_body_class ) ? implode( ' ', array_keys( $add_body_class ) ) : false );
}

/* METATAGS - TITLE */

if( !function_exists( 'meta_title' ) ) {

    function meta_title() {

        return meta_default( '', \query\main::get_option( 'sitetitle' ) );

    }

}

/* METATAGS - KEYWORDS */

if( !function_exists( 'meta_keywords' ) ) {

    function meta_keywords() {

        return meta_default( '', \query\main::get_option( 'meta_keywords' ) );

    }

}

/* METATAGS - DESCRIPTION */

if( !function_exists( 'meta_description' ) ) {

    function meta_description() {

        return meta_default( '', \query\main::get_option( 'meta_description' ) );

    }

}

/* METATAGS - IMAGE */

if( !function_exists( 'meta_image' ) ) {

    function meta_image( $image = '' ) {

        return $image;

    }

}

/* METATAGS - DEFAULT */

function meta_default( $list = array(), $text = '' ) {

    if( empty( $list ) ) {
        $list = array( '%YEAR%' => date('Y'), '%MONTH%' => date('F') );
    }

    return str_replace( array_keys( $list ), array_values( $list ), esc_html( $text ) );

}

/* DEFAULT VALUE FOR CURRENT PAGE */

if( !function_exists( 'this_is_page' ) ) {

    function this_is_page() {

        return false;

    }

}

if( !function_exists( 'this_is_template_page' ) ) {

    function this_is_template_page() {

        return false;

    }

}

if( !function_exists( 'this_is_store' ) ) {

    function this_is_store() {

        return false;

    }

}

if( !function_exists( 'this_is_stores_page' ) ) {

    function this_is_stores_page() {

        return false;

    }

}

if( !function_exists( 'this_is_reviews_page' ) ) {

    function this_is_reviews_page() {

        return false;

    }

}

if( !function_exists( 'this_is_coupon' ) ) {

    function this_is_coupon() {

        return false;

    }

}

if( !function_exists( 'this_is_product' ) ) {

    function this_is_product() {

        return false;

    }

}

if( !function_exists( 'this_is_category' ) ) {

    function this_is_category() {

        return false;

    }

}

if( !function_exists( 'this_is_search_page' ) ) {

    function this_is_search_page() {

        return false;

    }

}

if( !function_exists( 'this_is_404_page' ) ) {

    function this_is_404_page() {

        return false;

    }

}

if( !function_exists( 'this_is_user_section' ) ) {

    function this_is_user_section() {

        return false;

    }

}

if( !function_exists( 'this_is_home_page' ) ) {

    function this_is_home_page() {

        if( this_is_page() || this_is_template_page() || this_is_store() || this_is_stores_page() || this_is_reviews_page() ||
        this_is_coupon() || this_is_product() || this_is_category() || this_is_search_page() || this_is_404_page() || this_is_user_section() ) {
            return false;
        }

        return true;

    }

}

/* THEME MENU */

function site_menu( $location = '' ) {

    $menu = new \site\menu( $location );

    return $menu->build_links();

}

/* CHECK IF THERE ARE FILLED INFORMATION TO LOGIN WITH GOOGLE+ */

function google_login() {

    if( \query\main::get_option( 'google_clientID' ) === '' || \query\main::get_option( 'google_secret' ) === '' || \query\main::get_option( 'google_ruri' ) === '' ) {
        return false;
    }

    return true;

}

/* CHECK IF THERE ARE FILLED INFORMATION FOR GOOGLE MAPS */

function google_maps() {

    if( \query\main::get_option( 'google_maps_key' ) === '' ) {
        return false;
    }

    return true;

}

/* CHECK IF THERE ARE FILLED INFORMATION TO LOGIN WITH FACEBOOK */

function facebook_login() {

    if( \query\main::get_option( 'facebook_appID' ) === '' || \query\main::get_option( 'facebook_secret' ) === '' ) {
        return false;
    }

    return true;

}

/* LANGUAGES */

function languages() {
    global $LANG;
    return $LANG['$languages'];
}

function current_language() {
    global $LANG;
    return $LANG['$current'];
}

/* PAYMENT GATEWAYS */

function payment_gateways() {
    return \site\payment::gateways();
}

/* PRICES */

function prices( $out = 'array' ) {
    $prices = array( 'store' => \query\main::get_option( 'price_store' ), 'coupon' => \query\main::get_option( 'price_coupon' ), 'coupon_max_days' => \query\main::get_option( 'price_max_days' ), 'product' => \query\main::get_option( 'price_product' ), 'product_max_days' => \query\main::get_option( 'price_product_max_days' ) ); ;
    if( $out == 'object' ) {
        return (object) $prices;
    }
    return $prices;
}

/* CHECK IF USER IT'S LOGGED */

function logout() {
    return \user\main::logout();
}

/* CHECK IF A STORE IS SAVED TO FAVORITES */

function is_favorite( $id = 0 ) {

    global $GET;

    $id = empty( $id ) && isset( $GET['id'] ) ? $GET['id'] : $id;
    if( empty( $id ) ) {
        return false;
    }

    if( $GLOBALS['me'] ) {
        return \user\main::check_favorite( $GLOBALS['me']->ID, $id );
    } else {
        return false;
    }

}

/* CHECK IF A STORE/COUPON OR PRODUCT IS SAVED */

function is_saved( $id = 0, $type = '' ) {

    global $GET;

    $id = empty( $id ) && isset( $GET['id'] ) ? $GET['id'] : $id;
    if( empty( $id ) ) {
        return false;
    }

    if( $GLOBALS['me'] ) {
        return \user\main::check_saved( $GLOBALS['me']->ID, $id, $type );
    } else {
        return false;
    }

}

/* CHECK IF A COUPON IS CLAIMED */

function is_coupon_claimed( $id = 0 ) {

    global $GET;

    $id = empty( $id ) && isset( $GET['id'] ) ? $GET['id'] : $id;
    if( empty( $id ) ) {
        return false;
    }

    if( $GLOBALS['me'] ) {
        return \user\main::check_coupon_claimed( $GLOBALS['me']->ID, $id );
    } else {
        return false;
    }

}

/* NUMBER OF ITEMS */

function site_count( $type = '', $category = array() ) {

    switch( $type ) {
        case 'store':
        case 'stores':
        return \query\main::stores( $category );
        break;
        case 'coupon':
        case 'coupons':
        return \query\main::coupons( $category );
        break;
        case 'product':
        case 'products':
        return \query\main::products( $category );
        break;
        case 'review':
        case 'reviews':
        return \query\main::reviews( $category );
        break;
        case 'user':
        case 'users':
        return \query\main::users( $category );
        break;
        case 'category':
        case 'categories':
        return \query\main::categories( $category );
        break;
        default:
        return 'NaN';
        break;
    }

}

/* PROFILES ON SOCIAL NETWORKS */

function social_networds() {

    $profile = array();
    if( ( $facebook = \query\main::get_option( 'social_facebook' ) ) && !empty( $facebook ) ) {
        $profile['facebook'] = $facebook;
    }
    if( ( $google = \query\main::get_option( 'social_google' ) ) && !empty( $google ) ) {
        $profile['google'] = $google;
    }
    if( ( $twitter = \query\main::get_option( 'social_twitter' ) ) && !empty( $twitter ) ) {
        $profile['twitter'] = $twitter;
    }
    if( ( $flickr = \query\main::get_option( 'social_flickr' ) ) && !empty( $flickr ) ) {
        $profile['flickr'] = $flickr;
    }
    if( ( $linkedin = \query\main::get_option( 'social_linkedin' ) ) && !empty( $linkedin ) ) {
        $profile['linkedin'] = $linkedin;
    }
    if( ( $vimeo = \query\main::get_option( 'social_vimeo' ) ) && !empty( $vimeo ) ) {
        $profile['vimeo'] = $vimeo;
    }
    if( ( $youtube = \query\main::get_option( 'social_youtube' ) ) && !empty( $youtube ) ) {
        $profile['youtube'] = $youtube;
    }
    if( ( $myspace = \query\main::get_option( 'social_myspace' ) ) && !empty( $myspace ) ) {
        $profile['myspace'] = $myspace;
    }
    if( ( $reddit = \query\main::get_option( 'social_reddit' ) ) && !empty( $reddit ) ) {
        $profile['reddit'] = $reddit;
    }
    if( ( $pinterest = \query\main::get_option( 'social_pinterest' ) ) && !empty( $pinterest ) ) {
        $profile['pinterest'] = $pinterest;
    }
    return $profile;

}

/* SHOW WIDGET */

function show_widgets( $id ) {

    global $add_widgets_zone;
    $zones = array();
    if( !empty( $add_widgets_zone ) && is_array( $add_widgets_zone ) ) {
        $zones = $add_widgets_zone;
    }
    if( function_exists( 'register_widgets' ) ) {
      $zones += register_widgets();
    }

    if( !empty( $zones ) ) {
        if( in_array( $id, array_keys( $zones ) ) ) {

            $data = \query\main::show_widgets( $id );

            if( empty( $data ) ) {
                return false;
            }

            ob_start();
            foreach( $data as $k => $v ) {
                list( $ID, $title, $limit, $type, $order, $content, $extra, $mobile_view ) = array( $v['ID'], $v['title'], $v['limit'], $v['type'], $v['orderby'], $v['content'], $v['extra'], $v['mobile_view'] );
                @require $v['file'];
            }
            $output = ob_get_contents();
            ob_end_clean();

            return $output;
        }
    }

    return false;

}

/* LOGIN FORM */

function login_form( $redirect_url = '', $loc = 'regular', bool $ajax = false ) {

    // require captcha?
    $captcha = (boolean) \query\main::get_option( 'login_captcha' );

    $form = '<div class="login_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['data']['csrf'] ) && \site\utils::check_csrf( $_POST['data']['csrf'], 'login_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['data'] );

    if( !$captcha || ( isset( $_SESSION['captcha']['code'] ) && isset( $pd['captcha'] ) && $_SESSION['captcha']['code'] == $pd['captcha'] ) ) {

    try {

        $session = \user\main::login( $pd );

        $_SESSION['session'] = $session;

        $form .= '<div class="success">' . t( 'login_success', "You have successfully logged in." ) . '</div>';
        $form .= '<meta http-equiv="refresh" content="2; url='. $GLOBALS['siteURL'] . 'setSession.php' . ( !empty( $redirect_url ) ? '?back=' . base64_encode( $redirect_url ) : '' ) . '">';

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    } else
        $form .= '<div class="error">' . t( 'msg_invalidcaptcha', "Wrong security code." ) . '</div>';

    }

    $csrf = $_SESSION['login_csrf'] = \site\utils::str_random(12);

    if( $captcha ) {

        require_once DIR . '/' . LBDIR . '/captcha-master/captcha.php';

        $_SESSION['captcha'] = simple_php_captcha( array('location' => $loc) );

    }

    $form .= '<form method="POST" action="#"' . ( $ajax ? ' data-ajax="' . ajax_call_url( "login" ) . '"' : '' ) . '>';

    // Username
    $fields['username']['position'] = 1;
    $fields['username']['markup'] = '<div class="form_field"><label for="data[username]">' . t( 'form_email', "Email Address" ) . ':</label> <div><input type="email" name="data[username]" id="data[username]" value="' . ( isset( $pd['username'] ) ? $pd['username'] : '' ) . '" required /></div></div>';

    // Password
    $fields['password']['position'] = 2;
    $fields['password']['markup'] = '<div class="form_field"><label for="data[password]">' . t( 'form_password', "Password" ) . ':</label> <div><input type="password" name="data[password]" id="data[password]" value="" required /></div></div>';

    // Keep Logged
    $fields['keep_logged']['position'] = 3;
    $fields['keep_logged']['markup'] = '<div class="form_field no-label"><input type="checkbox" name="data[keep_logged]" id="data[keep_logged]" class="inputCheckbox" /> <label for="data[keep_logged]">' . t( 'msg_keep_log', "Keep me logged!" ) . '</label></div>';

    if( $captcha ) {

        // Captcha
        $fields['captcha']['position'] = 4;
        $fields['captcha']['markup'] = '<div class="form_field"><label for="data[captcha]">' . t( 'form_securitycheck', "Security Check" ) . ':</label> <div><img src="' . $_SESSION['captcha']['image_src'] . '" alt="CAPTCHA code"> <input type="text" name="data[captcha]" id="data[captcha]"    placeholder="' . t( 'form_securitycheck_ph', "Enter the characters from the image" ) . '" required /></div></div>';

    }

    global $add_form_fields;

    $custom_fields = array();
    if( !empty( $add_form_fields['login'] ) && is_array( $add_form_fields) ) {
        foreach( $add_form_fields['login'] as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $custom_fields[$id]['markup'] = \site\fields::build_extra( $link['fields'], ( isset( $pd['extra'] ) ? $pd['extra'] : array() ), 'data[extra]' );
        }
    }

    $fields = value_with_filter( 'default_user_register_fields', $fields ) + $custom_fields;

    uasort( $fields, function( $a, $b ) {
        if( (double) $a['position'] === (double) $b['position'] ) return 0;
        return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
    } );

    foreach( $fields as $key => $f ) {
        $form .= $f['markup'];
    }

    $form .= '<input type="hidden" name="data[csrf]" value="' . $csrf . '" />
    <button>' . t( 'login', "Login" ) . '</button>
    </form>

    </div>';

    return $form;

}

/* REGISTER FORM */

function register_form( $redirect_url = '', $loc = 'regular' ) {

    if( \query\main::get_option( 'registrations' ) == 'opened' ) {

    // require captcha?
    $captcha = (boolean) \query\main::get_option( 'register_captcha' );

    $form = '<div class="register_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['data']['csrf'] ) && \site\utils::check_csrf( $_POST['data']['csrf'], 'register_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['data'] );
    $pd['extra'] = value_with_filter( 'user_save_register_extra_fields', ( isset( $pd['extra'] ) ? \site\utils::array_sanitize( $pd['extra'] ) : array() ), array() );

    if( !$captcha || ( isset( $_SESSION['captcha']['code'] ) && isset( $pd['captcha'] ) && $_SESSION['captcha']['code'] == $pd['captcha'] ) ) {

    try {

        $session = \user\main::register( $pd );

        $_SESSION['session'] = $session;

        $form .= '<div class="success">' . t( 'register_success', "You have successfully registered." ) . '</div>';
        $form .= '<meta http-equiv="refresh" content="2; url='. $GLOBALS['siteURL'] . 'setSession.php' . ( !empty( $redirect_url ) ? '?back=' . base64_encode( $redirect_url ) : '' ) . '">';

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    } else
        $form .= '<div class="error">' . t( 'msg_invalidcaptcha', "Wrong security code." ) . '</div>';

    }

    $csrf = $_SESSION['register_csrf'] = \site\utils::str_random(12);


    if( $captcha ) {

        require_once DIR . '/' . LBDIR . '/captcha-master/captcha.php';

        $_SESSION['captcha'] = simple_php_captcha( array( 'location' => $loc ) );

    }

    $form .= '<form method="POST" action="#">';

    // Username
    $fields['username']['position'] = 1;
    $fields['username']['markup'] = '<div class="form_field"><label for="data[username]">' . t( 'form_name', "Name" ) . ':</label> <div><input type="text" name="data[username]" id="data[username]" value="' . ( isset( $pd['username'] ) ? $pd['username'] : '' ) . '" required /></div></div>';

    // Email
    $fields['email']['position'] = 2;
    $fields['email']['markup'] = '<div class="form_field"><label for="data[email]">' . t( 'form_email', "Email Address" ) . ':</label> <div><input type="email" name="data[email]" id="data[email]" value="' . ( isset( $pd['email'] ) ? $pd['email'] : '' ) . '" required /></div></div>';

    // Password
    $fields['password']['position'] = 3;
    $fields['password']['markup'] = '<div class="form_field"><label for="data[password]">' . t( 'form_password', "Password" ) . ':</label> <div><input type="password" name="data[password]" id="data[password]" value="" required /></div></div>';

    // Password Again
    $fields['password2']['position'] = 4;
    $fields['password2']['markup'] = '<div class="form_field"><label for="data[password2]">' . t( 'form_password_again', "Confirm Password" ) . ':</label> <div><input type="password" name="data[password2]" id="data[password2]" value="" required /></div></div>';

    if( $captcha ) {

        // Captcha
        $fields['captcha']['position'] = 5;
        $fields['captcha']['markup'] = '<div class="form_field"><label for="data[captcha]">' . t( 'form_securitycheck', "Security Check" ) . ':</label> <div><img src="' . $_SESSION['captcha']['image_src'] . '" alt="CAPTCHA code"> <input type="text" name="data[captcha]" id="data[captcha]" placeholder="' . t( 'form_securitycheck_ph', "Enter the characters from the image" ) . '" required /></div></div>';

    }

    global $add_form_fields;

    $custom_fields = array();
    if( !empty( $add_form_fields['register'] ) && is_array( $add_form_fields) ) {
        foreach( $add_form_fields['register'] as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $custom_fields[$id]['markup'] = \site\fields::build_extra( $link['fields'], ( isset( $pd['extra'] ) ? $pd['extra'] : array() ), 'data[extra]' );
        }
    }

    $fields = value_with_filter( 'default_user_register_fields', $fields ) + $custom_fields;

    uasort( $fields, function( $a, $b ) {
        if( (double) $a['position'] === (double) $b['position'] ) return 0;
        return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
    } );

    foreach( $fields as $key => $f ) {
        $form .= $f['markup'];
    }

    $form .= '<input type="hidden" name="data[csrf]" value="' . $csrf . '" />
    <button>' . t( 'register', "Register" ) . '</button>
    </form>

    </div>';

    return $form;

    } else return '<div class="info_form">' . t( 'register_not_allowed', "Registrations are not allowed at this time." ) . '</div>';

}

/* FORGOT PASSWORD FORM */

function forgot_password_form() {

    global $_GET;

    $form = '<div class="forgot_password_form other_form">';

    if( isset( $_GET['uid'] ) && isset( $_GET['session'] ) && \user\mail_sessions::check( 'password_recovery', array( 'user' => $_GET['uid'], 'session' => $_GET['session'] ) )) {

    /* RESET PASSWORD FORM */

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['forgot_password'] ) && \site\utils::check_csrf( $_POST['forgot_password']['csrf'], 'forgot_password_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['forgot_password'] );

    try {

        \user\main::reset_password( $_GET['uid'], $pd );
        $form .= '<div class="success">' . t( 'reset_pwd_success', "Your password has been set, you can login now." ) . '</div>';

        \user\mail_sessions::clear( 'password_recovery', array( 'user' => $_GET['uid'] ) );

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    }

    $csrf = $_SESSION['forgot_password_csrf'] = \site\utils::str_random(12);

    $form .= '<form method="POST" action="#">
    <div class="form_field"><label for="forgot_password[email]">' . t( 'change_pwd_form_new', "New Password" ) . ':</label> <div><input type="password" name="forgot_password[password1]" id="forgot_password[password1]" value="" required /></div></div>
    <div class="form_field"><label for="forgot_password[email]">' . t( 'change_pwd_form_new2', "Confirm New Password" ) . ':</label> <div><input type="password" name="forgot_password[password2]" id="forgot_password[password2]" value="" required /></div></div>
    <input type="hidden" name="forgot_password[csrf]" value="' . $csrf . '" />
    <button>' . t( 'reset_pwd_button', "Reset Password" ) . '</button>
    </form>';

    } else {

    /* SEND A SESSION TO HIS EMAIL ADDRESS FORM */

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['forgot_password'] ) && \site\utils::check_csrf( $_POST['forgot_password']['csrf'], 'forgot_password_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['forgot_password'] );

    try {

        \user\main::recovery_password( $_POST['forgot_password'] );
        $form .= '<div class="success">' . t( 'fp_success', "An email has been sent, please check your inbox!" ) . '</div>';

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    }

    $csrf = $_SESSION['forgot_password_csrf'] = \site\utils::str_random(12);

    $form .= '<form method="POST" action="#">
    <div class="form_field"><label for="forgot_password[email]">' . t( 'form_email', "Email Address" ) . ':</label> <div><input type="email" name="forgot_password[email]" id="forgot_password[email]" value="' . ( isset( $pd['email'] ) ? $pd['email'] : '' ) . '" required /></div></div>
    <input type="hidden" name="forgot_password[csrf]" value="' . $csrf . '" />
    <button>' . t( 'recovery', "Recovery" ) . '</button>
    </form>';

    }

    $form .= '</div>';

    return $form;

}

/* POST REVIEW FORM */

function write_review_form( $id = 0, bool $ajax = false ) {

    global $GET;

    if( empty( $id ) && isset( $GET['id'] ) ) {
        $id = $GET['id'];
    }

    if( $GLOBALS['me'] && !empty( $id ) ) {

    if( ! (boolean) \query\main::get_option( 'allow_reviews' ) ) {

        return '<div class="info_form">' . t( 'review_not_allowed', "New reviews are not allowed at this time." ) . '</div>';

    }

    $form = '<div class="write_review_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['write_review_form'] ) && \site\utils::check_csrf( $_POST['write_review_form']['csrf'], 'write_review_form_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['write_review_form'] );

    try {

        \user\main::write_review( $id, $GLOBALS['me']->ID, $pd );
        $form .= '<div class="success">' . t( 'review_sent', "Thank you! Your review has been sent." ) . '</div>';

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    }

    $csrf = $_SESSION['write_review_form_csrf'] = \site\utils::str_random(12);

    $form .= '<form method="POST" action="#"' . ( $ajax ? ' data-ajax="' . ajax_call_url( "write_review" ) . '"' : '' ) . '>
    <div class="form_field"><label for="write_review_form[stars]">' . t( 'form_stars', "Rating" )    . ':</label> <div><select name="write_review_form[stars]" id="write_review_form[stars]">
    <option value="5">5</option>
    <option value="4">4</option>
    <option value="3">3</option>
    <option value="2">2</option>
    <option value="1">1</option>
    </select></div></div>
    <div class="form_field"><label for="write_review_form[text]">' . t( 'form_text', "Text" )    . ':</label> <div><textarea name="write_review_form[text]" id="write_review_form[text]" required></textarea></div></div>
    <input type="hidden" name="write_review_form[csrf]" value="' . $csrf . '" />
    <input type="hidden" name="write_review_form[store_id]" value="' . (int) $id . '" />
    <button>' . t( 'post_review', "Post review" )    . '</button>
    </form>

    </div>';

    return $form;

    } else return '<div class="info_form">' . t( 'unavailable_form', "This form is only for logged members!" ) . '</div>';

}

/* SUGGEST STORE FORM */

function suggest_store_form( $auto_select = array( 'intent' => 1 ), $loc = '_regular' ) {

    // require captcha?
    $captcha = (boolean) \query\main::get_option( 'suggest_captcha' );

    // id is important only for auto select (intent), please read the documentation
    $intent = array( 1 => t( 'suggestion_store_owner', "I'm the owner of this store/brand" ), 2 => t( 'suggestion_just_suggestion', "I just want to make a suggestion" ) );

    $form = '<div class="suggest_store_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['suggest_store_form' . $loc] ) && \site\utils::check_csrf( $_POST['suggest_store_form' . $loc]['csrf'], 'suggest_store' . $loc . '_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['suggest_store_form' . $loc] );

    if( !$captcha || ( isset( $_SESSION['captcha' . $loc]['code'] ) && isset( $pd['captcha' . $loc] ) && $_SESSION['captcha' . $loc]['code'] == $pd['captcha' . $loc] ) ) {

    try {

        $id = $GLOBALS['me'] ? $GLOBALS['me']->ID : 0;

        \user\main::suggest_store( $id, $pd, $intent );
        $form .= '<div class="success">' . t( 'suggestion_sent', "Your suggestion has been sent." ) . '</div>';

        unset( $pd );

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    } else
        $form .= '<div class="error">' . t( 'msg_invalidcaptcha', "Wrong security code." ) . '</div>';

    }

    $csrf = $_SESSION['suggest_store' . $loc . '_csrf'] = \site\utils::str_random(12);

    if( $captcha ) {

    require_once DIR . '/' . LBDIR . '/captcha-master/captcha.php';

    $_SESSION['captcha' . $loc] = simple_php_captcha( array( 'location' => $loc ) );

    }

    $form .= '<form method="POST" action="#widget_suggest">
    <div class="form_field empty_label"><label for="suggest_store_form' . $loc . '[intent]"></label>
    <div><select name="suggest_store_form' . $loc . '[intent]" id="suggest_store_form' . $loc . '[intent]">';
    foreach( $intent as $k => $v )$form .= '<option value="' . $k . '"' . ( ( $_SERVER['REQUEST_METHOD'] != 'POST' && !empty( $auto_select['intent'] ) && ( $auto_select['intent'] == $k || $auto_select['intent'] == $v ) ) || ( isset( $pd['intent'] ) && $pd['intent'] == $k ) ? ' selected' : '' ) . '>' . $v . '</option>';
    $form .= '</select></div>
    </div>
    <div class="form_field"><label for="suggest_store_form' . $loc . '[name]">' . t( 'form_name', "Name" ) . ':</label> <div><input type="text" name="suggest_store_form' . $loc . '[name]" id="suggest_store_form' . $loc . '[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : '' ) . '" placeholder="' . t( 'suggestion_name_ph', "Store/brand name" ) . '" required /></div></div>
    <div class="form_field"><label for="suggest_store_form' . $loc . '[url]">' . t( 'form_store_url', "Store URL" ) . ':</label> <div><input type="text" name="suggest_store_form' . $loc . '[url]" id="suggest_store_form' . $loc . '[url]" value="' . ( isset( $pd['url'] ) ? $pd['url'] : 'http://' ) . '" placeholder="http://" required /></div></div>
    <div class="form_field"><label for="suggest_store_form' . $loc . '[description]">' . t( 'form_description', "Description" ) . ':</label> <div><textarea name="suggest_store_form' . $loc . '[description]" id="suggest_store_form' . $loc . '[description]">' . ( isset( $pd['description'] ) ? $pd['description'] : '' ) . '</textarea></div></div>
    <div class="form_field"><label for="suggest_store_form' . $loc . '[message]">' . t( 'form_message_for_us', "Message For Us" ) . ':</label> <div><textarea name="suggest_store_form' . $loc . '[message]" id="suggest_store_form' . $loc . '[message]">' . ( isset( $pd['message'] ) ? $pd['message'] : '' ) . '</textarea></div></div>';
    if( $captcha ) {
    $form .= '<div class="form_field"><label for="suggest_store_form' . $loc . '[captcha' . $loc . ']">' . t( 'form_securitycheck', "Security Check" ) . ':</label> <div><img src="' . $_SESSION['captcha' . $loc]['image_src'] . '" alt="CAPTCHA code"> <input type="text" name="suggest_store_form' . $loc . '[captcha' . $loc . ']" id="suggest_store_form' . $loc . '[captcha' . $loc . ']"    placeholder="' . t( 'form_securitycheck_ph', "Enter the characters from the image" ) . '" required /></div></div>';
    }
    $form .= '<input type="hidden" name="suggest_store_form' . $loc . '[csrf]" value="' . $csrf . '" />
    <button>' . t( 'send', "Send" ) . '</button>
    </form>

    </div>';

    return $form;

}

/* NEWSLETTER FORM */

function newsletter_form( $loc = '_regular', $id_attr = 'widget_newsletter', $hide_captcha = false ) {

    // require captcha?
    $captcha = !$hide_captcha && (boolean) \query\main::get_option( 'subscribe_captcha' );

    $form = '<div class="subscribe_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['newsletter_form' . $loc] ) && \site\utils::check_csrf( $_POST['newsletter_form' . $loc]['csrf'], 'newsletter_form' . $loc . '_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['newsletter_form' . $loc] );

    if( !$captcha || ( isset( $_SESSION['captcha' . $loc]['code'] ) && isset( $pd['captcha' . $loc] ) && $_SESSION['captcha' . $loc]['code'] == $pd['captcha' . $loc] ) ) {

    try {

        $id = $GLOBALS['me'] ? $GLOBALS['me']->ID : 0;

        $type = \user\main::subscribe( $id, $pd );
        if( $type == 1 ) $form .= '<div class="success">' . sprintf( t( 'newsletter_reqconfirm', "Please check your inbox (%s) and confirm your subscription. (please check Spam directory also)" ), $pd['email'] ) . '</div>';
        else $form .= '<div class="success">' . t( 'newsletter_success', "You had been successfully subscribed, thank you!" ) . '</div>';

        unset( $pd );

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    } else
        $form .= '<div class="error">' . t( 'msg_invalidcaptcha', "Wrong security code." ) . '</div>';

    }

    $csrf = $_SESSION['newsletter_form' . $loc . '_csrf'] = \site\utils::str_random(12);

    if( $captcha ) {

    require_once DIR . '/' . LBDIR . '/captcha-master/captcha.php';

    $_SESSION['captcha' . $loc] = simple_php_captcha( array( 'location' => $loc ) );

    }

    $form .= '<form method="POST" action="#' . $id_attr . '">
    <input type="email" name="newsletter_form' . $loc . '[email]" value="' . ( isset( $pd['email'] ) ? $pd['email'] : '' ) . '" placeholder="' . t( 'form_email', "Email Address" ) . '" required />';
    if( $captcha ) {
    $form .= '<div class="form_field"><label for="newsletter_form' . $loc . '[captcha' . $loc . ']">' . t( 'form_securitycheck', "Security Check" ) . ':</label> <div><img src="' . $_SESSION['captcha' . $loc]['image_src'] . '" alt="CAPTCHA code"> <input type="text" name="newsletter_form' . $loc . '[captcha' . $loc . ']" id="newsletter_form' . $loc . '[captcha' . $loc . ']"    placeholder="' . t( 'form_securitycheck_ph', "Enter the characters from the image" ) . '" required /></div></div>';
    }
    $form .= '<input type="hidden" name="newsletter_form' . $loc . '[csrf]" value="' . $csrf . '" />
    <button>' . t( 'subscribe', "Subscribe" ) . '</button>
    </form>

    </div>';

    return $form;

}

/* CONTACT FORM */

function contact_form( $loc = '_regular' ) {

    // require captcha?
    $captcha = (boolean) \query\main::get_option( 'contact_captcha' );

    $form = '<div class="contact_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['contact_form' . $loc] ) && \site\utils::check_csrf( $_POST['contact_form' . $loc]['csrf'], 'contact_form' . $loc . '_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['contact_form' . $loc] );

    if( !$captcha || ( isset( $_SESSION['captcha' . $loc]['code'] ) && isset( $pd['captcha' . $loc] ) && $_SESSION['captcha' . $loc]['code'] == $pd['captcha' . $loc] ) ) {

    try {

        $id = $GLOBALS['me'] ? $GLOBALS['me']->ID : 0;

        \user\main::send_contact( $pd );
        $form .= '<div class="success">' . t( 'sendcontact_success', "Your message has been sent. Thank you!" ) . '</div>';

        unset( $pd );

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    } else
        $form .= '<div class="error">' . t( 'msg_invalidcaptcha', "Wrong security code." ) . '</div>';

    }

    $csrf = $_SESSION['contact_form' . $loc . '_csrf'] = \site\utils::str_random(12);

    if( $captcha ) {

    require_once DIR . '/' . LBDIR . '/captcha-master/captcha.php';

    $_SESSION['captcha' . $loc] = simple_php_captcha( array('location' => $loc) );

    }

    $form .= '<form method="POST" action="#widget_contact">
    <div class="form_field"><label for="contact_form' . $loc . '[name]">' . t( 'form_name', "Name" ) . ':</label> <div><input type="text" name="contact_form' . $loc . '[name]" id="contact_form' . $loc . '[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : '' ) . '" required /></div></div>
    <div class="form_field"><label for="contact_form' . $loc . '[email]">' . t( 'form_email', "Email Address" ) . ':</label> <div><input type="email" name="contact_form' . $loc . '[email]" id="contact_form' . $loc . '[email]" value="' . ( isset( $pd['email'] ) ? $pd['email'] : '' ) . '" required /></div></div>
    <div class="form_field"><label for="contact_form' . $loc . '[message]">' . t( 'form_message', "Message" ) . ':</label> <div><textarea name="contact_form' . $loc . '[message]" id="contact_form' . $loc . '[message]">' . ( isset( $pd['message'] ) ? $pd['message'] : '' ) . '</textarea></div></div>';
    if( $captcha ) {
    $form .= '<div class="form_field"><label for="contact_form' . $loc . '[captcha' . $loc . ']">' . t( 'form_securitycheck', "Security Check" ) . ':</label> <div><img src="' . $_SESSION['captcha' . $loc]['image_src'] . '" alt="CAPTCHA code"> <input type="text" name="contact_form' . $loc . '[captcha' . $loc . ']" id="contact_form' . $loc . '[captcha' . $loc . ']"    placeholder="' . t( 'form_securitycheck_ph', "Enter the characters from the image" ) . '" required /></div></div>';
    }
    $form .= '<input type="hidden" name="contact_form' . $loc . '[csrf]" value="' . $csrf . '" />
    <button>' . t( 'send', "Send" ) . '</button>
    </form>

    </div>';

    return $form;

}