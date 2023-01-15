<?php

namespace user;

/** */

class main {

/* USER LOGIN */

public static function is_logged() {

    global $db;

    if( !isset( $_COOKIE['user-session'] ) ) {

        return false;

    } else {

        $stmt = $db->stmt_init();

        $stmt->prepare( "SELECT user FROM " . DB_TABLE_PREFIX . "sessions WHERE session = ?" );
        $stmt->bind_param( "s", $_COOKIE['user-session'] );
        $stmt->bind_result( $id );
        $stmt->execute();
        $stmt->fetch();

    if( !empty( $id ) ) {

        $stmt->prepare( "SELECT name, email, avatar, points, credits, ipaddr, privileges, erole, subscriber, last_login, (SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores WHERE user = u.id), visits, valid, ban, extra, date FROM " . DB_TABLE_PREFIX . "users u WHERE id = ?" );
        $stmt->bind_param( "i", $id );
        $stmt->bind_result( $name, $email, $avatar, $points, $credits, $ip, $privileges, $erole, $subscriber, $last_login, $stores, $visits, $valid, $ban, $extra, $date );
        $stmt->execute();
        $stmt->fetch();

        // update action
        $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET points = IF(last_action < DATE(NOW()), points + ?, points), last_action = NOW() WHERE id = ?" );
        $daily_points = \query\main::get_option( 'u_points_davisit' );
        $stmt->bind_param( "ii", $daily_points, $id );
        $stmt->execute();

        $stmt->close();

        return (object) array( 'ID' => $id, 'Name' => esc_html( $name ), 'Email' => esc_html( $email ), 'Avatar' => esc_html( $avatar ), 'Points' => $points, 'Credits' => $credits, 'IP' => esc_html( $ip ), 'Privileges' => $privileges, 'Erole' => @unserialize( $erole ), 'Last_login' => $last_login, 'Stores' => $stores, 'Visits' => $visits, 'Extra' => @unserialize( $extra ), 'Date' => $date, 'is_subscribed' => $subscriber, 'is_confirmed' => $valid, 'is_banned' => ( strtotime( $ban ) > time() ? true : false ), 'is_subadmin' => ( $privileges >= 1 ? true : false ), 'is_admin' => ( $privileges > 1 ? true : false ) );

    } else {

        $stmt->close();

        return false;

    }

    }

}

/* BANNED */

public static function banned( $type = '', $IP = '' ) {

    global $db;

    switch( $type ) {

        case 'registration':
            $stmt = $db->stmt_init();
            $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "banned WHERE ipaddr = ? AND registration = 1" );
            $userip = empty( $IP ) ? \site\utils::getIP() : $IP;
            $stmt->bind_param( "s", $userip );
            $stmt->execute();
            $stmt->bind_result( $count );
            $stmt->fetch();
            $stmt->close();
            if( $count > 0 ) return true;
            return false;
        break;

        case 'login':
            $stmt = $db->stmt_init();
            $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "banned WHERE ipaddr = ? AND login = 1" );
            $userip = empty( $IP ) ? \site\utils::getIP() : $IP;
            $stmt->bind_param( "s", $userip );
            $stmt->execute();
            $stmt->bind_result( $count );
            $stmt->fetch();
            $stmt->close();
            if( $count > 0 ) return true;
            return false;
        break;

        default:
            $stmt = $db->stmt_init();
            $stmt->prepare( "SELECT id, redirect_to FROM " . DB_TABLE_PREFIX . "banned WHERE ipaddr = ? AND site = 1 AND ( expiration = 0 OR ( expiration = 1 AND expiration_date > NOW() ) )" );
            $userip = empty( $IP ) ? \site\utils::getIP() : $IP;
            $stmt->bind_param( "s", $userip );
            $stmt->execute();
            $stmt->bind_result( $id, $new_location );
            $stmt->fetch();
            $stmt->close();
            if( !empty( $id ) ) return $new_location;
            return false;
        break;

    }

    return false;

}

/* USER LOGOUT */

public static function logout() {

    global $db;

    if( !isset( $_COOKIE['user-session'] ) ) {

        return false;

    } else {

        $stmt = $db->stmt_init();

        $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "sessions WHERE session = ?" );
        $stmt->bind_param( "s", $_COOKIE['user-session'] );
        $execute = $stmt->execute();
        $stmt->close();

        if( $execute ) {
            return true;
        }

        return false;

    }

}

/* USER LOGIN */

public static function login( $post, $privileges = 0 ) {

    global $db;

    $session = '';

    if( self::banned( 'login' ) ) {
        throw new \Exception( t( 'msg_banned', "Sorry, but this action isn't permitted for you at this time." ) );
    } else if( ( $custom_error = value_with_filter( 'user_login_error', false, $post ) ) ) {
        throw new \Exception( $custom_error );
    } else {

        $stmt = $db->stmt_init();
        $stmt->prepare( "SELECT id, password, ban FROM " . DB_TABLE_PREFIX . "users WHERE email = ? AND privileges >= ?" );
        $stmt->bind_param( "si", $post['username'], $privileges );
        $stmt->execute();
        $stmt->bind_result( $id, $password, $ban );
        $stmt->fetch();

        if( empty( $id ) ) {

            // user does not even exist

            $stmt->close();

            throw new \Exception( t( 'login_invalid', "Login details are invalid." ) );

        } else if( strtotime( $ban ) > time() ) {

            // banned user

            $stmt->close();

            throw new \Exception( t( 'login_banaccount', "Your account seems to be banned for security reasons, often for failed login attempts. Please try later." ) );

        } else if( (string)$password !== (string) md5( $post['password'] ) ) {

            // wrong password

            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET fail_attempts = IF(fail_attempts >= " . BAN_AFTER_ATTEMPTS . ", 1, fail_attempts + 1), ban = IF(fail_attempts >= " . BAN_AFTER_ATTEMPTS . ", DATE_ADD(NOW(), INTERVAL " . BAN_AFTER_FAIL . " MINUTE), ban) WHERE email = ?" );
            $stmt->bind_param( "s", $post['username'] );
            $stmt->execute();
            $stmt->close();

            throw new \Exception( t( 'login_invalid', "Login details are invalid." ) );

        } else {

            $session = md5( \site\utils::str_random(15) );

            // delete old sessions
            $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "sessions WHERE user = ?" );
            $stmt->bind_param( "i", $id );
            $stmt->execute();

            // insert new session
            $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "sessions SET user = ?, session = ?, expiration = DATE_ADD(NOW(), INTERVAL " . ( isset( $post['keep_logged'] ) ? DEF_USER_SESSION_KL : DEF_USER_SESSION ) . " MINUTE), date = NOW()" );
            $stmt->bind_param( "is", $id, $session );

            if( !$stmt->execute() ) {

                $stmt->close();

                throw new \Exception( t( 'msg_error', "Error!" ) );

            } else {

                $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET ipaddr = ?, last_login = NOW(), visits = visits + 1, fail_attempts = 0 WHERE id = ?" );

                $userip = \site\utils::getIP();

                $stmt->bind_param( "si", $userip, $id );

                if( $stmt->execute() ) {
                    do_action( 'user-login', $id );
                }

                $stmt->close();

            }

        }

    }

    return $session;

}

/* USER REGISTER */

public static function register( $post ) {

    global $db;

    $session = '';

    $max_acc = (int) \query\main::get_option( 'accounts_per_ip' );

    if( $max_acc !== 0 && (int) \query\main::users( array( 'ip' => \site\utils::getIP() ) ) >= $max_acc ) {
        throw new \Exception( t( 'msg_error', "Error!" ) ); // administrator don't allow that manny accounts
    } else if( self::banned( 'registration' ) ) {
        throw new \Exception( t( 'msg_banned', "Sorry, but this action isn't permitted for you at this time." ) );
    } else if( !isset( $post['email'] ) || !filter_var( $post['email'], FILTER_VALIDATE_EMAIL ) ) {
        throw new \Exception( t( 'register_usevalide', "Please use a valid email address." ) );
    } else if( !isset( $post['username'] ) ) {
        throw new \Exception( t( 'register_complete_name', "Please fill the name." ) );
    } else if( !preg_match( '/(^[a-zA-Z0-9 ]{3,25}$)/', $post['username'] ) ) {
        throw new \Exception( t( 'register_invalid_name', "The name should not contain special characters, not less than 3 and no more than 25 characters." ) );
    } else if( !isset( $post['password'] ) || !isset( $post['password2'] ) ) {
        throw new \Exception( t( 'register_paswdreq', "Both passwords are required." ) );
    } else if( substr_count( $post['password'], ' ' ) ) {
        throw new \Exception( t( 'register_invalid_paswd', "Password should not contain spaces, not less than 5 and no more than 40 characters." ) );
    } else if( $post['password'] != $post['password2'] ) {
        throw new \Exception( t( 'register_passwdnm', "Passwords do not match!" ) );
    } else if( ( $custom_error = value_with_filter( 'user_register_error', false, $post ) ) ) {
        throw new \Exception( $custom_error );
    } else {

    if( !( $session = self::insert_user( $post ) ) ) {
        throw new \Exception( t( 'register_accexists', "This email address already exists." ) );
    }

    return $session;

    }

}

/* INSERT USER */

public static function insert_user( $info = array(), $autologin = false, $autovalid = false ) {

    global $db;

    $stmt = $db->stmt_init();

    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "users (name, email, password, points, ipaddr, last_action, valid, refid, extra, date) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, NOW())" );

    $passwd = isset( $info['password'] ) ? md5( $info['password'] ) : md5( \site\utils::str_random(15) );
    $points = (int) \query\main::get_option( 'u_def_points' );
    $IPaddr = \site\utils::getIP();
    $valid = (int) ( $autovalid ? 1 : (boolean) \query\main::get_option( 'u_confirm_req' ) );
    $refid = isset( $_COOKIE['referrer'] ) ? (int) $_COOKIE['referrer'] : 0;
    $extra = @serialize( ( isset( $info['extra'] ) ? array_filter( $info['extra'] ) : array() ) );

    $stmt->bind_param( "sssssiis", $info['username'], $info['email'], $passwd, $points, $IPaddr, $valid, $refid, $extra );
    $execute = $stmt->execute();
    $insert_id = $stmt->insert_id;

    if( !$execute && !$autologin ) {

        $stmt->close();

        return false;

    } else {

        if( $execute ) {
            do_action( 'user-registered', $insert_id );
        }

        $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "users WHERE email = ?" );
        $stmt->bind_param( "s", $info['email'] );
        $stmt->execute();
        $stmt->bind_result( $id );
        $stmt->fetch();

        $session = md5( \site\utils::str_random(15) );

        $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "sessions SET user = ?, session = ?, expiration = DATE_ADD(NOW(), INTERVAL " . DEF_USER_SESSION . " MINUTE), date = NOW()" );
        $stmt->bind_param( "is", $id, $session );
        $stmt->execute();

        $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET last_login = NOW(), visits = 1 WHERE id = ?" );
        $stmt->bind_param( "i", $id );
        $stmt->execute();

        if( !$valid ) {

            $cofirm_session = md5( \site\utils::str_random(15) );
            if( \user\mail_sessions::insert( 'confirmation', array( 'user' => $id, 'session' => $cofirm_session ) ) )
            \site\mail::send( $info['email'], t( 'email_acc_title', "Activate account" ) . ' - ' . \query\main::get_option( 'sitename' ), array( 'template' => 'account_confirmation' ), array( 'hello_name' => sprintf( t( 'email_text_hello', "Hello %s" ), $info['username'] ), 'confirmation_main_text' => t( 'email_acc_maintext', "Click on the link bellow to confirm your account." ), 'confirmation_button' => t( 'email_acc_button', "Activate account!" ), 'link' => \site\utils::update_uri( $GLOBALS['siteURL'] . 'verify.php', array( 'user' => $id, 'token' => $cofirm_session ) ) ) );

        } else if( $valid && $refid !== 0 ) {

            // add points to user who referred the new user
            \user\update::add_points( $refid, \query\main::get_option( 'u_points_refer' ) );

        }

        $stmt->close();

        return $session;

    }

    return true;

}

/* USER RECOVERY PASSWORD */

public static function recovery_password( $post, $path = '', $privileges = 0 ) {

    global $db;

    if( !isset( $post['email'] ) || !filter_var( $post['email'], FILTER_VALIDATE_EMAIL ) ) {
        throw new \Exception( t( 'register_usevalide', "Please use a valid email address." ) );
    } else {

        $stmt = $db->stmt_init();
        $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "users WHERE email = ? AND privileges >= ?" );
        $stmt->bind_param( "si", $post['email'], $privileges );
        $stmt->bind_result( $user );
        $execute = $stmt->execute();
        $stmt->fetch();
        $stmt->close();

        if( !$execute || empty( $user ) ) {
            throw new \Exception( t( 'fp_unkwacc', "Sorry, we couldn't find this account in our database." ) );
        } else {

            $session = md5( \site\utils::str_random(15) );

            if( \user\mail_sessions::insert( 'password_recovery', array( 'user' => $user, 'session' => $session ) ) ) {

                // send email
                if( \site\mail::send( $post['email'], t( 'email_reset_title', "Reset your password" ) . ' - ' . \query\main::get_option( 'sitename' ), array( 'template' => 'password_reset', 'path' => $path ), array( 'reset_main_text' => t( 'email_reset_maintext', "Click on the link bellow to reset your password." ), 'reset_button' => t( 'email_reset_button', "Reset password!" ), 'link' => \site\utils::update_uri( '', array( 'uid' => $user, 'session' => $session ) ) ) ) )

                return true;

            }

            throw new \Exception( t( 'msg_error', "Error!" ) );

        }

    }

}

/* RESET PASSWORD */

public static function reset_password( $id, $post ) {

    global $db;

    if( !isset( $post['password1'] ) || substr_count( $post['password1'], ' ' ) ) {
        throw new \Exception( t( 'reset_pwd_wrong_np', "Password should not contain spaces, not less than 5 and no more than 40 characters." ) );
    } else if( !isset( $post['password1'] ) || !isset( $post['password2'] ) || $post['password1'] != $post['password2'] ) {
        throw new \Exception( t( 'reset_pwd_pwddm', "Passwords do not match!" ) );
    } else {

        $stmt = $db->stmt_init();
        $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET password = ? WHERE id = ?" );

        $password = md5( $post['password1'] );

        $stmt->bind_param( "si", $password, $id );
        $execute = $stmt->execute();
        $stmt->close();

        if( !$execute ) throw new \Exception( t( 'msg_error', "Error!" ) );

    }

}

/* CHANGE PASSWORD */

public static function change_password( $id, $post ) {

    global $db;

    if( !isset( $post['new'] ) || substr_count( $post['new'], ' ' ) ) {
        throw new \Exception( t( 'change_pwd_wrong_np', "Password should not contain spaces, not less than 5 and no more than 40 characters." ) );
    } else if( !isset( $post['new'] ) || !isset( $post['new2'] ) || $post['new'] != $post['new2'] ) {
        throw new \Exception( t( 'change_pwd_pwddm', "Passwords do not match!" ) );
    } else {

        $stmt = $db->stmt_init();
        $stmt->prepare( "SELECT password FROM " . DB_TABLE_PREFIX . "users WHERE id = ?" );
        $stmt->bind_param( "i", $id );
        $stmt->bind_result( $password );
        $stmt->execute();
        $stmt->fetch();

        if( md5( $post['old'] ) == $password ) {

        $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET password = ? WHERE id = ?" );

        $new = md5( $post['new'] );

        $stmt->bind_param( "si", $new, $id );
        $execute = $stmt->execute();
        $stmt->close();

        if( $execute ) {

            return true;

        } else throw new \Exception( t( 'msg_error', "Error!" ) );

        } else {

            $stmt->close();

            throw new \Exception( t( 'change_pwd_wrongpwd', "Your current password it's wrong!" ) );

        }

    }

}

/* EDIT PROFILE */

public static function edit_profile( $id, $post ) {

    global $db;

    if( !isset( $post['username'] ) ) {
        throw new \Exception( t( 'profile_complete_name', "Please fill the name." ) );
    } else if( !preg_match( '/(^[a-zA-Z0-9 ]{3,25}$)/', $post['username'] ) ) {
        throw new \Exception( t( 'profile_invalid_name', "The name should not contain special characters, not less than 3 and no more than 25 characters." ) );
    } else {

        $avatar = \site\images::upload( $_FILES['data_avatar'], 'avatar_', array( 'max_size' => 1024, 'max_width' => 600, 'max_height' => 600, 'current' => $GLOBALS['me']->Avatar ) );

        $stmt = $db->stmt_init();
        $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET name = ?, avatar = ?, subscriber = ?, extra = ? WHERE id = ?" );

        $subscriber = ( isset( $post['subscriber'] ) ? 1 : 0 );
        $extra = @serialize( array_filter( $post['extra'] ) );

        $stmt->bind_param( "ssisi", $post['username'], $avatar, $subscriber, $extra, $id );
        $execute = $stmt->execute();
        $stmt->close();

        if( $execute ) {

            do_action( 'user-profile-edited', $id );

            return (object) array( 'avatar' => $avatar );

        } else {

            throw new \Exception( t( 'msg_error', "Error!" ) );

        }

    }

}

/* WRITE REVIEW */

public static function write_review( $id, $user, $post ) {

    global $db;

    if( !( $allow = (int) \query\main::get_option( 'allow_reviews' ) ) || !isset( $post['stars'] ) || !in_array( $post['stars'], array( 1,2,3,4,5 ) ) ) {
        throw new \Exception( t( 'msg_error', "Error!" ) ); // this error can appear only when the user try to modify post data OR administrator don't allow new reviews
    } else if( $allow === 2 && !$GLOBALS['me']->is_confirmed ) {
        throw new \Exception( t( 'review_write_notv', "Your account isn't confirmed, you can't write reviews." ) );
    } else if( !isset( $post['text'] ) || trim( $post['text'] ) == '' ) {
        throw new \Exception( t( 'review_write_text', "Please fill a message." ) );
    } else {

        $stmt = $db->stmt_init();
        $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "reviews (user, store, text, stars, valid, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())" );

        $valid = (boolean) \query\main::get_option( 'review_validate' );

        $stmt->bind_param( "iisiii", $user, $id, $post['text'], $post['stars'], $valid, $user );
        $execute = $stmt->execute();

        if( $execute ) {

            do_action( 'user-write-review', array( 'id' => $id, 'user' => $user ) );

            if( ( $ppr = \query\main::get_option( 'u_points_review' ) ) > 0 ) {

                $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET points = points + ? WHERE id = ?" );
                $stmt->bind_param( "ii", $ppr, $user );
                $stmt->execute();

            }

            $stmt->close();

            return true;

        } else {

            throw new \Exception( t( 'msg_error', "Error!" ) );

        }

    }

}

/* ADD STORE TO FAVORITES */

public static function favorite( $id, $store, $action = '' ) {

    global $db;

    if( empty( $action ) ) {
        if( self::check_favorite( $id, $store ) ) {
            $action = 'remove';
        } else {
            $action = 'add';
        }
    }

    if( $action == 'add' ) {

        if( !self::check_favorite( $id, $store ) ) {

            $stmt = $db->stmt_init();
            $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "favorite (user, store, date) VALUES (?, ?, NOW())" );
            $stmt->bind_param( "ii", $id, $store );
            $execute = $stmt->execute();
            $stmt->close();

            if( $execute ) {
                return 'added';
            }

        }

    } else if( $action == 'remove' ) {

        $stmt = $db->stmt_init();
        $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "favorite WHERE user = ? AND store = ?" );
        $stmt->bind_param( "ii", $id, $store );
        $execute = $stmt->execute();
        $stmt->close();

        if( $execute ) {
            return 'removed';
        }

    }

    return false;

}

/* SAVE STORE/COUPON OR PRODUCT */

public static function save( $id, $item, $type, $action = '' ) {

    global $db;

    if( !in_array( $type, array( 'store',  'coupon', 'product' ) ) ) {
        return false;
    }

    if( empty( $action ) ) {
        if( self::check_saved( $id, $item, $type ) ) {
            $action = 'unsave';
        } else {
            $action = 'save';
        }
    }

    if( $action == 'save' ) {

        if( !self::check_saved( $id, $item, $type ) ) {

            $stmt = $db->stmt_init();
            $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "saved (user, item, type, date) VALUES (?, ?, ?, NOW())" );
            $stmt->bind_param( "iis", $id, $item, $type );
            $execute = $stmt->execute();
            $stmt->close();

            if( $execute ) {
                return 'saved';
            }

        }

    } else if( $action == 'unsave' ) {

        $stmt = $db->stmt_init();
        $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "saved WHERE user = ? AND item = ? AND type = ?" );
        $stmt->bind_param( "iis", $id, $item, $type );
        $execute = $stmt->execute();
        $stmt->close();

        if( $execute ) {
            return 'unsaved';
        }

    }

    return false;

}

/* VOTE A COUPON */

public static function vote( $id, $vote ) {

    global $db;

    $allow = (int) \query\main::get_option( 'allow_votes' );

    if( $allow === 0 ) {
        throw new \Exception( t( 'x', 'x' ) );
    } else if( $allow === 2 && !$GLOBALS['me'] ) {
        throw new \Exception( t( 'y', 'y' ) );
    } else if( !\query\main::item_exists( $id ) ) {
        throw new \Exception( t( 'unexpected', 'Unexpected' ) );
    }

    $user_ip = \site\utils::getIP();

    if( self::check_vote_ip( $id, $user_ip ) ) {

        throw new \Exception( t( 'msg_already_voted', 'Already voted' ) );

    } else {

        $stmt = $db->stmt_init();
        $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "coupon_votes (coupon, user, vote, ipaddr, date) VALUES (?, ?, ?, ?, NOW())" );

        $user_id = $GLOBALS['me'] ? $GLOBALS['me']->ID : 0;

        $stmt->bind_param( "iiis", $id, $user_id, $vote, $user_ip );

        if( $stmt->execute() ) {

            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET votes = votes+1, votes_percent = ? WHERE id = ?" );

            $item = \query\main::item_info( $id );
            $new_percent = ( $item->votes_percent * $item->votes + ( (boolean) $vote ? 100 : 0 ) ) / ( $item->votes + 1 );

            $stmt->bind_param( "di", $new_percent, $id );
            $stmt->execute();
            $stmt->close();

            return true;

        } else throw new \Exception( t( 'unexpected', 'Unexpected' ) );

    }

}

/* CLAIM COUPON */

public static function claim_coupon( $id, $user ) {

    if( !\query\main::item_exists( $id ) ) {
        return false;
    }

    $info = \query\main::item_info( $id );

    if( !$info->is_show_in_store ) {
        return false;
    }

    if( $info->claim_limit !== 0 && $info->claims >= $info->claim_limit ) {
        return false;
    }

    global $db;

    if( !self::check_coupon_claimed( $user, $id ) ) {

        $stmt = $db->stmt_init();
        $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "coupon_claims (coupon, user, code, date) VALUES (?, ?, ?, NOW())" );
        $code = \site\utils::str_random( 6, true, false, true );
        $stmt->bind_param( "iis", $id, $user, $code );
        $execute = $stmt->execute();

        if( $execute )  {
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET claims = claims + 1 WHERE id =  ?" );
            $stmt->bind_param( "i", $id );
            $stmt->execute();
            $stmt->close();

            return 'claimed';
        }

        $stmt->close();

    }

    return false;

}

/* CHECK IF A STORE IS FAVORITE */

public static function check_favorite( $id, $store ) {

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "favorite WHERE user = ? AND store = ?" );
    $stmt->bind_param( "ii", $id, $store );
    $stmt->bind_result( $count );
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();

    if( $count > 0 ) {
        return true;
    }

    return false;

}

/* CHECK IF A STORE/COUPON OR PRODUCT IS SAVED */

public static function check_saved( $id, $item, $type ) {

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "saved WHERE user = ? AND item = ? AND type = ?" );
    $stmt->bind_param( "iis", $id, $item, $type );
    $stmt->bind_result( $count );
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();

    if( $count > 0 ) {
        return true;
    }

    return false;

}

/* CHECK IF A COUPON HAS BEEN CLAIMED */

public static function check_coupon_claimed( $uid, $item ) {

    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, coupon, user, code, used, used_date, date FROM " . DB_TABLE_PREFIX . "coupon_claims WHERE coupon = ? AND user = ?" );
    $stmt->bind_param( "ii", $item, $uid );
    $stmt->bind_result( $id, $coupon, $user, $code, $used, $user_date, $date );
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();

    if( $id ) {
        return (object) array( 'ID' => $id, 'code' => $code, 'couponID' => $coupon, 'userID' => $user, 'used' => $used, 'used_date' => $user_date, 'date' => $date );
    }

    return false;

}

/* CHECK IF USER (BY IP) VOTED A COUPON */

public static function check_vote_ip( $id, $ip, $self_ip = false ) {

    global $db;

    if( $self_ip ) {
        $ip = \site\utils::getIP();
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupon_votes WHERE coupon = ? AND ipaddr = ?" );
    $stmt->bind_param( "is", $id, $ip );
    $stmt->bind_result( $count );
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();

    if( $count > 0 ) {
        return true;
    }

    return false;

}

/* SUGGEST STORE */

public static function suggest_store( $id, $post, $intent ) {

    global $db;

    $post = array_map( 'trim', $post );

    if( !isset( $post['intent'] ) || !in_array( $post['intent'], array_keys( $intent ) ) ) {
        throw new \Exception( t( 'msg_error', "Error!" ) ); // this error can appear only when user try to modify post data
    } else if( !isset( $post['name'] ) || trim( $post['name'] ) == '' ) {
        throw new \Exception( t( 'suggestion_pwn', "Please fill a name for this store/brand." ) );
    } else if( !isset( $post['url'] ) || !filter_var( $post['url'], FILTER_VALIDATE_URL ) ) {
        throw new \Exception( t( 'suggestion_wrong_url', "Website address seems to be invalid." ) );
    } else if( !isset( $post['description'] ) || strlen( $post['description'] ) < 10 ) {
        throw new \Exception( t( 'suggestion_shdesc', "Please fill a short description about the store/brand you want to suggest." ) );
    } else {

        $stmt = $db->stmt_init();
        $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "suggestions (user, type, name, url, description, message, date) VALUES (?, ?, ?, ?, ?, ?, NOW())" );

        $stmt->bind_param( "iissss", $id, $post['intent'], $post['name'], $post['url'], $post['description'], $post['message'] );
        $execute = $stmt->execute();
        $stmt->close();

        if( $execute ) {
            return true;
        }

        throw new \Exception( t( 'msg_error', "Error!" ) );

    }

}

/* SUBMIT COUPON */

public static function submit_coupon( $post ) {

    global $db;

    $post = \site\utils::array_map_recursive( 'trim', $post );

    if( !isset( $post['type'] ) || !isset( $post['store'] ) || !\query\main::have_store( $post['store'], $GLOBALS['me']->ID ) ) {
        throw new \Exception( t( 'msg_error', "Error!" ) );    // this error can appear only when user try to modify post data
    } else if( !isset( $post['name'] ) || trim( $post['name'] ) == ''    ) {
        throw new \Exception( t( 'submit_cou_writename', "Please fill a name for this coupon." ) );
    } else if( !isset( $post['url'] ) || !empty( $post['url'] ) && !preg_match( '/(^http(s)?:\/\/)/', $post['url'] ) ) {
        throw new \Exception( t( 'submit_cou_writeurl', "Please fill a good URL for this coupon/deal or leave it blank." ) );
    } else if( !isset( $post['description'] ) || strlen( $post['description'] ) < 10 ) {
        throw new \Exception( t( 'submit_cou_writedesc', "Please fill a short description about the coupon that you want to submit." ) );
    } else if( !isset( $post['end'] ) || !isset( $post['end_hour'] ) || strtotime( $post['end'] ) < strtotime( 'today' ) ) {
        throw new \Exception( t( 'submit_cou_wrong_ed', "End date must be greater than today." ) );
    } else {

        $end = date( 'Y-m-d H:i:s', strtotime( $post['end'] . ' ' . $post['end_hour'] ) );

        if( !empty( $post['sponsored'] ) && (int) $post['sponsored'] >= 1 && (int) $post['sponsored'] <= 30 ) {
            $days       = (int) $post['sponsored'];
            $prices     = prices( 'object' );
            $paid_until = time();
            $paid_until += $days * ( 24 * 3600 );
            $cost       = $prices->coupon * $days;
        } else {
            $paid_until = time();
            $cost       = 0;
        }

        if( $GLOBALS['me']->Credits < $cost ) {
            throw new \Exception( sprintf( t( 'msg_notenoughpoints', "You need %s credits for this. Now you have only %s credits." ), $cost, $GLOBALS['me']->Credits ) );
        }

        $image = \site\images::upload( $_FILES['data_image'], 'coupon_', array( 'current' => '' ) );

        $stmt = $db->stmt_init();
        $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "coupons (user, store, category, printable, show_in_store, available_online, title, link, description, tags, image, code, source, claim_limit, visible, start, expiration, lastupdate_by, lastupdate, paid_until, extra, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), FROM_UNIXTIME(?), ?, NOW())" );

        $start = date( 'Y-m-d H:i:s', strtotime( $post['start'] . ' ' . $post['start_hour'] ) );

        // save cost until
        $valid = (boolean) \query\main::get_option( 'coupon_validate' );

        $printable      = $limit = 0;
        $avbl_online    = 1;
        $source         = '';
        $show_in_store  = false;

        switch( (int) $post['type'] ) {
            case 1:
                $printable = 1;
            break;

            case 2:
                $printable = 1;
                $source = isset( $_FILES['submit_coupon_form_source'] ) ? \site\images::upload( $_FILES['submit_coupon_form_source'], 'print_', array( 'current' => '' ) ) : '';
            break;

            case 3:
            $show_in_store = true;
            if( !empty( $post['limit'] ) ) {
                $limit = (int) $post['limit'];
            }
            break;

            default:
                if( !isset( $post['avbl_online'] ) ) {
                    $post['code'] = $post['url'] = '';
                    $avbl_online = 0;
                }
            break;
        }

        $extra = @serialize( array_filter( $post['extra'] ) );

        $stmt->bind_param( "iiiiiisssssssiississ", $GLOBALS['me']->ID, $post['store'], $post['category'], $printable, $show_in_store, $avbl_online, $post['name'], $post['url'], $post['description'], $post['tags'], $image, $post['code'], $source, $limit, $valid, $start, $end, $GLOBALS['me']->ID, $paid_until, $extra );
        $execute = $stmt->execute();
        $insert_id = $stmt->insert_id;
        $stmt->close();

        if( $execute ) {

            // deduct credits

            \user\update::add_credits( $GLOBALS['me']->ID, -$cost );

            do_action( 'user-coupon-submitted', $insert_id );

            return true;

        }

        throw new \Exception( t( 'msg_error', "Error!" ) );

    }

}

/* EDIT COUPON */

public static function edit_coupon( $id, $post ) {

    global $db;

    $post = \site\utils::array_map_recursive( 'trim', $post );

    if( !isset( $post['type'] ) || !isset( $post['store'] ) || !\query\main::have_store( $post['store'], $GLOBALS['me']->ID ) ) {
        throw new \Exception( t( 'msg_error', "Error!" ) );    // this error can appear only when user try to modify post data
    } else if( !isset( $post['name'] ) || trim( $post['name'] ) == '' ) {
        throw new \Exception( t( 'edit_cou_writename', "Please fill a name for this coupon." ) );
    } else if( !isset( $post['url'] ) || !empty( $post['url'] ) && !preg_match( '/(^http(s)?:\/\/)/', $post['url'] ) ) {
        throw new \Exception( t( 'edit_cou_writeurl', "Please fill a good URL for this coupon/deal or leave it blank." ) );
    } else if( !isset( $post['description'] ) || strlen( $post['description'] ) < 10 ) {
        throw new \Exception( t( 'edit_cou_writedesc', "Please fill a short description about the coupon you want to edit." ) );
    } else {

        $end        = date( 'Y-m-d H:i:s', strtotime( $post['end'] . ' ' . $post['end_hour'] ) );
        $info       = \query\main::item_info( $id );
        $paid_until = $info->paid_until && strtotime( $info->paid_until ) > time() ? strtotime( $info->paid_until ) : ( time() - 1 );

        if( !empty( $post['sponsored'] ) && (int) $post['sponsored'] >= 1 && (int) $post['sponsored'] <= 30 ) {
            $days       = (int) $post['sponsored'];
            $prices     = prices( 'object' );
            $paid_until = $info->paid_until && strtotime( $info->paid_until ) > time() ? strtotime( $info->paid_until ) : time();
            $paid_until += $days * ( 24 * 3600 );
            $cost       = $prices->product * $days;
        } else {
            $cost       = 0;
        }

        if( $GLOBALS['me']->Credits < $cost ) {
            throw new \Exception( sprintf( t( 'msg_notenoughpoints', "You need %s credits for this. Now you have only %s credits." ), $cost, $GLOBALS['me']->Credits ) );
        }

        $image = \site\images::upload( $_FILES['data_image'], 'coupon_', array( 'current' => $info->image ) );

        $stmt = $db->stmt_init();
        $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET store = ?, category = ?, printable = ?, show_in_store = ?, available_online = ?, title = ?, link = ?, description = ?, tags = ?, image = ?, code = ?, source = ?, claim_limit = ?, start = ?, expiration = ?, lastupdate_by = ?, lastupdate = NOW(), paid_until = FROM_UNIXTIME(?), extra = ? WHERE id = ?" );

        $start = date( 'Y-m-d H:i:s', strtotime( $post['start'] . ' ' . $post['start_hour'] ) );

        $printable = $limit = 0;
        $avbl_online = 1;
        $source = '';
        $source_now = $info->is_local_source ? str_replace( $GLOBALS['siteURL'], '', $info->source ) : $info->source;;
        $show_in_store = false;

        if( $info->store_is_physical ) {

            switch( (int) $post['type'] ) {
                case 1:
                    $printable = 1;
                    if( $info->is_local_source ) $delete_source = 1;
                break;

                case 2:
                    $printable = 1;
                    $source = isset( $_FILES['edit_coupon_form_source'] ) ? \site\images::upload( $_FILES['edit_coupon_form_source'], 'print_', array( 'current' => $source_now ) ) : $source_now;
                break;

                case 3:
                    $show_in_store = true;
                    if( !empty( $post['limit'] ) ) {
                        $limit = (int) $post['limit'];
                    }
                break;

                default:
                    if( !isset( $post['avbl_online'] ) ) {
                        $post['code'] = $post['url'] = '';
                        $avbl_online = 0;
                    }
                    if( $info->is_local_source ) $delete_source = 1;
                break;
            }

        }

        $extra = @serialize( array_merge( $info->extra, array_filter( $post['extra'] ) ) );

        $stmt->bind_param( "iiiiisssssssississi", $post['store'], $post['category'], $printable, $show_in_store, $avbl_online, $post['name'], $post['url'], $post['description'], $post['tags'], $image, $post['code'], $source, $limit, $start, $end, $GLOBALS['me']->ID, $paid_until, $extra, $id );
        $execute = $stmt->execute();
        $stmt->close();

        if( $execute ) {

            // delete source in case that needed

            if( isset( $delete_source ) ) {
                @unlink( DIR . '/' . $source_now );
            }

            // deduct credits

            \user\update::add_credits( $GLOBALS['me']->ID, -$cost );

            do_action( 'user-coupon-edited', $id );

            return true;

        }

        throw new \Exception( t( 'msg_error', "Error!" ) );

    }

}

/* SUBMIT PRODUCT */

public static function submit_product( $post ) {

    global $db;

    $post = \site\utils::array_map_recursive( 'trim', $post );

    if( !isset( $post['store'] ) || !\query\main::have_store( $post['store'], $GLOBALS['me']->ID ) ) {
        throw new \Exception( t( 'msg_error', "Error!" ) );    // this error can appear only when user try to modify post data
    } else if( !isset( $post['name'] ) || trim( $post['name'] ) == ''    ) {
        throw new \Exception( t( 'submit_prod_writename', "Please fill a name for this product." ) );
    } else if( !isset( $post['url'] ) || !empty( $post['url'] ) && !preg_match( '/(^http(s)?:\/\/)/', $post['url'] ) ) {
        throw new \Exception( t( 'submit_prod_writeurl', "Please fill a good URL for this product." ) );
    } else if( !isset( $post['description'] ) || strlen( $post['description'] ) < 10 ) {
        throw new \Exception( t( 'submit_prod_writedesc', "Please fill a short description about the product that you want to submit." ) );
    } else if( !isset( $post['end'] ) || !isset( $post['end_hour'] ) || strtotime( $post['end'] ) < strtotime( 'today' ) ) {
        throw new \Exception( t( 'submit_prod_wrong_ed', "End date must be greater than today." ) );
    } else {

        $end = date( 'Y-m-d H:i:s', strtotime( $post['end'] . ' ' . $post['end_hour'] ) );

        if( !empty( $post['sponsored'] ) && (int) $post['sponsored'] >= 1 && (int) $post['sponsored'] <= 30 ) {
            $days       = (int) $post['sponsored'];
            $prices     = prices( 'object' );
            $paid_until = time();
            $paid_until += $days * ( 24 * 3600 );
            $cost       = $prices->product * $days;
        } else {
            $paid_until = time();
            $cost       = 0;
        }

        if( $GLOBALS['me']->Credits < $cost ) {
            throw new \Exception( sprintf( t( 'msg_notenoughpoints', "You need %s credits for this. Now you have only %s credits." ), $cost, $GLOBALS['me']->Credits ) );
        }

        $stmt = $db->stmt_init();
        $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "products (user, store, category, title, link, description, tags, image, price, old_price, currency, visible, start, expiration, lastupdate_by, lastupdate, paid_until, extra, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), FROM_UNIXTIME(?), ?, NOW())" );

        $start = date( 'Y-m-d H:i:s', strtotime( $post['start'] . ' ' . $post['start_hour'] ) );

        $image = \site\images::upload( $_FILES['data_image'], 'product_', array( 'current' => '' ) );
        $valid = (boolean) \query\main::get_option( 'product_validate' );
        $extra = @serialize( array_filter( $post['extra'] ) );

        $stmt->bind_param( "iiisssssddsississ", $GLOBALS['me']->ID, $post['store'], $post['category'], $post['name'], $post['url'], $post['description'], $post['tags'], $image, $post['price'], $post['old_price'], $post['currency'], $valid, $start, $end, $GLOBALS['me']->ID, $paid_until, $extra );
        $execute = $stmt->execute();
        $insert_id = $stmt->insert_id;
        $stmt->close();

        if( $execute ) {

            // deduct credits

            \user\update::add_credits( $GLOBALS['me']->ID, -$cost );

            do_action( 'user-product-submitted', $insert_id );

            return true;

        }

        throw new \Exception( t( 'msg_error', "Error!" ) );

    }

}

/* EDIT PRODUCT */

public static function edit_product( $id, $post ) {

    global $db;

    $post = \site\utils::array_map_recursive( 'trim', $post );

    if( !isset( $post['store'] ) || !\query\main::have_store( $post['store'], $GLOBALS['me']->ID ) ) {
        throw new \Exception( t( 'msg_error', "Error!" ) );    // this error can appear only when user try to modify post data
    } else if( !isset( $post['name'] ) || trim( $post['name'] ) == '' ) {
        throw new \Exception( t( 'edit_prod_writename', "Please fill a name for this product." ) );
    } else if( !isset( $post['url'] ) || !empty( $post['url'] ) && !preg_match( '/(^http(s)?:\/\/)/', $post['url'] ) ) {
        throw new \Exception( t( 'edit_prod_writeurl', "Please fill a good URL for this product." ) );
    } else if( !isset( $post['description'] ) || strlen( $post['description'] ) < 10 ) {
        throw new \Exception( t( 'edit_prod_writedesc', "Please fill a short description about the product that you want to edit." ) );
    } else {

        $end        = date( 'Y-m-d H:i:s', strtotime( $post['end'] . ' ' . $post['end_hour'] ) );
        $info       = \query\main::product_info( $id );
        $paid_until = $info->paid_until && strtotime( $info->paid_until ) > time() ? strtotime( $info->paid_until ) : ( time() - 1 );

        if( !empty( $post['sponsored'] ) && (int) $post['sponsored'] >= 1 && (int) $post['sponsored'] <= 30 ) {
            $days       = (int) $post['sponsored'];
            $prices     = prices( 'object' );
            $paid_until = $info->paid_until && strtotime( $info->paid_until ) > time() ? strtotime( $info->paid_until ) : time();
            $paid_until += $days * ( 24 * 3600 );
            $cost       = $prices->product * $days;
        } else {
            $cost       = 0;
        }

        if( $GLOBALS['me']->Credits < $cost ) {
            throw new \Exception( sprintf( t( 'msg_notenoughpoints', "You need %s credits for this. Now you have only %s credits." ), $cost, $GLOBALS['me']->Credits ) );
        }

        $image = \site\images::upload( $_FILES['data_image'], 'product_', array( 'current' => $info->image ) );

        $stmt = $db->stmt_init();
        $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "products SET store = ?, category = ?, title = ?, link = ?, description = ?, tags = ?, image = ?, price = ?, old_price = ?, currency = ?, start = ?, expiration = ?, lastupdate_by = ?, lastupdate = NOW(), paid_until = FROM_UNIXTIME(?), extra = ? WHERE id = ?" );

        $start = date( 'Y-m-d H:i:s', strtotime( $post['start'] . ' ' . $post['start_hour'] ) );

        $extra = @serialize( array_merge( $info->extra, array_filter( $post['extra'] ) ) );

        $stmt->bind_param( "iisssssddsssissi", $post['store'], $post['category'], $post['name'], $post['url'], $post['description'], $post['tags'], $image, $post['price'], $post['old_price'], $post['currency'], $start, $end, $GLOBALS['me']->ID, $paid_until, $extra, $id );
        $execute = $stmt->execute();
        $stmt->close();

        if( $execute ) {

            // deduct credits

            \user\update::add_credits( $GLOBALS['me']->ID, -$cost );

            do_action( 'user-product-edited', $id );

            return (object) array( 'image' => $image );

        }

        throw new \Exception( t( 'msg_error', "Error!" ) );

    }

}

/* SUBMIT STORE */

public static function submit_store( $id, $post ) {

    global $db;

    $post = \site\utils::array_map_recursive( 'trim', $post );

    if( !isset( $post['name'] ) || !isset( $post['type'] ) || trim( $post['name'] ) === '' ) {
        throw new \Exception( t( 'submit_store_writename', "Please fill a name for this store/brand." ) );
    } else if( !isset( $post['url'] ) || ( (int) $post['type'] === 0 &&    !preg_match( '/(^http(s)?:\/\/)([a-zA-Z0-9-]{3,100}).([a-zA-Z]{2,12})/', $post['url'] ) ) ) {
        throw new \Exception( t( 'submit_store_wrongweb', "Website address seems to be invalid." ) );
    } else if( !isset( $post['description'] ) || strlen( $post['description'] ) < 10 ) {
        throw new \Exception( t( 'submit_store_writedesc', "Please fill a short description about the store/brand you want to edit." ) );
    } else {

        if( $GLOBALS['me']->Credits < ( $cost = (int) \query\main::get_option( 'price_store' ) ) ) {
            throw new \Exception( sprintf( t( 'msg_notenoughpoints', "You need %s credits for this. Now you have only %s credits." ), $cost, $GLOBALS['me']->Credits ) );
        }

        $logo = \site\images::upload( $_FILES['data_logo'], 'logo_', array( 'current' => '' ) );

        $stmt = $db->stmt_init();
        $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "stores (user, category, name, physical, link, description, tags, image, hours, phoneno, sellonline, visible, lastupdate_by, lastupdate, extra, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())" );

        // autovalidate this store?

        $valid = (boolean) \query\main::get_option( 'store_validate' );

        $hours = '';
        $phone = isset( $post['phone'] ) && preg_match( '/^[0-9\(\)\-\s]{3,20}$/', $post['phone'] ) ? $post['phone'] : '';
        $sellonline = isset( $post['sellonline'] ) ? 1 : 0;

        switch( (int) $post['type'] ) {
            case 1:
                if( !isset( $post['notb_hours'] ) && is_array( $post['hours'] ) ) {
                    $hours = @serialize( $post['hours'] );
                }
            break;

            default:
                $sellonline = 1;
            break;
        }

        $extra = @serialize( array_filter( $post['extra'] ) );

        $stmt->bind_param( "iisissssssiiis", $GLOBALS['me']->ID, $post['category'], $post['name'], $post['type'], $post['url'], $post['description'], $post['tags'], $logo, $hours, $phone, $sellonline, $valid, $GLOBALS['me']->ID, $extra );
        $execute = $stmt->execute();
        $insert_id = $stmt->insert_id;
        $stmt->close();

        if( $execute ) {

            // deduct credits

            \user\update::add_credits( $GLOBALS['me']->ID, -$cost );

            do_action( 'user-store-submitted', $insert_id );

            return (object) array( 'image' => $logo );

        }

        throw new \Exception( t( 'msg_error', "Error!" ) );

    }

}

/* EDIT STORE */

public static function edit_store( $id, $post ) {

    global $db;

    $post = \site\utils::array_map_recursive( 'trim', $post );

    if( !isset( $post['type'] ) || !\query\main::have_store( $id, $GLOBALS['me']->ID ) ) {
        throw new \Exception( t( 'msg_error', "Error!" ) );    // this error can appear only when user try to modify post data
    } else if( !isset( $post['name'] ) || trim( $post['name'] ) == '' ) {
        throw new \Exception( t( 'edit_store_writename', "Please fill a name for this store/brand." ) );
    } else if( !isset( $post['url'] ) || ( (int) $post['type'] === 0 && !preg_match( '/(^http(s)?:\/\/)([a-zA-Z0-9-]{3,100}).([a-zA-Z]{2,12})/', $post['url'] ) ) ) {
        throw new \Exception( t( 'edit_store_wrongweb', "Website address seems to be invalid." ) );
    } else if( !isset( $post['description'] ) || strlen( $post['description'] ) < 10 ) {
        throw new \Exception( t( 'edit_store_writedesc', "Please fill a short description about the store/brand you want to edit." ) );
    } else {

        $store = \query\main::store_info( $id );

        $logo = \site\images::upload( $_FILES['data_logo'], 'logo_', array( 'current' => $store->image ) );

        $stmt = $db->stmt_init();
        $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET category = ?, name = ?, physical = ?, link = ?, description = ?, tags = ?, image = ?, hours = ?, phoneno = ?, sellonline = ?, lastupdate_by = ?, lastupdate = NOW(), extra = ? WHERE id = ?" );

        $hours = '';
        $phone = isset( $post['phone'] ) && preg_match( '/^[0-9\(\)\-\s]{3,20}$/', $post['phone'] ) ? $post['phone'] : '';
        $sellonline = isset( $post['sellonline'] ) ? 1 : 0;

        switch( (int) $post['type'] ) {
            case 1:
                if( !isset( $post['notb_hours'] ) && is_array( $post['hours'] ) ) {
                    $hours = @serialize( $post['hours'] );
                }
            break;

            default:
                $sellonline = 1;
                if( $store->is_physical ) $delete_locations = 1;
            break;
        }

        $extra = @serialize( array_merge( $store->extra, array_filter( $post['extra'] ) ) );

        $stmt->bind_param( "isissssssiisi", $post['category'], $post['name'], $post['type'], $post['url'], $post['description'], $post['tags'], $logo, $hours, $phone, $sellonline, $GLOBALS['me']->ID, $extra, $id );
        if( $stmt->execute() ) {

            // delete locations in case that needed

            if( isset( $delete_locations ) ) {
                $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "store_locations WHERE store = ?" );
                $stmt->bind_param( "i", $id );
                $stmt->execute();
            }

            $stmt->close();

            do_action( 'user-store-updated', $id );

            return true;

        } else {

            $stmt->close();

            throw new \Exception( t( 'msg_error', "Error!" ) );

        }

    }

}

/* SUBMIT STORE LOCATION */

public static function submit_store_location( $post ) {

    global $db;

    $post = array_map( 'trim', $post );

    if( !isset( $post['store'] ) || !isset( $post['address'] ) || !isset( $post['zip'] ) || !isset( $post['country'] ) || !isset( $post['state'] ) || !isset( $post['city'] ) || !isset( $post['mapmarker'] ) || !\query\main::have_store( $post['store'], $GLOBALS['me']->ID ) ) {
        throw new \Exception( t( 'msg_error', "Error!" ) );    // this error can appear only when user try to modify post data
    } else {

        $stmt = $db->stmt_init();
        $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "store_locations (user, store, country, countryID, state, stateID, city, cityID, zip, address, lat, lng, point, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, POINT(?, ?), ?, NOW(), NOW())" );

        if( $country = \query\locations::country_exists( $post['country'] ) ) {
            list( $country_id, $country_name ) = array( $country['ID'], $country['name'] );
        } else {
            list( $country_id, $country_name ) = array( 0, $post['country'] );
        }

        if( $state = \query\locations::state_exists( $post['state'] ) ) {
            list( $state_id, $state_name ) = array( $state['ID'], $state['name'] );
        } else {
            list( $state_id, $state_name ) = array( 0, $post['state'] );
        }

        if( $city = \query\locations::city_exists( $post['city'] ) ) {
            list( $city_id, $city_name ) = array( $city['ID'], $city['name'] );
        } else {
            list( $city_id, $city_name ) = array( 0, $post['city'] );
        }

        $post['mapmarker'] = preg_replace( '/[(\]*\[\)]/', '$1', $post['mapmarker'] );
        $post['mapmarker'] = array_filter( explode( ',', $post['mapmarker'] ) );

        $stmt->bind_param( "iisisisissddddi", $GLOBALS['me']->ID, $post['store'], $country_name, $country_id, $state_name, $state_id, $city_name, $city_id, $post['zip'], $post['address'], $post['mapmarker'][0], $post['mapmarker'][1], $post['mapmarker'][0], $post['mapmarker'][1], $GLOBALS['me']->ID );
        $execute = $stmt->execute();
        $stmt->close();

        if( $execute ) {

            return true;

        } else {

            throw new \Exception( t( 'msg_error', "Error!" ) );

        }

    }

}

/* EDIT STORE LOCATION */

public static function edit_store_location( $id, $post ) {

    global $db;

    $post = array_map( 'trim', $post );

    if( !isset( $post['store'] ) || !isset( $post['address'] ) || !isset( $post['zip'] ) || !isset( $post['country'] ) || !isset( $post['state'] ) || !isset( $post['city'] ) || !isset( $post['mapmarker'] ) || !\query\main::have_store( $post['store'], $GLOBALS['me']->ID ) ) {
        throw new \Exception( t( 'msg_error', "Error!" ) );    // this error can appear only when user try to modify post data
    } else {

        $stmt = $db->stmt_init();
        $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "store_locations SET store = ?, country = ?, countryID = ?, state = ?, stateID = ?, city = ?, cityID = ?, zip = ?, address = ?, lat = ?, lng = ?, point = POINT(?, ?), lastupdate_by = ?, lastupdate = NOW() WHERE id = ?" );

        // country
        $country = \query\locations::country_info( $post['country'] );

        // state
        $state = \query\locations::state_info( $post['state'] );

        // city
        $city = \query\locations::city_info( $post['city'] );

        if( empty( $country->name ) || empty( $state->name ) || empty( $city->name ) ) {
            return false;
        }

        $post['mapmarker'] = preg_replace( '/[(\]*\[\)]/', '$1', $post['mapmarker'] );
        $post['mapmarker'] = array_filter( explode( ',', $post['mapmarker'] ) );

        $stmt->bind_param( "isisisissddddii", $post['store'], $country->name, $post['country'], $state->name, $post['state'], $city->name, $post['city'], $post['zip'], $post['address'], $post['mapmarker'][0], $post['mapmarker'][1], $post['mapmarker'][0], $post['mapmarker'][1], $GLOBALS['me']->ID, $id );
        $execute = $stmt->execute();
        $stmt->close();

        if( $execute ) {

        return true;

        } else {

        throw new \Exception( t( 'msg_error', "Error!" ) );

        }

    }

}

/* SUBSCRIBE */

public static function subscribe( $id, $post ) {

    global $db;

    $post = array_map( 'trim', $post );

    if( !isset( $post['email'] ) || !filter_var( $post['email'], FILTER_VALIDATE_EMAIL ) ) {
        throw new \Exception( t( 'newsletter_usevalide', "Please use a valid email address." ) );
    } else {

        $stmt = $db->stmt_init();
        $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "newsletter (email, ipaddr, date) VALUES (?, ?, NOW())" );

        $IP = \site\utils::getIP();

        $stmt->bind_param( "ss", $post['email'], $IP );
        $execute = $stmt->execute();
        $stmt->close();

        if( !$execute ) {
            throw new \Exception( t( 'newsletter_subscribed', "You are already subscribed to our newsletter!" ) );
        } else {

            do_action( 'user-subscribe', array( 'email' => $post['email'] ) );

            if( \query\main::get_option( 'subscr_confirm_req' ) ) {

                $session = md5( \site\utils::str_random(15) );

                if( \user\mail_sessions::insert( 'subscription', array( 'email' => $post['email'], 'session' => $session ) ) && \site\mail::send( $post['email'], t( 'email_sub_title', "Confirm subscription" ) . ' - ' . \query\main::get_option( 'sitename' ), array( 'template' => 'confirm_subscription' ), array( 'confirmation_main_text' => t( 'email_sub_maintext', "One more step left! Thank you for subscription to our newsletter, please confirm your subscription by clicking the link below." ), 'confirmation_button' => t( 'email_sub_button', "Confirm subscription!" ), 'link' => \site\utils::update_uri( $GLOBALS['siteURL'] . 'verify.php', array( 'action' => 'subscribe', 'email' => $post['email'], 'token' => $session ) ) ) ) )

                return 1;

                else {

                    // the email could not be sent, so delete it from the database

                    $stmt = $db->stmt_init();
                    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "newsletter WHERE email = ?" );
                    $stmt->bind_param( "s", $post['email'] );
                    $stmt->execute();
                    $stmt->close();

                    throw new \Exception( t( 'msg_error', "Error!" ) );

                }

            } else {

                // auto-validate the subscription

                $stmt = $db->stmt_init();
                $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "newsletter SET econf = 1 WHERE email = ?" );
                $stmt->bind_param( "s", $post['email'] );
                $stmt->execute();
                $stmt->close();

                if( $execute ) {

                    do_action( 'user-subscribe-confirmed', array( 'email' => $post['email'] ) );

                    return 2;

                }

                else

                throw new \Exception( t( 'msg_error', "Error!" ) );

            }

        }

    }

}

/* UNSUBSCRIBE */

public static function unsubscribe( $post ) {

global $db;

$post = array_map( 'trim', $post );

if( !isset( $post['email'] ) || !filter_var( $post['email'], FILTER_VALIDATE_EMAIL ) ) {
    throw new \Exception( t( 'newsletter_usevalide', "Please use a valid email address." ) );
} else {

    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "newsletter WHERE email = ?" );
    $stmt->bind_param( "s", $post['email'] );
    $stmt->bind_result( $count );
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();

    if( $count == 0 ) {

    throw new \Exception( t( 'uunsubscr_notsubscr', "You are not subscribed to our newsletter." ) );

    }

    if( (boolean) \query\main::get_option( 'unsubscr_confirm_req' ) ) {

    $session = md5( \site\utils::str_random(15) );

    if( \user\mail_sessions::insert( 'unsubscription', array( 'email' => $post['email'], 'session' => $session ) ) &&
    \site\mail::send( $post['email'], t( 'email_unsub_title', "Confirm unsubscription" ) . ' - ' . \query\main::get_option( 'sitename' ), array( 'template' => 'confirm_unsubscription' ), array( 'confirmation_main_text' => t( 'email_unsub_maintext', "You are not unsubscribed now. You can unsubscribe by clicking the link below, but please be sure you don't want to receive messages from us anymore." ), 'confirmation_button' => t( 'email_unsub_button', "Confirm unsubscription!" ), 'link' => \site\utils::update_uri( $GLOBALS['siteURL'] . 'verify.php', array( 'action' => 'unsubscribe2', 'email' => $post['email'], 'token' => $session ) ) ) ) )

    return 1;

    else
    throw new \Exception( t( 'msg_error', "Error!" ) );

    } else {

    // auto-unsubscribe

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "newsletter WHERE email = ?" );
    $stmt->bind_param( "s", $post['email'] );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) return 2;
    else
    throw new \Exception( t( 'msg_error', "Error!" ) );

    }

}

}

/* CLAIM A REWARD */

public static function get_reward( $id, $post ) {

global $db;

if( !$GLOBALS['me'] ) {
    throw new \Exception( t( 'msg_error', "Error!" ) );
}

if( !\query\main::reward_exists( $id, array( 'user_view' ) ) ) {
    throw new \Exception( t( 'claim_reward_dontexist', "This reward doesn't exists." ) );
} else if( ( $reward = \query\main::reward_info( $id ) ) && $reward->points > $GLOBALS['me']->Points ) {
    throw new \Exception( t( 'claim_reward_mrepts', "You don't have enough points to redeem this." ) );
} else {

    // check required fields

    foreach( $reward->fields as $field ) {
        if( (boolean) $field['require'] ) {

            switch( $field['type'] ) {

                case 'email':
                if( !isset( $post[$field['name']] ) || !filter_var( $post[$field['name']], FILTER_VALIDATE_EMAIL ) )
                throw new \Exception( t( 'claim_reward_reqinv', "Some of required fields are incomplete." ) );
                break;

                case 'number':
                if( !isset( $post[$field['name']] ) || !filter_var( $post[$field['name']], FILTER_VALIDATE_INT ) )
                throw new \Exception( t( 'claim_reward_reqinv', "Some of required fields are incomplete." ) );
                break;

                default:
                if( empty( $post[$field['name']] ) )
                throw new \Exception( t( 'claim_reward_reqinv', "Some of required fields are incomplete." ) );
                break;

            }
        }
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "rewards_reqs (name, user, points, reward, fields, lastupdate_by, lastupdate, claimed, date) VALUES (?, ?, ?, ?, ?, ?, NOW(), 0, NOW())" );

    $fields = @serialize( $post );

    $stmt->bind_param( "siiisi", $reward->title, $GLOBALS['me']->ID, $reward->points, $reward->ID, $fields, $GLOBALS['me']->ID );

    if( $stmt->execute() ) {

    // deduct points from this user
    \user\update::add_points( $GLOBALS['me']->ID, -$reward->points );

    $stmt->close();

    return true;

    } else {

    $stmt->close();

    throw new \Exception( t( 'msg_error', "Error!" ) );

    }

    }

}

/* CHECK COUPON CODE */

public static function check_coupon_code( $code ) {

    global $db;

    if( !$GLOBALS['me'] || !isset( $code ) || !( $coupon = \query\claims::coupon_code_exists( $code ) ) ) {
        throw new \Exception( t( 'msg_error', "Error!" ) );
    } else {
        $stmt = $db->stmt_init();
        $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupon_claims SET used = 1, used_date = NOW() WHERE id = ? AND used = 0" );

        $coupon_id = $coupon['ID'];

        $stmt->bind_param( "i", $coupon_id );
        $execute = $stmt->execute();
        $stmt->close();

        if( $execute ) {
            return $coupon;
        }

        throw new \Exception( t( 'msg_error', "Error!" ) );
    }

}

/* SET COUPON CODE UNUSED */

public static function set_coupon_code_unused( $code ) {

    global $db;

    if( !$GLOBALS['me'] || !( $coupon = \query\claims::coupon_code_exists( $code ) ) ) {
        throw new \Exception( t( 'msg_error', "Error!" ) );
    } else {

        $stmt = $db->stmt_init();
        $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupon_claims SET used = 0 WHERE id = ?" );

        $coupon_id = $coupon['ID'];

        $stmt->bind_param( "i", $coupon_id );
        $execute = $stmt->execute();
        $stmt->close();

        if( $execute ) {

        return true;

        } else {

        throw new \Exception( t( 'msg_error', "Error!" ) );

        }
    }

}

/* USER SEND MESSAGE VIA CONTACT FORM */

public static function send_contact( $post ) {

    global $db;

    if( empty( $post['name'] ) ) {
        throw new \Exception( t( 'sendcontact_complete_name', "Please fill the name." ) );
    } else if( !isset( $post['email'] ) || !filter_var( $post['email'], FILTER_VALIDATE_EMAIL ) ) {
        throw new \Exception( t( 'sendcontact_usevalide', "Please use a valid email address." ) );
    } else if( !isset( $post['message'] ) || strlen( $post['message'] ) < 10 ) {
        throw new \Exception( t( 'sendcontact_writemsg', "Please fill a short message for us." ) );
    } else {

        // send email
        if( \site\mail::send( \query\main::get_option( 'email_contact' ), t( 'email_sec_title', "Contact" ) . ' - ' . \query\main::get_option( 'sitename' ), array( 'template' => 'contact_form', 'reply_name' => $post['name'], 'reply_to' => $post['email'] ), array( 'name' => t( 'email_sec_name', "Name" ), 'c_name' => $post['name'], 'email' => t( 'email_sec_email', "Email" ), 'c_email' => $post['email'], 'c_msg' => $post['message'] ) ) ) {
            return true;
        }

        throw new \Exception( t( 'msg_error', "Error!" ) );

    }

}

}