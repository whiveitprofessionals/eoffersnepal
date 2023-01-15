<?php

echo '<div class="widget widget_products_v2' . ( !$mobile_view ? ' mobile_view' : '' ) . '">';
if( !empty( $title ) ) {
  echo '<h2>' . ts( $title ) . '</h2>';
}

echo '<ul class="list">';

foreach( products_custom( array( 'show' => ( !empty( $type ) ? $type : '' ), 'orderby' => ( !empty( $order ) ? $order : '' ), 'max' => ( !empty( $limit ) ? $limit : 10 ) ) ) as $item ) {
  echo '<li><a href="' . $item->link . '"><img src="' . product_avatar( $item->image ) . '" alt="" />  <span>' . $item->title . '</span></a></li>';
}
echo '</ul>
</div>';