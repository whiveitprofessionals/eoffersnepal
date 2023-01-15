<footer class="main">

<div class="container">

    <div class="row">

    <div class="col-md-3 text-center-m">
        <h5><?php te( 'coupons', 'Coupons' ); ?></h5>
        <ul class="flinks">
            <li><a href="<?php echo tlink( 'tpage/coupons', 'type=recent' ); ?>"><i class="fa fa-angle-right"></i> <?php te( 'theme_coupons_recently_added', 'Recently Added' ); ?></a></li>
            <li><a href="<?php echo tlink( 'tpage/coupons', 'type=expiring_soon' ); ?>"><i class="fa fa-angle-right"></i> <?php te( 'theme_coupons_expiring_soon', 'Expiring Soon' ); ?></a></li>
            <li><a href="<?php echo tlink( 'tpage/coupons', 'type=printable' ); ?>"><i class="fa fa-angle-right"></i> <?php te( 'theme_coupons_printable', 'Printable' ); ?></a></li>
            <li><a href="<?php echo tlink( 'tpage/coupons', 'type=codes' ); ?>"><i class="fa fa-angle-right"></i> <?php te( 'theme_coupons_codes', 'Coupon Codes' ); ?></a></li>
        </ul>
    </div>

    <div class="col-md-3 text-center-m">
    <h5><?php te( 'stores', 'Stores' ); ?></h5>
        <ul class="flinks">
            <li><a href="<?php echo tlink( 'stores' ); ?>"><i class="fa fa-angle-right"></i> <?php te( 'theme_all_stores', 'All Stores' ); ?></a></li>
            <li><a href="<?php echo tlink( 'stores', 'type=top' ); ?>"><i class="fa fa-angle-right"></i> <?php te( 'theme_top_stores', 'Top Stores' ); ?></a></li>
            <li><a href="<?php echo tlink( 'stores', 'type=most-voted' ); ?>"><i class="fa fa-angle-right"></i> <?php te( 'theme_most_voted', 'Most Voted' ); ?></a></li>
            <li><a href="<?php echo tlink( 'tpage/suggest' ); ?>"><i class="fa fa-angle-right"></i> <?php te( 'theme_make_a_suggestion', 'Make A Suggestion' ); ?></a></li>
        </ul>
    </div>

    <div class="col-md-3 text-center-m">
        <h5><?php te( 'theme_connect_with_us', 'Connect With Us' ); ?></h5>
        <ul class="flinks">
            <li><a href="<?php echo tlink( 'tpage/contact' ); ?>"><i class="fa fa-angle-right"></i> <?php te( 'theme_contact', 'Contact' ); ?></a></li>
            <?php $about_us = get_page_link( 1, true );
            if( $about_us ) echo '<li><a href="' . $about_us[0] . '"><i class="fa fa-angle-right"></i> ' . $about_us['info']->name . '</a></li>'; ?>
            <li><a href="<?php echo tlink( 'tpage/register' ); ?>"><i class="fa fa-angle-right"></i> <?php te( 'theme_register_button', 'Register' ); ?></a></li>
            <li><a href="<?php echo tlink( 'tpage/login' ); ?>"><i class="fa fa-angle-right"></i> <?php te( 'theme_login_button', 'Sign In' ); ?></a></li>
        </ul>
    </div>

    <div class="col-md-3 text-center-m">

        <section class="fo-newsletter mb15" id="footer_newsletter">
            <?php echo newsletter_form( '_footer_form', 'footer_newsletter' ); ?>
        </section>

        <?php

        $networks = social_networds();

        if( isset( $networks['myspace'] ) ) {
            unset( $networks['myspace'] );
        }

        if( !empty( $networks ) ) {

            echo '<h5>' . t( 'theme_social_networks', 'Social Networks' ) . '</h5>';

            echo '<ul class="social-links">';
            foreach( $networks as $name => $url ) {
                echo '<li><a href="' . esc_html( $url ) . '"><i class="fa fa-' . $name . '"></i></a></li>';
            }
            echo '</ul>';
        }

        ?>

    </div>

</div>

<div class="row pt0">

    <div class="col-md-12 text-center-m">
    <?php

    $site_desc = description();

    if( !empty( $site_desc ) ) {
        echo '<span class="site_desc">' . $site_desc . '</span>';
    }

    ?>
    </div>

</div>

</div>

</footer>

<footer class="footer-bottom">

<div class="container">

<div class="row">

    <div class="col-md-6 text-center-m">
        <?php echo site_name(); ?>
    </div>

    <div class="col-md-6 text-right text-center-m">
        Powered by <a href="//couponscms.com" target="_blank">CouponsCMS.com</a>
    </div>

</div>

</div>

</footer>

<script>
    var login_page = "<?php echo tlink( 'tpage/login' ); ?>";
</script>

<?php if( couponscms_has_local_stores() && google_maps() && ( this_is_store() || this_is_coupon() || this_is_product() ) ) { ?>
<script src="//maps.googleapis.com/maps/api/js?key=<?php echo esc_html( option( 'google_maps_key' ) ); ?>"></script>
<script src="<?php echo THEME_LOCATION; ?>/assets/js/map.js"></script>
<?php } ?>

</body>

</html>