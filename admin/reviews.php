<?php

switch( $_GET['action'] ) {

/** ADD REVIEW */

case 'add':

if( !ab_to( array( 'reviews' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'reviews_add_title', "Add Review" ) . '</h2>

<div style="float:right;margin:0 2px 0 0;">
<a href="?route=reviews.php&amp;action=list" class="btn">' . t( 'reviews_view', "View Reviews" ) . '</a>
</div>';

$subtitle = t( 'reviews_add_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_reviews_page', 'after_title_add_reviews_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'reviews_csrf' ) ) {

    if( isset( $_POST['store'] ) && isset( $_POST['user'] ) && isset( $_POST['stars'] ) && isset( $_POST['text'] ) )
    if( admin\actions::add_review(
    array(
    'user' => $_POST['user'],
    'store' => $_POST['store'],
    'text' => $_POST['text'],
    'stars' => $_POST['stars'],
    'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 )
    ) ) )

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf = $_SESSION['reviews_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>' . t( 'form_store_id', "Store ID" ) . ':</span><div data-search="store"><input type="text" name="store" value="' . ( $storeID = ( isset( $_POST['store'] ) ? (int) $_POST['store'] : ( !empty( $_GET['store'] ) ? (int) $_GET['store'] : '' ) ) ) . '" required /><a href="#" class="downarr"></a>';
if( empty( $storeID ) || !\query\main::store_exists( $storeID ) ) {
    echo '<span class="idinfo"></span>';
} else {
    $store_info = \query\main::store_info( $storeID );
    echo '<span class="idinfo">' . $store_info->name . ' (ID: ' . $store_info->ID . ')</span>';
}
echo '</div></div>

<div class="row"><span>' . t( 'form_user_id', "User ID" ) . ':</span><div data-search="user"><input type="text" name="user" value="' . ( $userID = ( isset( $_POST['user'] ) ? (int) $_POST['user'] : ( !empty( $_GET['user'] ) ? (int) $_GET['user'] : $GLOBALS['me']->ID ) ) ) . '" required /><a href="#" class="downarr"></a>';
if( empty( $userID ) || !\query\main::user_exists( $userID ) ) {
    echo '<span class="idinfo"></span>';
} else {
    $user_info = \query\main::user_info( $userID );
    echo '<span class="idinfo">' . $user_info->name . ' (ID: ' . $user_info->ID . ')</span>';
}
echo '</div></div>

<div class="row"><span>' . t( 'form_stars', "Rating" ) . ':</span>
<div>
<select name="stars">';
foreach( array( 1, 2, 3, 4, 5 ) as $note )echo '<option value="' . $note . '"' . ( $note == 5 ? ' selected' : '' ) . '>' . $note . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'form_text', "Text" ) . ':</span><div><textarea name="text" style="min-height:200px;"></textarea></div></div>
<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish" checked /> <label for="publish"><span></span> ' . t( 'msg_pubreview', "Publish this review" ) . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'reviews_add_button', "Add Review" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

/** EDIT REVIEW */

case 'edit':

if( !ab_to( array( 'reviews' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'reviews_edit_title', "Edit Review" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $review_exists = \query\main::review_exists( $_GET['id'] ) ) ) {

$info = \query\main::review_info( $_GET['id'], array( 'no_emoticons' => true, 'no_filters' => true ) );

$ab_edt    = ab_to( array( 'pages' => 'edit' ) );
$ab_del = ab_to( array( 'pages' => 'delete' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( $ab_del ) echo '<li><a href="?route=reviews.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>';
if( $info->valid ) {
    if( $ab_edt )echo '<li><a href="?route=reviews.php&amp;action=list&amp;type=unpublish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'unpublish', "Unpublish" ) . '</a></li>';
} else {
    if( $ab_edt )echo '<li><a href="?route=reviews.php&amp;action=list&amp;type=publish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'publish', "Publish" ) . '</a></li>';
}
echo '</ul>
</div>';

}

}

echo '<a href="?route=reviews.php&amp;action=list" class="btn">' . t( 'reviews_view', "View Reviews" ) . '</a>
</div>';

$subtitle = t( 'reviews_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $review_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_reviews_page', 'after_title_edit_reviews_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'reviews_csrf' ) ) {

    if( isset( $_POST['store'] ) && isset( $_POST['user'] ) && isset( $_POST['stars'] ) && isset( $_POST['text'] ) )
    if( admin\actions::edit_review( $_GET['id'],
    array(
    'user' => $_POST['user'],
    'store' => $_POST['store'],
    'text' => $_POST['text'],
    'stars' => $_POST['stars'],
    'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 )
    ) ) ) {

    $info = \query\main::review_info( $_GET['id'], array( 'no_emoticons' => true, 'no_filters' => true ) );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$_SESSION['reviews_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . t( 'form_store_id', "Store ID" ) . ':</span><div data-search="store"><input type="text" name="store" value="' . $info->storeID . '" required /><a href="#" class="downarr"></a>
<span class="idinfo">' . ( !empty( $info->store_name ) ? $info->store_name . ' (ID: ' . $info->storeID . ')' : '' ) . '</span>
</div></div>

<div class="row"><span>' . t( 'form_user_id', "User ID" ) . ':</span><div data-search="user"><input type="text" name="user" value="' . $info->userID . '" required /><a href="#" class="downarr"></a>
<span class="idinfo">' . ( !empty( $info->user_name ) ? $info->user_name . ' (ID: ' . $info->userID . ')' : '' ) . '</span>
</div></div>

<div class="row"><span>' . t( 'form_stars', "Rating" ) . ':</span>
<div>
<select name="stars">';
foreach( array( 1, 2, 3, 4, 5 ) as $note )echo '<option value="' . $note . '"' . ( $note == $info->stars ? ' selected' : '' ) . '>' . $note . '</option>';
echo '</select>
</div></div>

<div class="row"><span>' . t( 'form_text', "Text" ) . ':</span><div><textarea name="text" style="min-height:200px;">' . $info->text . '</textarea></div></div>
<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish"' . ( $info->valid ? ' checked' : '' ) . ' /> <label for="publish"><span></span> ' . t( 'msg_pubreview', "Publish this review" ) . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'reviews_edit_button', "Edit Review" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

echo '<div class="title" style="margin-top:40px;">

<h2>' . t( 'reviews_info_title', "Information About This Review" ) . '</h2>

</div>';

echo '<div class="info-table" style="padding-bottom:20px;">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . t( 'store_name', "Store name" ) . ':</span> <div>' . ( empty( $info->store_name ) ? '-' : ( ab_to( array( 'stores' => 'edit' ) ) ? '<a href="?route=stores.php&amp;action=edit&amp;id=' . $info->storeID . '">' . $info->store_name . '</a>' : $info->store_name ) ) . '</div></div>
<div class="row"><span>' . t( 'last_update_by', "Last update by" ) . ':</span> <div>' . ( empty( $info->lastupdate_by_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->lastupdate_by . '">' . $info->lastupdate_by_name . '</a>' : $info->lastupdate_by_name ) ) . '</div></div>
<div class="row"><span>' . t( 'last_update_on', "Last update on" ) . ':</span> <div>' . $info->last_update . '</div></div>
<div class="row"><span>' . t( 'added_by', "Added by" ) . ':</span> <div>' . ( empty( $info->user_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->userID . '">' . $info->user_name . '</a>' : $info->user_name ) ) . '</div></div>
<div class="row"><span>' . t( 'added_on', "Added on" ) . ':</span> <div>' . $info->date . '</div></div>

</div>';

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '.</div>';

break;

/** LIST OF REVIEWS */

default:

if( !ab_to( array( 'reviews' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'reviews_title', "Reviews" ) . '</h2>

<div style="float:right;margin:0 2px 0 0;">';
if( ab_to( array( 'reviews' => 'add' ) ) ) echo '<a href="?route=reviews.php&amp;action=add" class="btn">' . t( 'reviews_add', "Add Review" ) . '</a>';
echo '</div>';

$subtitle = t( 'reviews_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_reviews_page', 'after_title_list_reviews_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'reviews_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_review( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

    if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
    if( admin\actions::action_review( $_POST['action'], array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'reviews_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_review( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::action_review( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['reviews_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="reviews.php" />
<input type="hidden" name="action" value="list" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

 <select name="view">';
foreach( array( 'all' => t( 'all_reviews', "All reviews" ), '' => t( 'view_published', "Published" ), 'notvalid' => t( 'view_notpublished', "Not published" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['view'] ) && $_GET['view'] == $k ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="reviews.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['view'] ) ) {
echo '<input type="hidden" name="view" value="' . esc_html( $_GET['view'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'reviews_search_input', "Search reviews" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$p = \query\main::have_reviews( $options = array( 'per_page' => 10, 'store' => (isset( $_GET['store'] ) ? $_GET['store'] : ''), 'user' => (isset( $_GET['user'] ) ? $_GET['user'] : ''), 'show' => (isset( $_GET['view'] ) ? $_GET['view'] : 'all'), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( !empty( $_GET['store'] ) || !empty( $_GET['user'] ) || isset( $_GET['view'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=reviews.php&amp;action=list">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=reviews.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

$ab_edt    = ab_to( array( 'reviews' => 'edit' ) );
$ab_del    = ab_to( array( 'reviews' => 'delete' ) );

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

foreach( \query\main::while_reviews( array_merge( array( 'page' => $p['page'], 'orderby' => ( isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc' ) ), $options ), '', array( 'no_emoticons' => true, 'no_filters' => true ) ) as $item ) {

    $links = array();

    if( $ab_edt ) {
        $links['edit'] = '<a href="?route=reviews.php&amp;action=edit&amp;id=' . $item->ID . '">' . t( 'edit', "Edit" ) . '</a>';
        $links['publish'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( $item->valid ? 'unpublish' : 'publish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( $item->valid ? t( 'unpublish', "Unpublish" ) : t( 'publish', "Publish" ) ). '</a>';
    }
    if( $ab_del ) $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'action' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'review', $item, $links );

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

} else echo '<div class="a-alert">' . t( 'no_reviews_yet', "No reviews yet." ) . '</div>';

break;

}