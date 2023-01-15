<?php

echo '<div class="widget widget_stores' . ( !$mobile_view ? ' mobile_view' : '' ) . '">';
if( !empty( $title ) ) {
  echo '<h2>' . ts( $title ) . '</h2>';
}

echo '<ul class="list">';
foreach( stores_custom( array( 'show' => ( !empty( $type ) ? $type : '' ), 'orderby' => ( !empty( $order ) ? $order : '' ), 'max' => ( !empty( $limit ) ? $limit : 10 ) ) ) as $item ) echo '<li><a href="' . $item->link . '">' . $item->name . '</a></li>';
echo '</ul>
</div>';