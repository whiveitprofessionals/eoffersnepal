<?php

switch( $_GET['action'] ) {

/** ADD REWARD */

case 'add':

if( !$GLOBALS['me']->is_admin ) die;

echo '<div class="title">

<h2>' . t( 'rewards_add_title', "Add New Reward" ) . '</h2>

<div style="float:right;margin:0 2px 0 0;">
<a href="?route=rewards.php&amp;action=list" class="btn">' . t( 'rewards_view', "View Rewards" ) . '</a>
</div>';

$subtitle = t( 'rewards_add_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_rewards_page', 'after_title_add_rewards_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'rewards_csrf' ) ) {

    if( isset( $_POST['name'] )    && isset( $_POST['points'] ) && isset( $_POST['text'] ) && isset( $_POST['fields'] ) )
    if( admin\actions::add_reward(
    array(
    'name' => $_POST['name'],
    'points' => $_POST['points'],
    'description' => $_POST['text'],
    'fields' => $_POST['fields'],
    'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 )
    ) ) )

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf = $_SESSION['rewards_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">

<div class="row"><span>' . t( 'form_name', "Name" ) . ':</span><div><input type="text" name="name" value="" /></div></div>
<div class="row"><span>' . t( 'form_image', "Image" ) . ':</span> <div><input type="file" name="logo" /></div> </div>
<div class="row"><span>' . t( 'rewards_form_points', "Points" ) . ' <span class="info"><span>' . t( 'rewards_form_ipoints', "The number of points which an user must  have to redeem this reward." ) . '</span></span>:</span><div><input type="number" name="points" value="100" /></div> </div>
<div class="row"><span>' . t( 'form_description', "Description" ) . ':</span><div><textarea name="text" style="min-height:100px;"></textarea></div></div>
<div class="row fields"><span>' . t( 'rewards_form_fields', "Fields" ) . ' <span class="info"><span>' . t( 'rewards_form_ifields', "To reward an user you may require some more information such as home address or PayPal account." ) . '</span></span>:</span><div>

<ul class="sortable fields_table">
<li class="head fixed" style="display: none;">
<div>' . t( 'rewards_table_name', "Name" ) . '</div>
<div>' . t( 'rewards_table_type', "Type" ) . '</div>
<div>' . t( 'rewards_table_value', "Value" ) . '</div>
<div></div>
<div class="options"></div>
</li>

<li class="fields_table_new" style="display:none;">

<div><input type="input" name="fields[name][]" /></div>
<div><select name="fields[type][]">';
foreach( array( 'text' => t( 'rewards_type_text', "Text" ), 'number' => t( 'rewards_type_number', "Number" ), 'email' => t( 'rewards_type_email', "Email" ), 'hidden' => t( 'rewards_type_hidden', "Hidden" ) ) as $k => $t ) echo '<option value="' . $k . '">' . $t . '</option>';
echo '</select></div>
<div><input type="text" name="fields[value][]" /></div>
<div><select name="fields[require][]">';
foreach( array( 0 => t( 'rewards_notrequired', "Not Required" ), 1 => t( 'rewards_required', "Required" ) ) as $k => $t ) echo '<option value="' . $k . '">' . $t . '</option>';
echo '</select></div>

<div class="options">
    <a href="#" class="move">v</a>
    <a href="#" class="remove">V</a>
</div>

</li>
</ul>

<a href="#" class="btn">' . t( 'rewards_addfield_button', "Add field" ) . '</a>

</div></div>

<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish" checked /> <label for="publish"><span></span> ' . t( 'msg_pubreward', "Publish this reward" ) . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'rewards_add_button', "Add Reward" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

/** EDIT REWARD */

case 'edit':

if( !$GLOBALS['me']->is_admin ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'rewards_edit_title', "Edit Reward" ) . '</h2>

<div style="float:right;margin:0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $reward_exists = \query\main::reward_exists( $_GET['id'] ) ) ) {

$info = \query\main::reward_info( $_GET['id'] );

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( $info->visible ) {
    echo '<li><a href="?route=rewards.php&amp;type=unpublish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'unpublish', "Unpublish" ) . '</a></li>';
} else {
    echo '<li><a href="?route=rewards.php&amp;type=publish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'publish', "Publish" ) . '</a></li>';
}
echo '<li><a href="?route=rewards.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>
</ul>
</div>';

}

echo '<a href="?route=rewards.php&amp;action=list" class="btn">' . t( 'rewards_view', "View Rewards" ) . '</a>
</div>';

$subtitle = t( 'rewards_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $reward_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_rewards_page', 'after_title_edit_rewards_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'rewards_csrf' ) ) {

    if( isset( $_POST['name'] ) && isset( $_POST['text'] ) && isset( $_POST['points'] ) && isset( $_POST['fields'] ) )
    if( admin\actions::edit_reward( $_GET['id'],
    array(
    'points' => $_POST['points'],
    'name' => $_POST['name'],
    'description' => $_POST['text'],
    'fields' => $_POST['fields'],
    'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 )
    ) ) ) {

    $info = \query\main::reward_info( $_GET['id'] );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'rewards_csrf' ) ) {

if( $_GET['type'] == 'delete_image' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_reward_image( $_GET['id'] ) ) {
    $info->image = '';
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$_SESSION['rewards_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">

<div class="row"><span>' . t( 'form_name', "Name" ) . ':</span><div><input type="text" name="name" value="' . $info->title . '" /></div></div>
<div class="row"><span>' . t( 'form_image', "Image" ) . ':</span>

<div>
<div style="display: table; margin-bottom: 2px;"><img src="' . \query\main::reward_avatar( $info->image ) . '" class="avt" alt="" style="display:table-cell;vertical-align:middle;max-width:120px;height:80px;margin: 0 20px 5px 0;" />
<div style="display: table-cell; vertical-align: middle; margin-left: 25px;">';
if( !empty( $info->image ) ) echo '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete_image', 'token' => $csrf ) ) . '" class="btn" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';
echo '</div>
</div>

<input type="file" name="logo" /></div> </div>

<div class="row"><span>' . t( 'rewards_form_points', "Points" ) . ' <span class="info"><span>' . t( 'rewards_form_ipoints', "The number of points which an user must  have to redeem this reward." ) . '</span></span>:</span><div><input type="number" name="points" value="' . $info->points . '" /></div> </div>
<div class="row"><span>' . t( 'form_description', "Description" ) . ':</span><div><textarea name="text" style="min-height:100px;">' . $info->description . '</textarea></div></div>

<div class="row fields"><span>' . t( 'rewards_form_fields', "Fields" ) . ' <span class="info"><span>' . t( 'rewards_form_ifields', "To reward an user you may require some more information such as home address or PayPal account." ) . '</span></span>:</span><div>';

echo '<ul class="sortable fields_table">
<li class="head fixed"' . ( empty( $info->fields ) ? ' style="display: none;"' : '' ) . '>
<div>' . t( 'rewards_table_name', "Name" ) . '</div>
<div>' . t( 'rewards_table_type', "Type" ) . '</div>
<div>' . t( 'rewards_table_value', "Value" ) . '</div>
<div></div>
<div class="options"></div>
</li>

<li class="fields_table_new" style="display:none;">

<div><input type="input" name="fields[name][]" /></div>
<div><select name="fields[type][]">';
foreach( array( 'text' => t( 'rewards_type_text', "Text" ), 'number' => t( 'rewards_type_number', "Number" ), 'email' => t( 'rewards_type_email', "Email" ), 'hidden' => t( 'rewards_type_hidden', "Hidden" ) ) as $k => $t ) echo '<option value="' . $k . '">' . $t . '</option>';
echo '</select></div>
<div><input type="text" name="fields[value][]" /></div>
<div><select name="fields[require][]">';
foreach( array( 0 => t( 'rewards_notrequired', "Not Required" ), 1 => t( 'rewards_required', "Required" ) ) as $k => $t ) echo '<option value="' . $k . '">' . $t . '</option>';
echo '</select></div>

<div class="options">
    <a href="#" class="move">v</a>
    <a href="#" class="remove">V</a>
</div>

</li>';

if( !empty( $info->fields ) ) {

foreach( $info->fields as $v ) {

echo '<li class="added_field">

<div><input type="input" name="fields[name][]" value="' . esc_html( $v['name'] ) . '" /></div>
<div><select name="fields[type][]">';
foreach( array( 'text' => t( 'rewards_type_text', "Text" ), 'number' => t( 'rewards_type_number', "Number" ), 'email' => t( 'rewards_type_email', "Email" ), 'hidden' => t( 'rewards_type_hidden', "Hidden" ) ) as $k => $t ) echo '<option value="' . $k . '"' . ( $k == esc_html( $v['type'] ) ? ' selected' : '' ) . '>' . $t . '</option>';
echo '</select></div>
<div><input type="text" name="fields[value][]" value="' . esc_html( $v['value'] ) . '" /></div>
<div><select name="fields[require][]">';
foreach( array( 0 => t( 'rewards_notrequired', "Not Required" ), 1 => t( 'rewards_required', "Required" ) ) as $k => $t ) echo '<option value="' . $k . '"' . ( $k == esc_html( $v['require'] ) ? ' selected' : '' ) . '>' . $t . '</option>';
echo '</select></div>

<div class="options">
    <a href="#" class="move">v</a>
    <a href="#" class="remove">V</a>
</div>

</li>';

}

}

echo '</ul>

<a href="#" class="btn">' . t( 'rewards_addfield_button', "Add field" ) . '</a>

</div></div>

<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish" ' . ( $info->visible ? ' checked' : '' ) . ' /> <label for="publish"><span></span> ' . t( 'msg_pubreward', "Publish this reward" ) . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'rewards_edit_button', "Edit Reward" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>


<div class="title" style="margin-top:40px;">

<h2>' . t( 'rewards_info_title', "Information About This Reward" ) . '</h2>

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

/** VIEW CLAIM REQUEST */

case 'view_rewardreq':

if( !ab_to( array( 'claim_reqs' => 'view' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'rewards_viewcr_title', "View Claim Request" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $request_exists = \query\main::reward_req_exists( $_GET['id'] ) ) ) {

$info = \query\main::reward_req_info( $_GET['id'] );

$ab_edt    = ab_to( array( 'claim_reqs' => 'edit' ) );
$ab_del = ab_to( array( 'claim_reqs' => 'delete' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( $ab_del ) echo '<li><a href="?route=rewards.php&amp;action=requests&amp;type=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>';
if( $info->claimed ) {
    if( $ab_edt ) echo '<li><a href="?route=rewards.php&amp;action=requests&amp;type=unclaim&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'unclaim', "Unclaim" ) . '</a></li>';
} else {
    if( $ab_edt ) echo '<li><a href="?route=rewards.php&amp;action=requests&amp;type=claim&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'claim', "Claim" ) . '</a></li>';
}
echo '</ul>
</div>';

}

}

echo '<a href="?route=rewards.php&amp;action=requests" class="btn">' . t( 'rewards_viewcr_button', "Claim Requests" ) . '</a>
</div>';

$subtitle = t( 'rewards_viewcr_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $request_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_rewards_page', 'after_title_view_request_rewards_page' ) );

$_SESSION['rewards_csrf'] = $csrf;

echo '<div class="info-table">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . t( 'rewards_req_form_pused', "Points used" ) . ':</span> <div>' . $info->points . '</div></div>
<div class="row"><span>' . t( 'rewards_form_reward', "Reward" ) . ':</span> <div>' . ( $info->reward_exists ? '<a href="?route=rewards.php&amp;action=requests&amp;reward=' . $info->reward . '">' . $info->name . '</a>' . ( $GLOBALS['me']->is_admin ? ' / <a href="?route=rewards.php&amp;action=edit&amp;id=' . $info->reward . '">' . t( 'rewards_edit_button', "Edit Reward" ) . '</a>' : '' ) : $info->name ) . '</div></div>';

if( !empty( $info->fields ) ) {
foreach( $info->fields as $k => $v )
    echo '<div class="row"><span>' . esc_html( $k ) . ':</span> <div>' . ( !empty( $v ) ? esc_html( $v ) : '-' ) . '</div></div>';
}

echo '<div class="row"><span>' . t( 'last_update_by', "Last update by" ) . ':</span> <div>' . ( empty( $info->lastupdate_by_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->lastupdate_by . '">' . $info->lastupdate_by_name . '</a>' : $info->lastupdate_by_name ) ) . '</div></div>
<div class="row"><span>' . t( 'last_update_on', "Last update on" ) . ':</span> <div>' . $info->last_update . '</div></div>
<div class="row"><span>' . t( 'added_by', "Added by" ) . ':</span> <div>' . ( empty( $info->user_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->user . '">' . $info->user_name . '</a>' : $info->user_name ) ) . '</div></div>
<div class="row"><span>' . t( 'added_on', "Added on" ) . ':</span> <div>' . $info->date . '</div></div>

</div>';

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/** LIST OF CLAIM REQUESTS */

case 'requests':

if( !ab_to( array( 'claim_reqs' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'rewards_req_title', "Claim Requests" ) . '</h2>';

$subtitle = t( 'rewards_req_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_rewards_page', 'after_title_list_requests_rewards_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'rewards_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_reward_req( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::action_reward_req( $_POST['action'], array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'rewards_csrf' ) ) {

if( $_GET['type'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_reward_req( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'claim' || $_GET['type'] == 'unclaim' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::action_reward_req( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['rewards_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="rewards.php" />
<input type="hidden" name="action" value="requests" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'points' => t( 'order_points', "Points" ), 'points desc' => t( 'order_points_desc', "Points DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

 <select name="view">';
foreach( array( '' => t( 'all_requests', "All requests" ), 'valid' => t( 'view_claimed', "Claimed" ), 'notvalid' => t( 'view_unclaimed', "Unclaimed" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['view'] ) && $_GET['view'] == $k ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="rewards.php" />
<input type="hidden" name="action" value="requests" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['view'] ) ) {
echo '<input type="hidden" name="view" value="' . esc_html( $_GET['view'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'rewards_req_search_input', "Search claim requests" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';


$p = \query\main::have_rewards_reqs( $options = array( 'per_page' => 10, 'user' => (isset( $_GET['user'] ) ? $_GET['user'] : ''), 'reward' => (isset( $_GET['reward'] ) ? $_GET['reward'] : ''), 'show' => (isset( $_GET['view'] ) ? $_GET['view'] : ''), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( !empty( $_GET['user'] ) || !empty( $_GET['reward'] ) || !empty( $_GET['view'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=rewards.php&amp;action=requests">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=rewards.php&amp;action=requests" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

$ab_edt    = ab_to( array( 'claim_reqs' => 'edit' ) );
$ab_del    = ab_to( array( 'claim_reqs' => 'delete' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="bulk_options">';

    if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button> ';

    if( $ab_edt ) {
        echo t( 'action', "Action" ) . ':
        <select name="action">';
        foreach( array( 'claim' => t( 'claim', "Claim" ), 'unclaim' => t( 'unclaim', "Unclaim" ) ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
        echo '</select>
        <button class="btn" name="set_action">' . t( 'set_all', "Set to All" ) . '</button>';
    }

echo '</div>';

}

foreach( \query\main::while_rewards_reqs( array_merge( array( 'page' => $p['page'], 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

    $links = array();

    $links['view'] = '<a href="?route=rewards.php&amp;action=view_rewardreq&amp;id=' . $item->ID . '">' . t( 'view', "View" ) . '</a>';
    if( $ab_edt ) $links['claim_unclaim'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( $item->claimed ? 'unclaim' : 'claim' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( $item->claimed ? t( 'unclaim', "Unclaim" ) : t( 'claim', "Claim" ) ) . '</a>';
    if( $ab_del ) $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'reward_request', $item, $links );

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

} else echo '<div class="a-alert">' . t( 'no_claimreq_yet', "No claim requests yet." ) . '</div>';

break;

/** LIST OF REWARDS */

default:

if( !$GLOBALS['me']->is_admin ) die;

echo '<div class="title">

<h2>' . t( 'rewards_title', "Rewards" ) . '</h2>

<div style="float:right;margin:0 2px 0 0;">
<a href="?route=rewards.php&amp;action=add" class="btn">' . t( 'rewards_add', "Add Reward" ) . '</a>
</div>';

$subtitle = t( 'rewards_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_rewards_page', 'after_title_list_rewards_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'rewards_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_reward( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'rewards_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_reward( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::action_reward( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['rewards_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="rewards.php" />
<input type="hidden" name="action" value="list" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'points' => t( 'order_points', "Points" ), 'points desc' => t( 'order_points_desc', "Points DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="rewards.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'rewards_search_input', "Search rewards" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$p = \query\main::have_rewards( $options = array( 'per_page' => 10, 'show' => 'all', 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( !empty( $_GET['search'] ) ) echo ' / <a href="?route=rewards.php&amp;action=list">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=rewards.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>

<div class="bulk_options">
    <button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button>
</div>';

foreach( \query\main::while_rewards( array_merge( array( 'page' => $p['page'], 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

    $links = array();

    $links['edit'] = '<a href="?route=rewards.php&amp;action=edit&amp;id=' . $item->ID . '">' . t( 'edit', "Edit" ) . '</a>';
    $links['publish'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( !$item->visible ? 'publish' : 'unpublish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( !$item->visible ? t( 'publish', "Publish" ) : t( 'unpublish', "Unpublish" ) ) . '</a>';
    $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'action' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'reward', $item, $links );

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

} else echo '<div class="a-alert">' . t( 'no_rewards_yet', "No rewards yet." ) . '</div>';

break;

}