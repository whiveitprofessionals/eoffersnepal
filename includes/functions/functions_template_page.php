<?php

global $GET, $add_theme_page;

if( isset( $GET['id'] ) && ( ( $file_exists = file_exists( theme_location( true ) . '/' . $GET['id'] . '.php' ) ) || ( is_array( $add_theme_page ) && in_array( strtolower( \site\utils::file_path( $GET['id'] ) ), array_keys( $add_theme_page ) ) ) ) ) {

    /* IS CURRENT PAGE */

    function this_is_template_page( $identifier = 0 ) {

        if( !empty( $identifier ) ) {

            global $GET;

            if( isset( $GET['id'] ) && strcasecmp( $GET['id'], $identifier ) == 0 ) {
                return true;
            }
            return false;

        }

        return true;

    }

    /* ACTION BEFORE DISPLAYING THE CONTENT */

    do_action( 'before_template_page_page', $GET['id'] );

} else {

    /* THIS IS 404 PAGE */

    function this_is_404_page() {

        return true;

    }

}