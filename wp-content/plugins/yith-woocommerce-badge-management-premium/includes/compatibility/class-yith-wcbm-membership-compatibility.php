<?php
/**
 * Membership Compatibility Class
 *
 * @auhtor  Arcifa Giuseppe <giuseppe.arcifa@yithemes.com>
 * @package YITH\BadgeManagementPremium\Compatibility
 * @since   2.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YITH_WCBM_Membership_Compatibility' ) ) {
	/**
	 * Dynamic Pricing Compatibility Class
	 */
	class YITH_WCBM_Membership_Compatibility {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_WCBM_Membership_Compatibility
		 */
		protected static $instance;

		/**
		 * Return the class instance
		 *
		 * @return YITH_WCBM_Membership_Compatibility
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCBM_Membership_Compatibility constructor.
		 */
		public function __construct() {
			add_filter( 'yith_plugin_fw_metabox_yith-wcbm-badge-rules_field_pre_get_value', array( $this, 'initialize_value_in_metabox_field' ), 10, 4 );

			add_action( 'save_post_' . YITH_WCBM_Post_Types_Premium::$badge_rule, array( $this, 'save_badge_rule' ) );

			add_filter( 'yith_wcbm_badge_rules_default_fields', array( $this, 'add_membership_integration_fields_in_badge_rules_panel' ) );
			add_filter( 'yith_wcbm_badge_rule_is_valid_for_user', array( $this, 'add_membership_check_in_badge_rule_valid' ), 10, 3 );
		}

		/**
		 * Handle Badge Rule saving
		 *
		 * @param int $post_id Badge Rule ID.
		 */
		public function save_badge_rule( $post_id ) {
			if ( isset( $_POST['yith_wcbm_badge_rule_security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['yith_wcbm_badge_rule_security'] ) ), 'yith_wcbm_save_badge_rule' )  ) {
				if ( empty( $_REQUEST['yith_wcbm_badge_rule']['_membership-plans'] ) ) {
					delete_post_meta( $post_id, '_membership-plans' );
				} else {
					update_post_meta( $post_id, '_membership-plans', $_REQUEST['yith_wcbm_badge_rule']['_membership-plans'] );
				}
			}
		}

		/**
		 * Filter the value initialized in metabox fields
		 *
		 * @param null   $value      The value.
		 * @param int    $post_id    The post ID.
		 * @param string $field_name The field name.
		 * @param array  $field      The field.
		 *
		 * @return mixed
		 */
		public function initialize_value_in_metabox_field( $value, $post_id, $field_name, $field ) {
			$prop = preg_replace( '/yith_wcbm_badge_rule|\[|\]/m', '', $field['name'] );

			if ( '_membership-plans' === $prop ) {
				$value = get_post_meta( $post_id, $prop, true );
			}

			return $value;
		}

		/**
		 * Add Membership integration fields to Badge Rule panel
		 *
		 * @param array $fields Default Badge rule fields.
		 *
		 * @return array
		 */
		public function add_membership_integration_fields_in_badge_rules_panel( $fields ) {

			$membership_plan_members = array(
				'type'     => 'ajax-posts',
				'label'    => __( 'Show badge to members of these plans', 'yith-woocommerce-badges-management' ),
				'desc'     => __( 'Choose which users can access to this products list.', 'yith-woocommerce-badges-management' ),
				'multiple' => true,
				'name'     => 'yith_wcbm_badge_rule[_membership-plans]',
				'data'     => array(
					'minimum_input_length' => '1',
					'placeholder'          => __( 'Search Membership plans...', 'text-domain' ),
					'post_type'            => 'yith-wcmbs-plan',
				),
				'deps'     => array(
					'id'    => '_show_badge_to',
					'value' => 'membership-plan-members',
					'type'  => 'hide',
				),
			);

			$fields['show_badge_to']['options']['membership-plan-members'] = __( 'Only members of a specific plan', 'yith-woocommerce-badges-management' );
			$fields['show_badge_to']['desc']                               = __( 'Choose if the badge will be shown to all users or only specific user roles or members of a plan', 'yith-woocommerce-badges-management' );
			$fields['membership-plans']                                    = $membership_plan_members;

			return $fields;
		}

		/**
		 * Add membership checks to rule validity
		 *
		 * @param bool                 $valid      Is Valid.
		 * @param int                  $user_id    User ID.
		 * @param YITH_WCBM_Badge_Rule $badge_rule Badge Rule.
		 *
		 * @return bool
		 */
		public function add_membership_check_in_badge_rule_valid( $valid, $user_id, $badge_rule ) {
			if ( 'membership-plan-members' === $badge_rule->get_show_badge_to() ) {
				$valid              = false;
				$permitted_plan_ids = $badge_rule->get_meta( '_membership-plans', true );
				$user_id            = ! $user_id ? get_current_user_id() : $user_id;
				if ( $permitted_plan_ids && $user_id && function_exists( 'yith_wcmbs_get_memberships' ) ) {
					$member        = YITH_WCMBS_Members()->get_member( get_current_user_id() );
					$user_plan_ids = $member->get_membership_plans() ?? array();
					$valid         = ! ! array_intersect( $user_plan_ids, $permitted_plan_ids );
				}
			}

			return $valid;
		}
	}
}
