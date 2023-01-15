<?php

namespace admin;

/** */

class main {

public function navigation() {

    global $add_admin_menu;

    $custom_menu = array();

    if( !empty( $add_admin_menu ) && is_array( $add_admin_menu ) ) {
        foreach( $add_admin_menu as $id => $link ) {
            if( isset( $id ) && !empty( $link['name'] ) ) {
                if( !isset( $link['perm'] ) || $GLOBALS['me']->is_admin || ( isset( $link['perm'] ) && (boolean) $link['perm'] ) ) {
                    $custom_menu[$id]['name'] = '<a href="' . ( !empty( $link['url'] ) ? esc_html( $link['url'] ) : '?route=link.php&amp;main=' . esc_html( $id ) ) . '"' . ( !empty( $link['target'] ) ? ' target="' . esc_html( $link['target'] ) . '"' : '' ) . '>' . ( !empty( $link['icon'] ) ? '<img src="' . esc_html( $link['icon'] ) . '" alt="" />' : '' ) . esc_html( $link['name'] ) . '</a>';
                    $custom_menu[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
                    if( isset( $link['class'] ) ) {
                        $custom_menu[$id]['class'] = esc_html( $link['class'] );
                    }
                    if( isset( $link['subnav'] ) && is_array( $link['subnav'] ) ) {
                        foreach( $link['subnav'] as $subnav_id => $subnav_link ) {
                            if( isset( $subnav_id ) && isset( $subnav_link['name'] ) ) {
                                if( !isset( $subnav_link['perm'] ) || $GLOBALS['me']->is_admin || (boolean) $subnav_link['perm'] )
                                $custom_menu[$id]['subnav'][$subnav_id] = '<a href="' . ( !empty( $subnav_link['url'] ) ? esc_html( $subnav_link['url'] ) : '?route=link.php&amp;main=' . esc_html( $id ) . '&amp;action=' . esc_html( $subnav_id ) ) . '"' . ( !empty( $subnav_link['target'] ) ? ' target="' . esc_html( $subnav_link['target'] ) . '"' : '' ) . '>' . esc_html( $subnav_link['name'] ) . '</a>';
                            }
                        }
                    }
                }
            }
        }
    }

    return self::default_links() + $custom_menu;

}

public function default_links() {

    $nav = array();

    $nav['dashboard']['name'] = '<a href="?route=dashboard.php">' . t( 'dashboard', "Dashboard" ) . '</a>';
    $nav['dashboard']['class'] = 'dashboard';
    $nav['dashboard']['position'] = 1;

    if( ( $ab = ab_to( array( 'stores' => array( 'view', 'add' ), 'categories' => 'view' ) ) ) && list( $stores_view, $stores_add, $categories_view ) = $ab ) {
        $nav['stores']['name'] = '<a href="?route=stores.php">' . t( 'stores', "Stores" ) . '</a>';
        $nav['stores']['class'] = 'stores';
        $nav['stores']['position'] = 2;
        $nav['stores']['other'] = array( 'categories' ); // php files without .php extension
        if( $categories_view ) {
            $nav['stores']['subnav']['categories'] = '<a href="?route=categories.php&amp;action=list">' . t( 'categories', "Categories" ) . '</a>';
        }
        if( $stores_add ) {
            $nav['stores']['subnav']['add'] = '<a href="?route=stores.php&amp;action=add">' . t( 'stores_add', "Add Store" ) . '</a>';
        }
        if( $stores_view ) {
            $nav['stores']['subnav']['list'] = '<a href="?route=stores.php&amp;action=list">' . t( 'stores_view', "View Stores" ) . '</a>';
        }
    }

    if( ( $ab = ab_to( array( 'coupons' => array( 'view', 'add' ) ) ) ) && list( $coupons_view, $coupons_add ) = $ab ) {
        $nav['coupons']['name'] = '<a href="?route=coupons.php">' . t( 'coupons', "Coupons" ) . '</a>';
        $nav['coupons']['class'] = 'coupons';
        $nav['coupons']['position'] = 3;
        if( $coupons_add ) {
            $nav['coupons']['subnav']['add'] = '<a href="?route=coupons.php&amp;action=add">' . t( 'coupons_add', "Add Coupon" ) . '</a>';
        }
        if( $coupons_view ) {
            $nav['coupons']['subnav']['list'] = '<a href="?route=coupons.php&amp;action=list">' . t( 'coupons_view', "View Coupons" ) . '</a>';
        }
    }

    if( ( $ab = ab_to( array( 'products' => array( 'view', 'add' ) ) ) ) && list( $products_view, $products_add ) = $ab ) {
        $nav['products']['name'] = '<a href="?route=products.php">' . t( 'products', "Products" ) . '</a>';
        $nav['products']['class'] = 'products';
        $nav['products']['position'] = 4;
        if( $products_add ) {
            $nav['products']['subnav']['add'] = '<a href="?route=products.php&amp;action=add">' . t( 'products_add', "Add Product" ) . '</a>';
        }
        if( $products_view ) {
            $nav['products']['subnav']['list'] = '<a href="?route=products.php&amp;action=list">' . t( 'products_view', "View Products" ) . '</a>';
        }
    }

    if( ( $ab = ab_to( array( 'locations' => array( 'view', 'add' ) ) ) ) && list( $locations_view, $locations_add ) = $ab ) {
        $nav['locations']['name'] = '<a href="?route=locations.php&amp;action=cities">' . t( 'locations', "Locations" ) . '</a>';
        $nav['locations']['class'] = 'locations';
        $nav['locations']['position'] = 5;
        if( ( $locations_add = ab_to( array( 'locations' => 'add' ) ) ) ) {
            $nav['locations']['subnav']['country_add'] = '<a href="?route=locations.php&amp;action=country_add">' . t( 'locations_country_add', "Add Country" ) . '</a>';
        }
        if( $locations_view ) {
            $nav['locations']['subnav']['countries'] = '<a href="?route=locations.php&amp;action=countries">' . t( 'locations_country_view', "View Countries" ) . '</a>';
        }
        if( $locations_add ) {
            $nav['locations']['subnav']['state_add'] = '<a href="?route=locations.php&amp;action=state_add">' . t( 'locations_state_add', "Add State" ) . '</a>';
        }
        if( $locations_view  ) {
            $nav['locations']['subnav']['states'] = '<a href="?route=locations.php&amp;action=states">' . t( 'locations_state_view', "View States" ) . '</a>';
        }
        if( $locations_add ) {
            $nav['locations']['subnav']['city_add'] = '<a href="?route=locations.php&amp;action=city_add">' . t( 'locations_city_add', "Add City" ) . '</a>';
        }
        if( $locations_view ) {
            $nav['locations']['subnav']['cities'] = '<a href="?route=locations.php&amp;action=cities">' . t( 'locations_city_view', "View Cities" ) . '</a>';
        }
    }

    if( ( $ab = ab_to( array( 'feed' => array( 'view', 'import' ) ) ) ) && list( $feed_view, $feed_import ) = $ab ) {
        $nav['feed']['name'] = '<a href="?route=feed.php">' . t( 'feed', "Feed" ) . '</a>';
        $nav['feed']['class'] = 'feed';
        $nav['feed']['position'] = 6;
        if( $feed_view ) {
            $nav['feed']['subnav']['list'] = '<a href="?route=feed.php&amp;action=list">' . t( 'stores', "Stores" ) . '</a>';
            $nav['feed']['subnav']['coupons'] = '<a href="?route=feed.php&amp;action=coupons">' . t( 'coupons', "Coupons" ) . '</a>';
            $nav['feed']['subnav']['products'] = '<a href="?route=feed.php&amp;action=products">' . t( 'products', "Products" ) . '</a>';
        }
        if( $feed_import ) {
            $nav['feed']['subnav']['import'] = '<a href="?route=feed.php&amp;action=import">' . t( 'feed_icoupons', "Check/Update" ) . '</a>';
        }
        if( $GLOBALS['me']->is_admin ) {
            $nav['feed']['subnav']['info'] = '<a href="?route=feed.php&amp;action=info">' . t( 'information', "Information" ) . '</a>';
        }
    }

    if( ( $ab = ab_to( array( 'pages' => array( 'view', 'add' ) ) ) ) && list( $pages_view, $pages_add ) = $ab ) {
        $nav['pages']['name'] = '<a href="?route=pages.php">' . t( 'pages', "Pages" ) . '</a>';
        $nav['pages']['class'] = 'pages';
        $nav['pages']['position'] = 7;
        if( $pages_add ) {
            $nav['pages']['subnav']['add'] = '<a href="?route=pages.php&amp;action=add">' . t( 'pages_add', "Add Page" ) . '</a>';
        }
        if( $pages_view ) {
            $nav['pages']['subnav']['list'] = '<a href="?route=pages.php&amp;action=list">' . t( 'pages_view', "View Pages" ) . '</a>';
        }
    }

    if( ( $ab = ab_to( array( 'users' => array( 'view', 'add' ), 'subscribers' => 'view' ) ) ) && list( $users_view, $users_add, $subscribers_view ) = $ab ) {
        $nav['users']['name'] = '<a href="?route=users.php">' . t( 'users', "Users" ) . '</a>';
        $nav['users']['class'] = 'users';
        $nav['users']['position'] = 8;
        if( $subscribers_view ) {
            $nav['users']['subnav']['subscribers'] = '<a href="?route=users.php&amp;action=subscribers">' . t( 'users_subscribers', "Subscribers" ) . '</a>';
        }
        if( $GLOBALS['me']->is_admin ) {
            $nav['users']['subnav']['sessions'] = '<a href="?route=users.php&amp;action=sessions">' . t( 'users_sessions', "Active Sessions" ) . '</a>';
        }
        if( $users_add ) {
            $nav['users']['subnav']['add'] = '<a href="?route=users.php&amp;action=add">' . t( 'users_add', "Add User" ) . '</a>';
        }
        if( $users_view ) {
            $nav['users']['subnav']['list'] = '<a href="?route=users.php&amp;action=list">' . t( 'users_view', "View Users" ) . '</a>';
        }
    }

    if( $payments_view = ab_to( array( 'payments' => 'view' ) ) ) {
        $nav['payments']['name'] = '<a href="?route=paymenbts.php">' . t( 'payments', "Payments" ) . '</a>';
        $nav['payments']['class'] = 'payments';
        $nav['payments']['position'] = 9;
        if( $GLOBALS['me']->is_admin ) {
            $nav['payments']['subnav']['plan_add'] = '<a href="?route=payments.php&amp;action=plan_add">' . t( 'payments_plan_add', "Add Plan" ) . '</a>';
            $nav['payments']['subnav']['plan_view'] = '<a href="?route=payments.php&amp;action=plan_view">' . t( 'payments_plan_view', "View Plans" ) . '</a>';
        }
        if( $payments_view ) {
            $nav['payments']['subnav']['list'] = '<a href="?route=payments.php&amp;action=list">' . t( 'payments_invoices', "Invoices" ) . '</a>';
        }
    }

    if( ( $ab = ab_to( array( 'reviews' => array( 'view', 'add' ) ) ) ) && list( $reviews_view, $reviews_add ) = $ab ) {
        $nav['reviews']['name'] = '<a href="?route=reviews.php">' . t( 'reviews', "Reviews" ) . '</a>';
        $nav['reviews']['class'] = 'reviews';
        $nav['reviews']['position'] = 10;
        if( $reviews_add ) {
            $nav['reviews']['subnav']['add'] = '<a href="?route=reviews.php&amp;action=add">' . t( 'reviews_add', "Add Review" ) . '</a>';
        }
        if( $reviews_view ) {
            $nav['reviews']['subnav']['list'] = '<a href="?route=reviews.php&amp;action=list">' . t( 'reviews_view', "View Reviews" ) . '</a>';
        }
    }

    if( ab_to( array( 'suggestions' => 'view' ) ) ) {
        $nav['suggestions']['name'] = '<a href="?route=suggestions.php">' . t( 'suggestions', "Suggestions" ) . '</a>';
        $nav['suggestions']['class'] = 'suggestions';
        $nav['suggestions']['position'] = 11;
    }

    if( ab_to( array( 'claim_reqs' => 'view' ) ) && template::have_rewards() ) {
        $nav['rewards']['name'] = '<a href="?route=rewards.php">' . t( 'rewards', "Rewards" ) . '</a>';
        $nav['rewards']['class'] = 'rewards';
        $nav['rewards']['position'] = 11;
        if( $GLOBALS['me']->is_admin ) {
            $nav['rewards']['subnav']['add'] = '<a href="?route=rewards.php&amp;action=add">' . t( 'rewards_add', "Add Reward" ) . '</a>';
            $nav['rewards']['subnav']['list'] = '<a href="?route=rewards.php&amp;action=list">' . t( 'rewards_view', "View Rewards" ) . '</a>';
        }
        $nav['rewards']['subnav']['requests'] = '<a href="?route=rewards.php&amp;action=requests">' . t( 'rewards_claimr', "Claim Requests" ) . '</a>';
    }

    if( $GLOBALS['me']->is_admin ) {
        $nav['themes']['name'] = '<a href="?route=themes.php">' . t( 'themes', "Themes" ) . '</a>';
        $nav['themes']['class'] = 'themes';
        $nav['themes']['position'] = 12;
        $nav['themes']['subnav']['upload'] = '<a href="?route=themes.php&amp;action=upload">' . t( 'themes_upload', "Upload Theme" ) . '</a>';
        $nav['themes']['subnav']['editor'] = '<a href="?route=themes.php&amp;action=editor&amp;id=' . ( $theme_id = \query\main::get_option( 'theme' ) ) . '">' . t( 'themes_editor', "Theme Editor" ) . '</a>';
        if( template::have_theme_options() ) {
            $nav['themes']['subnav']['options'] = '<a href="?route=themes.php&amp;action=options&amp;id=' . $theme_id . '">' . t( 'themes_options', 'Theme Options' ) . '</a>';
        }
        $nav['themes']['subnav']['menus'] = '<a href="?route=themes.php&amp;action=menus">' . t( 'themes_menus', "Menus" ) . '</a>';
        $nav['themes']['subnav']['list'] = '<a href="?route=themes.php&amp;action=list">' . t( 'themes_view', "View Themes" ) . '</a>';
    }

    if( $GLOBALS['me']->is_admin && template::have_widgets() ) {
        $nav['widgets']['name'] = '<a href="?route=widgets.php">' . t( 'widgets', "Widgets" ) . '</a>';
        $nav['widgets']['class'] = 'widgets';
        $nav['widgets']['position'] = 13;
    }

    if( ab_to( array( 'reports' => 'view' ) ) ) {
        $nav['clicks']['name'] = '<a href="?route=clicks.php">' . t( 'ratings', "Reports" ) . '</a>';
        $nav['clicks']['class'] = 'reports';
        $nav['clicks']['position'] = 14;
        $nav['clicks']['subnav']['list'] = '<a href="?route=clicks.php&amp;action=list">' . t( 'ratings_clicks', "Clicks" ) . '</a>';
    }

    if( $GLOBALS['me']->is_admin ) {

        $nav['plugins']['name'] = '<a href="?route=plugins.php">' . t( 'plugins', "Plugins" ) . '</a>';
        $nav['plugins']['class'] = 'plugins';
        $nav['plugins']['position'] = 15;
        $nav['plugins']['subnav']['install'] = '<a href="?route=plugins.php&amp;action=install">' . t( 'plugins_install', "Install" ) . '</a>';
        $nav['plugins']['subnav']['list'] = '<a href="?route=plugins.php&amp;action=list">' . t( 'plugins_view', "View Plugins" ) . '</a>';

    }

    foreach( \query\main::user_plugins( false, 'menu' ) as $plugin ) {
        if( ( $GLOBALS['me']->is_subadmin && $plugin->subadmin_view ) || $GLOBALS['me']->is_admin ) {
            $plugin_dir = dirname( $plugin->main_file );
            $nav[$plugin_dir]['name'] = '<a href="?plugin=' . $plugin->main_file . '">' . $plugin->name . '</a>';
            $nav[$plugin_dir]['class'] = 'plugin' . $plugin->menu_icon;
            $nav[$plugin_dir]['position'] = 16;
            if( isset( $plugin->vars['menu_add'] ) )
            foreach( $plugin->vars['menu_add'] as $subnavp ) {
                $nav[$plugin_dir]['subnav'][] = '<a href="?plugin=' . str_replace( '&', '&amp;', $subnavp['url'] ) . '">' . esc_html( $subnavp['title'] ) . '</a>';
            }
        }
    }

    if( $GLOBALS['me']->is_admin ) {

        $nav['settings']['name'] = '<a href="?route=settings.php">' . t( 'settings', "Settings" ) . '</a>';
        $nav['settings']['class'] = 'settings';
        $nav['settings']['position'] = 17;
        $nav['settings']['other'] = array( 'banned' );
        $nav['settings']['subnav']['general'] = '<a href="?route=settings.php&amp;action=general">' . t( 'settings_general', "General" ) . '</a>';
        $nav['settings']['subnav']['meta'] = '<a href="?route=settings.php&amp;action=meta">' . t( 'settings_metatags', "Meta Tags" ) . '</a>';
        $nav['settings']['subnav']['seolinks'] = '<a href="?route=settings.php&amp;action=seolinks">' . t( 'settings_seolinks', "SEO Links" ) . '</a>';
        $nav['settings']['subnav']['prices'] = '<a href="?route=settings.php&amp;action=prices">' . t( 'settings_prices', "Prices" ) . '</a>';
        $nav['settings']['subnav']['default'] = '<a href="?route=settings.php&amp;action=default">' . t( 'settings_default', "Default Info" ) . '</a>';
        $nav['settings']['subnav']['theme'] = '<a href="?route=settings.php&amp;action=theme">' . t( 'settings_theme', "Theme" ) . '</a>';
        $nav['settings']['subnav']['security'] = '<a href="?route=settings.php&amp;action=security">' . t( 'settings_security', "Security" ) . '</a>';
        $nav['settings']['subnav']['api'] = '<a href="?route=settings.php&amp;action=api">' . t( 'settings_api', "API / External Accounts" ) . '</a>';
        $nav['settings']['subnav']['feed'] = '<a href="?route=settings.php&amp;action=feed">' . t( 'settings_feed', "Feed" ) . '</a>';
        $nav['settings']['subnav']['cron'] = '<a href="?route=settings.php&amp;action=cron">' . t( 'settings_cron', "Cron" ) . '</a>';
        $nav['settings']['subnav']['banned'] = '<a href="?route=banned.php&amp;action=list">' . t( 'settings_banned', "Banned IPs" ) . '</a>';
        $nav['settings']['subnav']['socialacc'] = '<a href="?route=settings.php&amp;action=socialacc">' . t( 'settings_socialnet', "Social Networks" ) . '</a>';

    }

    return value_with_filter( 'admin-menu', $nav );

}

public function get_nav_item( $main = '', $action = '' ) {

    global $add_admin_menu;

    if( !empty( $action ) ) {
        if( isset( $add_admin_menu[$main]['subnav'][$action] ) ) {
            return $add_admin_menu[$main]['subnav'][$action];
        }
    } else {
        if( isset( $add_admin_menu[$main] ) ) {
            return $add_admin_menu[$main];
        }
    }
    return false;

}

public function coupon_fields( $info = object, $csrf = '' ) {

    do_action( 'before_coupon_fields', $info );

    global $add_coupon_fields;

    $custom_fields  = array();
    $sections       = array();

    if( !empty( $add_coupon_fields ) && is_array( $add_coupon_fields) ) {
        foreach( $add_coupon_fields as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $extra = widgets::build_extra( $link['fields'], ( isset( $info->extra ) && is_array( $info->extra ) ? $info->extra : array() ), '', 'extra', $info );
            $custom_fields[$id]['markup'] = $extra['markup'];
            $sections = array_merge( $sections, $extra['sections'] );
        }
    }

    return array( 'fields' => self::default_coupon_fields( $info, $csrf ) + $custom_fields, 'sections' => $sections );

}

public function default_coupon_fields( $info = object, $csrf = '' ) {

    $fields = array();

    // Store
    if( isset( $info->storeID ) ) $storeID = $info->storeID;
    else {
        $storeID = ( isset( $_POST['store'] ) ? (int) $_POST['store'] : ( !empty( $_GET['store'] ) ? (int) $_GET['store'] : '' ) );
    }
    $store = '<div class="row autoset-cat"><span>' . t( 'form_store_id', "Store ID" ) . ':</span><div data-search="store"><input type="text" name="store" value="' . $storeID . '" required /><a href="#" class="downarr"></a>';
    if( empty( $storeID ) || !\query\main::store_exists( $storeID ) ) {
        $store .= '<span class="idinfo"></span>';
    } else {
        $store_info = \query\main::store_info( $storeID );
        // enable options for local stores
        if( !empty( $store_info->is_physical ) ) {
            $info->store_is_physical = true;
        }
        $store .= '<span class="idinfo">' . $store_info->name . ' (ID: ' . $store_info->ID . ')</span>';
    }
    $store .= '</div></div>';

    $fields['store']['position'] = 1;
    $fields['store']['markup'] = $store;

    // Category
    $category = '<div class="row"><span>' . t( 'form_category', "Category" ) . ':</span>
    <div><select name="category">';
    foreach( \query\main::group_categories( array( 'max' => 0 ), array( 'no_emoticons' => true, 'no_filters' => true ) ) as $cat ) {
        $category .= '<optgroup label="' . ts( $cat['info']->name ) . '">';
        $category .= '<option value="' . $cat['info']->ID . '"' . ( isset( $info->catID ) && $info->catID == $cat['info']->ID ? ' selected' : '' ) . '>' . ts( $cat['info']->name ) . '</option>';
        if( isset( $cat['subcats'] ) ) {
            foreach( $cat['subcats'] as $subcat ) {
                $category .= '<option value="' . $subcat->ID . '"' . ( isset( $info->catID ) && $info->catID == $subcat->ID ? ' selected' : '' ) . '>' . ts( $subcat->name ) . '</option>';
            }
        }
        $category .= '</optgroup>';
    }
    $category .= '</select></div></div>';

    $fields['category']['position'] = 2;
    $fields['category']['markup'] = $category;

    // Title
    $fields['title']['position'] = 3;
    $fields['title']['markup'] = '<div class="row"><span>' . t( 'form_name', "Name" ) . ':</span><div><input type="text" name="name" value="' . ( isset( $info->title ) ? $info->title : '' ) . '" maxlength="255" required /></div></div>';

    // Coupon type
    $type = '<div class="row coupon_type' . ( !isset( $info->store_is_physical ) || !$info->store_is_physical ? ' required-hidden" style="display:none;"' : '"' ) . '><span>' . t( 'form_type', "Type" ) . ':</span><div><select name="coupon_type" data-sell_online="' . ( isset( $info->store_sellonline ) && $info->store_sellonline ? 'true' : 'false' ) . '">';

    $coupon_type = 0;
    if( isset( $info->is_printable ) && $info->is_printable ) {
        if( empty( $info->source ) ) {
            $coupon_type = 1;
        } else {
            $coupon_type = 2;
        }
    } else if( !empty( $info->is_show_in_store ) ) {
        $coupon_type = 3;
    }

    foreach( array( 0 => t( 'dealnrprint', "Deal (not require printing)" ), 1 => t( 'printhtml', "Printable (HTML version)" ), 2 => t( 'printsource', "Printable (using source)" ), 3 => t( 'showinstore', "Show In Store" ) ) as $type_id => $type_name ) {
        $type .= '<option value="' . $type_id . '"' . ( $coupon_type === $type_id ? ' selected' : '' ) . '>' . $type_name . '</option>';
    }
    $type .= '</select></div></div>';

    $fields['type']['position'] = 4;
    $fields['type']['markup'] = $type;

    // Coupon source
    $source = '<div class="row' . ( $coupon_type !== 2 ? ' required-hidden" style="display:none;"' : '"' ) . ' data-required=\'' . json_encode( array( 'coupon_type' => 2 ) ) . '\'><span>' . t( 'form_source', "Source" ) . ':</span><div>';

    if( isset( $info->is_local_source ) && $info->is_local_source ) {

        $source .= '<div>
        <div style="display:table;margin-bottom:2px;"><img src="' . ( isset( $info->source ) ? $info->source : '' ) . '" class="avt" alt="" style="display:table-cell;vertical-align:middle;max-width:120px;height:80px;margin:0 20px 5px 0;" />
        <div style="display:table-cell;vertical-align:middle;margin-left:25px;">';
        if( !empty( $info->source ) ) $source .= '<a href="' . $info->source . '" target="_blank" class="btn">' . t( 'view', "View" ) . '</a> <a href="' . \site\utils::update_uri( '', array( 'type' => 'delete_source', 'token' => $csrf ) ) . '" class="btn" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';
        $source .= '</div>
        </div>
        </div>';

    }

    $source .= '<input type="file" name="coupon_source" value=""' . ( isset( $info->is_local_source ) && !$info->is_local_source && !empty( $info->source ) ? ' style="display:none;"' : '' ) . ' />
    <input type="text" name="coupon_source_url" placeholder="http://" value="' . ( isset( $info->is_local_source ) && !$info->is_local_source ? $info->source : '' ) . '"' . ( !isset( $info->is_local_source ) || ( $info->is_local_source || empty( $info->source ) ) ? ' style="display: none;"' : '' ) . ' />
    <input type="checkbox" name="coupon_online_source" id="switch-ft"' . ( isset( $info->is_local_source ) && !$info->is_local_source && !empty( $info->source ) ? ' checked' : '' ) . ' /> <label for="switch-ft"><span></span> ' . t( 'msg_useexternal', "Use an external source" ) . '</label>
    </div></div>';

    $fields['source']['position'] = 5;
    $fields['source']['markup'] = $source;

    // Store sell online !?
    $fields['sell_online']['position'] = 6;
    $fields['sell_online']['markup'] = '<div class="row coupon_avl_online' . ( !isset( $info->store_is_physical ) || !isset( $info->store_sellonline ) || ( $coupon_type !== 0 || !$info->store_is_physical || ( $info->store_is_physical && !$info->store_sellonline ) ) ? ' required-hidden" style="display:none;"' : '"' ) . ' data-required=\'' . json_encode( array( 'coupon_type' => 0 ) ) . '\' data-omit-required="' . ( isset( $info->store_is_physical ) && isset( $info->store_sellonline ) && ( $info->store_is_physical && !$info->store_sellonline ) ? 1 : 0 ) . '"><span>' . t( 'form_avabonline', "Available Online" ) . ':</span><div><input type="checkbox" name="coupon_use_online" id="cuo" value="1"' . ( isset( $info->store_sellonline ) && isset( $info->is_available_online ) && $info->store_sellonline && $info->is_available_online ? ' checked' : '' ) . ' /> <label for="cuo"><span></span> ' . t( 'coupons_avabonline', "This coupon is available online" ) . '</label></div></div>';

    // Limit / How many coupons can be used
    $fields['limit']['position'] = 7;
    $fields['limit']['markup'] = '<div class="row' . ( !isset( $info->ID ) || empty( $info->store_is_physical ) || $coupon_type !== 3 ? ' required-hidden" style="display:none;"' : '"' ) . ' data-required=\'' . json_encode( array( 'coupon_type' => 3 ) ) . '\'><span>' . t( 'form_limit', "Limit" ) . ' <span class="info"><span>' . t( 'msg_showinstore', "Maximum number of coupons that can be claimed for this coupon. 0 = unlimited." ) . '</span></span>:</span><div><input type="number" name="limit" value="' . ( isset( $info->claim_limit ) ? $info->claim_limit : 0 ) . '" /></div></div>';

    // Coupon code
    $fields['code']['position'] = 8;
    $fields['code']['markup'] = '<div class="row' . ( isset( $info->ID ) && ( !isset( $info->store_is_physical ) || !isset( $info->store_sellonline ) || !isset( $info->is_available_online ) || ( $coupon_type !== 0 || ( $info->store_is_physical && !$info->store_sellonline ) || !$info->is_available_online ) ) ? ' required-hidden" style="display:none;"' : '"' ) . ' data-required=\'' . json_encode( array( 'coupon_type' => 0, 'coupon_use_online' => 1 ) ) . '\' data-skip-required="' . ( ( !isset( $info->store_is_physical ) || !$info->store_is_physical ) && $coupon_type === 0 ? 1 : 0 ) . '"><span>' . t( 'form_code', "Code" ) . ':</span><div><input type="text" name="code" value="' . ( isset( $info->code ) ? $info->code : '' ) . '" /></div></div>';

    // Coupon url
    $fields['url']['position'] = 9;
    $fields['url']['markup'] = '<div class="row' . ( isset( $info->ID ) && ( !isset( $info->store_is_physical ) || !isset( $info->store_sellonline ) || !isset( $info->is_available_online ) || ( $coupon_type !== 0 || ( $info->store_is_physical && !$info->store_sellonline ) || !$info->is_available_online ) ) ? ' required-hidden" style="display:none;"' : '"' ) . ' data-required=\'' . json_encode( array( 'coupon_type' => 0 ) ) . '\' data-skip-required="' . ( ( !isset( $info->store_is_physical ) || !isset( $info->store_sellonline ) || !isset( $info->is_available_online ) || ( ( $info->store_is_physical && !$info->store_sellonline ) || !$info->store_is_physical ) ) && $coupon_type === 0 ? 1 : 0 ) . '" data-required-name="coupon_url"><span>' . t( 'form_coupon_url', "Coupon URL" ) . ':</span><div><input type="checkbox" name="coupon_ownlink" id="ownlink" value="1"' . ( empty( $info->original_url ) ? ' checked' : '' ) . ' /> <label for="ownlink"><span></span>' . t( 'coupons_use_link', "Use store address" ) . '</label> <br />
    <input type="text" name="link" value="' . ( !empty( $info->original_url ) ? $info->original_url : 'http://' ) . '"' . ( empty( $info->original_url ) ? ' style="display:none;"' : '' ) . ' />
    </div></div>';

    // Description
    $fields['description']['position'] = 10;
    $fields['description']['markup'] = '<div class="row"><span>' . t( 'form_description', "Description" ) . ':</span><div><textarea name="description">' . ( isset( $info->description ) ? $info->description : '' ). '</textarea></div></div>';

    // Tags
    $fields['tags']['position'] = 11;
    $fields['tags']['markup'] = '<div class="row"><span>' . t( 'form_tags', "Tags" ) . ':</span><div><input type="text" name="tags" value="' . ( isset( $info->tags ) ? $info->tags : '' ) . '" /></div></div>';

    // Coupon image
    $image = '<div class="row image-upload"><span>' . t( 'form_image', "Image" ) . ':</span>

    <div>';

    if( !empty( $info->image ) ) {

        $image .= '<div style="display:table;margin-bottom:2px;"><img src="' . ( $imgsrc = ( preg_match( '/^http(s)?/i', $info->image ) ? $info->image : '../' . $info->image ) ) . '" class="avt" alt="" style="display:table-cell;vertical-align:middle;max-width:100px;height:50px;margin:0 20px 5px 0;" />
        <div style="display: table-cell;vertical-align:middle;margin-left:25px;">
        <a href="' . $imgsrc . '" target="_blank" class="btn">' . t( 'view', "View" ) . '</a> <a href="' . \site\utils::update_uri( '', array( 'type' => 'delete_image', 'token' => $csrf ) ) . '" class="btn" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>
        </div>
        </div>';

    }

    $image .= '<input type="file" name="image" accept="image/*" />
    </div> </div>';

    $fields['image']['position'] = 12;
    $fields['image']['markup'] = $image;

    // Cashback
    $fields['cashback']['position'] = 13;
    $fields['cashback']['markup'] = '<div class="row more_field' . ( !isset( $info->cashback ) || $info->cashback === 0 ? ' required-hidden" style="display:none;"' : '"' ) . '><span>' . t( 'form_reward_points', "Reward Points" ) . ' <span class="info"><span>' . t( 'coupons_form_ireward_points', "Make sure that you can track the sales for this coupon." ) . '</span></span>:</span><div><input type="numer" name="reward_points" value="' . ( isset( $info->cashback ) ? $info->cashback : 0 ) . '" /></div></div>';

    // Verified
    $fields['verified']['position'] = 14;
    $fields['verified']['markup'] = '<div class="row more_field required-hidden" style="display:none;"><span>' . t( 'verified', "Verified" ) . ':</span><div><input type="checkbox" name="verified" id="row-verified"' . ( isset( $info->is_verified ) && $info->is_verified ? ' checked' : '' ) . ' /> <label for="row-verified"><span></span> ' . t( 'msg_setcouponverif', "Set this coupon as verified manually" ) . '</label></div></div>';

    // Last verification
    $fields['last_verification']['position'] = 15;
    $fields['last_verification']['markup'] = '<div class="row' . ( !isset( $info->is_verified ) || !$info->is_verified ? ' required-hidden" style="display:none;"' : '"' ) . '><span>' . t( 'last_verification', "Last Verification" ) . ':</span><div><input type="date" name="lverified[date]" value="' . ( isset( $info->is_verified ) && $info->is_verified ? date( 'Y-m-d', strtotime( $info->last_check ) ) : date( 'Y-m-d' ) ) . '" class="datepicker" style="display:inline-block;width:68%;margin-right:2%" /><input type="time" name="lverified[hour]" value="' . ( isset( $info->is_verified ) && $info->is_verified ? date( 'H:i', strtotime( $info->last_check ) ) : date( 'H:i' ) ) . '" class="hourpicker" style="display:inline-block;width:30%" /></div></div>';

    // Votes
    $fields['votes']['position'] = 16;
    $fields['votes']['markup'] = '<div class="row more_field required-hidden" style="display:none;"><span>' . t( 'votes', "Votes" ) . ':</span><div><input type="number" name="votes" value="' . ( isset( $info->votes ) ? $info->votes : 0 ) . '" /></div></div>';

    // Votes average
    $fields['votes_average']['position'] = 17;
    $fields['votes_average']['markup'] = '<div class="row more_field required-hidden" style="display:none;"><span>' . t( 'votes_average', "Votes Average" ) . ':</span><div><input type="text" name="votes_average" value="' . ( isset( $info->votes_percent ) ? $info->votes_percent : 0 ) . '" /></div></div>';

    // Start date
    $fields['start_date']['position'] = 18;
    $fields['start_date']['markup'] = '<div class="row"><span>' . t( 'form_start_date', "Start Date" ) . ':</span><div><input type="date" name="start[date]" value="' . ( isset( $info->start_date ) ? date( 'Y-m-d', strtotime( $info->start_date ) ) : '' ) . '" class="datepicker"    style="display:inline-block;width:68%;margin-right:2%" /><input type="time" name="start[hour]" value="' . ( isset( $info->start_date ) ? date( 'H:i', strtotime( $info->start_date ) ) : '00:00' ) . '" class="hourpicker" style="display:inline-block;width:30%" /></div></div>';

    // End date
    $fields['end_date']['position'] = 19;
    $fields['end_date']['markup'] = '<div class="row"><span>' . t( 'form_end_date', "End Date" ) . ':</span><div><input type="date" name="end[date]" value="' . ( isset( $info->expiration_date ) ? date( 'Y-m-d', strtotime( $info->expiration_date ) ) : '' ) . '" class="datepicker" style="display:inline-block;width:68%;margin-right:2%" /><input type="time" name="end[hour]" value="' . ( isset( $info->expiration_date ) ? date( 'H:i', strtotime( $info->expiration_date ) ) : '00:00' ) . '" class="hourpicker" style="display:inline-block;width:30%" /></div></div>';

    // Add to
    $fields['add_to']['position'] = 20;
    $fields['add_to']['markup'] = '<div class="row"><span>' . t( 'form_addto', "Add to" ) . ':</span><div>
    <input type="checkbox" name="popular" id="popular"' . ( isset( $info->is_popular ) && $info->is_popular ? ' checked' : '' ) . ' /> <label for="popular"><span></span> ' . t( 'coupons_addpopular', "Popular" ) . '</label> <br />
    <input type="checkbox" name="exclusive" id="exclusive"' . ( isset( $info->is_exclusive  ) && $info->is_exclusive ? ' checked' : '' ) . ' /> <label for="exclusive"><span></span> ' . t( 'coupons_addexclusive', "Exclusive" ) . '</label></div></div>';

    // Publish
    $fields['publish']['position'] = 21;
    $fields['publish']['markup'] = '<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish"' . ( !isset( $info->visible ) || $info->visible ? ' checked' : '' ) . ' /> <label for="publish"><span></span> ' . t( 'msg_pubcoupon', "Publish this coupon" ) . '</label></div></div>';

    return value_with_filter( 'default_coupon_fields', $fields );

}

public function product_fields( $info = object, $csrf = '' ) {

    do_action( 'before_product_fields', $info );

    global $add_product_fields;

    $custom_fields  = array();
    $sections       = array();

    if( !empty( $add_product_fields ) && is_array( $add_product_fields) ) {
        foreach( $add_product_fields as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $extra = widgets::build_extra( $link['fields'], ( isset( $info->extra ) && is_array( $info->extra ) ? $info->extra : array() ), '', 'extra', $info );
            $custom_fields[$id]['markup'] = $extra['markup'];
            $sections = array_merge( $sections, $extra['sections'] );
        }
    }

    return array( 'fields' => self::default_product_fields( $info, $csrf ) + $custom_fields, 'sections' => $sections );

}

public function default_product_fields( $info = object, $csrf = '' ) {

    $fields = array();

    // Store
    if( isset( $info->storeID ) ) $storeID = $info->storeID;
    else {
        $storeID = ( isset( $_POST['store'] ) ? (int) $_POST['store'] : ( !empty( $_GET['store'] ) ? (int) $_GET['store'] : '' ) );
    }
    $store = '<div class="row autoset-cat"><span>' . t( 'form_store_id', "Store ID" ) . ':</span><div data-search="store"><input type="text" name="store" value="' . $storeID . '" required /><a href="#" class="downarr"></a>';
    if( empty( $storeID ) || !\query\main::store_exists( $storeID ) ) {
        $store .= '<span class="idinfo"></span>';
    } else {
        $store_info = \query\main::store_info( $storeID );
        $store .= '<span class="idinfo">' . $store_info->name . ' (ID: ' . $store_info->ID . ')</span>';
    }
    $store .= '</div></div>';

    $fields['store']['position'] = 1;
    $fields['store']['markup'] = $store;

    // Category
    $category = '<div class="row"><span>' . t( 'form_category', "Category" ) . ':</span>
    <div><select name="category">';
    foreach( \query\main::group_categories( array( 'max' => 0 ), array( 'no_emoticons' => true, 'no_filters' => true ) ) as $cat ) {
        $category .= '<optgroup label="' . ts( $cat['info']->name ) . '">';
        $category .= '<option value="' . $cat['info']->ID . '"' . ( isset( $info->catID ) && $info->catID == $cat['info']->ID ? ' selected' : '' ) . '>' . ts( $cat['info']->name ) . '</option>';
        if( isset( $cat['subcats'] ) ) {
            foreach( $cat['subcats'] as $subcat ) {
                $category .= '<option value="' . $subcat->ID . '"' . ( isset( $info->catID ) && $info->catID == $subcat->ID ? ' selected' : '' ) . '>' . ts( $subcat->name ) . '</option>';
            }
        }
        $category .= '</optgroup>';
    }
    $category .= '</select></div></div>';

    $fields['category']['position'] = 2;
    $fields['category']['markup'] = $category;

    // Name
    $fields['name']['position'] = 3;
    $fields['name']['markup'] = '<div class="row"><span>' . t( 'form_name', "Name" ) . ':</span><div><input type="text" name="name" value="' . ( isset( $info->title ) ? $info->title : '' ). '" maxlength="255" required /></div></div>';

    // Price
    $fields['price']['position'] = 4;
    $fields['price']['markup'] = '<div class="row"><span>' . t( 'form_price', "Price" ) . ':</span><div><input type="text" name="price" value="' . ( !empty( $info->price ) ? \site\utils::money_format( $info->price ) : '' ) . '" placeholder="' . \site\utils::money_format( 0.00 ) . '" /></div></div>';

    // Old price
    $fields['old_price']['position'] = 5;
    $fields['old_price']['markup'] = '<div class="row"><span>' . t( 'form_old_price', "Old Price" ) . ':</span><div><input type="text" name="old_price" value="' . ( !empty( $info->old_price ) ? \site\utils::money_format( $info->old_price ) : '' ) . '" placeholder="' . \site\utils::money_format( 0.00 ) . '" /></div></div>';

    // Currency
    $fields['currency']['position'] = 6;
    $fields['currency']['markup'] = '<div class="row"><span>' . t( 'form_currency', "Currency" ) . ':</span><div><input type="text" name="currency" value="' . ( isset( $info->currency ) ? $info->currency : '' ) . '" /></div></div>';

    // Link
    $fields['link']['position'] = 7;
    $fields['link']['markup'] = '<div class="row product_link' . ( isset( $info->ID ) && empty( $info->store_sellonline ) ? ' required-hidden" style="display:none;"' : '"' ) . '><span>' . t( 'form_product_url', "Product URL" ) . ':</span><div><input type="checkbox" name="product_ownlink" value="1" id="ownlink"' . ( empty( $info->original_url ) ? ' checked' : '' ) . ' /> <label for="ownlink"><span></span> ' . t( 'products_use_link', "Use store address" ) . '</label> <br />
    <input type="text" name="link" value="' . ( !empty( $info->original_url ) ? $info->original_url : 'http://' ) . '"' . ( empty( $info->original_url ) ? ' style="display:none;"' : '' ) . ' />
    </div></div>';

    // Description
    $fields['description']['position'] = 8;
    $fields['description']['markup'] = '<div class="row"><span>' . t( 'form_description', "Description" ) . ':</span><div><textarea name="description">' . ( isset( $info->description ) ? $info->description : '' ) . '</textarea></div></div>';

    // Tags
    $fields['tags']['position'] = 9;
    $fields['tags']['markup'] = '<div class="row"><span>' . t( 'form_tags', "Tags" ) . ':</span><div><input type="text" name="tags" value="' . ( isset( $info->tags ) ? $info->tags : '' ) . '" /></div></div>';

    // Image
    $image = '<div class="row image-upload"><span>' . t( 'form_image', "Image" ) . ':</span>

    <div>';

    if( !empty( $info->image ) ) {

        $image .= '<div style="display:table;margin-bottom:2px;"><img src="' . ( $imgsrc = \query\main::product_avatar( $info->image ) ) . '" class="avt" alt="" style="display:table-cell;vertical-align:middle;max-width:120px;height:80px;margin:0 20px 5px 0;" />
        <div style="display:table-cell;vertical-align:middle;margin-left:25px;">';
        if( !empty( $info->image ) ) $image .= '<a href="' . $imgsrc . '" target="_blank" class="btn">' . t( 'view', "View" ) . '</a> <a href="' . \site\utils::update_uri( '', array( 'type' => 'delete_image', 'token' => $csrf ) ) . '" class="btn" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';
        $image .= '</div>
        </div>';

    }

    $image .= '<input type="file" name="image" accept="image/*" />
    </div> </div>';

    $fields['image']['position'] = 10;
    $fields['image']['markup'] = $image;

    // Cashback
    $fields['cashback']['position'] = 11;
    $fields['cashback']['markup'] = '<div class="row more_field' . ( !isset( $info->cashback ) || $info->cashback === 0 ? ' required-hidden" style="display:none;"' : '"' ) . '><span>' . t( 'form_reward_points', "Reward Points" ) . ' <span class="info"><span>' . t( 'products_form_ireward_points', "Make sure that you can track the sales for this product." ) . '</span></span>:</span><div><input type="numer" name="reward_points" value="' . ( isset( $info->cashback ) ? $info->cashback : 0 ) . '" /></div></div>';

    // Start date
    $fields['start_date']['position'] = 12;
    $fields['start_date']['markup'] = '<div class="row"><span>' . t( 'form_start_date', "Start Date" ) . ':</span><div><input type="date" name="start[date]" value="' . ( isset( $info->start_date ) ? date( 'Y-m-d', strtotime( $info->start_date ) ) : '' ) . '" class="datepicker" style="display:inline-block;width:68%;margin-right:2%" /><input type="time" name="start[hour]" value="' . ( isset( $info->start_date ) ? date( 'H:i', strtotime( $info->start_date ) ) : '' ) . '" class="hourpicker" style="display:inline-block;width:30%" /></div></div>';

    // End date
    $fields['end_date']['position'] = 13;
    $fields['end_date']['markup'] = '<div class="row"><span>' . t( 'form_end_date', "End Date" ) . ':</span><div><input type="date" name="end[date]" value="' . ( isset( $info->expiration_date ) ? date( 'Y-m-d', strtotime( $info->expiration_date ) ) : '' ) . '" class="datepicker" style="display:inline-block;width:68%;margin-right:2%" /><input type="time" name="end[hour]" value="' . ( isset( $info->expiration_date ) ? date( 'H:i', strtotime( $info->expiration_date ) ) : '' ) . '" class="hourpicker" style="display:inline-block;width:30%" /></div></div>';

    // Add to
    $fields['add_to']['position'] = 14;
    $fields['add_to']['markup'] = '<div class="row"><span>' . t( 'form_addto', "Add to" ) . ':</span><div>
    <input type="checkbox" name="popular" id="popular"' . ( isset( $info->is_popular ) && $info->is_popular ? ' checked' : '' ) . ' /> <label for="popular"><span></span> ' . t( 'products_addpopular', "Popular" ) . '</label></div></div>';

    // Publish
    $fields['publish']['position'] = 15;
    $fields['publish']['markup'] = '<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish"' . ( !isset( $info->visible ) || $info->visible ? ' checked' : '' ) . ' /> <label for="publish"><span></span> ' . t( 'msg_pubproduct', "Publish this product" ) . '</label></div></div>';

    return value_with_filter( 'default_product_fields', $fields );

}

public function store_fields( $info = object, $csrf = '' ) {

    do_action( 'before_store_fields', $info );

    global $add_store_fields;

    $custom_fields  = array();
    $sections       = array();

    if( !empty( $add_store_fields ) && is_array( $add_store_fields) ) {
        foreach( $add_store_fields as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $extra = widgets::build_extra( $link['fields'], ( isset( $info->extra ) && is_array( $info->extra ) ? $info->extra : array() ), '', 'extra', $info );
            $custom_fields[$id]['markup'] = $extra['markup'];
            $sections = array_merge( $sections, $extra['sections'] );
        }
    }

    return array( 'fields' => self::default_store_fields( $info, $csrf ) + $custom_fields, 'sections' => $sections );

}

public function default_store_fields( $info = object, $csrf = '' ) {

    $fields = array();

    // User
    if( isset( $info->userID ) ) $userID = $info->userID;
    else {
        $userID = ( isset( $_POST['user'] ) ? (int) $_POST['user'] : ( !empty( $_GET['user'] ) ? (int) $_GET['user'] : $GLOBALS['me']->ID ) );
    }
    $user = '<div class="row"><span>' . t( 'form_user_id', "User ID" ) . ':</span><div data-search="user"><input type="text" name="user" value="' . $userID . '" required /><a href="#" class="downarr"></a>';
    if( empty( $userID ) || !\query\main::user_exists( $userID ) ) {
        $user .= '<span class="idinfo"></span>';
    } else {
        $user_info = \query\main::user_info( $userID );
        $user .= '<span class="idinfo">' . $user_info->name . ' (ID: ' . $user_info->ID . ')</span>';
    }
    $user .= '</div></div>';

    $fields['user']['position'] = 1;
    $fields['user']['markup'] = $user;

    // Category
    $category = '<div class="row"><span>' . t( 'form_category', "Category" ) . ':</span>
    <div><select name="category">';
    foreach( \query\main::group_categories( array( 'max' => 0 ), array( 'no_emoticons' => true, 'no_filters' => true ) ) as $cat ) {
        $category .= '<optgroup label="' . ts( $cat['info']->name ) . '">';
        $category .= '<option value="' . $cat['info']->ID . '"' . ( isset( $info->catID ) && $info->catID == $cat['info']->ID ? ' selected' : '' ) . '>' . ts( $cat['info']->name ) . '</option>';
        if( isset( $cat['subcats'] ) ) {
            foreach( $cat['subcats'] as $subcat ) {
                $category .= '<option value="' . $subcat->ID . '"' . ( isset( $info->catID ) && $info->catID == $subcat->ID ? ' selected' : '' ) . '>' . ts( $subcat->name ) . '</option>';
            }
        }
      $category .= '</optgroup>';
    }
    $category .= '</select></div></div>';

    $fields['category']['position'] = 2;
    $fields['category']['markup'] = $category;

    // Name
    $fields['name']['position'] = 3;
    $fields['name']['markup'] = '<div class="row"><span>' . t( 'form_name', "Name" ) . ':</span><div><input type="text" name="name" value="' . ( isset( $info->name ) ? $info->name : '' ) . '" required /></div></div>';

    // Type
    $type = '<div class="row"><span>' . t( 'form_type', "Type" ) . ':</span>
    <div><select name="store_type">';
    foreach( array( 0 => t( 'onlinestore', "Online Store" ), 1 => t( 'physicalstore', "Physical Store" ) ) as $type_id => $type_msg ) {
        $type .= '<option value="' . $type_id . '"' . ( $type_id == 1 && ( !isset( $info->is_physical ) || !$info->is_physical ) ? '' : ' selected' ) . '>' . $type_msg . '</option>';
    }
    $type .= '</select>';
    if( isset( $info->is_physical ) && $info->is_physical ) $type .= '<span class="a-error" style="display:none;margin: 5px 0 0 0;width:100%;box-sizing:border-box;">' . t( 'msg_strcaseonline', "<strong>Note:*</strong> Some information about this store as a physical store will be erased (e.g.: locations)." ) . '</span>';
    $type .= '</div></div>';

    $fields['type']['position'] = 4;
    $fields['type']['markup'] = $type;

    // Check if local
    if( isset( $info->ID ) ) {
        $physical = '<div class="row"' . ( !isset( $info->is_physical ) || !$info->is_physical ? ' style="display: none;"' : '' ) . ' data-required=\'' . json_encode( array( 'store_type' => 1 ) ) . '\' data-required-name="locations"><span>' . t( 'form_locations', "Locations" ) . ':</span>
        <div class="locations-info">';

        if( !isset( $info->is_physical ) || !$info->is_physical ) {
            $physical .= '<div class="a-message">' . t( 'msg_physicalfirst', "Save it as a physical store before being able to add locations." ) . '</div>';
        } else {
            if( \query\locations::store_locations( array( 'store' => $info->ID ) ) ) {
                $physical .= '<ul>';
                foreach( \query\locations::while_store_locations( array( 'max' => 0, 'store' => $info->ID ) ) as $loc ) {
                  $physical .= '<li>' . implode( ', ', array_filter( array( $loc->zip, $loc->country, $loc->state, $loc->city, $loc->address ) ) ) . ' <a href="?route=locations.php&amp;action=edit_store_location&amp;id=' . $loc->ID . '">' . t( 'edit', "Edit" ) . '</a> <a href="?route=stores.php&amp;action=edit&amp;id=' . $info->ID . '&amp;type=delete_location&amp;locID=' . $loc->ID . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>';
                }
                $physical .= '</ul>';
            } else {
                $physical .=  '<div class="a-message">' . t( 'msg_no_locations', "No locations yet." ) . '</div>';
            }
            $physical .= '<a href="?route=locations.php&amp;action=add_store_location&amp;id=' . $info->ID . '">' . t( 'add', "Add" ) . '</a>';
        }

        $physical .= '</div></div>';

        $fields['physical']['position'] = 5;
        $fields['physical']['markup'] = $physical;
    }

    // Hours
    $hours_markup = '<div class="row"' . ( !isset( $info->is_physical ) || !$info->is_physical ? ' style="display: none;"' : '' ) . ' data-required=\'' . json_encode( array( 'store_type' => 1 ) ) . '\'><span>' . t( 'form_hours', "Hours" ) . ':</span>
    <div class="hours-info"> <input name="hours-bi" type="checkbox" id="hours-bi"' . ( empty( $info->hours ) ? ' checked' : '' ) . '/> <label for="hours-bi"><span></span> ' . t( 'msg_blankinfo', "Blank information" ) . '</label>
    <ul' . ( empty( $info->hours ) ? ' style="display: none;"' : '' ) . '>';

    if( \query\main::get_option( 'hour_format' ) == 12 ) {

        $hours = array( '01:00 AM', '01:15 AM', '01:30 AM', '01:45 AM', '02:00 AM', '02:15 AM', '02:30 AM', '02:45 AM', '03:00 AM', '03:15 AM', '03:30 AM ', '03:45 AM ', '04:00 AM ', '04:15 AM', '04:30 AM', '04:45 AM',
                    '05:00 AM', '05:15 AM', '05:30 AM', '05:45 AM', '06:00 AM', '06:15 AM', '06:30 AM', '06:45 AM', '07:00 AM', '07:15 AM', '07:30 AM', '07:45 AM', '08:00 AM', '08:15 AM', '08:30 AM', '08:45 AM',
                    '09:00 AM', '09:15 AM', '09:30 AM', '09:45 AM', '10:00 AM', '10:15 AM', '10:30 AM', '10:45 AM', '11:00 AM', '11:15 AM', '11:30 AM', '11:45 AM', '12:00 AM', '12:15 AM', '12:30 AM', '12:45 AM',
                    '01:00 PM', '01:15 PM', '01:30 PM', '01:45 PM', '02:00 PM', '02:15 PM', '02:30 PM', '02:45 PM', '03:00 PM', '03:15 PM', '03:30 PM', '03:45 PM', '04:00 PM', '04:15 PM', '04:30 PM', '04:45 PM',
                    '05:00 PM', '05:15 PM', '05:30 PM', '05:45 PM', '06:00 PM', '06:15 PM', '06:30 PM', '06:45 PM', '07:00 PM', '07:15 PM', '07:30 PM', '07:45 PM', '08:00 PM', '08:15 PM', '08:30 PM', '08:45 PM',
                    '09:00 PM', '09:15 PM', '09:30 PM', '09:45 PM', '10:00 PM', '10:15 PM', '10:30 PM', '10:45 PM', '11:00 PM', '11:15 PM', '11:30 PM', '11:45 PM', '12:00 PM', '12:15 PM', '12:30 PM', '12:45 PM' );

    } else {

        $hours = array( '01:00', '01:15', '01:30', '01:45', '02:00', '02:15', '02:30', '02:45', '03:00', '03:15', '03:30', '03:45', '04:00', '04:15', '04:30', '04:45',
                    '05:00', '05:15', '05:30', '05:45', '06:00', '06:15', '06:30', '06:45', '07:00', '07:15', '07:30', '07:45', '08:00', '08:15', '08:30', '08:45',
                    '09:00', '09:15', '09:30', '09:45', '10:00', '10:15', '10:30', '10:45', '11:00', '11:15', '11:30', '11:45', '12:00', '12:15', '12:30', '12:45',
                    '13:00', '13:15', '13:30', '13:45', '14:00', '14:15', '14:30', '14:45', '15:00', '15:15', '15:30', '15:45', '16:00', '16:15', '16:30', '16:45',
                    '17:00', '17:15', '17:30', '17:45', '18:00', '18:15', '18:30', '18:45', '19:00', '19:15', '19:30', '19:45', '20:00', '20:15', '20:30', '20:45',
                    '21:00', '21:15', '21:30', '21:45', '22:00', '22:15', '22:30', '22:45', '23:00', '23:15', '23:30', '23:45', '24:00', '24:15', '24:30', '24:45' );

    }

    foreach( \site\utils::days_of_week() as $k => $v ) {
        $hours_markup .= '<li>
        <span><input name="hours[' . $k . '][opened]" type="checkbox" value="yes" id="day_' . $k . '"' . ( isset( $info->hours[$k]['opened'] ) ? ' checked' : '' ) . ' /> <label for="day_' . $k . '"><span></span> ' . $v . '</label></span>';
        $hours_markup .= '<span>
        <select name="hours[' . $k . '][from]">';
        foreach( $hours as $no ) {
            $hours_markup .= '<option value="' . $no . '"' . ( isset( $info->hours[$k]['from'] ) && $info->hours[$k]['from'] == $no ? ' selected' : '' ) . '>' . $no . '</option>';
        }
        $hours_markup .= '</select> - <select name="hours[' . $k . '][to]">';
        foreach( $hours as $no ) {
            $hours_markup .= '<option value="' . $no . '"' . ( isset( $info->hours[$k]['to'] ) && $info->hours[$k]['to'] == $no ? ' selected' : '' ) . '>' . $no . '</option>';
        }
        $hours_markup .= '</select>
        </span>
        </li>';
    }
    $hours_markup .= '</ul></div></div>';

    $fields['hours']['position'] = 6;
    $fields['hours']['markup'] = $hours_markup;

    // Check if sells online
    $fields['sell_online']['position'] = 7;
    $fields['sell_online']['markup'] = '<div class="row"' . ( !isset( $info->is_physical ) || !$info->is_physical ? ' style="display:none;"' : '' ) . ' data-required=\'' . json_encode( array( 'store_type' => 1 ) ) . '\'><span>' . t( 'form_sell_online', "Sell Online" ) . ':</span><div class="sell_online_cb"><input type="checkbox" id="sellonline" name="sellonline" value="1"' . ( isset( $info->sellonline2 ) && $info->sellonline2 ? ' checked' : '' ) . ' /> <label for="sellonline"><span></span> <b>' . t( 'yes', "Yes" ) . '</b><b>' . t( 'no', "No" ) . '</b></label></div></div>';

    // Store's URL
    $fields['url']['position'] = 8;
    $fields['url']['markup'] = '<div class="row"><span>' . t( 'form_store_url', "Store URL" ) . ':</span><div><input type="text" name="url" value="' . ( isset( $info->url ) ? $info->url : '' ) . '" /></div></div>';

    // Description
    $fields['description']['position'] = 9;
    $fields['description']['markup'] = '<div class="row"><span>' . t( 'form_description', "Description" ) . ':</span><div><textarea name="description">' . ( isset( $info->description ) ? $info->description : '' ). '</textarea></div></div>';

    // Tags
    $fields['tags']['position'] = 10;
    $fields['tags']['markup'] = '<div class="row"><span>' . t( 'form_tags', "Tags" ) . ':</span><div><input type="text" name="tags" value="' . ( isset( $info->tags ) ? $info->tags : '' ) . '" /></div></div>';

    // Image
    $image = '<div class="row image-upload"><span>' . t( 'form_logo', "Logo" ) . ':</span>
    <div>';
    if( !empty( $info->image ) ) {
        $image .= '<div style="display:table;margin-bottom:2px;"><img src="' . ( $imgsrc = ( preg_match( '/^http(s)?/i', $info->image ) ? $info->image :  '../' . $info->image ) ) . '" class="avt" alt="" style="display:table-cell;vertical-align:middle;vertical-align:middle;max-width:100px;height:50px;margin:0 20px 5px 0;" />
        <div style="display:table-cell;vertical-align:middle;margin-left:25px;">';
        $image .= '<a href="' . $imgsrc . '" target="_blank" class="btn">' . t( 'view', "View" ) . '</a> <a href="' . \site\utils::update_uri( '', array( 'type' => 'delete_image', 'token' => $csrf ) ) . '" class="btn" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>
        </div>
        </div>';
    }
    $image .= '<input type="file" name="logo" accept="image/*" />
    </div></div>';

    $fields['image']['position'] = 11;
    $fields['image']['markup'] = $image;

    // Phone no
    $fields['phone']['position'] = 12;
    $fields['phone']['markup'] = '<div class="row"><span>' . t( 'phone_no', "Phone Number" ) . ':</span><div><input type="text" name="phone" value="' . ( isset( $info->phone_no ) ? $info->phone_no : '' ) . '" /></div></div>';

    // Add to
    $fields['add_to']['position'] = 13;
    $fields['add_to']['markup'] = '<div class="row"><span>' . t( 'form_addto', "Add to" ) . ':</span><div><input type="checkbox" name="popular" id="popular"' . ( isset( $info->is_popular ) && $info->is_popular ? ' checked' : '' ) . ' /> <label for="popular"><span></span> ' . t( 'coupons_addpopular', "Popular" ) . '</label></div></div>
    <div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish"' . ( !isset( $info->visible ) || $info->visible ? ' checked' : '' ) . ' /> <label for="publish"><span></span> ' . t( 'msg_pubstore', "Publish this store" ) . '</label></div></div>';

    return value_with_filter( 'default_store_fields', $fields );

}

public function category_fields( $info = object, $csrf = '' ) {

    do_action( 'before_category_fields', $info );

    global $add_category_fields;

    $custom_fields  = array();
    $sections       = array();

    if( !empty( $add_category_fields ) && is_array( $add_category_fields) ) {
        foreach( $add_category_fields as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $extra = widgets::build_extra( $link['fields'], ( isset( $info->extra ) && is_array( $info->extra ) ? $info->extra : array() ), '', 'extra', $info );
            $custom_fields[$id]['markup'] = $extra['markup'];
            $sections = array_merge( $sections, $extra['sections'] );        }
    }

    return array( 'fields' => self::default_category_fields( $info, $csrf ) + $custom_fields, 'sections' => $sections );
}

public function default_category_fields( $info = object, $csrf = '' ) {

    $fields = array();

    // Subcategory
    $category = '<div class="row"><span>' . t( 'form_subcategoryfor', "Subcategory for" ) . ':</span>
    <div><select name="category">';
    $category .= '<option value="0"' . ( !isset( $info->subcatID ) || $info->subcatID === 0 ? ' selected' : '' ) . '>' . t( 'no_category_select', "No category" ) . '</option>';
    foreach( \query\main::while_categories( array( 'max' => 0, 'show' => 'cats' ), array( 'no_emoticons' => true, 'no_filters' => true ) ) as $cat ) $category .= '<option value="' . $cat->ID . '"' . ( ( isset( $info->subcatID ) && $info->subcatID === $cat->ID ) || ( !isset( $info->subcatID ) && isset( $_GET['cat'] ) && $_GET['cat'] == $cat->ID ) ? ' selected' : '' ) . '>' . $cat->name . '</option>';
    $category .= '</select></div></div>';

    $fields['subcategory']['position'] = 1;
    $fields['subcategory']['markup'] = $category;

    // Name
    $fields['name']['position'] = 2;
    $fields['name']['markup'] = '<div class="row"><span>' . t( 'form_name', "Name" ) . ':</span><div><input type="text" name="name" value="' . ( isset( $info->name ) ? $info->name : '' ) . '" required /></div></div>';

    // Description
    $fields['description']['position'] = 3;
    $fields['description']['markup'] = '<div class="row"><span>' . t( 'form_description', "Description" ) . ':</span><div><textarea name="text" style="min-height:100px;">' . ( isset( $info->description ) ? $info->description : '' ) . '</textarea></div></div>';

    return value_with_filter( 'default_category_fields', $fields );

}

public function user_fields( $info = object, $csrf = '' ) {

    do_action( 'before_user_fields', $info );

    global $add_user_fields;

    $custom_fields  = array();
    $sections       = array();

    if( !empty( $add_user_fields ) && is_array( $add_user_fields) ) {
        foreach( $add_user_fields as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $extra = widgets::build_extra( $link['fields'], ( isset( $info->extra ) && is_array( $info->extra ) ? $info->extra : array() ), '', 'extra', $info );
            $custom_fields[$id]['markup'] = $extra['markup'];
            $sections = array_merge( $sections, $extra['sections'] );          }
    }

    return array( 'fields' => self::default_user_fields( $info, $csrf ) + $custom_fields, 'sections' => $sections );

}

public function default_user_fields( $info = object, $csrf = '' ) {

    $fields = array();

    // Name
    $fields['name']['position'] = 1;
    $fields['name']['markup'] = '<div class="row"><span>' . t( 'form_name', "Name" ) . ':</span><div><input type="text" name="name" value="' . ( isset( $info->name ) ? $info->name : '' ) . '" /></div></div>';

    // Email
    $fields['email']['position'] = 2;
    $fields['email']['markup'] = '<div class="row"><span>' . t( 'form_email', "Email Address" ) . ':</span><div><input type="text" name="email" value="' . ( isset( $info->email ) ? $info->email : '' ) . '" /></div></div>';

    // Send copy/Passowrd
    if( empty( $info ) ) {

    // Send copy
    $fields['send_copy']['position'] = 3;
    $fields['send_copy']['markup'] = '<div class="row"><span> </span><div><input type="checkbox" name="send_copy" id="send_copy" checked /> <label for="send_copy"><span></span> ' . t( 'msg_sendcacc', "Send to this email address a copy of the new account" ) . '</label></div></div>';

    // Password
    $fields['password']['position'] = 4;
    $fields['password']['markup'] = '<div class="row"><span>' . t( 'form_password', "Password" ) . ':</span><div><input type="password" name="password" value="" /></div></div>';

    }

    // Avatar
    $avatar = '<div class="row image-upload"><span>' . t( 'form_avatar', "Avatar" ) . ':</span>
    <div>';
    if( !empty( $info->avatar ) ) {
        $avatar .= '<div style="display: table; margin-bottom: 2px;"><img src="' . ( $imgsrc = \query\main::user_avatar( $info->avatar ) ) . '" class="avt" alt="" style="display:table-cell;vertical-align:middle;max-width:120px;height:80px;margin:0 20px 5px 0;" />
        <div style="display: table-cell; vertical-align: middle; margin-left: 25px;">';
        if( !empty( $info->avatar ) ) $avatar .= '<a href="' . $imgsrc . '" target="_blank" class="btn">' . t( 'view', "View" ) . '</a> <a href="' . \site\utils::update_uri( '', array( 'type' => 'delete_avatar', 'token' => $csrf ) ) . '" class="btn" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';
        $avatar .= '</div>
        </div>';
    }

    $avatar .= '<input type="file" name="logo" accept="image/*" />
    </div> </div>';

    $fields['avatar']['position'] = 5;
    $fields['avatar']['markup'] = $avatar;

    // Points
    $fields['points']['position'] = 6;
    $fields['points']['markup'] = '<div class="row"><span>' . t( 'form_points', "Points" ) . ':</span><div><input type="number" name="points" value="' . ( isset( $info->points ) ? $info->points : '' ) . '" min="0" /></div></div>';

    // Credits/Role only for admin
    if( $GLOBALS['me']->is_admin ) {

    // Credits
    $fields['credits']['position'] = 7;
    $fields['credits']['markup'] = '<div class="row"><span>' . t( 'form_credits', "Credits" ) . ':</span><div><input type="number" name="credits" value="' . ( isset( $info->credits ) ? $info->credits : '' ) . '" min="0" /></div></div>';

    // Role
    $role = '<div class="row"><span>' . t( 'form_role', "Role" ) . ':</span><div><select name="privileges">';
    foreach( array( 0 => t( 'form_role_member', "Member" ), 1 => t( 'form_role_subadmin', "Sub-Administrator" ), 2 => t( 'form_role_admin', "Administrator" ) ) as $k => $v ) $role .= '<option value="' . $k . '"' . ( isset( $info->privileges ) && $info->privileges == $k ? ' selected' : '' ) . '>' . $v . '</option>';
    $role .= '</select></div></div>

    <div class="row"><span> </span>

    <div id="privileges_scope"' . ( !isset( $info->privileges ) || $info->privileges !== 1 ? ' style="display: none;"' : '' ) . '>

    <div> <h2>' . t( 'stores', "Stores" ) . ':</h2> <div> <div><input type="checkbox" name="erole[stores][view]" id="erole[stores][view]" value="1"' . ( isset( $info->erole['stores']['view'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[stores][view]"><span></span> ' . t( 'view', "View" ) . '</label></div> <div><input type="checkbox" name="erole[stores][add]" id="erole[stores][add]" value="1"' . ( isset( $info->erole['stores']['add'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[stores][add]"><span></span> ' . t( 'add', "Add" ) . '</label> <br /> <input type="checkbox" name="erole[stores][import]" id="erole[stores][import]" value="1"' . ( isset( $info->erole['stores']['import'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[stores][import]"><span></span> ' . t( 'import', "Import" ) . '</label> <br /> <input type="checkbox" name="erole[stores][export]" id="erole[stores][export]" value="1"' . ( isset( $info->erole['stores']['export'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[stores][export]"><span></span> ' . t( 'export', "Export" ) . '</label></div> <div><input type="checkbox" name="erole[stores][edit]" id="erole[stores][edit]" value="1"' . ( isset( $info->erole['stores']['edit'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[stores][edit]"><span></span> ' . t( 'edit', "Edit" ) . '</label></div> <div><input type="checkbox" name="erole[stores][delete]" id="erole[stores][delete]" value="1"' . ( isset( $info->erole['stores']['delete'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[stores][delete]"><span></span> ' . t( 'delete', "Delete" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'categories', "Categories" ) . ':</h2> <div> <div><input type="checkbox" name="erole[categories][view]" id="erole[categories][view]" value="1"' . ( isset( $info->erole['categories']['view'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[categories][view]"><span></span> ' . t( 'view', "View" ) . '</label></div> <div><input type="checkbox" name="erole[categories][add]" id="erole[categories][add]" value="1"' . ( isset( $info->erole['categories']['add'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[categories][add]"><span></span> ' . t( 'add', "Add" ) . '</label></div> <div><input type="checkbox" name="erole[categories][edit]" id="erole[categories][edit]" value="1"' . ( isset( $info->erole['categories']['edit'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[categories][edit]"><span></span> ' . t( 'edit', "Edit" ) . '</label></div> <div><input type="checkbox" name="erole[categories][delete]" id="erole[categories][delete]" value="1"' . ( isset( $info->erole['categories']['delete'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[categories][delete]"><span></span> ' . t( 'delete', "Delete" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'coupons', "Coupons" ) . ':</h2> <div> <div><input type="checkbox" name="erole[coupons][view]" id="erole[coupons][view]" value="1"' . ( isset( $info->erole['coupons']['view'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[coupons][view]"><span></span> ' . t( 'view', "View" ) . '</label></div> <div><input type="checkbox" name="erole[coupons][add]" id="erole[coupons][add]" value="1"' . ( isset( $info->erole['coupons']['add'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[coupons][add]"><span></span> ' . t( 'add', "Add" ) . '</label> <br /> <input type="checkbox" name="erole[coupons][import]" id="erole[coupons][import]" value="1"' . ( isset( $info->erole['coupons']['import'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[coupons][import]"><span></span> ' . t( 'import', "Import" ) . '</label> <br /> <input type="checkbox" name="erole[coupons][export]" id="erole[coupons][export]" value="1"' . ( isset( $info->erole['coupons']['export'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[coupons][export]"><span></span> ' . t( 'export', "Export" ) . '</label></div> <div><input type="checkbox" name="erole[coupons][edit]" id="erole[coupons][edit]" value="1"' . ( isset( $info->erole['coupons']['edit'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[coupons][edit]"><span></span> ' . t( 'edit', "Edit" ) . '</label></div> <div><input type="checkbox" name="erole[coupons][delete]" id="erole[coupons][delete]" value="1"' . ( isset( $info->erole['coupons']['delete'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[coupons][delete]"><span></span> ' . t( 'delete', "Delete" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'products', "Products" ) . ':</h2> <div> <div><input type="checkbox" name="erole[products][view]" id="erole[products][view]" value="1"' . ( isset( $info->erole['products']['view'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[products][view]"><span></span> ' . t( 'view', "View" ) . '</label></div> <div><input type="checkbox" name="erole[products][add]" id="erole[products][add]" value="1"' . ( isset( $info->erole['products']['add'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[products][add]"><span></span> ' . t( 'add', "Add" ) . '</label> <br /> <input type="checkbox" name="erole[products][import]" id="erole[products][import]" value="1"' . ( isset( $info->erole['products']['import'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[products][import]"><span></span> ' . t( 'import', "Import" ) . '</label> <br /> <input type="checkbox" name="erole[products][export]" id="erole[products][export]" value="1"' . ( isset( $info->erole['products']['export'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[products][export]"><span></span> ' . t( 'export', "Export" ) . '</label></div> <div><input type="checkbox" name="erole[products][edit]" id="erole[products][edit]" value="1"' . ( isset( $info->erole['products']['edit'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[products][edit]"><span></span> ' . t( 'edit', "Edit" ) . '</label></div> <div><input type="checkbox" name="erole[products][delete]" id="erole[products][delete]" value="1"' . ( isset( $info->erole['products']['delete'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[products][delete]"><span></span> ' . t( 'delete', "Delete" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'locations', "Locations" ) . ':</h2> <div> <div><input type="checkbox" name="erole[locations][view]" id="erole[locations][view]" value="1"' . ( isset( $info->erole['locations']['view'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[locations][view]"><span></span> ' . t( 'view', "View" ) . '</label></div> <div><input type="checkbox" name="erole[locations][add]" id="erole[locations][add]" value="1"' . ( isset( $info->erole['locations']['add'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[locations][add]"><span></span> ' . t( 'add', "Add" ) . '</label></div> <div><input type="checkbox" name="erole[locations][edit]" id="erole[locations][edit]" value="1"' . ( isset( $info->erole['locations']['edit'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[locations][edit]"><span></span> ' . t( 'edit', "Edit" ) . '</label></div> <div><input type="checkbox" name="erole[locations][delete]" id="erole[locations][delete]" value="1"' . ( isset( $info->erole['locations']['delete'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[locations][delete]"><span></span> ' . t( 'delete', "Delete" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'users', "Users" ) . ':</h2> <div> <div><input type="checkbox" name="erole[users][view]" id="erole[users][view]" value="1"' . ( isset( $info->erole['users']['view'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[users][view]"><span></span> ' . t( 'view', "View" ) . '</label></div> <div><input type="checkbox" name="erole[users][add]" id="erole[users][add]" value="1"' . ( isset( $info->erole['users']['add'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[users][add]"><span></span> ' . t( 'add', "Add" ) . '</label></div> <div><input type="checkbox" name="erole[users][edit]" id="erole[users][edit]" value="1"' . ( isset( $info->erole['users']['edit'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[users][edit]"><span></span> ' . t( 'edit', "Edit" ) . '</label></div> <div><input type="checkbox" name="erole[users][delete]" id="erole[users][delete]" value="1"' . ( isset( $info->erole['users']['delete'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[users][delete]"><span></span> ' . t( 'delete', "Delete" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'users_subscribers', "Subscribers" ) . ':</h2> <div> <div><input type="checkbox" name="erole[subscribers][view]" id="erole[subscribers][view]" value="1"' . ( isset( $info->erole['subscribers']['view'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[subscribers][view]"><span></span> ' . t( 'view', "View" ) . '</label></div> <div><input type="checkbox" name="erole[subscribers][import]" id="erole[subscribers][import]" value="1"' . ( isset( $info->erole['subscribers']['import'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[subscribers][import]"><span></span> ' . t( 'import', "Import" ) . '</label> <br /> <input type="checkbox" name="erole[subscribers][export]" id="erole[subscribers][export]" value="1"' . ( isset( $info->erole['subscribers']['export'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[subscribers][export]"><span></span> ' . t( 'export', "Export" ) . '</label></div> <div><input type="checkbox" name="erole[subscribers][edit]" id="erole[subscribers][edit]" value="1"' . ( isset( $info->erole['subscribers']['edit'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[subscribers][edit]"><span></span> ' . t( 'edit', "Edit" ) . '</label></div> <div><input type="checkbox" name="erole[subscribers][delete]" id="erole[subscribers][delete]" value="1"' . ( isset( $info->erole['subscribers']['delete'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[subscribers][delete]"><span></span> ' . t( 'delete', "Delete" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'pages', "Pages" ) . ':</h2> <div> <div><input type="checkbox" name="erole[pages][view]" id="erole[pages][view]" value="1"' . ( isset( $info->erole['pages']['view'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[pages][view]"><span></span> ' . t( 'view', "View" ) . '</label></div> <div><input type="checkbox" name="erole[pages][add]" id="erole[pages][add]" value="1"' . ( isset( $info->erole['pages']['add'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[pages][add]"><span></span> ' . t( 'add', "Add" ) . '</label></div> <div><input type="checkbox" name="erole[pages][edit]" id="erole[pages][edit]" value="1"' . ( isset( $info->erole['pages']['edit'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[pages][edit]"><span></span> ' . t( 'edit', "Edit" ) . '</label></div> <div><input type="checkbox" name="erole[pages][delete]" id="erole[pages][delete]" value="1"' . ( isset( $info->erole['pages']['delete'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[pages][delete]"><span></span> ' . t( 'delete', "Delete" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'reviews', "Reviews" ) . ':</h2> <div> <div><input type="checkbox" name="erole[reviews][view]" id="erole[reviews][view]" value="1"' . ( isset( $info->erole['reviews']['view'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[reviews][view]"><span></span> ' . t( 'view', "View" ) . '</label></div> <div><input type="checkbox" name="erole[reviews][add]" id="erole[reviews][add]" value="1"' . ( isset( $info->erole['reviews']['add'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[reviews][add]"><span></span> ' . t( 'add', "Add" ) . '</label></div> <div><input type="checkbox" name="erole[reviews][edit]" id="erole[reviews][edit]" value="1"' . ( isset( $info->erole['reviews']['edit'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[reviews][edit]"><span></span> ' . t( 'edit', "Edit" ) . '</label></div> <div><input type="checkbox" name="erole[reviews][delete]" id="erole[reviews][delete]" value="1"' . ( isset( $info->erole['reviews']['delete'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[reviews][delete]"><span></span> ' . t( 'delete', "Delete" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'rewards_claimr', "Claim Requests" ) . ':</h2> <div> <div><input type="checkbox" name="erole[claim_reqs][view]" id="erole[claim_reqs][view]" value="1"' . ( isset( $info->erole['claim_reqs']['view'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[claim_reqs][view]"><span></span> ' . t( 'view', "View" ) . '</label></div> <div><input type="checkbox" name="erole[claim_reqs][edit]" id="erole[claim_reqs][edit]" value="1"' . ( isset( $info->erole['claim_reqs']['edit'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[claim_reqs][edit]"><span></span> ' . t( 'edit', "Edit" ) . '</label></div> <div><input type="checkbox" name="erole[claim_reqs][delete]" id="erole[claim_reqs][delete]" value="1"' . ( isset( $info->erole['claim_reqs']['delete'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[claim_reqs][delete]"><span></span> ' . t( 'delete', "Delete" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'suggestions', "Suggestions" ) . ':</h2> <div> <div><input type="checkbox" name="erole[suggestions][view]" id="erole[suggestions][view]" value="1"' . ( isset( $info->erole['suggestions']['view'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[suggestions][view]"><span></span> ' . t( 'view', "View" ) . '</label></div> <div><input type="checkbox" name="erole[suggestions][edit]" id="erole[suggestions][edit]" value="1"' . ( isset( $info->erole['suggestions']['edit'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[suggestions][edit]"><span></span> ' . t( 'edit', "Edit" ) . '</label></div> <div><input type="checkbox" name="erole[suggestions][delete]" id="erole[suggestions][delete]" value="1"' . ( isset( $info->erole['suggestions']['delete'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[suggestions][delete]"><span></span> ' . t( 'delete', "Delete" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'chat_title', "Chat / Notes" ) . ':</h2> <div> <div><input type="checkbox" name="erole[chat][view]" id="erole[chat][view]" value="1"' . ( isset( $info->erole['chat']['view'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[chat][view]"><span></span> ' . t( 'view', "View" ) . '</label></div> <div><input type="checkbox" name="erole[chat][add]" id="erole[chat][add]" value="1"' . ( isset( $info->erole['chat']['add'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[chat][add]"><span></span> ' . t( 'chat_write_button', "Write" ) . '</label></div> <div><input type="checkbox" name="erole[chat][delete]" id="erole[chat][delete]" value="1"' . ( isset( $info->erole['chat']['delete'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[chat][delete]"><span></span> ' . t( 'delete', "Delete" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'ratings', "Reports" ) . ':</h2> <div> <div><input type="checkbox" name="erole[reports][view]" id="erole[reports][view]" value="1"' . ( isset( $info->erole['reports']['view'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[reports][view]"><span></span> ' . t( 'view', "View" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'feed', "Feed" ) . ':</h2> <div> <div><input type="checkbox" name="erole[feed][view]" id="erole[feed][view]" value="1"' . ( isset( $info->erole['feed']['view'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[feed][view]"><span></span> ' . t( 'view', "View" ) . '</label></div> <div><input type="checkbox" name="erole[feed][import]" id="erole[feed][import]" value="1"' . ( isset( $info->erole['feed']['import'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[feed][import]"><span></span> ' . t( 'import', "Import" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'payments', "Payments" ) . ':</h2> <div> <div><input type="checkbox" name="erole[payments][view]" id="erole[payments][view]" value="1"' . ( isset( $info->erole['payments']['view'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[payments][view]"><span></span> ' . t( 'view', "View" ) . '</label></div> <div><input type="checkbox" name="erole[payments][edit]" id="erole[payments][edit]" value="1"' . ( isset( $info->erole['payments']['edit'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[payments][edit]"><span></span> ' . t( 'edit', "Edit" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'others', "Others" ) . ':</h2> <div> <div><input type="checkbox" name="erole[mail][send]" id="erole[mail][send]" value="1"' . ( isset( $info->erole['mail']['send'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[mail][send]"><span></span> ' . t( 'send_email', "Send Email" ) . '</label></div> <div><input type="checkbox" name="erole[users][ban]" id="erole[users][ban]" value="1"' . ( isset( $info->erole['users']['ban'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[users][ban]"><span></span> ' . t( 'ban', "Ban" ) . '</label></div> </div> </div>
    <div> <h2>' . t( 'gallery', "Gallery" ) . ':</h2> <div> <div><input type="checkbox" name="erole[gallery][upload]" id="erole[gallery][upload]" value="1"' . ( isset( $info->erole['gallery']['upload'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[gallery][upload]"><span></span> ' . t( 'upload', "Upload" ) . '</label></div> <div><input type="checkbox" name="erole[gallery][delete]" id="erole[gallery][delete]" value="1"' . ( isset( $info->erole['gallery']['delete'] ) || !isset( $info->is_subadmin )  || !$info->is_subadmin ? ' checked' : '' ) . ' /> <label for="erole[gallery][delete]"><span></span> ' . t( 'delete', "Delete" ) . '</label></div> </div> </div>

    </div>

    </div>';

    $fields['role']['position'] = 8;
    $fields['role']['markup'] = $role;

    }

    // Subscriber
    $fields['subscriber']['position'] = 9;
    $fields['subscriber']['markup'] = '<div class="row"><span>' . t( 'form_subscriber', "Subscribe" ) . ':</span><div><input type="checkbox" name="subscriber" id="subscriber"' . ( !isset( $info->is_subscribed ) || $info->is_subscribed ? ' checked' : '' ) . ' /> <label for="subscriber"><span></span> ' . t( 'msg_setsub', "Set this user as subscriber to newsletter" ) . '</label></div></div>';

    // Confirm
    $fields['confirm']['position'] = 10;
    $fields['confirm']['markup'] = '<div class="row"><span>' . t( 'form_confirm', "Confirm" ) . ':</span><div><input type="checkbox" name="confirm" id="confirm"' . ( !isset( $info->is_confirmed ) || $info->is_confirmed ? ' checked' : '' ) . ' /> <label for="confirm"><span></span> ' . t( 'msg_setconf', "Set this user as confirmed by email" ) . '</label></div></div>';

    return value_with_filter( 'default_user_fields', $fields );

}

public function page_fields( $info = object, $csrf = '' ) {

    do_action( 'before_page_fields', $info );

    global $add_page_fields;

    $custom_fields  = array();
    $sections       = array();

    if( !empty( $add_page_fields ) && is_array( $add_page_fields) ) {
        foreach( $add_page_fields as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $extra = widgets::build_extra( $link['fields'], ( isset( $info->extra ) && is_array( $info->extra ) ? $info->extra : array() ), '', 'extra', $info );
            $custom_fields[$id]['markup'] = $extra['markup'];
            $sections = array_merge( $sections, $extra['sections'] );         }
    }

    return array( 'fields' => self::default_page_fields( $info, $csrf ) + $custom_fields, 'sections' => $sections );

}

public function default_page_fields( $info = object, $csrf = '' ) {

    $fields = array();

    // Name
    $fields['name']['position'] = 1;
    $fields['name']['markup'] = '<div class="row"><span>' . t( 'form_name', "Name" ) . ':</span><div><input type="text" name="name" value="' . ( isset( $info->name ) ? $info->name : '' ) . '" required /></div></div>';

    // Text
    $fields['text']['position'] = 2;
    $fields['text']['markup'] = '<div class="row"><span>' . t( 'form_text', "Content" ) . ':</span><div><textarea name="text" style="min-height:400px;">' . ( isset( $info->text ) ? $info->text : '' ) . '</textarea></div></div>';

    // Publish
    $fields['publish']['position'] = 3;
    $fields['publish']['markup'] = '<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish"' . ( !isset( $info->visible ) || $info->visible ? ' checked' : '' ) . ' /> <label for="publish"><span></span> ' . t( 'msg_pubpage', "Publish this page" ) . '</label></div></div>';


    return value_with_filter( 'default_page_fields', $fields );

}

public function meta_tags_fields( $info = object, $csrf = '', $omit_filter = false ) {

    if( $omit_filter ) {
        return self::default_meta_tags_fields( $info, $csrf );
    }

    $custom_fields = array();

    $extra_fields = value_with_filter( 'add-meta-tags-fields', array() );

    if( !empty( $extra_fields ) && is_array( $extra_fields ) ) {
        foreach( $extra_fields as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $custom_fields[$id]['markup'] = widgets::build_extra( $link['fields'], ( isset( $info->extra ) && is_array( $info->extra ) ? $info->extra : array() ), '', 'extra', $info )['markup'];
        }
    }

    return self::default_meta_tags_fields( $info, $csrf ) + $custom_fields;

}

public function default_meta_tags_fields( $info = object, $csrf = '' ) {

    $fields = array();

    // Title
    $fields['name']['position'] = 1;
    $fields['name']['markup'] = '<div class="row"><span>' . t( 'settings_form_metatitle', "Title" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_title" value="' . ( isset( $info->meta_title ) ? $info->meta_title : '' ) . '" /></div></div>';

    // Tags
    $fields['text']['position'] = 2;
    $fields['text']['markup'] = '<div class="row"><span>' . t( 'settings_form_metakeywords', "Keywords" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_keywords" value="' . ( isset( $info->meta_keywords ) ? $info->meta_keywords : '' ) . '"></div></div>';

    // Description
    $fields['publish']['position'] = 3;
    $fields['publish']['markup'] = '<div class="row"><span>' . t( 'settings_form_metadesc', "Description" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_desc">' . ( isset( $info->meta_description ) ? $info->meta_description : '' ) . '</textarea></div></div>';


    return $fields;

}

public function admin_theme() {

    $themes = self::admin_themes();
    $current_theme = \query\main::get_option( 'admintheme' );

    if( in_array( $current_theme, array_keys( $themes ) ) ) {
        return $themes[$current_theme];
    }
    return $themes['default'];

}

public function admin_themes() {

    global $add_admin_themes;

    $themes = array();

    $themes['default']['name'] = 'Default';
    $themes['default']['src']['css'][] = 'theme/default.css';
    if( !empty( $add_admin_themes ) && is_array( $add_admin_themes ) ) {
        foreach( $add_admin_themes as $theme_id => $options ) {
            if( isset( $options['name'] ) && isset( $options['src']['css'] ) ) {
                $themes[$theme_id]['name'] = esc_html( $options['name'] );
                if( is_array( $options['src']['css'] ) ) {
                    foreach( $options['src']['css'] as $css ) {
                        $themes[$theme_id]['src']['css'][] = esc_html( $css );
                    }
                } else {
                    $themes[$theme_id]['src']['css'][] = esc_html( $options['src']['css'] );

                }
                if( isset( $options['src']['js'] ) ) {
                    if( is_array( $options['src']['js'] ) ) {
                        foreach( $options['src']['js'] as $js ) {
                            $themes[$theme_id]['src']['js'][] = esc_html( $js );
                        }
                    } else {
                        $themes[$theme_id]['src']['js'][] = esc_html( $options['src']['js'] );
                    }
                }
            }
        }
    }

    return $themes;

}

public function coupon_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_coupon', $links, $item );

    if( isset( $lists['coupon'] ) && ( $callback = \site\utils::check_callback( $lists['coupon'] ) ) ) {
        return call_user_func( $callback, $item, $links );
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>

    <img src="' . \query\main::store_avatar( !empty( $item->image ) ? $item->image : $item->store_img )    . '" alt="" style="width:80px;min-width:80px;" />

    <div class="info-div"><h2>' . ( !$item->visible ? '<span class="msg-error">' . t( 'notpublished', 'Not Published' ) . '</span> ' : '' ) . ( $item->feedID !== 0 ? '<span class="msg-alert" title="' . t( 'added_through_feed_msg', 'Added through feed.' ) . '">' . t( 'added_through_feed', 'Imported' ) . '</span> ' : '' ) . ( !$item->is_expired ? '<span class="msg-success">' . t( 'active', 'Active' ) . '</span> ' : '<span class="msg-error">' . t( 'expired',  'Expired' ) . '</span> ' ) . $item->title . '
    <span class="fright date">' . date( 'Y.m.d, ' . ( \query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i' ), strtotime( $item->date ) ) . '</span></h2>
    <a href="?route=coupons.php&amp;store=' . $item->storeID . '">' . $item->store_name . '</a></div>

    </div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function product_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_product', $links, $item );

    if( isset( $lists['product'] ) && ( $callback = \site\utils::check_callback( $lists['product'] ) ) ) {
        return call_user_func( $callback, $item, $links );
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>

    <img src="' . \query\main::product_avatar( $item->image ) . '" alt="" style="height:50px;width:50px;min-width:50px;" />

    <div class="info-div"><h2>' . ( !$item->visible ? '<span class="msg-error">' . t( 'notpublished', 'Not Published' ) . '</span> ' : '' ) . ( $item->feedID !== 0 ? '<span class="msg-alert" title="' . t( 'added_through_feed_msg', 'Added through feed.' ) . '">' . t( 'added_through_feed', 'Imported' ) . '</span> ' : '' ) . ( !$item->is_expired ? '<span class="msg-success">' . t( 'active', 'Active' ) . '</span> ' : '<span class="msg-error">' . t( 'expired',  'Expired' ) . '</span> ' ) . $item->title . '
    <span class="fright date">' . date( 'Y.m.d, ' . ( \query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i' ), strtotime( $item->date ) ) . '</span></h2>';
    if( !empty( $item->storeID ) ) {
        echo '<a href="?route=products.php&amp;store=' . $item->storeID . '">' . $item->store_name . '</a>';
    }
    echo '</div>
    
    </div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function store_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_store', $links, $item );

    if( isset( $lists['store'] ) && ( $callback = \site\utils::check_callback( $lists['store'] ) ) ) {
        return call_user_func( $callback, $item, $links );
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>

    <img src="' . \query\main::store_avatar( $item->image ) . '" alt="" style="width:80px;min-width:80px;" />

    <div class="info-div"><h2>' . ( !$item->visible ? '<span class="msg-error">' . t( 'notpublished', 'Not Published' ) . '</span> ' : '' ) . ( $item->feedID !== 0 ? '<span class="msg-alert" title="' . t( 'added_through_feed_msg', 'Added through feed.' ) . '">' . t( 'added_through_feed', 'Imported' ) . '</span> ' : '' ) . $item->name . '
    <span class="fright date">' . date( 'Y.m.d, ' . ( \query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i' ), strtotime( $item->date ) ) . '</span></h2>';
    if( ab_to( array( 'coupons' => 'view' ) ) ) {
        echo ( empty( $item->coupons ) ? t( 'no_coupons_store', 'No coupons yet' ) : '<a href="?route=coupons.php&amp;store=' . $item->ID . '">' . sprintf( t( 'nr_coupons_store', '%s coupons' ), $item->coupons ) . '</a>' );
        if( ( $ab_view_pr = ab_to( array( 'products' => 'view' ) ) ) ) {
            echo '<br />';
        }
    }
    if( $ab_view_pr ) {
    echo ( empty( $item->products ) ? t( 'no_products_store', 'No products yet' ) : '<a href="?route=products.php&amp;store=' . $item->ID . '">' . sprintf(  t( 'nr_products_store', '%s products' ), $item->products ) . '</a>' );
    }
    echo '</div>

    </div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function category_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_category', $links, $item );

    if( isset( $lists['category'] ) && ( $callback = \site\utils::check_callback( $lists['category'] ) ) ) {
        return call_user_func( $callback, $item, $links );
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>
    <div class="info-div"><h2>' . $item->name . '</h2></div>

    </div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function user_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_user', $links, $item );

    if( isset( $lists['user'] ) && ( $callback = \site\utils::check_callback( $lists['user'] ) ) ) {
        return call_user_func( $callback, $item, $links );
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>

    <img src="' . \query\main::user_avatar( $item->avatar ) . '" alt="" />

    <div class="info-div"><h2>' . ( $item->is_confirmed ? '<span class="msg-success">' . t( 'verified', 'Verified' ) . '</span> ' : '<span class="msg-error">' . t( 'notverified', 'Unverified' ) . '</span> ' ) . ( $item->is_banned ? '<span class="msg-alert" title="' . sprintf( t( 'msg_banned_until', 'Banned until %s' ), $item->ban ) . '">' . t( 'banned', 'Banned' ) . '</span> ' : '' ) . $item->name . '
    <span class="fright date">' . date( 'Y.m.d, ' . ( \query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i' ), strtotime( $item->date ) ) . '</span></h2></div>

    </div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function page_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_page', $links, $item );

    if( isset( $lists['page'] ) && ( $callback = \site\utils::check_callback( $lists['page'] ) ) ) {
        return call_user_func( $callback, $item, $links );
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>
    <div class="info-div"><h2>' . ( $item->visible ? '<span class="msg-success">' . t( 'published', 'Published' ) . '</span> ' : '<span class="msg-error">' . t( 'notpublished', 'Not Published' ) . '</span> ' ) . $item->name . '</h2></div>

    </div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function click_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_click', $links, $item );

    if( isset( $lists['click'] ) && ( $callback = \site\utils::check_callback( $lists['click'] ) ) ) {
        return call_user_func( $callback, $item, $links );
    }

    echo '<li>
    <div>

    <img src="' . \query\main::store_avatar( $item->store_img ) . '" alt="" style="width: 80px;" />

    <div class="info-div">

    <h2>' . ( !empty( $item->country ) ? '<img src="../' . LBDIR . '/iptocountry/flags/' . strtolower( $item->country ) . '.png" alt="' . $item->country_full . '" title="' . $item->country_full . '" /> ' : '' ) . '<span style="color: ' . ( !empty( $item->user ) ? '#990099' : '#003366' ) . ';" title="' . $item->browser . '">' . $item->IP . ( !empty( $item->user ) && ( $user = \query\main::user_info( $item->user ) ) ? ' / ' . $user->name : '' ) . '</span>
    <span class="fright date">' . date( 'Y.m.d, ' . ( \query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i' ), strtotime( $item->date ) ) . '</span></h2>
    <a href="?route=clicks.php&amp;store=' . $item->storeID . '">' . $item->store_name . '</a> ';
    if( !empty( $item->couponID ) && empty( $item->productID ) ) {
        echo '(' . t( 'clicksr_couponid', 'Coupon ID' ) . ': <a href="?route=clicks.php&amp;coupon=' . $item->couponID . '">' . $item->couponID . '</a>)';
    } else if( empty( $item->couponID ) && !empty( $item->productID ) ) {
        echo '(' . t( 'clicksr_productid', 'Product ID' ) . ': <a href="?route=clicks.php&amp;product=' . $item->productID . '">' . $item->productID . '</a>)';
    }
    echo '</div></div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function review_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_review', $links, $item );

    if( isset( $lists['review'] ) && ( $callback = \site\utils::check_callback( $lists['review'] ) ) ) {
        return call_user_func( $callback, $item, $links );
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>

    <img src="' . \query\main::user_avatar( $item->user_avatar ) . '" alt="" />

    <div class="info-div">

    <h2>' . ( $item->valid ? '<span class="msg-success">' . t( 'published', 'Published' ) . '</span> ' : '<span class="msg-error">' . t( 'notpublished', 'Not Published' ) . '</span> ' ) . sprintf( t( 'reviews_byto', 'By %s to %s' ), '<a href="?route=reviews.php&amp;action=list&amp;user=' . $item->userID . '">' . $item->user_name . '</a>', '<a href="?route=reviews.php&amp;action=list&amp;store=' . $item->storeID . '">' . ( \query\main::store_info( $item->storeID, array( 'no_emoticons' => true, 'no_filters' => true ) )->name ) . '</a>' ) . '
    <span class="fright date">' . date( 'Y.m.d, ' . ( \query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i' ), strtotime( $item->date ) ) . '</span></h2>

    <div class="info-bar">' . $item->text . '</div>

    </div></div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function ban_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_ban', $links, $item );

    if( isset( $lists['ban'] ) && ( $callback = \site\utils::check_callback( $lists['ban'] ) ) ) {
        return call_user_func( $callback, $item, $links );
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>
    <div class="info-div"><h2>' . $item->IP . '</h2></div>

    </div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function plugin_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_plugin', $links, $item );

    if( isset( $lists['plugin'] ) && ( $callback = \site\utils::check_callback( $lists['plugin'] ) ) ) {
        return call_user_func( $callback, $item, $links );
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>

    <img src="' . ( empty( $item->image ) ? '../' . DEFAULT_IMAGES_LOC . '/plugin_ico.png' : '../' . $item->image ) . '" alt="" style="width: 70px;" />

    <div class="info-div"><h2>' . ( !$item->visible ? '<span class="msg-error">' . t( 'notpublished', 'Not Published' ) . '</span> ' : '' ) . $item->name . '
    <span class="fright date">' . date( 'Y.m.d, ' . ( \query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i' ), strtotime( $item->date ) ) . '</span></h2>
    v ' . sprintf( '%0.2f', $item->version ). '
    </div>

    </div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function theme_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_theme', $links, $item );

    if( isset( $lists['theme'] ) && function_exists( $lists['theme'] ) ) {
        return $lists['theme']($item['item'], $links);
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item['item'] . ']" id="id[' . $item['item'] . ']"' . ( $item['current_theme'] == $item['item'] ? ' disabled' : '' ) . ' /> <label for="id[' . $item['item'] . ']"><span></span></label>';

    echo '<img src="' . \query\main::theme_avatar( str_replace( DIR . '/', '', current(glob( DIR . '/' . THEMES_LOC . '/' . $item['item'] . '/preview.[jpg][pni][gf]' )) ) ) . '" alt="" style="width:85px;height:85px;" />';

    echo '<div class="info-div"><h2>' . ( $item['current_theme'] == $item['item'] ? '<span class="msg-success">' . t( 'themes_used', 'Used' ) . '</span> ' : '' ) . $item['item'] . '</h2>

    <div class="info-bar">';

    $infoList = array();

    if( $info = template::read_theme_info_file( $item['item'] ) ) {

    if( isset( $info['version'] ) ) {
        $infoList[] = t( 'themes_version', 'Version' ) . ': <b>' . esc_html( $info['version'] ) . '</b>';
    }

    if( isset( $info['published_by'] ) ) {
        $infoList[] = t( 'themes_published_by', 'Published by' ) . ': <b>' . esc_html( $info['published_by'] ) . '</b>';
    }

    if( isset( $info['publisher_url'] ) ) {
        $infoList[] = t( 'themes_publisher_url', 'URL Publisher' ) . ': <a href="' . esc_html( $info['publisher_url'] ) . '" target="_blank">' . esc_html( $info['publisher_url'] ) . '</a>';
    }

    if( isset( $info['description'] ) ) {
        $infoList[] = '<a href="#" class="show_theme_desc"><span>&#8601;</span> ' . t( 'description', 'Description' ) . '</a>';
    }

    if( empty( $info ) ) echo t( 'themes_no_info', 'No information about this theme.' );
    else

    echo implode( ', ', $infoList );

    if( isset( $info['description'] ) ) {
        echo '<div class="theme-desc">' . esc_html( $info['description'] ) . '</div>';
    }

    } else {

        echo t( 'themes_no_info', 'No information about this theme.' );

    }

    echo '</div></div></div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function country_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_country', $links, $item );

    if( isset( $lists['country'] ) && function_exists( $lists['country'] ) ) {
        return $lists['country']($item['item'], $links);
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>
    <div class="info-div"><h2>' . ( $item->visible ? '<span class="msg-success">' . t( 'published', 'Published' ) . '</span> ' : '<span class="msg-error">' . t( 'notpublished', 'Not Published' ) . '</span> ' ) . $item->name . '</h2></div>

    </div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function state_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_state', $links, $item );

    if( isset( $lists['state'] ) && function_exists( $lists['state'] ) ) {
        return $lists['state']($item['item'], $links);
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>
    <div class="info-div"><h2>' . ( $item->visible ? '<span class="msg-success">' . t( 'published', 'Published' ) . '</span> ' : '<span class="msg-error">' . t( 'notpublished', 'Not Published' ) . '</span> ' ) . $item->name . '</h2></div>

    </div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function city_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_city', $links, $item );

    if( isset( $lists['city'] ) && function_exists( $lists['city'] ) ) {
        return $lists['city']($item['item'], $links);
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>
    <div class="info-div"><h2>' . ( $item->visible ? '<span class="msg-success">' . t( 'published', 'Published' ) . '</span> ' : '<span class="msg-error">' . t( 'notpublished', 'Not Published' ) . '</span> ' ) . $item->name . '</h2></div>

    </div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function subscriber_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_subscriber', $links, $item );

    if( isset( $lists['subscriber'] ) && function_exists( $lists['subscriber'] ) ) {
        return $lists['subscriber']($item['item'], $links);
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']"' . ( $item->is_user ? ' disabled' : '' ) . ' /> <label for="id[' . $item->ID . ']"><span></span></label>';

    if( $item->is_user ) {

    echo '<img src="' . \query\main::user_avatar( $item->user_avatar ) . '" alt="" />

    <div class="info-div"><h2>' . ( $item->verified ? '<span class="msg-success">' . t( 'verified', 'Verified' ) . '</span> ' : '<span class="msg-error">' . t( 'notverified', 'Unverified' ) . '</span> ' ) . $item->user_name . '
    <span class="fright date">' . date( 'Y.m.d, ' . ( \query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i' ), strtotime( $item->date ) ) . '</span></h2>
    ' . esc_html( $item->email ) . '</div>

    </div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links['is_user'] );
    echo '</div>';

    } else {

    echo '<div style="display: table-cell; content: \' \'; width:10px;"></div>

    <div class="info-div"><h2>' . ( $item->verified ? '<span class="msg-success">' . t( 'verified', 'Verified' ) . '</span> ' : '<span class="msg-error">' . t( 'notverified', 'Unverified' ) . '</span> ' ) . esc_html( $item->email ) . '
    <span class="fright date">' . date( 'Y.m.d, ' . ( \query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i' ), strtotime( $item->date ) ) . '</span></h2></div>

    </div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links['subscriber'] );
    echo '</div>';

    }

    echo '</li>';

}

public function active_session_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_active_session', $links, $item );

    if( isset( $lists['active_session'] ) && function_exists( $lists['active_session'] ) ) {
        return $lists['active_session']($item['item'], $links);
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>

    <img src="' . \query\main::user_avatar( $item->avatar ) . '" alt="" />
    <div class="info-div"><h2>' . $item->name . '
    <span class="fright date">' . date( 'Y.m.d, ' . ( \query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i' ), strtotime( $item->date ) ) . '</span></h2></div>

    </div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function suggestion_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_suggestion', $links, $item );

    if( isset( $lists['suggestion'] ) && function_exists( $lists['suggestion'] ) ) {
        return $lists['suggestion']($item['item'], $links);
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>

    <div style="display: table-cell; content: \' \'; width:10px;"></div>

    <div class="info-div">

    <h2>' . ( $item->read ? '<span class="msg-error">' . t( 'read', 'Read' ) . '</span> ' : '<span class="msg-success">' . t( 'unread', 'Unread' ) . '</span> ' ) . $item->name . '
    <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2>

    <div class="info-bar">' . template::suggestion_intent( $item->type ) . '</div>

    </div></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>
    </li>';

}

public function payment_plan_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_payment_plan', $links, $item );

    if( isset( $lists['payment_plan'] ) && function_exists( $lists['payment_plan'] ) ) {
        return $lists['payment_plan']($item['item'], $links);
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>

    <img src="' . \query\main::payment_plan_avatar( $item->image ) . '" alt="" />
    <div class="info-div"><h2>' . ( $item->visible ? '<span class="msg-success">' . t( 'published', "Published" ) . '</span>' : '<span class="msg-error">' . t( 'notpublished', "Not Published" ) . '</span>' ) . ' ' . $item->name . ' (' . $item->price_format . ')</h2>
    ' . t( 'form_credits', "Credits" ) . ': <b>' . $item->credits . '</b>
    </div></div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>

    </li>';

}

public function payment_invoice_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_payment_invoice', $links, $item );

    if( isset( $lists['payment_invoice'] ) && function_exists( $lists['payment_invoice'] ) ) {
        return $lists['payment_invoice']($item['item'], $links);
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>

    <img src="' . \query\main::user_avatar( $item->user_avatar ) . '" alt="" />

    <div class="info-div">

    <h2>' . ( $item->paid ? '<span class="msg-success">' . $item->state . '</span>' : '<span class="msg-alert">' . $item->state . '</span>' ) . ' ' . $item->user_name . '
    <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2>

    <div class="info-bar">' . t( 'form_amount', "Amount" ) . ': ' . $item->price_format . ' <span class="info"><span>' . t( 'pmts_form_gateway', "Gateway" ) . ': ' . $item->gateway . ' <br /> ' . $item->details . '</span></span> / ' . t( 'pmts_form_delivered', "Delivered" ) . ': ' . ( $item->delivered ? '<span class="msg-success">' . t( 'yes', "Yes" ) . '</span>' : '<span class="msg-error">' . t( 'no', "No" ) . '</span>' ) . '</div>

    </div></div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>

    </li>';

}

public function reward_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_reward', $links, $item );

    if( isset( $lists['reward'] ) && function_exists( $lists['reward'] ) ) {
        return $lists['reward']($item['item'], $links);
    }

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>

    <img src="' . \query\main::reward_avatar( $item->image ) . '" alt="" />

    <div class="info-div">

    <h2>' . ( $item->visible ? '<span class="msg-success">' . t( 'published', "Published" ) . '</span>' : '<span class="msg-error">' . t( 'notpublished', "Not Published" ) . '</span>' ) . ' ' . $item->title . '</h2>

    </div></div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>

    </li>';

}

public function reward_request_list_markup( $item, $links ) {

    $lists = get( 'admin-list-style' );

    $links = value_with_filter( 'admin_list_reward_request', $links, $item );

    if( isset( $lists['reward_request'] ) && function_exists( $lists['reward_request'] ) ) {
        return $lists['reward_request']($item['item'], $links);
    }

    $user = \query\main::user_info( $item->user );

    echo '<li>
    <div>

    <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>

    <img src="' . \query\main::user_avatar( $user->avatar ) . '" alt="" />

    <div class="info-div">

    <h2>' . ( $item->claimed ? '<span class="msg-success">' . t( 'claimed', "Claimed" ) . '</span>' : '<span class="msg-error">' . t( 'notclaimed', "Unclaimed" ) . '</span>' ) . ( empty( $user->name ) ? ' -' : ' <a href="?route=rewards.php&amp;action=requests&amp;user=' . $item->user . '">' . $user->name . '</a>' ) . '
    <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2>

    ' . ( $item->reward_exists ? '<a href="?route=rewards.php&amp;action=requests&amp;reward=' . $item->reward . '">' . $item->name . '</a>' : $item->name ) . ' / ' . t( 'rewards_req_form_pused', "Points used" ) . ': <b>' . $item->points . '</b>

    </div></div>

    <div style="clear:both;"></div>

    <div class="options">';
    echo implode( $links );
    echo '</div>

    </li>';

}

}