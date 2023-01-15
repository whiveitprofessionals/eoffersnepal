<?php

function couponscms_coupon_item( $item = object, $owner_view = false, $single_redirect = false ) {

    $item->is_owner_view = $owner_view;

    $markup = do_action( 'before_coupon_outside', $item );

    $markup .= '<div class="list-item coupon clearfix" id="coupon-' . $item->ID . '">';

    $markup .= do_action( 'before_coupon_inside', $item );

    $markup .= '<div class="list-item-content coupon-content">';

    if( !$owner_view && ( $item->cashback != 0 || $item->is_verified ) ) {
        $markup .= '<div class="extra">';
        if( $item->cashback != 0 ) {
            $markup .= '<div class="gift" data-tooltip title="' . sprintf( t( 'theme_cashback_msg', "Use it and you'll receive %s points" ), $item->cashback ) . '"><i class="fa fa-gift"></i></div>';
        }
        if( $item->is_verified ) {
            $markup .= '<div class="check" data-tooltip title="' . sprintf( t( 'theme_verified_msg', 'Verified manually, last time on %s' ), couponscms_dateformat( $item->last_check ) ) . '"><i class="fa fa-check"></i></div>';
        }
        $markup .= '</div>';
    }

    $markup .= '<div class="left">';
    $markup .= '<img src="' . store_avatar( ( !empty( $item->image ) ? $item->image : $item->store_img ) ) . '" alt="" />';
    if( ( $rating = couponscms_rating( (int) $item->stars, $item->reviews ) ) ) {
        $markup .= '<a href="' . $item->store_reviews_link . '">' . $rating . '</a>';
    }
    $markup .= '<a href="' . $item->store_link . '">' . ts( $item->store_name ) . '</a>';
    $markup .= '</div>';

    $markup .= '<div class="middle"><h3>';
    if( !( $owner_view || $item->is_expired ) ) {
        $markup .='<a href="#" data-ajax-call="' . ajax_call_url( "save" ) . '" data-data=\'' . json_encode( array( 'item' => $item->ID, 'type' => 'coupon', 'added_message' => '<i class="fa fa-star"></i>', 'removed_message' => '<i class="fa fa-star-o"></i>' ) ) . '\'>' . ( is_saved( $item->ID, 'coupon' ) ? '<i class="fa fa-star"></i>' : '<i class="fa fa-star-o"></i>' ) . '</a> ';
    }
    $markup .= '<a href="' . $item->link . '">' . ts( $item->title ) . '</a></h3>';

    $item->description = strip_tags( ts( $item->description ) );
    if( strlen( $item->description ) > 70 ) {
        $item->description = substr( $item->description, 0, 50 ) .
        '<span class="more-link">... <a href="#"><i class="fa fa-caret-down"></i> ' . t( 'theme_more', 'More' ) . '</a></span>' .
        '<span class="hidden-part">' . substr( $item->description, 50 )  . '</span>' .
        '<span class="less-link"><a href="#"><i class="fa fa-caret-up"></i> ' . t( 'theme_less', 'Less' ) . '</a></span>';
    }

    $markup .= '<div class="description">' . ( !empty( $item->description ) ? ts( $item->description ) : t( 'theme_no_description', 'No description.' ) ) . '</div>';
    if( $item->is_expired ) {
        $markup .= t( 'theme_expired', 'Expired' );
    } else if( !$item->is_started ) {
        $markup .= sprintf( t( 'theme_starts', 'Starts %s' ), couponscms_dateformat( $item->start_date ) );
    } else {
        $markup .= sprintf( t( 'theme_expires', 'Expires %s' ), couponscms_dateformat( $item->expiration_date ) );
    }

    $markup .= '</div>';

    $markup .= '<div class="right">';

    if( $owner_view ) {
        $markup .= '<a href="' . get_update( array( 'action' => 'edit-coupon', 'id' => $item->ID ) ) . '" class="icon-button icon-border"><i class="fa fa-pencil"></i><span>' . t( 'theme_edit', 'Edit' ) . '</span></a>';
        if( $item->is_show_in_store ) {
            $markup .= '<a href="' . get_update( array( 'action' => 'coupon-claims', 'id' => $item->ID ) ) . '" class="claims">' . sprintf( t( 'theme_claims', 'Claims (%s)' ), $item->claims ) . '</a>';
        }
    } else {

    if( $item->is_printable ) {
        $markup .= '<a href="' . get_target_link( 'coupon', $item->ID ) . '" class="icon-button icon-border"><i class="fa fa-print"></i><span>' . t( 'theme_print', 'Print It' ) . '</span></a>';
    } else if( $item->is_show_in_store ) {
        if( ( $claimed = is_coupon_claimed( $item->ID ) ) ) {
            $markup .= '<div class="qr-code">
            <img src="https://chart.googleapis.com/chart?cht=qr&chl=' . urlencode( tlink( 'user/account', 'action=check&code=' . $claimed->code ) ) . '&chs=180x180&choe=UTF-8&chld=L|2" alt="qr code" />';
            $markup .= '<a href="#" data-code="' . $claimed->code . '">' . t( 'theme_claimed_show_code', 'Show Code' ) . '</a>';
            $markup .= '</div>';
        } else if( $item->claim_limit == 0 || $item->claim_limit > $item->claims ) {
            $markup .= '<a href="#" data-ajax-call="' . ajax_call_url( "claim" ) . '" data-data=\'' . json_encode( array( 'item' => $item->ID, 'claimed_message' => '<i class="fa fa-check"></i><span> ' . t( 'theme_claimed', 'Claimed !' ) ) ) . '\' data-after-ajax="coupon_claimed" data-confirmation="' . t( 'theme_claim_ask', 'Do you want to claim and use this coupon in store?' ) . '" class="icon-button icon-border"><i class="fa fa-plus"></i><span>' . t( 'theme_claim', 'Claim' ) . '</span></a>';
        }
    } else if( $item->is_coupon ) {
        if( couponscms_view_store_coupons( $item->storeID ) ) {
            $markup .= '<div class="code-revealed"><i class="fa fa-scissors"></i> ' . $item->code . '</div>
            <div class="copy-code"><a href="#" data-copy-this><i class="fa fa-copy"></i> ' . t( 'theme_copy', 'Copy' ) . '</a>
            <input type="text" name="copy" value="' . $item->code . '" /></div>';
        } else {
            $code_preview = strlen( $item->code ) > 2 ? '<i>' . substr( $item->code, 0, 2 ) . '...</i>' : '<i>...</i>';
            $markup .= '<a href="' . get_target_link( 'coupon', $item->ID, array( 'reveal_code' => true, 'backTo' => base64_encode( ( $single_redirect ? $item->link : get_update() ) ) ) ) . '" target="_blank" class="icon-button icon-border" data-target-on-click="' . get_target_link( 'coupon', $item->ID ) . '">' . $code_preview . '<span>' . t( 'theme_claimed_show_code', 'View Code' ) . '</span></a>';
        }
    } else {
        $markup .= '<a href="' . get_target_link( 'coupon', $item->ID ) . '" target="_blank" class="icon-button icon-border"><i class="fa fa-shopping-bag"></i><span>' . t( 'theme_get_deal', 'Get Deal' ) . '</span></a>';
    }

    }

    $markup .= '</div>

    </div>';

    $stats = array();
    if( $item->clicks > 0 ) {
        $stats[] = sprintf( t( 'theme_stats_used', '%s used' ), $item->clicks );
    }
    if( $item->votes > 0 ) {
        $stats[] = sprintf( t( 'theme_stats_percent_rate', '%s success rate' ), (int) $item->votes_percent . '%' );
    }

    $markup .= '<div class="sub-info clearfix">';
    $markup .= '<div class="left">' . ( !empty( $stats ) ? implode( ', ', $stats ) : '' ) . '</div>';

    $markup .= '<div class="middle">';                                                                 
    if( option( 'allow_votes' ) && !( $owner_view || $item->is_expired || !$item->is_started ) ) {
        $markup .= '<div class="vote-buttons">
                        <span class="rate-msg">' . t( 'theme_rate', 'Rate:' ) . '</span>
                        <a href="#" class="success" data-tooltip title="' . t( 'theme_works_msg', "It works !" ) . '" data-ajax-call="' . ajax_call_url( "vote" ) . '" data-after-ajax="ajax_voted" data-data=\'' . json_encode( array( 'item' => $item->ID, 'vote' => 1, 'voted_message' => '<i class="fa fa-check-circle"></i> ' . t( 'theme_voted_msg', 'Voted' ), 'already_voted_message' => '<i class="fa fa-check-circle"></i> ' . t( 'theme_voted_msg', 'Voted' ) ) ) . '\'><i class="fa fa-smile-o"></i></a>
                        <a href="#" class="failed" data-tooltip title="' . t( 'theme_doesnt_work_msg', "It doesn't work !" ) . '" data-ajax-call="' . ajax_call_url( "vote" ) . '" data-after-ajax="ajax_voted" data-data=\'' . json_encode( array( 'item' => $item->ID, 'vote' => 0, 'voted_message' => '<i class="fa fa-check-circle"></i> ' . t( 'theme_voted_msg', 'Voted' ), 'already_voted_message' => '<i class="fa fa-check-circle"></i> ' . t( 'theme_voted_msg', 'Voted' ) ) ) . '\'><i class="fa fa-frown-o"></i></a>
                    </div>';
    }
    $markup .= '</div>';

    $markup .= '<div class="right">
    ' . couponscms_share_links( $item->link ) . '
    <a href="#" class="share"><i class="fa fa-caret-left"></i> ' . t( 'theme_share', 'Share' ) . '</a>
    </div>
    </div>';

    $markup .= do_action( 'after_coupon_inside', $item );

    $markup .= '</div>';

    $markup .= do_action( 'after_coupon_outside', $item );

    return $markup;

}

function couponscms_claims_item( $item = object ) {

    $markup = do_action( 'before_claims_item_outside', $item );

    $markup .= '<div class="list-item claims_item clearfix">';

    $markup .= do_action( 'before_claims_item_inside', $item );

    $markup .= '<div class="list-item-content claims-item-content">';
    $markup .= '<div class="middle">';
    $markup .= '<h3>' . ( $item->is_used ? $item->code : '***' . substr( $item->code, -3 ) ) . '</h3>';
    $markup .= '<div class="list-info">' . sprintf( t( 'theme_claims_used_state', 'Used: %s' ), ( $item->is_used ? t( 'yes', 'Yes' ) : t( 'no', 'No' ) ) ) . '</div>';
    if( $item->is_used ) {
        $markup .= '<div class="list-info">' . sprintf( t( 'theme_claims_used_date', 'Used Date: %s' ), $item->used_date ) . '</div>';
    }
    $markup .= '<div class="list-info">' . sprintf( t( 'theme_claims_claimed_date', 'Claimed Date: %s' ), $item->date ) . '</div>';
    $markup .= '</div>

    </div>';

    $markup .= do_action( 'after_reward_reqest_inside', $item );

    $markup .= '</div>';

    $markup .= do_action( 'after_reward_reqest_outside', $item );

    return $markup;

}