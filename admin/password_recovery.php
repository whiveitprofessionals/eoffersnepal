<div class="password_recovery">

<div class="wrapper">

<?php

$form = '';

if( isset( $_GET['uid'] ) && isset( $_GET['session'] ) && \user\mail_sessions::check( 'password_recovery', array( 'user' => $_GET['uid'], 'session' => $_GET['session'] ) )) {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['forgot_password_form'] ) && isset( $_POST['forgot_password_form']['csrf'] ) && isset( $_SESSION['csrf']['forgot_password'] ) && $_POST['forgot_password_form']['csrf'] == $_SESSION['csrf']['forgot_password'] ) {

  $pd = \site\utils::validate_user_data( $_POST['forgot_password_form'] );

  try {

    \user\main::reset_password( $_GET['uid'], $pd );
    $form .= '<div class="success">' . t( 'reset_pwd_success', "Your password has been set, you can login now." ) . '</div>';

    \user\mail_sessions::clear( 'password_recovery', array( 'user' => $_GET['uid'] ) );

  }

  catch( Exception $e ){

    $form .= '<div class="error">' . $e->getMessage() . '</div>';

  }

}

$csrf = $_SESSION['csrf']['forgot_password'] = \site\utils::str_random(12);

$form .= '<form action="#" method="POST">
<input type="password" name="forgot_password_form[password1]" value="' . (isset( $pd['password1'] ) ? $pd['password1'] : '') . '" placeholder="' . t( 'change_pwd_form_new', "New Password" ) . '" required />
<input type="password" name="forgot_password_form[password2]" value="' . (isset( $pd['password2'] ) ? $pd['password2'] : '') . '" placeholder="' . t( 'change_pwd_form_new2', "Confirm New Password" ) . '" required />
<button>' . t( 'reset_pwd_button', "Reset Password" ) . '</button>
<input type="hidden" name="forgot_password_form[csrf]" value="' . $csrf . '" />
</form>';

} else {

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['forgot_password_form'] ) && isset( $_POST['forgot_password_form']['csrf'] ) && isset( $_SESSION['csrf']['forgot_password'] ) && $_POST['forgot_password_form']['csrf'] == $_SESSION['csrf']['forgot_password'] ) {

  $pd = \site\utils::validate_user_data( $_POST['forgot_password_form'] );

  try {

    \user\main::recovery_password( $_POST['forgot_password_form'], '../', 1 );
    $form .= '<div class="success">' . t( 'fp_success', "An email has been sent, please check your inbox!" ) . '</div>';

  }

  catch( Exception $e ){

    $form .= '<div class="error">' . $e->getMessage() . '</div>';

  }

}

$csrf = $_SESSION['csrf']['forgot_password'] = \site\utils::str_random(12);

$form .= '<form action="#" method="POST">
<input type="text" name="forgot_password_form[email]" value="' . (isset( $pd['email'] ) ? $pd['email'] : '') . '" placeholder="' . t( 'form_email', "Email Address" ) . '" required />
<button>' . t( 'recovery', "Recovery" ) . '</button>
<input type="hidden" name="forgot_password_form[csrf]" value="' . $csrf . '" />
</form>';

}

echo $form;

?>

<div style="margin: 20px 0 0 0; text-align: center;">
<a href="?">&#8592; <?php echo t( 'login', "Login" ); ?></a>
</div>

</div>

<div class="links">

<a href="../">&#8592; <?php echo sprintf( t( 'visit_site', "Visit %s" ), \query\main::get_option( 'sitename' ) ); ?></a>
<a href="http://couponsCMS.com">CouponsCMS.com</a>

</div>

</div>