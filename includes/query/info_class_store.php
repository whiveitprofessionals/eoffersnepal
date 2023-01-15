<?php

namespace query;

/** */

class info_class_store {

    protected $ID;

    function __construct( $id ) {
        $this->ID = $id;
    }

    /* GET IF STORE EXISTS */

    public function exists() {
        return main::store_exists( $this->ID );
    }

    /* GET INFORMATION ABOUT STORE */

    public function info() {
        return main::store_info( $this->ID );
    }

}