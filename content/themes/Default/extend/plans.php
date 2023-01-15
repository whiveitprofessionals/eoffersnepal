<?php

function couponscms_plans_item( $item = object ) {

    $markup = do_action( 'before_plans_outside', $item );

    $markup .= '<div class="list-item payment-plans clearfix">';

    $markup .= do_action( 'before_plans_inside', $item );

    $markup .= '<div class="list-item-content plans-content">';

    $markup .= '<div class="left">';
    $markup .= '<img src="' . payment_plan_avatar( $item->image ) . '" alt="" />';
    $markup .= '</div>';

    $markup .= '<div class="middle">';
    $markup .= '<h3>' . ts( $item->name ) . '</h3>';
    $markup .= '<div class="description">' . ts( $item->description ) . '</div>';
    $markup .= '<div class="points">' . sprintf( t( 'theme_payment_plan_price', 'Price: <b>%s</b>' ), $item->price_format ) . '</div>';
    $markup .= '</div>';

    $markup .= '<div class="right">';
    $markup .= '<a href="' . tlink( 'pay', 'plan=' . $item->ID ) . '" class="icon-button icon-border"><i class="fa fa-plus"></i><span>' . sprintf( t( 'theme_payment_plan_button', '%s credits' ), $item->credits ) . '</span></a>';
    $markup .= '</div>

    </div>';

    $markup .= do_action( 'after_plans_inside', $item );

    $markup .= '</div>';

    $markup .= do_action( 'after_plans_outside', $item );

    return $markup;

}