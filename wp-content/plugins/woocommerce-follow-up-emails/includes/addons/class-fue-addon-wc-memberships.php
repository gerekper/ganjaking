<?php

/**
 * Class FUE_Addon_WC_Memberships
 */
class FUE_Addon_WC_Memberships {

	/**
	 * class constructor
	 */
	public function __construct() {
		// subscriptions integration
		add_filter( 'fue_email_types', array($this, 'register_email_type') );
		add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts'), 20 );

		// trigger fields
		add_filter( 'fue_email_form_trigger_fields', array($this, 'register_trigger_fields') );

		$this->include_files();
	}

	public function include_files() {
		include_once 'wc-memberships/class-fue-addon-wc-memberships-manual-emails.php';
		include_once 'wc-memberships/class-fue-addon-wc-memberships-scheduler.php';
		include_once 'wc-memberships/class-fue-addon-wc-memberships-variables.php';

		new FUE_Addon_WC_Memberships_Manual_Emails();
		new FUE_Addon_WC_Memberships_Variables();
		new FUE_Addon_WC_Memberships_Scheduler();
	}

	/**
	 * Check if the WC Memberships plugin is installed and active
	 * @return bool
	 */
	public static function is_installed() {
		return function_exists( 'init_woocommerce_memberships' ) || class_exists( 'WC_Memberships_Loader' );
	}

	/**
	 * Register custom email type
	 *
	 * @param array $types
	 * @return array
	 * @todo Descriptions
	 */
	public function register_email_type( $types ) {
		$triggers = array(
			'membership_before_expire'  => __('before membership expires', 'follow_up_emails')
		);
		foreach ( wc_memberships_get_user_membership_statuses() as $status => $labels ) {
			$triggers[ $status ] = sprintf( __('after membership status: %s', 'follow_up_emails'), $labels['label'] );
		}
		$props = array(
			'label'                 => __('WC Memberships', 'follow_up_emails'),
			'singular_label'        => __('WC Memberships', 'follow_up_emails'),
			'triggers'              => $triggers,
			'durations'             => Follow_Up_Emails::$durations,
			'long_description'      => __('', 'follow_up_emails'),
			'short_description'     => __('', 'follow_up_emails')
		);
		$types[] = new FUE_Email_Type( 'wc_memberships', $props );
		return $types;
	}

	/**
	 * Register styles and scripts used in rendering the Admin UI
	 */
	public function admin_scripts() {
		$screen = get_current_screen();
		if ( $screen->id == 'follow_up_email' ) {
			wp_enqueue_script(
				'fue-form-wc-memberships',
				FUE_TEMPLATES_URL .'/js/email-form-wc-memberships.js',
				array('jquery', 'fue-form-woocommerce'),
				FUE_VERSION
			);
		}
	}

	/**
	 * Add plan selector to the Trigger tab
	 *
	 * @param FUE_Email $email
	 */
	public function register_trigger_fields( $email ) {
		// load the categories
		if ( $email->type == 'wc_memberships' ) {
			include FUE_TEMPLATES_DIR .'/email-form/wc-memberships/plan-selector.php';
		}
	}

	/**
	 * Evaluate rules and return as a readable string
	 *
	 * @param WC_Memberships_Membership_Plan_Rule $rule
	 * @return string
	 */
	public static function discount_rule_string( $rule ) {
		$str = '-'; // unknown content type
		$content_type = $rule->get_content_type();

		// figure out the amount
		$amount = $rule->get_discount_type() == 'percentage'
			? $rule->get_discount_amount() .'%'
			: wc_price( $rule->get_discount_amount() );

		if ( $content_type == 'post_type' ) {
			$object_ids = $rule->get_object_ids();

			if ( empty( $object_ids ) ) {
				$str = sprintf( __('%s discount on all products', 'follow_up_emails'), $amount );
			} else {
				$object_names = array();
				foreach ( $object_ids as $object_id ) {
					$object_names[] = get_the_title( $object_id );
				}

				$str = ( count( $object_ids ) == 1 )
					? sprintf( __('%s discount on %s', 'follow_up_emails'), $amount, $object_names[0] )
					: sprintf(
						__('%s discount on the following products: %s', 'follow_up_emails'),
						$amount,
						implode( ', ', $object_names )
					);

			}
		} elseif ( $content_type == 'taxonomy' ) {
			$object_ids = $rule->get_object_ids();

			if ( empty( $object_ids ) ) {
				$str = sprintf( __('%s discount on all categories', 'follow_up_emails'), $amount );
			} else {
				$object_names = array();
				foreach ( $object_ids as $object_id ) {
					$object = get_term( $object_id, $rule->get_content_type_name() );
					$object_names[] = $object->name;
				}

				$str = ( count( $object_names ) == 1 )
					? sprintf( __('%s discount on the %s category', 'follow_up_emails'), $amount, $object_names[0] )
					: sprintf(
						__('%s discount on the following categories: %s', 'follow_up_emails'),
						$amount,
						implode( ', ', $object_names )
					);

			}
		}

		return apply_filters( 'fue_wc_memberships_discount_rule_string', $str, $rule );
	}

}

if ( FUE_Addon_WC_Memberships::is_installed() )
	new FUE_Addon_WC_Memberships();
