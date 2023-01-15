<?php

if( !( $template_widgets = admin\template::have_widgets() ) || !$GLOBALS['me']->is_admin ) {
    die;
}

switch( $_GET['action'] ) {

/** EDIT WIDGET */

case 'edit':

if( ( $exists = ( isset( $_GET['id'] ) && \query\main::widget_exists( $_GET['id'] ) ) ) ) {

    $info = \query\main::widget_info( $_GET['id'] );

}

echo '<div class="title">

<h2>' . t( 'widgets_edit_title', "Edit Widget" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=widgets.php&amp;action=list' . ( $exists ? '&amp;zone=' . $info->zone : '' ) . '" class="btn">' . t( 'widgets_view', "View Widgets" ) . '</a>
</div>';

$subtitle = t( 'widgets_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $exists ) {

do_action( array( 'after_title_inner_page', 'after_title_widgets_page', 'after_title_edit_widgets_page' ) );

if( $widget = admin\widgets::widget_from_id( $info->widget_id ) ) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'widgets2_csrf' ) ) {

    if( isset( $_POST['title'] ) )
    if( admin\actions::edit_widget( $_GET['id'],
    array(
    'title' => $_POST['title'],
    'position' => isset( $_POST['position'] ) ? $_POST['position'] : '',
    'text' => ( isset( $_POST['text'] ) ? $_POST['text'] : '' ),
    'type' => ( isset( $_POST['type'] ) ? $_POST['type'] : '' ),
    'order' => ( isset( $_POST['orderby'] ) ? $_POST['orderby'] : '' ),
    'limit' => ( isset( $_POST['limit'] ) ? $_POST['limit'] : '' ),
    'allow_html' => ( !empty( $widget->allow_html ) && isset( $_POST['html'] ) ? 1 : 0 ),
    'extra' => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() ),
    'mobi_view' => ( isset( $_POST['mobi_view'] ) ? 1 : 0 )
    ) ) ) {

    $info = \query\main::widget_info( $_GET['id'] );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf = $_SESSION['widgets2_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">

<div class="row"><span>' . t( 'form_title', "Title" ) . ':</span><div><input type="text" name="title" value="' . $info->title . '" /></div></div>';

if( !isset( $widget->position ) || $widget->position ) {
    echo '<div class="row"><span>' . t( 'form_position', "Position" ) . ':</span><div><input type="number" name="position" value="' . $info->position . '" min="1" /></div></div>';
}

if( !empty( $widget->allow_text ) ) echo '<div class="row"><span>' . t( 'form_text', "Text" ) . ':</span><div><textarea name="text">' . $info->text . '</textarea></div></div>';

if( !empty( $widget->allow_html ) ) echo '<div class="row"><span></span><div><input type="checkbox" name="html" id="allow_html"' . ( $info->html ? ' checked' : '' ) . ' /> <label for="allow_html"><span></span> ' . t( 'widget_allow_html', "Allow HTML" ) . '</label></div></div>';

if( isset( $widget->allow_show ) && ( $type = $widget->allow_show ) ) {
echo '<div class="row"><span>' . t( 'form_show_only', "Show Only" ) . ':</span><div><select name="type">';
foreach( $type as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $info->type ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select></div></div>';
}

if( isset( $widget->allow_orderby ) && ( $orderby = $widget->allow_orderby ) ) {
echo '<div class="row"><span>' . t( 'form_orderby', "Order by" ) . ':</span><div><select name="orderby">';
foreach( $orderby as $k => $v ) echo '<option value="' . $k . '"' . ( $k == $info->orderby ? ' selected' : '' ) . '>' . $v . '</option>';
echo '</select></div></div>';
}

if( !empty( $widget->allow_limit ) ) echo '<div class="row"><span>' . t( 'form_limit', "Limit" ) . ':</span><div><input type="number" name="limit" value="' . $info->limit . '" min="1"' . ( !empty( $widget->max_limit ) ? ' max="' . $widget->max_limit . '"' : '' ) . ' /></div></div>';

if( !empty( $widget->extra_fields ) ) {
    echo admin\widgets::build_extra( $widget->extra_fields, $info->extra )['markup'];
}

echo '<div class="row"><span>' . t( 'form_mobilev', "Mobile View" ) . ':</span><div><input type="checkbox" name="mobi_view" id="mobi_view"' . ( $info->mobile_view ? ' checked' : '' ) . ' /> <label for="mobi_view"><span></span> ' . t( 'msg_widget_mobilev', "Make this widget visible on mobile devices" ) . '</label></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'widgets_edit_button', "Edit Widget" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

} else echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/** LIST OF WIDGETS */

default:

if( isset( $_GET['zone'] ) && in_array( $_GET['zone'], array_keys( $template_widgets ) ) ) {
    list( $zone_id, $zone ) = array( $_GET['zone'], $template_widgets[$_GET['zone']] );
} else {
    list( $zone_id, $zone ) = array( key( $template_widgets ), current( $template_widgets ) );
}

$available = admin\widgets::available_list( $zone_id );

echo '<div class="title">

<h2>' . t( 'widgets_title', "Widgets" ) . '</h2>

<div style="float:right;margin:0 2px 0 0;">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="widgets.php" />
' . t( 'widgets_zones', "Zones" ) . ': <select name="zone">';
foreach( $template_widgets as $ID => $widgets )echo '<option value="' . $ID . '"' . ( $ID == $zone_id ? ' selected' : '' ) . '>' . $widgets['name'] . '</option>';
echo '</select>
<button class="btn">' . t( 'widgets_viewzone', "View Zone" ) . '</button>
</form>

</div>';

$subtitle = t( 'widgets_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_widgets_page', 'after_title_list_widgets_page' ) );

if( isset( $_GET['token'] ) && isset( $_GET['id'] ) && check_csrf( $_GET['token'], 'widgets_csrf' ) ) {

    if( isset( $_GET['add'] ) ) {

    if( in_array( $_GET['id'], array_keys( $available ) ) ) {

    $widget_info = $available[$_GET['id']];

    if( admin\actions::add_widget( $zone_id, $_GET['id'],
    array(
    'title' => $widget_info['name'],
    'file' => $widget_info['file'],
    'limit' => ( isset( $widget_info['def_limit'] ) ? $widget_info['def_limit'] : 10 ),
    'text' => ( isset( $widget_info['text'] ) ? $widget_info['text'] : '' )
    ) ) )

    echo '<div class="a-success">' . t( 'msg_added', "Added!" )    . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" )    . '</div>';

    }

    } else if( isset( $_GET['delete'] ) ) {

    if( admin\actions::delete_widget( $zone_id, $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" )    . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" )    . '</div>';

    }

}

$token = $_SESSION['widgets_csrf'] = \site\utils::str_random(10);

/* */

$zone_widgets = \query\main::show_widgets( $zone_id );

/* */

echo '<div class="form-table">

<ul class="elements-list el-two">

<li class="head">' . t( 'widgets_available', "Available Widgets" ) . '</li>';

foreach( $available as $ID => $widget ) {

    echo '<li>
    <div class="info-div"><h2>' . esc_html( $widget['name'] ) . '</h2></div>
    <div class="options">
    <a href="?route=widgets.php&amp;zone=' . $zone_id . '&amp;id=' . $ID . '&amp;add&amp;token=' . $token . '">' . t( 'add', "Add" ) . '</a>
    </div>';
    if( !empty( $widget['description'] ) ) {
        echo '<div style="color: #000; font-size: 13px; margin-top: 10px;">' . $widget['description'] . '</div>';
    }
    echo '</li>';

}

echo '</ul>

<ul class="elements-list el-two">

<li class="head">' . esc_html( $zone['name'] ) . '
<span>' . ( empty( $zone['description'] ) ? t( 'widgets_no_description', "No description for this zone." ) : esc_html( $zone['description'] ) ) . '</span></li>';

if( empty( $zone_widgets ) ) {
    echo '<li>' . t( 'widgets_no_widgets', "No widgets in this zone." ) . '</li>';
} else {

foreach( $zone_widgets as $widget ) {

    echo '<li>
    <div class="info-div"><h2>' . ( !isset( $available[$widget['widget_id']] ) ? t( 'widget_does_not_exist', 'Widget does not exist, delete it !' ) : $available[$widget['widget_id']]['name'] ) . '</h2></div>
    <div class="options">
    <a href="?route=widgets.php&amp;action=edit&amp;id=' . $widget['ID'] . '">' . t( 'edit', "Edit" ) . '</a>
    <a href="?route=widgets.php&amp;zone=' . $zone_id . '&amp;id=' . $widget['ID'] . '&amp;delete&amp;token=' . $token . '">' . t( 'delete', "Delete" ) . '</a>
    </div>
    </li>';

}

}

echo '</ul>

</div>';

break;

}