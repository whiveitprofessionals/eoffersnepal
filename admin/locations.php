<?php

switch( $_GET['action'] ) {

/* ADD COUNTRY */

case 'country_add':

if( !ab_to( array( 'locations' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'loc_cou_add_title', "Add Country" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=locations.php&amp;action=countries" class="btn">' . t( 'locations_country_view', "View Countries" ) . '</a>
</div>';

$subtitle = t( 'loc_cou_add_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_locations_page', 'after_title_add_country_locations_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'locations_csrf' ) ) {

    if( isset( $_POST['name'] ) && isset( $_POST['mapmarker'] ) )
    if( admin\actions::add_country(
    array(
    'name' => $_POST['name'],
    'marker' => $_POST['mapmarker'],
    'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 )
    ) ) )

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf = $_SESSION['locations_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>' . t( 'loc_form_country', "Country" ) . ':</span><div><input type="text" name="name" value="" required /></div></div>';
if( ( $mapskey = \query\main::get_option( 'google_maps_key' ) ) && !empty( $mapskey ) ) {
    echo '<div class="row"><span>' . t( 'form_map', "Map" ) . ':</span><div id="map" style="height: 400px;"></div></div>';
}
echo '<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish" checked /> <label for="publish"><span></span> ' . t( 'msg_publoc', "Publish this location" ) . '</label></div></div>

<input type="hidden" name="mapmarker" value="39.82,-101.47" />
<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div><button class="btn btn-important">' . t( 'loc_cou_add_button', "Add Country" ) . '</button></div>
    <div></div>
</div>

</form>

</div>';

if( !empty( $mapskey ) ) {

$use_places = (boolean) \query\main::get_option( 'google_maps_places' );

if( $use_places ) echo '<input id="pac-input" />'; ?>

<script>

var marker, map, deflat, deflng;

deflat = 39.82;
deflng = -101.47;

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 6,
        center: {lat: deflat, lng: deflng}
    });

    marker = new google.maps.Marker({
        map: map,
        draggable: true,
        position: {lat: deflat, lng: deflng}
    });

    marker.addListener('dragend', toggleBounce);

    <?php if( $use_places ) { ?>

    // Create the search box and link it to the UI element.
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    searchBox.addListener('places_changed', function(){
        var places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }

            marker.setPosition(place.geometry.location);
            map.setCenter(place.geometry.location);

            setMarker(place.geometry.location);
        });
    });

    <?php } ?>
}

function toggleBounce() {
    setMarker(this.getPosition());
}

function setMarker( position ) {
    $('input[name="mapmarker"]').val(position);
}

</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_html( $mapskey ); ?><?php if( $use_places ) echo '&libraries=places'; ?>&callback=initMap"></script>

<?php

}

break;

/* EDIT COUNTRY */

case 'edit_country':

if( !ab_to( array( 'locations' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'loc_cou_edit_title', "Edit Country" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $page_exists = \query\locations::country_exists() ) ) {

$info = \query\locations::country_info();

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( ab_to( array( 'locations' => 'delete' ) ) ) echo '<li><a href="?route=locations.php&amp;action=countries&amp;type=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>';
if( $info->visible ) {
    echo '<li><a href="?route=locations.php&amp;action=countries&amp;type=unpublish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'unpublish', "Unpublish" ) . '</a></li>';
} else {
    echo '<li><a href="?route=locations.php&amp;action=countries&amp;type=publish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'publish', "Publish" ) . '</a></li>';
}
echo '</ul>
</div>';

}

echo '<a href="?route=locations.php&amp;action=countries" class="btn">' . t( 'locations_country_view', "View Countries" ) . '</a>
</div>';

$subtitle = t( 'loc_cou_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $page_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_locations_page', 'after_title_edit_country_locations_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'locations_csrf' ) ) {

    if( isset( $_POST['name'] ) && isset( $_POST['mapmarker'] ) )
    if( admin\actions::edit_country( $_GET['id'],
    array(
    'name' => $_POST['name'],
    'marker' => $_POST['mapmarker'],
    'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 )
    ) ) ) {

    $info = \query\locations::country_info( $_GET['id'] );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$_SESSION['locations_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . t( 'loc_form_country', "Country" ) . ':</span><div><input type="text" name="name" value="' . $info->name . '" required /></div></div>';
if( ( $mapskey = \query\main::get_option( 'google_maps_key' ) ) && !empty( $mapskey ) ) {
    echo '<div class="row"><span>' . t( 'form_map', "Map" ) . ':</span><div id="map" style="height: 400px;"></div></div>';
}
echo '<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish"' . ( $info->visible ? ' checked' : '' ) . ' /> <label for="publish"><span></span> ' . t( 'msg_publoc', "Publish this location" ) . '</label></div></div>

<input type="hidden" name="mapmarker" value="' . $info->lat . ',' . $info->lng . '" data-lat="' . $info->lat . '" data-lng="' . $info->lng . '" />
<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'loc_cou_edit_button', "Edit Country" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

echo '<div class="title" style="margin-top: 40px;">

<h2>' . t( 'loc_cou_info_title', "Information About This Country" ) . '</h2>

</div>';

$lastupdate_by = \query\main::user_info( $info->lastupdate_by );
$added_by = \query\main::user_info( $info->user );

echo '<div class="info-table" style="padding-bottom: 20px;">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . t( 'last_update_by', "Last update by" ) . ':</span> <div>' . ( empty( $lastupdate_by->name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $lastupdate_by->ID . '">' . $lastupdate_by->name . '</a>' : $lastupdate_by->name ) ) . '</div></div>
<div class="row"><span>' . t( 'last_update_on', "Last update on" ) . ':</span> <div>' . $info->last_update . '</div></div>
<div class="row"><span>' . t( 'added_by', "Added by" ) . ':</span> <div>' . ( empty( $added_by->name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $added_by->ID . '">' . $added_by->name . '</a>' : $added_by->name ) ) . '</div></div>
<div class="row"><span>' . t( 'added_on', "Added on" ) . ':</span> <div>' . $info->date . '</div></div>

</div>';

if( !empty( $mapskey ) ) {

$use_places = (boolean) \query\main::get_option( 'google_maps_places' );

if( $use_places ) echo '<input id="pac-input" />'; ?>

<script>

var marker, map, deflat, deflng;

deflat = parseFloat( $('[name="mapmarker"]').attr('data-lat') );
deflng = parseFloat( $('[name="mapmarker"]').attr('data-lng') );

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 6,
        center: {lat: deflat, lng: deflng}
    });

    marker = new google.maps.Marker({
        map: map,
        draggable: true,
        position: {lat: deflat, lng: deflng}
    });

    marker.addListener('dragend', toggleBounce);

    <?php if( $use_places ) { ?>

    // Create the search box and link it to the UI element.
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    searchBox.addListener('places_changed', function(){
        var places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }

            marker.setPosition(place.geometry.location);
            map.setCenter(place.geometry.location);

            setMarker(place.geometry.location);
        });
    });

    <?php } ?>
}

function toggleBounce() {
    setMarker(this.getPosition());
}

function setMarker( position ) {
    $('input[name="mapmarker"]').val(position);
}

</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_html( $mapskey ); ?><?php if( $use_places ) echo '&libraries=places'; ?>&callback=initMap"></script>

<?php } ?>

<?php

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/* LIST OF COUNTRIES */

case 'countries':

if( !ab_to( array( 'locations' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'loc_cou_title', "Countries" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';
if( ab_to( array( 'locations' => 'add' ) ) ) echo '<a href="?route=locations.php&amp;action=country_add" class="btn">' . t( 'locations_country_add', "Add Country" ) . '</a>';
echo '</div>';

$subtitle = t( 'loc_cou_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_locations_page', 'after_title_list_countries_locations_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'locations_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_country( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

    if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
    if( admin\actions::action_country( $_POST['action'], array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'locations_csrf' ) ) {

if( $_GET['type'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_country( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::action_country( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['locations_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="locations.php" />
<input type="hidden" name="action" value="countries" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'update' => t( 'order_last_update', "Last update" ), 'update desc' => t( 'order_last_update_desc', "Last update DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'name' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="locations.php" />
<input type="hidden" name="action" value="countries" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'loc_cou_search_input', "Search countries" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$p = \query\locations::have_countries( $options = array( 'per_page' => 10, 'show' => 'all', 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( !empty( $_GET['search'] ) ) echo ' / <a href="?route=locations.php&amp;action=countries">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=locations.php&amp;action=countries" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

$ab_edt    = ab_to( array( 'locations' => 'edit' ) );
$ab_del    = ab_to( array( 'locations' => 'delete' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="bulk_options">';

    if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button> ';

    if( $ab_edt ) {
        echo t( 'action', "Action" ) . ':
        <select name="action">';
        foreach( array( 'publish' => t( 'publish', "Publish" ), 'unpublish' => t( 'unpublish', "Unpublish" ) ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
        echo '</select>
        <button class="btn" name="set_action">' . t( 'set_all', "Set to All" ) . '</button>';
    }

echo '</div>';

}

foreach( \query\locations::while_countries( array_merge( array( 'page' => $p['page'], 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

    $links = array();

    $links['view_states'] = '<a href="?route=locations.php&amp;action=states&amp;country=' . $item->ID . '">' . t( 'locations_state_view', "View States" ) . '</a>';
    if( $ab_edt ) {
        $links['edit'] = '<a href="?route=locations.php&amp;action=edit_country&amp;id=' . $item->ID . '">' . t( 'edit', "Edit" ) . '</a>';
        $links['publish'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( !$item->visible ? 'publish' : 'unpublish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( !$item->visible ? t( 'publish', "Publish" ) : t( 'unpublish', "Unpublish" ) ) . '</a>';
    }
    if( $ab_del ) $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'country', $item, $links );

}

echo '</ul>

<input type="hidden" name="csrf" value="' . $csrf . '" />

</form>';

if( isset( $p['prev_page'] ) || isset( $p['next_page'] ) ) {
    echo '<div class="pagination">';

    if( isset( $p['prev_page'] ) ) echo '<a href="' . $p['prev_page'] . '" class="btn">' . t( 'prev_page', "&larr; Prev" ) . '</a>';
    if( isset( $p['next_page'] ) ) echo '<a href="' . $p['next_page'] . '" class="btn">' . t( 'next_page', "Next &rarr;" ) . '</a>';

    if( $p['pages'] > 1 ) {
    echo '<div class="pag_goto">' . sprintf( t( 'pageofpages', "Page <b>%s</b> of <b>%s</b>" ), $page = $p['page'], $pages = $p['pages'] ) . '
    <form action="#" method="GET">';
    foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . esc_html( $gk ) . '" value="' . esc_html( $gv ) . '" />';
    echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
    <button class="btn">' . t( 'go', "Go" ) . '</button>
    </form>
    </div>';
    }

    echo '</div>';
}

} else {

    echo '<div class="a-alert">' . t( 'no_countries_yet', "No countries yet." ) . '</div>';

}

break;

/* ADD STATE */

case 'state_add':

if( !ab_to( array( 'locations' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'loc_state_add_title', "Add State" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=locations.php&amp;action=states" class="btn">' . t( 'locations_state_view', "View States" ) . '</a>
</div>';

$subtitle = t( 'loc_state_add_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_locations_page', 'after_title_add_state_locations_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'locations_csrf' ) ) {

    if( isset( $_POST['name'] ) && isset( $_POST['country'] ) && isset( $_POST['mapmarker'] ) )
    if( admin\actions::add_state( array(
    'name' => $_POST['name'],
    'country' => $_POST['country'],
    'marker' => $_POST['mapmarker'],
    'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 )
    ) ) )

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf = $_SESSION['locations_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>' . t( 'loc_form_state', "State" ) . ':</span><div><input type="text" name="name" value="" required /></div></div>
<div class="row"><span>' . t( 'loc_form_country', "Country" ) . ':</span><div><select name="country">';
foreach( \query\locations::while_countries( array( 'max' => 0, 'show' => 'all' ) ) as $k => $v ) {
    echo '<option value="' . $v->ID . '" data-lat="' . $v->lat . '" data-lng="' . $v->lng . '"' . ( $k == 0 ? ' selected' : '' ) . '>' . $v->name . '</option>';
}
echo '</select></div></div>';
if( ( $mapskey = \query\main::get_option( 'google_maps_key' ) ) && !empty( $mapskey ) ) {
    echo '<div class="row"><span>' . t( 'form_map', "Map" ) . ':</span><div id="map" style="height: 400px;"></div></div>';
}
echo '<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish" checked /> <label for="publish"><span></span> ' . t( 'msg_publoc', "Publish this location" ) . '</label></div></div>

<input type="hidden" name="mapmarker" value="39.82,-101.47" />
<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'loc_state_add_button', "Add State" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

if( !empty( $mapskey ) ) {

$use_places = (boolean) \query\main::get_option( 'google_maps_places' );

if( $use_places ) echo '<input id="pac-input" />'; ?>

<script>

var marker, map, newlatlng, deflat, deflng;

deflat = 39.82;
deflng = -101.47;

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 7,
        center: {lat: deflat, lng: deflng}
    });

    marker = new google.maps.Marker({
        map: map,
        draggable: true,
        position: {lat: deflat, lng: deflng}
    });

    marker.addListener('dragend', toggleBounce);

    <?php if( $use_places ) { ?>

    // Create the search box and link it to the UI element.
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    searchBox.addListener('places_changed', function(){
        var places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }

            marker.setPosition(place.geometry.location);
            map.setCenter(place.geometry.location);

            setMarker(place.geometry.location);
        });
    });

    <?php } ?>
}

function toggleBounce() {
    setMarker(this.getPosition());
}

function setMarker( position ) {
    $('input[name="mapmarker"]').val(position);
}

function updateMarker(){
    var lat = $('select[name="country"]').find('option:selected').attr('data-lat');
    var lng = $('select[name="country"]').find('option:selected').attr('data-lng');
    newlatlng = new google.maps.LatLng(lat,lng);
    marker.setPosition(newlatlng);
    map.setCenter(newlatlng);
}

$( document ).ready(function() {

    $(document).on('change', 'select[name="country"]', function(){
        updateMarker();
    });

});

</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_html( $mapskey ); ?><?php if( $use_places ) echo '&libraries=places'; ?>&callback=initMap"></script>

<?php }

break;

/* EDIT STATE */

case 'edit_state':

if( !ab_to( array( 'locations' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'loc_state_edit_title', "Edit State" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $page_exists = \query\locations::state_exists() ) ) {

$info = \query\locations::state_info();

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( ab_to( array( 'locations' => 'delete' ) ) ) echo '<li><a href="?route=locations.php&amp;action=states&amp;type=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>';
if( $info->visible ) {
    echo '<li><a href="?route=locations.php&amp;action=states&amp;type=unpublish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'unpublish', "Unpublish" ) . '</a></li>';
} else {
    echo '<li><a href="?route=locations.php&amp;action=states&amp;type=publish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'publish', "Publish" ) . '</a></li>';
}
echo '</ul>
</div>';

}

echo '<a href="?route=locations.php&amp;action=states" class="btn">' . t( 'locations_state_view', "View States" ) . '</a>
</div>';

$subtitle = t( 'loc_state_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $page_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_locations_page', 'after_title_edit_state_locations_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'locations_csrf' ) ) {

    if( isset( $_POST['name'] ) && isset( $_POST['country'] ) && isset( $_POST['mapmarker'] ) )
    if( admin\actions::edit_state( $_GET['id'],
    array(
    'name' => $_POST['name'],
    'country' => $_POST['country'],
    'marker' => $_POST['mapmarker'],
    'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 )
    ) ) ) {

    $info = \query\locations::state_info( $_GET['id'] );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$_SESSION['locations_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . t( 'loc_form_state', "State" ) . ':</span><div><input type="text" name="name" value="' . $info->name . '" required /></div></div>
<div class="row"><span>' . t( 'loc_form_country', "Country" ) . ':</span><div><select name="country">';
foreach( \query\locations::while_countries( array( 'max' => 0, 'show' => 'all' ) ) as $v ) {
    echo '<option value="' . $v->ID . '" data-lat="' . $v->lat . '" data-lng="' . $v->lng . '"' . ( $info->country == $v->ID ? ' selected' : '' ) . '>' . $v->name . '</option>';
}
echo '</select></div></div>';
if( ( $mapskey = \query\main::get_option( 'google_maps_key' ) ) && !empty( $mapskey ) ) {
    echo '<div class="row"><span>' . t( 'form_map', "Map" ) . ':</span><div id="map" style="height: 400px;"></div></div>';
}
echo '<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish"' . ( $info->visible ? ' checked' : '' ) . ' /> <label for="publish"><span></span> ' . t( 'msg_publoc', "Publish this location" ) . '</label></div></div>

<input type="hidden" name="mapmarker" value="' . $info->lat . ',' . $info->lng . '" data-lat="' . $info->lat . '" data-lng="' . $info->lng . '" />
<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'loc_state_edit_button', "Edit State" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

echo '<div class="title" style="margin-top: 40px;">

<h2>' . t( 'loc_state_info_title', "Information About This State" ) . '</h2>

</div>';

$lastupdate_by = \query\main::user_info( $info->lastupdate_by );
$added_by = \query\main::user_info( $info->user );

echo '<div class="info-table" style="padding-bottom: 20px;">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . t( 'last_update_by', "Last update by" ) . ':</span> <div>' . ( empty( $lastupdate_by->name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $lastupdate_by->ID . '">' . $lastupdate_by->name . '</a>' : $lastupdate_by->name ) ) . '</div></div>
<div class="row"><span>' . t( 'last_update_on', "Last update on" ) . ':</span> <div>' . $info->last_update . '</div></div>
<div class="row"><span>' . t( 'added_by', "Added by" ) . ':</span> <div>' . ( empty( $added_by->name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $added_by->ID . '">' . $added_by->name . '</a>' : $added_by->name ) ) . '</div></div>
<div class="row"><span>' . t( 'added_on', "Added on" ) . ':</span> <div>' . $info->date . '</div></div>

</div>';

if( !empty( $mapskey ) ) {

$use_places = (boolean) \query\main::get_option( 'google_maps_places' );

if( $use_places ) echo '<input id="pac-input" />'; ?>

<script>

var marker, map, newlatlng, deflat, deflng;

deflat = parseFloat( $('[name="mapmarker"]').attr('data-lat') );
deflng = parseFloat( $('[name="mapmarker"]').attr('data-lng') );

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 6,
        center: {lat: deflat, lng: deflng}
    });

    marker = new google.maps.Marker({
        map: map,
        draggable: true,
        position: {lat: deflat, lng: deflng}
    });

    marker.addListener('dragend', toggleBounce);

    <?php if( $use_places ) { ?>

    // Create the search box and link it to the UI element.
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    searchBox.addListener('places_changed', function(){
        var places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }

            marker.setPosition(place.geometry.location);
            map.setCenter(place.geometry.location);

            setMarker(place.geometry.location);
        });
    });

    <?php } ?>
}

function toggleBounce() {
    setMarker(this.getPosition());
}

function setMarker( position ) {
    $('input[name="mapmarker"]').val(position);
}

function updateMarker(){
    var lat = $('select[name="country"]').find('option:selected').attr('data-lat');
    var lng = $('select[name="country"]').find('option:selected').attr('data-lng');
    newlatlng = new google.maps.LatLng(lat,lng);
    marker.setPosition(newlatlng);
    map.setCenter(newlatlng);
}

$( document ).ready(function() {

    $(document).on('change', 'select[name="country"]', function(){
        updateMarker();
    });

});

</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_html( $mapskey ); ?><?php if( $use_places ) echo '&libraries=places'; ?>&callback=initMap"></script>

<?php }

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/* LIST OF STATES */

case 'states':

if( !ab_to( array( 'locations' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'loc_state_title', "States" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';
if( ab_to( array( 'locations' => 'add' ) ) ) echo '<a href="?route=locations.php&amp;action=state_add" class="btn">' . t( 'locations_state_add', "Add State" ) . '</a>';
echo '</div>';

$subtitle = t( 'loc_state_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_locations_page', 'after_title_list_states_locations_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'locations_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_state( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

    if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
    if( admin\actions::action_state( $_POST['action'], array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'locations_csrf' ) ) {

if( $_GET['type'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_state( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::action_state( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['locations_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="locations.php" />
<input type="hidden" name="action" value="states" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'update' => t( 'order_last_update', "Last update" ), 'update desc' => t( 'order_last_update_desc', "Last update DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'name' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="locations.php" />
<input type="hidden" name="action" value="states" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'loc_state_search_input', "Search states" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$p = \query\locations::have_states( $options = array( 'per_page' => 10, 'country' => (isset( $_GET['country'] ) ? $_GET['country'] : ''), 'show' => 'all', 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( !empty( $_GET['search'] ) ) echo ' / <a href="?route=locations.php&amp;action=states">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=locations.php&amp;action=states" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

$ab_edt    = ab_to( array( 'locations' => 'edit' ) );
$ab_del    = ab_to( array( 'locations' => 'delete' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="bulk_options">';

    if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button> ';

    if( $ab_edt ) {
        echo t( 'action', "Action" ) . ':
        <select name="action">';
        foreach( array( 'publish' => t( 'publish', "Publish" ), 'unpublish' => t( 'unpublish', "Unpublish" ) ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
        echo '</select>
        <button class="btn" name="set_action">' . t( 'set_all', "Set to All" ) . '</button>';
    }

echo '</div>';

}

foreach( \query\locations::while_states( array_merge( array( 'page' => $p['page'], 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

    $links = array();

    $links['view_cities'] = '<a href="?route=locations.php&amp;action=cities&amp;state=' . $item->ID . '">' . t( 'locations_city_view', "View Cities" ) . '</a>';
    if( $ab_edt ) {
        $links['edit'] = '<a href="?route=locations.php&amp;action=edit_state&amp;id=' . $item->ID . '">' . t( 'edit', "Edit" ) . '</a>';
        $links['publish'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( !$item->visible ? 'publish' : 'unpublish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( !$item->visible ? t( 'publish', "Publish" ) : t( 'unpublish', "Unpublish" ) ) . '</a>';
    }
    if( $ab_del ) $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'state', $item, $links );

}

echo '</ul>

<input type="hidden" name="csrf" value="' . $csrf . '" />

</form>';

if( isset( $p['prev_page'] ) || isset( $p['next_page'] ) ) {
    echo '<div class="pagination">';

    if( isset( $p['prev_page'] ) ) echo '<a href="' . $p['prev_page'] . '" class="btn">' . t( 'prev_page', "&larr; Prev" ) . '</a>';
    if( isset( $p['next_page'] ) ) echo '<a href="' . $p['next_page'] . '" class="btn">' . t( 'next_page', "Next &rarr;" ) . '</a>';

    if( $p['pages'] > 1 ) {
    echo '<div class="pag_goto">' . sprintf( t( 'pageofpages', "Page <b>%s</b> of <b>%s</b>" ), $page = $p['page'], $pages = $p['pages'] ) . '
    <form action="#" method="GET">';
    foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . esc_html( $gk ) . '" value="' . esc_html( $gv ) . '" />';
    echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
    <button class="btn">' . t( 'go', "Go" ) . '</button>
    </form>
    </div>';
    }

    echo '</div>';
}

} else echo '<div class="a-alert">' . t( 'no_states_yet', "No states yet." ) . '</div>';

break;

/* ADD CITY */

case 'city_add':

if( !ab_to( array( 'locations' => 'add' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'loc_city_add_title', "Add City" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">
<a href="?route=locations.php&amp;action=cities" class="btn">' . t( 'locations_city_view', "View Cities" ) . '</a>
</div>';

$subtitle = t( 'loc_city_add_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_locations_page', 'after_title_add_city_locations_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'locations_csrf' ) ) {

    if( isset( $_POST['name'] ) && isset( $_POST['country'] ) && isset( $_POST['state'] ) && isset( $_POST['mapmarker'] ) )
    if( admin\actions::add_city(
    array(
    'name' => $_POST['name'],
    'country' => $_POST['country'],
    'state' => $_POST['state'],
    'marker' => $_POST['mapmarker'],
    'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 )
    ) ) )

    echo '<div class="a-success">' . t( 'msg_added', "Added!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$csrf = $_SESSION['locations_csrf'] = \site\utils::str_random(10);

echo '<div class="form-table">

<form action="#" method="POST" autocomplete="off">

<div class="row"><span>' . t( 'loc_form_city', "City" ) . ':</span><div><input type="text" name="name" value="" required /></div></div>
<div class="row"><span>' . t( 'loc_form_country', "Country" ) . ':</span><div><select name="country">';
$country = 0;
foreach( \query\locations::while_countries( array( 'max' => 0, 'show' => 'all' ) ) as $k => $v ) {
    if( $k === 0 ) {
        $country = $v->ID;
    }
    echo '<option value="' . $v->ID . '">' . $v->name . '</option>';
}
echo '</select></div></div>
<div class="row"><span>' . t( 'loc_form_state', "State" ) . ':</span><div><select name="state">';
foreach( \query\locations::while_states( array( 'max' => 0, 'country' => $country, 'show' => 'all' ) ) as $k => $v ) {
    echo '<option value="' . $v->ID . '" data-lat="' . $v->lat . '" data-lng="' . $v->lng . '"' . ( $k == 0 ? ' selected' : '' ) . '>' . $v->name . '</option>';
}
echo '</select></div></div>';
if( ( $mapskey = \query\main::get_option( 'google_maps_key' ) ) && !empty( $mapskey ) ) {
    echo '<div class="row"><span>' . t( 'form_map', "Map" ) . ':</span><div id="map" style="height: 400px;"></div></div>';
}
echo '<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish" checked /> <label for="publish"><span></span> ' . t( 'msg_publoc', "Publish this location" ) . '</label></div></div>

<input type="hidden" name="mapmarker" value="39.82,-101.47" />
<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'loc_city_add_button', "Add City" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

if( !empty( $mapskey ) ) {

$use_places = (boolean) \query\main::get_option( 'google_maps_places' );

if( $use_places ) echo '<input id="pac-input" />'; ?>

<script>

var marker, map, newlatlng, deflat, deflng;

deflat = 39.82;
deflng = -101.47;

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 8,
        center: {lat: deflat, lng: deflng}
    });

    marker = new google.maps.Marker({
        map: map,
        draggable: true,
        position: {lat: deflat, lng: deflng}
    });

    marker.addListener('dragend', toggleBounce);

    <?php if( $use_places ) { ?>

    // Create the search box and link it to the UI element.
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    searchBox.addListener('places_changed', function(){
        var places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }

            marker.setPosition(place.geometry.location);
            map.setCenter(place.geometry.location);


            setMarker(place.geometry.location);
        });
    });

    <?php } ?>
}

function toggleBounce() {
    setMarker(this.getPosition());
}

function setMarker( position ) {
    $('input[name="mapmarker"]').val(position);
}

function updateMarker(){
    var lat = $('select[name="state"]').find('option:selected').attr('data-lat');
    var lng = $('select[name="state"]').find('option:selected').attr('data-lng');
    newlatlng = new google.maps.LatLng(lat,lng);
    marker.setPosition(newlatlng);
    map.setCenter(newlatlng);
}

</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_html( $mapskey ); ?><?php if( $use_places ) echo '&libraries=places'; ?>&callback=initMap"></script>

<?php } ?>

<script>

$( document ).ready(function() {

$( 'select[name="country"]' ).search_store_location( {}, "country", function(){
    if( typeof updateMarker === 'function' ) {
        updateMarker();
    }
} );

$(document).on('change', 'select[name="state"]', function(){
    if( typeof updateMarker === 'function' ) {
        updateMarker();
    }
});

});

</script>

<script src="js/search_location.js"></script>

<?php

break;

/* EDIT CITY */

case 'edit_city':

if( !ab_to( array( 'locations' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'loc_city_edit_title', "Edit City" ) . '</h2>

<div style="float:right; margin: 0 2px 0 0;">';

if( isset( $_GET['id'] ) && ( $page_exists = \query\locations::city_exists() ) ) {

$info = \query\locations::city_info();

echo '<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
if( ab_to( array( 'locations' => 'delete' ) ) ) echo '<li><a href="?route=locations.php&amp;action=cities&amp;type=delete&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>';
if( $info->visible ) {
    echo '<li><a href="?route=locations.php&amp;action=cities&amp;type=unpublish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'unpublish', "Unpublish" ) . '</a></li>';
} else {
    echo '<li><a href="?route=locations.php&amp;action=cities&amp;type=publish&amp;id=' . $_GET['id'] . '&amp;token=' . $csrf . '">' . t( 'publish', "Publish" ) . '</a></li>';
}
echo '</ul>
</div>';

}

echo '<a href="?route=locations.php&amp;action=cities" class="btn">' . t( 'locations_city_view', "View Cities" ) . '</a>
</div>';

$subtitle = t( 'loc_city_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $page_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_locations_page', 'after_title_edit_city_locations_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'locations_csrf' ) ) {

    if( isset( $_POST['name'] ) && isset( $_POST['country'] ) && isset( $_POST['state'] ) && isset( $_POST['mapmarker'] ) )
    if( admin\actions::edit_city( $_GET['id'],
    array(
    'name' => $_POST['name'],
    'country' => $_POST['country'],
    'state' => $_POST['state'],
    'marker' => $_POST['mapmarker'],
    'publish' => ( isset( $_POST['publish'] ) ? 1 : 0 )
    ) ) ) {

    $info = \query\locations::city_info( $_GET['id'] );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$_SESSION['locations_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . t( 'loc_form_city', "City" ) . ':</span><div><input type="text" name="name" value="' . $info->name . '" required /></div></div>
<div class="row"><span>' . t( 'loc_form_country', "Country" ) . ':</span><div><select name="country">';
foreach( \query\locations::while_countries( array( 'max' => 0, 'show' => 'all' ) ) as $v ) {
    echo '<option value="' . $v->ID . '"' . ( $info->country == $v->ID ? ' selected' : '' ) . '>' . $v->name . '</option>';
}
echo '</select></div></div>
<div class="row"><span>' . t( 'loc_form_state', "State" ) . ':</span><div><select name="state">';
foreach( \query\locations::while_states( array( 'max' => 0, 'country' => $info->country, 'show' => 'all' ) ) as $v ) {
    echo '<option value="' . $v->ID . '" data-lat="' . $v->lat . '" data-lng="' . $v->lng . '"' . ( $info->state == $v->ID ? ' selected' : '' ) . '>' . $v->name . '</option>';
}
echo '</select></div></div>';
if( ( $mapskey = \query\main::get_option( 'google_maps_key' ) ) && !empty( $mapskey ) ) {
    echo '<div class="row"><span>' . t( 'form_map', "Map" ) . ':</span><div id="map" style="height: 400px;"></div></div>';
}
echo '<div class="row"><span>' . t( 'form_publish', "Publish" ) . ':</span><div><input type="checkbox" name="publish" id="publish"' . ( $info->visible ? ' checked' : '' ) . ' /> <label for="publish"><span></span> ' . t( 'msg_publoc', "Publish this location" ) . '</label></div></div>

<input type="hidden" name="mapmarker" value="' . $info->lat . ',' . $info->lng . '" data-lat="' . $info->lat . '" data-lng="' . $info->lng . '" />
<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'loc_city_edit_button', "Edit City" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

echo '<div class="title" style="margin-top: 40px;">

<h2>' . t( 'loc_city_info_title', "Information About This City" ) . '</h2>

</div>';

$lastupdate_by = \query\main::user_info( $info->lastupdate_by );
$added_by = \query\main::user_info( $info->user );

echo '<div class="info-table" style="padding-bottom: 20px;">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . t( 'last_update_by', "Last update by" ) . ':</span> <div>' . ( empty( $lastupdate_by->name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $lastupdate_by->ID . '">' . $lastupdate_by->name . '</a>' : $lastupdate_by->name ) ) . '</div></div>
<div class="row"><span>' . t( 'last_update_on', "Last update on" ) . ':</span> <div>' . $info->last_update . '</div></div>
<div class="row"><span>' . t( 'added_by', "Added by" ) . ':</span> <div>' . ( empty( $added_by->name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $added_by->ID . '">' . $added_by->name . '</a>' : $added_by->name ) ) . '</div></div>
<div class="row"><span>' . t( 'added_on', "Added on" ) . ':</span> <div>' . $info->date . '</div></div>

</div>';

if( !empty( $mapskey ) ) {

$use_places = (boolean) \query\main::get_option( 'google_maps_places' );

if( $use_places ) echo '<input id="pac-input" />'; ?>

<script>

var marker, map, newlatlng, deflat, deflng;

deflat = parseFloat( $('[name="mapmarker"]').attr('data-lat') );
deflng = parseFloat( $('[name="mapmarker"]').attr('data-lng') );

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 8,
        center: {lat: deflat, lng: deflng}
    });

    marker = new google.maps.Marker({
        map: map,
        draggable: true,
        position: {lat: deflat, lng: deflng}
    });

    marker.addListener('dragend', toggleBounce);

    <?php if( $use_places ) { ?>

    // Create the search box and link it to the UI element.
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    searchBox.addListener('places_changed', function(){
        var places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }

            marker.setPosition(place.geometry.location);
            map.setCenter(place.geometry.location);

            setMarker(place.geometry.location);
        });
    });

    <?php } ?>
}

function toggleBounce() {
    setMarker(this.getPosition());
}

function setMarker( position ) {
    $('input[name="mapmarker"]').val(position);
}

function updateMarker(){
    var lat = $('select[name="state"]').find('option:selected').attr('data-lat');
    var lng = $('select[name="state"]').find('option:selected').attr('data-lng');
    newlatlng = new google.maps.LatLng(lat,lng);
    marker.setPosition(newlatlng);
    map.setCenter(newlatlng);
}

</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_html( $mapskey ); ?><?php if( $use_places ) echo '&libraries=places'; ?>&callback=initMap"></script>

<?php } ?>

<script>

$( document ).ready(function() {

$( 'select[name="country"]' ).search_store_location( {}, "country", function(){
    if( typeof updateMarker === 'function' ) {
        updateMarker();
    }
} );

$(document).on('change', 'select[name="state"]', function(){
    if( typeof updateMarker === 'function' ) {
        updateMarker();
    }
});

});

</script>

<script src="js/search_location.js"></script>

<?php

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/* LIST OF CITIES */

case 'cities':

if( !ab_to( array( 'locations' => 'view' ) ) ) die;

echo '<div class="title">

<h2>' . t( 'loc_city_title', "Cities" ) . '</h2>

<div style="float:right;margin:0 2px 0 0;">';
if( ab_to( array( 'locations' => 'add' ) ) ) echo '<a href="?route=locations.php&amp;action=city_add" class="btn">' . t( 'locations_city_add', "Add City" ) . '</a>';
echo '</div>';

$subtitle = t( 'loc_city_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_locations_page', 'after_title_list_cities_locations_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'locations_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

    if( isset( $_POST['id'] ) )
    if( admin\actions::delete_city( array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( isset( $_POST['set_action'] ) ) {

    if( isset( $_POST['id'] ) && isset( $_POST['action'] ) )
    if( admin\actions::action_city( $_POST['action'], array_keys( $_POST['id'] ) ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['type'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'locations_csrf' ) ) {

if( $_GET['type'] == 'delete' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::delete_city( $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

} else if( $_GET['type'] == 'publish' || $_GET['type'] == 'unpublish' ) {

    if( isset( $_GET['id'] ) )
    if( admin\actions::action_city( $_GET['type'], $_GET['id'] ) )
    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['locations_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="locations.php" />
<input type="hidden" name="action" value="cities" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ), 'update' => t( 'order_last_update', "Last update" ), 'update desc' => t( 'order_last_update_desc', "Last update DESC" ), 'name' => t( 'order_name', "Name" ), 'name desc' => t( 'order_name_desc', "Name DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'name' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="locations.php" />
<input type="hidden" name="action" value="cities" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'loc_city_search_input', "Search cities" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$p = \query\locations::have_cities( $options = array( 'per_page' => 10,    'state' => (isset( $_GET['state'] ) ? $_GET['state'] : ''), 'show' => 'all', 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( !empty( $_GET['search'] ) ) echo ' / <a href="?route=locations.php&amp;action=cities">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=locations.php&amp;action=cities" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>';

$ab_edt    = ab_to( array( 'locations' => 'edit' ) );
$ab_del    = ab_to( array( 'locations' => 'delete' ) );

if( $ab_edt || $ab_del ) {

echo '<div class="bulk_options">';

    if( $ab_del ) echo '<button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button> ';

    if( $ab_edt ) {
        echo t( 'action', "Action" ) . ':
        <select name="action">';
        foreach( array( 'publish' => t( 'publish', "Publish" ), 'unpublish' => t( 'unpublish', "Unpublish" ) ) as $k => $v )echo '<option value="' . $k . '">' . $v . '</option>';
        echo '</select>
        <button class="btn" name="set_action">' . t( 'set_all', "Set to All" ) . '</button>';
    }

echo '</div>';

}

foreach( \query\locations::while_cities( array_merge( array( 'page' => $p['page'], 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

    $links = array();

    if( $ab_edt ) {
        $links['edit'] = '<a href="?route=locations.php&amp;action=edit_city&amp;id=' . $item->ID . '">' . t( 'edit', "Edit" ) . '</a>';
        $links['publish'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => ( !$item->visible ? 'publish' : 'unpublish' ), 'id' => $item->ID, 'token' => $csrf ) ) . '">' . ( !$item->visible ? t( 'publish', "Publish" ) : t( 'unpublish', "Unpublish" ) ) . '</a>';
    }
    if( $ab_del ) $links['delete'] = '<a href="' . \site\utils::update_uri( '', array( 'type' => 'delete', 'id' => $item->ID, 'token' => $csrf ) ) . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

    echo get_list_type( 'city', $item, $links );

}

echo '</ul>

<input type="hidden" name="csrf" value="' . $csrf . '" />

</form>';

if( isset( $p['prev_page'] ) || isset( $p['next_page'] ) ) {
    echo '<div class="pagination">';

    if( isset( $p['prev_page'] ) ) echo '<a href="' . $p['prev_page'] . '" class="btn">' . t( 'prev_page', "&larr; Prev" ) . '</a>';
    if( isset( $p['next_page'] ) ) echo '<a href="' . $p['next_page'] . '" class="btn">' . t( 'next_page', "Next &rarr;" ) . '</a>';

    if( $p['pages'] > 1 ) {
    echo '<div class="pag_goto">' . sprintf( t( 'pageofpages', "Page <b>%s</b> of <b>%s</b>" ), $page = $p['page'], $pages = $p['pages'] ) . '
    <form action="#" method="GET">';
    foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . esc_html( $gk ) . '" value="' . esc_html( $gv ) . '" />';
    echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
    <button class="btn">' . t( 'go', "Go" ) . '</button>
    </form>
    </div>';
    }

    echo '</div>';
}

} else echo '<div class="a-alert">' . t( 'no_cities_yet', "No cities yet." ) . '</div>';

break;

/* ADD STORE LOCATION */

case 'add_store_location':

if( !ab_to( array( 'stores' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'store_loc_add_title', "Add Store Address" ) . '</h2>';

if( isset( $_GET['id'] ) && ( $store_exists = \query\main::store_exists( $_GET['id'] ) ) && ( $info = \query\main::store_info( $_GET['id'] ) ) && $info->is_physical ) {

echo '<div style="float:right; margin: 0 2px 0 0;">

<a href="?route=stores.php&amp;action=edit&amp;id=' . $_GET['id'] . '" class="btn">' . t( 'back', "Back" ) . '</a>

</div>';

}

$subtitle = t( 'store_loc_add_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( isset( $store_exists ) && $store_exists && $info->is_physical ) {

do_action( array( 'after_title_inner_page', 'after_title_locations_page', 'after_title_add_store_location_locations_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'stores_csrf' ) ) {

    if( isset( $_POST['address'] ) && isset( $_POST['zip'] ) && isset( $_POST['country'] ) && isset( $_POST['state'] ) && isset( $_POST['city'] ) && isset( $_POST['mapmarker'] ) )
    if( admin\actions::add_store_location(
    array(
    'Store' => $_GET['id'],
    'Address' => $_POST['address'],
    'Zip' => $_POST['zip'],
    'Country' => $_POST['country'],
    'State' => $_POST['state'],
    'City' => $_POST['city'],
    'Marker' => $_POST['mapmarker']
    ) ) ) {

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$_SESSION['stores_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . t( 'form_address', "Address" ) . ':</span><div><input type="text" name="address" value="" required /></div></div>
<div class="row"><span>' . t( 'loc_form_zip', "ZIP" ) . ':</span><div><input type="text" name="zip" value="" /></div></div>

<div class="row"><span>' . t( 'loc_form_country', "Country" ) . ':</span><div><select name="country">';
$country = $state = 0;
foreach( \query\locations::while_countries( array( 'max' => 0, 'show' => 'all' ) ) as $k => $v ) {
    if( $k === 0 ) {
        $country = $v->ID;
    }
    echo '<option value="' . $v->ID . '">' . $v->name . '</option>';
}
echo '</select></div></div>

<div class="row"><span>' . t( 'loc_form_state', "State" ) . ':</span><div><select name="state">';
foreach( \query\locations::while_states( array( 'max' => 0, 'country' => $country, 'show' => 'all' ) ) as $k => $v ) {
    if( $k === 0 ) {
        $state = $v->ID;
    }
    echo '<option value="' . $v->ID . '">' . $v->name . '</option>';
}
echo '</select></div></div>';

echo '<div class="row"><span>' . t( 'loc_form_city', "City" ) . ':</span><div><select name="city">';
foreach( \query\locations::while_cities( array( 'max' => 0, 'state' => $state, 'show' => 'all' ) ) as $v ) {
    echo '<option value="' . $v->ID . '" data-lat="' . $v->lat . '" data-lng="' . $v->lng . '">' . $v->name . '</option>';
}
echo '</select></div></div>';

if( ( $mapskey = \query\main::get_option( 'google_maps_key' ) ) && !empty( $mapskey ) ) {
    echo '<div class="row"><span>' . t( 'form_map', "Map" ) . ':</span><div id="map" style="height: 400px;"></div></div>';
}

echo '<input type="hidden" name="mapmarker" value="39.82,-101.47" />
<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'store_loc_add_button', "Add Address" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

if( !empty( $mapskey ) ) {

$use_places = (boolean) \query\main::get_option( 'google_maps_places' );

if( $use_places ) echo '<input id="pac-input" />'; ?>

<script>

var marker, map, newlatlng, deflat, deflng;

deflat = 39.82;
deflng = -101.47;

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 14,
        center: {lat: deflat, lng: deflng}
    });

    marker = new google.maps.Marker({
        map: map,
        draggable: true,
        position: {lat: deflat, lng: deflng}
    });

    marker.addListener('dragend', toggleBounce);

    <?php if( $use_places ) { ?>

    // Create the search box and link it to the UI element.
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    searchBox.addListener('places_changed', function(){
        var places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }

            marker.setPosition(place.geometry.location);
            map.setCenter(place.geometry.location);

            setMarker(place.geometry.location);
        });
    });

    <?php } ?>
}

function toggleBounce() {
    setMarker(this.getPosition());
}

function setMarker( position ) {
    $('input[name="mapmarker"]').val(position);
}

function updateMarker(){
    var lat = $('select[name="city"]').find('option:selected').attr('data-lat');
    var lng = $('select[name="city"]').find('option:selected').attr('data-lng');
    newlatlng = new google.maps.LatLng(lat,lng);
    marker.setPosition(newlatlng);
    map.setCenter(newlatlng);
}

</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_html( $mapskey ); ?><?php if( $use_places ) echo '&libraries=places'; ?>&callback=initMap"></script>

<?php } ?>

<script>

$( document ).ready(function() {

    $( 'select[name="country"]' ).search_store_location( {autoLoadCity: true}, "country", function(){
        if( typeof updateMarker === 'function' ) {
            updateMarker();
        }
    } );

    $( 'select[name="state"]' ).search_store_location( {autoLoadCity: true}, "state", function(){
        if( typeof updateMarker === 'function' ) {
            updateMarker();
        }
    } );

    $( 'select[name="city"]' ).search_store_location( {}, "city", function(){
        if( typeof updateMarker === 'function' ) {
            updateMarker();
        }
    } );

});

</script>

<script src="js/search_location.js"></script>

<?php

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

/* EDIT STORE LOCATION */

case 'edit_store_location':

if( !ab_to( array( 'stores' => 'edit' ) ) ) die;

$csrf = \site\utils::str_random(10);

echo '<div class="title">

<h2>' . t( 'store_loc_edit_title', "Edit Store Address" ) . '</h2>';

if( isset( $_GET['id'] ) && ( $store_exists = \query\locations::store_location_exists() ) ) {

$info = \query\locations::store_location_info( $_GET['id'] );

echo '<div style="float:right;margin:0 2px 0 0;">

<div class="options">
<a href="#" class="btn">' . t( 'options', "Options" ) . '</a>
<ul>';
echo '<li><a href="?route=stores.php&amp;action=edit&amp;id=' . $info->storeID . '&amp;type=delete_location&amp;locID=' . $_GET['id'] . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a></li>';
echo '</ul>
</div>

<a href="?route=stores.php&amp;action=edit&amp;id=' . $info->storeID . '" class="btn">' . t( 'back', "Back" ) . '</a>
</div>';

}

$subtitle = t( 'store_loc_edit_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

if( $store_exists ) {

do_action( array( 'after_title_inner_page', 'after_title_locations_page', 'after_title_edit_store_location_locations_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'stores_csrf' ) ) {

    if( isset( $_POST['address'] ) && isset( $_POST['zip'] ) && isset( $_POST['country'] ) && isset( $_POST['state'] ) && isset( $_POST['city'] ) && isset( $_POST['mapmarker'] ) )
    if( admin\actions::edit_store_location( $_GET['id'],
    array(
    'Address' => $_POST['address'],
    'Zip' => $_POST['zip'],
    'Country' => $_POST['country'],
    'State' => $_POST['state'],
    'City' => $_POST['city'],
    'Marker' => $_POST['mapmarker']
    ) ) ) {

    $info = \query\locations::store_location_info( $_GET['id'] );

    echo '<div class="a-success">' . t( 'msg_saved', "Saved!" ) . '</div>';
    } else
    echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

$_SESSION['stores_csrf'] = $csrf;

echo '<div class="form-table">

<form action="#" method="POST">

<div class="row"><span>' . t( 'form_address', "Address" ) . ':</span><div><input type="text" name="address" value="' . $info->address . '" required /></div></div>
<div class="row"><span>' . t( 'loc_form_zip', "ZIP" ) . ':</span><div><input type="text" name="zip" value="' . $info->zip . '" /></div></div>

<div class="row"><span>' . t( 'loc_form_country', "Country" ) . ':</span><div><select name="country">';
foreach( \query\locations::while_countries( array( 'max' => 0, 'show' => 'all' ) ) as $v ) {
    echo '<option value="' . $v->ID . '"' . ( $info->countryID == $v->ID ? ' selected' : '' ) . '>' . $v->name . '</option>';
}
echo '</select></div></div>

<div class="row"><span>' . t( 'loc_form_state', "State" ) . ':</span><div><select name="state">';
foreach( \query\locations::while_states( array( 'max' => 0, 'country' => $info->countryID, 'show' => 'all' ) ) as $v ) {
    echo '<option value="' . $v->ID . '"' . ( $info->stateID == $v->ID ? ' selected' : '' ) . '>' . $v->name . '</option>';
}
echo '</select></div></div>';

echo '<div class="row"><span>' . t( 'loc_form_city', "City" ) . ':</span><div><select name="city">';
foreach( \query\locations::while_cities( array( 'max' => 0, 'state' => $info->stateID, 'show' => 'all' ) ) as $v ) {
    echo '<option value="' . $v->ID . '" data-lat="' . $v->lat . '" data-lng="' . $v->lng . '"' . ( $info->cityID == $v->ID ? ' selected' : '' ) . '>' . $v->name . '</option>';
}
echo '</select></div></div>';

if( ( $mapskey = \query\main::get_option( 'google_maps_key' ) ) && !empty( $mapskey ) ) {
    echo '<div class="row"><span>' . t( 'form_map', "Map" ) . ':</span><div id="map" style="height: 400px;"></div></div>';
}

echo '<input type="hidden" name="mapmarker" value="' . $info->lat . ',' . $info->lng . '" data-lat="' . $info->lat . '" data-lng="' . $info->lng . '" />
<input type="hidden" name="csrf" value="' . $csrf . '" />

<div class="twocols">
    <div>
        <button class="btn btn-important">' . t( 'store_loc_edit_button', "Edit Address" ) . '</button>
    </div>
    <div></div>
</div>

</form>

</div>';

echo '<div class="title" style="margin-top:40px;">

<h2>' . t( 'store_loc_edit_info_title', "Information About This Location" ) . '</h2>

</div>';

$lastupdate_by = \query\main::user_info( $info->lastupdate_by );
$added_by = \query\main::user_info( $info->userID );

echo '<div class="info-table" style="padding-bottom:20px;">

<div class="row"><span>ID:</span> <div>' . $info->ID . '</div></div>
<div class="row"><span>' . t( 'last_update_by', "Last update by" ) . ':</span> <div>' . ( empty( $lastupdate_by->name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $lastupdate_by->ID . '">' . $lastupdate_by->name . '</a>' : $lastupdate_by->name ) ) . '</div></div>
<div class="row"><span>' . t( 'last_update_on', "Last update on" ) . ':</span> <div>' . $info->last_update . '</div></div>
<div class="row"><span>' . t( 'added_by', "Added by" ) . ':</span> <div>' . ( empty( $added_by->name ) ? '-' : ( ab_to( array( 'users' => 'edit' ) ) ? '<a href="?route=users.php&amp;action=edit&amp;id=' . $added_by->ID . '">' . $added_by->name . '</a>' : $added_by->name ) ) . '</div></div>
<div class="row"><span>' . t( 'added_on', "Added on" ) . ':</span> <div>' . $info->date . '</div></div>

</div>';

if( !empty( $mapskey ) ) {

$use_places = (boolean) \query\main::get_option( 'google_maps_places' );

if( $use_places ) echo '<input id="pac-input" />'; ?>

<script>

var marker, map, newlatlng, deflat, deflng;

deflat = parseFloat( $('[name="mapmarker"]').attr('data-lat') );
deflng = parseFloat( $('[name="mapmarker"]').attr('data-lng') );

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 14,
        center: {lat: deflat, lng: deflng}
    });

    marker = new google.maps.Marker({
        map: map,
        draggable: true,
        position: {lat: deflat, lng: deflng}
    });

    marker.addListener('dragend', toggleBounce);

    <?php if( $use_places ) { ?>

    // Create the search box and link it to the UI element.
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    searchBox.addListener('places_changed', function(){
        var places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }

            marker.setPosition(place.geometry.location);
            map.setCenter(place.geometry.location);

            setMarker(place.geometry.location);
        });
    });

    <?php } ?>
}

function toggleBounce() {
    setMarker(this.getPosition());
}

function setMarker( position ) {
    $('input[name="mapmarker"]').val(position);
}

function updateMarker(){
    var lat = $('select[name="city"]').find('option:selected').attr('data-lat');
    var lng = $('select[name="city"]').find('option:selected').attr('data-lng');
    newlatlng = new google.maps.LatLng(lat,lng);
    marker.setPosition(newlatlng);
    map.setCenter(newlatlng);
}

</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_html( $mapskey ); ?><?php if( $use_places ) echo '&libraries=places'; ?>&callback=initMap"></script>

<?php } ?>

<script>

$( document ).ready(function() {

    $( 'select[name="country"]' ).search_store_location( {autoLoadCity: true}, "country", function(){
        if( typeof updateMarker === 'function' ) {
            updateMarker();
        }
    } );

    $( 'select[name="state"]' ).search_store_location( {autoLoadCity: true}, "state", function(){
        if( typeof updateMarker === 'function' ) {
            updateMarker();
        }
    } );

    $( 'select[name="city"]' ).search_store_location( {}, "city", function(){
        if( typeof updateMarker === 'function' ) {
            updateMarker();
        }
    } );

});

</script>

<script src="js/search_location.js"></script>

<?php

} else echo '<div class="a-error">' . t( 'invalid_id', "Invalid ID" ) . '</div>';

break;

}