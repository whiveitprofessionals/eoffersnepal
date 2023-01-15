<?php

if( !ab_to( array( 'stores' => 'export' ) ) || $_SERVER["REQUEST_METHOD"] != 'POST' || !isset( $_POST['csrf'] ) || !isset( $_SESSION['coupons_csrf'] ) || $_POST['csrf'] != $_SESSION['coupons_csrf'] ) {

// redirect to the last page if this can't be downloaded for any reason from above

echo '<script type="text/javascript">

window.onload = function(){
  window.history.go(-1);
}

</script>';

die;

}

$from = isset( $_POST['date']['from'] ) ? strtotime( $_POST['date']['from'] ) : strtotime( '2000-01-01' );
$to = isset( $_POST['date']['to'] ) ? strtotime( $_POST['date']['to'] ) : strtotime( 'tomorrow' );

// disable caching

header( "Expires: Tue, 03 Jul 2001 06:00:00 GMT" );
header( "Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate" );
header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );

// force download

header( "Content-Type: application/force-download" );
header( "Content-Type: application/octet-stream" );
header( "Content-Type: application/download" );

// disposition / encoding on response body

header( "Content-Disposition: attachment; filename=coupons_" . date( 'dMy', $from ) . "-" . date( 'dMy', $to ) . ".csv" );
header( "Content-Transfer-Encoding: binary" );

$file = fopen( 'php://output', 'w' );

$head = array();
$head[] = 'Title';
$head[] = 'Link';
$head[] = 'Description';
$head[] = 'Tags';
$head[] = 'Image';
$head[] = 'Code';
$head[] = 'Start';
$head[] = 'End';
$head[] = 'Store URL';
if( $url = isset( $_POST['fields']['url'] ) ) {
  $head[] = 'URL';
}
if( $printable = isset( $_POST['fields']['printable'] ) ) {
  $head[] = 'Printable';
}
if( $avab_online = isset( $_POST['fields']['avab_online'] ) ) {
  $head[] = 'Available Online';
}
if( $source = isset( $_POST['fields']['source'] ) ) {
  $head[] = 'Source';
}
if( $store_name = isset( $_POST['fields']['store_name'] ) ) {
  $head[] = 'Store name';
}

fputcsv( $file, array_values( $head ) );

foreach( \query\main::while_items( array( 'max' => 0, 'categories' => ( isset( $_POST['category'] ) ? $_POST['category'] : 0 ), 'date' => $from . ',' . $to ), '', array( 'no_emoticons' => true ) ) as $coupon ) {

$line = array();

$line[] = htmlspecialchars_decode( $coupon->title );
$line[] = htmlspecialchars_decode( $coupon->url );
$line[] = htmlspecialchars_decode( $coupon->description );
$line[] = htmlspecialchars_decode( $coupon->tags );
$line[] = filter_var( $coupon->image, FILTER_VALIDATE_URL ) ? $coupon->image : ( empty( $coupon->image ) ? '' : $GLOBALS['siteURL'] . $coupon->image );
$line[] = htmlspecialchars_decode( $coupon->code );
$line[] = $coupon->start_date;
$line[] = $coupon->expiration_date;
$line[] = htmlspecialchars_decode( $coupon->store_url );
if( $url ) {
  $line[] = htmlspecialchars_decode( $coupon->link );
}
if( $printable ) {
  $line[] = $coupon->is_printable;
}
if( $avab_online ) {
  $line[] = $coupon->is_available_online;
}
if( $source ) {
  $line[] = $coupon->source;
}
if( $store_name ) {
  $line[] = htmlspecialchars_decode( $coupon->store_name );
}

fputcsv( $file, $line );

}

fclose( $file );