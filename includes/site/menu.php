<?php

namespace site;

/** */

class menu {

    function __construct( $menu_id = '' ) {
        $this->id   = $menu_id;
    }

    public function links() {
        global $add_menu;

        if( ( $custom_menu = \query\main::get_option( 'links_menu_' . $this->id ) ) ) {
            return value_with_filter( 'menu-links', @unserialize( $custom_menu ) );
        }

        if( !isset( $add_menu[$this->id] ) ) return false;
        
        if( is_callable( $add_menu[$this->id] ) ) {
            return value_with_filter( 'menu-links', call_user_func( $add_menu[$this->id] ) );
        }

        return false;
    }

    public function build_links( $links = false, &$l = array() ) {
        $links = $links ? $links : $this->links();
        if( !$links ) {
            return array();
        }

        $l = array();
        $i = 0;
        foreach( $links as $link ) {
            $l[$i] = $this->prepare_link( $link );
            if( isset( $link['links'] ) ) {
                $this->build_links( $link['links'], $l[$i]['subnav'] );
                unset( $l[$i]['links'] );
            }
            $i++;
        }
        return $l;
    }

    private function prepare_link( &$link ) {
        $item['selected']   = false;
        $item['dropdown']   = isset( $link['links'] ) && is_array( $link['links'] ) ? true : false;
        $item['classes']    = array();

        if( !empty( $link['class'] ) ) {
            $item['classes'][] = $link['class'];
        }

        if( !isset( $link['identifier'] ) ) {
            $link['identifier'] = '';
        }

        $item['url'] = ( isset( $link['url'] ) ? esc_html( $link['url'] ) : '#' );

        $type = isset( $link['type'] ) ? $link['type'] : 'custom';

        switch( $type ) {
            case 'home':
            if( this_is_home_page() ) {
                $item['selected']   = true;
                $item['classes'][]  = 'active';
            }
            $item['url'] = tlink( 'index' );
            break;

            case 'store':
            if( this_is_store( $link['identifier'] ) ) {
                $item['selected']   = true;
                $item['classes'][]  = 'active';
            }
            break;

            case 'stores':
            if( this_is_stores_page() ) {
                $item['selected']   = true;
                $item['classes'][]  = 'active';
            }
            $item['url'] = tlink( 'stores' );
            break;

            case 'coupon':
            if( this_is_coupon( $link['identifier'] ) ) {
                $item['selected']   = true;
                $item['classes'][]  = 'active';
            }
            break;

            case 'product':
            if( this_is_product( $link['identifier'] ) ) {
                $item['selected']   = true;
                $item['classes'][]  = 'active';
            }
            break;

            case 'search':
            if( this_is_search_page() ) {
                $item['selected']   = true;
                $item['classes'][]  = 'active';
            }
            break;

            case 'user_section':
            if( $link['identifier'][0] != '/' ) {
                $link['identifier'] = '/' . $link['identifier'];
            }
            if( this_is_user_section( $link['identifier'] ) ) {
                $item['selected']   = true;
                $item['classes'][]  = 'active';
            }
            $item['url'] = tlink( 'user' . $link['identifier']  );
            break;

            case 'category':
            if( this_is_category( $link['identifier'] ) ) {
                $item['selected']   = true;
                $item['classes'][]  = 'active';
            }
            break;

            case 'template_page':
            if( this_is_template_page( $link['identifier'] ) ) {
                $item['selected'] = true;
                $item['classes'][] = 'active';
            }
            $item['url'] = tlink( 'tpage/' . $link['identifier'] );
            break;

            case 'categories':
                foreach( \query\main::group_categories( array( 'max' => 0, 'orderby' => 'name' ) ) as $cat_id => $cat ) {
                    $link['links']['category_' . $cat_id] = array( 'type' => 'category', 'name' => $cat['info']->name, 'url' => $cat['info']->link, 'identifier' => ( !empty( $cat['info']->url_title ) ? $cat['info']->url_title : $cat['info']->ID ) );
                    if( isset( $cat['subcats'] ) ) {
                        foreach( $cat['subcats'] as $subcat_id => $subcat ) {
                            $link['links']['category_' . $cat_id]['links']['category_' . $subcat_id] = array( 'type' => 'category', 'name' => $subcat->name, 'url' => $subcat->link, 'identifier' => ( !empty( $subcat->url_title ) ? $subcat->url_title : $subcat->ID ) );
                        }
                    }
                }
                $item['dropdown'] = true;
            break;

            case 'custom':
            if( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] == substr( strrchr( $item['url'], '://' ), 3 ) ) {
                $item['selected'] = true;
                $item['classes'][] = 'active';
            }
            break;
        }

        return array_merge( $link, $item );
    }

}