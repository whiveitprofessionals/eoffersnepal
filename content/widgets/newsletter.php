<?php

echo '<div class="widget widget_newsletter' . ( !$mobile_view ? ' mobile_view' : '' ) . '" id="widget_newsletter_' . $ID . '">';
if( !empty( $title ) ) {
  echo '<h2>' . ts( $title ) . '</h2>';
}

if( !empty( $content ) ) echo '<div class="text">' . ts( $content ) . '</div>';
echo newsletter_form( '_widget' . $ID );

echo '</div>';