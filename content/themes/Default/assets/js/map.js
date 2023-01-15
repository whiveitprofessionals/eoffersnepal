var canvas = $( '#map_canvas' );

if( typeof canvas != 'undefined' ) {

function initMap() {
    var map;
    //var bounds = new google.maps.LatLngBounds();
    var mapOptions = {
        zoom: canvas.data( 'zoom' ),
        center: new google.maps.LatLng( canvas.data( 'lat' ), canvas.data( 'lng' ) ),
        mapTypeId: 'roadmap',
        styles: [ { 'elementType': 'geometry', 'stylers': [ { 'color': '#f5f5f5' } ] }, { 'elementType': 'labels.icon', 'stylers': [ { 'visibility': 'off' } ] }, { 'elementType': 'labels.text.fill', 'stylers': [ { 'color': '#616161' } ] }, { 'elementType': 'labels.text.stroke', 'stylers': [ { 'color': '#f5f5f5' } ] }, { 'featureType': 'administrative.land_parcel', 'elementType': 'labels.text.fill', 'stylers': [ { 'color': '#bdbdbd' } ] }, { 'featureType': 'poi', 'elementType': 'geometry', 'stylers': [ { 'color': '#eeeeee' } ] }, { 'featureType': 'poi', 'elementType': 'labels.text.fill', 'stylers': [ { 'color': '#757575' } ] }, { 'featureType': 'poi.park', 'elementType': 'geometry', 'stylers': [ { 'color': '#e5e5e5' } ] }, { 'featureType': 'poi.park', 'elementType': 'labels.text.fill', 'stylers': [ { 'color': '#9e9e9e' } ] }, { 'featureType': 'road', 'elementType': 'geometry', 'stylers': [ { 'color': '#ffffff' } ] }, { 'featureType': 'road.arterial', 'elementType': 'labels.text.fill', 'stylers': [ { 'color': '#757575' } ] }, { 'featureType': 'road.highway', 'elementType': 'geometry', 'stylers': [ { 'color': '#dadada' } ] }, { 'featureType': 'road.highway', 'elementType': 'labels.text.fill', 'stylers': [ { 'color': '#616161' } ] }, { 'featureType': 'road.local', 'elementType': 'labels.text.fill', 'stylers': [ { 'color': '#9e9e9e' } ] }, { 'featureType': 'transit.line', 'elementType': 'geometry', 'stylers': [ { 'color': '#e5e5e5' } ] }, { 'featureType': 'transit.station', 'elementType': 'geometry', 'stylers': [ { 'color': '#eeeeee' } ] }, { 'featureType': 'water', 'elementType': 'geometry', 'stylers': [ { 'color': '#c9c9c9' } ] }, { 'featureType': 'water', 'elementType': 'labels.text.fill', 'stylers': [ { 'color': '#9e9e9e' } ] } ]
    };

    // Display a map on the page
    map = new google.maps.Map( document.getElementById( 'map_canvas' ), mapOptions );
    map.setTilt( 45 );

    // Multiple Markers
    var markers = [];

    $( '.store-locations li' ).each( function() {
        var t = $(this);
        markers.push( [t.data( 'content' ), t.data( 'lat' ), t.data( 'lng' ), '<h4>' + t.data( 'title' ) + '</h4>' + t.data( 'content' )] );
    });

    // Display multiple markers on a map
    var infoWindow = new google.maps.InfoWindow(), marker, i;

    // Loop through our array of markers & place each one on the map
    for( i = 0; i < markers.length; i++ ) {
        var position = new google.maps.LatLng( markers[i][1], markers[i][2] );
        //bounds.extend(position);
        marker = new google.maps.Marker({
            position: position,
            map: map,
            title: markers[i][0],
            icon: canvas.data( 'marker-icon' )
        });

        // Allow each marker to have an info window
        google.maps.event.addListener( marker, 'click', ( function( marker, i ) {
            return function() {
                infoWindow.setContent( markers[i][3] );
                infoWindow.open( map, marker );
            }
        })( marker, i ) );

        // Automatically center the map fitting all markers on the screen
        //map.fitBounds( bounds );
    }

    /*
    // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
    var boundsListener = google.maps.event.addListener(( map ), 'bounds_changed', function( event ) {
        this.setZoom(14);
        google.maps.event.removeListener( boundsListener );
    });
    */

$( '[data-map-recenter]' ).on( 'click', function(e) {
    e.preventDefault();
    var data = $(this).data( 'map-recenter' ).split( ',' );
    map.setCenter( new google.maps.LatLng( data[0], data[1] ) );
});

}

google.maps.event.addDomListener( window, 'load', initMap );

}