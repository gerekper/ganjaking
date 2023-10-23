<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase: Filenames
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var object $addon
 * @var string $addon_type
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

?>

<div id="options-editor-color">

	<!--<h3><?php echo esc_html__( 'Separator', 'yith-woocommerce-product-add-ons' ); ?></h3>-->

	<!-- Option field -->
	<div class="field-wrap addon-field-grid">
		<label for="option-separator-style"><?php echo esc_html__( 'Separator style', 'yith-woocommerce-product-add-ons' ); ?></label>
		<div class="field rule">
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'      => 'option-separator-style',
					'name'    => 'option_separator_style',
                    'class'   => 'wc-enhanced-select',
					'type'    => 'select',
					'value'   => $addon->get_setting( 'separator_style' ),
					'options' => array(
						'simple_border' => esc_html__( 'Simple Border', 'yith-woocommerce-product-add-ons' ),
						'double_border' => esc_html__( 'Double Border', 'yith-woocommerce-product-add-ons' ),
						'dotted_border' => esc_html__( 'Dotted Border', 'yith-woocommerce-product-add-ons' ),
						'dashed_border' => esc_html__( 'Dashed Border', 'yith-woocommerce-product-add-ons' ),
						'empty_space'   => esc_html__( 'Empty Space', 'yith-woocommerce-product-add-ons' ),
					),
				),
				true
			);
			?>
		</div>
        <span class="description"><?php echo esc_html__( 'Choose the separator style.', 'yith-woocommerce-product-add-ons' ); ?></span>
    </div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap addon-field-grid">
		<label for="option-separator-width"><?php echo esc_html__( 'Width', 'yith-woocommerce-product-add-ons' ); ?> (%)</label>
		<div class="field">
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'    => 'option-separator-width',
					'name'  => 'option_separator_width',
					'type'  => 'slider',
					'min'   => 1,
					'max'   => 100,
					'step'  => 1,
					'value' => $addon->get_setting( 'separator_width', 100 ),
				),
				true
			);
			?>
		</div>
        <span class="description">
            <?php echo esc_html__( 'Set the width value of the separator.', 'yith-woocommerce-product-add-ons' ); ?>
        </span>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap addon-field-grid">
		<label for="option-separator-size"><?php echo esc_html__( 'Height', 'yith-woocommerce-product-add-ons' ); ?> (px)</label>
		<div class="field">
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'    => 'option-separator-size',
					'name'  => 'option_separator_size',
					'type'  => 'number',
					'min'   => 0,
					'value' => $addon->get_setting( 'separator_size', 2 ),
				),
				true
			);
			?>
		</div>
        <span class="description">
            <?php echo esc_html__( 'Set the height value of the separator.', 'yith-woocommerce-product-add-ons' ); ?>
        </span>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap option-separator-color addon-field-grid" style="<?php echo $addon->get_setting( 'separator_style' ) === 'empty_space' ? 'display: none;' : ''; ?>">
		<label for="option-separator-color"><?php echo esc_html__( 'Border color', 'yith-woocommerce-product-add-ons' ); ?></label>
		<div class="field rule">
			<?php
			$value = $addon->get_setting( 'separator_color', '#AA0000' );
			yith_plugin_fw_get_field(
				array(
					'id'            => 'option-separator-color',
					'name'          => 'option_separator_color',
					'type'          => 'colorpicker',
					'alpha_enabled' => false,
					'default'       => '#AA0000',
					'value'         => $value,
				),
				true
			);
			?>
		</div>
        <span class="description"><?php echo esc_html__( 'Set the color for the separator border.', 'yith-woocommerce-product-add-ons' ); ?></span>
    </div>
	<!-- End option field -->

</div><!-- #options-editor-color -->
