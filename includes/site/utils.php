<?php

namespace site;

/** */

class utils {

    public static function str_random( $limit, $upper = true, $lower = true, $numbers = true ) {
        $upperl = $upper    ? range( 'A', 'Z' ) : array();
        $lowerl = $lower    ? range( 'a', 'z' ) : array();
        $numeric= $numbers  ? range( 1, 9 )     : array();
        return substr( str_shuffle( str_repeat( implode( '', array_merge( $upperl, $lowerl, $numeric ) ), 3 ) ), 0, ( $limit < 183 ? $limit : 183 ) ); // 183 maximum
    }

    public static function array_map_recursive( $func, $arr) {
        array_walk_recursive( $arr, function( &$w ) use ( $func ) {
            $w = $func( $w );
        });
        return $arr;
    }

    public static function array_sanitize( $arr ) {
        return array_filter( array_map( function( $v ) {
            if( is_array( $v ) ) {
                    return self::array_sanitize( $v );
            } else {
                    return $v;
            }
        }, $arr ) );
    }

    public static function get_extension( $file ) {
        return strstr( strtolower( basename( $file ) ), '.' );
    }

    public static function file_has_extension( $file, $allowed ) {
        if( !is_array( $allowed ) ) {
            $allowed = array_map( 'strtolower', explode( ',', $allowed ) );
        }
        if( in_array( strtolower( self::get_extension( $file ) ), $allowed ) ) {
            return true;
        }

        return false;
    }

    public static function file_path( $file ) {
       if( substr_count( $file, '/' ) >= 1 ) {
            $explode_path = explode( '/', $file );
            $path = array_splice( $explode_path, 0, -1 );
            return implode( '/', $path );
        }
        return $file;
    }

    public static function clear_name( $name ) {
        $clean = preg_replace( '/[^a-z0-9_]/i', '', str_replace( ' ', '_', $name ) );
        return preg_replace( '!__+!', '-', rtrim( $clean, '_' ) );
    }

    public static function encodeurl( $url ) {
        //cyrylic transcription
        $cyrylicFrom = array( 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я' );
        $cyrylicTo     = array( 'A', 'B', 'W', 'G', 'D', 'Ie', 'Io', 'Z', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'Ch', 'C', 'Tch', 'Sh', 'Shtch', '', 'Y', '', 'E', 'Iu', 'Ia', 'a', 'b', 'w', 'g', 'd', 'ie', 'io', 'z', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'ch', 'c', 'tch', 'sh', 'shtch', '', 'y', '', 'e', 'iu', 'ia' );
        // all others
        $from   = array( 'Á', 'À', 'Ả', 'Â', 'Ä', 'Ă', 'Ā', 'Ã', 'Å', 'Ą', 'Æ', 'Ć', 'Ċ', 'Ĉ', 'Č', 'Ç', 'Ď', 'Đ', 'Ð', 'É', 'È', 'Ė', 'Ê', 'Ë', 'Ě', 'Ē', 'Ę', 'Ə', 'Ġ', 'Ĝ', 'Ğ', 'Ģ', 'á', 'à', 'ả', 'â', 'ä', 'ă', 'ā', 'ã', 'å', 'ą', 'æ', 'ć', 'ċ', 'ĉ', 'č', 'ç', 'ď', 'đ', 'ð', 'é', 'è', 'ė', 'ê', 'ë', 'ě', 'ē', 'ę', 'ə', 'ġ', 'ĝ', 'ğ', 'ģ', 'Ĥ', 'Ħ', 'I', 'Í', 'Ì', 'İ', 'Î', 'Ï', 'Ī', 'Į', 'Ĳ', 'Ĵ', 'Ķ', 'Ļ', 'Ł', 'Ń', 'Ň', 'Ñ', 'Ņ', 'Ó', 'Ò', 'Ô', 'Ö', 'Õ', 'Ő', 'Ø', 'Ơ', 'Œ', 'ĥ', 'ħ', 'ı', 'í', 'ì', 'i', 'î', 'ï', 'ī', 'į', 'ĳ', 'ĵ', 'ķ', 'ļ', 'ł', 'ń', 'ň', 'ñ', 'ņ', 'ó', 'ò', 'ô', 'ö', 'õ', 'ő', 'ø', 'ơ', 'œ', 'Ŕ', 'Ř', 'Ś', 'Ŝ', 'Š', 'Ş', 'Ť', 'Ţ', 'Þ', 'Ú', 'Ù', 'Û', 'Ü', 'Ŭ', 'Ū', 'Ů', 'Ų', 'Ű', 'Ư', 'Ŵ', 'Ý', 'Ŷ', 'Ÿ', 'Ź', 'Ż', 'Ž', 'ŕ', 'ř', 'ś', 'ŝ', 'š', 'ş', 'ß', 'ť', 'ţ', 'þ', 'ú', 'ù', 'û', 'ü', 'ŭ', 'ū', 'ů', 'ų', 'ű', 'ư', 'ŵ', 'ý', 'ŷ', 'ÿ', 'ź', 'ż', 'ž' );
        $to     = array( 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'C', 'C', 'C', 'C', 'D', 'D', 'D', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'G', 'G', 'G', 'G', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'c', 'c', 'c', 'c', 'd', 'd', 'd', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'g', 'g', 'g', 'g', 'H', 'H', 'I', 'I', 'I', 'I', 'I', 'I', 'I', 'I', 'IJ', 'J', 'K', 'L', 'L', 'N', 'N', 'N', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'CE', 'h', 'h', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'ij', 'j', 'k', 'l', 'l', 'n', 'n', 'n', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'R', 'R', 'S', 'S', 'S', 'S', 'T', 'T', 'T', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'W', 'Y', 'Y', 'Y', 'Z', 'Z', 'Z', 'r', 'r', 's', 's', 's', 's', 'B', 't', 't', 'b', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'w', 'y', 'y', 'y', 'z', 'z', 'z' );

        $from   = array_merge( $from, $cyrylicFrom );
        $to     = array_merge( $to, $cyrylicTo );

        return self::clear_name( str_replace( $from, $to, $url ) );
    }

    public static function money_format( $money ) {
        return number_format( (double) $money, 2, ( defined( 'MONEY_DECIMAL_SEPARATOR' ) ? MONEY_DECIMAL_SEPARATOR : '.' ), ( defined( 'MONEY_THOUSAND_SEPARATOR' ) ? MONEY_THOUSAND_SEPARATOR : '' ) );
    }

    public static function make_money_format( $money ) {
        $fineprice = str_replace( array( ( defined( 'MONEY_THOUSAND_SEPARATOR' ) ? MONEY_THOUSAND_SEPARATOR : '' ), ( defined( 'MONEY_DECIMAL_SEPARATOR' ) ? MONEY_DECIMAL_SEPARATOR : '.' ) ), array( '', '.' ), $money );
        preg_match( '/([0-9\.]+)/', $fineprice, $match );

        return ( isset( $match[0] ) ? $match[0] : 0.00 );
    }

    public static function make_seo_link( $dir = '', $name = '', $custom_url = '', $id = '', $extension = '.html' ) {
        if( !empty( $custom_url ) ) {
            return $GLOBALS['siteURL'] . ( !empty( $dir ) ? $dir . '/' : '' ). $custom_url . $extension;
        } else if( !empty( $name ) ) {
            return $GLOBALS['siteURL'] . ( !empty( $dir ) ? $dir . '/' : '' ) . strtolower( self::encodeurl( $name  ) ) . ( !empty( $id ) ? '-' . $id : '' ) . $extension;
        }

        return $GLOBALS['siteURL'] . ( !empty( $dir ) ? $dir : '' ) . '/';
    }

    public static function make_template_seo_link( $dir = '', $name = '', $custom_url = '', $id = '', $extension = '.html' ) {
        if( !empty( $custom_url ) ) {
            return $GLOBALS['siteURL'] . '?tpage=' . ( !empty( $dir ) ? $dir . '/' : '' ). $custom_url . $extension;
        } else if( !empty( $name ) ) {
            return $GLOBALS['siteURL'] . '?tpage=' . ( !empty( $dir ) ? $dir . '/' : '' ) . strtolower( self::encodeurl( $name ) ) . ( !empty( $id ) ? '-' . $id : '' ) . $extension;
        }

        return $GLOBALS['siteURL'] . '?tpage=' . ( !empty( $dir ) ? $dir : '' ) . '/';
    }

    public static function getIP() {
        if( isset( $_SERVER['HTTP_CLIENT_IP'] ) && filter_var( $_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP ))
            return $_SERVER['HTTP_CLIENT_IP'];
        else if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && filter_var( $_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP ) )
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        else
            return $_SERVER['REMOTE_ADDR'];
    }

    public static function site_protocol() {
        if( ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) || $_SERVER['SERVER_PORT'] == 443 ) {
            return 'https://';
        }

        return 'http://';
    }

    public static function site_url() {
        $urlfopt = \query\main::get_option( 'siteurl' );
        if( !empty( $urlfopt ) ) {
            return rtrim( $urlfopt, '/' ) . '/';
        }

        return self::site_protocol() . rtrim( $_SERVER['HTTP_HOST'], '/' ) . '/';
    }

    public static function update_uri( $request_uri, $def_query, $action = 'update' ) {
        if( empty( $request_uri ) || $request_uri == 'this' ) {
            $request_uri = self::site_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        $p = parse_url( $request_uri );

        $query = array();
        if( !empty( $p['query'] ) ) {
            parse_str( $p['query'], $query );
        }

        switch( $action ) {
            case 'update':
                $query = array_merge( $query, $def_query );
            break;

            case 'remove':
                foreach( $def_query as $k => $v ) unset( $query[$v] );
            break;

            default:
            break;
        }

        return $p['scheme'] . '://' . $p['host'] . $p['path'] . '?' . http_build_query( $query, '', '&' );
    }

    public static function dbp( $str ) {
        global $db;
        return $db->real_escape_string( $str );
    }

    public static function check_csrf( $post, $session ) {
        if( isset( $_SESSION[$session] ) && $post == $_SESSION[$session] ) {
            unset( $_SESSION[$session] );
            return true;
        }

        return false;
    }

    public static function timeconvert( $string = null, $timezone = 'UTC', $format = 'U' ) {
        $date = new \DateTime( $string, timezone_open( \query\main::get_option('timezone') ) );
        $date->setTimeZone(new \DateTimeZone( $timezone ) );

        return $date->format( $format );
    }

    public static function validate_user_data( $arr, $allow_array_value = false ) {
        $func = 'esc_html';
        array_walk_recursive( $arr, function( &$w ) use ( $func ) {
            $w = $func( $w );
        });

        return $arr;
    }

    public static function days_of_week() {
        $alldays = array( 'sunday' => t( 'sunday', 'Sunday' ), 'monday' => t( 'monday', 'Monday' ), 'tuesday' => t( 'tuesday', 'Tuesday' ), 'wednesday' => t( 'wednesday', 'Wednesday' ), 'thursday' => t( 'thursday', 'Thursday' ), 'friday' => t( 'friday', 'Friday' ), 'saturday' => t( 'saturday', 'Saturday' ) );
        $fday = defined( 'FDOW' ) ? strtolower( FDOW ) : 'sunday';
        $afr = false;
        $a = $b = array();

        foreach( $alldays as $k => $v ) {

            if( $k == $fday ) {
                $afr = true;
            }

            if( $afr ) {
            $b[$k] = $v;
            } else {
                $a[$k] = $v;
            }

        }

        return array_merge( $b, $a );
    }

    public static function check_callback( $callback = '' ) {
        if( is_array( $callback ) ) {
            if( isset( $callback[0] ) && isset( $callback[1] ) && method_exists( $callback[0], $callback[1] ) ) {
                return $callback[0] . '::' . $callback[1];
            }
        } else if( is_string( $callback ) && function_exists( $callback ) ) {
            return $callback;
        } else if( is_callable( $callback ) ) {
            return $callback;
        }

        return false;
    }

    public static function build_atts( $list = array(), $before_str = ' ' ) {
        $str = '';
        if( is_array( $list ) ) {
            foreach( $list as $k => $v ) {
                $str .= $k . '="' . $v . '" ';
            }
        }

        return $before_str . trim( $str );
    }

    public static function extract_atts( $str = '' ) {
        if( !empty( $str ) ) {
            preg_match_all( '/\s?(.+?)\=\"(.+?)\"/i', $str, $matches );
            if( isset( $matches[1] ) && isset( $matches[2] ) ) {
                return array_combine( $matches[1], $matches[2] );
            }
        }
        return false;
    }

    public static function get_coords_from_str( string $loc ) {
        $KEY    = \query\main::get_option( 'google_maps_key' );
        if( !$KEY ) 
        return false;
        $URL    = file_get_contents( 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode( $loc ) . '&key=' . $KEY );
        $JSON   = json_decode( $URL, true );

        if( isset( $JSON['error_message'] ) || empty( $JSON['results'] ) )
        return false;

        $RESS   = $JSON['results'];
        $RES    = current( $RESS );
        
        return $RES['geometry']['location'];
    }

}