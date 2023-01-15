<?php

$answer = array();

foreach( \query\main::while_categories( array( 'max' => 50, 'orderby' => 'name', 'search' => ( isset( $_POST['search'] ) ? urldecode( $_POST['search'] ) : '' ), 'show' => ( isset( $_GET['show'] ) ? $_GET['show'] : 'all' ) ) ) as $item ) {

    $answer[$item->ID] = array( 'name' => ts( $item->name ) );

}

echo json_encode( $answer );