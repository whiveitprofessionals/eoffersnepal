<?php

namespace admin;

/** */

class template {

    public static function have_widgets() {
        global $add_widgets_zone;
        $added_zones = array();
        if( !empty( $add_widgets_zone ) && is_array( $add_widgets_zone ) ) {
            $added_zones = $add_widgets_zone;
        }
        if( function_exists( 'register_widgets' ) ) {
          $added_zones += register_widgets();
        }
        if( !empty( $added_zones ) ) {
            return $added_zones;
        }
        return false;
    }

    public static function have_rewards() {
        if( function_exists( 'theme_has_rewards' ) && theme_has_rewards() ) {
          return true;
        }
        return false;
    }

    public static function have_theme_options() {
        if( function_exists( 'theme_options' ) ) {
            $options = theme_options();
            if( is_array( $options ) && count( $options ) > 0 ) {
                return true;
            }
        }
        return false;
    }

    public static function suggestion_intent( $id ) {
        switch( $id ) {
            case 1:
            return t( 'suggestion_store_owner', "I'm the owner of this store/brand" );
            break;

            case 2:
            return t( 'suggestion_just_suggestion', "I just want to make a suggestion" );
            break;
        }
        return '-';
    }

    public static function read_dirs( $dir = '' ) {
        $dir = empty( $dir ) ? DIR . '/' . THEMES_LOC : $dir;

        if( !is_dir( $dir ) ) {
            return false;
        }

        $files = array();

        foreach( scandir( $dir ) as $f ) {
            if( $f !== '.' && $f !== '..' ) {
            if( is_dir( rtrim( $dir, '/' ) . '/' . $f ) ) {
                $files['dirs'][] = $f;
            } else {
                $files['files'][] = $f;
            }
            }
        }
        return $files;
    }

    public static function read_theme_info_file( $theme = '' ) {
        if( empty( $theme ) || !is_dir( $theme_loc = rtrim( DIR . '/' . THEMES_LOC, '/' ) . '/' . $theme ) ) {
            return false;
        }

        if( !file_exists( rtrim( $theme_loc, '/' ) . '/' . 'info.txt'  ) ) {
            return false;
        }

        $info = array();

        if( $content = @file_get_contents( rtrim( $theme_loc, '/' ) . '/' . 'info.txt' ) ) {

            $lines = explode( "\n", $content );

            foreach( $lines as $line ) {

                $line = explode( ':', trim( $line ), 2 );

                switch( trim( strtolower( $line[0] ) ) ) {

                case 'version':
                $info['version'] = trim( $line[1] );
                break;

                case 'published by':
                preg_match( '/(.*)\ (http(.*))?/i', $line[1], $pb );
                if( isset( $pb[1] ) )$info['published_by'] = $pb[1];
                if( isset( $pb[2] ) )$info['publisher_url'] = $pb[2];
                break;

                case 'description':
                $info['description'] = trim( $line[1] );
                break;

                }

            }

        }
        return $info;
    }

    public static function theme_editor_map( $theme = '' ) {
        $files = self::map_of_files_recursive( DIR . '/' . THEMES_LOC . '/' . $theme, '.php,.html,.htm,.xhtml,.css,.js' );
        return array_map( function( $file ) {
            return substr( $file, 1 );
        }, array_values( $files ) );
    }

    public static function plugin_editor_map( $plugin = '' ) {
        $files = self::map_of_files_recursive( DIR . '/' . UPDIR . '/' . $plugin, '.php,.html,.htm,.xhtml,.css,.js' );
        return array_map( function( $file ) {
            return substr( $file, 1 );
        }, $files );
    }

    public static function theme_min() {
        return array( '404.php', 'category.php', 'index.php', 'page.php', 'search.php', 'site_header.php', 'site_footer.php', 'store.php', 'stores.php', 'style.css' );
    }

    public static function theme_have_min( $files = array() ) {
        $required_files = self::theme_min();
        if( count( array_intersect( $files, $required_files ) ) !== count( $required_files ) ) {
            return false;
        }

        return true;
    }

    public static function map_of_files_recursive( $directory, $allowed_ext = '' ) {
        if( !is_dir( $directory ) ) {
          return false;
        }

        $dir = array();

        foreach( new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $directory ) ) as $filename ) {
          if( \site\utils::file_has_extension( $filename, $allowed_ext ) )
          $dir[] = str_replace( $directory, '', $filename );
        }

         return $dir;
    }

}