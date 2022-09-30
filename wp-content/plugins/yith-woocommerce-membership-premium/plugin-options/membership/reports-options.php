<?php
// Exit if accessed directly
! defined( 'YITH_WCMBS' ) && exit();

return array(
	'membership-reports' => array(
		'membership-reports-tab' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_wcmbs_membership_report_tab',
		)
	)
);