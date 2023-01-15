<?php

function couponscms_store_item( $item = object, $owner_view = false ) {

    $item->is_owner_view = $owner_view;

    $markup = do_action( 'before_store_outside', $item );

    $markup .= '<div class="list-item store clearfix">';

    $markup .= do_action( 'before_store_inside', $item );

    $markup .= '<div class="list-item-content store-content">';

    $markup .= '<div class="left">';
    $markup .= '<img src="' . store_avatar( $item->image ) . '" alt="" />';
    if( ( $rating = couponscms_rating( (int) $item->stars, $item->reviews ) ) ) {
        $markup .= '<a href="' . $item->reviews_link . '">' . $rating . '</a>';
    }
    $markup .= '</div>';

    $markup .= '<div class="middle"><h3>';
    if( !$owner_view ) {
        $markup .='<a href="#" data-ajax-call="' . ajax_call_url( "save" ) . '" data-data=\'' . json_encode( array( 'item' => $item->ID, 'type' => 'store', 'added_message' => '<i class="fa fa-star"></i>', 'removed_message' => '<i class="fa fa-star-o"></i>' ) ) . '\'>' . ( is_saved( $item->ID, 'store' ) ? '<i class="fa fa-star"></i>' : '<i class="fa fa-star-o"></i>' ) . '</a> ';
    }
    $markup .= '<a href="' . $item->link . '">' . ts( $item->name ) . '</a></h3>';

    $item->description = strip_tags( ts( $item->description ) );
    if( strlen( $item->description ) > 180 ) {
        $item->description = substr( $item->description, 0, 160 ) .
        '<span class="more-link">... <a href="#"><i class="fa fa-caret-down"></i> ' . t( 'theme_more', 'More' ) . '</a></span>' .
        '<span class="hidden-part">' . substr( $item->description, 160 )  . '</span>' .
        '<span class="less-link"><a href="#"><i class="fa fa-caret-up"></i> ' . t( 'theme_less', 'Less' ) . '</a></span>';
    }

    $markup .= '<div class="description">' . ( !empty( $item->description ) ? $item->description : t( 'theme_no_description', 'No description.' ) ) . '</div>';

    $markup .='<a href="#" data-ajax-call="' . ajax_call_url( "favorite" ) . '" data-data=\'' . json_encode( array( 'store' => $item->ID, 'added_message' => '<i class="fa fa-heart"></i> ' . t( 'theme_remove_favorite', 'Remove favorite' ), 'removed_message' => '<i class="fa fa-heart-o"></i> ' . t( 'theme_add_favorite', 'Add favorite' ) ) ) . '\'>' . ( is_favorite( $item->ID ) ? '<i class="fa fa-heart"></i> ' . t( 'theme_remove_favorite', 'Remove favorite' ) : '<i class="fa fa-heart-o"></i> ' . t( 'theme_add_favorite', 'Add favorite' ) ) . '</a>';

    $markup .= '</div>';

    if( $owner_view ) {
        $markup .= '<div class="right">';
        $markup .= '<a href="' . get_update( array( 'action' => 'edit-store', 'id' => $item->ID ) ) . '" class="icon-button icon-border"><i class="fa fa-pencil"></i><span>' . t( 'theme_edit', 'Edit' ) . '</span></a>';
        $markup .= '</div>';
    }


    $markup .= '</div>';

    $stats = array();
    if( !empty( $item->coupons ) ) {
        $stats[] = sprintf( t( 'theme_store_coupons', '%s coupons' ), $item->coupons );
    }
    if( !empty( $item->products ) ) {
        $stats[] = sprintf( t( 'theme_store_products', '%s products' ), $item->products );
    }

    $markup .= '<div class="sub-info clearfix">';
    $markup .= '<div class="left">' . ( !empty( $stats ) ? implode( ', ', $stats ) : '' ) . '</div>';

    $markup .= '<div class="right">
    ' . couponscms_share_links( $item->link ) . '
    <a href="#" class="share"><i class="fa fa-caret-left"></i> ' . t( 'theme_share', 'Share' ) . '</a>
    </div>
    </div>';

    $markup .= do_action( 'after_store_inside', $item );

    $markup .= '</div>';

    $markup .= do_action( 'after_store_outside', $item );

    return $markup;

}