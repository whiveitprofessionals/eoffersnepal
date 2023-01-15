$( document ).ready(function() {

"use strict";

$(document).on( 'click', '[data-ajax-call]:not(.disabled)', function(e) {
    e.preventDefault();
    var t = $(this);
    var url = t.data( 'ajax-call' ),
    data = t.data( 'data' );

    if( t.data( 'confirmation' ) == undefined || confirm( t.data( 'confirmation' ) ) ) {
        $.post( url, data, function( result ) {
            if( t.data( 'after-ajax' ) != undefined ) {
                switch( t.data( 'after-ajax' ) ) {
                    case 'ajax_voted':
                        if( result.state != 'success' ) {
                            window.location = login_page;
                        } else {
                            t.parents( '.vote-buttons, .single-vote' ).html( '<span class="ajax-message">' + result.message + '</span>' );
                            $('.tooltip').remove();
                        }
                    break;
                    case 'coupon_claimed':
                        if( result.state == 'success' ) {
                            t.addClass( 'disabled' );
                            t.html( result.message );
                        } else {
                            window.location = login_page;
                        }
                    break;
                }
            } else {
                if( result.state != 'success' ) {
                    window.location = login_page;
                } else {
                    t.html( result.message );
                }
            }
        }, "json" );
    }
});

});