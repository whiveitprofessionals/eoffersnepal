<?php

if( !isset( $_GET['secret'] ) || $_GET['secret'] !== \query\main::get_option( 'cron_secret' ) ) {
  die( 'Unauthorized' );
}

require_once DIR . '/' . ADMINDIR . '/includes/feed.php';
require_once DIR . '/' . ADMINDIR . '/includes/actions.php';
require_once DIR . '/' . ADMINDIR . '/includes/admin_query.php';

try {

  $feed = new admin\feed( \query\main::get_option( 'feedserver_ID' ), \query\main::get_option( 'feedserver_secret' ) );

  $ids = array();
  foreach( \query\main::while_stores( array( 'max' => 0, 'show' => 'feed' ) ) as $store ) {
    $ids[] = $store->feedID;
  }

  $ca = $cae = $cu = $cue = $pa = $pae = $pu = $pue = 0;

  if( !empty( $ids ) ) {

  $last_check = \site\utils::timeconvert( date( 'Y-m-d H:i:s', \query\main::get_option( 'lfeed_check' ) ), $feed->timezone );

  /* CHECK FOR UPDATES FIRST */

  if( (boolean) \query\main::get_option( 'feed_moddt' ) ) {

  /* UPDATE COUPONS */

  try {

    $coupons = $feed->coupons( $options = array( 'store' => implode( ',', array_values( $ids ) ), 'update' => $last_check ) );

    if( !empty( $coupons['Info']['Results'] ) ) {

    for( $cp = 1; $cp <= $coupons['Info']['Results']; $cp++ ) {

    if( $cp != 1 ) {
        $coupons = $feed->coupons( array_merge( array( 'page' => $cp ), $options ) );
    }

    if( isset( $coupons['List'] ) ) {
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
    }

    usleep( 200000 ); // put a break after every page

    }

    }

  }

  catch( Exception $e ) { }

  /*

  UPDATE PRODUCTS

  */

  try {

    $products = $feed->products( $options = array( 'store' => implode( ',', array_values( $ids ) ), 'update' => $last_check ) );

    if( !empty( $products['Info']['Results'] ) ) {

    for( $cp = 1; $cp <= $products['Info']['Pages']; $cp++ ) {

    if( $cp != 1 ) {
      $products = $feed->products( array_merge( array( 'page' => $cp ), $options ) );
    }

    if( isset( $products['List'] ) ) {
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
    }

    usleep( 200000 ); // put a break after every page

    }

    }

  }

  catch( Exception $e ) { }

  }

  /* IMPORT COUPONS */

  if( !isset( $_GET['omit_coupons'] ) || $_GET['omit_coupons'] !== 'true' ) {

  try {

    $coupons = $feed->coupons( $options = array( 'per_page' => 30, 'store' => implode( ',', array_values( $ids ) ), 'view' => (!(boolean) \query\main::get_option( 'feed_iexpc' ) ? 'active' : ''), 'date' => $last_check ) );

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
        'feedID' => $coupon['ID'],
        'store' => $store->ID,
        'category' => $store->catID,
        'popular' => 0,
        'exclusive' => 0,
        'printable' => $coupon['is_printable'],
        'show_in_store' => 0,
        'available_online' => $coupon['is_avbl_online'],
        'name' => $coupon['Title'],
        'link' => ( filter_var( $coupon['URL'], FILTER_VALIDATE_URL ) ? $coupon['URL'] : '' ),
        'code' => $coupon['Code'],
        'claim_limit' => 0,
        'source' => $coupon['Source'],
        'description' => $coupon['Description'],
        'tags' => $coupon['Tags'],
        'cashback' => 0,
        'start' => $coupon['Start_Date'],
        'end' => $coupon['End_Date'],
        'image' => array(),
        'votes' => 0,
        'votes_average' => 0,
        'verified' => 0,
        'last_verif' => date( 'Y-m-d H:i:s' ),
        'publish' => 1,
        'meta_title' => '',
        'meta_keywords' => '',
        'meta_desc' => '',
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

  if( !isset( $_GET['omit_products'] ) || $_GET['omit_products'] !== 'true' ) {

  try {

    $products = $feed->products( $options = array( 'store' => implode( ',', array_values( $ids ) ), 'view' => (!(boolean) \query\main::get_option( 'feed_iexpp' ) ? 'active' : ''), 'date' => $last_check ) );

    if( !empty( $products['Info']['Results'] ) ) {

    $impimg = (boolean) \query\main::get_option( 'feed_uppics' );

    for( $cp = 1; $cp <= $products['Info']['Pages']; $cp++ ) {

    if( $cp != 1 ) {
      $products = $feed->products( array_merge( array( 'page' => $cp ), $options ) );
    }

    foreach( $products['List'] as $product ) {

    if( !admin_query::product_imported( $product['ID'] ) ) {

    if( ( $store = admin_query::store_imported( $product['Store_ID'] ) ) &&
    admin\actions::add_product(
    array(
        'feedID' => $product['ID'],
        'store' => $store->ID,
        'category' => $store->catID,
        'popular' => 0,
        'name' => $product['Title'],
        'price' => $product['Price'],
        'old_price' => $product['Old_Price'],
        'currency' => strtoupper( $product['Currency'] ),
        'link' => ( filter_var( $product['URL'], FILTER_VALIDATE_URL ) ? $product['URL'] : '' ),
        'description' => $product['Description'],
        'tags' => $product['Tags'],
        'cashback' => 0,
        'start' => $product['Start_Date'],
        'end' => $product['End_Date'],
        'publish' => 1,
        'import_image' => $impimg,
        'image_url' => $product['Image'],
        'image' => '',
        'meta_title' => '',
        'meta_keywords' => '',
        'meta_desc' => '',
        'extra' => array()
    ) ) ) {
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

  // you can use $ca, $cae, $cu, $cue, $pa, $pae, $pu, $pue variables to create logs or something ...

  }

  echo 'OK';

}

catch( Exception $e ) {
  echo $e->getMessage();
}