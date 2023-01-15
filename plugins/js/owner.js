(function ( $ ) {

"use strict";

$.fn.change_coupon_type = function( options ) {

var option = $.extend({
  onLoad: false,
  ESource: '[data-source]',
  EAvblOnline: '[data-avbl-online]',
  ELimit: '[data-limit]',
  Element: '[data-change-type]',
  HideElements: '[data-hideSS]',
  ShowElements: '',
  Uncheck: '[data-is_online]',
  Container: 'form'
}, options );

var $this = this;
var sellOnline = Boolean( $( 'option:selected', $this ).attr( 'sellOnline' ) );
var container = $this.parents( $(option.Container) );
var source = container.find( $(option.ESource) );
var avblonline = container.find( $(option.EAvblOnline) );
var limit = container.find( $(option.ELimit) );

function doit( val ) {

if( option.HideElements != '' ) {
  $(option.HideElements).hide();
}

if( option.ShowElements != '' ) {
  $(option.ShowElements).show();
}

if( option.Uncheck != '' ) {
  $(option.Uncheck).prop( 'checked', false );
}

val = Number( val );

  switch( val ) {
    case 0:
        source.slideUp( 100 );
        limit.slideUp( 100 );
        avblonline.slideDown( 100 );
    break;
    case 1:
        source.slideUp( 100 );
        limit.slideUp(100);
        avblonline.slideUp( 100 );
    break;
    case 2:
        avblonline.slideUp( 100 );
        limit.slideUp( 100 );
        source.slideDown( 100 );
    break;
    case 3:
        source.slideUp( 100 );
        avblonline.slideUp( 100 );
        limit.slideDown( 100 );
    break;
  }

}

if( option.onLoad ) {

$( option.Element ).on( 'change keyup', function() {
  doit( $(this).val() );
}).change();

} else {

$( option.Element ).on( 'change keyup', function() {
  doit( $(this).val() );
});

}

};

$.fn.switch_store_from_coupon = function( options ) {

var option = $.extend({
  onLoad: false,
  EShow: '[data-showSS]',
  EHide: '[data-hideSS]',
  EHideOther: '[data-source],[data-avbl-online]',
  Container: 'form'
}, options );

var $this = this;
var container = $this.parents( option.Container );
var selem = container.find( option.EShow );
var helem = container.find( option.EHide );

function doit() {

if( option.EHideOther != '' ) {
  $( option.EHideOther ).hide();
}

if( Boolean( $( 'option:selected', $this ).attr( 'isPhysical' ) ) ) {
  helem.slideUp( 100, function(){
    selem.slideDown( 100 );
  });
  $this.change_coupon_type( {onLoad: true} );
} else {
  helem.slideDown( 100, function(){
    selem.slideUp( 100 );
  });
}

}

if( option.onLoad ) {

this.on('change keyup',function() {
  doit();
}).change();

} else {

this.on('change keyup',function() {
  doit();
});

}

};

$.fn.switch_store_type = function( options ) {

var option = $.extend({
  onLoad: false,
  ActionOn: 0,
  EShow: '[data-showSS]',
  EHide: '[data-hideSS]',
  EHideOther: '',
  Container: 'form'
}, options );

var $this = this;
var container = $this.parents( option.Container );
var selem = container.find( option.EShow );
var helem = container.find( option.EHide );

function doit() {

if( option.EHideOther != '' ) {
  $( option.EHideOther ).hide();
}

if( Number( $this.val() ) === option.ActionOn ) {
  helem.slideUp( 100, function(){
    selem.slideDown( 100 );
  });
  $this.change_coupon_type( {onLoad: true} );
} else {
  helem.slideDown( 100, function(){
    selem.slideUp( 100 );
  });
}

}

if( option.onLoad ) {

this.on('change keyup',function() {
  doit();
}).change();

} else {

this.on('change keyup',function() {
  doit();
});

}

};

}( jQuery ));