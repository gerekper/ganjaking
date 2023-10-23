<?php
/**
 * Badge Rules table option page
 *
 * @package YITH\BagdeManagementPremium\PluginOptions
 */

$rule_table = array(
	'badge-rules' => array(
		'badge-rules' => array(
			'type'          => 'post_type',
			'post_type'     => YITH_WCBM_Post_Types_Premium::$badge_rule,
			'wp-list-style' => 'classic',
		),
	),
);


return apply_filters( 'yith_wcbm_badge_rules_options', $rule_table );
