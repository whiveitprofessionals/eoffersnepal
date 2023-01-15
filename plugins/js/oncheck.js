(function ( $ ) {

"use strict";

$.fn.oncheck = function( options, type ) {

var option = $.extend({
  onLoad: false,
  Elements: '',
  Container: 'form'
}, options );

var $this = this;
var elements = $this.parents( option.Container ).find( option.Elements );

function fhide() {
  $(elements).hide();
}

function fshow() {
  $(elements).show();
}

function doit() {
if( $this.is( ':checked' ) ) {
  if( type === 'unchecked' ) {
    fhide();
  } else {
    fshow();
  }
  } else {
  if( type === 'unchecked' ) {
    fshow();
  } else {
    fhide();
  }
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