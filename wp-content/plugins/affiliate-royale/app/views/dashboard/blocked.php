<h2><?php _e('Your Affiliate Account Has Been Blocked', 'affiliate-royale', 'easy-affiliate'); ?></h2>

<?php
$blocked_message = $user->get_blocked_message();
if(!empty($blocked_message)) { echo "<p>{$blocked_message}</p>"; }

