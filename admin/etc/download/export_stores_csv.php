<?php

if( !ab_to( array( 'stores' => 'export' ) ) || $_SERVER["REQUEST_METHOD"] != 'POST' || !isset( $_POST['csrf'] ) || !isset( $_SESSION['stores_csrf'] ) || $_POST['csrf'] != $_SESSION['stores_csrf'] ) {

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

header( "Content-Disposition: attachment; filename=stores_" . date( 'dMy', $from ) . "-" . date( 'dMy', $to ) . ".csv" );
header( "Content-Transfer-Encoding: binary" );

$file = fopen( 'php://output', 'w' );

$head = array();
$head[] = 'Name';
$head[] = 'Link';
$head[] = 'Description';
$head[] = 'Tags';
$head[] = 'Image';
if( $url = isset( $_POST['fields']['url'] ) ) {
  $head[] = 'URL';
}
if( $type = isset( $_POST['fields']['type'] ) ) {
  $head[] = 'Type';
}
if( $sell_online = isset( $_POST['fields']['sell_online'] ) ) {
  $head[] = 'Sell Online';
}
if( $hours = isset( $_POST['fields']['hours'] ) ) {
  $head[] = 'Hours';
}
if( $locations = isset( $_POST['fields']['locations'] ) ) {
  $head[] = 'Locations';
}
if( $phone = isset( $_POST['fields']['phone'] ) ) {
  $head[] = 'Phone No';
}

fputcsv( $file, array_values( $head ) );

foreach( \query\main::while_stores( array( 'max' => 0, 'categories' => ( isset( $_POST['category'] ) ? $_POST['category'] : 0 ), 'date' => $from . ',' . $to ), array( 'no_emoticons' => true ) ) as $store ) {

$line = array();

$line[] = htmlspecialchars_decode( $store->name );
$line[] = htmlspecialchars_decode( $store->url );
$line[] = htmlspecialchars_decode( $store->description );
$line[] = htmlspecialchars_decode( $store->tags );
if( empty( $store->image ) ) {
  $line[] = '';
} else {
  $line[] = filter_var( $store->image, FILTER_VALIDATE_URL ) ? $store->image : ( empty( $store->image ) ? '' : $GLOBALS['siteURL'] . $store->image );
}
if( $url ) {
  $line[] = htmlspecialchars_decode( $store->link );
}
if( $type ) {
  $line[] = $store->is_physical;
}
if( $sell_online ) {
  $line[] = $store->sellonline;
}
if( $hours ) {
  $line[] = !empty( $store->hours ) ? @serialize( $store->hours ) : '';
}
if( $locations ) {
  $loc = array();
  if( $store->is_physical && \query\locations::store_locations( array( 'store' => $store->ID ) ) ) {
  foreach( \query\locations::while_store_locations( array( 'max' => 0, 'store' => $store->ID ) ) as $location ) {
    $loc[$location->ID] = array( 'Country' => $location->country, 'State' => $location->state, 'City' => $location->city, 'Zip' => $location->zip, 'Address' => $location->address, 'Lat' => $location->lat, 'Lng' => $location->lng );
  }
  }
  $line[] = !empty( $loc ) ? @serialize( $loc ) : '';
}
if( $phone ) {
  $line[] = !empty( $store->phone_no ) ? $store->phone_no : '';
}

fputcsv( $file, $line );

}

fclose( $file );