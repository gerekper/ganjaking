<?php

/**
 * Class FUE_Addon_Points_And_Rewards
 */
class FUE_Addon_Points_And_Rewards {

	/**
	 * class constructor
	 */
	public function __construct() {

		if (self::is_installed()) {
			add_filter( 'fue_email_types', array($this, 'register_email_type') );

			// reports
			add_filter( 'fue_trigger_str', array($this, 'trigger_string'), 10, 2 );

			add_action( 'fue_email_form_interval_meta', array($this, 'add_interval_meta') );
			add_action( 'fue_email_form_scripts', array($this, 'email_form_script') );

			// Send Manual
			add_action( 'fue_manual_types', array($this, 'manual_types') );
			add_action( 'fue_manual_type_actions', array($this, 'manual_type_actions') );
			add_action( 'fue_manual_js', array($this, 'manual_js') );
			add_filter( 'fue_manual_email_recipients', array($this, 'get_manual_email_recipients'), 10, 2 );

			add_action( 'wc_points_rewards_after_increase_points', array($this, 'after_points_increased'), 10, 5 );
			add_action( 'fue_email_variables_list', array($this, 'email_variables_list') );

			add_action( 'fue_before_variable_replacements', array($this, 'register_variable_replacements'), 11, 4 );

		}
	}

	/**
	 * Check if the plugin is active
	 *
	 * @return bool
	 */
	public static function is_installed() {
		return class_exists('WC_Points_Rewards');
	}

	/**
	 * Register custom email type
	 *
	 * @param array $types
	 * @return array
	 */
	public function register_email_type( $types ) {
		$triggers = array(
			'points_earned'             => __( 'After: Points Earned', 'wc_followup_emails' ),
			'points_greater_than'       => __( 'Earned Points per order is greater than', 'wc_followup_emails' ),
			'points_total_greater_than' => __( 'Total earned points is greater than', 'wc_followup_emails' ),
		);
		$props = array(
			'label'                 => __('Points and Rewards Emails', 'follow_up_emails'),
			'singular_label'        => __('Points and Rewards Email', 'follow_up_emails'),
			'triggers'              => $triggers,
			'durations'             => Follow_Up_Emails::$durations,
			'long_description'      => __('Points and Rewards emails will send to a user based upon the point earnings status you define when creating your emails.', 'follow_up_emails'),
			'short_description'     => __('Points and Rewards emails will send to a user based upon the point earnings status you define when creating your emails.', 'follow_up_emails')
		);
		$types[] = new FUE_Email_Type( 'points_and_rewards', $props );

		return $types;
	}

	/**
	 * Email form field
	 *
	 * @param FUE_Email $email
	 */
	public function add_interval_meta( $email ) {
		?>
		<span class="points-greater-than-meta" style="display:none;">
			<input type="text" style="width: 50px" name="meta[points_greater_than]" value="<?php if (isset($email->meta['points_greater_than'])) echo esc_attr( $email->meta['points_greater_than'] ); ?>" />
		</span>
		<span class="points-total-greater-than-meta" style="display:none;">
			<input type="text" style="width: 50px" name="meta[points_total_greater_than]" value="<?php if (isset($email->meta['points_total_greater_than'])) echo esc_attr( $email->meta['points_total_greater_than'] ); ?>" />
		</span>
		<?php
	}

	/**
	 * JS for email form
	 */
	public function email_form_script() {
		wp_enqueue_script( 'fue-form-points-and-rewards', FUE_TEMPLATES_URL .'/js/email-form-points-and-rewards.js' );
	}

	/**
	 * Additional recipient options for manual emails
	 */
	public function manual_types() {
		$options = array(
			'points_rewards_over'   => __('Points &amp; Rewards: Customers that have over N points', 'follow_up_emails'),
			'points_rewards_under'  => __('Points &amp; Rewards: Customers that have under N points', 'follow_up_emails')
		);

		include FUE_TEMPLATES_DIR .'/email-form/points-and-rewards/manual-email-types.php';

	}

	/**
	 * The actions for the additional manual email options
	 * @param FUE_Email $email
	 */
	public function manual_type_actions($email) {
		include FUE_TEMPLATES_DIR .'/email-form/points-and-rewards/manual-email-actions.php';
	}

	/**
	 * Inline JS for sending manual emails
	 */
	public function manual_js() {
		?>
		jQuery( '#send_type' ).on( 'change', function() {
			switch (jQuery(this).val()) {
				case "points_rewards_over":
				case "points_rewards_under":
					jQuery(".send-type-points_rewards_points").show();
					break;
			}
		} ).trigger( 'change' );
	<?php
	}

	/**
	 * Load all recipients matching the provided send type
	 *
	 * @param array $recipients
	 * @param array $post
	 *
	 * @return array
	 */
	public function get_manual_email_recipients( $recipients, $post ) {
		global $wc_points_rewards, $wpdb;

		$send_type  = $post['send_type'];

		if ( isset( $post['points_rewards_points'] ) && ( $send_type == 'points_rewards_over' || $send_type == 'points_rewards_under' ) ) {
			$points = absint( $post['points_rewards_points'] );

			if ( $send_type == 'points_rewards_over' ) {
				$users = $wpdb->get_results( $wpdb->prepare(
					"SELECT p.user_id, SUM(p.points_balance) AS points, u.user_email, u.display_name
				FROM {$wc_points_rewards->user_points_db_tablename} p, {$wpdb->users} u
				WHERE p.user_id = u.ID
				GROUP BY user_id
				HAVING points > %d",
					$points
				) );
			} else {
				$users = $wpdb->get_results( $wpdb->prepare(
					"SELECT p.user_id, SUM(p.points_balance) AS points, u.user_email, u.display_name
				FROM {$wc_points_rewards->user_points_db_tablename} p, {$wpdb->users} u
				WHERE p.user_id = u.ID
				GROUP BY user_id
				HAVING points < %d",
					$points
				) );
			}

			foreach ( $users as $user ) {
				$key    = $user->user_id .'|'. $user->user_email .'|'. $user->display_name;
				$value  = array( $user->user_id, $user->user_email, $user->display_name );

				if (! isset($recipients[$key]) ) {
					$recipients[$key] = $value;
				}
			}
		}

		return $recipients;
	}

	/**
	 * Action fired after points have been increased
	 *
	 * @param int       $user_id
	 * @param int       $points
	 * @param string    $event_type
	 * @param array     $data
	 * @param int       $order_id
	 */
	public function after_points_increased( $user_id, $points, $event_type, $data = null, $order_id = 0 ) {
		// P&R can sometimes pass null order_id, but this column is required by db structure, so we need to save it as 0.
		if( null === $order_id ) {
			$order_id = 0;
		}

		$emails = fue_get_emails( 'points_and_rewards', FUE_Email::STATUS_ACTIVE, array(
			'meta_query'    => array(
				array(
					'key'       => '_interval_type',
					'value'     => array( 'points_earned', 'points_greater_than', 'points_total_greater_than' ),
					'compare'   => 'IN'
				)
			)
		) );

		foreach ( $emails as $email ) {

			if ( $email->interval_type == 'points_greater_than' ) {
				$meta = maybe_unserialize( $email->meta );
				if ( $points < $meta['points_greater_than'] ) continue;
			}

			if ( $email->interval_type == 'points_total_greater_than' ) {
				$meta         = maybe_unserialize( $email->meta );
				$total_points = WC_Points_Rewards_Manager::get_users_points( $user_id );
				if ( $total_points < $meta['points_total_greater_than'] ) continue;
			}

			$insert = array(
				'send_on'       => $email->get_send_timestamp(),
				'email_id'      => $email->id,
				'user_id'       => $user_id,
				'order_id'      => $order_id,
				'is_cart'       => 0
			);

			$email_order_id = FUE_Sending_Scheduler::queue_email( $insert, $email );

			if ( !is_wp_error( $email_order_id ) ) {
				$data = array(
					'user_id'       => $user_id,
					'points'        => $points,
					'event_type'    => $event_type
				);
				update_option( 'fue_email_order_'. $email_order_id, $data );

				// Tell FUE that an email order has been created
				// to stop it from sending storewide emails
				if (! defined('FUE_ORDER_CREATED'))
					define('FUE_ORDER_CREATED', true);
			}

		}
	}

	/**
	 * Available variables
	 * @param FUE_Email $email
	 */
	public function email_variables_list( $email ) {
		global $woocommerce;

		if ( $email->type != 'points_and_rewards' ) {
			return;
		}
		?>
		<li class="var hideable var_points_and_rewards"><strong>{current_points}</strong> <img class="help_tip" title="<?php esc_attr_e('The current user\'s number of points', 'wc_followup_emails'); ?>" src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_points_and_rewards"><strong>{points_earned}</strong> <img class="help_tip" title="<?php esc_attr_e('The number of points earned', 'wc_followup_emails'); ?>" src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_points_and_rewards"><strong>{reward_event_description}</strong> <img class="help_tip" title="<?php esc_attr_e('The description of the action', 'wc_followup_emails'); ?>" src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<?php
	}

	/**
	 * Register variables for replacement
	 *
	 * @param FUE_Sending_Email_Variables   $var
	 * @param array                 $email_data
	 * @param FUE_Email             $email
	 * @param object                $queue_item
	 */
	public function register_variable_replacements( $var, $email_data, $email, $queue_item ) {
		$variables = array( 'current_points', 'points_earned', 'reward_event_description' );

		// use test data if the test flag is set
		if ( isset( $email_data['test'] ) && $email_data['test'] ) {
			$variables = $this->add_test_variable_replacements( $variables, $email_data, $email );
		} else {
			$variables = $this->add_variable_replacements( $variables, $email_data, $queue_item, $email );
		}

		$var->register( $variables );
	}

	/**
	 * Scan through the keys of $variables and apply the replacement if one is found
	 * @param array     $variables
	 * @param array     $email_data
	 * @param object    $queue_item
	 * @param FUE_Email $email
	 * @return array
	 */
	protected function add_variable_replacements( $variables, $email_data, $queue_item, $email ) {
		global $wc_points_rewards;

		if ( $email->type == 'manual' ) {
			$current_points = 0;

			if ( $email_data['user_id'] ) {
				$current_points = WC_Points_Rewards_Manager::get_users_points( $email_data['user_id'] );
			}

			$variables['current_points'] = $current_points;

			return $variables;
		}

		$event_data = get_option( 'fue_email_order_'. $queue_item->id, false );

		if (! $event_data ) {
			$event_data = array(
				'user_id'       => 0,
				'points'        => 0,
				'event_type'    => ''
			);
		}
		$current_points = 0;
		$points         = $event_data['points'];
		$description    = WC_Points_Rewards_Manager::event_type_description($event_data['event_type']);

		if ( $event_data['user_id'] ) {
			$current_points = WC_Points_Rewards_Manager::get_users_points( $event_data['user_id'] );
		}

		$variables['current_points']            = $current_points;
		$variables['points_earned']             = $points;
		$variables['reward_event_description']  = $description;

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
	protected function add_test_variable_replacements( $variables, $email_data, $email ) {
		$variables['current_points']            = 75;
		$variables['points_earned']             = 50;
		$variables['reward_event_description']  = 'Test event description';

		return $variables;
	}

	/**
	 * Format the trigger string that is displayed in the email reports
	 *
	 * @param string    $string
	 * @param FUE_Email $email
	 *
	 * @return string
	 */
	public function trigger_string( $string, $email ) {
		if ( $email->trigger == 'points_greater_than' ) {
			$email_type = $email->get_email_type();
			$meta = maybe_unserialize( $email->meta );
			$string = sprintf(
				__('%d %s %s %d'),
				$email->interval,
				Follow_Up_Emails::get_duration( $email->duration, $email->interval ),
				$email_type->get_trigger_name( $email->trigger ),
				$meta['points_greater_than']
			);
		}

		return $string;
	}

}

$GLOBALS['fue_points_and_rewards'] = new FUE_Addon_Points_And_Rewards();
