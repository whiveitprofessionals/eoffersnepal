<?php

/* THERE OPTIONS */
if( !function_exists( 'theme_options' ) ) {
    function theme_options() {
        // id => options
        $options = array();
        $options['search_title']    = array( 'type' => 'text',      'title' => t( 'theme_options_search_title', 'Search Box Title' ),   'placeholder' => 'Search for coupons, products or stores' );
        $options['search_image']    = array( 'type' => 'image',     'title' => t( 'theme_options_search_image', 'Search Box Image' ),   'cat_id' => 'to', 'info' => t( 'theme_options_search_image_info', 'Background image used for search box.' ) );
        $options['contact_tel']     = array( 'type' => 'text',      'title' => t( 'theme_options_contact_phone', 'Contact (tel)' ),     'placeholder' => '(123) 123-1234' );
        $options['contact_email']   = array( 'type' => 'text',      'title' => t( 'theme_options_contact_email', 'Contact (email)' ),   'placeholder' => 'contact@example.com' );
        $options['date_format']     = array( 'type' => 'text',      'title' => t( 'theme_options_date_format', 'Date Format' ),         'default' => 'd.m.Y', 'info' => t( 'theme_options_date_format_info', 'Default date format is: d.m.Y' ) );
        $options['map_zoom']        = array( 'type' => 'number',    'title' => t( 'theme_options_map_zoom', 'Map Zoom' ),               'default' => 16 );
        $options['map_marker_icon'] = array( 'type' => 'image',     'title' => t( 'theme_options_map_marker_icon', 'Map Marker Icon' ), 'default' => THEME_LOCATION . '/assets/img/pin.png', 'cat_id' => 'to' );
        $options['site_multilang']  = array( 'type' => 'checkbox',  'title' => t( 'theme_options_multilang', 'Multi Language' ),        'label' => t( 'theme_options_multilang_label', 'Display language switcher with flags' ) );
        $options['items_on_index']  = array( 'type' => 'text',      'title' => t( 'theme_options_index_items', 'Items On Index Page' ), 'multi' => true, 'sortable' => true, 'info' => t( 'theme_options_index_items_info', 'Display types: coupons, products or stores. Accept inline options separated with pipes, format: type|limit|where|order by. Example: coupons|15|active,printable|date' ) );
        $options['extra_css']       = array( 'type' => 'textarea',  'title' => t( 'theme_options_extra_css', 'Extra CSS' ) );
        $options['extra_js']        = array( 'type' => 'textarea',  'title' => t( 'theme_options_extra_js', 'Extra JS' ) );

        return $options;
    }
}

/* CUSTOM CATEGORY FOR THEME OPTIONS IN GALLERY */

add( 'filter', 'gallery-categories', function( $cats ) {
    $cats['to'] = t( 'themes_options', 'Theme Options' );
    return $cats;
});