<?php

echo '<div class="widget widget_search' . ( !$mobile_view ? ' mobile_view' : '' ) . '">';
if( !empty( $title ) ) {
  echo '<h2>' . ts( $title ) . '</h2>';
}

echo '<form action="' . tlink( 'search' ) . '" method="GET">
<input type="text" name="s" />
<button>' . t( 'search', "Search" ) . '</button>
</form>
</div>';