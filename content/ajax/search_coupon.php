<?php

$answer = array();

foreach( \query\main::while_items( array( 'max' => 50, 'orderby' => 'title', 'search' => ( isset( $_POST['search'] ) ? urldecode( $_POST['search'] ) : '' ), 'show' => ( isset( $_POST['show'] ) ? urldecode( $_POST['show'] ) : 'all' ) ), '', array( 'no_emoticons' => true, 'no_filters' => true ) ) as $item ) {

    $answer[$item->ID] = array( 'name' => $item->title );

}

echo json_encode( $answer );