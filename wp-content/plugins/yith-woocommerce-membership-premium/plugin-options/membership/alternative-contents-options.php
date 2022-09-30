<?php
// Exit if accessed directly
! defined( 'YITH_WCMBS' ) && exit();

return array(
	'membership-alternative-contents' => array(
		'membership-alternative-contents-list' => array(
			'type'      => 'post_type',
			'post_type' => YITH_WCMBS_Post_Types::$alternative_contents
		),
	)
);
