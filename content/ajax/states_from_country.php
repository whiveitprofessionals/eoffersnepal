<?php

$answer = array();

if( isset( $_POST['id'] ) ) {

    foreach( \query\locations::while_states( array( 'max' => 0, 'orderby' => 'name', 'country' => (isset( $_POST['id'] ) ? $_POST['id'] : ''), 'search' => (isset( $_POST['search'] ) ? urldecode( $_POST['search'] ) : '') ) ) as $item ) {

        $answer[] = array( 'ID' => $item->ID, 'name' => $item->name, 'lat' => $item->lat, 'lng' => $item->lng );

    }

}

echo json_encode( $answer ); 