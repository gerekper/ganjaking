<?php
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var object $addon
 * @var int    $x
 * @var string $setting_hide_images
 * @var string $required_message
 * @var array  $settings
 * @var string $image_replacement
 * @var string $option_description
 * @var string $option_image
 * @var string $price
 * @var string $price_method
 * @var string $price_sale
 * @var string $price_type
 * @var string $currency
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

//Settings configuration.
extract($settings );

$hide_option_prices = apply_filters( 'yith_wapo_hide_option_prices', $hide_option_prices, $addon );
$show_in_a_grid      = wc_string_to_bool( $show_in_a_grid );
$options_width_css   = $show_in_a_grid && 1 == $options_per_row ? 'width: ' . $options_width . '%' : 'width: 100%';

$hide_option_images  = wc_string_to_bool( $hide_option_images );
$hide_option_label   = wc_string_to_bool( $hide_option_label );
$hide_option_prices  = wc_string_to_bool( $hide_option_prices );
$hide_product_prices = wc_string_to_bool( $hide_product_prices );

$image_replacement = $addon->get_image_replacement( $addon, $x );

// Options configuration.
$show_variation_att = apply_filters( 'yith_wapo_show_attributes_on_variations', true );

$price_type = '';

$product_id   = $addon->get_option( 'product', $x );
$product_id   = apply_filters( 'yith_wapo_addon_product_id', $product_id );
$_product     = wc_get_product( $product_id );

if ( $_product instanceof WC_Product ) {

	$price_type = '';
	$parent_id  = '';

	$_product_name = $_product->get_title();
	$instock       = $_product->is_in_stock();

    $show_product_description = apply_filters( 'yith_wapo_show_product_description', false );
    $product_description = $_product->get_short_description();

	if ( 'hide' === $product_out_of_stock && ! $instock ) {
		return;
	}

	$price_method = $addon->get_option( 'price_method', $x, 'free', false );
	if ( 'product' !== $price_method ) {
		$price_type = $addon->get_option( 'price_type', $x, 'fixed', false );
	}
	$selected = $addon->get_option( 'default', $x, 'no' ) === 'yes';
	$checked  = $addon->get_option( 'default', $x, 'no' ) === 'yes' ? 'checked="checked"' : '';
	$required = $addon->get_option( 'required', $x, 'no', false ) === 'yes';

	if ( $_product instanceof WC_Product_Variation ) {
		$variation      = new WC_Product_Variation( $product_id );
		if ( $show_variation_att ) {
			$var_attributes = implode( ' / ', $variation->get_variation_attributes() );
			$_product_name  = $_product_name . ' - ' . urldecode( $var_attributes );
		}
		$parent_id      = $variation->get_parent_id();
	}

	$_product_price           = wc_get_price_to_display( $_product );
    $show_empty_product_image = apply_filters( 'yith_wapo_show_empty_product_image', true );
	$_product_image           = $_product->get_image( array( 100, 100 ), array(), $show_empty_product_image );

	$option_price      = ! empty( $price_sale ) && 'undefined' !== $price_sale ? $price_sale : $price;
	$option_price_html = '';
	if ( 'product' === $price_method ) {
		$price_sale = '';
		$option_price = $_product_price;
		$option_price_html = ! $hide_product_prices ? '<small class="option-price">' . wc_price( $option_price ) . '</small>' : '';

	} elseif ( 'discount' === $price_method ) {
		$option_price          = $_product_price;
		$option_discount_value = $addon->get_price( $x );
		$price_sale            = $option_price - $option_discount_value;
		if ( 'percentage' === $price_type ) {
			$price_sale = $option_price - ( ( $option_price / 100 ) * $option_discount_value );
		}

		$option_price_html = ! $hide_product_prices ?
			'<small class="option-price"><del>' . wc_price( $option_price ) . '</del> ' . wc_price( $price_sale ) . '</small>' : '';
	} else {
		$option_price_html = $addon->get_option_price_html( $x, $currency );
	}


	?>

	<div id="yith-wapo-option-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
		 class="yith-wapo-option selection-<?php echo esc_attr( $selection_type ); ?><?php echo $selected ? ' selected' : ''; ?><?php echo ! $instock ? ' out-of-stock' : ''; ?>"
		 data-replace-image="<?php echo esc_attr( $image_replacement ); ?>"
		 data-product-id="<?php echo esc_attr( $_product->get_id() ); ?>"
         style="<?php echo esc_attr( $options_width_css ); ?>"
	>

		<?php
		if ( 'left' === $addon_options_images_position ) {
			//TODO: use wc_get_template() function.
			include YITH_WAPO_DIR . '/templates/front/option-image.php'; }
		?>

		<input type="checkbox"
			   id="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
			   class="yith-proteo-standard-checkbox yith-wapo-option-value"
			   name="yith_wapo[][<?php echo esc_attr( $addon->id . '-' . $x ); ?>]"
			   value="<?php echo 'product-' . esc_attr( $_product->get_id() ) . '-1'; ?>"
			   data-price="<?php echo esc_attr( $option_price ); ?>"
			<?php
			if ( $price > 0 ) {
				?>
				data-price-sale="<?php echo esc_attr( $price_sale ); ?>"
				<?php
			}
			?>
			   data-price-type="<?php echo esc_attr( $price_type ); ?>"
			   data-price-method="<?php echo esc_attr( $price_method ); ?>"
			   data-first-free-enabled="<?php echo esc_attr( $first_options_selected ); ?>"
			   data-first-free-options="<?php echo esc_attr( $first_free_options ); ?>"
			   data-addon-id="<?php echo esc_attr( $addon->id ); ?>"
			<?php echo $required ? 'required' : ''; ?>
			<?php echo ! $instock ? 'disabled="disabled"' : ''; ?>
			<?php echo esc_attr( $checked ); ?>
			   style="display: none;">

		<?php // Changed <label> tag by a <div> tag ?>
		<div for="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>" class="product-container<?php echo ! $instock ? ' disabled' : ''; ?>">
			<div class="product-image">
				<?php echo wp_kses_post( $_product_image ); ?>
			</div>
			<div class="product-info">
				<!-- PRODUCT NAME -->
				<span class="product-name"><?php echo wp_kses_post( $_product_name ); ?></span>
				<?php
				if ( $show_sku === 'yes' && $_product->get_sku() !== '' ) {
					echo '<div><small style="font-size: 11px;">SKU: ' . esc_html( $_product->get_sku() ) . '</small></div>'; }
				?>
				<?php
				do_action( 'yith_wapo_after_addon_product_name', $_product, $product_id, $addon );
				?>

				<!-- PRICE -->
				<?php echo ! $hide_option_prices ? wp_kses_post( $option_price_html ) : ''; ?>

                <?php
                do_action( 'yith_wapo_after_addon_product_price', $_product, $product_id, $addon );
                ?>

				<?php
					if ( $show_product_description && '' !== $product_description ) {
						echo '<p class="wapo-addon-description">' . stripslashes( $product_description ) . '</p>'; // phpcs:ignore
					}
				?>
				<!-- STOCK -->
				<?php
				$stock_class  = '';
				$stock_style  = '';
				$stock_status = '';
				if ( $instock ) {
					$stock_class = 'in-stock';
					$stock_style = 'margin-bottom: 10px';
					if ( $_product->get_manage_stock() ) {
						$stock_status = $_product->get_stock_quantity() . ' ' . esc_html__( 'in stock', 'yith-woocommerce-product-add-ons' );
					} else {
						$stock_status = esc_html__( 'In stock', 'yith-woocommerce-product-add-ons' );
					}
				} else {
					$stock_class  = 'out-of-stock';
					$stock_status = esc_html__( 'Out of stock', 'yith-woocommerce-product-add-ons' );
				}
				$stock_qty = $_product->get_manage_stock() ? $_product->get_stock_quantity() : false;
				if ( 'yes' === $show_stock ) {
					echo '<div style="' . esc_attr( $stock_style ) . '"><small class="stock ' . esc_attr( $stock_class ) . '">' . esc_html( $stock_status ) . '</small></div>';
				}
				?>

				<?php if ( $_product->get_stock_status() === 'instock' ) : ?>

					<div class="option-add-to-cart">
						<?php

						$input_name           = 'yith_wapo_product_qty[' . esc_attr( $addon->id . '-' . $x ) . ']';

						if ( 'yes' === $show_quantity ) {

							$default_qty = apply_filters( 'yith_wapo_default_product_qty', 1, $_product );

							$input_class_quantity = array( 'input-text', 'qty', 'text', 'wapo-product-qty' );
							$max_value            = $_product->get_stock_quantity();

							woocommerce_quantity_input(
								array(
									'input_id'    => $input_name,
									/**
									 * APPLY_FILTERS: yith_wapo_input_class_quantity_product
									 *
									 * Filter the array with the CSS clases for the quantity input in add-on type Products.
									 *
									 * @param array      $input_class_quantity CSS classes
									 * @param WC_Product $_product             WooCommerce product
									 *
									 * @return array
									 */
									'classes'     => apply_filters( 'yith_wapo_input_class_quantity_product', $input_class_quantity, $_product ),
									'input_name'  => $input_name,
									'min_value'   => apply_filters( 'yith_wapo_product_quantity_input_min', 1, $_product ),
									'max_value'   => apply_filters( 'yith_wapo_product_quantity_input_max', $max_value, $_product ),
									'input_value' => $default_qty,
									//'step'        => '',
								)
							);
						}
						?>
						<?php if ( 'yes' === $show_add_to_cart ) : ?>
							<a href="?add-to-cart=<?php echo esc_attr( $_product->get_id() ); ?>&quantity=1" class="button add_to_cart_button">
								<?php echo esc_html__( 'Add to cart', 'yith-woocommerce-product-add-ons' ); ?>
							</a>
						<?php endif; ?>

						<?php
						if ( apply_filters( 'yith_wapo_show_addon_product_add_to_quote', false ) ) :
							function_exists( 'yith_ywraq_render_button' ) ? yith_ywraq_render_button( $product_id ) : '';
						endif;
						?>
					</div>

				<?php endif; ?>
			</div>

			<?php
			if ( apply_filters( 'yith_wapo_show_addon_product_link', false ) ) {
				$link_target = apply_filters( 'yith_wapo_show_addon_product_link_target', '' );
				echo '<a class="button view-product" target="' . $link_target . '" href="' . get_permalink( $product_id ) . '">' . esc_html__( 'View product', 'yith-woocommerce-product-add-ons' ) . '</a>';
			}
			?>
		</div>

		<?php
		if ( 'right' === $addon_options_images_position ) {
			//TODO: use wc_get_template() function.
			include YITH_WAPO_DIR . '/templates/front/option-image.php'; }
		?>

		<?php if ( $addon->get_option( 'tooltip', $x ) !== '' ) : ?>
			<span class="tooltip">
				<span><?php echo esc_attr( $addon->get_option( 'tooltip', $x ) ); ?></span>
			</span>
		<?php endif; ?>

		<?php
		if ( 'above' === $addon_options_images_position ) {
			//TODO: use wc_get_template() function.
			include YITH_WAPO_DIR . '/templates/front/option-image.php'; }
		?>

		<?php if ( '' !== $option_description ) : ?>
			<p class="description">
				<?php echo wp_kses_post( $option_description ); ?>
			</p>
		<?php endif; ?>

		<!-- Sold individually -->
		<?php if ( 'yes' === $sell_individually ) : ?>
			<input type="hidden" name="yith_wapo_sell_individually[<?php echo esc_attr( $addon->id . '-' . $x ); ?>]" value="yes">
		<?php endif; ?>
	</div>
	<?php
}
