<?php

switch( $_GET['action'] ) {

/** ADD PLAN */

case 'plan_add':

if( !$GLOBALS['me']->is_admin ) die;

echo '<div class="title">

<h2>' . t( 'pmts_addplan_title', "Add New Plan" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=payments.php&amp;action=plan_view" class="btn">' . t( 'payments_plan_view', "View Plans" ) . '</a>
</div>';

$subtitle = t( 'pmts_addplan_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_payments_page', 'after_title_add_plan_payments_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'payments_csrf' ) ) {

    if( isset( $_POST['name'] ) && isset( $_POST['price'] ) && isset( $_POST['credits'] ) && isset( $_POST['text'] ) )
    if( admin\actions::add_payment_plan(
    array(
    'name' => $_POST['name'],
    'price' => $_POST['price'],
    'credits' => $_POST['credits'],
    'description' => $_POST['text'],
    'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 )
    ) ) )

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf = $_SESSION['payments_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">

<div class="row"><span>' . t( 'form_name', "Name" ) . ':</span><div><input type="text" name="name" value="" /></div></div>
<div class="row"><span>' . t( 'form_price', "Price" ) . ':</span><div><input type="text" name="price" value="" placeholder="' . sprintf( PRICE_FORMAT, \site\utils::money_format( 0.00 ) ) . '" /></div> </div>
<div class="row"><span>' . t( 'form_credits', "Credits" ) . ':</span><div><input type="number" name="credits" min="0" value="10" /></div></div>
<div class="row"><span>' . t( 'form_description', "Description" ) . ':</span><div><textarea name="text" style="min-height:100px;"></textarea></div></div>
<div class="row image-upload"><span>' . t( 'form_image', "Image" ) . ':</span> <div><input type="file" name="logo" accept="image/*" /></div></div>

<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish" checked /> <label for="publish"><span></span> ' . t( 'msg_pubpplan', "Publish this plan" ) . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'pmts_plans_add', "Add Plan" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

/** EDIT PLAN */

case 'plan_edit':

if( !$GLOBALS['me']->is_admin ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'pmts_editplan_title', "Edit Plan" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $plan_exists = \query\payments::plan_exists( $_GET['id'] ) ) ) {

$info = \query\payments::plan_info( $_GET['id'] );

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>
<li><a href="?route=payments.php&amp;action=plan_view&amp;type=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>
</ul>
</div>';

}

echo '<a href="?route=payments.php&amp;action=plan_view" class="btn">' . t( 'payments_plan_view', "View Plans" ) . '</a>
</div>';

$subtitle = t( 'pmts_editplan_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $plan_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_payments_page', 'after_title_edit_plan_payments_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'payments_csrf' ) ) {

    if( isset( $_POST['name'] ) && isset( $_POST['text'] ) && isset( $_POST['price'] ) && isset( $_POST['credits'] ) )
    if( admin\actions::edit_payment_plan( $_GET['id'],
    array(
    'name' => $_POST['name'],
    'description' => $_POST['text'],
    'price' => $_POST['price'],
    'credits' => $_POST['credits'],
    'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 )
    ) ) ) {

    $info = \query\payments::plan_info( $_GET['id'] );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'payments_csrf' ) ) {

if( $_GET['type'] == 'delete_image' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_payment_plan_image( $_GET['id'] ) ) {
    $info->image = '';
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$_SESSION['payments_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">

<div class="row"><span>' . t( 'form_name', "Name" ) . ':</span><div><input type="text" name="name" value="' . $info->name . '" /></div></div>
<div class="row"><span>' . t( 'form_price', "Price" ) . ':</span><div><input type="text" name="price" value="' . $info->price_format . '" placeholder="' . sprintf( PRICE_FORMAT, \site\utils::money_format( 0.00 ) ) . '" /></div> </div>
<div class="row"><span>' . t( 'form_credits', "Credits" ) . ':</span><div><input type="number" name="credits" min="0" value="' . $info->credits . '" /></div> </div>

<div class="row image-upload"><span>' . t( 'form_image', "Image" ) . ':</span>

<div>
<div style="display: table; margin-bottom: 2px;"><img src="' . \query\main::payment_plan_avatar( $info->image ) . '" class="avt" alt="" style="display:table-cell;vertical-align:middle;max-width:120px;height:80px;margin:0 20px 5px 0;" />
<div style="display: table-cell; vertical-align: middle; margin-left: 25px;">';
if( !empty( $info->image ) ) echo '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete_image', 'token' => $csrf ) ) . '" class="btn" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';
echo '</div>
</div>

<input type="file" name="logo" accept="image/*" /></div> </div>
<div class="row"><span>' . t( 'form_description', "Description" ) . ':</span><div><textarea name="text" style="min-height:100px;">' . $info->description . '</textarea></div></div>
<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish" ' . ( $info->visible ? ' checked' : '' ) . ' /> <label for="publish"><span></span> ' . t( 'msg_pubpplan', "Publish this plan" ) . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'pmts_editplan_button', "Edit Plan" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>


<div class="title" style="margin-top:40px;">

<h2>' . t( 'pmts_plan_info_title', "Information About This Plan" ) . '</h2>

</div>';

echo '<div class="info-table" style="padding-bottom:20px;">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . t( 'last_update_by', "Last update by" ) . ':</span> <div>' . ( empty( $info->lastupdate_by_name ) ? '-' : '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->lastupdate_by . '">' . $info->lastupdate_by_name . '</a>' ) . '</div></div>
<div class="row"><span>' . t( 'last_update_on', "Last update on" ) . ':</span> <div>' . $info->last_update . '</div></div>
<div class="row"><span>' . t( 'added_by', "Added by" ) . ':</span> <div>' . ( empty( $info->user_name ) ? '-' : '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->user . '">' . $info->user_name . '</a>' ) . '</div></div>
<div class="row"><span>' . t( 'added_on', "Added on" ) . ':</span> <div>' . $info->date . '</div></div>
</div>';

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/** LIST OF PLANS */

case 'plan_view':

if( !$GLOBALS['me']->is_admin ) die;

echo '<div class="title">

<h2>' . t( 'pmts_plans_title', "Payment Plans" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=payments.php&amp;action=plan_add" class="btn">' . t( 'payments_plan_add', "Add Plan" ) . '</a>';
echo '</div>';

$subtitle = t( 'pmts_plans_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_payments_page', 'after_title_view_plan_payments_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'payments_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_payment_plan( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

    if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
    if( admin\actions::payment_plan_action( $_POST['action'], array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'payments_csrf' ) ) {

if( $_GET['type'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_payment_plan( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::payment_plan_action( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['payments_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="payments.php" />
<input type="hidden" name="action" value="plan_view" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'price' => t( 'order_price', "Price" ), 'price desc' => t( 'order_price_desc', "Price DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="payments.php" />
<input type="hidden" name="action" value="plan_view" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'pmts_plans_search_input', "Search plans" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$p = \query\payments::have_plans( $options = array( 'per_page' => 10, 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( !empty( $_GET['search'] ) ) echo ' / <a href="?route=payments.php&amp;action=plan_view">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=payments.php&amp;action=plan_view" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>

<div class="bulk_options">

    <button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button> ';

    echo t( 'action', "Action" ) . ': ';
    echo '<select name="action">';
    foreach( array( 'publish' => t( 'publish', "Publish" ), 'unpublish' => t( 'unpublish', "Unpublish" ) ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
    echo '</select>
    <button class="btn" name="set_action">' . t( 'set_all', "Set to All" ) . '</button>

</div>';

foreach( \query\payments::while_plans( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

    $links = array();

    $links['edit'] = '<a href="?route=payments.php&amp;action=plan_edit&amp;id=' . $item->ID . '">' . t( 'edit', "Edit" ) . '</a>';
    $links['publish'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( !$item->visible ? 'publish' : 'unpublish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( !$item->visible ? t( 'publish', "Publish" ) : t( 'unpublish', "Unpublish" ) ) . '</a>';
    $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'payment_plan', $item, $links );

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

} else echo '<div class="a-alert">' . t( 'no_pmtsplans_yet', "No payment plans yet." ) . '</div>';

break;

/** VIEW INVOICE */

case 'invoice_view':

if( !ab_to( array( 'payments' => 'view' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'pmts_viewinv_title', "View Invoice" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $invoice_exists = \query\payments::invoice_exists( $_GET['id'] ) ) ) {

$info = \query\payments::invoice_info( $_GET['id'] );

if( ab_to( array( 'payments' => 'edit' ) ) ) {
echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( $GLOBALS['me']->is_admin ) echo '<li><a href="?route=suggestions.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>';
if( $info->paid ) {
    echo '<li><a href="?route=payments.php&amp;action=list&amp;type=unpaid&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'set_as_unpaid', "Set as Unpaid" ) . '</a></li>';
} else {
    echo '<li><a href="?route=payments.php&amp;action=list&amp;type=paid&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'set_as_paid', "Set as Paid" ) . '</a></li>';
}
if( $info->delivered ) {
    echo '<li><a href="?route=payments.php&amp;action=list&amp;type=undelivered&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'set_as_undelivered', "Set as Undelivered" ) . '</a></li>';
} else {
    echo '<li><a href="?route=payments.php&amp;action=list&amp;type=delivered&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'set_as_delivered', "Set as Delivered" ) . '</a></li>';
}
echo '</ul>
</div>';
}

}

echo '<a href="?route=payments.php&amp;action=list" class="btn">' . t( 'payments_invoices', "Invoices" ) . '</a>
</div>';

$subtitle = t( 'pmts_viewinv_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $invoice_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_payments_page', 'after_title_view_invoice_payments_page' ) );

$_SESSION['payments_csrf'] = $csrf;

echo '<div class="info-table">

<div class="row"><span>' . t( 'pmts_form_gateway', "Gateway" ) . ':</span><div>' . $info->gateway . '</div></div>
<div class="row"><span>' . t( 'pmts_form_transid', "Transaction ID" ) . ':</span><div>' . $info->transaction_id . '</div></div>
<div class="row"><span>' . t( 'pmts_form_state', "State" ) . ':</span><div>' . $info->state . '</div></div>
<div class="row"><span>' . t( 'pmts_form_paid', "Paid" ) . ':</span><div>' . ( $info->paid ? t( 'yes', "Yes" ) : t( 'no', "No" ) ). '</div></div>
<div class="row"><span>' . t( 'pmts_form_delivered', "Delivered" ) . ':</span><div>' . ( $info->delivered ? t( 'yes', "Yes" ) : t( 'no', "No" ) ). '</div></div>
<div class="row"><span>' . t( 'form_details', "Details" ) . ':</span><div>' . $info->details . '</div></div>
<div class="row"><span>' . t( 'pmts_form_items', "Items" ) . ':</span><div>

<ul style="list-style-type:none;">';

foreach( $info->items as $line ) {
    echo '<li>' . ( is_array( $line ) ? esc_html( implode( ' / ', $line ) ) : esc_html( $line ) ) . '</li>';
}
echo '</ul>

</div></div>

<div class="row"><span>' . t( 'last_update_by', "Last update by" )    . ':</span> <div>' . ( empty( $info->lastupdate_by_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->lastupdate_by . '">' . $info->lastupdate_by_name . '</a>' : $info->lastupdate_by_name ) ) . '</div></div>
<div class="row"><span>' . t( 'last_update_on', "Last update on" ) . ':</span><div>' . $info->last_update . '</div></div>
<div class="row"><span>' . t( 'owner', "Owner" ) . ':</span><div>' . ( empty( $info->user_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->user . '">' . $info->user_name . '</a>' : $info->user_name ) ) . '</div></div>
<div class="row"><span>' . t( 'added_on', "Added on" ) . ':</span><div>' . $info->date . '</div></div>

</div>';

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/** LIST OF TRANSACTIONS */

default:

if( !ab_to( array( 'payments' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'pmts_invoices_title', "Invoices" ) . '</h2>';

$subtitle = t( 'pmts_invoices_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_payments_page', 'after_title_list_transactions_payments_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'payments_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_payment( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

    if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
    if( admin\actions::action_payment( $_POST['action'], array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'payments_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_payment( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( in_array( $_GET['type'], array( 'paid', 'unpaid', 'delivered', 'undelivered' ) ) ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::action_payment( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['payments_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="payments.php" />
<input type="hidden" name="action" value="list" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'price' => t( 'order_price', "Price" ), 'price desc' => t( 'order_price_desc', "Price DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select> ';

echo '<select name="view">';
foreach( array( '' => t( 'all_invoices', "All invoices" ), 'paid' => t( 'view_paid', "Paid" ), 'unpaid' => t( 'view_unpaid', "Unpaid" ), 'delivered' => t( 'view_delivered', "Delivered" ), 'undelivered' => t( 'view_undelivered', "Undelivered" ), 'undeliveredpayments' => t( 'view_paidandundelivered', "Paid&Undeliv." ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['view'] ) && $_GET['view'] == $k ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="payments.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'pmts_trans_search_input', "Search invoices" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$p = \query\payments::have_invoices( $options = array( 'per_page' => 10, 'show' => (isset( $_GET['view'] ) ? $_GET['view'] : ''), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( !empty( $_GET['view'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=payments.php&amp;action=list">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=payments.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

$ab_edt    = ab_to( array( 'payments' => 'edit' ) );
$ab_del = $GLOBALS['me']->is_admin;

if( $ab_edt ) {

echo '<div class="bulk_options">';

    if( $GLOBALS['me']->is_admin ) echo '<button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button> ';

    echo t( 'action', "Action" ) . ':
    <select name="action">';
    foreach( array( 'paid' => t( 'paid', "Paid" ), 'unpaid' => t( 'unpaid', "Unpaid" ), 'delivered' => t( 'delivered', "Delivered" ), 'undelivered' => t( 'undelivered', "Undelivered" ) ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
    echo '</select>
    <button class="btn" name="set_action">' . t( 'set_all', "Set to All" ) . '</button>';

echo '</div>';

}

foreach( \query\payments::while_invoices( array_merge( array( 'page' => $p['page'], 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

    $links = array();

    $links['view'] = '<a href="?route=payments.php&amp;action=invoice_view&amp;id=' . $item->ID . '">' . t( 'view', "View" ) . '</a>';
    if( $ab_edt ) {
    $links['paid_unpaid'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( $item->paid ? 'unpaid' : 'paid' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( $item->paid ? t( 'unpaid', "Unpaid" ) : t( 'paid', "Paid" ) ). '</a>';
    $links['delivered_undelivered'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( $item->delivered ? 'undelivered' : 'delivered' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( $item->delivered ? t( 'undelivered', "Undelivered" ) : t( 'delivered', "Delivered" ) ). '</a>';
    }
    if( $ab_del ) $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'action' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'payment_invoice', $item, $links );

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

} else echo '<div class="a-alert">' . t( 'no_pmtstrans_yet', "No invoices yet." ) . '</div>';

break;

}