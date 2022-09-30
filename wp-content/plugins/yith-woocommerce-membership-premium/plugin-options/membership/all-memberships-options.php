<?php
// Exit if accessed directly
! defined( 'YITH_WCMBS' ) && exit();

return array(
	'membership-all-memberships' => array(
		'membership-all-memberships-list' => array(
			'type'      => 'post_type',
			'post_type' => YITH_WCMBS_Post_Types::$membership
		),
	)
);
