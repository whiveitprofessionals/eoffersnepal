$( document ).ready(function() {

"use strict";

$( document ).on( 'click', 'body', function() {
    $( '[data-opened]' ).slideUp( 'fast' );
});

$( '.category-select > a' ).on( 'click', function( e ) {
    e.preventDefault();
    e.stopPropagation();
    var t = $(this);
    var list = t.nextAll( 'ul' );
    if( list.is(':visible') ) {
        list.slideUp( 'fast' ).removeAttr( 'data-opened' );
    } else {
        list.slideDown( 'fast' ).attr( 'data-opened', '' );
    }
});

$( '.category-select li > a' ).on( 'click', function( e ) {
    e.preventDefault();
    e.stopPropagation();
    var t = $(this);
    var val = t.data( 'attr' ),
    list = t.closest( 'ul' )
    a = list.prevAll( 'a' ).find( 'span' ),
    input = list.prevAll( 'input' );

    a.text( t.text() );
    input.val( val );
    list.slideUp( 'fast' ).removeAttr( 'data-opened' );

});

$( '.description .more-link a, .description .less-link a' ).on( 'click', function(e) {
    e.preventDefault();
    var desc = $(this).parents( '.description' );
    var part = desc.find( '.hidden-part' ),
    more = desc.find( '.more-link' ),
    less = desc.find( '.less-link' );
    if( part.is( ':visible' ) ) {
        part.hide();
        more.show();
        less.hide();
    } else {
        part.show();
        more.hide();
        less.show();
    }
});

$( '.owl-carousel' ).each( function() {
    var t = $(this);
    t.owlCarousel({
        loop:( t.data( 'loop' ) != undefined ? true : false ),
        margin:10,
        autoplay:( t.data( 'autoplay' ) != undefined ? true : false ),
        nav:( t.data( 'arrows' ) != undefined ? true : false ),
        dots:( t.data( 'bullets' ) != undefined ? true : false ),
        navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
                nav:false
            },
            600:{
                items:3
            },
            1000:{
                items:5
            }
        }
    });
});

$( '.user-sub-menu > a' ).on( 'click', function( e ) {
    e.preventDefault();
    var ul = $(this).next( 'ul' );
    var menu = $(this).parents( '.user-menu' );
    if( ul.is( ':visible' ) ) {
        ul.slideUp( 'fast' );
    } else {
        menu.find( '.user-sub-menu > ul:visible' ).slideUp();
        ul.slideDown( 'fast' );
    }
});

$( '.mmenu' ).on( 'click', function() {
    var t = $(this);
    var menu = $( '.main-nav' );
    t.toggleClass( 'open' );
    if( menu.is( ':visible' ) ) {
        menu.slideUp();
    } else {
        menu.slideDown();
    }
});

$( window ).resize(function(){

    if( $( window ).width() > 768 ) {

        var mmenu = $( '.mmenu' );
        var menu = $( '.main-nav' );

        mmenu.removeClass( 'open' );
        menu.removeAttr( 'style' );

    }

}).resize();

$( '.list-item .sub-info a.share' ).on( 'click', function( e ) {
    e.preventDefault();
    var ul = $(this).prev( 'ul' );
    if( ul.is( ':visible' ) ) {
        ul.fadeOut( 'fast' );
    } else {
        ul.fadeIn( 'fast' ).css( 'display', 'inline-block' );
    }
});

$( '.item-text .info .links-list a.hours' ).on( 'click', function( e ) {
    e.preventDefault();
    var ul = $(this).next( 'ul' );
    if( ul.is( ':visible' ) ) {
        ul.fadeOut( 'fast' );
    } else {
        ul.fadeIn( 'fast' );
    }
});

$( '.question > a' ).on( 'click', function( e ) {
    e.preventDefault();
    var answer = $(this).next( '.answer' );
    if( answer.is( ':visible' ) ) {
        answer.slideUp( 'fast' );
    } else {
        answer.slideDown( 'fast' );
    }
});

$( '[data-href]' ).on( 'change', function() {
    window.location = $(this).data( 'href' );
});

$( '[data-tooltip]' ).tooltip();

$( '[href="#search"]' ).on( 'click', function( e ) {
    e.preventDefault();
    $( '#search-popup' ).fadeIn( 300 );
});

$( '[href="#close-search-popup"]' ).on( 'click', function( e ) {
    e.preventDefault();
    $( '#search-popup' ).fadeOut( 300 );
});

$( '[data-copy-this]' ).on( 'click', function( e ) {
    e.preventDefault();
    $(this).nextAll( 'input' ).focus().select();
    document.execCommand( 'copy' );
    t.nextAll( 'input' ).blur();  
});

$( '[data-target-on-click]' ).on( 'click', function() {
    var t = $(this);
    setTimeout(function(){
        var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        if( isSafari ) {
            t.removeAttr( 'target' );
        }
        window.location = t.data( 'target-on-click' );
    }, 1000);
});

$( '[data-copy-this]' ).on( 'click', function() {
    $(this).fadeTo('fast', 0).delay( 5000 ).fadeTo( 'slow', 1 );
});

$( '[data-code]' ).on( 'click', function(e) {
    e.preventDefault();
    $(this).hide();
    $(this).after( '<span>' + $(this).data( 'code' ) + '</span>' );
    $(this).remove();
});

/* COUPONS CMS */

$( '.claim_reward_form form button' ).on( 'click', function(e) {
    var t = $(this);

    if( t.prevAll( '.extra_form' ).html() != 'undefied' && t.prevAll( '.extra_form' ).is( ':hidden' ) ) {
        e.preventDefault();
        t.prevAll( '.extra_form' ).slideDown();
    }
});

$( document ).on( 'keypress', '.subscribe_form input[type="email"]', function(e) {
    var captcha = $(this).next();
    if( captcha.is( ':hidden' ) ) {
        captcha.slideDown();
    }
});

});