<?php

$types = array();
$types['recently']      = array( 'label' => t( 'theme_coupons_recently_added', 'Recently Added' ),  'url' => get_remove( array( 'page', 'type' ) ),                                                 'orderby' => 'date desc',   'show' => 'active',             'limit' => 100 );
$types['expiring']      = array( 'label' => t( 'theme_coupons_expiring_soon', 'Expiring Soon' ),    'url' => get_update( array( 'type' => 'expiring' ), get_remove( array( 'page', 'type' ) ) ),    'orderby' => 'expiration',  'show' => 'active',             'limit' => 100 );
$types['printable']     = array( 'label' => t( 'theme_coupons_printable', 'Printable' ),            'url' => get_update( array( 'type' => 'printable' ), get_remove( array( 'page', 'type' ) ) ),   'orderby' => '',            'show' => 'active,printable',   'limit' => 100 );
$types['codes']         = array( 'label' => t( 'theme_coupons_codes', 'Coupon Codes' ),             'url' => get_update( array( 'type' => 'codes' ), get_remove( array( 'page', 'type' ) ) ),       'orderby' => '',            'show' => 'active,codes',       'limit' => 100 );
$types['exclusive']     = array( 'label' => t( 'theme_coupons_exclusive', 'Exclusive' ),            'url' => get_update( array( 'type' => 'exclusive' ), get_remove( array( 'page', 'type' ) ) ),   'orderby' => '',            'show' => 'active,exclusive',   'limit' => 100 );
$types['popular']       = array( 'label' => t( 'theme_coupons_popular', 'Popular' ),                'url' => get_update( array( 'type' => 'popular' ), get_remove( array( 'page', 'type' ) ) ),     'orderby' => '',            'show' => 'active,popular',     'limit' => 100 );
$types['verified']      = array( 'label' => t( 'theme_coupons_verified', 'Verified' ),              'url' => get_update( array( 'type' => 'verified' ), get_remove( array( 'page', 'type' ) ) ),    'orderby' => '',            'show' => 'active,verified',    'limit' => 100 );

$type = isset( $_GET['type'] ) && in_array( $_GET['type'], array_keys( $types ) ) ? $_GET['type'] : 'recently';

$atts = array();
$atts['show'] = $types[$type]['show'];
$atts['limit'] = $types[$type]['limit'];

$pagination = have_items_custom( $atts );

?>

<div class="container pt50 pb50">

<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <h2><?php te( 'coupons', 'Coupons' ); ?></h2>
            <?php echo '<span>' . $types[$type]['label'] . ( !$pagination['results'] ? ' - ' . t( 'theme_no_coupons_yet', 'No coupons yet.' ) : '' ) . '</span>'; ?>
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
            foreach( items_custom( ( array( 'orderby' => $types[$type]['orderby'] ) + $atts ) ) as $item ) {
                echo couponscms_coupon_item( $item );
            }

            echo couponscms_theme_pagination( $pagination );

        } else echo '<div class="alert">' . t( 'theme_no_coupons_list',  'Huh :( No coupons found here.' ) . '</div>'; ?>
    </div>

    <div class="col-md-4">
        <?php echo do_action( 'before_widgets_right' );
        echo show_widgets( 'right' );
        echo do_action( 'after_widgets_right' ); ?>
    </div>
</div>

</div>