<div class="sign_in">

<div class="wrapper">

<?php

echo do_action( 'ap_before_login_form' );

$form = '';

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['login_form'] ) && isset( $_POST['login_form']['csrf'] ) && isset( $_SESSION['csrf']['login'] ) && $_POST['login_form']['csrf'] == $_SESSION['csrf']['login'] ) {

  $pd = \site\utils::validate_user_data( $_POST['login_form'] );

  try {

    $session = \user\main::login( $pd, 1 );

    $_SESSION['session'] = $session;

    $form .= '<div class="success">' . t( 'login_success', "You have successfully logged in." ) . '</div>';
    $form .= '<meta http-equiv="refresh" content="1; url=../setSession.php?back=' . base64_encode( ADMINDIR ) . '">';

  }

  catch( Exception $e ){

    $form .= '<div class="error">' . $e->getMessage() . '</div>';

    }

}

$csrf = $_SESSION['csrf']['login'] = \site\utils::str_random(12);

echo $form;

?>

<form action="#" method="POST">
<input type="text" name="login_form[username]" value="<?php echo (isset( $pd['username'] ) ? esc_html( $pd['username'] ) : ''); ?>" placeholder="<?php echo t( 'form_email', "Email Address" ); ?>" required />
<input type="password" name="login_form[password]" placeholder="<?php echo t( 'form_password', "Password" ); ?>" required />
<button><?php echo t( 'login', "Login" ); ?></button>
<span style="float: right"><input type="checkbox" name="login_form[keep_logged]" id="login_form[keep_logged]" /> <label for="login_form[keep_logged]"><span></span> <?php echo t( 'msg_keep_log', "Keep me logged!" ); ?></label></span>
<input type="hidden" name="login_form[csrf]" value="<?php echo $csrf; ?>" />
</form>

<div style="margin: 20px 0 0 0; text-align: center;">
<a href="?action=password_recovery"><?php echo t( 'forgot_password', "Forgot your password?" ); ?></a>
</div>

<?php echo do_action( 'ap_after_login_form' ); ?>

</div>

<div class="links">
<a href="../">&#8592; <?php echo sprintf( t( 'visit_site', "Visit %s" ), \query\main::get_option( 'sitename' ) ); ?></a>
<a href="http://couponsCMS.com">CouponsCMS.com</a>
</div>

</div>