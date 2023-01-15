<?php $item = the_item(); ?>

<div class="container pt50 pb50">

<?php echo do_action( 'product_before_info' ); ?>

<div class="row">
    <div class="col-md-12">
        <div class="widget item-text">
            <div class="avatar">
                <img src="<?php echo store_avatar( ( !empty( $item->image ) ? $item->image : $item->store_img ) ); ?>" alt="" />
            </div>
            <div class="info">
                <h2 class="clearfix"><?php tse( $item->title ); ?>
                    <?php if( !empty( $item->reviews ) ) { ?>
                        <div class="rating"><a href="<?php echo $item->store_reviews_link; ?>"><?php echo couponscms_rating( (int) $item->stars, $item->reviews ); ?></a></div>
                    <?php } ?>
                </h2>
                <?php echo ( !empty( $item->description ) ? '<span>' . ts( $item->description ) . '</span>' : t( 'theme_no_description', 'No description.' ) ); ?>
                <ul class="description-links">
                    <?php if( !empty( $item->price ) ) {
                    echo '<li class="text-center">';
                    echo '<div class="price">' . ( !empty( $item->old_price ) ? '<span class="old-price">' . price_format( $item->old_price ) . '</span>' : '' ) . '<span class="current-price">' . price_format( $item->price, $item->currency ) . '</span></div>';
                    } ?>
                    <li><i class="fa fa-hourglass-half"></i>
                    <?php if( $item->is_expired ) {
                    echo '<span class="expired exp-date">' . t( 'theme_expired', 'Expired' ) . '</span>';
                    } else if( !$item->is_started ) {
                        echo '<span class="starts exp-date">' . sprintf( t( 'theme_starts', 'Starts <strong>%s</strong>' ), couponscms_dateformat( $item->start_date ) ) . '</span>';
                    } else {
                        echo '<span class="expires exp-date">' . sprintf( t( 'theme_expires', 'Expires <strong>%s</strong>' ), couponscms_dateformat( $item->expiration_date ) ) . '</span>';
                    } ?>
                    </li>
                </ul>
                <?php if( !empty( $item->url ) || !empty( $item->store_url ) ) { ?>
                <div class="code">
                    <div class="visit-link-msg"><?php echo sprintf( t( 'theme_msg_product', '<a href="%s" target="_blank"><i class="fa fa-external-link"></i> Click here</a> and get the deal.' ), get_target_link( 'product', $item->ID ) ); ?></div>
                </div>
                <?php } ?>

                <ul class="links-list">
                    <li class="line-after"><a href="#" data-ajax-call="<?php echo ajax_call_url( "save" ); ?>" data-data='<?php echo json_encode( array( 'item' => $item->ID, 'type' => 'product', 'added_message' => '<i class="fa fa-star"></i> ' . t( 'theme_unsave_product', 'Unsave this product' ), 'removed_message' => '<i class="fa fa-star-o"></i> ' . t( 'theme_save_product', 'Save this product' ) ) ); ?>'><?php echo ( is_saved( $item->ID, 'product' ) ? '<i class="fa fa-star"></i> ' . t( 'theme_unsave_product', 'Unsave this product' ) : '<i class="fa fa-star-o"></i> ' . t( 'theme_save_product', 'Save this product' ) ); ?></a></li>
                    <?php if( !empty( $item->store_name ) ) { ?>
                    <li><a href="<?php echo $item->store_link; ?>"><i class="fa fa-university"></i> <?php echo sprintf( t( 'theme_store_s_profile', "%s's Profile" ), ts( $item->store_name ) ); ?></a></li>
                    <?php if( !empty( $item->store_url ) ) { ?>
                    <li><a href="<?php echo get_target_link( 'store', $item->storeID ); ?>"><i class="fa fa-external-link"></i> <?php echo sprintf( t( 'theme_store_s_website', "%s's Website" ), ts( $item->store_name ) ); ?></a></li>
                    <?php }
                    } ?>
                    <?php if( $item->store_is_physical ) {
                        $locations = store_locations( $item->storeID );
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
                </ul>
                <div class="item-share">
                    <?php echo sprintf( t( 'theme_store_share_links', 'Share: %s' ), couponscms_share_links( $item->link ) ); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo do_action( 'product_after_info' );

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

<div class="row">

<div class="col-md-8">

    <?php echo do_action( 'product_before_items' );

    if( ( $results = have_products_custom( array( 'limit' => option( 'items_per_page' ) ) ) ) && $results['results'] ) {

        foreach( products_custom( array( 'orderby' => 'rand' ) ) as $item ) {
            echo couponscms_product_item( $item );
        }

    }

    echo do_action( 'product_after_items' ); ?>

</div>

<div class="col-md-4">
    <?php echo do_action( 'before_widgets_right' );
    echo show_widgets( 'right' );
    echo do_action( 'after_widgets_right' ); ?>
</div>

</div>

</div>