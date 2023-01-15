<?php

// add a placeholder for an action
do_action( 'click-link-before' );

// check if is used 'store' instead 'id'
if( isset( $_GET['store'] ) && !isset( $_GET['id'] ) ) {
    $_GET['id'] = $_GET['store'];
}

// this is not a valid click. if not, redirect to your website
if( !isset( $_GET['id'] ) && !isset( $_GET['coupon'] ) && !isset( $_GET['product'] ) ) {
    header( 'Location: ' . $GLOBALS['siteURL'] );
    die;
}

// check if store exists. if not, redirect to your website
if( isset( $_GET['id'] ) && !\query\main::store_exists( $_GET['id'] ) ) {
    header( 'Location: ' . $GLOBALS['siteURL'] );
    die;
}

// check if coupon exists. if not, redirect to your website
if( isset( $_GET['coupon'] ) && !\query\main::item_exists( $_GET['coupon'] ) ) {
    header( 'Location: ' . $GLOBALS['siteURL'] );
    die;
}

// check if product exists. if not, redirect to your website
if( isset( $_GET['product'] ) && !\query\main::product_exists( $_GET['product'] ) ) {
    header( 'Location: ' . $GLOBALS['siteURL'] );
    die;
}

require_once LBDIR . '/iptocountry/class.php';

$myIP           = \site\utils::getIP();

$aIP            = new IpToCountry;
$aIP->IP        = $myIP;

$IPinfo         = $aIP->info();

//
$coupon         = $product = 0;
$subid          = value_with_filter( 'click-subid', '' );

if( isset( $_GET['id'] ) ) {

    $info       = \query\main::store_info( $_GET['id'] );

    $store      = $info->ID;
    $url        = $info->url;

    $type       = 'Store';
    $typeID     = (int) $_GET['id'];

} else if( isset( $_GET['coupon'] ) ) {

    $info       = \query\main::item_info( $_GET['coupon'] );

    $store      = $info->storeID;
    $coupon     = $info->ID;
    if( !empty( $_GET['reveal_code'] ) ) {
        $url    = ( isset( $_GET['backTo'] ) ? ( filter_var( $_GET['backTo'], FILTER_VALIDATE_URL ) ? $_GET['backTo'] : base64_decode( $_GET['backTo'] ) ) . '#coupon-' . $info->ID : $info->url );
        $_SESSION['couponscms_rc'][] = $info->storeID;
    } else if( $info->is_printable ) {
        $url    = $GLOBALS['siteURL'] . '?plugin=print&id=' . $info->ID;
    } else {
        $url    = $info->url;
    }

    $type       = 'Coupon';
    $typeID     = (int) $_GET['coupon'];

} else if( isset( $_GET['product'] ) ) {

    $info       = \query\main::product_info( $_GET['product'] );

    $store      = value_with_filter( 'click-store-id', (int) $info->storeID );
    $product    = $info->ID;
    if( !empty( $_GET['reveal_code'] ) ) {
        $url    = ( isset( $_GET['backTo'] ) ? ( filter_var( $_GET['backTo'], FILTER_VALIDATE_URL ) ? $_GET['backTo'] : base64_decode( $_GET['backTo'] ) ) . '#product-' . $info->ID : $info->url );
        $_SESSION['couponscms_rc'][] = $info->storeID;
    } else {
        $url    = $info->url;
    }

    $type       = 'Product';
    $typeID     = (int) $_GET['product'];

}

// prepare URL for traking

$url = str_ireplace( array( '{TYPE}', '{UID}', '{ID}', '{REF_ID}' ), array( $type, ( $GLOBALS['me'] ? $GLOBALS['me']->ID : 'UNL' ), $typeID, ( isset( $_COOKIE['referrer'] ) ? (int) $_COOKIE['referrer'] : 0 ) ), $url );

if( empty( $_GET['reveal_code'] ) ) {

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "click WHERE store = ? AND coupon = ? AND product = ? AND subid = ? AND ipaddr = ? AND date > DATE_ADD(NOW(), INTERVAL -5 MINUTE)" );
    $stmt->bind_param( "iiiss", $store, $coupon, $product, $subid, $myIP );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();

    if( $count === 0 ) {

        $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "click (store, coupon, product, user, subid, ipaddr, browser, country1, country2, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())" );
        $user = $GLOBALS['me'] ? $GLOBALS['me']->ID : 0;
        $stmt->bind_param( "iiiisssss", $store, $coupon, $product, $user, $subid, $myIP, $_SERVER['HTTP_USER_AGENT'], $IPinfo->country, $IPinfo->country_full );

        if( $stmt->execute() ) {
            // add a placeholder for an action
            do_action( 'click-link-inserted', array( 'store' => $store, 'coupon' => $coupon, 'product' => $product, 'user' => $user, 'IP' => $myIP, 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'country' => $IPinfo->country, 'country_full' => $IPinfo->country_full ) );

            if( $type == 'Coupon' ) {
                $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET clicks = clicks+1 WHERE id = ?" );
                $stmt->bind_param( "i", $coupon );
                $stmt->execute();
            }
        }

    }

    $stmt->close();

}

header( 'Location: ' . value_with_filter( 'click-link-url', html_decode( $url ) ) );