<?php

/** HEADER **/

  header('Content-Type: application/xml');

/** ----- **/

$categories = array();

$categories['max'] = 100; // max should be defined

if( isset( $_GET['cat'] ) ) {

  $categories['categories'] = $_GET['cat'];

} else if( isset( $_GET['store'] ) ) {

  $categories['store'] = $_GET['store'];

}

if( isset( $_GET['show'] ) ) {
  $categories['show'] = $_GET['show'] . ',visible';
}

if( isset( $_GET['orderby'] ) ) {
  $categories['orderby'] = $_GET['orderby'];
}

echo '<?xml version="1.0" encoding="UTF-8" ?>

<rss version="2.0">

    <channel>

    <title>' . \query\main::get_option( 'sitename' ) . ' Coupons</title>
    <link>' . $GLOBALS['siteURL'] . '</link>
    <description>List of coupons</description>
    <language>en-us</language>';

    foreach( \query\main::while_items( $categories ) as $item ) {

      echo '
        <item>
            <title>' . $item->title . '</title>
            <link>' . $item->link . '</link>
            <description><![CDATA[' . $item->description . ']]></description>
            <pubDate>' . date( 'r', strtotime( $item->date ) ) . '</pubDate>
            <guid>' . $item->link . '</guid>
        </item>
      ';

      }

    echo '</channel>
</rss>';

?>