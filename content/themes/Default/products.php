<?php

$types = array();
$types['recent']        = array( 'label' => t( 'theme_products_recently_added', 'Recently Added' ),  'url' => get_update( array( 'type' => 'recent' ), get_remove( array( 'page' ) ) ),             'orderby' => 'date desc',      'show' => 'visible',         'limit' => 100 );                                      
$types['expiring']      = array( 'label' => t( 'theme_products_expiring_soon', 'Expiring Soon' ),    'url' => get_update( array( 'type' => 'expiring' ), get_remove( array( 'page', 'type' ) ) ),   'orderby' => 'expiration',     'show' => 'visible',         'limit' => 100 );
$types['popular']       = array( 'label' => t( 'theme_products_popular', 'Popular' ),                'url' => get_update( array( 'type' => 'popular' ), get_remove( array( 'page', 'type' ) ) ),    'orderby' => '',               'show' => 'visible,popular', 'limit' => 100 );

$type = isset( $_GET['type'] ) && in_array( $_GET['type'], array_keys( $types ) ) ? $_GET['type'] : 'recent';

$atts = array();
$atts['show'] = $types[$type]['show'];
$atts['limit'] = $types[$type]['limit'];

$pagination = have_products_custom( $atts );

?>

<div class="container pt50 pb50">

<div class="row">                                                                                                                                                                
    <div class="col-md-12">
        <div class="widget">
            <h2><?php te( 'products', 'Products' ); ?></h2>
            <?php echo '<span>' . $types[$type]['label'] . ( !$pagination['results'] ? ' - ' . t( 'theme_no_products_yet', 'No products yet.' ) : '' ) . '</span>'; ?>
        </div>
    </div>
</div>

<div class="row mb15">

    <div class="col-md-12 text-center-m">
        <ul class="button-set">
            <?php foreach( $types as $type_id => $type_nav ) {
                echo '<li' . ( $type_id == $type ? ' class="selected"' : '' ) . '><a href="' . $type_nav['url'] . '">' . $type_nav['label'] . '</a></li>';
            } ?>
        </ul>
    </div>

</div>

<div class="row">
    <div class="col-md-8">
        <?php if( $pagination['results'] ) {
            foreach( products_custom( ( array( 'orderby' => $types[$type]['orderby'] ) + $atts ) ) as $item ) {
                echo couponscms_product_item( $item );
            }

            echo couponscms_theme_pagination( $pagination );

        } else echo '<div class="alert">' . t( 'theme_no_products_list',  'Huh :( No products found here.' ) . '</div>'; ?>
    </div>

    <div class="col-md-4">
        <?php echo do_action( 'before_widgets_right' );
        echo show_widgets( 'right' );
        echo do_action( 'after_widgets_right' ); ?>
    </div>
</div>

</div>