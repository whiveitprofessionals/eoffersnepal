<?php

namespace site;

/** */

class language {

public static function languages() {

    $lang = array();

    // built-in languages

    // english
    $lang['english']['name'] = 'English';
    $lang['english']['image'] = $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/us_flag.svg';
    $lang['english']['location'] = DIR . '/' . LDIR . '/english.php';

    // romanian
    $lang['romanian']['name'] = 'Română';
    $lang['romanian']['image'] = $GLOBALS['siteURL'] . DEFAULT_IMAGES_LOC . '/ro_flag.svg';
    $lang['romanian']['location'] = DIR . '/' . LDIR . '/romanian.php';

    // user plugins
    foreach( \query\main::user_plugins( 'language' ) as $ulang ) {
        $basename = basename( $ulang->main_file, '.php' );
        $lang[$basename]['name'] = $ulang->name;
        $lang[$basename]['image'] = $GLOBALS['siteURL'] . $ulang->image;
        $lang[$basename]['location'] = DIR . '/' . UPDIR . '/' . $ulang->main_file;
    }
    
    return $lang;

}

}