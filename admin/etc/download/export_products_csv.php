<?php

if( !ab_to( array( 'stores' => 'export' ) ) || $_SERVER["REQUEST_METHOD"] != 'POST' || !isset( $_POST['csrf'] ) || !isset( $_SESSION['products_csrf'] ) || $_POST['csrf'] != $_SESSION['products_csrf'] ) {

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

header( "Content-Disposition: attachment; filename=products_" . date( 'dMy', $from ) . "-" . date( 'dMy', $to ) . ".csv" );
header( "Content-Transfer-Encoding: binary" );

$file = fopen( 'php://output', 'w' );

$head = array();
$head[] = 'Title';
$head[] = 'Link';
$head[] = 'Description';
$head[] = 'Tags';
$head[] = 'Image';
$head[] = 'Price';
$head[] = 'Old Price';
$head[] = 'Currency';
$head[] = 'Start';
$head[] = 'End';
$head[] = 'Store URL';
if( $url = isset( $_POST['fields']['url'] ) ) {
  $head[] = 'URL';
}
if( $store_url = isset( $_POST['fields']['store_name'] ) ) {
  $head[] = 'Store name';
}

fputcsv( $file, array_values( $head ) );

foreach( \query\main::while_products( array( 'max' => 0, 'categories' => ( isset( $_POST['category'] ) ? $_POST['category'] : 0 ), 'date' => $from . ',' . $to ), '', array( 'no_emoticons' => true ) ) as $product ) {

$line = array();

$line[] = htmlspecialchars_decode( $product->title );
$line[] = htmlspecialchars_decode( $product->url );
$line[] = htmlspecialchars_decode( $product->description );
$line[] = htmlspecialchars_decode( $product->tags );
$line[] = filter_var( $product->image, FILTER_VALIDATE_URL ) ? $product->image : ( empty( $product->image ) ? '' : $GLOBALS['siteURL'] . $product->image );
$line[] = $product->price;
$line[] = $product->old_price;
$line[] = $product->currency;
$line[] = $product->start_date;
$line[] = $product->expiration_date;
$line[] = htmlspecialchars_decode( $product->store_url );
if( $url ) {
  $line[] = htmlspecialchars_decode( $product->link );
}
if( $store_url ) {
  $line[] = htmlspecialchars_decode( $product->store_name );
}

fputcsv( $file, $line );

}

fclose( $file );