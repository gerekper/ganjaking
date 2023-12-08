<?php

namespace ElementPack\Modules\Mailchimp;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function __construct() {
		parent::__construct();

		add_action('wp_ajax_element_pack_mailchimp_subscribe', [$this, 'mailchimp_subscribe']);
		add_action('wp_ajax_nopriv_element_pack_mailchimp_subscribe', [$this, 'mailchimp_subscribe']);
	}

	public function get_name() {
		return 'mailchimp';
	}

	public function get_widgets() {

		$widgets = ['Mailchimp'];

		return $widgets;
	}

	/**
	 * subscribe mailchimp with api key
	 * @param  string $email        any valid email
	 * @param  string $status       subscribe or unsubscribe
	 * @param  array  $merge_fields First name and last name of subscriber
	 * @return [type]               [description]
	 */
	public function mailchimp_subscriber_status($email, $status, $merge_fields = array('FNAME' => '', 'LNAME' => '')) {

		$options = get_option('element_pack_api_settings');
		$list_id = (!empty($options['mailchimp_list_id'])) ? $options['mailchimp_list_id'] : ''; // Your list is here
		$api_key = (!empty($options['mailchimp_api_key'])) ? $options['mailchimp_api_key'] : ''; // Your mailchimp api key here

		$args = array(
			'method' => 'PUT',
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode('user:' . $api_key)
			),
			'body' => json_encode(array(
				'email_address' => $email,
				'status'        => $status,
				'merge_fields'  => $merge_fields
			))
		);
		$response = wp_remote_post('https://' . substr($api_key, strpos($api_key, '-') + 1) . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . md5(strtolower($email)), $args);

		$body = json_decode($response['body']);

		return $body;
	}


	public function mailchimp_subscribe() {

		$fname = (isset($_POST['fname']) && !empty($_POST['fname'])) ? sanitize_text_field($_POST['fname']) : '';
		$result  = $this->mailchimp_subscriber_status(sanitize_text_field($_POST['email']), 'subscribed', ['FNAME' => $fname, 'LNAME' => '']);

		if ($result->status == 400) {
			if (isset($result->detail) && !empty($result->detail)) {
				echo '<div class="bdt-text-warning">' . esc_html($result->detail) . '</div>';
			} else {
				echo '<div class="bdt-text-warning">' . esc_html_x('Your request could not be processed', 'Mailchimp String', 'bdthemes-element-pack') . '</div>';
			}
		} elseif ($result->status == 401) {
			echo '<div class="bdt-text-warning">' . esc_html_x('Error: You did not set the API keys or List ID in admin settings!', 'Mailchimp String', 'bdthemes-element-pack') . '</div>';
		} elseif ($result->status == 200 || $result->status == 'subscribed') {
			echo '<span bdt-icon="icon: check" class="bdt-icon"></span> ' . esc_html_x('Thank you, You have subscribed successfully', 'Mailchimp String', 'bdthemes-element-pack');
		} else {
			echo '<div class="bdt-text-danger">' . esc_html_x('An unexpected internal error has occurred. Please contact Support for more information.', 'Mailchimp String', 'bdthemes-element-pack') . '</div>';
		}
		die;
	}
}
