<?php

// Exit if accessed directly
! defined( 'YITH_WCPB' )  && exit();

return array(
	'premium' => array(
		'landing' => array(
			'type' => 'custom_tab',
			'action' => 'yith_wcpb_premium_tab',
			'hide_sidebar' => true,
		)
	)
);