<?php

switch( $_GET['action'] ) {

/** LIST OF NEWS */

default:

echo '<div class="title">

<h2>' . t( 'news_title', "News & Tutorials" ) . '</h2>';

$subtitle = t( 'news_subtitle' );

if( !empty( $subtitle ) ) {
  echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_news_page', 'after_title_list_news_page' ) );

if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['csrf'] ) && check_csrf( $_POST['csrf'], 'news_csrf' ) ) {

if( isset( $_POST['delete'] ) ) {

  if( isset( $_POST['id'] ) )
  if( admin\actions::delete_news( array_keys( $_POST['id'] ) ) )
  echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
  else
  echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

} else if( isset( $_GET['action'] ) && isset( $_GET['token'] ) && check_csrf( $_GET['token'], 'news_csrf' ) ) {

if( $_GET['action'] == 'delete' ) {

  if( isset( $_GET['id'] ) )
  if( admin\actions::delete_news( $_GET['id'] ) )
  echo '<div class="a-success">' . t( 'msg_deleted', "Deleted!" ) . '</div>';
  else
  echo '<div class="a-error">' . t( 'msg_error', "Error!" ) . '</div>';

}

}

$csrf = $_SESSION['news_csrf'] = \site\utils::str_random(10);

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="news.php" />
<input type="hidden" name="action" value="list" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || !isset( $_GET['orderby'] ) && $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>';

if( isset( $_GET['search'] ) ) {
echo '<input type="hidden" name="search" value="' . esc_html( $_GET['search'] ) . '" />';
}

echo ' <button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="news.php" />
<input type="hidden" name="action" value="list" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'news_search_input', "Search in news/tutorials" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$p = admin\admin_query::have_news( $options = array( 'per_page' => 10, 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( !empty( $_GET['search'] ) ) echo ' / <a href="?route=news.php&amp;action=list">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<form action="?route=news.php&amp;action=list" method="POST">

<ul class="elements-list">

<li class="head"><input type="checkbox" id="selectall" data-checkall /> <label for="selectall"><span></span> ' . t( 'name', "Name" ) . '</label></li>

<div class="bulk_options">
  <button class="btn" name="delete" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete_all', "Delete All" ) . '</button>
</div>';

foreach( admin\admin_query::while_news( array_merge( array( 'page' => $p['page'], 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

  echo '<li>
  <div>

  <input type="checkbox" name="id[' . $item->ID . ']" id="id[' . $item->ID . ']" /> <label for="id[' . $item->ID . ']"><span></span></label>
  <div class="info-div"><h2>' . $item->title . '</h2></div>

  </div>

  <div style="clear:both;"></div>

  <div class="options">
  <a href="' . $item->url . '" target="_blank">' . t( 'news_read_view', "Read/View" ) . '</a>';
  if( $GLOBALS['me']->is_admin ) echo '<a href="?route=news.php&amp;action=delete&amp;id=' . $item->ID . '&amp;token=' . $csrf . '" data-delete-msg="' . t( 'delete_msg', "Are you sure that you want to delete this?" ) . '">' . t( 'delete', "Delete" ) . '</a>';

  echo '</div>
  </li>';

}

echo '</ul>

<input type="hidden" name="csrf" value="' . $csrf . '" />

</form>';

if( isset( $p['prev_page'] ) || isset( $p['next_page'] ) ) {
  echo '<div class="pagination">';

  if( isset( $p['prev_page'] ) ) echo '<a href="' . $p['prev_page'] . '" class="btn">' . t( 'prev_page', "&larr; Prev" ) . '</a>';
  if( isset( $p['next_page'] ) ) echo '<a href="' . $p['next_page'] . '" class="btn">' . t( 'next_page', "Next &rarr;" ) . '</a>';

  if( $p['pages'] > 1 ) {
  echo '<div class="pag_goto">' . sprintf( t( 'pageofpages', "Page <b>%s</b> of <b>%s</b>" ), $page = $p['page'], $pages = $p['pages'] ) . '
  <form action="#" method="GET">';
  foreach( $_GET as $gk => $gv ) if( $gk !== 'page' ) echo '<input type="hidden" name="' . esc_html( $gk ) . '" value="' . esc_html( $gv ) . '" />';
  echo '<input type="number" name="page" min="1" max="' . $pages . '" size="5" value="' . $page . '" />
  <button class="btn">' . t( 'go', "Go" ) . '</button>
  </form>
  </div>';
  }

  echo '</div>';
}

} else echo '<div class="a-alert">' . t( 'no_news_yet', "There are no news or tutorials for the moment." ) . '</div>';

break;

}

?>