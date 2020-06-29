<?php
// Exit if accessed directly
! defined( 'YITH_POS' ) && exit();

$receipts = array(

	'receipts' => array(
		'receipts_list' => array(
			'type' => 'post_type',
			'post_type' => YITH_POS_Post_Types::$receipt
		),
	)
);

return apply_filters( 'yith_pos_panel_receipts_tab', $receipts );