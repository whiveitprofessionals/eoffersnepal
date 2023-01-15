(function ( $ ) {

"use strict";

// SHOW/HIDE FUNCTION - USED FOR MENU SLIDE DOWN/UP

$.fn.show_next = function( options ) {

var option = $.extend({
    element: '',
    type: 'next',
    animate_before: '',
    animate_duration: 200,
    animate: 'slideDown',
    animate_hide: 'slideUp',
    animate_hide_duration: 200,
    close_on_click: false
}, options );

var $this = this, target;

if( option.type == 'next' ) {

    target = this.nextAll( option.element );

} else {

    target = this.find( option.element );

}

function animate( animate, duration, utarget, cb ) {

    if( utarget == '' ) {
        utarget = target;
    }

    switch( animate ) {

        case 'slideUp':

           utarget.slideUp( duration, function(){

            if( cb ) {
              if ( $.isFunction( options.cb_notvisible ) ) {
                options.cb_notvisible.call(this);
              }
            }

          } );

        break;

        case 'fadeOut':

           utarget.fadeOut( duration, function(){

            if( cb ) {
              if ( $.isFunction( options.cb_notvisible ) ) {
                options.cb_notvisible.call(this);
              }
            }

          } );

        break;

        case 'slideDown':

           target.slideDown( duration, function(){

           if ( $.isFunction( options.cb_visible ) ) {
             options.cb_visible.call(this);
           }

          } );

        break;

        case 'fadeIn':

           target.fadeIn( duration, function(){

           if ( $.isFunction( options.cb_visible ) ) {
             options.cb_visible.call(this);
           }

          } );

        break;

        case 'toggle':

           target.toggle( duration, function(){

           if ( $.isFunction( options.cb_visible ) ) {
             options.cb_visible.call(this);
           }

          } );

        break;

    }

}


// the app may require to all other visible elements

if( option.animate_before == 'hide' ) {

    animate( option.animate_hide, option.animate_hide_duration, $( option.element + ':visible' ), false );

}

if( target.is(':visible') ) {

    animate( option.animate_hide, option.animate_hide_duration, '', true );

} else {

    animate( option.animate, option.animate_duration, '', true );

}

if( option.close_on_click ) {

    $('html').on( 'click', function(e){

        if( $( e.target ).closest( option.element ).length === 0 ) {
            animate( option.animate_hide, option.animate_hide_duration, '', true );
        }

    });

}

};

// ON SELECT FUNCTION - USED TO CREATE EVENTS ON DEFINED OPTION IS SELECTED

$.fn.on_selected = function( options ) {

var option = $.extend({
    selected: 1
}, options );

var $this = this;

function show() {

if( $this.val() == option.selected || ( option.selected == 'checked' && $this.is(':checked') ) ) {

    if ( $.isFunction( options.cb_true ) ) {
        option.cb_true.call(this, $this);
    }

} else {

    if ( $.isFunction( options.cb_false ) ) {
        option.cb_false.call(this, $this);
    }

}

}

this.on('change keyup',function() {
    show();
});

};

// SEARCH DATA FUNCTION - USED TO SEARCH USERS OR STORES

$.fn.data_search = function( options ) {

var option = $.extend({
    search: '../?ajax=search_store'
}, options );

var $this = this;

var cclas = $this.attr('data-set-class') != undefined ? ' ' + $this.attr( 'data-set-class' ) : '';

function search( $this ) {

    var $input = $this.find( 'input' );
    $this.find( '.search_box' ).html( '<div style="min-height:40px;margin-top:17px;text-align:center;"><img src="theme/loader2.svg" alt="" /></div>' );

    var val = $input.val();

    $.post( option.search, { search: val }, function( data ) {

        var search_container, ajaxoptions;

        if( data.length === 0 ) {
            search_container = '<div style="padding:5px;">Nothing found.</div>';
        } else {

        search_container = '<ul class="search-results">';

        $.each(data, function( id, ajax ) {

        ajaxoptions = '';
        $.each( ajax, function( avalue ) {
            ajaxoptions += 'data-' + avalue + '="' + ajax[avalue] + '"';
        });

        search_container += '<li data-id="' + id + '"' + ajaxoptions + '>' + ajax.name + '</li>';

        });

        search_container += '</ul>';

        }

        $this.find( '.search_box' ).html( search_container );

        /* li event */

        $this.find( '.search-results' ).find( 'li' ).one( 'click', function(e) {

            e.preventDefault();

            var $a = $this.find( 'a' );
            var $info = $this.find( '.idinfo' );

            // set the new value into input after click
            $input.val( $(this).data( 'id' ) );

            // set the new name and id to the info
            $info.text( $(this).data( 'name' ) + ' (ID: ' + $(this).data( 'id' ) + ')' );

            // set arrow orientation down
            $a.removeClass( 'uparr' ).addClass( 'downarr' );

            // remove the box
            $this.find( '.search_box' ).remove();

            // update category
            if( $this.parents( '.row' ).hasClass( 'autoset-cat' ) ) {
                $( 'select[name="category"]' ).val( $(this).data( 'catid' ) );
            }

        });

  }, "json" );

}

/* click on arrow event */

$this.find( 'a' ).on( 'click', function(e) {

    e.preventDefault();

    var $this = $(this).parents( '[data-search]' );

    if( $this.find( '.search_box' ).length > 0 ) {

        // set arrow orientation up
        $(this).removeClass( 'uparr' ).addClass( 'downarr' );

        // remove the box
        $this.find( '.search_box' ).remove();

    } else {

        // set arrow orientation down
        $(this).removeClass( 'downarr' ).addClass( 'uparr' );

        // add the box
        $this.append( '<div class="search_box' + cclas + '"></div>' );

        search( $this );

}

});

/* select the value when focus */

$this.find( 'input' ).on( 'focus', function(e) {
    $(this).select();
});

/* input value change event */

$this.find( 'input' ).on( 'keyup', function() {

    var $this = $(this).parents( '[data-search]' );
    var $a = $this.find( 'a' );
    var $info = $this.find( '.idinfo' );

    if( $(this).val() == '' ) {

        // remove name and id from the info
        $info.text( '' );

        // set arrow orientation down
        $a.removeClass( 'uparr' ).addClass( 'downarr' );

        // remove the box
        $this.find( '.search_box' ).remove();
        return false;

    }

    if( $this.find( '.search_box' ).length > 0 ) {

        search( $this );

    } else {

    if( $.isNumeric( $(this).val() ) ) {

        // remove name and id from the info
        $info.text( '' );

        return false;

    }

    // set arrow orientation up
    $a.removeClass( 'downarr' ).addClass( 'uparr' );

    // add the box
    $this.append( '<div class="search_box' + cclas + '"></div>' );

    search( $this );

    }

});

};

// INPUT REQUIRED

$.fn.input_required = function( options ) {

var option = $.extend({
    omit_list: [],
    allow_omit: true,
    skip_list: [],
    allow_skip: true,
    show_list: []
}, options );

var form = this,
list = {},
eles = {};

this.find( '[data-required]' ).each( function() {

    var t = $(this),
    name,
    is_container = false;

    if( t.is( '[data-required-name]' ) ) {
        name = t.data( 'required-name' );
        is_container = true;
    } else {
        name = t.find( 'input,select,textarea' ).attr( 'name' );
    }

    var data = t.data( 'required' ),
    omit = t.data( 'omit-required' ),
    skip = t.data( 'skip-required' );

    if( option.allow_omit ) {
        if( omit != undefined && omit ) {
            option.omit_list.push( name );
        }
    }

    if( option.allow_skip ) {
        if( skip != undefined && skip ) {
            option.skip_list.push( name );
        }
    }

    var key = 0;
    list[name] = {};
	list[name]['cond'] = {};
    list[name]['is_container'] = is_container;

    eles[name] = {};

    $.each( data, function( id, obj ) {

        eles[name][id] = {};

        if( obj.constructor == Array ) {

            $.each( obj, function( idi, obji ) {
                list[name]['cond'][key] = {};
                var type = 'in';
                obj = obj.toString();
                if( obji.substring( 0, 2 ) == '!!' ) {
                    type = 'not_in';
                    obji = obji.substring( 2 );
                }
                list[name]['cond'][key]['type'] = type;
                list[name]['cond'][key]['value'] = obji;
                list[name]['cond'][key]['el']  = id;
                eles[name][id][key] = '';

                key++;
            });

        } else {

            list[name]['cond'][key] = {};
            var type = 'in';
            obj = obj.toString();
            if( obj.substring( 0, 2 ) == '!!' ) {
                type = 'not_in';
                obj = obj.substring( 2 );
            }
            list[name]['cond'][key]['type'] = type;
            list[name]['cond'][key]['value'] = obj;
            list[name]['cond'][key]['el']  = id;
            eles[name][id][key] = '';

            key++;

        }

    });

    key = 0;

});

var items = Object.keys(list).length;

function parent_is_visible( pel, pname, p_is_container ) {
    if( $.inArray( pname, option.omit_list ) != -1 ) {
        return false;
    }

    if( ( p_is_container && form.find( '[data-required-name="' + pname + '"]' ).not( ':visible' ) ) || ( !p_is_container && form.find( '[name="' + pel + '"]' ).parents( '.row' ).is( ':visible' ) ) ) {
        return true;
    }

    return false;
}

function action( name, el, val, is_container, use_skip_list ) {

    if( name in list && ( !use_skip_list || $.inArray( name, option.skip_list ) == -1 ) ) {

        var cond = list[name]['cond'];

        $.each( cond, function( id, obj ) {
            if( el == obj.el ) {
                if( obj.type == 'in' ) {
                    if( val == obj.value ) {
                        if( !$.isEmptyObject( eles[name][el] ) ) {
                            delete eles[name][el][id];
                        }
                    } else {
                        if( $.isEmptyObject( eles[name][el] ) ) {
                            eles[name][el] = {};
                        }
                        eles[name][el][id] = '';
                    }
                } else if( obj.type == 'not_in' ) {
                    if( val != obj.value ) {
                        if( !$.isEmptyObject( eles[name][el] ) ) {
                            delete eles[name][el][id];
                        }
                    } else {
                        if( $.isEmptyObject( eles[name][el] ) ) {
                            eles[name][el] = {};
                        }
                        eles[name][el][id] = '';
                    }
                }
            }
            if( $.isPlainObject( eles[name][el] ) && Object.keys( eles[name][el] ).length == 0 ) {
                delete eles[name][el];
            }
        });

        if( $.inArray( name, option.show_list ) != -1 || ( Object.keys( eles[name] ).length == 0 && parent_is_visible( el, name, is_container ) ) ) {
            if( is_container ) {
                form.find( '[data-required-name="' + name + '"]:first' ).addClass('required-visible').css( 'display', 'table' );
            } else {
                form.find( '[name="' + name + '"]' ).parents( '.row:first' ).addClass('required-visible').css( 'display', 'table' );
            }
        } else {
            if( is_container ) {
                form.find( '[data-required-name="' + name + '"]:first' ).removeClass('required-visible').addClass('required-hidden').hide();
            } else {
                form.find( '[name="' + name + '"]' ).parents( '.row:first' ).removeClass('required-visible').addClass('required-hidden').hide();
            }
        }
    }
}

if( items > 0 ) {

    $.each( list, function( id, obj ) {
        $.each( obj.cond, function( id2, obj2 ) {

            var el = form.find( '[name="' + obj2.el + '"]' );
            var val = el.val();

            if( el.prop( 'tagName' ) == 'INPUT' && el.attr( 'type' ) == 'checkbox' ) {
                val = el.prop( 'checked' ) ? 1 : 0;
            }
            action( id, obj2.el, val, obj.is_container, true );

        });
    });

    this.find( 'input,select,textarea' ).on( 'change keyup', function() {

        var t = $(this);
        var name = t.attr( 'name' ),
        val = t.val();

        if( t.prop( 'tagName' ) == 'INPUT' && t.attr( 'type' ) == 'checkbox' ) {
            val = t.prop( 'checked' ) ? 1 : 0;
        }

        $.each( list, function( id, obj ) {
            $.each( obj.cond, function( id2, obj2 ) {
                if( name == obj2.el ) {
                    action( id, name, val, obj.is_container, false );
                }
            } );
        });

    });

}

};

// SELECT IMAGE - IMAGE UPLOAD

$.fn.select_image = function( options ) {

    var option = $.extend({
        //
    }, options );

    var $this = this;
    var button = this.find( 'a.btn' );
    var multi = button.is( '[data-multi]' ),
    category = '',
    search = '';

    var default_modal_html;
    var modal = $( '.modal' ); 
    var selected = {};

    button.on( 'click', function(e) {
        e.preventDefault();
        if( modal.length == 0 ) {
            if( typeof button.data( 'category' ) !== 'undefined' ) {
                category = button.data( 'category' );
            }
            $( 'body' ).append( '<div class="modal"><img src="theme/loader.svg" alt="" /></div>' );
            
            modal = $( '.modal' );
            var default_selected = button.nextAll( 'input[type="hidden"]' ).val();
            if( default_selected !== '' ) {
                selected = JSON.parse( default_selected );
            }
            $.post( '?ajax=image-upload-modal.php', { multi: multi, category: category }, function( data ) {
                modal.html( '' ).append( data );
                default_modal_html = modal.find( '.modal-loader' ).html();
                modal.find( '.modal-container' ).fadeIn( 100, function() {
                    activate_modal();
                    load_content( { multi: multi, selected: Object.keys( selected ), category: category } );
                } );
            });
        } 
    });

    function activate_modal() {
        modal.on( 'click', function(e) {
            if (e.target !== this)
            return;
            e.preventDefault();
            modal.fadeOut( 100, function() {
                modal.remove();
                modal = $();
                selected = {};
                search = '';
                category = '';
            });        
        } );

        modal.find( '.btn.modal-close' ).on( 'click', function(e) {
            e.preventDefault();
            modal.fadeOut( 100, function() {
                modal.remove();
                modal = $();
                selected = {};
                search = '';
                category = '';
            } );
        });

        modal.find( '.btn.modal-save' ).on( 'click', function(e) {
            e.preventDefault();
            var el = button.prevAll( 'ul.images-list' );
            if( el.length === 0 ) {
                el = $( '<ul class="images-list clearfix"></ul>' ).insertBefore( button );
            }

            if( Object.keys( selected ).length > 0 ) {
                var data = '';
                $.each( selected, function( k, v ) {
                    data += '<li><img src="' + ( /^(f|ht)tps?:\/\//i.test( v ) ? v : '../' + v ) + '" /></li>';
                } );
                el.html( data );
            } else {
                el.remove();
            }
            button.nextAll( 'input[type="hidden"]' ).val( JSON.stringify( selected ) )
            modal.find( '.btn.modal-close' ).trigger( 'click' );
        });

        modal.find( '.btn.modal-upload' ).on( 'click', function(e) {
            e.preventDefault();
            var container = modal.find( '.modal-upload-container' );
            if( container.is( ':visible' ) ) {
                container.fadeOut( 100 );
                $(this).removeClass( 'btn-active' );
            } else {
                container.fadeIn( 100 );
                $(this).addClass( 'btn-active' );
            }
        } );

        modal.find( '.modal-upload-container form input[type="file"]' ).on( 'change', function() {
            var form = $(this).parents( 'form' );
            var formData = new FormData( form[0] );
            var msg = $(this).parents( '.modal-upload-container' ).find( '> span' );
            var default_msg = msg.html();

            formData.append( 'category', category );

            msg.html( '<img src="theme/loader2.svg" alt="" />' );

            $.ajax({
                type: 'POST',
                url: '?ajax=gallery-upload.php',
                data: formData,
                xhr: function() {
                    var Xhr = $.ajaxSettings.xhr();
                    return Xhr;
                },
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    msg.html( default_msg );
                    load_content( { multi: multi, selected: Object.keys( selected ), category: category } );
                },
                error: function(data) {
                    console.log(data);
                }
            });
        } );

        modal.find( '[data-modal-search]' ).on( 'keyup', function() {
            search = $(this).val();
            load_content( { multi: multi, selected: Object.keys( selected ), search: search, category: category } );
        } );
 
        modal.find( '[data-modal-category]' ).on( 'change', function() {
            category = $(this).val();
            load_content( { multi: multi, selected: Object.keys( selected ), search: search, category: category } );
        } );
    }

    function activate_content() {
        modal.find( '.modal-items input[type="checkbox"], .modal-items input[type="radio"]' ).on( 'change', function() {
            var t = $(this);
            var the_id = t.attr( 'id' ),
            type = t.attr( 'type' );
            
            if( type == 'radio' ) {
                selected = {};
                selected[the_id] = t.val();
            } else if( type == 'checkbox' ) {
                if( t.prop( 'checked' ) ) {
                    selected[the_id] = t.val();
                } else {
                    delete selected[the_id];
                } 
            }

            if(  modal.find( '.modal-save' ).is( ':hidden' ) ) {
                modal.find( '.modal-save' ).fadeIn( 100 );
            }
        } );

        modal.find( '.modal-items > li .links > a.delete' ).on( 'click', function(e) {
            e.preventDefault();
            var li = $(this).parents( 'li' );
            var the_id = $(this).attr( 'id' );
            li.append( '<div class="empty-content"><img src="theme/loader2.svg" alt=""></div>' );
            $.post( '?ajax=gallery-delete-image.php', {id: the_id}, function( data ) {
                if( data.success ) {
                    li.fadeOut( 100, function() {
                        li.remove();
                    } );
                } else {
                    li.find( '.empty-content' ).remove();
                }
            }, "json" );
        } );
    }

    function load_content( postdata ) {
        var modal_content = modal.find( '.modal-loader' );
        modal_content.html( default_modal_html );
        $.post( '?ajax=gallery-images.php', postdata, function( data ) {
            modal_content.html( data );
            activate_content();
        } );
    }
     
};

}( jQuery ));