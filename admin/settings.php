<?php

if( !$GLOBALS['me']->is_admin ) die;

switch( $_GET['action'] ) {

/** DEFAULT IMAGES AND OTHERS */

case 'default':

echo '<div class="title">

<h2>' . t( 'settings_general_title', "Modify Site Settings" ) . '</h2>';

$subtitle = t( 'settings_default_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_settings_page', 'after_title_default_settings_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

    if( isset( $_POST['user_avatar'] ) && isset( $_POST['store_avatar'] ) && isset( $_POST['def_user_points'] ) && isset( $_POST['points_per_review'] ) && isset( $_POST['points_per_dailyv'] ) && isset( $_POST['points_per_refer'] ) && isset( $_POST['refer_cookie_duration'] ) )
    if( admin\actions::set_option(
    array(
    'default_user_avatar' => $_POST['user_avatar'],
    'default_store_avatar' => $_POST['store_avatar'],
    'default_reward_avatar' => $_POST['reward_avatar'],
    'u_def_points' => (int) $_POST['def_user_points'],
    'u_confirm_req' => ( isset( $_POST['def_user_confirmation'] ) ? 0 : 1 ),
    'subscr_confirm_req' => ( isset( $_POST['def_subscr_conf'] ) ? 1 : 0 ),
    'unsubscr_confirm_req' => ( isset( $_POST['def_unsubscr_conf'] ) ? 1 : 0 ),
    'u_points_review' => (int) $_POST['points_per_review'],
    'u_points_davisit' => (int) $_POST['points_per_dailyv'],
    'u_points_refer' => (int) $_POST['points_per_refer'],
    'refer_cookie' => (int) $_POST['refer_cookie_duration'],
    'smilies_coupons' => ( isset( $_POST['scoupons'] ) ? 1 : 0 ),
    'smilies_products' => ( isset( $_POST['sproducts'] ) ? 1 : 0 ),
    'smilies_stores' => ( isset( $_POST['sstores'] ) ? 1 : 0 ),
    'smilies_categories' => ( isset( $_POST['scategories'] ) ? 1 : 0 ),
    'smilies_reviews' => ( isset( $_POST['sreviews'] ) ? 1 : 0 ),
    'smilies_pages' => ( isset( $_POST['spages'] ) ? 1 : 0 )
    ) ) )

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'settings_save_error', "One or more options can't be saved." ) . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . t( 'settings_form_defua', "User Avatar" ) . ':</span><div class="images-list">';
$def_user_avatar = \query\main::get_option( 'default_user_avatar' );
foreach( glob( '../' . DEFAULT_IMAGES_LOC . '/avatar*.[pjg][npi][gf]' ) as $k ) echo '<input type="radio" id="' . basename( $k ) . '" name="user_avatar" value="' . basename( $k ) . '"' . ( basename( $k ) == $def_user_avatar ? ' checked' : '' ) . ' /> <label for="' . basename( $k ) . '"><span></span> <img src="' . $k . '" alt="" /></label>';
echo '</div></div>

<div class="row"><span>' . t( 'settings_form_defsa', "Store Avatar" ) . ':</span><div class="images-list">';
$def_store_avatar = \query\main::get_option( 'default_store_avatar' );
foreach( glob( '../' . DEFAULT_IMAGES_LOC . '/store_avatar*.[pjg][npi][gf]' ) as $k ) echo '<input type="radio" id="' . basename( $k ) . '" name="store_avatar" value="' . basename( $k ) . '" ' . ( basename( $k ) == $def_store_avatar ? ' checked' : '' ) . ' /> <label for="' . basename( $k ) . '"><span></span> <img src="' . $k . '" alt="" style="width: 60px;" /></label>';
echo '</div></div>

<div class="row"><span>' . t( 'settings_form_defra', "Reward Avatar" ) . ':</span><div class="images-list">';
$def_reward_avatar = \query\main::get_option( 'default_reward_avatar' );
foreach( glob( '../' . DEFAULT_IMAGES_LOC . '/reward_avatar*.[pjg][npi][gf]' ) as $k ) echo '<input type="radio" id="' . basename( $k ) . '" name="reward_avatar" value="' . basename( $k ) . '" ' . ( basename( $k ) == $def_reward_avatar ? ' checked' : '' ) . ' /> <label for="' . basename( $k ) . '"><span></span> <img src="' . $k . '" alt="" /></label>';
echo '</div></div>

<div class="row"><span>' . t( 'settings_form_defup', "User Points" ) . ' <span class="info"><span>' . t( 'settings_form_idefup', "The number of points that a user receives when sign up." ) . '</span></span>:</span><div><input type="number" name="def_user_points" value="' . (int) \query\main::get_option( 'u_def_points' ) . '" min="0" /></div></div>
<div class="row"><span>' . t( 'settings_form_defcr', "Users Confirmation" ) . ':</span><div><input type="checkbox" name="def_user_confirmation" id="def_user_confirmation"' . ( !\query\main::get_option( 'u_confirm_req' ) ? ' checked' : '' ) . ' /> <label for="def_user_confirmation"><span></span> ' . t( 'msg_require_user_conf', "Require email confirmation for new accounts" ) . '</label></div></div>
<div class="row"><span>' . t( 'settings_form_defsubscr', "Subscribe Confirmation" ) . ':</span><div><input type="checkbox" name="def_subscr_conf" id="def_subscr_confirmation"' . ( \query\main::get_option( 'subscr_confirm_req' ) ? ' checked' : '' ) . ' /> <label for="def_subscr_confirmation"><span></span> ' . t( 'msg_require_subscr_conf', "Require email confirmation for new subscribers" ) . '</label></div></div>
<div class="row"><span>' . t( 'settings_form_defunsubscr', "Unsubscribe Confirmation" ) . ':</span><div><input type="checkbox" name="def_unsubscr_conf" id="def_unsubscr_confirmation"' . ( \query\main::get_option( 'unsubscr_confirm_req' ) ? ' checked' : '' ) . ' /> <label for="def_unsubscr_confirmation"><span></span> ' . t( 'msg_require_unsubscr_conf', "Require email confirmation to unsubscribe" ) . '</label></div></div>
<div class="row"><span>' . t( 'settings_form_defusml', "Use Smilies For" ) . ' <span class="info"><span>';
foreach( \site\content::smilies() as $code => $src ) {
    echo $code . ' <img src="' . $src . '" alt="" /><br />';
}
echo '<a href="//www.iconfinder.com/iconsets/gowemto-smileys" style="color:#FFF;font-size:12px;">By Hemmat Ibrahim</a>';
echo '</span></span>:</span><div>
<input type="checkbox" name="scoupons" id="scoupons"' . ( (boolean) \query\main::get_option( 'smilies_coupons' ) ? ' checked' : '' ) . ' /><label for="scoupons"><span></span> ' . t( 'coupons', "Coupons" ) . '</label>
<input type="checkbox" name="sproducts" id="sproducts"' . ( (boolean) \query\main::get_option( 'smilies_products' ) ? ' checked' : '' ) . ' /><label for="sproducts"><span></span> ' . t( 'products', "Products" ) . '</label>
<input type="checkbox" name="sstores" id="sstores"' . ( (boolean) \query\main::get_option( 'smilies_stores' ) ? ' checked' : '' ) . ' /><label for="sstores"><span></span> ' . t( 'stores', "Stores" ) . '</label>
<input type="checkbox" name="scategories" id="scategories"' . ( (boolean) \query\main::get_option( 'smilies_categories' ) ? ' checked' : '' ) . ' /><label for="scategories"><span></span> ' . t( 'categories', "Categories" ) . '</label>
<input type="checkbox" name="spages" id="spages"' . ( (boolean) \query\main::get_option( 'smilies_pages' ) ? ' checked' : '' ) . ' /><label for="spages"><span></span> ' . t( 'pages', "Pages" ) . '</label>
<input type="checkbox" name="sreviews" id="sreviews"' . ( (boolean) \query\main::get_option( 'smilies_reviews' ) ? ' checked' : '' ) . ' /><label for="sreviews"><span></span> ' . t( 'reviews', "Reviews" ) . '</label>
</div></div>

<div class="section-content">
    <div class="title">
        <h2>' . t( 'settings_userrew_title', "Rewards for User Activity" ) . '</h2>
    </div>

    <div class="content">
        <div class="row"><span>' . t( 'settings_form_pprev', "Points per Review" ) . ':</span><div><input type="number" name="points_per_review" value="' . (int) \query\main::get_option( 'u_points_review' ) . '" min="0" /></div></div>
        <div class="row"><span>' . t( 'settings_form_ppdv', "Daily Points" ) . ' <span class="info"><span>' . t( 'settings_form_ippdv', "The number of points that a user receives daily for at least one visit of this website." ) . '</span></span>:</span><div><input type="number" name="points_per_dailyv" value="' . (int) \query\main::get_option( 'u_points_davisit' ) . '" min="0" /></div></div>
        <div class="row"><span>' . t( 'settings_form_ppref', "Points per Refer" ) . ' <span class="info"><span>' . t( 'settings_form_ippref', "The number of points that a user receives when refer a friend on website." ) . '</span></span>:</span><div><input type="number" name="points_per_refer" value="' . (int) \query\main::get_option( 'u_points_refer' ) . '" min="0" /></div></div>
        <div class="row"><span>' . t( 'settings_form_cokref', "Cookie Duration" ) . ':</span>
        <div>
        <select name="refer_cookie_duration">';
        $refcookie_duration = \query\main::get_option( 'refer_cookie' );
        foreach( array( 15, 30, 60, 90 ) as $v ) echo '<option value="' . $v . '"' . ( $v == $refcookie_duration ? ' selected' : '' ) . '>' . $v . ' ' . t( 'days', "Days" ) . '</option>';
        echo '</select>
        </div></div>
    </div>
</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'settings_save_button', "Save Settings" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

/** SECURITY PREFERENCES */

case 'security':

echo '<div class="title">

<h2>' . t( 'settings_general_title', "Modify Site Settings" ) . '</h2>';

$subtitle = t( 'settings_security_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_settings_page', 'after_title_security_settings_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

    if( admin\actions::set_option(
    array(
    'login_captcha'     => ( isset( $_POST['login'] ) ? 1 : 0 ),
    'register_captcha'  => ( isset( $_POST['register'] ) ? 1 : 0 ),
    'contact_captcha'   => ( isset( $_POST['contact'] ) ? 1 : 0 ),
    'suggest_captcha'   => ( isset( $_POST['suggest'] ) ? 1 : 0 ),
    'subscribe_captcha' => ( isset( $_POST['subscribe'] ) ? 1 : 0 )
    ) ) )

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'settings_save_error', "One or more options can't be saved." ) . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . t( 'settings_form_captcha', "Use captcha for" ) . ':</span><div><ul class="checkbox-list">
<li><input type="checkbox" name="login" id="login"' . ( \query\main::get_option( 'login_captcha' ) ? ' checked' : '' ) . ' /> <label for="login"><span></span> ' . t( 'msg_loginform', "Login form" ) . '</label> </li>
<li><input type="checkbox" name="register" id="register"' . ( \query\main::get_option( 'register_captcha' ) ? ' checked' : '' ) . ' /> <label for="register"><span></span> ' . t( 'msg_registerform', "Register form" ) . '</label> </li>
<li><input type="checkbox" name="contact" id="contact"' . ( \query\main::get_option( 'contact_captcha' ) ? ' checked' : '' ) . ' /> <label for="contact"><span></span> ' . t( 'msg_contactform', "Contact form" ) . '</label> </li>
<li><input type="checkbox" name="suggest" id="suggest"' . ( \query\main::get_option( 'suggest_captcha' ) ? ' checked' : '' ) . ' /> <label for="suggest"><span></span> ' . t( 'msg_suggestform', "Suggestion form" ) . '</label></li>
<li><input type="checkbox" name="subscribe" id="subscribe"' . ( \query\main::get_option( 'subscribe_captcha' ) ? ' checked' : '' ) . ' /> <label for="subscribe"><span></span> ' . t( 'msg_subscribeform', "Subscribe form" ) . '</label></li>
</ul></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'settings_save_button', "Save Settings" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

/** META TAGS */

case 'meta':

echo '<div class="title">

<h2>' . t( 'settings_general_title', "Modify Site Settings" ) . '</h2>';

$subtitle = t( 'settings_meta_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_settings_page', 'after_title_meta_settings_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

    if( isset( $_POST['sitetitle'] ) && isset( $_POST['meta_keywords'] ) && isset( $_POST['meta_description'] ) && isset( $_POST['meta_coupon_title'] ) && isset( $_POST['meta_coupon_keywords'] ) && isset( $_POST['meta_coupon_desc'] ) && isset( $_POST['meta_product_title'] ) && isset( $_POST['meta_product_keywords'] ) && isset( $_POST['meta_product_desc'] ) && isset( $_POST['meta_store_title'] ) && isset( $_POST['meta_store_keywords'] ) && isset( $_POST['meta_store_desc'] ) && isset( $_POST['meta_reviews_title'] ) && isset( $_POST['meta_reviews_keywords'] ) && isset( $_POST['meta_reviews_desc'] ) && isset( $_POST['meta_category_title'] ) && isset( $_POST['meta_category_keywords'] ) && isset( $_POST['meta_category_desc'] ) )
    if( admin\actions::set_option(
    array(
    'sitetitle' => $_POST['sitetitle'],
    'meta_keywords' => $_POST['meta_keywords'],
    'meta_description' => $_POST['meta_description'],
    'meta_coupon_title' => $_POST['meta_coupon_title'],
    'meta_coupon_keywords' => $_POST['meta_coupon_keywords'],
    'meta_coupon_desc' => $_POST['meta_coupon_desc'],
    'meta_product_title' => $_POST['meta_product_title'],
    'meta_product_keywords' => $_POST['meta_product_keywords'],
    'meta_product_desc' => $_POST['meta_product_desc'],
    'meta_store_title' => $_POST['meta_store_title'],
    'meta_store_keywords' => $_POST['meta_store_keywords'],
    'meta_store_desc' => $_POST['meta_store_desc'],
    'meta_reviews_title' => $_POST['meta_reviews_title'],
    'meta_reviews_keywords' => $_POST['meta_reviews_keywords'],
    'meta_reviews_desc' => $_POST['meta_reviews_desc'],
    'meta_category_title' => $_POST['meta_category_title'],
    'meta_category_keywords' => $_POST['meta_category_keywords'],
    'meta_category_desc' => $_POST['meta_category_desc']
    ) ) )

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'settings_save_error', "One or more options can't be saved." ) . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . t( 'settings_form_metatitle', "Title" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="sitetitle" value="' . esc_html( \query\main::get_option( 'sitetitle' ) ) . '" /></div></div>
<div class="row"><span>' . t( 'settings_form_metakeywords', "Keywords" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_keywords">' . \query\main::get_option( 'meta_keywords' ) . '</textarea></div></div>
<div class="row"><span>' . t( 'settings_form_metadesc', "Description" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_description">' . \query\main::get_option( 'meta_description' ) . '</textarea></div></div>';

echo '<div class="section-content">
    <div class="title">
        <h2>' . t( 'settings_meta_pcoupon', "Coupon Page" ) . '</h2>
    </div>

    <div class="content">
        <div class="row"><span>' . t( 'settings_form_metatitle', "Title" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%NAME%, %STORE_NAME%, %EXPIRATION%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_coupon_title" value="' . esc_html( \query\main::get_option( 'meta_coupon_title' ) ) . '" /></div></div>
        <div class="row"><span>' . t( 'settings_form_metakeywords', "Keywords" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%NAME%, %STORE_NAME%, %EXPIRATION%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_coupon_keywords">' . \query\main::get_option( 'meta_coupon_keywords' ) . '</textarea></div></div>
        <div class="row"><span>' . t( 'settings_form_metadesc', "Description" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%NAME%, %STORE_NAME%, %EXPIRATION%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_coupon_desc">' . \query\main::get_option( 'meta_coupon_desc' ) . '</textarea></div></div>
    </div>
</div>';

echo '<div class="section-content">
    <div class="title">
        <h2>' . t( 'settings_meta_pproduct', "Product Page" ) . '</h2>
    </div>

    <div class="content">
        <div class="row"><span>' . t( 'settings_form_metatitle', "Title" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%NAME%, %STORE_NAME%, %EXPIRATION%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_product_title" value="' . esc_html( \query\main::get_option( 'meta_product_title' ) ) . '" /></div></div>
        <div class="row"><span>' . t( 'settings_form_metakeywords', "Keywords" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%NAME%, %STORE_NAME%, %EXPIRATION%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_product_keywords">' . \query\main::get_option( 'meta_product_keywords' ) . '</textarea></div></div>
        <div class="row"><span>' . t( 'settings_form_metadesc', "Description" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%NAME%, %STORE_NAME%, %EXPIRATION%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_product_desc">' . \query\main::get_option( 'meta_product_desc' ) . '</textarea></div></div>
    </div>
</div>';

echo '<div class="section-content">
    <div class="title">
        <h2>' . t( 'settings_meta_pstore', "Store Page" ) . '</h2>
    </div>

    <div class="content">
        <div class="row"><span>' . t( 'settings_form_metatitle', "Title" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%NAME%, %DESCRIPTION%, %COUPONS%, %REVIEWS%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_store_title" value="' . esc_html( \query\main::get_option( 'meta_store_title' ) ) . '" /></div></div>
        <div class="row"><span>' . t( 'settings_form_metakeywords', "Keywords" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%NAME%, %DESCRIPTION%, %COUPONS%, %REVIEWS%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_store_keywords">' . \query\main::get_option( 'meta_store_keywords' ) . '</textarea></div></div>
        <div class="row"><span>' . t( 'settings_form_metadesc', "Description" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%NAME%, %DESCRIPTION%, %COUPONS%, %REVIEWS%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_store_desc">' . \query\main::get_option( 'meta_store_desc' ) . '</textarea></div></div>
    </div>
</div>';

echo '<div class="section-content">
    <div class="title">
        <h2>' . t( 'settings_meta_previews', "Reviews Page" ) . '</h2>
    </div>

    <div class="content">
        <div class="row"><span>' . t( 'settings_form_metatitle', "Title" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%NAME%, %COUPONS%, %REVIEWS%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_reviews_title" value="' . esc_html( \query\main::get_option( 'meta_reviews_title' ) ) . '" /></div></div>
        <div class="row"><span>' . t( 'settings_form_metakeywords', "Keywords" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%NAME%, %COUPONS%, %REVIEWS%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_reviews_keywords">' . \query\main::get_option( 'meta_reviews_keywords' ) . '</textarea></div></div>
        <div class="row"><span>' . t( 'settings_form_metadesc', "Description" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%NAME%, %COUPONS%, %REVIEWS%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_reviews_desc">' . \query\main::get_option( 'meta_reviews_desc' ) . '</textarea></div></div>
    </div>
</div>';

echo '<div class="section-content">
    <div class="title">
        <h2>' . t( 'settings_meta_pcategory', "Category Page" ) . '</h2>
    </div>

    <div class="content">
        <div class="row"><span>' . t( 'settings_form_metatitle', "Title" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%NAME%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><input type="text" name="meta_category_title" value="' . esc_html( \query\main::get_option( 'meta_category_title' ) ) . '" /></div></div>
        <div class="row"><span>' . t( 'settings_form_metakeywords', "Keywords" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%NAME%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_category_keywords">' . \query\main::get_option( 'meta_category_keywords' ) . '</textarea></div></div>
        <div class="row"><span>' . t( 'settings_form_metadesc', "Description" ) . ' <span class="info"><span>' . sprintf( t( 'settings_form_imetatitle', "Supported shortcodes: %s" ), '%NAME%, %MONTH%, %YEAR%' ) . '</span></span>:</span><div><textarea name="meta_category_desc">' . \query\main::get_option( 'meta_category_desc' ) . '</textarea></div></div>
    </div>
</div>';

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'settings_save_button', "Save Settings" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

/** APIs AND EXTERNAL ACCOUNTS */

case 'api':

echo '<div class="title">

<h2>' . t( 'settings_general_title', "Modify Site Settings" ) . '</h2>';

$subtitle = t( 'settings_api_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_settings_page', 'after_title_api_settings_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

    if( isset( $_POST['fbid'] ) && isset( $_POST['fbsecret'] ) && isset( $_POST['ggclient'] ) && isset( $_POST['ggsecret'] ) && isset( $_POST['gguri'] ) && isset( $_POST['ggkey'] ) && isset( $_POST['ppmode'] ) && isset( $_POST['ppid'] ) && isset( $_POST['ppsecret'] ) && isset( $_POST['feed_server'] ) && isset( $_POST['ggauth'] ) && isset( $_POST['ggcid'] ) && isset( $_POST['ggcsecret'] ) )
    if( admin\actions::set_option(
    array(
    'facebook_appID' => $_POST['fbid'],
    'facebook_secret' => $_POST['fbsecret'],
    'google_clientID' => $_POST['ggclient'],
    'google_secret' => $_POST['ggsecret'],
    'google_ruri' => $_POST['gguri'],
    'google_maps_key' => $_POST['ggkey'],
    'google_maps_places' => ( !empty( $_POST['ggplaces'] ) ? 1 : 0 ),
    'paypal_mode' => $_POST['ppmode'],
    'paypal_ID' => $_POST['ppid'],
    'paypal_secret' => $_POST['ppsecret'],
    'feedserver' => $_POST['feed_server'],
    'feedserver_auth' => $_POST['ggauth'],
    'feedserver_ID' => $_POST['ggcid'],
    'feedserver_secret' => $_POST['ggcsecret']
    ) ) )

    echo '<div class="a-success" style="margin-bottom: 15px;">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error" style="margin-bottom: 15px;">' . t( 'settings_save_error', "One or more options can't be saved." ) . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST">

<div class="section-content" style="margin-top:0;">
    <div class="title">
        <h2>Facebook</h2>
    </div>

    <div class="content">
        <div class="row"><span>' . t( 'settings_form_fbid', "Facebook AppID" ) . ':</span><div><input type="text" name="fbid" value="' . esc_html( \query\main::get_option( 'facebook_appID' ) ) . '" /></div></div>
        <div class="row"><span>' . t( 'settings_form_fbsecret', "Facebook Secret" ) . ':</span><div><input type="text" name="fbsecret" value="' . esc_html( \query\main::get_option( 'facebook_secret' ) ) . '" /></div></div>
    </div>
</div>

<div class="section-content">
    <div class="title">
        <h2>Google</h2>
    </div>

    <div class="content">
        <div class="row"><span>' . t( 'settings_form_ggclid', "Google ClientID" ) . ':</span><div><input type="text" name="ggclient" value="' . esc_html( \query\main::get_option( 'google_clientID' ) ) . '" /></div></div>
        <div class="row"><span>' . t( 'settings_form_ggsec', "Google Secret" ) . ':</span><div><input type="text" name="ggsecret" value="' . esc_html( \query\main::get_option( 'google_secret' ) ) . '" /></div></div>
        <div class="row"><span>' . t( 'settings_form_ggruri', "Google Request URI" ) . ':</span><div><input type="text" name="gguri" value="' . esc_html( \query\main::get_option( 'google_ruri' ) ) . '" /></div></div>
    </div>
</div>

<div class="section-content">
    <div class="title">
        <h2>Google Maps</h2>
    </div>

    <div class="content">
        <div class="row"><span>' . t( 'settings_form_ggkey', "Google Key" ) . ':</span><div><input type="text" name="ggkey" value="' . esc_html( \query\main::get_option( 'google_maps_key' ) ) . '" /></div></div>
        <div class="row"><span>' . t( 'settings_form_ggplaces', "Google Places API" ) . ':</span><div><input id="ggplaces" type="checkbox" name="ggplaces"' . ( (boolean) \query\main::get_option( 'google_maps_places' ) ? ' checked' : '' ) . ' /><label for="ggplaces"><span></span> ' . t( 'settings_form_ggplaces_label', 'Allow Places API, used to search locations' ) . '</label></div></div>
    </div>
</div>

<div class="section-content">
    <div class="title">
        <h2>PayPal</h2>
    </div>

    <div class="content">
        <div class="row"><span>' . t( 'settings_form_paypmode', "Mode" ) . ':</span>
        <div><select name="ppmode">';
        $paypal_mode = strtolower( \query\main::get_option( 'paypal_mode' ) );
        foreach( array( 'sandbox' => 'Sandbox', 'live' => 'Live' ) as $k => $v )echo '<option value="' . $k . '"' . ( $k == $paypal_mode ? ' selected' : '' ) . '>' . $v . '</option>';
        echo '</select>
        </div></div>

        <div class="row"><span>' . t( 'settings_form_paypid', "PayPal ClientID" ) . ':</span><div><input type="text" name="ppid" value="' . esc_html( \query\main::get_option( 'paypal_ID' ) ) . '" /></div></div>
        <div class="row"><span>' . t( 'settings_form_paypsecret', "PayPal Secret" ) . ':</span><div><input type="text" name="ppsecret" value="' . esc_html( \query\main::get_option( 'paypal_secret' ) ) . '" /></div></div>
    </div>
</div>

<div class="section-content">
    <div class="title">
        <h2>Feed Server</h2>
    </div>

    <div class="content">
        <div class="row"><span>' . t( 'settings_form_feedserver', "Server" ) . ':</span>
        <div><select name="feed_server">';
        $myserver = strtolower( \query\main::get_option( 'feedserver' ) );
        foreach( \site\feed::servers() as $k => $v )echo '<option value="' . $k . '"' . ( $k == $myserver ? ' selected' : '' ) . '>' . esc_html( $v['name'] ) . '</option>';
        echo '</select>
        </div></div>

        <div class="row"><span>' . t( 'settings_form_feedauth', "Authentication" ) . ':</span>
        <div>
        <select name="ggauth">';
        $auth_type = \query\main::get_option( 'feedserver_auth' );
        foreach( array( 'GET' => t( 'settings_select_getparam', "GET Parameter" ), 'HTTP' => t( 'settings_select_httpauth', "HTTP Authentication" ) ) as $k => $v ) echo '<option value="' . $k . '"' . ( strcasecmp( $k, $auth_type ) == 0 ? ' selected' : '' ) . '>' . $v . '</option>';
        echo '</select>
        </div></div>

        <div class="row"><span>' . t( 'settings_form_ggid', "User ID" ) . ':</span><div><input type="text" name="ggcid" value="' . esc_html( \query\main::get_option( 'feedserver_ID' ) ) . '" /></div></div>
        <div class="row"><span>' . t( 'settings_form_ggsecret', "User Secret" ) . ':</span><div><input type="text" name="ggcsecret" value="' . esc_html( \query\main::get_option( 'feedserver_secret' ) ) . '" /></div></div>
    </div>
</div>';

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'settings_save_button', "Save Settings" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

/** SEO LINKS */

case 'seolinks':

echo '<div class="title">

<h2>' . t( 'settings_general_title', "Modify Site Settings" ) . '</h2>';

$subtitle = t( 'settings_seolinks_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_settings_page', 'after_title_seo_links_settings_page' ) );

$extensions = array( '' => t( 'none', "None" ), '.htm' => '.htm', '.html' => '.html', '.php' => '.php' );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['post'] ) && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

    $post = array_map( function( $w ) {
        return preg_replace( '/[^a-z0-9$]/i', '', strtolower( $w ) );
    }, $_POST['post'] );

    if( admin\actions::set_option(
    array(
    'seo_link_coupon' => $post['coupon'],
    'seo_link_product' => $post['product'],
    'seo_link_store' => $post['store'],
    'seo_link_reviews' => $post['reviews'],
    'seo_link_category' => $post['category'],
    'seo_link_stores' => $post['stores'],
    'seo_link_search' => $post['search'],
    'seo_link_user' => $post['user'],
    'seo_link_plugin' => $post['plugin'],
    'extension' => isset( $_POST['extension'] ) && in_array( $_POST['extension'], array_keys( $extensions ) ) ? $_POST['extension'] : ''
    ) ) )

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'settings_save_error', "One or more options can't be saved." ) . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . t( 'settings_form_lcoupon', "Coupon Link" ) . ':</span><div><input type="text" name="post[coupon]" value="' . esc_html( ( $seo_coupon = \query\main::get_option( 'seo_link_coupon' ) ) ) . '" maxlength="32" class="sinspan" required />
' . ( $site_url = rtrim( $GLOBALS['siteURL'], '/' ) ) . '/<span style="color: #7D0000;">' . $seo_coupon . '</span>/example_coupon-1<span class="dlinkext">' . ( $seo_ext = esc_html( \query\main::get_option( 'extension' ) ) ) . '</span></div></div>

<div class="row"><span>' . t( 'settings_form_lproduct', "Product Link" ) . ':</span><div><input type="text" name="post[product]" value="' . esc_html( ( $seo_product = \query\main::get_option( 'seo_link_product' ) ) ) . '" maxlength="32" class="sinspan" required />
' . ( $site_url = rtrim( $GLOBALS['siteURL'], '/' ) ) . '/<span style="color: #7D0000;">' . $seo_product . '</span>/example_product-1<span class="dlinkext">' . $seo_ext . '</span></div></div>

<div class="row"><span>' . t( 'settings_form_lstore', "Store Link" ) . ':</span><div><input type="text" name="post[store]" value="' . esc_html( ( $seo_store = \query\main::get_option( 'seo_link_store' ) ) ) . '" maxlength="32" class="sinspan" required />
' . $site_url . '/<span style="color: #7D0000;">' . $seo_store . '</span>/example_store-1<span class="dlinkext">' . $seo_ext . '</span></div></div>

<div class="row"><span>' . t( 'settings_form_lreviews', "Reviews Link" ) . ':</span><div><input type="text" name="post[reviews]" value="' . esc_html( ( $seo_reviews = \query\main::get_option( 'seo_link_reviews' ) ) ) . '" maxlength="32" class="sinspan" required />
' . $site_url . '/<span style="color: #7D0000;">' . $seo_reviews . '</span>/example_store-1<span class="dlinkext">' . $seo_ext . '</span></div></div>

<div class="row"><span>' . t( 'settings_form_lcategory', "Category Link" ) . ':</span><div><input type="text" name="post[category]" value="' . esc_html( ( $seo_category = \query\main::get_option( 'seo_link_category' ) ) ) . '" maxlength="32" class="sinspan" required />
' . $site_url . '/<span style="color: #7D0000;">' . $seo_category . '</span>/example_category-1<span class="dlinkext">' . $seo_ext . '</span></div></div>

<div class="row"><span>' . t( 'settings_form_lstores', "Stores Link" ) . ':</span><div><input type="text" name="post[stores]" value="' . esc_html( ( $seo_stores = \query\main::get_option( 'seo_link_stores' ) ) ) . '" maxlength="32" class="sinspan" required />
' . $site_url . '/<span style="color: #7D0000;">' . $seo_stores . '</span><span class="dlinkext">' . $seo_ext . '</span></div></div>

<div class="row"><span>' . t( 'settings_form_lsearch', "Search Link" ) . ':</span><div><input type="text" name="post[search]" value="' . esc_html( ( $seo_search = \query\main::get_option( 'seo_link_search' ) ) ) . '" maxlength="32" class="sinspan" required />
' . $site_url . '/<span style="color: #7D0000;">' . $seo_search . '</span><span class="dlinkext">' . $seo_ext . '</span>?s=example</div></div>

<div class="row"><span>' . t( 'settings_form_luser', "User Link" ) . ':</span><div><input type="text" name="post[user]" value="' . esc_html( ( $seo_user = \query\main::get_option( 'seo_link_user' ) ) ) . '" maxlength="32" class="sinspan" required />
' . $site_url . '/<span style="color: #7D0000;">' . $seo_user . '</span>/example<span class="dlinkext">' . $seo_ext . '</span></div></div>

<div class="row"><span>' . t( 'settings_form_lplugin', "Plugin Link" ) . ':</span><div><input type="text" name="post[plugin]" value="' . esc_html( ( $seo_plugin = \query\main::get_option( 'seo_link_plugin' ) ) ) . '" maxlength="32"    class="sinspan" required />
' . $site_url . '/<span style="color: #7D0000;">' . $seo_plugin . '</span>/example_plugin<span class="dlinkext">' . $seo_ext . '</span></div></div>

<div class="row"><span>' . t( 'extension', "Extension" ) . ':</span><div>';
foreach( $extensions as $k => $v ) {
    echo '<input type="radio" name="extension" value="' . $k . '" id="ext' . $k . '" class="cextension"' . ( $seo_ext == $k ? ' checked' : '' ) . ' /> <label for="ext' . $k . '"><span></span> ' . $v . '</label> ';
}
echo '</div></div>';

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'settings_save_button', "Save Settings" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

/** PRICES */

case 'prices':

echo '<div class="title">

<h2>' . t( 'settings_general_title', "Modify Site Settings" ) . '</h2>';

$subtitle = t( 'settings_prices_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_settings_page', 'after_title_prices_settings_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

    if( isset( $_POST['store'] ) && isset( $_POST['coupon'] ) && isset( $_POST['product'] ) )
    if( admin\actions::set_option(
    array(
    'price_store' => $_POST['store'],
    'price_coupon' => $_POST['coupon'],
    'price_product' => $_POST['product']
    ) ) )

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'settings_save_error', "One or more options can't be saved." ) . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . t( 'settings_from_pricstore', "Store (credits)" ) . ' <span class="info"><span>' . t( 'settings_from_ipricstore', "One time fee. Will be paid only when a new store is added." ) . '</span></span>:</span><div><input type="number" name="store" value="' . (int) \query\main::get_option( 'price_store' ) . '" min="0" /></div></div>
<div class="row"><span>' . t( 'settings_from_priccoupon', "Coupon (credits per day)" ) . ' <span class="info"><span>' . t( 'settings_from_ipriccoupon', "Number of credits per day for - sponsored - badge and its benefits" ) . '</span></span>:</span><div><input type="number" name="coupon" value="' . (int) \query\main::get_option( 'price_coupon' ) . '" min="0" /></div></div>
<div class="row"><span>' . t( 'settings_from_pricproduct', "Product (credits per day)" ) . ' <span class="info"><span>' . t( 'settings_from_ipriccoupon', "Number of credits per day for - sponsored - badge and its benefits" ) . '</span></span>:</span><div><input type="number" name="product" value="' . (int) \query\main::get_option( 'price_product' ) . '" min="0" /></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'settings_save_button', "Save Settings" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

/** FEED SETTINGS */

case 'feed':

echo '<div class="title">

<h2>' . t( 'settings_general_title', "Modify Site Settings" ) . '</h2>';

$subtitle = t( 'settings_feed_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_settings_page', 'after_title_feed_settings_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

    if( admin\actions::set_option(
    array(
    'feed_uppics' => ( isset( $_POST['uphotos'] ) ? 1 : 0 ),
    'feed_iexpc' => ( isset( $_POST['feed_iexpc'] ) ? 1 : 0 ),
    'feed_iexpp' => ( isset( $_POST['feed_iexpp'] ) ? 1 : 0 ),
    'feed_moddt' => ( isset( $_POST['feed_moddt'] ) ? 1 : 0 )
    ) ) )

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'settings_save_error', "One or more options can't be saved." ) . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . t( 'settings_form_fimimg', "Upload Images" ) . ':</span><div><input type="checkbox" name="uphotos" id="uphotos"' . ( \query\main::get_option( 'feed_uppics' ) ? ' checked' : '' ) . '/> <label for="uphotos"><span></span> ' . t( 'msg_feed_upload_imgs', "Upload images on my server" ) . '</label></div></div>
<div class="row"><span>' . t( 'settings_form_prefc', "Coupons Preferences" ) . ':</span><div>
<input type="checkbox" name="feed_iexpc" id="feed_iexpc"' . ( \query\main::get_option( 'feed_iexpc' ) ? ' checked' : '' ) . ' /> <label for="feed_iexpc"><span></span> ' . t( 'msg_feed_cpnpref_impexp', "Import expired coupons" ) . '</label>
</div></div>
<div class="row"><span>' . t( 'settings_form_prefp', "Products Preferences" ) . ':</span><div>
<input type="checkbox" name="feed_iexpp" id="feed_iexpp"' . ( \query\main::get_option( 'feed_iexpp' ) ? ' checked' : '' ) . ' /> <label for="feed_iexpp"><span></span> ' . t( 'msg_feed_ppnpref_impexp', "Import expired products" ) . '</label>
</div></div>
<div class="row"><span></span><div><input type="checkbox" name="feed_moddt" id="feed_moddt"' . ( \query\main::get_option( 'feed_moddt' ) ? ' checked' : '' ) . ' /> <label for="feed_moddt"><span></span> ' . t( 'msg_feed_cpnpref_moddt', "Allow the source to modify information" ) . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'settings_save_button', "Save Settings" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

/** CRON LINKS */

case 'cron':

echo '<div class="title">

<h2>' . t( 'settings_general_title', "Modify Site Settings" ) . '</h2>';

$subtitle = t( 'settings_cron_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_settings_page', 'after_title_cron_settings_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

    if( admin\actions::set_option(
    array(
    'cron_secret' => md5( \site\utils::str_random(10) )
    ) ) )

    echo '<div class="a-success" style="margin-bottom: 15px;">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error" style="margin-bottom: 15px;">' . t( 'settings_save_error', "One or more options can't be saved." ) . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

$secret = \query\main::get_option( 'cron_secret' );

echo t( 'settings_cron_d', "In UNIX, Cron it's a job scheduler which allows to execute some actions at some point in time. In our case, we use Cron to clear information and import coupons automatically." );

echo '<div class="title" style="margin-top: 20px;">
    <h2>' . t( 'settings_cron_clrdat', "Clear Data" ) . '</h2>
</div>';

echo '<div class="a-message">' . t( 'settings_cron_clrdat_d', "The following line will check to clear expired information every 5 minutes." ) . '</div>
<div class="page-toolbar hideinput"><input type="text" value="*/5 * * * * wget -O - ' . $GLOBALS['siteURL'] . ( defined( 'SEO_LINKS' ) && SEO_LINKS ? 'cron/cleardata.php?secret=' . $secret : '?cron=cleardata&amp;secret=' . $secret ) . ' >/dev/null 2>&amp;1" onfocus="$(this).select();" readonly /></div>';

echo '<div class="title" style="margin-top: 20px;">
    <h2>' . t( 'settings_cron_feedi', "Feed Import" ) . '</h2>
</div>';

echo '<div class="a-message">' . t( 'settings_cron_feedi_d', "The following line will fetch information from the Feed source every 6 hours. (import coupons, update coupons in case you allow that)." ) . '</div>
<div class="page-toolbar hideinput"><input type="text" value="* */6 * * * wget -O - ' . $GLOBALS['siteURL'] . ( defined( 'SEO_LINKS' ) && SEO_LINKS ? 'cron/feed.php?secret=' . $secret : '?cron=feed&amp;secret=' . $secret ) . ' >/dev/null 2>&amp;1" onfocus="$(this).select();" readonly /></div>';
echo '<form action="#" method="POST">

<div style="margin:20px 0;">' . t( 'settings_cron_sec_d', "The links above can be also accessed in any browser. To not be used by unauthorized persons these are protected by a 'secret' parameter. This parameter can be changed as many times you want. Old parameter will be automatically deleted by generating the next one." ) . '</div>

<button class="btn">' . t( 'settings_cron_sec_cbtn', "Change 'Secret'" ) . '</button>

<input type="hidden" name="csrf" value="' . $csrf . '" />

</form>';

break;

/** SOCIAL NETWORKS */

case 'socialacc':

echo '<div class="title">

<h2>' . t( 'settings_general_title', "Modify Site Settings" ) . '</h2>';

$subtitle = t( 'settings_socnet_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_settings_page', 'after_title_profiles_settings_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['post'] ) && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

    $post = array_map( function( $w ) {
        if( preg_match( '/^http(s)?:\/\//i', $w ) ) {
            return substr( $w, 0, 200 );
        }
    }, $_POST['post'] );

    if( admin\actions::set_option(
    array(
    'social_facebook' => $post['facebook'],
    'social_google' => $post['google'],
    'social_twitter' => $post['twitter'],
    'social_flickr' => $post['flickr'],
    'social_linkedin' => $post['linkedin'],
    'social_vimeo' => $post['videmo'],
    'social_youtube' => $post['youtube'],
    'social_myspace' => $post['myspace'],
    'social_reddit' => $post['reddit'],
    'social_pinterest' => $post['pinterest']
    ) ) )

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'settings_save_error', "One or more options can't be saved." ) . '</div>';

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>Facebook:</span><div><input type="text" name="post[facebook]" value="' . esc_html( \query\main::get_option( 'social_facebook' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Google+:</span><div><input type="text" name="post[google]" value="' . esc_html( \query\main::get_option( 'social_google' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Twitter:</span><div><input type="text" name="post[twitter]" value="' . esc_html( \query\main::get_option( 'social_twitter' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Flickr:</span><div><input type="text" name="post[flickr]" value="' . esc_html( \query\main::get_option( 'social_flickr' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Linkedin:</span><div><input type="text" name="post[linkedin]" value="' . esc_html( \query\main::get_option( 'social_linkedin' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Vimeo:</span><div><input type="text" name="post[videmo]" value="' . esc_html( \query\main::get_option( 'social_vimeo' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Youtube:</span><div><input type="text" name="post[youtube]" value="' . esc_html( \query\main::get_option( 'social_youtube' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>MySpace:</span><div><input type="text" name="post[myspace]" value="' . esc_html( \query\main::get_option( 'social_myspace' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Reddit:</span><div><input type="text" name="post[reddit]" value="' . esc_html( \query\main::get_option( 'social_reddit' ) ) . '" maxlength="200" /></div></div>
<div class="row"><span>Pinterest:</span><div><input type="text" name="post[pinterest]" value="' . esc_html( \query\main::get_option( 'social_pinterest' ) ) . '" maxlength="200" /></div></div>';

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'settings_save_button', "Save Settings" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

/** THEME &    */

case 'theme':

echo '<div class="title">

<h2>' . t( 'settings_general_title', "Modify Site Settings" ) . '</h2>';

$subtitle = t( 'settings_theme_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_settings_page', 'after_title_theme_settings_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

    if( isset( $_FILES['logo'] ) && isset( $_FILES['favicon'] ) ) {

    // delete old favicons and/or logos
    $files = glob( DIR . '/' . UPLOAD_IMAGES_LOC . '/{logo,favicon}.{jpg,png,gif,svg}', GLOB_BRACE );
    foreach( $files as $file ){
        $bn = strstr( basename( $file ), '.', true );
        if( isset( $_FILES[$bn] ) && $_FILES[$bn]['size'] > 0 ) {
        @unlink( $file );
        }
    }

    if( admin\actions::set_option(
    array(
    'site_logo' => ( $_FILES['logo']['size'] > 0 ? $GLOBALS['siteURL'] . \site\images::upload( $_FILES['logo'], '', array( 'name' => 'logo', 'path' => DIR . '/' ) ) : \query\main::get_option( 'site_logo' ) ),
    'site_favicon' => ( $_FILES['favicon']['size'] > 0 ? $GLOBALS['siteURL'] . \site\images::upload( $_FILES['favicon'], '', array( 'name' => 'favicon', 'path' => DIR . '/' ) ) : \query\main::get_option( 'site_favicon' ) ),
    'site_indexfollow' => isset( $_POST['search_engines'] ) ? 1 : 0
    ) ) )

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'settings_save_error', "One or more options can't be saved." ) . '</div>';

    }

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">

<div class="row image-upload"><span>' . t( 'settings_from_logo', "Site Logo" ) . ':</span>
<div>';
$logo = \query\main::get_option( 'site_logo' );
if( !empty( $logo ) ) {
echo '<img src="' . esc_html( $logo ). '" alt="" style="max-width:100px;max-height:100px;margin:0 20px 5px 0;" />';
} else {
echo t( 'msg_notset', "Not set" );
}
echo '<input type="file" name="logo" accept="image/*" />
</div> </div>

<div class="row image-upload"><span>' . t( 'settings_from_favicon', "Favicon" ) . ':</span>
<div>';
$favicon = \query\main::get_option( 'site_favicon' );
echo '<div>';
if( !empty( $favicon ) ) {
echo '<img src="' . esc_html( $favicon ) . '" alt="" style="max-width:50px;max-height:50px;margin:0 20px 5px 0;" />';
} else {
echo t( 'msg_notset', "Not set" );
}
echo '</div>';
echo '<input type="file" name="favicon" accept="image/*" />
</div> </div>

<div class="row"><span>' . t( 'settings_from_searchengines', "Search Engines" ) . ' <span class="info"><span>' . t( 'settings_from_isearchengines', "If you uncheck the option, this website will not be indexed or followed by search engines." ) . '</span></span>:</span><div><input type="checkbox" name="search_engines" id="search_engines"' . ( (boolean) \query\main::get_option( 'site_indexfollow' ) ? ' checked' : '' ) . ' /><label for="search_engines"><span></span> ' . t( 'msg_asearchengines', "Allow search engines to index and follow this website." ) . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'settings_save_button', "Save Settings" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

/** GENERAL SETTINGS */

default:

require_once DIR . '/' . IDIR . '/others/GMT_list.php';

echo '<div class="title">

<h2>' . t( 'settings_general_title', "Modify Site Settings" ) . '</h2>';

$subtitle = t( 'settings_general_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_settings_page', 'after_title_general_settings_page' ) );

if( isset( $_SESSION['js_settings'] ) ) {

    if( isset( $_GET['success'] ) && $_GET['success'] == 'true' )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else {
    echo '<div class="a-error">' . t( 'settings_save_error', "One or more options can't be saved." ) . '</div>';
    }

    unset( $_SESSION['js_settings'] );

}

$csrf = $_SESSION['settings_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="?route=post-actions.php&amp;action=general-settings" method="POST">

<div class="row"><span>' . t( 'settings_form_sitename', "Site Name" ) . ':</span><div><input type="text" name="sitename" value="' . esc_html( \query\main::get_option( 'sitename' ) ) . '" /></div></div>
<div class="row"><span>' . t( 'settings_form_siteurl', "Site URL" ) . ':</span><div><input type="text" name="siteurl" value="' . esc_html( \query\main::get_option( 'siteurl' ) ) . '" /></div></div>
<div class="row"><span>' . t( 'settings_form_sitedesc', "Site Description" ) . ':</span><div><textarea name="description">' . \query\main::get_option( 'sitedescription' ) . '</textarea></div></div>
<div class="row"><span>' . t( 'settings_form_maintenance', "Maintenance Mode" ) . ':</span>
<div>
<select name="maintenance">';
$maintenance = (boolean) \query\main::get_option( 'maintenance' );
foreach( array( 0 => t( 'no', "No" ), 1 => t( 'yes', "Yes" ) ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $maintenance ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>
<div class="row"><span>' . t( 'settings_form_itemspp', "Items Per Page" ) . ':</span><div><input type="number" name="ipp" value="' . (int) \query\main::get_option( 'items_per_page' ) . '" /></div></div>
<div class="row"><span>' . t( 'settings_form_userregs', "User Registrations" ) . ':</span>

<div>
<select name="registrations"><option value="opened">' . t( 'settings_select_opened', "Anyone can register" ) . '</option><option value="closed"' . ( \query\main::get_option( 'registrations' ) != 'opened' ? ' selected' : '' ) . '>' . t( 'settings_select_closed', "Nobody can register" ) . '</option></select>
</div></div>

<div class="row"><span>' . t( 'settings_form_accpip', "Accounts Per IP" ) . ':</span>
<div>
<select name="accounts_per_ip">';
$accounts_per_ip = \query\main::get_option( 'accounts_per_ip' );
foreach( array( 0 => t( 'unlimited', "Unlimited" ), 1 => '1 / IP', 2 => '2 / IP', 3 => '3 / IP', 4 => '4 / IP', 5 => '5 / IP', 7 => '7 / IP', 10 => '10 / IP' ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $accounts_per_ip ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_form_deleteoldc', "Delete Expired Coupons" ) . ':</span>
<div>
<select name="delete_old_coupons">';
$delete_old_coupons = \query\main::get_option( 'delete_old_coupons' );
foreach( array_merge( array( 0 => t( 'never', "Never" ), 1 => '1 ' . t( 'day', "Day" ) ), range( 2,15 ), array( 30, 45, 60, 120, 365 ) ) as $k => $v ) echo '<option value="' . (int) $k . '"' . ( $k == $delete_old_coupons ? ' selected' : '' ) . '>' . ( (int) $k > 1 ? $v . ' ' . t( 'days', "Days" ) : $v ) . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_form_deleteoldp', "Delete Expired Products" ) . ':</span>
<div>
<select name="delete_old_products">';
$delete_old_products = \query\main::get_option( 'delete_old_products' );
foreach( array_merge( array( 0 => t( 'never', "Never" ), 1 => '1 ' . t( 'day', "Day" ) ), range( 2,15 ), array( 30, 45, 60, 120, 365 ) ) as $k => $v ) echo '<option value="' . (int) $k . '"' . ( $k == $delete_old_products ? ' selected' : '' ) . '>' . ( (int) $k > 1 ? $v . ' ' . t( 'days', "Days" ) : $v ) . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_form_deleteoldv', "Delete Old Votes" ) . ':</span>
<div>
<select name="delete_old_votes">';
$delete_old_coupons = \query\main::get_option( 'delete_old_votes' );
foreach( array_merge( array( 0 => t( 'never', "Never" ), 1 => '1 ' . t( 'day', "Day" ) ), range( 2,15 ), array( 30, 45, 60, 120, 365 ) ) as $k => $v ) echo '<option value="' . (int) $k . '"' . ( $k == $delete_old_coupons ? ' selected' : '' ) . '>' . ( (int) $k > 1 ? $v . ' ' . t( 'days', "Days" ) : $v ) . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_form_allowvotes', "Allow New Votes" ) . ':</span>
<div>
<select name="allow_votes">';
$allow_votes = \query\main::get_option( 'allow_votes' );
foreach( array( 0 => t( 'no', "No" ), 1 => t( 'yes', "Yes" ), 2 => t( 'settings_option_onlymembers', "Only by members" ) ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $allow_votes ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_form_allowrev', "Allow New Reviews" ) . ':</span>
<div>
<select name="allow_revs">';
$allow_reviews = \query\main::get_option( 'allow_reviews' );
foreach( array( 0 => t( 'no', "No" ), 1 => t( 'yes', "Yes" ), 2 => t( 'settings_option_onlyvalid', "Only by verified members" ) ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $allow_reviews ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_form_autovalrev', "Autovalidate Reviews" ) . ':</span>
<div>
<select name="auvalid_revs">';
$review_validate = (boolean) \query\main::get_option( 'review_validate' );
foreach( array( 0 => t( 'no', "No" ), 1 => t( 'yes', "Yes" ) ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $review_validate ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_form_allowsto', "Allow New Stores" ) . ':</span>
<div>
<select name="allow_stores">';
$allow_stores = (boolean) \query\main::get_option( 'allow_stores' );
foreach( array( 0 => t( 'no', "No" ), 1 => t( 'yes', "Yes" ) ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $allow_stores ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_form_autovalsto', "Autovalidate Stores" ) . ':</span>
<div>
<select name="auvalid_stos">';
$store_validate = (boolean) \query\main::get_option( 'store_validate' );
foreach( array( 0 => t( 'no', "No" ), 1 => t( 'yes', "Yes" ) ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $store_validate ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_form_allowcou', "Allow New Coupons" ) . ':</span>
<div>
<select name="allow_coupons">';
$allow_coupons = (boolean) \query\main::get_option( 'allow_coupons' );
foreach( array( 0 => t( 'no', "No" ), 1 => t( 'yes', "Yes" ) ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $allow_coupons ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_form_autovalcou', "Autovalidate Coupons" ) . ':</span>
<div>
<select name="auvalid_cous">';
$coupon_validate = (boolean) \query\main::get_option( 'coupon_validate' );
foreach( array( 0 => t( 'no', "No" ), 1 => t( 'yes', "Yes" ) ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $coupon_validate ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_form_allowprod', "Allow New Products" ) . ':</span>
<div>
<select name="allow_products">';
$allow_coupons = (boolean) \query\main::get_option( 'allow_products' );
foreach( array( 0 => t( 'no', "No" ), 1 => t( 'yes', "Yes" ) ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $allow_coupons ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_form_autovalprod', "Autovalidate Products" ) . ':</span>
<div>
<select name="auvalid_prods">';
$coupon_validate = (boolean) \query\main::get_option( 'product_validate' );
foreach( array( 0 => t( 'no', "No" ), 1 => t( 'yes', "Yes" ) ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $coupon_validate ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_form_sitelang', "Site Language" ) . ':</span>
<div>
<select name="site_lang">';
$sitelang = \query\main::get_option( 'sitelang' );
foreach( ( $languages = \site\language::languages() ) as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $sitelang ? ' selected' : '' ) . '>' . esc_html( $v['name'] ) . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_form_adminlang', "Admin Panel Language" ) . ':</span>
<div>
<select name="adminpanel_lang">';
$adminlang = \query\main::get_option( 'adminpanel_lang' );
foreach( $languages as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $adminlang ? ' selected' : '' ) . '>' . esc_html( $v['name'] ) . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_form_adminthm', "Admin Panel Theme" ) . ':</span>
<div>
<select name="admin_theme">';
$admin_theme = \query\main::get_option( 'admintheme' );
$themes = $GLOBALS['admin_main_class']->admin_themes();
foreach( $themes as $id => $theme ) echo '<option value="' . $id . '" data-theme-preview="' . $theme['src']['css'][0] . '"' . ( $id == $admin_theme ? ' selected' : '' ) . '>' . esc_html( $theme['name'] ) . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'settings_timezone', "Timezone" ) . ':</span><div><select name="timezone">';
$timezone = \query\main::get_option( 'timezone' );        ;
foreach( $gmt as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $timezone ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select></div></div>

<div class="row"><span>' . t( 'settings_hour_format', "Hour Format" ) . ':</span><div><select name="hour_format">';
$hourformat = \query\main::get_option( 'hour_format' );
foreach( array( 12, 24 ) as $k ) echo '<option value="' . $k . '"' . ( $k == $hourformat ? ' selected' : '' ) . '>' . $k . (' ' . strtolower( t( 'hours', "Hours" ) )) . '</option>';
echo '</select></div></div>

<div class="row"><span>' . t( 'settings_form_emailfn', "Name Email" ) . ' <span class="info"><span>' . t( 'settings_form_iemailfn', "All sent emails appear as being sent by this name." ) . '</span></span>:</span><div><input type="text" name="email_from_name" value="' . esc_html( \query\main::get_option( 'email_from_name' ) ) . '" /></div></div>
<div class="row"><span>' . t( 'settings_form_emailas', "Answer Email" ) . ' <span class="info"><span>' . t( 'settings_form_iemailas', "All sent emails appear as being sent from this email address." ) . '</span></span>:</span><div><input type="email" name="email_answer_to" value="' . esc_html( \query\main::get_option( 'email_answer_to' ) ) . '" /></div></div>
<div class="row"><span>' . t( 'settings_form_emailcntct', "Contact Email" ) . ' <span class="info"><span>' . t( 'settings_form_iemailcntct', "On this email address you will receive emails from the contact form." ) . '</span></span>:</span><div><input type="email" name="email_contact" value="' . esc_html( \query\main::get_option( 'email_contact' ) ) . '" /></div></div>
<div class="row"><span>' . t( 'settings_form_mailmeth', "Mail Method" ) . ':</span><div><select name="mail_meth">';
foreach( array( 'PHP Mail', 'sendmail', 'SMTP' ) as $meth ) echo '<option value="' . $meth . '"' . ( \query\main::get_option( 'mail_method' ) == $meth ? ' selected' : '' ) . '>' . $meth . '</option>';
echo '</select></div></div>

<div' . ( \query\main::get_option( 'mail_method' ) != 'SMTP' ? ' style="display: none;"' : '' ) . '>
<div class="row"><span>' . t( 'settings_from_smtpauth', "SMTP Authentication" ) . ':</span><div><input type="checkbox" name="smtp_auth" id="smtp_auth"' . ( \query\main::get_option( 'smtp_auth' ) ? ' checked' : '' ) . ' /><label for="smtp_auth"></label></div></div>
<div class="row"><span>' . t( 'settings_from_smtphost', "SMTP Server" ) . ':</span><div><input type="text" name="smtp_host" value="' . esc_html( \query\main::get_option( 'smtp_host' ) ) . '" /></div></div>
<div class="row"><span>' . t( 'settings_from_smtpport', "SMTP Port" ) . ':</span><div><input type="text" name="smtp_port" value="' . esc_html( \query\main::get_option( 'smtp_port' ) ) . '" /></div></div>
<div class="row"><span>' . t( 'settings_from_smtpuser', "SMTP Username" ) . ':</span><div><input type="text" name="smtp_user" value="' . esc_html( \query\main::get_option( 'smtp_user' ) ). '" /></div></div>
<div class="row"><span>' . t( 'settings_from_smtppass', "SMTP Password" ) . ':</span><div><input type="text" name="smtp_pass" value="' . esc_html( \query\main::get_option( 'smtp_password' ) ) . '" /></div></div>
</div>

<div' . ( \query\main::get_option( 'mail_method' ) != 'sendmail' ? ' style="display: none;"' : '' ) . '>
<div class="row"><span>' . t( 'settings_from_snmapath', "Path to sendmail" ) . ':</span><div><input type="text" name="sendmail_path" value="' . esc_html( \query\main::get_option( 'sendmail_path' ) ) . '" /></div></div>
</div>

<div class="row"><span>' . t( 'settings_form_mailsign', "Emails Signature" ) . ':</span><div><textarea name="mailsign">' . \query\main::get_option( 'mail_signature' ) . '</textarea></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'settings_save_button', "Save Settings" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

}