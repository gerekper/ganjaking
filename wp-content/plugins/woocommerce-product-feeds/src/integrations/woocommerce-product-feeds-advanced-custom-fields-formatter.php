<?php

/**
 * Discover ACF custom fields, and make them available as pre-population options.
 *
 * Class WoocommerceProductFeedsAdvancedCustomFields
 */
class WoocommerceProductFeedsAdvancedCustomFieldsFormatter {
	/**
	 * @param $field_object
	 * @param $default
	 * @param $prepopulate
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	public function get_value( $field_object, $default, $prepopulate ) {
		if ( empty( $field_object['type'] ) ) {
			return $default;
		}
		switch ( $field_object['type'] ) {
			case 'button_group':
			case 'select':
			case 'radio':
				return $this->get_optioned_value( $field_object, $default );
				break;
			case 'file':
			case 'image':
				return $this->get_file_value( $field_object, $default, $prepopulate );
				break;
			case 'link':
				return $this->get_link_value( $field_object, $default );
				break;
			case 'taxonomy':
				return $this->get_taxonomy_value( $field_object, $default );
				break;
			case 'true_false':
				return $this->get_true_false_value( $field_object, $default );
				break;
			case 'date_picker':
			case 'date_time_picker':
			case 'number':
			case 'page_link':
			case 'range':
			case 'text':
			case 'textarea':
			case 'url':
			case 'wysiwyg':
			default:
				return $this->get_raw_value( $field_object, $default );
				break;
		}
	}

	/**
	 * @param $field_object
	 * @param $default
	 *
	 * @return array
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function get_optioned_value( $field_object, $default ) {
		$results = [];
		$values  = ! is_array( $field_object['value'] ) ?
			[ $field_object['value'] ] :
			$field_object['value'];
		foreach ( $values as $value ) {
			$results[] = isset( $field_object['choices'][ $value ] ) ?
				$field_object['choices'][ $value ] :
				$value;
		}

		return $results;
	}

	/**
	 * @param $field_object
	 * @param $default
	 * @param $prepopulate
	 *
	 * @return array
	 */
	private function get_file_value( $field_object, $default, $prepopulate ) {
		$config = explode( ':', $prepopulate );
		if ( 'name' === $config[2] ) {
			return ! empty( $field_object['value']['filename'] ) ?
				[ $field_object['value']['filename'] ] :
				$default;
		}

		return ! empty( $field_object['value']['url'] ) ?
			[ $field_object['value']['url'] ] :
			$default;
	}

	/**
	 * @param $field_object
	 * @param $default
	 *
	 * @return array|mixed
	 */
	private function get_link_value( $field_object, $default ) {
		return ! empty( $field_object['value']['url'] ) ?
			[ $field_object['value']['url'] ] :
			$default;
	}

	/**
	 * @param $field_object
	 * @param $default
	 *
	 * @return array|mixed
	 */
	private function get_raw_value( $field_object, $default ) {
		return ! empty( $field_object['value'] ) ?
			[ $field_object['value'] ] :
			$default;
	}

	/**
	 * @param $field_object
	 * @param $default
	 *
	 * @return mixed
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function get_true_false_value( $field_object, $default ) {
		return $field_object['value'] ?
			$field_object['ui_on_text'] :
			$field_object['ui_off_text'];
	}

	/**
	 * Get the value, and map the term IDs or term objects to term names.
	 *
	 * @param $field_object
	 * @param $default
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function get_taxonomy_value( $field_object, $default ) {
		$values  = $field_object['value'];
		$results = [];
		foreach ( $values as $value ) {
			if ( is_int( $value ) ) {
				$value = get_term( $value );
			}
			if ( isset( $value->name ) ) {
				$results[] = $value->name;
			}
		}

		return $results;
	}
}
