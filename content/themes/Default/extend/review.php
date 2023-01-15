<?php

function couponscms_review_item( $item = object, $owner_view = false ) {

    $item->is_owner_view = $owner_view;

    $markup = do_action( 'before_review_outside', $item );

    $markup .= '<div class="list-item review clearfix">';

    $markup .= do_action( 'before_review_inside', $item );

    $markup .= '<div class="list-item-content review-content">';

    $markup .= '<div class="left">';
    $markup .= '<img src="' . user_avatar( $item->user_avatar ) . '" alt="" />';
    $markup .= '</div>';

    $markup .= '<div class="middle">';

    $markup .= '<h3>' .$item->user_name . '</h3>';

    if( ( $rating = couponscms_rating( (int) $item->stars ) ) ) {
        $markup .= $rating;
    }

    $markup .= '<div class="description">' . $item->text . '</div>';

    $markup .= '<i>' . couponscms_dateformat( $item->date ) . '</i>';

    $markup .= '</div>

    </div>';

    $markup .= do_action( 'after_review_inside', $item );

    $markup .= '</div>';

    $markup .= do_action( 'after_review_outside', $item );

    return $markup;

}