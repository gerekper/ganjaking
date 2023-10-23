<?php
/**
 * Option Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var int $x
 * @var string $addon_type
 * @var YITH_WAPO_Addon $addon
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$price_type        = $addon->get_option( 'price_type', $x, 'fixed', false );
$option_color_type = $addon->get_option( 'color_type', $x, 'color', false );
$color_b           = $addon->get_option( 'color_b', $x, '', false );

//todo: Remove v3 check next major release (v5)
$v3_check = false;

$option_color_type = 'dingle' === $option_color_type ? 'single' : $option_color_type;
if ( 'single' === $option_color_type ) {
	$v3_check = true;
}
if ( 'color' === $option_color_type && '' === $color_b ) {
	$v3_check = true;
}

?>

<?php if ( 'product' !== $addon_type ) : ?>

	<div class="option-common-fields">
		<!-- Option field -->
		<div class="field-wrap addon-field-grid">
			<label for="option-label-<?php echo esc_attr( $x ); ?>">
                <?php
                // translators: add-on option.
                echo esc_html__('Label', 'yith-woocommerce-product-add-ons') ?>
            </label>
            <div class="label-field">
                <div class="field">
                    <input type="text" name="options[label][]" id="option-label-<?php echo esc_attr( $x ); ?>" value="<?php echo esc_html( $addon->get_option( 'label', $x, '', false ) ); ?>"
                           class="addon-option-label">
                </div>
            </div>
		</div>

        <?php if ( 'select' !== $addon_type ) : ?>
            <!-- Option field -->
            <div class="field-wrap addon-field-grid">
                <label for="option-tooltip-<?php echo esc_attr( $x ); ?>">
                    <?php
                    // translators: general add-on option.
                    echo esc_html__('Tooltip', 'yith-woocommerce-product-add-ons');
                    ?>
                </label>
                <div class="field">
                    <input type="text" name="options[tooltip][]" id="option-tooltip-<?php echo esc_attr( $x ); ?>" value="<?php echo esc_html( $addon->get_option( 'tooltip', $x, '', false ) ); ?>">
                </div>
            </div>
            <!-- End option field -->
        <?php endif; ?>

		<!-- Option field -->
		<div class="field-wrap addon-field-grid">
			<label for="option-description-<?php echo esc_attr( $x ); ?>">
                <?php
                // translators: add-on option.
                echo esc_html__('Description', 'yith-woocommerce-product-add-ons');
                ?>
            </label>
			<div class="field">
				<input type="text" name="options[description][]" id="option-description-<?php echo esc_attr( $x ); ?>" value="<?php echo esc_html( $addon->get_option( 'description', $x, '', false ) ); ?>">
			</div>
		</div>
		<!-- End option field -->

	</div>

    <?php
        do_action( 'yith_wapo_options_before_add_image', $addon, $x, $addon_type );
    ?>

		<!-- Option field -->
		<div class="field-wrap addon-field-grid">
			<label for="option-show-image-<?php echo esc_attr( $x ); ?>">
                <?php
                // translators: general add-on option.
                echo esc_html__('Add image', 'yith-woocommerce-product-add-ons');
                ?>
            </label>
			<div class="field">
				<?php
				yith_plugin_fw_get_field(
					array(
						'id'    => 'option-show-image-' . $x,
						'class' => 'enabler',
						'name'  => 'options[show_image][' . $x . ']',
						'type'  => 'onoff',
						'value' => $addon->get_option( 'show_image', $x, 'no', false ),
					),
					true
				);
				?>
			</div>
            <span class="description">
                <?php
                // translators: general add-on option.
                echo esc_html__( 'Enable to upload an image for this option.', 'yith-woocommerce-product-add-ons' );
                ?>
                <br />
                <?php
                // translators: general add-on option.
                echo sprintf( esc_html__( 'You can use this image to %1$s replace the default product image %2$s (enabling the option in the "Display & Style" tab).', 'yith-woocommerce-product-add-ons' ),
                    '<b>',
                    '</b>',
                ); ?>
            </span>
		</div>
		<!-- End option field -->

		<!-- Option field -->
		<div class="field-wrap option-image-container enabled-by-option-show-image-<?php echo esc_attr( $x ); ?> addon-field-grid" style="display: none;">
			<label for="option-image-<?php echo esc_attr( $x ); ?>">
                <?php
                // translators: general add-on option.
                echo esc_html__('Image', 'yith-woocommerce-product-add-ons');
                ?>
            </label>
			<div class="field">
				<?php
				yith_plugin_fw_get_field(
					array(
						'id'    => 'option-image-' . $x,
						'class' => 'option-image',
						'name'  => 'options[image][]',
						'type'  => 'media',
						'value' => $addon->get_option( 'image', $x, '', false ),
					),
					true
				);
				?>
			</div>
		</div>
		<!-- End option field -->

<?php else : ?>

	<!-- Option field -->
	<?php
	$product_label       = $addon->get_option( 'label', $x, '', false );
	$product_addon_label = $product_label ?? '';
	?>
	<input type="hidden" name="options[label][]" class="yith-wapo-product-addon-label" value="<?php echo esc_html( $product_addon_label ); ?>">
	<!-- End option field -->

<?php endif; ?>

<!-- Option field -->
<div class="field-wrap addon-field-grid">
	<label>
        <?php
        // translators: general add-on option.
        echo esc_html__('Price', 'yith-woocommerce-product-add-ons');
        ?>
    </label>
	<div class="field">
		<?php
		$option_price_method = $addon->get_option( 'price_method', $x, 'free', false );
		$price_methods       = array(
            // translators: general add-on option.
            'free'     => __( 'Product price doesn\'t change - set option as free', 'yith-woocommerce-product-add-ons' ),
            // translators: general add-on option.
            'increase' => __( 'Product price increase - set option price', 'yith-woocommerce-product-add-ons' ),
            // translators: general add-on option.
            'decrease' => __( 'Product price decrease - set discount', 'yith-woocommerce-product-add-ons' ),
		);

        $price_methods = apply_filters( 'yith_wapo_price_methods', $price_methods, $addon );

		yith_plugin_fw_get_field(
			array(
				'id'      => 'option-price-method-' . $x,
				'class'   => 'option-price-method wc-enhanced-select',
				'name'    => 'options[price_method][]',
				'type'    => 'select',
				'value'   => $option_price_method,
				'options' => $price_methods,
			),
			true
		);
		?>
	</div>
</div>
<!-- End option field -->

<!-- Option field -->
<div id="option-cost-<?php echo esc_attr( $x ); ?>" class="field-wrap option-cost addon-field-grid"
	style="<?php echo 'increase' !== $option_price_method && 'decrease' !== $option_price_method && 'discount' !== $option_price_method ? 'display: none;' : ''; ?>">
	<label>
        <span>
            <?php
        // translators: general add-on option.
        echo esc_html__( 'Option cost', 'yith-woocommerce-product-add-ons' );?></span><?php echo esc_html( ':' ); ?>
    </label>
    <div>
        <div class="field">
            <small class="option-price-method-increase" style="<?php echo 'decrease' === $option_price_method || 'discount' === $option_price_method ? 'display: none;' : ''; ?>">
                <?php
                // translators: general add-on option.
                echo strtoupper( esc_html__( 'regular', 'yith-woocommerce-product-add-ons' ) );
                ?>
            </small>
            <small class="option-price-method-decrease" style="<?php echo 'increase' === $option_price_method ? 'display: none;' : ''; ?>">
                <?php
                // translators: general add-on option.
                echo strtoupper( esc_html__( 'discount', 'yith-woocommerce-product-add-ons' ) );
                ?>
            </small>
            <input type="text" name="options[price][]" id="option-price" value="<?php echo esc_html( $addon->get_option( 'price', $x, '', false ) ); ?>" class="mini">
        </div>
        <div class="field option-price-sale" style="<?php echo 'multiplied' === $price_type || 'decrease' === $option_price_method || 'discount' === $option_price_method ? 'display: none;' : ''; ?>">
            <small>
                <?php
                // translators: general add-on option.
                echo strtoupper( esc_html__( 'sale', 'yith-woocommerce-product-add-ons' ) );
                ?>
            </small>
            <input type="text" name="options[price_sale][]" id="option-price-sale" value="<?php echo esc_html( $addon->get_option( 'price_sale', $x ) ); ?>" class="mini">
        </div>
        <div class="field">
            <?php
            $price_options = array(
                // translators: general add-on option.
                'fixed'      => __( 'Fixed amount', 'yith-woocommerce-product-add-ons' ),
                'percentage' => __( 'Percentage', 'yith-woocommerce-product-add-ons' ),
            );
            if ( 'text' === $addon_type ) {
                // translators: general add-on option.
                $price_options['characters'] = __( 'Price multiplied by string length', 'yith-woocommerce-product-add-ons' );
            }

            $price_options = apply_filters( 'yith_wapo_price_options', $price_options, $addon );

            yith_plugin_fw_get_field(
                array(
                    'id'      => 'option-price-type',
                    'name'    => 'options[price_type][]',
                    'type'    => 'select',
                    'class'   => 'wc-enhanced-select option-price-type',
                    'value'   => $addon->get_option( 'price_type', $x, 'fixed', false ),
                    'options' => $price_options,
                ),
                true
            );
            ?>
        </div>
    </div>

</div>
<!-- End option field -->

<?php if ( 'select' !== $addon_type && 'date' !== $addon_type && 'radio' !== $addon_type ) : ?>

	<!-- Option field -->
	<div class="field-wrap addon-field-grid">
        <label for="option-required-<?php echo esc_attr( $x ); ?>"><?php
            // translators: general add-on option.
            echo esc_html__('Required', 'yith-woocommerce-product-add-ons')
            ?>
        </label>
		<div class="field">
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'    => 'option-required-' . $x,
					'name'  => 'options[required][' . $x . ']',
					'type'  => 'onoff',
					'value' => $addon->get_option( 'required', $x, 'no', false ),
				),
				true
			);
			?>
		</div>
        <span class="description">
            <?php
            // translators: general add-on option.
            echo esc_html__( 'Enable to make this option mandatory for users.', 'yith-woocommerce-product-add-ons' );
            ?>
        </span>
	</div>
	<!-- End option field -->

<?php endif; ?>