<?php

switch( $_GET['action'] ) {

/** ADD CATEGORY */

case 'add':

if( !ab_to( array( 'categories' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . ( isset( $_GET['subcat'] ) ? t( 'subcategories_add_title', "Add New Subcategory" ) : t( 'categories_add_title', "Add New Category" ) ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=categories.php&amp;action=list" class="btn">' . t( 'categories_view', "View Categories" ) . '</a>
</div>';

$subtitle1 = t( 'categories_add_subtitle' );
$subtitle2 = t( 'subcategories_add_subtitle' );

if( !empty( $subtitle1 ) || ( isset( $_GET['subcat'] ) && !empty( $subtitle2 ) ) ) {
    echo '<span>' . ( isset( $_GET['subcat'] ) ? $subtitle2 : $subtitle1 ) . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_category_page', 'after_title_add_category_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'categories_csrf' ) ) {

    if( isset( $_POST['name'] ) && isset( $_POST['text'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_keywords'] ) && isset( $_POST['meta_desc'] ) )
    if( ( $new_category_id = admin\actions::add_category(
    value_with_filter( 'save_category_values', array(
    'name'          => ( isset( $_POST['name'] ) ? $_POST['name'] : '' ),
    'description'   => ( isset( $_POST['text'] ) ? $_POST['text'] : '' ),
    'category'      => ( isset( $_POST['category'] ) ? $_POST['category'] : 0 ),
    'meta_title'    => ( isset( $_POST['meta_title'] ) ? $_POST['meta_title'] : '' ),
    'meta_keywords' => ( isset( $_POST['meta_keywords'] ) ? $_POST['meta_keywords'] : '' ),
    'meta_desc'     => ( isset( $_POST['meta_desc'] ) ? $_POST['meta_desc'] : '' ),
    'extra'         => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() )
    ) ) ) ) ) {

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';

    do_action( array( 'admin_category_added_edited', 'admin_category_added' ), $new_category_id );
    
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf   = $_SESSION['categories_csrf'] = \site\utils::str_random(10);

$main   = $GLOBALS['admin_main_class']->category_fields( array(), $csrf );
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

do_action( array( 'admin_category_after_form_add_edit', 'admin_category_after_form_add' ) );

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
        <button class="btn btn-important">' . ( isset( $_GET['subcat'] ) ? t( 'subcategories_add_button', "Add Subcategory" ) : t( 'categories_add_button', "Add Category" ) ) . '</button>
    </div>
    <div>
        <a href="#" class="btn" id="modify_mt_but">' . t( 'pages_editmt_button', "Meta Tags" ) . '</a>
    </div>
</div>

</form>

</div>';

break;

/** EDIT CATEGORY */

case 'edit':

if( !ab_to( array( 'categories' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'categories_edit_title', "Edit Category" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $category_exists = \query\main::category_exists( $_GET['id'] ) ) ) {

$info = \query\main::category_info( $_GET['id'], array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

$ab_del = ab_to( array( 'categories' => 'delete' ) );

if( $ab_del ) {

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( $ab_del ) echo '<li><a href="?route=categories.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>';
echo '</ul>
</div>';

}

}

echo '<a href="?route=categories.php&amp;action=list" class="btn">' . t( 'categories_view', "View Categories" ) . '</a>
</div>';

$subtitle = t( 'categories_edit_subtitle', "Edit category" );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_category_page', 'after_title_edit_category_page' ), $info->ID );

if( $category_exists ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'categories_csrf' ) ) {

if( isset( $_POST['change_url_title'] ) ) {

    if( isset( $_POST['url_title'] ) )
    if( admin\actions::edit_category_url( $_GET['id'],
    array(
    'title' => $_POST['url_title']
    ) ) ) {

    $info = \query\main::category_info( $_GET['id'], array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else {

    if( isset( $_POST['name'] ) && isset( $_POST['text'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_keywords'] ) && isset( $_POST['meta_desc'] ) )
    if( admin\actions::edit_category( $_GET['id'],
    value_with_filter( 'save_category_values', array(
    'name'          => ( isset( $_POST['name'] ) ? $_POST['name'] : '' ),
    'description'   => ( isset( $_POST['text'] ) ? $_POST['text'] : '' ),
    'category'      => ( isset( $_POST['category'] ) ? $_POST['category'] : 0 ),
    'meta_title'    => ( isset( $_POST['meta_title'] ) ? $_POST['meta_title'] : '' ),
    'meta_keywords' => ( isset( $_POST['meta_keywords'] ) ? $_POST['meta_keywords'] : '' ),
    'meta_desc'     => ( isset( $_POST['meta_desc'] ) ? $_POST['meta_desc'] : '' ),
    'extra'         => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() )
    ) ) ) ) {

    $info = \query\main::category_info( $_GET['id'], array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';

    do_action( array( 'admin_category_added_edited', 'admin_category_edited' ), $info->ID );

    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$_SESSION['categories_csrf'] = $csrf;

$main   = $GLOBALS['admin_main_class']->category_fields( $info, $csrf );
$fields = $main['fields'];

admin\widgets::get_page_tabs( $main );

echo '<div class="form-table">

<form action="?route=categories.php&amp;action=edit&amp;id=' . $info->ID . '" method="POST">';

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

do_action( array( 'admin_category_after_form_add_edit', 'admin_category_after_form_edit' ), $info );

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
        <button class="btn btn-important">' . t( 'categories_edit_button', "Edit Category" ) . '</button>
    </div>
    <div>
        <a href="#" class="btn" id="modify_mt_but">' . t( 'pages_editmt_button', "Meta Tags" ) . '</a>
    </div>
</div>

</form>

</div>';

echo '<div class="title" style="margin-top: 40px;">

<h2>' . t( 'categories_info_title', "Information About This Category" ) . '</h2>

</div>';

echo '<div class="info-table" id="info-table" style="padding-bottom: 20px;">

<form action="?route=categories.php&amp;action=edit&amp;id=' . $info->ID . '#info-table" method="POST" autocomplete="off">';

$stat_rows              = array();
$stat_rows['id']        = '<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>';
$stat_rows['url']       = '<div class="row"><span>' . t( 'page_url', "Page URL" ) . ':</span> <div class="modify_url">
<div' . ( isset( $_GET['editurl'] ) ? ' style="display: none;"' : '' ) . '><a href="' . $info->link . '" target="_blank">' . $info->link . '</a> / <a href="?route=categories.php&amp;action=edit&amp;id=' . $info->ID . '&amp;editurl#info-table">' . t( 'edit', "Edit" ) . '</a></div>
<div' . ( !isset( $_GET['editurl'] ) ? ' style="display: none;"' : '' ) . '>
<input type="text" name="url_title" value="' . $info->url_title . '" placeholder="' . $info->name . '" style="display: block; width: 100%; box-sizing: border-box;" />
<input type="hidden" name="csrf" value="' . $csrf . '" />
<button name="change_url_title" class="btn save">' . t( 'save', "Save" ) . '</button> <a href="?route=categories.php&amp;action=edit&amp;id=' . $info->ID . '#info-table" class="btn close">' . t( 'cancel', "Cancel" ) . '</a>
</div>
</div></div>';
$stat_rows['added_on']  = '<div class="row"><span>' . t( 'added_by', "Added by" ) . ':</span> <div>' . ( empty( $info->user_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->user . '">' . $info->user_name . '</a>' : $info->user_name ) ) . '</div></div>';
$stat_rows['added_date']= '<div class="row"><span>' . t( 'added_on', "Added on" ) . ':</span> <div>' . $info->date . '</div></div>';

echo implode( '', value_with_filter( 'admin_category_stats', $stat_rows ) );

echo '</form>

</div>';

} else echo '<div class="a-error">Invalid ID.</div>';

break;

/** LIST OF CATEGORIES */

default:

if( !ab_to( array( 'categories' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'categories_title', "Categories" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';
if(  ( $ab_add = ab_to( array( 'categories' => 'add' ) ) ) )echo ' <a href="?route=categories.php&amp;action=add" class="btn">' . t( 'categories_add', "Add Category" ) . '</a>';
echo '</div>';

$subtitle = t( 'categories_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_category_page', 'after_title_categories_list_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'categories_csrf' ) ) {

    if( isset( $_POST['delete'] ) ) {

        if( isset( $_POST['id'] ) )
        if( admin\actions::delete_category( array_keys( $_POST['id'] ) ) )
        echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
        else
        echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

    }

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'categories_csrf' ) ) {

    if( $_GET['action'] == 'delete' ) {

        if( isset( $_GET['id'] ) )
        if( admin\actions::delete_category( $_GET['id'] ) )
        echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
        else
        echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

    }

}

$csrf = $_SESSION['categories_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off" novalidate>

<input type="hidden" name="route" value="categories.php" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'name' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

<input type="hidden" name="action" value="list" />

<button class="btn">' . t( 'view', "View" ) . '</button>

</form>

</div>';

$custom_toolbar = do_action( 'admin_categories_list_custom_toolbar' );

if( !empty( $custom_toolbar ) ) {
    echo '<div class="page-toolbar">';
    echo $custom_toolbar;
    echo '</div>';
}

$p = \query\main::have_categories( ( $options = value_with_filter( 'admin_view_categories_args', array( 'per_page' => 10 ) ) ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( value_with_filter( 'admin_categories_list_reset_view', false ) ) echo ' / <a href="?route=categories.php&amp;action=list">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=categories.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

$ab_edt = ab_to( array( 'categories' => 'edit' ) );
$ab_del = ab_to( array( 'categories' => 'delete' ) );

if( $ab_del ) {
echo '<div class="bulk_options">
    <button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button>
</div>';
}

foreach( \query\main::while_categories( array_merge( array( 'page' => $p['page'], 'orderby' => value_with_filter( 'admin_view_categories_orderby', ( isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc' ) ) ), $options ), array( 'no_emoticons' => true, 'no_filters' => true ) ) as $item ) {

    $links = array();

    if( $ab_edt ) $links['edit'] = '<a href="?route=categories.php&amp;action=edit&amp;id=' . $item->ID . '">' . t( 'edit', "Edit" ) . '</a>';
    if( $ab_add && !$item->is_subcat ) $links['add'] = '<a href="?route=categories.php&amp;action=add&amp;subcat&amp;cat=' . $item->ID . '">' . t( 'categories_add', "Add Category" ) . '</a>';
    if( $ab_del ) $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'action' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'category', $item, $links );


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

} else echo '<div class="a-alert">' . t( 'no_categories_yet', "No categories yet." ) . '</div>';

break;

}