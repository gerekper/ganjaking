<?php
// Exit if accessed directly
! defined( 'YITH_POS' ) && exit();

$stores = array(

	'stores' => array(
		'stores_list' => array(
			'type' => 'post_type',
			'post_type' => YITH_POS_Post_Types::$store
		),
	)
);

return apply_filters( 'yith_pos_panel_stores_tab', $stores );