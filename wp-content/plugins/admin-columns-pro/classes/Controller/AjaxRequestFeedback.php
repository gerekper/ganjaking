<?php

namespace ACP\Controller;

use AC\Ajax;
use AC\Registrable;

class AjaxRequestFeedback implements Registrable {

	/**
	 * @var string
	 */
	private $version;

	public function __construct( $version ) {
		$this->version = (string) $version;
	}

	public function register() {
		$this->get_ajax_handler()->register();
	}

	/**
	 * @return Ajax\Handler
	 */
	protected function get_ajax_handler() {
		$handler = new Ajax\Handler();
		$handler->set_action( 'acp-send-feedback' )
		        ->set_callback( [ $this, 'ajax_send_feedback' ] );

		return $handler;
	}

	public function ajax_send_feedback() {
		$this->get_ajax_handler()->verify_request();

		$email = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );

		if ( ! is_email( $email ) ) {
			wp_send_json_error( __( 'Please insert a valid email so we can reply to your feedback.', 'codepress-admin-columns' ) );
		}

		$feedback = filter_input( INPUT_POST, 'feedback', FILTER_SANITIZE_STRING );

		if ( empty( $feedback ) ) {
			wp_send_json_error( __( 'Your feedback form is empty.', 'codepress-admin-columns' ) );
		}

		$headers = [
			sprintf( 'From: <%s>', trim( $email ) ),
			'Content-Type: text/html',
		];

		wp_mail(
			acp_support_email(),
			sprintf( 'Beta Feedback on Admin Columns Pro %s', $this->version ),
			nl2br( $feedback ),
			$headers
		);

		wp_send_json_success( __( 'Thank you very much for your feedback!' ) );
	}

}