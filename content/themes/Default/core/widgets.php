<?php

/* REGISTER WIDGETS */
if( !function_exists( 'register_widgets' ) ) {
    function register_widgets() {
        // section id => options
        $widgets = array();
        $widgets['right'] = array( 'name' => 'Right Side Widgets', 'description' => 'Appears on the right side of the page.' );
        $widgets['featured_top'] = array( 'name' => 'Featured Top Widgets', 'description' => 'Appears on top of index page.' );
        $widgets['featured_bottom'] = array( 'name' => 'Featured Bottom Widgets', 'description' => 'Appears on bottom of index page.' );
        return $widgets;
    }
}

/* REMOVE UNWANTED  WIDGETS FOR "FEATURED" */
remove( 'widgets', array( 'featured_top' => array( '*' ), 'featured_bottom' => array( '*' ) ) );

/* ADD CUSTOM WIDGETS FOR "FEATURE" */

$common_widget_fields = array();

$common_widget_fields['autoplay'] = array( 'type' => 'checkbox', 'title' => 'Autoplay', 'label' => 'Check to allow autoplay' );
$common_widget_fields['loop'] = array( 'type' => 'checkbox', 'title' => 'Loop', 'label' => 'Check to display elements continually' );
$common_widget_fields['arrows'] = array( 'type' => 'checkbox', 'title' => 'Navigation Arrows', 'label' => 'Check to display navigation arrows' );
$common_widget_fields['bullets'] = array( 'type' => 'checkbox', 'title' => 'Navigation Bullets', 'label' => 'Check to display navigation bullets' );

// Add featured stores
$widget = array();
$widget['stores'] = array( 'type' => 'stores', 'title' => 'Stores List', 'multi' => true, 'sortable' => true );
$add_widget = array( 'zone' => array( 'featured_top', 'featured_bottom' ), 'extra_fields' => ( $widget + $common_widget_fields ), 'name' => 'Featured Stores', 'file' => COUPONSCMS_CORE_LOCATION . '/widgets/featured_stores.php', 'position' => true, 'allow_limit' => false );
add( 'widgets', 'featured_stores_list', $add_widget );

// Add featured coupons
$widget = array();
$widget['coupons'] = array( 'type' => 'coupons', 'title' => 'Coupons List', 'multi' => true, 'sortable' => true );
$add_widget = array( 'zone' => array( 'featured_top', 'featured_bottom' ), 'extra_fields' => ( $widget + $common_widget_fields ), 'name' => 'Featured Coupons', 'file' => COUPONSCMS_CORE_LOCATION . '/widgets/featured_coupons.php', 'position' => true, 'allow_limit' => false );
add( 'widgets', 'featured_coupons_list', $add_widget );

// Add featured products
$widget = array();
$widget['products'] = array( 'type' => 'products', 'title' => 'Products List', 'multi' => true, 'sortable' => true );
$add_widget = array( 'zone' => array( 'featured_top', 'featured_bottom' ), 'extra_fields' => ( $widget + $common_widget_fields ), 'name' => 'Featured Products', 'file' => COUPONSCMS_CORE_LOCATION . '/widgets/featured_products.php', 'position' => true, 'allow_limit' => false );
add( 'widgets', 'featured_products_list', $add_widget );
