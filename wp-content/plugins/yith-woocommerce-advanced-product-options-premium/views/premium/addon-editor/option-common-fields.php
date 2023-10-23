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

		<?php
        // Start COLOR
        if ( 'color' === $addon_type ) : ?>
			<!-- Option field -->
			<div class="field-wrap addon-field-grid">
				<label for="option-color-type-<?php echo esc_attr( $x ); ?>">
                    <?php
                    // translators: option for Add-on type Color Swatch
                    echo esc_html__( 'Show as', 'yith-woocommerce-product-add-ons' );
                    ?>
                </label>
				<div class="field color-show-as">
					<?php
					yith_plugin_fw_get_field(
						array(
							'id'      => 'option-color-type-' . $x,
							'class'   => 'option-color-type wc-enhanced-select',
							'name'    => 'options[color_type][]',
							'type'    => 'select',
							'value'   => $option_color_type,
							'options' => array(
                                // translators: option for Add-on type Color Swatch
								'color' => __( 'Color swatch', 'yith-woocommerce-product-add-ons' ),
                                // translators: option for Add-on type Color Swatch
								'image'  => __( 'Image swatch', 'yith-woocommerce-product-add-ons' ),
							),
						),
						true
					);
					?>
				</div>
			</div>
			<!-- End option field -->
            <!-- Option field -->
            <div class="field-wrap color addon-field-grid">
                <label for="option-color-<?php echo esc_attr( $x ); ?>">
                <?php
                // translators: option for Add-on type Color Swatch.
                echo esc_html__( 'Color', 'yith-woocommerce-product-add-ons' );
                    ?>
                </label>
                <div class="field yith-color-swatches">
                    <div class="color-swatch color_a">
                        <?php
                        yith_plugin_fw_get_field(
                            array(
                                'id'            => 'option-color-' . $x,
                                'name'          => 'options[color][]',
                                'type'          => 'colorpicker',
                                'alpha_enabled' => false,
                                'default'       => '#AA0000',
                                'value'         => $addon->get_option( 'color', $x, '#AA0000', false ),
                            ),
                            true
                        );
                        ?>
                        <div class="color-swatch-add <?php echo esc_html( ! $v3_check ? 'color-hidden' : '' ) ?>">
                            <a href="">+ <?php
                                // translators: option for Add-on type Color Swatch.
                                echo esc_html__( 'Add color', 'yith-woocommerce-product-add-ons' );
                                ?>
                            </a>
                        </div>
                    </div>
                    <div class="color-swatch color_b <?php echo ( $v3_check ) ? 'color-hidden' : '' ?>">
                        <?php
                        $color_b_attributes = array(
                            'id'            => 'option-color-b-' . $x,
                            'name'          => 'options[color_b][' . $x . ']',
                            'type'          => 'colorpicker',
                            'alpha_enabled' => false,
                            'default'       => '#AA0000',
                            'value'         => $color_b,
                        );
                        if ( '' === $color_b || $v3_check ) {
                            $color_b_attributes['custom_attributes'] = array(
                                'disabled' => 'disabled',
                            );
                        }
                        yith_plugin_fw_get_field(
                            $color_b_attributes,
                            true
                        );
                        ?>
                        <img src="<?php echo esc_attr( YITH_WAPO_ASSETS_URL ); ?>/img/close_small.svg" class="color-b-close" alt="Close icon"/>
                    </div>
                </div>
            </div>
            <!-- End option field -->
            <!-- Option field -->
            <div class="field-wrap color_image addon-field-grid" style="display: none;">
                <label for="option-color-image-<?php echo esc_attr( $x ); ?>">
                    <?php
                    // translators: general add-on option.
                    echo esc_html__('Image', 'yith-woocommerce-product-add-ons');
                    ?>
                </label>
                <div class="field">
                    <?php
                    yith_plugin_fw_get_field(
                        array(
                            'id'    => 'option-color-image-' . $x,
                            'name'  => 'options[color_image][]',
                            'type'  => 'media',
                            'value' => $addon->get_option( 'color_image', $x, '', false ),
                        ),
                        true
                    );
                    ?>
                </div>
            </div>
            <!-- End option field -->
		<?php endif;
        // End COLOR
        ?>
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
                <div class="label-in-cart enabler revert">
                    <?php
                    yith_plugin_fw_get_field(
                        array(
                            'id'    => 'label-in-cart-' . $x,
                            'class' => 'checkbox',
                            'name'  => 'options[label_in_cart][' . $x . ']',
                            'type'  => 'checkbox',
                            'default' => 'yes',
                            'value' => $addon->get_option( 'label_in_cart', $x, 'yes', false ),
                        ),
                        true
                    );
                    ?>
                    <label for="<?php echo 'label-in-cart-' . $x ?>">
                        <?php
                        // translators: Edit add-on panel > Label option
                        echo esc_html__( 'Use also as label in cart', 'yith-woocommerce-product-add-ons' ); ?>
                    </label>
                </div>
            </div>
		</div>
        <div class="field-wrap label-in-cart-container enabled-by-label-in-cart-<?php echo esc_attr( $x ); ?> addon-field-grid">
            <label for="label-in-cart-opt-<?php echo esc_attr( $x ); ?>"><?php
                // translators: general add-on option after activating "Use also as label in cart" option.
                echo esc_html__('Label in cart', 'yith-woocommerce-product-add-ons');
                ?>
            </label>
            <div class="field">
                <?php
                yith_plugin_fw_get_field(
                    array(
                        'id'    => 'label-in-cart-opt-' . $x,
                        'class' => '',
                        'name'  => 'options[label_in_cart_opt][]',
                        'type'  => 'text',
                        'value' => $addon->get_option( 'label_in_cart_opt', $x, '', false ),
                    ),
                    true
                );
                ?>
            </div>
        </div>
		<!-- End option field -->
        <?php if ( 'text' === $addon_type || 'textarea' === $addon_type ) : ?>
            <!-- Option field -->
            <div class="field-wrap addon-field-grid">
                <label for="option-placeholder-<?php echo esc_attr( $x ); ?>"><?php
                    // translators: general add-on option.
                    echo esc_html__('Placeholder', 'yith-woocommerce-product-add-ons');
                    ?>
                </label>
                <div class="field">
                    <input type="text" name="options[placeholder][]" id="option-placeholder-<?php echo esc_attr( $x ); ?>" value="<?php echo esc_html( $addon->get_option( 'placeholder', $x, '', false ) ); ?>">
                </div>
            </div>
            <!-- End option field -->
        <?php endif; ?>

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
		if ( 'number' === $addon_type ) {
            $price_methods = array(
                // translators: general add-on option.
                'free'            => __( 'Product price doesn\'t change - set option as free', 'yith-woocommerce-product-add-ons' ),
				'increase'        => __( 'Product price increase - set option price', 'yith-woocommerce-product-add-ons' ),
				'decrease'        => __( 'Product price decrease - set discount', 'yith-woocommerce-product-add-ons' ),
				'value_x_product' => __( 'Value multiplied by product price', 'yith-woocommerce-product-add-ons' ),
			);
		}
		if ( 'product' === $addon_type ) {
			$option_price_method = $addon->get_option( 'price_method', $x, 'product', false );
            $price_methods       = array(
                // translators: general add-on option.
                'free'     => __( 'Product price doesn\'t change - set option as free', 'yith-woocommerce-product-add-ons' ),
				'increase' => __( 'Product price increase - set option price', 'yith-woocommerce-product-add-ons' ),
				'decrease' => __( 'Product price decrease - set discount', 'yith-woocommerce-product-add-ons' ),
				'product'  => __( 'Use price of linked product', 'yith-woocommerce-product-add-ons' ),
				'discount' => __( 'Discount price of linked product', 'yith-woocommerce-product-add-ons' ),
			);
		}

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
            if ( 'number' === $addon_type ) {
                // translators: general add-on option.
                $price_options['multiplied'] = __( 'Price multiplied by value', 'yith-woocommerce-product-add-ons' );
            }
            if ( 'text' === $addon_type || 'textarea' === $addon_type ) {
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

<?php if ( 'file' === $addon_type ) : ?>

	<!-- Option field -->
	<div class="field-wrap addon-field-grid">
		<label>
            <?php
            // translators: general add-on option.
            echo esc_html__('Allow multi-upload', 'yith-woocommerce-product-add-ons');
            ?>
        </label>
		<div class="field">
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'    => 'option-multiupload-' . $x,
					'name'  => 'options[multiupload][' . $x . ']',
					'type'  => 'onoff',
					'class' => 'enabler',
					'value' => $addon->get_option( 'multiupload', $x, 'no', false ),
				),
				true
			);
			?>
		</div>
        <span class="description">
            <?php
            // translators: general add-on option.
            echo esc_html__( 'Enable to allow the users to upload multiple files in the uploader.', 'yith-woocommerce-product-add-ons' );
            ?>
        </span>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap enabled-by-option-multiupload-<?php echo esc_attr( $x ); ?> addon-field-grid" style="display: none">
		<label>
            <?php
            // translators: general add-on option.
            echo esc_html__('Users can upload a max of', 'yith-woocommerce-product-add-ons')
            ?>
        </label>
		<div class="field">
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'    => 'option-multiupload-max-' . $x,
					'name'  => 'options[multiupload_max][' . $x . ']',
					'type'  => 'number',
                    'min'   => 0,
					'class' => 'option-multiupload-max',
					'value' => $addon->get_option( 'multiupload_max', $x, '', false ),
				),
				true
			);
			?>
		</div>
        <span class="description">
            <?php
            echo sprintf(
            // translators: %s is a break line.
                __( 'Optional: set the max number of files a user can upload. %s Leave empty if the user can upload files without any limits.', 'yith-woocommerce-product-add-ons' ),
                '<br>'
            );
            ?>
        </span>
	</div>
	<!-- End option field -->

<?php endif; ?>
