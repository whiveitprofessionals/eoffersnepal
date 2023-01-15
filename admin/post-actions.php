<?php

if( isset( $_GET['action'] ) ) {

    switch( $_GET['action'] ) {

    case 'general-settings':

        if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'settings_csrf' ) ) {

            require_once DIR . '/' . IDIR . '/others/GMT_list.php';

            if( isset( $_POST['sitename'] ) && isset( $_POST['siteurl'] ) && isset( $_POST['description'] ) && isset( $_POST['ipp'] ) && isset( $_POST['maintenance'] ) && isset( $_POST['registrations'] ) && isset( $_POST['accounts_per_ip'] ) && isset( $_POST['delete_old_coupons'] ) && isset( $_POST['delete_old_products'] ) && isset( $_POST['delete_old_votes'] ) && isset( $_POST['allow_revs'] ) && isset( $_POST['auvalid_revs'] ) && isset( $_POST['allow_stores'] ) && isset( $_POST['allow_coupons'] ) && isset( $_POST['allow_votes'] ) && isset( $_POST['site_lang'] ) && isset( $_POST['adminpanel_lang'] ) && isset( $_POST['timezone'] ) && isset( $_POST['hour_format'] ) && isset( $_POST['email_from_name'] ) && isset( $_POST['email_answer_to'] ) && isset( $_POST['email_contact'] ) && isset( $_POST['mail_meth'] ) && isset( $_POST['smtp_host'] ) && isset( $_POST['smtp_port'] ) && isset( $_POST['smtp_user'] ) && isset( $_POST['smtp_pass'] ) && isset( $_POST['sendmail_path'] ) && isset( $_POST['admin_theme'] ) )

            $_SESSION['js_settings'] = true;

            if( admin\actions::set_option(
            array(
                'sitename' => $_POST['sitename'],
                'siteurl' => rtrim( $_POST['siteurl'], '/' ),
                'sitedescription' => $_POST['description'],
                'items_per_page' => (int) $_POST['ipp'],
                'maintenance' => (boolean) $_POST['maintenance'],
                'registrations' => $_POST['registrations'],
                'delete_old_coupons' => (int) $_POST['delete_old_coupons'],
                'delete_old_products' => (int) $_POST['delete_old_products'],
                'delete_old_votes' => (int) $_POST['delete_old_votes'],
                'accounts_per_ip' => (int) $_POST['accounts_per_ip'],
                'allow_reviews' => (int) $_POST['allow_revs'],
                'review_validate' => (boolean) $_POST['auvalid_revs'],
                'allow_stores' => (boolean) $_POST['allow_stores'],
                'store_validate' => (boolean) $_POST['auvalid_stos'],
                'allow_coupons' => (boolean) $_POST['allow_coupons'],
                'coupon_validate' => (boolean) $_POST['auvalid_cous'],
                'allow_products' => (boolean) $_POST['allow_products'],
                'allow_votes' => (int) $_POST['allow_votes'],
                'product_validate' => (boolean) $_POST['auvalid_prods'],
                'sitelang' => $_POST['site_lang'],
                'adminpanel_lang' => $_POST['adminpanel_lang'],
                'timezone' => ( in_array( $_POST['timezone'], array_keys( $gmt ) ) ? $_POST['timezone'] : 'America/New_York' ),
                'hour_format' => ( in_array( $_POST['hour_format'], array( 12, 24 ) ) ? $_POST['hour_format'] : 24 ),
                'email_from_name' => $_POST['email_from_name'],
                'email_answer_to' => $_POST['email_answer_to'],
                'email_contact' => $_POST['email_contact'],
                'mail_method' => $_POST['mail_meth'],
                'smtp_auth' => ( isset( $_POST['smtp_auth'] ) ? 1 : 0 ),
                'smtp_host' => $_POST['smtp_host'],
                'smtp_port' => $_POST['smtp_port'],
                'smtp_user' => $_POST['smtp_user'],
                'smtp_password' => $_POST['smtp_pass'],
                'sendmail_path' => $_POST['sendmail_path'],
                'admintheme' => $_POST['admin_theme'],
                'mail_signature' => $_POST['mailsign']
            ) ) ) {

                header( 'Location: ?route=settings.php&action=general&success=true' );
                die;

            } else {

                header( 'Location: ?route=settings.php&action=general&success=false' );
                die;

            }

        }

    break;

    case 'switch-theme':

        if( isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'themes_csrf' ) ) {

            if( isset( $_GET['id'] ) ) {

                $_SESSION['js_settings'] = true;

                if( !admin\template::theme_have_min( admin\template::theme_editor_map( $_GET['id'] ) ) ) {

                    header( 'Location: ?route=themes.php&action=activate&id=' . $_GET['id'] . '&success=false' );
                    die;

                }  
                
                $current_theme = theme();

                if( admin\actions::set_option( array( 'theme' => $_GET['id'] ) ) ) {

                    do_action( 'admin_theme_deactivated', $current_theme );

                    header( 'Location: ?route=themes.php&action=activate&id=' . $_GET['id'] . '&success=true' );
                    die;

                }

            }

        }

        header( 'Location: ?route=themes.php' );
        die;

    break;

    }

}

?>