<?php
// Exit if accessed directly
! defined( 'YITH_WCMBS' ) && exit();

return array(
	'membership-membership-plans' => array(
		'membership-membership-plans-list' => array(
			'type'      => 'post_type',
			'post_type' => YITH_WCMBS_Post_Types::$plan
		),
	)
);
