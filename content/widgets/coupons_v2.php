<?php

echo '<div class="widget widget_coupons_v2' . ( !$mobile_view ? ' mobile_view' : '' ) . '">';
if( !empty( $title ) ) {
  echo '<h2>' . ts( $title ) . '</h2>';
}

echo '<ul class="list">';

foreach( items_custom( array( 'show' => ( !empty( $type ) ? $type : '' ), 'orderby' => ( !empty( $order ) ? $order : '' ), 'max' => ( !empty( $limit ) ? $limit : 10 ) ) ) as $item ) {
  echo '<li><a href="' . $item->link . '"><img src="' . store_avatar( ( !empty( $item->image ) ? $item->image : $item->store_img ) ) . '" alt="" />  <span>' . $item->title . '</span></a></li>';
}
echo '</ul>
</div>';