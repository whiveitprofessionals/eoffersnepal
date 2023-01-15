<?php

switch( $_GET['action'] ) {

/** SERVER/INFORMATION */

case 'info':

if( !$GLOBALS['me']->is_admin ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'feed_information_title', "Information About Server" ) . '</h2>';

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_feed_page', 'after_title_server_info_feed_page' ) );

try {

$feed = new admin\feed();

$info = $feed->getServer;

echo '<div class="info-table">';
echo '<div class="row"><span>' . t( 'form_status', "Status" ) . ':</span><div>OK</div></div>
<div class="row"><span>' . t( 'form_name', "Name" ) . ':</span><div>' . esc_html( $info['name'] ) . '</div></div>
<div class="row"><span>' . t( 'settings_form_feedauth', "Authentication" ) . ':</span><div>' . esc_html( $feed->auth_type ) . '</div></div>
<div class="row"><span>' . t( 'form_timezone', "Timezone" ) . ':</span><div>' . esc_html( $feed->timezone ) . '</div></div>';
if( isset( $info['DESCRIPTION'] ) ) {
    echo '<div class="row"><span>' . t( 'form_description', "Description" ) . ':</span><div>' . esc_html( $info['DESCRIPTION'] ) . '</div></div>';
}
if( isset( $info['URL'] ) ) {
    echo '<div class="row"><span>' . t( 'form_website', "Website" ) . ':</span><div><a href="' . esc_html( $info['URL'] ) . '" target="_blank">' . esc_html( $info['URL'] ) . '</a></div></div>';
}
if( isset( $info['CONTACT'] ) ) {
    echo '<div class="row"><span>' . t( 'form_contact', "Contact" ) . ':</span><div><a href="?route=users.php&action=sendmail&email=' . esc_html( $info['CONTACT'] ) . '">' . esc_html( $info['CONTACT'] ) . '</div></div>';
}
echo '</div>';

}

catch( Exception $e ) {

    echo '<div class="row"><span>' . t( 'form_status', "Status" ) . ':</span><div>' . $e->getMessage() . '</div></div>';

}

break;

/** IMPORT COUPONS */

case 'import':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'feed_istores_title', "Import Coupons" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=feed.php&amp;action=stores" class="btn">' . t( 'stores', "Stores" ) . '</a>
<a href="?route=feed.php&amp;action=coupons" class="btn">' . t( 'coupons', "Coupons" ) . '</a>
<a href="?route=feed.php&amp;action=products" class="btn">' . t( 'products', "Products" ) . '</a>
</div>';

$subtitle = t( 'feed_istores_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_feed_page', 'after_title_server_import_feed_page' ) );

if( ( $feeds = \query\main::stores( array( 'show' => 'feed' ) ) ) === 0 ) {

echo '<div class="a-error">' . t( 'feed_importe1', "At this moment you have no stores or brands imported, you are able to import coupons automatically only for stores that have assigned a feed ID. Feeds ID are assigned when imported through API." ). '</div>';

} else {

echo '<div class="a-alert">' . sprintf( t( 'feed_importnr_stores', "Fetch data for %s stores that you have imported through API." ), $feeds ) . '</div>';

echo '<div class="form-table">

<form action="#" method="GET" autocomplete="off">

<input type="hidden" name="route" value="feed.php" />
<input type="hidden" name="action" value="import2" />
<div class="row"><span>' . t( 'feed_form_addfrom', "Added from" ) . ':</span><div><input type="date" name="date[]" value="' . date( 'Y-m-d', ( $lcheck = \query\main::get_option( 'lfeed_check' ) ) ) . '" class="datepicker" style="display:inline-block;width:68%;margin-right:2%" /><input type="time" name="date[]" value="' . date( 'H:i', $lcheck ) . '" class="hourpicker" style="display:inline-block;width:30%" /></div></div>
<div class="row"><span>' . t( 'feed_form_import', "Import" ) . ':</span><div><ul class="checkbox-list">
<li><input type="checkbox" name="import_coupons" value="1" id="import_coupons" checked /><label for="import_coupons" style="display:block;"><span></span> ' . t( 'coupons', "Coupons" ) . '</label></li>
<li><input type="checkbox" name="import_ecoupons" value="1" id="import_ecoupons"' . ( \query\main::get_option( 'feed_iexpc' ) ? ' checked' : '' ) . ' /> <label for="import_ecoupons" style="display:block;"><span></span> ' . t( 'msg_feed_cpnpref_impexp', "Import expired coupons" ) . '</label></li>
<li><input type="checkbox" name="import_products" value="1" id="import_products" checked /><label for="import_products" style="display:block;"><span></span> ' . t( 'products', "Products" ) . '</label></li>
<li><input type="checkbox" name="import_eproducts" value="1" id="import_eproducts"' . ( \query\main::get_option( 'feed_iexpp' ) ? ' checked' : '' ) . ' /> <label for="import_eproducts" style="display:block;"><span></span> ' . t( 'msg_feed_ppnpref_impexp', "Import expired products" ) . '</label></li>
</ul></div></div>

<div class="twocols">
    <div><button class="btn btn-important">' . t( 'feed_icoupons_button', "Start Importing" ) . '</button></div>
    <div></div>
</div>

</form>

</div>';

}

break;

/* IMPORT COUPONS&PRODUCTS AUTOMATICALLY */

case 'import2':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['token'] ) && check_csrf( $_POST['token'], 'feed_import_csrf' ) ) {

require_once 'includes/feed.php';

try {

    $feed = new admin\feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );

    $ids = array();
    foreach( \query\main::while_stores( array( 'max' => 0, 'show' => 'feed' ) ) as $store ) {
        $ids[] = $store->feedID;
    }

    $ca = $cae = $cu = $cue = $pa = $pae = $pu = $pue = 0;

    if( !empty( $ids ) ) {

    $last_check = \query\main::get_option( 'lfeed_check' );

    /* CHECK FOR UPDATES FIRST */

    if( (boolean) \query\main::get_option( 'feed_moddt' ) ) {

    /* UPDATE COUPONS */

    try {

        $coupons = $feed->coupons( $options = array( 'store' => implode( ',', array_values( $ids ) ), 'update' => \site\utils::timeconvert( date( 'Y-m-d H:i:s', $last_check ), $feed->timezone ) ) );

        if( !empty( $coupons['Info']['Results'] ) ) {

        for( $cp = 1; $cp <= $coupons['Info']['Results']; $cp++ ) {

        if( $cp != 1 ) {
            $coupons = $feed->coupons( array_merge( array( 'page' => $cp ), $options ) );
        }

        if( !isset( $coupons['List'] ) ) {
            continue;
        }

        foreach( $coupons['List'] as $coupon ) {

        if( ( $couponi = admin\admin_query::coupon_imported( $coupon['ID'] ) ) ) {

        if( admin\actions::edit_item2( $couponi->ID,
        array(
        'printable' => $coupon['is_printable'],
        'available_online' => $coupon['is_avbl_online'],
        'name' => $coupon['Title'],
        'link' => ( filter_var( $coupon['URL'], FILTER_VALIDATE_URL ) ? $coupon['URL'] : '' ),
        'code' => $coupon['Code'],
        'source' => $coupon['Source'],
        'description' => $coupon['Description'],
        'tags' => $coupon['Tags'],
        'start' => $coupon['Start_Date'],
        'end' => $coupon['End_Date']
        ) ) ) {
            $cu++;
        } else {
            $cue++;
        }

        }

        }

        usleep( 200000 ); // put a break after every page

        }

        }

    }

    catch( Exception $e ) { }

    /* UPDATE PRODUCTS */

    try {

        $products = $feed->products( $options = array( 'store' => implode( ',', array_values( $ids ) ), 'update' => \site\utils::timeconvert( date( 'Y-m-d H:i:s', $last_check ), $feed->timezone ) ) );

        if( !empty( $products['Info']['Results'] ) ) {

        for( $cp = 1; $cp <= $products['Info']['Pages']; $cp++ ) {

        if( $cp != 1 ) {
            $products = $feed->products( array_merge( array( 'page' => $cp ), $options ) );
        }

        if( !isset( $products['List'] ) ) {
            continue;
        }

        foreach( $products['List'] as $product ) {

        if( ( $producti = admin\admin_query::product_imported( $product['ID'] ) ) ) {

        if( admin\actions::edit_product2( $producti->ID,
        array(
        'name' => $product['Title'],
        'price' => $product['Price'],
        'old_price' =>$product ['Old_Price'],
        'currency' => strtoupper( $product['Currency'] ),
        'link' => ( filter_var( $product['URL'], FILTER_VALIDATE_URL ) ? $product['URL'] : '' ),
        'description' => $product['Description'],
        'tags' => $product['Tags'],
        'start' => $product['Start_Date'],
        'end' => $product['End_Date']
        ) ) ) {
            $pu++;
        } else {
            $pue++;
        }

        }

        }

        usleep( 200000 ); // put a break after every page

        }

        }

    }

    catch( Exception $e ) { }

    }

    /* IMPORT COUPONS */

    if( isset( $_GET['import_coupons'] ) ) {

    try {

        $coupons = $feed->coupons( $options = array( 'per_page' => 30, 'store' => implode( ',', array_values( $ids ) ), 'view' => (!isset( $_GET['import_ecoupons'] ) ? 'active' : ''), 'date' => \site\utils::timeconvert( implode( $_GET['date'], ' ' ), $feed->timezone ) ) );

        if( !empty( $coupons['Info']['Results'] ) ) {

        for( $cp = 1; $cp <= $coupons['Info']['Pages']; $cp++ ) {

        if( $cp != 1 ) {
            $coupons = $feed->coupons( array_merge( array( 'page' => $cp ), $options ) );
        }

        foreach( $coupons['List'] as $coupon ) {

        if( !admin\admin_query::coupon_imported( $coupon['ID'] ) ) {

        if( ( $store = admin\admin_query::store_imported( $coupon['Store_ID'] ) ) &&
        admin\actions::add_item(
        array(
        'feedID'        => $coupon['ID'],
        'store'         => $store->ID,
        'category'      => $store->catID,
        'popular'       => 0,
        'exclusive'     => 0,
        'printable'     => $coupon['is_printable'],
        'show_in_store' => 0,
        'available_online'=> $coupon['is_avbl_online'],
        'name'          => $coupon['Title'],
        'link'          => ( filter_var( $coupon['URL'], FILTER_VALIDATE_URL ) ? $coupon['URL'] : '' ),
        'code'          => $coupon['Code'],
        'claim_limit'   => 0,
        'source'        => $coupon['Source'],
        'description'   => $coupon['Description'],
        'tags'          => $coupon['Tags'],
        'cashback'      => 0,
        'start'         => $coupon['Start_Date'],
        'end'           => $coupon['End_Date'],
        'image'         => array(),
        'votes'         => 0,
        'votes_average' => 0,
        'verified'      => 0,
        'last_verif'    => date( 'Y-m-d H:i:s' ),
        'publish'       => 1,
        'meta_title'    => '',
        'meta_keywords' => '',
        'meta_desc'     => '',
        'extra' => array()
        ) ) ) {
            $ca++;
        } else {
            $cae++;
        }

        }

        }

        usleep( 200000 ); // put a break after every page

        }

        }

    }

    catch( Exception $e ) { }

    }

    /* IMPORT PRODUCTS */

    if( isset( $_GET['import_products'] ) ) {

    try {

        $products = $feed->products( $options = array( 'store' => implode( ',', array_values( $ids ) ), 'view' => (!isset( $_GET['import_eproducts'] ) ? 'active' : ''), 'date' => \site\utils::timeconvert( implode( $_GET['date'], ' ' ), $feed->timezone ) ) );

        if( !empty( $products['Info']['Results'] ) ) {

        $impimg = (boolean) \query\main::get_option( 'feed_uppics' );

        for( $cp = 1; $cp <= $products['Info']['Pages']; $cp++ ) {

        if( $cp != 1 ) {
            $products = $feed->products( array_merge( array( 'page' => $cp ), $options ) );
        }

        foreach( $products['List'] as $product ) {

        if( !admin\admin_query::product_imported( $product['ID'] ) ) {

        if( ( $store = admin\admin_query::store_imported( $product['Store_ID'] ) ) &&
        admin\actions::add_product(
        value_with_filter( 'save_imported_product_values', array(
        'feedID'        => $product['ID'],
        'store'         => $store->ID,
        'category'      => $store->catID,
        'popular'       => 0,
        'name'          => $product['Title'],
        'price'         => $product['Price'],
        'old_price'     => $product['Old_Price'],
        'currency'      => strtoupper( $product['Currency'] ),
        'link'          => ( filter_var( $product['URL'], FILTER_VALIDATE_URL ) ? $product['URL'] : '' ),
        'description'   => $product['Description'],
        'tags'          => $product['Tags'],
        'cashback'      => 0,
        'start'         => $product['Start_Date'],
        'end'           => $product['End_Date'],
        'publish'       => 1,
        'import_image'  => $impimg,
        'image_url'     => $product['Image'],
        'image'         => '',
        'meta_title'    => '',
        'meta_keywords' => '',
        'meta_desc'     => '',
        'extra'         => array()
        ) ) ) ) {
            $pa++;
        } else {
            $pae++;
        }

        }

        }

        usleep( 200000 ); // put a break after every page

        }

        }

    }

    catch( Exception $e ) { }

    }

    admin\actions::set_option( array( 'lfeed_check' => time() ) ); // update time for last feed check

    echo '<div class="a-message">' . t( 'msg_feed_finished', "Import procedure has been successfully finished." ) . '</div>';

    echo '<ul class="announce-box">
    <li>' . t( 'feed_coupons_sucimp', "Coupons imported" ) . ':<b>' . $ca . '</b> <span>' . t( 'form_error', "Error" ) . ': ' . $cae . '</span></li>
    <li>' . t( 'feed_products_sucimp', "Products imported" ) . ':<b>' . $pa . '</b> <span>' . t( 'form_error', "Error" ) . ': ' . $pae . '</span></li>
    <li>' . t( 'feed_coupons_sucupt', "Coupons updated" ) . ':<b>' . $cu . '</b> <span>' . t( 'form_error', "Error" ) . ': ' . $cue . '</span></li>
    <li>' . t( 'feed_products_sucupt', "Products updated" ) . ':<b>' . $pu . '</b> <span>' . t( 'form_error', "Error" ) . ': ' . $pue . '</span></li>
    </ul>';

    }

}

catch ( Exception $e ) {
    echo '<div class="a-error">' . $e->getMessage() . '</div>';
}

}

$csrf = $_SESSION['feed_import_csrf'] = \site\utils::str_random(10);

if( $_SERVER['REQUEST_METHOD'] != 'POST' ) {

echo '<script>
window.onload = function() {
setTimeout(function(){
    document.forms[\'import_now\'].submit();
}, 1000);
}
</script>';

echo '<div style="text-align: center;">
    <h2>' . t( 'feed_import_dleave', "Please do not leave this page during the import!" ) . '</h2>
</div>

<form id="import_now" action="#" method="POST">
    <input type="hidden" name="token" value="' . $csrf . '" />
</form>';

} else {
    echo '<a href="#" class="btn" onclick="window.history.go(-2)">' . t( 'back', "Back" ) . '</a>';
}

break;

/* IMPORT COUPONS */

case 'import_coupons':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;

if( empty( $_POST['id'] ) || !is_array( $_POST['id'] ) ) {
    echo '<div class="a-error">' . t( 'msg_feed_seltoimp', "Select the items that you want to import." ) . '</div>';
} else {

if( isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'feed_import_csrf' ) ) {

$suc = $err = 0;

echo '<div class="a-message">' . t( 'msg_feed_finished', "Import procedure has been successfully finished." ) . '</div>';

foreach( $_POST['id'] as $coupon ) {

    $coupon = json_decode( urldecode( $coupon ), true );

    $ID = key( $coupon );
    $coupon = $coupon[$ID];

    if( isset( $coupon['store'] ) && isset( $_POST['category'] ) && isset( $coupon['Title'] ) && isset( $coupon['is_printable'] ) && isset( $coupon['is_avbl_online'] ) && isset( $coupon['Code'] ) && isset( $coupon['Source'] ) && isset( $coupon['URL'] ) && isset( $coupon['Description'] ) && isset( $coupon['Tags'] ) && isset( $coupon['Start_Date'] ) && isset( $coupon['End_Date'] ) )
    if( \query\main::store_exists( $coupon['store'] ) && admin\actions::add_item(
    value_with_filter( 'save_imported_coupon_values', array(
    'feedID'        => $ID,
    'store'         => $coupon['store'],
    'category'      => $_POST['category'],
    'popular'       => 0,
    'exclusive'     => 0,
    'printable'     => $coupon['is_printable'],
    'show_in_store' => 0,
    'available_online'=> $coupon['is_avbl_online'],
    'name'          => $coupon['Title'],
    'link'          => ( filter_var( $coupon['URL'], FILTER_VALIDATE_URL ) ? $coupon['URL'] : '' ),
    'code'          => $coupon['Code'],
    'claim_limit'   => 0,
    'source'        => $coupon['Source'],
    'description'   => $coupon['Description'],
    'tags'          => $coupon['Tags'],
    'cashback'      => 0,
    'start'         => $coupon['Start_Date'],
    'end'           => $coupon['End_Date'],
    'image'         => array(),
    'votes'         => 0,
    'votes_average' => 0,
    'verified'      => 0,
    'last_verif'    => date( 'Y-m-d H:i:s' ),
    'publish'       => 1,
    'meta_title'    => '',
    'meta_keywords' => '',
    'meta_desc'     => '',
    'extra'         => array()
    ) ) ) ) {
        $suc++;
    } else {
        $err++;
    }

}

echo '<ul class="announce-box">
<li>' . t( 'feed_coupons_sucimp', "Coupons imported" ) . ':<b>' . $suc . '</b><span>' . t( 'form_error', "Error" ) . ': ' . $err . '</span></li>
</ul>';

}

}

echo '<a href="#" class="btn" onclick="window.history.go(-1)">' . t( 'back', "Back" ) . '</a>';

break;

/* IMPORT PRODUCTS */

case 'import_products':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;

if( empty( $_POST['id'] ) || !is_array( $_POST['id'] ) ) {
    echo '<div class="a-error">' . t( 'msg_feed_seltoimp', "Select the items that you want to import." ) . '</div>';
} else {

if( isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'feed_import_csrf' ) ) {

$suc = $err = 0;

echo '<div class="a-message">' . t( 'msg_feed_finished', "Import procedure has been successfully finished." ) . '</div>';

foreach( $_POST['id'] as $product ) {

    $product = json_decode( urldecode( $product ), true );

    $ID = key( $product );
    $product = $product[$ID];

    $impimg = (boolean) \query\main::get_option( 'feed_uppics' );

    if( isset( $product['store'] ) && isset( $_POST['category'] ) && isset( $product['Title'] ) && isset( $product['URL'] ) && isset( $product['Description'] ) && isset( $product['Tags'] ) && isset( $product['Image'] ) && isset( $product['Start_Date'] ) && isset( $product['End_Date'] ) )
    if( \query\main::store_exists( $product['store'] ) && admin\actions::add_product(
    value_with_filter( 'save_imported_product_values', array(
    'feedID'        => $ID,
    'store'         => $product['store'],
    'category'      => $_POST['category'],
    'popular'       => 0,
    'name'          => $product['Title'],
    'price'         => $product['Price'],
    'old_price'     => $product['Old_Price'],
    'currency'      => strtoupper( $product['Currency'] ),
    'link'          => ( filter_var( $product['URL'], FILTER_VALIDATE_URL ) ? $product['URL'] : '' ),
    'description'   => $product['Description'],
    'tags'          => $product['Tags'],
    'cashback'      => 0,
    'start'         => $product['Start_Date'],
    'end'           => $product['End_Date'],
    'publish'       => 1,
    'import_image'  => $impimg,
    'image_url'     => $product['Image'],
    'image'         => '',
    'meta_title'    => '',
    'meta_keywords' => '',
    'meta_desc'     => '',
    'extra'         => array()
    ) ) ) ) {
        $suc++;
    } else {
        $err++;
    }

}

echo '<ul class="announce-box">
<li>' . t( 'feed_products_sucimp', "Products imported" ) . ':<b>' . $suc . '</b><span>' . t( 'form_error', "Error" ) . ': ' . $err . '</span></li>
</ul>';

}

}

echo '<a href="#" class="btn" onclick="window.history.go(-1)">' . t( 'back', "Back" ) . '</a>';

break;

/* IMPORT STORES */

case 'import_stores':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;

if( empty( $_POST['id'] ) || !is_array( $_POST['id'] ) ) {
    echo '<div class="a-error">' . t( 'msg_feed_seltoimp', "Select the items that you want to import." ) . '</div>';
} else {

if( isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'feed_import_csrf' ) ) {

$sa = $sae = $ca = $cae = $pa = $pae = 0;

$impimg = (boolean) \query\main::get_option( 'feed_uppics' );

echo '<div class="a-message">' . t( 'msg_feed_finished', "Import procedure has been successfully finished." ) . '</div>';

foreach( $_POST['id'] as $store ) {

    $store = json_decode( urldecode( $store ), true );

    $ID = key( $store );
    $store = $store[$ID];

    if( isset( $_POST['category'] ) && isset( $store['Name'] ) && isset( $store['is_physical'] ) && isset( $store['URL'] ) && isset( $store['Description'] ) && isset( $store['Tags'] ) && isset( $store['Image'] ) )

    if( $storeID = admin\actions::add_store(
    value_with_filter( 'save_imported_store_values', array(
    'feedID'        => $ID,
    'user'          => $GLOBALS['me']->ID,
    'category'      => $_POST['category'],
    'name'          => $store['Name'],
    'type'          => $store['is_physical'],
    'url'           => $store['URL'],
    'description'   => $store['Description'],
    'tags'          => $store['Tags'],
    'hours'         => ( !empty( $store['Hours'] ) ? @serialize( $store['Hours'] ) : '' ),
    'sellonline'    => $store['Sell_Online'],
    'popular'       => 0,
    'publish'       => 1,
    'import_logo'   => $impimg,
    'logo_url'      => $store['Image'],
    'logo'          => '',
    'phone'         => $store['Phone'],
    'meta_title'    => '',
    'meta_keywords' => '',
    'meta_desc'     => '',
    'extra'         => array()
    ) ) ) ) {

    if( isset( $store['Locations'] ) && is_array( $store['Locations'] ) ) {
        foreach( $store['Locations'] as $location ) {
            admin\actions::add_store_location( array_merge( array( 'Store' => $storeID ), $location ) );
        }
    }

if( isset( $_POST['coupons'] ) || isset( $_POST['products'] ) ) {

require_once 'includes/feed.php';

try {

    if( !isset( $feed ) ) {
        $feed = new admin\feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );
    }

    /* IMPORT COUPONS */

    if( isset( $_POST['coupons'] ) ) {

    try {

        $coupons = $feed->coupons( $options = array( 'per_page' => 30, 'store' => $ID, 'view' => (!(boolean) \query\main::get_option( 'feed_iexpc' ) ? 'active' : '') ) );

        if( !empty( $coupons['Info']['Results'] ) ) {

        for( $cp = 1; $cp <= $coupons['Info']['Pages']; $cp++ ) {

        if( $cp != 1 ) {
            $coupons = $feed->coupons( array_merge( array( 'page' => $cp ), $options ) );
        }

        foreach( $coupons['List'] as $coupon ) {

        if( !admin\admin_query::coupon_imported( $coupon['ID'] ) ) {

        if( ( $store = admin\admin_query::store_imported( $coupon['Store_ID'] ) ) &&
        admin\actions::add_item(
        value_with_filter( 'save_imported_coupon_values', array(
        'feedID'        => $coupon['ID'],
        'store'         => $store->ID,
        'category'      => $store->catID,
        'popular'       => 0,
        'exclusive'     => 0,
        'printable'     => $coupon['is_printable'],
        'show_in_store' => 0,
        'available_online'=> $coupon['is_avbl_online'],
        'name'          => $coupon['Title'],
        'link'          => ( filter_var( $coupon['URL'], FILTER_VALIDATE_URL ) ? $coupon['URL'] : '' ),
        'code'          => $coupon['Code'],
        'claim_limit'   => 0,
        'source'        => $coupon['Source'],
        'description'   => $coupon['Description'],
        'tags'          => $coupon['Tags'],
        'cashback'      => 0,
        'start'         => $coupon['Start_Date'],
        'end'           => $coupon['End_Date'],
        'image'         => array(),
        'votes'         => 0,
        'votes_average' => 0,
        'verified'      => 0,
        'last_verif'    => '',
        'publish'       => 1,
        'meta_title'    => '',
        'meta_keywords' => '',
        'meta_desc'     => '',
        'extra'         => array()
        ) ) ) ) {
            $ca++;
        } else {
            $cae++;
        }

        }

        }

        usleep( 200000 ); // put a break after every page

        }

        }

    }

    catch( Exception $e ) { }

    }

    /* IMPORT PRODUCTS */

    if( isset( $_POST['products'] ) ) {

    try {

        $products = $feed->products( $options = array( 'store' => $ID, 'view' => (!(boolean) \query\main::get_option( 'feed_iexpp' ) ? 'active' : '') ) );

        if( !empty( $products['Info']['Results'] ) ) {

        for( $cp = 1; $cp <= $products['Info']['Pages']; $cp++ ) {

        if( $cp != 1 ) {
            $products = $feed->products( array_merge( array( 'page' => $cp ), $options ) );
        }

        foreach( $products['List'] as $product ) {

        if( !admin\admin_query::product_imported( $product['ID'] ) ) {

        if( ( $store = admin\admin_query::store_imported( $product['Store_ID'] ) ) &&
        admin\actions::add_product(
        value_with_filter( 'save_imported_product_values', array(
        'feedID'        => $product['ID'],
        'store'         => $store->ID,
        'category'      => $store->catID,
        'popular'       => 0,
        'name'          => $product['Title'],
        'price'         => $product['Price'],
        'old_price'     => $product['Old_Price'],
        'currency'      => strtoupper( $product['Currency'] ),
        'link'          => ( filter_var( $product['URL'], FILTER_VALIDATE_URL ) ? $product['URL'] : '' ),
        'description'   => $product['Description'],
        'tags'          => $product['Tags'],
        'cashback'      => 0,
        'start'         => $product['Start_Date'],
        'end'           => $product['End_Date'],
        'publish'       => 1,
        'import_image'  => $impimg,
        'image_url'     => $product['Image'],
        'image'         => '',
        'meta_title'    => '',
        'meta_keywords' => '',
        'meta_desc'     => '',
        'extra'         => array()
        ) ) ) ) {
            $pa++;
        } else {
            $pae++;
        }

        }

        }

        usleep( 200000 ); // put a break after every page

        }

        }

    }

    catch( Exception $e ) { }

    }

}

catch( Exception $e ) { }

}

        $sa++;
    } else {
        $sae++;
    }

}

echo '<ul class="announce-box">
<li>' . t( 'feed_stores_sucimp', "Stores imported" ) . ':<b>' . $sa . '</b><span>' . t( 'form_error', "Error" ) . ': ' . $sae . '</span></li>
<li>' . t( 'feed_coupons_sucimp', "Coupons imported" ) . ':<b>' . $ca . '</b><span>' . t( 'form_error', "Error" ) . ': ' . $cae . '</span></li>
<li>' . t( 'feed_products_sucimp', "Products imported" ) . ':<b>' . $pa . '</b><span>' . t( 'form_error', "Error" ) . ': ' . $pae . '</span></li>
</ul>';

}

}

echo '<a href="#" class="btn" onclick="window.history.go(-1)">' . t( 'back', "Back" ) . '</a>';

break;

/* PREVIEW COUPON */

case 'preview_coupon':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;

if( isset( $_GET['id'] ) ) {

require_once 'includes/feed.php';

try {

    $feed = new admin\feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );

    try {

        $coupon = $feed->coupon( $_GET['id'] );

        if( $store_imported = admin\admin_query::store_imported( $coupon['Store_ID'] ) ) {
            // Store ID
            $coupon['store'] = $store_imported->ID;
            // Coupon ID
            $ID = $_GET['id'];
        }

    }

    catch( Exception $e ) { }

}

catch( Exception $e ) { }

} else if( isset( $_GET['coupon'] ) ) {

    $coupon = json_decode( urldecode( $_GET['coupon'] ), true );

    if( !empty( $coupon ) ) {
        // Coupon ID
        $ID = key( $coupon );
        // Coupon Info
        $coupon = current( $coupon );
    }

}

if( !isset( $ID ) ) echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

else {

$imported = admin\admin_query::coupon_imported( $ID );

echo '<div class="title">

<h2>' . t( 'feed_picoupon_title', "Preview & Import" ) . '</h2>

<div style="float:right;margin:0 2px 0 0;">

<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>
<li><a href="#" class="more_fields">' . t( 'more', "More" ) . '</a></li>
</ul>
</div>

<a href="?route=feed.php&amp;action=coupons" class="btn">' . t( 'coupons_view', "View Coupons" ) . '</a>
</div>';

$subtitle = t( 'feed_picoupon_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_feed_page', 'after_title_add_coupon_page', 'after_title_preview_coupon_feed_page', 'after_title_preview_coupon_page' ) );

if( ( $store_imported = \query\main::store_exists( $coupon['store'] ) ) ) {
    $store = \query\main::store_info( $coupon['store'] );
}

$info = array();
$info['storeID']        = ( isset( $store ) ? $store->ID : '' );
$info['title']          = ( !empty( $coupon['Title'] ) ? $coupon['Title'] : '' );
$info['original_url']   = ( !empty( $coupon['URL'] ) ? $coupon['URL'] : '' );
$info['code']           = ( !empty( $coupon['Code'] ) ? $coupon['Code'] : '' );
$info['catID']          = ( !empty( $coupon['Category'] ) ? $coupon['Category'] : ( isset( $store ) ? $store->catID : '' ) );
if( !isset( $_GET['type'] ) || !isset( $_GET['token'] ) || $_GET['type'] != 'delete_image' || !check_csrf( $_GET['token'], 'feed_import_csrf' ) ) {
    $info['image']      = ( !empty( $coupon['Image'] ) ? $coupon['Image'] : '' );
    $skip_image         = true;
}
$info['description']    = ( !empty( $coupon['Description'] ) ? $coupon['Description'] : '' );
$info['start_date']     = ( !empty( $coupon['Start_Date'] ) ? $coupon['Start_Date'] : '' );
$info['expiration_date']= ( !empty( $coupon['End_Date'] ) ? $coupon['End_Date'] : '' );
$info['store_is_physical']  = ( isset( $store ) ? $store->is_physical : '' );
$info['store_sellonline']   = ( isset( $store ) ? $store->sellonline : '' );
$info['is_available_online']= ( isset( $store ) && ( !$store->is_physical || !$store->sellonline ) ? false : false );
$info = (object) $info;

if( $imported ) {
    echo '<div class="a-alert">' . t( 'msg_feed_cimported', "This coupon is already imported." ) . '</div>';
} else if( $store_imported ) {

if( isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'feed_import_csrf' ) ) {

    if( isset( $_GET['type'] ) && $_GET['type'] == 'delete_image' ) {
        $info->image    = '';
        $skip_image     = true;
    }

    if( isset( $_POST['store'] ) && isset( $_POST['category'] ) && isset( $_POST['name'] ) && isset( $_POST['coupon_type'] ) && isset( $_POST['coupon_source_url'] ) && isset( $_FILES['coupon_source'] ) && isset( $_POST['code'] ) && isset( $_POST['description'] ) && isset( $_POST['tags'] ) && isset( $_POST['reward_points'] ) && isset( $_POST['votes'] ) && isset( $_POST['votes_average'] ) && isset( $_POST['lverified'] ) && isset( $_POST['start'] ) && isset( $_POST['end'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_keywords'] ) && isset( $_POST['meta_desc'] ) )
    if( ( $new_coupon_id = admin\actions::add_item(
    value_with_filter( 'save_coupon_values', array(
    'feedID'        => $ID,
    'store'         => ( isset( $_POST['store'] ) ? $_POST['store'] : 0 ),
    'category'      => ( isset( $_POST['category'] ) ? $_POST['category'] : 0 ),
    'popular'       => ( isset( $_POST['popular'] ) ? 1 : 0 ),
    'exclusive'     => ( isset( $_POST['exclusive'] ) ? 1 : 0 ),
    'printable'     => ( isset( $_POST['coupon_type'] ) && in_array( (int) $_POST['coupon_type'], array( 1, 2 ) ) ? 1 : 0 ),
    'show_in_store' => ( isset( $_POST['coupon_type'] ) && (int) $_POST['coupon_type'] === 3 ? 1 : 0 ),
    'available_online' => ( isset( $_POST['coupon_use_online'] ) ? 1 : 0 ),
    'name'          => ( isset( $_POST['name'] ) ? $_POST['name'] : '' ),
    'link'          => ( !isset( $_POST['coupon_ownlink'] ) && isset( $_POST['link'] ) && preg_match( '/^http(s)?/i', $_POST['link'] ) ? $_POST['link'] : '' ),
    'code'          => ( isset( $_POST['coupon_type'] ) && (int) $_POST['coupon_type'] === 0 ? $_POST['code'] : '' ),
    'claim_limit'   => ( isset( $_POST['limit'] ) ? (int) $_POST['limit'] : 0 ),
    'source'        => ( isset( $_POST['coupon_type'] ) && (int) $_POST['coupon_type'] === 2 ? ( isset( $_POST['coupon_online_source'] ) ? ( filter_var( $_POST['coupon_source_url'], FILTER_VALIDATE_URL ) ? $_POST['coupon_source_url'] : '' ) : $_FILES['coupon_source'] ) : '' ),
    'description'   => ( isset( $_POST['description'] ) ? $_POST['description'] : '' ),
    'tags'          => ( isset( $_POST['tags'] ) ? $_POST['tags'] : '' ),
    'cashback'      => ( isset( $_POST['reward_points'] ) ? (int) $_POST['reward_points'] : 0 ),
    'start'         => ( isset( $_POST['start'] ) ? date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['start'] ) ) ) : '' ),
    'end'           => ( isset( $_POST['end'] ) ? date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['end'] ) ) ) : '' ),
    'image'         => ( isset( $_FILES['image'] ) ? $_FILES['image'] : array() ),
    'import_image'  => ( !empty( $_FILES['image']['size'] ) || $skip_image ? (boolean) \query\main::get_option( 'feed_uppics' ) : false ),
    'image_url'     => ( isset( $info->image ) ? $info->image : '' ),
    'votes'         => ( isset( $_POST['votes'] ) ? (int) $_POST['votes'] : 0 ),
    'votes_average' => ( isset( $_POST['votes_average'] ) ? (double) $_POST['votes_average'] : 0 ),
    'verified'      => ( isset( $_POST['verified'] ) ? 1 : 0 ),
    'last_verif'    => ( isset( $_POST['lverified'] ) ? date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['lverified'] ) ) ) : '' ),
    'publish'       => ( isset( $_POST['publish'] ) ? 1 : 0 ),
    'meta_title'    => ( isset( $_POST['meta_title'] ) ? $_POST['meta_title'] : '' ),
    'meta_keywords' => ( isset( $_POST['meta_keywords'] ) ? $_POST['meta_keywords'] : '' ),
    'meta_desc'     => ( isset( $_POST['meta_desc'] ) ? $_POST['meta_desc'] : '' ),
    'extra'         => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() )
    ) ) ) ) ) {

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';

    do_action( array( 'admin_coupon_added_edited', 'admin_coupon_added', 'admin_coupon_imported' ), $new_coupon_id );

    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';
}

}

$csrf = $_SESSION['feed_import_csrf'] = \site\utils::str_random(10);

$main   = $GLOBALS['admin_main_class']->coupon_fields( $info, $csrf );
$fields = $main['fields'];

admin\widgets::get_page_tabs( $main );

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">';

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

echo '<div id="modify_mt">

<div class="title">
    <h2>' . t( 'pages_title_meta', "Modify Personalized Meta-Tags" ) . '</h2>
</div>

<div class="content">';

$fields = $GLOBALS['admin_main_class']->meta_tags_fields( array(), $csrf );

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

echo '</div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<div class="twocols">
    <div>
        <button class="btn btn-important"' . ( $imported || !$store_imported ? ' disabled' : '' ) . '>' . t( 'import', "Import" ) . '</button>
    </div>
    <div>
        <a href="#" class="btn" id="modify_mt_but">' . t( 'pages_editmt_button', "Meta Tags" ) . '</a>
    </div>
</div>

</form>

</div>';

}

break;

/** PREVIEW PRODUCT */

case 'preview_product':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;


if( isset( $_GET['id'] ) ) {

require_once 'includes/feed.php';

try {

    $feed = new admin\feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );

    try {

        $product = $feed->product( $_GET['id'] );

        if( $store_imported = admin\admin_query::store_imported( $product['Store_ID'] ) ) {
            // Store ID
            $product['store'] = $store_imported->ID;
            // Product ID
            $ID = $_GET['id'];
        }

    }

    catch( Exception $e ) { }

}

catch( Exception $e ) { }

} else if( isset( $_GET['product'] ) ) {

    $product = json_decode( urldecode( $_GET['product'] ), true );

    // Product ID
    $ID = key( $product );

    $product = current( $product );

}

if( !isset( $ID ) ) echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

else {

$imported = admin\admin_query::product_imported( $ID );

echo '<div class="title">

<h2>' . t( 'feed_piproduct_title', "Preview & Import" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=feed.php&amp;action=products" class="btn">' . t( 'products_view', "View Products" ) . '</a>
</div>';

$subtitle = t( 'feed_piproduct_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_feed_page', 'after_title_add_product_page', 'after_title_preview_product_feed_page', 'after_title_preview_product_page' ) );

if( ( $store_imported = \query\main::store_exists( $product['store'] ) ) ) {
    $store = \query\main::store_info( $product['store'] );
}

$info = array();
$info['storeID']        = ( isset( $store ) ? $store->ID : '' );
$info['title']          = ( !empty( $product['Title'] ) ? $product['Title'] : '' );
$info['price']          = ( !empty( $product['Price'] ) ? $product['Price'] : '' );
$info['old_price']      = ( !empty( $product['Old_Price'] ) ? $product['Old_Price'] : '' );
$info['currency']       = ( !empty( $product['Currency'] ) ? $product['Currency'] : '' );
$info['catID']          = ( !empty( $product['Category'] ) ? $product['Category'] : ( isset( $store ) ? $store->catID : '' ) );
if( !isset( $_GET['type'] ) || !isset( $_GET['token'] ) || $_GET['type'] != 'delete_image' || !check_csrf( $_GET['token'], 'feed_import_csrf' ) ) {
    $info['image']      = ( !empty( $product['Image'] ) ? $product['Image'] : '' );
    $skip_image         = true;
}
$info['description']    = ( !empty( $product['Description'] ) ? $product['Description'] : '' );
$info['start_date']     = ( !empty( $product['Start_Date'] ) ? $product['Start_Date'] : '' );
$info['expiration_date']= ( !empty( $product['End_Date'] ) ? $product['End_Date'] : '' );
$info = (object) $info;

if( $imported ) {
    echo '<div class="a-alert">' . t( 'msg_feed_pimported', "This product is already imported." ) . '</div>';
} else if( $store_imported ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['product'] ) && is_array( $_POST['product'] ) ) {

if( isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'feed_import_csrf' ) ) {

    if( isset( $_GET['type'] ) && $_GET['type'] == 'delete_image' ) {
        $info->image    = '';
        $skip_image     = true;
    }

    if( ( $new_product_id = admin\actions::add_product(
    value_with_filter( 'save_product_values', array(
    'feedID'        => $ID,
    'store'         => ( isset( $_POST['store'] ) ? $_POST['store'] : 0 ),
    'category'      => ( isset( $_POST['category'] ) ? $_POST['category'] : 0 ),
    'popular'       => ( isset( $_POST['popular'] ) ? 1 : 0 ),
    'name'          => ( isset( $_POST['name'] ) ? $_POST['name'] : '' ),
    'price'         => ( isset( $_POST['price'] ) ? $_POST['price'] : '' ),
    'old_price'     => ( isset( $_POST['old_price'] ) ? $_POST['old_price'] : '' ),
    'currency'      => ( isset( $_POST['currency'] ) ? strtolower( $_POST['currency'] ) : '' ),
    'link'          => ( !isset( $_POST['product_ownlink'] ) && isset( $_POST['link'] ) && filter_var( $_POST['link'], FILTER_VALIDATE_URL ) ? $_POST['link'] : '' ),
    'description'   => ( isset( $_POST['description'] ) ? $_POST['description'] : '' ),
    'tags'          => ( isset( $_POST['tags'] ) ? $_POST['tags'] : '' ),
    'cashback'      => ( isset( $_POST['reward_points'] ) ? (int) $_POST['reward_points'] : 0 ),
    'start'         => ( isset( $_POST['start'] ) ? date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['start'] ) ) ) : '' ),
    'end'           => ( isset( $_POST['end'] ) ? date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['end'] ) ) ) : '' ),
    'publish'       => ( isset( $_POST['publish'] ) ? 1 : 0 ),
    'image'         => ( isset( $_FILES['image'] ) ? $_FILES['image'] : array() ),
    'import_image'  => ( !empty( $_FILES['image']['size'] ) || $skip_image ? (boolean) \query\main::get_option( 'feed_uppics' ) : false ),
    'image_url'     => ( isset( $info->image ) ? $info->image : '' ),
    'meta_title'    => ( isset( $_POST['meta_title'] ) ? $_POST['meta_title'] : '' ),
    'meta_keywords' => ( isset( $_POST['meta_keywords'] ) ? $_POST['meta_keywords'] : '' ),
    'meta_desc'     => ( isset( $_POST['meta_desc'] ) ? $_POST['meta_desc'] : '' ),
    'extra'         => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() )
    ) ) ) ) ) {

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';

    do_action( array( 'admin_product_added_edited', 'admin_product_added', 'admin_product_imported' ), $new_product_id );

    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

}

$csrf = $_SESSION['feed_import_csrf'] = \site\utils::str_random(10);

$main   = $GLOBALS['admin_main_class']->product_fields( $info, $csrf );
$fields = $main['fields'];

admin\widgets::get_page_tabs( $main );

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">';

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

echo '<div id="modify_mt">

<div class="title">
    <h2>' . t( 'pages_title_meta', "Modify Personalized Meta-Tags" ) . '</h2>
</div>

<div class="content">';

$fields = $GLOBALS['admin_main_class']->meta_tags_fields( array(), $csrf );

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

echo '</div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<input type="hidden" name="feedID" value="' . $ID . '" />
<input type="hidden" name="product[Image]" value="' . $product['Image'] . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important"' . ( $imported || !$store_imported ? ' disabled' : '' ) . '>' . t( 'import', "Import" ) . '</button>
    </div>
    <div>
        <a href="#" class="btn" id="modify_mt_but">' . t( 'pages_editmt_button', "Meta Tags" ) . '</a>
    </div>
</div>

</form>

</div>';

}

break;

/* PREVIEW STORE */

case 'preview_store':

if( !ab_to( array( 'feed' => 'import' ) ) ) die;

if( isset( $_GET['id'] ) ) {

require_once 'includes/feed.php';

try {

    $feed = new admin\feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );

    try {

        $store = $feed->store( $_GET['id'] );

        // Store ID
        $ID = $_GET['id'];

    }

    catch( Exception $e ) { }

}

catch( Exception $e ) { }

} else if( isset( $_GET['store'] ) ) {

    $store = json_decode( urldecode( $_GET['store'] ), true );

    if( !empty( $store ) ) {
        // Coupon ID
        $ID = key( $store );
        // Coupon Info
        $store = current( $store );
    }

}

if( !isset( $ID ) ) echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

else {

$imported = admin\admin_query::store_imported( $ID );

echo '<div class="title">

<h2>' . t( 'feed_pistore_title', "Preview & Import" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=feed.php&amp;action=list" class="btn">' . t( 'stores_view', "View Stores" ) . '</a>
</div>';

$subtitle = t( 'feed_pistore_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_feed_page', 'after_title_add_store_page', 'after_title_preview_store_feed_page', 'after_title_preview_store_page' ) );

$info = array();
$info['name']           = ( !empty( $store['Name'] ) ? $store['Name'] : '' );
$info['url']            = ( !empty( $store['URL'] ) ? $store['URL'] : '' );
$info['description']    = ( !empty( $store['Description'] ) ? $store['Description'] : '' );
$info['hours']          = ( !empty( $store['Hours'] ) ? $store['Hours'] : '' );
if( !isset( $_GET['type'] ) || !isset( $_GET['token'] ) || $_GET['type'] != 'delete_image' || !check_csrf( $_GET['token'], 'feed_import_csrf' ) ) {
    $info['image']      = ( !empty( $store['Image'] ) ? $store['Image'] : '' );
    $skip_image         = true;
}
$info['is_physical']    = ( !empty( $store['is_physical'] ) ? (boolean) $store['is_physical'] : false );
$info['sellonline2']    = ( !empty( $store['is_physical'] ) && isset( $store['Sell_Online'] ) ? (boolean) $store['Sell_Online'] : true );
$info = (object) $info;

if( $imported ) {
    echo '<div class="a-alert">' . t( 'msg_feed_simported', "This store is already imported." ) . '</div>';
} else {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['store'] ) && is_array( $_POST['store'] ) ) {

if( isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'feed_import_csrf' ) ) {

    if( isset( $_GET['type'] ) && $_GET['type'] == 'delete_image' ) {
        $info->image    = '';
        $skip_image     = true;
    }

    if( ( $new_store_id = admin\actions::add_store(
    value_with_filter( 'save_store_values', array(
    'feedID'        => $ID,
    'user'          => ( isset( $_POST['user'] ) ? $_POST['user'] : 0 ),
    'category'      => ( isset( $_POST['category'] ) ? $_POST['category'] : 0 ),
    'popular'       => ( isset( $_POST['popular'] ) ? 1 : 0 ),
    'name'          => ( isset( $_POST['name'] ) ? $_POST['name'] : '' ),
    'type'          => ( isset( $_POST['store_type'] ) ? $_POST['store_type'] : '' ),
    'url'           => ( isset( $_POST['url'] ) ? $_POST['url'] : '' ),
    'description'   => ( isset( $_POST['description'] ) ? $_POST['description'] : '' ),
    'hours'         => ( isset( $_POST['hours-bi'] ) ? array() : ( isset( $_POST['hours'] ) ? $_POST['hours'] : '' ) ),
    'tags'          => ( isset( $_POST['tags'] ) ? $_POST['tags'] : '' ),
    'sellonline'    => ( isset( $_POST['sellonline'] ) ? 1 : 0 ),
    'publish'       => ( isset( $_POST['publish'] ) ? 1 : 0 ),
    'logo'          => ( isset( $_FILES['logo'] ) ? $_FILES['logo'] : array() ),
    'import_logo'   => ( !empty( $_FILES['logo']['size'] ) || $skip_image ? (boolean) \query\main::get_option( 'feed_uppics' ) : false ),
    'logo_url'      => ( isset( $info->image ) ? $info->image : '' ),
    'phone'         => ( isset( $_POST['phone'] ) ? $_POST['phone'] : '' ),
    'meta_title'    => ( isset( $_POST['meta_title'] ) ? $_POST['meta_title'] : '' ),
    'meta_keywords' => ( isset( $_POST['meta_keywords'] ) ? $_POST['meta_keywords'] : '' ),
    'meta_desc'     => ( isset( $_POST['meta_desc'] ) ? $_POST['meta_desc'] : '' ),
    'extra'         => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() )
    ) ) ) ) ) {

    if( isset( $store['Locations'] ) && is_array( $store['Locations'] ) ) {
        foreach( $store['Locations'] as $location ) {
            admin\actions::add_store_location( array_merge( array( 'Store' => $new_store_id ), $location ) );
        }
    }

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';

    do_action( array( 'admin_store_added_edited', 'admin_store_added', 'admin_store_imported' ), $new_store_id );

    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

}

$csrf = $_SESSION['feed_import_csrf'] = \site\utils::str_random(10);

$main   = $GLOBALS['admin_main_class']->store_fields( $info, $csrf );
$fields = $main['fields'];

admin\widgets::get_page_tabs( $main );

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">';

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

echo '<div id="modify_mt">

<div class="title">
    <h2>' . t( 'pages_title_meta', "Modify Personalized Meta-Tags" ) . '</h2>
</div>

<div class="content">';

$fields = $GLOBALS['admin_main_class']->meta_tags_fields( array(), $csrf );

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

echo '</div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />
<input type="hidden" name="feedID" value="' . $ID . '" />
<input type="hidden" name="store[Image]" value="' . $store['Image'] . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important"' . ( $imported ? ' disabled' : '' ) . '>' . t( 'import', "Import" ) . '</button>
    </div>
    <div>
        <a href="#" class="btn" id="modify_mt_but">' . t( 'pages_editmt_button', "Meta Tags" ) . '</a>
    </div>
</div>

</form>

</div>';

}

break;

/* LIST OF FEED COUPONS */

case 'coupons':

if( !ab_to( array( 'feed' => 'view' ) ) ) die;

require_once 'includes/feed.php';

try {

    $feed = new admin\feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );
    $feed->exportAs( 'object' );

    try {

        $coupons = $feed->coupons( array( 'page' => (isset( $_GET['page'] ) ? $_GET['page'] : 1), 'per_page' => 10, 'orderby' => (isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'date desc'), 'store' => (isset( $_GET['store'] ) ? $_GET['store'] : ''), 'category' => (isset( $_GET['category'] ) ? $_GET['category'] : ''), 'search' => (isset( $_GET['search'] ) ? $_GET['search'] : '') ) );

        echo '<div class="title">

        <h2>' . t( 'coupons_title', "Coupons" ) . '</h2>

        <div style="float:right;margin:0 2px 0 0;">';
        if( ab_to( array( 'feed' => 'import' ) ) ) echo '<a href="?route=feed.php&amp;action=import" class="btn">' . t( 'feed_icoupons', "Check/Update" ) . '</a>';
        echo '</div>';

        $subtitle = t( 'feed_coupons_subtitle' );

        if( !empty( $subtitle ) ) {
            echo '<span>' . $subtitle . '</span>';
        }

        echo '</div>';

        do_action( array( 'after_title_inner_page', 'after_title_feed_page', 'after_title_list_coupons_feed_page' ) );

        $csrf = $_SESSION['feed_import_csrf'] = \site\utils::str_random(10);

        echo '<div class="page-toolbar">

        <form action="#" method="GET" autocomplete="off">
        <input type="hidden" name="route" value="feed.php" />
        <input type="hidden" name="action" value="coupons" />

        ' . t( 'order_by', "Order by" ) . ':
        <select name="orderby">';
        foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'update' => t( 'order_last_update', "Last update" ), 'update desc' => t( 'order_last_update_desc', "Last update DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
        echo '</select> ';

        try {

            $category = $feed->categories();

            echo '<select name="category">
            <option value="">' . t( 'all_categories', "All categories" ) . '</option>';
            foreach( $category['List'] as $k => $v ) {

                echo '<optgroup label="' . $v->Name . '">';
                echo '<option value="' . $k . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $k ? ' selected' : '' ) . '>' . $v->Name . '</option>';
                if( isset( $v->Subcategories ) ) {
                    foreach( $v->Subcategories as $k1 => $v1 ) {
                        echo '<option value="' . $k1 . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $k1 ? ' selected' : '' ) . '>' . $v1->Name . '</option>';
                    }
                }
                echo '</optgroup>';
            }
            echo '</select>';

        }

        catch( Exception $e ) { }

        if( isset( $_GET['search'] ) ) {
        echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
        }

        echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

        </form>

        <form action="#" method="GET" autocomplete="off">
        <input type="hidden" name="route" value="feed.php" />
        <input type="hidden" name="action" value="coupons" />';

        if( isset( $_GET['orderby'] ) ) {
            echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
        }

        if( isset( $_GET['category'] ) ) {
            echo '<input type="hidden" name="category" value="' . esc_html( $_GET['category'] ) . '" />';
        }

        echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'coupons_search_input', "Search coupons" ) . '" />
        <button class="btn">' . t( 'search', "Search" ) . '</button>
        </form>

        </div>';

        echo '<div class="results">' . ( (int) $coupons['Info']->Results === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $coupons['Info']->Results ) : sprintf( t( 'results', "<b>%s</b> results" ), $coupons['Info']->Results ) );
        if( !empty( $_GET['store'] ) || !empty( $_GET['category'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=feed.php&amp;action=coupons">' . t( 'reset_view', "Reset view" ) . '</a>';
        echo '</div>';

        if( $coupons['Info']->Results ) {

        echo '<form action="?route=feed.php&amp;action=import_coupons" method="POST">

        <ul class="elements-list">

        <li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

        $feed_im    = ab_to( array( 'feed' => 'import' ) );

        if( $feed_im ) {

        echo '<div class="bulk_options">';

        echo t( 'category', "Category" ) . ':
        <select name="category">';
        foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
            echo '<optgroup label="' . $cat['info']->name . '">';
            echo '<option value="' . $cat['info']->ID . '">' . $cat['info']->name . '</option>';
            if( isset( $cat['subcats'] ) ) {
                foreach( $cat['subcats'] as $subcat ) {
                    echo '<option value="' . $subcat->ID . '">' . $subcat->name . '</option>';
                }
            }
            echo '</optgroup>';
        }
        echo '</select>

        <button class="btn">' . t( 'import_all', "Import All" ) . '</button>';
        echo '</div>';

        }

        foreach( $coupons['List'] as $item ) {

            $imported = admin\admin_query::coupon_imported( $item->ID );
            $store_imported = admin\admin_query::store_imported( $item->Store_ID );

            $jsdt = array();

            if( !$imported && $store_imported ) {

            $jsdt[$item->ID]['store'] = $store_imported->ID;
            $jsdt[$item->ID]['Title'] = $item->Title;
            $jsdt[$item->ID]['URL'] = $item->URL;
            $jsdt[$item->ID]['Code'] = $item->Code;
            $jsdt[$item->ID]['Tags'] = $item->Tags;
            $jsdt[$item->ID]['Image'] = $item->Image;
            $jsdt[$item->ID]['Description'] = $item->Description;
            $jsdt[$item->ID]['Start_Date'] = $item->Start_Date;
            $jsdt[$item->ID]['End_Date'] = $item->End_Date;
            $jsdt[$item->ID]['is_printable'] = $item->is_printable;
            $jsdt[$item->ID]['is_avbl_online'] = $item->is_avbl_online;
            $jsdt[$item->ID]['Source'] = $item->Source;
            $jsdt[$item->ID]['is_local_source'] = $item->is_local_source;
            $jsdt[$item->ID]['Store_ID'] = $item->Store_ID;
            $jsdt[$item->ID]['store_is_physical'] = $item->store_is_physical;
            $jsdt[$item->ID]['store_sell_online'] = $item->store_sell_online;
            //$jsdt[$item->ID]['Store_Name'] = $item->Store_Name;

            }

            echo '<li>
            <div>

            <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" value="' . ( $cdata = urlencode( json_encode( $jsdt ) ) ) . '"' . ( $imported || !$store_imported ? ' disabled' : '' ) . '/> <label for="id[' . $item->ID . ']"><span></span></label>

            <img src="' . \query\main::store_avatar( ( !empty( $item->Image ) ? $item->Image : $item->Store_Image ) ) . '" alt="" style="width:80px;" />

            <div class="info-div"><h2>' . ( $imported ? '<span class="msg-alert" title="' . t( 'added_through_feed_msg', "Added through feed." ) . '">' . t( 'added_through_feed', "Imported" ) . '</span> ' : '' ) . ( $item->is_active ? '<span class="msg-success">' . t( 'active', "Active" ) . '</span> ' : '<span class="msg-error">' . t( 'expired', "Expired" ) . '</span> ' ) . $item->Title . '</h2>
            ' . ( !$store_imported ? '<span class="msg-error">' . t( 'notadded_through_feed', "Not Imported" ) . '</span> ' : '' ) . '<a href="?route=feed.php&amp;action=coupons&amp;store=' . $item->Store_ID . '">' . $item->Store_Name . '</a>' . ( !$store_imported ? ' / <a href="?route=feed.php&amp;action=preview_store&amp;id=' . $item->Store_ID . '">' . t( 'preview_import', "Preview & Import" ) . '</a>' : '' ) . '</div>

            </div>

            <div style="clear:both;"></div>

            <div class="options">';
            if( !$imported && $store_imported && $feed_im ) {
                echo '<a href="?route=feed.php&amp;action=preview_coupon&amp;coupon=' . $cdata . '">' . t( 'preview_import', "Preview & Import" ) . '</a>';
            }

            if( !empty( $item->Description ) ) {
                echo '<a href="javascript:void(0)" onclick="$(this).show_next( { element: \'div\' } ); return false;">' . t( 'description', "Description" ) . '</a>';
                echo '<div style="display: none; margin: 10px 0; font-size: 12px;">' . nl2br( $item->Description ) . '</div>';
            }

            echo '</div>
            </li>';

        }

        echo '</ul>

        <input type="hidden" name="csrf" value="' . $csrf . '" />

        </form>';

        if( ( $pages = ceil( $coupons['Info']->Results / 10 ) ) > 1 ) {

        $page = isset( $_GET['page'] ) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
        $page = $page > $pages ? $pages : $page;

        echo '<div class="pagination">';

        if( $page > 1 )echo '<a href="' . \site\utils::update_uri( '', array( 'page' => $page - 1 ) ) . '" class="btn">' . t( 'prev_page', "&larr; Prev" ) . '</a>';
        if( $page < $pages )echo '<a href="' . \site\utils::update_uri( '', array( 'page' => $page + 1 ) ) . '" class="btn">' . t( 'next_page', "Next &rarr;" ) . '</a>';

        if( $pages > 1 ) {
        echo '<div class="pag_goto">' . sprintf( t( 'pageofpages', "Page <b>%s</b> of <b>%s</b>" ), $page, $pages ) . '
        <form action="#" method="GET">';
        foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . esc_html( $gk ) . '" value="' . esc_html( $gv ) . '" />';
        echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
        <button class="btn">' . t( 'go', "Go" ) . '</button>
        </form>
        </div>';
        }

        echo '</div>';

        }

        } else echo '<div class="a-alert">' . t( 'no_coupons_yet', "No coupons yet." ) . '</div>';

    }

    catch ( Exception $e ){
        echo '<div class="a-alert">' . $e->getMessage() . '</div>';
    }

}

catch ( Exception $e ) {
    echo '<div class="a-error">' . $e->getMessage() . '</div>';
}

break;

/* LIST OF FEED PRODUCTS */

case 'products':

if( !ab_to( array( 'feed' => 'view' ) ) ) die;

require_once 'includes/feed.php';

try {

    $feed = new admin\feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );
    $feed->exportAs( 'object' );

    try {

        $products = $feed->products( array( 'page' => (isset( $_GET['page'] ) ? $_GET['page'] : 1), 'per_page' => 10, 'orderby' => (isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'date desc'), 'store' => (isset( $_GET['store'] ) ? $_GET['store'] : ''), 'category' => (isset( $_GET['category'] ) ? $_GET['category'] : ''), 'search' => (isset( $_GET['search'] ) ? $_GET['search'] : '') ) );

        echo '<div class="title">

        <h2>' . t( 'products_title', "Products" ) . '</h2>

        <div style="float:right; margin: 0 2px 0 0;">';
        if( ab_to( array( 'feed' => 'import' ) ) ) echo '<a href="?route=feed.php&amp;action=import" class="btn">' . t( 'feed_icoupons', "Check/Update" ) . '</a>';
        echo '</div>';

        $subtitle = t( 'feed_products_subtitle' );

        if( !empty( $subtitle ) ) {
            echo '<span>' . $subtitle . '</span>';
        }

        echo '</div>';

        do_action( array( 'after_title_inner_page', 'after_title_feed_page', 'after_title_list_products_feed_page' ) );

        $csrf = $_SESSION['feed_import_csrf'] = \site\utils::str_random(10);

        echo '<div class="page-toolbar">

        <form action="#" method="GET" autocomplete="off">
        <input type="hidden" name="route" value="feed.php" />
        <input type="hidden" name="action" value="products" />

        ' . t( 'order_by', "Order by" ) . ':
        <select name="orderby">';
        foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'update' => t( 'order_last_update', "Last update" ), 'update desc' => t( 'order_last_update_desc', "Last update DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
        echo '</select> ';

        try {

            $category = $feed->categories();

            echo '<select name="category">
            <option value="">' . t( 'all_categories', "All categories" ) . '</option>';
            foreach( $category['List'] as $k => $v ) {

                echo '<optgroup label="' . $v->Name . '">';
                echo '<option value="' . $k . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $k ? ' selected' : '' ) . '>' . $v->Name . '</option>';
                if( isset( $v->Subcategories ) ) {
                    foreach( $v->Subcategories as $k1 => $v1 ) {
                        echo '<option value="' . $k1 . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $k1 ? ' selected' : '' ) . '>' . $v1->Name . '</option>';
                    }
                }
                echo '</optgroup>';
            }
            echo '</select>';

        }

        catch( Exception $e ) { }

        if( isset( $_GET['search'] ) ) {
            echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
        }

        echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

        </form>

        <form action="#" method="GET" autocomplete="off">
        <input type="hidden" name="route" value="feed.php" />
        <input type="hidden" name="action" value="products" />';

        if( isset( $_GET['orderby'] ) ) {
        echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
        }

        if( isset( $_GET['category'] ) ) {
        echo '<input type="hidden" name="category" value="' . esc_html( $_GET['category'] ) . '" />';
        }

        echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'products_search_input', "Search products" ) . '" />
        <button class="btn">' . t( 'search', "Search" ) . '</button>
        </form>

        </div>';

        echo '<div class="results">' . ( (int) $products['Info']->Results === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $products['Info']->Results ) : sprintf( t( 'results', "<b>%s</b> results" ), $products['Info']->Results ) );
        if( !empty( $_GET['store'] ) || !empty( $_GET['category'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=feed.php&amp;action=products">' . t( 'reset_view', "Reset view" ) . '</a>';
        echo '</div>';

        if( $products['Info']->Results ) {

        echo '<form action="?route=feed.php&amp;action=import_products" method="POST">

        <ul class="elements-list">

        <li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

        $feed_im    = ab_to( array( 'feed' => 'import' ) );

        if( $feed_im ) {

        echo '<div class="bulk_options">';

        echo t( 'category', "Category" ) . ':
        <select name="category">';
        foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
            echo '<optgroup label="' . $cat['info']->name . '">';
            echo '<option value="' . $cat['info']->ID . '">' . $cat['info']->name . '</option>';
            if( isset( $cat['subcats'] ) ) {
                foreach( $cat['subcats'] as $subcat ) {
                    echo '<option value="' . $subcat->ID . '">' . $subcat->name . '</option>';
                }
            }
            echo '</optgroup>';
        }
        echo '</select>

        <button class="btn">' . t( 'import_all', "Import All" ) . '</button>';
        echo '</div>';

        }

        foreach( $products['List'] as $item ) {

            $imported = admin\admin_query::product_imported( $item->ID );
            $store_imported = admin\admin_query::store_imported( $item->Store_ID );

            $jsdt = array();

            if( !$imported && $store_imported ) {

            $jsdt[$item->ID]['store'] = $store_imported->ID;
            $jsdt[$item->ID]['Title'] = $item->Title;
            $jsdt[$item->ID]['URL'] = $item->URL;
            $jsdt[$item->ID]['Price'] = $item->Price;
            $jsdt[$item->ID]['Old_Price'] = $item->Old_Price;
            $jsdt[$item->ID]['Currency'] = $item->Currency;
            $jsdt[$item->ID]['Tags'] = $item->Tags;
            $jsdt[$item->ID]['Description'] = $item->Description;
            $jsdt[$item->ID]['Image'] = $item->Image;
            $jsdt[$item->ID]['Start_Date'] = $item->Start_Date;
            $jsdt[$item->ID]['End_Date'] = $item->End_Date;
            $jsdt[$item->ID]['Store_ID'] = $item->Store_ID;
            //$jsdt[$item->ID]['Store_Name'] = $item->Store_Name;

            }

            echo '<li>
            <div>

            <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" value="' . ( $cdata = urlencode( json_encode( $jsdt ) ) ) . '"' . ( $imported || !$store_imported ? ' disabled' : '' ) . '/> <label for="id[' . $item->ID . ']"><span></span></label>

            <img src="' . \query\main::product_avatar( $item->Image ) . '" alt="" style="height: 50px; width: 50px;" />

            <div class="info-div"><h2>' . ( $imported ? '<span class="msg-alert" title="' . t( 'added_through_feed_msg', "Added through feed." ) . '">' . t( 'added_through_feed', "Imported" ) . '</span> ' : '' ) . ( $item->is_active ? '<span class="msg-success">' . t( 'active', "Active" ) . '</span> ' : '<span class="msg-error">' . t( 'expired', "Expired" ) . '</span> ' ) . $item->Title . '</h2>
            ' . ( !$store_imported ? '<span class="msg-error">' . t( 'notadded_through_feed', "Not Imported" ) . '</span> ' : '' ) . '<a href="?route=feed.php&amp;action=products&amp;store=' . $item->Store_ID . '">' . $item->Store_Name . '</a>' . ( !$store_imported ? ' / <a href="?route=feed.php&amp;action=preview_store&amp;id=' . $item->Store_ID . '">' . t( 'preview_import', "Preview & Import" ) . '</a>' : '' ) . '</div>

            </div>

            <div style="clear:both;"></div>

            <div class="options">';
            if( !$imported && $store_imported && $feed_im ) {
                echo '<a href="?route=feed.php&amp;action=preview_product&amp;product=' . $cdata . '">' . t( 'preview_import', "Preview & Import" ) . '</a>';
            }

            if( !empty( $item->Description ) ) {
            echo '<a href="javascript:void(0)" onclick="$(this).show_next( { element: \'div\' } ); return false;">' . t( 'description', "Description" ) . '</a>';
            echo '<div style="display: none; margin: 10px 0; font-size: 12px;">' . nl2br( $item->Description ) . '</div>';
            }

            echo '</div>
            </li>';

        }

        echo '</ul>

        <input type="hidden" name="csrf" value="' . $csrf . '" />

        </form>';

        if( ( $pages = ceil( $products['Info']->Results / 10 ) ) > 1 ) {

        $page = isset( $_GET['page'] ) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
        $page = $page > $pages ? $pages : $page;

        echo '<div class="pagination">';

        if( $page > 1 )echo '<a href="' . \site\utils::update_uri( '', array( 'page' => $page - 1 ) ) . '" class="btn">' . t( 'prev_page', "&larr; Prev" ) . '</a>';
        if( $page < $pages )echo '<a href="' . \site\utils::update_uri( '', array( 'page' => $page + 1 ) ) . '" class="btn">' . t( 'next_page', "Next &rarr;" ) . '</a>';

        if( $pages > 1 ) {
        echo '<div class="pag_goto">' . sprintf( t( 'pageofpages', "Page <b>%s</b> of <b>%s</b>" ), $page, $pages ) . '
        <form action="#" method="GET">';
        foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . esc_html( $gk ) . '" value="' . esc_html( $gv ) . '" />';
        echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
        <button class="btn">' . t( 'go', "Go" ) . '</button>
        </form>
        </div>';
        }

        echo '</div>';

        }

        } else echo '<div class="a-alert">' . t( 'no_products_yet', "No products yet." ) . '</div>';

    }

    catch ( Exception $e ){
        echo '<div class="a-alert">' . $e->getMessage() . '</div>';
    }

}

catch ( Exception $e ) {
    echo '<div class="a-error">' . $e->getMessage() . '</div>';
}

break;

/* LIST OF FEED STORES */

default:

if( !ab_to( array( 'feed' => 'view' ) ) ) die;

require_once 'includes/feed.php';

try {

    $feed = new admin\feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );
    $feed->exportAs( 'object' );

    try {

        $stores = $feed->stores( array( 'orderby' => (isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'date desc'), 'page' => (isset( $_GET['page'] ) ? $_GET['page'] : 1), 'per_page' => 10, 'category' => (isset( $_GET['category'] ) ? $_GET['category'] : ''), 'search' => (isset( $_GET['search'] ) ? $_GET['search'] : '') ) );

        echo '<div class="title">

        <h2>' . t( 'stores_title', "Stores" ) . '</h2>';

        $subtitle = t( 'feed_stores_subtitle' );

        if( !empty( $subtitle ) ) {
            echo '<span>' . $subtitle . '</span>';
        }

        echo '</div>';

        do_action( array( 'after_title_inner_page', 'after_title_feed_page', 'after_title_list_stores_feed_page' ) );

        $csrf = $_SESSION['feed_import_csrf'] = \site\utils::str_random(10);

        echo '<div class="page-toolbar">

        <form action="#" method="GET" autocomplete="off">
        <input type="hidden" name="route" value="feed.php" />
        <input type="hidden" name="action" value="list" />

        ' . t( 'order_by', "Order by" ) . ':
        <select name="orderby">';
        foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'update' => t( 'order_last_update', "Last update" ), 'update desc' => t( 'order_last_update_desc', "Last update DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
        echo '</select> ';

        try {

            $category = $feed->categories();

            echo '<select name="category">
            <option value="">' . t( 'all_categories', "All categories" ) . '</option>';
            foreach( $category['List'] as $k => $v ) {

                echo '<optgroup label="' . $v->Name . '">';
                echo '<option value="' . $k . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $k ? ' selected' : '' ) . '>' . $v->Name . '</option>';
                if( isset( $v->Subcategories ) ) {
                    foreach( $v->Subcategories as $k1 => $v1 ) {
                        echo '<option value="' . $k1 . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $k1 ? ' selected' : '' ) . '>' . $v1->Name . '</option>';
                    }
                }
                echo '</optgroup>';
            }
            echo '</select>';

        }

        catch( Exception $e ) { }

        if( isset( $_GET['search'] ) ) {
        echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
        }

        echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

        </form>

        <form action="#" method="GET" autocomplete="off">
        <input type="hidden" name="route" value="feed.php" />
        <input type="hidden" name="action" value="list" />';

        if( isset( $_GET['orderby'] ) ) {
        echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
        }

        if( isset( $_GET['category'] ) ) {
        echo '<input type="hidden" name="category" value="' . esc_html( $_GET['category'] ) . '" />';
        }

        echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'stores_search_input', "Search stores" ) . '" />
        <button class="btn">' . t( 'search', "Search" ) . '</button>
        </form>

        </div>';

        echo '<div class="results">' . ( (int) $stores['Info']->Results === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $stores['Info']->Results ) : sprintf( t( 'results', "<b>%s</b> results" ), $stores['Info']->Results ) );
        if( !empty( $_GET['category'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=feed.php&amp;action=list">' . t( 'reset_view', "Reset view" ) . '</a>';
        echo '</div>';

        if( $stores['Info']->Results ) {

        echo '<form action="?route=feed.php&amp;action=import_stores" method="POST">

        <ul class="elements-list">

        <li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

        $feed_im    = ab_to( array( 'feed' => 'import' ) );

        if( $feed_im ) {

        echo '<div class="bulk_options">';

        echo t( 'category', "Category" ) . ':
        <select name="category">';
        foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
            echo '<optgroup label="' . $cat['info']->name . '">';
            echo '<option value="' . $cat['info']->ID . '">' . $cat['info']->name . '</option>';
            if( isset( $cat['subcats'] ) ) {
                foreach( $cat['subcats'] as $subcat ) {
                    echo '<option value="' . $subcat->ID . '">' . $subcat->name . '</option>';
                }
            }
            echo '</optgroup>';
        }
        echo '</select>

        <input type="checkbox" name="coupons" value="1" id="import_t_coupons" checked /> <label for="import_t_coupons"><span></span> ' . t( 'feed_icouponstoo', "Import their coupons" ) . '</label>
        <input type="checkbox" name="products" value="1" id="import_t_products" checked /> <label for="import_t_products"><span></span> ' . t( 'feed_iproductstoo', "Import their products" ) . '</label>

        <button class="btn">' . t( 'import_all', "Import All" ) . '</button>';

        echo '</div>';

        }

        foreach( $stores['List'] as $item ) {

            $imported = admin\admin_query::store_imported( $item->ID );

            $jsdt = array();

            if( !$imported ) {

            $jsdt[$item->ID]['Name'] = $item->Name;
            $jsdt[$item->ID]['URL'] = $item->URL;
            $jsdt[$item->ID]['Tags'] = $item->Tags;
            $jsdt[$item->ID]['Description'] = $item->Description;
            $jsdt[$item->ID]['Image'] = $item->Image;
            $jsdt[$item->ID]['Phone'] = $item->Phone;
            $jsdt[$item->ID]['is_physical'] = $item->is_physical;
            $jsdt[$item->ID]['Hours'] = ( is_object( $item->Hours ) ? (array) get_object_vars( $item->Hours ) : array() );
            $jsdt[$item->ID]['Sell_Online'] = $item->Sell_Online;
            foreach( $item->Locations as $location ) {
                $jsdt[$item->ID]['Locations'][] = (array) get_object_vars( $location );
            }

            }

            echo '<li>
            <div>

            <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" value="' . ( $cdata = urlencode( json_encode( $jsdt ) ) ) . '"' . ( $imported ? ' disabled' : '' ) . ' /> <label for="id[' . $item->ID . ']"><span></span></label>

            <img src="' . \query\main::store_avatar( $item->Image ) . '" alt="" style="width: 80px;" />

            <div class="info-div"><h2>' . ( $imported ? '<span class="msg-alert" title="' . t( 'added_through_feed_msg', "Added through feed." ) . '">' . t( 'added_through_feed', "Imported" ) . '</span> ' : '' ) . $item->Name . '</h2>
            ' . ( empty( $item->Coupons ) ? t( 'no_coupons_store', "No coupons yet" ) : '<a href="?route=feed.php&amp;action=coupons&amp;store=' . $item->ID . '">' . sprintf( t( 'nr_coupons_store', "%s coupons" ), $item->Coupons ) . '</a>' ) . '
            <br />
            ' . ( empty( $item->Products ) ? t( 'no_products_store', "No products yet" ) : '<a href="?route=feed.php&amp;action=products&amp;store=' . $item->ID . '">' . sprintf( t( 'nr_products_store', "%s products" ), $item->Products ) . '</a>' ) . '

            </div>

            </div>

            <div style="clear:both;"></div>

            <div class="options">';
            if( !$imported && $feed_im ) {
                echo '<a href="?route=feed.php&amp;action=preview_store&amp;store=' . $cdata . '">' . t( 'preview_import', "Preview & Import" ) . '</a>';
            }

            if( !empty( $item->Description ) ) {
                echo '<a href="javascript:void(0)" onclick="$(this).show_next( { element: \'div\' } ); return false;">' . t( 'description', "Description" ) . '</a>';
                echo '<div style="display: none; margin: 10px 0; font-size: 12px;">' . nl2br( $item->Description ) . '</div>';
            }

            echo '</div>
            </li>';

        }

        echo '</ul>

        <input type="hidden" name="csrf" value="' . $csrf . '" />

        </form>';

        if( ( $pages = ceil( $stores['Info']->Results / 10 ) ) > 1 ) {

        $page = isset( $_GET['page'] ) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
        $page = $page > $pages ? $pages : $page;

        echo '<div class="pagination">';

        if( $page > 1 )echo '<a href="' . \site\utils::update_uri( '', array( 'page' => $page - 1 ) ) . '" class="btn">' . t( 'prev_page', "&larr; Prev" ) . '</a>';
        if( $page < $pages )echo '<a href="' . \site\utils::update_uri( '', array( 'page' => $page + 1 ) ) . '" class="btn">' . t( 'next_page', "Next &rarr;" ) . '</a>';

        if( $pages > 1 ) {
        echo '<div class="pag_goto">' . sprintf( t( 'pageofpages', "Page <b>%s</b> of <b>%s</b>" ), $page, $pages ) . '
        <form action="#" method="GET">';
        foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . esc_html( $gk ) . '" value="' . esc_html( $gv ) . '" />';
        echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
        <button class="btn">' . t( 'go', "Go" ) . '</button>
        </form>
        </div>';
        }

        echo '</div>';

        }

        } else echo '<div class="a-alert">' . t( 'no_stores_yet', "No stores yet." ) . '</div>';

    }

    catch ( Exception $e ) {
        echo '<div class="a-alert">' . $e->getMessage() . '</div>';
    }

}

catch ( Exception $e ) {
    echo '<div class="a-error">' . $e->getMessage() . '</div>';
}

break;

}