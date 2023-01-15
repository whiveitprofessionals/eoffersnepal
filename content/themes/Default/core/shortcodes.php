<?php

/* LOAD SHORTCODE FILES */
$shortcode_files = glob( COUPONSCMS_CORE_LOCATION . '/shortcodes/shortcode_*.php' );
foreach( $shortcode_files as $file ) {
    require_once $file;
}