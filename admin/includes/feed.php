<?php

namespace admin;

/** */

class feed {

/* TIMEOUT, TIME UNTIL STOP TRYING TO CONNECT TO FEED SERVER */

public $timeout = 5;

/* EXPORT RESULTS AS OBJECT OR ARRAY */

public $export_as = 'array';

/* TIMEZONE */

public $timezone;

/* LOGIN */

private $user;
private $pass;

/* Construct function */

function __construct( $user = '', $pass = '' ) {

    $this->user = $user;
    $this->pass = $pass;

    $this->getServer = $this->getServerInfo();

    $this->auth_type = isset( $this->getServer['AUTHENTICATION'] ) && in_array( strtolower( $this->getServer['AUTHENTICATION'] ), array( 'get', 'http' ) ) ? $this->getServer['AUTHENTICATION'] : \query\main::get_option( 'feedserver_auth' );
    $this->timezone = !empty( $this->getServer['TIMEZONE'] ) ? $this->getServer['TIMEZONE'] : date( 'e' );

}

/* Get the Feed Server information/variables */

public function getServerInfo() {

    return $this->checkserver();

}

/* Check if this is a valid Feed server and ready to be used */

private function checkserver() {

    $server = \site\feed::server( \query\main::get_option( 'feedserver' ) );

    if( !$server ) {
        throw new \Exception( t( 'feed_e_invalid', "This Feed Server doesn't exists." ) );
    }

    if( !file_exists( DIR . '/' . $server['config'] ) ) {
        throw new \Exception( t( 'feed_e_configmiss', "Server configuration is missing." ) );
    }

    require_once DIR . '/' . $server['config'];

    if( !isset( $server['COUPON_URL'] ) ||
    !isset( $server['COUPONS_URL'] ) ||
    !isset( $server['STORE_URL'] ) ||
    !isset( $server['STORES_URL'] ) ||
    !isset( $server['CATEGORIES_URL'] ) ) {
        throw new \Exception( t( 'feed_e_serverr', "Wrong configuration. This Server cannot be used." ) );
    }

    return $server;

}

/* Connect to Feed server */

private function connect( $url = '', $method = 'GET', $getdata = array(), $postdata = array() ) {

    if( !$this->getServer ) {
        throw new \Exception( t( 'feed_e_serverr', "Wrong configuration. This Server cannot be used." ) );
    }

    $opts = array('http' =>
    array(
    'method'    => $method,
    'content' => http_build_query( $postdata ),
    'timeout' => $this->timeout)
    );

    if( $this->auth_type === 'HTTP' ) {
        $opts = array('http' =>
        array(
        'header' => 'Authorization: Basic ' . base64_encode( $this->user . ':' . $this->pass )
        )
        );
    } else {
        $getdata = array_merge( array( 'auth' => base64_encode( $this->user . ':' . $this->pass ) ), $getdata );
    }

    $url = htmlspecialchars_decode( \site\utils::update_uri( $url, $getdata ) );

    $result = @file_get_contents( $url, false, stream_context_create( $opts ) );

    if( (boolean) $result === false ) {
        throw new \Exception( t( 'feed_e_servtout', "Connection to server timed out" ) );
    }

    switch( $http_response_header[0] ) {
        case 'HTTP/1.1 200 OK':
            return $this->parse( $result );
        break;

        case 'HTTP/1.1 204 No Content':
            //  No content
        break;

        case 'HTTP/1.1 401 Unauthorized':
            throw new \Exception( 'Unauthorized !' );
        break;

        case 'HTTP/1.1 402 Payment Required':
            throw new \Exception( 'You have reached the limit ! Please check your limits.' );
        break;

        case 'HTTP/1.1 404 Not Found':
            throw new \Exception( 'The content that you tried to get wasn\'t found.' );
        break;

        case 'HTTP/1.1 405 Method Not Allowed':
            throw new \Exception( 'Method not allowed.' );
        break;

        default:
            throw new \Exception( 'Unexpected.' );
        break;
    }

}

/* Change the export format */

public function exportAs( $type = '' ) {

    return $this->export_as = $type;

}

/* Parse answer */

public function parse( $content ) {

    switch( $this->export_as ) {
        case 'object':
                return (array) json_decode( $content );
        break;
        default:
                return json_decode( $content, TRUE );
        break;
    }

}

/* Get information about a store from source */

public function store( $ID = 0 ) {

    return $this->connect( $this->getServer['STORE_URL'], 'GET', array( 'ID' => $ID ) );

}

/* Get information about stores from source */

public function stores( $getdata = array(), $postdata = array() ) {

    return $this->connect( $this->getServer['STORES_URL'], 'GET', (array) $getdata, (array) $postdata );

}

/* Get information about a coupon from source */

public function coupon( $ID = 0, $postdata = array() ) {

    return $this->connect( $this->getServer['COUPON_URL'], 'GET', array( 'ID' => $ID ) );

}

/* Get information about coupons from source */

public function coupons( $getdata = array(), $postdata = array() ) {

    return $this->connect( $this->getServer['COUPONS_URL'], 'GET', (array) $getdata, (array) $postdata );

}

/* Get information about a product from source */

public function product( $ID = 0, $postdata = array() ) {

    return $this->connect( $this->getServer['PRODUCT_URL'], 'GET', array( 'ID' => $ID ) );

}

/* Get information about products from source */

public function products( $getdata = array(), $postdata = array() ) {

    return $this->connect( $this->getServer['PRODUCTS_URL'], 'GET', (array) $getdata, (array) $postdata );

}

/* Get information about categories from source */

public function categories( $getdata = array() ) {

    return $this->connect( $this->getServer['CATEGORIES_URL'], 'GET', (array) $getdata );

}

}