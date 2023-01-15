<?php

function couponscms_product_item( $item = object, $owner_view = false ) {

    $item->is_owner_view = $owner_view;

    $markup = do_action( 'before_product_outside', $item );

    $markup .= '<div class="list-item product clearfix">';

    $markup .= do_action( 'before_product_inside', $item );

    $markup .= '<div class="list-item-content product-content">';

    $discount = couponscms_discount( $item->old_price, $item->price );

    if( ( !$owner_view && ( $item->cashback != 0 || !empty( $discount ) ) ) ) {
        $markup .= '<div class="extra">';
        $markup .= '<div class="discount" data-tooltip title="' . sprintf( t( 'theme_product_discount_msg', "Purchase %s and get %s OFF" ), $item->title, $discount . '%' ) . '">-' . $discount . '%</div>';
        if( $item->cashback != 0 ) {
            $markup .= '<div class="gift" data-tooltip title="' . sprintf( t( 'theme_cashback_product_msg', "Purchase %s and you'll receive %s points" ), $item->title, $item->cashback ) . '"><i class="fa fa-gift"></i></div>';
        }
        $markup .= '</div>';
    }

    $markup .= '<div class="left">';
    $markup .= '<img src="' . product_avatar( ( !empty( $item->image ) ? $item->image : '' ) ) . '" alt="" />';
    $markup .= '</div>';

    $markup .= '<div class="middle"><h3>';
    if( !( $owner_view || $item->is_expired ) ) {
        $markup .='<a href="#" data-ajax-call="' . ajax_call_url( "save" ) . '" data-data=\'' . json_encode( array( 'item' => $item->ID, 'type' => 'product', 'added_message' => '<i class="fa fa-star"></i>', 'removed_message' => '<i class="fa fa-star-o"></i>' ) ) . '\'>' . ( is_saved( $item->ID, 'product' ) ? '<i class="fa fa-star"></i>' : '<i class="fa fa-star-o"></i>' ) . '</a> ';
    }
    $markup .= '<a href="' . $item->link . '">' . ts( $item->title ) . '</a></h3>';

    $item->description = strip_tags( ts( $item->description ) );
    if( strlen( $item->description ) > 70 ) {
        $item->description = substr( $item->description, 0, 50 ) .
        '<span class="more-link">... <a href="#"><i class="fa fa-caret-down"></i> ' . t( 'theme_more', 'More' ) . '</a></span>' .
        '<span class="hidden-part">' . substr( $item->description, 50 )  . '</span>' .
        '<span class="less-link"><a href="#"><i class="fa fa-caret-up"></i> ' . t( 'theme_less', 'Less' ) . '</a></span>';
    }

    $markup .= '<div class="description">' . ( !empty( $item->description ) ? $item->description : t( 'theme_no_description', 'No description.' ) ) . '</div>';
    if( $item->is_expired ) {
        $markup .= t( 'theme_expired', 'Expired' );
    } else if( !$item->is_started ) {
        $markup .= sprintf( t( 'theme_starts', 'Starts %s' ), couponscms_dateformat( $item->start_date ) );
    } else {
        $markup .= sprintf( t( 'theme_expires', 'Expires %s' ), couponscms_dateformat( $item->expiration_date ) );
    }

    $markup .= '<div class="sold-by">' . t( 'theme_sold_by', 'Sold by' ) . ' <a href="' . $item->store_link . '">' . ts( $item->store_name ) . '</a>';
    if( ( $rating = couponscms_rating( (int) $item->stars, $item->reviews ) ) ) {
        $markup .= '<a href="' . $item->store_reviews_link . '">' . $rating . '</a>';
    }
    $markup .= '</div>';

    $markup .= '</div>';

    $markup .= '<div class="right">';

    if( !empty( $item->price ) ) {
        $markup .= '<div class="price">' . ( !empty( $item->old_price ) ? '<span class="old-price">' . price_format( $item->old_price ) . '</span>' : '' ) . '<span class="current-price">' . price_format( $item->price, $item->currency ) . '</span></div>';
    }

    if( $owner_view ) {
        $markup .= '<a href="' . get_update( array( 'action' => 'edit-product', 'id' => $item->ID ) ) . '" class="icon-button icon-border"><i class="fa fa-pencil"></i><span>' . t( 'theme_edit', 'Edit' ) . '</span></a>';
    } else {
        if( !empty( $item->url ) || !empty( $item->store_url ) )
        $markup .= '<a href="' . get_target_link( 'product', $item->ID ) . '" class="icon-button icon-border" target="_blank"><i class="fa fa-shopping-bag"></i><span>' . t( 'theme_get_it', 'Get it' ) . '</span></a>';
    }

    $markup .= '</div>

    </div>';

    $stats = array();

    $markup .= '<div class="sub-info clearfix">';
    $markup .= '<div class="left">' . ( !empty( $stats ) ? implode( ', ', $stats ) : '' ) . '</div>';

    $markup .= '<div class="right">
    ' . couponscms_share_links( $item->link ) . '
    <a href="#" class="share"><i class="fa fa-caret-left"></i> ' . t( 'theme_share', 'Share' ) . '</a>
    </div>
    </div>';

    $markup .= do_action( 'after_product_inside', $item );

    $markup .= '</div>';

    $markup .= do_action( 'after_product_outside', $item );

    return $markup;

}