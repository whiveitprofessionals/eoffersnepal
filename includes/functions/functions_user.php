<?php

/* CHECK IF HAVE FAVORITE STORES */

function have_favorites( $category = array() ) {
    return \query\favorites::have_favorites( $category );
}

/* INFORMATION ABOUT FAVORITE STORES */

function favorites( $category = array() ) {
    return \query\favorites::fetch_favorites( $category );
}

/* CHECK IF HAVE FAVORITE COUPONS */

function have_favorite_items( $category = array() ) {
    return \query\favorites::have_items( $category );
}

/* INFORMATION ABOUT FAVORITE COUPONS */

function favorite_items( $category = array() ) {
    return \query\favorites::fetch_items( $category );
}

/* CHECK IF HAVE FAVORTE PRODUCTS */

function have_favorite_products( $category = array() ) {
    return \query\favorites::have_products( $category );
}

/* INFORMATION ABOUT FAVORTE PRODUCTS */

function favorite_products( $category = array() ) {
    return \query\favorites::fetch_products( $category );
}

/* CHECK IF HAVE SAVED STORES */

function have_saved_stores( $category = array() ) {
    return \query\saved::have_stores( $category );
}

/* INFORMATION ABOUT SAVED STORES */

function saved_stores( $category = array() ) {
    return \query\saved::fetch_stores( $category );
}

/* CHECK IF HAVE SAVED COUPONS */

function have_saved_items( $category = array() ) {
    return \query\saved::have_items( $category );
}

/* INFORMATION ABOUT SAVED COUPONS */

function saved_items( $category = array() ) {
    return \query\saved::fetch_items( $category );
}

/* CHECK IF HAVE CLAIMED COUPONS */

function have_claimed_items( $category = array() ) {
    return \query\claims::have_items( $category );
}

/* INFORMATION ABOUT CLAIMED COUPONS */

function claimed_items( $category = array() ) {
    return \query\claims::fetch_items( $category );
}

/* CHECK IF HAVE SAVED PRODUCTS */

function have_saved_products( $category = array() ) {
    return \query\saved::have_products( $category );
}

/* INFORMATION ABOUT SAVED PRODUCTS */

function saved_products( $category = array() ) {
    return \query\saved::fetch_products( $category );
}

/* CHECK IF HAVE REWARD REQUESTS */

function have_reward_reqs( $category = array()    ) {
    return \query\main::have_rewards_reqs( array_merge( $category, array( 'user' => $GLOBALS['me']->ID ) ) );
}

/* INFORMATION ABOUT REWARD REQUESTS */

function reward_reqs( $category = array() ) {
    return \query\main::while_rewards_reqs( array_merge( $category, array( 'user' => $GLOBALS['me']->ID ) ) );
}

/* CHECK IF HAVE STORES */

function have_stores( $category = array() ) {
    if( !$GLOBALS['me'] ) {
        return false;
    }
    return \query\main::have_stores( array_merge( $category, array( 'user' => $GLOBALS['me']->ID ) ) );
}


/* INFORMATION ABOUT STORES */

function stores( $category = array() ) {
    if( !$GLOBALS['me'] ) {
        return false;
    }
    return \query\main::while_stores( array_merge( $category, array( 'user' => $GLOBALS['me']->ID ) ) );
}

/* CHECK IF HAVE COUPONS */

function have_coupons( $category = array() ) {
    if( !$GLOBALS['me'] ) {
        return false;
    }
    return \query\main::have_items( array_merge( $category, array( 'store_owner' => $GLOBALS['me']->ID ) ) );
}


/* INFORMATION ABOUT COUPONS */

function coupons( $category = array() ) {
    if( !$GLOBALS['me'] ) {
        return false;
    }
    return \query\main::while_items( array_merge( $category, array( 'store_owner' => $GLOBALS['me']->ID ) ) );
}

/* CHECK IF HAVE PRODUCTS */

function have_products( $category = array() ) {
    if( !$GLOBALS['me'] ) {
        return false;
    }
    return \query\main::have_products( array_merge( $category, array( 'store_owner' => $GLOBALS['me']->ID ) ) );
}


/* INFORMATION ABOUT PRODUCTS */

function products( $category = array() ) {
    if( !$GLOBALS['me'] ) {
        return false;
    }
    return \query\main::while_products( array_merge( $category, array( 'store_owner' => $GLOBALS['me']->ID ) ) );
}

if( $GLOBALS['me'] ) {

    /* THIS IS USER SECTION */

    function this_is_user_section( $identifier = 0 ) {

        if( !empty( $identifier ) ) {

            global $GET;

            if( isset( $GET['id'] ) && strcasecmp( $GET['id'], $identifier ) == 0 ) {
                return true;
            }
            return false;

        }

        return true;

    }

} else {

    /* THIS IS 404 PAGE */

    function this_is_404_page() {

        return true;

    }

}

/* EDIT PROFILE FORM */

function edit_profile_form() {

    if( $GLOBALS['me'] ) {

    $form = '<div class="edit_profile_form other_form">';

    $user = $GLOBALS['me'];

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['data']['csrf'] ) && \site\utils::check_csrf( $_POST['data']['csrf'], 'edit_profile_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['data'] );
    $pd['extra'] = value_with_filter( 'user_save_user_extra_fields', ( isset( $pd['extra'] ) ? \site\utils::array_sanitize( $pd['extra'] ) : array() ), array() );

    try {

        \user\main::edit_profile( $GLOBALS['me']->ID, $pd );

        $form .= '<div class="success">' . t( 'profile_success', "Your profile has been updated." ) . '</div>';

        // check again information about this item

        $user = \user\main::is_logged();

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    }

    $csrf = $_SESSION['edit_profile_csrf'] = \site\utils::str_random(12);

    $form .= '<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">';

    // Username
    $fields['username']['position'] = 1;
    $fields['username']['markup'] = '<div class="form_field"><label for="data[username]">' . t( 'form_name', "Name" ) . ':</label> <div><input type="text" name="data[username]" id="data[username]" value="' . $user->Name . '" required /></div></div>';

    // Email
    $fields['email']['position'] = 2;
    $fields['email']['markup'] = '<div class="form_field"><label for="data[email]">' . t( 'form_email', "Email Address" ) . ':</label> <div><input type="text" name="data[email]" id="data[email]" value="' . $user->Email . '" disabled /></div></div>';

    // Image
    $fields['image']['position'] = 3;
    $fields['image']['markup'] = '<div class="form_field"><label for="data_avatar">' . t( 'form_avatar', "Avatar" ) . ':</label> <div><img src="' . user_avatar( $user->Avatar ) . '" alt="" class="data_avatar" style="max-width:150px;height:150px;" /> <input type="file" name="data_avatar" id="data_avatar" class="inputFile" />
    <label for="data_avatar" class="fileUpload"></label>
    <span>' .t( 'form_avatar_info', 'Note:* max width: 600px, max height: 600px.' ) . '</span></div></div>';

    // Subscribe
    $fields['subscribe']['position'] = 4;
    $fields['subscribe']['markup'] = '<div class="form_field"><span>' . t( 'form_subscriber', "Subscribe" ) . ':</span> <div><input type="checkbox" name="data[subscriber]" id="data[subscriber]" class="inputCheckbox" ' . ( $user->is_subscribed ? 'checked ' : '' ) . '/> <label for="data[subscriber]">' . t( 'msg_subscribe', "Subscribe to our newsletter." ) . '</label></div></div>';

    global $add_form_fields;

    $custom_fields = array();
    if( !empty( $add_form_fields['user'] ) && is_array( $add_form_fields['user'] ) ) {
        foreach( $add_form_fields['user'] as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $custom_fields[$id]['markup'] = \site\fields::build_extra( $link['fields'], $user->Extra, 'data[extra]' );
        }
    }

    $fields = value_with_filter( 'default_user_user_fields', $fields ) + $custom_fields;

    uasort( $fields, function( $a, $b ) {
        if( (double) $a['position'] === (double) $b['position'] ) return 0;
        return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
    } );

    foreach( $fields as $key => $f ) {
        $form .= $f['markup'];
    }

    $form .= '<input type="hidden" name="data[csrf]" value="' . $csrf . '" />
    <button>' . t( 'profile_button', "Edit Profile" ) . '</button>
    </form>

    </div>';

    return $form;

    } else return '<div class="info_form">' . t( 'unavailable_form', "This form is only for logged members!" ) . '</div>';

}

/* CHANGE PASSWORD FORM */

function change_password_form() {

    if( $GLOBALS['me'] ) {

    $form = '<div class="change_password_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['change_password_form'] ) && \site\utils::check_csrf( $_POST['change_password_form']['csrf'], 'change_password_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['change_password_form'] );

    try {

        \user\main::change_password( $GLOBALS['me']->ID, $pd );

        $form .= '<div class="success">' . t( 'change_pwd_success', "Your password has been changed." ) . '</div>';

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    }

    $csrf = $_SESSION['change_password_csrf'] = \site\utils::str_random(12);

    $form .= '<form action="#" method="POST" autocomplete="off">
    <div class="form_field"><label for="change_password_form[old]">' . t( 'change_pwd_form_old', "Old Password" ) . ':</label> <div><input type="password" name="change_password_form[old]" id="change_password_form[old]" value="" required /></div></div>
    <div class="form_field"><label for="change_password_form[new]">' . t( 'change_pwd_form_new', "New Password" ) . ':</label> <div><input type="password" name="change_password_form[new]" id="change_password_form[new]" value="" required /></div></div>
    <div class="form_field"><label for="change_password_form[new2]">' . t( 'change_pwd_form_new2', "Confirm New Password" ) . ':</label> <div><input type="password" name="change_password_form[new2]" id="change_password_form[new2]" value="" required /></div></div>
    <input type="hidden" name="change_password_form[csrf]" value="' . $csrf . '" />
    <button>' . t( 'change_pwd_button', "Change Password" ) . '</button>
    </form>

    </div>';

    return $form;

    } else return '<div class="info_form">' . t( 'unavailable_form', "This form is only for logged members!" ) . '</div>';

}

/* GET REWARD FORM */

function create_reward_request( $item ) {

    if( $GLOBALS['me'] ) {

    $form = '';

    if( $item->points <= $GLOBALS['me']->Points ) {

    $form = '<div class="claim_reward_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['submit_reward_request_' . $item->ID . '_form'] ) && \site\utils::check_csrf( $_POST['submit_reward_request_' . $item->ID . '_form']['csrf'], 'submit_reward_request_' . $item->ID . '_csrf' ) ) {

    $post = empty( $post ) ? ( isset( $_POST['Reward'][$item->ID] ) ? (array) $_POST['Reward'][$item->ID] : '' ) : $post;

     try {

        \user\main::get_reward( $item->ID, $post );

        $form .= '<div class="success">' . t( 'claim_reward_success', "The request has been successfully sent." ) . '</div>';

        unset( $_POST );

    }

    catch( Exception $e ) {
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    }

    $csrf = $_SESSION['submit_reward_request_' . $item->ID . '_csrf'] = \site\utils::str_random(12);

    $form .= '<form action="#" method="POST" autocomplete="off">';

    if( !empty( $item->fields ) ) {
        $form .= '<div class="extra_form">';
            foreach( $item->fields as $v ) {
                $form .= '<div class="form_field"' . ( $v['type'] == 'hidden' ? ' style="display:none;"' : '' ) . '><label for="' . $v['name'] . '">' . ts( $v['name'] ) . ( $v['require'] ? '*' : '' ) . ':</label> <div>
                <input type="' . $v['type'] . '" name="Reward[' . $item->ID . '][' . $v['name'] . ']" id="' . $v['name'] . '" value=""' . ( $v['require'] ? ' required' : '' ) . ' /></div></div>';
            }
        $form .= '</div>';
    }

    $form .= '<input type="hidden" name="submit_reward_request_' . $item->ID . '_form[csrf]" value="' . $csrf . '" />
    <button>' . t( 'claim_now', 'Claim' ) . '</button>';

    $form .= '</form>';

    $form .= '</div>';

    }

    return $form;

    } else return '<div class="info_form">' . t( 'unavailable_form', "This form is only for logged members!" ) . '</div>';

}

/* CHECK COUPON CODE FORM */

function check_coupon_code() {

    if( $GLOBALS['me'] ) {

    $form = '';

    $form = '<div class="check_coupon_code_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' || ( isset( $_GET['code'] ) && !isset( $_GET['set_unused'] ) ) ) {

        $code = '';

        if( isset( $_POST['code'] ) ) {
            $code = $_POST['code'];
        } else if( isset( $_GET['code'] ) ) {
            $code = $_GET['code'];
        }

         try {

            $info   = \user\main::check_coupon_code( $code );

            if( $info['used'] )
                $form   .= '<div class="alert">' . sprintf( t( 'check_coupon_used', 'This is a valid coupon code. Used: %s' ), $info['used_date'] ) . '</div>';
            else
            $form   .= '<div class="success">' . t( 'check_coupon_success', 'This is a valid coupon code, it has been set automatically as "Used". If you want to undo this action please click on the button below.' ) . '</div>';
            $form   .= '<div class="button-info"><a href="' . get_update( array( 'code' => $code, 'set_unused' => true ) ) . '" class="butt btn">' . t( 'check_coupon_unused_button', 'Set as "Unused".' ) . '</a></div>';

            unset( $_POST );

        }

        catch( Exception $e ) {
            $form .= '<div class="error">' . $e->getMessage() . '</div>';
        }

    } else if( isset( $_GET['set_unused'] ) ) {

        try {

            \user\main::set_coupon_code_unused( $_GET['code'] );

            $form .= '<div class="success">' . t( 'check_coupon_unused_success', 'Coupon set as "Unused".' ) . '</div>';

        }

        catch( Exception $e ) {
            $form .= '<div class="error">' . $e->getMessage() . '</div>';
        }

    }

    $form .= '<form action="#" method="GET" autocomplete="off">';

    $form .= '<div class="form_field"><label for="code">' . t( 'form_verify_code', "Coupon code" ) . ':</label> <div><input type="text" name="code" id="code" value="' . ( isset( $pd['code'] ) ? $pd['code'] : ( isset( $_GET['view_code'] ) ? esc_html( $_GET['view_code'] ) : '' ) ) . '" maxlength="255" required /></div></div>';

    $form .= '<button>' . t( 'verify_code', 'Verify Code' ) . '</button>';

    $form .= '</form>';

    $form .= '</div>';

    return $form;

    } else return '<div class="info_form">' . t( 'unavailable_form2', "This form is only for store/brand owners." ) . '</div>';

}

/* SUBMIT NEW COUPON FORM */

function submit_coupon_form( $auto_select = array( 'store' => '' ) ) {

    if( $GLOBALS['me'] ) {

    if( $GLOBALS['me']->Stores > 0 ) {

    if( ! (boolean) \query\main::get_option( 'allow_coupons' ) ) {

        return '<div class="info_form">' . t( 'submit_cou_not_allowed', "New coupons are not allowed at this time." ) . '</div>';

    }

    $pd = array();

    $form = '<div class="submit_coupon_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['data']['csrf'] ) && \site\utils::check_csrf( $_POST['data']['csrf'], 'submit_coupon_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['data'] );
    $pd['extra'] = value_with_filter( 'user_save_coupon_extra_fields', ( isset( $pd['extra'] ) ? \site\utils::array_sanitize( $pd['extra'] ) : array() ), array() );

    try {

        \user\main::submit_coupon( $pd );

        $form .= '<div class="success">' . t( 'submit_cou_success', "The coupon has been added." ) . '</div>';

        unset( $pd );

    }

    catch( Exception $e ) {
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    }

    $csrf = $_SESSION['submit_coupon_csrf'] = \site\utils::str_random(12);

    $form .= '<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">';

    // Store
    $store = '<div class="form_field"><label for="data[store]">' . t( 'submit_cou_addto', "Add to" ) . ':</label>
    <div><select name="data[store]" id="data[store]" data-switch-store="">';
    foreach( stores_custom( array( 'user' => $GLOBALS['me']->ID, 'max' => 0 ), '', array( 'no_emoticons' => true, 'no_filters' => true ) ) as $v ) {
        $store .= '<option value="' . $v->ID . '" isPhysical="' . $v->is_physical . '" sellOnline="' . $v->sellonline . '"' . ( !isset( $pd['store'] ) || ( isset( $pd['store'] ) && $pd['store'] == $v->ID ) ? ' selected' : '' ) . '>' . ts( $v->name ) . '</option>';
    }
    $store .= '</select></div>
    </div>';

    $fields['store']['position'] = 1;
    $fields['store']['markup'] = $store;

    // Category
    $category_markup = '<div class="form_field"><label for="data[category]">' . t( 'form_category', "Category" ) . ':</label>
    <div><select name="data[category]" id="data[category]">';
    $category = isset( $pd['category'] ) ? $pd['category'] : 0;
    foreach( \query\main::group_categories( array( 'max' => 0 ), array( 'no_emoticons' => true, 'no_filters' => true ) ) as $cat ) {
        $category_markup .= '<optgroup label="' . $cat['info']->name . '">';
        $category_markup .= '<option value="' . $cat['info']->ID . '"' . ( $category == $cat['info']->ID ? ' selected' : '' ) . '>' . $cat['info']->name . '</option>';
        if( isset( $cat['subcats'] ) ) {
            foreach( $cat['subcats'] as $subcat ) {
                $category_markup .= '<option value="' . $subcat->ID . '"' . ( $category == $subcat->ID ? ' selected' : '' ) . '>' . $subcat->name . '</option>';
            }
        }
        $category_markup .= '</optgroup>';
    }
    $category_markup .= '</select></div>
    </div>';

    $fields['category']['position'] = 2;
    $fields['category']['markup'] = $category_markup;

    // Title
    $fields['title']['position'] = 3;
    $fields['title']['markup'] = '<div class="form_field"><label for="data[name]">' . t( 'form_name', "Name" ) . ':</label> <div><input type="text" name="data[name]" id="data[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : '' ) . '" maxlength="255" placeholder="' . t( 'submit_cou_name_ph', "Coupon name" ) . '" required /></div></div>';

    // Coupon type
    $type_markup = '<div class="form_field" data-showSS style="display:none;"><label for="data[type]">' . t( 'form_type', "Type" ) . ':</label>
    <div><select name="data[type]" id="data[type]" data-change-type>';
    foreach( array( 0 => t( 'dealnrprint', "Deal (not require printing)" ), 1 => t( 'printhtml', "Printable (HTML version)" ), 2 => t( 'printsource', "Printable (using source)" ),  3 => t( 'showinstore', "Show In Store" ) ) as $type_id => $type_name ) {
        $type_markup .= '<option value="' . $type_id . '"' . ( $type_id === 0 ? ' selected' : '' ) . '>' . $type_name . '</option>';
    }
    $type_markup .= '</select></div></div>';

    $fields['type']['position'] = 4;
    $fields['type']['markup'] = $type_markup;

    // Coupon source
    $fields['source']['position'] = 5;
    $fields['source']['markup'] = '<div class="form_field" data-source style="display:none;"><label for="data_source">' . t( 'form_source', "Source" ) . ':</label> <div>
    <input type="file" name="data_source" id="data_source" class="inputFile" /> <label for="data_source" class="fileUpload"></label></div></div>';

    // Store sell online !?
    $fields['sell_online']['position'] = 6;
    $fields['sell_online']['markup'] = '<div class="form_field" data-avbl-online style="display:none;"><span>' . t( 'form_avabonline', "Available Online" ) . ':</span><div>
    <input type="checkbox" name="data[avbl_online]" id="data[avbl_online]" class="inputCheckbox" data-is_online value="1" /> <label for="data[avbl_online]"><span></span> ' . t( 'coupons_avabonline', "This coupon is available online" ) . '</label></div></div>';

    // Limit / How many coupons can be used
    $fields['limit']['position'] = 7;
    $fields['limit']['markup'] = '<div class="form_field" data-limit style="display:none;"><label for="data[limit]">' . t( 'form_limit', "Limit" ) . ':</label> <div><input type="text" name="data[limit]" id="data[limit]" value="' . ( isset( $pd['limit'] ) ? $pd['limit'] : '' ) . '" placeholder="' . t( 'msg_showinstore', "Maximum number of coupons that can be claimed for this coupon. 0 = unlimited." ) . '" /></div></div>';

    // Coupon code
    $fields['code']['position'] = 8;
    $fields['code']['markup'] = '<div class="form_field" data-hideSS data-coupon-code><label for="data[code]">' . t( 'form_code', "Code" ) . ':</label> <div><input type="text" name="data[code]" id="data[code]" value="' . ( isset( $pd['code'] ) ? $pd['code'] : '' ) . '" placeholder="' . t( 'submit_cou_code_ph', "Coupon code, please leave it blank if not necessary." ) . '" /></div></div>';

    // Coupon url
    $fields['url']['position'] = 9;
    $fields['url']['markup'] = '<div class="form_field" data-hideSS data-coupon-url><label for="data[url]">' . t( 'form_coupon_url', "Coupon URL" ) . ':</label> <div><input type="text" name="data[url]" id="data[url]" value="' . ( isset( $pd['url'] ) ? $pd['url'] : '' ) . '" placeholder="' . t( 'submit_cou_url_ph', "Optional. Leave it blank if it is not a special page for this offer." ) . '" /></div></div>';

    // Description
    $fields['description']['position'] = 10;
    $fields['description']['markup'] = '<div class="form_field"><label for="data[description]">' . t( 'form_description', "Description" ) . ':</label> <div><textarea name="data[description]" id="data[description]" style="height:100px;">' . ( isset( $pd['description'] ) ? $pd['description'] : '' ) . '</textarea></div></div>';

    // Tags
    $fields['tags']['position'] = 11;
    $fields['tags']['markup'] = '<div class="form_field"><label for="data[tags]">' . t( 'form_tags', "Tags" ) . ':</label> <div><input type="text" name="data[tags]" id="data[tags]" value="' . ( isset( $pd['tags'] ) ? $pd['tags'] : '') . '" /></div></div>';

    // Coupon image
    $fields['image']['position'] = 12;
    $fields['image']['markup'] = '<div class="form_field"><label for="data_image">' . t( 'form_image', "Image" ) . ':</label> <div><input type="file" name="data_image" id="data_image" class="inputFile" /><label for="data_image" class="fileUpload"></label></div></div>';

    // Start date
    $fields['start_date']['position'] = 13;
    $fields['start_date']['markup'] = '<div class="form_field"><label for="data[start]">' . t( 'form_start_date', "Start Date" ) . ':</label> <div><input type="date" name="data[start]" id="data[start]" value="' . ( isset( $pd['start'] ) ? $pd['start'] : '' ) . '" style="width:79%;margin-right:1%;" /><input type="time" name="data[start_hour]" value="' . ( isset( $pd['start_hour'] ) ? $pd['start_hour'] : '00:00' ) . '" style="width:20%" /></div></div>';

    // End date
    $fields['end_date']['position'] = 14;
    $fields['end_date']['markup'] = '<div class="form_field"><label for="data[end]">' . t( 'form_end_date', "End Date" ) . ':</label> <div><input type="date" name="data[end]" id="data[end]" value="' . ( isset( $pd['end'] ) ? $pd['end'] : '' ) . '" style="width: 79%;margin-right:1%;" /><input type="time" name="data[end_hour]" value="' . ( isset( $pd['end_hour'] ) ? $pd['end_hour'] : '00:00' ) . '" style="width:20%" /></div></div>';

    // Sponsored
    $price  = coupon_price();
    $option = '<option value="">' . t( 'sponsored_no_upgrade_coupon', 'Unsponsored, this coupon will appear after sponsored coupons in search results.' ) . '</option>';
    for( $i = 1; $i <= 30; $i++ ) {
        $option .= '<option value="' . $i . '">' . sprintf( t( 'sponsored_days', 'Until %s (%s credits)' ), date( 'd F y H:i', strtotime( '+' . $i . ' day', time() ) ), ( $i * $price ) ) . '</option>' . "\n";
    }
    $fields['supported']['position'] = 15;
    $fields['supported']['markup'] = '<div class="form_field"><label for="data[sponsored]">' . t( 'form_sponsored', "Sponsored" ) . ':</label>
        <div>
            <select name="data[sponsored]" id="data[sponsored]">
                ' . $option . '
            </select>
            <span>' . t( 'form_sponsored_info', 'Sponsored products appears first in search results' ) . '</span>
        </div>
    </div>';

    global $add_form_fields;

    $custom_fields = array();
    if( !empty( $add_form_fields['coupon'] ) && is_array( $add_form_fields['coupon'] ) ) {
        foreach( $add_form_fields['coupon'] as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $custom_fields[$id]['markup'] = \site\fields::build_extra( $link['fields'], ( isset( $pd['extra'] ) ? $pd['extra'] : array() ), 'data[extra]' );
        }
    }

    $fields = value_with_filter( 'default_user_coupon_fields', $fields ) + $custom_fields;

    uasort( $fields, function( $a, $b ) {
        if( (double) $a['position'] === (double) $b['position'] ) return 0;
        return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
    } );

    foreach( $fields as $key => $f ) {
        $form .= $f['markup'];
    }

    $form .= '<input type="hidden" name="data[csrf]" value="' . $csrf . '" />
    <button>' . t( 'submit_cou_button', "Add Coupon" ) . '</button>
    </form>

    </div>

    <script src="' . $GLOBALS['siteURL'] . PDIR . '/js/oncheck.js"></script>
    <script src="' . $GLOBALS['siteURL'] . PDIR . '/js/owner.js"></script>

    <script>
    $( document ).ready(function() {

    $( "select[data-switch-store]" ).switch_store_from_coupon( {onLoad: true} );
    $( "select[data-switch-store]" ).change_coupon_type();
    $( "input[data-is_online]" ).oncheck( {Elements: "[data-coupon-code],[data-coupon-url]"} );

    });
    </script>';

    return $form;

    } else return '<div class="info_form">' . t( 'unavailable_form2', "This form is only for store/brand owners." ) . '</div>';

    } else return '<div class="info_form">' . t( 'unavailable_form', "This form is only for logged members!" ) . '</div>';

}

/* EDIT COUPON FORM */

function edit_coupon_form( $id ) {

    if( $GLOBALS['me'] ) {

    if( $GLOBALS['me']->Stores > 0 ) {

    $coupon = \query\main::item_info( $id, array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

    if( !\query\main::have_store( $coupon->storeID, $GLOBALS['me']->ID ) ) {

        return '<div class="info_form">' . t( 'edit_cou_cant', "You don't own this coupon." ) . '</div>';

    }

    $form = '<div class="edit_coupon_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['data']['csrf'] ) && \site\utils::check_csrf( $_POST['data']['csrf'], 'edit_coupon_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['data'] );
    $pd['extra'] = array_merge( (array) $coupon->extra, value_with_filter( 'user_save_coupon_extra_fields', ( isset( $pd['extra'] ) ? \site\utils::array_sanitize( $pd['extra'] ) : array() ), array() ) );

    try {

        \user\main::edit_coupon( $id, $pd );

        $form .= '<div class="success">' . t( 'edit_cou_success', "The coupon has been updated." ) . '</div>';

        // check again information about this item

        $coupon = \query\main::item_info( $id, array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

        unset( $pd );

    }

    catch( Exception $e ) {
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    }

    $csrf = $_SESSION['edit_coupon_csrf'] = \site\utils::str_random(12);

    $fields = array();

    $form .= '<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">';

    // Store
    $store = '<div class="form_field"><label for="data[store]">' . t( 'submit_cou_addto', "Add to" ) . ':</label>
    <div><select name="data[store]" id="data[store]" data-switch-store="">';
    foreach( stores_custom( array( 'user' => $GLOBALS['me']->ID, 'max' => 0 ), '', array( 'no_emoticons' => true, 'no_filters' => true ) ) as $v ) {
        $store .= '<option value="' . $v->ID . '" isPhysical="' . $v->is_physical . '" sellOnline="' . $v->sellonline . '"' . ( ( !isset( $pd['store'] ) && $coupon->storeID == $v->ID ) || ( isset( $pd['store'] ) && $pd['store'] == $v->ID ) ? ' selected' : '' ) . '>' . ts( $v->name ) . '</option>';
    }
    $store .= '</select></div>
    </div>';

    $fields['store']['position'] = 1;
    $fields['store']['markup'] = $store;

    // Category
    $category_markup = '<div class="form_field"><label for="data[category]">' . t( 'form_category', "Category" ) . ':</label>
    <div><select name="data[category]" id="data[category]">';
    $category = isset( $pd['category'] ) ? $pd['category'] : $coupon->catID;
    foreach( \query\main::group_categories( array( 'max' => 0 ), array( 'no_emoticons' => true, 'no_filters' => true ) ) as $cat ) {
        $category_markup .= '<optgroup label="' . ts( $cat['info']->name ) . '">';
        $category_markup .= '<option value="' . $cat['info']->ID . '"' . ( $category == $cat['info']->ID ? ' selected' : '' ) . '>' . ts( $cat['info']->name ) . '</option>';
        if( isset( $cat['subcats'] ) ) {
            foreach( $cat['subcats'] as $subcat ) {
                $category_markup .= '<option value="' . $subcat->ID . '"' . ( $category == $subcat->ID ? ' selected' : '' ) . '>' . ts( $subcat->name ) . '</option>';
            }
        }
        $category_markup .= '</optgroup>';
    }
    $category_markup .= '</select></div>
    </div>';

    $fields['category']['position'] = 2;
    $fields['category']['markup'] = $category_markup;

    // Title
    $fields['title']['position'] = 3;
    $fields['title']['markup'] = '<div class="form_field"><label for="data[name]">' . t( 'form_name', "Name" ) . ':</label> <div><input type="text" name="data[name]" id="data[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : $coupon->title ) . '" maxlength="255" placeholder="' . t( 'submit_cou_name_ph', "Coupon name" ) . '" required /></div></div>';

    // Coupon type
    $type_markup = '<div class="form_field" data-showSS' . ( !$coupon->store_is_physical ? ' style="display:none;"' : '' ) . '><label for="data[type]">' . t( 'form_type', "Type" ) . ':</label>
    <div><select name="data[type]" id="data[type]" data-change-type>';
    $type = 0;
    if( $coupon->is_printable ) {
        if( empty( $coupon->source ) ) {
            $type = 1;
        } else {
            $type = 2;
        }
    } else if( !empty( $coupon->is_show_in_store ) ) {
        $type = 3;
    }
    foreach( array( 0 => t( 'dealnrprint', "Deal (not require printing)" ), 1 => t( 'printhtml', "Printable (HTML version)" ), 2 => t( 'printsource', "Printable (using source)" ), 3 => t( 'showinstore', "Show In Store" ) ) as $type_id => $type_name ) {
        $type_markup .= '<option value="' . $type_id . '"' . ( $type === $type_id ? ' selected' : '' ) . '>' . $type_name . '</option>';
    }
    $type_markup .= '</select></div></div>';

    $fields['type']['position'] = 4;
    $fields['type']['markup'] = $type_markup;

    // Coupon source
    $source = '<div class="form_field" data-source' . ( $type !== 2 ? ' style="display:none;"' : '' ) . '><label for="data_source">' . t( 'form_source', "Source" ) . ':</label> <div>';
    if( $coupon->is_local_source ) {
        $source .= '<img src="' . $coupon->source . '" alt="" class="form_source_avatar" style="max-width:80px;height:80px;" />';
    }
    $source .= '<input type="file" name="data_source" id="data_source" class="inputFile" /><label for="data_source" class="fileUpload"></label></div></div>';

    $fields['source']['position'] = 5;
    $fields['source']['markup'] = $source;

    // Store sell online !?
    $fields['sell_online']['position'] = 6;
    $fields['sell_online']['markup'] = '<div class="form_field" data-avbl-online' . ( $type !== 0 || ( $coupon->store_is_physical && !$coupon->store_sellonline ) || !$coupon->store_is_physical ? ' style="display:none;"' : '' ) . '><span>' . t( 'form_avabonline', "Available Online" ) . ':</span><div>
    <input type="checkbox" name="data[avbl_online]" id="data[avbl_online]" class="inputCheckbox" data-is_online value="1"' . ( $type === 0 && $coupon->is_available_online ? ' checked' : '' ) . ' /> <label for="data[avbl_online]"><span></span> ' . t( 'coupons_avabonline', "This coupon is available online" ) . '</label></div></div>';

    // Limit / How many coupons can be used
    $fields['limit']['position'] = 7;
    $fields['limit']['markup'] = '<div class="form_field" data-limit' . ( !$coupon->store_is_physical || $type !== 3 ? ' style="display:none;"' : '' ) . '><label for="data[limit]">' . t( 'form_limit', "Limit" ) . ':</label> <div><input type="text" name="data[limit]" id="data[limit]" value="' . ( isset( $pd['limit'] ) ? $pd['limit'] : $coupon->claim_limit ) . '" placeholder="' . t( 'msg_showinstore', "Maximum number of coupons that can be claimed for this coupon. 0 = unlimited." ) . '" /></div></div>';

    // Coupon code
    $fields['code']['position'] = 8;
    $fields['code']['markup'] = '<div class="form_field" data-hideSS data-coupon-code' . ( $type !== 0 || ( $coupon->store_is_physical && !$coupon->store_sellonline ) || !$coupon->is_available_online ? ' style="display:none;"' : '' ) . '><label for="data[code]">' . t( 'form_code', "Code" ) . ':</label> <div><input type="text" name="data[code]" id="data[code]" value="' . ( isset( $pd['code'] ) ? $pd['code'] : $coupon->code ) . '" placeholder="' . t( 'submit_cou_code_ph', "Coupon code, please leave it blank if not necessary." ) . '" /></div></div>';

    // Coupon url
    $fields['url']['position'] = 9;
    $fields['url']['markup'] = '<div class="form_field" data-hideSS data-coupon-url' . ( $type !== 0 || ( $coupon->store_is_physical && !$coupon->store_sellonline ) || !$coupon->is_available_online ? ' style="display:none;"' : '' ) . '><label for="data[url]">' . t( 'form_coupon_url', "Coupon URL" ) . ':</label> <div><input type="text" name="data[url]" id="data[url]" value="' . ( isset( $pd['url'] ) ? $pd['url'] : $coupon->original_url ) . '" placeholder="' . t( 'submit_cou_url_ph', "Optional. Leave it blank if it is not a special page for this offer." ) . '" /></div></div>';

    // Description
    $fields['description']['position'] = 10;
    $fields['description']['markup'] = '<div class="form_field"><label for="data[description]">' . t( 'form_description', "Description" ) . ':</label> <div><textarea name="data[description]" id="data[description]" style="height:100px;">' . ( isset( $pd['description'] ) ? $pd['description'] : $coupon->description ) . '</textarea></div></div>';

    // Tags
    $fields['tags']['position'] = 11;
    $fields['tags']['markup'] = '<div class="form_field"><label for="data[tags]">' . t( 'form_tags', "Tags" ) . ':</label> <div><input type="text" name="data[tags]" id="data[tags]" value="' . ( isset( $pd['tags'] ) ? $pd['tags'] : $coupon->tags ) . '" /></div></div>';

    // Coupon image
    $image = '<div class="form_field"><label for="data_image">' . t( 'form_image', "Image" ) . ':</label> <div>';
    if( !empty( $coupon->image ) ) {
        $image .= '<img src="' . image( $coupon->image ) . '" alt="" style="max-width:150px;max-height:50px;" />';
    }
    $image .= '<input type="file" name="data_image" id="data_image" class="inputFile" /><label for="data_image" class="fileUpload"></label></div></div>';

    $fields['image']['position'] = 12;
    $fields['image']['markup'] = $image;

    // Start date
    $fields['start_date']['position'] = 13;
    $fields['start_date']['markup'] = '<div class="form_field"><label for="data[start]">' . t( 'form_start_date', "Start Date" ) . ':</label> <div><input type="date" name="data[start]" id="data[start]" value="' . ( isset( $pd['start'] ) ? $pd['start'] : date( 'Y-m-d', strtotime( $coupon->start_date ) ) ) . '" style="width:79%;margin-right:1%;" /><input type="time" name="data[start_hour]" value="' . ( isset( $pd['start_hour'] ) ? $pd['start_hour'] : date( 'H:i', strtotime( $coupon->start_date ) ) ) . '" style="width:20%;" /></div></div>';

    // End date
    $fields['end_date']['position'] = 14;
    $fields['end_date']['markup'] = '<div class="form_field"><label for="data[end]">' . t( 'form_end_date', "End Date" ) . ':</label> <div><input type="date" name="data[end]" id="data[end]" value="' . ( isset( $pd['end'] ) ? $pd['end'] : date( 'Y-m-d', strtotime( $coupon->expiration_date ) ) ) . '" style="width:79%;margin-right:1%;" /><input type="time" name="data[end_hour]" value="' . ( isset( $pd['end_hour'] ) ? $pd['end_hour'] : date( 'H:i', strtotime( $coupon->expiration_date ) ) ) . '" style="width:20%;" /></div></div>';

    // Sponsored
    $price  = coupon_price();
    $time   = ( $sActive = ( $coupon->paid_until && strtotime( $coupon->paid_until ) > time() ) ) ? strtotime( $coupon->paid_until ) : time();
    $option = '<option value="">' . ( !$sActive ? t( 'sponsored_no_upgrade_coupon', 'Unsponsored, this coupon will appear after sponsored coupons in search results.' ) : sprintf( t( 'sponsored_until', 'Sponsored until %s' ), date( 'd F y H:i', $time ) ) ) . '</option>';
    for( $i = 1; $i <= 30; $i++ ) {
        $option .= '<option value="' . $i . '">' . sprintf( t( 'sponsored_days', 'Until %s (%s credits)' ), date( 'd F y H:i', strtotime( '+' . $i . ' day', $time ) ), ( $i * $price ) ) . '</option>' . "\n";
    }
    $fields['supported']['position'] = 15;
    $fields['supported']['markup'] = '<div class="form_field"><label for="data[sponsored]">' . t( 'form_sponsored', "Sponsored" ) . ':</label>
        <div>
            <select name="data[sponsored]" id="data[sponsored]">
                ' . $option . '
            </select>
            <span>' . t( 'form_sponsored_info', 'Sponsored products appears first in search results' ) . '</span>
        </div>
    </div>';
    
    global $add_form_fields;

    $custom_fields = array();
    if( !empty( $add_form_fields['coupon'] ) && is_array( $add_form_fields['coupon']) ) {
        foreach( $add_form_fields['coupon'] as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $custom_fields[$id]['markup'] = \site\fields::build_extra( $link['fields'], ( isset( $coupon->extra ) && is_array( $coupon->extra ) ? $coupon->extra : array() ), 'data[extra]', $coupon );
        }
    }

    $fields = value_with_filter( 'default_user_coupon_fields', $fields ) + $custom_fields;

    uasort( $fields, function( $a, $b ) {
        if( (double) $a['position'] === (double) $b['position'] ) return 0;
        return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
    } );

    foreach( $fields as $key => $f ) {
        $form .= $f['markup'];
    }

    $form .= '<input type="hidden" name="data[csrf]" value="' . $csrf . '" />
    <button>' . t( 'edit_cou_button', "Edit Coupon" ) . '</button>
    </form>

    </div>

    <script src="' . $GLOBALS['siteURL'] . PDIR . '/js/oncheck.js"></script>
    <script src="' . $GLOBALS['siteURL'] . PDIR . '/js/owner.js"></script>

    <script>
    $( document ).ready(function() {

    $( "select[data-switch-store]" ).switch_store_from_coupon();
    $( "select[data-switch-store]" ).change_coupon_type();
    $( "input[data-is_online]" ).oncheck( {Elements: "[data-coupon-code],[data-coupon-url]"} );

    });
    </script>';

    return $form;

    } else return '<div class="info_form">' . t( 'unavailable_form2', "This form is only for store/brand owners." ) . '</div>';

    } else return '<div class="info_form">' . t( 'unavailable_form', "This form is only for logged members!" ) . '</div>';

}

/* SUBMIT NEW PRODUCT FORM */

function submit_product_form( $auto_select = array( 'store' => '' ) ) {

    if( $GLOBALS['me'] ) {

    if( $GLOBALS['me']->Stores > 0 ) {

    if( ! (boolean) \query\main::get_option( 'allow_products' ) ) {

        return '<div class="info_form">' . t( 'submit_prod_not_allowed', "New products are not allowed at this time." ) . '</div>';

    }

    $pd = array();

    $form = '<div class="submit_product_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['data']['csrf'] ) && \site\utils::check_csrf( $_POST['data']['csrf'], 'submit_product_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['data'] );
    $pd['extra'] = value_with_filter( 'user_save_product_extra_fields', ( isset( $pd['extra'] ) ? \site\utils::array_sanitize( $pd['extra'] ) : array() ), array() );

    try {

        \user\main::submit_product( $pd );

        $form .= '<div class="success">' . t( 'submit_prod_success', "The product has been added." ) . '</div>';

        unset( $pd );

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    }

    $csrf = $_SESSION['submit_product_csrf'] = \site\utils::str_random(12);

    $form .= '<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">';

    // Store
    $store = '<div class="form_field"><label for="data[store]">' . t( 'submit_prod_addto', "Add to" ) . ':</label>
    <div><select name="data[store]" id="data[store]">';
    foreach( stores_custom( array( 'user' => $GLOBALS['me']->ID, 'max' => 0 ), '', array( 'no_emoticons' => true, 'no_filters' => true ) ) as $v ) {
        $store .= '<option value="' . $v->ID . '"' . ( ( !isset( $pd['store'] ) && !empty( $auto_select['store'] ) && ( $auto_select['store'] == $v->ID || $auto_select['store'] == $v->name ) ) || ( isset( $pd['store'] ) && $pd['store'] == $v->ID ) ? ' selected' : '' ) . '>' . ts( $v->name ) . '</option>';
    }
    $store .= '</select></div>
    </div>';

    $fields['store']['position'] = 1;
    $fields['store']['markup'] = $store;

    // Category
    $category_markup = '<div class="form_field"><label for="data[category]">' . t( 'form_category', "Category" ) . ':</label>
    <div><select name="data[category]" id="data[category]">';
    foreach( \query\main::group_categories( array( 'max' => 0 ), array( 'no_emoticons' => true, 'no_filters' => true ) ) as $cat ) {
        $category_markup .= '<optgroup label="' . ts( $cat['info']->name ) . '">';
        $category_markup .= '<option value="' . $cat['info']->ID . '"' . ( isset( $pd['category'] ) && $pd['category']== $cat['info']->ID ? ' selected' : '' ) . '>' . ts( $cat['info']->name ) . '</option>';
        if( isset( $cat['subcats'] ) ) {
            foreach( $cat['subcats'] as $subcat ) {
              $category_markup .= '<option value="' . $subcat->ID . '"' . ( isset( $pd['category'] ) && $pd['category']== $cat['info']->ID ? ' selected' : '' ) . '>' . ts( $subcat->name ) . '</option>';
            }
        }
        $category_markup .= '</optgroup>';
    }
    $category_markup .= '</select></div>
    </div>';

    $fields['category']['position'] = 2;
    $fields['category']['markup'] = $category_markup;

    // Name
    $fields['name']['position'] = 3;
    $fields['name']['markup'] = '<div class="form_field"><label for="data[name]">' . t( 'form_name', "Name" ) . ':</label> <div><input type="text" name="data[name]" id="data[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : '' ) . '" maxlength="255" placeholder="' . t( 'submit_prod_name_ph', "Product name" ) . '" required /></div></div>';

    // Price
    $fields['price']['position'] = 4;
    $fields['price']['markup'] = '<div class="form_field"><label for="data[price]">' . t( 'form_price', "Price" ) . ':</label> <div><input type="text" name="data[price]" id="data[price]" value="' . ( isset( $pd['price'] ) ? $pd['price'] : '' ) . '" placeholder="' . t( 'submit_prod_price_ph', "Selling price now, with discount." ) . '" /></div></div>';

    // Old price
    $fields['old_price']['position'] = 5;
    $fields['old_price']['markup'] = '<div class="form_field"><label for="data[old_price]">' . t( 'form_old_price', "Old Price" ) . ':</label> <div><input type="text" name="data[old_price]" id="data[old_price]" value="' . ( isset( $pd['old_price'] ) ? $pd['old_price'] : '' ) . '" placeholder="' . t( 'submit_prod_oldprice_ph', "Last price without discount." ) . '" /></div></div>';

    // Currency
    $fields['currency']['position'] = 6;
    $fields['currency']['markup'] = '<div class="form_field"><label for="data[currency]">' . t( 'currency', "Currency" ) . ':</label> <div><input type="text" name="data[currency]" id="data[currency]" value="' . ( isset( $pd['currency'] ) ? $pd['currency'] : CURRENCY ) . '" /></div></div>';

    // Link
    $fields['link']['position'] = 7;
    $fields['link']['markup'] = '<div class="form_field"><label for="data[url]">' . t( 'form_product_url', "Product URL" ) . ':</label> <div><input type="text" name="data[url]" id="data[url]" value="' . ( isset( $pd['url'] ) ? $pd['url'] : '' ) . '" /></div></div>';

    // Description
    $fields['description']['position'] = 8;
    $fields['description']['markup'] = '<div class="form_field"><label for="data[description]">' . t( 'form_description', "Description" ) . ':</label> <div><textarea name="data[description]" id="data[description]" style="height:100px;">' . ( isset( $pd['description'] ) ? $pd['description'] : '' ) . '</textarea></div></div>';

    // Tag
    $fields['tags']['position'] = 9;
    $fields['tags']['markup'] = '<div class="form_field"><label for="data[tags]">' . t( 'form_tags', "Tags" ) . ':</label> <div><input type="text" name="data[tags]" id="data[tags]" value="' . ( isset( $pd['tags'] ) ? $pd['tags'] : '' ) . '" /></div></div>';

    // Image
    $fields['image']['position'] = 10;
    $fields['image']['markup'] = '<div class="form_field"><label for="data_image">' . t( 'form_image', "Image" ) . ':</label> <div><input type="file" name="data_image" id="data_image" class="inputFile" /><label for="data_image" class="fileUpload"></label></div></div>';

    // Start date
    $fields['start_date']['position'] = 11;
    $fields['start_date']['markup'] = '<div class="form_field"><label for="data[start]">' . t( 'form_start_date', "Start Date" ) . ':</label> <div><input type="date" name="data[start]" id="data[start]" value="' . ( isset( $pd['start'] ) ? $pd['start'] : '' ) . '" style="width:79%;margin-right:1%;" /><input type="time" name="data[start_hour]" value="' . ( isset( $pd['start_hour'] ) ? $pd['start_hour'] : '00:00' ) . '" style="width:20%" /></div></div>';

    // End date
    $fields['end_date']['position'] = 12;
    $fields['end_date']['markup'] = '<div class="form_field"><label for="data[end]">' . t( 'form_end_date', "End Date" ) . ':</label> <div><input type="date" name="data[end]" id="data[end]" value="' . ( isset( $pd['end'] ) ? $pd['end'] : '' ) . '" style="width:79%;margin-right:1%;" /><input type="time" name="data[end_hour]" value="' . ( isset( $pd['end_hour'] ) ? $pd['end_hour'] : '00:00' ) . '" style="width:20%" /></div></div>';

    // Sponsored
    $price  = product_price();
    $option = '<option value="">' . t( 'sponsored_no_upgrade_product', 'Unsponsored, this product will appear after sponsored product in search results.' ) . '</option>';
    for( $i = 1; $i <= 30; $i++ ) {
        $option .= '<option value="' . $i . '">' . sprintf( t( 'sponsored_days', 'Until %s (%s credits)' ), date( 'd F y H:i', strtotime( '+' . $i . ' day', time() ) ), ( $i * $price ) ) . '</option>' . "\n";
    }
    $fields['supported']['position'] = 15;
    $fields['supported']['markup'] = '<div class="form_field"><label for="data[sponsored]">' . t( 'form_sponsored', "Sponsored" ) . ':</label>
        <div>
            <select name="data[sponsored]" id="data[sponsored]">
                ' . $option . '
            </select>
            <span>' . t( 'form_sponsored_info', 'Sponsored products appears first in search results' ) . '</span>
        </div>
    </div>';

    global $add_form_fields;

    $custom_fields = array();
    if( !empty( $add_form_fields['product'] ) && is_array( $add_form_fields['product']) ) {
        foreach( $add_form_fields['product'] as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $custom_fields[$id]['markup'] = \site\fields::build_extra( $link['fields'], ( isset( $pd['extra'] ) ? $pd['extra'] : array() ), 'data[extra]' );
        }
    }

    $fields = value_with_filter( 'default_user_product_fields', $fields ) + $custom_fields;

    uasort( $fields, function( $a, $b ) {
        if( (double) $a['position'] === (double) $b['position'] ) return 0;
        return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
    } );

    foreach( $fields as $key => $f ) {
        $form .= $f['markup'];
    }

    $form .= '<input type="hidden" name="data[csrf]" value="' . $csrf . '" />
    <button>' . t( 'submit_prod_button', "Add Product" ) . '</button>
    </form>

    </div>';

    return $form;

    } else return '<div class="info_form">' . t( 'unavailable_form2', "This form is only for store/brand owners." ) . '</div>';

    } else return '<div class="info_form">' . t( 'unavailable_form', "This form is only for logged members!" ) . '</div>';

}

/* EDIT PRODUCT FORM */

function edit_product_form( $id ) {

    if( $GLOBALS['me'] ) {

    if( $GLOBALS['me']->Stores > 0 ) {

    $product = \query\main::product_info( $id, array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

    if( !\query\main::have_store( $product->storeID, $GLOBALS['me']->ID ) ) {

        return '<div class="info_form">' . t( 'edit_prod_cant', "You don't own this product." ) . '</div>';

    }

    /* */

    $form = '<div class="edit_product_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['data']['csrf'] ) && \site\utils::check_csrf( $_POST['data']['csrf'], 'edit_product_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['data'] );
    $pd['extra'] = value_with_filter( 'user_save_product_extra_fields', ( isset( $pd['extra'] ) ? \site\utils::array_sanitize( $pd['extra'] ) : array() ), array() );

    try {

        \user\main::edit_product( $id, $pd );

        $form .= '<div class="success">' . t( 'edit_prod_success', "The product has been updated." ) . '</div>';

        // check again information about this item

        $product = \query\main::product_info( $id, array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    }

    $csrf = $_SESSION['edit_product_csrf'] = \site\utils::str_random(12);

    $form .= '<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">';

    // Store
    $store = '<div class="form_field"><label for="data[store]">' . t( 'submit_prod_addto', "Add to" ) . ':</label>
    <div><select name="data[store]" id="data[store]">';
    foreach( stores_custom( array( 'user' => $GLOBALS['me']->ID, 'max' => 0 ), '', array( 'no_emoticons' => true, 'no_filters' => true ) ) as $v ) {
        $store .= '<option value="' . $v->ID . '"' . ( ( !isset( $pd['store'] ) && $product->storeID == $v->ID ) || ( isset( $pd['store'] ) && $pd['store'] == $v->ID ) ? ' selected' : '' ) . '>' . ts( $v->name ) . '</option>';
    }
    $store .= '</select></div>
    </div>';

    $fields['store']['position'] = 1;
    $fields['store']['markup'] = $store;

    // Category
    $category_markup = '<div class="form_field"><label for="data[category]">' . t( 'form_category', "Category" ) . ':</label>
    <div><select name="data[category]" id="data[category]">';
    $category = isset( $pd['category'] ) ? $pd['category'] : $product->catID;
    foreach( \query\main::group_categories( array( 'max' => 0 ), array( 'no_emoticons' => true, 'no_filters' => true ) ) as $cat ) {
        $category_markup .= '<optgroup label="' . ts( $cat['info']->name ) . '">';
        $category_markup .= '<option value="' . $cat['info']->ID . '"' . ( $category == $cat['info']->ID ? ' selected' : '' ) . '>' . ts( $cat['info']->name ) . '</option>';
        if( isset( $cat['subcats'] ) ) {
            foreach( $cat['subcats'] as $subcat ) {
                $category_markup .= '<option value="' . $subcat->ID . '"' . ( $category == $subcat->ID ? ' selected' : '' ) . '>' . ts( $subcat->name ) . '</option>';
            }
        }
        $category_markup .= '</optgroup>';
    }
    $category_markup .= '</select></div>
    </div>';

    $fields['category']['position'] = 2;
    $fields['category']['markup'] = $category_markup;

    // Name
    $fields['name']['position'] = 3;
    $fields['name']['markup'] = '<div class="form_field"><label for="data[name]">' . t( 'form_name', "Name" ) . ':</label> <div><input type="text" name="data[name]" id="data[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : $product->title ) . '" maxlength="255" placeholder="' . t( 'submit_prod_name_ph', "Product name" ) . '" required /></div></div>';

    // Price
    $fields['price']['position'] = 4;
    $fields['price']['markup'] = '<div class="form_field"><label for="data[price]">' . t( 'form_price', "Price" ) . ':</label> <div><input type="text" name="data[price]" id="data[price]" value="' . ( isset( $pd['price'] ) ? $pd['price'] : $product->price ) . '" placeholder="' . t( 'submit_prod_price_ph', "Selling price now, with discount." ) . '" /></div></div>';

    // Old price
    $fields['old_price']['position'] = 5;
    $fields['old_price']['markup'] = '<div class="form_field"><label for="data[old_price]">' . t( 'form_old_price', "Old Price" ) . ':</label> <div><input type="text" name="data[old_price]" id="data[old_price]" value="' . ( isset( $pd['old_price'] ) ? $pd['old_price'] : $product->old_price ) . '" placeholder="' . t( 'submit_prod_oldprice_ph', "Last price without discount." ) . '" /></div></div>';

    // Currency
    $fields['currency']['position'] = 6;
    $fields['currency']['markup'] = '<div class="form_field"><label for="data[currency]">' . t( 'currency', "Currency" ) . ':</label> <div><input type="text" name="data[currency]" id="data[currency]" value="' . ( isset( $pd['currency'] ) ? $pd['currency'] : $product->currency ) . '" /></div></div>';

    // Link
    $fields['link']['position'] = 7;
    $fields['link']['markup'] = '<div class="form_field"><label for="data[url]">' . t( 'form_product_url', "Product URL" ) . ':</label> <div><input type="text" name="data[url]" id="data[url]" value="' . ( isset( $pd['url'] ) ? $pd['url'] : $product->url ) . '" /></div></div>';

    // Description
    $fields['description']['position'] = 8;
    $fields['description']['markup'] = '<div class="form_field"><label for="data[description]">' . t( 'form_description', "Description" ) . ':</label> <div><textarea name="data[description]" id="data[description]" style="height:100px;">' . ( isset( $pd['description'] ) ? $pd['description'] : $product->description ) . '</textarea></div></div>';

    // Tag
    $fields['tags']['position'] = 9;
    $fields['tags']['markup'] = '<div class="form_field"><label for="data[tags]">' . t( 'form_tags', "Tags" ) . ':</label> <div><input type="text" name="data[tags]" id="data[tags]" value="' . ( isset( $pd['tags'] ) ? $pd['tags'] : $product->tags ) . '" /></div></div>';

    // Image
    $image = '<div class="form_field"><label for="data_image">' . t( 'form_image', "Image" ) . ':</label> <div>';
    if( !empty( $product->image ) ) {
        $image .= '<img src="' . image( $product->image ) . '" alt="" style="max-width:150px;max-height:50px;" />';
    }
    $image .= '<input type="file" name="data_image" id="data_image" class="inputFile" /><label for="data_image" class="fileUpload"></label></div></div>';

    $fields['image']['position'] = 10;
    $fields['image']['markup'] = $image;

    // Start date
    $fields['start_date']['position'] = 11;
    $fields['start_date']['markup'] = '<div class="form_field"><label for="data[start]">' . t( 'form_start_date', "Start Date" ) . ':</label> <div><input type="date" name="data[start]" id="data[start]" value="' . ( isset( $pd['start'] ) ? $pd['start'] : date( 'Y-m-d', strtotime( $product->start_date ) ) ) . '" style="width:79%;margin-right:1%;" /><input type="time" name="data[start_hour]" value="' . ( isset( $pd['start_hour'] ) ? $pd['start_hour'] : date( 'H:i', strtotime( $product->start_date ) ) ) . '" style="width:20%" /></div></div>';

    // End date
    $fields['end_date']['position'] = 12;
    $fields['end_date']['markup'] = '<div class="form_field"><label for="data[end]">' . t( 'form_end_date', "End Date" ) . ':</label> <div><input type="date" name="data[end]" id="data[end]" value="' . ( isset( $pd['end'] ) ? $pd['end'] : date( 'Y-m-d', strtotime( $product->expiration_date ) ) ) . '" style="width:79%;margin-right:1%;" /><input type="time" name="data[end_hour]" value="' . ( isset( $pd['end_hour'] ) ? $pd['end_hour'] : date( 'H:i', strtotime( $product->expiration_date ) ) ) . '" style="width:20%" /></div></div>';

    // Sponsored
    $price  = product_price();
    $time   = ( $sActive = ( $product->paid_until && strtotime( $product->paid_until ) > time() ) ) ? strtotime( $product->paid_until ) : time();
    $option = '<option value="">' . ( !$sActive ? t( 'sponsored_no_upgrade_product', 'Unsponsored, this product will appear after sponsored products in search results.' ) : sprintf( t( 'sponsored_until', 'Sponsored until %s' ), date( 'd F y H:i', $time ) ) ) . '</option>';
    for( $i = 1; $i <= 30; $i++ ) {
        $option .= '<option value="' . $i . '">' . sprintf( t( 'sponsored_days', 'Until %s (%s credits)' ), date( 'd F y H:i', strtotime( '+' . $i . ' day', $time ) ), ( $i * $price ) ) . '</option>' . "\n";
    }
    $fields['supported']['position'] = 13;
    $fields['supported']['markup'] = '<div class="form_field"><label for="data[sponsored]">' . t( 'form_sponsored', "Sponsored" ) . ':</label>
        <div>
            <select name="data[sponsored]" id="data[sponsored]">
                ' . $option . '
            </select>
            <span>' . t( 'form_sponsored_info', 'Sponsored products appears first in search results' ) . '</span>
        </div>
    </div>';

    global $add_form_fields;

    $custom_fields = array();
    if( !empty( $add_form_fields['product'] ) && is_array( $add_form_fields['product']) ) {
        foreach( $add_form_fields['product'] as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $custom_fields[$id]['markup'] = \site\fields::build_extra( $link['fields'], ( isset( $product->extra ) ? $product->extra : array() ), 'data[extra]' );
        }
    }

    $fields = value_with_filter( 'default_user_product_fields', $fields ) + $custom_fields;

    uasort( $fields, function( $a, $b ) {
        if( (double) $a['position'] === (double) $b['position'] ) return 0;
        return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
    } );

    foreach( $fields as $key => $f ) {
        $form .= $f['markup'];
    }

    $form .= '<input type="hidden" name="data[csrf]" value="' . $csrf . '" />
    <button>' . t( 'edit_prod_button', "Edit Product" ) . '</button>
    </form>

    </div>';

    return $form;

    } else return '<div class="info_form">' . t( 'unavailable_form2', "This form is only for store/brand owners." ) . '</div>';

    } else return '<div class="info_form">' . t( 'unavailable_form', "This form is only for logged members!" ) . '</div>';

}

/* SUBMIT NEW STORE FORM */

function submit_store_form( $auto_select = array( 'store' => '' ) ) {

    if( $GLOBALS['me'] ) {

    if( ! (boolean) \query\main::get_option( 'allow_stores' ) ) {

        return '<div class="info_form">' . t( 'submit_store_not_allowed', "New stores are not allowed at this time." ) . '</div>';

    }

    $form = '<div class="submit_store_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['data']['csrf'] ) && \site\utils::check_csrf( $_POST['data']['csrf'], 'submit_store_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['data'] );
    $pd['extra'] = value_with_filter( 'user_save_store_extra_fields', ( isset( $pd['extra'] ) ? \site\utils::array_sanitize( $pd['extra'] ) : array() ), array() );

    try {

        \user\main::submit_store( $GLOBALS['me']->ID, $pd );

        $form .= '<div class="success">' . t( 'submit_store_success', "The store/brand has been added." ) . '</div>';

        unset( $pd );

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    }

    $csrf = $_SESSION['submit_store_csrf'] = \site\utils::str_random(12);

    $form .= '<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">';

    // Category
    $category_markup = '<div class="form_field"><label for="data[category]">' . t( 'form_category', "Category" ) . ':</label>
    <div><select name="data[category]" id="data[category]">';
    $category = isset( $pd['category'] ) ? $pd['category'] : 0;
    foreach( \query\main::group_categories( array( 'max' => 0 ), array( 'no_emoticons' => true, 'no_filters' => true ) ) as $cat ) {
        $category_markup .= '<optgroup label="' . ts( $cat['info']->name ) . '">';
        $category_markup .= '<option value="' . $cat['info']->ID . '"' . ( $category == $cat['info']->ID ? ' selected' : '' ) . '>' . ts( $cat['info']->name ) . '</option>';
        if( isset( $cat['subcats'] ) ) {
            foreach( $cat['subcats'] as $subcat ) {
                $category_markup .= '<option value="' . $subcat->ID . '"' . ( $category == $subcat->ID ? ' selected' : '' ) . '>' . ts( $subcat->name ) . '</option>';
            }
        }
        $category_markup .= '</optgroup>';
    }
    $category_markup .= '</select></div>
    </div>';

    $fields['category']['position'] = 1;
    $fields['category']['markup'] = $category_markup;

    // Name
    $fields['name']['position'] = 2;
    $fields['name']['markup'] = '<div class="form_field"><label for="data[name]">' . t( 'form_name', "Name" ) . ':</label> <div><input type="text" name="data[name]" id="data[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : '' ) . '" placeholder="' . t( 'submit_store_name_ph', "Store/brand name" ) . '" required /></div></div>';

    // Type
    $type = '<div class="form_field"><label for="data[type]">' . t( 'form_type', "Type" ) . ':</label>
    <div><select name="data[type]" id="data[type]" data-switch-type>';
    foreach( array( 0 => t( 'onlinestore', "Online Store" ), 1 => t( 'physicalstore', "Physical Store" ) ) as $type_id => $type_msg ) {
        $type .= '<option value="' . $type_id . '">' . $type_msg . '</option>';
    }

    $type .= '</select>
    </div></div>';

    $fields['type']['position'] = 3;
    $fields['type']['markup'] = $type;

    // Locations
    $locations = '<div class="form_field" data-hideSS style="display:none;"><span>' . t( 'form_locations', "Locations" ) . ':</span>
    <div class="form_field-locations">';
    $locations .= '<div>' . t( 'msg_strcaselocal', "You will be able to add locations after adding the store." ) . '</div>';
    $locations .= '</div></div>';

    $fields['locations']['position'] = 4;
    $fields['locations']['markup'] = $locations;

    // Hours
    $hours_markup = '<div class="form_field" data-hideSS style="display:none;"><label>' . t( 'form_hours', "Hours" ) . ':</label>
    <div class="form_field-hours"> <input name="data[notb_hours]" type="checkbox" id="data[notb_hours]" class="inputCheckbox" data-show-hours="" value="1" checked /> <label for="data[notb_hours]"><span></span> ' . t( 'msg_blankinfo', "Blank information" ) . '</label>
    <ul data-store-hours="" style="display:none;">';

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
        <input name="data[hours][' . $k . '][opened]" type="checkbox" value="yes" class="inputCheckbox" id="day_' . $k . '"' . ( isset( $store->hours[$k]['opened'] ) ? ' checked' : '' ) . ' /> <label for="day_' . $k . '">' . $v . '</label>
        <span>
        <select name="data[hours][' . $k . '][from]">';
        foreach( $hours as $no ) {
          $hours_markup .= '<option value="' . $no . '"' . ( isset( $store->hours[$k]['from'] ) && $store->hours[$k]['from'] == $no ? ' selected' : '' ) . '>' . $no . '</option>';
        }
        $hours_markup .= '</select>
        <select name="data[hours][' . $k . '][to]">';
        foreach( $hours as $no ) {
          $hours_markup .= '<option value="' . $no . '"' . ( isset( $store->hours[$k]['to'] ) && $store->hours[$k]['to'] == $no ? ' selected' : '' ) . '>' . $no . '</option>';
        }
        $hours_markup .= '</select>
        </span>
        </li>';
    }
    $hours_markup .= '</ul></div></div>';

    $fields['hours']['position'] = 5;
    $fields['hours']['markup'] = $hours_markup;

    // Check if sells online
    $fields['sell_online']['position'] = 6;
    $fields['sell_online']['markup'] = '<div class="form_field" data-hideSS style="display:none;"><label>' . t( 'form_sell_online', "Sell Online" ) . ':</label><div class="form_field-sellonline"><input type="checkbox" id="data[sellonline]" name="data[sellonline]" value="1" class="inputCheckbox" checked /> <label for="data[sellonline]"><span>' . t( 'yes', "Yes" ) . '</span><span>' . t( 'no', "No" ) . '</span></label></div></div>';

    // Store's URL
    $fields['url']['position'] = 7;
    $fields['url']['markup'] = '<div class="form_field"><label for="data[url]">' . t( 'form_store_url', "Store URL" ) . ':</label> <div><input type="text" name="data[url]" value="' . ( isset( $pd['url'] ) ? $pd['url'] : '' ) . '" /></div></div>';

    // Description
    $fields['description']['position'] = 8;
    $fields['description']['markup'] = '<div class="form_field"><label for="data[description]">' . t( 'form_description', "Description" ) . ':</label> <div><textarea name="data[description]" id="data[description]" style="height:100px;">' . ( isset( $pd['description'] ) ? $pd['description'] : '' ) . '</textarea></div></div>';

    // Tags
    $fields['tags']['position'] = 8;
    $fields['tags']['markup'] = '<div class="form_field"><label for="data[tags]">' . t( 'form_tags', "Tags" ) . ':</label> <div><input type="text" name="data[tags]" id="data[tags]" value="' . ( isset( $pd['tags'] ) ? $pd['tags'] : '' ) . '" /></div></div>';

    // Image
    $fields['image']['position'] = 10;
    $fields['image']['markup'] = '<div class="form_field"><label for="data_logo">' . t( 'form_logo', "Logo" ) . ':</label> <div><input type="file" name="data_logo" id="data_logo" class="inputFile" /><label for="data_logo" class="fileUpload"></label></div></div>';

    // Phone no
    $fields['phone']['position'] = 11;
    $fields['phone']['markup'] = '<div class="form_field"><label for="data[phone]">' . t( 'phone_no', "Phone Number" ) . ':</label> <div><input type="text" name="data[phone]" id="data[phone]" value="' . ( isset( $pd['phone'] ) ? $pd['phone'] : '' ) . '" placeholder="(541) 754-3010" /></div></div>';

    global $add_form_fields;

    $custom_fields = array();
    if( !empty( $add_form_fields['store'] ) && is_array( $add_form_fields['store']) ) {
        foreach( $add_form_fields['store'] as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $custom_fields[$id]['markup'] = \site\fields::build_extra( $link['fields'], ( isset( $pd['extra'] ) ? $pd['extra'] : array() ), 'data[extra]' );
        }
    }

    $fields = value_with_filter( 'default_user_store_fields', $fields ) + $custom_fields;

    uasort( $fields, function( $a, $b ) {
        if( (double) $a['position'] === (double) $b['position'] ) return 0;
        return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
    } );

    foreach( $fields as $key => $f ) {
        $form .= $f['markup'];
    }

    $form .= '<input type="hidden" name="data[csrf]" value="' . $csrf . '" />
    <button>' . t( 'submit_store_button', "Add Store/Brand" ) . '</button>
    </form>

    </div>

    <script src="' . $GLOBALS['siteURL'] . PDIR . '/js/oncheck.js"></script>
    <script src="' . $GLOBALS['siteURL'] . PDIR . '/js/owner.js"></script>

    <script>
    $( document ).ready(function() {

    $( "select[data-switch-type]" ).switch_store_type();
    $( "input[data-show-hours]" ).oncheck( {Elements: "[data-store-hours]"}, "unchecked" );

    });
    </script>';

    return $form;

    } else return '<div class="info_form">' . t( 'unavailable_form', "This form is only for logged members!" ) . '</div>';

}

/* SUBMIT STORE FORM */

function edit_store_form( $id, $extra = array() ) {

    if( $GLOBALS['me'] ) {

    if( $GLOBALS['me']->Stores > 0 ) {

    $store = \query\main::store_info( $id, array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

    if( $store->userID !== $GLOBALS['me']->ID ) {

        return '<div class="info_form">' . t( 'edit_store_cant', "You don't own this store/brand." ) . '</div>';

    }

    /* */

    $form = '<div class="edit_store_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['data']['csrf'] ) && \site\utils::check_csrf( $_POST['data']['csrf'], 'edit_store_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['data'] );
    $pd['extra'] = value_with_filter( 'user_save_store_extra_fields', ( isset( $pd['extra'] ) ? \site\utils::array_sanitize( $pd['extra'] ) : array() ), array() );

    try {

        \user\main::edit_store( $id, $pd );

        $form .= '<div class="success">' . t( 'edit_store_success', "The store/brand has been updated." ) . '</div>';

        // check again information about this store

        $store = \query\main::store_info( $id, array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

        unset( $pd );

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    }

    $csrf = $_SESSION['edit_store_csrf'] = \site\utils::str_random(12);

    $form .= '<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">';

    // Category
    $category_markup = '<div class="form_field"><label for="data[category]">' . t( 'form_category', "Category" ) . ':</label>
    <div><select name="data[category]" id="data[category]">';
    $category = isset( $pd['category'] ) ? $pd['category'] : $store->catID;
    foreach( \query\main::group_categories( array( 'max' => 0 ), array( 'no_emoticons' => true, 'no_filters' => true ) ) as $cat ) {
        $category_markup .= '<optgroup label="' . ts( $cat['info']->name ) . '">';
        $category_markup .= '<option value="' . $cat['info']->ID . '"' . ( $category == $cat['info']->ID ? ' selected' : '' ) . '>' . ts( $cat['info']->name ) . '</option>';
        if( isset( $cat['subcats'] ) ) {
            foreach( $cat['subcats'] as $subcat ) {
                $category_markup .= '<option value="' . $subcat->ID . '"' . ( $category == $subcat->ID ? ' selected' : '' ) . '>' . ts( $subcat->name ) . '</option>';
            }
        }
        $category_markup .= '</optgroup>';
    }
    $category_markup .= '</select></div>
    </div>';

    $fields['category']['position'] = 1;
    $fields['category']['markup'] = $category_markup;

    // Name
    $fields['name']['position'] = 2;
    $fields['name']['markup'] = '<div class="form_field"><label for="data[name]">' . t( 'form_name', "Name" ) . ':</label> <div><input type="text" name="data[name]" id="data[name]" value="' . ( isset( $pd['name'] ) ? $pd['name'] : $store->name ) . '" placeholder="' . t( 'submit_store_name_ph', "Store/brand name" ) . '" required /></div></div>';

    // Type
    $type = '<div class="form_field"><label for="data[type]">' . t( 'form_type', "Type" ) . ':</label>
    <div><select name="data[type]" id="data[type]" data-switch-type>';
    foreach( array( 0 => t( 'onlinestore', "Online Store" ), 1 => t( 'physicalstore', "Physical Store" ) ) as $type_id => $type_msg ) {
        $type .= '<option value="' . $type_id . '"' . ( $type_id == 1 && !$store->is_physical ? '' : ' selected' ) . '>' . $type_msg . '</option>';
    }

    $type .= '</select>
    </div></div>';

    $fields['type']['position'] = 3;
    $fields['type']['markup'] = $type;

    // Locations
    $locations = '<div class="form_field" data-hideSS' . ( !$store->is_physical ? ' style="display:none;"' : '' ) . '><span>' . t( 'form_locations', "Locations" ) . ':</span>';
    if( !$store->is_physical ) {
        $locations .= '<div>' . t( 'msg_physicalfirst', "Save it as a physical store before being able to add locations." ) . '</div>';
    } else {

        if( \query\locations::store_locations( array( 'store' => $store->ID ) ) ) {

            $locations .= '<ul class="submit_store_form_locations">';
            foreach( \query\locations::while_store_locations( array( 'max' => 0, 'store' => $store->ID ) ) as $loc ) {
                $locations .= '<li>';
                if( isset( $extra['LOCATION_EDIT_LINK'] ) ) $locations .= '<a href="' . str_replace( '%ID%', $loc->ID, $extra['LOCATION_EDIT_LINK'] ) . '">' . t( 'edit', "Edit" ) . '</a>';
                if( isset( $extra['LOCATION_DELETE_LINK'] ) ) $locations .= '<a href="' . str_replace( '%ID%', $loc->ID, $extra['LOCATION_DELETE_LINK'] ) . '">' . t( 'delete', "Delete" ) . '</a>';

                $locations .= implode( ', ', array_filter( array( $loc->zip, $loc->country, $loc->state, $loc->city, $loc->address ) ) ) . '</li>';
            }
            $locations .= '</ul>';

        } else {
            $locations .= '<div class="alert">' . t( 'msg_no_locations', "No locations yet." ) . '</div>';
        }

        if( isset( $extra['LOCATION_ADD_LINK'] ) ) $locations .= '<div><a href="' . $extra['LOCATION_ADD_LINK'] . '" class="btn">' . t( 'add', "Add" ) . '</a></div>';

    }
    $locations .= '</div>';

    $fields['locations']['position'] = 4;
    $fields['locations']['markup'] = $locations;

    // Hours
    $hours_markup = '<div class="form_field" data-hideSS' . ( !$store->is_physical ? ' style="display:none;"' : '' ) . '><label>' . t( 'form_hours', "Hours" ) . ':</label>
    <div class="form_field-hours"> <input name="data[notb_hours]" type="checkbox" id="data[notb_hours]" class="inputCheckbox" data-show-hours="" value="1"' . ( empty( $store->hours ) ? ' checked' : '' ) . ' /> <label for="data[notb_hours]"><span></span> ' . t( 'msg_blankinfo', "Blank information" ) . '</label>
    <ul data-store-hours=""' . ( empty( $store->hours ) ? ' style="display:none;"' : '' ) . '>';
    
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

        <input name="data[hours][' . $k . '][opened]" type="checkbox" value="yes" class="inputCheckbox" id="day_' . $k . '"' . ( isset( $store->hours[$k]['opened'] ) ? ' checked' : '' ) . ' /> <label for="day_' . $k . '">' . $v . '</label>
        <span>
        <select name="data[hours][' . $k . '][from]">';
        foreach( $hours as $no ) {
            $hours_markup .= '<option value="' . $no . '"' . ( isset( $store->hours[$k]['from'] ) && $store->hours[$k]['from'] == $no ? ' selected' : '' ) . '>' . $no . '</option>';
        }
        $hours_markup .= '</select>
        <select name="data[hours][' . $k . '][to]">';
        foreach( $hours as $no ) {
            $hours_markup .= '<option value="' . $no . '"' . ( isset( $store->hours[$k]['to'] ) && $store->hours[$k]['to'] == $no ? ' selected' : '' ) . '>' . $no . '</option>';
        }
        $hours_markup .= '</select>
        </span>

        </li>';
    }
    $hours_markup .= '</ul></div></div>';

    $fields['hours']['position'] = 5;
    $fields['hours']['markup'] = $hours_markup;

    // Check if sells online
    $fields['sell_online']['position'] = 6;
    $fields['sell_online']['markup'] = '<div class="form_field" data-hideSS' . ( !$store->is_physical ? ' style="display:none;"' : '' ) . '><label>' . t( 'form_sell_online', "Sell Online" ) . ':</label><div class="form_field-sellonline"><input type="checkbox" id="data[sellonline]" name="data[sellonline]" value="1" class="inputCheckbox"' . ( $store->sellonline2 ? ' checked' : '' ) . ' /> <label for="data[sellonline]"><span>' . t( 'yes', "Yes" ) . '</span><span>' . t( 'no', "No" ) . '</span></label></div></div>';

    // Store's URL
    $fields['url']['position'] = 7;
    $fields['url']['markup'] = '<div class="form_field"><label for="data[url]">' . t( 'form_store_url', "Store URL" ) . ':</label> <div><input type="text" name="data[url]" value="' . ( isset( $pd['url'] ) ? $pd['url'] : $store->url ) . '" /></div></div>';

    // Description
    $fields['description']['position'] = 8;
    $fields['description']['markup'] = '<div class="form_field"><label for="data[description]">' . t( 'form_description', "Description" ) . ':</label> <div><textarea name="data[description]" id="data[description]" style="height:100px;">' . ( isset( $pd['description'] ) ? $pd['description'] : $store->description ) . '</textarea></div></div>';

    // Tags
    $fields['tags']['position'] = 9;
    $fields['tags']['markup'] = '<div class="form_field"><label for="data[tags]">' . t( 'form_tags', "Tags" ) . ':</label> <div><input type="text" name="data[tags]" id="data[tags]" value="' . ( isset( $pd['tags'] ) ? $pd['tags'] : $store->tags ) . '" /></div></div>';

    // Image
    $image = '<div class="form_field"><label for="data_logo">' . t( 'form_image', "Image" ) . ':</label> <div>';
    if( !empty( $store->image ) ) {
        $image .= '<img src="' . store_avatar( $store->image ) . '" alt="" style="max-width:150px;max-height:50px;" />';
    }
    $image .= '<input type="file" name="data_logo" id="data_logo" class="inputFile" /><label for="data_logo" class="fileUpload"></label></div></div>';

    $fields['image']['position'] = 10;
    $fields['image']['markup'] = $image;

    // Phone no
    $fields['phone']['position'] = 11;
    $fields['phone']['markup'] = '<div class="form_field"><label for="data[phone]">' . t( 'phone_no', "Phone Number" ) . ':</label> <div><input type="text" name="data[phone]" id="data[phone]" value="' . ( isset( $pd['phone'] ) ? $pd['phone'] : $store->phone_no ) . '" placeholder="(541) 754-3010" /></div></div>';

    global $add_form_fields;

    $custom_fields = array();
    if( !empty( $add_form_fields['store'] ) && is_array( $add_form_fields['store']) ) {
        foreach( $add_form_fields['store'] as $id => $link ) {
            $custom_fields[$id]['position'] = isset( $link['position'] ) ? $link['position'] : 99;
            $custom_fields[$id]['markup'] = \site\fields::build_extra( $link['fields'], ( isset( $store->extra ) ? $store->extra : array() ), 'data[extra]' );
        }
    }

    $fields = value_with_filter( 'default_user_store_fields', $fields ) + $custom_fields;

    uasort( $fields, function( $a, $b ) {
        if( (double) $a['position'] === (double) $b['position'] ) return 0;
        return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
    } );

    foreach( $fields as $key => $f ) {
        $form .= $f['markup'];
    }

    $form .= '<input type="hidden" name="data[csrf]" value="' . $csrf . '" />
    <button>' . t( 'edit_store_button', "Edit Store/Brand" ) . '</button>
    </form>

    </div>

    <script src="' . $GLOBALS['siteURL'] . PDIR . '/js/oncheck.js"></script>
    <script src="' . $GLOBALS['siteURL'] . PDIR . '/js/owner.js"></script>

    <script>
    $( document ).ready(function() {

    $( "select[data-switch-type]" ).switch_store_type();
    $( "input[data-show-hours]" ).oncheck( {Elements: "[data-store-hours]"}, "unchecked" );

    });
    </script>';

    return $form;

    } else return '<div class="info_form">' . t( 'unavailable_form2', "This form is only for store/brand owners." ) . '</div>';

    } else return '<div class="info_form">' . t( 'unavailable_form', "This form is only for logged members!" ) . '</div>';

}

/* SUBMIT LOCATION FOR STORE FORM */

function submit_store_location_form( $store = 0 ) {

    if( $GLOBALS['me'] ) {

    if( $GLOBALS['me']->Stores > 0 ) {

    $form = '<div class="submit_store_location_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['data']['csrf'] ) && \site\utils::check_csrf( $_POST['data']['csrf'], 'submit_store_loc_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['data'] );

    try {

        \user\main::submit_store_location( $pd );

        $form .= '<div class="success">' . t( 'submit_store_loc_success', "The store/brand location has been added." ) . '</div>';

        unset( $pd );

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    }

    $csrf = $_SESSION['submit_store_loc_csrf'] = \site\utils::str_random(12);

    $form .= '<form action="#" method="POST" autocomplete="off">
    <div class="form_field"><label for="data[store]">' . t( 'form_store', "Store" ) . ':</label><div><select name="data[store]" id="data[store]">';
    foreach( \query\main::while_stores( array( 'max' => 0, 'user' => $GLOBALS['me']->ID, 'show' => 'physical' ), '', array( 'no_emoticons' => true, 'no_filters' => true ) ) as $k => $v ) {
        $form .= '<option value="' . $v->ID . '"' . ( $store == $v->ID ? ' selected' : '' ) . '>' . ts( $v->name ) . '</option>';
    }
    $form .= '</select></div></div>
    <div class="form_field"><label for="data[address]">' . t( 'form_address', "Address" ) . ':</label><div><input type="text" name="data[address]" id="data[address]" value="' . ( isset( $pd['address'] ) ? $pd['address'] : '' ) . '" required /></div></div>
    <div class="form_field"><label for="data[zip]">' . t( 'loc_form_zip', "ZIP" ) . ':</label><div><input type="text" name="data[zip]" id="data[zip]" value="' . ( isset( $pd['zip'] ) ? $pd['zip'] : '' ) . '" /></div></div>
    <div class="form_field"><label for="data[country]">' . t( 'loc_form_country', "Country" ) . ':</label><div><select name="data[country]" id="data[country]" data-search-country="">';
    $country = $state = 0;
    foreach( \query\locations::while_countries( array( 'max' => 0 ) ) as $k => $v ) {
        if( $k === 0 ) {
            $country = $v->ID;
        }
        $form .= '<option value="' . $v->ID . '">' . $v->name . '</option>';
    }
    $form .= '</select></div></div>
    <div class="form_field"><label for="data[state]">' . t( 'loc_form_state', "State" ) . ':</label><div><select name="data[state]" id="data[state]" data-search-state="">';
    foreach( \query\locations::while_states( array( 'max' => 0, 'country' => $country ) ) as $k => $v ) {
        if( $k === 0 ) {
            $state = $v->ID;
        }
        $form .= '<option value="' . $v->ID . '">' . $v->name . '</option>';
    }
    $form .= '</select></div></div>';
    $form .= '<div class="form_field"><label for="data[city]">' . t( 'loc_form_city', "City" ) . ':</label><div><select name="data[city]" id="data[city]" data-search-city="">';
    foreach( \query\locations::while_cities( array( 'max' => 0, 'state' => $state ) ) as $v ) {
        $form .= '<option value="' . $v->ID . '" data-lat="' . $v->lat . '" data-lng="' . $v->lng . '">' . $v->name . '</option>';
    }
    $form .= '</select></div></div>';
    if( ( $mapskey = \query\main::get_option( 'google_maps_key' ) ) && !empty( $mapskey ) ) {
        $form .= '<div class="form_field"><label>' . t( 'form_map', "Map" ) . ':</label><div id="map" style="height: 300px;"></div></div>';
    }
    $form .= '<input type="hidden" name="data[mapmarker]" value="39.82,-101.47" data-marker="" />
    <input type="hidden" name="data[csrf]" value="' . $csrf . '" />
    <button>' . t( 'submit_store_loc_button', "Add Store/Brand Location" ) . '</button>
    </form>

    </div>';

    if( !empty( $mapskey ) ) { ?>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_html( $mapskey ); ?>&signed_in=true&callback=initMap"></script>

    <script>
        var marker, map, newlatlng, deflat, deflng;

        deflat = 39.82;
        deflng = -101.47;

        function toggleBounce() {
            $("input[data-marker]").val(this.getPosition());
        }

        function updateMarker(){
            var lat = $("select[data-search-city]").find("option:selected").attr("data-lat");
            var lng = $("select[data-search-city]").find("option:selected").attr("data-lng");
            newlatlng = new google.maps.LatLng(lat,lng);
            marker.setPosition(newlatlng);
            map.setCenter(newlatlng);
        }

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 14,
                center: {lat: deflat, lng: deflng}
            });

            marker = new google.maps.Marker({
                map: map,
                draggable: true,
                position: {lat: deflat, lng: deflng}
            });
            marker.addListener("dragend", toggleBounce);
        }
        </script>

    <?php } ?>

    <script>

    $( document ).ready(function() {

        $( "select[data-search-country]" ).search_store_location( {SearchLocation: "<?php echo $GLOBALS['siteURL']; ?>"}, "country", function(){
        updateMarker();
        } );
        $( "select[data-search-state]" ).search_store_location( {SearchLocation: "<?php echo $GLOBALS['siteURL']; ?>"}, "state", function(){
        updateMarker();
        } );
        $( "select[data-search-city]" ).search_store_location( {SearchLocation: "<?php echo $GLOBALS['siteURL']; ?>"}, "city", function(){
        updateMarker();
        } );

    });

    </script>

    <script src="<?php echo $GLOBALS['siteURL'] . PDIR; ?>/js/search_location.js"></script>

    <?php

    return $form;

    } else return '<div class="info_form">' . t( 'unavailable_form2', "This form is only for store/brand owners." ) . '</div>';

    } else return '<div class="info_form">' . t( 'unavailable_form', "This form is only for logged members!" ) . '</div>';

}

/* EDIT LOCATION FOR STORE FORM */

function edit_store_location_form( $id ) {

    if( $GLOBALS['me'] ) {

    if( $GLOBALS['me']->Stores > 0 ) {

    $location = \query\locations::store_location_info( $id );

    if( !\query\main::have_store( $location->storeID, $GLOBALS['me']->ID ) ) {

        return '<div class="info_form">' . t( 'edit_store_cant', "You don't own this store/brand." ) . '</div>';

    }

    $form = '<div class="submit_store_location_form other_form">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['edit_store_loc_form'] ) && \site\utils::check_csrf( $_POST['edit_store_loc_form']['csrf'], 'edit_store_loc_csrf' ) ) {

    $pd = \site\utils::validate_user_data( $_POST['edit_store_loc_form'] );

    try {

        \user\main::edit_store_location( $id, $pd );

        $form .= '<div class="success">' . t( 'edit_store_loc_success', "The store/brand location has been updated." ) . '</div>';

        // check again information about this location

        $location = \query\locations::store_location_info( $id );

        unset( $pd );

    }

    catch( Exception $e ){
        $form .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    }

    $csrf = $_SESSION['edit_store_loc_csrf'] = \site\utils::str_random(12);

    $form .= '<form action="#" method="POST" autocomplete="off">                                     
    <div class="form_field"><label for="edit_store_loc_form[store]">' . t( 'form_store', "Store" ) . ':</label><div><select name="edit_store_loc_form[store]" id="edit_store_loc_form[store]">';
    foreach( \query\main::while_stores( array( 'max' => 0, 'user' => $GLOBALS['me']->ID, 'show' => 'physical' ), '', array( 'no_emoticons' => true, 'no_filters' => true ) ) as $k => $v ) {
        $form .= '<option value="' . $v->ID . '"' . ( $location->storeID == $v->ID ? ' selected' : '' ) . '>' . $v->name . '</option>';
    }
    $form .= '</select></div></div>
    <div class="form_field"><label for="edit_store_loc_form[address]">' . t( 'form_address', "Address" ) . ':</label><div><input type="text" name="edit_store_loc_form[address]" id="edit_store_loc_form[address]" value="' . ( isset( $pd['address'] ) ? $pd['address'] : $location->address ) . '" required /></div></div>
    <div class="form_field"><label for="edit_store_loc_form[zip]">' . t( 'loc_form_zip', "ZIP" ) . ':</label><div><input type="text" name="edit_store_loc_form[zip]" id="edit_store_loc_form[zip]" value="' . ( isset( $pd['zip'] ) ? $pd['zip'] : $location->zip ) . '" /></div></div>
    <div class="form_field"><label for="edit_store_loc_form[country]">' . t( 'loc_form_country', "Country" ) . ':</label><div><select name="edit_store_loc_form[country]" id="edit_store_loc_form[country]" data-search-country="">';
    foreach( \query\locations::while_countries( array( 'max' => 0 ) ) as $k => $v ) {
        $form .= '<option value="' . $v->ID . '"' . ( $location->countryID == $v->ID ? ' selected' : '' ) . '>' . $v->name . '</option>';
    }
    $form .= '</select></div></div>
    <div class="form_field"><label for="edit_store_loc_form[state]">' . t( 'loc_form_state', "State" ) . ':</label><div><select name="edit_store_loc_form[state]" id="edit_store_loc_form[state]" data-search-state="">';
    foreach( \query\locations::while_states( array( 'max' => 0, 'country' => $location->countryID ) ) as $k => $v ) {
        $form .= '<option value="' . $v->ID . '"' . ( $location->stateID == $v->ID ? ' selected' : '' ) . '>' . $v->name . '</option>';
    }
    $form .= '</select></div></div>';
    $form .= '<div class="form_field"><label for="edit_store_loc_form[city]">' . t( 'loc_form_city', "City" ) . ':</label><div><select name="edit_store_loc_form[city]" id="edit_store_loc_form[city]" data-search-city="">';
    foreach( \query\locations::while_cities( array( 'max' => 0, 'state' => $location->stateID ) ) as $v ) {
        $form .= '<option value="' . $v->ID . '" data-lat="' . $v->lat . '" data-lng="' . $v->lng . '"' . ( $location->cityID == $v->ID ? ' selected' : '' ) . '>' . $v->name . '</option>';
    }
    $form .= '</select></div></div>';
    if( ( $mapskey = \query\main::get_option( 'google_maps_key' ) ) && !empty( $mapskey ) ) {
        $form .= '<div class="form_field"><label>' . t( 'form_map', "Map" ) . ':</label><div id="map" style="height: 300px;"></div></div>';
    }
    $form .= '<input type="hidden" name="edit_store_loc_form[mapmarker]" value="' . $location->lat . ',' . $location->lng . '" data-lat="' . $location->lat . '" data-lng="' . $location->lng . '" data-marker="" />
    <input type="hidden" name="edit_store_loc_form[csrf]" value="' . $csrf . '" />
    <button>' . t( 'edit_store_loc_button', "Edit Store/Brand Location" ) . '</button>
    </form>

    </div>';

    if( !empty( $mapskey ) ) { ?>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_html( $mapskey ); ?>&signed_in=true&callback=initMap"></script>

    <script>
        $( document ).ready(function() {
            var deflat = parseFloat( $("input[data-marker]").attr("data-lat") );
            var deflng = parseFloat( $("input[data-marker]").attr("data-lng") );

            newlatlng = new google.maps.LatLng(deflat,deflng);
            marker.setPosition(newlatlng);
            map.setCenter(newlatlng);
        });

        var marker, map, newlatlng;

        var deflat = 39.82;
        var deflng = -101.47;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 14,
                center: {lat: deflat, lng: deflng}
            });

            marker = new google.maps.Marker({
                map: map,
                draggable: true,
                position: {lat: deflat, lng: deflng}
            });
            marker.addListener("dragend", toggleBounce);
        }

        function toggleBounce() {
            $("input[data-marker]").val(this.getPosition());
        }

        function updateMarker() {
            var lat = $("select[data-search-city]").find("option:selected").attr("data-lat");
            var lng = $("select[data-search-city]").find("option:selected").attr("data-lng");
            newlatlng = new google.maps.LatLng(lat,lng);
            marker.setPosition(newlatlng);
            map.setCenter(newlatlng);
        }
    </script>

    <?php } ?>

    <script>

    $( document ).ready(function() {

        $( "select[data-search-country]" ).search_store_location( {SearchLocation: "<?php echo $GLOBALS['siteURL']; ?>"}, "country", function(){
        updateMarker();
        } );
        $( "select[data-search-state]" ).search_store_location( {SearchLocation: "<?php echo $GLOBALS['siteURL']; ?>"}, "state", function(){
        updateMarker();
        } );
        $( "select[data-search-city]" ).search_store_location( {SearchLocation: "<?php echo $GLOBALS['siteURL']; ?>"}, "city", function(){
        updateMarker();
        } );

    });

    </script>

    <script src="<?php echo $GLOBALS['siteURL'] . PDIR; ?>/js/search_location.js"></script>

    <?php

    return $form;

    } else return '<div class="info_form">' . t( 'unavailable_form2', "This form is only for store/brand owners." ) . '</div>';

    } else return '<div class="info_form">' . t( 'unavailable_form', "This form is only for logged members!" ) . '</div>';

}