<?php

namespace query;

/** */

class info_class_product {

    protected $ID;

    function __construct( $id ) {
        $this->ID = $id;
    }

    /* GET IF PRODUCT EXISTS */

    public function exists() {
        return main::product_exists( $this->ID );
    }

    /* GET INFORMATION ABOUT PRODUCT */

    public function info() {
        return main::product_info( $this->ID );
    }

}