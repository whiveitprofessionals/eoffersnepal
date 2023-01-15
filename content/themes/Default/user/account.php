<?php $me = me(); ?>

<div class="container pt50 pb50">

<?php if( !$me->is_confirmed ) { ?>

<div class="row">

    <div class="col-md-12">
        <div class="alert">
            <?php echo sprintf( t( 'theme_not_confirmed_msg', "Your account is not verified yet ! Please check your inbox (%s) and verify it as fast as possible." ), $me->Email ); ?>
        </div>
    </div>

</div>

<?php } ?>

<div class="row">

    <div class="col-md-12">
        <div class="user-header">
            <img src="<?php echo user_avatar( $me->Avatar ); ?>" alt="" />
            <div class="user-header-right">
                <h3><?php echo $me->Name; ?></h3>
                <h5><?php echo '<i class="fa fa-gift"></i> ' . t( 'theme_points', 'Points:' ) . ' ' . $me->Points; ?></h5>
                <h5><?php echo '<i class="fa fa-money"></i> ' . t( 'theme_credits', 'Credits:' ) . ' ' . $me->Credits; ?></h5>
                <a href="<?php echo get_update( array( 'action' => 'purchase' ), tlink( 'user/account' ) ); ?>"><?php te( 'theme_add_credits', 'Add Credits' ); ?></a>
            </div>
        </div>
    </div>

</div>

<?php

/* USER NAVIGATION LINKS */

$user_nav = array();

$user_nav['favorites']['parent'] = '<i class="fa fa-heart"></i> ' . t( 'theme_admin_nav_favorites', 'Favorites' );
$user_nav['favorites']['fav-stores'] = '<i class="fa fa-book"></i> ' . t( 'theme_admin_nav_stores', 'Stores' );
$user_nav['favorites']['fav-coupons'] = '<i class="fa fa-ticket"></i> ' . t( 'theme_admin_nav_coupons', 'Coupons' );
if( couponscms_has_products() ) {
    $user_nav['favorites']['fav-products'] = '<i class="fa fa-cart-arrow-down"></i> ' . t( 'theme_admin_nav_products', 'Products' );
}

$user_nav['saved']['parent'] = '<i class="fa fa-star"></i> ' . t( 'theme_admin_nav_saved', 'Saved' );
$user_nav['saved']['saved-stores'] = '<i class="fa fa-book"></i> ' . t( 'theme_admin_nav_stores', 'Stores' );
$user_nav['saved']['saved-coupons'] = '<i class="fa fa-ticket"></i> ' . t( 'theme_admin_nav_coupons', 'Coupons' );
if( couponscms_has_products() ) {
    $user_nav['saved']['saved-products'] = '<i class="fa fa-cart-arrow-down"></i> ' . t( 'theme_admin_nav_products', 'Products' );
}

$user_nav['my-claims'] = '<i class="fa fa-barcode"></i> ' . t( 'theme_admin_nav_my_claims', 'Claimed Coupons' );

if( theme_has_rewards() ) {
    $user_nav['rewards']['parent'] = '<i class="fa fa-gift"></i> ' . t( 'theme_admin_nav_rewards', 'Rewards' );
    $user_nav['rewards']['rewards'] = '<i class="fa fa-gift"></i> ' . t( 'theme_admin_nav_rewards', 'View Rewards' );
    $user_nav['rewards']['reward-reqs'] = '<i class="fa fa-paper-plane-o"></i> ' . t( 'theme_admin_nav_reward_requests', 'Reward Reqests' );
}

if( is_store_owner() ) {
    $user_nav['my-stores']['parent'] = '<i class="fa fa-book"></i> ' . t( 'theme_admin_nav_my_stores', 'My Stores' );
    $user_nav['my-stores']['add-store'] = '<i class="fa fa-plus"></i> ' . t( 'theme_admin_nav_add_store', 'Add' );
    $user_nav['my-stores']['my-stores'] = '<i class="fa fa-bars"></i> ' . t( 'theme_admin_nav_view_stores', 'View' );

    $user_nav['my-coupons']['parent'] = '<i class="fa fa-ticket"></i> ' . t( 'theme_admin_nav_my_coupons', 'My Coupons' );
    $user_nav['my-coupons']['add-coupon'] = '<i class="fa fa-plus"></i> ' . t( 'theme_admin_nav_add_coupon', 'Add' );
    $user_nav['my-coupons']['my-coupons'] = '<i class="fa fa-bars"></i> ' . t( 'theme_admin_nav_view_coupons', 'View' );
    $user_nav['my-coupons']['check'] = '<i class="fa fa-check"></i> ' . t( 'theme_admin_nav_check_coupons', 'Check Codes' );

    if( couponscms_has_products() ) {
        $user_nav['my-products']['parent'] = '<i class="fa fa-cart-arrow-down"></i> ' . t( 'theme_admin_nav_my_products', 'My Products' );
        $user_nav['my-products']['add-product'] = '<i class="fa fa-plus"></i> ' . t( 'theme_admin_nav_add_product', 'Add' );
        $user_nav['my-products']['my-products'] = '<i class="fa fa-bars"></i> ' . t( 'theme_admin_nav_view_products', 'View' );
    }
} else {
    $user_nav['add-store'] = '<i class="fa fa-book"></i> ' . t( 'theme_admin_nav_add_your_store', 'Add Your Store' );
}

$user_nav['edit-profile'] = '<i class="fa fa-edit"></i> ' . t( 'theme_admin_nav_edit_profile', 'Edit Profile' );

$user_nav['change-password'] = '<i class="fa fa-edit"></i> ' . t( 'theme_admin_nav_change_password', 'Change Password' );

$user_nav['refer-friend'] = '<i class="fa fa-users"></i> ' . t( 'theme_admin_nav_refer_friend', 'Refer a Friend' );

$user_nav[tlink( 'user/logout' )] = '<i class="fa fa-close"></i> ' . t( 'theme_admin_nav_logout', 'Logout' );

/* MENU VALUE */

$user_nav_value = isset( $_GET['action'] ) ? $_GET['action'] : 'edit-profile';

?>

<div class="row push-container">

    <div class="col-md-3 push-right mb20-m">
        <?php echo do_action( 'user_before_nav' ); ?>
        <ul class="user-menu">
            <?php foreach( value_with_filter( 'user_nav', $user_nav ) as $parent_id => $parent ) {
                    if( is_array( $parent ) ) {
                        echo '<li class="user-sub-menu' . ( $parent_id == $user_nav_value || in_array( $user_nav_value, array_keys( $parent ) ) ? ' active' : '' ) . '">';
                        echo '<a href="' . ( filter_var( $parent_id, FILTER_VALIDATE_URL ) ? $parent_id : '#' ) . '">' . $parent['parent'] . ' <i class="fa fa-caret-down"></i></a>';
                        unset( $parent['parent'] );
                        echo '<ul>';
                        foreach( $parent as $child_id => $child ) {
                            echo '<li' . ( $child_id == $user_nav_value ? ' class="active"' : '' ) . '><a href="' . ( filter_var( $child_id, FILTER_VALIDATE_URL ) ? $child_id : get_update( array( 'action' => $child_id ), tlink( 'user/account' ) ) ) . '">' . $child . '</a></li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<li' . ( $parent_id == $user_nav_value ? ' class="active"' : '' ) . '>';
                        echo '<a href="' . ( filter_var( $parent_id, FILTER_VALIDATE_URL ) ? $parent_id : get_update( array( 'action' => $parent_id ), tlink( 'user/account' ) ) ) . '">' . $parent . '</a>';
                    }
                echo '</li>';
            } ?>
        </ul>
        <?php echo do_action( 'user_after_nav' ); ?>
    </div>

    <div class="col-md-9">
        <?php echo do_action( 'user_account_before' );

        $action = isset( $_GET['action'] ) ? $_GET['action'] : 'general';

        if( ( $custom_content = value_with_filter( 'user_nav_content_' . $action, false ) ) ) {
            echo $custom_content;
        } else {

        switch( $action ) {

            case 'fav-stores':
                if( ( $pagination = have_favorites() ) && $pagination['results'] > 0 ) {
                    foreach( favorites( array( 'orderby' => 'date desc', 'page' => $pagination['page'] ) ) as $item ) {
                        echo couponscms_store_item( $item );
                    }
                    echo couponscms_theme_pagination( $pagination );
                } else echo '<div class="alert">' . t( 'theme_no_favorite_stores',  "You don't have favorite stores yet!" ) . '</div>';
            break;

            case 'fav-coupons':
                if( ( $pagination = have_favorite_items() ) && $pagination['results'] > 0 ) {
                    foreach( favorite_items( array( 'orderby' => 'date desc', 'page' => $pagination['page'] ) ) as $item ) {
                        echo couponscms_coupon_item( $item );
                    }
                    echo couponscms_theme_pagination( $pagination );
                } else echo '<div class="alert">' . t( 'theme_no_favorite_coupons',  "No coupons from your favorite stores!" ) . '</div>';
            break;

            case 'fav-products':
                if( ( $pagination = have_favorite_products() ) && $pagination['results'] > 0 ) {
                    foreach( favorite_products( array( 'orderby' => 'date asc', 'page' => $pagination['page'] ) ) as $item ) {
                        echo couponscms_product_item( $item );
                    }
                    echo couponscms_theme_pagination( $pagination );
                } else echo '<div class="alert">' . t( 'theme_no_favorite_products',  "No products from your favorite stores!" ) . '</div>';
            break;

            case 'saved-stores':
                if( ( $pagination = have_saved_stores() ) && $pagination['results'] > 0 ) {
                    foreach( saved_stores( array( 'orderby' => 'added_date desc', 'page' => $pagination['page'] ) ) as $item ) {
                        echo couponscms_store_item( $item );
                    }
                    echo couponscms_theme_pagination( $pagination );
                } else echo '<div class="alert">' . t( 'theme_no_saved_stores',  "You don't have stores saved!" ) . '</div>';
            break;

            case 'saved-coupons':
                if( ( $pagination = have_saved_items() ) && $pagination['results'] > 0 ) {
                    foreach( saved_items( array( 'orderby' => 'added_date desc', 'page' => $pagination['page'] ) ) as $item ) {
                        echo couponscms_coupon_item( $item );
                    }
                    echo couponscms_theme_pagination( $pagination );
                } else echo '<div class="alert">' . t( 'theme_no_saved_coupons',  "You don't have coupons saved!" ) . '</div>';
            break;

            case 'saved-products':
                if( ( $pagination = have_saved_products() ) && $pagination['results'] > 0 ) {
                    foreach( saved_products( array( 'orderby' => 'added_date desc', 'page' => $pagination['page'] ) ) as $item ) {
                        echo couponscms_product_item( $item );
                    }
                    echo couponscms_theme_pagination( $pagination );
                } else echo '<div class="alert">' . t( 'theme_no_saved_products',  "You don't have products saved!" ) . '</div>';
            break;

            case 'rewards':
                if( ( $pagination = have_rewards() ) && $pagination['results'] > 0 ) {
                    foreach( rewards( array( 'orderby' => 'name desc', 'page' => $pagination['page'] ) ) as $item ) {
                        echo couponscms_reward_item( $item );
                    }
                    echo couponscms_theme_pagination( $pagination );
                } else echo '<div class="alert">' . t( 'theme_no_rewards',  'No rewards at this time!' ) . '</div>';
            break;

            case 'reward-reqs':
                if( ( $pagination = have_reward_reqs() ) && $pagination['results'] > 0 ) {
                    foreach( reward_reqs( array( 'orderby' => 'date desc', 'page' => $pagination['page'] ) ) as $item ) {
                        echo couponscms_reward_request_item( $item );
                    }
                    echo couponscms_theme_pagination( $pagination );
                } else echo '<div class="alert">' . t( 'theme_no_reward_reqs',  "You don't have any reward request yet!" ) . '</div>';
            break;

            case 'my-stores':
                if( ( $pagination = have_stores( array( 'show' => 'all' ) ) ) && $pagination['results'] > 0 ) {
                    foreach( stores( array( 'orderby' => 'date desc', 'page' => $pagination['page'], 'show' => 'all' ) ) as $item ) {
                        echo couponscms_store_item( $item, true );
                    }
                    echo couponscms_theme_pagination( $pagination );
                } else echo '<div class="alert">' . t( 'theme_owner_no_stores',  "You don't have stores added yet!" ) . '</div>';
            break;

            case 'add-store':
                if( ( $store_price = store_price() ) > my_credits() ) echo '<div class="msg-warning">' . sprintf( t( 'theme_no_credits_add_store', "You need %s more credits to add a store. Please add more credits." ),  ( $store_price - my_credits() ) ) . '</div>';
                else {
                    if( $store_price ) echo '<div class="alert">' . sprintf( t( 'theme_credits_to_add_store', 'You will be charged with %s credits for adding a new store.' ), $store_price ) . '</div>';
                    echo submit_store_form();
                }
            break;

            case 'edit-store':
                if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) echo '<div class="alert">' . t( 'theme_no_credits_edt_store', 'You will not be charged for editing this store.' ) . '</div>';
                echo edit_store_form( ( $storeID = ( isset( $_GET['id'] ) ? $_GET['id'] : 0 ) ), array( 'LOCATION_ADD_LINK' => '?action=add-store-location&id=' . $storeID, 'LOCATION_EDIT_LINK' => '?action=edit-store-location&id=%ID%', 'LOCATION_DELETE_LINK' => '?action=delete_store_location&id=%ID%' ) );
            break;

            case 'add-store-location':
                echo submit_store_location_form( ( isset( $_GET['id'] ) ? $_GET['id'] : 0 ) );
            break;

            case 'edit-store-location':
                echo edit_store_location_form( ( isset( $_GET['id'] ) ? $_GET['id'] : 0 ) );
            break;

            case 'my-coupons':
                if( ( $pagination = have_coupons( array( 'show' => 'all' ) ) ) && $pagination['results'] > 0 ) {
                    foreach( coupons( array( 'orderby' => 'date desc', 'page' => $pagination['page'], 'show' => 'all' ) ) as $item ) {
                        echo couponscms_coupon_item( $item, true );
                    }
                    echo couponscms_theme_pagination( $pagination );
                } else echo '<div class="alert">' . t( 'theme_owner_no_coupons',  "You don't have coupons added yet!" ) . '</div>';
            break;

            case 'add-coupon':
                if( ( $coupon_price = coupon_price() ) > my_credits() ) echo '<div class="msg-warning">' . sprintf( t( 'theme_no_credits_add_coupon', "You need %s more credits to add a coupon. Please add more credits." ),  ( $coupon_price - my_credits() ) ) . '</div>';
                else {
                    if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) echo '<div class="msg-warning">' . sprintf( t( 'theme_credits_to_add_coupon', 'You will be charged with %s credits for adding a new coupon. Also, you should know that you will be charged with %s credits for every %s days when this coupon is active. Example: if the expiration date for this coupon will be after %s days (3 x %s days) you will be charged with %s credits (3 x %s credits)' ), $coupon_price, $coupon_price, ( $coupon_days = coupon_price_days() ), ( $coupon_days * 3 ), $coupon_days, ( $coupon_price * 3 ), $coupon_price ) . '</div>';
                    echo submit_coupon_form();
                }
            break;

            case 'edit-coupon':
                if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) echo '<div class="alert">' . sprintf( t( 'theme_credits_to_edit_coupon', 'You will be charged with %s credits for every %s days when this coupon is active. Example: if the expiration date for this coupon will be after %s days (3 x %s days) you will be charged with %s credits (3 x %s credits)' ), ( $coupon_price = coupon_price() ), $coupon_price, ( $coupon_days = coupon_price_days() ), ( $coupon_days * 3 ), $coupon_days, ( $coupon_price * 3 ), $coupon_price ) . '</div>';
                echo edit_coupon_form( ( isset( $_GET['id'] ) ? $_GET['id'] : 0 ) );
            break;

            case 'coupon-claims':
            if( isset( $_GET['id'] ) && \query\main::item_exists( $_GET['id'] ) )  {
                $coupon = \query\main::item_info( $_GET['id'] );
                if( \query\main::have_store( $coupon->storeID, $me->ID ) ) {
                    if( ( $pagination = \query\claims::have_claims( array( 'coupon' => $coupon->ID ) ) ) && $pagination['results'] > 0 ) {
                        foreach( \query\claims::fetch_claims( array( 'coupon' => $coupon->ID, 'orderby' => 'date desc', 'page' => $pagination['page'], 'show' => 'all' ) ) as $item ) {
                            echo couponscms_claims_item( $item );
                        }
                        echo couponscms_theme_pagination( $pagination );
                    } else echo '<div class="alert">' . t( 'theme_owner_no_coupons',  "You don't have coupons added yet!" ) . '</div>';
                } else echo '<div class="alert">' . t( 'edit_cou_cant', "You don't own this coupon." ) . '</div>';
            } else echo '<div class="alert">' . t( 'edit_cou_cant', "You don't own this coupon." ) . '</div>';
            break;

            case 'check':
                echo check_coupon_code();
            break;

            case 'my-products':
                if( ( $pagination = have_products( array( 'show' => 'all' ) ) ) && $pagination['results'] > 0 ) {
                    foreach( products( array( 'orderby' => 'date desc', 'page' => $pagination['page'], 'show' => 'all' ) ) as $item ) {
                        echo couponscms_product_item( $item, true );
                    }
                    echo couponscms_theme_pagination( $pagination );
                } else echo '<div class="alert">' . t( 'theme_owner_no_products',  "You don't have products added yet!" ) . '</div>';
            break;

            case 'add-product':
                if( ( $product_price = product_price() ) > my_credits() ) echo '<div class="msg-warning">' . sprintf( t( 'theme_no_credits_add_product', "You need %s more credits to add a product. Please add more credits." ),  ( $product_price - my_credits() ) ) . '</div>';
                else {
                    if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) echo '<div class="alert">' . sprintf( t( 'theme_credits_to_add_product', 'You will be charged with %s credits for adding a new product. Also, you should know that you will be charged with %s credits for every %s days when this product is active. Example: if the expiration date for this product will be after %s days (3 x %s days) you will be charged with %s credits (3 x %s credits)' ), $product_price, $product_price, ( $product_days = product_price_days() ), ( $product_days * 3 ), $product_days, ( $product_price * 3 ), $product_price ) . '</div>';
                    echo submit_product_form();
                }
            break;

            case 'edit-product':
                if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) echo '<div class="alert">' . sprintf( t( 'theme_credits_to_edit_product', 'You will be charged with %s credits for every %s days when this product is active. Example: if the expiration date for this product will be after %s days (3 x %s days) you will be charged with %s credits (3 x %s credits)' ), ( $product_price = product_price() ), $product_price, ( $product_days = product_price_days() ), ( $product_days * 3 ), $product_days, ( $product_price * 3 ), $product_price ) . '</div>';
                echo edit_product_form( ( isset( $_GET['id'] ) ? $_GET['id'] : 0 ) );
            break;

            case 'my-claims':
                if( ( $pagination = have_claimed_items( array( 'show' => 'all' ) ) ) && $pagination['results'] > 0 ) {
                    foreach( claimed_items( array( 'orderby' => 'date desc', 'page' => $pagination['page'], 'show' => 'all' ) ) as $item ) {
                        echo couponscms_coupon_item( $item );
                    }
                    echo couponscms_theme_pagination( $pagination );
                } else echo '<div class="alert">' . t( 'theme_owner_no_claimed_coupons',  "You don't have coupons claimed yet!" ) . '</div>';
            break;

            case 'change-password':
                echo change_password_form();
            break;

            case 'refer-friend':
                echo '<div class="text-center">
                <h2>' . t( 'theme_referer_share_text', 'Share the following link everywhere:' ) . '</h2>
                <input type="text" value="' . site_url() . '?ref=' . $me->ID . '" class="share-link" onClick="$(this).select()" />
                </div>';
            break;

            case 'purchase':
                if( ( $pagination = have_payment_plans() ) && $pagination['results'] > 0 ) {
                    foreach( payment_plans( array( 'orderby' => 'date desc', 'page' => $pagination['page'] ) ) as $item ) {
                        echo couponscms_plans_item( $item );
                    }
                    echo couponscms_theme_pagination( $pagination );
                } else echo '<div class="alert">' . t( 'theme_no_plans',  'No plans at this moment. Please check again later.' ) . '</div>';
            break;

            default:
                add( 'filter', 'top_profile_link_classes', function( $classes ) {
                    return $classes . ' button-active';
                });
                echo edit_profile_form();
            break;
        }

        }

        echo do_action( 'user_account_after' ); ?>
    </div>

</div>

</div>