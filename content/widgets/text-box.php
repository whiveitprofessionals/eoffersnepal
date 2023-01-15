<?php

echo '<div class="widget widget_text-box' . ( !$mobile_view ? ' mobile_view' : '' ) . '">';

if( !empty( $title ) ) {
  echo '<h2>' . ts( $title ) . '</h2>';
}

echo '<span>';

echo ts( $content );

echo '</span></div>';