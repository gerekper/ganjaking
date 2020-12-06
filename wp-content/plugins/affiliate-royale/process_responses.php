<?php
error_reporting(E_ALL);

if(!defined('STDIN'))
  die("You're unauthorized to view this page.");

echo "\nAffiliate Royale - Silent Posts Delay Script\n\n";

chdir(dirname(__FILE__));

require_once('../../../wp-load.php');

require_once(ABSPATH . WPINC . '/user.php');

$check_time = time() - 15*60; // Make sure its been in the queue for at least 15 minutes

$responses = WafpResponse::get_all_by_status_and_ts( 'pending', $check_time );

if(count($responses) > 0){
  foreach($responses as $resp) {
    echo "*** Processing Gateway Response id: {$resp->id} / type: {$resp->type} / ts: {$resp->created_ts}\n";
    // Artificially set $_POST
    $_POST = unserialize($resp->response);

    if( $resp->type == 'Authorize.net' ) {
      WafpAuthorizeController::_process_message();
      WafpResponse::update_status( $resp->id, 'complete' );
    }
  }
}
else
  echo "No Silent Posts to Process.\n\n\n";

exit(0);
