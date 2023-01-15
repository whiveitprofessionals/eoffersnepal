<?php

namespace admin;

/** */

class plugin {

    /* CHECK IF PLUGIN EXISTS */

    public static function info() {

        if( !isset( $_GET['plugin'] ) ) {
            return false;
        }

        return admin_query::plugin_info( '^' . dirname( $_GET['plugin'] ) . '/' );

    }

}