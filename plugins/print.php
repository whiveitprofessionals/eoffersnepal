<?php

if( !isset( $_GET['id'] ) || !\query\main::item_exists( $_GET['id'] ) ) {
    header( 'Location: ' . $GLOBALS['siteURL'] );
    die;
}

$info = \query\main::item_info( $_GET['id'] );

if( !$info->is_printable ) {
    header( 'Location: ' . $GLOBALS['siteURL'] );
    die;
}

if( !empty( $info->source ) ) {
    if( !\site\utils::file_has_extension( $info->source, '.jpg,.jpeg,.png,.gif' ) ) {
    header( 'Location: ' . $info->source );
    die;
    }
}

echo '<!DOCTYPE html>

<html>

    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title>' . $info->title . '</title>
        <link href="//fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900" rel="stylesheet" />
        <link href="' . MISCDIR . '/print.css" media="all" rel="stylesheet" />

    </head>

<body>

<div class="smalldev">
' . t( 'msg_cant_print_sd', "To print this coupon please visit us from a larger device." ) . '<br />
<a href="' . $info->link . '">&larr; ' . t( 'back', "Back" ) . '</a>
</div>

<div class="container">

<a href="#" class="btn margin-bottom-10" id="print">' . t( 'print_coupon', "Print Coupon" ) . '</a>';

if( empty( $info->source ) ) {

    echo '<div class="gen-container">

    <div class="sp">
    <img src="' . \query\main::store_avatar( $info->store_img ) . '" style="width:50%;max-height:100px;" alt="" />

    <h3>' . $info->store_name . '</h3>

    <ul>';
    if( !empty( $info->store_phone_no ) ) {
        echo '<li><span>' . t( 'phone_no', "Phone Number" ) . ':</span> ' . $info->store_phone_no . '</li>';
    }
    echo '<li class="tbl"><span>' . t( 'form_address', "Address" ) . ':</span>';
    if( \query\locations::store_locations( array( 'store' => $info->storeID ) ) ) {
    echo '<ul>';
    foreach( \query\locations::while_store_locations( array( 'max' => 0, 'store' => $info->storeID ) ) as $loc ) {
        echo '<li>' . implode( ', ', array_filter( array( $loc->state, $loc->city, $loc->address ) ) ) . '</li>';
    }
    echo '</ul>';
    } else {
        echo '-';
    }
    echo '</li>
    </ul>
    </div>

    <div class="cp">

    <h2>' . $info->title . '</h2>

    <ul>';
    if( !$info->is_started ) {
        echo '<li><span>' . t( 'form_start_date', "Start Date" ) . ':</span> ' . date( 'Y-m-d, H:i', strtotime( $info->start_date ) ) . ' (GMT ' . date( 'P' ) . ')</li>';
    }
    echo '<li><span>' . t( 'form_expiration_date', "Expiration Date" ) . ':</span> ' . date( 'Y-m-d, H:i', strtotime( $info->expiration_date ) ) . ' (GMT ' . date( 'P' ) . ')</li>
    <li><span>' . t( 'form_description', "Description" ) . ':</span> ' . ( !empty( $info->description ) ? $info->description : '-' ) . '</li>
    </ul>
    </div>

    </div>';

} else {

    echo '<div class="pic-container">
    <img src="' . $info->source . '" alt="coupon" />
    </div>';

}

echo '<div class="margin-top-10">
<a href="' . $info->link . '">&larr; ' . t( 'back', "Back" ) . '</a> <br />
<a href="' . $info->store_link . '">&larr; ' . $info->store_name . '</a>
</div>

</div>

<script>

document.getElementById("print").onclick = function(){
    window.print();
};

</script>

</body>

</html>';