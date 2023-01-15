<?php

switch( $_GET['action'] ) {

/** EXPORT PRODUCTS */

case 'export':

if( !ab_to( array( 'products' => 'export' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'products_export_title', "Export Products" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=products.php&amp;action=list" class="btn">' . t( 'products_view', "View Products" ) . '</a>
</div>';

$subtitle = t( 'products_export_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_product_page', 'after_title_export_products_page' ) );

$csrf = $_SESSION['products_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">';

echo '<form action="?download=export_products_csv.php" method="POST">

<div class="row"><span>' . t( 'form_category', "Category" ) . ':</span>
<div><select name="category">
<option value="0">' . t( 'stores_option_all', "All" ) . '</option>';
foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
    echo '<optgroup label="' . ts( $cat['info']->name ) . '">';
    echo '<option value="' . $cat['info']->ID . '">' . ts( $cat['info']->name ) . '</option>';
    if( isset( $cat['subcats'] ) ) {
        foreach( $cat['subcats'] as $subcat ) {
            echo '<option value="' . $subcat->ID . '">' . ts( $subcat->name ) . '</option>';
        }
    }
    echo '</optgroup>';
}
echo '</select></div></div>

<div class="row"><span>' . t( 'form_datefrom', "Date from" ) . ':</span><div><input type="date" name="date[from]" value="2000-01-01" class="datepicker" /></div></div>
<div class="row"><span>' . t( 'from_dateto', "Date to" ) . ':</span><div><input type="date" name="date[to]" value="' . date( 'Y-m-d', strtotime( 'tomorrow' ) ) . '" class="datepicker" /></div></div>
<div class="row"><span>' . t( 'subscribers_form_exportfields', "Export the Fields" ) . ':</span><div>
<input type="checkbox" name="fields[name]" id="name" checked disabled /> <label for="name"><span></span> ' . t( 'field_title', "Title" ) . '</label>
<input type="checkbox" name="fields[link]" id="link" checked disabled /> <label for="link"><span></span> ' . t( 'field_link', "Link/Website" ) . '</label>
<input type="checkbox" name="fields[description]" id="description" checked disabled /> <label for="description"><span></span> ' . t( 'field_description', "Description" ) . '</label>
<input type="checkbox" name="fields[tags]" id="tags" checked disabled /> <label for="tags"><span></span> ' . t( 'field_tags', "Tags" ) . '</label>
<input type="checkbox" name="fields[image]" id="image" checked disabled /> <label for="image"><span></span> ' . t( 'field_image', "Image" ) . '</label>
<input type="checkbox" name="fields[price]" id="price" checked disabled /> <label for="price"><span></span> ' . t( 'field_price', "Price" ) . '</label>
<input type="checkbox" name="fields[old_price]" id="old_price" checked disabled /> <label for="old_price"><span></span> ' . t( 'field_oldprice', "Old Price" ) . '</label>
<input type="checkbox" name="fields[currency]" id="currency" checked disabled /> <label for="currency"><span></span> ' . t( 'field_currency', "Currency" ) . '</label>
<input type="checkbox" name="fields[start_date]" id="start_date" checked disabled /> <label for="start_date"><span></span> ' . t( 'field_start_date', "Start Date" ) . '</label>
<input type="checkbox" name="fields[end_date]" id="end_date" checked disabled /> <label for="end_date"><span></span> ' . t( 'field_end_date', "End Date" ) . '</label>
<input type="checkbox" name="fields[store_url]" id="store_url" checked disabled /> <label for="store_url"><span></span> ' . t( 'field_store_url', "Store Website" ) . '</label>
<input type="checkbox" name="fields[url]" id="url" checked /> <label for="url"><span></span> ' . t( 'field_url', "URL" ) . '</label>
<input type="checkbox" name="fields[store_name]" id="store_name" /> <label for="store_name"><span></span> ' . t( 'field_store_name', "Store Name" ) . '</label>
</div></div>
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

/** IMPORT PRODUCTS */

case 'import':

if( !ab_to( array( 'products' => 'import' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'products_import_title', "Import Products" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=products.php&amp;action=list" class="btn">' . t( 'products_view', "View Products" ) . '</a>
</div>';

$subtitle = t( 'products_import_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_product_page', 'after_title_import_products_page' ) );

echo '<div id="upload-theme-form">';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'products_csrf' ) ) {

    if( isset( $_POST['category'] ) && isset( $_FILES['file'] ) )
    if( $import = admin\actions::import_products(
    array(
    'category'  => $_POST['category'],
    'file'      => $_FILES['file'],
    'omit_first_line' => ( isset( $_POST['omitfirst'] ) ? 1 : 0 ),
    'fields'    => $_POST['field']
    ) ) )

    echo '<div class="a-success">' . sprintf( t( 'msg_productsimported', "%s products imported, %s errors." ), $import[0], $import[1] ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else {

    echo '<div class="a-alert">' . t( 'msg_import_products', "To import products correctly, fields from the CSV file should be exactly in this order: Title*, Link(URL), Description, Tags, Image, Price, Old Price, Currency, Start Date, Expiration Date, Store Link/Website*." ) . '</div>';
    echo '<div class="a-message">' . t( 'msg_import_products_n', "Note*: You are able to import products only for existing stores, the products are imported to stores with same domain name. Practically, stores must be imported before products." ) . '</div>';

}

$csrf = $_SESSION['products_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">

<div class="row"><span>' . t( 'form_in_category', "In Category" ) . ':</span>
<div><select name="category">
<option value="0">' . t( 'option_no_category', "No category" ) . '</option>';
foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
    echo '<optgroup label="' . ts( $cat['info']->name ) . '">';
    echo '<option value="' . $cat['info']->ID . '">' . ts( $cat['info']->name ) . '</option>';
    if( isset( $cat['subcats'] ) ) {
        foreach( $cat['subcats'] as $subcat ) {
            echo '<option value="' . $subcat->ID . '">' . ts( $subcat->name ) . '</option>';
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
<input type="hidden" name="field[5]" value="price" />
<input type="hidden" name="field[6]" value="old_price" />
<input type="hidden" name="field[7]" value="currency" />
<input type="hidden" name="field[8]" value="start" />
<input type="hidden" name="field[9]" value="expiration" />
<input type="hidden" name="field[10]" value="store_url" />
<input type="hidden" name="field[11]" value="store_name" />
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
    <a href="?route=products.php&action=import_advanced" class="btn">' . t( 'data_import_advanced', "Data Import Advanced" ) . '</a>
</div>

</div>';

echo '<div id="process-theme">
    <h2>' . t( 'msg_upload_dleave', "Please do not leave this page during the import!" ) . '</h2>
</div>';

break;

/** IMPORT PRODUCTS - ADVANCED */

case 'import_advanced':

if( !ab_to( array( 'products' => 'import' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'products_import_title', "Import Products" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=products.php&amp;action=list" class="btn">' . t( 'products_view', "View Products" ) . '</a>
</div>';

$subtitle = t( 'products_import_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_product_page', 'after_title_advanced_import_products_page' ) );

if( !isset( $_POST['ready'] ) || !isset( $_POST['fields'] ) || (int) $_POST['fields'] < 2 ) {
    echo '<div class="form-table">

<form action="#" method="POST">';

if( !empty( $_GET['store'] ) && \query\main::store_exists( $_GET['store'] ) ) {
    echo '<div class="row"><span>' . t( 'form_store_id', "Store ID" ) . ':</span><div data-search="store"><input type="text" name="store" value="' . (int) $_GET['store'] . '" required /><a href="#" class="downarr"></a>';
    $store_info = \query\main::store_info( $_GET['store'] );
    echo '<span class="idinfo">' . $store_info->name . ' (ID: ' . $store_info->ID . ')</span>';
    echo '</div></div>';
}

echo '<div class="row"><span>' . t( 'form_fields', "Fields" ) . ':</span><div><input type="number" name="fields" value="" min="2" max="60" required /></div></div>';

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

$storeID = 0;

if( !empty( $_GET['store'] ) && \query\main::store_exists( $_GET['store'] ) ) {
    $storeID = (int) $_GET['store'];
}

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'stores_csrf' ) ) {

    if( isset( $_POST['category'] ) && isset( $_FILES['file'] ) && isset( $_POST['field'] ) && isset( $_POST['defined_end_date'] ) )
    if( $import = admin\actions::import_products(
    array(
    'store'     => $storeID,
    'category'  => $_POST['category'],
    'file'      => $_FILES['file'],
    'omit_first_line' => ( isset( $_POST['omitfirst'] ) ? 1 : 0 ),
    'fields'    => $_POST['field'],
    'def_ed'    => ( isset( $_POST['d_end_date'] ) ? 1 : 0 ),
    'end_date'  => implode( ' ', $_POST['defined_end_date'] )
    ) ) )

    echo '<div class="a-success">' . sprintf( t( 'msg_productsimported', "%s products imported, %s errors." ), $import[0], $import[1] ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else {

    echo '<div class="a-message">' . t( 'msg_import_products_n', "Note*: You are able to import products only for existing stores, the products are imported to stores with same domain name. Practically, stores must be imported before products." ) . '</div>';

}

$csrf = $_SESSION['stores_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">';

if( !empty( $storeID ) ) {
    echo '<div class="row"><span>' . t( 'form_store_id', "Store ID" ) . ':</span><div data-search="store"><input type="text" name="store" value="' . $storeID . '" disabled />';
    $store_info = \query\main::store_info( $storeID );
    echo '<span class="idinfo">' . $store_info->name . ' (ID: ' . $store_info->ID . ')</span>';
    echo '</div></div>';
}

echo '<div class="row"><span>' . t( 'form_in_category', "In Category" ) . ':</span>
<div><select name="category">
<option value="0">' . t( 'option_no_category', "No category" ) . '</option>';
foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
    echo '<optgroup label="' . ts( $cat['info']->name ) . '">';
    echo '<option value="' . $cat['info']->ID . '">' . ts( $cat['info']->name ) . '</option>';
    if( isset( $cat['subcats'] ) ) {
        foreach( $cat['subcats'] as $subcat ) {
            echo '<option value="' . $subcat->ID . '">' . ts( $subcat->name ) . '</option>';
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
    <option value="name">' . t( 'field_title', "Title" ) . '*</option>
    <option value="link">' . t( 'field_link', "Link/Website" ) . '</option>
    <option value="description">' . t( 'field_description', "Description" ) . '</option>
    <option value="tags">' . t( 'field_tags', "Tags" ) . '</option>
    <option value="image">' . t( 'field_image', "Image" ) . '</option>
    <option value="price">' . t( 'field_price', "Price" ) . '</option>
    <option value="old_price">' . t( 'field_oldprice', "Old Price" ) . '</option>
    <option value="currency">' . t( 'field_currency', "Currency" ) . '</option>
    <option value="start">' . t( 'field_start_date', "Start Date" ) . '</option>
    <option value="expiration">' . t( 'field_end_date', "End Date" ) . '</option>';
    if( empty( $storeID ) ) {
        echo '<option value="store_url">' . t( 'field_store_url', "Store Website" ) . '*</option>
        <option value="store_name">' . t( 'field_store_name', "Store Name" ) . '</option>';
    }
    echo '</select></div></div>';

}

echo '<div id="modify_mt">

<div class="title">
    <h2>' . t( 'import_more_options_title', "More Options for Import" ) . '</h2>
</div>

<div class="row"><span>' . t( 'import_form_def_end_date', "Define End Date" ) . ' <span class="info"><span>' . t( 'import_form_def_iend_date', 'In case that expiration date field is mising, "End Date" can be defined by default.' ) . '</span></span>:</span><div><input type="checkbox" name="d_end_date" id="d_end_date" value="1" /> <label for="d_end_date"><span></span> ' . t( 'import_def_products_end_date', "Define end date for products" ) . '</label></div></div>
<div class="row" data-required=\'' . json_encode( array( 'd_end_date' => 1 ) ) . '\'"><span>' . t( 'form_end_date', "End Date" ) . ':</span><div><input type="date" name="defined_end_date[date]" value="' . date( 'Y-m-d', strtotime( '+6 months' ) ). '" class="datepicker" style="display:inline-block;width:68%;margin-right:2%" /><input type="time" name="defined_end_date[hour]" value="00:00" class="hourpicker" style="display:inline-block;width:30%" /></div></div>
</div>

<input type="hidden" name="ready" value="1" />
<input type="hidden" name="fields" value="' . (int) $_POST['fields'] . '" />
<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'button_import', "Import" ) . '</button>
    </div>
    <div>
        <a href="#" class="btn" id="modify_mt_but">' . t( 'button_more', "More" ) . '</a>
    </div>
</div>

</form>

</div></div>';

echo '<div id="process-theme">
    <h2>' . t( 'msg_upload_dleave', "Please do not leave this page during the import!" ) . '</h2>
</div>';

}

break;

/** ADD PRODUCT */

case 'add':

if( !ab_to( array( 'products' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'products_add_title', "Add New Product" ) . '</h2>

<div style="float:right;margin:0 2px 0 0;">

<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>
<li><a href="#" class="more_fields">' . t( 'more', "More" ) . '</a></li>
</ul>
</div>

<a href="?route=products.php&amp;action=list" class="btn">' . t( 'products_view', "View Products" ) . '</a>
</div>';

$subtitle = t( 'products_add_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_product_page', 'after_title_add_product_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'products_csrf' ) ) {

    if( ( $new_product_id = admin\actions::add_product(
    value_with_filter( 'save_product_values', array(
    'store'         => ( isset( $_POST['store'] ) ? $_POST['store'] : 0 ),
    'category'      => ( isset( $_POST['category'] ) ? $_POST['category'] : 0 ),
    'popular'       => ( isset( $_POST['popular'] ) ? 1 : 0 ),
    'name'          => ( isset( $_POST['name'] ) ? $_POST['name'] : '' ),
    'price'         => ( isset( $_POST['price'] ) ? $_POST['price'] : '' ),
    'old_price'     => ( isset( $_POST['old_price'] ) ? $_POST['old_price'] : '' ),
    'currency'      => ( isset( $_POST['currency'] ) ? $_POST['currency'] : '' ),
    'link'          => ( !isset( $_POST['product_ownlink'] ) && isset( $_POST['link'] ) && filter_var( $_POST['link'], FILTER_VALIDATE_URL ) ? $_POST['link'] : '' ),
    'description'   => ( isset( $_POST['description'] ) ? $_POST['description'] : '' ),
    'tags'          => ( isset( $_POST['tags'] ) ? $_POST['tags'] : '' ),
    'cashback'      => ( isset( $_POST['reward_points'] ) ? (int) $_POST['reward_points'] : 0 ),
    'start'         => ( isset( $_POST['start'] ) ? date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['start'] ) ) ) : date( 'Y-m-d H:i:s', time() ) ),
    'end'           => ( isset( $_POST['end'] ) ? date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['end'] ) ) ) : date( 'Y-m-d H:i:s', time() ) ),
    'publish'       => ( isset( $_POST['publish'] ) ? 1 : 0 ),
    'image'         => ( isset( $_FILES['image'] ) ? $_FILES['image'] : array() ),
    'meta_title'    => ( isset( $_POST['meta_title'] ) ? $_POST['meta_title'] : '' ),
    'meta_keywords' => ( isset( $_POST['meta_keywords'] ) ? $_POST['meta_keywords'] : '' ),
    'meta_desc'     => ( isset( $_POST['meta_desc'] ) ? $_POST['meta_desc'] : '' ),
    'extra'         => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() )
    ) ) ) ) ) {

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';

    do_action( array( 'admin_product_added_edited', 'admin_product_added' ), $new_product_id );

    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf   = $_SESSION['products_csrf'] = \site\utils::str_random(10);

$main   = $GLOBALS['admin_main_class']->product_fields( (object) array(), $csrf );
$fields = $main['fields'];

admin\widgets::get_page_tabs( $main );

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off" class="product-form">';

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

do_action( array( 'admin_product_after_form_add_edit', 'admin_product_after_form_add' ) );

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
        <button class="btn btn-important">' . t( 'products_add_button', "Add Product" ) . '</button>
    </div>
    <div>
        <a href="#" class="btn" id="modify_mt_but">' . t( 'pages_editmt_button', "Meta Tags" ) . '</a>
    </div>
</div>

</form>

</div>';

break;

/** EDIT PRODUCT */

case 'edit':

if( !ab_to( array( 'products' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'products_edit_title', "Edit Product" ) . '</h2>

<div style="float: right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $item_exists = \query\main::product_exists( $_GET['id'] ) ) ) {

$info = \query\main::product_info( $_GET['id'], array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( $info->cashback === 0 ) echo '<li><a href="#" class="more_fields">' . t( 'more', "More" ) . '</a></li>';
if( ab_to( array( 'stores' => 'delete' ) ) ) echo '<li><a href="?route=products.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>';
if( $info->visible ) {
    echo '<li><a href="?route=products.php&amp;action=list&amp;type=unpublish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'unpublish', "Unpublish" ) . '</a></li>';
} else {
    echo '<li><a href="?route=products.php&amp;action=list&amp;type=publish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'publish', "Publish" ) . '</a></li>';
}
echo '</ul>
</div>';

}

echo '<a href="?route=products.php&amp;action=list" class="btn">' . t( 'products_view', "View Products" ) . '</a>

</div>';

$subtitle = t( 'products_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $item_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_product_page', 'after_title_edit_product_page' ), $info->ID );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'products_csrf' )) {

if( isset( $_POST['change_url_title'] ) ) {

    if( isset( $_POST['url_title'] ) )
    if( admin\actions::edit_product_url( $_GET['id'], array( 'title' => $_POST['url_title'] ) ) ) {

    $info = \query\main::product_info( $_GET['id'], array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else {

    if( admin\actions::edit_product( $_GET['id'],
    value_with_filter( 'save_product_values', array(
    'store'         => ( isset( $_POST['store'] ) ? $_POST['store'] : 0 ),
    'category'      => ( isset( $_POST['category'] ) ? $_POST['category'] : 0 ),
    'popular'       => ( isset( $_POST['popular'] ) ? 1 : 0 ),
    'name'          => ( isset( $_POST['name'] ) ? $_POST['name'] : '' ),
    'price'         => ( isset( $_POST['price'] ) ? $_POST['price'] : '' ),
    'old_price'     => ( isset( $_POST['old_price'] ) ? $_POST['old_price'] : '' ),
    'currency'      => ( isset( $_POST['currency'] ) ? $_POST['currency'] : '' ),
    'link'          => ( !isset( $_POST['product_ownlink'] ) && isset( $_POST['link'] ) && filter_var( $_POST['link'], FILTER_VALIDATE_URL ) ? $_POST['link'] : '' ),
    'description'   => ( isset( $_POST['description'] ) ? $_POST['description'] : '' ),
    'tags'          => ( isset( $_POST['tags'] ) ? $_POST['tags'] : '' ),
    'cashback'      => ( isset( $_POST['reward_points'] ) ? (int) $_POST['reward_points'] : 0 ),
    'start'         => ( isset( $_POST['start'] ) ? implode( ' ', $_POST['start'] ) : '' ),
    'end'           => ( isset( $_POST['end'] ) ? implode( ' ', $_POST['end'] ) : '' ),
    'publish'       => ( isset( $_POST['publish'] ) ? 1 : 0 ),
    'image'         => ( isset( $_FILES['image'] ) ? $_FILES['image'] : array() ),
    'meta_title'    => ( isset( $_POST['meta_title'] ) ? $_POST['meta_title'] : '' ),
    'meta_keywords' => ( isset( $_POST['meta_keywords'] ) ? $_POST['meta_keywords'] : '' ),
    'meta_desc'     => ( isset( $_POST['meta_desc'] ) ? $_POST['meta_desc'] : '' ),
    'extra'         => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() )
    ) ) ) ) {

    $info = \query\main::product_info( $_GET['id'], array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';

    do_action( array( 'admin_product_added_edited', 'admin_product_edited' ), $info->ID );

    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'products_csrf' ) ) {

if( $_GET['type'] == 'delete_image' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_product_image( $_GET['id'] ) ) {
    $info->image = '';
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$_SESSION['products_csrf'] = $csrf;

$main   = $GLOBALS['admin_main_class']->product_fields( $info, $csrf );
$fields = $main['fields'];

admin\widgets::get_page_tabs( $main );

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off" class="product-form">';

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

do_action( array( 'admin_product_after_form_add_edit', 'admin_product_after_form_edit' ), $info );

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
        <button class="btn btn-important">' . t( 'products_edit_button', "Edit Product" ) . '</button>
    </div>
    <div>
        <a href="#" class="btn" id="modify_mt_but">' . t( 'pages_editmt_button', "Meta Tags" ) . '</a>
    </div>
</div>

</form>

</div>';

echo '<div class="title" style="margin-top:40px;">

<h2>' . t( 'products_info_title', "Information About This Product" ) . '</h2>

</div>';

echo '<div class="info-table" id="info-table" style="padding-bottom: 20px;">

<form action="?route=products.php&amp;action=edit&amp;id=' . $info->ID . '#info-table" method="POST" autocomplete="off">';

$stat_rows              = array();
$stat_rows['id']        = '<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>';
$stat_rows['views']     = '<div class="row"><span>' . t( 'views', "Views" ) . ':</span> <div>' . $info->views . '</div></div>';
$stat_rows['url']       = '<div class="row"><span>' . t( 'page_url', "Page URL" ) . ':</span> <div class="modify_url">
<div' . ( isset( $_GET['editurl'] ) ? ' style="display: none;"' : '' ) . '><a href="' . $info->link . '" target="_blank">' . $info->link . '</a> / <a href="?route=products.php&amp;action=edit&amp;id=' . $info->ID . '&amp;editurl#info-table">' . t( 'edit', "Edit" ) . '</a></div>
<div' . ( !isset( $_GET['editurl'] ) ? ' style="display: none;"' : '' ) . '>
<input type="text" name="url_title" value="' . $info->url_title . '" placeholder="' . $info->title . '" style="display: block; width: 100%; box-sizing: border-box;" />
<input type="hidden" name="csrf" value="' . $csrf . '" />
<button name="change_url_title" class="btn save">' . t( 'save', "Save" ) . '</button> <a href="?route=products.php&amp;action=edit&amp;id=' . $info->ID . '#info-table" class="btn close">' . t( 'cancel', "Cancel" ) . '</a>
</div>
</div></div>';
$stat_rows['store']     = '<div class="row"><span>' . t( 'store_name', "Store name" ) . ':</span> <div>' . ( empty( $info->store_name ) ? '-' : ( ab_to( array( 'stores' => 'edit' ) ) ? '<a href="?route=stores.php&amp;action=edit&amp;id=' . $info->storeID . '">' . $info->store_name . '</a>' : $info->store_name ) ) . '</div></div>';
$stat_rows['lu_by']     = '<div class="row"><span>' . t( 'last_update_by', "Last update by" )    . ':</span> <div>' . ( empty( $info->lastupdate_by_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->lastupdate_by . '">' . $info->lastupdate_by_name . '</a>' : $info->lastupdate_by_name ) ) . '</div></div>';
$stat_rows['lu_date']   = '<div class="row"><span>' . t( 'last_update_on', "Last update on" )    . ':</span> <div>' . $info->last_update . '</div></div>';
$stat_rows['added_by']  = '<div class="row"><span>' . t( 'added_by', "Added by" ) . ':</span> <div>' . ( empty( $info->user_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->userID . '">' . $info->user_name . '</a>' : $info->user_name ) ) . '</div></div>';
$stat_rows['added_date']= '<div class="row"><span>' . t( 'added_on', "Added on" )    . ':</span> <div>' . $info->date . '</div></div>';

echo implode( '', value_with_filter( 'admin_product_stats', $stat_rows ) );

echo '</form>

</div>';

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/** LIST OF PRODUCTS */

default:

if( !ab_to( array( 'products' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'products_title', "Products" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( ( $ab = ab_to( array( 'products' => array( 'export', 'import' ) ) ) ) && list( $ab_exp, $ab_imp ) = $ab ) {

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( $ab_imp ) echo '<li><a href="?route=products.php&amp;action=import">' . t( 'import', "Import" ) . '</a></li>';
if( $ab_exp ) echo '<li><a href="?route=products.php&amp;action=export">' . t( 'export', "Export" ) . '</a></li>';

echo '</ul>
</div>';

}

if( ab_to( array( 'products' => 'add' ) ) ) echo '<a href="?route=products.php&amp;action=add" class="btn">' . t( 'products_add', "Add Product" ) . '</a>';
echo '</div>';

$subtitle = t( 'products_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_product_page', 'after_title_list_products_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'products_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_product( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

    if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
    if( admin\actions::action_product( $_POST['action'], array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'products_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_product( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::action_product( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['products_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="products.php" />
<input type="hidden" name="action" value="list" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'price' => t( 'order_price', "Price" ), 'price desc' => t( 'order_price_desc', "Price DESC" ), 'discount' => t( 'order_discount', "Discount" ), 'discount desc' => t( 'order_discount_desc', "Discount DESC" ), 'views' => t( 'order_views', "Views" ), 'views desc' => t( 'order_views_desc', "Views DESC" ), 'update' => t( 'order_last_update', "Last update" ), 'update desc' => t( 'order_last_update_desc', "Last update DESC" ), 'active' => t( 'order_expiration', "Expiration date" ), 'active DESC' => t( 'order_expiration_desc', "Expiration date DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

 <select name="category">
<option value="">' . t( 'all_categories', "All categories" ) . '</option>';
foreach( \query\main::group_categories( array( 'max' => 0 ) ) as $cat ) {
    echo '<optgroup label="' . ts( $cat['info']->name ) . '">';
    echo '<option value="' . $cat['info']->ID . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $cat['info']->ID ? ' selected' : '' ) . '>' . ts( $cat['info']->name ) . '</option>';
    if( isset( $cat['subcats'] ) ) {
        foreach( $cat['subcats'] as $subcat ) {
            echo '<option value="' . $subcat->ID . '"' . ( isset( $_GET['category'] ) && $_GET['category'] == $subcat->ID ? ' selected' : '' ) . '>' . ts( $subcat->name ) . '</option>';
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
<input type="hidden" name="route" value="products.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
    echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['category'] ) ) {
    echo '<input type="hidden" name="category" value="' . esc_html( $_GET['category'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'products_search_input', "Search products" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$custom_toolbar = do_action( 'admin_products_list_custom_toolbar' );

if( !empty( $custom_toolbar ) ) {
    echo '<div class="page-toolbar">';
    echo $custom_toolbar;
    echo '</div>';
}

$p = \query\main::have_products( ( $options = value_with_filter( 'admin_view_products_args', array( 'per_page' => 10, 'store' => ( isset( $_GET['store'] ) ? $_GET['store'] : '' ), 'user' => ( isset( $_GET['user'] ) ? $_GET['user'] : '' ), 'categories' => ( isset( $_GET['category'] ) ? $_GET['category'] : '' ), 'show' => ( isset( $_GET['view'] ) ? $_GET['view'] : 'all' ), 'search' => ( isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '' ) ) ) ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
$reset = ( !empty( $_GET['store'] ) || !empty( $_GET['user'] ) || !empty( $_GET['category'] ) || !empty( $_GET['view'] ) || !empty( $_GET['search'] ) );
if( value_with_filter( 'admin_products_list_reset_view', $reset ) ) echo ' / <a href="?route=products.php&amp;action=list">' . t( 'reset_view', "Reset view" ) . '</a>';

echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=products.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

$ab_edt    = ab_to( array( 'products' => 'edit' ) );
$ab_del    = ab_to( array( 'products' => 'delete' ) );

if( $ab_del ) {

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

foreach( \query\main::while_products( array_merge( array( 'page' => $p['page'], 'orderby' => value_with_filter( 'admin_view_products_orderby', ( isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc' ) ) ), $options ), '', array( 'no_emoticons' => true, 'no_filters' => true ) ) as $item ) {

    $links = array();

    if( $ab_edt ) {
        $links['edit'] = '<a href="?route=products.php&amp;action=edit&amp;id=' . $item->ID . '">' . t( 'edit', "Edit" ) . '</a>';
        $links['publish'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( !$item->visible ? 'publish' : 'unpublish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( !$item->visible ? t( 'publish', "Publish" ) : t( 'unpublish', "Unpublish" ) ) . '</a>';
    }
    if( $ab_del ) $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'action' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'product', $item, $links );

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

} else echo '<div class="a-alert">' . t( 'no_products_yet', "No products yet." ) . '</div>';

break;

}