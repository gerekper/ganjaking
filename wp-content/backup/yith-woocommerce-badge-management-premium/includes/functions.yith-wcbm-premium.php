<?php
/**
 * Functions Premium
 *
 * @author  Yithemes
 * @package YITH WooCommerce Badge Management
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCBM' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Print the content of metabox options [PREMIUM]
 *
 * @return   void
 * @since    1.0
 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
 */
if ( ! function_exists( 'yith_wcbm_metabox_options_content_premium' ) ) {
	function yith_wcbm_metabox_options_content_premium( $args ) {
		extract( $args );
		$rotation = is_array( $rotation ) ? $rotation : array( 'x' => 0, 'y' => 0, 'z' => 0 );
		?>
		<div class="tab-container">
			<ul>
				<li>
					<a id="btn-text" href="#tab-text"><?php echo __( 'Text Badge', 'yith-woocommerce-badges-management' ) ?></a>
				</li>
				<li>
					<a id="btn-css" href="#tab-css"><?php echo __( 'CSS Badge', 'yith-woocommerce-badges-management' ) ?></a>
				</li>
				<li>
					<a id="btn-image" href="#tab-image"><?php echo __( 'Image Badge', 'yith-woocommerce-badges-management' ) ?></a>
				</li>
				<li>
					<a id="btn-advanced" href="#tab-advanced"><?php echo __( 'Advanced Badge', 'yith-woocommerce-badges-management' ) ?></a>
				</li>
			</ul>
			<?php
			//if the badge was created by free version
			if ( strlen( $image_url ) > 0 && strlen( $image_url ) < 6 ) {
				$image_url = YITH_WCBM_ASSETS_URL . '/images/image-badge/' . $image_url;
			}
			?>
			<input class="update-preview" type="hidden" value="<?php echo $type ?>" data-type="<?php echo $type ?>" name="_badge_meta[type]" id="yith-wcbm-badge-type">
			<input class="update-preview" type="hidden" value="<?php echo $image_url ?>" name="_badge_meta[image_url]" id="yith-wcbm-image-url">
			<input class="update-preview" type="hidden" value="<?php echo $advanced_badge ?>" name="_badge_meta[advanced_badge]" id="yith-wcbm-advanced-badge">
			<input class="update-preview" type="hidden" value="<?php echo $css_badge ?>" name="_badge_meta[css_badge]" id="yith-wcbm-css-badge">

			<div class="half-left">
				<div id="tab-text">
					<div class="section-container">
						<div class="section-title"> <?php echo __( 'Text Options', 'yith-woocommerce-badges-management' ) ?></div>
						<table class="section-table">
							<tr>
								<td class="table-title">
									<label><?php echo __( 'Text', 'yith-woocommerce-badges-management' ) ?></label>
								</td>
								<td class="table-content">
									<input class="update-preview" type="text" value="<?php echo $text ?>" name="_badge_meta[text]" id="yith-wcbm-text">
								</td>
							</tr>
							<tr>
								<td class="table-title">
									<label><?php echo __( 'Text Color', 'yith-woocommerce-badges-management' ) ?></label>
								</td>
								<td class="table-content">
									<input type="text" class="yith-wcbm-color-picker" name="_badge_meta[txt_color]" value="<?php echo $txt_color ?>"
											data-default-color="<?php echo $txt_color_default; ?>" id="yith-wcbm-txt-color">
								</td>
							</tr>
							<tr>
								<td class="table-title">
									<label><?php echo __( 'Font Size (pixel)', 'yith-woocommerce-badges-management' ) ?></label>
								</td>
								<td class="table-content">
									<input class="update-preview" type="text" value="<?php echo $font_size ?>" name="_badge_meta[font_size]" id="yith-wcbm-font-size">
								</td>
							</tr>
							<tr>
								<td class="table-title table-align-top">
									<label><?php echo __( 'Line Height (pixel)', 'yith-woocommerce-badges-management' ) ?></label>
								</td>
								<td class="table-content">
									<input class="update-preview" type="text" value="<?php echo $line_height ?>" name="_badge_meta[line_height]" id="yith-wcbm-line-height">

									<div class="table-description"><?php echo __( '[set -1 to set it equal to height of the badge]', 'yith-woocommerce-badges-management' ) ?></div>
								</td>
							</tr>
						</table>
					</div>
					<!-- section-container -->

					<div class="section-container">
						<div class="section-title"> <?php echo __( 'Style Options', 'yith-woocommerce-badges-management' ) ?></div>
						<table class="section-table">
							<tr>
								<td class="table-title">
									<label><?php echo __( 'Background Color', 'yith-woocommerce-badges-management' ) ?></label>
								</td>
								<td class="table-content">
									<input type="text" class="yith-wcbm-color-picker" name="_badge_meta[bg_color]" value="<?php echo $bg_color ?>"
											data-default-color="<?php echo $bg_color_default; ?>" id="yith-wcbm-bg-color">
								</td>
							</tr>
							<tr>
								<td class="table-title table-align-top">
									<label><?php echo __( 'Size (pixel)', 'yith-woocommerce-badges-management' ) ?></label><br/>
								</td>
								<td class="table-content">
									<table class="table-mini-title">
										<tr>
											<td>
												<input class="update-preview" type="text" size="4" value="<?php echo $width ?>" name="_badge_meta[width]" id="yith-wcbm-width">
											</td>
											<td>
												<input class="update-preview" type="text" size="4" value="<?php echo $height ?>" name="_badge_meta[height]" id="yith-wcbm-height">
											</td>
										</tr>
										<tr>
											<th>
												<?php echo __( 'Width', 'yith-woocommerce-badges-management' ) ?>
											</th>
											<th>
												<?php echo __( 'Height', 'yith-woocommerce-badges-management' ) ?>
											</th>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class="table-title">
									<label><?php echo __( 'Border Radius (pixel)', 'yith-woocommerce-badges-management' ) ?></label>
								</td>
								<td class="table-content">
									<table class="table-four-colums table-mini-title">
										<tr>
											<td>
												<input class="update-preview" type="text" size="4" value="<?php echo $border_top_left_radius ?>" name="_badge_meta[border_top_left_radius]"
														id="yith-wcbm-border-top-left-radius"></td>
											<td>
												<input class="update-preview" type="text" size="4" value="<?php echo $border_top_right_radius ?>" name="_badge_meta[border_top_right_radius]"
														id="yith-wcbm-border-top-right-radius"></td>
											<td>
												<input class="update-preview" type="text" size="4" value="<?php echo $border_bottom_right_radius ?>"
														name="_badge_meta[border_bottom_right_radius]"
														id="yith-wcbm-border-bottom-right-radius"></td>
											<td>
												<input class="update-preview" type="text" size="4" value="<?php echo $border_bottom_left_radius ?>"
														name="_badge_meta[border_bottom_left_radius]"
														id="yith-wcbm-border-bottom-left-radius"></td>
										</tr>
										<tr>
											<th>Top Left</th>
											<th>Top Right</th>
											<th>Bottom Right</th>
											<th>Bottom Left</th>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class="table-title">
									<label><?php echo __( 'Padding (pixel)', 'yith-woocommerce-badges-management' ) ?></label>
								</td>
								<td class="table-content">
									<table class="table-four-colums table-mini-title">
										<tr>
											<td>
												<input class="update-preview" type="text" size="4" value="<?php echo $padding_top ?>" name="_badge_meta[padding_top]"
														id="yith-wcbm-padding-top"></td>
											<td>
												<input class="update-preview" type="text" size="4" value="<?php echo $padding_right ?>" name="_badge_meta[padding_right]"
														id="yith-wcbm-padding-right"></td>
											<td>
												<input class="update-preview" type="text" size="4" value="<?php echo $padding_bottom ?>" name="_badge_meta[padding_bottom]"
														id="yith-wcbm-padding-bottom">
											</td>
											<td>
												<input class="update-preview" type="text" size="4" value="<?php echo $padding_left ?>" name="_badge_meta[padding_left]"
														id="yith-wcbm-padding-left"></td>
										</tr>
										<tr>
											<th>Top</th>
											<th>Right</th>
											<th>Bottom</th>
											<th>Left</th>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
					<!-- section-container -->
				</div>
				<!-- tab-text -->
				<div id="tab-css">
					<div class="section-container">
						<div class="section-title"> <?php echo __( 'Select the CSS Badge', 'yith-woocommerce-badges-management' ) ?></div>
						<div class="section-content-container">
							<?php
							for ( $i = 1; $i < 9; $i ++ ) {
								$img_url = YITH_WCBM_ASSETS_URL . '/images/css-badge/' . $i . '.png';
								echo '<div class="yith-wcbm-select-image-btn button-select-css" badge_css_index="' . $i . '" style="background-image:url(' . $img_url . ')">';
								echo '</div>';
							}
							?>
						</div>
						<!-- section-content-container -->
					</div>
					<!-- section-container -->

					<div class="section-container">
						<div class="section-title"> <?php echo __( 'CSS Options', 'yith-woocommerce-badges-management' ) ?></div>
						<table class="section-table">
							<tr>
								<td class="table-title">
									<label><?php echo __( 'Text', 'yith-woocommerce-badges-management' ) ?></label>
								</td>
								<td class="table-content">
									<input class="update-preview" type="text" value="<?php echo $css_text ?>" name="_badge_meta[css_text]" id="yith-wcbm-css-text">
								</td>
							</tr>
							<tr>
								<td class="table-title">
									<label><?php echo __( 'Badge Color', 'yith-woocommerce-badges-management' ) ?></label>
								</td>
								<td class="table-content">
									<input type="text" class="yith-wcbm-color-picker" name="_badge_meta[css_bg_color]" value="<?php echo $css_bg_color ?>"
											data-default-color="<?php echo $css_bg_color_default; ?>" id="yith-wcbm-css-bg-color">
								</td>
							</tr>
							<tr>
								<td class="table-title">
									<label><?php echo __( 'Text Color', 'yith-woocommerce-badges-management' ) ?></label>
								</td>
								<td class="table-content">
									<input type="text" class="yith-wcbm-color-picker" name="_badge_meta[css_text_color]" value="<?php echo $css_text_color ?>"
											data-default-color="<?php echo $css_text_color_default; ?>" id="yith-wcbm-css-text-color">
								</td>
							</tr>
						</table>
					</div>
					<!-- section-container -->

				</div>
				<div id="tab-image">
					<div class="section-container">
						<div class="section-title"> <?php echo __( 'Select the Image Badge', 'yith-woocommerce-badges-management' ) ?></div>
						<div class="section-content-container">
							<?php
							for ( $i = 1; $i < 10; $i ++ ) {
								$img_url = YITH_WCBM_ASSETS_URL . '/images/image-badge/' . $i . '.png';
								echo '<div class="yith-wcbm-select-image-btn button-select-image" badge_image_url="' . $img_url . '" style="background-image:url(' . $img_url . ')">';
								echo '</div>';
							}

							// Custom Image Badge Uploaded
							echo "<div id='custom-image-badges'>";
							echo "</div>";
							?>
						</div>
						<!-- section-content-container -->
					</div>
					<!-- section-container -->

					<div class="section-container">
						<div class="section-title"> <?php echo __( 'Upload', 'yith-woocommerce-badges-management' ) ?></div>
						<div class="section-content-container">
							<?php yith_wcbm_insert_image_uploader(); ?>
						</div>
						<!-- section-content-container -->
					</div>
					<!-- section-container -->

				</div>
				<div id="tab-advanced">
					<div class="section-container">
						<div class="section-title"> <?php echo __( 'Select the Advanced Badge', 'yith-woocommerce-badges-management' ) ?></div>
						<div class="section-content-container">
							<?php
							for ( $i = 1; $i < 11; $i ++ ) {
								$img_url = YITH_WCBM_ASSETS_URL . '/images/advanced-sale/' . $i . '.png';
								echo '<div class="yith-wcbm-select-image-btn button-select-advanced" badge_advanced_index="' . $i . '" style="background-image:url(' . $img_url . ')">';
								echo '</div>';
							}
							?>
						</div>
						<!-- section-content-container -->
					</div>
					<!-- section-container -->

					<div class="section-container">
						<div class="section-title"> <?php echo __( 'Advanced Options', 'yith-woocommerce-badges-management' ) ?></div>
						<table class="section-table" id="yith-wcbm-advanced-options">
							<tr class="yith-wcbm-advanced-colors">
								<td class="table-title">
									<label><?php echo __( 'Badge Color', 'yith-woocommerce-badges-management' ) ?></label>
								</td>
								<td class="table-content">
									<input type="text" class="yith-wcbm-color-picker" name="_badge_meta[advanced_bg_color]" value="<?php echo $advanced_bg_color ?>"
											data-default-color="<?php echo $advanced_bg_color_default; ?>" id="yith-wcbm-advanced-bg-color">
								</td>
							</tr>
							<tr class="yith-wcbm-advanced-colors">
								<td class="table-title">
									<label><?php echo __( 'Text Color', 'yith-woocommerce-badges-management' ) ?></label>
								</td>
								<td class="table-content">
									<input type="text" class="yith-wcbm-color-picker" name="_badge_meta[advanced_text_color]" value="<?php echo $advanced_text_color ?>"
											data-default-color="<?php echo $advanced_text_color_default; ?>" id="yith-wcbm-advanced-text-color">
								</td>
							</tr>
							<tr id="yith-wcbm-advanced-display">
								<td class="table-title">
									<label><?php echo __( 'Display', 'yith-woocommerce-badges-management' ) ?></label>
								</td>
								<td class="table-content">
									<input type="radio" id="yith-wcbm-advanced-display-percentage" name="_badge_meta[advanced_display]"
											value="percentage" <?php checked( $advanced_display === 'percentage', true, true ); ?> >
									<label for="yith-wcbm-advanced-display-percentage"><?php _e( 'Percentage', 'yith-woocommerce-badges-management' ); ?></label>
									<br/>
									<input type="radio" id="yith-wcbm-advanced-display-amount" name="_badge_meta[advanced_display]"
											value="amount" <?php checked( $advanced_display === 'amount', true, true ); ?>>
									<label for="yith-wcbm-advanced-display-amount"><?php _e( 'Amount', 'yith-woocommerce-badges-management' ); ?></label>
								</td>
							</tr>
						</table>
					</div>
					<!-- section-container -->

				</div>

				<div class="section-container">
					<div class="section-title"> <?php echo __( 'Opacity and position', 'yith-woocommerce-badges-management' ) ?></div>
					<table class="section-table">
						<tr>
							<td class="table-title">
								<label><?php echo __( 'Opacity', 'yith-woocommerce-badges-management' ) ?></label><br/>
							</td>
							<td class="table-content">
								<div style="width:100%; height:30px;">
									<input class="update-preview yith-wcbm-range-input" type="range" min="0" max="100" step="1" value="<?php echo $opacity ?>" name="_badge_meta[opacity]" id="yith-wcbm-opacity"
											oninput=";">

									<div id="output-opacity" class="yith-wcbm-range-output"><?php echo $opacity ?></div>
								</div>
								<div class="table-description"><?php echo __( '[0:transparent | 100:opaque]', 'yith-woocommerce-badges-management' ) ?></div>
							</td>
						</tr>
						<tr>
							<td class="table-title">
								<label><?php echo __( 'Rotation', 'yith-woocommerce-badges-management' ) ?></label><br/>
							</td>
							<td class="table-content">
								<table id="yith-wcbm-3d-rotation-table" class="table-mini-title">
									<tr>
										<td>
											<div style="width:100%; height:30px;">
												<input class="update-preview yith-wcbm-range-input" type="range" min="0" max="360" step="1" value="<?php echo $rotation['x'] ?>" name="_badge_meta[rotation][x]" id="yith-wcbm-rotation-x"
														oninput=";">

												<div id="output-rotation-x" class="yith-wcbm-range-output"><?php echo $rotation['x'] ?></div>
											</div>
										</td>
										<td>
											<div style="width:100%; height:30px;">
												<input class="update-preview yith-wcbm-range-input" type="range" min="0" max="360" step="1" value="<?php echo $rotation['y'] ?>" name="_badge_meta[rotation][y]" id="yith-wcbm-rotation-y"
														oninput=";">

												<div id="output-rotation-y" class="yith-wcbm-range-output"><?php echo $rotation['y'] ?></div>
											</div>
										</td>
										<td>
											<div style="width:100%; height:30px;">
												<input class="update-preview yith-wcbm-range-input" type="range" min="0" max="360" step="1" value="<?php echo $rotation['z'] ?>" name="_badge_meta[rotation][z]" id="yith-wcbm-rotation-z"
														oninput=";">

												<div id="output-rotation-x" class="yith-wcbm-range-output"><?php echo $rotation['z'] ?></div>
											</div>
										</td>
										<td>
											<span id="yith-wcbm-rotation-mode-change" data-value="slider"><?php _e( 'Number', 'yith-woocommerce-badges-management' ) ?></span>
										</td>
									</tr>
									<tr>
										<th><?php _e( 'X', 'yith-woocommerce-badges-management' ) ?></th>
										<th><?php _e( 'Y', 'yith-woocommerce-badges-management' ) ?></th>
										<th><?php _e( 'Z', 'yith-woocommerce-badges-management' ) ?></th>
										<th><?php _e( 'SET MODE', 'yith-woocommerce-badges-management' ) ?></th>
									</tr>
								</table>

							</td>
						</tr>
						<tr>
							<td class="table-title">
								<label><?php echo __( 'Flip Text', 'yith-woocommerce-badges-management' ) ?></label><br/>
							</td>
							<td class="table-content">
								<table style="width:100%; max-width:300px">
									<tr>
										<td>
											<input class="update-preview" type="checkbox" <?php checked( $flip_text_horizontally ) ?> name="_badge_meta[flip_text_horizontally]" id="yith-wcbm-flip-text-horizontally">
											<label for="yith-wcbm-flip-text-horizontally"><?php _e( 'Horizontally', 'yith-woocommerce-badges-management' ) ?></label>
										</td>
										<td>
											<input class="update-preview" type="checkbox" <?php checked( $flip_text_vertically ) ?> name="_badge_meta[flip_text_vertically]" id="yith-wcbm-flip-text-vertically">
											<label for="yith-wcbm-flip-text-vertically"><?php _e( 'Vertically', 'yith-woocommerce-badges-management' ) ?></label>
										</td>
									</tr>
								</table>

							</td>
						</tr>
						<tr>
							<td class="table-title">
								<label><?php echo __( 'Anchor Point', 'yith-woocommerce-badges-management' ) ?></label>
							</td>
							<td class="table-content">
								<select class="update-preview" name="_badge_meta[position]" id="yith-wcbm-position">
									<option
											value="top-left" <?php echo selected( $position, 'top-left', false ) ?>><?php echo __( 'top-left', 'yith-woocommerce-badges-management' ) ?></option>
									;
									<option
											value="top-right" <?php echo selected( $position, 'top-right', false ) ?>><?php echo __( 'top-right', 'yith-woocommerce-badges-management' ) ?></option>
									;
									<option
											value="bottom-left" <?php echo selected( $position, 'bottom-left', false ) ?>><?php echo __( 'bottom-left', 'yith-woocommerce-badges-management' ) ?></option>
									;
									<option
											value="bottom-right" <?php echo selected( $position, 'bottom-right', false ) ?>><?php echo __( 'bottom-right', 'yith-woocommerce-badges-management' ) ?></option>
									;
								</select>

								<div class="table-description"><?php echo __( '[for Drag and Drop positioning]', 'yith-woocommerce-badges-management' ) ?></div>
							</td>
						</tr>
						<tr>
							<td class="table-title">
								<label><?php echo __( 'Position (pixel or percentual)', 'yith-woocommerce-badges-management' ) ?></label><br/>
							</td>
							<td class="table-content">
								<table class="table-four-colums table-mini-title">
									<tr>
										<td>
											<input class="update-preview" type="text" size="4" value="<?php echo $pos_top ?>" name="_badge_meta[pos_top]" id="yith-wcbm-pos-top">
										</td>
										<td>
											<input class="update-preview" type="text" size="4" value="<?php echo $pos_bottom ?>" name="_badge_meta[pos_bottom]" id="yith-wcbm-pos-bottom">
										</td>
										<td>
											<input class="update-preview" type="text" size="4" value="<?php echo $pos_left ?>" name="_badge_meta[pos_left]" id="yith-wcbm-pos-left">
										</td>
										<td>
											<input class="update-preview" type="text" size="4" value="<?php echo $pos_right ?>" name="_badge_meta[pos_right]" id="yith-wcbm-pos-right">
										</td>
									</tr>
									<tr>
										<th><?php echo __( 'Top', 'yith-woocommerce-badges-management' ); ?></th>
										<th><?php echo __( 'Bottom', 'yith-woocommerce-badges-management' ); ?></th>
										<th><?php echo __( 'Left', 'yith-woocommerce-badges-management' ); ?></th>
										<th><?php echo __( 'Right', 'yith-woocommerce-badges-management' ); ?></th>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="table-title">
								<label><?php echo __( 'Center Positioning', 'yith-woocommerce-badges-management' ) ?></label><br/>
							</td>
							<td class="table-content">
								<table class="table-four-colums table-mini-title">
									<tr>
										<td>
											<img id="yith-wcbm-pos-top-center" width="30px" src="<?php echo YITH_WCBM_ASSETS_URL . '/images/icons/top-center.png'; ?>"/>
										</td>
										<td>
											<img id="yith-wcbm-pos-bottom-center" width="30px" src="<?php echo YITH_WCBM_ASSETS_URL . '/images/icons/bottom-center.png'; ?>"/>
										</td>
										<td>
											<img id="yith-wcbm-pos-left-center" width="30px" src="<?php echo YITH_WCBM_ASSETS_URL . '/images/icons/left-center.png'; ?>"/>
										</td>
										<td>
											<img id="yith-wcbm-pos-right-center" width="30px" src="<?php echo YITH_WCBM_ASSETS_URL . '/images/icons/right-center.png'; ?>"/>
										</td>
										<td>
											<img id="yith-wcbm-pos-center" width="30px" src="<?php echo YITH_WCBM_ASSETS_URL . '/images/icons/center.png'; ?>"/>
										</td>
									</tr>
									<tr>
										<th><?php echo __( 'Top', 'yith-woocommerce-badges-management' ); ?></th>
										<th><?php echo __( 'Bottom', 'yith-woocommerce-badges-management' ); ?></th>
										<th><?php echo __( 'Left', 'yith-woocommerce-badges-management' ); ?></th>
										<th><?php echo __( 'Right', 'yith-woocommerce-badges-management' ); ?></th>
										<th><?php echo __( 'Center', 'yith-woocommerce-badges-management' ); ?></th>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="table-title">
								<label for="yith-wcbm-scale-on-mobile"><?php echo __( 'Scale (on mobile)', 'yith-woocommerce-badges-management' ) ?></label><br/>
							</td>
							<td class="table-content">
								<input type="number" name="_badge_meta[scale_on_mobile]" id="yith-wcbm-scale-on-mobile" min="0" step="0.1" value="<?php echo $scale_on_mobile ?>">
							</td>
						</tr>
					</table>
				</div>
				<!-- section-container -->
			</div>


			<div class="half-right">
				<h3 id="preview-title"> <?php echo __( 'Preview', 'yith-woocommerce-badges-management' ) ?> </h3>

				<div id="preview-desc"> <?php echo __( 'Use Drag and Drop for positioning', 'yith-woocommerce-badges-management' ) ?> </div>
				<div id="preview-bg">
					<div id="preview-style-badge">
					</div>
					<div id="preview-badge">
					</div>
					<div id="preview-loader">
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

/**
 * Insert Uploader button
 *
 * @return   string
 * @since    1.0
 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
 */
if ( ! function_exists( 'yith_wcbm_insert_image_uploader' ) ) {
	function yith_wcbm_insert_image_uploader() {
		wp_enqueue_script( 'jquery' );
		// This will enqueue the Media Uploader script
		wp_enqueue_media();
		?>
		<div class="uploader_sect">
			<label for="image_url"><?php echo __( 'Upload Custom Image', 'yith-woocommerce-badges-management' ) ?></label>
			<input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="<?php echo __( 'Upload', 'yith-woocommerce-badges-management' ) ?>">
		</div>
		<?php
	}
}

if ( ! function_exists( 'yith_wcmb_is_wpml_parent_based_on_default_language' ) ) {
	function yith_wcmb_is_wpml_parent_based_on_default_language() {
		return apply_filters( 'yith_wcmb_is_wpml_parent_based_on_default_language', false );
	}
}

if ( ! function_exists( 'yith_wcmb_wpml_autosync_product_badge_translations' ) ) {
	function yith_wcmb_wpml_autosync_product_badge_translations() {
		return apply_filters( 'yith_wcmb_wpml_autosync_product_badge_translations', false );
	}
}

if ( ! function_exists( 'yith_wcbm_get_terms_in_default_language' ) ) {
	function yith_wcbm_get_terms_in_default_language( $post_id, $taxonomy ) {
		global $sitepress;
		if ( $sitepress ) {
			$current_language = is_admin() ? $sitepress->get_admin_language() : $sitepress->get_current_language();
			$default_language = $sitepress->get_default_language();
			$change_language  = $current_language !== $default_language;

			if ( $change_language ) {
				$sitepress->switch_lang( $default_language );
			}

			$terms = get_the_terms( $post_id, $taxonomy );

			if ( $change_language ) {
				$sitepress->switch_lang( $current_language );
			}
		} else {
			$terms = get_the_terms( $post_id, $taxonomy );
		}

		return $terms;
	}
}

/**
 * has the product some badges?
 *
 * @param WC_Product $product
 *
 * @return bool
 * @since 1.3.26
 */
function yith_wcbm_product_has_badges( $product ) {
	return ! ! yith_wcbm_get_product_badges( $product );
}

/**
 * Get the badge ids for a specific product
 *
 * @param WC_Product $product
 *
 * @return array
 * @since 1.3.26
 */
function yith_wcbm_get_product_badges( $product ) {
	$product = wc_get_product( $product );
	$badges  = array();

	if ( $product && $product->is_type( 'variation' ) ) {
		// prevent issues with some themes that prints each variation directly in Shop page
		$product = wc_get_product( $product->get_parent_id() );
	}

	if ( $product ) {
		$product_id = $product->get_id();

		// Category
		if ( yith_wcmb_is_wpml_parent_based_on_default_language() ) {
			$prod_cats = yith_wcbm_get_terms_in_default_language( $product_id, 'product_cat' );
		} else {
			$prod_cats = get_the_terms( $product_id, 'product_cat' );
		}

		if ( ! empty( $prod_cats ) ) {
			foreach ( $prod_cats as $prod_cat ) {
				$cat_id    = $prod_cat->term_id;
				$cat_badge = get_option( 'yith-wcbm-category-badge-' . $cat_id );
				if ( ! empty( $cat_badge ) && $cat_badge != 'none' ) {
					$badges[] = $cat_badge;
				}
			}
		}

		// Shipping Class
		if ( yith_wcmb_is_wpml_parent_based_on_default_language() ) {
			$shipping_classes = yith_wcbm_get_terms_in_default_language( $product_id, 'product_shipping_class' );
		} else {
			$shipping_classes = get_the_terms( $product_id, 'product_shipping_class' );
		}
		if ( $shipping_classes && ! is_wp_error( $shipping_classes ) ) {
			$current_shipping_class_id = current( $shipping_classes )->term_id;
			$shipping_class_badge      = get_option( 'yith-wcbm-shipping-class-badge-' . $current_shipping_class_id );
			if ( ! empty( $shipping_class_badge ) && $shipping_class_badge != 'none' ) {
				$badges[] = $shipping_class_badge;
			}
		}

		// Recent Badge
		$newness         = get_option( 'yith-wcbm-badge-newer-than' );
		$recent_badge_id = get_option( 'yith-wcbm-recent-products-badge' );
		if ( $newness > 0 && ! empty( $recent_badge_id ) && $recent_badge_id != 'none' ) {
			$postdate      = get_the_time( 'Y-m-d', $product_id );
			$postdatestamp = strtotime( $postdate );
			if ( ( time() - ( 60 * 60 * 24 * $newness ) ) < $postdatestamp ) {
				$badges[] = $recent_badge_id;
			}
		}

		// Featured
		$featured_badge = get_option( 'yith-wcbm-featured-badge' );
		if ( ! empty( $featured_badge ) && $featured_badge != 'none' ) {
			$show_featured_badge = apply_filters( 'yith_wcbm_show_featured_badge_on_product', $product->is_featured(), $product );
			if ( $show_featured_badge ) {
				$badges[] = $featured_badge;
			}
		}

		// On sale && Advanced on sale
		$on_sale_badge      = get_option( 'yith-wcbm-on-sale-badge' );
		$product_is_on_sale = yith_wcbm_product_is_on_sale( $product );
		$show_on_sale_badge = apply_filters( 'yith_wcbm_show_on_sale_badge_on_product', $product_is_on_sale, $product );
		if ( ! empty( $on_sale_badge ) && $on_sale_badge != 'none' && $show_on_sale_badge ) {
			$badges[] = $on_sale_badge;
		}

		// Out of stock
		$out_of_stock_badge      = get_option( 'yith-wcbm-out-of-stock-badge' );
		$show_out_of_stock_badge = apply_filters( 'yith_wcbm_show_out_of_stock_badge_on_product', ! $product->is_in_stock(), $product );
		if ( ! empty( $out_of_stock_badge ) && $out_of_stock_badge != 'none' && $show_out_of_stock_badge ) {
			$badges[] = $out_of_stock_badge;
		}

		// Low stock
		$low_stock_badge      = get_option( 'yith-wcbm-low-stock-badge' );
		$low_stock_qty        = get_option( 'yith-wcbm-low-stock-qty', '3' );
		$show_low_stock_badge = apply_filters( 'yith_wcbm_show_low_stock_badge_on_product', $product->managing_stock() && $product->is_in_stock() && $product->get_stock_quantity() <= $low_stock_qty, $product );
		if ( ! empty( $low_stock_badge ) && $low_stock_badge != 'none' && $show_low_stock_badge ) {
			$badges[] = $low_stock_badge;
		}

		// Product Badge
		$badge_info            = yith_wcbm_get_product_badge_info( $product );
		$badge_ids             = $badge_info['badge_ids'];
		$start_date            = $badge_info['start_date'];
		$end_date              = $badge_info['end_date'];
		$product_badge_visible = true;

		if ( $badge_ids ) {
			$today_midnight = strtotime( 'midnight', current_time( 'timestamp', false ) );

			// check for Start Date
			if ( ! empty( $start_date ) && strtotime( $start_date ) > $today_midnight ) {
				$product_badge_visible = false;
			}
			// check for End Date
			if ( ! empty( $end_date ) && strtotime( $end_date ) < $today_midnight ) {
				$product_badge_visible = false;
			}

			if ( $product_badge_visible ) {
				foreach ( $badge_ids as $badge_id ) {
					$badges[] = $badge_id;
				}
			}
		}

		$badges = array_unique( array_filter( array_map( 'absint', $badges ) ) );
	}

	return $badges;
}

/**
 * Print all badges for product in frontend
 *
 * @return   string
 * @since    1.0
 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
 */
if ( ! function_exists( 'yith_wcbm_get_badges_premium' ) ) {
	function yith_wcbm_get_badges_premium( $product, $deprecated = null ) {
		if ( func_num_args() > 1 ) {
			$product = $deprecated;
		}
		$product = wc_get_product( $product );

		if ( $product && $product->is_type( 'variation' ) ) {
			// prevent issues with some themes that prints each variation directly in Shop page
			$product = wc_get_product( $product->get_parent_id() );
		}

		if ( ! $product ) {
			return '';
		}

		$product_id = $product->get_id();

		$badges_html    = '';
		$badges_to_show = yith_wcbm_get_product_badges( $product );
		$badges_to_show = apply_filters( 'yith_wcmb_badges_to_show_on_product', $badges_to_show, $product );

		foreach ( $badges_to_show as $badge_id ) {
			$badges_html .= yith_wcbm_get_badge_premium( $badge_id, $product_id );
		}

		/**
		 * TODO: remove deprecated argument. It was leaved to prevent issue if someone used this filter with 3 args
		 * */
		$deprecated_arg = 0;

		return apply_filters( 'yith_wcmb_get_badges_premium', $badges_html, $product, $deprecated_arg );
	}
}

/**
 * Print the content of badge in frontend
 *
 * @return   string
 * @since    1.0
 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
 */

if ( ! function_exists( 'yith_wcbm_get_badge_premium' ) ) {
	function yith_wcbm_get_badge_premium( $id_badge, $product_id ) {
		$id_badge = yith_wcbm_wpml_translate_badge_id( $id_badge );
		$badge    = '';

		if ( $id_badge && $product_id && 'publish' === get_post_status( $id_badge ) ) {
			$bm_meta = get_post_meta( $id_badge, '_badge_meta', true );

			$default = array(
				'type'                        => 'text',
				'text'                        => '',
				'txt_color_default'           => '#000000',
				'txt_color'                   => '#000000',
				'bg_color_default'            => '#2470FF',
				'bg_color'                    => '#2470FF',
				'advanced_bg_color'           => '',
				'advanced_bg_color_default'   => '',
				'advanced_text_color'         => '',
				'advanced_text_color_default' => '',
				'advanced_badge'              => 1,
				'advanced_display'            => 'percentage',
				'css_badge'                   => 1,
				'css_bg_color'                => '',
				'css_bg_color_default'        => '',
				'css_text_color'              => '',
				'css_text_color_default'      => '',
				'css_text'                    => '',
				'width'                       => '100',
				'height'                      => '50',
				'position'                    => 'top-left',
				'image_url'                   => '',
				'pos_top'                     => 0,
				'pos_bottom'                  => 0,
				'pos_left'                    => 0,
				'pos_right'                   => 0,
				'border_top_left_radius'      => 0,
				'border_top_right_radius'     => 0,
				'border_bottom_right_radius'  => 0,
				'border_bottom_left_radius'   => 0,
				'padding_top'                 => 0,
				'padding_bottom'              => 0,
				'padding_left'                => 0,
				'padding_right'               => 0,
				'font_size'                   => 13,
				'line_height'                 => - 1,
				'opacity'                     => 100,
				'product_id'                  => $product_id,
				'id_badge'                    => $id_badge,
			);

			if ( ! isset( $bm_meta['pos_top'] ) ) {
				$position = isset( $bm_meta['position'] ) ? $bm_meta['position'] : 'top-left';
				if ( $position == 'top-right' ) {
					$default['pos_bottom'] = 'auto';
					$default['pos_left']   = 'auto';
				} else if ( $position == 'bottom-left' ) {
					$default['pos_top']   = 'auto';
					$default['pos_right'] = 'auto';
				} else if ( $position == 'bottom-right' ) {
					$default['pos_top']  = 'auto';
					$default['pos_left'] = 'auto';
				} else {
					$default['pos_bottom'] = 'auto';
					$default['pos_right']  = 'auto';
				}
			}

			$args = wp_parse_args( $bm_meta, $default );
			$args = apply_filters( 'yith_wcbm_badge_content_args', $args );

			ob_start();
			yith_wcbm_get_template( 'badge_content_premium.php', $args );
			$badge = ob_get_clean();
		}

		return apply_filters( 'yith_wcbm_get_badge_premium', $badge, $id_badge, $product_id );
	}
}

if ( ! function_exists( 'yith_wcbm_get_badge_style' ) ) {
	function yith_wcbm_get_badge_style( $args ) {
		// $type, $id_badge_name, $id_badge, $color, $txt_color
		extract( $args );
		include( YITH_WCBM_DIR . 'badge-styles/' . $type . '-badge-styles.php' );
	}
}

if ( ! function_exists( 'yith_wcbm_wpml_translate_badge_id' ) ) {

	function yith_wcbm_wpml_translate_badge_id( $id ) {

		global $sitepress;

		if ( isset( $sitepress ) ) {

			if ( function_exists( 'icl_object_id' ) ) {
				$id = icl_object_id( $id, 'any', true );
			} else if ( function_exists( 'wpml_object_id_filter' ) ) {
				$id = wpml_object_id_filter( $id, 'any', true );
			}
		}

		return $id;
	}
}

if ( ! function_exists( 'yith_wcbm_product_is_on_sale' ) ) {
	function yith_wcbm_product_is_on_sale( $product ) {
		$product_is_on_sale = false;
		if ( $product = wc_get_product( $product ) ) {
			$product_is_on_sale = $product->is_on_sale();

			if ( apply_filters( 'yith_wcbm_product_is_on_sale_based_on_woocommerce', false ) ) {
				return $product_is_on_sale;
			}

			if ( ! $product_is_on_sale && defined( 'YITH_YWDPD_PREMIUM' ) || defined( 'YWCRBP_PREMIUM' ) ) {

				if ( $product->is_type( 'variable' ) ) {
					$children = $product->get_children();

					foreach ( $children as $child_id ) {
						$child = wc_get_product( $child_id );
						if ( ! $child ) {
							continue;
						}

						if ( $child->get_regular_price() > $child->get_price() ) {
							$product_is_on_sale = true;
							break;
						}
					}
				} else {
					$product_is_on_sale = $product->get_regular_price() > $product->get_price();
				}
			}

			$product_is_on_sale = $product_is_on_sale && ! $product->is_type( 'auction' );

			/* check if the price is not empty (catalog mode support) */
			$product_is_on_sale = $product_is_on_sale && ( $product->is_type( 'variable' ) || '' !== $product->get_price() );
		}

		return apply_filters( 'yith_wcbm_product_is_on_sale', $product_is_on_sale, $product );
	}
}

if ( ! function_exists( 'yith_wcbm_get_product_badge_info' ) ) {
	function yith_wcbm_get_product_badge_info( $product ) {
		$info    = array(
			'badge_ids'  => array(),
			'start_date' => '',
			'end_date'   => '',
		);
		$product = wc_get_product( $product );
		if ( $product ) {
			$bm_meta = yit_get_prop( $product, '_yith_wcbm_product_meta', true );
			if ( ! empty( $bm_meta['id_badge'] ) ) {
				$info['badge_ids'] = is_array( $bm_meta['id_badge'] ) ? $bm_meta['id_badge'] : array( $bm_meta['id_badge'] );
			}

			if ( ! empty( $bm_meta['start_date'] ) ) {
				$info['start_date'] = $bm_meta['start_date'];
			}

			if ( ! empty( $bm_meta['end_date'] ) ) {
				$info['end_date'] = $bm_meta['end_date'];
			}
		}

		return $info;
	}
}

if ( ! function_exists( 'yith_wcbm_get_transform_origin_by_positions' ) ) {
	function yith_wcbm_get_transform_origin_by_positions( $top, $right, $bottom, $left ) {
		list( $x, $y ) = array( 'left', 'top' );
		if ( $top === 'auto' ) {
			$y = 'bottom';
		} else if ( $bottom === 'auto' ) {
			$y = 'top';
		}

		if ( $left === 'auto' ) {
			$x = 'right';
		} else if ( $right === 'auto' ) {
			$x = 'left';
		}

		if ( strpos( $left, 'calc' ) === 0 || strpos( $right, 'calc' ) === 0 ) {
			$x = 'center';
		}

		return sprintf( '%s %s', $x, $y );
	}
}

if ( ! function_exists( 'yith_wcbm_is_settings_panel' ) ) {
	function yith_wcbm_is_settings_panel() {
		return isset( $_GET['page'] ) && "yith_wcbm_panel" === $_GET['page'];
	}
}
