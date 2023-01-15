<?php

switch( $_GET['action'] ) {

/** VIEW SUGGESTION */

case 'view':

if( !ab_to( array( 'suggestions' => 'view' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'suggestions_view_title', "View Suggestion" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $sugestion_exists = admin\admin_query::suggestion_exists( $_GET['id'] ) ) ) {

$ab_edt    = ab_to( array( 'suggestions' => 'edit' ) );
$ab_del = ab_to( array( 'suggestions' => 'delete' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( $ab_del ) echo '<li><a href="?route=suggestions.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>';
if( $ab_edt ) echo '<li><a href="?route=suggestions.php&amp;action=list&amp;type=unread&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'set_as_unread', "Set as Unread" ) . '</a></li>';
echo '</ul>
</div>';

}

}

echo '<a href="?route=suggestions.php&amp;action=list" class="btn">' . t( 'suggestions_view', "View Suggestions" ) . '</a>
</div>';

$subtitle = t( 'suggestions_view_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $sugestion_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_suggestions_page', 'after_title_view_suggestions_page' ) );

// Set automaticaly read this suggestion
admin\actions::action_suggestions( 'read', $_GET['id'] );

$_SESSION['suggestions_csrf'] = $csrf;

$info = admin\admin_query::suggestion_info( $_GET['id'] );

echo '<div class="info-table">

<div class="row"><span>' . t( 'form_name', "Name" ) . ':</span><div>' . $info->name . '</div></div>
<div class="row"><span>' . t( 'form_store_url', "Store URL" ) . ':</span><div><a href="' . $info->url . '">' . $info->url . '</a></div></div>
<div class="row"><span>' . t( 'form_description', "Description" ) . ':</span><div>' . $info->description . '</div></div>
<div class="row"><span>' . t( 'form_message_for_us', "Message For Us" ) . ':</span><div>' . $info->message . '</div></div>';

if( $info->user == 0 ) {

    $addby = '-';

} else {

    $info_user = \query\main::user_info( $info->user );

    $addby = empty( $info_user ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info_user->ID . '">' . $info_user->name . '</a>' : $info_user->name );

}

echo '<div class="row"><span>' . t( 'added_by', "Added by" ) . ':</span><div>' . $addby . '</div></div>

<div class="row"><span>' . t( 'added_on', "Added on" ) . ':</span><div>' . $info->date . '</div></div>

</div>';

} else {

    echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

}

break;

/** LIST OF SUGGESTIONS */

default:

if( !ab_to( array( 'suggestions' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'suggestions_title', "Suggestions" ) . '</h2>';

$subtitle = t( 'suggestions_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_suggestions_page', 'after_title_list_suggestions_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'suggestions_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_suggestion( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

    if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
    if( admin\actions::action_suggestions( $_POST['action'], array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'suggestions_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_suggestion( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'read' || $_GET['type'] == 'unread' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::action_suggestions( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['suggestions_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="suggestions.php" />
<input type="hidden" name="action" value="list" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

 <select name="view">';
foreach( array( '' => t( 'all_suggestions', "All suggestions" ), 'read' => t( 'view_read', "Read" ), 'notread' => t( 'view_unread', "Unread" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['view'] ) && $_GET['view'] == $k ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="suggestions.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['view'] ) ) {
echo '<input type="hidden" name="view" value="' . esc_html( $_GET['view'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'suggestions_search_input', "Search in suggestions" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$p = admin\admin_query::have_suggestions( $options = array( 'per_page' => 10, 'show' => (isset( $_GET['view'] ) ? $_GET['view'] : ''), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( !empty( $_GET['view'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=suggestions.php&amp;action=list">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=suggestions.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

$ab_edt    = ab_to( array( 'suggestions' => 'edit' ) );
$ab_del    = ab_to( array( 'suggestions' => 'delete' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="bulk_options">';

if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button> ';

    if( $ab_edt ) {
        echo t( 'action', "Action" ) . ':
        <select name="action">';
        foreach( array( 'read' => t( 'read', "Read" ), 'unread' => t( 'unread', "Unread" ) ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
        echo '</select>
        <button class="btn" name="set_action">' . t( 'set_all', "Set to All" ) . '</button>';
    }

echo '</div>';

}

foreach( admin\admin_query::while_suggestions( array_merge( array( 'page' => $p['page'], 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

    $links = array();

    $links['view'] = '<a href="?route=suggestions.php&amp;action=view&amp;id=' . $item->ID . '">' . t( 'view', "View" ) . '</a>';
    if( $ab_edt ) $links['edit'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( $item->read ? 'unread' : 'read' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( $item->read ? t( 'set_as_unread', "Set as Unread" ) : t( 'set_as_read', "Set as Read" ) ) . '</a>';
    if( $ab_del ) $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'action' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'suggestion', $item, $links );

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

} else echo '<div class="a-alert">' . t( 'no_suggestions_yet', "No suggestions yet." ) . '</div>';

break;

}