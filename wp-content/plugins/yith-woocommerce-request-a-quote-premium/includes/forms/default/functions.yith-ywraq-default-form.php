<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements helper functions for YITH WooCommerce Request A Quote for the default form
 *
 * @package YITH
 * @since   2.0.0
 * @author  YITH
 */

if ( ! function_exists( 'ywraq_get_default_form_fields' ) ) {
	/**
	 * Get default fields
	 *
	 * @return array
	 * @since 2.0.0
	 * @author Emanuela Castorina
	 */
	function ywraq_get_default_form_fields() {
		return apply_filters( 'ywraq_default_form_fields', array(
			'first_name' => array(
				'id'          => 'first_name',
				'type'        => 'text',
				'class'       => array(),
				'label'       => __( 'First Name', 'yith-woocommerce-request-a-quote' ),
				'placeholder' => '',
				'enabled'     => 1,
				'validate'    => array(),
				'required'    => 1
			),
			'last_name'  => array(
				'id'          => 'last_name',
				'type'        => 'text',
				'class'       => array(),
				'label'       => __( 'Last Name', 'yith-woocommerce-request-a-quote' ),
				'placeholder' => '',
				'enabled'     => 1,
				'validate'    => array(),
				'required'    => 0
			),
			'email'      => array(
				'id'          => 'email',
				'type'        => 'email',
				'class'       => array(),
				'label'       => __( 'Email', 'yith-woocommerce-request-a-quote' ),
				'placeholder' => '',
				'enabled'     => 1,
				'validate'    => array( 'email' ),
				'required'    => 1
			),
			'message'    => array(
				'id'          => 'message',
				'type'        => 'textarea',
				'class'       => array(),
				'label'       => __( 'Message', 'yith-woocommerce-request-a-quote' ),
				'placeholder' => '',
				'validate'    => array(),
				'enabled'     => 1,
				'required'    => 0
			),

		) );

	}
}

if ( ! function_exists( 'ywraq_get_default_form_fields_keys' ) ) {
	/**
	 * Get default form fields keys
	 *
	 * @return array
	 * @since 2.0.0
	 * @author Emanuela Castorina
	 */
	function ywraq_get_default_form_fields_keys() {

		$fields = ywraq_get_default_form_fields();

		return is_array( $fields ) ? array_keys( $fields ) : array();
	}
}

if ( ! function_exists( 'ywraq_extrafields_from_previous_version' ) ) {
	/**
	 * @return array
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywraq_extrafields_from_previous_version() {
		$extra_fields               = array();
		$optional_form_upload_field = get_option( 'ywraq_additional_upload_field' ) == 'yes';
		$default_connect_to_field   = ywraq_get_connect_fields();
		for ( $i = 1; $i < 4; $i ++ ) {
			$sufx                     = ( 1 == $i ) ? '' : '_' . $i;
			$opt_name                 = 'ywraq_additional_text_field' . $sufx;
			$name                     = 'rqa_text_field' . $sufx;
			$optional_form_text_field = get_option( $opt_name ) == 'yes';
			if ( $optional_form_text_field ) {
				$connect                           = get_option( 'ywraq_additional_text_field_meta' . $sufx );
				$sub_connect                       = substr( $connect, 1 );
				$connect                           = in_array( $sub_connect, $default_connect_to_field ) ? $sub_connect : $connect;
				$optional_form_text_field_required = ( get_option( 'ywraq_additional_text_field_required' . $sufx ) == 'yes' ) ? 1 : 0;
				$extra_fields[ $name ]             = array(
					'id'               => 'rqa_text_field_row' . $sufx,
					'type'             => 'text',
					'class'            => array(),
					'label'            => get_option( 'ywraq_additional_text_field_label' . $sufx ),
					'placeholder'      => '',
					'validate'         => array(),
					'enabled'          => 1,
					'required'         => $optional_form_text_field_required,
					'connect_to_field' => $connect
				);
			}
		}
		if ( $optional_form_upload_field ) {
			$extra_fields['rqa_upload_field'] = array(
				'id'                        => 'rqa-upload-field',
				'type'                      => 'ywraq_upload',
				'class'                     => array(),
				'label'                     => get_option( 'ywraq_additional_upload_field_label' ),
				'placeholder'               => '',
				'validate'                  => array(),
				'enabled'                   => 1,
				'max_filesize'              => '',
				'upload_allowed_extensions' => '',
				'required'                  => 0
			);
		}

		return $extra_fields;
	}
}

if ( ! function_exists( 'ywraq_validate_form_fields_option' ) ) {
	/**
	 * Validate fields option and add defaults value
	 *
	 * @param array $fields
	 *
	 * @return array
	 * @since 2.0.0
	 * @author Emanuela Castorina
	 */
	function ywraq_validate_form_fields_option( $fields ) {

		if ( empty( $fields ) ) {
			return array();
		}

		foreach ( $fields as &$field ) {
			// type standard text fo not set
			! isset( $field['type'] ) && $field['type'] = 'text';
			// label empty if not set
			! isset( $field['label'] ) && $field['label'] = '';
			// placeholder empty if not set
			! isset( $field['placeholder'] ) && $field['placeholder'] = '';
			// set options for select type
			$options = '';
			if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
				foreach ( $field['options'] as $key => $value ) {

					// support no latin language
					$key   = urldecode( $key );
					$value = urldecode( $value );

					// exclude empty options
					if ( ! $key && ! $value ) {
						continue;
					}
					$options .= $key . '::' . $value;
					if ( key( array_slice( $field['options'], - 1, 1, true ) ) != $key ) {
						$options .= '|';
					}
				}
			}
			$field['options'] = $options;
			// set class and position for field
			if ( isset( $field['class'] ) && is_array( $field['class'] ) ) {
				$positions = ywraq_get_array_positions_form_field();
				foreach ( $field['class'] as $key => $single_class ) {
					if ( is_array( $positions ) && array_key_exists( $single_class, $positions ) ) {
						$field['position'] = $single_class;
						unset( $field['class'][ $key ] );
						break;
					}
				}
				$field['class'] = implode( ',', $field['class'] );
			}
			// set empty if position not set
			! isset( $field['position'] ) && $field['position'] = '';

			// set empty if upload allowed extensions not set
			! isset( $field['upload_allowed_extensions'] ) && $field['upload_allowed_extensions'] = '';

			// set empty if upload allowed extensions not set
			! isset( $field['max_filesize'] ) && $field['max_filesize'] = '';

			// set label class foe field
			$field['label_class'] = ( isset( $field['label_class'] ) && is_array( $field['label_class'] ) ) ? implode( ',', $field['label_class'] ) : '';
			// set validation
			$field['validate'] = ( isset( $field['validate'] ) && is_array( $field['validate'] ) ) ? implode( ',', $field['validate'] ) : '';
			//set connect to field
			$field['connect_to_field'] = ( isset( $field['connect_to_field'] ) && $field['connect_to_field'] ) ? $field['connect_to_field'] : '';
			// set required ( default false )
			$field['required'] = ( ! isset( $field['required'] ) || ! $field['required'] ) ? '0' : '1';
			// set clear ( default false )
			$field['clear'] = ( ! isset( $field['clear'] ) || ! $field['clear'] ) ? '0' : '1';
			// set enabled ( default true )
			$field['enabled'] = ( isset( $field['enabled'] ) && ! $field['enabled'] ) ? '0' : '1';
			// set show in email ( default true )
			$field['show_in_email'] = ( isset( $field['show_in_email'] ) && ! $field['show_in_email'] ) ? '0' : '1';
			// set show in order ( default true )
			$field['show_in_order'] = ( isset( $field['show_in_order'] ) && ! $field['show_in_order'] ) ? '0' : '1';
			// set show in my-account ( default true )
			$field['show_in_account'] = ( isset( $field['show_in_account'] ) && ! $field['show_in_account'] ) ? '0' : '1';

		}

		return $fields;
	}
}

if ( ! function_exists( 'ywraq_get_form_fields' ) ) {
	/**
	 * Get request a quote fields
	 *
	 * @param bool $validate
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 * @author Emanuela Castorina
	 */
	function ywraq_get_form_fields( $validate = false ) {

		// first check in options
		$fields = get_option( 'ywraq_fields_form_options', array() );

		// if options is empty gets the defaults form fields
		if ( empty( $fields ) ) {
			$fields      = ywraq_get_default_form_fields();
			$extrafields = ywraq_extrafields_from_previous_version();
			$fields      = array_merge( $fields, $extrafields );
		}

		// first validate if is admin
		if ( $validate ) {
			$fields = ywraq_validate_form_fields_option( $fields );
		}

		return apply_filters( 'ywraq_form_fields', $fields );
	}
}

if ( ! function_exists( 'ywraq_get_array_validation_form_field' ) ) {
	/**
	 * Get an array with all validation field
	 *
	 * @return array
	 * @author Emanuela Castorina
	 * @since 2.0.0
	 */
	function ywraq_get_array_validation_form_field() {
		return apply_filters( 'ywraq_validation_form_field_options_array', array(
			''      => __( 'No validation', 'yith-woocommerce-request-a-quote' ),
			'phone' => __( 'Phone', 'yith-woocommerce-request-a-quote' ),
			'email' => __( 'Email', 'yith-woocommerce-request-a-quote' ),
			'file'  => __( 'File', 'yith-woocommerce-request-a-quote' )
		) );
	}
}

if ( ! function_exists( 'ywraq_get_array_positions_form_field' ) ) {
	/**
	 * Get an array with all positions field
	 *
	 * @return array
	 * @author Emanuela Castorina
	 * @since 2.0.0
	 */
	function ywraq_get_array_positions_form_field() {
		return apply_filters( 'ywraq_positions_form_field_options_array', array(
			'form-row-first' => __( 'First', 'yith-woocommerce-request-a-quote' ),
			'form-row-last'  => __( 'Last', 'yith-woocommerce-request-a-quote' ),
			'form-row-wide'  => __( 'Wide', 'yith-woocommerce-request-a-quote' )
		) );
	}
}

if ( ! function_exists( 'ywraq_get_form_field_type' ) ) {
	/**
	 * Get type for fields
	 *
	 * @return array
	 * @since 2.0.0
	 * @author Emanuela Castorina
	 */
	function ywraq_get_form_field_type() {
		$types = array(
			'text'              => __( 'Text', 'yith-woocommerce-request-a-quote' ),
			'tel'               => __( 'Phone', 'yith-woocommerce-request-a-quote' ),
			'textarea'          => __( 'Textarea', 'yith-woocommerce-request-a-quote' ),
			'radio'             => __( 'Radio', 'yith-woocommerce-request-a-quote' ),
			'checkbox'          => __( 'Checkbox', 'yith-woocommerce-request-a-quote' ),
			'select'            => __( 'Select', 'yith-woocommerce-request-a-quote' ),
			'country'           => __( 'Country', 'yith-woocommerce-request-a-quote' ),
			'state'             => __( 'State', 'yith-woocommerce-request-a-quote' ),
			'ywraq_upload'      => __( 'Upload', 'yith-woocommerce-request-a-quote' ),
			'ywraq_multiselect' => __( 'Multi select', 'yith-woocommerce-request-a-quote' ),
			'ywraq_datepicker'  => __( 'Date', 'yith-woocommerce-request-a-quote' ),
			'ywraq_timepicker'  => __( 'Time', 'yith-woocommerce-request-a-quote' ),
			'ywraq_acceptance'  => __( 'Acceptance', 'yith-woocommerce-request-a-quote' ),
			'ywraq_heading'     => __( 'Heading', 'yith-woocommerce-request-a-quote' )
		);

		return apply_filters( 'ywraq_form_field_types', $types );
	}
}


if ( ! function_exists( 'ywraq_get_upload_mime_types' ) ) {
	/**
	 * @param $mime_types
	 *
	 * @return array|bool
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywraq_get_upload_mime_types( $mime_types ) {
		if ( ! $mime_types ) {
			return false;
		}
		$mime_types        = array_map( 'trim', explode( ',', $mime_types ) );
		$allowed_mime_type = get_allowed_mime_types();
		$new_mime          = array();
		foreach ( $mime_types as $mime_type ) {
			switch ( $mime_type ) {
				case 'jpg':
				case 'jpeg':
				case 'jpe':
					$mime_type = 'jpg|jpeg|jpe';
					break;
				case 'tiff':
				case 'tif':
					$mime_type = 'tiff|tif';
					break;
				default:
					$mime_type = apply_filters( 'ywraq_check_sigle_mime_type', $mime_type );
			}

			if ( isset( $allowed_mime_type[ $mime_type ] ) ) {
				$new_mime[ $mime_type ] = $allowed_mime_type[ $mime_type ];
			} else {
				$new_mime[ $mime_type ] = $mime_type;
			}
		}

		return $new_mime;
	}
}

if ( ! function_exists( 'ywraq_get_default_form_attachment' ) ) {
	/**
	 * Extract the file paths of attachments from request array filled.
	 *
	 * Check also if files exist.
	 *
	 * @param $args
	 *
	 * @param string $type
	 *
	 * @return array
	 * @since 2.0
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywraq_get_default_form_attachment( $args, $type = 'file' ) {
		$attachments = array();
		foreach ( $args as $name => $arg ) {
			if ( isset( $arg['type'] ) && 'ywraq_upload' == $arg['type'] ) {
				$file_info = $arg['value'];
				$key       = isset( $item['label'] ) ? $arg['label'] : $name;
				if ( isset( $file_info[ $type ] ) && ! empty( $file_info ) && file_exists( $file_info['file'] ) ) {
					$attachments[ $key ] = $file_info[ $type ];
				}
			}
		}

		return $attachments;
	}
}

if ( ! function_exists( 'ywraq_field_filter_wpml_strings' ) ) {
	/**
	 * Filter field strings for WPML translations
	 *
	 * @param string $field_key
	 * @param array $field
	 *
	 * @return array
	 * @author Francesco Licandro
	 * @since 1.0.10
	 */
	function ywraq_field_filter_wpml_strings( $field_key, $field ) {
		if ( ! class_exists( 'SitePress' ) ) {
			return $field;
		}
		// get label if any
		if ( isset( $field['label'] ) && $field['label'] ) {
			$field['label'] = apply_filters( 'wpml_translate_single_string', $field['label'], 'yith-woocommerce-request-a-quote', 'plugin_ywraq_' . $field_key . '_label' );
		}
		// get placeholder if any
		if ( isset( $field['placeholder'] ) && $field['placeholder'] ) {
			$field['placeholder'] = apply_filters( 'wpml_translate_single_string', $field['placeholder'], 'yith-woocommerce-request-a-quote', 'plugin_ywraq_' . $field_key . '_placeholder' );
		}

		if ( ! empty( $field['options'] ) && is_array( $field['options'] ) ) {

			foreach ( $field['options'] as $option_key => $option ) {
				if ( $option === '' ) {
					continue;
				}
				// register single option
				$field['options'][ $option_key ] = apply_filters( 'wpml_translate_single_string', $option, 'yith-woocommerce-request-a-quote', 'plugin_ywraq_' . $field_key . '_' . $option_key );
			}
		}

		return $field;
	}
}

if ( ! function_exists( 'ywraq_replace_policy_page_link_placeholders' ) ) {
	/**
	 * Replaces placeholders with links to WooCommerce policy pages.
	 *
	 * @param string $text Text to find/replace within.
	 *
	 * @return string
	 * @since 1.3.5
	 *
	 */
	function ywraq_replace_policy_page_link_placeholders( $text ) {
		return function_exists( 'wc_replace_policy_page_link_placeholders' ) ? wc_replace_policy_page_link_placeholders( $text ) : $text;
	}
}
