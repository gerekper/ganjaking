<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?><div id='woocommerce_advanced_product_labels' class='panel woocommerce_options_panel hidden'>

	<div class='options_group'><?php

		woocommerce_wp_checkbox( array(
			'id'          => '_wapl_label_exclude',
			'label'       => __( 'Exclude Global Labels', 'woocommerce-advanced-product-labels' ),
			'description' => __( 'When checked, global labels will be excluded', 'woocommerce-advanced-product-labels' ),
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

			woocommerce_wp_text_input( array(
				'id'          => '_wapl_label_text',
				'label'       => __( 'Label text', 'woocommerce-advanced-product-labels' ),
				'desc_tip'    => true,
				'description' => __( 'What text do you want the label to show?', 'woocommerce-advanced-product-labels' ),
			) );

			woocommerce_wp_select( array(
				'id'      => '_wapl_label_style',
				'label'   => __( 'Label style', 'woocommerce-advanced-product-labels' ),
				'options' => wapl_get_label_styles()
			) );

			$label_custom_bg_color   = isset( $label['custom_bg_color'] ) ? $label['custom_bg_color'] : '#D9534F';
			$label_custom_text_color = isset( $label['custom_text_color'] ) ? $label['custom_text_color'] : '#fff';

			?><p class='form-field _wapl_label_custom_bg_color_field wapl-custom-colors custom-colors <?php echo isset( $label['style'] ) && $label['style'] == 'custom' ? '' : 'hidden'; ?>'>
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
				),
			) );

		?></div>

		<div class='wapl-column' style='width: 20%; margin-top: 20px; padding-left: 40px; border-left: 1px solid #ddd;'>

			<div id='wapl-label-preview'>
				<img width='150' height='150' title='' alt='' src='<?php echo apply_filters( 'wapl_preview_image_src', 'data:image/gif;base64,R0lGODdhlgCWAOMAAMzMzJaWlr6+vpycnLGxsaOjo8XFxbe3t6qqqgAAAAAAAAAAAAAAAAAAAAAAAAAAACwAAAAAlgCWAAAE/hDISau9OOvNu/9gKI5kaZ5oqq5s675wLM90bd94ru987//AoHBILBqPyKRyyWw6n9CodEqtWq/YrHbL7Xq/4LB4TC6bz+i0es1uu9/wuHxOr9vv+Lx+z+/7/4CBgoOEhYaHiImKi4yNjo+QkZKTlJWWl5iZmpucnZ6foHcCAwMTAaenBxMCBQEFBiajpRKoqautr2cEp7MApwjAAhIGA64BvSK7x6YBwAjCAMTGyGK7rb3LFbsEAAgBqsnTptQA293fZQaq2b7krbACzSPq7eMW7wDxCGjsxwTPE4oNc2XhlIB4ATT0G/APGgCB0Qie6VcL2kIL3oDJy0ARlUVsz+TEsEPw6sDGi/dIFdgwsuRJkPxCZkNZAaFDDOwozIQ5MSREiAYkVggaAJZCnwkfJg26sucEcEol4NN3QRm3o08DJp260Uw2k9yYSjDnDarOAgVC6pwFNmJTsujKoD3VtFjauNKuXWh1wGSBffdaSbRbDFzenGNqLb12VcIoV0YrnKI1uWCtYYwpPM4VqrPnz6BDix5NurTp06hTq17NurXr17Bjy55Nu7bt27hz697Nu7fv38CDCx9OvLjx48iTK1/OvLnz59CjS59OvfqLCAA7' ); ?>' /><?php

				echo wapl_get_label_html( $label );

				?><p><strong>Product name</strong></p>
			</div>
		</div>
	</div>

</div>
