<?php $item = the_item(); ?>

<div class="container pt50 pb50">

<?php echo do_action( 'coupon_before_info' ); ?>

<div class="row">
    <div class="col-md-12">
        <div class="widget item-text" id="coupon-<?php echo $item->ID; ?>">
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

                <?php $stats = array();
                if( $item->is_verified ) {
                    $stats[] = '<li><i class="fa fa-check"></i> ' . sprintf( t( 'theme_verified_msg', 'Verified manually, last time on %s' ), couponscms_dateformat( $item->last_check ) ) . '</li>';
                }
                if( $item->clicks > 0 ) {
                    $stats[] = '<li><i class="fa fa-bookmark"></i> <span>' . sprintf( t( 'theme_stats_used', '%s used' ), $item->clicks )  . '</span></li>';
                }
                if( $item->votes > 0 ) {
                    $stats[] = '<li><i class="fa fa-thumbs-up"></i> <span>' . sprintf( t( 'theme_stats_percent_rate', '%s success rate' ), (int) $item->votes_percent . '%' )  . '</span></li>';
                } ?>
                <ul class="description-links">
                <?php echo implode( "\n", $stats ); ?>
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

                <?php if( !empty( $item->code ) ) { ?>
                <div class="code">
                <div class="code-inner">
                <?php if( couponscms_view_store_coupons( $item->storeID ) ) { ?>
                    <i class="fa fa-scissors"></i>
                    <div class="code-text">
                        <h3><?php echo $item->code; ?></h3>
                        <a href="#" class="butt" data-copy-this><?php te( 'theme_copy', 'Copy' ); ?></a>
                        <input type="text" name="copy" value="<?php echo $item->code; ?>" />
                    </div>
                <?php } else { ?>
                    <div class="code-text">
                        <a href="<?php echo get_target_link( 'coupon', $item->ID, array( 'reveal_code' => true, 'backTo' => base64_encode( $item->link ) ) ); ?>" target="_blank" class="butt" data-target-on-click="<?php echo get_target_link( 'coupon', $item->ID ); ?>"><?php te( 'theme_show_code', 'Show coupon code' ); ?></a>
                    </div>
                <?php } ?>
                </div>
                </div>
                <?php } ?>

                <div class="code">
                <?php if( $item->is_show_in_store ) {
                    if( ( $claimed = is_coupon_claimed( $item->ID ) ) ) {
                        echo '<div class="qr-code">
                        <img src="https://chart.googleapis.com/chart?cht=qr&chl=' . urlencode( tlink( 'user/account', 'action=check&code=' . $claimed->code ) ) . '&chs=180x180&choe=UTF-8&chld=L|2" alt="qr code" />';
                        echo '<a href="#" data-code="' . $claimed->code . '">' . t( 'theme_claimed_show_code', 'Show Code' ) . '</a>';
                        echo '</div>';
                    } else if( $item->claim_limit == 0 || $item->claim_limit > $item->claims ) {
                        echo '<a href="#" data-ajax-call="' . ajax_call_url( "claim" ) . '" data-data=\'' . json_encode( array( 'item' => $item->ID, 'claimed_message' => '<i class="fa fa-check"></i><span> ' . t( 'theme_claimed', 'Claimed !' ) ) ) . '\' data-after-ajax="coupon_claimed" data-confirmation="' . t( 'theme_claim_ask', 'Do you want to claim and use this coupon in store?' ) . '" class="icon-button icon-border"><i class="fa fa-plus"></i><span>' . t( 'theme_claim', 'Claim' ) . '</span></a>';
                    }
                } else if( $item->is_printable ) { ?>
                    <div class="visit-link-msg"><?php echo sprintf( t( 'theme_msg_print_coupon', '<a href="%s" target="_blank"><i class="fa fa-print"></i> Click here</a> to print the coupon.' ), get_target_link( 'coupon', $item->ID ) ); ?></div>
                <?php } else if( !empty( $item->code ) ) { ?>
                    <div class="visit-link-msg"><?php echo sprintf( t( 'theme_msg_coupon', 'Copy the coupon code. <a href="%s" target="_blank"><i class="fa fa-external-link"></i> Click here</a> and apply it to cart.' ), get_target_link( 'coupon', $item->ID ) ); ?></div>
                <?php } else { ?>
                    <div class="visit-link-msg"><?php echo sprintf( t( 'theme_msg_visit_deal', 'No code needed. <a href="%s" target="_blank"><i class="fa fa-external-link"></i> Click here</a> and get the deal.' ), get_target_link( 'coupon', $item->ID ) ); ?></div>
                <?php } ?>
                </div>

                <?php if( ( $can_rate = option( 'allow_votes' ) && !( $item->is_expired || !$item->is_started ) ) || $item->votes > 0 ) { ?>
                <div class="single-votes">
                    <?php if( $item->votes > 0 ) { ?>
                    <div class="single-votes-stats">
                        <?php echo sprintf( t( 'theme_stats_percent_rate', '%s success rate' ), (int) $item->votes_percent . '%' ); ?>
                        <div class="vote-line">
                            <div style="width: <?php echo (int) $item->votes_percent; ?>%!important;"></div>
                            <div></div>
                        </div>
                    </div>
                    <?php }
                    if( $can_rate ) { ?>
                    <div class="single-vote">
                        <span class="rate-msg"><?php te( 'theme_rate', 'Rate:' ); ?></span>
                        <a href="#" class="success" data-tooltip title="<?php te( 'theme_works_msg', "It works !" ); ?>" data-ajax-call="<?php echo ajax_call_url( "vote" ); ?>" data-after-ajax="ajax_voted" data-data='<?php echo json_encode( array( 'item' => $item->ID, 'vote' => 1, 'voted_message' => '<i class="fa fa-check-circle"></i> ' . t( 'theme_voted_msg', 'Voted' ), 'already_voted_message' => '<i class="fa fa-check-circle"></i> ' . t( 'theme_voted_msg', 'Voted' ) ) ); ?>'><i class="fa fa-smile-o"></i></a>
                        <a href="#" class="failed" data-tooltip title="<?php te( 'theme_doesnt_work_msg', "It doesn't work !" ); ?>" data-ajax-call="<?php echo ajax_call_url( "vote" ); ?>" data-after-ajax="ajax_voted" data-data='<?php echo json_encode( array( 'item' => $item->ID, 'vote' => 0, 'voted_message' => '<i class="fa fa-check-circle"></i> ' . t( 'theme_voted_msg', 'Voted' ), 'already_voted_message' => '<i class="fa fa-check-circle"></i> ' . t( 'theme_voted_msg', 'Voted' ) ) ); ?>'><i class="fa fa-frown-o"></i></a>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>

                <ul class="links-list">
                    <li class="line-after"><a href="#" data-ajax-call="<?php echo ajax_call_url( "save" ); ?>" data-data='<?php echo json_encode( array( 'item' => $item->ID, 'type' => 'coupon', 'added_message' => '<i class="fa fa-star"></i> ' . t( 'theme_unsave_coupon', 'Unsave this coupon' ), 'removed_message' => '<i class="fa fa-star-o"></i> ' . t( 'theme_save_coupon', 'Save this coupon' ) ) ); ?>'><?php echo ( is_saved( $item->ID, 'coupon' ) ? '<i class="fa fa-star"></i> ' . t( 'theme_unsave_coupon', 'Unsave this coupon' ) : '<i class="fa fa-star-o"></i> ' . t( 'theme_save_coupon', 'Save this coupon' ) ); ?></a></li>
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

<?php echo do_action( 'coupon_after_info' );

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

    <?php echo do_action( 'coupon_before_items' );

    if( ( $results = have_items_custom( array( 'limit' => option( 'items_per_page' ) ) ) ) && $results['results'] ) {

        foreach( items_custom( array( 'orderby' => 'rand' ) ) as $item ) {
            echo couponscms_coupon_item( $item, false, true );
        }

    }

    echo do_action( 'coupon_after_items' ); ?>

</div>

<div class="col-md-4">
    <?php echo do_action( 'before_widgets_right' );
    echo show_widgets( 'right' );
    echo do_action( 'after_widgets_right' ); ?>
</div>

</div>

</div>