<?php

$store = the_item();

?>

<div class="container pt50 pb50">

<?php echo do_action( 'store_reviews_before_info' ); ?>

<div class="row">
    <div class="col-md-12">
        <div class="widget item-text">
            <div class="avatar">
                <img src="<?php echo store_avatar( ( !empty( $store->image ) ? $store->image : '' ) ); ?>" alt="" />
            </div>
            <div class="info">
                <h2 class="clearfix"><?php tse( $store->name ); ?>
                    <?php if( !empty( $store->reviews ) ) { ?>
                        <div class="rating"><?php echo couponscms_rating( (int) $store->stars, $store->reviews ); ?></div>
                    <?php } ?>
                </h2>
                <span><?php te( 'theme_list_of_reviews', 'List of reviews received from members' ); ?></span>

                <ul class="links-list">
                    <li><a href="<?php echo $store->link; ?>"><i class="fa fa-arrow-left"></i> <?php te( 'theme_store_back_to', 'View Profile' ); ?></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php echo do_action( 'store_reviews_after_info' ); ?>

<div class="row">

<div class="col-md-8">
    <?php echo do_action( 'store_reviews_before_items' );

    if( ( $results = have_items() ) ) {

        foreach( items( array( 'orderby' => 'date desc' ) ) as $item ) {
            echo couponscms_review_item( $item );
        }

        echo couponscms_theme_pagination( $results );

    } else {

        echo '<div class="alert">' . sprintf( t( 'theme_no_reviews_store',  '%s has no reviews yet.' ), ts( $store->name ) ) . '</div>';

    }

    echo do_action( 'store_reviews_after_items' ); ?>
</div>

<div class="col-md-4">
    <div class="widget">
        <h2><?php te( 'theme_write_review', 'Write A Review' ); ?></h2>
        <?php echo write_review_form(); ?>
    </div>

    <?php echo do_action( 'before_widgets_right' );
    echo show_widgets( 'right' );
    echo do_action( 'after_widgets_right' ); ?>
</div>

</div>

</div>