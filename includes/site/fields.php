<?php

namespace site;

/** */

class fields {

public static function build_extra( $fields = array(), $value = array(), $post_name = 'extra', $item = false ) {

    if( empty( $fields ) ) {
        return '';
    }

    $markup = '';

    foreach( $fields as $id => $opt ) {

        switch( $opt['type'] ) {

            case 'text':
                $markup .= '<div class="form_field' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"><label for="' . $post_name . '[' . $id . ']">' . esc_html( $opt['title'] ) . ':</label><div><input name="' . $post_name . '[' . $id . ']" id="' . $post_name . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? esc_html( $value[$id] ) : ( isset( $opt['default'] ) ? esc_html( $opt['default'] ) : '' ) ) . '"' . ( isset( $opt['placeholder'] ) ? ' placeholder="' . esc_html( $opt['placeholder'] ) . '"' : '' ) . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="text" autocomplete="off"></div></div>';
            break;

            case 'select':
                $markup .= '<div class="form_field' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"><label for="' . $post_name . '[' . $id . ']">' . esc_html( $opt['title'] ) . ':</label><div><select name="' . $post_name . '[' . $id . ']" id="' . $post_name . '[' . $id . ']"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . '>';
                    foreach( $opt['options'] as $k => $v ) {
                        $markup .= '<option value="' . esc_html( $k ) . '"' . ( ( isset( $value[$id] ) && $k == $value[$id] ) || ( !isset( $value[$id] ) && isset( $opt['default'] ) && $k == $opt['default'] ) ? ' selected' : '' ) . '>' . esc_html( $v ) . '</option>';
                    }
                $markup .= '</select>
                </div></div>';
            break;

            case 'textarea':
                $markup .= '<div class="form_field' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"><label for="' . $post_name . '[' . $id . ']">' . esc_html( $opt['title'] ) . ':</label><div><textarea name="' . $post_name . '[' . $id . ']" id="' . $post_name . '[' . $id . ']"' . ( isset( $opt['placeholder'] ) ? ' placeholder="' . esc_html( $opt['placeholder'] ) . '"' : '' ) . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . '>' . ( isset( $value[$id] ) ? esc_html( $value[$id] ) : ( isset( $opt['default'] ) ? esc_html( $opt['default'] ) : '' ) ) . '</textarea></div></div>';
            break;

            case 'checkbox':
                $markup .= '<div class="form_field' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"><label>' . esc_html( $opt['title'] ) . ':</label><div><input name="' . $post_name . '[' . $id . ']" id="' . $post_name . '[' . $id . ']"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="checkbox"' . ( !empty( $value[$id] ) ? ' checked' : '' ) . '> <label for="' . $post_name . '[' . $id . ']"><span></span> ' . ( !empty( $opt['label'] ) ? esc_html( $opt['label'] ) : '' ) . '</label></div></div>';
            break;

            case 'hidden':
                $markup .= '<input name="' . $post_name . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? esc_html( $value[$id] ) : ( isset( $opt['default'] ) ? esc_html( $opt['default'] ) : '' ) ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="hidden">';
            break;

            case 'number':
                $markup .= '<div class="form_field' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"><label for="' . $post_name . '[' . $id . ']">' . esc_html( $opt['title'] ) . ':</label><div><input name="' . $post_name . '[' . $id . ']" id="' . $post_name . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? (int) $value[$id] : ( isset( $opt['default'] ) ? (int) $opt['default'] : '' ) ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="number"' . ( isset( $opt['min'] ) ? ' min="' . (int) $opt['min'] . '"' : '' ) . ( isset( $opt['max'] ) ? ' max="' . (int) $opt['max'] . '"' : '' ) . ' autocomplete="off" /></div></div>';
            break;

            case 'price':
                $markup .= '<div class="form_field' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"><label for="' . $post_name . '[' . $id . ']">' . esc_html( $opt['title'] ) . ':</label><div><input name="' . $post_name . '[' . $id . ']" id="' . $post_name . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? \site\utils::money_format( $value[$id] ) : ( isset( $opt['default'] ) ? esc_html( $opt['default'] ) : '' ) ) . '"' . ( isset( $opt['placeholder'] ) ? ' placeholder="' . esc_html( $opt['placeholder'] ) . '"' : '' ) . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="text" autocomplete="off"></div></div>';
            break;

            case 'callback':
                if( isset( $opt['callback'] ) && is_callable( $opt['callback'] ) )
                $markup .= '<div class="form_field' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"><label for="' . $post_name . '[' . $id . ']">' . esc_html( $opt['title'] ) . ':</label><div>' . call_user_func( $opt['callback'], $item, $value, $post_name ) . '</div></div>';
            break;

        }
    }

    return $markup;

}

}