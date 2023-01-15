<?php

if( !$GLOBALS['me']->is_admin ) die;

switch( $_GET['action'] ) {

/** UPLOAD THEME */

case 'upload':

echo '<div class="title">

<h2>' . t( 'themes_upload_title', "Upload Theme" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=themes.php&amp;action=list" class="btn">' . t( 'themes_view', "View Themes" ) . '</a>
</div>';

$subtitle = t( 'themes_upload_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_themes_page', 'after_title_install_themes_page' ) );

echo '<div id="upload-theme-form">';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'themes_csrf' ) ) {

    if( isset( $_FILES['file'] ) ) {

    try {
        admin\actions::extract_theme( $_FILES['file']['name'], $_FILES['file']['tmp_name'] );
        echo '<div class="a-success">' . t( 'themes_installed', "The theme has been installed." ) . '</div>';
    }

    catch( Exception $e ) {
        echo '<div class="a-error">' . $e->getMessage() . '</div>';
    }

    } else if( isset( $_POST['URL'] ) ) {

    try {
        admin\actions::extract_theme( $_POST['URL'] );
        echo '<div class="a-success">' . t( 'themes_installed', "The theme has been installed." ) . '</div>';
    }

    catch( Exception $e ) {
        echo '<div class="a-error">' . $e->getMessage() . '</div>';
    }

    }

}

$csrf = $_SESSION['themes_csrf'] = \site\utils::str_random(10);

/* */

if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) echo '<div class="a-alert">' . t( 'themes_upload_msg', 'Big attention! It\'s a risk everytime when you upload themes from unofficial sources. You can check for new themes at <a href="http://couponscms.com/themes.html" class="link" target="_blank">CouponsCMS.com</a>' ) . '</div>';

echo '<div class="form-table col">';

echo '<form action="#" method="POST" enctype="multipart/form-data">
<div class="row"><span>' . t( 'themes_select_theme', "Select Theme" ) . ':</span><div><input type="file" name="file" value="" /></div></div>
<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . t( 'themes_upload_button', "Upload Theme" ) . '</button>
</form>

<div style="margin: 10px 0; text-align: center;">
    <h2>' . t( 'themes_orviaurl', "Download via URL" ) . '</h2>
</div>

<form action="#" method="POST">
<div class="row"><span>' . t( 'themes_url_theme', "Theme URL" ) . ':</span><div><input type="text" name="URL" value="" placeholder="' . t( 'themes_urlph', "example: http://couponscms.com/themes/newtheme.zip" ) . '" /></div></div>
<input type="hidden" name="csrf" value="' . $csrf . '" />
<button class="btn">' . t( 'themes_install_button', "Install Theme" ) . '</button>
</form>';

echo '</div></div>';

echo '<div id="process-theme">
    <h2>' . t( 'themes_upload_dleave', "Please do not leave this page during the upload!" ) . '</h2>
</div>';

break;

/** THEME EDITOR */

case 'editor':

echo '<div class="title">

<h2>' . t( 'themes_edit_title', "Theme Editor" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=themes.php&amp;action=common' . ( isset( $_GET['id'] ) ? '&amp;id=' . $_GET['id'] : '' ) . '" class="btn">' . t( 'themes_common', "Common Content" ) . '</a>
<a href="?route=themes.php&amp;action=list" class="btn">' . t( 'themes_view', "View Themes" ) . '</a>
</div>';

$subtitle = t( 'themes_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_themes_page', 'after_title_editor_themes_page' ) );

if( isset( $_GET['id'] ) && is_dir( DIR . '/' . THEMES_LOC . '/' . str_replace( array( '../', './', '..\\', '.\\' ), '', $_GET['id'] )    ) ) {

if( empty( $_GET['page'] ) )
    $page = '/index.php';

else {

if( file_exists( DIR . '/' . THEMES_LOC . '/' . $_GET['id'] . '/' . ( $page_loc = str_replace( array( '../', './', '..\\', '.\\' ), '', $_GET['page'] ) ) ) ) {
    $page = $page_loc;
} else {
    $page = '/index.php';
}

}

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'themes_csrf' ) ) {

    if( isset( $_POST['text'] ) )
    if( admin\actions::edit_theme_page( $_GET['id'],
    array(
    'page' => $page,
    'text' => $_POST['text'] )
    ) )

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf = $_SESSION['themes_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

' . sprintf( t( 'theme_edit_title', "Edit theme: %s" ), esc_html( $_GET['id'] ) ) . '

<form action="#" method="GET" autocomplete="off" style="float: right;">
<input type="hidden" name="route" value="themes.php" />
<input type="hidden" name="action" value="editor" />
<input type="hidden" name="id" value="' . esc_html( $_GET['id'] ) . '" />
<select name="page">';
foreach( admin\template::theme_editor_map( $_GET['id'] ) as $p )echo '<option value="' . $p . '"' . ( $p == ltrim( $page, '/' ) ? ' selected' : '' ) . '>' . $p . '</option>';
echo '</select>
<button class="btn">' . t( 'view', "View" ) . '</button>
</form>

</div>';

echo '<div class="form-table">

<form action="#" method="POST">

<textarea name="text" style="min-height: 450px; width: 100%; box-sizing: border-box; margin-bottom: 10px;">' . esc_html( file_get_contents( DIR . '/' . THEMES_LOC . '/' . $_GET['id'] . '/' . $page ) ) . '</textarea>

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

/** THEME OPTIONS */

case 'options':

if( admin\template::have_theme_options() ) {

$theme = esc_html( \query\main::get_option( 'theme' ) );

echo '<div class="title">

<h2>' . t( 'themes_edit_options_title', "Theme Options" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=themes.php&amp;action=list" class="btn">' . t( 'themes_view', "View Themes" ) . '</a>
</div>';

$subtitle = t( 'themes_edit_options_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . sprintf( $subtitle, $theme ) . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_themes_page', 'after_title_theme_options_themes_page' ) );

$info = \query\main::get_option( 'theme_options_' . strtolower( $theme ), true );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'themes_csrf' ) ) {

    if( isset( $_POST['extra'] ) )
    if( ( $options = admin\actions::edit_theme_options( strtolower( $theme ), $_POST['extra'] ) ) ) {

    $info = $options;

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf   = $_SESSION['themes_csrf'] = \site\utils::str_random(10);

$main   = admin\widgets::build_extra( theme_options(), $info );

admin\widgets::get_page_tabs( $main );

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data">';

echo $main['markup'];

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'save', "Save" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

}

break;

/** COMMPON PARTS EDITOR */

case 'common':

echo '<div class="title">

<h2>' . t( 'themes_common_title', "Common Content" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
' . ( isset( $_GET['id'] ) ? '<a href="?route=themes.php&amp;action=editor&amp;id=' . $_GET['id'] . '" class="btn">' . t( 'themes_editor', "Theme Editor" ) . '</a>' : '' ) . '
<a href="?route=themes.php&amp;action=list" class="btn">' . t( 'themes_view', "View Themes" ) . '</a>
</div>';

$subtitle = t( 'themes_common_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_themes_page', 'after_title_common_themes_page' ) );

if( empty( $_GET['page'] ) )
    $page = 'head.html';

else {

if( file_exists( DIR . '/' . COMMON_LOCATION . '/' . str_replace( array( '../', './', '..\\', '.\\' ), '', $_GET['page'] ) ) ) {
    $page = $_GET['page'];
} else {
    $page = 'head.html';
}

}

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'themes_csrf' ) ) {

    if( isset( $_POST['text'] ) )
    if( @file_put_contents( DIR . '/' . COMMON_LOCATION . '/' . $page, trim( $_POST['text'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf = $_SESSION['themes_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off" style="float: right;">
<input type="hidden" name="route" value="themes.php" />
<input type="hidden" name="action" value="common" />
' . ( isset( $_GET['id'] ) ? '<input type="hidden" name="id" value="' . $_GET['id'] . '" />' : '' ) . '
<select name="page">';
foreach( array( 'add_head() Extra lines' => 'head.html' ) as $k => $v )echo '<option value="' . $v . '"' . ( $v == $page ? ' selected' : '' ) . '>' . $k . '</option>';
echo '</select>
<button class="btn">' . t( 'view', "View" ) . '</button>
</form>

</div>';

echo '<div class="form-table">

<form action="#" method="POST">

<textarea name="text" style="min-height: 450px; width: 100%; box-sizing: border-box; margin-bottom: 10px;">' . esc_html( file_get_contents( DIR . '/' . COMMON_LOCATION . '/' . $page ) ) . '</textarea>

<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'save', "Save" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

break;

/** LIST OF MENUS */

case 'menus':

echo '<div class="title">

<h2>' . t( 'themes_menus_title', "Menus" ) . '</h2>';

$menus  = get( 'menus' );
$current_menu   = isset( $_GET['menu'] ) && in_array( $_GET['menu'], array_keys( (array) $menus ) ) ? $_GET['menu'] : ( !empty( $menus ) ? key( $menus ) : false );

if( $current_menu ) {
    echo '<div style="float:right;margin:0 2px 0 0;">
        <form action="#" method="GET" autocomplete="off">
            <input type="hidden" name="route" value="themes.php" />
            <input type="hidden" name="action" value="menus" />
            ' . t( 'themes_menus_menu', "Menu" ) . ': <select name="menu">';
            foreach( array_keys( $menus ) as $menu_id ) echo '<option value="' . $menu_id . '"' . ( $menu_id == $current_menu ? ' selected' : '' ) . '>' . $menu_id . '</option>';
            echo '</select>
            <button class="btn">' . t( 'themes_menus_viewmenu', "View Menu" ) . '</button>
        </form>
    </div>';
}

$subtitle = t( 'themes_menus_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $current_menu ) {

do_action( array( 'after_title_inner_page', 'after_title_themes_page', 'after_title_list_themes_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'themes_csrf' ) ) {

    if( admin\actions::edit_menu( $current_menu, ( isset( $_POST['links'] ) ? $_POST['links'] : array() ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_GET['do'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'themes_csrf' ) ) {

    if( $_GET['do'] == 'reset' ) {
        if( admin\actions::remove_option( array( 'links_menu_' . $current_menu ) ) )
        echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
        else
        echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';
    }

}

$csrf = $_SESSION['themes_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table no-border">

<div class="elements-list el-two">

<form action="?route=themes.php&amp;action=menus&amp;menu=' . $current_menu . '" method="POST" id="modify-menu-form">';

$menu = new \site\menu( $current_menu );

echo modify_menu( ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['links'] ) ? $_POST['links'] : $menu->links() ) );

echo '<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'save', "Save" ) . '</button>
    </div>
    <div>
        <a href="?route=themes.php&amp;action=menus&amp;do=reset&amp;menu=' . $current_menu . '&amp;token=' . $csrf . '">' . t( 'themes_menu_reset_default', 'Reset to default menu' ) . '</a>
    </div>
</div>

</form>

</div>

<div class="elements-list el-two">

<div class="info-container">
    <h5>' . t( 'themes_menus_add_links_title', 'Add items in menu' ) . '</h5>
    ' . t( 'themes_menus_add_links_info', 'Drag the item in menu' ) . '
</div>';

$link_options = array();

// Custom link
$markup = '<ul class="arrange-menu menu-options">';
$markup .= '<li><div class="head"><h2 class="move"><span>' . t( 'themes_menu_form_custom_title', 'Custom link' ) . '</span>
<div class="options">
    <a href="#" class="ccms hide">w</a>
    <a href="#" class="ccms view">S</a>
    <a href="#" class="ccms remove">V</a>
</div></h2>';
$markup .= '<div class="content">';
$fields = array();
$fields['name']         = array( 'type' => 'text',      'title' => t( 'themes_menu_form_title', 'Title' ), 'class' => 'name', 'default' => t( 'themes_menu_form_custom_link', 'Custom link' ) );
$fields['url']          = array( 'type' => 'text',      'title' => t( 'themes_menu_form_url', 'URL' ) );
$fields['open_type']    = array( 'type' => 'select',    'title' => t( 'themes_menu_form_open_type', 'Open Type' ), 'options' => array( '_self' => t( 'themes_menu_form_self_location', 'Self / Same window' ), '_blank' => t( 'themes_menu_form_blank_location', 'Blank / New window' ) ) );
$fields['class']        = array( 'type' => 'text',      'title' => t( 'themes_menu_form_extra_class', 'Extra Class (CSS)' ) );
$fields['type']         = array( 'type' => 'hidden',    'default' => 'custom' );
$markup .= build_form( 'links{id}', value_with_filter( array( 'modify_menu_fields', 'modify_menu_fields_custom' ), $fields, false ) )['markup'];
$markup .= '</div></div>';
$markup .= '<ul class="arrange-menu"></ul>';
$markup .= '</li>';
$markup .= '</ul>';

$link_options['custom'] = $markup;

// Homepage link
$markup = '<ul class="arrange-menu menu-options">';
$markup .= '<li><div class="head"><h2 class="move"><span>' . t( 'themes_menu_form_homepage_title', 'Home' ) . '</span>
<div class="options">
    <a href="#" class="ccms hide">w</a>
    <a href="#" class="ccms view">S</a>
    <a href="#" class="ccms remove">V</a>
</div></h2>';
$markup .= '<div class="content">';
$fields = array();
$fields['name']         = array( 'type' => 'text',      'title' => t( 'themes_menu_form_title', 'Title' ), 'class' => 'name', 'default' => t( 'themes_menu_form_home_link', 'Home' ) );
$fields['open_type']    = array( 'type' => 'select',    'title' => t( 'themes_menu_form_open_type', 'Open Type' ), 'options' => array( '_self' => t( 'themes_menu_form_self_location', 'Self / Same window' ), '_blank' => t( 'themes_menu_form_blank_location', 'Blank / New window' ) ) );
$fields['class']        = array( 'type' => 'text',      'title' => t( 'themes_menu_form_extra_class', 'Extra Class (CSS)' ) );
$fields['type']         = array( 'type' => 'hidden',    'default' => 'home' );
$markup .= build_form( 'links{id}', value_with_filter( array( 'modify_menu_fields', 'modify_menu_fields_homepage' ), $fields, false ) )['markup'];
$markup .= '</div></div>';
$markup .= '<ul class="arrange-menu"></ul>';
$markup .= '</li>';
$markup .= '</ul>';

$link_options['homepage'] = $markup;

// Stores link
$markup = '<ul class="arrange-menu menu-options">';
$markup .= '<li><div class="head"><h2 class="move"><span>' . t( 'themes_menu_form_stores_title', 'Stores' ) . '</span>
<div class="options">
    <a href="#" class="ccms hide">w</a>
    <a href="#" class="ccms view">S</a>
    <a href="#" class="ccms remove">V</a>
</div></h2>';
$markup .= '<div class="content">';
$fields = array();
$fields['name']         = array( 'type' => 'text',      'title' => t( 'themes_menu_form_title', 'Title' ), 'class' => 'name', 'default' => t( 'themes_menu_form_stores_link', 'Stores' ) );
$fields['open_type']    = array( 'type' => 'select',    'title' => t( 'themes_menu_form_open_type', 'Open Type' ), 'options' => array( '_self' => t( 'themes_menu_form_self_location', 'Self / Same window' ), '_blank' => t( 'themes_menu_form_blank_location', 'Blank / New window' ) ) );
$fields['class']        = array( 'type' => 'text',      'title' => t( 'themes_menu_form_extra_class', 'Extra Class (CSS)' ) );
$fields['type']         = array( 'type' => 'hidden',    'default' => 'stores' );
$markup .= build_form( 'links{id}', value_with_filter( array( 'modify_menu_fields', 'modify_menu_fields_stores' ), $fields, false ) )['markup'];
$markup .= '</div></div>';
$markup .= '<ul class="arrange-menu"></ul>';
$markup .= '</li>';
$markup .= '</ul>';

$link_options['stores'] = $markup;

// Categories link
$markup = '<ul class="arrange-menu menu-options">';
$markup .= '<li><div class="head"><h2 class="move"><span>' . t( 'themes_menu_form_categories_title', 'Categories' ) . '</span>
<div class="options">
    <a href="#" class="ccms hide">w</a>
    <a href="#" class="ccms view">S</a>
    <a href="#" class="ccms remove">V</a>
</div></h2>';
$markup .= '<div class="content">';
$fields = array();
$fields['name']         = array( 'type' => 'text',      'title' => t( 'themes_menu_form_title', 'Title' ), 'class' => 'name', 'default' => t( 'themes_menu_form_categories_link', 'Categories' ) );
$fields['class']        = array( 'type' => 'text',      'title' => t( 'themes_menu_form_extra_class', 'Extra Class (CSS)' ) );
$fields['type']         = array( 'type' => 'hidden',    'default' => 'categories' );
$markup .= build_form( 'links{id}', value_with_filter( array( 'modify_menu_fields', 'modify_menu_fields_categories' ), $fields, false ) )['markup'];
$markup .= '</div></div>';
$markup .= '<ul class="arrange-menu"></ul>';
$markup .= '</li>';
$markup .= '</ul>';

$link_options['categories'] = $markup;

// Search link
$markup = '<ul class="arrange-menu menu-options">';
$markup .= '<li><div class="head"><h2 class="move"><span>' . t( 'themes_menu_form_search_title', 'Search' ) . '</span>
<div class="options">
    <a href="#" class="ccms hide">w</a>
    <a href="#" class="ccms view">S</a>
    <a href="#" class="ccms remove">V</a>
</div></h2>';
$markup .= '<div class="content">';
$fields = array();
$fields['name']         = array( 'type' => 'text',      'title' => t( 'themes_menu_form_title', 'Title' ), 'class' => 'name', 'default' => t( 'themes_menu_form_search_link', 'Search' ) );
$fields['url']          = array( 'type' => 'text',      'title' => t( 'themes_menu_form_url', 'URL' ), 'default' => '#search' );
$fields['open_type']    = array( 'type' => 'select',    'title' => t( 'themes_menu_form_open_type', 'Open Type' ), 'options' => array( '_self' => t( 'themes_menu_form_self_location', 'Self / Same window' ), '_blank' => t( 'themes_menu_form_blank_location', 'Blank / New window' ) ) );
$fields['class']        = array( 'type' => 'text',      'title' => t( 'themes_menu_form_extra_class', 'Extra Class (CSS)' ) );   
$fields['type']         = array( 'type' => 'hidden',    'default' => 'search' );
$markup .= build_form( 'links{id}', value_with_filter( array( 'modify_menu_fields', 'modify_menu_fields_search' ), $fields, false ) )['markup'];
$markup .= '</div></div>';
$markup .= '<ul class="arrange-menu"></ul>';
$markup .= '</li>';
$markup .= '</ul>';

$link_options['search'] = $markup;

echo implode( "\n", value_with_filter( 'menu_link_options', $link_options ) );

echo '</div>';

echo '</div>';

} else echo '<div class="a-alert">' . t( 'no_menus_yet', "This theme does not use a menu." ) . '</div>';

break;

/** LIST OF THEMES */

default:

echo '<div class="title">

<h2>' . t( 'themes_title', "Themes" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=themes.php&amp;action=upload" class="btn">' . t( 'themes_upload', "Upload Theme" ) . '</a>
</div>';

$subtitle = t( 'themes_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_themes_page', 'after_title_list_themes_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'themes_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_theme( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['action'] ) ) {

    if( isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'themes_csrf' ) && $_GET['action'] == 'delete' ) {

        if( isset( $_GET['id'] ) )
        if( admin\actions::delete_theme( $_GET['id'] ) )
        echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
        else
        echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

    } else if( $_GET['action'] == 'activate' ) {

        if( isset( $_SESSION['js_settings'] ) ) {
            if( isset( $_GET['success'] ) && $_GET['success'] == 'true' ) {
                echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
                unset( $_SESSION['js_settings'] );
                do_action( array( 'admin_theme_switched', 'admin_theme_activated' ), $_GET['id'] );
            } else {
                echo '<div class="a-error">' . t( 'msg_invalid_theme', "Sorry, but this theme seems to be invalid." ) . '</div>';
                unset( $_SESSION['js_settings'] );
            }
        }

    }

}

$csrf = $_SESSION['themes_csrf'] = \site\utils::str_random(10);

$themes = admin\template::read_dirs();
$current = \query\main::get_option( 'theme' );

if( count( $themes ) > 0 ) {

echo '<div class="form-table">

<form action="?route=themes.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>

<div class="bulk_options">
    <button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button>
</div>';

$per_page   = 5;
$page       = ( !empty( $_GET['page'] ) ? $_GET['page'] : 1 );
$pages      = ceil( count( $themes['dirs'] ) / $per_page );

if( $page > $pages ) {
    $page = $pages;
}

$start = ( $page - 1 ) * $per_page;

foreach( array_slice( $themes['dirs'], $start, $per_page ) as $item ) {

    $links = array();

    if( $current == $item ) {
        $links['editor'] = '<a href="?route=themes.php&amp;action=editor&amp;id=' . $item . '">' . t( 'editor', "Editor" ) . '</a>';
    } else {
        $links['activate'] = '<a href="?route=post-actions.php&amp;action=switch-theme&amp;id=' . $item . '&amp;token=' . $csrf . '">' . t( 'activate', "Activate" ) . '</a>';
        $links['editor'] = '<a href="?route=themes.php&amp;action=editor&amp;id=' . $item . '">' . t( 'editor', "Editor" ) . '</a>';
        $links['delete'] = '<a href="?route=themes.php&amp;action=delete&amp;id=' . $item . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';
    }

    echo get_list_type( 'theme', array( 'item' => $item,  'current_theme' => $current ), $links );

}

echo '</ul>

<input type="hidden" name="csrf" value="' . $csrf . '" />

</form>

</div>';

if( $pages > 1 ) {
echo '<div class="pagination">';
if( $page > 1 )echo '<a href="?route=themes.php&amp;action=list&amp;page=' . ($page-1) . '" class="btn">' . t( 'prev_page', "&larr; Prev" ) . '</a>';
if( $pages > $page )echo '<a href="?route=themes.php&amp;action=list&amp;page=' . ($page+1) . '" class="btn">' . t( 'next_page', "Next &rarr;" ) . '</a>';
echo '</div>';
}

} else echo '<div class="a-alert">' . t( 'no_themes_yet', "No themes yet." ) . '</div>';

break;

}