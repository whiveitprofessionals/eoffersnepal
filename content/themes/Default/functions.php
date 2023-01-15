<?php

/* DEFINE CONSTANTS */
define( 'THEME_LOCATION', theme_location() );
define( 'COUPONSCMS_CORE_LOCATION', theme_location2() . '/core' );

/* REQUIRED PARTS AND FUNCTIONS */
require_once 'core/theme_options.php';
require_once 'core/widgets.php';
require_once 'core/shortcodes.php';
require_once 'core/functions.php';
require_once 'extend/store.php';
require_once 'extend/product.php';
require_once 'extend/coupon.php';
require_once 'extend/review.php';
require_once 'extend/reward.php';
require_once 'extend/plans.php';
require_once 'extend/pagination.php';
require_once 'extend/menu.php';

/* ADD THEME STYLES */
add( 'styles', THEME_LOCATION . '/assets/css/bootstrap.min.css',    array( 'media' => 'all', 'rel' => 'stylesheet' ) );
add( 'styles', THEME_LOCATION . '/assets/css/font-awesome.min.css', array( 'media' => 'all', 'rel' => 'stylesheet' ) );
add( 'styles', THEME_LOCATION . '/style.css',                       array( 'media' => 'all', 'rel' => 'stylesheet' ) );
add( 'styles', THEME_LOCATION . '/assets/css/couponscms.css',       array( 'media' => 'all', 'rel' => 'stylesheet' ) );
add( 'styles', THEME_LOCATION . '/assets/css/framework.css',        array( 'media' => 'all', 'rel' => 'stylesheet' ) );
add( 'styles', THEME_LOCATION . '/assets/css/owl.carousel.min.css', array( 'media' => 'all', 'rel' => 'stylesheet' ) );
add( 'styles', THEME_LOCATION . '/assets/css/responsive.css',       array( 'media' => 'all', 'rel' => 'stylesheet' ) );
add( 'styles', '//fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900', array( 'rel' => 'stylesheet' ) );

/* ADD THEME SCRIPTS */
add( 'scripts', 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js' );
add( 'scripts', '<script>window.jQuery || document.write(\'<script src="' . site_url( 'assets/js/jquery.3.6.0.min.js') . '">\x3C/script>\')</script>' );
add( 'scripts', THEME_LOCATION . '/assets/js/functions.js' );
add( 'scripts', THEME_LOCATION . '/assets/js/ajax.js' );
add( 'scripts', THEME_LOCATION . '/assets/js/bootstrap.min.js' );
add( 'scripts', THEME_LOCATION . '/assets/js/owl.carousel.min.js' );

/* USE OR DON'T USE REWARDS */
function theme_has_rewards() {
    return true;
}

/* LANGUAGES LOCATION */
function theme_languages_location() {
    return 'languages';
}

/* ADD THEME MENU */                  
add( 'menu', 'main', 'theme_menu' );

/* BUILD SITE'S MENU */
function theme_menu() {
    $links = array();

    $links['home'] = array( 'type' => 'home', 'name' => t( 'theme_nav_home', 'Home' ) );

    $links['categories'] = array( 'name' => t( 'theme_nav_categories', 'Categories' ), 'url' => '#' );

    foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat_id => $cat ) {
        $links['categories']['links']['category_' . $cat_id] = array( 'type' => 'category', 'name' => $cat['info']->name, 'url' => $cat['info']->link, 'identifier' => ( !empty( $cat['info']->url_title ) ? $cat['info']->url_title : $cat['info']->ID ) );
        if( isset( $cat['subcats'] ) ) {
            foreach( $cat['subcats'] as $subcat_id => $subcat ) {
                $links['categories']['links']['category_' . $cat_id]['links']['category_' . $subcat_id] = array( 'type' => 'category', 'name' => $subcat->name, 'url' => $subcat->link, 'identifier' => ( !empty( $subcat->url_title ) ? $subcat->url_title : $subcat->ID ) );
            }
        }
    }

    $links['stores'] = array( 'name' => t( 'theme_nav_stores', 'Stores' ), 'url' => tlink( 'stores' ) );
    $links['stores']['links'][] = array( 'name' => t( 'theme_all_stores', 'All Stores' ), 'url' => tlink( 'stores' ) );
    $links['stores']['links'][] = array( 'name' => t( 'theme_top_stores', 'Top Stores' ), 'url' => tlink( 'stores', 'type=top' ) );
    $links['stores']['links'][] = array( 'name' => t( 'theme_most_voted', 'Most Voted' ), 'url' => tlink( 'stores', 'type=most-voted' ) );
    $links['stores']['links'][] = array( 'name' => t( 'theme_popular', 'Popular' ), 'url' => tlink( 'stores', 'type=popular' ) );

    $links['coupons'] = array( 'name' => t( 'theme_nav_coupons', 'Coupons' ), 'url' => '#' );
    $links['coupons']['links'][] = array( 'name' => t( 'theme_coupons_recently_added', 'Recently Added' ), 'url' => tlink( 'tpage/coupons', 'type=recent' ) );
    $links['coupons']['links'][] = array( 'name' => t( 'theme_coupons_expiring_soon', 'Expiring Soon' ), 'url' => tlink( 'tpage/coupons', 'type=expiring' ) );
    $links['coupons']['links'][] = array( 'name' => t( 'theme_coupons_printable', 'Printable' ), 'url' => tlink( 'tpage/coupons', 'type=printable' ) );
    $links['coupons']['links'][] = array( 'name' => t( 'theme_coupons_codes', 'Coupon Codes' ), 'url' => tlink( 'tpage/coupons', 'type=codes' ) );
    $links['coupons']['links'][] = array( 'name' => t( 'theme_coupons_exclusive', 'Exclusive' ), 'url' => tlink( 'tpage/coupons', 'type=exclusive' ) );
    $links['coupons']['links'][] = array( 'name' => t( 'theme_coupons_popular', 'Popular' ), 'url' => tlink( 'tpage/coupons', 'type=popular' ) );
    $links['coupons']['links'][] = array( 'name' => t( 'theme_coupons_verified', 'Verified' ), 'url' => tlink( 'tpage/coupons', 'type=verified' ) );

    if( couponscms_has_products() ) {
        $links['products'] = array( 'name' => t( 'theme_nav_products', 'Products' ), 'url' => '#' );
        $links['products']['links'][] = array( 'name' => t( 'theme_products_recently_added', 'Recently Added' ), 'url' => tlink( 'tpage/products', 'type=recent' ) );
        $links['products']['links'][] = array( 'name' => t( 'theme_products_expiring_soon', 'Expiring Soon' ), 'url' => tlink( 'tpage/products', 'type=expiring' ) );
        $links['products']['links'][] = array( 'name' => t( 'theme_products_popular', 'Popular' ), 'url' => tlink( 'tpage/products', 'type=popular' ) );
    }

    $links[] = array( 'type' => 'search', 'name' => t( 'theme_nav_search', 'Search' ), 'url' => '#search' );

    return $links;
}

/* APPLY OPTIONS */
if( ( $search_box_bg = get_theme_option( 'search_image' ) ) && !empty( $search_box_bg ) ) {
    if( !filter_var( $search_box_bg, FILTER_VALIDATE_URL ) ) {
        $search_box_gallery = @json_decode( $search_box_bg ); 
        if( $search_box_gallery ) {
            $search_box_bg = current( $search_box_gallery );    
        }
    }
    add( 'inline-style', '.search-container:not(.fixed-popup)::after {background-image:url("' . esc_html( $search_box_bg ) . '")}' );
}

/* ADD EXTRA CSS */
add( 'in-head', add_extra_css() );

function add_extra_css() {
    if( ( $ecss = get_theme_option( 'extra_css' ) ) ) {
        return "<style>\n" . $ecss . "\n</style>";
    }
}

/* ADD EXTRA JS */
add( 'in-head', add_extra_js() );

function add_extra_js() {
    if( ( $ejs = get_theme_option( 'extra_js' ) ) ) {
        return "<script>" . $ejs . "\n</script>";
    }
}