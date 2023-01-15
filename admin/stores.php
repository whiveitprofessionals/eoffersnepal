<?php

switch( $_GET['action'] ) {

/** EXPORT STORES */

case 'export':

if( !ab_to( array( 'stores' => 'export' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'stores_export_title', "Export Stores" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=stores.php&amp;action=list" class="btn">' . t( 'stores_view', "View Stores" ) . '</a>
</div>';

$subtitle = t( 'stores_export_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_store_page', 'after_title_export_stores_page' ) );

$csrf = $_SESSION['stores_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">';

echo '<form action="?download=export_stores_csv.php" method="POST">

<div class="row"><span>' . t( 'form_category', "Category" ) . ':</span>
<div><select name="category">
<option value="0">' . t( 'stores_option_all', "All" ) . '</option>';
foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
    echo '<optgroup label="' . $cat['info']->name . '">';
    echo '<option value="' . $cat['info']->ID . '">' . $cat['info']->name . '</option>';
    if( isset( $cat['subcats'] ) ) {
        foreach( $cat['subcats'] as $subcat ) {
            echo '<option value="' . $subcat->ID . '">' . $subcat->name . '</option>';
        }
    }
    echo '</optgroup>';
}
echo '</select></div></div>

<div class="row"><span>' . t( 'form_datefrom', "Date from" ) . ':</span><div><input type="date" name="date[from]" value="2000-01-01" class="datepicker" /></div></div>
<div class="row"><span>' . t( 'from_dateto', "Date to" ) . ':</span><div><input type="date" name="date[to]" value="' . date( 'Y-m-d', strtotime( 'tomorrow' ) ) . '" class="datepicker" /></div></div>
<div class="row"><span>' . t( 'subscribers_form_exportfields', "Export the Fields" ) . ':</span><div>
<input type="checkbox" name="fields[name]" id="name" checked disabled /> <label for="name"><span></span> ' . t( 'field_name', "Name" ) . '</label>
<input type="checkbox" name="fields[link]" id="link" checked disabled /> <label for="link"><span></span> ' . t( 'field_link', "Link/Website" ) . '</label>
<input type="checkbox" name="fields[description]" id="description" checked disabled /> <label for="description"><span></span> ' . t( 'field_description', "Description" ) . '</label>
<input type="checkbox" name="fields[tags]" id="tags" checked disabled /> <label for="tags"><span></span> ' . t( 'field_tags', "Tags" ) . '</label>
<input type="checkbox" name="fields[image]" id="image" checked disabled /> <label for="image"><span></span> ' . t( 'field_image', "Image" ) . '</label>
<input type="checkbox" name="fields[url]" id="url" checked /> <label for="url"><span></span> ' . t( 'field_url', "URL" ) . '</label>
<input type="checkbox" name="fields[type]" id="type" checked /> <label for="type"><span></span> ' . t( 'field_store_type', "Store Type" ) . '</label>
<input type="checkbox" name="fields[sell_online]" id="sell_online" checked /> <label for="sell_online"><span></span> ' . t( 'field_store_sellonline', "Store Sell Online" ) . '</label>
<input type="checkbox" name="fields[hours]" id="hours" checked /> <label for="hours"><span></span> ' . t( 'field_store_hours', "Store Hours" ) . '</label>
<input type="checkbox" name="fields[locations]" id="locations" checked /> <label for="locations"><span></span> ' . t( 'field_store_locations', "Store Locations" ) . '</label>
<input type="checkbox" name="fields[phone]" id="phone" checked /> <label for="phone"><span></span> ' . t( 'phone_no', "Phone Number" ) . '</label></div></div>
<input type="hidden" name="csrf" value="' . $csrf . '" />
<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'subscribers_export_button', "Export" ) . '</button>
    </div>
    <div></div>
</div>
</form>';

echo '</div>';

break;

/** IMPORT STORES */

case 'import':

if( !ab_to( array( 'stores' => 'import' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'stores_import_title', "Import Stores" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=stores.php&amp;action=list" class="btn">' . t( 'stores_view', "View Stores" ) . '</a>
</div>';

$subtitle = t( 'stores_import_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_store_page', 'after_title_import_stores_page' ) );

echo '<div id="upload-theme-form">';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'stores_csrf' ) ) {

    if( isset( $_POST['category'] ) && isset( $_FILES['file'] ) && isset( $_POST['field'] ) )
    if( $import = admin\actions::import_stores(
    array(
    'category' => $_POST['category'],
    'file' => $_FILES['file'],
    'omit_first_line' => ( isset( $_POST['omitfirst'] ) ? 1 : 0 ),
    'fields' => $_POST['field']
    ) ) )

    echo '<div class="a-success">' . sprintf( t( 'msg_storesimported', "%s stores imported, %s errors." ), $import[0], $import[1] ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else {

    echo '<div class="a-alert">' . t( 'msg_import_stores', "To import stores correctly, fields from the CSV file should be exactly in this order: Name*, Link/Website*, Description, Tags, Image, Type, Sell Online, Hours, Locations, Phone no." ) . '</div>';

}

$csrf = $_SESSION['stores_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">

<div class="row"><span>' . t( 'form_in_category', "In Category" ) . ':</span>
<div><select name="category">
<option value="0">' . t( 'option_no_category', "No category" ) . '</option>';
foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
    echo '<optgroup label="' . $cat['info']->name . '">';
    echo '<option value="' . $cat['info']->ID . '">' . $cat['info']->name . '</option>';
    if( isset( $cat['subcats'] ) ) {
        foreach( $cat['subcats'] as $subcat ) {
            echo '<option value="' . $subcat->ID . '">' . $subcat->name . '</option>';
        }
    }
    echo '</optgroup>';
}
echo '</select></div></div>

<div class="row"><span>' . t( 'form_csv_file', "CSV File" ) . ':</span><div><input type="file" name="file" value="" /></div></div>
<div class="row"><span></span><div><input type="checkbox" name="omitfirst" id="omitfirst" value="1" checked /> <label for="omitfirst"><span></span> ' . t( 'msg_csvomitfirst', "Omit first line" ) . '</label></div></div>

<input type="hidden" name="field[0]" value="name" />
<input type="hidden" name="field[1]" value="link" />
<input type="hidden" name="field[2]" value="description" />
<input type="hidden" name="field[3]" value="tags" />
<input type="hidden" name="field[4]" value="image" />
<input type="hidden" name="field[5]" value="type" />
<input type="hidden" name="field[6]" value="sellonline" />
<input type="hidden" name="field[7]" value="hours" />
<input type="hidden" name="field[8]" value="locations" />
<input type="hidden" name="field[9]" value="phone" />
<input type="hidden" name="csrf" value="' . $csrf . '" />
<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'button_import', "Import" ) . '</button>
    </div>
    <div></div>
</div>
</form>

</div>

<hr />

<div style="text-align: center;">
    <a href="?route=stores.php&action=import_advanced" class="btn">' . t( 'data_import_advanced', "Data Import Advanced" ) . '</a>
</div>

</div>';

echo '<div id="process-theme">
    <h2>' . t( 'msg_upload_dleave', "Please do not leave this page during the import!" ) . '</h2>
</div>';

break;

/** IMPORT STORES - ADVANCED */

case 'import_advanced':

if( !ab_to( array( 'stores' => 'import' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'stores_import_title', "Import Stores" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=stores.php&amp;action=list" class="btn">' . t( 'stores_view', "View Stores" ) . '</a>
</div>';

$subtitle = t( 'stores_import_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_store_page', 'after_title_advanced_import_stores_page' ) );

if( !isset( $_POST['ready'] ) || !isset( $_POST['fields'] ) || (int) $_POST['fields'] < 2 ) {
    echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . t( 'form_fields', "Fields" ) . ':</span><div><input type="number" name="fields" value="" min="2" max="40" required /></div></div>';

echo '<input type="hidden" name="ready" value="1" />
<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'next_page', "Next &rarr;" ) . '</button>
    </div>
    <div></div>
</div>
</form>

</div></div>';

} else {

echo '<div id="upload-theme-form">';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'stores_csrf' ) ) {

    if( isset( $_POST['category'] ) && isset( $_FILES['file'] ) && isset( $_POST['field'] ) )
    if( $import = admin\actions::import_stores(
    array(
    'category' => $_POST['category'],
    'file' => $_FILES['file'],
    'omit_first_line' => ( isset( $_POST['omitfirst'] ) ? 1 : 0 ),
    'fields' => $_POST['field']
    ) ) )

    echo '<div class="a-success">' . sprintf( t( 'msg_storesimported', "%s stores imported, %s errors." ), $import[0], $import[1] ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf = $_SESSION['stores_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">

<div class="row"><span>' . t( 'form_in_category', "In Category" ) . ':</span>
<div><select name="category">
<option value="0">' . t( 'option_no_category', "No category" ) . '</option>';
foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
    echo '<optgroup label="' . $cat['info']->name . '">';
    echo '<option value="' . $cat['info']->ID . '">' . $cat['info']->name . '</option>';
    if( isset( $cat['subcats'] ) ) {
        foreach( $cat['subcats'] as $subcat ) {
            echo '<option value="' . $subcat->ID . '">' . $subcat->name . '</option>';
        }
    }
    echo '</optgroup>';
}
echo '</select></div></div>

<div class="row"><span>' . t( 'form_csv_file', "CSV File" ) . ':</span><div><input type="file" name="file" value="" /></div></div>
<div class="row"><span></span><div><input type="checkbox" name="omitfirst" id="omitfirst" value="1" checked /> <label for="omitfirst"><span></span> ' . t( 'msg_csvomitfirst', "Omit first line" ) . '</label></div></div>';

for( $i = 0; $i < (int) $_POST['fields']; $i++ ) {

echo '<div class="row"><span>' . t( 'form_field', "Field" ) . ' #' . ($i+1) . ':</span>
<div><select name="field[' . $i . ']">
<option value="">' . t( 'field_omit', "-- Omit this field --" ) . '</option>
<option value="name">' . t( 'field_name', "Name" ) . '*</option>
<option value="link">' . t( 'field_link', "Link/Website" ) . '*</option>
<option value="description">' . t( 'field_description', "Description" ) . '</option>
<option value="tags">' . t( 'field_tags', "Tags" ) . '</option>
<option value="image">' . t( 'field_image', "Image" ) . '</option>
<option value="type">' . t( 'field_store_type', "Store Type" ) . '</option>
<option value="sellonline">' . t( 'field_store_sellonline', "Store Sell Online" ) . '</option>
<option value="hours">' . t( 'field_store_hours', "Store Hours" ) . '</option>
<option value="locations">' . t( 'field_store_locations', "Store Locations" ) . '</option>
<option value="phone">' . t( 'phone_no', "Phone Number" ) . '</option>';
echo '</select></div></div>';

}

echo '<input type="hidden" name="ready" value="1" />
<input type="hidden" name="fields" value="' . (int) $_POST['fields'] . '" />
<input type="hidden" name="csrf" value="' . $csrf . '" />
<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'button_import', "Import" ) . '</button>
    </div>
    <div></div>
</div>
</form>

</div></div>';

echo '<div id="process-theme">
    <h2>' . t( 'msg_upload_dleave', "Please do not leave this page during the import!" ) . '</h2>
</div>';

}

break;

/** ADD STORE */

case 'add':

if( !ab_to( array( 'stores' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'stores_add_title', "Add New Store" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=stores.php&amp;action=list" class="btn">' . t( 'stores_view', "View Stores" ) . '</a>
</div>';

$subtitle = t( 'stores_add_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_store_page', 'after_title_add_store_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'stores_csrf' ) ) {

    if( ( $new_store_id = admin\actions::add_store(
    value_with_filter( 'save_store_values', array(
    'user'          => ( isset( $_POST['user'] ) ? $_POST['user'] : 0 ),
    'category'      => ( isset( $_POST['category'] ) ? $_POST['category'] : 0 ),
    'popular'       => ( isset( $_POST['popular'] ) ? 1 : 0 ),
    'name'          => ( isset( $_POST['name'] ) ? $_POST['name'] : '' ),
    'type'          => ( isset( $_POST['store_type'] ) ? $_POST['store_type'] : '' ),
    'url'           => ( isset( $_POST['url'] ) ? $_POST['url'] : '' ),
    'description'   => ( isset( $_POST['description'] ) ? $_POST['description'] : '' ),
    'hours'         => ( isset( $_POST['hours-bi'] ) ? array() : ( isset( $_POST['hours'] ) ? $_POST['hours'] : '' ) ),
    'tags'          => ( isset( $_POST['tags'] ) ? $_POST['tags'] : '' ),
    'sellonline'    => ( isset( $_POST['sellonline'] ) ? 1 : 0 ),
    'publish'       => ( isset( $_POST['publish'] ) ? 1 : 0 ),
    'logo'          => ( isset( $_FILES['logo'] ) ? $_FILES['logo'] : array() ),
    'phone'         => ( isset( $_POST['phone'] ) ? $_POST['phone'] : '' ),
    'meta_title'    => ( isset( $_POST['meta_title'] ) ? $_POST['meta_title'] : '' ),
    'meta_keywords' => ( isset( $_POST['meta_keywords'] ) ? $_POST['meta_keywords'] : '' ),
    'meta_desc'     => ( isset( $_POST['meta_desc'] ) ? $_POST['meta_desc'] : '' ),
    'extra'         => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() )
    ) ) ) ) ) {

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';

    do_action( array( 'admin_store_added_edited', 'admin_store_added' ), $new_store_id );

    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf   = $_SESSION['stores_csrf'] = \site\utils::str_random(10);

$main   = $GLOBALS['admin_main_class']->store_fields( array(), $csrf );
$fields = $main['fields'];

admin\widgets::get_page_tabs( $main );

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">';

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

do_action( array( 'admin_store_after_form_add_edit', 'admin_store_after_form_add' ) );

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
        <button class="btn btn-important">' . t( 'stores_add_button', "Add Store" ) . '</button>
    </div>
    <div>
        <a href="#" class="btn" id="modify_mt_but">' . t( 'pages_editmt_button', "Meta Tags" ) . '</a>
    </div>
</div>

</form>

</div>';

break;

/** EDIT STORE */

case 'edit':

if( !ab_to( array( 'stores' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'stores_edit_title', "Edit Store" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $store_exists = \query\main::store_exists( $_GET['id'] ) ) ) {

$info = \query\main::store_info( $_GET['id'], array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( ab_to( array( 'coupons' => 'add' ) ) ) echo '<li><a href="?route=coupons.php&amp;action=add&amp;store=' . $_GET['id'] . '">' . t( 'coupons_add_button', "Add Coupon" ) . '</a></li>';
if( ab_to( array( 'products' => 'add' ) ) ) echo '<li><a href="?route=products.php&amp;action=add&amp;store=' . $_GET['id'] . '">' . t( 'products_add_button', "Add Product" ) . '</a></li>';
if( ab_to( array( 'stores' => 'delete' ) ) ) echo '<li><a href="?route=stores.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_store', "Attention*: With stores will be deleted and their associated coupons ! Are you sure that you want tot delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>';
if( $info->visible ) {
    echo '<li><a href="?route=stores.php&amp;action=list&amp;type=unpublish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'unpublish', "Unpublish" ) . '</a></li>';
} else {
    echo '<li><a href="?route=stores.php&amp;action=list&amp;type=publish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'publish', "Publish" ) . '</a></li>';
}
echo '</ul>
</div>';

}

echo '<a href="?route=stores.php&amp;action=list" class="btn">' . t( 'stores_view', "View Stores" ) . '</a>
</div>';

$subtitle = t( 'stores_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( isset( $store_exists ) && $store_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_store_page', 'after_title_add_store_page' ), $info->ID );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'stores_csrf' ) ) {

if( isset( $_POST['change_url_title'] ) ) {

    if( isset( $_POST['url_title'] ) )
    if( admin\actions::edit_store_url( $_GET['id'],
    array(
    'title' => $_POST['url_title']
    ) ) ) {

    $info = \query\main::store_info( $_GET['id'], array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else {

    if( admin\actions::edit_store( $_GET['id'],
    value_with_filter( 'save_store_values', array(
    'user'          => ( isset( $_POST['user'] ) ? $_POST['user'] : 0 ),
    'category'      => ( isset( $_POST['category'] ) ? $_POST['category'] : 0 ),
    'popular'       => ( isset( $_POST['popular'] ) ? 1 : 0 ),
    'name'          => ( isset( $_POST['name'] ) ? $_POST['name'] : '' ),
    'type'          => ( isset( $_POST['store_type'] ) ? $_POST['store_type'] : '' ),
    'url'           => ( isset( $_POST['url'] ) ? $_POST['url'] : '' ),
    'description'   => ( isset( $_POST['description'] ) ? $_POST['description'] : '' ),
    'hours'         => ( isset( $_POST['hours-bi'] ) ? array() : ( isset( $_POST['hours'] ) ? $_POST['hours'] : '' ) ),
    'tags'          => ( isset( $_POST['tags'] ) ? $_POST['tags'] : '' ),
    'sellonline'    => ( isset( $_POST['sellonline'] ) ? 1 : 0 ),
    'publish'       => ( isset( $_POST['publish'] ) ? 1 : 0 ),
    'logo'          => ( isset( $_FILES['logo'] ) ? $_FILES['logo'] : array() ),
    'phone'         => ( isset( $_POST['phone'] ) ? $_POST['phone'] : '' ),
    'meta_title'    => ( isset( $_POST['meta_title'] ) ? $_POST['meta_title'] : '' ),
    'meta_keywords' => ( isset( $_POST['meta_keywords'] ) ? $_POST['meta_keywords'] : '' ),
    'meta_desc'     => ( isset( $_POST['meta_desc'] ) ? $_POST['meta_desc'] : '' ),
    'extra'         => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() )
    ) ) ) ) {

    $info = \query\main::store_info( $_GET['id'], array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';

    do_action( array( 'admin_store_added_edited', 'admin_store_edited' ), $info->ID );

    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'stores_csrf' ) ) {

if( $_GET['type'] == 'delete_image' ) {

    if( admin\actions::delete_store_image( $_GET['id'] ) ) {
    $info->image = '';
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'delete_location' ) {

    if( isset( $_GET['locID'] ) )
    if( admin\actions::delete_store_location( $_GET['locID'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$_SESSION['stores_csrf'] = $csrf;

$main   = $GLOBALS['admin_main_class']->store_fields( $info, $csrf );
$fields = $main['fields'];

admin\widgets::get_page_tabs( $main );

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">';

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

do_action( array( 'admin_store_after_form_add_edit', 'admin_store_after_form_edit' ), $info );

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
        <button class="btn btn-important">' . t( 'stores_edit_button', "Edit Store" ) . '</button>
    </div>
    <div>
        <a href="#" class="btn" id="modify_mt_but">' . t( 'pages_editmt_button', "Meta Tags" ) . '</a>
    </div>
</div>

</form>

</div>';

echo '<div class="title" style="margin-top:40px;">

<h2>' . t( 'stores_info_title', "Information About This Store" ) . '</h2>

</div>';

echo '<div class="info-table" id="info-table" style="padding-bottom:20px;">

<form action="?route=stores.php&amp;action=edit&amp;id=' . $info->ID . '#info-table" method="POST" autocomplete="off">';

$stat_rows              = array();
$stat_rows['id']        = '<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>';
$stat_rows['views']     = '<div class="row"><span>' . t( 'views', "Views" ) . ':</span> <div>' . $info->views . '</div></div>';
$stat_rows['url']       = '<div class="row"><span>' . t( 'page_url', "Page URL" ) . ':</span> <div class="modify_url">
<div' . ( isset( $_GET['editurl'] ) ? ' style="display: none;"' : '' ) . '><a href="' . $info->link . '" target="_blank">' . $info->link . '</a> / <a href="?route=stores.php&amp;action=edit&amp;id=' . $info->ID . '&amp;editurl#info-table">' . t( 'edit', "Edit" ) . '</a></div>
<div' . ( !isset( $_GET['editurl'] ) ? ' style="display: none;"' : '' ) . '>
<input type="text" name="url_title" value="' . $info->url_title . '" placeholder="' . $info->name . '" style="display: block; width: 100%; box-sizing: border-box;" />
<input type="hidden" name="csrf" value="' . $csrf . '" />
<button name="change_url_title" class="btn save">' . t( 'save', "Save" ) . '</button> <a href="?route=stores.php&amp;action=edit&amp;id=' . $info->ID . '#info-table" class="btn close">' . t( 'cancel', "Cancel" ) . '</a>
</div>
</div></div>';
$stat_rows['owner']     = '<div class="row"><span>' . t( 'owner', "Owner" ) . ':</span> <div>' . ( empty( $info->user_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->userID . '">' . $info->user_name . '</a>' : $info->user_name ) ) . '</div></div>';
$stat_rows['lu_by']     = '<div class="row"><span>' . t( 'last_update_by', "Last update by" ) . ':</span> <div>' . ( empty( $info->lastupdate_by_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->lastupdate_by . '">' . $info->lastupdate_by_name . '</a>' : $info->lastupdate_by_name ) ) . '</div></div>';
$stat_rows['lu_date']   = '<div class="row"><span>' . t( 'last_update_on', "Last update on" ) . ':</span> <div>' . $info->last_update . '</div></div>';
$stat_rows['coupons']   = '<div class="row"><span>' . t( 'coupons', "Coupons" ) . ':</span> <div>' . ( ab_to( array( 'coupons' => 'view' ) ) ? '<a href="?route=coupons.php&amp;action=list&amp;store=' . $info->ID . '">' . $info->coupons . '</a>' : $info->coupons ) . ( ab_to( array( 'coupons' => 'add' ) ) ? ' / <a href="?route=coupons.php&amp;action=add&amp;store=' . $info->ID . '&amp;category=' . $info->catID . '">' . t( 'coupons_add_button', "Add Coupon" ) . '</a>' : '' ) . ( ab_to( array( 'coupons' => 'import' ) ) ? ' / <a href="?route=coupons.php&amp;action=import_advanced&amp;store=' . $info->ID . '">' . t( 'button_import', "Import" ) . '</a>' : '' ) . '</div></div>';
$stat_rows['products']  = '<div class="row"><span>' . t( 'products', "Products" ) . ':</span> <div>' . ( ab_to( array( 'products' => 'view' ) ) ? '<a href="?route=products.php&amp;action=list&amp;store=' . $info->ID . '">' . $info->products . '</a>' : $info->products ) . ( ab_to( array( 'products' => 'add' ) ) ? ' / <a href="?route=products.php&amp;action=add&amp;store=' . $info->ID . '&amp;category=' . $info->catID . '">' . t( 'products_add_button', "Add Product" ) . '</a>' : '' ) . ( ab_to( array( 'products' => 'import' ) ) ? ' / <a href="?route=products.php&amp;action=import_advanced&amp;store=' . $info->ID . '">' . t( 'button_import', "Import" ) . '</a>' : '' ) . '</div></div>';
$stat_rows['reviews']   = '<div class="row"><span>' . t( 'reviews', "Reviews" ) . ':</span> <div>' . ( ab_to( array( 'reviews' => 'view' ) ) ? '<a href="?route=reviews.php&amp;action=list&amp;store=' . $info->ID . '">' . $info->reviews . '</a>' : $info->reviews ) . ( ab_to( array( 'reviews' => 'add' ) ) ? ' / <a href="?route=reviews.php&amp;action=add&amp;store=' . $info->ID . '">' . t( 'reviews_add_button', "Add Review" ) . '</a>' : '' ) . '</div></div>';
$stat_rows['added_date']= '<div class="row"><span>' . t( 'added_on', "Added on" ) . ':</span> <div>' . $info->date . '</div></div>';

echo implode( '', value_with_filter( 'admin_store_stats', $stat_rows ) );

echo '</form>

</div>';

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/** LIST OF STORES */

default:

if( !ab_to( array( 'stores' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'stores_title', "Stores" ) . '</h2>';

echo '<div style="float:right; margin: 0 2px 0 0;">';

if( ( $ab = ab_to( array( 'stores' => array( 'export', 'import' ) ) ) ) && list( $ab_exp, $ab_imp ) = $ab ) {

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( $ab_imp ) echo '<li><a href="?route=stores.php&amp;action=import">' . t( 'import', "Import" ) . '</a></li>';
if( $ab_exp ) echo '<li><a href="?route=stores.php&amp;action=export">' . t( 'export', "Export" ) . '</a></li>';

echo '</ul>
</div>';

}

if( $ab_add = ab_to( array( 'stores' => 'add' ) ) ) echo '<a href="?route=stores.php&amp;action=add" class="btn">' . t( 'stores_add', "Add Store" ) . '</a>';
echo '</div>';

$subtitle = t( 'stores_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_store_page', 'after_title_list_stores_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'stores_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_store( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['change_cat'] ) ) {

    if( isset( $_POST['id'] ) && isset( $_POST['category'] ) )
    if( admin\actions::change_store_category( array_keys( $_POST['id'] ), $_POST['category'] ) )
    echo '<div class="a-success">' . t( 'msg_changed', "Changed!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

    if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
    if( admin\actions::action_store( $_POST['action'], array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'stores_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_store( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::action_store( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['stores_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="stores.php" />
<input type="hidden" name="action" value="list" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'votes' => t( 'order_votes', "Votes" ), 'votes desc' => t( 'order_votes_desc', "Votes DESC" ), 'rating' => t( 'order_rating', "Rating" ), 'rating desc' => t( 'order_rating_desc', "Rating DESC" ), 'views' => t( 'order_views', "Views" ), 'views desc' => t( 'order_views_desc', "Views DESC" ), 'update' => t( 'order_last_update', "Last update" ), 'update desc' => t( 'order_last_update_desc', "Last update DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select> ';

echo '<select name="category">
<option value="">' . t( 'all_categories', "All categories" ) . '</option>';
foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
    echo '<optgroup label="' . $cat['info']->name . '">';
    echo '<option value="' . $cat['info']->ID . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $cat['info']->ID ? ' selected' : '' ) . '>' . $cat['info']->name . '</option>';
    if( isset( $cat['subcats'] ) ) {
        foreach( $cat['subcats'] as $subcat ) {
            echo '<option value="' . $subcat->ID . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $subcat->ID ? ' selected' : '' ) . '>' . $subcat->name . '</option>';
        }
    }
    echo '</optgroup>';
}
echo '</select>';

if( isset( $_GET['search'] ) ) {
    echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>
</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="stores.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
    echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['category'] ) ) {
    echo '<input type="hidden" name="category" value="' . esc_html( $_GET['category'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'stores_search_input', "Search stores" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$custom_toolbar = do_action( 'admin_stores_list_custom_toolbar' );

if( !empty( $custom_toolbar ) ) {
    echo '<div class="page-toolbar">';
    echo $custom_toolbar;
    echo '</div>';
}

$p = \query\main::have_stores( ( $options = value_with_filter( 'admin_view_stores_args', array( 'per_page' => 10, 'user' => ( isset( $_GET['user'] ) ? $_GET['user'] : '' ), 'categories' => ( isset( $_GET['category'] ) ? $_GET['category'] : '' ), 'show' => ( isset( $_GET['view'] ) ? $_GET['view'] : 'all' ), 'search' => ( isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '' ) ) ) ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
$reset = ( !empty( $_GET['user'] ) || !empty( $_GET['category'] ) || !empty( $_GET['view'] ) || !empty( $_GET['search'] ) );
if( value_with_filter( 'admin_stores_list_reset_view', $reset ) ) echo ' / <a href="?route=stores.php&amp;action=list">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=stores.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

$ab_edt    = ab_to( array( 'stores' => 'edit' ) );
$ab_del    = ab_to( array( 'stores' => 'delete' ) );
$feed_view = ab_to( array( 'feed' => 'view' ) );
$ab_add_co = ab_to( array( 'coupons' => 'add' ) );
$ab_add_pr = ab_to( array( 'products' => 'add' ) );

if( $ab_edt || $ab_del ) {
echo '<div class="bulk_options">';

if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . t( 'delete_store', "Attention*: With stores will be deleted and their associated coupons ! Are you sure that you want tot delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button> ';

if( $ab_edt ) {
    echo t( 'action', "Action" ) . ':
    <select name="action">';
    foreach( array( 'publish' => t( 'publish', "Publish" ), 'unpublish' => t( 'unpublish', "Unpublish" ) ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
    echo '</select>
    <button class="btn" name="set_action">' . t( 'set_all', "Set to All" ) . '</button> ';

    echo t( 'category', "Category" ) . ':
    <select name="category">';
    foreach( \query\main::while_categories( array( 'max' => 0 ) ) as $cat )echo '<option value="' . $cat->ID . '">' . $cat->name . '</option>';
    echo '</select>
    <button class="btn" name="change_cat">' . t( 'move_all', "Move All" ) . '</button>';
}

echo '</div>';
}

foreach( \query\main::while_stores( array_merge( array( 'page' => $p['page'], 'orderby' => value_with_filter( 'admin_view_stores_orderby', ( isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc' ) ) ), $options ), '', array( 'no_emoticons' => true, 'no_filters' => true ) ) as $item ) {

    $links = array();

    if( $ab_edt ) {
        $links['edit'] = '<a href="?route=stores.php&amp;action=edit&amp;id=' . $item->ID . '">' . t( 'edit', "Edit" ) . '</a>';
        $links['publish'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( !$item->visible ? 'publish' : 'unpublish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( !$item->visible ? t( 'publish', "Publish" ) : t( 'unpublish', "Unpublish" ) ) . '</a>';
    }
    if( $ab_add_co ) $links['add_coupon']  ='<a href="?route=coupons.php&amp;action=add&amp;store=' . $item->ID . '&amp;category=' . $item->catID . '">' . t( 'coupons_add_button', "Add Coupon" ) . '</a>';
    if( $ab_add_pr ) $links['add_product'] = '<a href="?route=products.php&amp;action=add&amp;store=' . $item->ID . '&amp;category=' . $item->catID . '">' . t( 'products_add_button', "Add Product" ) . '</a>';
    if( $feed_view && $item->feedID !== 0 ) {
        $links['feed_coupons'] = '<a href="?route=feed.php&amp;action=coupons&amp;store=' . $item->feedID . '">' . t( 'feed_coupons_link', "Feed Coupons" ) . '</a>';
        $links['feed_stores'] = '<a href="?route=feed.php&amp;action=products&amp;store=' . $item->feedID . '">' . t( 'feed_products_link', "Feed Products" ) . '</a>';
    }
    if( $ab_del ) $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'action' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_store', "Attention*: With stores will be deleted and their associated coupons ! Are you sure that you want tot delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'store', $item, $links );

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

} else echo '<div class="a-alert">' . t( 'no_stores_yet', "No stores yet." ) . '</div>';

break;

}