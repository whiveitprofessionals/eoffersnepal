<?php

echo '<div class="widget widget_contact' . ( !$mobile_view ? ' mobile_view' : '' ) . '" id="widget_contact' . $ID . '">';
if( !empty( $title ) ) {
  echo '<h2>' . ts( $title ) . '</h2>';
}

if( !empty( $content ) ) echo '<div class="text">' . ts( $content ) . '</div>';
echo contact_form( '_widget' . $ID );

echo '</div>';