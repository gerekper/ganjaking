<?php
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var object $addon
 * @var string $addon_type
 * @var int    $x
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$colorpicker_show = $addon->get_option( 'colorpicker_show', $x, 'default_color', false );

?>

<div class="fields">
		<!-- Option field -->
		<div class="field-wrap addon-field-grid">
			<label for="option-colorpicker-show-<?php echo esc_attr( $x ); ?>"><?php echo esc_html_x( 'In picker show', '[ADMIN] Colorpicker add-on option', 'yith-woocommerce-product-add-ons' ); ?></label>
			<div class="field colorpicker-show-as">
				<?php
				yith_plugin_fw_get_field(
					array(
						'id'      => 'option-colorpicker-show-' . $x,
						'class'   => 'option-colorpicker-show wc-enhanced-select',
						'name'    => 'options[colorpicker_show][]',
						'type'    => 'select',
						'value'   => $colorpicker_show,
						'options' => array(
							'default_color' => _x( 'A default color', '[ADMIN] Colorpicker add-on option', 'yith-woocommerce-product-add-ons' ),
							'placeholder'   => _x( 'A placeholder text', '[ADMIN] Colorpicker add-on option', 'yith-woocommerce-product-add-ons' ),
						),
					),
					true
				);
				?>
			</div>
		</div>
		<!-- End option field -->
		<!-- Option field -->
		<div class="field-wrap default-colorpicker addon-field-grid" style="<?php echo 'default_color' !== $colorpicker_show ? 'display: none;' : ''; ?>">
			<label for="option-colorpicker-<?php echo esc_attr( $x ); ?>"><?php echo esc_html_x( 'Default color', '[ADMIN] Colorpicker add-on option', 'yith-woocommerce-product-add-ons' ); ?></label>
			<div class="field">
				<?php
				yith_plugin_fw_get_field(
					array(
						'id'            => 'option-colorpicker-' . $x,
						'name'          => 'options[colorpicker][]',
						'type'          => 'colorpicker',
						'alpha_enabled' => false,
						'default'       => '#',
						'value'         => $addon->get_option( 'colorpicker', $x, '#ffffff', false ),
					),
					true
				);
				?>
			</div>
		</div>
		<!-- End option field -->
			<!-- Option field -->
		<div class="field-wrap colorpicker-placeholder addon-field-grid" style="<?php echo 'placeholder' !== $colorpicker_show ? 'display: none;' : ''; ?>">
			<label for="option-tooltip-<?php echo esc_attr( $x ); ?>"><?php echo esc_html_x( 'Placeholder', '[ADMIN] Colorpicker add-on option > In picker show > Placeholder text', 'yith-woocommerce-product-add-ons' ); ?>:</label>
            <div class="field">
                <input type="text" name="options[placeholder][]" id="option-tooltip-<?php echo esc_attr( $x ); ?>" value="<?php echo esc_html( $addon->get_option( 'placeholder', $x, '', false ) ); ?>">
            </div>
		</div>
			<!-- End option field -->

	<?php
	yith_wapo_get_view(
		'addon-editor/option-common-fields.php',
		array(
			'x'          => $x,
			'addon_type' => $addon_type,
			'addon'      => $addon
		),
        defined( 'YITH_WAPO_PREMIUM' ) && YITH_WAPO_PREMIUM ? 'premium/' : ''
    );
	?>
</div>
