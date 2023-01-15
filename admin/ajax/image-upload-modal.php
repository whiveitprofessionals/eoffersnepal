<?php if( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
    die;
} ?>

<div class="modal-container">
    <div class="page-toolbar">
        <h2><?php te( 'gallery_title', 'Gallery' ); ?></h2>
        <span class="right-text">
            <a href="#" class="btn btn-important modal-save"><?php te( 'save', 'Save' ); ?></a>
            <a href="#" class="btn modal-close"><?php te( 'close', 'Close' ); ?></a>
        </span>
    </div>

    <div class="modal-content">
        <div class="page-toolbar">
            <?php if( ab_to( array( 'gallery' => 'upload' ) ) ) { ?>
            <a href="#" class="btn modal-upload"><?php te( 'upload', 'Upload' ); ?></a>
            <?php } ?>

            <span class="right-text">
                <?php echo sprintf( '%s: ', t( 'view', 'View' ) ); ?>
                <select name="category" data-modal-category>
                    <option value=""><?php te( 'show_all', 'All' ); ?></option>
                    <?php if( isset( $_POST['multi'] ) && $_POST['multi'] === 'true' ) {
                        echo '<option value="selected">' . t( 'gallery_selected_only', 'Selected only' ) . '</option>';
                    }
                    $custom_categories = value_with_filter( 'gallery-categories' );
                    foreach( $custom_categories as $cat_id => $cat_name ) {
                        echo '<option value="' . $cat_id . '"' . ( !empty( $_POST['category'] ) && $_POST['category'] == $cat_id ? ' selected' : '' ) . '>' . $cat_name . '</option>';
                    } ?>
                </select>
                <input name="search" value="" placeholder="<?php te( 'gallery_search_input', 'Search images' ); ?>" type="search" data-modal-search>
            </span>
        </div>

        <div class="modal-upload-container">
            <span><?php te( 'gallery_select_file', 'Browse or drag images here' ); ?></span>
            <form action="#" enctype="multipart/form-data">
                <input name="file[]" multiple="" type="file">
            </form>
        </div>

        <div class="modal-loader">
            <div class="a-alert"><?php te( 'please_wait', 'Please wait ...' ); ?></div>
        </div>
    </div>
</div>