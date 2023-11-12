<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'CORE_DIR', 'ai/class-vc-ai-modal-controller.php' );

/**
 * Class respond for a text generation with AI engine.
 *
 * @since 7.2
 */
class Vc_Ai_Content_Generator {

	/**
	 * AI API connector instance.
	 * @since 7.2
	 * @var Vc_Ai_Api_Connector
	 */
	public $api_connector;

	/**
	 * AI modal instance.
	 * @since 7.2
	 * @var Vc_Ai_Modal_Controller
	 */
	public $modal_ai;

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
		$this->modal_ai = new Vc_Ai_Modal_Controller();
	}

	/**
	 * Generate content with AI from initial data.
	 *
	 * @since 7.2
	 * @return string | WP_Error
	 */
	public function generate( $data ) {
		$data = $this->api_connector->convert_data_to_request_format( $data );

		if ( ! $this->is_valid_data( $data ) ) {
			return new WP_Error(
				'ai_error_invalid_user_data',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 618): invalid user data', 'js_composer' )
			);
		}

		if ( ! $this->check_required_fields( $data ) ) {
			return new WP_Error(
				'ai_error_invalid_user_data',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 619): not all required fields provided', 'js_composer' )
			);
		}

		$data = $this->edit_data_before_request( $data );

		$is_messaged_data = true;
		$response_data = $this->api_connector->get_api_response_data( $data, $this->api_url_part, $is_messaged_data );

		if ( is_wp_error( $response_data ) ) {
			return $response_data;
		}

		return $this->edit_data_after_response( $response_data );
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

		if ( empty( $data['contentType'] ) ) {
			return false;
		}

		$content_type_optionality_list = $this->modal_ai->get_content_type_form_fields_optionality();
		$content_type = $data['contentType'];

		if ( ! isset( $content_type_optionality_list[ $content_type ] ) ||
			! is_array( $content_type_optionality_list[ $content_type ] ) ) {

			return false;
		}

		$required_fields_list = $content_type_optionality_list[ $content_type ];

		foreach ( $required_fields_list as $required_field ) {
			if ( ! array_key_exists( $required_field, $data ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if data is valid then we send to AI API.
	 *
	 * @since 7.2
	 * @param mixed $data
	 * @return bool
	 */
	public function is_valid_data( $data ) {

		if ( isset( $data['wpb-ai-element-type'] ) && ! is_string( $data['wpb-ai-element-type'] ) ) {
			return false;
		}

		if ( isset( $data['contentType'] ) && is_string( $data['contentType'] ) &&
			! array_key_exists( $data['contentType'], $this->modal_ai->get_content_generate_variant() ) ) {

			return false;
		}

		if ( isset( $data['toneOfVoice'] ) && is_string( $data['toneOfVoice'] ) &&
			! array_key_exists( $data['toneOfVoice'], $this->modal_ai->get_ton_of_voice_list() ) ) {

			return false;
		}

		if ( isset( $data['length'] ) && is_string( $data['length'] ) &&
			! array_key_exists( $data['length'], $this->modal_ai->get_number_of_symbols_list( $data['wpb-ai-element-type'] ) ) ) {

			return false;
		}

		if ( isset( $data['language'] ) && is_string( $data['language'] ) &&
			! in_array( $data['language'], $this->modal_ai->get_languages_list() ) ) {

			return false;
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
			// we should cut the prompt for all data excerpt translated and improved to 200 words.
			$full_length_content_type = [ 'improve_existing', 'translate' ];
			if ( ! in_array( $data['contentType'], $full_length_content_type ) ) {

				$words = preg_split( '/\s+/', $data['prompt'] );
				if ( count( $words ) > 2000 ) {
					$truncated_words = array_slice( $words, 0, 2000 );
					$data['prompt'] = implode( ' ', $truncated_words );
				}
			}
		}

		return $data;
	}

	/**
	 * In some cases we should adjust data after response from API.
	 *
	 * @since 7.2
	 * @param string $response_data
	 * @return string
	 */
	public function edit_data_after_response( $response_data ) {
		// check a regular expression pattern to match quoted strings.
		if ( preg_match( '/^"(.*)"$/', $response_data, $matches ) ) {
			// If a match is found, extract the content between the quotes and return it.
			$response_data = $matches[1];
		}

		return $response_data;
	}
}
