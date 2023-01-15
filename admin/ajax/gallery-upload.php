<?php

for( $i = 0; $i < count( $_FILES['file']['name'] ); $i++ ) {
    if( admin\actions::upload_gallery_image( 
        array( 'file' => 
            array( 
                'name' => $_FILES['file']['name'][$i],
                'type' => $_FILES['file']['type'][$i],
                'tmp_name' => $_FILES['file']['tmp_name'][$i],
                'error' => $_FILES['file']['error'][$i],
                'size' => $_FILES['file']['size'][$i]
            ),  
            'cat_id' => ( isset( $_POST['category'] ) && $_POST['category'] !== 'selected' ? $_POST['category'] : '' ) 
            ) 
        )
    ) {
        return true;
    } else {
        return false;
    }
}