<?php

if( isset( $_POST['id'] ) && admin\actions::delete_gallery_image( $_POST['id'] ) ) {
    echo json_encode( array( 'success' => true ) );
} else {
    echo json_encode( array( 'success' => false ) );
}