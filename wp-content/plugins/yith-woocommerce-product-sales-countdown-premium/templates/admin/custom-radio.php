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
 * Implements a custom radio in YWPC plugin admin tab
 *
 * @class   YWPC_Custom_Select
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 *
 */
class YWPC_Custom_Radio {

	/**
	 * Outputs a custom radio template in plugin options panel
	 *
	 * @since   1.0.0
	 *
	 * @param   $option
	 *
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	public static function output( $option ) {

		$custom_attributes = array();

		if ( ! empty( $option['custom_attributes'] ) && is_array( $option['custom_attributes'] ) ) {
			foreach ( $option['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		$option_value = WC_Admin_Settings::get_option( $option['id'], $option['default'] );

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo sanitize_title( $option['type'] ) ?>">
				<fieldset>
					<?php echo $option['desc']; ?>
					<ul>
						<?php
						foreach ( $option['options'] as $key => $val ) {
							?>
							<li>
								<label>
									<input
										name="<?php echo esc_attr( $option['id'] ); ?>"
										value="<?php echo $key; ?>"
										type="radio"
										style="<?php echo esc_attr( $option['css'] ); ?>"
										class="<?php echo esc_attr( $option['class'] ); ?>"
										<?php echo implode( ' ', $custom_attributes ); ?>
										<?php checked( $key, $option_value ); ?>
									/> <?php echo $val ?>
								</label>
							</li>
							<?php
						}
						?>
					</ul>
					<div class="ywpc-preview">
						<div class="ywpc-countdown ywpc-item-0">
							<div class="ywpc-header">
								<?php esc_html_e( 'Sale ends in', 'yith-woocommerce-product-countdown' ); ?>
							</div>
							<div class="ywpc-timer">
								<div class="ywpc-days">
									<div class="ywpc-amount">
										<span class="ywpc-char-1">1</span>
										<span class="ywpc-char-1">1</span>
										<span class="ywpc-char-2">0</span>
									</div>
									<div class="ywpc-label">
										<?php esc_html_e( 'Days', 'yith-woocommerce-product-countdown' ) ?>
									</div>
								</div>
								<div class="ywpc-hours">
									<div class="ywpc-amount">
										<span class="ywpc-char-1">1</span>
										<span class="ywpc-char-2">2</span>
									</div>
									<div class="ywpc-label">
										<?php esc_html_e( 'Hours', 'yith-woocommerce-product-countdown' ) ?>
									</div>
								</div>
								<div class="ywpc-minutes">
									<div class="ywpc-amount">
										<span class="ywpc-char-1">2</span>
										<span class="ywpc-char-2">3</span>
									</div>
									<div class="ywpc-label">
										<?php esc_html_e( 'Minutes', 'yith-woocommerce-product-countdown' ) ?>
									</div>
								</div>
								<div class="ywpc-seconds">
									<div class="ywpc-amount">
										<span class="ywpc-char-1">4</span>
										<span class="ywpc-char-2">1</span>
									</div>
									<div class="ywpc-label">
										<?php esc_html_e( 'Seconds', 'yith-woocommerce-product-countdown' ) ?>
									</div>
								</div>
							</div>
						</div>
						<div class="ywpc-sale-bar ywpc-item-0">
							<div class="ywpc-header" style="width: 34%;">
								<?php esc_html_e( 'On sale', 'yith-woocommerce-product-countdown' ); ?>
							</div>
							<div class="ywpc-bar">
								<div class="ywpc-back">
									<div class="ywpc-fore" style="width: 40%">
									</div>
								</div>
								<div class="ywpc-label">
									<?php
									if ( ! is_rtl() ) {
										printf( esc_html__( '%d/%d Sold', 'yith-woocommerce-product-countdown' ), 40, 100 );
									} else {
										printf( esc_html__( '%d/%d Sold', 'yith-woocommerce-product-countdown' ), 100, 40 );
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
				<script type="text/javascript">
					jQuery(function ($) {

						$(window).load(function () {

							$('.forminp-<?php echo sanitize_title( $option['type'] ) ?> input').click(function () {

								$('#ywpc-frontend-css').attr('href', '<?php echo YWPC_ASSETS_URL; ?>/css/ywpc-style-' + $(this).val() + '<?php echo defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ?>.css');

								if ($(this).val() == '1') {
									$('.ywpc-days').css('width', '120px');
								} else if ($(this).val() == '2') {
									$('.ywpc-days').css('width', '110px');
								} else {
									$('.ywpc-days').css('width', '80px');
								}

							});

						});

					});
				</script>
			</td>
		</tr>
		<?php
	}

}