<?php if( ( $me = me() ) ) { ?>

<?php if( logout() ) { ?>

<div class="container pt50 pb50">

<div class="row pt50">

    <div class="col-md-12 text-center title">
        <h1><?php echo sprintf( t( 'theme_logout_title', '<strong>%s</strong>, we hope you will come back soon !' ), $me->Name ); ?></h1>
    </div>

</div>

<div class="row pb50">

    <div class="col-md-12 text-center">
        <h3><?php echo sprintf( t( 'theme_logout_msg', 'You will be redirected in %s seconds.' ), '<span id="redirect-in">5</span>' ); ?></h3>
    </div>

</div>

</div>

<script>

var redirect = document.getElementById( 'redirect-in' );
var seconds = parseInt( redirect.innerHTML );

var goto = setInterval(function(){

if( seconds < 2 ) {
  location.href = '<?php echo site_url(); ?>';
  clearInterval( goto );
} else {
  seconds--;
  redirect.innerHTML = seconds;
}

}, 1000);

</script>

<?php

} else echo read_template_part( '404' );

} else echo read_template_part( '404' );

?>