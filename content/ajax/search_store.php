<?php
//header('Content-Type: application/json');
$answer = array();

foreach( \query\main::while_stores( array( 'max' => 50, 'orderby' => 'name', 'search' => ( isset( $_POST['search'] ) ? urldecode( $_POST['search'] ) : '' ), 'show' => ( isset( $_GET['show'] ) ? $_GET['show'] : 'all' ) ), '', array( 'no_emoticons' => true, 'no_filters' => true ) ) as $item ) {

    $answer[$item->ID] = array( 'name' => $item->name, 'catID' => $item->catID, 'sell_online' => $item->sellonline, 'store-is_physical' => $item->is_physical );

}

echo json_encode( $answer );