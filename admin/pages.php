<?php

switch( $_GET['action'] ) {

/** ADD PAGE */

case 'add':

if( !ab_to( array( 'pages' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'pages_add_title', "Add New Page" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=pages.php&amp;action=list" class="btn">' . t( 'pages_view', "View Pages" ) . '</a>
</div>';

$subtitle = t( 'pages_add_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_pages_page', 'after_title_add_page_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'pages_csrf' ) ) {

    if( isset( $_POST['name'] ) && isset( $_POST['text'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_keywords'] ) && isset( $_POST['meta_desc'] ) )
    if( ( $new_page_id = admin\actions::add_page(
    value_with_filter( 'save_page_values', array(
    'name'          => ( isset( $_POST['name'] ) ? $_POST['name'] : '' ),
    'text'          => ( isset( $_POST['text'] ) ? $_POST['text'] : '' ),
    'publish'       => ( isset( $_POST['publish'] ) ? 1 : 0 ),
    'meta_title'    => ( isset( $_POST['meta_title'] ) ? $_POST['meta_title'] : '' ),
    'meta_keywords' => ( isset( $_POST['meta_keywords'] ) ? $_POST['meta_keywords'] : '' ),
    'meta_desc'     => ( isset( $_POST['meta_desc'] ) ? $_POST['meta_desc'] : '' ),
    'extra'         => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() )
    ) ) ) ) ) {

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';

    do_action( array( 'admin_page_added_edited', 'admin_page_added' ), $new_page_id );
    
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf   = $_SESSION['pages_csrf'] = \site\utils::str_random(10);

$main   = $GLOBALS['admin_main_class']->page_fields( array(), $csrf );
$fields = $main['fields'];

admin\widgets::get_page_tabs( $main );

echo '<div class="form-table">

<form action="#" method="POST" autocomplete="off">';

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

do_action( array( 'admin_page_after_form_add_edit', 'admin_page_after_form_add' ) );

echo '<div id="modify_mt">

<div class="title">
    <h2>' . t( 'pages_title_meta', "Modify Personalized Meta-Tags" ) . '</h2>
</div>

<div class="content">';

$fields = $GLOBALS['admin_main_class']->meta_tags_fields( array(), $csrf );

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

echo '</div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'pages_add_button', "Add Page" ) . '</button>
    </div>
    <div>
        <a href="#" class="btn" id="modify_mt_but">' . t( 'pages_editmt_button', "Meta Tags" ) . '</a>
    </div>
</div>

</form>

</div>';

break;

/** EDIT PAGE */

case 'edit':

if( !ab_to( array( 'pages' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'pages_edit_title', "Edit Page" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $page_exists = \query\main::page_exists( $_GET['id'] ) ) ) {

$info = \query\main::page_info( $_GET['id'], array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( ab_to( array( 'pages' => 'delete' ) ) ) echo '<li><a href="?route=pages.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>';
if( $info->visible ) {
    echo '<li><a href="?route=pages.php&amp;action=plans&amp;type=unpublish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'unpublish', "Unpublish" ) . '</a></li>';
} else {
    echo '<li><a href="?route=pages.php&amp;action=plans&amp;type=publish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'publish', "Publish" ) . '</a></li>';
}
echo '</ul>
</div>';

}

echo '<a href="?route=pages.php&amp;action=list" class="btn">' . t( 'pages_view', "View Pages" ) . '</a>
</div>';

$subtitle = t( 'pages_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $page_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_pages_page', 'after_title_edit_page_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'pages_csrf' ) ) {

if( isset( $_POST['change_url_title'] ) ) {

    if( isset( $_POST['url_title'] ) )
    if( admin\actions::edit_page_url( $_GET['id'],
    array(
    'title' => $_POST['url_title']
    ) ) ) {

    $info = \query\main::page_info( $_GET['id'], array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else {

    if( isset( $_POST['name'] ) && isset( $_POST['text'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_keywords'] ) && isset( $_POST['meta_desc'] ) )
    if( admin\actions::edit_page( $_GET['id'],
    value_with_filter( 'save_page_values', array(
    'name'          => ( isset( $_POST['name'] ) ? $_POST['name'] : '' ),
    'text'          => ( isset( $_POST['text'] ) ? $_POST['text'] : '' ),
    'publish'       => ( isset( $_POST['publish'] ) ? 1 : 0 ),
    'meta_title'    => ( isset( $_POST['meta_title'] ) ? $_POST['meta_title'] : '' ),
    'meta_keywords' => ( isset( $_POST['meta_keywords'] ) ? $_POST['meta_keywords'] : '' ),
    'meta_desc'     => ( isset( $_POST['meta_desc'] ) ? $_POST['meta_desc'] : '' ),
    'extra'         => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() )
    ) ) ) ) {

    $info = \query\main::page_info( $_GET['id'], array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';

    do_action( array( 'admin_page_added_edited', 'admin_page_edited' ), $info->ID );
    
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$_SESSION['pages_csrf'] = $csrf;

$main   = $GLOBALS['admin_main_class']->page_fields( $info, $csrf );
$fields = $main['fields'];

admin\widgets::get_page_tabs( $main );

echo '<div class="form-table">

<form action="?route=pages.php&amp;action=edit&amp;id=' . $info->ID . '" method="POST">';

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

do_action( array( 'admin_page_after_form_add_edit', 'admin_page_after_form_edit' ), $info );

echo '<div id="modify_mt">

<div class="title">
    <h2>' . t( 'pages_title_meta', "Modify Personalized Meta-Tags" ) . '</h2>
</div>

<div class="content">';

$fields = $GLOBALS['admin_main_class']->meta_tags_fields( $info, $csrf );

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

echo '</div>

</div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'pages_edit_button', "Edit Page" ) . '</button>
    </div>
    <div>
        <a href="#" class="btn" id="modify_mt_but">' . t( 'pages_editmt_button', "Meta Tags" ) . '</a>
    </div>
</div>

</form>

</div>';

echo '<div class="title" style="margin-top:40px;">

<h2>' . t( 'pages_info_title', "Information About This Page" ) . '</h2>

</div>';

echo '<div class="info-table" id="info-table" style="padding-bottom: 20px;">

<form action="?route=pages.php&amp;action=edit&amp;id=' . $info->ID . '#info-table" method="POST" autocomplete="off">';

$stat_rows              = array();
$stat_rows['id']        = '<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>';
$stat_rows['views']     = '<div class="row"><span>' . t( 'views', "Views" ) . ':</span> <div>' . $info->views . '</div></div>';
$stat_rows['url']       = '<div class="row"><span>' . t( 'page_url', "Page URL" ) . ':</span> <div class="modify_url">
<div' . ( isset( $_GET['editurl'] ) ? ' style="display: none;"' : '' ) . '><a href="' . $info->link . '" target="_blank">' . $info->link . '</a> / <a href="?route=pages.php&amp;action=edit&amp;id=' . $info->ID . '&amp;editurl#info-table">' . t( 'edit', "Edit" ) . '</a></div>
<div' . ( !isset( $_GET['editurl'] ) ? ' style="display: none;"' : '' ) . '>
<input type="text" name="url_title" value="' . $info->url_title . '" placeholder="' . $info->name . '" style="display: block; width: 100%; box-sizing: border-box;" />
<input type="hidden" name="csrf" value="' . $csrf . '" />
<button name="change_url_title" class="btn save">' . t( 'save', "Save" ) . '</button> <a href="?route=pages.php&amp;action=edit&amp;id=' . $info->ID . '#info-table" class="btn close">' . t( 'cancel', "Cancel" ) . '</a>
</div>
</div></div>';
$stat_rows['lu_by']     = '<div class="row"><span>' . t( 'last_update_by', "Last update by" ) . ':</span> <div>' . ( empty( $info->lastupdate_by_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->lastupdate_by . '">' . $info->lastupdate_by_name . '</a>' : $info->lastupdate_by_name ) ) . '</div></div>';
$stat_rows['lu_date']   = '<div class="row"><span>' . t( 'last_update_on', "Last update on" ) . ':</span> <div>' . $info->last_update . '</div></div>';
$stat_rows['added_by']  = '<div class="row"><span>' . t( 'added_by', "Added by" ) . ':</span> <div>' . ( empty( $info->user_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->user . '">' . $info->user_name . '</a>' : $info->user_name ) ) . '</div></div>';
$stat_rows['added_date']= '<div class="row"><span>' . t( 'added_on', "Added on" ) . ':</span> <div>' . $info->date . '</div></div>';

echo implode( '', value_with_filter( 'admin_page_stats', $stat_rows ) );

echo '</form>

</div>';

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/** LIST OF PAGES */

default:

if( !ab_to( array( 'pages' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'pages_title', "Pages" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';
if( ab_to( array( 'pages' => 'add' ) ) ) echo '<a href="?route=pages.php&amp;action=add" class="btn">' . t( 'pages_add', "Add Page" ) . '</a>';
echo '</div>';

$subtitle = t( 'pages_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_pages_page', 'after_title_list_pages_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'pages_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_page( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

    if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
    if( admin\actions::action_page( $_POST['action'], array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'pages_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_page( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::action_page( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['pages_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="pages.php" />
<input type="hidden" name="action" value="list" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'update' => t( 'order_last_update', "Last update" ), 'update desc' => t( 'order_last_update_desc', "Last update DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'views' => t( 'order_views', "Views" ), 'views desc' => t( 'order_views_desc', "Views DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'name' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
    echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="pages.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
    echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'pages_search_input', "Search pages" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$custom_toolbar = do_action( 'admin_pages_list_custom_toolbar' );

if( !empty( $custom_toolbar ) ) {
    echo '<div class="page-toolbar">';
    echo $custom_toolbar;
    echo '</div>';
}

$p = \query\main::have_pages( ( $options = value_with_filter( 'admin_view_pages_args', array( 'per_page' => 10, 'show' => 'all', 'search' => ( isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '' ) ) ) ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
$reset = ( !empty( $_GET['search'] ) );
if( value_with_filter( 'admin_pages_list_reset_view', $reset ) ) echo ' / <a href="?route=pages.php&amp;action=list">' . t( 'reset_view', "Reset view" ) . '</a>';

echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=pages.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

$ab_edt    = ab_to( array( 'pages' => 'edit' ) );
$ab_del    = ab_to( array( 'pages' => 'delete' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="bulk_options">';

    if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button> ';

    if( $ab_edt ) {
        echo t( 'action', "Action" ) . ':
        <select name="action">';
        foreach( array( 'publish' => t( 'publish', "Publish" ), 'unpublish' => t( 'unpublish', "Unpublish" ) ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
        echo '</select>
        <button class="btn" name="set_action">' . t( 'set_all', "Set to All" ) . '</button>';
    }

echo '</div>';

}

foreach( \query\main::while_pages( array_merge( array( 'page' => $p['page'], 'orderby' => value_with_filter( 'admin_view_pages_orderby', ( isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc' ) ) ), $options ), array( 'no_emoticons' => true, 'no_filters' => true ) ) as $item ) {

    $links = array();

    if( $ab_edt ) {
        $links['edit'] = '<a href="?route=pages.php&amp;action=edit&amp;id=' . $item->ID . '">' . t( 'edit', "Edit" ) . '</a>';
        $links['publish'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( !$item->visible ? 'publish' : 'unpublish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( !$item->visible ? t( 'publish', "Publish" ) : t( 'unpublish', "Unpublish" ) ) . '</a>';
    }
    if( $ab_del ) $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'action' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'page', $item, $links );

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

} else echo '<div class="a-alert">' . t( 'no_pages_yet', "No pages yet." ) . '</div>';

break;

}