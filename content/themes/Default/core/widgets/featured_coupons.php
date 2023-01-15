<?php if( isset( $extra['coupons'] ) && is_array( $extra['coupons'] ) && have_items_custom( ( $filter = array( 'show' => 'all', 'ids' => implode( ',', array_values( $extra['coupons'] ) ) ) ) ) ) {
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
<div class="featured-coupons-container widget-featured-coupons owl-widget<?php echo ( !$mobile_view ? ' mobile_view' : '' ); ?>">
<?php if( !empty( $title ) ) {
  echo '<h3 class="text-center-m">' . $title . '</h3>';
} ?>

<?php $coupons = items_custom( $filter ); ?>

<div class="owl-carousel coupons-carousel"<?php echo ( !empty( $atts ) ? ' ' . implode( ' ', $atts ) : '' ); ?>>

    <?php foreach( $coupons as $coupon ) { ?>

    <div class="item">
        <a href="<?php echo $coupon->link; ?>">
            <img src="<?php echo store_avatar( ( !empty( $coupon->image ) ? $coupon->image : $coupon->store_img ) ); ?>" alt="" />
            <h4><?php echo $coupon->title; ?></h4>
        </a>
    </div>

    <?php } ?>

</div>

</div>

<?php } ?>