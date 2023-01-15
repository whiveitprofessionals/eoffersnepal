<?php

/* CHECK IF AN USER IT'S ABLE TO */

function ab_to( $action = array() ) {
    $urole = $GLOBALS['me']->Erole;

    $ucan = array();

    foreach( $action as $k => $v ) {

        if( is_array( $v ) ) {

        foreach( $v as $v1 ) {

            if( $GLOBALS['me']->is_admin ) {
                $ucan[] = 1;
                continue;
            }
            
            if( !in_array( $k, array_keys( $urole ) ) || !in_array( $v1, array_keys( $urole[$k] ) ) ) {
                    $ucan[] = 0;
            } else {
                    $ucan[] = 1;
            }

        }

        } else {

            if( $GLOBALS['me']->is_admin ) {
                $ucan[] = 1;
                continue;
            }

            if( !in_array( $k, array_keys( $urole ) ) || !in_array( $v, array_keys( $urole[$k] ) ) ) {
                $ucan[] = 0;
            } else {
                $ucan[] = 1;
            }

        }

    }

    if( !in_array( 1, $ucan ) ) {
        return false;
    }

    return $ucan;
}

/* CHECK CSRF */

function check_csrf( $post, $session ) {
    return \site\utils::check_csrf( $post, $session );
}

/* CHECK IF AN IP ADDRESS IS VALID */

function valid_ip( $ip ) {
  if( preg_match( '/^([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3})$/', $ip ) ) {
    return true;
  }

  return false;
}

/* ADD HEAD */

function add_admin_head() {
    global $add_admin_styles, $add_admin_scripts, $add_admin_inline_style, $add_admin_to_head;

    $head = '<title>' . str_replace( array( '%YEAR%', '%MONTH%' ), array( date( 'Y' ), date( 'F' ) ), esc_html( \query\main::get_option( 'sitetitle' ) ) ) . ' - ' . t( 'admin_panel', 'Admin Panel' ) . '</title>' . "\r\n";
    $head .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' . "\r\n";
    $head .= '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">' . "\r\n";
    $head .= '<meta name="robots" content="noindex, nofollow">' . "\r\n";

    $theme = \query\main::get_option( 'admintheme' );
    $themes = $GLOBALS['admin_main_class']->admin_themes();

    if( in_array( $theme, array_keys( $themes ) ) ) {
        foreach( $themes[$theme]['src']['css'] as $style ) {
            $head .= '<link href="' . $style . '" media="all" rel="stylesheet" />' . "\r\n";
        }
    } else {
        $theme = 'default';
        $head .= '<link href="' . $themes['default']['src']['css'][0] . '" media="all" rel="stylesheet" />' . "\r\n";
    }

    if( is_array( $add_admin_styles ) ) {
            foreach( $add_admin_styles as $style => $options ) {
                $head .= '<link href="' . $style . '"' . ( !empty( $options ) ? \site\utils::build_atts( $options, ' ' ) : '' ) . ' />';
                $head .= "\r\n";
            }
    }

    $head .= '<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600" rel="stylesheet" />' . "\r\n";
    $head .= '<link href="theme/jquery.datetimepicker.min.css" media="all" rel="stylesheet" />' . "\r\n";
    $head .= '<link href="theme/simple-scrollbar.css" media="all" rel="stylesheet" />' . "\r\n";
    $head .= '<script src="//ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>' . "\r\n";
    $head .= '<script>window.jQuery || document.write(\'<script src="' . site_url( 'assets/js/jquery.3.6.0.min.js') . '">\x3C/script>\')</script>';
    $head .= '<script src="' . site_url( 'assets/js/jquery-ui.min.js' ) . '"></script>' . "\r\n";
    $head .= '<script src="//www.google.com/jsapi"></script>' . "\r\n";
    $head .= '<script src="js/jqColorPicker.min.js"></script>' . "\r\n";
    $head .= '<script src="js/jquery.datetimepicker.full.min.js"></script>' . "\r\n";
    $head .= '<script src="js/simple-scrollbar.min.js"></script>' . "\r\n";
    $head .= '<script src="js/plugins.js"></script>' . "\r\n";
    $head .= '<script src="html/actions.js"></script>' . "\r\n";

    if( isset( $themes[$theme]['src']['js'] ) ) {
        foreach( $themes[$theme]['src']['js'] as $js ) {
            $head .= '<script src="' . $js . '"></script>' . "\r\n";
        }
    }

    if( is_array( $add_admin_scripts ) ) {
        foreach( $add_admin_scripts as $script => $options ) {
            $head .= '<script src="' . $script . '"' . ( !empty( $options ) ? \site\utils::build_atts( $options, ' ' ) : '' ) . '></script>';
            $head .= "\r\n";
        }
    }

    if( is_array( $add_admin_inline_style ) ) {
        $head .= "<style>\r\n";
        foreach( $add_admin_inline_style as $inline_style ) {
            $head .= $inline_style . "\r\n";
        }
        $head .= "</style>\r\n";
    }

    if( is_array( $add_admin_to_head ) ) {
        foreach( $add_admin_to_head as $custom_head_text ) {
            $head .= $custom_head_text . "\r\n";
        }
    }

    return $head;
}

/* ITEMS LIST MARKUP */

function get_list_type( $type, $item = object, $links = array() ) {
    switch( $type ) {
        case 'coupon':
            return $GLOBALS['admin_main_class']->coupon_list_markup( $item, $links );
        break;
        case 'product':
            return $GLOBALS['admin_main_class']->product_list_markup( $item, $links );
        break;
        case 'store':
            return $GLOBALS['admin_main_class']->store_list_markup( $item, $links );
        break;
        case 'category':
            return $GLOBALS['admin_main_class']->category_list_markup( $item, $links );
        break;
        case 'user':
            return $GLOBALS['admin_main_class']->user_list_markup( $item, $links );
        break;
        case 'page':
            return $GLOBALS['admin_main_class']->page_list_markup( $item, $links );
        break;
        case 'click':
            return $GLOBALS['admin_main_class']->click_list_markup( $item, $links );
        break;
        case 'review':
            return $GLOBALS['admin_main_class']->review_list_markup( $item, $links );
        break;
        case 'ban':
            return $GLOBALS['admin_main_class']->ban_list_markup( $item, $links );
        break;
        case 'plugin':
            return $GLOBALS['admin_main_class']->plugin_list_markup( $item, $links );
        break;
        case 'theme':
            return $GLOBALS['admin_main_class']->theme_list_markup( $item, $links );
        break;
        case 'country':
            return $GLOBALS['admin_main_class']->country_list_markup( $item, $links );
        break;
        case 'state':
            return $GLOBALS['admin_main_class']->state_list_markup( $item, $links );
        break;
        case 'city':
            return $GLOBALS['admin_main_class']->city_list_markup( $item, $links );
        break;
        case 'subscriber':
            return $GLOBALS['admin_main_class']->subscriber_list_markup( $item, $links );
        break;
        case 'active_session':
            return $GLOBALS['admin_main_class']->active_session_list_markup( $item, $links );
        break;
        case 'suggestion':
            return $GLOBALS['admin_main_class']->suggestion_list_markup( $item, $links );
        break;
        case 'payment_plan':
            return $GLOBALS['admin_main_class']->payment_plan_list_markup( $item, $links );
        break;
        case 'payment_invoice':
            return $GLOBALS['admin_main_class']->payment_invoice_list_markup( $item, $links );
        break;
        case 'reward':
            return $GLOBALS['admin_main_class']->reward_list_markup( $item, $links );
        break;
        case 'reward_request':
            return $GLOBALS['admin_main_class']->reward_request_list_markup( $item, $links );
        break;
    }
}

/* DEVELOPER TOOLS */

// Build form fields from array

function build_form( $fields_key = 'form_data', $fields = [], $value = array(), $item = false ) {
    return \admin\widgets::build_extra( $fields, $value, '', $fields_key, $item );
}

// Create a title & content section

function build_section( $id, $title, $callback, $visible_by_default = true ) {
    if( isset( $_SESSION['ses_set'][$id] ) ) {
        $visible_by_default = (boolean) $_SESSION['ses_set'][$id];
    }
    $markup = '<div class="section-content">
    <h2 class="title">' . $title . ' <a href="#" class="ccms updown" data-set="' . $id . '">' . ( $visible_by_default ? 'R' : 'S' ) . '</a></h2>
    <div class="content"' . ( !$visible_by_default ? ' style="display:none;"' : '' ) . '>';
    if( is_callable( $callback ) ) {
        $markup .= call_user_func( $callback );
    } else {
        $markup .= $callback;
    }
    $markup .= '</div>
    </div>';

    return $markup;
}

// Modify menu markup

function modify_menu( $links ) {
    $markup = '<ul class="arrange-menu">';
    foreach( $links as $link ) {
        $markup .= '<li><div class="head"><h2 class="move"><span>' . esc_html( $link['name'] ) . '</span>
        <div class="options">
            <a href="#" class="ccms hide">w</a>
            <a href="#" class="ccms view">S</a>
            <a href="#" class="ccms remove">V</a>
        </div></h2>';
        $markup .= '<div class="content">';
        $fields = array();
        $fields['name']         = array( 'type' => 'text', 'title' => t( 'themes_menu_form_title', 'Title' ), 'class' => 'name' );
        if( !isset( $link['type'] ) || !in_array( $link['type'], array( 'home', 'store', 'stores', 'categories' ) ) ) {
            $fields['url']      = array( 'type' => 'text', 'title' => t( 'themes_menu_form_url', 'URL' ) );
        }
        if( !isset( $link['type'] ) || !in_array( $link['type'], array( 'categories' ) ) ) {
            $fields['open_type']= array( 'type' => 'select', 'title' => t( 'themes_menu_form_open_type', 'Open Type' ), 'options' => array( '_self' => t( 'themes_menu_form_self_location', 'Self / Same window' ), '_blank' => t( 'themes_menu_form_blank_location', 'Blank / New window' ) ) );
        }
        $fields['class']        = array( 'type' => 'text',      'title' => t( 'themes_menu_form_extra_class', 'Extra Class (CSS)' ) );
        $fields['type']         = array( 'type' => 'hidden' );
        if( isset( $link['identifier'] ) ) {
            $fields['identifier']   = array( 'type' => 'hidden' );
        }
        $markup .= build_form( 'links{id}', value_with_filter( 'modify_menu_fields', $fields, $link ), $link )['markup'];
        $markup .= '</div></div>';
        if( isset( $link['links'] ) ) {
            $markup .= modify_menu( $link['links'] );
        } else {
            $markup .= '<ul class="arrange-menu"></ul>';
        }
        $markup .= '</li>';
    }
    $markup .= '</ul>';

    return $markup;
}

// Custom page link

function admin_custom_link( $params = array(), $default_params = array( 'route' => 'link.php' ) ) {
    return '?' . http_build_query( array_merge( $default_params, $params ), '', '&amp;');
}

function admin_user_link( $link, $params = array() ) {
    return '?' . http_build_query( array_merge( $params, array( 'route' => 'link.php', 'main' => $link ) ), '', '&amp;');
}