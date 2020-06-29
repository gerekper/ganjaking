<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WC_Custom_Country_Select' ) ) {

	/**
	 * Outputs a custom select template in plugin options panel
	 *
	 * @class   YITH_WC_Custom_Advanced_Select
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YITH_WC_Custom_Country_Select {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Custom_Country_Select
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_action( 'woocommerce_admin_field_yith-wc-country-select', array( $this, 'output' ) );
			add_action( 'wp_ajax_yith_acs_json_search_countries', array( $this, 'yith_acs_json_search_countries' ), 10 );

		}

		/**
		 * Outputs a custom select template in plugin options panel
		 *
		 * @since   1.0.0
		 *
		 * @param   $option
		 *
		 * @author  Alberto Ruggiero
		 * @return  void
		 */
		public function output( $option ) {

			$custom_attributes = array();

			if ( ! empty( $option['custom_attributes'] ) && is_array( $option['custom_attributes'] ) ) {
				foreach ( $option['custom_attributes'] as $attribute => $attribute_value ) {
					$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
				}
			}

			$option_value  = WC_Admin_Settings::get_option( $option['id'], $option['default'] );
			$countries     = wc()->countries->get_countries();
			$data_selected = '';
			$value         = '';

			if ( $option['multiple'] == 'true' ) {

				$country_codes = ( ! is_array( $option_value ) ) ? explode( ',', $option_value ) : $option_value;
				$json_ids      = array();

				foreach ( $country_codes as $country_id ) {

					if ( isset( $countries[ $country_id ] ) ) {

						$json_ids[ $country_id ] = $this->format_country_name( $country_id, $countries[ $country_id ] );

					}

				}

				$data_selected = esc_attr( json_encode( $json_ids ) );
				$value         = array_keys( $json_ids );

			} else {

				if ( $option_value != '' && isset( $countries[ $option_value ] ) ) {

					$data_selected = $this->format_country_name( $option_value, $countries[ $option_value ] );
					$value         = $option_value;

				}

			}

			?>
            <tr valign="top" class="titledesc">
                <th scope="row">
                    <label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option['title'] ); ?></label>
                </th>
                <td class="forminp forminp-<?php echo sanitize_title( $option['type'] ) ?>">
                    <select
                        multiple="multiple"
                        id="<?php echo esc_attr( $option['id'] ); ?>"
                        name="<?php echo esc_attr( $option['id'] ); ?>[]"
                        style="<?php echo esc_attr( $option['css'] ); ?>"
                        data-placeholder="<?php echo esc_attr( $option['placeholder'] ) ?>"
                        data-action="yith_acs_json_search_countries"
                        class="wc-enhanced-select"
                        data-multiple="<?php echo $option['multiple'] ?>"
                        data-selected="<?php echo $data_selected; ?>"
						<?php echo implode( ' ', $custom_attributes ); ?> >
						<?php
						if ( ! empty( $countries ) ) {
							foreach ( $countries as $key => $val ) {
								echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $value ), true, false ) . '>' . $val . '</option>';
							}
						}
						?>
                    </select>
                    <span class="description"><?php echo $option['desc']; ?></span>
                </td>
            </tr>
			<?php

		}

		/**
		 * Get countries list
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function yith_acs_json_search_countries() {

			$term      = (string) urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );
			$countries = wc()->countries->get_countries();
			$to_json   = array();

			foreach ( $countries as $key => $country ) {

				if ( strpos( strtolower( $country ), strtolower( $term ) ) !== false ) {

					$to_json[ $key ] = $this->format_country_name( $key, $country );

				}

			}

			wp_send_json( $to_json );

		}

		/**
		 * Format country name
		 *
		 * @since   1.0.0
		 *
		 * @param   $code
		 * @param   $name
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function format_country_name( $code, $name ) {

			return sprintf( '%s &ndash; %s', $code, $name );
		}

	}

	new YITH_WC_Custom_Country_Select();

}