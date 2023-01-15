<?php

echo '<div class="widget widget_coupons' . ( !$mobile_view ? ' mobile_view' : '' ) . '">';
if( !empty( $title ) ) {
  echo '<h2>' . ts( $title ) . '</h2>';
}

echo '<ul class="list">';
foreach( items_custom( array( 'show' => ( !empty( $type ) ? $type : '' ), 'orderby' => ( !empty( $order ) ? $order : '' ), 'max' => ( !empty( $limit ) ? $limit : 10 ) ) ) as $item ) echo '<li><a href="' . $item->link . '">' . $item->title . '</a></li>';
echo '</ul>
</div>';