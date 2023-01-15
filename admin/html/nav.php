<div class="top-nav">

    <ul class="left-top">
        <li><a href="../"> <?php echo t( 'top_menu', "MENU" ); ?></a></li>
        <?php $lmenu_links = array();
        $lmenu_links['visit_website'] = '<li><a href="../"> ' . sprintf( t( 'visit_site', "Visit %s" ), \query\main::get_option( 'sitename' ) ) . '</a></li>';
        $lmenu_links['couponscms'] = '<li><a href="//couponscms.com">CouponsCMS.com</a></li>';
        echo implode( "\n", value_with_filter( 'ap_menu_top_left_links', $lmenu_links ) ); ?>
    </ul>

    <ul class="right-top">
        <?php $rmenu_links = array();
        if( ab_to( array( 'mail' => 'send' ) ) ) {
            $rmenu_links['send_mail'] = '<li><a href="?route=users.php&amp;action=sendmail" class="sendmail"></a></li>';
        }
        echo implode( "\n", value_with_filter( 'ap_menu_top_right_links', $rmenu_links ) ); ?>
       <li><a href="<?php echo ( ab_to( array( 'users' => 'edit' ) ) ? '?route=users.php&amp;action=edit&amp;id=' . $GLOBALS['me']->ID : '#' ); ?>" class="avatar"><img src="<?php echo \query\main::user_avatar( $GLOBALS['me']->Avatar ); ?>" alt="" /> <?php echo $GLOBALS['me']->Name; ?></a>
       <div class="profhov"><a href="?route=logout.php"><?php echo t( 'logout', "Logout" ); ?></a></div></li>
    </ul>

</div>

<div class="main-nav ss-container">

<ul class="nav">

<?php

$nav = $GLOBALS['admin_main_class']->navigation();

uasort( $nav, function( $a, $b ) {
    if( (double) $a['position'] === (double) $b['position'] ) return 0;
    return ( (double) $a['position'] < (double) $b['position'] ? -1 : 1 );
} );

foreach( $nav as $key => $n ) {

    $nav_class = array();
    $show_subnav = $subnav_active = $plugin_active = false;

    if( isset( $n['class'] ) ) {
        $nav_class[] = esc_html( $n['class'] );
    }

    if( isset( $n['subnav'] ) ) {
        $nav_class['dropdown'] =  'drop-down';
    }

    if( isset( $n['selected'] ) && $n['selected'] ) {
        $nav_class['selected'] = 'secselected drop-down-i';
        $show_subnav = true;
    }

    if( !empty( $_GET['plugin'] ) && dirname( $_GET['plugin'] ) == $key ) {

        $nav_class[] = 'secselected drop-down-i';
        $show_subnav = $plugin_active = true;

    } else if( !empty( $_GET['route'] ) ) {

        $page_name = str_replace( '.php', '', $_GET['route'] );

        if( ( $page_name != 'link' && ( $page_name == $key || ( $subnav_active = ( isset( $n['other'] ) && is_array( $n['other'] ) && in_array( $page_name, $n['other'] ) ) ) ) ) ||
        ( $page_name == 'link' && isset( $_GET['main'] ) && $_GET['main'] == $key ) ) {
            $nav_class[] = 'secselected drop-down-i';
            $show_subnav = true;
        }

    }

    echo '<li' . ( !empty( $nav_class ) ? ' class="' . implode( ' ', $nav_class ) . '"' : '' ) . '>' . $n['name'];
    if( isset( $n['subnav'] ) ) {
      echo '<ul class="subnav"' . ( $show_subnav ? ' style="display:block;"' : '' ) . '>';
      foreach( $n['subnav'] as $subnav_key => $subnav_link ) {
        echo '<li' .    (
                        !$plugin_active &&
                        ( $show_subnav && !$subnav_active && isset( $_GET['action'] ) && $_GET['action'] == $subnav_key ) ||
                        ( $subnav_active && $page_name == $subnav_key ) ||
                        ( isset( $n['selected_subnav'] ) && $n['selected_subnav'] == $subnav_key )

                        ? ' class="secselected"' : '' ) . '>' . $subnav_link . '</li>';
      }
      echo '</ul>';
    }
    echo '</li>';

}

?>

</ul>

</div>