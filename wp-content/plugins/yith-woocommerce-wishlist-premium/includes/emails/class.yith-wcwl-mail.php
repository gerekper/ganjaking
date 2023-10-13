<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Plugin email common class
 *
 * @author  YITH
 * @package YITH\Wishlist
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Mail' ) ) {
	/**
	 * Email Class
	 * Extend WC_Email to gift card email
	 *
	 * @class    YITH_WCWL_Mail
	 * @extends  WC_Email
	 */
	class YITH_WCWL_Mail extends WC_Email {

		/**
		 * Generate custom fields by using YITH framework fields.
		 *
		 * @param string $key The key of the field.
		 * @param array  $data The attributes of the field as an associative array.
		 *
		 * @return string
		 */
		public function generate_yith_wcwl_field_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$value     = $this->get_option( $key );
			$defaults  = array(
				'title'                => '',
				'label'                => '',
				'yith_wcwl_field_type' => 'text',
				'description'          => '',
				'desc_tip'             => false,
			);

			wp_enqueue_script( 'yith-plugin-fw-fields' );
			wp_enqueue_style( 'yith-plugin-fw-fields' );

			$data = wp_parse_args( $data, $defaults );

			$field          = $data;
			$field['type']  = $data['yith_wcwl_field_type'];
			$field['name']  = $field_key;
			$field['value'] = $value;
			$private_keys   = array( 'label', 'title', 'description', 'yith_wcwl_field_type', 'desc_tip' );

			foreach ( $private_keys as $private_key ) {
				unset( $field[ $private_key ] );
			}

			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?><?php echo wp_kses_post( $this->get_tooltip_html( $data ) ); // phpcs:ignore ?></label>
				</th>
				<td class="forminp yith-plugin-ui">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
						<?php yith_plugin_fw_get_field( $field, true, true ); ?>
						<?php echo wp_kses_post( $this->get_description_html( $data ) ); // phpcs:ignore ?>
					</fieldset>
				</td>
			</tr>
			<?php

			return ob_get_clean();
		}
	}
}
