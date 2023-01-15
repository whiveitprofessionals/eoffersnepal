<?php

if( !$GLOBALS['me']->is_admin ) die;

switch( $_GET['action'] ) {

/** ADD ENTRY */

case 'add':

echo '<div class="title">

<h2>' . t( 'banned_add_title', "Add New Entry" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=banned.php&amp;action=list" class="btn">' . t( 'banned_view', "View List" ) . '</a>
</div>';

$subtitle = t( 'banned_add_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_settings_page', 'after_title_ban_settings_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'banned_csrf' ) ) {

    if( isset( $_POST['ip'] ) && valid_ip( $_POST['ip'] ) )
    if( admin\actions::add_banned(
    array(
    'ipaddr' => $_POST['ip'],
    'registration' => ( isset( $_POST['register'] ) ? 1 : 0 ),
    'login' => ( isset( $_POST['login'] ) ? 1 : 0 ),
    'site' => ( isset( $_POST['shn-site'] ) ? 1 : 0 ),
    'redirect' => ( isset( $_POST['redirect'] ) ? $_POST['redirect'] : '' ),
    'expiration' => ( !isset( $_POST['shn-expiration'] ) ? 1 : 0 ),
    'expiration_date' => ( !isset( $_POST['shn-expiration'] ) && isset( $_POST['expiration'] ) ? date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['expiration'] ) ) ) : '' )
    ) ) )

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf = $_SESSION['banned_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>' . t( 'form_ip', "IP Address" ) . ':</span><div><input type="text" name="ip" value="' . ( $_SERVER['REQUEST_METHOD'] == 'GET' && !empty( $_GET['ip'] ) ? esc_html( $_GET['ip'] ) : '' ) . '" required /></div></div>
<div class="row"><span>' . t( 'bann_form_block', "Block" ) . ':</span><div>
<input type="checkbox" name="register" id="register" checked /> <label for="register"><span></span> ' . t( 'bann_registrations', "Registrations" ) . '</label> <br />
<input type="checkbox" name="login" id="login" checked /> <label for="login"><span></span> ' . t( 'bann_login', "Login" ) . '</label> <br />
<input type="checkbox" name="shn-site" id="site" /> <label for="site"><span></span> ' . t( 'bann_site', "Entire site" ) . '</label>
</div></div>
<div class="row" style="display: none;"><span>' . t( 'bann_form_redirect', "Redirect to" ) . ':</span><div><input type="text" name="redirect" value="http://" /></div></div>
<div class="row"><span>' . t( 'bann_form_expiration', "Expiration" ) . ':</span><div>
<input type="checkbox" name="shn-expiration" checked id="expiration" /> <label for="expiration"><span></span> ' . t( 'bann_neverexp', "Never" ) . '</label>
</div></div>
<div class="row" style="display: none;"><span>' . t( 'form_expiration_date', "Expiration Date" ) . ':</span><div><input type="date" name="expiration[date]" value="' . date( 'Y-m-d', strtotime( '+1 week' ) ) . '" class="datepicker" style="display:inline-block;width:68%;margin-right:2%" /><input type="time" name="expiration[hour]" value="00:00" class="hourpicker" style="display:inline-block;width:30%" /></div>
</div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div><button class="btn btn-important">' . t( 'banned_add_button', "Add Entry" ) . '</button></div>
    <div></div>
</div>

</form>

</div>';

break;

/** EDIT ENTRY */

case 'edit':

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'banned_edit_title', "Edit Entry" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $banned_exists = admin\admin_query::banned_exists( $_GET['id'] ) ) ) {

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>
<li><a href="?route=banned.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>
</ul>
</div>';

}

echo '<a href="?route=banned.php&amp;action=list" class="btn">' . t( 'banned_view', "View List" ) . '</a>
</div>';

$subtitle = t( 'banned_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $banned_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_settings_page', 'after_title_unban_settings_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'banned_csrf' ) ) {

    if( isset( $_POST['ip'] ) && valid_ip( $_POST['ip'] ) )
    if( admin\actions::edit_banned( $_GET['id'],
    array(
    'ipaddr' => $_POST['ip'],
    'registration' => ( isset( $_POST['register'] ) ? 1 : 0 ),
    'login' => ( isset( $_POST['login'] ) ? 1 : 0 ),
    'site' => ( isset( $_POST['shn-site'] ) ? 1 : 0 ),
    'redirect' => ( isset( $_POST['redirect'] ) ? $_POST['redirect'] : '' ),
    'expiration' => ( !isset( $_POST['shn-expiration'] ) ? 1 : 0 ),
    'expiration_date' => ( !isset( $_POST['shn-expiration'] ) && isset( $_POST['expiration'] ) ? date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['expiration'] ) ) ) : ''
    ) ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$_SESSION['banned_csrf'] = $csrf;

$info = admin\admin_query::banned_info( $_GET['id'] );

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">

<div class="row"><span>' . t( 'form_ip', "IP Address" ) . ':</span><div><input type="text" name="ip" value="' . $info->IP . '" required /></div></div>
<div class="row"><span>' . t( 'bann_form_block', "Block" ) . ':</span><div>
<input type="checkbox" name="register" id="register"' . ( $info->registration ? ' checked' : '' ) . ' /> <label for="register"><span></span> ' . t( 'bann_registrations', "Registrations" ) . '</label> <br />
<input type="checkbox" name="login" id="login"' . ( $info->login ? ' checked' : '' ) . ' /> <label for="login"><span></span> ' . t( 'bann_login', "Login" ) . '</label> <br />
<input type="checkbox" name="shn-site" id="site"' . ( $info->site ? ' checked' : '' ) . ' /> <label for="site"><span></span> ' . t( 'bann_site', "Entire site" ) . '</label>
</div></div>
<div class="row"' . ( !$info->site ? ' style="display: none;"' : '' ) . '><span>' . t( 'bann_form_redirect', "Redirect to" ) . ':</span><div><input type="text" name="redirect" value="' . $info->redirect_to . '" /></div></div>
<div class="row" style="display: none;"><span>' . t( 'bann_form_redirect', "Redirect to" ) . ':</span><div><input type="text" name="redirect" value="http://" /></div></div>
<div class="row"><span>' . t( 'bann_form_expiration', "Expiration" ) . ':</span><div>
<input type="checkbox" name="shn-expiration" id="expiration" ' . ( !$info->expiration ? ' checked' : '' ) . ' /> <label for="expiration"><span></span> ' . t( 'bann_neverexp', "Never" ) . '</label>
</div></div>
<div class="row" ' . ( !$info->expiration ? ' style="display: none;"' : '' ) . '><span>' . t( 'form_expiration_date', "Expiration Date" ) . ':</span><div><input type="date" name="expiration[date]" value="' .    date( 'Y-m-d', ( $info->expiration ? strtotime( $info->expiration_date ) : strtotime( '+1 week' ) ) ) . '" class="datepicker" style="display:inline-block;width:68%;margin-right:2%" /><input type="time" name="expiration[hour]" value="' . date( 'H:i', strtotime( $info->expiration_date ) ) . '" class="hourpicker" style="display:inline-block;width:30%" /></div>
</div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div><button class="btn btn-important">' . t( 'banned_edit_button', "Edit Entry" ) . '</button></div>
    <div></div>
</div>

</form>

</div>';

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/** LIST OF BANNED IP's */

default:

echo '<div class="title">

<h2>' . t( 'banned_title', "Banned List" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=banned.php&amp;action=add" class="btn">' . t( 'banned_add', "Add Entry" ) . '</a>
</div>';

$subtitle = t( 'banned_subtitle', "List of blocked IPs" );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_settings_page', 'after_title_list_bans_settings_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'banned_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_banned( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'banned_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_banned( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['banned_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="banned.php" />
<input type="hidden" name="action" value="list" />

Order by:
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="banned.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'bann_search_input', "Search IP" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$p = admin\admin_query::have_banned( $options = array( 'per_page' => 10, 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( !empty( $_GET['search'] ) ) echo ' / <a href="?route=banned.php&amp;action=list">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=banned.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>

<div class="bulk_options">
<button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button>

</div>';

foreach( admin\admin_query::while_banned( array_merge( array( 'page' => $p['page'], 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

    $links = array();

    $links['edit'] = '<a href="?route=banned.php&action=edit&id=' . $item->ID . '">' . t( 'edit', "Edit" ) . '</a>';
    $links['delete'] = '<a href="?route=banned.php&action=delete&id=' . $item->ID . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'ban', $item, $links );

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

} else echo '<div class="a-alert">' . t( 'no_banned_yet', "No IPs banned yet." ) . '</div>';

break;

}