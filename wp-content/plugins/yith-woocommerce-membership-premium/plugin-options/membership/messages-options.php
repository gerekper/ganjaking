<?php
// Exit if accessed directly
! defined( 'YITH_WCMBS' ) && exit();

return array(
	'membership-messages' => array(
		'membership-messages-list' => array(
			'type'      => 'post_type',
			'post_type' => YITH_WCMBS_Post_Types::$thread
		),
	)
);
