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

	<!--<h3><?php echo esc_html__( 'Heading', 'yith-woocommerce-product-add-ons' ); ?></h3>-->

	<!-- Option field -->
	<div class="field-wrap addon-field-grid">
		<label for="option-heading-text"><?php echo esc_html__( 'Heading text', 'yith-woocommerce-product-add-ons' ); ?></label>
		<div class="field">
			<input type="text" name="option_heading_text" id="option-heading-text" value="<?php echo esc_attr( $addon->get_setting( 'heading_text' ) ); ?>">
			<span class="description"><?php echo esc_html__( 'Enter a text for this heading.', 'yith-woocommerce-product-add-ons' ); ?></span>
		</div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap addon-field-grid">
		<label for="option-heading-type"><?php echo esc_html__( 'Heading type', 'yith-woocommerce-product-add-ons' ); ?></label>
		<div class="field rule">
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'      => 'option-heading-type',
					'name'    => 'option_heading_type',
                    'class'   => 'wc-enhanced-select',
					'type'    => 'select',
					'value'   => $addon->get_setting( 'heading_type' ),
					'options' => array(
						'h1' => 'H1',
						'h2' => 'H2',
						'h3' => 'H3',
						'h4' => 'H4',
						'h5' => 'H5',
						'h6' => 'H6',
					),
				),
				true
			);
			?>
		</div>
        <span class="description"><?php echo esc_html__( 'Choose the heading type.', 'yith-woocommerce-product-add-ons' ); ?></span>
    </div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap addon-field-grid">
		<label for="option-heading-color"><?php echo esc_html__( 'Heading color', 'yith-woocommerce-product-add-ons' ); ?></label>
		<div class="field rule">
			<?php
			$value = $addon->get_setting( 'heading_color', '#AA0000' );
			yith_plugin_fw_get_field(
				array(
					'id'            => 'option-heading-color',
					'name'          => 'option_heading_color',
					'type'          => 'colorpicker',
					'alpha_enabled' => true,
					'default'       => '#AA0000',
					'value'         => $value,
				),
				true
			);
			?>
		</div>
        <span class="description"><?php echo esc_html__( 'Choose the color of this heading.', 'yith-woocommerce-product-add-ons' ); ?></span>
    </div>
	<!-- End option field -->

</div><!-- #options-editor-color -->
