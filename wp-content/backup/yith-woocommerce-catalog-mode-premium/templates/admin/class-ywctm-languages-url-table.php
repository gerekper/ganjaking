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
 * Outputs a custom table template in plugin options panel
 *
 * @class   YWCTM_Languages_Url_Table
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 *
 */
class YWCTM_Languages_Url_Table {

	/**
	 * Single instance of the class
	 *
	 * @var \YWCTM_Languages_Url_Table
	 * @since 1.0.0
	 */
	protected static $instance;

	/**
	 * Returns single instance of the class
	 *
	 * @return \YWCTM_Languages_Url_Table
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
	 * @since   1.0.0
	 * @return  mixed
	 * @author  Alberto Ruggiero
	 */
	public function __construct() {

		add_action( 'woocommerce_admin_field_ywctm-languages-url-table', array( $this, 'output' ) );

	}

	/**
	 * Outputs a custom table template in plugin options panel
	 *
	 * @since   1.0.0
	 *
	 * @param   $option
	 *
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	public function output( $option ) {

		$option_value = get_option( $option['id'] );
		$languages    = apply_filters( 'wpml_active_languages', null, array() );

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option['title'] ); ?></label>
			</th>
			<td class="forminp" id="<?php echo $option['id'] ?>">

				<table class="widefat wp-list-table ywctm-languages-form-table <?php echo $option['class'] ?>" cellspacing="0">
					<thead>
					<tr>
						<th>
							<?php _ex( 'Language', '[active languages column]', 'yith-woocommerce-catalog-mode' ); ?>
						</th>
						<th>
							<?php _ex( 'URL', '[url column]', 'yith-woocommerce-catalog-mode' ); ?>
						</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ( $languages as $language ) : ?>
						<tr>
							<td class="main-column">
								<?php echo $language['translated_name'] ?>
							</td>
							<td>
								<input
									name="<?php echo $option['id'] ?>[<?php echo $language['language_code']; ?>]"
									id="<?php echo $option['id'] ?>[<?php echo $language['language_code']; ?>]"
									style="width: 100%"
									type="text"
									value="<?php echo $option_value[ $language['language_code'] ]; ?>"
								/>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>

				</table>

			</td>
		</tr>
		<?php
	}

}

/**
 * Unique access to instance of YWCTM_Languages_Url_Table class
 *
 * @return \YWCTM_Languages_Url_Table
 */
function YWCTM_Languages_Url_Table() {

	return YWCTM_Languages_Url_Table::get_instance();

}

new YWCTM_Languages_Url_Table();