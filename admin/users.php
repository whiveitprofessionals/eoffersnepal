<?php

switch( $_GET['action'] ) {

/** SEND EMAIL */

case 'sendmail':

if( !ab_to( array( 'mail' => 'send' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'users_sendmail_title', "Send Email" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=users.php&amp;action=list" class="btn">' . t( 'users_view', "View Users" ) . '</a>
</div>';

$subtitle = t( 'users_sendmail_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_user_page', 'after_title_send_mail_user_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'sendmail_csrf' ) ) {

    if( !empty( $_POST['fname'] ) && !empty( $_POST['femail'] ) && !empty( $_POST['temails'] ) && !empty( $_POST['subject'] ) && !empty( $_POST['text'] ) ) {

    $suc = $err = 0;

    foreach( array_unique( array_filter( explode( ',', $_POST['temails'] ) ) ) as $email ) {

    if( \site\mail::send( trim( $email ), $_POST['subject'], array( 'template' => 'sendmail', 'path' => '../', 'from_email' => $_POST['femail'], 'from_name' => $_POST['fname'], 'reply_to' => $_POST['femail'], 'reply_name' => $_POST['fname'] ), array( 'text' => nl2br( $_POST['text'] ) ) ) ) {
        $suc++;
    } else $err++;

    }

    if( $suc > $err )
    echo '<div class="a-success">' . sprintf( t( 'msg_mailssent', "%s emails sent, %s errors." ), $suc, $err ) . '</div>';
    else
    echo '<div class="a-error">' . sprintf( t( 'msg_mailssent', "%s emails sent, %s errors." ), $suc, $err ) . '</div>';

    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf = $_SESSION['sendmail_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">

<div class="row"><span>' . t( 'users_sendmail_fname', "From Name" ) . ':</span><div><input type="text" name="fname" value="' . \query\main::get_option( 'email_from_name' ) . '" required /></div></div>
<div class="row"><span>' . t( 'users_sendmail_femail', "From Email" ) . ':</span><div><input type="text" name="femail" value="' . \query\main::get_option( 'email_answer_to' ) . '" required /></div></div>
<div class="row"><span>' . t( 'users_sendmail_temails', "To Emails" ) . ' <span class="info"><span>' . t( 'users_sendmail_itemails', "You can send to multiple email addresses, separate them with comma." ) . '</span></span>:</span><div><input type="text" name="temails" value="' . ( isset( $_GET['email'] ) ? esc_html( $_GET['email'] ) : '' ) . '" required /></div></div>
<div class="row"><span>' . t( 'users_sendmail_subject', "Subject" ) . ':</span><div><input type="text" name="subject" value="" required /></div></div>
<div class="row"><span>' . t( 'users_sendmail_text', "Message" ) . ':</span><div><textarea name="text" style="min-height: 200px;">' . esc_html( \query\main::get_option( 'mail_signature' ) ) . '</textarea></div></div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'users_sendmail_button', "Send Email" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

/** ADD USER */

case 'add':

if( !ab_to( array( 'users' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'users_add_title', "Add New User" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=users.php&amp;action=list" class="btn">' . t( 'users_view', "View Users" ) . '</a>
</div>';

$subtitle = t( 'users_add_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_user_page', 'after_title_add_user_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'users_csrf' ) ) {

    if( isset( $_POST['name'] ) && isset( $_POST['email'] ) && isset( $_POST['password'] ) && isset( $_POST['points'] ) && ( ! $GLOBALS['me']->is_admin || isset( $_POST['privileges'] ) && in_array( $_POST['privileges'], array( 0, 1, 2 ) ) ) )
    if( ( $new_user_id = admin\actions::add_user(
    value_with_filter( 'save_user_values', array(
    'name'          => ( isset( $_POST['name'] ) ? $_POST['name'] : '' ),
    'email'         => ( isset( $_POST['email'] ) ? $_POST['email'] : '' ),
    'password'      => ( isset( $_POST['password'] ) ? $_POST['password'] : '' ),
    'points'        => ( isset( $_POST['points'] ) ? (int) $_POST['points'] : 0 ),
    'credits'       => ( $GLOBALS['me']->is_admin && isset( $_POST['credits'] ) ? (int) $_POST['credits'] : 0 ),
    'privileges'    => ( $GLOBALS['me']->is_admin ? $_POST['privileges'] : array() ),
    'erole'         => ( $GLOBALS['me']->is_admin && isset( $_POST['erole'] ) && (int) $_POST['privileges'] === 1 ? $_POST['erole'] : array() ),
    'subscriber'    => ( isset( $_POST['subscriber'] ) ? 1 : 0 ),
    'confirm'       => ( isset( $_POST['confirm'] ) ? 1 : 0 ),
    'extra'         => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() )
    ) ) ) ) ) {

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';

    do_action( array( 'admin_user_added_edited', 'admin_user_added' ), $new_user_id );

    if( isset( $_POST['send_copy'] ) ) {
        \site\mail::send( $_POST['email'], t( 'email_ac_title', "Account created" ) . ' - ' . \query\main::get_option( 'sitename' ), array( 'template' => 'account_creation', 'path' => '../' ), array( 'ac_main_text' => sprintf( t( 'email_ac_maintext', "Your account on %s has been created." ), \query\main::get_option( 'sitename' ) ), 'form_email' => t( 'email_ac_email', "Email" ), 'form_password' => t( 'email_ac_password', "Password" ), 'email' => $_POST['email'], 'password' => $_POST['password'], 'link' => \query\main::get_option( 'siteurl' ) ) );
    }

    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf   = $_SESSION['users_csrf'] = \site\utils::str_random(10);

$main   = $GLOBALS['admin_main_class']->user_fields( array(), $csrf );
$fields = $main['fields'];

admin\widgets::get_page_tabs( $main );

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">';

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

do_action( array( 'admin_user_after_form_add_edit', 'admin_user_after_form_add' ) );

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'users_add_button', "Add User" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

/** EDIT USER */

case 'edit':

if( !( ab_to( array( 'users' => 'edit' ) ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'users_edit_title', "Edit User" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $user_exists = \query\main::user_exists( $_GET['id'] ) ) ) {

$info = \query\main::user_info( $_GET['id'], array( 'no_emoticons' => true, 'no_filters' => true ) );

echo '<div class="options">

<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( ab_to( array( 'users' => 'delete' ) ) ) echo '<li><a href="?route=users.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>';
if( $info->is_confirmed ) {
    echo '<li><a href="?route=users.php&amp;action=list&amp;type=unverify&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'unverify', "Unverify" ) . '</a></li>';
} else {
    echo '<li><a href="?route=users.php&amp;action=list&amp;type=verify&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'verify', "Verify" ) . '</a></li>';
}
if( ab_to( array( 'mail' => 'send' ) ) )     echo '<li><a href="?route=users.php&amp;action=sendmail&amp;email=' . $info->email . '">' . t( 'send_email', "Send Email" ) . '</a></li>';
echo '</ul>
</div>';

}

echo '<a href="?route=users.php&amp;action=list" class="btn">' . t( 'users_view', "View Users" ) . '</a>
</div>';

$subtitle = t( 'users_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $user_exists && ( ! $GLOBALS['me']->is_admin && $info->is_admin ) ) {

    echo '<div class="a-alert">' . t( 'can_edit_info', "Sorry ! You can't edit this." ) . '</div>';

} else if( $user_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_user_page', 'after_title_edit_user_page' ), $info->ID );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'users_csrf' ) ) {

if( isset( $_POST['ban_user'] ) || isset( $_POST['unban_user'] ) ) {

    if( admin\actions::ban_user( $_GET['id'],
    array(
    'date' => ( isset( $_POST['unban_user'] ) ? strtotime( '1 second ago' ) : ( isset( $_POST['expiration']['date'] ) && isset( $_POST['expiration']['hour'] ) ? strtotime( $_POST['expiration']['date'] . ',' . $_POST['expiration']['hour'] ) : strtotime( '1 second ago' ) ) )
    ) ) ) {

    $info = \query\main::user_info( $_GET['id'], array( 'no_emoticons' => true, 'no_filters' => true ) );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['change_password'] ) ) {

    if( isset( $_POST['password'] ) )
    if( admin\actions::change_user_password( $_GET['id'],
    array(
    'password' => $_POST['password']
    ) ) )

    echo '<div class="a-success">' . t( 'msg_changed', "Changed!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else {

    if( isset( $_POST['name'] ) && isset( $_POST['email'] ) && isset( $_POST['points'] ) && ( ! $GLOBALS['me']->is_admin || isset( $_POST['privileges'] ) && in_array( $_POST['privileges'], array( 0, 1, 2 ) ) ) )
    if( admin\actions::edit_user( $_GET['id'],
    value_with_filter( 'save_user_values', array(
    'name'          => ( isset( $_POST['name'] ) ? $_POST['name'] : '' ),
    'email'         => ( isset( $_POST['email'] ) ? $_POST['email'] : '' ),
    'points'        => ( isset( $_POST['points'] ) ? (int) $_POST['points'] : 0 ),
    'credits'       => ( $GLOBALS['me']->is_admin && isset( $_POST['credits'] ) ? (int) $_POST['credits'] : $info->credits ),
    'privileges'    => ( $GLOBALS['me']->is_admin ? $_POST['privileges'] : $info->privileges ),
    'erole'         => ( $GLOBALS['me']->is_admin && isset( $_POST['erole'] ) && (int) $_POST['privileges'] === 1 ? $_POST['erole'] : $info->erole ),
    'subscriber'    => ( isset( $_POST['subscriber'] ) ? 1 : 0 ),
    'confirm'       => ( isset( $_POST['confirm'] ) ? 1 : 0 ),
    'extra'         => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() )
    ) ) ) ) {

    $info = \query\main::user_info( $_GET['id'], array( 'no_emoticons' => true, 'no_filters' => true ) );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';

    do_action( array( 'admin_user_added_edited', 'admin_user_edited' ), $info->ID );

    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'users_csrf' ) ) {

if( $_GET['type'] == 'delete_avatar' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_user_avatar( $_GET['id'] ) ) {

    $info->avatar = '';

    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$_SESSION['users_csrf'] = $csrf;

$main   = $GLOBALS['admin_main_class']->user_fields( $info, $csrf );
$fields = $main['fields'];

admin\widgets::get_page_tabs( $main );

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">';

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

do_action( array( 'admin_user_after_form_add_edit', 'admin_user_after_form_edit' ), $info );

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'users_edit_button', "Edit User" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

if( ab_to( array( 'users' => 'ban' ) ) && !$info->is_admin ) {

echo '<div class="title" style="margin-top:40px;">

<h2>' . t( 'users_ban_title', "Ban User" ) . '</h2>

</div>';

if( $info->is_banned ) echo '<div class="a-error">' . sprintf( t( 'msg_banned_until', "Banned until %s" ), $info->ban ) . '</div>';

echo '<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>' . t( 'form_expiration_date', "Expiration Date" ) . ':</span><div><input type="date" name="expiration[date]" value="' .    date( 'Y-m-d', ( $info->is_banned ? strtotime( $info->ban ) : strtotime( '+1 week' ) ) ) . '" class="datepicker" style="display:inline-block;width:68%;margin-right:2%" /><input type="time" name="expiration[hour]" value="' . ( $info->is_banned ? date( 'H:i', strtotime( $info->ban ) ) : date( 'H:i' ) ) . '" class="hourpicker" style="display:inline-block;width:30%" /></div></div>';

if( !$info->is_banned ) echo '<div class="row"><span>' . t( 'form_fastchoice', "Fast Choice" ) . ':</span><div id="ban_fast_choice"><a href="#" data=\'{"interval":"day","nr":1}\'>1 ' . t( 'day', "Day" ) . '</a> / <a href="#" data=\'{"interval":"day","nr":2}\'>2 ' . t( 'days', "Days" ) . '</a> / <a href="#" data=\'{"interval":"day","nr":3}\'>3 ' . t( 'days', "Days" ) . '</a> / <a href="#" data=\'{"interval":"week","nr":1}\'>1 ' . t( 'week', "Week" ) . '</a> / <a href="#" data=\'{"interval":"month","nr":1}\'>1 ' . t( 'month', "Month" ) . '</a> / <a href="#" data=\'{"interval":"month","nr":3}\'>3 ' . t( 'months', "Months" ) . '</a> / <a href="#" data=\'{"interval":"month","nr":6}\'>6 ' . t( 'months', "Months" ) . '</a> / <a href="#" data=\'{"interval":"year","nr":1}\'>1 ' . t( 'year', "Year" ) . '</a></div></div>';

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important" name="ban_user">' . ( $info->is_banned ? t( 'users_ban_updatebutton', "Update Ban" ) : t( 'users_ban_button', "Ban User" ) ) . '</button>
    </div>
    <div>';
    if( $info->is_banned ) echo '<button class="btn" name="unban_user">' . t( 'users_unban_button', "Unban" ) . '</button>';
    echo '</div>
</div>

</form>

</div>';

}

echo '<div class="title" style="margin-top:40px;">

<h2>' . t( 'users_cp_title', "Change Password" ) . '</h2>

</div>

<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>' . t( 'form_new_password', "New Password" ) . ':</span><div><input type="password" name="password" value="" /></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important" name="change_password">' . t( 'users_cp_button', "Change Password" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>


<div class="title" style="margin-top:40px;">

<h2>' . t( 'users_info_title', "Information About This User" ) . '</h2>

</div>';

echo '<div class="info-table" style="padding-bottom:20px;">

<form action="?route=users.php&amp;action=edit&amp;id=' . $info->ID . '#info-table" method="POST" autocomplete="off">';

$stat_rows              = array();
$stat_rows['id']        = '<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>';
$referred_markup = '<div class="row"><span>' . t( 'reffered', "Referred" ) . ':</span> <div>';
if( empty( $info->refid ) ) $referred_markup .= t( 'no', "No" );
else {
    $ref_user = \query\main::user_info( $info->refid );
    $referred_markup .= ( empty( $ref_user->name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->refid . '">' . $ref_user->name . '</a>' : $ref_user->name ) );
}
$referred_markup .= '</div></div>';
$stat_rows['referred']  = $referred_markup;
$referrers_markup = '<div class="row"><span>' . t( 'referrers', "Referrers" ) . ':</span> <div>';
if( ( $referrers = \query\main::users( array( 'referrer' => $info->ID ) ) ) > 0 )
    $referrers_markup .= ( ab_to( array( 'users' => 'view' ) ) ? '<a href="?route=users.php&amp;action=list&amp;referrer=' . $info->ID . '">' . $referrers . '</a>' : $referrers );
else {
    $referrers_markup .= 0;
}
$referrers_markup .= '</div></div>';
$stat_rows['referrers'] = $referrers_markup;
$stat_rows['visits']    = '<div class="row"><span>' . t( 'visits', "Visits" ) . ':</span> <div>' . $info->visits . '</div></div>';
$stat_rows['reg_date']  = '<div class="row"><span>' . t( 'registered_on', "Registered on" ) . ':</span> <div>' . $info->date . '</div></div>';
$stat_rows['lv_date']   = '<div class="row"><span>' . t( 'last_visit', "Last visit" ) . ':</span> <div>' . $info->last_login . '</div></div>';
$stat_rows['la_date']   = '<div class="row"><span>' . t( 'last_action', "Last action" ) . ':</span> <div>' . $info->last_action . '</div></div>';
$stat_rows['reviews']   = '<div class="row"><span>' . t( 'reviews', "Reviews" ) . ':</span> <div>' . ( ab_to( array( 'reviews' => 'view' ) ) ? '<a href="?route=reviews.php&amp;action=list&amp;user=' . $info->ID . '">' . $info->reviews . '</a>' : $info->reviews ) . ( ab_to( array( 'reviews' => 'add' ) ) ? ' / <a href="?route=reviews.php&amp;action=add&amp;user=' . $info->ID . '">' . t( 'reviews_add_button', "Add Review" ) . '</a>' : '' ) . '</div></div>';
$stat_rows['stores']    = '<div class="row"><span>' . t( 'stores', "Stores" ) . ':</span> <div>' . ( ab_to( array( 'stores' => 'view' ) ) ? '<a href="?route=stores.php&amp;action=list&amp;user=' . $info->ID . '">' . $info->stores . '</a>' : $info->stores ) . ( ab_to( array( 'stores' => 'add' ) ) ? ' / <a href="?route=stores.php&amp;action=add&amp;user=' . $info->ID . '">' . t( 'stores_add_button', "Add Store" ) . '</a>' : '' ) . '</div></div>';
$stat_rows['coupons']   = '<div class="row"><span>' . t( 'coupons', "Coupons" ) . ':</span> <div>' . ( ab_to( array( 'coupons' => 'view' ) ) ? '<a href="?route=coupons.php&amp;action=list&amp;user=' . $info->ID . '">' . $info->coupons . '</a>' : $info->coupons ) . '</div></div>';
$stat_rows['products']  = '<div class="row"><span>' . t( 'products', "Products" ) . ':</span> <div>' . ( ab_to( array( 'products' => 'view' ) ) ? '<a href="?route=products.php&amp;action=list&amp;user=' . $info->ID . '">' . $info->products . '</a>' : $info->products ) . '</div></div>';

if( $GLOBALS['me']->is_admin ) {
    $stat_rows['ip']  = '<div class="row"><span>' . t( 'form_ip', "IP Address" ) . ':</span> <div><a href="?route=users.php&amp;action=list&amp;search=' . $info->IP . '">' . $info->IP . '</a> / <a href="?route=banned.php&amp;action=add&amp;ip=' . $info->IP . '">' . t( 'bann_ip', "Ban IP?" ) . '</a></div></div>';
}

echo implode( '', value_with_filter( 'admin_user_stats', $stat_rows ) );

echo '</form>

</div>';

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/** LIST OF USER SESSIONS */

case 'sessions':

if( ! $GLOBALS['me']->is_admin ) die;

echo '<div class="title">

<h2>' . t( 'sessions_title', "Active Sessions" ) . '</h2>';

$subtitle = t( 'sessions_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_user_page', 'after_title_sessions_user_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'users_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_sessions( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'users_csrf' ) ) {

if( $_GET['type'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_sessions( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['users_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="users.php" />
<input type="hidden" name="action" value="sessions" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="users.php" />
<input type="hidden" name="action" value="sessions" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'users_search_input', "Search users" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$p = admin\admin_query::have_usessions( $options = array( 'per_page' => 10, 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( !empty( $_GET['search'] ) ) echo ' / <a href="?route=users.php&amp;action=sessions">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=users.php&amp;action=sessions" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>

<div class="bulk_options">
    <button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button>
</div>';

foreach( admin\admin_query::while_usessions( array_merge( array( 'page' => $p['page'], 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

    $links = array();

    $links['edit_user'] = '<a href="?route=users.php&amp;action=edit&amp;id=' . $item->userID . '">' . t( 'sessions_edit_user', "Edit User" ) . '</a>';
    $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'active_session', $item, $links );

}

echo '</ul>

<input type="hidden" name="csrf" value="' . $csrf . '" />

</form>';

if( isset( $p['prev_page'] ) || isset( $p['next_page'] ) ) {
    echo '<div class="pagination">';

    if( isset( $p['prev_page'] ) ) echo '<a href="' . $p['prev_page'] . '" class="btn">' . t( 'prev_page', "&larr; Prev" ) . '</a>';
    if( isset( $p['next_page'] ) ) echo '<a href="' . $p['next_page'] . '" class="btn">' . t( 'next_page', "Next &rarr;" ) . '</a>';

    if( $p['pages'] > 1 ) {
    echo '<div class="pag_goto">' . sprintf( t( 'pageofpages', "Page <b>%s</b> of <b>%s</b>" ), $page = $p['page'], $pages = $p['pages'] ) . '
    <form action="#" method="GET">';
    foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . esc_html( $gk ) . '" value="' . esc_html( $gv ) . '" />';
    echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
    <button class="btn">' . t( 'go', "Go" ) . '</button>
    </form>
    </div>';
    }

    echo '</div>';
}

} else {

    echo '<div class="a-alert">' . t( 'no_users_yet', "No users yet." ) . '</div>';

}

break;

/** IMPORT SUBSCRIBERS */

case 'importsub':

if( !ab_to( array( 'subscribers' => 'import' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'subscribers_import_title', "Import Subscribers" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=users.php&amp;action=subscribers" class="btn">' . t( 'subscribers_view', "View Subscribers" ) . '</a>
</div>';

$subtitle = t( 'subscribers_import_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_user_page', 'after_title_import_subscribers_user_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'users_csrf' ) ) {

    if( isset( $_POST['emails'] ) )
    if( admin\actions::import_subscribers(
    array(
    'emails' => $_POST['emails'],
    'confirm' => ( isset( $_POST['confirm'] ) ? 1 : 0 ) )
    ) ) {

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf = $_SESSION['users_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">';

echo '<form action="#" method="POST">
<div class="row"><span>' . t( 'form_emails', "Email Addresses" ) . ':</span><div><textarea name="emails" style="min-height:200px;"></textarea></div></div>
<div class="row"><span>' . t( 'form_confirm', "Confirm" ) . ':</span><div><input type="checkbox" name="confirm" id="confirm" checked /> <label for="confirm"><span></span> ' . t( 'msg_setallconf', "Set all as confirmed by email" ) . '</label></div></div>
<input type="hidden" name="csrf" value="' . $csrf . '" />
<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'button_import', "Import" ) . '</button>
    </div>
    <div></div>
</div>
</form>';

echo '</div>';

break;

/** EXPORT SUBSCRIBERS */

case 'exportsub':

if( !ab_to( array( 'subscribers' => 'export' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'subscribers_export_title', "Export Subscribers" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=users.php&amp;action=subscribers" class="btn">' . t( 'subscribers_view', "View Subscribers" ) . '</a>
</div>';

$subtitle = t( 'subscribers_export_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_user_page', 'after_title_export_subscribers_user_page' ) );

$csrf = $_SESSION['subscribers_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">';

echo '<form action="?download=export_subscribers_csv.php" method="POST">
<div class="row"><span>' . t( 'subscribers_form_exporttype', "Export Subscribers" ) . ':</span><div><select name="view"><option value="" selected>' . t( 'subscribers_option_all', "All" ) . '</option><option value="verified">' . t( 'subscribers_option_verified', "Verified" ) . '</option><option value="notverified">' . t( 'subscribers_option_unverified', "Unverified" ) . '</option></select></div></div>
<div class="row"><span>' . t( 'form_datefrom', "Date from" ) . ':</span><div><input type="date" name="date[from]" value="' . date( 'Y-m-d', \query\main::get_option( 'siteinstalled' ) ) . '" class="datepicker" /></div></div>
<div class="row"><span>' . t( 'from_dateto', "Date to" ) . ':</span><div><input type="date" name="date[to]" value="' . date( 'Y-m-d', strtotime( 'tomorrow' ) ) . '" class="datepicker" /></div></div>
<div class="row"><span>' . t( 'subscribers_form_exportfields', "Export the Fields" ) . ':</span><div>
<input type="checkbox" name="fields[name]" id="name" /> <label for="name"><span></span> ' . t( 'name', "Name" ) . '</label>
<input type="checkbox" name="fields[email]" id="email" checked disabled /> <label for="email"><span></span> ' . t( 'email', "Email" ) . '</label></div></div>
<input type="hidden" name="csrf" value="' . $csrf . '" />
<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'subscribers_export_button', "Export" ) . '</button>
    </div>
    <div></div>
</div>
</form>';

echo '</div>';

break;

/** EDIT SUBSCRIBER */

case 'editsub':

if( !ab_to( array( 'subscribers' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'subscribers_edit_title', "Edit Subscriber" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $subscriber_exists = admin\admin_query::subscriber_exists( $_GET['id'] ) ) ) {

$info = admin\admin_query::subscriber_info( $_GET['id'] );

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>
<li><a href="?route=users.php&amp;action=subscribers&amp;type=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>
</ul>
</div>';

}

echo '<a href="?route=users.php&amp;action=subscribers" class="btn">' . t( 'subscribers_view', "View Subscribers" ) . '</a>
</div>';

$subtitle = t( 'subscribers_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $subscriber_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_user_page', 'after_title_edit_subscriber_user_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'users_csrf' ) ) {

    if( isset( $_POST['email'] ) )
    if( admin\actions::edit_subscriber( $_GET['id'],
    array(
    'email' => $_POST['email'],
    'confirm' => ( isset( $_POST['confirm'] ) ? 1 : 0 )
    ) ) ) {

    $info = admin\admin_query::subscriber_info( $_GET['id'] );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$_SESSION['users_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . t( 'form_email', "Email Address" ) . ':</span><div><input type="email" name="email" value="' . esc_html( $info->email ) . '" /></div></div>
<div class="row"><span>' . t( 'form_confirm', "Confirm" ) . ':</span><div><input type="checkbox" name="confirm" id="confirm"' . ( $info->verified ? ' checked' : '' ) . ' /> <label for="confirm"><span></span> ' . t( 'msg_setconfe', "Set this as confirmed by email" ) . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'subscribers_edit_button', "Edit Subscriber" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>

<div class="title" style="margin-top:40px;">

<h2>' . t( 'subscribers_info_title', "Information About This Subscriber" ) . '</h2>

</div>';

echo '<div class="info-table" style="padding-bottom: 20px;">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . t( 'form_ip', "IP Address" ) . ':</span> <div>' . ( !empty( $info->IP ) ? '<a href="?route=users.php&amp;action=subscribers&amp;search=' . $info->IP . '">' . $info->IP . '</a> / <a href="?route=banned.php&amp;action=add&amp;ip=' . $info->IP . '">' . t( 'bann_ip', "Ban IP?" ) . '</a>' : '-' ) .'</div></div>
<div class="row"><span>' . t( 'added_on', "Added on" ) . ':</span><div>' . $info->date . '</div></div>

</div>';

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/** LIST OF SUBSCRIBERS */

case 'subscribers':

if( !ab_to( array( 'subscribers' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'newsletter_title', "Subscribers" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( ( $ab = ab_to( array( 'subscribers' => array( 'export', 'import' ) ) ) ) && list( $ab_exp, $ab_imp ) = $ab ) {

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( $ab_imp ) echo '<li><a href="?route=users.php&amp;action=importsub">' . t( 'import', "Import" ) . '</a></li>';
if( $ab_exp ) echo '<li><a href="?route=users.php&amp;action=exportsub">' . t( 'export', "Export" ) . '</a></li>';

echo '</ul>
</div>';

}

echo '</div>';

$subtitle = t( 'newsletter_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_user_page', 'after_title_list_subscribers_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'users_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_subscriber( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

    if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
    if( admin\actions::action_subscriber( $_POST['action'], array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'users_csrf' ) ) {

if( $_GET['type'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_subscriber( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'verify' || $_GET['type'] == 'unverify' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::action_subscriber( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['users_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="users.php" />
<input type="hidden" name="action" value="subscribers" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'email' => t( 'order_email', "Email" ), 'email desc' => t( 'order_email_desc', "Email DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

 <select name="view">
<option value="">' . t( 'all_subscribers', "All subscribers" ) . '</option>';
foreach( array( 'verified' => t( 'view_verified', "Verified" ), 'notverified' => t( 'view_notverified', "Unverified" ) ) as $kt => $kv )echo '<option value="' . $kt . '"' . (isset( $_GET['view'] ) && $_GET['view'] == $kt ? ' selected' : '') . '>' . $kv . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="users.php" />
<input type="hidden" name="action" value="subscribers" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['view'] ) ) {
echo '<input type="hidden" name="view" value="' . esc_html( $_GET['view'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'subscribers_search_input', "Search subscribers" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$p = admin\admin_query::have_subscribers( $options = array( 'per_page' => 10, 'show' => (isset( $_GET['view'] ) ? $_GET['view'] : ''), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( isset( $_GET['view'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=users.php&amp;action=subscribers">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=users.php&amp;action=subscribers" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

$ab_edt= ab_to( array( 'subscribers' => 'edit' ) );
$ab_del= ab_to( array( 'subscribers' => 'delete' ) );
$ab_sm = ab_to( array( 'mail' => 'send' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="bulk_options">';

    if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button> ';

    if( $ab_edt ) {
        echo t( 'action', "Action" ) . ':
        <select name="action">';
        foreach( array( 'verify' => t( 'verify', "Verify" ), 'unverify' => t( 'unverify', "Unverify" ) ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
        echo '</select>

        <button class="btn" name="set_action">' . t( 'set_all', "Set to All" ) . '</button>';
    }

echo '</div>';

}

foreach( admin\admin_query::while_subscribers( array_merge( array( 'page' => $p['page'], 'orderby' => ( isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc' ) ), $options ) ) as $item ) {

    $links = array();

    if( $item->is_user ) {
        $links['is_user']['edit'] = '<a href="?route=users.php&amp;action=edit&amp;id=' . $item->ID . '">' . t( 'sessions_edit_user', "Edit User" ) . '</a>';
    } else {

    if( $ab_edt ) {
        $links['subscriber']['edit'] = '<a href="?route=users.php&amp;action=editsub&amp;id=' . $item->ID . '">' . t( 'edit', "Edit" ) . '</a>';
        $links['subscriber']['verify'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( $item->verified ? 'unverify' : 'verify' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( $item->verified ? t( 'unverify', "Unverify" ) : t( 'verify', "Verify" ) ) . '</a>';
    }
    if( $ab_sm ) $links['subscriber']['send_email'] = '<a href="' . \site\utils::update_uri( '', array( 'action' => 'sendmail', 'email' => $item->email ) ) . '">' . t( 'send_email', "Send Email" ) . '</a>';
    if( $ab_del ) $links['subscriber']['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    }

    echo get_list_type( 'subscriber', $item, $links );

}

echo '</ul>

<input type="hidden" name="csrf" value="' . $csrf . '" />

</form>';

if( isset( $p['prev_page'] ) || isset( $p['next_page'] ) ) {
    echo '<div class="pagination">';

    if( isset( $p['prev_page'] ) ) echo '<a href="' . $p['prev_page'] . '" class="btn">' . t( 'prev_page', "&larr; Prev" ) . '</a>';
    if( isset( $p['next_page'] ) ) echo '<a href="' . $p['next_page'] . '" class="btn">' . t( 'next_page', "Next &rarr;" ) . '</a>';

    if( $p['pages'] > 1 ) {
    echo '<div class="pag_goto">' . sprintf( t( 'pageofpages', "Page <b>%s</b> of <b>%s</b>" ), $page = $p['page'], $pages = $p['pages'] ) . '
    <form action="#" method="GET">';
    foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . esc_html( $gk ) . '" value="' . esc_html( $gv ) . '" />';
    echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
    <button class="btn">' . t( 'go', "Go" ) . '</button>
    </form>
    </div>';
    }

    echo '</div>';
}

} else echo '<div class="a-alert">' . t( 'no_subsribers_yet', "No subscribers yet." ) . '</div>';

break;

/** LIST OF USERS */

default:

if( !ab_to( array( 'users' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'users_title', "Users" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';
if( ab_to( array( 'users' => 'add' ) ) ) echo '<a href="?route=users.php&amp;action=add" class="btn">' . t( 'users_add', "Add User" ) . '</a>';
echo '</div>';

$subtitle = t( 'users_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_user_page', 'after_title_list_users_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'users_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_user( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

    if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
    if( admin\actions::action_user( $_POST['action'], array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'users_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_user( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'verify' || $_GET['type'] == 'unverify' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::action_user( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['users_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="users.php" />
<input type="hidden" name="action" value="list" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'action' => t( 'order_action', "Action" ), 'action desc' => t( 'order_action_desc', "Action DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'visits' => t( 'order_visits', "Visits" ), 'visits desc' => t( 'order_visits_desc', "Visits DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

 <select name="view">
<option value="">' . t( 'all_users', "All users" ) . '</option>';
foreach( array( 'subadmins' => t( 'view_subadmins', "Sub-Administrators" ), 'admins' => t( 'view_admins', "Administrators" ), 'verified' => t( 'view_verified', "Verified" ), 'notverified' => t( 'view_notverified', "Unverified" ), 'banned' => t( 'view_banned', "Banned" ) ) as $kt => $kv )echo '<option value="' . $kt . '"' . (isset( $_GET['view'] ) && $_GET['view'] == $kt ? ' selected' : '') . '>' . $kv . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="users.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['view'] ) ) {
echo '<input type="hidden" name="view" value="' . esc_html( $_GET['view'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'users_search_input', "Search users" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$custom_toolbar = do_action( 'admin_users_list_custom_toolbar' );

if( !empty( $custom_toolbar ) ) {
    echo '<div class="page-toolbar">';
    echo $custom_toolbar;
    echo '</div>';
}

$p = \query\main::have_users( ( $options = value_with_filter( 'admin_view_users_args', array( 'per_page' => 10, 'referrer' => ( isset( $_GET['referrer'] ) ? $_GET['referrer'] : '' ), 'show' => ( isset( $_GET['view'] ) ? $_GET['view'] : '' ), 'search' => ( isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '' ) ) ) ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
$reset = ( isset( $_GET['referrer'] ) || isset( $_GET['view'] ) || !empty( $_GET['search'] ) );
if( value_with_filter( 'admin_users_list_reset_view', $reset ) ) echo ' / <a href="?route=users.php&amp;action=list">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=users.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

$ab_edt= ab_to( array( 'users' => 'edit' ) );
$ab_del= ab_to( array( 'users' => 'delete' ) );
$ab_sm = ab_to( array( 'mail' => 'send' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="bulk_options">';

if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button> ';

if( $ab_edt ) {
    echo t( 'action', "Action" ) . ':
    <select name="action">';
    foreach( array( 'verify' => t( 'verify', "Verify" ), 'unverify' => t( 'unverify', "Unverify" ) ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
    echo '</select>
    <button class="btn" name="set_action">' . t( 'set_all', "Set to All" ) . '</button>';
}

echo '</div>';

}

foreach( \query\main::while_users( array_merge( array( 'page' => $p['page'], 'orderby' => value_with_filter( 'admin_view_users_orderby', ( isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc' ) ) ), $options ), array( 'no_emoticons' => true, 'no_filters' => true ) ) as $item ) {

    $links = array();

    if( $ab_edt ) {
        $links['edit'] = '<a href="?route=users.php&amp;action=edit&amp;id=' . $item->ID . '">' . t( 'edit', "Edit" ) . '</a>';
        $links['verify'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( $item->is_confirmed ? 'unverify' : 'verify' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( $item->is_confirmed ? t( 'unverify', "Unverify" ) : t( 'verify', "Verify" ) ) . '</a>';
    }
    if( $ab_sm ) $links['send_email'] = '<a href="' . \site\utils::update_uri( '', array( 'action' => 'sendmail', 'email' => $item->email ) ) . '">' . t( 'send_email', "Send Email" ) . '</a>';
    if( $ab_del ) $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'action' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'user', $item, $links );

}

echo '</ul>

<input type="hidden" name="csrf" value="' . $csrf . '" />

</form>';

if( isset( $p['prev_page'] ) || isset( $p['next_page'] ) ) {
    echo '<div class="pagination">';

    if( isset( $p['prev_page'] ) ) echo '<a href="' . $p['prev_page'] . '" class="btn">' . t( 'prev_page', "&larr; Prev" ) . '</a>';
    if( isset( $p['next_page'] ) ) echo '<a href="' . $p['next_page'] . '" class="btn">' . t( 'next_page', "Next &rarr;" ) . '</a>';

    if( $p['pages'] > 1 ) {
    echo '<div class="pag_goto">' . sprintf( t( 'pageofpages', "Page <b>%s</b> of <b>%s</b>" ), $page = $p['page'], $pages = $p['pages'] ) . '
    <form action="#" method="GET">';
    foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . esc_html( $gk ) . '" value="' . esc_html( $gv ) . '" />';
    echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
    <button class="btn">' . t( 'go', "Go" ) . '</button>
    </form>
    </div>';
    }

    echo '</div>';
}

} else echo '<div class="a-alert">' . t( 'no_users_yet', "No users yet." ) . '</div>';

break;

}