<?php

namespace query;

/** */

class info_class_user {

    protected $ID;

    function __construct( $id ) {
        $this->ID = $id;
    }

    /* GET IF USER EXISTS */

    public function exists() {
        return main::user_exists( $this->ID );
    }

    /* GET INFORMATION ABOUT USER */

    public function info() {
        return main::user_info( $this->ID );
    }

}