<?php

namespace admin;

/** */

class widgets {

public static function widgets_list() {

  $widgets = array();

  $widgets['search_box']= array( 'name' => t( 'widget_search_box', "Search box" ), 'file' =>  WIGETS_LOCATION . '/search.php', 'def_type' => '', 'allow_orderby' => false, 'allow_show' => false );
  $widgets['categories']= array( 'name' => t( 'widget_categories', "Categories" ), 'file' => WIGETS_LOCATION . '/categories.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => t( 'order_random', "Random" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'views' => t( 'order_views', "Views" ), 'views desc' => t( 'order_views_desc', "Views DESC" ), 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ) ), 'allow_show' => false, 'allow_limit' => true );
  $widgets['coupons']   = array( 'name' => t( 'widget_coupons', "Coupons" ), 'file' => WIGETS_LOCATION . '/coupons.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => t( 'order_random', "Random" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'views' => t( 'order_views', "Views" ), 'views desc' => t( 'order_views_desc', "Views DESC" ), 'votes' => t( 'order_votes', "Votes" ), 'votes desc' => t( 'order_votes_desc', "Votes DESC" ), 'rating' => t( 'order_rating', "Rating" ), 'rating desc' => t( 'order_rating_desc', "Rating DESC" ), 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ) ), 'allow_show' => array( '' => t( 'show_all', "All" ), 'active' => t( 'show_active', "Active" ), 'popular' => t( 'show_popular', "Popular" ), 'exclusive' => t( 'show_exclusive', "Exclusive" ) ), 'allow_limit' => true );
  $widgets['coupons2']  = array( 'name' => t( 'widget_coupons', "Coupons" ), 'description' => 'v2', 'file' => WIGETS_LOCATION . '/coupons_v2.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => t( 'order_random', "Random" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'views' => t( 'order_views', "Views" ), 'views desc' => t( 'order_views_desc', "Views DESC" ), 'votes' => t( 'order_votes', "Votes" ), 'votes desc' => t( 'order_votes_desc', "Votes DESC" ), 'rating' => t( 'order_rating', "Rating" ), 'rating desc' => t( 'order_rating_desc', "Rating DESC" ), 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ) ), 'allow_show' => array( '' => t( 'show_all', "All" ), 'active' => t( 'show_active', "Active" ), 'popular' => t( 'show_popular', "Popular" ), 'exclusive' => t( 'show_exclusive', "Exclusive" ) ), 'allow_limit' => true );
  $widgets['reviews']   = array( 'name' => t( 'widget_reviews', "Reviews" ), 'file' => WIGETS_LOCATION . '/reviews.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => t( 'order_random', "Random" ), 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ) ), 'allow_show' => false, 'allow_limit' => true );
  $widgets['pages']     = array( 'name' => t( 'widget_pages', "Pages" ), 'file' => WIGETS_LOCATION . '/pages.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => t( 'order_random', "Random" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'views' => t( 'order_views', "Views" ), 'views desc' => t( 'order_views_desc', "Views DESC" ) ), 'allow_show' => false, 'allow_limit' => true );
  $widgets['stores']    = array( 'name' => t( 'widget_stores', "Stores" ), 'file' => WIGETS_LOCATION . '/stores.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => t( 'order_random', "Random" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'views' => t( 'order_views', "Views" ), 'views desc' => t( 'order_views_desc', "Views DESC" ), 'votes' => t( 'order_votes', "Votes" ), 'votes desc' => t( 'order_votes_desc', "Votes DESC" ), 'rating' => t( 'order_rating', "Rating" ), 'rating desc' => t( 'order_rating_desc', "Rating DESC" ), 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ) ), 'allow_show' => array( '' => t( 'show_all', "All" ), 'popular' => t( 'show_popular', "Popular" ) ), 'allow_limit' => true );
  $widgets['stores2']   = array( 'name' => t( 'widget_stores', "Stores" ), 'description' => 'v2', 'file' => WIGETS_LOCATION . '/stores_v2.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => t( 'order_random', "Random" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'views' => t( 'order_views', "Views" ), 'views desc' => t( 'order_views_desc', "Views DESC" ), 'votes' => t( 'order_votes', "Votes" ), 'votes desc' => t( 'order_votes_desc', "Votes DESC" ), 'rating' => t( 'order_rating', "Rating" ), 'rating desc' => t( 'order_rating_desc', "Rating DESC" ), 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ) ), 'allow_show' => array( '' => t( 'show_all', "All" ), 'popular' => t( 'show_popular', "Popular" ) ), 'allow_limit' => true );
  $widgets['history']   = array( 'name' => t( 'widget_history', "View History" ), 'description' => 'v2', 'file' => WIGETS_LOCATION . '/stores_history.php', 'def_type' => '', 'def_limit' => 10, 'max_limit' => 30, 'allow_orderby' => false, 'allow_show' => false, 'allow_limit' => true );
  $widgets['text_box']  = array( 'name' => t( 'widget_text_box', "Text Box" ), 'file' => WIGETS_LOCATION . '/text-box.php', 'def_type' => '', 'text' => t( 'widget_text_box_deftext', "Write some text here ..." ), 'allow_text' => true, 'allow_html' => true, 'allow_orderby' => false, 'allow_show' => false  );
  $widgets['users']     = array( 'name' => t( 'widget_users', "Users" ), 'file' => WIGETS_LOCATION . '/users.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => t( 'order_random', "Random" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'points' => t( 'order_points', "Points" ), 'points desc' => t( 'order_points_desc', "Points DESC" ), 'visits' => t( 'order_visits', "Visits" ), 'visits desc' => t( 'order_visits_desc', "Visits DESC" ) ), 'allow_show' => false, 'allow_limit' => true );
  $widgets['newsletter']= array( 'name' => t( 'widget_newsletter', "Newsletter" ), 'file' => WIGETS_LOCATION . '/newsletter.php', 'def_type' => '', 'text' => t( 'widget_text_newsletter', "Subscribe to our newsletter!" ), 'allow_text' => true, 'allow_orderby' => false, 'allow_show' => false );
  $widgets['fb_like']   = array( 'name' => t( 'widget_fb_like_box', "Facebook Like Box" ), 'file' => WIGETS_LOCATION . '/facebook_like_box.php', 'def_type' => '', 'text' => t( 'widget_fb_like_box_msg', "Put your Facebook page address here !" ), 'allow_text' => true, 'allow_orderby' => false, 'allow_show' => false );
  $widgets['suggest']   = array( 'name' => t( 'widget_suggest', "Send suggestion form" ), 'file' => WIGETS_LOCATION . '/suggest.php', 'def_type' => '', 'text' => t( 'widget_suggest_msg', "Did we missed your favorite store? Please suggest it here." ), 'allow_text' => true, 'allow_orderby' => false, 'allow_show' => false );
  $widgets['contact']   = array( 'name' => t( 'widget_contact', "Contact form" ), 'file' => WIGETS_LOCATION . '/contact.php', 'def_type' => '', 'text' => t( 'widget_contact_msg', "Send us a message" ), 'allow_text' => true, 'allow_orderby' => false, 'allow_show' => false );
  $widgets['products']  = array( 'name' => t( 'widget_products', "Products" ), 'file' => WIGETS_LOCATION . '/products.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => t( 'order_random', "Random" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'views' => t( 'order_views', "Views" ), 'views desc' => t( 'order_views_desc', "Views DESC" ), 'votes' => t( 'order_votes', "Votes" ), 'votes desc' => t( 'order_votes_desc', "Votes DESC" ), 'rating' => t( 'order_rating', "Rating" ), 'rating desc' => t( 'order_rating_desc', "Rating DESC" ), 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ) ), 'allow_show' => array( '' => t( 'show_all', "All" ), 'active' => t( 'show_active', "Active" ), 'popular' => t( 'show_popular', "Popular" ), 'exclusive' => t( 'show_exclusive', "Exclusive" ) ), 'allow_limit' => true );
  $widgets['products2'] = array( 'name' => t( 'widget_products', "Products" ), 'description' => 'v2', 'file' => WIGETS_LOCATION . '/products_v2.php', 'def_type' => '', 'def_limit' => 10, 'allow_orderby' => array( 'rand' => t( 'order_random', "Random" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'votes' => t( 'order_votes', "Votes" ), 'votes desc' => t( 'order_votes_desc', "Votes DESC" ), 'rating' => t( 'order_rating', "Rating" ), 'rating desc' => t( 'order_rating_desc', "Rating DESC" ), 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ) ), 'allow_show' => array( '' => t( 'show_all', "All" ), 'active' => t( 'show_active', "Active" ), 'popular' => t( 'show_popular', "Popular" ), 'exclusive' => t( 'show_exclusive', "Exclusive" ) ), 'allow_limit' => true );

  return $widgets;

}

public static function widget_from_id( $id ) {

    global $add_widgets;
    $add_widgets = (array) $add_widgets;

    $list = self::widgets_list() + $add_widgets;

    if( in_array( $id, array_keys( $list ) ) ) {
      return (object) $list[$id];
    }

    return false;

}

public static function available_list( $zone = '' ) {

    global $add_widgets, $remove_widgets;
    $add_widgets = (array) $add_widgets;

    $list = self::widgets_list();

    if( isset( $remove_widgets[$zone] ) ) {

        if( is_string( $remove_widgets[$zone] ) ) {
            $remove_widgets[$zone] = array( $remove_widgets[$zone] );
        }

        if( array_search( '*', $remove_widgets[$zone] ) == false ) $list = array();
        else $list = array_diff_key( $list, array_flip( $remove_widgets[$zone] ) );

    }

    if( !empty( $add_widgets ) ) {

        $add_widgets = array_filter( array_map( function( $v ) use ( $zone ) {
            if( isset( $v['zone'] ) ) {
                switch( gettype( $v['zone'] ) ) {
                    case 'array':
                        if( !in_array( $zone, $v['zone'] ) ) return false;
                    break;
                    case 'string':
                        if( $v['zone'] != $zone ) return false;
                    break;
                }
            }
            return $v;
        }, $add_widgets ) );

        $list = $list + $add_widgets;
    }

    return $list;

}

public static function build_extra( $fields = array(), $value = array(), $extra_id = '', $post_name = 'extra', $item = false ) {

    if( empty( $fields ) ) {
        return array( 'markup' => '', 'sections' => array() );
    }

    $markup     = '';
    $sections   = array();

    foreach( $fields as $id => $opt ) {
        $class = array();
        if( !empty( $opt['class'] ) ) {
            $class[] = esc_html( $opt['class'] );
        }

        $is_section = ( isset( $opt['section_id'] ) && isset( $opt['section'] ) );

        if( $is_section ) {
            $sections[$opt['section_id']] = $opt['section'];
            $opt['row_atts'] = !empty( $opt['row_atts'] ) ? array_merge( $opt['row_atts'], array( 'data-in-section' => $opt['section_id'] ) ) : array( 'data-in-section' => $opt['section_id'] );
        }

        if( !empty( $opt['required'] ) ) {
            $opt['row_atts']['data-required'] = esc_html( str_replace( array( '{this}' ), array( $post_name . $extra_id ), json_encode( $opt['required'] ) ) );
        }

        if( !empty( $opt['multi'] ) && !isset( $opt['type'] ) ) {

            if( !empty( $opt['required'] ) ) {
                $opt['row_atts']['data-required-name'] = $id;
            }

            $markup .= '<div class="row multi-rows' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ':</span>

            <div class="sortable">';

            $r = 0;
            if( !empty( $value[$opt['id']] ) ) {
                foreach( $value[$opt['id']] as $rid => $values ) {
                    $markup .= '<div class="rows">
                    <div class="head-row">' . esc_html( $opt['rows_title'] ) . '
                    <div class="options">
                    <a href="#" class="view">S</a>';
                    if( !isset( $opt['sortable'] ) || $opt['sortable'] ) {
                        $markup .= '<a href="#" class="move">v</a>';
                    }
                    $markup .= '<a href="#" class="remove">V</a></div>
                    </div>
                    <div class="rows-list">';
                    $markup .= self::build_extra( $opt['fields'], $values, $extra_id . '[' . $opt['id'] . '][' . $rid . ']', $post_name, $item )['markup'];
                    $markup .= '</div>
                    </div>';
                    $r++;
                }
            }

            $markup .= '<a href="#" class="btn" data-start-row="' . $r . '">' . ( !empty( $opt['add_button_title'] ) ? esc_html( $opt['add_button_title'] ) : t( 'add_row', 'Add row' ) ) . '</a>';

            $markup .= '<div class="rows" style="display:none;">
            <div class="head-row">' . esc_html( $opt['rows_title'] ) . '
            <div class="options">';
            $markup .= '<a href="#" class="view">S</a>';
            $markup .= '<a href="#" class="move">v</a>';
            $markup .= '<a href="#" class="remove">V</a>';
            $markup .= '</div>
            </div>

            <div class="rows-list">';
            $markup .= self::build_extra( $opt['fields'], array(), $extra_id . '[' . $opt['id'] . '][{id}]', $post_name, $item )['markup'];
            $markup .= '</div>
            </div>

            </div>

            </div>';

            continue;

        }

        if( isset( $opt['groups'] ) ) {

            if( !empty( $opt['required'] ) ) {
                $opt['row_atts']['data-required-name'] = $id;
            }

            $markup .= '<div class="row multi-rows' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ':</span>
            <div>';

            foreach( $opt['groups'] as $gid => $values ) {
                $markup .= '<div class="rows">
                <div class="head-row">' . esc_html( $values['title'] ) . '
                <div class="options">
                <a href="#" class="view">S</a></div>
                </div>
                <div class="rows-list">';
                $markup .= self::build_extra( $values['fields'], ( isset( $value[$id][$gid] ) ? $value[$id][$gid] : array() ), $extra_id . '[' . $id . '][' . $gid . ']', $post_name, $item )['markup'];
                $markup .= '</div>
                </div>';
            }

            $markup .= '</div>
            </div>';

            continue;

        }

        if( isset( $opt['group'] ) ) {

            if( !empty( $opt['required'] ) ) {
                $opt['row_atts']['data-required-name'] = $id;
            }

            $markup .= '<div class="row multi-rows' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ':</span>
            <div>';

            $markup .= '<div class="rows">
            <div class="head-row">' . esc_html( $opt['group']['title'] ) . '
            <div class="options">
            <a href="#" class="view">S</a></div>
            </div>
            <div class="rows-list">';
            $markup .= self::build_extra( $opt['group']['fields'], ( isset( $value[$id] ) ? $value[$id] : array() ), $extra_id . '[' . $id . ']', $post_name, $item )['markup'];
            $markup .= '</div>
            </div>';

            $markup .= '</div>
            </div>';

            continue;

        }

        if( !isset( $opt['type'] ) ) {
            continue;
        }

        switch( $opt['type'] ) {

            case 'text':
            if( empty( $opt['multi'] ) ) {
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div><input name="' . $post_name . $extra_id . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? esc_html( $value[$id] ) : ( isset( $opt['default'] ) ? esc_html( $opt['default'] ) : '' ) ) . '"' . ( isset( $opt['placeholder'] ) ? ' placeholder="' . esc_html( $opt['placeholder'] ) . '"' : '' ) . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="text" autocomplete="off"></div></div>';
            } else {
                $markup .= '<div class="row fields' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div>
                <ul class="fields_table full_fields' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? ' sortable' : '' ) . '">';
                if( !empty( $value[$id] ) && is_array( $value[$id] ) ) {
                    $key = '';
                    foreach( $value[$id] as $k => $val ) {
                        if( !empty( $opt['keep_key'] ) ) {
                            $key = $k;
                        }
                        $markup .= '<li class="added_field">
                        <div><input name="' . $post_name . $extra_id . '[' . $id . '][' . $key . ']" value="' . esc_html( $val ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="text" autocomplete="off" /></div>
                        <div class="options">
                            ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                            <a href="#" class="remove">V</a>
                        </div>
                        </li>';
                    }
                }
                $markup .= '<li class="fields_table_new" style="display:none;">
                <div><input name="' . $post_name . $extra_id . '[' . $id . '][]" value=""' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="text" autocomplete="off" /></div>
                <div class="options">
                    ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                    <a href="#" class="remove">V</a>
                </div>
                </li>';
                $markup .= '</ul>
                <a href="#" class="btn"' . ( !empty( $opt['keep_key'] ) ? ' data-keeps-key' : '' ) . '>' . ( !empty( $opt['add_button_title'] ) ? esc_html( $opt['add_button_title'] ) : t( 'add_field', 'Add field' ) ) . '</a>
                </div></div>';
            }
            break;

            case 'select':
            if( empty( $opt['multi'] ) ) {
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div><select name="' . $post_name . $extra_id . '[' . $id . ']"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . '>';
                    foreach( $opt['options'] as $k => $v ) {
                        $markup .= '<option value="' . esc_html( $k ) . '"' . ( ( isset( $value[$id] ) && $k == $value[$id] ) || ( !isset( $value[$id] ) && isset( $opt['default'] ) && $k == $opt['default'] ) ? ' selected' : '' ) . '>' . esc_html( $v ) . '</option>';
                    }
                $markup .= '</select></div></div>';
            } else {
                $markup .= '<div class="row fields' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div>
                <ul class="fields_table full_fields' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? ' sortable' : '' ) . '">';
                if( !empty( $value[$id] ) && is_array( $value[$id] ) ) {
                    $key = '';
                    foreach( $value[$id] as $k => $val ) {
                        if( !empty( $opt['keep_key'] ) ) {
                            $key = $k;
                        }
                        $markup .= '<li class="added_field">
                        <div><select name="' . $post_name . $extra_id . '[' . $id . '][' . $key . ']"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . '>';
                        foreach( $opt['options'] as $k => $v ) {
                            $markup .= '<option value="' . esc_html( $k ) . '"' . ( $k == $val ? ' selected' : '' ) . '>' . esc_html( $v ) . '</option>';
                        }
                        $markup .= '</select></div>
                        <div class="options">
                            ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                            <a href="#" class="remove">V</a>
                        </div>
                        </li>';
                    }
                }
                $markup .= '<li class="fields_table_new" style="display:none;">
                <div><select name="' . $post_name . $extra_id . '[' . $id . '][]"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . '>';
                    foreach( $opt['options'] as $k => $v ) {
                        $markup .= '<option value="' . esc_html( $k ) . '">' . esc_html( $v ) . '</option>';
                    }
                $markup .= '</select></div>
                <div class="options">
                    ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                    <a href="#" class="remove">V</a>
                </div>
                </li>';
                $markup .= '</ul>
                <a href="#" class="btn"' . ( !empty( $opt['keep_key'] ) ? ' data-keeps-key' : '' ) . '>' . ( !empty( $opt['add_button_title'] ) ? esc_html( $opt['add_button_title'] ) : t( 'add_field', 'Add field' ) ) . '</a>
                </div></div>';
            }
            break;

            case 'textarea':
            if( empty( $opt['multi'] ) ) {
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div><textarea name="' . $post_name . $extra_id . '[' . $id . ']"' . ( isset( $opt['placeholder'] ) ? ' placeholder="' . esc_html( $opt['placeholder'] ) . '"' : '' ) . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . '>' . ( isset( $value[$id] ) ? esc_html( $value[$id] ) : ( isset( $opt['default'] ) ? esc_html( $opt['default'] ) : '' ) ) . '</textarea></div></div>';
            } else {
                $markup .= '<div class="row fields' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div>
                <ul class="fields_table full_fields' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? ' sortable' : '' ) . '">';
                if( !empty( $value[$id] ) && is_array( $value[$id] ) ) {
                    $key = '';
                    foreach( $value[$id] as $k => $val ) {
                        if( !empty( $opt['keep_key'] ) ) {
                            $key = $k;
                        }
                        $markup .= '<li class="added_field">
                        <div><textarea name="' . $post_name . $extra_id . '[' . $id . '][' . $key . ']"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . '>' . esc_html( $val ) . '</textarea></div>
                        <div class="options">
                            ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                            <a href="#" class="remove">V</a>
                        </div>
                        </li>';
                    }
                }
                $markup .= '<li class="fields_table_new" style="display:none;">
                <div><textarea name="' . $post_name . $extra_id . '[' . $id . '][]"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . '></textarea></div>
                <div class="options">
                    ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                    <a href="#" class="remove">V</a>
                </div>
                </li>';
                $markup .= '</ul>
                <a href="#" class="btn"' . ( !empty( $opt['keep_key'] ) ? ' data-keeps-key' : '' ) . '>' . ( !empty( $opt['add_button_title'] ) ? esc_html( $opt['add_button_title'] ) : t( 'add_field', 'Add field' ) ) . '</a>
                </div></div>';
            }
            break;

            case 'checkbox':
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div><label><input name="' . $post_name . $extra_id . '[' . $id . ']"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="checkbox"' . ( !empty( $value[$id] ) ? ' checked' : ( !empty( $opt['default'] ) ? ' checked' : '' ) ) . '> <span></span> ' . ( !empty( $opt['label'] ) ? esc_html( $opt['label'] ) : '' ) . '</label></div></div>';
            break;

            case 'hidden':
                $markup .= '<input name="' . $post_name . $extra_id . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? esc_html( $value[$id] ) : ( isset( $opt['default'] ) ? esc_html( $opt['default'] ) : '' ) ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="hidden">';
            break;

            case 'number':
            if( empty( $opt['multi'] ) ) {
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div><input name="' . $post_name . $extra_id . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? (int) $value[$id] : ( isset( $opt['default'] ) ? (int) $opt['default'] : '' ) ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="number"' . ( isset( $opt['min'] ) ? ' min="' . (int) $opt['min'] . '"' : '' ) . ( isset( $opt['max'] ) ? ' max="' . (int) $opt['max'] . '"' : '' ) . ' autocomplete="off" /></div></div>';
            } else {
                $markup .= '<div class="row fields' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div>
                <ul class="fields_table full_fields' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? ' sortable' : '' ) . '">';
                if( !empty( $value[$id] ) && is_array( $value[$id] ) ) {
                    $key = '';
                    foreach( $value[$id] as $k => $val ) {
                        if( !empty( $opt['keep_key'] ) ) {
                            $key = $k;
                        }
                        $markup .= '<li class="added_field">
                        <div><input name="' . $post_name . $extra_id . '[' . $id . '][' . $key . ']" value="' . esc_html( $val ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="number"' . ( isset( $opt['min'] ) ? ' min="' . (int) $opt['min'] . '"' : '' ) . ( isset( $opt['max'] ) ? ' max="' . (int) $opt['max'] . '"' : '' ) . ' autocomplete="off" /></div>
                        <div class="options">
                            ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                            <a href="#" class="remove">V</a>
                        </div>
                        </li>';
                    }
                }
                $markup .= '<li class="fields_table_new" style="display:none;">
                <div><input name="' . $post_name . $extra_id . '[' . $id . '][]" value=""' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="number" min="' . ( isset( $opt['min'] ) ? ' min="' . (int) $opt['min'] . '"' : '' ) . ( isset( $opt['max'] ) ? ' max="' . (int) $opt['max'] . '"' : '' ) . ' autocomplete="off" /></div>
                <div class="options">
                    ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                    <a href="#" class="remove">V</a>
                </div>
                </li>';
                $markup .= '</ul>
                <a href="#" class="btn"' . ( !empty( $opt['keep_key'] ) ? ' data-keeps-key' : '' ) . '>' . ( !empty( $opt['add_button_title'] ) ? esc_html( $opt['add_button_title'] ) : t( 'add_field', 'Add field' ) ) . '</a>
                </div></div>';
            }
            break;

            case 'price':
            if( empty( $opt['multi'] ) ) {
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div><input name="' . $post_name . $extra_id . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? \site\utils::money_format( $value[$id] ) : ( isset( $opt['default'] ) ? esc_html( $opt['default'] ) : '' ) ) . '"' . ( isset( $opt['placeholder'] ) ? ' placeholder="' . esc_html( $opt['placeholder'] ) . '"' : '' ) . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="text" autocomplete="off"></div></div>';
            } else {
                $markup .= '<div class="row fields' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div>
                <ul class="fields_table full_fields' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? ' sortable' : '' ) . '">';
                if( !empty( $value[$id] ) && is_array( $value[$id] ) ) {
                    $key = '';
                    foreach( $value[$id] as $k => $val ) {
                        if( !empty( $opt['keep_key'] ) ) {
                            $key = $k;
                        }
                        $markup .= '<li class="added_field">
                        <div><input name="' . $post_name . $extra_id . '[' . $id . '][' . $key . ']" value="' . \site\utils::money_format( $val ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="text" autocomplete="off" /></div>
                        <div class="options">
                            ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                            <a href="#" class="remove">V</a>
                        </div>
                        </li>';
                    }
                }
                $markup .= '<li class="fields_table_new" style="display:none;">
                <div><input name="' . $post_name . $extra_id . '[' . $id . '][]" value=""' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="text" autocomplete="off" /></div>
                <div class="options">
                    ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                    <a href="#" class="remove">V</a>
                </div>
                </li>';
                $markup .= '</ul>
                <a href="#" class="btn"' . ( !empty( $opt['keep_key'] ) ? ' data-keeps-key' : '' ) . '>' . ( !empty( $opt['add_button_title'] ) ? esc_html( $opt['add_button_title'] ) : t( 'add_field', 'Add field' ) ) . '</a>
                </div></div>';
            }
            break;

            case 'stores':
            if( empty( $opt['multi'] ) ) {
                $store_markup = '';
                if( isset( $value[$id] ) && \query\main::store_exists( $value[$id] ) ) {
                    $store_info = \query\main::store_info( $value[$id], array( 'no_emoticons' => true, 'no_filters' => true ) );
                    $store_markup = $store_info->name . ' (ID: ' . $store_info->ID . ')';
                }
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div data-search="store" data-set-class="wstore"><input type="text" name="' . $post_name . $extra_id . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? esc_html( $value[$id] ) : '' ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' autocomplete="off" /><a href="#" class="downarr"></a>
                                <span class="idinfo">' . $store_markup . '</span>
                            </div></div>';
            } else {
                $markup .= '<div class="row fields' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div>
                <ul class="fields_table full_fields' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? ' sortable' : '' ) . '">';
                if( !empty( $value[$id] ) && is_array( $value[$id] ) ) {
                    $key = '';
                    foreach( $value[$id] as $k => $val ) {
                        if( !empty( $opt['keep_key'] ) ) {
                            $key = $k;
                        }
                        $markup .= '<li class="added_field">';
                        $store_markup = '';
                        if( ( $exists = \query\main::store_exists( $val ) ) ) {
                            $store_info = \query\main::store_info( $val, array( 'no_emoticons' => true, 'no_filters' => true ) );
                            $store_markup = $store_info->name . ' (ID: ' . $store_info->ID . ')';
                        }
                        $markup .= '<div data-search="store" data-set-class="wstore"><input type="text" name="' . $post_name . $extra_id . '[' . $id . '][' . $key . ']" value="' . ( !empty( $exists ) ? esc_html( $val ) : '' ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' autocomplete="off" /><a href="#" class="downarr"></a>
                                        <span class="idinfo">' . $store_markup . '</span>
                                    </div>
                        <div class="options">
                            ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                            <a href="#" class="remove">V</a>
                        </div>
                        </li>';
                    }
                }
                $markup .= '<li class="fields_table_new" style="display:none;">
                    <div data-search="store" data-set-class="wstore"><input type="text" name="' . $post_name . $extra_id . '[' . $id . '][]" value=""' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' autocomplete="off" /><a href="#" class="downarr"></a>
                    <span class="idinfo"></span></div>
                    <div class="options">
                        ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                        <a href="#" class="remove">V</a>
                    </div>
                    </li>
                </ul>
                <a href="#" class="btn"' . ( !empty( $opt['keep_key'] ) ? ' data-keeps-key' : '' ) . '>' . ( !empty( $opt['add_button_title'] ) ? esc_html( $opt['add_button_title'] ) : t( 'add_field', 'Add field' ) ) . '</a>
                </div></div>';
            }
            break;

            case 'coupons':
            if( empty( $opt['multi'] ) ) {
                $coupon_markup = '';
                if( isset( $value[$id] ) && \query\main::item_exists( $value[$id] ) ) {
                    $coupon_info = \query\main::item_info( $value[$id], array( 'no_emoticons' => true, 'no_filters' => true ) );
                    $coupon_markup = $coupon_info->title . ' (ID: ' . $coupon_info->ID . ')';
                }
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div data-search="coupon" data-set-class="wcoupon"><input type="text" name="' . $post_name . $extra_id . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? esc_html( $value[$id] ) : '' ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' autocomplete="off" /><a href="#" class="downarr"></a>
                                <span class="idinfo">' . $coupon_markup . '</span>
                            </div></div>';
            } else {
                $markup .= '<div class="row fields' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div>
                <ul class="fields_table full_fields' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? ' sortable' : '' ) . '">';
                if( !empty( $value[$id] ) && is_array( $value[$id] ) ) {
                    $key = '';
                    foreach( $value[$id] as $k => $val ) {
                        if( !empty( $opt['keep_key'] ) ) {
                            $key = $k;
                        }
                        $markup .= '<li class="added_field">';
                        $coupon_markup = '';
                        if( ( $exists = \query\main::item_exists( $val ) ) ) {
                            $coupon_info = \query\main::item_info( $val, array( 'no_emoticons' => true, 'no_filters' => true ) );
                            $coupon_markup = $coupon_info->title . ' (ID: ' . $coupon_info->ID . ')';
                        }
                        $markup .= '<div data-search="coupon" data-set-class="wcoupon"><input type="text" name="' . $post_name . $extra_id . '[' . $id . '][' . $key . ']" value="' . ( !empty( $exists ) ? esc_html( $val ) : '' ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' autocomplete="off" /><a href="#" class="downarr"></a>
                                        <span class="idinfo">' . $coupon_markup . '</span>
                                    </div>
                        <div class="options">
                            ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                            <a href="#" class="remove">V</a>
                        </div>
                        </li>';
                    }
                }
                $markup .= '<li class="fields_table_new" style="display:none;">
                    <div data-search="coupon" data-set-class="wcoupon"><input type="text" name="' . $post_name . $extra_id . '[' . $id . '][]" value=""' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' autocomplete="off" /><a href="#" class="downarr"></a>
                    <span class="idinfo"></span></div>
                    <div class="options">
                        ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                        <a href="#" class="remove">V</a>
                    </div>
                    </li>
                </ul>
                <a href="#" class="btn"' . ( !empty( $opt['keep_key'] ) ? ' data-keeps-key' : '' ) . '>' . ( !empty( $opt['add_button_title'] ) ? esc_html( $opt['add_button_title'] ) : t( 'add_field', 'Add field' ) ) . '</a>
                </div></div>';
            }
            break;

            case 'products':
            if( empty( $opt['multi'] ) ) {
                $product_markup = '';
                if( isset( $value[$id] ) && \query\main::product_exists( $value[$id] ) ) {
                    $product_info = \query\main::product_info( $value[$id], array( 'no_emoticons' => true, 'no_filters' => true ) );
                    $product_markup = $product_info->title . ' (ID: ' . $product_info->ID . ')';
                }
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div data-search="product" data-set-class="wproduct"><input type="text" name="' . $post_name . $extra_id . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? esc_html( $value[$id] ) : '' ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' autocomplete="off" /><a href="#" class="downarr"></a>
                                <span class="idinfo">' . $product_markup . '</span>
                            </div></div>';
            } else {
                $markup .= '<div class="row fields' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div>
                <ul class="fields_table full_fields' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? ' sortable' : '' ) . '">';
                if( !empty( $value[$id] ) && is_array( $value[$id] ) ) {
                    $key = '';
                    foreach( $value[$id] as $k => $val ) {
                        if( !empty( $opt['keep_key'] ) ) {
                            $key = $k;
                        }
                        $markup .= '<li class="added_field">';
                        $product_markup = '';
                        if( ( $exists = \query\main::product_exists( $val ) ) ) {
                            $product_info = \query\main::product_info( $val, array( 'no_emoticons' => true, 'no_filters' => true ) );
                            $product_markup = $product_info->title . ' (ID: ' . $product_info->ID . ')';
                        }
                        $markup .= '<div data-search="product" data-set-class="wproduct"><input type="text" name="' . $post_name . $extra_id . '[' . $id . '][' . $key . ']" value="' . ( !empty( $exists ) ? esc_html( $val ) : '' ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' autocomplete="off" /><a href="#" class="downarr"></a>
                                        <span class="idinfo">' . $product_markup . '</span>
                                    </div>
                        <div class="options">
                            ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                            <a href="#" class="remove">V</a>
                        </div>
                        </li>';
                    }
                }
                $markup .= '<li class="fields_table_new" style="display:none;">
                    <div data-search="product" data-set-class="wproduct"><input type="text" name="' . $post_name . $extra_id . '[' . $id . '][]" value=""' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' autocomplete="off" /><a href="#" class="downarr"></a>
                    <span class="idinfo"></span></div>
                    <div class="options">
                        ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                        <a href="#" class="remove">V</a>
                    </div>
                    </li>
                </ul>
                <a href="#" class="btn"' . ( !empty( $opt['keep_key'] ) ? ' data-keeps-key' : '' ) . '>' . ( !empty( $opt['add_button_title'] ) ? esc_html( $opt['add_button_title'] ) : t( 'add_field', 'Add field' ) ) . '</a>
                </div></div>';
            }
            break;

            case 'users':
            if( empty( $opt['multi'] ) ) {
                $user_markup = '';
                if( isset( $value[$id] ) && \query\main::user_exists( $value[$id] ) ) {
                    $user_info = \query\main::user_info( $value[$id] );
                    $user_markup = $user_info->name . ' (ID: ' . $user_info->ID . ')';
                }
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div data-search="user" data-set-class="wuser"><input type="text" name="' . $post_name . $extra_id . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? esc_html( $value[$id] ) : '' ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' autocomplete="off" /><a href="#" class="downarr"></a>
                                <span class="idinfo">' . $user_markup . '</span>
                            </div></div>';
            } else {
                $markup .= '<div class="row fields' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div>
                <ul class="fields_table full_fields' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? ' sortable' : '' ) . '">';
                if( !empty( $value[$id] ) && is_array( $value[$id] ) ) {
                    $key = '';
                    foreach( $value[$id] as $k => $val ) {
                        if( !empty( $opt['keep_key'] ) ) {
                            $key = $k;
                        }
                        $markup .= '<li class="added_field">';
                        $user_markup = '';
                        if( ( $exists = \query\main::user_exists( $val ) ) ) {
                            $user_info = \query\main::user_info( $val );
                            $user_markup = $user_info->name . ' (ID: ' . $user_info->ID . ')';
                        }
                        $markup .= '<div data-search="user" data-set-class="wuser"><input type="text" name="' . $post_name . $extra_id . '[' . $id . '][' . $key . ']" value="' . ( !empty( $exists ) ? esc_html( $val ) : '' ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' autocomplete="off" /><a href="#" class="downarr"></a>
                                        <span class="idinfo">' . $user_markup . '</span>
                                    </div>
                        <div class="options">
                            ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                            <a href="#" class="remove">V</a>
                        </div>
                        </li>';
                    }
                }
                $markup .= '<li class="fields_table_new" style="display:none;">
                    <div data-search="user" data-set-class="wuser"><input type="text" name="' . $post_name . $extra_id . '[' . $id . '][]" value=""' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' autocomplete="off" /><a href="#" class="downarr"></a>
                    <span class="idinfo"></span></div>
                    <div class="options">
                        ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                        <a href="#" class="remove">V</a>
                    </div>
                    </li>
                </ul>
                <a href="#" class="btn"' . ( !empty( $opt['keep_key'] ) ? ' data-keeps-key' : '' ) . '>' . ( !empty( $opt['add_button_title'] ) ? esc_html( $opt['add_button_title'] ) : t( 'add_field', 'Add field' ) ) . '</a>
                </div></div>';
            }
            break;

            case 'categories':
            if( empty( $opt['multi'] ) ) {
                $category_markup = '';
                if( isset( $value[$id] ) && \query\main::category_exists( $value[$id] ) ) {
                    $category_info = \query\main::category_info( $value[$id] );
                    $category_markup = $category_info->name . ' (ID: ' . $category_info->ID . ')';
                }
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div data-search="category" data-set-class="wcategory"><input type="text" name="' . $post_name . $extra_id . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? esc_html( $value[$id] ) : '' ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' autocomplete="off" /><a href="#" class="downarr"></a>
                                <span class="idinfo">' . $category_markup . '</span>
                            </div></div>';
            } else {
                $markup .= '<div class="row fields' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div>
                <ul class="fields_table full_fields' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? ' sortable' : '' ) . '">';
                if( !empty( $value[$id] ) && is_array( $value[$id] ) ) {
                    $key = '';
                    foreach( $value[$id] as $k => $val ) {
                        if( !empty( $opt['keep_key'] ) ) {
                            $key = $k;
                        }
                        $markup .= '<li class="added_field">';
                        $category_markup = '';
                        if( ( $exists = \query\main::category_exists( $val ) ) ) {
                            $category_info = \query\main::category_info( $val );
                            $category_markup = $category_info->name . ' (ID: ' . $category_info->ID . ')';
                        }
                        $markup .= '<div data-search="category" data-set-class="wcategory"><input type="text" name="' . $post_name . $extra_id . '[' . $id . '][' . $key . ']" value="' . ( !empty( $exists ) ? esc_html( $val ) : '' ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' autocomplete="off" /><a href="#" class="downarr"></a>
                                        <span class="idinfo">' . $category_markup . '</span>
                                    </div>
                        <div class="options">
                            ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                            <a href="#" class="remove">V</a>
                        </div>
                        </li>';
                    }
                }
                $markup .= '<li class="fields_table_new" style="display:none;">
                    <div data-search="category" data-set-class="wcategory"><input type="text" name="' . $post_name . $extra_id . '[' . $id . '][]" value=""' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' autocomplete="off" /><a href="#" class="downarr"></a>
                    <span class="idinfo"></span></div>
                    <div class="options">
                        ' . ( isset( $opt['sortable'] ) && $opt['sortable'] ? '<a href="#" class="move">v</a>' : '' ) . '
                        <a href="#" class="remove">V</a>
                    </div>
                    </li>
                </ul>
                <a href="#" class="btn"' . ( !empty( $opt['keep_key'] ) ? ' data-keeps-key' : '' ) . '>' . ( !empty( $opt['add_button_title'] ) ? esc_html( $opt['add_button_title'] ) : t( 'add_field', 'Add field' ) ) . '</a>
                </div></div>';
            }
            break;

            case 'image':
            case 'images':
                if( $opt['type'] == 'images' ) {
                    $opt['multi'] = true;
                }
                $markup .= '<div class="row select-image' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div>';
                $vitems = array();
                if( !empty( $value[$id] ) && $value[$id] != '{}' ) {
                    $items = @json_decode( $value[$id] );
                    if( $items ) {
                        $markup .= '<ul class="images-list clearfix">';
                        foreach( $items as $pic_id => $image ) {
                            if( \query\gallery::exists( $pic_id ) ) {
                                $vitems[$pic_id] = $image;
                                $markup .= '<li><img src="' . ( preg_match( '/^http(s)?/i', $image ) ? $image :  '../' . $image ) . '" alt="" /></li>';
                            }
                        }
                        $markup .= '</ul>';
                    }
                }
                $markup .= ( !empty( $opt['multi'] ) ? '<a href="#" data-multi class="btn"' . ( !empty( $opt['cat_id'] ) ? ' data-category="' . $opt['cat_id'] . '"' : '' ) . '>' . t( 'gallery_button_select_imgs', 'Select images' ) . '</a>' : '<a href="#" class="btn"' . ( !empty( $opt['cat_id'] ) ? ' data-category="' . $opt['cat_id'] . '"' : '' ) . '>' . t( 'gallery_button_select_img', 'Select image' ) . '</a>' );
                $markup .= '<input type="hidden" name="' . $post_name . $extra_id . '[' . $id . ']" value="' . ( !empty( $vitems ) ? esc_html( json_encode( $vitems ) ) : '' ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' />';
                $markup .= '</div></div>';
            break;

            case 'colorpicker':
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div><input name="' . $post_name . $extra_id . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? esc_html( $value[$id] ) : ( isset( $opt['default'] ) ? esc_html( $opt['default'] ) : '' ) ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="text" class="colorpicker" autocomplete="off"></div></div>';
            break;

            case 'datepicker':
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div><input name="' . $post_name . $extra_id . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? esc_html( $value[$id] ) : ( isset( $opt['default'] ) ? esc_html( $opt['default'] ) : '' ) ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="text" class="datepicker" autocomplete="off"></div></div>';
            break;

            case 'hourpicker':
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div><input name="' . $post_name . $extra_id . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? esc_html( $value[$id] ) : ( isset( $opt['default'] ) ? esc_html( $opt['default'] ) : '' ) ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="text" class="hourpicker" autocomplete="off"></div></div>';
            break;

            case 'timepicker':
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div><input name="' . $post_name . $extra_id . '[' . $id . ']" value="' . ( isset( $value[$id] ) ? esc_html( $value[$id] ) : ( isset( $opt['default'] ) ? esc_html( $opt['default'] ) : '' ) ) . '"' . ( !empty( $opt['atts'] ) ? \site\utils::build_atts( $opt['atts'] ) : '' ) . ' type="text" class="timepicker" autocomplete="off"></div></div>';
            break;

            case 'callback':
                if( isset( $opt['callback'] ) && is_callable( $opt['callback'] ) )
                $markup .= '<div class="row' . ( !empty( $class ) ? ' ' . implode( $class ) : '' ) . '"' . ( !empty( $opt['row_atts'] ) ? \site\utils::build_atts( $opt['row_atts'] ) : '' ) . '><span>' . esc_html( $opt['title'] ) . ( !empty( $opt['info'] ) ? ' <span class="info"><span>' . esc_html( $opt['info'] ) . '</span></span>' : '' ) . ':</span><div>' . call_user_func( $opt['callback'], $item, $value, $post_name ) . '</div></div>';
            break;

        }
    }

    return array( 'markup' => $markup, 'sections' => $sections );

}

public static function get_page_tabs( $fields ) {
    if( !empty( $fields['sections'] ) ) {
        echo '<ul class="page-options-menu">';
        echo '<li class="active"><a href="#">' . t( 'general_label', 'General' ) . '</a></li>';
        foreach( $fields['sections'] as $section_id => $section_name ) {
            echo '<li><a href="#" data-section="' . $section_id . '">' . $section_name . '</a></li>';
        }
        echo '</ul>';
    }
}

}