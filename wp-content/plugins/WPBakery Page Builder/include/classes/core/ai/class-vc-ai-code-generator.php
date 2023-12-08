<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class respond for a code generation with AI engine.
 *
 * @since 7.2
 */
class Vc_Ai_Code_Generator {

	/**
	 * AI API connector instance.
	 * @since 7.2
	 * @var Vc_Ai_Api_Connector
	 */
	public $api_connector;

	/**
	 * Part of url api that for a current generator.
	 * @since 7.2
	 * @var string
	 */
	public $api_url_part = 'content';

	/**
	 * Vc_Ai_Text_Generator constructor.
	 * @since 7.2
	 */
	public function __construct() {
		$this->api_connector = new Vc_Ai_Api_Connector();
	}

	/**
	 * Generate content with AI from initial data.
	 *
	 * @since 7.2
	 * @return string | WP_Error
	 */
	public function generate( $data ) {
		$data = $this->api_connector->convert_data_to_request_format( $data );
		if ( ! $this->check_required_fields( $data ) ) {
			return new WP_Error(
				'ai_error_invalid_user_data',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 619): not all required fields provided', 'js_composer' )
			);
		}

		$data = $this->edit_data_before_request( $data );

		$is_messaged_data = true;
		return $this->api_connector->get_api_response_data( $data, $this->api_url_part, $is_messaged_data );
	}

	/**
	 * Check if all form required fields are provided.
	 *
	 * @param array $data
	 * @return bool
	 */
	public function check_required_fields( $data ) {
		if ( ! is_array( $data ) ) {
			return false;
		}

		$required_fields_list = [
			'prompt',
		];

		foreach ( $required_fields_list as $required_field ) {
			if ( ! array_key_exists( $required_field, $data ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * For some userdata we should adjust data before send to API.
	 *
	 * @param array $data
	 * @return array
	 */
	public function edit_data_before_request( $data ) {
		if ( isset( $data['prompt'] ) ) {
			// we should cut the prompt for all data excerpt translated and improved.
			$words = preg_split( '/\s+/', $data['prompt'] );
			if ( count( $words ) > 2000 ) {
				$truncated_words = array_slice( $words, 0, 2000 );
				$data['prompt'] = implode( ' ', $truncated_words );
			}
		}

		return $data;
	}
}
