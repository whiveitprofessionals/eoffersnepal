<?php

function couponscms_menu_subnav_item( $subnav = array() ) {
    if( isset( $subnav['subnav'] ) ) {
        echo '<ul class="sub-nav">';
        foreach( $subnav['subnav'] as $link ) {
            echo '<li' . ( !empty( $link['classes'] ) ? ' class="' . implode( ' ', $link['classes'] ) . '"' : '' ) . '><a href="' . esc_html( $link['url'] ) . '"' . ( isset( $link['open_type'] ) && in_array( $link['open_type'], array( '_self', '_blank' ) ) ? ' target="' . $link['open_type'] . '"' : '' ) . '>' . esc_html( ts( $link['name'] ) ) . ( $link['dropdown'] ? ' <i class="fa fa-angle-right"></i>' : '' ) . '</a>';
            echo couponscms_menu_subnav_item( $link );
            echo '</li>';
        }
        echo '</ul>';
    }
}

function couponscms_menu( $menu_id = '' ) {
    foreach( site_menu( $menu_id ) as $link ) {
        echo '<li' . ( !empty( $link['classes'] ) ? ' class="' . implode( ' ', $link['classes'] ) . '"' : '' ) . '><a href="' . $link['url'] . '"' . ( isset( $link['open_type'] ) && in_array( $link['open_type'], array( '_self', '_blank' ) ) ? ' target="' . $link['open_type'] . '"' : '' ) . '>' . esc_html( ts( $link['name'] ) )  . ( $link['dropdown'] ? ' <i class="fa fa-angle-down"></i>' : '' ) . '</a>';
        couponscms_menu_subnav_item( $link );
        echo '</li>';
    }
}

?>