<?php

/**
 * Class FUE_Addon_Comming_Soon_Pro
 */
class FUE_Addon_Coming_Soon_Pro {

	/**
	 * class constructor
	 */
	public function __construct() {

		// manual emails
		add_action( 'fue_manual_types', array($this, 'manual_types') );
		add_filter( 'fue_manual_email_recipients', array($this, 'manual_email_recipients'), 10, 2 );
		add_action( 'fue_manual_js', array($this, 'manual_form_script') );

	}

	/**
	 * Check if the Coming Soon Pro plugin is installed and active
	 * @return bool
	 */
	public static function is_installed() {
		return class_exists('SEED_CSPV4');
	}

	/**
	 * CSP option for manual emails
	 */
	public function manual_types() {
		?><option value="csp_subscribers"><?php esc_html_e('Coming Soon Pro Subscribers', 'follow_up_emails'); ?></option><?php
	}

	/**
	 * Get all CSP subscribers
	 *
	 * @param array $recipients
	 * @param array $post
	 *
	 * @return array
	 */
	public function manual_email_recipients( $recipients, $post ) {
		global $wpdb;

		if ( $post['send_type'] == 'csp_subscribers' ) {

			$subscribers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}csp3_subscribers");

			foreach ( $subscribers as $subscriber ) {
				$key = '0|'. $subscriber->email .'|'. $subscriber->fname .' '. $subscriber->lname;
				$recipients[$key] = array(0, $subscriber->email, $subscriber->fname .' '. $subscriber->lname);
			}
		}

		return $recipients;
	}


}

if ( FUE_Addon_Coming_Soon_Pro::is_installed() )
	new FUE_Addon_Coming_Soon_Pro();
