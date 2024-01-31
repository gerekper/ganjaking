<?php

namespace ElementPack\Modules\WebhookForm;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) {
	exit;
}

class Module extends Element_Pack_Module_Base {

	public static $api_settings;

	public function get_name() {
		return 'webhook-form';
	}

	public function get_widgets() {

		$widgets = ['Webhook_Form'];

		return $widgets;
	}

	public function __construct() {
		parent::__construct();
		add_action('wp_ajax_nopriv_submit_webhook_form', array($this, 'submit_webhook_form'));
		add_action('wp_ajax_submit_webhook_form', array($this, 'submit_webhook_form'));
		$this::$api_settings = get_option('element_pack_api_settings');
	}

	public function is_valid_captcha() {

		$ep_api_settings = $this::$api_settings;

		if (isset($_POST['g-recaptcha-response']) and !empty($ep_api_settings['recaptcha_secret_key'])) {
			$request  = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $ep_api_settings['recaptcha_secret_key'] . '&response=' . esc_textarea($_POST["g-recaptcha-response"]) . '&remoteip=' . $_SERVER["REMOTE_ADDR"]);
			$response = wp_remote_retrieve_body($request);

			$result = json_decode($response, TRUE);

			if (isset($result['success']) && $result['success'] == 1) {
				// Captcha ok
				return true;
			} else {
				// Captcha failed;
				return false;
			}
		}

		return false;
	}

	public function submit_webhook_form() {

		if (!wp_verify_nonce($_POST['nonce'], 'element-pack-site')) {
			echo json_encode(array(
				'success' => false,
				'message' => esc_html__('Nonce verification failed', 'bdthemes-element-pack'),
			));
			wp_die();
		}

		$post_id         = sanitize_text_field($_REQUEST['page_id']);
		$widget_id       = sanitize_text_field($_REQUEST['widget_id']);
		$transient_key   = 'bdt_ep_webhook_form_data_' . $widget_id;
		$transient_value = get_transient($transient_key);
		$ep_api_settings = $this::$api_settings;


		$form_data = array();

		foreach ($_POST as $field => $value) {
			if (is_email($value)) {
				$value = sanitize_email($value);
			} else {
				$value = sanitize_textarea_field($value);
			}

			$form_data[$field] = strip_tags($value);
		}

		$success_text = isset($form_data['success_text']) & !empty($form_data['success_text']) ? esc_html($form_data['success_text']) : esc_html__('Your data has been sent successfully.', 'bdthemes-element-pack');

		unset($form_data['action']);
		unset($form_data['nonce']);

		if (isset($form_data['widget_id'])) {
			unset($form_data['widget_id']);
		}

		$headers = array();

		if (!empty($transient_value['header'])) {
			$headers = array_merge($headers, $transient_value['header']);
		}

		if (!empty($transient_value['body'])) {
			$form_data = array_merge($form_data, $transient_value['body']);
		}

		$hook_url = $transient_value['webhook_url'];

		if (empty($hook_url)) {
			echo json_encode(array(
				'success' => false,
				'message' => esc_html__('Webhook URL empty.', 'bdthemes-element-pack'),
			));
			wp_die();
		}

		/** Recaptcha*/


		$widget_settings = $this->get_widget_settings($post_id, $widget_id);

		if (isset($widget_settings['show_recaptcha']) && $widget_settings['show_recaptcha'] == 'yes') {
			if (!empty($ep_api_settings['recaptcha_site_key']) and !empty($ep_api_settings['recaptcha_secret_key'])) {
				if (!$this->is_valid_captcha()) {
					echo json_encode(array(
						'success' => false,
						'message' => esc_html__('Error in the reCaptcha.', 'bdthemes-element-pack'),
					));
					wp_die();
				}
			}
		}

		$updated_url = str_replace("&#038;", "&", $hook_url);

		$response = wp_remote_post($updated_url, array(
			'headers' => $headers,
			'body'    => $form_data,
		));

		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			echo json_encode(array(
				'success' => false,
				'message' => esc_html__($error_message, 'bdthemes-element-pack'),
			));
		} else {
			$body = wp_remote_retrieve_body($response);
			$body = json_decode($body, true);

			if (isset($body['success']) && !$body['success']) {
				echo json_encode(array(
					'success' => false,
					'message' => isset($body['data']['message']) ? esc_html($body['data']['message']) : esc_html__('Error in the response body.', 'bdthemes-element-pack'),
				));
			} else {
				echo json_encode(array(
					'success' => true,
					'message' => $success_text,
				));
			}
		}

		wp_die();
	}
}
