<?php echo do_action( 'index_before_search' );
couponscms_search_form();
echo do_action( 'index_after_search' ); ?>

<div class="container pt50 pb50">

   <?php if( $featured_top_widgets = show_widgets( 'featured_top' ) ) { ?>
    <div class="row">
        <div class="col-md-12">
            <?php echo do_action( 'before_widgets_featured_top' );
            echo $featured_top_widgets;
            echo do_action( 'after_widgets_featured_top' ); ?>
        </div>
    </div>
    <?php } ?>

    <div class="row">
        <div class="col-md-8">

        <?php echo couponscms_home_items(); ?>

        </div>

        <div class="col-md-4">
            <?php echo do_action( 'before_widgets_right' );
            echo show_widgets( 'right' );
            echo do_action( 'after_widgets_right' ); ?>
        </div>
    </div>

   <?php if( $featured_bottom_widgets = show_widgets( 'featured_bottom' ) ) { ?>
    <div class="row">
        <div class="col-md-12 bottom-widgets">
            <?php echo do_action( 'before_widgets_featured_bottom' );
            echo $featured_bottom_widgets;
            echo do_action( 'after_widgets_featured_bottom' ); ?>
        </div>
    </div>
    <?php } ?>

</div>