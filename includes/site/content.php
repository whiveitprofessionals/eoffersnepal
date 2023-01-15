<?php

namespace site;

/** */

class content {

/* List of smilies */

public static function smilies() {

    $loc = $GLOBALS['siteURL'] . MISCDIR . '/smilies/';

    return array(
        ':angry:' => $loc . 'angry.png',
        ':h:' => $loc . 'heart.png',
        ':hb:' => $loc . 'broken-heart.png',
        ':confused:' => $loc . 'confused.png',
        ':cry:' => $loc . 'cry.png',
        ':dizzy:' => $loc . 'dizzy.png',
        ':happy:' => $loc . 'happy.png',
        ':laugh:' => $loc . 'laugh.png',
        ':lol:' => $loc . 'lol.png',
        ':neutral:' => $loc . 'neutral.png',
        ':sad:' => $loc . 'sad.png',
        ':shock:' => $loc . 'shock.png',
        ':smile:' => $loc . 'smile.png',
        ':tongue:' => $loc . 'tongue.png',
        ':wink:' => $loc . 'wink.png'
    );

}

/* List of filters */

public static function filters( $text = '', $place = '' ) {

    global $add_filters;

    if( isset( $add_filters[$place] ) && is_array( $add_filters[$place] ) ) {
        foreach( $add_filters[$place] as $callback ) {

            if( ( $callback = \site\utils::check_callback( $callback ) ) ) {

                ob_start();
                echo call_user_func( $callback, $text );
                $text = ob_get_contents();
                ob_end_clean();

            }

        }
    }

    return $text;

}

/* List of shortcodes */

public static function shortcodes( $text = '' ) {

    global $add_shortcodes;

    if( !empty( $add_shortcodes ) && is_array( $add_shortcodes ) ) {
        foreach( $add_shortcodes as $id => $callback ) {

            if( ( $callback = \site\utils::check_callback( $callback ) ) ) {

                preg_match_all( '/\[\b' . $id . '\b(.*?)?\](?:(.+?)?\[\/\b' . $id . '\b\])?/i', (string) $text, $matches );

                foreach( $matches[0] as $index => $shortcode ) {
                    $atts   = isset( $matches[1][$index] ) ? \site\utils::extract_atts( $matches[1][$index] ) : '';
                    $content= isset( $matches[2][$index] ) ? $matches[2][$index] : '';

                    ob_start();
                    echo call_user_func( $callback, $atts, $content );
                    $cb = ob_get_contents();
                    ob_end_clean();

                    $text = str_replace( $matches[0][$index], $cb, $text );
                }

            }
        }
    }

    return $text;

}

/* Process the title/name */

public static function title( $place = '', $text = '', $use_emoticons = true, $escape = true, $allow_filters = true ) {

    if( $escape ) $text = esc_html( $text );

    if( $allow_filters ) {

        // enable filters
        $text = self::filters( $text, $place );

    }

    if( $use_emoticons ) {

        // list with available smilies
        $words = self::smilies();

        // add smilies
        $text = preg_replace_callback( '/:[a-z]+:/', function( $match ) use ( $words ) {
        if( isset( $words[$match[0]] ) )
            return ( '<img src="' . $words[$match[0]] . '" alt="' . $match[0] . '" />' );
        else
            return( $match[0] );
        }, $text );

    }

    return $text;

}

/* Process the content */

public static function content( $place = '', $text = '', $use_emoticons = true, $use_shortcodes = true, $autolinks = false, $escape = true, $allow_filters = true ) {

    if( $use_shortcodes ) {

        // enable shortcodes
        $text = self::shortcodes( $text );

    }

    if( $escape ) $text = esc_html( $text );

    if( $allow_filters ) {

        // enable filters
        $text = self::filters( $text, $place );

    }

    if( $autolinks ) {

        // make links clickable
        $text = preg_replace( '/((www|http:\/\/)[^ ]+)/', '<a href="\1">\1</a>', $text );

    }

    if( $use_emoticons ) {

        // list with available smilies
        $words = self::smilies();

        // add smilies
        $text = preg_replace_callback( '/:[a-z]+:/', function( $match ) use ( $words ) {
        if( isset( $words[$match[0]] ) )
            return ( '<img src="' . $words[$match[0]] . '" alt="' . $match[0] . '" />' );
        else
            return( $match[0] );
        }, $text );

    }

    return $text;

}

}