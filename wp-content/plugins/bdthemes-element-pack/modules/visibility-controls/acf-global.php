<?php
/**
 * ACF Handler
 *
 * Contains helper functions for ACF fields.
 */

namespace ElementPack\Modules\VisibilityControls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class ACF_Global.
 */
class ACF_Global {

	/**
	 * Format Acf Options.
	 *
	 * @since 4.4.8
	 * @access public
	 *
	 * @param array $posts query objects - available custom fields -.
	 * @param array $options display options.
	 *
	 * @return array $results formated control options.
	 */
	public static function format_acf_query_result( $posts, $options ) {

		$results         = array();
		$show_type       = $options['show_type'];
		$show_field_type = $options['show_field_type'];
		$show_group      = $options['show_group'];

		foreach ( $posts as $post ) {

			$acf_settings = unserialize( $post->post_content, ['allowed_classes' => false] ); // TODO:: check for a safer method.

			$acf_type = $show_field_type ? ucwords( $acf_settings['type'] ) . ': ' : '';

			if ( ! in_array( $acf_settings['type'], self::get_allowed_field_types( $options['field_type'] ), true ) ) {
				continue; }

			$acf_group = $show_group ? ' ( ' . get_the_title( $post->post_parent ) . ' ) ' : '';

			$option_label = $acf_type . $post->post_title . $acf_group;

			$results[ $post->post_name ] = $option_label;
		}

		return $results;
	}

	/**
	 * Get ACF Options Pages Ids.
	 *
	 * List of ids of all options pages registered.
	 *
	 * @access public
	 * @since  4.4.8
	 * @return array $options_page_groups_ids   pages id
	 */
	public static function get_acf_options_pages_ids() {

		$options_page_groups_ids = array();

		if ( function_exists( 'acf_options_page' ) ) {
			$pages = acf_options_page()->get_pages();

			foreach ( $pages as $slug => $page ) {
				$options_page_groups = acf_get_field_groups(
					array(
						'options_page' => $slug,
					)
				);

				foreach ( $options_page_groups as $options_page_group ) {
					$options_page_groups_ids[] = $options_page_group['ID'];
				}
			}
		}

		return $options_page_groups_ids;
	}

	/**
	 * Check if the ACF field is in an options page.
	 *
	 * @access public
	 * @since 4.4.8
	 *
	 * @param int $parent field parent id.
	 * @return bool
	 */
	public static function in_option_page( $parent ) {

		$option_pgs_ids = self::get_acf_options_pages_ids();

		return in_array( $parent, $option_pgs_ids, true );

	}

	/**
	 * Returns allowed field types
	 *
	 * @access public
	 * @since  4.4.8
	 *
	 * @param string $type field category.
	 * @return array
	 */
	public static function get_allowed_field_types( $type ) {

		$default_types = array(
			'textual' => array(
				'text',
				'textarea',
				'number',
				'range',
				'email',
				'url',
				'password',
				'wysiwyg',
			),
			'date'    => array(
				'date_picker',
				'date_time_picker',
			),
			'choice'  => array(
				'select',
				'checkbox',
				'radio',
			),
			'boolean' => array(
				'true_false',
			),
		);

		return $default_types[ $type ];
	}

	/**
	 * Format Acf Values into array ['val : lablel'] || ['val : val']
	 *
	 * @access public
	 * @since 4.4.8
	 *
	 * @param string  $values acf         choice field value/s.
	 * @param string  $return_format      acf field return format.
	 * @param boolean $is_radio           true if the field is radio button.
	 * @param boolean $single_select      true if the field is a select option and multiple value is disabled.
	 *
	 * @return array
	 */
	public static function format_acf_values( $values, $return_format, $is_radio, $single_select = false ) {

		$formated_values = array();

		if ( $is_radio || $single_select ) {

			if ( 'array' === $return_format ) {
				array_push( $formated_values, $values['value'] . ' : ' . $values['label'] );
			} else {
				array_push( $formated_values, $values . ' : ' . $values );
			}
		} else {

			$values = acf_decode_choices( $values );

			foreach ( $values as $index => $value ) {
				if ( 'array' === $return_format ) {
					array_push( $formated_values, $value['value'] . ' : ' . $value['label'] );
				} else {
					array_push( $formated_values, $value . ' : ' . $value );
				}
			}
		}

		return $formated_values;
	}

	/**
	 * Get ACF field value.
	 *
	 * @access public
	 * @since 4.4.8
	 *
	 * @param string $field_key  acf key.
	 * @param int    $parent     acf parent id.
	 */
	public function get_acf_field_value( $field_key, $parent ) {

		if ( self::in_option_page( $parent ) ) {

			return get_field_object( $field_key, 'option' )['value'];
		} else {

			if ( is_preview() ) {
				add_filter( 'acf/pre_load_post_id', array( $this, 'fix_post_id_on_preview' ), 10, 2 );
			}

			return get_field_object( $field_key )['value'];
		}

	}


	/**
	 * Fix PostId conflict on Preview.
	 *
	 * @access public
	 * @since 4.4.8
	 *
	 * @param null $null       $null.
	 * @param int  $post_id    post id.
	 */
	public static function fix_post_id_on_preview( $null, $post_id ) {

		if ( is_preview() ) {
			return get_the_ID();
		} else {
			$acf_post_id = isset( $post_id->ID ) ? $post_id->ID : $post_id;

			if ( ! empty( $acf_post_id ) ) {
				return $acf_post_id;
			} else {
				return $null;
			}
		}
	}

}
