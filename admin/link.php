<?php

if( isset( $_GET['main'] ) ) {

    $item = $GLOBALS['admin_main_class']->get_nav_item( $_GET['main'], ( isset( $_GET['action'] ) ? $_GET['action'] : '' ) );

    if( $item && ( !isset( $item['perm'] ) || $GLOBALS['me']->is_admin || (boolean) $item['perm'] ) ) {

        if( !empty( $item['page_title'] ) ) {

            echo '<div class="title">
            <h2>' . esc_html( $item['page_title'] ) . '</h2>';
            if( !empty( $item['page_subtitle'] ) ) {
              echo '<span>' . esc_html( $item['page_subtitle'] ) . '</span>';
            }
            echo '</div>';

        }

        if( isset( $item['callback'] ) && \site\utils::check_callback( $item['callback'] ) ) {
            echo $item['callback']();
        } else echo '<div class="a-error">' . t( 'invalid_callback', "Invalid callback !" ) . '</div>';

    }

}