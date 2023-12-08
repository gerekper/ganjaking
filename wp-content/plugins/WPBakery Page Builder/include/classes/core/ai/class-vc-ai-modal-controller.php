<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class respond for AI modal interaction.
 *
 * @since 7.2
 */
class Vc_Ai_Modal_Controller {
	/**
	 * Credits limit per a site.
	 * we use it value only if we do not have response value.
	 * @since 7.2
	 * @var int
	 */
	public $credits_limit;

	/**
	 * Get AI modal data.
	 *
	 * @since 7.2
	 * @param array $data
	 * @return array
	 */
	public function get_modal_data( $data ) {

		$response['type'] = 'promo';

		$access_status = $this->get_access_ai_api_status( $data );
		if ( 'license_no_valid' === $access_status ) {
			$response['content'] = vc_get_template(
				'editors/popups/ai/promo.tpl.php',
				[
					'logo_template_path' => 'editors/popups/ai/happy-ai-logo.tpl.php',
					'message_template_path' => 'editors/popups/ai/message-modal-access-ai.tpl.php',
					'modal_controller' => $this,
				]
			);
		} elseif ( 'credits_expired' === $access_status ) {
			$response['content'] = vc_get_template(
				'editors/popups/ai/promo.tpl.php',
				[
					'logo_template_path' => 'editors/popups/ai/sad-ai-logo.tpl.php',
					'message_template_path' => 'editors/popups/ai/message-modal-more-credits.tpl.php',
					'modal_controller' => $this,
				]
			);
		} elseif ( is_wp_error( $access_status ) ) {
			$response['content'] = vc_get_template(
				'editors/popups/ai/promo.tpl.php',
				[
					'logo_template_path' => 'editors/popups/ai/sad-ai-logo.tpl.php',
					'message_template_path' => 'editors/popups/ai/message-modal-custom.tpl.php',
					'modal_controller' => $this,
					'error_message' => $access_status->get_error_message(),
				]
			);
		} else {
			$response['type'] = 'content';
			$response['content'] = $this->get_ai_form_template( $data );
		}

		return $response;
	}

	/**
	 * Get AI form template.
	 *
	 * @since 7.2
	 * @param array $data
	 * @return string|WP_Error
	 */
	public function get_ai_form_template( $data ) {
		if ( ! is_string( $data['ai_element_type'] ) || ! is_string( $data['ai_element_id'] ) ) {
			return new WP_Error(
				'ai_error_invalid_user_data',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 620): wrong api response format', 'js_composer' )
			);
		}

		$element_form_fields_template_path =
			$this->get_modal_template_path( $data['ai_element_type'], $data['ai_element_id'] );

		if ( is_wp_error( $element_form_fields_template_path ) ) {
			return $element_form_fields_template_path;
		}

		return vc_get_template(
			'editors/popups/ai/generate-form.tpl.php',
			[
				'element_form_fields_template_path' => $element_form_fields_template_path,
				'ai_element_type' => $data['ai_element_type'],
				'ai_element_id' => $data['ai_element_id'],
				'ai_modal_controller' => $this,
			]
		);
	}

	/**
	 * Get access status to AI API.
	 *
	 * @since 7.2
	 * @return string | WP_Error
	 */
	public function get_access_ai_api_status( $data ) {
		if ( ! vc_license()->isActivated() ) {
			return 'license_no_valid';
		}

		require_once vc_path_dir( 'CORE_DIR', 'ai/class-vc-ai-api-connector.php' );

		$api_connector = new Vc_Ai_Api_Connector();
		$data = $api_connector->add_license_key_to_request_data( $data );
		$response = $api_connector->get_api_response_data( $data, 'status', true );

		if ( ! is_wp_error( $response ) ) {
			return 'success';
		}

		if ( isset( $response->errors['ai_error_response'][0] ) ) {
			$message = $response->errors['ai_error_response'][0];
			if ( strpos( $message, 'license has expired' ) !== false ) {
				return 'license_no_valid';
			}

			if ( strpos( $message, 'reached your monthly limit' ) !== false ) {
				preg_match( '/free (\d+) WPBakery/', $message, $matches );

				if ( isset( $matches[1] ) ) {
					$this->credits_limit = (int) $matches[1];
				}

				return 'credits_expired';
			}
		}

		return $response;
	}

	/**
	 * Get AI modal template path.
	 *
	 * @since 7.2
	 * @param string $ai_element_type
	 * @param string $ai_element_id
	 * @return string | WP_Error
	 */
	public function get_modal_template_path( $ai_element_type, $ai_element_id ) {
		$template_list = $this->get_modal_type_of_template_dependency_list();
		if ( ! is_array( $template_list ) ) {
			return new WP_Error(
				'ai_error_type_of_template_dependency_list_data',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 621): template file missing', 'js_composer' )
			);
		}

		if ( ! array_key_exists( $ai_element_type, $template_list ) ) {
			return new WP_Error(
				'ai_error_type_of_template_dependency_list_do_not_has_template',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 622): template file missing', 'js_composer' )
			);
		}

		$template_path = $this->get_modal_template_path_from_list_dependency( $ai_element_type, $ai_element_id, $template_list );
		if ( ! file_exists( vc_template( $template_path ) ) ) {
			return new WP_Error(
				'ai_error_type_of_template_dependency_list_do_not_has_template',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 622): file template does not exist', 'js_composer' )
			);
		}

		return $template_path;
	}

	/**
	 * Get AI modal type of template dependency list.
	 *
	 * @since 7.2
	 * @return mixed
	 */
	public function get_modal_type_of_template_dependency_list() {
		$type_dependency = [
			'textarea_html' => 'editors/popups/ai/generate-text.php',
			'textarea' => 'editors/popups/ai/generate-text.php',
			'textarea_raw_html' => [
				'default' => 'editors/popups/ai/generate-text.php',
				'textarea_raw_html_javascript_code' => 'editors/popups/ai/generate-code.php',
			],
			'textfield' => 'editors/popups/ai/generate-text.php',
			'custom_css' => 'editors/popups/ai/generate-code.php',
			'custom_js' => 'editors/popups/ai/generate-code.php',
		];

		return apply_filters( 'wpb_ai_modal_type_dependency', $type_dependency );
	}

	/**
	 * Get modal template path from list dependency.
	 *
	 * @since 7.2
	 * @param string $ai_element_type
	 * @param string $ai_element_id
	 * @param array $template_list
	 * @return string
	 */
	public function get_modal_template_path_from_list_dependency( $ai_element_type, $ai_element_id, $template_list ) {
		if ( is_string( $template_list[ $ai_element_type ] ) ) {
			return $template_list[ $ai_element_type ];
		}

		if ( ! empty( $template_list[ $ai_element_type ][ $ai_element_id ] ) ) {
			$template_path = $template_list[ $ai_element_type ][ $ai_element_id ];
		} else if ( ! empty( $template_list[ $ai_element_type ]['default'] ) ) {
			$template_path = $template_list[ $ai_element_type ]['default'];
		} else {
			$template_path = '';
		}

		return $template_path;
	}

	/**
	 * Get tone of voice options.
	 *
	 * @since 7.2
	 * @return array
	 */
	public function get_ton_of_voice_list() {
		$list = apply_filters( 'wpb_ai_tone_of_voice_list', [
			'approachable' => esc_html__( 'Approachable', 'js_composer' ),
			'excited' => esc_html__( 'Excited', 'js_composer' ),
			'playful' => esc_html__( 'Playful', 'js_composer' ),
			'assertive' => esc_html__( 'Assertive', 'js_composer' ),
			'formal' => esc_html__( 'Formal', 'js_composer' ),
			'poetic' => esc_html__( 'Poetic', 'js_composer' ),
			'bold' => esc_html__( 'Bold', 'js_composer' ),
			'friendly' => esc_html__( 'Friendly', 'js_composer' ),
			'positive' => esc_html__( 'Positive', 'js_composer' ),
			'candid' => esc_html__( 'Candid', 'js_composer' ),
			'funny' => esc_html__( 'Funny', 'js_composer' ),
			'powerful' => esc_html__( 'Powerful', 'js_composer' ),
			'caring' => esc_html__( 'Caring', 'js_composer' ),
			'gentle' => esc_html__( 'Gentle', 'js_composer' ),
			'professional' => esc_html__( 'Professional', 'js_composer' ),
			'casual' => esc_html__( 'Casual', 'js_composer' ),
			'helpful' => esc_html__( 'Helpful', 'js_composer' ),
			'quirky' => esc_html__( 'Quirky', 'js_composer' ),
			'cheerful' => esc_html__( 'Cheerful', 'js_composer' ),
			'hopeful' => esc_html__( 'Hopeful', 'js_composer' ),
			'reassuring' => esc_html__( 'Reassuring', 'js_composer' ),
			'clear' => esc_html__( 'Clear', 'js_composer' ),
			'humorous' => esc_html__( 'Humorous', 'js_composer' ),
			'reflective' => esc_html__( 'Reflective', 'js_composer' ),
			'commanding' => esc_html__( 'Commanding', 'js_composer' ),
			'informal' => esc_html__( 'Informal', 'js_composer' ),
			'respectful' => esc_html__( 'Respectful', 'js_composer' ),
			'comprehensive' => esc_html__( 'Comprehensive', 'js_composer' ),
			'informative' => esc_html__( 'Informative', 'js_composer' ),
			'romantic' => esc_html__( 'Romantic', 'js_composer' ),
			'confident' => esc_html__( 'Confident', 'js_composer' ),
			'inspirational' => esc_html__( 'Inspirational', 'js_composer' ),
			'sarcastic' => esc_html__( 'Sarcastic', 'js_composer' ),
			'conversational' => esc_html__( 'Conversational', 'js_composer' ),
			'inspiring' => esc_html__( 'Inspiring', 'js_composer' ),
			'scientific' => esc_html__( 'Scientific', 'js_composer' ),
			'curious' => esc_html__( 'Curious', 'js_composer' ),
			'lively' => esc_html__( 'Lively', 'js_composer' ),
			'serious' => esc_html__( 'Serious', 'js_composer' ),
			'detailed' => esc_html__( 'Detailed', 'js_composer' ),
			'melancholic' => esc_html__( 'Melancholic', 'js_composer' ),
			'technical' => esc_html__( 'Technical', 'js_composer' ),
			'educational' => esc_html__( 'Educational', 'js_composer' ),
			'motivational' => esc_html__( 'Motivational', 'js_composer' ),
			'thought-provoking' => esc_html__( 'Thought-provoking', 'js_composer' ),
			'eloquent' => esc_html__( 'Eloquent', 'js_composer' ),
			'negative' => esc_html__( 'Negative', 'js_composer' ),
			'thoughtful' => esc_html__( 'Thoughtful', 'js_composer' ),
			'emotional' => esc_html__( 'Emotional', 'js_composer' ),
			'neutral' => esc_html__( 'Neutral', 'js_composer' ),
			'uplifting' => esc_html__( 'Uplifting', 'js_composer' ),
			'empathetic' => esc_html__( 'Empathetic', 'js_composer' ),
			'nostalgic' => esc_html__( 'Nostalgic', 'js_composer' ),
			'urgent' => esc_html__( 'Urgent', 'js_composer' ),
			'empowering' => esc_html__( 'Empowering', 'js_composer' ),
			'offbeat' => esc_html__( 'Offbeat', 'js_composer' ),
			'vibrant' => esc_html__( 'Vibrant', 'js_composer' ),
			'encouraging' => esc_html__( 'Encouraging', 'js_composer' ),
			'passionate' => esc_html__( 'Passionate', 'js_composer' ),
			'visionary' => esc_html__( 'Visionary', 'js_composer' ),
			'engaging' => esc_html__( 'Engaging', 'js_composer' ),
			'personal' => esc_html__( 'Personal', 'js_composer' ),
			'witty' => esc_html__( 'Witty', 'js_composer' ),
			'enthusiastic' => esc_html__( 'Enthusiastic', 'js_composer' ),
			'persuasive' => esc_html__( 'Persuasive', 'js_composer' ),
			'zealous' => esc_html__( 'Zealous', 'js_composer' ),
		]);

		$list = is_array( $list ) ? $list : [];
		asort( $list );

		return $list;
	}

	/**
	 * Get number of symbols options.
	 *
	 * @since 7.2
	 * @param string $ai_element_type
	 * @return array
	 */
	public function get_number_of_symbols_list( $ai_element_type ) {
		$list = apply_filters( 'wpb_ai_number_of_symbols_list', [
			'textarea_html' => [
				'[10,15]' => 'Title (up to 15 words)',
				'[15,25]' => 'Short description (up to 25 words)',
				'[20,50]' => 'Description (up to 50 words)',
				'[200,300]' => 'Long description (up to 300 words)',
				'[400,600]' => 'Short article (up to 600 words)',
				'[800,1200]' => 'Long article (800 - 1200 words)',
			],
			'textarea_raw_html' => [
				'[10,15]' => 'Title (up to 15 words)',
				'[15,25]' => 'Short description (up to 25 words)',
				'[20,50]' => 'Description (up to 50 words)',
				'[200,300]' => 'Long description (up to 300 words)',
				'[400,600]' => 'Short article (up to 600 words)',
				'[800,1200]' => 'Long article (800 - 1200 words)',
			],
			'textarea' => [
				'[10,15]' => 'Title (up to 15 words)',
				'[15,25]' => 'Short description (up to 25 words)',
				'[20,50]' => 'Description (up to 50 words)',
				'[200,300]' => 'Long description (up to 300 words)',
			],
			'textfield' => [
				'[10,15]' => 'Title (up to 15 words)',
				'[15,25]' => 'Short description (up to 25 words)',
			],
		]);

		if (
			! is_array( $list ) ||
			! is_array( $list[ $ai_element_type ] ) ||
			! array_key_exists( $ai_element_type, $list ) ) {

			$list = [];
		}

		return  $list[ $ai_element_type ];
	}

	/**
	 * Get content type options.
	 *
	 * @since 7.2
	 * @return array
	 */
	public function get_content_generate_variant() {
		$content = apply_filters( 'wpb_ai_content_type_list', [
			'new_content' => esc_html__( 'New content', 'js_composer' ),
			'improve_existing' => esc_html__( 'Improve existing', 'js_composer' ),
			'translate' => esc_html__( 'Translate', 'js_composer' ),
		]);

		return is_array( $content ) ? $content : [];
	}

	/**
	 * Get content type form fields optionality.
	 *
	 * @since 7.2
	 * @return array
	 */
	public function get_content_type_form_fields_optionality() {
		$optionality = apply_filters( 'wpb_ai_form_fields_optionality_content_type', [
			'new_content' => [
				'contentType',
				'prompt',
				'toneOfVoice',
				'length',
				'keyWords',
			],
			'improve_existing' => [
				'contentType',
				'toneOfVoice',
				'keyWords',
			],
			'translate' => [
				'contentType',
				'language',
			],
		]);

		return is_array( $optionality ) ? $optionality : [];
	}

	/**
	 * Output data attribute for some form fields optionality.
	 *
	 * @since 7.2
	 * @param string $field_slug
	 * @param string $optionality_field_slug
	 */
	public function output_optionality_data_attr( $field_slug, $optionality_field_slug ) {
		$output = '';

		if ( 'content_type' === $field_slug ) {
			$optionality = $this->get_content_type_form_fields_optionality();
			if ( array_key_exists( $optionality_field_slug, $optionality ) ) {
				$output = esc_attr( implode( '|', $optionality[ $optionality_field_slug ] ) );
			}
		}

		return ' data-form-fields-optionality="' . $output . '"';
	}

	/**
	 * Get languages list.
	 *
	 * @since 7.2
	 * @return array
	 */
	public function get_languages_list() {
		require_once ABSPATH . 'wp-admin/includes/translation-install.php';

		$language_list = [];
		$translation_list = wp_get_available_translations();

		foreach ( $translation_list as $language_data ) {
			$language_list[] = $language_data['english_name'];
		}

		asort( $language_list );

		return $language_list;
	}
}
