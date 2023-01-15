<?php

/* THEME USE PRODUCTS */
function couponscms_has_products() {
    return true;
}

/* THEME USE LOCAL STORES */
function couponscms_has_local_stores() {
    return true;
}

/* STARS RATING */
function couponscms_rating( $stars = 0, $votes = 0 ) {
    if( empty( $stars ) ) {
        return false;
    }
    return '<div class="rating-star"' . ( !empty( $votes ) ? ' data-tooltip title="' . sprintf( t( 'theme_rating_store', '%s stars rating from %s votes' ), $stars, $votes ) . '"' : '' ) . '>' .
    str_repeat( '<i class="fa fa-star"></i>', $stars ) .
    ( $stars < 5 ? str_repeat( '<i class="fa fa-star-o"></i>', ( 5 - $stars ) ) : '' ) . ( !empty( $votes ) ? ' (' . $votes . ')' : '' ) .
    '</div>';
}


/* DATE FORMAT */
function couponscms_dateformat( $date = '', $convert_to_unix = true ) {
    if( $convert_to_unix ) {
        $date = strtotime( $date );
    }

    $format = 'd.m.Y';

    if( ( $to_format = get_theme_option( 'date_format' ) ) && !empty( $to_format ) ) {
        $format = $to_format;
    }

    return date( $format, $date );
}

/* DISCOUNT IN PERCENTS */
function couponscms_discount( $old_price, $sale ) {
    if( empty( $old_price ) || empty( $sale ) ) {
        return false;
    }
    return (int) ( 100 - ( $sale / $old_price ) * 100 );
}

/* SHARE LINKS */
function couponscms_share_links( $link = '' ) {
    return '<ul class="share-links">
        <li><a href="https://www.facebook.com/sharer/sharer.php?u=' . $link . '"><i class="fa fa-facebook"></i></a></li>
        <li><a href="https://twitter.com/intent/tweet?text=' . $link . '"><i class="fa fa-twitter"></i></a></li>
    </ul>';
}

/* THEME LANGUAGES */
function couponscms_site_languages() {
    if( (boolean) option( 'allow_select_lang' ) && ( get_theme_option( 'site_multilang' ) ) ) {
        $markup = '';
        $markup .= '<ul class="col-6 text-right inline-ul-list site-languages">';
        foreach( site_languages() as $id => $lang ) {
            $markup .= '<li data-tooltip title="' . esc_html( $lang['name'] ) . '" data-placement="bottom"><a href="' . get_update( array( 'set_language' => $id) ) . '"><img src="' . esc_html( $lang['image'] ) . '" alt="" /></a></li>';
        }
        $markup .= '</ul>';

        return $markup;
    }
    return false;
}

/* VIEW CODE FOR A STORE */
function couponscms_view_store_coupons( $store_id = 0 ) {
    if( isset( $_SESSION['couponscms_rc'] ) && in_array( $store_id, $_SESSION['couponscms_rc'] ) ) {
        return true;
    }
    return false;
}

/* SEARCH FORM MARKUP */
function couponscms_search_form( $extra_class = '' ) {
    echo '<div class="search-container' . ( !empty( $extra_class ) ? ' ' . $extra_class : '' ) . '">
        <div class="container">
            <div class="row sc-title">
                <div class="col-md-12 text-center">';
                    $search_title = get_theme_option( 'search_title' );
                    echo '<h2>' . ( !empty( $search_title ) ? esc_html( $search_title ) : t( 'theme_search_title', 'Search for coupons, products or stores' ) ) . '</h2>
                </div>
            </div>
            <div class="sc-form">
                <form action="' . site_url() . '" method="GET" class="row">
                    <div class="col-md-6">
                        <div class="search-input">
                            <i class="fa fa-search"></i>
                            <input type="text" name="s" placeholder="' . t( 'theme_type_and_search', 'Type and press enter' ) . '" />
                        </div>
                    </div>
                    <div class="col-md-3 sc-select">
                        <div class="category-select">
                        <a href="#"><span>' . t( 'coupons', 'Coupons' ) . '</span> <i class="fa fa-angle-down"></i></a>
                        <input type="hidden" name="type" value="coupons" />
                            <ul>
                                <li><a href="#" data-attr="coupons">' . t( 'coupons', 'Coupons' ) . '</a></li>';
                                if( couponscms_has_products() ) {
                                    echo '<li><a href="#" data-attr="products">' . t( 'products', 'Products' ) . '</a></li>';
                                }
                                echo '<li><a href="#" data-attr="stores">' . t( 'stores', 'Stores' ) . '</a></li>';
                                if( couponscms_has_local_stores() ) {
                                    echo '<li><a href="#" data-attr="locations">' . t( 'theme_stores_by_location', 'Stores By Location' ) . '</a></li>';
                                }
                            echo '</ul>
                        </div>
                    </div>
                    <div class="col-md-3 sc-select">
                        <div class="category-select">
                        <a href="#"><span>' . t( 'theme_any_category', 'Any Category' ) . '</span> <i class="fa fa-angle-down"></i></a>
                        <input type="hidden" name="category" value="" />
                            <ul>';
                            foreach( all_grouped_categories() as $category ) {
                                echo '<li><a href="' . $category['info']->link . '" data-attr="' . $category['info']->ID . '">' . ts( $category['info']->name ) . '</a></li>';
                                if( isset( $category['subcats'] ) ) {
                                    foreach( $category['subcats'] as $subcategory ) {
                                        echo '<li><a href="' . $subcategory->link . '" data-attr="' . $subcategory->ID . '">- ' . ts( $subcategory->name ) . '</a></li>';
                                    }
                                }
                            }
                            echo '</ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>';
}

/* INDEX ITEMS */
function couponscms_home_items() {
    $items_on_home = value_with_filter( 'index_items', get_theme_option( 'items_on_index' ) );

    $markup = '';
    if( $items_on_home ) {
        if( is_array( $items_on_home ) ) {
            foreach( $items_on_home as $type ) {
                $opt = array_map( 'trim', explode( '|', $type ) );
                switch( $opt[0] ) {
                    case 'coupons':
                    foreach( items_custom( array( 'show' => ( isset( $opt[2] ) ? $opt[2] : 'all' ), 'orderby' => ( isset( $opt[3] ) ? $opt[3] : 'rand' ), 'max' => ( isset( $opt[1] ) ? (int) $opt[1] : 10 ) ) ) as $item ) {
                        $markup .= couponscms_coupon_item( $item );
                    }
                    break;
                    case 'products':
                    foreach( products_custom( array( 'show' => ( isset( $opt[2] ) ? $opt[2] : 'all' ), 'orderby' => ( isset( $opt[3] ) ? $opt[3] : 'rand' ), 'max' => ( isset( $opt[1] ) ? (int) $opt[1] : 10 ) ) ) as $item ) {
                        $markup .= couponscms_product_item( $item );
                    }
                    break;
                    case 'stores':
                    foreach( stores_custom( array( 'show' => ( isset( $opt[2] ) ? $opt[2] : 'all' ), 'orderby' => ( isset( $opt[3] ) ? $opt[3] : 'rand' ), 'max' => ( isset( $opt[1] ) ? (int) $opt[1] : 10 ) ) ) as $item ) {
                        $markup .= couponscms_store_item( $item );
                    }
                    break;
                }
            }
        }
    } else {
        foreach( items_custom( array( 'show' => 'all', 'orderby' => 'date', 'max' => 10 ) ) as $item ) {
            $markup .= couponscms_coupon_item( $item );
        }
    }
    return $markup;
}