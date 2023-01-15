<?php

namespace query;

/** */

class info_class_coupon {

    protected $ID;

    function __construct( $id ) {
        $this->ID = $id;
    }

    /* GET IF COUPON EXISTS */

    public function exists() {
        return main::item_exists( $this->ID );
    }

    /* GET INFORMATION ABOUT COUPON */

    public function info() {
        return main::item_info( $this->ID );
    }

}