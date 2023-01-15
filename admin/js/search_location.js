(function ( $ ) {

$.fn.search_store_location = function( options, type, callback ) {

var option = $.extend({
    onLoad: false,
    autoLoadCity: false,
    SearchLocation: '../',
    StateElement: 'select[name="state"]',
    CityElement: 'select[name="city"]',
    Container: 'form'
}, options );

var $this = this;

function change_country() {
    $.post( option.SearchLocation + '/?ajax=states_from_country', {id: $this.val()}, function(){
    }, "json" ).done( function( data ) {
        var newoptions = '', first, i = 0;
        $.each( data, function(k, v) {
        if( i === 0 ) {
            first = v.ID;
        }
        newoptions += '<option value="' + v.ID + '" data-lat="' + v.lat + '"    data-lng="' + v.lng + '"' + ( i === 0 ? ' selected' : '') + '>' + v.name + '</option>';
        i++;
        });
        $(option.StateElement).html( newoptions );
        if ( change_state(first) && $.isFunction( callback) ) {
            callback.call(this);
        }
    });
}

function change_state(val) {
    if( !val ) {
        val = $this.val();
    }
    $.post( option.SearchLocation + '/?ajax=cities_from_state', {id: val}, function() {
    }, "json" ).done( function( data ){
        var newoptions = '', i = 0;
        $.each( data, function(k, v) {
        newoptions += '<option value="' + v.ID + '" data-lat="' + v.lat + '"    data-lng="' + v.lng + '"' + ( i === 0 ? ' selected' : '') + '>' + v.name + '</option>';
        i++;
        });
        if( option.autoLoadCity ) {
            $(option.CityElement).html( newoptions );
        }
        if ( val && $.isFunction( callback) ) {
            callback.call(this);
        }
    });
}

function doit() {

switch( type ) {
    case 'country':
    change_country();
    break;
    case 'state':
    change_state();
    break;
    case 'city':
    if ( $.isFunction( callback) ) {
        callback.call(this);
    }
    break;
    default:
    alert( 'Unknown search type!' );
    return false;
    break;
}

}

if( option.onLoad ) {

$this.on( 'change keyup', function() {
    doit();
}).change();

} else {

$this.on( 'change keyup', function() {
    doit();
});

}

};

}( jQuery ));