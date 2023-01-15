<?php

function couponscms_theme_pagination( $pagination ) {

    $markup = '';

    $override = do_action( 'override_navigation', $pagination );

    if( !$override ) {

    if( isset( $pagination['prev_page'] ) || isset( $pagination['next_page'] ) ) {

        $markup .= '<div class="pages-nav">';
        $markup .= ( isset( $pagination['prev_page'] ) ? '<span><a href="' . $pagination['prev_page'] . '"><i class="fa fa-arrow-left"></i> ' . t( 'theme_previous', 'Previous' ) . '</a></span>' : '<span style="opacity: 0.2;"><i class="fa fa-arrow-left"></i> ' . t( 'theme_previous', 'Previous' ) . '</span>' );
        $markup .= ( isset( $pagination['next_page'] ) ? '<span><a href="' . $pagination['next_page'] . '">' . t( 'theme_next', 'Next' ) . ' <i class="fa fa-arrow-right"></i></a></span>' : '<span style="opacity: 0.2;">' . t( 'theme_next', 'Next' ) . ' <i class="fa fa-arrow-right"></i></span>' );
        $markup .= '<div class="num">' . $pagination['page'] . ' of ' . $pagination['pages'] . '</div>';
        $markup .= '</div>';

    }

    } else $markup .= $override;

    return $markup;

}