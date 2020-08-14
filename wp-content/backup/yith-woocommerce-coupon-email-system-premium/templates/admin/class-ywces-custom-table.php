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

/**
 * Outputs a custom table template for manage multiple thresholds in plugin options panel
 *
 * @class   YWCES_Custom_Table
 * @since   1.0.0
 * @author  Your Inspiration Themes
 *
 * @package Yithemes
 */
class YWCES_Custom_Table {

	/**
	 * Single instance of the class
	 *
	 * @since 1.0.0
	 * @var \YWCES_Custom_Table
	 */
	protected static $instance;

	/**
	 * Returns single instance of the class
	 *
	 * @return \YWCES_Custom_Table
	 * @since 1.0.0
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {

			self::$instance = new self( $_REQUEST );

		}

		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @return  mixed
	 * @since   1.0.0
	 * @author  Alberto Ruggiero
	 */
	public function __construct() {

		add_action( 'woocommerce_admin_field_ywces-table', array( $this, 'output' ) );
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'save' ), 10, 3 );

	}

	/**
	 * Outputs a custom table template for manage multiple thresholds in plugin options panel
	 *
	 * @param   $option
	 *
	 * @return  void
	 * @since   1.0.0
	 *
	 * @author  Alberto Ruggiero
	 */
	public function output( $option ) {

		$option_value = maybe_unserialize( get_option( $option['id'] ) );

		?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option['title'] ); ?></label>
            </th>
            <td class="forminp" id="<?php echo $option['id'] ?>">
                <table class="widefat wc_input_table sortable" cellspacing="0">
                    <thead>
                    <tr>
                        <th class="sort">&nbsp;</th>
                        <th><?php esc_html_e( 'Target value', 'yith-woocommerce-coupon-email-system' ); ?></th>
                        <th><?php esc_html_e( 'Coupon assigned', 'yith-woocommerce-coupon-email-system' ); ?></th>

                    </tr>
                    </thead>
                    <tbody class="thresholds ui-sortable">
					<?php
					if ( is_array( $option_value ) ) {

						$i = - 1;
						foreach ( $option_value as $row ) :
							$i ++; ?>
                            <tr class="threshold ui-sortable-handle">
                                <td class="sort">
                                    <input
                                        class=""
                                        type="hidden"
                                        value="<?php echo $row['customers'] ?>"
                                        name="<?php echo $option['id'] ?>[<?php echo $i; ?>][customers]"
                                        id="<?php echo $option['id'] ?>[<?php echo $i; ?>][customers]"
                                    />
                                </td>
                                <td>
                                    <input
                                        class="ywces-threshold-amount"
                                        type="number"
                                        value="<?php echo $row['amount'] ?>"
                                        name="<?php echo $option['id'] ?>[<?php echo $i; ?>][amount]"
                                        id="<?php echo $option['id'] ?>[<?php echo $i; ?>][amount]"
                                        min="1"
                                        value="10" />
                                </td>
                                <td>
                                    <select
                                        class="ywces-threshold-coupon"
                                        name="<?php echo $option['id'] ?>[<?php echo $i; ?>][coupon]"
                                        id="<?php echo $option['id'] ?>[<?php echo $i; ?>][coupon]"
                                        style="width: 100%">
										<?php foreach ( $option['options'] as $key => $val ) : ?>
                                            <option value="<?php echo esc_attr( $key ); ?>" <?php

											if ( is_array( $row['coupon'] ) ) {
												selected( in_array( $key, $row['coupon'] ), true );
											} else {
												selected( $row['coupon'], $key );
											}

											?>><?php echo $val ?></option>
										<?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
						<?php endforeach;

					}
					?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="3">
                            <a href="#" class="add button">
								<?php esc_html_e( '+ Add Threshold', 'yith-woocommerce-coupon-email-system' ); ?></a>
                            <a href="#" class="remove_rows button">
								<?php esc_html_e( 'Remove selected threshold(s)', 'yith-woocommerce-coupon-email-system' ); ?>
                            </a>
                        </th>
                    </tr>
                    </tfoot>
                </table>
                <script type="text/javascript">
                    jQuery(function () {
                        jQuery('#<?php echo $option['id'] ?>').on('click', 'a.add', function () {

                            var size = jQuery('#<?php echo $option['id'] ?> tbody .threshold').size();

                            jQuery('<tr class="threshold">\
									<td class="sort"><input type="hidden" class="" name="<?php echo $option['id'] ?>[' + size + '][customers]" id="<?php echo $option['id'] ?>[' + size + '][customers]" /></td>\
									<td><input type="number" class="ywces-threshold-amount" name="<?php echo $option['id'] ?>[' + size + '][amount]" id="<?php echo $option['id'] ?>[' + size + '][amount]" min="1" value="10" /></td>\
									<td><select class="ywces-threshold-coupon" name="<?php echo $option['id'] ?>[' + size + '][coupon]" id="<?php echo $option['id'] ?>[' + size + '][coupon]" style="width: 100%">\
									<?php foreach ( $option['options'] as $key => $val ) : ?>\
									    <option value="<?php echo esc_attr( $key ); ?>"><?php echo $val ?></option>\
									<?php endforeach; ?>\
									</select></td>\
								</tr>').appendTo('#<?php echo $option['id'] ?> table tbody');

                            return false;
                        });
                    });
                </script>
            </td>
        </tr>
		<?php
	}

	/**
	 * Saves custom textarea content
	 *
	 * @param $value
	 * @param $option
	 * @param $raw_value
	 *
	 * @return string
	 * @since   1.0.0
	 *
	 * @author  Alberto ruggiero
	 */
	public function save( $value, $option, $raw_value ) {

		if ( $option['type'] == 'ywces-table' ) {

			if ( empty( $value ) ) {
				$value = '';
			} else {
				$value = maybe_serialize( $value );
			}
		}

		return $value;

	}

}

/**
 * Unique access to instance of YWCES_Custom_Table class
 *
 * @return \YWCES_Custom_Table
 */
function YWCES_Custom_Table() {

	return YWCES_Custom_Table::get_instance();

}

new YWCES_Custom_Table();