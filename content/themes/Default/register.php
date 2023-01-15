<div class="container pt50 pb50">

<div class="row">

    <div class="col-md-12 text-center title">
        <h2><?php te( 'theme_register_title', 'Create Account' ); ?></h2>
    </div>

</div>

<div class="row pt50 pb50">

    <div class="col-md-8 offset-md-2">
        <?php echo register_form( tlink( 'user/account' ) ); ?>
    </div>

</div>

<?php if( ( $facebook_login = facebook_login() ) || google_login() ) { ?>

<div class="row pb50">

    <div class="col-md-12 text-center">
        <?php if( $facebook_login ) { ?>
          <a href="<?php echo tlink( 'plugin/facebook_login' ); ?>" class="icon-button icon-border"><i class="fa fa-facebook"></i><span><?php te( 'theme_register_with_facebook', 'Register with Facebook' ); ?></span></a>
        <?php } if( google_login() ) { ?>
          <a href="<?php echo tlink( 'plugin/google_login' ); ?>" class="icon-button icon-border"><i class="fa fa-google"></i><span><?php te( 'theme_register_with_google', 'Register with Google+' ); ?></span></a>
        <?php } ?>
    </div>

</div>

<?php } ?>

<div class="row">

    <div class="col-md-12 text-center">
        <a href="<?php echo tlink( 'tpage/login' ); ?>"><?php te( 'theme_already_have_account', 'Already have an account' ); ?></a>
    </div>

</div>

</div>