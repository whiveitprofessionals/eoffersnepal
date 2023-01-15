<?php if( isset( $extra['stores'] ) && is_array( $extra['stores'] ) && have_stores_custom( ( $filter = array( 'show' => 'all', 'ids' => implode( ',', array_values( $extra['stores'] ) ) ) ) ) ) {
    $atts = array();
    if( isset( $extra['autoplay'] ) && (boolean) $extra['autoplay'] ) {
        $atts[] = 'data-autoplay';
    }
    if( isset( $extra['loop'] ) && (boolean) $extra['loop'] ) {
        $atts[] = 'data-loop';
    }
    if( isset( $extra['arrows'] ) && (boolean) $extra['arrows'] ) {
        $atts[] = 'data-arrows';
    }
    if( isset( $extra['bullets'] ) && (boolean) $extra['bullets'] ) {
        $atts[] = 'data-bullets';
    }
?>
<div class="featured-stores-container widget-featured-stores owl-widget<?php echo ( !$mobile_view ? ' mobile_view' : '' ); ?>">
<?php if( !empty( $title ) ) {
  echo '<h3 class="text-center-m">' . $title . '</h3>';
} ?>

<?php $stores = stores_custom( $filter ); ?>

<div class="owl-carousel stores-carousel"<?php echo ( !empty( $atts ) ? ' ' . implode( ' ', $atts ) : '' ); ?>>

    <?php foreach( $stores as $store ) { ?>

    <div class="item">
        <a href="<?php echo $store->link; ?>">
            <img src="<?php echo store_avatar( $store->image ); ?>" alt="" />
            <h4><?php echo $store->name; ?></h4>
        </a>
    </div>

    <?php } ?>

</div>

</div>

<?php } ?>