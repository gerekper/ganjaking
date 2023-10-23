<?php
/**
 * Badge Rule Types
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\PluginOptions
 */

global $post;
$rule_type = sanitize_text_field( wp_unslash( $_REQUEST['badge_rule_type'] ?? $_REQUEST['yith_wcbm_badge_rule']['_type'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$rule      = yith_wcbm_get_badge_rule();
if ( ! $rule && isset( $_GET['post'] ) && get_post_type( absint( $_GET['post'] ) ) === YITH_WCBM_Post_Types_Premium::$badge_rule ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$rule = yith_wcbm_get_badge_rule( absint( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}
$rule_type = $rule_type || ! $rule ? $rule_type : $rule->get_type( 'edit' );

return array(
	'label'    => __( 'Product Badge', 'yith-woocommerce-badges-management' ),
	'pages'    => YITH_WCBM_Post_Types_Premium::$badge_rule,
	'priority' => 'high',
	'class'    => yith_set_wrapper_class(),
	'tabs'     => array(
		'rule-options' => array(
			'label'  => '',
			'fields' => array_merge(
				array(
					'yith_wcbm_badge_rule_security' => array(
						'type' => 'hidden',
						'std'  => wp_create_nonce( 'yith_wcbm_save_badge_rule' ),
						'name' => 'yith_wcbm_badge_rule_security',
					),
				),
				yith_wcbm_get_badge_rule_type_fields( $rule_type )
			),
		),
	),
);
