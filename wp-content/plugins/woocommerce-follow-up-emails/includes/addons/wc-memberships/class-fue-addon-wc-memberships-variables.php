<?php

class FUE_Addon_WC_Memberships_Variables {

	public function __construct() {
		add_action( 'fue_email_variables_list', array($this, 'email_variables_list') );
		add_action( 'fue_before_variable_replacements', array( $this, 'register_email_variable_replacements' ), 10, 4 );
	}

	/**
	 * List of available variables
	 * @param FUE_Email $email
	 */
	public function email_variables_list( $email ) {
		if ( $email->type != 'wc_memberships' ) {
			return;
		}
		include FUE_TEMPLATES_DIR .'/email-form/wc-memberships/variables.php';
	}

	/**
	 * Register additional variables to be replaced
	 *
	 * @param FUE_Sending_Email_Variables $var
	 * @param array                 $email_data
	 * @param FUE_Email             $email
	 * @param object                $queue_item
	 *
	 * @return void
	 */
	public function register_email_variable_replacements( $var, $email_data, $email, $queue_item = null ) {
		$variables = array(
			'membership_plan'           => '',
			'membership_plan_discounts' => '',
			'membership_renew_url'      => fue_replacement_url_var( '' ),
			'membership_end_date'       => '',
			'membership_start_date'     => ''
		);

		// use test data if the test flag is set
		$variables = ( isset( $email_data['test'] ) && $email_data['test'] )
			? $this->add_test_variable_replacements( $variables, $email_data, $email )
			: $this->add_variable_replacements( $variables, $email_data, $queue_item, $email );

		$var->register( $variables );
	}

	/**
	 * Scan through the keys of $variables and apply the replacement if one is found
	 * @param array                     $variables
	 * @param array                     $email_data
	 * @param FUE_Sending_Queue_Item    $queue_item
	 * @param FUE_Email                 $email
	 * @return array
	 */
	private function add_variable_replacements( $variables, $email_data, $queue_item, $email ) {
		$replacements   = $this->get_replacement_data( $email_data, $queue_item, $email );
		$variables      = $this->apply_replacements( $variables, $replacements );

		return $variables;
	}
	/**
	 * Add variable replacements for test emails
	 *
	 * @param array     $variables
	 * @param array     $email_data
	 * @param FUE_Email $email
	 *
	 * @return array
	 */
	private function add_test_variable_replacements( $variables, $email_data, $email ) {
		$replacements   = $this->get_test_replacement_data( $email_data, $email );
		$variables      = $this->apply_replacements( $variables, $replacements );
		
		return $variables;
	}
	/**
	 *
	 * @param array                     $email_data
	 * @param FUE_Sending_Queue_Item    $queue_item
	 * @param FUE_Email                 $email
	 * @return array
	 */
	private function get_replacement_data( $email_data, $queue_item, $email ) {
		$replacements = array();

		if ( empty( $queue_item->meta['membership_id'] ) ) {
			return $replacements;
		}

		$membership = wc_memberships_get_user_membership( $queue_item->meta['membership_id'] );
		if ( $membership ) {
			$replacements['membership_plan']        = get_the_title( $membership->get_plan_id() );
			$replacements['membership_renew_url']   = fue_replacement_url_var( $membership->get_renew_membership_url() );
			$replacements['membership_end_date']    = $membership->get_end_date( wc_date_format() );
			$replacements['membership_start_date']  = $membership->get_start_date( wc_date_format() );

			$rules = $membership->get_plan()->get_purchasing_discount_rules();

			ob_start();
			fue_get_template(
				'membership-plan-discounts.php',
				array('rules' => $rules),
				'follow-up-emails/email-variables/',
				FUE_TEMPLATES_DIR .'/email-variables/'
			);
			$membership_plan_discounts = ob_get_clean();

			$replacements['membership_plan_discounts'] = $membership_plan_discounts;
		}
		return $replacements;
	}
	/**
	 * Get replacement data for test emails
	 *
	 * @param array     $email_data
	 * @param FUE_Email $email
	 *
	 * @return array
	 */
	private function get_test_replacement_data( $email_data, $email ) {
		$replacements['membership_plan']          = 'Pro Plan';
		$replacements['membership_plan_discounts']= '';
		$replacements['membership_renew_url']     = fue_replacement_url_var( site_url() );
		$replacements['membership_end_date']      = date( wc_date_format(), current_time('timestamp') + ( 86400 * 7 ) );
		$replacements['membership_start_date']    = date( wc_date_format(), current_time('timestamp') );

		$plans = wc_memberships_get_membership_plans();
		$plan = array_pop( $plans );

		if ( $plan ) {
			$rules = $plan->get_purchasing_discount_rules();

			ob_start();
			fue_get_template(
				'membership-plan-discounts.php',
				array('rules' => $rules),
				'follow-up-emails/email-variables/',
				FUE_TEMPLATES_DIR .'/email-variables/'
			);
			$membership_plan_discounts = ob_get_clean();

			$replacements['membership_plan_discounts'] = $membership_plan_discounts;
		}

		return $replacements;
	}

	private function apply_replacements( $variables, $replacements ) {
		foreach ( array_keys( $variables ) as $key ) {
			if ( isset( $replacements[ $key ] ) ) {
				$variables[ $key ] = $replacements[ $key ];
			}
		}

		return $variables;
	}
}
