<?php

$types              = array();
$types['all']       = array( 'label' => t( 'theme_all_stores', 'All Stores' ),  'url' => get_remove( array( 'page', 'type', 'firstchar' ) ),                                                'orderby' => 'name',        'firstchar' => true,    'show' => 'visible' );
$types['top']       = array( 'label' => t( 'theme_top_stores', 'Top Stores' ),  'url' => get_update( array( 'type' => 'top' ), get_remove( array( 'page', 'type', 'firstchar' ) ) ),        'orderby' => 'rating desc', 'firstchar' => false,   'show' => 'visible',       'limit' => 50 );
$types['most-voted']= array( 'label' => t( 'theme_most_voted', 'Most Voted' ),  'url' => get_update( array( 'type' => 'most-voted' ), get_remove( array( 'page', 'type', 'firstchar' ) ) ), 'orderby' => 'votes desc',  'firstchar' => false,   'show' => 'visible',       'limit' => 50 );
$types['popular']   = array( 'label' => t( 'theme_most_popular', 'Popular' ),   'url' => get_update( array( 'type' => 'popular' ), get_remove( array( 'page', 'type', 'firstchar' ) ) ),    'orderby' => 'votes desc',  'firstchar' => false,   'show' => 'visible,popular','limit' => 50 );

$type = isset( $_GET['type'] ) && in_array( $_GET['type'], array_keys( $types ) ) ? $_GET['type'] : 'all';

$atts = array();

if( isset( $_GET['firstchar'] ) && $types[$type]['firstchar'] ) {
    $atts['firstchar'] = substr( $_GET['firstchar'], 0, 1 );
}

$atts['show'] = $types[$type]['show'];

if( isset( $types[$type]['limit'] ) ) {
    $atts['limit'] = $types[$type]['limit'];
}

have_items( $atts );

?>

<div class="container pt50 pb50">

<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <h2><?php te( 'stores', 'Stores' ); ?></h2>
            <?php echo '<span>' . $types[$type]['label'] . ( !results() ? ' - ' . t( 'theme_no_stores_yet', 'No stores yet.' ) : '' ) . '</span>'; ?>
        </div>
    </div>
</div>

<div class="row mb15">

    <div class="col-md-12 text-center-m">
        <ul class="button-set">
            <?php foreach( $types as $type_id => $type_nav ) {
                echo '<li' . ( $type_id == $type ? ' class="selected"' : '' ) . '><a href="' . $type_nav['url'] . '">' . $type_nav['label'] . '</a></li>';
            } ?>
        </ul>
    </div>

</div>

<?php if( $types[$type]['firstchar'] ) { ?>

<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <ul class="letters">
                <?php foreach( ( range( 'A', 'Z' ) + array( 27 => '0-9' ) )  as $char ) {
                    echo '<li' . ( isset( $_GET['firstchar'] ) && $_GET['firstchar'] == $char ? ' class="selected"' : '' ) . '><a href="' . get_update( array( 'firstchar' => $char ), tlink( 'stores' ) ) . '">' . $char . '</a></li>';
                }
                echo '<li><a href="' . get_remove( array( 'firstchar' ) ) . '">' . t( 'theme_all', 'All' ) . '</a></li>'; ?>
            </ul>
        </div>
    </div>
</div>

<?php } ?>

<div class="row">
    <div class="col-md-8">
        <?php if( results() ) {
            foreach( items( ( array( 'orderby' => $types[$type]['orderby'] ) + $atts ) ) as $item ) {
                echo couponscms_store_item( $item );
            }

            echo couponscms_theme_pagination( navigation() );

        } else echo '<div class="alert">' . t( 'theme_no_stores_list',  'Huh :( No stores found here.' ) . '</div>'; ?>
    </div>

    <div class="col-md-4">
        <?php echo do_action( 'before_widgets_right' );
        echo show_widgets( 'right' );
        echo do_action( 'after_widgets_right' ); ?>
    </div>
</div>

</div>