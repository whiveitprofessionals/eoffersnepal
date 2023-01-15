<?php

if( !$GLOBALS['me']->is_admin ) die;

switch( $_GET['action'] ) {

/** INSTALL PLUGIN */

case 'install':

echo '<div class="title">

<h2>' . t( 'plugins_istl_title', "Install Plugin" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=plugins.php&amp;action=list" class="btn">' . t( 'plugins_view', "View Plugins" ) . '</a>
</div>';

$subtitle = t( 'plugins_istl_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_plugins_page', 'after_title_install_plugins_page' ) );

echo '<div id="upload-theme-form">';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'plugins_csrf' ) ) {

    if( isset( $_FILES['file'] ) ) {

    try {
        admin\actions::extract_plugin( $_FILES['file']['name'], $_FILES['file']['tmp_name'] );
        echo '<div class="a-success">' . t( 'plugins_installed', "The plugin has been installed." ) . '</div>';
    }

    catch( Exception $e ) {
        echo '<div class="a-error">' . $e->getMessage() . '</div>';
    }

    } else if( isset( $_POST['URL'] ) ) {

    try {
        admin\actions::extract_plugin( $_POST['URL'] );
        echo '<div class="a-success">' . t( 'plugins_installed', "The plugin has been installed." ) . '</div>';
    }

    catch( Exception $e ) {
        echo '<div class="a-error">' . $e->getMessage() . '</div>';
    }

    }

}

$csrf = $_SESSION['plugins_csrf'] = \site\utils::str_random(10);

/* */

if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) echo '<div class="a-alert">' . t( 'plugins_install_msg', "Big attention! It's a risk everytime when you upload plugins from unofficial sources. You can check for new plugins at <a href='http://couponscms.com/plugins.html' class='link' target='_blank'>CouponsCMS.com</a>" ) . '</div>';

echo '<div class="form-table col">';

echo '<form action="#" method="POST" enctype="multipart/form-data">
<div class="row"><span>' . t( 'plugins_select_plugin', "Select Plugin" ) . ':</span><div><input type="file" name="file" value="" /></div></div>
<input type="hidden" name="csrf" value="' . $csrf . '" />
<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'plugins_upload_button', "Upload" ) . '</button>
    </div>
    <div></div>
</div>
</form>

<div style="margin: 10px 0; text-align: center;">
    <h2>' . t( 'plugins_orviaurl', "Download via URL" ) . '</h2>
</div>

<form action="#" method="POST">
<div class="row"><span>' . t( 'plugins_url_plugin', "Plugin URL" ) . ':</span><div><input type="text" name="URL" value="" placeholder="' . t( 'plugins_urlph', "example: http://couponscms.com/plugins/newplugin.zip" ) . '" /></div></div>
<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'plugins_install_button', "Install" ) . '</button>
    </div>
    <div></div>
</div>
</form>';

echo '</div></div>';

echo '<div id="process-theme">
    <h2>' . t( 'plugins_upload_dleave', "Please do not leave this page during the upload!" ) . '</h2>
</div>';

break;

/** PLUGIN EDITOR */

case 'editor':

echo '<div class="title">

<h2>' . t( 'plugins_edit_title', "Edit Plugin" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=plugins.php&amp;action=list" class="btn">' . t( 'plugins_view', "View Plugins" ) . '</a>
</div>';

$subtitle = t( 'plugins_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( isset( $_GET['id'] ) && admin\admin_query::plugin_exists( $_GET['id'] ) ) {

do_action( array( 'after_title_inner_page', 'after_title_plugins_page', 'after_title_editor_plugins_page' ) );

$info = admin\admin_query::plugin_info( $_GET['id'] );

$directory = dirname( $info->main_file );

if( empty( $_GET['page'] ) )
    $page = '/' . basename( $info->main_file );

else {

if( file_exists( DIR . '/' . UPDIR . '/' . $directory . '/' . ( $page_loc = str_replace( array( '../', './', '..\\', '.\\' ), '', $_GET['page'] ) ) ) ) {
    $page =  $page_loc;
} else {
    $page = '/' . basename( $info->main_file );
}

}

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'plugins_csrf' ) ) {

    if( isset( $_POST['text'] ) )
    if( admin\actions::edit_plugin_page( $directory,
    array(
    'page' => $page,
    'text' => $_POST['text']
    ) ) )

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf = $_SESSION['plugins_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

' . sprintf( t( 'plugin_editor_title', "Edit plugin: %s" ), $info->name ) . '

<form action="#" method="GET" autocomplete="off" style="float: right;">
<input type="hidden" name="route" value="plugins.php" />
<input type="hidden" name="action" value="editor" />
<input type="hidden" name="id" value="' . esc_html( $_GET['id'] ) . '" />
<select name="page">';
foreach( admin\template::plugin_editor_map( $directory ) as $p ) echo '<option value="' . $p . '"' . ( $p == $page ? ' selected' : '' ) . '>' . $p . '</option>';
echo '</select>
<button class="btn">' . t( 'view', "View" ) . '</button>
</form>

</div>';

echo '<div class="form-table">

<form action="#" method="POST">

<textarea name="text" style="min-height: 450px; width: 100%; box-sizing: border-box; margin-bottom: 10px;">' . esc_html( file_get_contents( DIR . '/' . UPDIR . '/' . $directory . '/' . $page ) ) . '</textarea>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'save', "Save" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/** EDIT PLUGIN */

case 'edit':

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'plugins_edit_title', "Edit Plugin" ) . '</h2>

<div style="float:right;margin:0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $plugin_exists = admin\admin_query::plugin_exists( $_GET['id'] ) ) ) {

$info = admin\admin_query::plugin_info( $_GET['id'] );

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
echo '<li><a href="?route=plugins.php&amp;action=editor&amp;id=' . $_GET['id'] . '">' . t( 'editor', "Editor" ) . '</a></li>';
echo '<li><a href="?route=plugins.php&amp;action=uninstall&amp;id=' . $_GET['id'] . '">' . t( 'plugins_uninstall', "Uninstall" ) . '</a></li>';

echo '</ul>
</div>';

}

echo '<a href="?route=plugins.php&amp;action=list" class="btn">' . t( 'plugins_view', "View Plugins" ) . '</a>
</div>';

$subtitle = t( 'plugins_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $plugin_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_plugins_page', 'after_title_edit_plugins_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'plugins_csrf' ) ) {

    if( isset( $_POST['description'] ) )
    if( admin\actions::edit_plugin( $_GET['id'],
    array(
    'description' => $_POST['description'],
    'menu' => ( isset( $_POST['in_menu'] ) ? 1 : 0 ),
    'icon' => ( isset( $_POST['menu_ico'] ) ? $_POST['menu_ico'] : 1 ),
    'subadmin_v' => ( isset( $_POST['view_subadmin'] ) ? 1 : 0 ),
    'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 )
    ) ) ) {

    $info = admin\admin_query::plugin_info( $_GET['id'] );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'plugins_csrf' ) ) {

if( $_GET['type'] == 'delete_image' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_plugin_image( $_GET['id'] ) ) {
    $info->image = '';
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$_SESSION['plugins_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">

<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span>

<div><input type="checkbox" name="publish" id="publish"' . ( $info->visible ? ' checked' : '' ) . ' /> <label for="publish"><span></span> ' . t( 'msg_pubplugin', "Publish this plugin" ) . '</label>';

if( $info->menu_ready ) {

echo '<div class="plugin-feature-icon"><input type="checkbox" name="in_menu" id="in_menu"' . ( $info->menu ? ' checked' : '' ) . ' /> <label for="in_menu"><span></span> ' . t( 'msg_pubplugmenu', "Publish this plugin in menu" ) . '</label><br />';
foreach( array( 1 => 'c', 2 => 'd', 3 => 'e', 4 => 'f', 5 => 'g', 6 => 'h', 7 => 'i', 8 => 'j', 9 => 'k', 10 => 'l', 11 => 'm', 12 => 'n', 13 => 'o' ) as $k => $v ) {
    echo '<input type="radio" id="icon-' . $k . '" name="menu_ico" value="' . $k . '"' . ( $info->menu_icon == $k ? ' checked' : '' ) . '> <label for="icon-' . $k . '"><span></span> <b class="couponscms-font">' . $v . '</b></label>';
}
echo '</div>';

}

echo '</div>
</div>

<div class="row"><span>' . t( 'form_accessibility', "Accessibility" ) . ':</span><div><input type="checkbox" name="view_subadmin" id="view_subadmin"' . ( $info->subadmin_view ? ' checked' : '' ) . ' /> <label for="view_subadmin"><span></span> ' . t( 'view_subadmins', "Sub-Administrators" ) . '</label></div></div>

<div class="row"><span>' . t( 'form_image', "Image" ) . ':</span>

<div>
<div style="display: table; margin-bottom: 2px;"><img src="' . ( empty( $info->image ) ? '../' . DEFAULT_IMAGES_LOC . '/plugin_ico.png' : '../' . $info->image ) . '" class="avt" alt="" style="display:table-cell;vertical-align:middle;width:100px;height:50px;margin:0 20px 5px 0;" />
<div style="display: table-cell; vertical-align: middle; margin-left: 25px;">';
if( !empty( $info->image ) ) echo '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete_image', 'token' => $csrf ) ) . '" class="btn" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';
echo '</div>
</div>

<input type="file" name="image" />
</div> </div>

<div class="row"><span>' . t( 'form_description', "Description" ) . ':</span><div><textarea name="description">' . $info->description . '</textarea></div></div>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'plugins_edit_button', "Edit Plugin" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

echo '<div class="title" style="margin-top: 40px;">

<h2>' . t( 'plugin_info_title', "Information About This Plugin" ) . '</h2>

</div>';

echo '<div class="info-table" style="padding-bottom: 20px;">';

$uploader = \query\main::user_info( $info->user )->name;

echo '<div class="row"><span>' . t( 'uploader', "Uploader" ) . ':</span> <div>' . ( empty( $uploader ) ? '-' : '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->user . '">' . $uploader . '</a>' ) . '</div></div>
<div class="row"><span>' . t( 'added_on', "Added on" ) . ':</span> <div>' . $info->date . '</div></div>

</div>';

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/** UNINSTALL */

case 'uninstall':

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'plugins_uninstall_title', "Uninstalling ..." ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';
echo '<a href="?route=plugins.php&amp;action=list" class="btn">' . t( 'plugins_view', "View Plugins" ) . '</a>
</div>';

echo '</div>';

if( isset( $_GET['id'] ) && ( $plugin_exists = admin\admin_query::plugin_exists( $_GET['id'] ) ) ) {

do_action( array( 'after_title_inner_page', 'after_title_plugins_page', 'after_title_uninstall_plugins_page' ) );

$info = admin\admin_query::plugin_info( $_GET['id'] );

echo '<div class="a-message">' . t( 'delete_plugin', "After confirmation these files and information will be permanently deleted." ) . '</div>';

echo '<div class="title" style="margin-top: 40px;">
    <h2>' . t( 'plugins_unist_files', "Files and directories" ) . '</h2>
</div>

<ul class="list-of-items">';
echo '<li>' . UPDIR . '/' . dirname( $info->main_file ) . '</li>';
echo '</ul>';

if( isset( $info->uninstall_preview['delete']['tables'] ) ) {
echo '<div class="title" style="margin-top: 40px;">
    <h2>' . t( 'plugins_unist_tables', "Database tables" ) . '</h2>
</div>

<ul class="list-of-items">';
foreach( explode( ',', $info->uninstall_preview['delete']['tables'] ) as $table ) {
    echo '<li>' . \site\plugin::replace_constant( $table ) . '</li>';
}
echo '</ul>';

}

if( isset( $info->uninstall_preview['delete']['columns'] ) ) {
echo '<div class="title" style="margin-top: 40px;">
    <h2>' . t( 'plugins_unist_columns', "Database columns" ) . '</h2>
</div>

<ul class="list-of-items">';
foreach( explode( ',', $info->uninstall_preview['delete']['columns'] ) as $column ) {
    $coltab = explode( '/', $column );
    if( count( $coltab ) === 2 ) {
    echo '<li>' . esc_html( $coltab[0] ) . ' from ' . \site\plugin::replace_constant( trim( $coltab[1] ) ) . '</li>';
    }
}
echo '</ul>';

}

if( isset( $info->uninstall_preview['delete']['options'] ) ) {
echo '<div class="title" style="margin-top: 40px;">
    <h2>' . t( 'plugins_unist_options', "Options" ) . '</h2>
</div>

<ul class="list-of-items">';
foreach( explode( ',', $info->uninstall_preview['delete']['options'] ) as $option ) {
    echo '<li>' . esc_html( $option ) . '</li>';
}
echo '</ul>';

}

$_SESSION['plugins_csrf'] = $csrf;

echo '<h3 style="text-align:center;">' . sprintf( t( 'plugins_unist_confq', "Are you sure that you want to uninstall %s?" ), $info->name ) . '</h3>';

echo '<form method="GET" style="text-align: center;">
<input type="hidden" name="route" value="plugins.php" />
<input type="hidden" name="action" value="delete" />
<input type="hidden" name="id" value="' . $_GET['id'] . '" />
<input type="hidden" name="token" value="' . $csrf . '" />
<button class="btn">' . t( 'plugins_cuinstall_button', "Confirm" ) . '</button>
</form>';

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/** LIST OF PLUGINS */

default:

echo '<div class="title">

<h2>' . t( 'plugins_title', "Plugins" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';
echo '<a href="?route=plugins.php&amp;action=install" class="btn">' . t( 'plugins_install', "Install" ) . '</a>';
echo '</div>';

$subtitle = t( 'plugins_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_plugins_page', 'after_title_list_plugins_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'plugins_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_plugin( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

    if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
    if( admin\actions::action_plugin( $_POST['action'], array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'plugins_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_plugin( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::action_plugin( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['plugins_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="plugins.php" />
<input type="hidden" name="action" value="list" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

 <select name="type">
<option value="">' . t( 'all_plugins', "All plugins" ) . '</option>';
foreach( array( 'languages' => t( 'view_languages', "Languages" ), 'payment_gateways' => t( 'view_payment_gateways', "Payment Gateways" ), 'feed_servers' => t( 'view_feed_servers', "Feed Servers" ), 'applications' => t( 'view_applications', "Applications" ) ) as $k => $type ) {
    echo '<option value="' . $k . '"' . ( isset( $_GET['type'] ) && $_GET['type'] == $k ? ' selected' : '' ) . '>' . $type . '</option>';
}

echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="plugins.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['type'] ) ) {
echo '<input type="hidden" name="type" value="' . esc_html( $_GET['type'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'plugins_search_input', "Search plugins" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$p = admin\admin_query::have_plugins( $options = array( 'per_page' => 10, 'show' => (isset( $_GET['type'] ) ? $_GET['type'] : ''), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( !empty( $_GET['type'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=plugins.php&amp;action=list">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=plugins.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

echo '<div class="bulk_options">';

    echo t( 'action', "Action" ) . ': ';
    echo '<select name="action">';
    foreach( array( 'publish' => t( 'publish', "Publish" ), 'unpublish' => t( 'unpublish', "Unpublish" ) ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
    echo '</select>
    <button class="btn" name="set_action">' . t( 'set_all', "Set to All" ) . '</button>';

echo '</div>';

foreach( admin\admin_query::while_plugins( array_merge( array( 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

    $links = array();

    if( empty( $item->scope ) ) {
        $links['open'] = '<a href="?plugin=' . $item->main_file . '">' . t( 'open', "Open" ) . '</a>';
    }
    $links['edit'] = '<a href="?route=plugins.php&amp;action=edit&amp;id=' . $item->ID . '">' . t( 'edit', "Edit" ) . '</a>';
    $links['publish'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( !$item->visible ? 'publish' : 'unpublish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( !$item->visible ? t( 'publish', "Publish" ) : t( 'unpublish', "Unpublish" ) ) . '</a>';

    if( !empty( $item->options_file ) ) {
        $links['options'] = '<a href="?plugin=' . $item->options_file . '">' . t( 'options', "Options" ) . '</a>';
    }

    $links['uninstall'] = '<a href="?route=plugins.php&amp;action=uninstall&amp;id=' . $item->ID . '">' . t( 'plugins_uninstall', "Uninstall" ) . '</a>';

    if( !empty( $item->description ) ) {
        $links['description'] = '<a href="javascript:void(0)" onclick="$(this).show_next( { element: \'div\' } ); return false;">' . t( 'description', "Description" ) . '</a>
        <div style="display: none; margin: 10px 0; font-size: 12px;">' . nl2br( $item->description ) . '</div>';
    }

    echo get_list_type( 'plugin', $item, $links );

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

} else echo '<div class="a-alert">' . t( 'no_plugins_yet', "No plugins yet." ) . '</div>';

break;

}