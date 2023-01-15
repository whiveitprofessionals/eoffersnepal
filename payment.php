<?php

/** START SESSION */

session_start();

/** REPORT ALL PHP ERRORS */

error_reporting( E_ALL );

/** REQUIRE SETTINGS */

require_once 'settings.php';

/** CONNECT TO DB */

require_once IDIR . '/site/db.php';

if( ( !isset( $db ) || ( $db_conn = $db->connect_errno ) ) && is_dir( 'install' ) ) {

    require_once 'install/index.php';
    die;

} else if( $db_conn ) {

    die('Failed to connect to MySQL (' . $db->connect_errno . ') ' . $db->connect_error);

}

$db->set_charset( DB_CHARSET );

/** */

spl_autoload_register(function ( $cn ) {

    $type = strstr( $cn, '\\', true );

    if( $type == 'plugin' ) {
        $cn = str_replace( '\\', '/', $cn );
        if( file_exists( ( $file = DIR . '/' . UPDIR . '/' . substr( $cn, strpos( $cn, '/' )+1 ) . '.php' ) ) )
        require_once $file;
    } else {
        if( file_exists( ( $file = DIR . '/' . IDIR . '/' . str_replace( '\\', '/', $cn ) . '.php' ) ) )
        require_once $file;
    }

});

/** */

$load = new \main\load;

if( !$GLOBALS['me'] || !\query\payments::plan_exists( $_GET['plan'], array( 'user_view' => '' ) ) ) {
    header( 'Location: index.php' );
    die;
}

$plan = \query\payments::plan_info( $_GET['plan'] );

try {

$gateway = ( isset( $_GET['gateway'] ) ? $_GET['gateway'] : '' );

$payment = new \payment\main( $gateway );

$payment->description = 'Purchase plan';
$payment->items[] = array( $plan->name, $plan->description, 1, $plan->price );

if( isset( $_POST['pay_direct'] ) ) {

    try {

    // redirect URLs, used for PayPal, but can be used for other other gateways also

    $payment->success_url = $GLOBALS['siteURL'] . "payment.php?gateway={$payment->gateway_name}&plan={$_GET['plan']}";
    $payment->cancel_url = $GLOBALS['siteURL'] . "payment.php?gateway={$payment->gateway_name}&plan={$_GET['plan']}";

    $answer = $payment->direct();

    // save transaction

    \query\payments::inset_payment( array( $GLOBALS['me']->ID, $payment->gateway_name, $answer['total'], $answer['id'], $answer['state'], @serialize( $answer['items'] ), $answer['details'], 0, 0 ) );

    // save token

    $_SESSION['payment_direct_token'] = $answer['id'];

    if( isset( $answer['href'] ) ) {
        header( 'Location: ' . $answer['href'] );
        die;
    }

    }

    catch( Exception $e ) {
        $pay_direct_errmsg = '<div class="error">' . $e->getMessage() . '</div>';
    }

}

$thegateway = $payment->gateway_name;

    echo '<!DOCTYPE html>

    <html>
        <head>

            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
            <meta name="robots" content="noindex, nofollow">
            <title>' . t( 'payments_metatitle', "Buy Credits" ) . '</title>
            <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
            <script src="' . MISCDIR . '/pay.js"></script>
            <link href="//fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900" rel="stylesheet" />
            <link href="' . MISCDIR . '/pay.css" media="all" rel="stylesheet" />

        </head>

    <body>

    <div class="msg">';

    if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['token'] ) && \site\utils::check_csrf( $_POST['token'], 'payment_csrf' ) ) {

    if( isset( $_POST['pay_direct'] ) ) {

    echo $pay_direct_errmsg;

    } else if( $payment->do_credit_card() && isset( $_POST['pay_credit_card'] ) ) {

    if( empty( $_POST['card']['type'] ) ) {
        echo '<div class="error">' . t( 'payments_msg_invseltype', "Please select the type of credit card." ) . '</div>';
    } else if( !( isset( $_POST['card']['name'] ) && ( $card_name = $_POST['card']['name'] ) &&    preg_match( '/^([a-zA-Z\']{2,})\s+([a-zA-Z\']{2,})(\s+([a-zA-Z\' ]+))?/', $card_name, $card_name_a ) ) ) {
        unset( $_POST['card']['name'] );
        echo '<div class="error">' . t( 'payments_msg_invnamecard', "Please fill the name on credit card" ) . '</div>';
    } else if( !( isset( $_POST['card']['number'] ) && ( $card_number = preg_replace( '/\s+/', '' , $_POST['card']['number'] ) ) && preg_match( '/^([0-9]{14,16})$/', $card_number ) ) ) {
        unset( $_POST['card']['number'] );
        echo '<div class="error">' . t( 'payments_msg_invnumber', "Please fill the credit card number." ) . '</div>';
    } else if( empty( $_POST['card']['month'] ) || empty( $_POST['card']['year'] ) ) {
        echo '<div class="error">' . t( 'payments_msg_invexp', "Please select the expiration date for this credit card." ) . '</div>';
    } else if( !( isset( $_POST['card']['cvv'] ) && preg_match( '/^([0-9]{3,4})$/', $_POST['card']['cvv'] ) ) ) {
        unset( $_POST['card']['cvv'] );
        echo '<div class="error">' . t( 'payments_msg_invcvv', "The CVV is the 3(4)-digit number on the back of your credit card." ) . '</div>';
    } else {

    $payment->cc_type = $_POST['card']['type'];
    $payment->cc_first_name = $card_name_a[1];
    $payment->cc_last_name = $card_name_a[2];
    $payment->cc_number = $card_number;
    $payment->cc_emonth = $_POST['card']['month'];
    $payment->cc_eyear = $_POST['card']['year'];
    $payment->cc_cvv = $_POST['card']['cvv'];

    try {

    $answer = $payment->credit_card();

    unset( $_POST );

    echo '<div class="success">' . t( 'payments_msg_confirmed', "Payment confirmed, thank you !" ) . '</div>';

    /*

    Action after purchase, add credits or something ...

    */

    // add user credits

    $delivered = \user\update::add_credits( $GLOBALS['me']->ID, $plan->credits );

    // save transaction

    // userID, gateway, amount paid, transcationID, state, items on invoice, details, paid, delivered

    \query\payments::inset_payment( array( $GLOBALS['me']->ID, $payment->gateway_name, $answer['total'], $answer['id'], $answer['state'], @serialize( $answer['items'] ), $answer['details'], 1, $delivered ) );

    }

    catch( Exception $e ) {

        // show getMessage() or just show an error message for all exceptions

        echo '<div class="error">' . t( 'payments_msg_error_cc', "We couldn't complete the transaction, verify the information, then try again." ) . '<br />' . $e->getMessage() . '</div>';

    }

    }

    }

    } else if( ( $payment_direct_token = $payment->execute_direct_payment() ) && isset( $_SESSION['payment_direct_token'] ) && $_SESSION['payment_direct_token'] = $payment_direct_token ) {

    unset( $_SESSION['payment_direct_token'] );

    try {

    $answer = $payment->execute_payment();

    echo '<div class="success">' . t( 'payments_msg_confirmed', "Payment confirmed, thank you !" ) . '</div>';

    /*

    Action after purchase, add credits or something ...

    */

    // add user credits

    $delivered = \user\update::add_credits( $GLOBALS['me']->ID, $plan->credits );

    // update transaction

    // state, userID, paid, delivered, transactionID

    \query\payments::update_payment( array( $answer['state'], $GLOBALS['me']->ID, 1, $delivered, $answer['id'] ) );

    }

    catch( Exception $e ) {
        echo '<div class="error">' . $e->getMessage() . '</div>';
    }

    }

    $csrf = $_SESSION['payment_csrf'] = \site\utils::str_random(10);

    echo '<div class="table">';

    echo '<section>

    <h2>' . t( 'payments_title_info', "Information" ) . '</h2>

    <ul class="table2">
    <li><span>' . t( 'form_price', "Price" ) . ':</span> <b>' . $plan->price_format . '</b></li>
    <li><span>' . t( 'form_plan', "Plan" ) . ':</span> <b>' . $plan->name . '</b></li>
    <li><span>' . t( 'form_credits', "Credits" ) . ':</span> <b>' . $plan->credits . '</b></li>
    <li><span>' . t( 'form_description', "Description" ) . ':</span> ' . $plan->description . '</li>
    </ul>

    </section>

    <section>';

    if( $docc = $payment->do_credit_card() ) {

    echo '<div class="pay-credt-card-form"' . ( isset( $_POST['credit_card'] ) ? ' style="display: block;"' : '' ) . '>

    <form method="POST" action="#" autocomplete="off">

    <ul class="table2">
    <li class="cardtype"><span>' . t( 'payments_form_cardtype', "Card Type" ) . ':</span>
    <div>';
    $sctd_type = isset( $_POST['card']['type'] ) ? $_POST['card']['type'] : 'visa';
    foreach( $payment->cards() as $id => $card ) {
        echo '<input type="radio" name="card[type]" value="' . $card['value'] . '" id="' . $id . '"' . ( $sctd_type == $card['value'] ? ' checked' : '' ) . ' /> <label for="' . $id . '"><img src="' .    $card['image'] . '" alt="*" style="height: 25px; width: 35px;" /></label> ';
    }
    echo '</div></li>
    <li><span>' . t( 'payments_form_nameoncard', "Name on Card" ) . ':</span> <div><input type="text" name="card[name]" value="' . ( isset( $_POST['card']['name'] ) ? esc_html( $_POST['card']['name'] ) : '' ) . '" placeholder="' . t( 'payments_nameoncard_ph', "FirstName LastName" ) . '" required /></div></li>
    <li><span>' . t( 'payments_form_cardnumber', "Card Number" ) . ':</span> <div><input type="text" name="card[number]" value="' . ( isset( $_POST['card']['number'] ) ? esc_html( $_POST['card']['number'] ) : '' ) . '" required /></div></li>
    <li><span>' . t( 'payments_form_cardexp', "Expiration" ) . ':</span> <div>
    <select name="card[month]" style="width: 47%;">
    <option value="0">' . t( 'month', "Month" ) . '</option>';
    $sctd_month = isset( $_POST['card']['month'] ) ? $_POST['card']['month'] : '';
    for( $i = 1; $i <= 12; $i++ ) {
        echo '<option value="' . $i . '"' . ( $i == $sctd_month ? ' selected' : '' ) . '>' . sprintf( '%02d', $i ) . '</option>';
    }
    echo '</select>
    <select name="card[year]" style="width: 46%; margin-left: 2%;">
    <option value="0">' . t( 'year', "Year" ) . '</option>';
    $sctd_year = isset( $_POST['card']['year'] ) ? $_POST['card']['year'] : '';
    for( $i = date( 'Y' ); $i < date( 'Y' ) + 15; $i++ ) {
        echo '<option value="' . $i . '"' . ( $i == $sctd_year ? ' selected' : '' ) . '>' . $i . '</option>';
    }
    echo '</select>
    </div></li>
    <li><span>' . t( 'payments_form_cvv', "CVV2" ) . ':</span> <div><input type="text" name="card[cvv]" value="' . ( isset( $_POST['card']['cvv'] ) ? esc_html( $_POST['card']['cvv'] ) : '' ) . '" maxlength="4" required /></div></li>

    <li><span></span> <div><button>' . t( 'payments_paynow_button', "Pay now" ) . '</button></div></li>

    </ul>

    <input type="hidden" name="credit_card" />
    <input type="hidden" name="pay_credit_card" />
    <input type="hidden" name="token" value="' . $csrf . '" />

    </form>

    </div>';

    }

    echo '<div class="pay-buttons"' . ( isset( $_POST['credit_card'] ) && $docc ? ' style="display: none;"' : '' ) . '>

    <h2>' . t( 'payments_choosetopay', "Choose to pay with" ) . ':</h2>

    <form method="POST" action="#" class="buttons">';
    if( $docc ) {
        echo '<button name="credit_card"><img src="' . DEFAULT_IMAGES_LOC . '/visa.svg" alt="" style="width: 50px; height: 30px; display: block; padding: 10px 0;" /></button>';
    }
    if( $payment->do_direct() ) {
        echo '<button name="pay_direct"><img src="' . $payment->gateway_info['image'] . '" alt="" style="width: 50px; height: 30px; display: block; padding: 10px 0;" /></button>';
    }
    echo '<input type="hidden" name="token" value="' . $csrf . '" />
    </form>

    <div class="choose-gateway">
    ' . t( 'payments_choosetopay', "Choose to pay with" ) . ':
    <select name="gateway" style="width: auto;">';
    foreach( \site\payment::gateways() as $id => $gateway ) {
        echo '<option value="payment.php?gateway=' . $id . '&amp;plan=' . $_GET['plan'] . '"' . ( $thegateway == $id ? ' selected' : '' ) . '>' . $gateway['name'] . '</option>';
    }
    echo '</select>
    </div>

    </div>';

    echo '</section>

    </div>

    <a href="index.php">' . t( 'cancel', "Cancel" ) . '</a>

    </div>

    </body>
    </html>';

}

catch( Exception $e ) {
    echo $e->getMessage();
}

$load->page_load_after();

$db->close();