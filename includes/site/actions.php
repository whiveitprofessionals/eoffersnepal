<?php

namespace site;

/** */

class actions {

public static function add( $type, $options, $extra ) {
    switch( $type ) {
        case 'widget':
        case 'widgets':
            global $add_widgets;
            $add_widgets[$options] = $extra;
        break;
        case 'widgets-zone':
            global $add_widgets_zone;
            $add_widgets_zone[$options] = $extra;
        break;
        case 'shortcode':
        case 'shortcodes':
            global $add_shortcodes;
            $add_shortcodes[$options] = $extra;
        break;
        case 'filter':
        case 'filters':
            global $add_filters;
            if( is_array( $options ) ) {
                foreach( $options as $option ) {
                    $add_filters[$option][] = $extra;
                }
            } else {
                $add_filters[$options][] = $extra;
            }
        break;
        case 'menu':
            global $add_menu;
            $add_menu[$options] = $extra;
        break;
        case 'ajax_call':
        case 'ajax-call':
            global $add_ajax_calls;
            $add_ajax_calls[$options] = $extra;
        break;
        case 'coupon-fields':
            global $add_coupon_fields;
            $add_coupon_fields[] = $options;
        break;
        case 'user-coupon-fields':
            global $add_form_fields;
            $add_form_fields['coupon'][] = $options;
        break;
        case 'product-fields':
            global $add_product_fields;
            $add_product_fields[] = $options;
        break;
        case 'user-product-fields':
            global $add_form_fields;
            $add_form_fields['product'][] = $options;
        break;
        case 'store-fields':
            global $add_store_fields;
            $add_store_fields[] = $options;
        break;
        case 'user-store-fields':
            global $add_form_fields;
            $add_form_fields['store'][] = $options;
        break;
        case 'category-fields':
            global $add_category_fields;
            $add_category_fields[] = $options;
        break;
        case 'page-fields':
            global $add_page_fields;
            $add_page_fields[] = $options;
        break;
        case 'user-fields':
            global $add_user_fields;
            $add_user_fields[] = $options;
        break;
        case 'user-user-fields':
            global $add_form_fields;
            $add_form_fields['user'][] = $options;
        break;
        case 'user-register-fields':
            global $add_form_fields;
            $add_form_fields['register'][] = $options;
        break;
        case 'user-login-fields':
            global $add_form_fields;
            $add_form_fields['login'][] = $options;
        break;
        case 'style':
        case 'styles':
            global $add_styles;
            $add_styles[$options] = $extra;
        break;
        case 'inline-style':
            global $add_inline_style;
            $add_inline_style[] = $options;
        break;
        case 'admin-inline-style':
            global $add_admin_inline_style;
            $add_admin_inline_style[] = $options;
        break;
        case 'in-head':
            global $add_to_head;
            $add_to_head[] = $options;
        break;
        case 'body-class':
        case 'body-classes':
            global $add_body_class;
            if( gettype( $options ) == 'array' ) {
                foreach( $options as $option ) {
                    $add_body_class[$option] = '';
                }
            } else {
                $add_body_class[$options] = '';
            }
        break;
        case 'admin-in-head':
            global $add_admin_to_head;
            $add_admin_to_head[] = $options;
        break;
        case 'admin-style':
        case 'admin-styles':
            global $add_admin_styles;
            $add_admin_styles[$options] = $extra;
        break;
        case 'script':
        case 'scripts':
            global $add_scripts;
            $add_scripts[$options] = $extra;
        break;
        case 'admin-script':
        case 'admin-scripts':
            global $add_admin_scripts;
            $add_admin_scripts[$options] = $extra;
        break;
        case 'admin-theme':
        case 'admin-themes':
            global $add_admin_themes;
            $add_admin_themes[$options] = $extra;
        break;
        case 'admin-menu':
            global $add_admin_menu;
            $add_admin_menu[$options] = $extra;
        break;
        case 'admin-list-style':
            global $add_admin_list_style;
            $add_admin_list_style[$options] = $extra;
        break;
        case 'action':
        case 'actions':
            global $add_action;
            $add_action[$options][] = $extra;
        break;
        case 'page-load-after':
            global $add_page_load_after;
            $add_page_load_after[$options] = ( !empty( $extra ) ? $extra : 1 );
        break;
        case 'theme-page':
        case 'theme-pages':
            global $add_theme_page;
            if( is_array( $options ) ) {
                foreach( $options as $option ) {
                    $add_theme_page[strtolower($option)] = $extra;
                }
            } else {
                $add_theme_page[strtolower($options)] = $extra;
            }
        break;
        case 'translation':
            global $add_translation;
            $add_translation[$options] = $extra;
        break;
    }
}

public static function get( $type, $options ) {
    switch( $type ) {
        case 'widget':
        case 'widgets':
            global $add_widgets;
            return $add_widgets;
        break;
        case 'widgets-zone':
            global $add_widgets_zone;
            return $add_widgets_zone;
        break;
        case 'shortcode':
        case 'shortcodes':
            global $add_shortcodes;
            return $add_shortcodes;
        break;
        case 'filter':
        case 'filters':
            global $add_filters;
            return ( !empty( $options ) ? ( isset( $add_filters[$options] ) ? $add_filters[$options] : false ) : $add_filters );
        break;
        case 'menu':
        case 'menus':
            global $add_menu;
            return ( !empty( $options ) ? ( isset( $add_menu[$options] ) ? $add_menu[$options] : false ) : $add_menu );
        break;
        case 'ajax_calls':
        case 'ajax-calls':
            global $add_ajax_calls;
            return $add_ajax_calls;
        break;
        case 'removed_widgets':
            global $remove_widgets;
            return $remove_widgets;
        break;
        case 'coupon-fields':
            global $add_coupon_fields;
            return $add_coupon_fields;
        break;
        case 'user-coupon-fields':
            global $add_form_fields;
            return ( isset( $add_form_fields['coupon'] ) ? $add_form_fields['coupon'] : false );
        break;
        case 'product-fields':
            global $add_product_fields;
            return $add_product_fields;
        break;
        case 'user-product-fields':
            global $add_form_fields;
            return ( isset( $add_form_fields['user'] ) ? $add_form_fields['user'] : false );
        break;
        case 'store-fields':
            global $add_store_fields;
            return $add_store_fields;
        break;
        case 'user-store-fields':
            global $add_form_fields;
            return ( isset( $add_form_fields['store'] ) ? $add_form_fields['store'] : false );
        break;
        case 'category-fields':
            global $add_category_fields;
            return $add_category_fields;
        break;
        case 'page-fields':
            global $add_page_fields;
            return $add_page_fields;
        break;
        case 'user-fields':
            global $add_user_fields;
            return $add_user_fields;
        break;
        case 'user-user-fields':
            global $add_form_fields;
            return ( isset( $add_form_fields['user'] ) ? $add_form_fields['user'] : false );
        break;
        case 'user-register-fields':
            global $add_form_fields;
            return ( isset( $add_form_fields['register'] ) ? $add_form_fields['register'] : false );
        break;
        case 'user-login-fields':
            global $add_form_fields;
            return ( isset( $add_form_fields['user'] ) ? $add_form_fields['user'] : false );
        break;
        case 'style':
        case 'styles':
            global $add_styles;
            return array_keys( $add_styles );
        break;
        case 'inline-style':
            global $add_inline_style;
            return array_keys( $add_inline_style );
        break;
        case 'in-head':
            global $add_to_head;
            return array_keys( $add_to_head );
        break;
        case 'body-class':
        case 'body-classes':
            global $add_body_class;
            return $add_body_class;
        break;
        case 'admin-style':
        case 'admin-styles':
            global $add_admin_styles;
            return array_keys( $add_admin_styles );
        break;
        case 'script':
        case 'scripts':
            global $add_scripts;
            return array_keys( $add_scripts );
        break;
        case 'admin-script':
        case 'admin-scripts':
            global $add_admin_scripts;
            return array_keys( $add_admin_scripts );
        break;
        case 'admin-theme':
        case 'admin-themes':
            global $add_admin_themes;
            return $add_admin_themes;
        break;
        case 'admin-list-style':
            global $add_admin_list_style;
            return $add_admin_list_style;
        break;
        case 'action':
        case 'actions':
            global $add_action;
            if( !empty( $options ) ) {
                return ( isset( $add_action[$options] ) ? $add_action[$options] : false );
            }
            return $add_action;
        break;
        case 'pages-load-after':
            global $add_page_load_after;
            return $add_page_load_after;
        break;
        case 'theme-page':
        case 'theme-pages':
            global $add_theme_page;
            return $add_theme_page;
        break;
        case 'translations':
            global $add_translation;
            return $add_translation;
        break;
    }
}

public static function remove( $type, $options, $extra ) {
    switch( $type ) {
        case 'widget':
        case 'widgets':
            global $remove_widgets;
            $remove_widgets = (array) $remove_widgets + (array) $options;
        break;
        case 'widgets-zone':
            global $add_widgets_zone;
            unset( $add_widgets_zone[$options] );
        break;
        case 'shortcode':
        case 'shortcodes':
            global $add_shortcodes;
            unset( $add_shortcodes[$options] );
        break;
        case 'filter':
        case 'filters':
            global $add_filters;
            unset( $add_filters[$options][array_search( $extra, array_values( $add_filters[$options] ) )] );
        break;
        case 'menu':
            global $add_menu;
            unset( $add_menu[$options] );
        break;
        case 'ajax_call':
        case 'ajax-call':
            global $add_ajax_calls;
            unset( $add_ajax_calls[$options] );
        break;
        case 'style':
        case 'styles':
            global $add_styles;
            unset( $add_styles[$options] );
        break;
        case 'admin-style':
        case 'admin-styles':
            global $add_admin_styles;
            unset( $add_admin_styles[$options] );
        break;
        case 'body-class':
        case 'body-classes':
            global $add_body_class;
            unset( $add_body_class[$options] );
        break;
        case 'script':
        case 'scripts':
            global $add_scripts;
            unset( $add_scripts[$options] );
        break;
        case 'admin-script':
        case 'admin-scripts':
            global $add_admin_scripts;
            unset( $add_admin_scripts[$options] );
        break;
        case 'admin-theme':
        case 'admin-themes':
            global $add_admin_themes;
            unset( $add_admin_themes[$options] );
        break;
        case 'admin-list-style':
            global $add_admin_list_style;
            unset( $add_admin_list_style[$options] );
        break;
        case 'action':
        case 'actions':
            global $add_action;
            unset( $add_action[$options] );
        break;
        case 'page-load-after':
            global $add_page_load_after;
            unset( $add_page_load_after[$options] );
        break;
        case 'theme-page':
        case 'theme-pages':
            global $add_theme_page;
            unset( $add_theme_page[strtolower($options)] );
        break;
        case 'translation':
            global $add_translation;
            unset( $add_translation[$options] );
        break;
    }
}

}