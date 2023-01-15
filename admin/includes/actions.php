<?php

namespace admin;

/** */

class actions {

/* SET OPTION */

public static function set_option( $opt = array() ) {

    global $db;

    // if( !$GLOBALS['me']->is_admin ) return false;

    $opt = array_map( 'trim', $opt );

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "options SET option_value = ? WHERE option_name = ?" );

    foreach( $opt as $k => $v ) {

        $stmt->bind_param( "ss", $v, $k );
        $stmt->execute();

        $cache = new \cache\main;
        $cache->update( 'options_' . $k, $v );

    }

    $stmt->close();

    return true;

}

/* REMOVE OPTION */

public static function remove_option( $opt = array() ) {

    global $db;

    // if( !$GLOBALS['me']->is_admin ) return false;

    $opt = array_map( 'trim', $opt );

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "options WHERE option_name = ?" );

    foreach( $opt as $o ) {

        $stmt->bind_param( "s", $o );
        $stmt->execute();

        $cache = new \cache\main;
        $cache->remove( 'options_' . $o );

    }

    $stmt->close();

    return true;

}

/* ADD CATEGORY */

public static function add_category( $opt = array() ) {

    global $db;

    if( !ab_to( array( 'categories' => 'add' ) ) ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "categories (subcategory, user, name, description, meta_title, meta_keywords, meta_desc, extra, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())" );

    $extra = \site\utils::array_sanitize( $opt['extra'] );
    $extra = @serialize( $extra );

    $stmt->bind_param( "iissssss", $opt['category'], $GLOBALS['me']->ID, $opt['name'], $opt['description'], $opt['meta_title'], $opt['meta_keywords'], $opt['meta_desc'], $extra );
    $execute = $stmt->execute();
    $insert_id = $stmt->insert_id;
    $stmt->close();

    if( $execute ) {
        return $insert_id;
    }

    return false;

}

/* EDIT CATEGORY */

public static function edit_category( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'categories' => 'edit' ) ) ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "categories SET subcategory = ?, name = ?, description = ?, meta_title = ?, meta_keywords = ?, meta_desc = ?, extra = ? WHERE id = ?" );

    $extra = \site\utils::array_sanitize( $opt['extra'] );
    $extra = @serialize( $extra );

    $stmt->bind_param( "issssssi", $opt['category'], $opt['name'], $opt['description'], $opt['meta_title'], $opt['meta_keywords'], $opt['meta_desc'], $extra, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT CATEGORY URL */

public static function edit_category_url( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'categories' => 'edit' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( !isset( $opt['title'] ) ) {
        return false;
    }

    $url = strtolower( \site\utils::encodeurl( $opt['title'] ) );

    $stmt = $db->stmt_init();

    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "categories WHERE id != ? AND url_title = ?" );
    $stmt->bind_param( "is", $id, $url );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();

    if( $count > 0 ) {
        return false;
    }

    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "categories SET url_title = ? WHERE id = ?" );
    $stmt->bind_param( "si", $url, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* DELETE CATEGORY */

public static function delete_category( $id ) {

    global $db;

    if( !ab_to( array( 'categories' => 'delete' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "categories WHERE id = ?" );

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        if( $stmt->execute() ) {
            do_action( 'admin_category_deleted', $ID );
        }
    }

    @$stmt->close();

    return true;

}

/* ADD STORE */

public static function add_store( $opt = array() ) {

    global $db;

    // if( !ab_to( array( 'stores' => 'add' ) ) ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    if( empty( $opt['name'] ) || ( $opt['type'] == 0 && empty( $opt['url'] ) ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "stores (feedID, user, category, popular, physical, name, link, description, tags, image, hours, phoneno, sellonline, visible, meta_title, meta_keywords, meta_desc, lastupdate_by, lastupdate, extra, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())" );

    $feedID = isset( $opt['feedID'] ) ? $opt['feedID'] : 0;
    $logo = \site\images::upload( ( !empty( $opt['import_logo'] ) && !empty( $opt['logo_url'] ) && empty( $opt['logo']['name'] ) ? $opt['logo_url'] : $opt['logo'] ), 'logo_', array( 'path' => DIR . '/', 'current' => ( !empty( $opt['logo_url'] ) ? $opt['logo_url'] : '' ) ) );
    $hours = @serialize( $opt['hours'] );
    $extra = \site\utils::array_sanitize( $opt['extra'] );
    $extra = @serialize( $extra );

    $stmt->bind_param( "iiiiisssssssiisssis", $feedID, $opt['user'], $opt['category'], $opt['popular'], $opt['type'], $opt['name'], $opt['url'], $opt['description'], $opt['tags'], $logo, $hours, $opt['phone'], $opt['sellonline'], $opt['publish'], $opt['meta_title'], $opt['meta_keywords'], $opt['meta_desc'], $GLOBALS['me']->ID, $extra );

    if( $stmt->execute() ) {
        $insert_id = $stmt->insert_id;
        $stmt->close();

        return $insert_id;
    }

    $stmt->close();

    return false;

}

/* IMPORT STORES */

public static function import_stores( $opt = array() ) {

    global $db;

    if( !ab_to( array( 'stores' => 'import' ) ) ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    if( empty( $opt['file'] ) || !\site\utils::file_has_extension( $opt['file']['name'], '.csv' ) ) {
        return false;
    }

    // default options

    $type = 0;
    $sellonline = 1;
    $name = $link = $description = $tags = $image = $hours = $phone = $locations = '';

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "stores (user, category, physical, name, link, description, tags, image, hours, phoneno, sellonline, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );

    $cat = !empty( $opt['category'] ) ? (int) $opt['category'] : 0;

    $success = $error = $line = 0;

    if ( ( $handle = fopen( $opt['file']['tmp_name'], 'r' ) ) !== false ) {

        while( ( $data = fgetcsv( $handle, 3000, ',' ) ) !== false ) {

        if( $line === 0 && $opt['omit_first_line'] ) {
            $line++;
            continue;
        }

        foreach( $opt['fields'] as $k => $var ) {
            ${$var} = isset( $data[$k] ) ? $data[$k] : '';
        }

        /*

        If store URL isn't valid, omit that row.

        */

        if( empty( $name ) || !filter_var( $link, FILTER_VALIDATE_URL ) || count( $data ) < 2 ) {
            $error++;
            continue;
        }

        $stmt2 = $db->stmt_init();
        $stmt2->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores WHERE name = ? OR link = ?" );
        $stmt2->bind_param( "ss", $name, $link );
        $stmt2->execute();
        $stmt2->bind_result( $count );
        $stmt2->fetch();
        $stmt2->close();

        if( $count > 0 ) {
            $error++;
            continue;
        }

        $stmt->bind_param( "iiisssssssii", $GLOBALS['me']->ID, $cat, $type, $name, $link, $description, $tags, $image, $hours, $phone, $sellonline, $GLOBALS['me']->ID );
        $execute = $stmt->execute();

        if( !$execute ) {
            $error++;
        } else {

        if( !empty( $locations ) ) {

        $stmt3 = $db->stmt_init();
        $stmt3->prepare( "SELECT LAST_INSERT_ID() FROM " . DB_TABLE_PREFIX . "stores" );
        $stmt3->execute();
        $stmt3->bind_result( $id );
        $stmt3->fetch();
        $stmt3->close();

        foreach( @unserialize( $locations ) as $location ) {
            self::add_store_location( array_merge( array( 'Store' => $id ), $location ) );
        }

        }

        $success++;

        }

        }

        fclose( $handle );

    }

    @$stmt->close();

    return array( $success, $error );

}

/* EDIT STORE */

public static function edit_store( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'stores' => 'edit' ) ) ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    if( empty( $opt['name'] ) || ( $opt['type'] == 0 && empty( $opt['url'] ) ) ) {
        return false;
    }

    $store = \query\main::store_info( $id );

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET user = ?, category = ?, popular = ?, physical = ?, name = ?, link = ?, description = ?, tags = ?, image = ?, hours = ?, phoneno = ?, sellonline = ?, visible = ?, meta_title = ?, meta_keywords = ?, meta_desc = ?, lastupdate_by = ?, lastupdate = NOW(), extra = ? WHERE id = ?" );

    $logo = \site\images::upload( $opt['logo'], 'logo_', array( 'path' => DIR . '/', 'current' => $store->image ) );
    $hours = @serialize( $opt['hours'] );
    $extra = \site\utils::array_sanitize( $opt['extra'] );
    $extra = @serialize( $extra );

    $stmt->bind_param( "iiiisssssssiisssisi", $opt['user'], $opt['category'], $opt['popular'], $opt['type'], $opt['name'], $opt['url'], $opt['description'], $opt['tags'], $logo, $hours, $opt['phone'], $opt['sellonline'], $opt['publish'], $opt['meta_title'], $opt['meta_keywords'], $opt['meta_desc'], $GLOBALS['me']->ID, $extra, $id );
    $execute = $stmt->execute();

    if( $execute && $store->is_physical && (int) $opt['type'] === 0 ) {

        // remove locations for this store
        $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "store_locations WHERE store = ?" );
        $stmt->bind_param( "i", $id );
        $stmt->execute();

    }

    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT STORE URL */

public static function edit_store_url( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'stores' => 'edit' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( !isset( $opt['title'] ) ) {
        return false;
    }

    $url = strtolower( \site\utils::encodeurl( $opt['title'] ) );

    $stmt = $db->stmt_init();

    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "stores WHERE id != ? AND url_title = ?" );
    $stmt->bind_param( "is", $id, $url );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();

    if( $count > 0 ) {
        return false;
    }

    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET url_title = ? WHERE id = ?" );
    $stmt->bind_param( "si", $url, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* SET ACTION TO STORE */

public static function action_store( $action, $id ) {

    global $db;

    if( !ab_to( array( 'stores' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'publish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET visible = 1 WHERE id = ?" );
        break;

        case 'unpublish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET visible = 0 WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* DELETE STORE */

public static function delete_store( $id ) {

    global $db;

    if( !ab_to( array( 'stores' => 'delete' ) ) ) return false;

    $id = (array) $id;

    foreach( $id as $ID ) {

    $stmt = $db->stmt_init();

    if( \query\main::store_exists( $ID ) ) {

    $store = \query\main::store_info( $ID );

    // delete the store
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "stores WHERE id = ?" );
    $stmt->bind_param( "i", $ID );

    if( $stmt->execute() ) {

    do_action( 'admin_store_deleted', $ID );

    // remove this store from favorites
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "favorite WHERE store = ?" );
    $stmt->bind_param( "i", $ID );
    $stmt->execute();

    // remove this store from saved
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "saved WHERE item = ? AND type = 'store'" );
    $stmt->bind_param( "i", $ID );
    $stmt->execute();

    // remove reviews for this store
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "reviews WHERE store = ?" );
    $stmt->bind_param( "i", $ID );
    $stmt->execute();

    // remove locations for this store
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "store_locations WHERE store = ?" );
    $stmt->bind_param( "i", $ID );
    $stmt->execute();

    // remove coupons for this store
    $stmt->prepare( "SELECT id, image, source FROM " . DB_TABLE_PREFIX . "coupons WHERE store = ?" );
    $stmt->bind_param( "i", $ID );
    $stmt->execute();
    $stmt->bind_result( $id, $image, $source );

    while( $stmt->fetch() ) {

    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "coupons WHERE id = ?" );
    $stmt->bind_param( "i", $id );

    if( $stmt->execute() ) {

    if( !empty( $source ) && !preg_match( '/^http(s)?/i', $source ) ) {
        @unlink( DIR . '/' . $source );
    }

    if( !empty( $image ) && !preg_match( '/^http(s)?/i', $image ) ) {
        @unlink( DIR . '/' . $image );
    }

    }

    }

    $stmt->close();

    // remove products for this store
    $stmt = $db->stmt_init();
    $stmt->prepare( "SELECT id, image FROM " . DB_TABLE_PREFIX . "products WHERE store = ?" );
    $stmt->bind_param( "i", $ID );
    $stmt->execute();
    $result = $stmt->get_result();

    while( ( $row = $result->fetch_assoc() ) ) {

    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "products WHERE id = ?" );
    $stmt->bind_param( "i", $id );

    if( $stmt->execute() && !empty( $image ) && !preg_match( '/^http(s)?/i', $image ) ) {
        @unlink( DIR . '/' . $image );
    }

    }

    $stmt->close();

    if( !empty( $store->image ) && !preg_match( '/^http(s)?/i', $store->image )    ) {
        @unlink( DIR . '/' . $store->image );
    }

    }

    }

    }

    return true;

}

/* DELETE STORE IMAGE */

public static function delete_store_image( $id ) {

    global $db;

    if( !ab_to( array( 'stores' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    foreach( $id as $ID ) {

    if( \query\main::store_exists( $ID ) ) {

    $store = \query\main::store_info( $ID );

    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET image = '' WHERE id = ?" );
    $stmt->bind_param( "i", $ID );

    if( $stmt->execute() && !empty( $store->image ) && !preg_match( '/^http(s)?/i', $store->image ) ) {
        @unlink( DIR . '/' . $store->image );
    }

    }

    }

    @$stmt->close();

    return true;

}

/* MOVE STORE */

public static function change_store_category( $id, $newcat ) {

    global $db;

    if( !ab_to( array( 'stores' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "stores SET category = ? WHERE id = ?" );

    foreach( $id as $ID ) {
        $stmt->bind_param( "ii", $newcat, $ID );
        $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* ADD COUPON */

public static function add_item( $opt = array() ) {

    global $db;

    // if( !ab_to( array( 'coupons' => 'add' ) ) ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "coupons (feedID, user, store, category, popular, exclusive, printable, show_in_store, available_online, title, link, description, tags, image, code, source, claim_limit, visible, start, expiration, cashback, meta_title, meta_keywords, meta_desc, votes, votes_percent, verified, last_verif, lastupdate_by, lastupdate, extra, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())" );

    $feedID = isset( $opt['feedID'] ) ? $opt['feedID'] : 0;
    $image = \site\images::upload( ( !empty( $opt['import_image'] ) && !empty( $opt['image_url'] ) && empty( $opt['image']['name'] ) ? $opt['image_url'] : $opt['image'] ), 'coupon_', array( 'path' => DIR . '/', 'current' => ( !empty( $opt['image_url'] ) ? $opt['image_url'] : '' ) ) );
    $source = \site\images::upload( ( is_array( $opt['source'] ) ? $opt['source'] : array() ), 'print_', array( 'path' => DIR . '/', 'current' => ( is_string( $opt['source'] ) ? $opt['source'] : '' ) ) );
    $extra = \site\utils::array_sanitize( $opt['extra'] );
    $extra = @serialize( $extra );

    $stmt->bind_param( "iiiiiiiiisssssssiississsidisis", $feedID, $GLOBALS['me']->ID, $opt['store'], $opt['category'], $opt['popular'], $opt['exclusive'], $opt['printable'], $opt['show_in_store'], $opt['available_online'], $opt['name'], $opt['link'], $opt['description'], $opt['tags'], $image, $opt['code'], $source, $opt['claim_limit'], $opt['publish'], $opt['start'], $opt['end'], $opt['cashback'], $opt['meta_title'], $opt['meta_keywords'], $opt['meta_desc'], $opt['votes'], $opt['votes_average'], $opt['verified'], $opt['last_verif'], $GLOBALS['me']->ID, $extra );
    $execute = $stmt->execute();
    $insert_id = $stmt->insert_id;
    $stmt->close();

    if( $execute ) {
        return $insert_id;
    }

    return false;

}

/* IMPORT COUPONS */

public static function import_items( $opt = array() ) {

    global $db;

    if( !ab_to( array( 'coupons' => 'import' ) ) ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    if( empty( $opt['file'] ) || !\site\utils::file_has_extension( $opt['file']['name'], '.csv' ) ) {
        return false;
    }

    // default options

    $printable = 0;
    $available_online = 1;

    /* */

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "coupons (user, store, category, printable, available_online, title, link, description, tags, image, code, source, visible, start, expiration, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, FROM_UNIXTIME(?), FROM_UNIXTIME(?), ?, NOW(), NOW())" );

    $cat = !empty( $opt['category'] ) ? (int) $opt['category'] : 0;

    $success = $error = $line = 0;

    if ( ( $handle = fopen( $opt['file']['tmp_name'], 'r' ) ) !== false ) {

        while( ( $data = fgetcsv( $handle, 3000, ',' ) ) !== false ) {

        if( $line === 0 && $opt['omit_first_line'] ) {
            $line++;
            continue;
        }

        // default options
        $name = $link = $description = $tags = $image = $code = $source = $start = $expiration = $store_url = $store_name = '';

        foreach( $opt['fields'] as $k => $var ) {
            ${$var} = isset( $data[$k] ) ? $data[$k] : '';
        }

        /* If store URL isn't valid, omit that row. */

        if( empty( $name ) || count( $data ) < 2 ) {
            $error++;
            continue;
        }

        if( !empty( $opt['store'] ) ) {

            $store = $opt['store'];

            $store_info = \query\main::store_info( $store );

            $store_type = (int) $store_info->is_physical;

            if( $cat === 0 ) {
                $cat = $store_info->catID;
            }

        } else {

            $stmt2 = $db->stmt_init();
            $stmt2->prepare( "SELECT id, category, physical FROM " . DB_TABLE_PREFIX . "stores WHERE name = ? OR (link != '' AND link = ?)" );

            $stmt2->bind_param( "ss", $store_name, $store_url );
            $stmt2->execute();
            $stmt2->bind_result( $store, $store_cat, $store_type );
            $stmt2->fetch();
            $stmt2->close();

            if( empty( $store ) ) {
                $error++;
                continue;
            }

            if( $cat === 0 ) {
                $cat = $store_cat;
            }

        }

        if( empty( $start ) ) {
            $std = time();
        } else {
            $std = preg_match( '/^\d+$/', $start ) ? $start : strtotime( $start );
        }

        if( empty( $expiration ) ) {
            $endd = ( !empty( $opt['def_ed'] ) ? strtotime( $opt['end_date'] ) : time() );
        } else {
            $endd = preg_match( '/^\d+$/', $expiration ) ? $expiration : strtotime( $expiration );
        }

        if( (int) $printable !== 0 && $store_type !== 0 ) {
            $printable = 1;
        }

        if( $available_online == '' && $store_type !== 0 && !empty( $code ) ) {
            $available_online = 1;
        }

        $stmt->bind_param( "iiiiisssssssssi", $GLOBALS['me']->ID, $store, $cat, $printable, $available_online, $name, $link, $description, $tags, $image, $code, $source, $std, $endd, $GLOBALS['me']->ID );
        $execute = $stmt->execute();

        if( !$execute ) {
            $error++;
        } else {
            $success++;
        }

        }

        fclose( $handle );

    }

    @$stmt->close();

    return array( $success, $error );

}

/* EDIT COUPON */

public static function edit_item( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'coupons' => 'edit' ) ) ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $coupon = \query\main::item_info( $id );

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET store = ?, category = ?, popular = ?, exclusive = ?, printable = ?, show_in_store = ?, available_online = ?, title = ?, link = ?, description = ?, tags = ?, image = ?, code = ?, source = ?, claim_limit = ?, visible = ?, start = ?, expiration = ?, cashback = ?, meta_title = ?, meta_keywords = ?, meta_desc = ?, votes = ?, votes_percent = ?, verified = ?, last_verif = ?, lastupdate_by = ?, lastupdate = NOW(), extra = ? WHERE id = ?" );

    $image = \site\images::upload( $opt['image'], 'coupon_', array(    'path' => DIR . '/', 'current' => $coupon->image ) );
    $source = is_array( $opt['source'] ) ? \site\images::upload( $opt['source'], 'print_', array( 'path' => DIR . '/', 'current' => '' ) ) : $opt['source'];
    $extra = \site\utils::array_sanitize( $opt['extra'] );
    $extra = @serialize( $extra );

    if( isset( $opt['remove_old_source'] ) && $opt['remove_old_source'] ) {
        @unlink( DIR . '/' . str_replace( $GLOBALS['siteURL'], '', $opt['remove_old_source'] ) );
    }

    $stmt->bind_param( "iiiiiiisssssssiississsidisisi", $opt['store'], $opt['category'], $opt['popular'], $opt['exclusive'], $opt['printable'], $opt['show_in_store'], $opt['available_online'], $opt['name'], $opt['link'], $opt['description'], $opt['tags'], $image, $opt['code'], $source, $opt['claim_limit'], $opt['publish'], $opt['start'], $opt['end'], $opt['cashback'], $opt['meta_title'], $opt['meta_keywords'], $opt['meta_desc'], $opt['votes'], $opt['votes_average'], $opt['verified'], $opt['last_verif'], $GLOBALS['me']->ID, $extra, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT COUPON - LIMITED INFORMATION */

public static function edit_item2( $id, $opt = array() ) {

    global $db;

    // if( !ab_to( array( 'coupons' => 'edit' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET printable = ?, available_online = ?, title = ?, link = ?, description = ?, tags = ?, code = ?, source = ?, start = ?, expiration = ?, lastupdate_by = 0, lastupdate = NOW() WHERE id = ?" );
    $stmt->bind_param( "iissssssssi", $opt['printable'], $opt['available_online'], $opt['name'], $opt['link'], $opt['description'], $opt['tags'], $opt['code'], $opt['source'], $opt['start'], $opt['end'], $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT COUPON URL */

public static function edit_item_url( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'coupons' => 'edit' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( !isset( $opt['title'] ) ) {
        return false;
    }

    $url = strtolower( \site\utils::encodeurl( $opt['title'] ) );

    $stmt = $db->stmt_init();

    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "coupons WHERE id != ? AND url_title = ?" );
    $stmt->bind_param( "is", $id, $url );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();

    if( $count > 0 ) {
        return false;
    }

    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET url_title = ? WHERE id = ?" );
    $stmt->bind_param( "si", $url, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* SET ACTION TO COUPON */

public static function action_item( $action, $id ) {

    global $db;

    if( !ab_to( array( 'coupons' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'publish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET visible = 1 WHERE id = ?" );
        break;

        case 'unpublish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET visible = 0 WHERE id = ?" );
        break;

        case 'updatevdate':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET verified = 1, last_verif = NOW() WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* DELETE COUPON */

public static function delete_item( $id ) {

    global $db;

    if( !ab_to( array( 'coupons' => 'delete' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    foreach( $id as $ID ) {

    if( \query\main::item_exists( $ID ) ) {

    $coupon = \query\main::item_info( $ID );

    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "coupons WHERE id = ?" );
    $stmt->bind_param( "i", $ID );

    if( $stmt->execute() ) {

    do_action( array( 'admin_item_deleted', 'admin_coupon_deleted' ), $ID );

    if( $coupon->is_local_source ) {
        @unlink( DIR . '/' . str_replace( $GLOBALS['siteURL'], '', $coupon->source ) );
    }

    if( !empty( $coupon->image ) && !preg_match( '/^http(s)?/i', $coupon->image )    ) {
        @unlink( DIR . '/' . $coupon->image );
    }

    // remove this item from saved
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "saved WHERE item = ? AND type = 'coupon'" );
    $stmt->bind_param( "i", $ID );
    $stmt->execute();

    }

    }

    }

    @$stmt->close();

    return true;

}

/* DELETE COUPON IMAGE */

public static function delete_item_image( $id ) {

    global $db;

    if( !ab_to( array( 'coupons' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    foreach( $id as $ID ) {

    if( \query\main::item_exists( $ID ) ) {

    $coupon = \query\main::item_info( $ID );

    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET image = '' WHERE id = ?" );
    $stmt->bind_param( "i", $ID );

    if( $stmt->execute() && !empty( $coupon->image ) && !preg_match( '/^http(s)?/i', $coupon->image ) ) {
        @unlink( DIR . '/' . $coupon->image );
    }

    }

    }

    @$stmt->close();

    return true;

}

/* DELETE COUPON SOURCE */

public static function delete_item_source( $id ) {

    global $db;

    if( !ab_to( array( 'coupons' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    foreach( $id as $ID ) {

    if( \query\main::item_exists( $ID ) ) {

    $coupon = \query\main::item_info( $ID );

    if( $coupon->is_local_source ) {

    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "coupons SET source = '' WHERE id = ?" );
    $stmt->bind_param( "i", $ID );
    $stmt->execute();

    @unlink( DIR . '/' . str_replace( $GLOBALS['siteURL'], '', $coupon->source ) );

    }

    }

    }

    @$stmt->close();

    return true;

}

/* ADD PRODUCT */

public static function add_product( $opt = array() ) {

    global $db;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "products (feedID, user, store, category, popular, title, link, description, tags, image, price, old_price, currency, visible, start, expiration, cashback, meta_title, meta_keywords, meta_desc, lastupdate_by, lastupdate, extra, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())" );

    $feedID = isset( $opt['feedID'] ) ? $opt['feedID'] : 0;
    $image = \site\images::upload( ( !empty( $opt['import_image'] ) && !empty( $opt['image_url'] ) && empty( $opt['image']['name'] ) ? $opt['image_url'] : $opt['image'] ), 'product_', array( 'path' => DIR . '/', 'current' => ( !empty( $opt['image_url'] ) ? $opt['image_url'] : '' ) ) );
    $opt['price'] = \site\utils::make_money_format( $opt['price'] );
    $opt['old_price'] = \site\utils::make_money_format( $opt['old_price'] );
    $extra = \site\utils::array_sanitize( $opt['extra'] );
    $extra = @serialize( $extra );

    $stmt->bind_param( "iiiiisssssddsississsis", $feedID, $GLOBALS['me']->ID, $opt['store'], $opt['category'], $opt['popular'], $opt['name'], $opt['link'], $opt['description'], $opt['tags'], $image, $opt['price'], $opt['old_price'], $opt['currency'], $opt['publish'], $opt['start'], $opt['end'], $opt['cashback'], $opt['meta_title'], $opt['meta_keywords'], $opt['meta_desc'], $GLOBALS['me']->ID, $extra );
    $execute = $stmt->execute();
    $insert_id = $stmt->insert_id;
    $stmt->close();

    if( $execute ) {
        return $insert_id;
    }

    return false;

}

/* IMPORT PRODUCTS */

public static function import_products( $opt = array() ) {

    global $db;

    if( !ab_to( array( 'products' => 'import' ) ) ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    if( empty( $opt['file'] ) || !\site\utils::file_has_extension( $opt['file']['name'], '.csv' ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "products (user, store, category, title, link, description, tags, image, price, old_price, currency, visible, start, expiration, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, FROM_UNIXTIME(?), FROM_UNIXTIME(?), ?, NOW(), NOW())" );

    $cat = !empty( $opt['category'] ) ? (int) $opt['category'] : 0;

    $success = $error = $line = 0;

    if ( ( $handle = fopen( $opt['file']['tmp_name'], 'r' ) ) !== false ) {

        while( ( $data = fgetcsv( $handle, 3000, ',' ) ) !== false ) {

        if( $line === 0 && $opt['omit_first_line'] ) {
            $line++;
            continue;
        }

        // default options

        $name = $link = $description = $tags = $image = $price = $old_price = $currency = $start = $expiration = $store_url = '';

        foreach( $opt['fields'] as $k => $var ) {
            ${$var} = isset( $data[$k] ) ? $data[$k] : '';
        }

        /* If store URL isn't valid, omit that row. */

        if( empty( $name ) || count( $data ) < 2 ) {
            $error++;
            continue;
        }

        if( !empty( $opt['store'] ) ) {

            $store = $opt['store'];

            if( $cat === 0 ) {
                $store_info = \query\main::store_info( $store );
                $cat = $store_info->catID;
            }

        } else {

            $stmt2 = $db->stmt_init();
            $stmt2->prepare( "SELECT id, category FROM " . DB_TABLE_PREFIX . "stores WHERE name = ? OR (link != '' AND link = ?)" );
            $stmt2->bind_param( "ss", $store_name, $store_url );
            $stmt2->execute();
            $stmt2->bind_result( $store, $store_cat );
            $stmt2->fetch();
            $stmt2->close();

            if( empty( $store ) ) {
                $error++;
                continue;
            }

            if( $cat === 0 ) {
                $cat = $store_cat;
            }

        }

        if( empty( $start ) ) {
            $std = time();
        } else {
            $std = preg_match( '/^\d+$/', $start ) ? $start : strtotime( $start );
        }

        if( empty( $expiration ) ) {
            $endd = ( !empty( $opt['def_ed'] ) ? strtotime( $opt['end_date'] ) : time() );
        } else {
            $endd = preg_match( '/^\d+$/', $expiration ) ? $expiration : strtotime( $expiration );
        }

        $stmt->bind_param( "iiisssssddsssi", $GLOBALS['me']->ID, $store, $cat, $name, $link, $description, $tags, $image, $price, $old_price, $currency, $std, $endd, $GLOBALS['me']->ID );
        $execute = $stmt->execute();

        if( !$execute ) {
            $error++;
        } else {
            $success++;
        }

        }

        fclose( $handle );

    }

    @$stmt->close();

    return array( $success, $error );

}

/* EDIT PRODUCT */

public static function edit_product( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'products' => 'edit' ) ) ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $product = \query\main::product_info( $id );

    $image = \site\images::upload( $_FILES['image'], 'product_', array( 'path' => DIR . '/', 'current' => $product->image ) );

    $opt['price'] = \site\utils::make_money_format( $opt['price'] );
    $opt['old_price'] = \site\utils::make_money_format( $opt['old_price'] );
    $extra = \site\utils::array_sanitize( $opt['extra'] );
    $extra = @serialize( $extra );

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "products SET store = ?, category = ?, popular = ?, title = ?, link = ?, description = ?, tags = ?, image = ?, price = ?, old_price = ?, currency = ?, visible = ?, start = ?, expiration = ?, cashback = ?, meta_title = ?, meta_keywords = ?, meta_desc = ?, lastupdate_by = ?, lastupdate = NOW(), extra = ? WHERE id = ?" );
    $stmt->bind_param( "iiisssssddsississsisi", $opt['store'], $opt['category'], $opt['popular'], $opt['name'],    $opt['link'], $opt['description'], $opt['tags'], $image, $opt['price'], $opt['old_price'], $opt['currency'], $opt['publish'], $opt['start'], $opt['end'], $opt['cashback'], $opt['meta_title'], $opt['meta_keywords'], $opt['meta_desc'], $GLOBALS['me']->ID, $extra, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT PRODUCT - LIMITED INFORMATION */

public static function edit_product2( $id, $opt = array() ) {

    global $db;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "products SET title = ?, link = ?, description = ?, tags = ?, price = ?, old_price = ?, currency = ?, start = ?, expiration = ?, lastupdate_by = 0, lastupdate = NOW() WHERE id = ?" );
    $stmt->bind_param( "ssssddsssi", $opt['name'], $opt['link'], $opt['description'], $opt['tags'], $opt['price'], $opt['old_price'], $opt['currency'], $opt['start'], $opt['end'], $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT PRODUCT URL */

public static function edit_product_url( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'products' => 'edit' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( !isset( $opt['title'] ) ) {
        return false;
    }

    $url = strtolower( \site\utils::encodeurl( $opt['title'] ) );

    $stmt = $db->stmt_init();

    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "products WHERE id != ? AND url_title = ?" );
    $stmt->bind_param( "is", $id, $url );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();

    if( $count > 0 ) {
        return false;
    }

    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "products SET url_title = ? WHERE id = ?" );
    $stmt->bind_param( "si", $url, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* SET ACTION TO PRODUCT */

public static function action_product( $action, $id ) {

    global $db;

    if( !ab_to( array( 'products' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'publish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "products SET visible = 1 WHERE id = ?" );
        break;

        case 'unpublish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "products SET visible = 0 WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* DELETE PRODUCT */

public static function delete_product( $id ) {

    global $db;

    if( !ab_to( array( 'products' => 'delete' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    foreach( $id as $ID ) {

    if( \query\main::product_exists( $ID ) ) {

    $product = \query\main::product_info( $ID );

    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "products WHERE id = ?" );
    $stmt->bind_param( "i", $ID );

    if( $stmt->execute() ) {
        do_action( 'admin_product_deleted', $ID );

        if( !empty( $product->image ) && !preg_match( '/^http(s)?/i', $product->image ) )
        @unlink( DIR . '/' . $product->image );
    }

    // remove this product from saved
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "saved WHERE item = ? AND type = 'product'" );
    $stmt->bind_param( "i", $ID );
    $stmt->execute();

    }

    }

    @$stmt->close();

    return true;

}

/* DELETE PRODUCT IMAGE */

public static function delete_product_image( $id ) {

    global $db;

    if( !ab_to( array( 'products' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    foreach( $id as $ID ) {

    if( \query\main::product_exists( $ID ) ) {

    $product = \query\main::product_info( $ID );

    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "products SET image = '' WHERE id = ?" );
    $stmt->bind_param( "i", $ID );

    if( $stmt->execute() && !empty( $product->image ) && !preg_match( '/^http(s)?/i', $product->image ) ) {
        @unlink( DIR . '/' . $product->image );
    }

    }

    }

    @$stmt->close();

    return true;

}

/* ADD PAGE */

public static function add_page( $opt = array() ) {

    global $db;

    if( !ab_to( array( 'pages' => 'add' ) ) ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "pages (user, name, text, visible, meta_title, meta_keywords, meta_desc, lastupdate_by, lastupdate, extra, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())" );

    $extra = \site\utils::array_sanitize( $opt['extra'] );
    $extra = @serialize( $extra );

    $stmt->bind_param( "ississsis", $GLOBALS['me']->ID, $opt['name'], $opt['text'], $opt['publish'], $opt['meta_title'], $opt['meta_keywords'], $opt['meta_desc'], $GLOBALS['me']->ID, $extra );
    $execute = $stmt->execute();
    $insert_id = $stmt->insert_id;
    $stmt->close();

    if( $execute ) {
        return $insert_id;
    }

    return false;

}

/* EDIT PAGE */

public static function edit_page( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'pages' => 'edit' ) ) ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "pages SET name = ?, text = ?, visible = ?, meta_title = ?, meta_keywords = ?, meta_desc = ?, lastupdate_by = ?, lastupdate = NOW(), extra = ? WHERE id = ?" );

    $extra = \site\utils::array_sanitize( $opt['extra'] );
    $extra = @serialize( $extra );

    $stmt->bind_param( "ssisssisi", $opt['name'],    $opt['text'], $opt['publish'], $opt['meta_title'], $opt['meta_keywords'], $opt['meta_desc'], $GLOBALS['me']->ID, $extra, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT PAGE URL */

public static function edit_page_url( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'pages' => 'edit' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( !isset( $opt['title'] ) ) {
        return false;
    }

    $url = strtolower( \site\utils::encodeurl( $opt['title'] ) );

    $stmt = $db->stmt_init();

    $stmt->prepare( "SELECT COUNT(*) FROM " . DB_TABLE_PREFIX . "pages WHERE id != ? AND url_title = ?" );
    $stmt->bind_param( "is", $id, $url );
    $stmt->execute();
    $stmt->bind_result( $count );
    $stmt->fetch();

    if( $count > 0 ) {
        return false;
    }

    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "pages SET url_title = ? WHERE id = ?" );
    $stmt->bind_param( "si", $url, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* SET ACTION TO A PAGE */

public static function action_page( $action, $id ) {

    global $db;

    if( !ab_to( array( 'pages' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'publish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "pages SET visible = 1 WHERE id = ?" );
        break;

        case 'unpublish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "pages SET visible = 0 WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* DELETE PAGE */

public static function delete_page( $id ) {

    global $db;

    if( !ab_to( array( 'pages' => 'delete' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "pages WHERE id = ?" );

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        if( $stmt->execute() ) {
            do_action( 'admin_page_deleted', $ID );
        }
    }

    @$stmt->close();

    return true;

}

/* ADD COUNTRY */

public static function add_country( $opt = array() ) {

    global $db;

    if( !ab_to( array( 'locations' => 'add' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $opt['marker'] = preg_replace( '/[(\]*\[\)]/', '$1', $opt['marker'] );
    $opt['marker'] = array_filter( explode( ',', $opt['marker'] ) );

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "countries (user, name, visible, lat, lng, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())" );
    $stmt->bind_param( "isiddi", $GLOBALS['me']->ID, $opt['name'], $opt['publish'],    $opt['marker'][0], $opt['marker'][1], $GLOBALS['me']->ID );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT COUNTRY */

public static function edit_country( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'locations' => 'edit' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $opt['marker'] = preg_replace( '/[(\]*\[\)]/', '$1', $opt['marker'] );
    $opt['marker'] = array_filter( explode( ',', $opt['marker'] ) );

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "countries SET name = ?, visible = ?, lat = ?, lng = ?, lastupdate_by = ?, lastupdate = NOW() WHERE id = ?" );
    $stmt->bind_param( "siddii", $opt['name'], $opt['publish'], $opt['marker'][0], $opt['marker'][1], $GLOBALS['me']->ID, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* SET ACTION TO A COUNTRY */

public static function action_country( $action, $id ) {

    global $db;

    if( !ab_to( array( 'locations' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'publish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "countries SET visible = 1 WHERE id = ?" );
        break;

        case 'unpublish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "countries SET visible = 0 WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* DELETE COUNTRY */

public static function delete_country( $id ) {

    global $db;

    if( !ab_to( array( 'locations' => 'delete' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    foreach( $id as $ID ) {

    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "countries WHERE id = ?" );
    $stmt->bind_param( "i", $ID );

    if( $stmt->execute() ) {

    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "states WHERE country = ?" );
    $stmt->bind_param( "i", $ID );
    $stmt->execute();

    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "cities WHERE country = ?" );
    $stmt->bind_param( "i", $ID );
    $stmt->execute();

    }

    }

    @$stmt->close();

    return true;

}

/* ADD STATE */

public static function add_state( $opt = array() ) {

    global $db;

    if( !ab_to( array( 'locations' => 'add' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $opt['marker'] = preg_replace( '/[(\]*\[\)]/', '$1', $opt['marker'] );
    $opt['marker'] = array_filter( explode( ',', $opt['marker'] ) );

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "states (user, name, country, visible, lat, lng, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );
    $stmt->bind_param( "isiiddi", $GLOBALS['me']->ID, $opt['name'], $opt['country'], $opt['publish'], $opt['marker'][0], $opt['marker'][1], $GLOBALS['me']->ID );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT STATE */

public static function edit_state( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'locations' => 'edit' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $opt['marker'] = preg_replace( '/[(\]*\[\)]/', '$1', $opt['marker'] );
    $opt['marker'] = array_filter( explode( ',', $opt['marker'] ) );

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "states SET name = ?, country = ?, visible = ?, lat = ?, lng = ?, lastupdate_by = ?, lastupdate = NOW() WHERE id = ?" );
    $stmt->bind_param( "siiddii", $opt['name'], $opt['country'], $opt['publish'], $opt['marker'][0], $opt['marker'][1], $GLOBALS['me']->ID, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* SET ACTION TO A STATE */

public static function action_state( $action, $id ) {

    global $db;

    if( !ab_to( array( 'locations' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'publish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "states SET visible = 1 WHERE id = ?" );
        break;

        case 'unpublish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "states SET visible = 0 WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* DELETE STATE */

public static function delete_state( $id ) {

    global $db;

    if( !ab_to( array( 'locations' => 'delete' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    foreach( $id as $ID ) {

    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "states WHERE id = ?" );
    $stmt->bind_param( "i", $ID );

    if( $stmt->execute() ) {

    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "cities WHERE state = ?" );
    $stmt->bind_param( "i", $ID );
    $stmt->execute();

    }

    }

    @$stmt->close();

    return true;

}

/* ADD CITY */

public static function add_city( $opt = array() ) {

    global $db;

    if( !ab_to( array( 'locations' => 'add' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $opt['marker'] = preg_replace( '/[(\]*\[\)]/', '$1', $opt['marker'] );
    $opt['marker'] = array_filter( explode( ',', $opt['marker'] ) );

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "cities (user, name, country, state, visible, lat, lng, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );
    $stmt->bind_param( "isiiiddi", $GLOBALS['me']->ID, $opt['name'], $opt['country'], $opt['state'], $opt['publish'], $opt['marker'][0], $opt['marker'][1], $GLOBALS['me']->ID );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT CITY */

public static function edit_city( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'locations' => 'edit' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['name'] ) ) {
        return false;
    }

    $opt['marker'] = preg_replace( '/[(\]*\[\)]/', '$1', $opt['marker'] );
    $opt['marker'] = array_filter( explode( ',', $opt['marker'] ) );

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "cities SET name = ?, country = ?, state = ?, visible = ?, lat = ?, lng = ?, lastupdate_by = ?, lastupdate = NOW() WHERE id = ?" );
    $stmt->bind_param( "siiiddii", $opt['name'], $opt['country'], $opt['state'], $opt['publish'], $opt['marker'][0], $opt['marker'][1], $GLOBALS['me']->ID, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* SET ACTION TO A CITY */

public static function action_city( $action, $id ) {

    global $db;

    if( !ab_to( array( 'locations' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'publish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "cities SET visible = 1 WHERE id = ?" );
        break;

        case 'unpublish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "cities SET visible = 0 WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* DELETE CITY */

public static function delete_city( $id ) {

    global $db;

    if( !ab_to( array( 'locations' => 'delete' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "cities WHERE id = ?" );

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    @$stmt->close();

    return true;

}

/* ADD STORE LOCATION */

public static function add_store_location( $opt = array() ) {

    global $db;

    if( !ab_to( array( 'stores' => 'edit' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['Store'] ) || empty( $opt['Country'] ) || empty( $opt['State'] ) || empty( $opt['City'] ) ) {
        return false;
    }

    if( !isset( $opt['Address'] ) ) $opt['Address'] = '';
    if( !isset( $opt['Zip'] ) ) $opt['Zip'] = '';

    if( isset( $opt['Marker'] ) ) {

    $opt['Marker'] = preg_replace( '/[(\]*\[\)]/', '$1', $opt['Marker'] );
    $opt['Marker'] = array_filter( explode( ',', $opt['Marker'] ) );

    $lat = $opt['Marker'][0];
    $lng = $opt['Marker'][1];

    } else if( isset( $opt['Lat'] ) && isset( $opt['Lng'] ) ) {

    $lat = $opt['Lat'];
    $lng = $opt['Lng'];

    } else {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "store_locations (user, store, country, countryID, state, stateID, city, cityID, zip, address, lat, lng, point, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, POINT(?, ?), ?, NOW(), NOW())" );

    if( $country = \query\locations::country_exists( $opt['Country'] ) ) {
        list( $country_id, $country_name ) = array( $country['ID'], $country['name'] );
    } else {
        list( $country_id, $country_name ) = array( 0, $opt['Country'] );
    }

    if( $state = \query\locations::state_exists( $opt['State'] ) ) {
        list( $state_id, $state_name ) = array( $state['ID'], $state['name'] );
    } else {
        list( $state_id, $state_name ) = array( 0, $opt['State'] );
    }

    if( $city = \query\locations::city_exists( $opt['City'] ) ) {
        list( $city_id, $city_name ) = array( $city['ID'], $city['name'] );
    } else {
        list( $city_id, $city_name ) = array( 0, $opt['City'] );
    }

    $stmt->bind_param( "iisisisissddddi", $GLOBALS['me']->ID, $opt['Store'], $country_name, $country_id, $state_name, $state_id, $city_name, $city_id, $opt['Zip'], $opt['Address'], $lat, $lng,  $lat, $lng, $GLOBALS['me']->ID );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT STORE LOCATION */

public static function edit_store_location( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'stores' => 'edit' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['Address'] ) ) {
        return false;
    }

    if( isset( $opt['Marker'] ) ) {

    $opt['Marker'] = preg_replace( '/[(\]*\[\)]/', '$1', $opt['Marker'] );
    $opt['Marker'] = array_filter( explode( ',', $opt['Marker'] ) );

    $lat = $opt['Marker'][0];
    $lng = $opt['Marker'][1];

    } else if( isset( $opt['Lat'] ) && isset( $opt['Lng'] ) ) {

    $lat = $opt['Lat'];
    $lng = $opt['Lng'];

    } else {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "store_locations SET country = ?, countryID = ?, state = ?, stateID = ?, city = ?, cityID = ?, zip = ?, address = ?, lat = ?, lng = ?, point = POINT(?, ?), lastupdate_by = ?, lastupdate = NOW() WHERE id = ?" );

    // country
    $country = \query\locations::country_info( $opt['Country'] );

    // state
    $state = \query\locations::state_info( $opt['State'] );

    // city
    $city = \query\locations::city_info( $opt['City'] );

    if( empty( $country->name ) || empty( $state->name ) || empty( $city->name ) ) {
        return false;
    }

    $stmt->bind_param( "sisisissddddii", $country->name, $opt['Country'], $state->name, $opt['State'], $city->name, $opt['City'], $opt['Zip'], $opt['Address'], $lat, $lng, $lat, $lng, $GLOBALS['me']->ID, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* DELETE STORE LOCATION */

public static function delete_store_location( $id ) {

    global $db;

    if( !ab_to( array( 'stores' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "store_locations WHERE id = ?" );

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    @$stmt->close();

    return true;

}

/* ADD USER */

public static function add_user( $opt = array() ) {

    global $db;

    if( !ab_to( array( 'users' => 'add' ) ) ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    if( empty( $opt['name'] ) || empty( $opt['email'] ) || empty( $opt['password'] ) ) {
        return false;
    }

    $stmt = $db->stmt_init();

    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "users (name, email, password, avatar, points, credits, privileges, erole, subscriber, valid, extra, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())" );

    $avatar = \site\images::upload( @$_FILES['logo'], 'avatar_', array(    'path' => DIR . '/', 'current' => '' ) );
    $password = md5( $opt['password'] );
    $extra = \site\utils::array_sanitize( $opt['extra'] );
    $extra = @serialize( $extra );

    $stmt->bind_param( "ssssiiisiis", $opt['name'], $opt['email'], $password, $avatar, $opt['points'], $opt['credits'], $opt['privileges'], @serialize( $opt['erole'] ), $opt['subscriber'], $opt['confirm'], $extra );

    if( $stmt->execute() ) {

    $insert_id = $stmt->insert_id;

    if( !$opt['confirm'] ) {

        $stmt->prepare( "SELECT id FROM " . DB_TABLE_PREFIX . "users WHERE email = ?" );
        $stmt->bind_param( "s", $opt['email'] );
        $stmt->execute();
        $stmt->bind_result( $id );
        $stmt->fetch();
        $stmt->close();

        $cofirm_session = md5( \site\utils::str_random(15) );

        if( \user\mail_sessions::insert( 'confirmation', array( 'user' => $id, 'session' => $cofirm_session ) ) ) {
            \site\mail::send( $opt['email'], t( 'email_acc_title', "Activate account" ) . ' - ' . \query\main::get_option( 'sitename' ), array( 'template' => 'account_confirmation', 'path' => '../' ), array( 'hello_name' => sprintf( t( 'email_text_hello', "Hello %s" ), $opt['name'] ), 'confirmation_main_text' => t( 'email_acc_maintext', "Click on the link bellow to confirm your account." ), 'confirmation_button' => t( 'email_acc_button', "Activate account!" ), 'link' => \site\utils::update_uri( $GLOBALS['siteURL'] . 'verify.php', array( 'user' => $id, 'token' => $cofirm_session ) ) ) );
        }

    }

    return $insert_id;

    }

    $stmt->close();

    return false;

}

/* EDIT USER */

public static function edit_user( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'users' => 'edit' ) ) ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    if( empty( $opt['name'] ) || empty( $opt['email'] ) ) {
        return false;
    }

    $user = \query\main::user_info( $id );

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET name = ?, email = ?, avatar = ?, points = ?, credits = ?, privileges = ?, erole = ?, subscriber = ?, valid = ?, extra= ? WHERE id = ?" );

    $avatar = \site\images::upload( @$_FILES['logo'], 'avatar_', array( 'path' => DIR . '/', 'current' => $user->avatar ) );
    $extra = \site\utils::array_sanitize( $opt['extra'] );
    $extra = array_merge( (array) $user->extra, $extra );
    $extra = @serialize( $extra );
    $erole = @serialize( $opt['erole'] );
    
    $stmt->bind_param( "sssiiisiisi", $opt['name'], $opt['email'], $avatar, $opt['points'], $opt['credits'], $opt['privileges'], $erole, $opt['subscriber'], $opt['confirm'], $extra, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* SET ACTION TO USER */

public static function action_user( $action, $id ) {

    global $db;

    if( !ab_to( array( 'users' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'verify':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET valid = 1 WHERE id = ?" );
        break;

        case 'unverify':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET valid = 0 WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* CHANGE USER PASSWORD */

public static function change_user_password( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'users' => 'edit' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['password'] ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET password = ? WHERE id = ?" );

    $pass = md5( $opt['password'] );

    $stmt->bind_param( "si", $pass, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* BAN USER */

public static function ban_user( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'users' => 'ban' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['date'] ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET ban = FROM_UNIXTIME(?) WHERE id = ?" );
    $stmt->bind_param( "si", $opt['date'], $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* DELETE USER */

public static function delete_user( $id ) {

    global $db;

    if( !ab_to( array( 'users' => 'delete' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    foreach( $id as $ID ) {

    if( \query\main::user_exists( $ID ) ) {

    $user = \query\main::user_info( $ID );

    // don't delete administrators
    if( !$user->is_admin ) {

    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "users WHERE id = ?" );
    $stmt->bind_param( "i", $ID );

    if( $stmt->execute() ) {

    // delete his session
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "sessions WHERE user = ?" );
    $stmt->bind_param( "i", $ID );
    $stmt->execute();

    // clear his favorites
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "favorite WHERE user = ?" );
    $stmt->bind_param( "i", $ID );
    $stmt->execute();

    if( !empty( $user->avatar ) && !preg_match( '/^http(s)?/i', $user->avatar ) ) {
        @unlink( DIR . '/' . $user->avatar );
    }

    }

    }

    }

    }

    @$stmt->close();

    return true;

}

/* DELETE USER AVATAR */

public static function delete_user_avatar( $id ) {

    global $db;

    if( !ab_to( array( 'users' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    foreach( $id as $ID ) {

    if( \query\main::user_exists( $ID ) ) {

    $user = \query\main::user_info( $ID );

    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "users SET avatar = '' WHERE id = ?" );
    $stmt->bind_param( "i", $ID );

    if( $stmt->execute() ) {

    if( !empty( $user->avatar ) && !preg_match( '/^http(s)?/i', $user->avatar ) ) {
        @unlink( DIR . '/' . $user->avatar );
    }

    }

    }

    }

    @$stmt->close();

    return true;

}

/* ADD WIDGET */

public static function add_widget( $zone, $id, $opt = array() ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "widgets (user, theme, widget_id, sidebar, location, title, stop, text, extra, last_update, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())" );

    $theme = \query\main::get_option( 'theme' );
    $extra = @serialize( $opt['extra'] );

    $stmt->bind_param( "isssssiss", $GLOBALS['me']->ID, $theme, $id, $zone, $opt['file'], $opt['title'], $opt['limit'], $opt['text'], $extra );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT WIDGET */

public static function edit_widget( $id, $opt = array() ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "widgets SET title = ?, stop = ?, type = ?, orderby = ?, position = ?, text = ?, extra = ?, html = ?, mobile_view = ?, last_update = NOW() WHERE id = ?" );

    $extra = \site\utils::array_sanitize( $opt['extra'] );
    $extra = @serialize( $extra );

    $stmt->bind_param( "sississiii", $opt['title'], $opt['limit'], $opt['type'], $opt['order'], $opt['position'], $opt['text'], $extra, $opt['allow_html'], $opt['mobi_view'], $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* DELETE WIDGET */

public static function delete_widget( $zone, $id ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "widgets WHERE id = ? AND theme = ? AND sidebar = ?" );

    $theme = \query\main::get_option( 'theme' );

    $stmt->bind_param( "iss",    $id, $theme, $zone);
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return true;

}

/* SET SUGGESTION AS READ */

public static function action_suggestions( $action, $id ) {

    global $db;

    if( !ab_to( array( 'suggestions' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'read':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "suggestions SET viewed = 1 WHERE id = ?" );
        break;

        case 'unread':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "suggestions SET viewed = 0 WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* DELETE SUGGESTION */

public static function delete_suggestion( $id ) {

    global $db;

    if( !ab_to( array( 'suggestions' => 'delete' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "suggestions WHERE id = ?" );

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    @$stmt->close();

    return true;

}

/* ADD REVIEW */

public static function add_review( $opt = array() ) {

    global $db;

    if( !ab_to( array( 'reviews' => 'add' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['text'] ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "reviews (user, store, text, stars, valid, lastupdate_by, lastupdate, date) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())" );
    $stmt->bind_param( "iisiii", $opt['user'], $opt['store'], $opt['text'], $opt['stars'], $opt['publish'], $GLOBALS['me']->ID );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT REVIEW */

public static function edit_review( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'reviews' => 'edit' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['text'] ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "reviews SET user = ?, store = ?, text = ?, stars = ?, valid = ?, lastupdate_by = ?, lastupdate = NOW() WHERE id = ?" );
    $stmt->bind_param( "iisiiii", $opt['user'], $opt['store'], $opt['text'], $opt['stars'], $opt['publish'], $GLOBALS['me']->ID, $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* SET ACTION TO REVIEW */

public static function action_review( $action, $id ) {

    global $db;

    if( !ab_to( array( 'reviews' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'publish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "reviews SET valid = 1 WHERE id = ?" );
        break;

        case 'unpublish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "reviews SET valid = 0 WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* DELETE REVIEW */

public static function delete_review( $id ) {

    global $db;

    if( !ab_to( array( 'reviews' => 'delete' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "reviews WHERE id = ?" );

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    @$stmt->close();

    return true;

}

/* EDIT A PAGE IN THEME */

public static function edit_theme_page( $id, $opt = array() ) {

    if( !$GLOBALS['me']->is_admin ) return false;

    $opt = array_map( 'trim', $opt );

    if( file_exists( DIR . '/' . THEMES_LOC . '/' . $id . '/' . $opt['page'] ) ) {
        if( !chmod( DIR . '/' . THEMES_LOC . '/' . $id . '/' . $opt['page'], 0777 ) ) {
            return false;
        }

        if( file_put_contents( DIR . '/' . THEMES_LOC . '/' . $id . '/' . $opt['page'], $opt['text'] ) ) {
            chmod( DIR . '/' . THEMES_LOC . '/' . $id . '/' . $opt['page'], 0644 );
            return true;
        }
    }

    return false;

}

/* EXTRACT THEME */

public static function extract_theme( $theme = '', $location = '' ) {

    if( !$GLOBALS['me']->is_admin ) return false;

    if( \site\utils::get_extension( basename( $theme ) ) !== '.zip' ) {
        throw new \Exception( t( 'themes_only_zip', "Please upload only themes in ZIP format." ) );
    }

    if( empty( $location ) ) {

    if( !$file = @file_put_contents( ( $temploc = DIR . '/' . TEMP_LOCATION . '/theme-' . time() . '.zip' ), file_get_contents( $theme ) ) ) {
        throw new \Exception( t( 'themes_wrongurl', "Sorry, but this URL seems to be invalid." ) );
    }

    $location = $uplocation = $temploc;

    }

    $zip = new \ZipArchive;

    if ( $zip->open( $location ) ) {

        $files_map = array();
        $files_map['tfiles'] = $files_map['mtfiles'] = $files_map['main_dirs'] = array();

        for( $i = 0; $i < $zip->numFiles; $i++ ) {
            if( preg_match( '/^([^\/]*)\/$/', $zip->getNameIndex( $i ) ) )
            $files_map['main_dirs'][] = $zip->getNameIndex( $i );
            else {
                if( substr_count( $zip->getNameIndex( $i ), '/' ) == 1 ) {
                    $files_map['mtfiles'][] = $zip->getNameIndex( $i );
                }
                $files_map['tfiles'][] = $zip->getNameIndex( $i );
            }
        }

        if( count( $files_map['main_dirs'] ) === 0 ) {
            // delete the temporary file
            if( isset( $uplocation ) ) @unlink( $uplocation );
            throw new \Exception( 'Directory missing' );
        }

        if( count( $files_map['main_dirs'] ) > 1 ) {
            // delete the temporary file
            if( isset( $uplocation ) ) @unlink( $uplocation );
            throw new \Exception( 'Too many directories' );
        }

        if( is_dir( DIR . '/' . THEMES_LOC .'/' . $files_map['main_dirs'][0] ) ) {
            // delete the temporary file
            if( isset( $uplocation ) ) @unlink( $uplocation );
            throw new \Exception( sprintf( t( 'themes_theme_exists', "%s directory already exists. The theme exists?" ), rtrim( $files_map['main_dirs'][0], '/' ) ) );
        }

        // all files inside theme

        if( !template::theme_have_min( array_map( 'basename', $files_map['mtfiles'] ) ) ) {
            // delete the temporary file
            if( isset( $uplocation ) ) @unlink( $uplocation );
            throw new \Exception( t( 'msg_invalid_theme', "Sorry, but this theme seems to be invalid." ) );
        }

        $extract = $zip->extractTo( DIR . '/' . THEMES_LOC, array_merge( $files_map['main_dirs'], $files_map['tfiles'] ) );

        $zip->close();

        if( !$extract ) {
            // delete the temporary file
            if( isset( $uplocation ) ) @unlink( $uplocation );
            throw new \Exception( t( 'themes_extracting_error', "Sorry, but this theme can't be unziped." ) );
        }

    } else {

        // delete the temporary file
        if( isset( $uplocation ) ) @unlink( $uplocation );
        throw new \Exception( t( 'themes_cantunzip', "Sorry, but your theme could not be unzipped." ) );

    }

    if( isset( $uplocation ) ) @unlink( $uplocation );

    if( file_exists( DIR . '/' . THEMES_LOC . '/' . $files_map['main_dirs'][0] . '/_install.php' ) ) {
        require_once DIR . '/' . THEMES_LOC . '/' . $files_map['main_dirs'][0] . '/_install.php';
    }

    return true;

}

/* EDIT THEME OPTIONS */

public static function edit_theme_options( $id, $opt = array() ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $is_valid_opt = \query\main::get_option( 'theme_options_' . $id );

    if( $is_valid_opt !== NULL ) {

        $opt = \site\utils::array_sanitize( $opt );

        self::set_option( array( 'theme_options_' . $id => @serialize( $opt ) ) );

        return $opt;

    } else {

        $opt = \site\utils::array_sanitize( $opt );

        \query\main::add_option( 'theme_options_' . $id, $opt, true );

        return $opt;

    }

}

/* EDIT THEME MENU */

public static function edit_menu( $id, $opt = array() ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $is_valid_opt = \query\main::get_option( 'links_menu_' . $id );

    if( $is_valid_opt !== NULL ) {

        $opt = \site\utils::array_sanitize( $opt );

        self::set_option( array( 'links_menu_' . $id => @serialize( $opt ) ) );

        if( empty( $opt ) ) {
            return true;
        }

        return $opt;

    } else {

        $opt = \site\utils::array_sanitize( $opt );

        \query\main::add_option( 'links_menu_' . $id, $opt, true );

        return $opt;

    }

}

/* DELETE THEME */

public static function delete_theme( $themes ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $themes = (array) $themes;

    foreach( $themes as $theme ) {

        if( \query\main::get_option( 'theme' ) !== $theme ) {
            if( file_exists( DIR . '/' . THEMES_LOC . '/' . $theme . '/_delete.php' ) ) {
                require_once DIR . '/' . THEMES_LOC . '/' . $theme . '/_delete.php';
            }
            
            \site\files::delete_directory( DIR . '/' . THEMES_LOC . '/' . $theme );
        }

    }

    return true;

}

/* EDIT A PAGE IN A PLUGIN */

public static function edit_plugin_page( $id, $opt = array() ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $opt = array_map( 'trim', $opt );

    $page = DIR . '/' . UPDIR . '/' . $id . '/' . $opt['page'];

    if( file_exists( $page ) ) {
        if( !is_writable( $page ) && !chmod( $page, 0777 ) ) {
            return false;
        }

        if( file_put_contents( $page, $opt['text'] ) ) {
            chmod( $page, 0644 );
            return true;
        }
    }

    return false;

}

/* EXTRACT PLUGIN */

public static function extract_plugin( $plugin = '', $location = '' ) {

    if( !$GLOBALS['me']->is_admin ) return false;

    if( \site\utils::get_extension( basename( $plugin ) ) !== '.zip' ) {
        throw new \Exception( t( 'plugins_only_zip', "Please upload only plugins in ZIP format." ) );
    }

    if( empty( $location ) ) {

    if( !$file = @file_put_contents( ( $temploc = DIR . '/' . TEMP_LOCATION . '/plugin-' . time() . '.zip' ), file_get_contents( $plugin )) ) {
        throw new \Exception( t( 'plugins_wrongurl', "Sorry, but this URL seems to be invalid." ) );
    }

    $location = $uplocation = $temploc;

    }

    $zip = new \ZipArchive;

    if ( $zip->open( $location ) ) {

        $files_map = array();
        $files_map['pfiles'] = $files_map['main_dirs'] = array();

        for( $i = 0; $i < $zip->numFiles; $i++ ) {
            if( preg_match( '/^([^\/]*)\/$/', $zip->getNameIndex( $i ) ) )
            $files_map['main_dirs'][] = $zip->getNameIndex( $i );
            else
            $files_map['pfiles'][] = $zip->getNameIndex( $i );
        }

        if( count( $files_map['main_dirs'] ) === 0 ) {
            // delete the temporary file
            if( isset( $uplocation ) ) @unlink( $uplocation );
            throw new \Exception( t( 'plugins_err_dirmiss', "Plugin directory is missing." ) );
        }

        if( count( $files_map['main_dirs'] ) > 1 ) {
            // delete the temporary file
            if( isset( $uplocation ) ) @unlink( $uplocation );
            throw new \Exception( t( 'plugins_err_manydirs', "Too many directories in this archive." ) );
        }

        if( is_dir( DIR . '/' . UPDIR .'/' . $files_map['main_dirs'][0] ) ) {
            // delete the temporary file
            if( isset( $uplocation ) ) @unlink( $uplocation );
            throw new \Exception( sprintf( t( 'plugins_plugin_exists', "%s directory already exists. The plugin exists?" ), rtrim( $files_map['main_dirs'][0], '/' ) ) );
        }

        // all files inside plugin
        $pfiles = array();
        foreach( $files_map['pfiles'] as $file ) {
            if( preg_match( '/^([^\/]*)\//', $file ) )
            $pfiles[] = $file;
        }

        $extract = $zip->extractTo( DIR . '/' . UPDIR, array_merge( $files_map['main_dirs'], $pfiles ) );

        $zip->close();

        if( !$extract ) {

            // delete the temporary file
            if( isset( $uplocation ) ) @unlink( $uplocation );
            throw new \Exception( t( 'plugins_extracting_error', "Sorry, but this plugin can't be unziped." ) );

        } else {

            /* Without errors until installation,
            Then try to install it. */

            try {
                $install = (new plugin_installer( $files_map['main_dirs'][0] ))->install();
                if( isset( $uplocation ) ) @unlink( $uplocation );
            }

            catch( Exception $e ){
                // delete the temporary files
                if( isset( $uplocation ) ) @unlink( $uplocation );
                \site\files::delete_directory( DIR . '/' . UPDIR . '/' . $files_map['main_dirs'][0] );
                throw new \Exception( $e->getMessage() );
            }

        }

    } else {

        // delete the temporary file
        if( isset( $uplocation ) ) @unlink( $uplocation );
        throw new \Exception( t( 'themes_cantunzip', "Sorry, but your theme could not be unzipped." ) );

    }

    if( isset( $uplocation ) ) @unlink( $uplocation );

    return true;

}

/* EDIT PLUGIN */

public static function edit_plugin( $id, $opt = array() ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $opt = array_map( 'trim', $opt );

    $plugin = admin_query::plugin_info( $id );

    $image = \site\images::upload( @$_FILES['image'], 'plugin_', array( 'path' => DIR . '/', 'current' => $plugin->image ) );

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "plugins SET image = ?, menu = ?, menu_icon = ?, subadmin_view = ?, description = ?, visible = ? WHERE id = ?" );
    $stmt->bind_param( "siiisii", $image, $opt['menu'], $opt['icon'],    $opt['subadmin_v'], $opt['description'], $opt['publish'], $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* SET ACTION TO PLUGIN */

public static function action_plugin( $action, $id ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'publish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "plugins SET visible = 1 WHERE id = ?" );
        break;

        case 'unpublish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "plugins SET visible = 0 WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* DELETE PLUGIN */

public static function delete_plugin( $id ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "plugins WHERE id = ?" );

    foreach( $id as $ID ) {

    $plugin = admin_query::plugin_info( $ID );

    // delete plugin
    $stmt->bind_param( "i", $ID );
    $stmt->execute();

    // directory
    $dir = rtrim( dirname( $plugin->main_file ), '/' );

    // delete tables
    if( isset( $plugin->uninstall_preview['delete']['tables'] ) ) {
        $tables = explode( ',', $plugin->uninstall_preview['delete']['tables'] );
        foreach( array_map( 'trim', $tables ) as $table ) {
            $table = \site\plugin::replace_constant( $table );
            $db->query( "DROP TABLE `{$table}`" );
        }
    }

    // delete options
    if( isset( $plugin->uninstall_preview['delete']['options'] ) ) {
        $rows = explode( ',', $plugin->uninstall_preview['delete']['options'] );
        foreach( array_map( 'trim', $rows ) as $row ) {
            $db->query( "DELETE FROM `" . DB_TABLE_PREFIX . "options` WHERE `option_name` = '{$row}'" );
        }
    }

    // delete table columns
    if( isset( $plugin->uninstall_preview['delete']['columns'] ) ) {
        $columns = explode( ',', $plugin->uninstall_preview['delete']['columns'] );
        foreach( array_map( 'trim', $columns ) as $column ) {
            $coltab = explode( '/', $column );
            if( count( $coltab ) === 2 ) {
                $table = \site\plugin::replace_constant( $coltab[1] );
                $db->query( "ALTER TABLE `{$table}` DROP {$coltab[0]}" );
            }
        }
    }

    // delete head lines
    $db->query( "DELETE FROM `" . DB_TABLE_PREFIX . "head` WHERE `plugin` = '{$dir}'" );

    /* Resolve possible problems caused by uninstalling */

    switch( $plugin->scope ) {
        case 'language':
        if( \query\main::get_option( 'sitelang' ) == 'up_' . strtolower( $plugin->name ) ) {
            self::set_option( array( 'sitelang' => 'english' ) );
        }
        if( \query\main::get_option( 'adminpanel_lang' ) == 'up_' . strtolower( $plugin->name ) ) {
            self::set_option( array( 'adminpanel_lang' => 'english' ) );
        }
        break;
     }


    // delete plugin directory
    \site\files::delete_directory( DIR . '/' . UPDIR . '/' . $dir );

    // delete image, if plugins has an image
    @unlink( DIR . '/' . $plugin->image );

    }

    @$stmt->close();

    return true;

}

/* DELETE PLUGIN IMAGE */

public static function delete_plugin_image( $id ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    foreach( $id as $ID ) {

    if( admin_query::plugin_exists( $ID ) ) {

    $plugin = admin_query::plugin_info( $ID );

    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "plugins SET image = '' WHERE id = ?" );
    $stmt->bind_param( "i", $ID );
    $stmt->execute();

    if( !empty( $plugin->image ) && !preg_match( '/^http(s)?/i', $plugin->image ) ) {
        @unlink( DIR . '/' . $plugin->image );
    }

    }

    }

    @$stmt->close();

    return true;

}

/* DELETE BANNED IP */

public static function delete_banned( $id ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "banned WHERE id = ?" );

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    @$stmt->close();

    return true;

}

/* ADD BANNED IP */

public static function add_banned( $opt = array() ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['ipaddr'] ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "banned (ipaddr, registration, login, site, redirect_to, expiration, expiration_date, date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())" );
    $stmt->bind_param( "siiisss", $opt['ipaddr'], $opt['registration'], $opt['login'], $opt['site'], $opt['redirect'], $opt['expiration'], $opt['expiration_date'] );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT BANNED IP */

public static function edit_banned( $id, $opt = array() ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $opt = array_map( 'trim', $opt );

    if( empty( $opt['ipaddr'] ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "banned SET ipaddr = ?, registration = ?, login = ?, site = ?, redirect_to = ?, expiration = ?, expiration_date = ? WHERE id = ?" );
    $stmt->bind_param( "siiisssi", $opt['ipaddr'], $opt['registration'], $opt['login'], $opt['site'], $opt['redirect'], $opt['expiration'], $opt['expiration_date'], $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* DELETE NEWS */

public static function delete_news( $id ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "news WHERE newsID = ?" );

    foreach( $id as $ID ) {
    $stmt->bind_param( "i", $ID );
    $stmt->execute();
    }

    @$stmt->close();

    return true;

}

/* DELETE USER SESSIONS */

public static function delete_sessions( $id ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "sessions WHERE id = ?" );

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    @$stmt->close();

    return true;

}

/* EDIT SUBSCRIBER */

public static function edit_subscriber( $id, $opt = array() ) {

    global $db;

    if( !ab_to( array( 'subscribers' => 'edit' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    if( !filter_var( $opt['email'], FILTER_VALIDATE_EMAIL ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "newsletter SET email = ?, econf = ? WHERE id = ?" );
    $stmt->bind_param( "sii", $opt['email'], $opt['confirm'], $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* IMPORT SUBSCRIBERS */

public static function import_subscribers( $opt = array() ) {

    global $db;

    if( !ab_to( array( 'subscribers' => 'import' ) ) ) return false;

    $opt = array_map( 'trim', $opt );

    preg_match_all( '/([a-z0-9-_.]+)\@([a-z0-9-_]+)\.([a-z]+)/i', $opt['emails'], $email );

    $emails = array_map( 'strtolower', $email[0] );

    if( empty( $emails ) ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "newsletter (email, econf, date) VALUES (?, ?, NOW())" );

    foreach( $emails as $email ) {
        $stmt->bind_param( "si", $email, $opt['confirm'] );
        if( $stmt->execute() ) {
            do_action( 'subscribe-import', array( 'email' => $email ) );
        }
    }

    $stmt->close();

    return true;

}

/* SET ACTION TO SUBSCRIBER */

public static function action_subscriber( $action, $id ) {

    global $db;

    if( !ab_to( array( 'subscribers' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'verify':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "newsletter SET econf = 1 WHERE id = ?" );
        break;

        case 'unverify':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "newsletter SET econf = 0 WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* DELETE SUBSCRIBER */

public static function delete_subscriber( $id ) {

    global $db;

    if( !ab_to( array( 'subscribers' => 'delete' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "newsletter WHERE id = ?" );

    foreach( $id as $ID ) {
    $stmt->bind_param( "i", $ID );
    $stmt->execute();
    }

    @$stmt->close();

    return true;

}

/* ADD REWARD */

public static function add_reward( $opt = array() ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $opt = array_map( function( $w ) {
        if( !is_array( $w ) ) return trim( $w );
        return $w;
    }, $opt );

    if( empty( $opt['name'] ) || $opt['points'] <= 0 ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "rewards (user, points, title, description, image, fields, lastupdate_by, lastupdate, visible, date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())" );

    $image = \site\images::upload( @$_FILES['logo'], 'reward_', array( 'path' => DIR . '/', 'current' => '' ) );

    $fields = array();
    for( $i = 0; $i < count( $opt['fields']['name'] ); $i++ ) {
        if( !empty( $opt['fields']['name'][$i] ) )
        $fields[] = array( 'name' => $opt['fields']['name'][$i], 'type' => $opt['fields']['type'][$i], 'value' => $opt['fields']['value'][$i], 'require' => ( isset( $opt['fields']['require'][$i] ) && in_array( $opt['fields']['require'][$i], array( 1, 2 ) ) ? $opt['fields']['require'][$i] : 0 ) );
    }

    $fields = @serialize( $fields );

    $stmt->bind_param( "iissssii", $GLOBALS['me']->ID, $opt['points'], $opt['name'], $opt['description'], $image, $fields, $GLOBALS['me']->ID, $opt['publish'] );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT REWARD */

public static function edit_reward( $id, $opt = array() ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $opt = array_map( function( $w ) {
        if( !is_array( $w ) ) return trim( $w );
        return $w;
    }, $opt );

    if( empty( $opt['name'] ) || $opt['points'] <= 0 ) {
        return false;
    }

    $reward = \query\main::reward_info( $id );

    $avatar = \site\images::upload( @$_FILES['logo'], 'reward_', array( 'path' => DIR . '/', 'current' => $reward->image ) );

    $fields = array();
    for( $i = 0; $i < count( $opt['fields']['name'] ); $i++ ) {
        if( !empty( $opt['fields']['name'][$i] ) )
        $fields[] = array( 'name' => $opt['fields']['name'][$i], 'type' => $opt['fields']['type'][$i], 'value' => $opt['fields']['value'][$i], 'require' => ( isset( $opt['fields']['require'][$i] ) && in_array( $opt['fields']['require'][$i], array( 1, 2 ) ) ? $opt['fields']['require'][$i] : 0 ) );
    }

    $fields = @serialize( $fields );

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "rewards SET points = ?, title = ?, description = ?, image = ?, fields = ?, lastupdate_by = ?, lastupdate = NOW(), visible = ? WHERE id = ?" );
    $stmt->bind_param( "issssiii", $opt['points'], $opt['name'], $opt['description'], $avatar, $fields, $GLOBALS['me']->ID, $opt['publish'], $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* DELETE REWARD */

public static function delete_reward( $id ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "rewards WHERE id = ?" );

    foreach( $id as $ID ) {

    if( \query\main::reward_exists( $ID ) ) {

    $reward = \query\main::reward_info( $ID );

    $stmt->bind_param( "i", $ID );

    if( $stmt->execute() && !empty( $reward->image ) && !preg_match( '/^http(s)?/i', $reward->image ) ) {
        @unlink( DIR . '/' . $reward->image );
    }

    }

    }

    @$stmt->close();

    return true;

}

/* SET ACTION TO REWARD */

public static function action_reward( $action, $id ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'publish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "rewards SET visible = 1 WHERE id = ?" );
        break;

        case 'unpublish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "rewards SET visible = 0 WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
    $stmt->bind_param( "i", $ID );
    $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* DELETE REWARD IMAGE */

public static function delete_reward_image( $id ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "rewards SET image = '' WHERE id = ?" );

    foreach( $id as $ID ) {

    if( \query\main::reward_exists( $ID ) ) {

    $reward = \query\main::reward_info( $ID );

    $stmt->bind_param( "i", $ID );

    if( $stmt->execute() && !empty( $reward->image ) && !preg_match( '/^http(s)?/i', $reward->image ) ) {
        @unlink( DIR . '/' . $reward->image );
    }

    }

    }

    @$stmt->close();

    return true;

}

/* ADD PAYMENT PLAN */

public static function add_payment_plan( $opt = array() ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $opt = array_map( 'trim', $opt );

    $opt['price'] = \site\utils::make_money_format( $opt['price'] );

    if( empty( $opt['name'] ) || $opt['price'] < 0 || $opt['credits'] <= 0 ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "p_plans (user, name, description, price, credits, image, lastupdate_by, lastupdate, visible, date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())" );

    $image = \site\images::upload( @$_FILES['logo'], 'payment_plan_', array(    'path' => DIR . '/', 'current' => '' ) );

    $stmt->bind_param( "issdisii", $GLOBALS['me']->ID, $opt['name'], $opt['description'], $opt['price'], $opt['credits'], $image, $GLOBALS['me']->ID, $opt['publish'] );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* EDIT REWARD */

public static function edit_payment_plan( $id, $opt = array() ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $opt = array_map( 'trim', $opt );

    $opt['price'] = \site\utils::make_money_format( $opt['price'] );

    if( empty( $opt['name'] ) || $opt['price'] < 0 || $opt['credits'] <= 0 ) {
        return false;
    }

    $plan = \query\payments::plan_info( $id );

    $avatar = \site\images::upload( @$_FILES['logo'], 'payment_plan_', array( 'path' => DIR . '/', 'current' => $plan->image ) );

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_plans SET name = ?, description = ?, price = ?, credits = ?, image = ?, lastupdate_by = ?, lastupdate = NOW(), visible = ? WHERE id = ?" );
    $stmt->bind_param( "ssdisiii", $opt['name'], $opt['description'], $opt['price'], $opt['credits'], $avatar, $GLOBALS['me']->ID, $opt['publish'], $id );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* DELETE REWARD */

public static function delete_payment_plan( $id ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "p_plans WHERE id = ?" );

    foreach( $id as $ID ) {

    if( \query\payments::plan_exists( $ID ) ) {

    $plan = \query\payments::plan_info( $ID );

    $stmt->bind_param( "i", $ID );

    if( $stmt->execute() && !empty( $plan->image ) && !preg_match( '/^http(s)?/i', $plan->image ) ) {
        @unlink( DIR . '/' . $plan->image );
    }

    }

    }

    @$stmt->close();

    return true;

}

/* DELETE PAYMENT PLAN IMAGE */

public static function delete_payment_plan_image( $id ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_plans SET image = '' WHERE id = ?" );

    foreach( $id as $ID ) {

    if( \query\payments::plan_exists( $ID ) ) {

    $plan = \query\payments::plan_info( $ID );

    $stmt->bind_param( "i", $ID );

    if( $stmt->execute() && !empty( $plan->image ) && !preg_match( '/^http(s)?/i', $plan->image ) ) {
        @unlink( DIR . '/' . $plan->image );
    }

    }

    }

    @$stmt->close();

    return true;

}

/* SET ACTION TO PAYMANT PLAN */

public static function payment_plan_action( $action, $id ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'publish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_plans SET visible = 1 WHERE id = ?" );
        break;

        case 'unpublish':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_plans SET visible = 0 WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
    $stmt->bind_param( "i", $ID );
    $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* DELETE PAYMENT - INVOICE */

public static function delete_payment( $id ) {

    global $db;

    if( !$GLOBALS['me']->is_admin ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "p_transactions WHERE id = ?" );

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    @$stmt->close();

    return true;

}

/* SET ACTION TO A PAYMENT TRANSACTION - INVOICE */

public static function action_payment( $action, $id ) {

    global $db;

    if( !ab_to( array( 'payments' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'paid':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_transactions SET paid = 1 WHERE id = ?" );
        break;

        case 'unpaid':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_transactions SET paid = 0 WHERE id = ?" );
        break;
        case 'delivered':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_transactions SET delivered = 1 WHERE id = ?" );
        break;

        case 'undelivered':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "p_transactions SET delivered = 0 WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* SET ACTION TO REWARD REQUEST */

public static function action_reward_req( $action, $id ) {

    global $db;

    if( !ab_to( array( 'claim_reqs' => 'edit' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    switch( $action ) {
        case 'claim':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "rewards_reqs SET claimed = 1 WHERE id = ?" );
        break;

        case 'unclaim':
            $stmt->prepare( "UPDATE " . DB_TABLE_PREFIX . "rewards_reqs SET claimed = 0 WHERE id = ?" );
        break;

        default:
            return false;
        break;
    }

    foreach( $id as $ID ) {
    $stmt->bind_param( "i", $ID );
    $stmt->execute();
    }

    $stmt->close();

    return true;

}

/* DELETE REWARD REQUEST */

public static function delete_reward_req( $id ) {

    global $db;

    if( !ab_to( array( 'claim_reqs' => 'delete' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "rewards_reqs WHERE id = ?" );

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    @$stmt->close();

    return true;

}

/* POST CHAT MESSAGE */

public static function post_chat_message( $msg ) {

    global $db;

    if( !ab_to( array( 'chat' => 'add' ) ) ) return false;

    if( trim( $msg ) == '' ) {
        return false;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "chat (user, text, date) VALUES (?, ?, NOW())" );
    $stmt->bind_param( "is", $GLOBALS['me']->ID, $msg );
    $execute = $stmt->execute();
    $stmt->close();

    if( $execute ) {
        return true;
    }

    return false;

}

/* DELETE CHAT MESSAGE */

public static function delete_chat_message( $id ) {

    global $db;

    if( !ab_to( array( 'chat' => 'delete' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "chat WHERE id = ?" );

    foreach( $id as $ID ) {
        $stmt->bind_param( "i", $ID );
        $stmt->execute();
    }

    @$stmt->close();

    return true;

}

/* CLEAR EXPIRED INFORMATION */

public static function cleardata( $extra = array() ) {

    global $db;

    $stmt = $db->stmt_init();

    // clear all expired email sessions
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "email_sessions WHERE expiration < NOW()" );
    $stmt->execute();

    // clear all expired banned IPs
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "banned WHERE expiration = 1 AND expiration_date < NOW()" );
    $stmt->execute();

    // clear all expired sessions
    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "sessions WHERE expiration < NOW()" );
    $stmt->execute();

    // delete expired coupons
    if( isset( $extra['coupons']['status'] ) && $extra['coupons']['status'] && isset( $extra['coupons']['interval'] ) && $extra['coupons']['interval'] !== 0 ) {

    $stmt->prepare( "SELECT id, image, source FROM " . DB_TABLE_PREFIX . "coupons WHERE DATE_ADD(expiration, INTERVAL " . $extra['coupons']['interval'] . " DAY) < NOW()" );
    $stmt->execute();
    $stmt->bind_result( $id, $image, $source );

    $d_coupons = [];
    while( $stmt->fetch() ) {
        $d_coupons[$id] = [ 'image' => $image, 'source' => $source ];
    }

    if( !empty( $d_coupons ) ) {

        foreach( $d_coupons as $cid => $cinfo ) {
            $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "coupons WHERE id = ?" );
            $stmt->bind_param( "i", $cid );

            if( $stmt->execute() ) {

                if( !empty( $cinfo['source'] ) && !preg_match( '/^http(s)?/i', $cinfo['source'] ) ) {
                    @unlink( DIR . '/' . $cinfo['source'] );
                }

                if( !empty( $cinfo['image'] ) && !preg_match( '/^http(s)?/i', $cinfo['image'] ) ) {
                    @unlink( DIR . '/' . $cinfo['image'] );
                }

            }
        }

    }

    }

    // delete expired products
    if( isset( $extra['products']['status'] ) && $extra['products']['status'] && isset( $extra['products']['interval'] ) && $extra['products']['interval'] !== 0 ) {

    $stmt->prepare( "SELECT id, image FROM " . DB_TABLE_PREFIX . "products WHERE DATE_ADD(expiration, INTERVAL " . $extra['coupons']['interval'] . " DAY) < NOW()" );
    $stmt->execute();
    $stmt->bind_result( $id, $image );

    $d_products = [];
    while( $stmt->fetch() ) {
        $d_products[$id] = [ 'image' => $image ];
    }

    if( !empty( $d_products ) ) {

        foreach( $d_products as $pid => $pinfo ) {
            $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "products WHERE id = ?" );
            $stmt->bind_param( "i", $pid );

            if( $stmt->execute() && !empty( $cinfo['image'] ) && !preg_match( '/^http(s)?/i', $cinfo['image'] ) ) {
                @unlink( DIR . '/' . $cinfo['image'] );
            }
        }

    }

    }

    // delete expired votes - for coupons
    $dexv_limit = (int) \query\main::get_option( 'delete_old_votes' );
    if( $dexv_limit !== 0 ) {

    $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "coupon_votes WHERE DATE_ADD(date, INTERVAL " . $dexv_limit . " DAY) < NOW()" );
    $stmt->execute();

    }

    $stmt->close();

}

/* ADD IMAGE IN GALLERY */

public static function upload_gallery_image( $opt = array() ) {

    global $db;

    if( !ab_to( array( 'gallery' => 'upload' ) ) ) return false;

    $opt = \site\utils::array_map_recursive( 'trim', $opt );

    $stmt = $db->stmt_init();
    $stmt->prepare( "INSERT INTO " . DB_TABLE_PREFIX . "gallery (user, title, cat_id, sizes, date) VALUES (?, ?, ?, ?, NOW())" );

    $image = \site\images::upload( $opt['file'], 'gallery_', array( 'path' => DIR . '/' ) );

    if( !$image ) {
        $stmt->close();
        return false;
    }

    $sizes = @serialize( array( 'original' => $image ) );

    $stmt->bind_param( "isss", $GLOBALS['me']->ID, $opt['file']['name'], $opt['cat_id'], $sizes );

    if( $stmt->execute() ) {
        $insert_id = $stmt->insert_id;
        $stmt->close();

        return $insert_id;
    }

    $stmt->close();

    return false;

}

/* DELETE GALLERY IMAGE */

public static function delete_gallery_image( $id ) {

    global $db;

    if( !ab_to( array( 'gallery' => 'delete' ) ) ) return false;

    $id = (array) $id;

    $stmt = $db->stmt_init();

    foreach( $id as $ID ) {

        if( \query\gallery::exists( $ID ) ) {

            $image = \query\gallery::image_info( $ID );

            $stmt->prepare( "DELETE FROM " . DB_TABLE_PREFIX . "gallery WHERE id = ?" );
            $stmt->bind_param( "i", $ID );

            if( $stmt->execute() && !empty( $image->sizes ) ) {
                foreach( $image->sizes as $size ) {
                    if( !preg_match( '/^http(s)?/i', $size) ) {
                        @unlink( DIR . '/' . $size );
                    }
                }
            }

        }

    }

    @$stmt->close();

    return true;

}

}