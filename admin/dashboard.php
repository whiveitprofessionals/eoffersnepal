<?php

echo do_action( 'admin_dashboard_top' );

echo '<div class="page-toolbar">
' .  sprintf( t( 'first_msg_v', "CouponsCMS version <b>%s</b> runing theme <b>%s</b>." ), VERSION, \query\main::get_option( 'theme' ) ) . ' <span class="right-text">' . t( 'server_time', "Server time" ) . ': ' . date( 'Y.m.d, ' . ( \query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i' ) ) . '</span>';
echo '</div>';

echo '<div class="form-table">

<div class="el-two">';

$left_columns = $alerts = array();

if( ( $a_suggs = admin\admin_query::suggestions( array( 'show' => 'notread' ) ) ) > 0 && ab_to( array( 'suggestions' => 'view' ) ) ) $alerts['suggs'] = '<a href="?route=suggestions.php&amp;action=list&amp;view=notread">' . sprintf( t( 'alerts_suggestions', "%s unread suggestions" ), $a_suggs ) . '</a>';
if( ( $a_rews = \query\main::reviews( array( 'show' => 'notvalid' ) ) ) > 0 && ab_to( array( 'reviews' => 'view' ) ) )$alerts['rews'] = '<a href="?route=reviews.php&amp;action=list&amp;view=notvalid">' . sprintf( t( 'alerts_reviews', "%s unpublished reviews" ), $a_rews ) . '</a>';
if( ( $a_clmreqs = \query\main::rewards_reqs( array( 'show' => 'notvalid' ) ) ) > 0 && ab_to( array( 'claim_reqs' => 'view' ) ) )$alerts['clmreqs'] = '<a href="?route=rewards.php&amp;action=requests&amp;view=notvalid">' . sprintf( t( 'alerts_rewardreq', "%s unclaimed rewards" ), $a_clmreqs ) . '</a>';
if( ( $a_coupons = \query\main::coupons( array( 'show' => 'notvisible' ) ) ) > 0 && ab_to( array( 'coupons' => 'view' ) ) )$alerts['coupons'] = '<a href="?route=coupons.php&amp;action=list&amp;view=notvisible">' . sprintf( t( 'alerts_unpubcoupons', "%s unpublished coupons" ), $a_coupons ) . '</a>';
if( ( $a_products = \query\main::products( array( 'show' => 'notvisible' ) ) ) > 0 && ab_to( array( 'products' => 'view' ) ) )$alerts['products'] = '<a href="?route=products.php&amp;action=list&amp;view=notvisible">' . sprintf( t( 'alerts_unpubproducts', "%s unpublished products" ), $a_products ) . '</a>';
if( ( $a_stores = \query\main::stores( array( 'show' => 'notvisible' ) ) ) > 0 && ab_to( array( 'claim_reqs' => 'view' ) ) )$alerts['stores'] = '<a href="?route=stores.php&amp;action=list&amp;view=notvisible">' . sprintf( t( 'alerts_unpubstores', "%s unpublished stores" ), $a_stores ) . '</a>';
if( ( $a_payments = \query\payments::invoices( array( 'show' => 'undeliveredpayments' ) ) ) > 0 && ab_to( array( 'payments' => 'view' ) ) )$alerts['payments'] = '<a href="?route=payments.php&amp;action=list&amp;view=undeliveredpayments">' . sprintf( t( 'alerts_undelipay', "%s invoices undelivered" ), $a_payments ) . '</a>';

$alerts = value_with_filter( 'admin_alerts_list', $alerts );

if( !empty( $alerts ) ) {
    $alerts_markup = '<section class="el-row">
    <h2>' . t( 'news_alerts', "Alerts" ) . ' <a href="#" class="updown" data-set="alerts">' . ( isset( $_SESSION['ses_set']['alerts'] ) && ( $show_alerts = $_SESSION['ses_set']['alerts'] ) ? 'S' : 'R' ) . '</a></h2>
    <div class="el-row-body"' . ( !empty( $show_alerts ) ? ' style="display: none;"' : '' ) . '>
    <ul class="elements-list">';
    foreach( $alerts as $v ) {
      $alerts_markup .= '<li>' . $v . '</li>';
    }
    $alerts_markup .= '</ul>
    </div>
    </section>';
    $left_columns['alerts'] = $alerts_markup;
}

if( $GLOBALS['me']->is_admin ) {
    $left_columns['payments'] = '<section class="el-row">
    <h2>' . t( 'payments', "Payments" ) . ' <a href="#" class="updown" data-set="payments">' . ( isset( $_SESSION['ses_set']['payments'] ) && ( $show_payments = $_SESSION['ses_set']['payments'] ) ? 'S' : 'R' ) . '</a></h2>
    <div class="el-row-body"' . ( !empty( $show_payments ) ? ' style="display: none;"' : '' ) . '>
        <ul class="announce-box abdash">
            <li>' . t( 'today', "Today" ) . ':<b>' . sprintf( PRICE_FORMAT, \site\utils::money_format( ( (double) \query\payments::payments( array( 'show' => 'paid', 'date' => strtotime( 'today' ) ) )['sum'] ) ) ) . '</b></li>
            <li>' . t( 'yesterday', "Yesterday" ) . ':<b>' . sprintf( PRICE_FORMAT, \site\utils::money_format( ( (double) \query\payments::payments( array( 'show' => 'paid', 'date' => strtotime( '-2 days 00:00:00' ) . ',' . strtotime( 'today' ) ) )['sum'] ) ) ) . '</b></li>
            <li>' . t( 'this_week', "This week" ) . ':<b>' . sprintf( PRICE_FORMAT, \site\utils::money_format( ( (double) \query\payments::payments( array( 'show' => 'paid', 'date' => strtotime( 'last week 00:00:00' ) ) )['sum'] ) ) ) . '</b></li>
            <li>' . t( 'this_month', "This month" ) . ':<b>' . sprintf( PRICE_FORMAT, \site\utils::money_format( ( (double) \query\payments::payments( array( 'show' => 'paid', 'date' => strtotime( 'first day of this month 00:00:00' ) ) )['sum'] ) ) ) . '</b></li>
        </ul>
    </div>
    </section>';
}

$links_array = array();

if( ( $ab = ab_to( array( 'stores' => array( 'view', 'add', 'import', 'export' ) ) ) ) && list( $stores_view, $stores_add, $stores_import, $stores_export ) = $ab ) {
    $link_markup = '<li>
        <div class="info-div"><b>' . \query\main::stores() . '</b> ' . t( 'stores', "Stores" ) . '</div>
        <div class="options">';
        if( $stores_view )  $link_markup .= '<a href="?route=stores.php&amp;action=list">' . t( 'view', "View" ) . '</a>';
        if( $stores_add )   $link_markup .= '<a href="?route=stores.php&amp;action=add">' . t( 'add', "Add" ) . '</a>';
        if( $stores_import ) $link_markup .= '<a href="?route=stores.php&amp;action=import">' . t( 'import', "Import" ) . '</a>';
        if( $stores_export ) $link_markup .= '<a href="?route=stores.php&amp;action=export">' . t( 'export', "Export" ) . '</a>';
        $link_markup .= '</div>
    </li>';
    $links_array['stores'] = $link_markup;
}

if( ( $ab = ab_to( array( 'coupons' => array( 'view', 'add', 'import', 'export' ) ) ) ) && list( $coupons_view, $coupons_add, $coupons_import, $coupons_export ) = $ab ) {
    $link_markup = '<li>
        <div class="info-div"><b>' . \query\main::coupons() . '</b> ' . t( 'coupons', "Coupons" ) . '</div>
        <div class="options">';
        if( $coupons_view ) $link_markup .= '<a href="?route=coupons.php&amp;action=list">' . t( 'view', "View" ) . '</a>';
        if( $coupons_add )  $link_markup .= '<a href="?route=coupons.php&amp;action=add">' . t( 'add', "Add" ) . '</a>';
        if( $coupons_import ) $link_markup .= '<a href="?route=coupons.php&amp;action=import">' . t( 'import', "Import" ) . '</a>';
        if( $coupons_export ) $link_markup .= '<a href="?route=coupons.php&amp;action=export">' . t( 'export', "Export" ) . '</a>';
        $link_markup .= '</div>
    </li>';
    $links_array['coupons'] = $link_markup;
}

if( ( $ab = ab_to( array( 'products' => array( 'view', 'add', 'import', 'export' ) ) ) ) && list( $products_view, $products_add, $products_import, $products_export ) = $ab ) {
    $link_markup = '<li>
        <div class="info-div"><b>' . \query\main::products() . '</b> ' . t( 'products', "Products" ) . '</div>
        <div class="options">';
        if( $products_view )    $link_markup .= '<a href="?route=products.php&amp;action=list">' . t( 'view', "View" ) . '</a>';
        if( $products_add )     $link_markup .= '<a href="?route=products.php&amp;action=add">' . t( 'add', "Add" ) . '</a>';
        if( $products_import )  $link_markup .= '<a href="?route=products.php&amp;action=import">' . t( 'import', "Import" ) . '</a>';
        if( $products_export )  $link_markup .= '<a href="?route=products.php&amp;action=export">' . t( 'export', "Export" ) . '</a>';
        $link_markup .= '</div>
    </li>';
    $links_array['products'] = $link_markup;
}

if( ( $ab = ab_to( array( 'categories' => array( 'view', 'add' ) ) ) ) && list( $categories_view, $categories_add ) = $ab ) {
    $link_markup = '<li>
        <div class="info-div"><b>' . \query\main::categories() . '</b> ' . t( 'categories', "Categories" ) . '</div>
        <div class="options">';
        if( $categories_view )  $link_markup .= '<a href="?route=categories.php&amp;action=list">' . t( 'view', "View" ) . '</a>';
        if( $categories_add )   $link_markup .= '<a href="?route=categories.php&amp;action=add">' . t( 'add', "Add" ) . '</a>';
        $link_markup .= '</div>
    </li>';
    $links_array['categories'] = $link_markup;
}

if( ( $ab = ab_to( array( 'pages' => array( 'view', 'add' ) ) ) ) && list( $pages_view, $pages_add ) = $ab ) {
    $link_markup = '<li>
        <div class="info-div"><b>' . \query\main::pages() . '</b> ' . t( 'pages', "Pages" ) . '</div>
        <div class="options">';
        if( $pages_view )   $link_markup .= '<a href="?route=pages.php&amp;action=list">' . t( 'view', "View" ) . '</a>';
        if( $pages_add )    $link_markup .= '<a href="?route=pages.php&amp;action=add">' . t( 'add', "Add" ) . '</a>';
        $link_markup .= '</div>
    </li>';
    $links_array['pages'] = $link_markup;
}

if( ( $ab = ab_to( array( 'users' => array( 'view', 'add' ) ) ) ) && list( $users_view, $users_add ) = $ab ) {
    $link_markup = '<li>
        <div class="info-div"><b>' . \query\main::users() . '</b> ' . t( 'users', "Users" ) . '</div>
        <div class="options">';
        if( $users_view )   $link_markup .= '<a href="?route=users.php&amp;action=list">' . t( 'view', "View" ) . '</a>';
        if( $users_add )    $link_markup .= '<a href="?route=users.php&amp;action=add">' . t( 'add', "Add" ) . '</a>';
        $link_markup .= '</div>
    </li>';
    $links_array['users'] = $link_markup;
}

if( $GLOBALS['me']->is_admin ) {
    $links_array['sessions'] = '<li>
        <div class="info-div"><b>' . admin\admin_query::user_sessions() . '</b> ' . t( 'users_sessions', "Active Sessions" ) . '</div>
        <div class="options">
        <a href="?route=users.php&amp;action=sessions">' . t( 'view', "View" ) . '</a>
        </div>
    </li>';
}

if( ( $ab = ab_to( array( 'subscribers' => array( 'view', 'import', 'export' ) ) ) ) && list( $subscribers_view, $subscribers_import, $subscribers_export ) = $ab ) {
    $link_markup = '<li>
        <div class="info-div"><b>' . admin\admin_query::subscribers() . '</b> ' . t( 'users_subscribers', "Subscribers" ) . '</div>
        <div class="options">';
        if( $subscribers_view )     $link_markup .= '<a href="?route=users.php&amp;action=subscribers">' . t( 'view', "View" ) . '</a>';
        if( $subscribers_import )   $link_markup .= '<a href="?route=users.php&amp;action=importsub">' . t( 'import', "Import" ) . '</a>';
        if( $subscribers_export )   $link_markup .= '<a href="?route=users.php&amp;action=exportsub">' . t( 'export', "Export" ) . '</a>';
        $link_markup .= '</div>
    </li>';
    $links_array['subscribers'] = $link_markup;
}

if( ( $ab = ab_to( array( 'reviews' => array( 'view', 'add' ) ) ) ) && list( $reviews_view, $reviews_add ) = $ab ) {
    $link_markup = '<li>
    <div class="info-div"><b>' . \query\main::reviews() . '</b> ' . t( 'reviews', "Reviews" ) . '</div>
    <div class="options">';
    if( $reviews_view ) $link_markup .= '<a href="?route=reviews.php&amp;action=list">' . t( 'view', "View" ) . '</a>';
    if( $reviews_add )  $link_markup .= '<a href="?route=reviews.php&amp;action=add">' . t( 'add', "Add" ) . '</a>';
    $link_markup .= '</div>
    </li>';
    $links_array['reviews'] = $link_markup;
}

if( ab_to( array( 'suggestions' => 'view' ) ) ) {
    $links_array['suggestions'] = '<li>
        <div class="info-div"><b>' . admin\admin_query::suggestions() . '</b> ' . t( 'suggestions', "Suggestions" ) . '</div>
        <div class="options">
        <a href="?route=suggestions.php&amp;action=list">' . t( 'view', "View" ) . '</a>
        </div>
    </li>';
}

if( $GLOBALS['me']->is_admin ) {
    $links_array['plugins'] = '<li>
        <div class="info-div"><b>' . admin\admin_query::plugins() . '</b> ' . t( 'plugins', "Plugins" ) . '</div>
        <div class="options">
        <a href="?route=plugins.php&amp;action=list">' . t( 'view', "View" ) . '</a>
        <a href="?route=plugins.php&amp;action=install">' . t( 'plugins_install', "Install" ) . '</a>
        </div>
    </li>';
}

$links_markup = '<ul class="elements-list">';
$links_markup .= implode( '', value_with_filter( 'admin_dashboard_links', $links_array ) );
$links_markup .= '</ul>';

$left_columns['links'] = $links_markup;

echo implode( '', value_with_filter( 'admin_dashboard_left_column', $left_columns ) );

echo '</div>

<div class="el-two">';

$right_columns = array();

if( ab_to( array( 'chat' => 'view' ) ) ) {

$chat_markup = '<section class="el-row">

<h2>' . t( 'chat_title', "Chat / Notes" ) . ' <a href="#" class="updown" data-set="chat">' . ( isset( $_SESSION['ses_set']['chat'] ) && ( $show_chat = $_SESSION['ses_set']['chat'] ) ? 'S' : 'R' ) . '</a></h2>

<div class="el-row-body"' . ( !empty( $show_chat ) ? ' style="display: none;"' : '' ) . '>

<div id="post-chat">';

$chat_csrf = \site\utils::str_random(10);

if( ab_to( array( 'chat' => 'add' ) ) ) {
    $chat_markup .= '<form action="#" method="POST">
    <input type="text" name="text" value="" placeholder="' . t( 'chat_write_input', "Message here ..." ) . '" />
    <a href="#" class="btn">' . t( 'reload', 'Reload' ) . '</a>
    <input type="hidden" name="chat_csrf" value="' . $chat_csrf . '" />
    </form>';
}

$chat_markup .= '</div>';

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
if( isset( $_POST['chat_csrf'] ) && check_csrf( $_POST['chat_csrf'], 'chat_csrf' ) && isset( $_POST['text'] ) )
    admin\actions::post_chat_message( $_POST['text'] );
}

$_SESSION['chat_csrf'] = $chat_csrf;

$chat_markup .= '<ul class="elements-list" id="chat-msgs-list">';

if( $chatmsgs = admin\admin_query::chat_messages() > 0 )

foreach( admin\admin_query::while_chat_messages( array( 'max' => 5, 'orderby' => 'date DESC' ) ) as $item ) {
    $chat_markup .= '<li>
        <div style="display:table;">
        <img src="' . \query\main::user_avatar( $item->user_avatar ) . '" alt="" />
        <div class="info-div"><h2>' . $item->user_name . '
        <span class="fright date">' . date( 'Y.m.d, ' . (\query\main::get_option( 'hour_format' ) == 12 ? 'g:i A' : 'G:i'), strtotime( $item->date ) ) . '</span></h2>
        <div class="info-bar">' . $item->text . '</div>
        </div></div>
    </li>';
}

else {
    $chat_markup .= '<li>' . t( 'no_chat_yet', "There are no messages for the moment." ) . '</li>';
}

$chat_markup .= '</ul>';

if( $chatmsgs > 0 ) {
    $chat_markup .= '<div class="links">
    <a href="?route=chat.php">' .t( 'chat_list', "View full list &rarr;" ) . '</a>
    </div>';
}

$chat_markup .= '</div>
</section>';

$right_columns['chat'] = $chat_markup;

}

if( ab_to( array( 'reports' => 'view' ) ) ) {

$reports_markup = '<section class="el-row">

<h2>' . t( 'clicksr_greport', "Graph Report" ) . ' <a href="#" class="updown" data-set="graprap">' . ( isset( $_SESSION['ses_set']['graprap'] ) && ( $show_graprap = $_SESSION['ses_set']['graprap'] ) ? 'S' : 'R' ) . '</a></h2>

<div class="el-row-body" id="report-date"' . ( !empty( $show_graprap ) ? ' style="display: none;"' : '' ) . '>

<select>';
foreach( array( 'hours' => t( 'hours', "Hours" ), 'days' => t( 'days', "Days" ), 'weeks' => t( 'weeks', "Weeks" ), 'months' => t( 'months', "Months" ) ) as $k => $v ) {
    $reports_markup .= '<option value="' . $k . '"' .( isset( $_SESSION['ses_set']['lgcl'] ) && $_SESSION['ses_set']['lgcl'] == $k ? ' selected' : '' ) . '>' . $v . '</option>';
}
$reports_markup .= '</select>';

$reports_markup .= "<script type='text/javascript'>

google.load('visualization', '1.1', {packages:['corechart']});
google.setOnLoadCallback(drawChart);

function drawChart() {

$('#report-date select').on('change', function() {

    var view = $(this).val();

    var jsonData = $.ajax({url: '?ajax=click_report_json.php&view=' + view + '&limit=5', dataType:'json', async: false}).responseText;

    var data = new google.visualization.DataTable(jsonData);

    var options = {

      legend: { position: 'none' },

      colors: ['green', '#003366', '#990099'],
      backgroundColor: {stroke: 'none', fill: 'none'},
      chartArea:{width: '100%', height: 'auto', top: 20}

    };

    var chart = new google.visualization.ColumnChart(document.getElementById('columnchart_material'));

    chart.draw(data, options);

}).change();

}

</script>";

$reports_markup .= '<div id="columnchart_material" style="margin:0 auto;"></div>

<div class="links">
  <a href="?route=clicks.php">' . t( 'news_list', "View list &rarr;" ) . '</a>
</div>

</div>

</section>';

$right_columns['reports'] = $reports_markup;

}

$news_markup = '<section class="el-row">

<h2>' . t( 'news_title', "News & Tutorials" ) . ' <a href="#" class="updown" data-set="news">' . ( isset( $_SESSION['ses_set']['news'] ) && ( $show_news = $_SESSION['ses_set']['news'] ) ? 'S' : 'R' ) . '</a></h2>

<div class="el-row-body"' . ( !empty( $show_news ) ? ' style="display: none;"' : '' ) . '>

<ul class="elements-list">';

if( $news = admin\admin_query::news() > 0 )

foreach( admin\admin_query::while_news( array( 'max' => 7, 'orderby' => 'date DESC' ) ) as $item ) {
    $news_markup .= '<li><a href="' . $item->url . '" target="_blank">' . $item->title . '</a></li>';
}

else {
    $news_markup .= '<li>' . t( 'no_news_yet', "There are no news or tutorials for the moment." ) . '</li>';
}

$news_markup .= '</ul>';

if( $news > 0 ) {
    $news_markup .= '<div class="links">
    <a href="?route=news.php">' .t( 'news_list', "View list &rarr;" ) . '</a>
    </div>';
}

$news_markup .= '</div>

</section>';

$right_columns['news'] = $news_markup;

echo implode( '', value_with_filter( 'admin_dashboard_right_column', $right_columns ) );

echo '</div>

</div>

<div style="clear: both"></div>';

echo '<div class="idashbtm">' . sprintf( t( 'operatesfrom', "Operates from %s" ), date('m/d/Y', \query\main::get_option( 'siteinstalled' )) ) . ' / <a href="//couponscms.com" target="_blank" style="color: #000;">CouponsCMS.com</a></div>';

echo do_action( 'admin_dashboard_bottom' );