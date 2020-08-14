<?php
// Exit if accessed directly
! defined( 'YITH_POS' ) && exit();

$registers = array(

	'registers' => array(
		'registers_list' => array(
			'type' => 'post_type',
			'post_type' => YITH_POS_Post_Types::$register
		),
	)
);

return apply_filters( 'yith_pos_panel_registers_tab', $registers );