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

?>

<div class="fields">

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

		<!-- Option field -->
		<div class="field-wrap addon-field-grid">
			<label for="option-characters-limit"><?php echo esc_html__( 'Limit input characters', 'yith-woocommerce-product-add-ons' ); ?>:</label>
			<div class="field">
				<?php
				yith_plugin_fw_get_field(
					array(
						'id'    => 'option-characters-limit',
						'class' => 'enabler',
						'name'  => 'options[characters_limit][]',
						'type'  => 'onoff',
						'value' => $addon->get_option( 'characters_limit', $x, '', false ),
					),
					true
				);
				?>
			</div>
		</div>
		<!-- End option field -->

		<!-- Option field -->
		<div class="field-wrap enabled-by-option-characters-limit addon-field-grid" style="display: none;">
			<label for="option-characters-limit"><?php echo esc_html__( 'Number of characters', 'yith-woocommerce-product-add-ons' ); ?>:</label>
			<div class="option-characters-limit">
                <div class="field">
                    <small><?php echo esc_html__( 'MIN', 'yith-woocommerce-product-add-ons' ); ?></small>
                    <input type="text" name="options[characters_limit_min][]" id="option-characters-limit-min" value="<?php echo esc_attr( $addon->get_option( 'characters_limit_min', $x, '', false ) ); ?>" class="mini">
                </div>
                <div class="field">
                    <small><?php echo esc_html__( 'MAX', 'yith-woocommerce-product-add-ons' ); ?></small>
                    <input type="text" name="options[characters_limit_max][]" id="option-characters-limit-max" value="<?php echo esc_attr( $addon->get_option( 'characters_limit_max', $x, '', false ) ); ?>" class="mini">
                </div>
            </div>
		</div>
		<!-- End option field -->

</div>
