<?php

if( !isset( $_GET['secret'] ) || $_GET['secret'] !== \query\main::get_option( 'cron_secret' ) ) {
  die( 'Unauthorized' );
}

require_once DIR . '/' . ADMINDIR . '/includes/actions.php';

admin\actions::cleardata( array(
'coupons' => array( 'status' => ( isset( $_GET['coupons'] ) && $_GET['coupons'] === 'true' ? 1 : 0 ), 'interval' => (int) \query\main::get_option( 'delete_old_coupons' ) ),
'products' => array( 'status' => ( isset( $_GET['products'] ) && $_GET['products'] === 'true' ? 1 : 0 ), 'interval' => (int) \query\main::get_option( 'delete_old_products' ) ) )
);

echo 'OK';