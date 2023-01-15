$(document).ready(function() {

"use strict";

init_special_forms();
row_sortable();

$(document).on( 'keyup', '.sinspan', function() {

    $(this).next( 'span' ).text( $(this).val() );

});


$(document).on( 'change', '.cextension', function() {

    $(this).parents( 'form' ).find( '.dlinkext' ).text( $(this).val() );

});


$(document).on( 'click', '.main-nav ul.nav > li.drop-down > a', function( e ) {

    e.preventDefault();

    var t = $(this);

    $( '.main-nav ul.nav li' ).removeClass( 'drop-down-i' );

    $(this).show_next({ animate_before: 'hide', animate: 'slideDown', animate_hide: 'slideUp', element: '.subnav',
        cb_visible: function(){
            t.parent().addClass( 'drop-down-i' );
        }
    });

});


$(document).on( 'click', '.title .options > a', function( e ) {

    e.preventDefault();

    var t = $(this);

    t.show_next({ close_on_click: true, animate: 'slideDown', animate_hide: 'slideUp', element: '.title .options ul',
        cb_visible: function(){
            t.addClass( 'btn-active' );
        },
        cb_notvisible: function() {
            t.removeClass( 'btn-active' );
        }
    });

});


$(document).on( 'click', '.title .options a.more_fields', function( e ) {

    e.preventDefault();

    var t = $(this), ul = $(this).parents('ul'), options = $(this).parents('.options');
    options.find( 'a:first' ).removeClass( 'btn-active' );

    options.parents( '.title' ).nextAll( '.form-table' ).find( '.more_field:hidden' ).removeClass( 'required-hidden' ).addClass( 'required-visible' ).slideDown(80);

    ul.slideUp( 80, function(){
        if( ul.find( 'li' ).length === 1 ) {
            options.remove();
        } else {
            t.parent().remove();
        }
    });

});


$(document).on( 'click', '.top-nav > ul.left-top li:first', function( e ) {

    e.preventDefault();

    $('body').show_next( { type: '', element: '.main-nav' } );

});


$(document).on( 'click', '.elements-list input[data-checkall]', function(){

    if( $(this).is( ':checked' ) ) {
        $(this).parents( '.elements-list' ).find( 'li input[type="checkbox"]:enabled' ).prop( 'checked', true );
    } else {
        $(this).parents( '.elements-list' ).find( 'li input[type="checkbox"]' ).prop( 'checked', false );
    }

});


$(document).on( 'change', '.elements-list input[data-checkall], .elements-list > li input[type="checkbox"]', function(){

    var count = $(this).parents( '.elements-list' ).find( 'li input[type="checkbox"]:checked' ).length;
    if( count > 0 ) {
        $(this).parents( '.elements-list' ).find( '.bulk_options' ).slideDown( 'fast' );
    } else {
        $(this).parents( '.elements-list' ).find( '.bulk_options' ).slideUp( 'fast' );
    }

});


$(document).on( 'click', '.info-bar > a.show_theme_desc', function( e ){

    e.preventDefault();

    var themedesc = $(this).parents( '.info-bar' ).find( '.theme-desc' );

    if( themedesc.is( ':visible' ) ) {
        $(this).children( 'span' ).text( '↙' );
        themedesc.slideUp( 'fast' );
    } else {
        $(this).children( 'span' ).text( '↗' );
        themedesc.slideDown( 'fast' );
    }

});


$( document ).on( 'submit', '#upload-theme-form form', function(){

    var form = $(this).parents( '#upload-theme-form' );

    form.hide();
    form.next( '#process-theme' ).show();

});


$( document ).on( 'submit', '#upload-plugin-form form', function(){

    var form = $(this).parents( '#upload-plugin-form' );

    form.hide();
    form.next( '#process-plugin' ).show();

});


$( document ).on( 'change', 'input[name="shn-site"], input[name="shn-expiration"]', function(){

    $(this).parents( '.row' ).show_next( { element: '.row:eq(0)' } );

});


$( document ).on( 'click', '#modify_mt_but', function( e ){

    e.preventDefault();

    $('body').show_next({ type: '', element: '#modify_mt' });

});


$( document ).on( 'change', '#ownlink', function(){

    $(this).show_next({ animate: 'fadeIn', animate_hide: 'fadeOut', animate_hide_duration: 100, element: 'input' });

});


$( document ).on( 'click', '#ban_fast_choice > a', function( e ){

    e.preventDefault();

    var json = jQuery.parseJSON( $(this).attr('data') );

    var date = new Date();

    date.setMonth( date.getMonth()+1 );

    switch( json.interval ) {
        case 'day':
        date.setHours( date.getHours() + 24*json.nr );
        break;
        case 'week':
        date.setHours( date.getHours() + 24*7*json.nr );
        break;
        case 'month':
        date.setMonth( date.getMonth() + json.nr );
        break;
        case 'year':
        date.setMonth( date.getMonth() + 12*json.nr );
        break;
    }

    if( date.getMonth() == 0 ) {
        date.setMonth(1);
    }

    var nd = date.getFullYear() + '-' + ( '0' + ( date.getMonth() ) ).slice(-2) + '-' + ( '0' + date.getDate() ).slice(-2);
    $(this).parents( '.row' ).prev( '.row' ).find( 'input[type="date"]' ).val( nd );

});

$(document).on( 'change', '#row-verified', function() {

    if( $(this).prop( 'checked' ) ) {
        $(this).parents( '.row' ).next().removeClass( 'required-hidden' ).addClass( 'required-visible' ).show();
    } else {
        $(this).parents( '.row' ).next().removeClass( 'required-visible' ).addClass( 'required-hidden' ).hide();
    }

});

$(document).on( 'click', 'li[data-store-is_physical]', function() {

    if( $(this).parents( '.product-form' ) ) {
        var is_physical = $(this).data( 'store-is_physical' ),
        sell_online = $(this).data( 'sell_online' );

        if( is_physical ) {
            $( '.coupon-form .coupon_type' ).removeClass( 'required-hidden' ).addClass( 'required-visible' ).show( 0, function() {
                var omit_list = [],
                allow_omit = false;
                if( !sell_online ) {
                    allow_omit = true;
                    omit_list.push( 'coupon_use_online' );
                    omit_list.push( 'coupon_url' );
                }
                $( '.coupon-form' ).input_required( { omit_list: omit_list, allow_omit: allow_omit, allow_skip: false } );
            });
        } else {
            $( '.coupon-form .coupon_type' ).removeClass( 'required-visible' ).addClass( 'required-hidden' ).hide( 0, function() {
                $( '.coupon-form' ).input_required( { show_list: ['code', 'coupon_url'] } );
            });
        }
    }

});

$(document).on( 'click', 'li[data-store-is_physical]', function() {

    if( $(this).parents( '.product-form' ) ) {
        var sell_online = $(this).data( 'sell_online' );

        if( sell_online ) {
            $( '.product-form .product_link' ).show();
        } else {
            $( '.product-form .product_link' ).hide();
        }
    }

});

$(document).on( 'click', '[data-delete-msg]', function() {

    if( !confirm( $(this).attr( 'data-delete-msg' ) ) ) {
        return false;
    }

});


$(document).on( 'click', 'section.el-row h2 > a', function( e ) {

    e.preventDefault();

    var t = $(this);
    var body = t.parents( 'section' ).find( '.el-row-body' ),
    type = 0;

    if( body.is( ':visible' ) ) {
        t.text( 'S' );
        body.slideUp( 200 );
        type = 1;
    } else {
        t.text( 'R' );
        body.slideDown( 200 );
        type = 0;
    }

    $.post( 'ajax/set_sessions.php', { ses: t.attr('data-set'), type: type } );

});


$(document).on( 'click', '.section-content > h2 a.updown', function( e ) {

    e.preventDefault();

    var t = $(this);
    var content = t.parents( '.section-content:first' ).find( '.content:first' ),
    type = 0;

    if( content.is( ':visible' ) ) {
        t.text( 'S' );
        content.slideUp( 200 );
        type = 0;
    } else {
        t.text( 'R' );
        content.slideDown( 200 );
        type = 1;
    }

    $.post( 'ajax/set_sessions.php', { ses: t.attr('data-set'), type: type } );

});


$(document).on( 'submit', '#post-chat form', function( e ) {

    e.preventDefault();

    var t = $(this);
    var it = t.find( '[name="text"]' ),
    csrf = t.find( '[name="chat_csrf"]' ).val(),
    text = it.val(),
    ul = $( '#chat-msgs-list' );
    var ul_val = ul.html();

    if( text == '' ) {
        return false;
    }

    ul.html( '<li style="line-height: 60px; text-align: center;"><img src="theme/loader2.svg" alt="" /></li>' );
    it.val( '' );

    $.post( '?ajax=post-chat-msg.php', {msg: text, csrf: csrf }, function( a ) {

    if( a.answer ) {

        $.get( '?ajax=chat-msgs.php', function( msg ){

        var newul = '';

        $.each( msg, function( k, v ) {
          newul += '<li> <div style="display: table;"> <img src="' + v.avatar + '" alt="" /> <div class="info-div"><h2>' + v.name + ' <span class="fright date">' + v.gfdate + '</span></h2> <div class="info-bar">' + v.text + '</div> </div></div> </li>';
        });

        ul.html( newul );

        }, "json" );

    } else {

        ul.html( ul_val );

    }

    }, "json" );

});


$(document).on( 'click', '#post-chat form a', function( e ) {

    e.preventDefault();

    var ul = $( '#chat-msgs-list' );

    ul.html( '<li style="line-height: 60px; text-align: center;"><img src="theme/loader2.svg" alt="" /></li>' );

    $.get( '?ajax=chat-msgs.php', function( msg ){

    var newul = '';

    $.each( msg, function( k, v ) {
        newul += '<li> <div style="display: table;"> <img src="' + v.avatar + '" alt="" /> <div class="info-div"><h2>' + v.name + ' <span class="fright date">' + v.gfdate + '</span></h2> <div class="info-bar">' + v.text + '</div> </div></div> </li>';
    });

    ul.html( newul );

    }, "json" );

});


$(document).on( 'change', 'select[name="mail_meth"]', function(){

    var parent = $(this).parents( 'div' );

    if( $(this).val() == 'SMTP' ) {
        parent.next().slideDown( 'fast' );
    } else {
        parent.next().slideUp( 'fast' );
    }

    if( $(this).val() == 'sendmail' ) {
        parent.next().next().slideDown( 'fast' );
    } else {
        parent.next().next().slideUp( 'fast' );
    }

});


$(document).on( 'change', 'select[name="admin_theme"]', function(){

    $( 'link:first' ).attr( 'href', $(this).data( 'theme-preview' ) );
    alert($(this).data( 'theme-preview' ));

});


$(document).on( 'change', 'input[name="import_coupons"], input[name="import_products"]', function(){

    if( $(this).prop('checked') ) {
        $(this).parents( 'li' ).nextAll( 'li:first' ).show( 200 );
    } else {
        $(this).parents( 'li' ).nextAll( 'li:first' ).hide( 200 );
    }

});


$( 'select[name="privileges"]' ).on_selected({ selected: 1,
    cb_true: function() {
        $( '#privileges_scope' ).slideDown( 200 );
    },
    cb_false: function() {
        $( '#privileges_scope' ).slideUp( 200 );
    }
});


$( '.locations-info input[name="locations-bi"]' ).on_selected({ selected: 'checked',
    cb_true: function( checkbox ) {
        checkbox.nextAll( 'ul,a,div' ).slideUp(100);
    },
    cb_false: function( checkbox ) {
        checkbox.nextAll( 'ul,a,div' ).slideDown(100);
    }
});


$( '.hours-info #hours-bi' ).on_selected({ selected: 'checked',
    cb_true: function( checkbox ) {
        checkbox.nextAll( 'ul' ).slideUp(100);
    },
    cb_false: function( checkbox ) {
        checkbox.nextAll( 'ul' ).slideDown(100);
    }
});


$( '#switch-ft' ).on_selected({ selected: 'checked',
    cb_true: function( checkbox ) {
        checkbox.prevAll( 'input:eq(1)' ).hide();
        checkbox.prevAll( 'input:eq(0)' ).show();
    },
    cb_false: function( checkbox ) {
        checkbox.prevAll( 'input:eq(0)' ).hide();
        checkbox.prevAll( 'input:eq(1)' ).show();
    }
});

$( '.row.select-image' ).each(function() {
    $(this).select_image();
});

/* DUPLICATE ROWS */

$(document).on( 'click', '.fields_table ~ a', function( e ){

    e.preventDefault();

    var ul = $(this).prev( '.fields_table' );
    var head = ul.children( '.head' ),
    row = ul.children( '.fields_table_new' ).html();

    ul.append( '<li class="added_field">' + row + '</li>' );

    var thisli = ul.find( 'li:last' );
    var type = thisli.find( '[data-search]' ).data( 'search' );

    if( type != undefined ) {

        switch( type ) {
            case 'store': thisli.data_search(); break;
            case 'user': thisli.data_search( { search: '../?ajax=search_user' } ); break;
            case 'coupon': thisli.data_search( { search: '../?ajax=search_coupon' } ); break;
            case 'product': thisli.data_search( { search: '../?ajax=search_product' } ); break;
            case 'category': thisli.data_search( { search: '../?ajax=search_category' } ); break;
        }

    }

    if( !head.is( ':visible' ) ) {
        head.show();
    }

    if( $(this).data( 'keeps-key' ) != undefined ) {
        ul.find( '.options .move' ).remove();
    }

});

$(document).on( 'click', '.fields_table li.added_field a:last-child', function( e ){

    e.preventDefault();

    var ul = $(this).parents( 'ul' ),
    head = ul.children( '.head' ),
    lis = ul.find( '.added_field' ).length;

    $(this).parents( 'li' ).remove();

    if( lis <= 1 ) {
        head.hide();
    }

});

$(document).on( 'click', '.multi-rows .options > a.view', function( e ) {

    e.preventDefault();

    var t = $(this);
    var body = t.parents( '.head-row' ).next();

    if( body.is( ':visible' ) ) {
        t.text( 'S' );
        body.slideUp( 200 );
    } else {
        t.text( 'R' );
        body.slideDown( 200 );
    }

});

$(document).on( 'click', '.multi-rows .options > a.remove', function( e ) {

    e.preventDefault();

    var t = $(this);
    var rows = t.parents( '.rows:first' );

    rows.slideUp(200, function(){
        rows.remove();
    });

});

$(document).on( 'click', '.multi-rows a[data-start-row]', function(e) {

    e.preventDefault();

    var t = $(this);
    var rows = t.next();

    var next_rows_html = $( '<div class="rows">' + rows.html() + '</div>' );

    var i = parseInt( t.attr( 'data-start-row' ) );

    $(next_rows_html).find( '[name]' ).each(function () {
       $(this).attr( 'name', function( index, name ) {
            return name.replace( /{id}/g, i );
       });
    });

    $(next_rows_html).find( '[data-required]' ).each(function () {
        $(this).attr( 'data-required', function( index, name ) {
             return name.replace( /{id}/g, i );
        });
     });

    i++;

    // set last row number
    t.attr( 'data-start-row', i );

    t.before( next_rows_html );

    // reinit sortable
    row_sortable();

    // reinit special form fields
    init_special_forms()

    // reinit gallery
    $(next_rows_html).find( '.row.select-image' ).each(function() {
        $(this).select_image();
    });

    $( 'form' ).input_required();

});

$( 'form' ).input_required();

/* Jquery UI */

$( '.arrange-menu:not(.menu-options)' ).sortable( {
    items: 'li:not(.fixed)',
    handle: '.move',
    connectWith: '.arrange-menu',
    start: function( e, ui ) {
        ui.placeholder.height( ui.helper.outerHeight() - 12 );
    },
    placeholder: 'movable-placeholder'
} ).disableSelection();

$( '.arrange-menu.menu-options li' ).draggable({
    connectToSortable: '.arrange-menu',
    helper : 'clone',
    stop: function( e, ui ) {
        $( '.arrange-menu:not(.menu-options)' ).sortable( {
            items: 'li:not(.fixed)',
            handle: '.move',
            connectWith: '.arrange-menu',
            start: function( e, ui ) {
                ui.placeholder.height( ui.helper.outerHeight() - 12 );
            },
            placeholder: 'movable-placeholder'
        } );

        $( '.arrange-menu:not(.menu-options) li' ).removeAttr( 'style' );

        // reinit special form fields
        init_special_forms();
    }
});

$( 'form#modify-menu-form' ).one( 'submit', function(e) {

    e.preventDefault();

    var t = $(this);
    var levels = t.find( '> .arrange-menu > li' );
    function a(x) {
        var i = 0;
        levels.each( function() {
            var s = x + '[' + i + ']';
            $(this).find( '> .head input,> .head select,> .head textarea' ).each( function() {
               $(this).attr( 'name', function( index, name ) {
                    return name.replace( /{id}/g, s );
               });
            });
            levels = $(this).find( '> .arrange-menu > li' );
            a( s + '[links]' );
        i++;
        });
    }

    a('');
    t.trigger( 'submit' );

});

$( document ).on( 'click', '#modify-menu-form .remove', function(e) {

    e.preventDefault();

    var t = $(this);
    t.parents( 'li:first' ).slideUp( 200, function(){
        $(this).remove();
    });

});

$( document ).on( 'click', '#modify-menu-form .hide', function(e) {

    e.preventDefault();

    var t = $(this);
    var ulchilds = t.parents( 'li:first' ).find( 'ul:first > li' );
    if( ulchilds.is( ':visible' ) ) {
        ulchilds.slideUp( 200, function() {
            t.text( 'x' );
        } );
    } else {
        ulchilds.slideDown( 200, function() {
            t.text( 'w' );
        } );
    }

});

$( document ).on( 'click', '#modify-menu-form .view', function(e) {

    e.preventDefault();

    var t = $(this);
    var content = t.parents( 'h2' ).next();
    if( content.is( ':visible' ) ) {
        content.slideUp( 200, function() {
            t.text( 'S' );
        } );
    } else {
        content.slideDown( 200, function() {
            t.text( 'R' );
        } );
    }

});

$( document ).on( 'keyup', '#modify-menu-form .content .name input', function() {

    var t = $(this);
    var title = t.parents( '.content' ).prev().find( '> span' );
    title.text( t.val() );

});

$( document ).on( 'click', '.page-options-menu li', function() {

    var t = $(this);
    var s = t.find( 'a' ).data( 'section' );

    if( t.hasClass( 'active' ) ) {
        return;
    }

    var olinks = t.parent();
    var form_table = olinks.nextAll( '.form-table:first' );

    olinks.find( 'li.active' ).removeClass( 'active' );
    t.addClass( 'active' );

    if( typeof s !== 'undefined' ) {
        form_table.find( 'form > .row' ).hide();
        form_table.find( 'form > .row[data-in-section="' + s + '"]:not(.required-hidden),form > .row.required-visible[data-in-section="' + s + '"]' ).css( 'display', 'table' );
    } else {
        form_table.find( 'form > .row' ).hide();
        form_table.find( 'form > .row:not([data-in-section]):not(.required-hidden), .row.required-visible:not([data-in-section])' ).css( 'display', 'table' );
    }

} );

// Disable browser's default datepicker
$( 'input[type="date"]' ).on( 'click', function(e) {
    e.preventDefault();
});

var el = document.querySelector( '.main-nav' );
if( el != null ) {
SimpleScrollbar.initEl(el);
}

});

/* SEARCH STUFF */

function init_special_forms() {
    $( '[data-search="store"]' ).data_search();
    $( '[data-search="user"]' ).each(function(){
        $(this).data_search( { search: '../?ajax=search_user' } );
    });
    $( '[data-search="coupon"]' ).each(function(){
        $(this).data_search( { search: '../?ajax=search_coupon' } );
    });
    $( '[data-search="product"]' ).each(function(){
        $(this).data_search( { search: '../?ajax=search_product' } );
    });
    $( '[data-search="category"]' ).each(function(){
        $(this).data_search( { search: '../?ajax=search_category' } );
    });
    $( '.colorpicker' ).each(function(){
        $(this).colorPicker();
    });
    $( '.datepicker' ).each(function(){
        $(this).datetimepicker( { timepicker:false, format:'Y-m-d' } );
    });
    $( '.hourpicker' ).each(function(){
        $(this).datetimepicker( { datepicker:false, format:'H:i' } );
    });
    $( '.timepicker' ).each(function(){
        $(this).datetimepicker( { format:'Y-m-d H:i' } );
    });
}

/* SORTABLE ROWS */
function row_sortable() {
    $( '.sortable' ).sortable( {
        items: '> li:not(.fixed),> .rows',
        handle: '.move',
        start: function( e, ui ) {
            ui.placeholder.height( ui.helper.outerHeight() );
        },
        placeholder: 'movable-placeholder'
    } ).disableSelection();
}