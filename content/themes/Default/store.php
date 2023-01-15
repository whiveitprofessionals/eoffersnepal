<?php

$store = the_item();

$atts = array();
if( !empty( $_GET['active'] ) ) {
    $atts[] = 'active';
}

$type = searched_type();

?>

<div class="container pt50 pb50">

<?php echo do_action( 'store_before_info' ); ?>

<div class="row">
    <div class="col-md-12">
        <div class="widget item-text">
            <div class="avatar">
                <img src="<?php echo store_avatar( ( !empty( $store->image ) ? $store->image : '' ) ); ?>" alt="" />
            </div>
            <div class="info">
                <h2 class="clearfix"><?php tse( $store->name ); ?>
                    <?php if( !empty( $store->reviews ) ) { ?>
                        <div class="rating"><a href="<?php echo $store->reviews_link; ?>"><?php echo couponscms_rating( (int) $store->stars, $store->reviews ); ?></a></div>
                    <?php } ?>
                </h2>
                <?php echo ( !empty( $store->description ) ? '<span>' . ts( $store->description ) . '</span>' : t( 'theme_no_description', 'No description.' ) ); ?>

                <ul class="links-list">
                    <li><a href="#" data-ajax-call="<?php echo ajax_call_url( "favorite" ); ?>" data-data='<?php echo json_encode( array( 'store' => $store->ID, 'added_message' => '<i class="fa fa-heart"></i> ' . t( 'theme_remove_favorite', 'Remove favorite' ), 'removed_message' => '<i class="fa fa-heart-o"></i> ' . t( 'theme_add_favorite', 'Add favorite' ) ) ); ?>'><?php echo ( is_favorite( $store->ID ) ? '<i class="fa fa-heart"></i> ' . t( 'theme_remove_favorite', 'Remove favorite' ) : '<i class="fa fa-heart-o"></i> ' . t( 'theme_add_favorite', 'Add favorite' ) ); ?></a></li>
                    <li><a href="#" data-ajax-call="<?php echo ajax_call_url( "save" ); ?>" data-data='<?php echo json_encode( array( 'item' => $store->ID, 'type' => 'store', 'added_message' => '<i class="fa fa-star"></i> ' . t( 'theme_unsave_store', 'Unsave this store' ), 'removed_message' => '<i class="fa fa-star-o"></i> ' . t( 'theme_save_store', 'Save this store' ) ) ); ?>'><?php echo ( is_saved( $store->ID, 'store' ) ? '<i class="fa fa-star"></i> ' . t( 'theme_unsave_store', 'Unsave this store' ) : '<i class="fa fa-star-o"></i> ' . t( 'theme_save_store', 'Save this store' ) ); ?></a></li>
                    <li><a href="<?php echo tlink( 'plugin/rss2', 'store=' . $store->ID ); ?>"><i class="fa fa-rss"></i> <?php te( 'theme_store_rss', 'RSS Feed' ); ?></a></li>
                    <li class="line-after"><a href="<?php echo $store->reviews_link; ?>"><i class="fa fa-pencil"></i> <?php te( 'theme_write_review', 'Write Review' ); ?></a></li>
                    <?php if( !empty( $store->url ) ) { ?>
                    <li><a href="<?php echo get_target_link( 'store', $store->ID ); ?>"><i class="fa fa-external-link"></i> <?php te( 'theme_store_visit', 'Visit Website' ); ?></a></li>
                    <?php }
                    if( $store->is_physical ) {
                    if( !empty( $store->hours ) ) {
                        $today = strtolower( date( 'l' ) ); ?>
                        <li><a href="#" class="hours"><i class="fa fa-clock-o"></i> <?php echo sprintf( t( 'theme_store_hours_today', 'Hours ( Today: %s )' ),  ( isset( $store->hours[$today]['opened'] ) ? $store->hours[$today]['from'] . ' - ' . $store->hours[$today]['to'] :  t( 'theme_store_closed', 'Closed' ) ) ); ?></a>
                        <?php
                            $daysofweek = days_of_week();
                            echo '<ul class="store-hours">';
                            foreach( $daysofweek as $day => $dayn ) {
                                echo '<li' . ( $day === $today ? ' class=\'htoday\'' : '' ) . '><span>' . $dayn . ':</span> <b>' . ( isset( $store->hours[$day]['opened'] ) ? $store->hours[$day]['from'] . ' - ' . $store->hours[$day]['to'] :  t( 'theme_store_closed', 'Closed' ) ) . '</b></li>';
                            }
                            echo '</ul></li>';
                        ?>
                        </li>
                    <?php }
                        $locations = store_locations( $store->ID );
                        if( !empty( $locations ) ) {
                            echo '<li><i class="fa fa-map-marker"></i> <ul class="store-locations">';
                            foreach( $locations as $location ) {
                                echo '<li data-lat="' . $location->lat . '" data-lng="' . $location->lng . '" data-title="' . implode( ', ', array( $location->city, $location->state ) ) . '" data-content="' . implode( ', ', array( $location->address, $location->zip ) ) . '">
                                    <a href="#" data-map-recenter="' . $location->lat . ',' . $location->lng . '">
                                        ' . implode( ', ', array( $location->address, $location->zip, $location->city, $location->state, $location->country ) ) . '
                                    </a>
                                </li>';
                            }
                            echo '</ul></li>';
                        }
                    } ?>
                    <?php if( !empty( $store->phone_no ) ) { ?>
                    <li><i class="fa fa-phone"></i> <?php echo sprintf( t( 'theme_phone_no', 'Phone Number: %s' ), $store->phone_no ); ?></li>
                    <?php } ?>
                </ul>
                <div class="item-share">
                    <?php echo sprintf( t( 'theme_store_share_links', 'Share: %s' ), couponscms_share_links( $store->link ) ); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo do_action( 'store_after_info' );

if( google_maps() && !empty( $locations ) ) {
$map_zoom = get_theme_option( 'map_zoom' );
$map_marker_icon = get_theme_option( 'map_marker_icon' ); ?>
<div id="map_wrapper" class="widget">
    <?php if( !filter_var( $map_marker_icon, FILTER_VALIDATE_URL ) ) {
        $custom_marker = @json_decode( $map_marker_icon ); 
        if( $custom_marker ) {
            $map_marker_icon = site_url( current( $custom_marker ) );    
        }
    } ?>
    <div id="map_canvas" data-zoom="<?php echo ( !empty( $map_zoom ) && is_numeric( $map_zoom ) ? (int) $map_zoom : 16 ); ?>" data-lat="<?php echo $locations[0]->lat; ?>" data-lng="<?php echo $locations[0]->lng; ?>" data-marker-icon="<?php echo ( !empty( $map_marker_icon ) ? $map_marker_icon : THEME_LOCATION . '/assets/img/pin.png' ); ?>"></div>
</div>
<?php } ?>

<div class="row mb15">

    <div class="col-md-<?php echo ( $type == 'stores' ? 12 : 6 ); ?> text-center-m">
        <?php $types = array();
        $types['coupons'] = array( 'label' => t( 'coupons', 'Coupons' ), 'url' => get_update( array( 'type' => 'coupons' ), get_remove( array( 'page', 'type' ) ) ) );
        if( couponscms_has_products() ) {
            $types['products'] = array( 'label' => t( 'products', 'Products' ), 'url' => get_update( array( 'type' => 'products' ), get_remove( array( 'page', 'type' ) ) ) );
        } ?>
        <ul class="button-set">
            <?php foreach( $types as $type_id => $type_nav ) {
                echo '<li' . ( $type_id == $type ? ' class="selected"' : '' ) . '><a href="' . $type_nav['url'] . '">' . $type_nav['label'] . '</a></li>';
            } ?>
        </ul>
    </div>

    <?php if( $type != 'stores' ) { ?>
    <div class="col-md-6 mt5 text-right text-center-m">
        <input type="checkbox" name="active" id="active" class="checkbox" data-href="<?php echo ( !empty( $_GET['active'] ) ? get_remove( array( 'page', 'active' ) ) : get_update( array( 'active' => 1 ), get_remove( array( 'page' ) ) ) ); ?>"<?php echo ( !empty( $_GET['active'] ) ? ' checked' : '' ); ?>> <label for="active"><?php te( 'theme_show_active_only', 'Active only' ); ?></label>
    </div>
    <?php } ?>

</div>

<div class="row">

<div class="col-md-8">

<?php echo do_action( 'store_before_items' );

if( $type === 'products' ) {

    if( ( $results = have_products( array( 'show' => ( !empty( $atts ) ? implode( ',', $atts ) : '' ) ) ) ) && $results['results'] ) {
        foreach( products( array( 'show' => ( !empty( $atts ) ? implode( ',', $atts ) : '' ), 'orderby' => 'date desc' ) ) as $item ) {
            echo couponscms_product_item( $item );
        }
        echo couponscms_theme_pagination( $results );
    } else {
        echo '<div class="alert">' . sprintf( t( 'theme_no_products_store',  '%s has no products yet.' ), ts( $store->name ) ) . '</div>';
        foreach( products_custom( array( 'show' => 'visible,active', 'orderby' => 'rand', 'max' => option( 'items_per_page' ) ) ) as $item ) {
            echo couponscms_product_item( $item );
        }
    }

} else {

    if( ( $results = have_items( array( 'show' => ( !empty( $atts ) ? implode( ',', $atts ) : '' ) ) ) ) && $results['results'] ) {
        foreach( items( array( 'show' => ( !empty( $atts ) ? implode( ',', $atts ) : '' ), 'orderby' => 'date desc' ) ) as $item ) {
            echo couponscms_coupon_item( $item );
        }
        echo couponscms_theme_pagination( $results );
    } else {
        echo '<div class="alert">' . sprintf( t( 'theme_no_coupons_store',  '%s has no coupons yet.' ), ts( $store->name ) ) . '</div>';
        foreach( items_custom( array( 'show' => ',active', 'orderby' => 'rand', 'max' => option( 'items_per_page' ) ) ) as $item ) {
            echo couponscms_coupon_item( $item, false, true );
        }
    }

}

echo do_action( 'store_after_items' ); ?>

</div>

<div class="col-md-4">
    <?php echo do_action( 'before_widgets_right' );
    echo show_widgets( 'right' );
    echo do_action( 'after_widgets_right' ); ?>
</div>

</div>

</div>