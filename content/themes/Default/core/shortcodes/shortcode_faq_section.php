<?php

class couponscms_faq {

    public static function shortcode( $atts, $content ) {
        $atts = extract( array_merge( array(
            'title' => 'Example question'
        ), (array) $atts ) );
        return '<div class="question">
                    <a href="#">' . esc_html( $title ) . '<i class="float-right fa fa-angle-down"></i></a>
                    <div class="answer">' . do_content( $content, false, true, false ) . '</div>
                </div>';
    }

}

/* SHORTCODES */
add( 'shortcodes', 'faq', array( 'couponscms_faq', 'shortcode' ) );