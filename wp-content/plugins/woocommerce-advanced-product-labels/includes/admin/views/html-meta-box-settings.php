<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

wp_nonce_field( 'wapl_global_label_meta_box', 'wapl_global_label_meta_box_nonce' );

global $post;
$label = get_post_meta( $post->ID, '_wapl_global_label', true );

$label               = wp_parse_args( $label, array(
	'id'                => $post->ID,
	'text'              => '',
	'style'             => '',
	'style_attr'        => '',
	'type'              => '',
	'align'             => '',
	'custom_bg_color'   => isset( $label['label_custom_background_color'] ) ? $label['label_custom_background_color'] : '#D9534F',
	'custom_text_color' => isset( $label['label_custom_text_color'] ) ? $label['label_custom_text_color'] : '#fff',
	'custom_image'      => isset( $label['custom_image'] ) ? $label['custom_image'] : 0,
	'position'          => $label['position'] ?? array( 'left' => null, 'top' => null ),
) );

?><div class='wapl-meta-box'>

	<div class='wapl-column'>


		<p class='wapl-global-option'>

			<label for='_wapl_label_type'><?php _e( 'Type', 'woocommerce-advanced-product-labels' ); ?></label>
			<select id='_wapl_label_type' name='_wapl_label[type]'><?php
				foreach ( wapl_get_label_types() as $key => $value ) :
					?><option value='<?php echo $key; ?>' <?php selected( $label['type'], $key ); ?>><?php echo $value; ?></option><?php
				endforeach;
			?></select>

		</p>

		<p class='wapl-global-option custom-image type-custom-show'>
			<label for='wapl-custom-image'><?php _e( 'Image', 'woocommerce-advanced-product-labels' ); ?></label>
			<input type='text' id="custom-image-url" value='<?php echo wp_get_attachment_url( $label['custom_image'] ); ?>' class='' readonly />
			<input type='hidden' name='_wapl_label[custom_image]' value='<?php echo $label['custom_image']; ?>' id='wapl-custom-image' class='' readonly />
		</p>
		<p class="custom-image type-custom-show">
			<label for='wapl-custom-image'></label>
			<input id="upload_image_button" type="button" class="button" value="<?php _e( 'Select image' ); ?>"/>
		</p>


		<p class='wapl-global-option type-custom-hidden'>
			<label for='_wapl_label_text'><?php _e( 'Text', 'woocommerce-advanced-product-labels' ); ?></label>
			<input type='text' id='_wapl_label_text' name='_wapl_label[text]' value='<?php echo esc_attr( $label['text'] ); ?>' size='25'/>
		</p>


		<p class='wapl-global-option type-custom-hidden'>
			<label for='_wapl_label_style'><?php _e( 'Color', 'woocommerce-advanced-product-labels' ); ?></label>
			<select name='_wapl_label[style]' class='wapl-select' id='_wapl_label_style'><?php
				foreach ( wapl_get_label_styles() as $key => $value ) :
					?><option value='<?php echo $key; ?>' <?php selected( $label['style'], $key ); ?>><?php echo $value; ?></option><?php
				endforeach;
			?></select>
		</p>

		<p class='wapl-global-option color-custom-show'>
			<label for='wapl-custom-background'><?php _e( 'Background color', 'woocommerce-advanced-product-labels' ); ?></label>
			<input type='text' name='_wapl_label[label_custom_background_color]' value='<?php echo $label['custom_bg_color']; ?>' id='wapl-custom-background' class='color-picker' />

			<label for='wapl-custom-text'><?php _e( 'Text color', 'woocommerce-advanced-product-labels' ); ?></label>
			<input type='text' name='_wapl_label[label_custom_text_color]' value='<?php echo $label['custom_text_color']; ?>' id='wapl-custom-text' class='color-picker' />
		</p>


		<p class='wapl-global-option'>

			<label for='_wapl_label_align'><?php _e( 'Align', 'woocommerce-advanced-product-labels' ); ?></label>
			<select name='_wapl_label[align]' class='wapl-select' id='_wapl_label_align'>
				<option value='none' <?php selected( $label['align'], 'none' ); ?>><?php _e( 'None', 'woocommerce-advanced-product-labels' ); ?></option>
				<option value='left' <?php selected( $label['align'], 'left' ); ?>><?php _e( 'Left', 'woocommerce-advanced-product-labels' ); ?></option>
				<option value='center' <?php selected( $label['align'], 'center' ); ?>><?php _e( 'Center', 'woocommerce-advanced-product-labels' ); ?></option>
				<option value='right' <?php selected( $label['align'], 'right' ); ?>><?php _e( 'Right', 'woocommerce-advanced-product-labels' ); ?></option>
				<option value='custom' <?php selected( $label['align'], 'custom' ); ?>><?php _e( 'Custom', 'woocommerce-advanced-product-labels' ); ?></option>
			</select>

		</p>

		<p class='wapl-global-option align-custom-show'>
			<label for=''><?php _e( 'Position', 'woocommerce-advanced-product-labels' ); ?></label>
			<label style="width: auto;"><?php _e( 'Top', 'woocommerce-advanced-product-labels' ); ?>: </label> <input type='number' style="width: 60px;" placeholder="Top" name='_wapl_label[position][top]' value='<?php echo $label['position']['top']; ?>' id='wapl-custom-position-top' class='' />
			<label style="width: auto;"><?php _e( 'Left', 'woocommerce-advanced-product-labels' ); ?>: </label> <input type='number' style="width: 60px;" placeholder="Left" name='_wapl_label[position][left]' value='<?php echo $label['position']['left']; ?>' id='wapl-custom-position-left' class='' />
		</p>

	</div>

	<div class='wapl-column' style='border-left: 1px solid #ddd; padding-left: 4%;'>

		<h2 class='wapl-preview-title'><?php
			_e( 'Preview', 'woocommerce-advanced-product-labels' );
			echo wc_help_tip( __( 'This provides a indication of the label, front-end may display the label differently based on the theme.', 'woocommerce-advanced-product-labels' ) );
		?></h2>

		<p>
			<input type="hidden" name="product_id" value="0" />
			<select class="wc-product-search" name="product_id" id="product_id" data-allow_clear="true" data-exclude_type="variable" onchange="this.form.submit()" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>"><?php

				if ( isset( $preview_product ) ) :
					?><option value="<?php echo absint( $preview_product->get_id() ); ?>"><?php echo $preview_product->get_formatted_name(); ?></option><?php
				endif;

				?><option value="0"><?php _e( 'Placeholder', 'woocommerce-advanced-product-labels' ); ?></option>
			</select>
		</p>

		<div id='wapl-global-preview'>

			<ul class="products columns-3" style="margin: 0; padding: 0;">
				<li class="product type-products first">
					<div class="woo-thumbnail-wrap">
						<div class="woo-thumbnail-wrap"><?php
							echo $image;
						?></div>
					</div>
					<?php echo wapl_get_label_html( $label ); ?>
					<h2 class="woocommerce-loop-product__title">Product title</h2>
				</li>
			</ul>

		</div>

	</div>

</div>
