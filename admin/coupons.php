<?php

switch( $_GET['action'] ) {

/** EXPORT COUPONS */

case 'export':

if( !ab_to( array( 'coupons' => 'export' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'coupons_export_title', "Export Coupons" ) . '</h2>

<div style="float:right;margin:0 2px 0 0;">
<a href="?route=coupons.php&amp;action=list" class="btn">' . t( 'coupons_view', "View Coupons" ) . '</a>
</div>';

$subtitle = t( 'coupons_export_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_coupon_page', 'after_title_export_coupons_page' ) );

$csrf = $_SESSION['coupons_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">';

echo '<form action="?download=export_coupons_csv.php" method="POST">

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
<input type="checkbox" name="fields[code]" id="code" checked disabled /> <label for="code"><span></span> ' . t( 'field_code', "Code" ) . '</label>
<input type="checkbox" name="fields[start_date]" id="start_date" checked disabled /> <label for="start_date"><span></span> ' . t( 'field_start_date', "Start Date" ) . '</label>
<input type="checkbox" name="fields[end_date]" id="end_date" checked disabled /> <label for="end_date"><span></span> ' . t( 'field_end_date', "End Date" ) . '</label>
<input type="checkbox" name="fields[store_url]" id="store_url" checked disabled /> <label for="store_url"><span></span> ' . t( 'field_store_url', "Store Website" ) . '</label>
<input type="checkbox" name="fields[url]" id="url" checked /> <label for="url"><span></span> ' . t( 'field_url', "URL" ) . '</label>
<input type="checkbox" name="fields[printable]" id="printable" checked /> <label for="printable"><span></span> ' . t( 'field_printable', "Printable" ) . '</label>
<input type="checkbox" name="fields[avab_online]" id="avab_online" checked /> <label for="avab_online"><span></span> ' . t( 'field_available_online', "Available Online" ) . '</label>
<input type="checkbox" name="fields[source]" id="source" checked /> <label for="source"><span></span> ' . t( 'field_source_url', "Source(URL)" ) . '</label>
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

/** IMPORT COUPONS */

case 'import':

if( !ab_to( array( 'coupons' => 'import' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'coupons_import_title', "Import Coupons" ) . '</h2>

<div style="float:right;margin:0 2px 0 0;">
<a href="?route=coupons.php&amp;action=list" class="btn">' . t( 'coupons_view', "View Coupons" ) . '</a>
</div>';

$subtitle = t( 'coupons_import_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_coupon_page', 'after_title_import_coupons_page' ) );

echo '<div id="upload-theme-form">';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'coupons_csrf' ) ) {

    if( isset( $_POST['category'] ) && isset( $_FILES['file'] ) )
    if( $import = admin\actions::import_items(
    array(
    'category'  => $_POST['category'],
    'file'      => $_FILES['file'],
    'omit_first_line' => ( isset( $_POST['omitfirst'] ) ? 1 : 0 ),
    'fields'    => $_POST['field']
    ) ) )

    echo '<div class="a-success">' . sprintf( t( 'msg_couponsimported', "%s coupons imported, %s errors." ), $import[0], $import[1] ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else {

    echo '<div class="a-alert">' . t( 'msg_import_coupons', "To import coupons correctly, fields from the CSV file should be exactly in this order: Title*, Link(URL), Description, Tags, Image, Code, Start Date, Expiration Date, Store Link/Website*, Printable, Available Online, Source(URL)." ) . '</div>';
    echo '<div class="a-message">' . t( 'msg_import_coupons_n', "Note*: You are able to import coupons only for existing stores, the coupons are imported to stores with same domain name. Practically, stores must be imported before coupons." ) . '</div>';

}

$csrf = $_SESSION['coupons_csrf'] = \site\utils::str_random(10);

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
<input type="hidden" name="field[5]" value="code" />
<input type="hidden" name="field[6]" value="start" />
<input type="hidden" name="field[7]" value="expiration" />
<input type="hidden" name="field[8]" value="store_url" />
<input type="hidden" name="field[9]" value="printable" />
<input type="hidden" name="field[10]" value="available_online" />
<input type="hidden" name="field[11]" value="source" />
<input type="hidden" name="field[12]" value="store_name" />
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

<div style="text-align:center;">
    <a href="?route=coupons.php&action=import_advanced" class="btn">' . t( 'data_import_advanced', "Data Import Advanced" ) . '</a>
</div>

</div>';

echo '<div id="process-theme">
    <h2>' . t( 'msg_upload_dleave', "Please do not leave this page during the import!" ) . '</h2>
</div>';

break;

/** IMPORT COUPONS - ADVANCED */

case 'import_advanced':

if( !ab_to( array( 'coupons' => 'import' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'coupons_import_title', "Import Coupons" ) . '</h2>

<div style="float:right;margin:0 2px 0 0;">
<a href="?route=coupons.php&amp;action=list" class="btn">' . t( 'coupons_view', "View Coupons" ) . '</a>
</div>';

$subtitle = t( 'coupons_import_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_coupon_page', 'after_title_advanced_import_coupons_page' ) );

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

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'coupons_csrf' ) ) {

    if( isset( $_POST['category'] ) && isset( $_FILES['file'] ) && isset( $_POST['field'] ) )
    if( $import = admin\actions::import_items(
    array(
    'store'     => $storeID,
    'category'  => $_POST['category'],
    'file'      => $_FILES['file'],
    'omit_first_line' => ( isset( $_POST['omitfirst'] ) ? 1 : 0 ),
    'fields'    => $_POST['field'],
    'def_ed'    => ( isset( $_POST['d_end_date'] ) ? 1 : 0 ),
    'end_date'  => date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['defined_end_date'] ) ) )
    ) ) )

    echo '<div class="a-success">' . sprintf( t( 'msg_couponsimported', "%s coupons imported, %s errors." ), $import[0], $import[1] ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else {

    echo '<div class="a-message">' . t( 'msg_import_coupons_n', "Note*: You are able to import coupons only for existing stores, the coupons are imported to stores with same domain name. Practically, stores must be imported before coupons." ) . '</div>';

}

$csrf = $_SESSION['coupons_csrf'] = \site\utils::str_random(10);

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
    <option value="name">' . t( 'field_title', "Title" ) . '*</option>
    <option value="link">' . t( 'field_link', "Link/Website" ) . '</option>
    <option value="description">' . t( 'field_description', "Description" ) . '</option>
    <option value="tags">' . t( 'field_tags', "Tags" ) . '</option>
    <option value="image">' . t( 'field_image', "Image" ) . '</option>
    <option value="code">' . t( 'field_code', "Code" ) . '</option>
    <option value="start">' . t( 'field_start_date', "Start Date" ) . '</option>
    <option value="expiration">' . t( 'field_end_date', "End Date" ) . '</option>';
    if( empty( $storeID ) ) {
        echo '<option value="store_url">' . t( 'field_store_url', "Store Website" ) . '*</option>
        <option value="store_name">' . t( 'field_store_name', "Store Name" ) . '</option>';
    }
    echo '<option value="printable">' . t( 'field_printable', "Printable" ) . '</option>
    <option value="available_online">' . t( 'field_available_online', "Available Online" ) . '</option>
    <option value="source">' . t( 'field_source_url', "Source(URL)" ) . '</option>';
    echo '</select></div></div>';

}
 
echo '<div id="modify_mt">

<div class="title">
    <h2>' . t( 'import_more_options_title', "More Options for Import" ) . '</h2>
</div>

<div class="row"><span>' . t( 'import_form_def_end_date', "Define End Date" ) . ' <span class="info"><span>' . t( 'import_form_def_iend_date', 'In case that expiration date field is mising, this input will be defined by default.' ) . '</span></span>:</span><div><input type="checkbox" name="d_end_date" id="d_end_date" value="1" /> <label for="d_end_date"><span></span> ' . t( 'import_def_coupons_end_date', "Define end date for coupons" ) . '</label></div></div>
<div class="row" data-required=\'' . json_encode( array( 'd_end_date' => 1 ) ) . '\'"><span>' . t( 'form_end_date', "End Date" ) . ':</span><div><input type="date" name="defined_end_date[date]" value="' . date( 'Y-m-d', strtotime( '+6 months' ) ). '" class="datepicker" style="display:inline-block;width:68%;margin-right:2%" /><input type="time" name="defined_end_date[hour]" value="00:00" class="hourpicker" style="display:inline-block;width:30%" /></div></div>
</div>

<input type="hidden" name="ready" value="1" />
<input type="hidden" name="fields" value="' . (int) $_POST['fields'] . '" />
<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
<div><button class="btn btn-important">' . t( 'button_import', "Import" ) . '</button></div>
<div><a href="#" class="btn" id="modify_mt_but">' . t( 'button_more', "More" ) . '</a></div>
</div>

</form>

</div></div>';

echo '<div id="process-theme">
    <h2>' . t( 'msg_upload_dleave', "Please do not leave this page during the import!" ) . '</h2>
</div>';

}

break;

/** ADD COUPON */

case 'add':

if( !ab_to( array( 'coupons' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'coupons_add_title', "Add New Coupon" ) . '</h2>

<div style="float:right;margin:0 2px 0 0;">

<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>
    <li><a href="#" class="more_fields">' . t( 'more', "More" ) . '</a></li>
</ul>
</div>

<a href="?route=coupons.php&amp;action=list" class="btn">' . t( 'coupons_view', "View Coupons" ) . '</a>
</div>';

$subtitle = t( 'coupons_add_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_coupon_page', 'after_title_add_coupon_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'coupons_csrf' ) ) {

    if( isset( $_POST['store'] ) && isset( $_POST['category'] ) && isset( $_POST['name'] ) && isset( $_POST['coupon_type'] ) && isset( $_POST['coupon_source_url'] ) && isset( $_FILES['coupon_source'] ) && isset( $_POST['code'] ) && isset( $_POST['description'] ) && isset( $_POST['tags'] ) && isset( $_POST['reward_points'] ) && isset( $_POST['votes'] ) && isset( $_POST['votes_average'] ) && isset( $_POST['lverified'] ) && isset( $_POST['start'] ) && isset( $_POST['end'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_keywords'] ) && isset( $_POST['meta_desc'] ) )
    if( ( $new_coupon_id = admin\actions::add_item(
    value_with_filter( 'save_coupon_values', array(
    'store'         => ( isset( $_POST['store'] ) ? $_POST['store'] : 0 ),
    'category'      => ( isset( $_POST['category'] ) ? $_POST['category'] : 0 ),
    'popular'       => ( isset( $_POST['popular'] ) ? 1 : 0 ),
    'exclusive'     => ( isset( $_POST['exclusive'] ) ? 1 : 0 ),
    'printable'     => ( isset( $_POST['coupon_type'] ) && in_array( (int) $_POST['coupon_type'], array( 1, 2 ) ) ? 1 : 0 ),
    'show_in_store' => ( isset( $_POST['coupon_type'] ) && (int) $_POST['coupon_type'] === 3 ? 1 : 0 ),
    'available_online' => ( isset( $_POST['coupon_use_online'] ) ? 1 : 0 ),
    'name'          => ( isset( $_POST['name'] ) ? $_POST['name'] : '' ),
    'link'          => ( !isset( $_POST['coupon_ownlink'] ) && isset( $_POST['link'] ) && preg_match( '/^http(s)?/i', $_POST['link'] ) ? $_POST['link'] : '' ),
    'code'          => ( isset( $_POST['coupon_type'] ) && (int) $_POST['coupon_type'] === 0 ? $_POST['code'] : '' ),
    'claim_limit'   => ( isset( $_POST['limit'] ) ? (int) $_POST['limit'] : 0 ),
    'source'        => ( isset( $_POST['coupon_type'] ) && (int) $_POST['coupon_type'] === 2 ? ( isset( $_POST['coupon_online_source'] ) ? ( filter_var( $_POST['coupon_source_url'], FILTER_VALIDATE_URL ) ? $_POST['coupon_source_url'] : '' ) : $_FILES['coupon_source'] ) : '' ),
    'description'   => ( isset( $_POST['description'] ) ? $_POST['description'] : '' ),
    'tags'          => ( isset( $_POST['tags'] ) ? $_POST['tags'] : '' ),
    'cashback'      => ( isset( $_POST['reward_points'] ) ? (int) $_POST['reward_points'] : 0 ),
    'start'         => ( isset( $_POST['start'] ) ? date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['start'] ) ) ) : date( 'Y-m-d H:i:s', time() ) ),
    'end'           => ( isset( $_POST['end'] ) ? date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['end'] ) ) ) : date( 'Y-m-d H:i:s', time() ) ),
    'image'         => ( isset( $_FILES['image'] ) && isset( $_FILES['image'] ) ? $_FILES['image'] : array() ),
    'votes'         => ( isset( $_POST['votes'] ) ? (int) $_POST['votes'] : 0 ),
    'votes_average' => ( isset( $_POST['votes_average'] ) ? (double) $_POST['votes_average'] : 0 ),
    'verified'      => ( isset( $_POST['verified'] ) ? 1 : 0 ),
    'last_verif'    => ( isset( $_POST['lverified'] ) ? date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['lverified'] ) ) ) : date( 'Y-m-d H:i:s', time() ) ),
    'publish'       => ( isset( $_POST['publish'] ) ? 1 : 0 ),
    'meta_title'    => ( isset( $_POST['meta_title'] ) ? $_POST['meta_title'] : '' ),
    'meta_keywords' => ( isset( $_POST['meta_keywords'] ) ? $_POST['meta_keywords'] : '' ),
    'meta_desc'     => ( isset( $_POST['meta_desc'] ) ? $_POST['meta_desc'] : '' ),
    'extra'         => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() )
    ) ) ) ) ) {

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';

    do_action( array( 'admin_coupon_added_edited', 'admin_coupon_added' ), $new_coupon_id );

    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf   = $_SESSION['coupons_csrf'] = \site\utils::str_random(10);

$main   = $GLOBALS['admin_main_class']->coupon_fields( (object) array(), $csrf );
$fields = $main['fields'];

admin\widgets::get_page_tabs( $main );

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off" class="coupon-form">';

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

do_action( array( 'admin_coupon_after_form_add_edit', 'admin_coupon_after_form_add' ) );

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
        <button class="btn btn-important">' . t( 'coupons_add_button', "Add Coupon" ) . '</button>
    </div>
    <div>
        <a href="#" class="btn" id="modify_mt_but">' . t( 'pages_editmt_button', "Meta Tags" ) . '</a>
    </div>
</div>

</form>

</div>';

break;

/** EDIT COUPON */

case 'edit':

if( !ab_to( array( 'coupons' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'coupons_edit_title', "Edit Coupon" ) . '</h2>

<div style="float: right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $item_exists = \query\main::item_exists( $_GET['id'] ) ) ) {

$info = \query\main::item_info( $_GET['id'], array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>
<li><a href="#" class="more_fields">' . t( 'more', "More" ) . '</a></li>';
if( ab_to( array( 'stores' => 'delete' ) ) ) echo '<li><a href="?route=coupons.php&amp;action=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>';
if( $info->visible ) {
    echo '<li><a href="?route=coupons.php&amp;action=list&amp;type=unpublish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'unpublish', "Unpublish" ) . '</a></li>';
} else {
    echo '<li><a href="?route=coupons.php&amp;action=list&amp;type=publish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'publish', "Publish" ) . '</a></li>';
}
echo '</ul>
</div>';

}

echo '<a href="?route=coupons.php&amp;action=list" class="btn">' . t( 'coupons_view', "View Coupons" ) . '</a>

</div>';

$subtitle = t( 'coupons_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $item_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_coupon_page', 'after_title_edit_coupon_page' ), $info->ID );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'coupons_csrf' ) ) {

if( isset( $_POST['change_url_title'] ) ) {

    if( isset( $_POST['url_title'] ) )
    if( admin\actions::edit_item_url( $_GET['id'], array( 'title' => $_POST['url_title'] ) ) ) {

    $info = \query\main::item_info( $_GET['id'], array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';

    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else {

    if( isset( $_POST['store'] ) && isset( $_POST['category'] ) && isset( $_POST['name'] ) && isset( $_POST['coupon_type'] ) && isset( $_POST['coupon_source_url'] ) && isset( $_FILES['coupon_source'] ) && isset( $_POST['code'] ) && isset( $_POST['description'] ) && isset( $_POST['tags'] ) && isset( $_POST['reward_points'] ) && isset( $_POST['votes'] ) && isset( $_POST['votes_average'] ) && isset( $_POST['lverified'] ) && isset( $_POST['start'] ) && isset( $_POST['end'] ) && isset( $_POST['meta_title'] ) && isset( $_POST['meta_keywords'] ) && isset( $_POST['meta_desc'] ) )
    if( admin\actions::edit_item( $_GET['id'],
    value_with_filter( 'save_coupon_values', array(
    'store'         => ( isset( $_POST['store'] ) ? $_POST['store'] : 0 ),
    'category'      => ( isset( $_POST['category'] ) ? $_POST['category'] : 0 ),
    'popular'       => ( isset( $_POST['popular'] ) ? 1 : 0 ),
    'exclusive'     => ( isset( $_POST['exclusive'] ) ? 1 : 0 ),
    'printable'     => ( isset( $_POST['coupon_type'] ) && in_array( (int) $_POST['coupon_type'], array( 1, 2 ) ) ? 1 : 0 ),
    'show_in_store' => ( isset( $_POST['coupon_type'] ) && (int) $_POST['coupon_type'] === 3 ? 1 : 0 ),
    'available_online' => ( isset( $_POST['coupon_use_online'] ) ? 1 : 0 ),
    'name'          => ( isset( $_POST['name'] ) ? $_POST['name'] : '' ),
    'link'          => ( !isset( $_POST['coupon_ownlink'] ) && isset( $_POST['link'] ) && preg_match( '/^http(s)?/i', $_POST['link'] ) ? $_POST['link'] : '' ),
    'code'          => ( isset( $_POST['coupon_type'] ) && (int) $_POST['coupon_type'] === 0 ? $_POST['code'] : '' ),
    'claim_limit'   => ( isset( $_POST['limit'] ) ? (int) $_POST['limit'] : 0 ),
    'source'        => ( isset( $_POST['coupon_type'] ) && (int) $_POST['coupon_type'] === 2 ? ( isset( $_POST['coupon_online_source'] ) ? ( filter_var( $_POST['coupon_source_url'], FILTER_VALIDATE_URL ) ? $_POST['coupon_source_url'] : '' ) : $_FILES['coupon_source'] ) : '' ),
    'description'   => ( isset( $_POST['description'] ) ? $_POST['description'] : '' ),
    'tags'          => ( isset( $_POST['tags'] ) ? $_POST['tags'] : '' ),
    'cashback'      => ( isset( $_POST['reward_points'] ) ? (int) $_POST['reward_points'] : 0 ),
    'start'         => ( isset( $_POST['start'] ) ? date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['start'] ) ) ) : date( 'Y-m-d H:i:s', time() ) ),
    'end'           => ( isset( $_POST['end'] ) ? date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['end'] ) ) ) : date( 'Y-m-d H:i:s', time() ) ),
    'image'         => ( isset( $_FILES['image'] ) && isset( $_FILES['image'] ) ? $_FILES['image'] : array() ),
    'votes'         => ( isset( $_POST['votes'] ) ? (int) $_POST['votes'] : 0 ),
    'votes_average' => ( isset( $_POST['votes_average'] ) ? (double) $_POST['votes_average'] : 0 ),
    'verified'      => ( isset( $_POST['verified'] ) ? 1 : 0 ),
    'last_verif'    => ( isset( $_POST['lverified'] ) ? date( 'Y-m-d H:i:s', strtotime( implode( ' ', $_POST['lverified'] ) ) ) : date( 'Y-m-d H:i:s', time() ) ),
    'publish'       => ( isset( $_POST['publish'] ) ? 1 : 0 ),
    'meta_title'    => ( isset( $_POST['meta_title'] ) ? $_POST['meta_title'] : '' ),
    'meta_keywords' => ( isset( $_POST['meta_keywords'] ) ? $_POST['meta_keywords'] : '' ),
    'meta_desc'     => ( isset( $_POST['meta_desc'] ) ? $_POST['meta_desc'] : '' ),
    'extra'         => ( isset( $_POST['extra'] ) ? $_POST['extra'] : array() )
    ) ) ) ) {

    $info = \query\main::item_info( $_GET['id'], array( 'no_emoticons' => true, 'no_shortcodes' => true, 'no_filters' => true ) );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';

    do_action( array( 'admin_coupon_added_edited', 'admin_coupon_edited' ), $info->ID );

    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'coupons_csrf' ) ) {

if( $_GET['type'] == 'delete_image' ) {

    if( admin\actions::delete_item_image( $_GET['id'] ) ) {
    $info->image = '';
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'delete_source' ) {

    if( admin\actions::delete_item_source( $_GET['id'] ) ) {

    $info = \query\main::item_info( $_GET['id'] );

    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$_SESSION['coupons_csrf'] = $csrf;

$main   = $GLOBALS['admin_main_class']->coupon_fields( $info, $csrf );
$fields = $main['fields'];

admin\widgets::get_page_tabs( $main );

echo '<div class="form-table">

<form action="#" method="POST" enctype="multipart/form-data" autocomplete="off" class="coupon-form">';

uasort( $fields, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $fields as $key => $f ) {
    echo $f['markup'];
}

do_action( array( 'admin_coupon_after_form_add_edit', 'admin_coupon_after_form_edit' ), $info );

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
        <button class="btn btn-important">' . t( 'coupons_edit_button', "Edit Coupon" ) . '</button>
    </div>
    <div>
        <a href="#" class="btn" id="modify_mt_but">' . t( 'pages_editmt_button', "Meta Tags" ) . '</a>
    </div>
</div>

</form>

</div>';

echo '<div class="title" style="margin-top:40px;">

<h2>' . t( 'coupons_info_title', "Information About This Coupon" ) . '</h2>

</div>';
   
echo '<div class="info-table" id="info-table" style="padding-bottom: 20px;">

<form action="?route=coupons.php&amp;action=edit&amp;id=' . $info->ID . '#info-table" method="POST" autocomplete="off">';

$stat_rows              = array();
$stat_rows['id']        = '<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>';
$stat_rows['views']     = '<div class="row"><span>' . t( 'views', "Views" ) . ':</span> <div>' . $info->views . '</div></div>';
$stat_rows['clicks']    = '<div class="row"><span>' . t( 'clicks', "Clicks" ) . ':</span> <div>' . $info->clicks . '</div></div>';
$stat_rows['url']       = '<div class="row"><span>' . t( 'page_url', "Page URL" ) . ':</span> <div class="modify_url">
<div' . ( isset( $_GET['editurl'] ) ? ' style="display: none;"' : '' ) . '><a href="' . $info->link . '" target="_blank">' . $info->link . '</a> / <a href="?route=coupons.php&amp;action=edit&amp;id=' . $info->ID . '&amp;editurl#info-table">' . t( 'edit', "Edit" ) . '</a></div>
<div' . ( !isset( $_GET['editurl'] ) ? ' style="display: none;"' : '' ) . '>
<input type="text" name="url_title" value="' . $info->url_title . '" placeholder="' . $info->title . '" style="display: block; width: 100%; box-sizing: border-box;" />
<input type="hidden" name="csrf" value="' . $csrf . '" />
<button name="change_url_title" class="btn save">' . t( 'save', "Save" ) . '</button> <a href="?route=coupons.php&amp;action=edit&amp;id=' . $info->ID . '#info-table" class="btn close">' . t( 'cancel', "Cancel" ) . '</a>
</div>
</div></div>';
$stat_rows['store']     = '<div class="row"><span>' . t( 'store_name', "Store name" ) . ':</span> <div>' . ( empty( $info->store_name ) ? '-' : ( ab_to( array( 'stores' => 'edit' ) ) ? '<a href="?route=stores.php&amp;action=edit&amp;id=' . $info->storeID . '">' . $info->store_name . '</a>' : $info->store_name ) ) . '</div></div>';
$stat_rows['lu_by']     = '<div class="row"><span>' . t( 'last_update_by', "Last update by" )    . ':</span> <div>' . ( empty( $info->lastupdate_by_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->lastupdate_by . '">' . $info->lastupdate_by_name . '</a>' : $info->lastupdate_by_name ) ) . '</div></div>';
$stat_rows['lu_date']   = '<div class="row"><span>' . t( 'last_update_on', "Last update on" )    . ':</span> <div>' . $info->last_update . '</div></div>';
$stat_rows['adden_by']  = '<div class="row"><span>' . t( 'added_by', "Added by" ) . ':</span> <div>' . ( empty( $info->user_name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $info->userID . '">' . $info->user_name . '</a>' : $info->user_name ) ) . '</div></div>';
$stat_rows['added_date']= '<div class="row"><span>' . t( 'added_on', "Added on" )    . ':</span> <div>' . $info->date . '</div></div>';

echo implode( '', value_with_filter( 'admin_coupon_stats', $stat_rows ) );

echo '</form>

</div>';

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/** LIST OF COUPONS */

default:

if( !ab_to( array( 'coupons' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'coupons_title', "Coupons" ) . '</h2>

<div style="float:right;margin:0 2px 0 0;">';

if( ( $ab = ab_to( array( 'coupons' => array( 'export', 'import' ) ) ) ) && list( $ab_exp, $ab_imp ) = $ab ) {

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( $ab_imp ) echo '<li><a href="?route=coupons.php&amp;action=import">' . t( 'import', "Import" ) . '</a></li>';
if( $ab_exp ) echo '<li><a href="?route=coupons.php&amp;action=export">' . t( 'export', "Export" ) . '</a></li>';

echo '</ul>
</div>';

}

if( ab_to( array( 'coupons' => 'add' ) ) ) echo '<a href="?route=coupons.php&amp;action=add" class="btn">' . t( 'coupons_add', "Add Coupon" ) . '</a>';
echo '</div>';

$subtitle = t( 'coupons_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_coupon_page', 'after_title_list_coupons_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'coupons_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_item( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

    if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
    if( admin\actions::action_item( $_POST['action'], array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'coupons_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_item( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::action_item( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['coupons_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="coupons.php" />
<input type="hidden" name="action" value="list" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ), 'views' => t( 'order_views', "Views" ), 'views desc' => t( 'order_views_desc', "Views DESC" ),    'clicks' => t( 'order_clicks', "Clicks" ), 'clicks desc' => t( 'order_clicks_desc', "Clicks DESC" ), 'update' => t( 'order_last_update', "Last update" ), 'update desc' => t( 'order_last_update_desc', "Last update DESC" ), 'active' => t( 'order_expiration', "Expiration date" ), 'active DESC' => t( 'order_expiration_desc', "Expiration date DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
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
<input type="hidden" name="route" value="coupons.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
    echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

if( isset( $_GET['category'] ) ) {
    echo '<input type="hidden" name="category" value="' . esc_html( $_GET['category'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'coupons_search_input', "Search coupons" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$custom_toolbar = do_action( 'admin_items_list_custom_toolbar' );

if( !empty( $custom_toolbar ) ) {
    echo '<div class="page-toolbar">';
    echo $custom_toolbar;
    echo '</div>';
}

$p = \query\main::have_items( ( $options = value_with_filter( 'admin_view_items_args', array( 'per_page' => 10, 'store' => ( isset( $_GET['store'] ) ? $_GET['store'] : '' ), 'user' => ( isset( $_GET['user'] ) ? $_GET['user'] : '' ), 'categories' => ( isset( $_GET['category'] ) ? $_GET['category'] : '' ), 'show' => ( isset( $_GET['view'] ) ? $_GET['view'] : 'all' ), 'search' => ( isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '' ) ) ) ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
$reset = ( !empty( $_GET['store'] ) || !empty( $_GET['user'] ) || !empty( $_GET['category'] ) || !empty( $_GET['view'] ) || !empty( $_GET['search'] ) );
if( value_with_filter( 'admin_items_list_reset_view', $reset ) ) echo ' / <a href="?route=coupons.php&amp;action=list">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=coupons.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

$ab_edt    = ab_to( array( 'coupons' => 'edit' ) );
$ab_del    = ab_to( array( 'coupons' => 'delete' ) );

if( $ab_del ) {

echo '<div class="bulk_options">';

if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button> ';

if( $ab_edt ) {
    echo t( 'action', "Action" ) . ':
    <select name="action">';
    foreach( array( 'publish' => t( 'publish', "Publish" ), 'unpublish' => t( 'unpublish', "Unpublish" ), 'updatevdate' => t( 'setverifnow', "Set as verified now" ) ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
    echo '</select>
    <button class="btn" name="set_action">' . t( 'set_all', "Set to All" ) . '</button>';
}

echo '</div>';

}

foreach( \query\main::while_items( array_merge( array( 'page' => $p['page'], 'orderby' => value_with_filter( 'admin_view_coupons_orderby', ( isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc' ) ) ), $options ), '', array( 'no_emoticons' => true, 'no_filters' => true ) ) as $item ) {

    $links = array();

    if( $ab_edt ) {
        $links['edit'] = '<a href="?route=coupons.php&amp;action=edit&amp;id=' . $item->ID . '">' . t( 'edit', "Edit" ) . '</a>';
        $links['publish'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( !$item->visible ? 'publish' : 'unpublish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( !$item->visible ? t( 'publish', "Publish" ) : t( 'unpublish', "Unpublish" ) ) . '</a>';
    }
    if( $ab_del ) $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'action' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'coupon', $item, $links );

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

} else echo '<div class="a-alert">' . t( 'no_coupons_yet', "No coupons yet." ) . '</div>';

break;

}