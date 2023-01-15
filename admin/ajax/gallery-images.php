<?php if( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
    die;
}

$args = array();

if( !empty( $_POST['search'] ) ) {
    $args['search'] = $_POST['search'];
}

if( !empty( $_POST['category'] ) ) {
    if( $_POST['category']  == 'selected' ) {
        if( !empty( $_POST['selected'] ) )
        $args['ids'] = implode( ',', $_POST['selected'] );
        else 
        die( '<div class="a-alert">' . t( 'no_images_in_gallery_yet', 'No images yet.' ) . '</div>' );
    } else {
        $args['category'] = $_POST['category'];
    }
}

$result = \query\gallery::have_images( $args );

if( $result['results'] ) { ?>
<ul class="modal-items">

<?php foreach( \query\gallery::fetch_images( array_merge( $args, array( 'orderby' => 'date desc', 'max' => 0 ) ) ) as $image ) {
    echo '<li>';
    echo '<div class="gallery-img">
    <img src="' . ( preg_match( '/^http(s)?/i', $image->sizes['original'] ) ? $image->sizes['original'] :  '../' . $image->sizes['original'] ) . '" />
    </div>
    <div class="gallery-image-details">';
    echo '<input type="' . ( isset( $_POST['multi'] ) && $_POST['multi'] === 'true' ? 'checkbox' : 'radio' ) . '" name="images" id="' . $image->ID . '" value="' . $image->sizes['original'] . '"' . ( isset( $_POST['selected'] ) && in_array( $image->ID, $_POST['selected'] ) ? ' checked' : '' ) . '  /> <label for="' . $image->ID . '">';
    echo '<span></span> <h4>' . $image->title . '</h4></label>';
    if( ab_to( array( 'gallery' => 'delete' ) ) ) {
        echo '<div class="links">
            <a href="#" id="' . $image->ID . '" class="delete"><span class="ccms">V</span> ' . t( 'delete', 'Delete' ) . '</a>
        </div>';
    }
    echo '</div>';
    echo '</li>';
} ?>

</ul>
<?php } else {
    echo '<div class="a-alert">' . t( 'no_images_in_gallery_yet', 'No images yet.' ) . '</div>';
} ?>