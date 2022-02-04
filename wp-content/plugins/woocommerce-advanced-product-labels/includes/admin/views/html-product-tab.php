<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?><div id='woocommerce_advanced_product_labels' class='panel woocommerce_options_panel hidden'>

	<div class='options_group'><?php

		woocommerce_wp_checkbox( array(
			'id'          => '_wapl_label_exclude',
			'label'       => __( 'Exclude Global Labels', 'woocommerce-advanced-product-labels' ),
			'description' => __( 'Check to exclude global labels', 'woocommerce-advanced-product-labels' ),
		) );

	?></div>

	<div class='options_group'>

		<div class='wapl-column' style='width: 50%;'><?php

			woocommerce_wp_select( array(
				'id'          => '_wapl_label_type',
				'label'       => __( 'Label type', 'woocommerce-advanced-product-labels' ),
				'desc_tip'    => true,
				'description' => __( '<strong>\'Flash\'</strong> is positioned on top of the product image<br/><strong>\'Label\'</strong> is positioned above the product title', 'woocommerce-advanced-product-labels' ),
				'options'     => wapl_get_label_types(),
			) );

			$custom_image = $label['custom_image'] ?? ''
			?>
			<p class='form-field custom-image type-custom-show'>
				<label for='wapl-custom-image'><?php _e( 'Image', 'woocommerce-advanced-product-labels' ); ?></label>
				<input type='text' id="custom-image-url" value='<?php echo wp_get_attachment_url( $custom_image ); ?>' class='' readonly/>
				<input type='hidden' name='_wapl_custom_image' value='<?php echo $custom_image; ?>' id='wapl-custom-image' class='' readonly/>
			</p>
			<p class="form-field custom-image type-custom-show">
				<label for='wapl-custom-image'></label>
				<input id="upload_image_button" type="button" class="button" value="<?php _e( 'Select image' ); ?>" style="margin-left: 0;"/>
			</p><?php

			woocommerce_wp_text_input( array(
				'id'            => '_wapl_label_text',
				'label'         => __( 'Label text', 'woocommerce-advanced-product-labels' ),
				'desc_tip'      => true,
				'wrapper_class' => 'type-custom-hidden',
				'description'   => __( 'What text do you want the label to show?', 'woocommerce-advanced-product-labels' ),
			) );

			woocommerce_wp_select( array(
				'id'            => '_wapl_label_style',
				'label'         => __( 'Label style', 'woocommerce-advanced-product-labels' ),
				'wrapper_class' => 'type-custom-hidden',
				'options'       => wapl_get_label_styles()
			) );

			$label_custom_bg_color   = $label['custom_bg_color'] ?? '#D9534F';
			$label_custom_text_color = $label['custom_text_color'] ?? '#fff';

			?><p class='form-field color-custom-show'>
				<label for='wapl-custom-background'><?php _e( 'Background color', 'woocommerce-advanced-product-labels' ); ?></label>
				<input type='text' name='_wapl_custom_bg_color' value='<?php echo $label_custom_bg_color; ?>' id='wapl-custom-background' class='color-picker' />

				<label for='wapl-custom-text'><?php _e( 'Text color', 'woocommerce-advanced-product-labels' ); ?></label>
				<input type='text' name='_wapl_custom_text_color' value='<?php echo $label_custom_text_color; ?>' id='wapl-custom-text' class='color-picker' />
			</p><?php

			woocommerce_wp_select( array(
				'id'      => '_wapl_label_align',
				'label'   => __( 'Label align', 'woocommerce-advanced-product-labels' ),
				'options' => array(
					'none'   => __( 'None', 'woocommerce-advanced-product-labels' ),
					'left'   => __( 'Left', 'woocommerce-advanced-product-labels' ),
					'right'  => __( 'Right', 'woocommerce-advanced-product-labels' ),
					'center' => __( 'Center', 'woocommerce-advanced-product-labels' ),
					'custom' => __( 'Custom', 'woocommerce-advanced-product-labels' ),
				),
			) );

			$position = $label['position'] ?? array( 'top' => null, 'left' => null );
			?>
			<p class='form-field align-custom-show'>
				<label for=''><?php _e( 'Position', 'woocommerce-advanced-product-labels' ); ?></label>
				<label style="width: auto; float: unset; margin: 0;"><?php _e( 'Top', 'woocommerce-advanced-product-labels' ); ?>: </label>
				<input style="width: 60px; float: unset;" type='number' placeholder="Top" name='_wapl_position[top]' value='<?php echo $position['top']; ?>' id='wapl-custom-position-top' class='' />
				<label style="width: auto; float: unset; margin: 0;"><?php _e( 'Left', 'woocommerce-advanced-product-labels' ); ?>: </label>
				<input style="width: 60px; float: unset;" type='number' placeholder="Left" name='_wapl_position[left]' value='<?php echo $position['left']; ?>' id='wapl-custom-position-left' class='' />
			</p>

		</div>

		<div class='wapl-column' style='width: 20%; margin-top: 20px; padding-left: 40px; border-left: 1px solid #ddd;'>

			<div id='wapl-global-preview'>

				<ul class="products columns-3" style="margin: 0; padding: 0;">
					<li class="product type-products first">
						<div class="woo-thumbnail-wrap">
							<div class="woo-thumbnail-wrap"><?php
								woocommerce_template_loop_product_thumbnail();
							?></div>
						</div>
						<?php echo wapl_get_label_html( $label ); ?>
						<h2 class="woocommerce-loop-product__title"><?php echo $GLOBALS['product']->get_name(); ?></h2>
					</li>
				</ul>

			</div>
		</div>
	</div>

</div>
