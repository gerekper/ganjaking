<?php // phpcs:ignore WordPress.NamingConventions
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\CategoryAccordion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YWCCA_Typography' ) ) {

	/**
	 * YWCCA_Typography
	 */
	class YWCCA_Typography {

		/**
		 * Output
		 *
		 * @param  mixed $option option.
		 * @return void
		 */
		public static function output( $option ) {

			$value = get_option( $option['id'], false );

			if ( ! $value ) {
				$value = $option['default'];
			}

			?>
			<style>
				.colorpick {
					width: auto !important;
				}
			</style>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option['title'] ); ?></label>
				</th>
			</tr>
			<tr valign="top">
				<td class="forminp forminp-<?php echo esc_attr( $option['type'] ); ?>[size]" style="width:20%;">

					<input type="number" name="<?php echo esc_attr( $option['id'] ); ?>[size]" min="1"
						value="<?php echo esc_attr( $value['size'] ); ?>" style="width: 60px;margin-left: 10px;">
				</td>
				<td class="forminp forminp-<?php echo esc_attr( $option['type'] ); ?>[unit]" style="width:20%;">
					<select name="<?php echo esc_attr( $option['id'] ); ?>[unit]"
							id="<?php echo esc_attr( $option['id'] ); ?>-unit">
						<option value="px" <?php selected( $value['unit'], 'px' ); ?>><?php esc_html_e( 'px', 'yith-woocommerce-category-accordion' ); ?></option>
						<option value="em" <?php selected( $value['unit'], 'em' ); ?>><?php esc_html_e( 'em', 'yith-woocommerce-category-accordion' ); ?></option>
						<option value="pt" <?php selected( $value['unit'], 'pt' ); ?>><?php esc_html_e( 'pt', 'yith-woocommerce-category-accordion' ); ?></option>
						<option value="rem" <?php selected( $value['unit'], 'rem' ); ?>><?php esc_html_e( 'rem', 'yith-woocommerce-category-accordion' ); ?></option>
					</select>
				</td>
				<td class="forminp forminp-<?php echo esc_attr( $option['type'] ); ?>[style]" style="width:20%;">

					<select name="<?php echo esc_attr( $option['id'] ); ?>[style]"
							id="<?php echo esc_attr( $option['id'] ); ?>-style">
						<option value="regular" <?php selected( $value['style'], 'regular' ); ?>><?php esc_html_e( 'Regular', 'yith-woocommerce-category-accordion' ); ?></option>
						<option value="bold" <?php selected( $value['style'], 'bold' ); ?>><?php esc_html_e( 'Bold', 'yith-woocommerce-category-accordion' ); ?></option>
						<option value="extra-bold" <?php selected( $value['style'], 'extra-bold' ); ?>><?php esc_html_e( 'Extra bold', 'yith-woocommerce-category-accordion' ); ?></option>
						<option value="italic" <?php selected( $value['style'], 'italic' ); ?>><?php esc_html_e( 'Italic', 'yith-woocommerce-category-accordion' ); ?></option>
						<option value="bold-italic" <?php selected( $value['style'], 'bold-italic' ); ?>><?php esc_html_e( 'Italic bold', 'yith-woocommerce-category-accordion' ); ?></option>
					</select>
				</td>
				<td class="forminp forminp-<?php echo esc_attr( $option['type'] ); ?>[transform]"
					style="width:20%;">

					<select name="<?php echo esc_attr( $option['id'] ); ?>[transform]"
							id="<?php echo esc_attr( $option['id'] ); ?>-transform">
						<option value="none" <?php selected( $value['transform'], 'none' ); ?>><?php esc_html_e( 'None', 'yith-woocommerce-category-accordion' ); ?></option>
						<option value="lowercase" <?php selected( $value['transform'], 'lowercase' ); ?>><?php esc_html_e( 'Lowercase', 'yith-woocommerce-category-accordion' ); ?></option>
						<option value="uppercase" <?php selected( $value['transform'], 'uppercase' ); ?>><?php esc_html_e( 'Uppercase', 'yith-woocommerce-category-accordion' ); ?></option>
						<option value="capitalize" <?php selected( $value['transform'], 'capitalize' ); ?>><?php esc_html_e( 'Capitalize', 'yith-woocommerce-category-accordion' ); ?></option>
					</select>
				</td>

				<td class="forminp forminp-<?php echo esc_attr( $option['type'] ); ?>[color]" style="width:20%;">

					<input type="text" name="<?php echo esc_attr( $option['id'] ); ?>[color]" class="colorpick" style=""
						value="<?php echo esc_attr( $value['color'] ); ?>">
				</td>


			</tr>
			<?php
		}
	}
}
