<?php
/**
 * Badge Settings View
 *
 * @var string $type              The badge type.
 * @var string $image_url         Image URL
 * @var string $txt_color         The text color
 * @var string $txt_color_default The text color default value
 * @var string $bg_color          The background color
 * @var string $bg_color_default  The background color default value
 * @var string $width             The width
 * @var string $height            The height
 * @var string $position          The badge position
 * @package YITH WooCommerce Badge Management
 */

?>
<div class="tab-container">
	<ul>
		<li><a id="btn-text" href="#tab-text"><?php esc_html_e( 'Text Badge', 'yith-woocommerce-badges-management' ); ?></a></li>
		<li><a id="btn-image" href="#tab-image"><?php esc_html_e( 'Image Badge', 'yith-woocommerce-badges-management' ); ?></a></li>
	</ul>

	<input class="update-preview" type="hidden" value="<?php echo sanitize_key( $type ); ?>" data-type="<?php echo sanitize_key( $type ); ?>" name="_badge_meta[type]" id="yith-wcbm-badge-type">
	<input class="update-preview" type="hidden" value="<?php echo esc_html( $image_url ); ?>" name="_badge_meta[image_url]" id="yith-wcbm-image-url">
	<input id="yith-wcbm-url-for-images" type="hidden" value="<?php echo esc_url_raw( YITH_WCBM_ASSETS_URL . '/images/' ); ?>">

	<div class="half-left">
		<div id="tab-text">
			<div class="section-container">
				<div class="section-title"> <?php esc_html_e( 'Text Options', 'yith-woocommerce-badges-management' ); ?></div>
				<table class="section-table">
					<tr>
						<td class="table-title">
							<label><?php esc_html_e( 'Text', 'yith-woocommerce-badges-management' ); ?></label>
						</td>
						<td class="table-content">
							<input class="update-preview" type="text" value="<?php echo wp_kses_post( $text ); ?>" name="_badge_meta[text]" id="yith-wcbm-text">
						</td>
					</tr>
					<tr>
						<td class="table-title">
							<label><?php esc_html_e( 'Text Color', 'yith-woocommerce-badges-management' ); ?></label>
						</td>
						<td class="table-content">
							<input type="text" class="yith-wcbm-color-picker"
									name="_badge_meta[txt_color]" value="<?php echo esc_html( $txt_color ); ?>"
									data-default-color="<?php echo esc_html( $txt_color_default ); ?>" id="yith-wcbm-txt-color">
						</td>
					</tr>
				</table>
			</div><!-- section-container -->

			<div class="section-container">
				<div class="section-title"> <?php esc_html_e( 'Style Options', 'yith-woocommerce-badges-management' ); ?></div>
				<table class="section-table">
					<tr>
						<td class="table-title">
							<label><?php esc_html_e( 'Background Color', 'yith-woocommerce-badges-management' ); ?></label>
						</td>
						<td class="table-content">
							<input type="text" class="yith-wcbm-color-picker" name="_badge_meta[bg_color]" value="<?php echo esc_html( $bg_color ); ?>"
									data-default-color="<?php echo esc_html( $bg_color_default ); ?>" id="yith-wcbm-bg-color">
						</td>
					</tr>
					<tr>
						<td class="table-title table-align-top">
							<label><?php esc_html_e( 'Size (pixel)', 'yith-woocommerce-badges-management' ); ?></label><br/>
						</td>
						<td class="table-content">
							<table class="table-mini-title">
								<tr>
									<td>
										<input class="update-preview" type="text" size="4" value="<?php echo esc_html( $width ); ?>" name="_badge_meta[width]" id="yith-wcbm-width">
									</td>
									<td>
										<input class="update-preview" type="text" size="4" value="<?php echo esc_html( $height ); ?>" name="_badge_meta[height]" id="yith-wcbm-height">
									</td>
								</tr>
								<tr>
									<th><?php esc_html_e( 'Width', 'yith-woocommerce-badges-management' ); ?></th>
									<th><?php esc_html_e( 'Height', 'yith-woocommerce-badges-management' ); ?></th>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div><!-- section-container -->
		</div><!-- tab-text -->

		<div id="tab-image">
			<div class="section-container">
				<div class="section-title"> <?php esc_html_e( 'Select the Image Badge', 'yith-woocommerce-badges-management' ); ?></div>
				<div class="section-content-container">
					<?php for ( $i = 1; $i < 5; $i ++ ) : ?>
						<?php
						$img_url = YITH_WCBM_ASSETS_URL . '/images/' . $i . '.png';
						$img     = $i . '.png';
						?>
						<div class='yith-wcbm-select-image-btn button-select-image' data-badge_image_url='<?php echo esc_html( $img ); ?>' style='background-image:url(<?php echo esc_url_raw( $img_url ); ?>)'></div>
					<?php endfor; ?>
					<div id='custom-image-badges'></div>

				</div> <!-- section-content-container -->
			</div> <!-- section-container -->
		</div>

		<div class="section-container">
			<div class="section-title"> <?php esc_html_e( 'Position', 'yith-woocommerce-badges-management' ); ?></div>
			<table class="section-table">
				<tr>
					<td class="table-title">
						<label><?php esc_html_e( 'Position', 'yith-woocommerce-badges-management' ); ?></label>
					</td>
					<td class="table-content">
						<select class="update-preview" name="_badge_meta[position]" id="yith-wcbm-position">
							<option value="top-left" <?php echo selected( $position, 'top-left', false ); ?>><?php esc_html_e( 'top-left', 'yith-woocommerce-badges-management' ); ?></option>
							<option value="top-right" <?php echo selected( $position, 'top-right', false ); ?>><?php esc_html_e( 'top-right', 'yith-woocommerce-badges-management' ); ?></option>
							<option value="bottom-left" <?php echo selected( $position, 'bottom-left', false ); ?>><?php esc_html_e( 'bottom-left', 'yith-woocommerce-badges-management' ); ?></option>
							<option value="bottom-right" <?php echo selected( $position, 'bottom-right', false ); ?>><?php esc_html_e( 'bottom-right', 'yith-woocommerce-badges-management' ); ?></option>
						</select>
					</td>
				</tr>
			</table>
		</div><!-- section-container -->
	</div>

	<div class="half-right">
		<h3 id="preview-title"><?php esc_html_e( 'Preview', 'yith-woocommerce-badges-management' ); ?></h3>
		<div id="preview-bg">
			<div id="preview-badge">
			</div>
		</div>
	</div>
</div>
