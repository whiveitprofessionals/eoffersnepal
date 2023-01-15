<?php

$category = the_item();

$atts = array();
if( !empty( $_GET['active'] ) ) {
    $atts['active'] = true;
}

have_items( $atts );

$type = searched_type();

?>

<div class="container pt50 pb50">

<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <h2><?php tse( $category->name ); ?></h2>
            <?php if( !empty( $category->description ) ) echo '<span>' . ts( $category->description ) . '</span>'; ?>
        </div>
    </div>
</div>

<div class="row mb15">

    <div class="col-md-<?php echo ( $type == 'stores' ? 12 : 6 ); ?> text-center-m">
        <?php $types = array();
        $types['coupons'] = array( 'label' => t( 'coupons', 'Coupons' ), 'url' => get_update( array( 'type' => 'coupons' ), get_remove( array( 'page', 'type' ) ) ) );
        if( couponscms_has_products() ) {
            $types['products'] = array( 'label' => t( 'products', 'Products' ), 'url' => get_update( array( 'type' => 'products' ), get_remove( array( 'page', 'type' ) ) ) );
        }
        $types['stores'] = array( 'label' => t( 'stores', 'Stores' ), 'url' => get_update( array( 'type' => 'stores' ), get_remove( array( 'page', 'type' ) ) ) );
        ?>
        <ul class="button-set">
            <?php foreach( $types as $type_id => $type_nav ) {
                echo '<li' . ( $type_id == $type ? ' class="selected"' : '' ) . '><a href="' . $type_nav['url'] . '">' . $type_nav['label'] . '</a></li>';
            } ?>
        </ul>
    </div>

    <?php if( results() && $type != 'stores' ) { ?>
    <div class="col-md-6 mt5 text-right text-center-m">
        <input type="checkbox" name="active" id="active" class="checkbox" data-href="<?php echo ( !empty( $_GET['active'] ) ? get_remove( array( 'page', 'active' ) ) : get_update( array( 'active' => 1 ), get_remove( array( 'page' ) ) ) ); ?>"<?php echo ( !empty( $_GET['active'] ) ? ' checked' : '' ); ?>> <label for="active"><?php te( 'theme_show_active_only', 'Active only' ); ?></label>
    </div>
    <?php } ?>

</div>

<div class="row">

<div class="col-md-8">

    <?php if( $type === 'products' ) {

    if( results() ) {

        foreach( items( ( $atts + array( 'orderby' => 'date desc' ) ) ) as $item ) {
            echo couponscms_product_item( $item );
        }

        echo couponscms_theme_pagination( navigation() );

    } else {

        echo '<div class="alert">' . t( 'theme_no_products_category',  'No products in this category.' ) . '</div>';

        foreach( products_custom( array( 'show' => 'visible,active', 'orderby' => 'rand', 'max' => option( 'items_per_page' ) ) ) as $item ) {
            echo couponscms_product_item( $item );
        }

    }

    } else if( $type === 'stores' ) {

    if( results() ) {

        foreach( items( array( 'orderby' => 'date desc' ) ) as $item ) {
            echo couponscms_store_item( $item );
        }

        echo couponscms_theme_pagination( navigation() );

    } else {

        echo '<div class="alert">' . t( 'theme_no_stores_category',  'No stores in this category.' ) . '</div>';

        foreach( stores_custom( array( 'show' => 'visible,active', 'orderby' => 'rand', 'max' => option( 'items_per_page' ) ) ) as $item ) {
            echo couponscms_store_item( $item );
        }

    }

    } else {

    if( results() ) {

        foreach( items( ( $atts + array( 'orderby' => 'date desc' ) ) ) as $item ) {
            echo couponscms_coupon_item( $item );
        }

        echo couponscms_theme_pagination( navigation() );

    } else {

        echo '<div class="alert">' . t( 'theme_no_coupons_category',  'No coupons in this category.' ) . '</div>';

        foreach( items_custom( array( 'show' => 'visible,active', 'orderby' => 'rand', 'max' => option( 'items_per_page' ) ) ) as $item ) {
            echo couponscms_coupon_item( $item, false, true );
        }

    }

    } ?>

</div>

<div class="col-md-4">
    <?php echo do_action( 'before_widgets_right' );
    echo show_widgets( 'right' );
    echo do_action( 'after_widgets_right' ); ?>
</div>

</div>

</div>