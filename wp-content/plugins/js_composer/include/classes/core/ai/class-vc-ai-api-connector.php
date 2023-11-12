<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Ai_Api_Connector helps to communicate with AI API.
 *
 * @since 7.2
 */
class Vc_Ai_Api_Connector {
	/**
	 * AI API url.
	 *
	 * @version 7.2
	 * @var string
	 */
	protected $ai_api_url = 'https://api-ai.wpbakery.com';

	/**
	 * Get AI element type response class and method controller with endpoint dependency.
	 *
	 * @since 7.2
	 * @return array
	 */
	public function get_ai_type_response_route_lib() {
		return [
			'textarea_html' => [
				'path' => vc_path_dir( 'CORE_DIR', 'ai/class-vc-ai-content-generator.php' ),
				'class' => 'Vc_Ai_Content_Generator',
				'method' => 'generate',
				'endpoint' => [
					'default' => 'generate-text',
					'length' => [
						'[800,1200]' => 'generate-article',
						'[400,600]' => 'generate-article',
						'[10,15]' => 'generate-title',
					],
					'contentType' => [
						'improve_existing' => 'rewrite-text',
						'translate' => 'translate-text',
					],
				],
			],
			'textarea_raw_html' => [
				'path' => [
					'default' => vc_path_dir( 'CORE_DIR', 'ai/class-vc-ai-content-generator.php' ),
					'wpb-ai-element-id' => [
						'textarea_raw_html_javascript_code' => vc_path_dir( 'CORE_DIR', 'ai/class-vc-ai-code-generator.php' ),
					],
				],
				'class' => [
					'default' => 'Vc_Ai_Content_Generator',
					'wpb-ai-element-id' => [
						'textarea_raw_html_javascript_code' => 'Vc_Ai_Code_Generator',
					],
				],
				'method' => 'generate',
				'endpoint' => [
					'default' => 'generate-text',
					'length' => [
						'[800,1200]' => 'generate-article',
						'[400,600]' => 'generate-article',
						'[10,15]' => 'generate-title',
					],
					'contentType' => [
						'improve_existing' => 'rewrite-text',
						'translate' => 'translate-text',
					],
					'wpb-ai-element-id' => [
						'textarea_raw_html_javascript_code' => 'generate-js',
					],
				],
			],
			'textarea' => [
				'path' => vc_path_dir( 'CORE_DIR', 'ai/class-vc-ai-content-generator.php' ),
				'class' => 'Vc_Ai_Content_Generator',
				'method' => 'generate',
				'endpoint' => [
					'default' => 'generate-text',
					'length' => [
						'[10,15]' => 'generate-title',
					],
					'contentType' => [
						'improve_existing' => 'rewrite-text',
						'translate' => 'translate-text',
					],
				],
			],
			'textfield' => [
				'path' => vc_path_dir( 'CORE_DIR', 'ai/class-vc-ai-content-generator.php' ),
				'class' => 'Vc_Ai_Content_Generator',
				'method' => 'generate',
				'endpoint' => [
					'default' => 'generate-title',
					'contentType' => [
						'improve_existing' => 'rewrite-text',
						'translate' => 'translate-text',
					],
				],
			],
			'custom_css' => [
				'path' => vc_path_dir( 'CORE_DIR', 'ai/class-vc-ai-code-generator.php' ),
				'class' => 'Vc_Ai_Code_Generator',
				'method' => 'generate',
				'endpoint' => 'generate-css',
			],
			'custom_js' => [
				'path' => vc_path_dir( 'CORE_DIR', 'ai/class-vc-ai-code-generator.php' ),
				'class' => 'Vc_Ai_Code_Generator',
				'method' => 'generate',
				'endpoint' => 'generate-js',
			],
		];
	}

	/**
	 * Get content from AI API.
	 *
	 * @version 7.2
	 * @param array $data
	 * @return WP_Error | string
	 */
	public function get_ai_content( $data ) {
		$ai_element_type_index = array_search( 'wpb-ai-element-type', array_column( $data, 'name' ) );
		if ( false === $ai_element_type_index ) {
			return new WP_Error(
				'ai_error_invalid_user_data',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 603): wpb-ai-element-type missing', 'js_composer' )
			);
		}

		if ( empty( $data[ $ai_element_type_index ]['value'] ) ||
			! is_string( $data[ $ai_element_type_index ]['value'] ) ) {
			return new WP_Error(
				'ai_error_invalid_user_data',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 604): wrong ai type value', 'js_composer' )
			);
		}

		$data = $this->add_license_key_to_request_data( $data );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$ai_element_type = $data[ $ai_element_type_index ]['value'];
		$route_controller = $this->get_route_controller( $ai_element_type, $data );
		if ( is_wp_error( $route_controller ) ) {
			return $route_controller;
		}

		$route_lib = $this->get_ai_type_response_route_lib();
		$endpoint = $this->get_resolved_route_optionality( $route_lib[ $ai_element_type ]['endpoint'], $data );

		if ( is_wp_error( $endpoint ) ) {
			return $endpoint;
		}

		$data['type'] = $endpoint;

		return $route_controller->{$route_lib[ $ai_element_type ]['method']}( $data );
	}

	/**
	 * Get data from route lib by type and check if it is valid.
	 *
	 * @since 7.2
	 * @param string $ai_element_type
	 * @param array $form_data
	 * @return object | WP_Error
	 */
	public function get_route_controller( $ai_element_type, $form_data ) {

		$route_lib = $this->get_ai_type_response_route_lib();

		if ( ! array_key_exists( $ai_element_type, $route_lib ) ) {
			return new WP_Error(
				'ai_error_route_list_do_not_have_type',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 605) wrong url. Please check: ', 'js_composer' ) .
				'Vc_Ai_Api_Connector::get_ai_type_response_route_lib()'
			);
		}

		$route_data = $route_lib[ $ai_element_type ];
		if ( ! isset( $route_data['path'], $route_data['class'], $route_data['method'], $route_data['endpoint'] ) ) {
			return new WP_Error(
				'ai_error_route_type_does_not_have_all_required_fields',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 606): url attribute/s missing. Please check: ', 'js_composer' ) .
				'Vc_Ai_Api_Connector::get_ai_type_response_route_lib()'
			);
		}

		$controller_path = $this->get_resolved_route_optionality( $route_lib[ $ai_element_type ]['path'], $form_data );
		if ( is_wp_error( $controller_path ) ) {
			return $controller_path;
		}

		if ( ! file_exists( $controller_path ) ) {
			return new WP_Error(
				'ai_error_file_controller_does_not_exist',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 607): file class controller does not exist. Please check: ', 'js_composer' ) .
				'Vc_Ai_Api_Connector::get_ai_type_response_route_lib()'
			);
		}

		require_once $controller_path;

		$class = $this->get_resolved_route_optionality( $route_lib[ $ai_element_type ]['class'], $form_data );
		if ( is_wp_error( $class ) ) {
			return $class;
		}

		if ( ! class_exists( $class ) ) {
			return new WP_Error(
				'ai_error_class_controller_does_not_exist',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 608): class controller does not exist. Please check: ', 'js_composer' ) .
				'Vc_Ai_Api_Connector::get_ai_type_response_route_lib()'
			);
		}

		$route_controller = new $class();
		if ( ! method_exists( $route_controller, $route_data['method'] ) ) {
			return new WP_Error(
				'ai_error_method_controller_does_not_exist',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 609): method controlled does not exist. Please check: ', 'js_composer' ) .
				'Vc_Ai_Api_Connector::get_ai_type_response_route_lib()'
			);
		}

		return $route_controller;
	}

	/**
	 * Get resolved endpoint optionality of endpoint lib.
	 *
	 * @since 7.2
	 * @param string|array $optionality
	 * @param array $data
	 * @return string | WP_Error
	 */
	public function get_resolved_route_optionality( $optionality, $data ) {
		if ( is_string( $optionality ) ) {
			return $optionality;
		}

		if ( ! is_array( $optionality ) ) {
			return new WP_Error(
				'ai_error_invalid_resolved_optionality',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 610): invalid data type. Please check: ', 'js_composer' ) .
				'Vc_Ai_Api_Connector::get_ai_type_response_route_lib()'
			);
		}

		$default = false;
		$resolved = false;
		foreach ( $optionality as $optionality_name => $optionality_value ) {
			if ( 'default' === $optionality_name && is_string( $optionality_value ) ) {
				$default = $optionality_value;
				continue;
			}

			if ( ! is_array( $optionality_value ) ) {
				continue;
			}

			foreach ( $optionality_value as $modal_form_param_name => $modal_form_param_value ) {
				$is_value_in_data = array_search( $optionality_name, array_column( $data, 'name' ) );

				if ( false === $is_value_in_data ) {
					continue;
				}

				if ( ! isset( $data[ $is_value_in_data ]['value'] ) ) {
					continue;
				}

				if ( strval( $modal_form_param_name ) === $data[ $is_value_in_data ]['value'] ) {
					$resolved = $modal_form_param_value;
					break;
				}
			}
		}

		if ( $resolved ) {
			return $resolved;
		} else if ( $default ) {
			return $default;
		} else {
			return new WP_Error(
				'ai_error_invalid_resolved_optionality',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 611): response from route lib failed. Please check: ', 'js_composer' ) .
				'Vc_Ai_Api_Connector::get_ai_type_response_route_lib()'
			);
		}
	}

	/**
	 * Get ai api response.
	 *
	 * @since 7.2
	 * @param array $data
	 * @param string $url_part
	 * @param bool $is_messaged_data
	 *
	 * @return array|WP_Error
	 */
	public function get_api_response_data( $data, $url_part, $is_messaged_data = false ) {
		$request_params = [
			'body' => $data,
			'timeout' => 3000,
		];

		$response = wp_remote_post( $this->ai_api_url . '/' . $url_part, $request_params );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( ! in_array( $response_code, [ 200, 403 ] ) ) {
			return new WP_Error(
				'ai_error_invalid_response',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 612): invalid response code', 'js_composer' ) . $response_code
			);
		}

		$response = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $this->is_cache_response_in_process( $response ) ) {
			return $response['data'];
		}

		$error = $this->get_response_error( $response, $is_messaged_data );
		if ( is_wp_error( $error ) ) {
			return $error;
		}

		if ( $is_messaged_data ) {
			return $this->get_message_from_data( $response['data'] );
		} else {
			return $response['data'];
		}
	}

	/**
	 * Try to return message from data.
	 *
	 * @since 7.2
	 * @param $response
	 * @return string|WP_Error
	 */
	public function get_message_from_data( $response ) {
		if ( empty( $response['message'] ) ) {
			return new WP_Error(
				'ai_error_empty_response_message',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 613): empty api response message', 'js_composer' )
			);
		}

		return $response['message'];
	}

	/**
	 * Get api response data from server cache.
	 *
	 * @param $data
	 * @return string | WP_Error
	 * @since 7.2
	 */
	public function get_api_response_data_from_cache( $data ) {
		if ( ! $this->check_cache_required_fields( $data ) ) {
			return new WP_Error(
				'ai_error_invalid_user_data',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 614): missing cache id', 'js_composer' )
			);
		}

		$response = $this->get_api_response_data( $data, 'cache', true );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( isset( $response['message'] ) ) {
			return $this->get_message_from_data( $response );
		}

		return $response;
	}

	/**
	 * Check if all required fields are provided to obtain cache server request.
	 *
	 * @since 7.2
	 * @param array $data
	 * @return bool
	 */
	public function check_cache_required_fields( $data ) {
		if ( ! is_array( $data ) ) {
			return false;
		}

		$required_fields_list = [
			'type',
			'cacheId',
		];

		foreach ( $required_fields_list as $required_field ) {
			if ( ! array_key_exists( $required_field, $data ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check is current process in process of caching.
	 *
	 * @since 7.2
	 * @param $response
	 * @return bool
	 */
	public function is_cache_response_in_process( $response ) {
		if ( ! isset( $response['data']['message'] ) ) {
			return false;
		}

		return 'cache_in_process' === $response['data']['message'];
	}

	/**
	 * Check if response has error.
	 *
	 * @since 7.2
	 * @param array $response
	 * @param bool $is_messaged_data
	 * @return false | WP_Error
	 */
	public function get_response_error( $response, $is_messaged_data ) {
		if ( ! isset( $response['status'] ) ) {
			return new WP_Error(
				'ai_error_missing_response_status',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 615): api response status missing', 'js_composer' )
			);
		}

		if ( ! isset( $response['data'] ) && ! isset( $response['message'] ) ) {
			return new WP_Error(
				'ai_error_missing_response_data',
				esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 616): api response data missing', 'js_composer' )
			);
		}

		if ( $is_messaged_data ) {
			if ( ! isset( $response['data']['message'] ) && ! isset( $response['message'] ) ) {
				return new WP_Error(
					'ai_error_missing_response_status',
					esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 617): api response message missing', 'js_composer' )
				);
			} else {
				$message = isset( $response['message'] ) ? $response['message'] : $response['data']['message'];
			}

			if ( ! $response['status'] ) {
				return new WP_Error(
					'ai_error_response',
					esc_html__(
						'An error occurred when requesting a response from WPBakery AI (Code: 624): ',
						'js_composer'
					) . $message
				);
			}
		}

		return false;
	}

	/**
	 * Add license key to request data.
	 *
	 * @since 7.2
	 * @param array $data
	 * @return array | WP_Error
	 */
	public function add_license_key_to_request_data( $data ) {
		$key = vc_license()->getLicenseKey();
		if ( empty( $key ) ) {
			return new WP_Error(
				'ai_error_response',
				esc_html__(
					'WPBakery Page Builder license not activated.',
					'js_composer'
				)
			);
		}

		$data['key'] = vc_license()->getLicenseKey();
		return $data;
	}


	/**
	 * Convert data to request format.
	 *
	 * @since 7.2
	 * @param array $data
	 * @return array
	 */
	public function convert_data_to_request_format( $data ) {
		foreach ( $data as $key => $value ) {
			if ( ! is_array( $value ) ) {
				continue;
			}

			if ( ! isset( $value['name'], $value['value'] ) ) {
				continue;
			}

			$data[ $value['name'] ] = $value['value'];
			unset( $data[ $key ] );
		}

		return $data;
	}
}
