<?php

if( \user\main::logout() )

  echo '<div style="text-align: center; margin-top: 20px;">

  <h2>' . t( 'msg_loggiout', "Logging out ..." ) . '</h2>';
  echo '<meta http-equiv="refresh" content="1; url=index.php">

  </div>';