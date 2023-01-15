<?php

if( !ab_to( array( 'reports' => 'view' ) ) ) die;

$view = isset( $_GET['view'] ) ? $_GET['view'] : 'days';

echo '<div class="title">

<h2>' . t( 'clicksr_title', "Clicks Report" ) . '</h2>';

$subtitle = t( 'clicksr_subtitle' );

if( !empty( $subtitle ) ) {
    echo '<span>' . $subtitle . '</span>';
}

echo '</div>';

do_action( array( 'after_title_inner_page', 'after_title_reports_page', 'after_title_clicks_reports_page' ) );

echo '<div class="page-toolbar" id="report-date">

' . t( 'clicksr_greport', "Graph Report" ) . '

<form action="#" method="GET" autocomplete="off" style="float: right;">
<input type="hidden" name="route" value="clicks.php" />
<select name="view">
<option value="hours"' . ( $view == 'hours' ? ' selected' : '' ) . '>' . t( 'hours', "Hours" ) . '</option>
<option value="days"' . ( $view == 'days' ? ' selected' : '' ) . '>' . t( 'days', "Days" ) . '</option>
<option value="weeks"' . ( $view == 'weeks' ? ' selected' : '' ) . '>' . t( 'weeks', "Weeks" ) . '</option>
<option value="months"' . ( $view == 'months' ? ' selected' : '' ) . '>' . t( 'months', "Months" ) . '</option>
</select>
<button class="btn">' . t( 'view', "View" ) . '</button>
</form>

</div>';

?>

<script type="text/javascript">

google.load("visualization", "1.1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

function drawChart() {

$('#report-date select').on('change', function() {

var view = $(this).val();

var jsonData = $.ajax({url: "?ajax=click_report_json.php&view=" + view, dataType:"json", async: false}).responseText;

var data = new google.visualization.DataTable(jsonData);

var options = {

    legend: { position: 'none' },

    colors: ['green', '#003366', '#990099'],
    backgroundColor: {stroke: 'none', fill: 'none'},
    chartArea:{width: '90%', height: 'auto', top: 20}

};

var chart = new google.visualization.ColumnChart(document.getElementById('columnchart_material'));

chart.draw(data, options);

}).change();

}

</script>

<div id="columnchart_material" style="width: 100%; margin:0 auto;"></div>

<?php

echo '<div class="page-toolbar">

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="clicks.php" />

' . t( 'order_by', "Order by" ) . ':
<select name="orderby">';
foreach( array( 'date' => t( 'order_date', "Date" ), 'date desc' => t( 'order_date_desc', "Date DESC" ) ) as $k => $v )echo '<option value="' . $k . '"' . (isset( $_GET['orderby'] ) && urldecode( $_GET['orderby'] ) == $k || $k == 'date desc' ? ' selected' : '') . '>' . $v . '</option>';
echo '</select>

<button class="btn">' . t( 'view', "View" ) . '</button>

</form>

<form action="#" method="GET" autocomplete="off">
<input type="hidden" name="route" value="clicks.php" />';

if( isset( $_GET['orderby'] ) ) {
echo '<input type="hidden" name="orderby" value="' . esc_html( $_GET['orderby'] ) . '" />';
}

echo '<input type="search" name="search" value="' . (isset( $_GET['search'] ) ? esc_html( $_GET['search'] ) : '') . '" placeholder="' . t( 'clicksr_search_input', "Search in report" ) . '" />
<button class="btn">' . t( 'search', "Search" ) . '</button>
</form>

</div>';

$p = admin\admin_query::have_clicks( $options = array( 'per_page' => 10, 'store' => (isset( $_GET['store'] ) ? $_GET['store'] : ''), 'coupon' => (isset( $_GET['coupon'] ) ? $_GET['coupon'] : ''), 'product' => (isset( $_GET['product'] ) ? $_GET['product'] : ''), 'search' => (isset( $_GET['search'] ) ? urldecode( $_GET['search'] ) : '') ) );

echo '<div class="results">' . ( (int) $p['results'] === 1 ? sprintf( t( 'result', "<b>%s</b> result" ), $p['results'] ) : sprintf( t( 'results', "<b>%s</b> results" ), $p['results'] ) );
if( !empty( $_GET['coupon'] ) || !empty( $_GET['product'] ) || !empty( $_GET['search'] ) ) echo ' / <a href="?route=clicks.php&amp;action=list">' . t( 'reset_view', "Reset view" ) . '</a>';
echo '</div>';

if( $p['results'] ) {

echo '<ul class="elements-list">';

$ab_edtu    = ab_to( array( 'users' => 'edit' ) );
$ab_edts    = ab_to( array( 'stores' => 'edit' ) );

foreach( admin\admin_query::while_clicks( array_merge( array( 'page' => $p['page'], 'orderby' => (isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date desc') ), $options ) ) as $item ) {

    $links = array();

    if( !empty( $item->user ) && $ab_edtu ) {
        $links['edit_user'] = '<a href="?route=users.php&amp;action=edit&amp;id=' . $item->user . '">' . t( 'clicksr_edit_user', "Edit User" ) . '</a>';
    }
    if( $ab_edts ) $links['edit_store'] = '<a href="?route=stores.php&amp;action=edit&amp;id=' . $item->storeID . '">' . t( 'clicksr_edit_store', "Edit Store" ) . '</a>';
    if( $GLOBALS['me']->is_admin ) $links['ban_ip'] = '<a href="?route=banned.php&amp;action=add&amp;ip=' . $item->IP . '">' . t( 'bann_ip', "Ban IP?" ) . '</a>';

    echo get_list_type( 'click', $item, $links );

}

echo '</ul>';

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

} else echo '<div class="a-alert">' . t( 'no_clicks_yet', "No clicks yet." ) . '</div>';