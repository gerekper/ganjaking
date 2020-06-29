<?php
/**
 * Variable product add to cart
 *
 * @version     3.5.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $post, $porto_settings;

$porto_woo_version = porto_get_woo_version_number();

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

$show_cart_button = true;
$show_only_price  = false;
if ( isset( $porto_settings['catalog-enable'] ) && $porto_settings['catalog-enable'] ) {
	if ( $porto_settings['catalog-admin'] || ( ! $porto_settings['catalog-admin'] && ! ( current_user_can( 'administrator' ) && is_user_logged_in() ) ) ) {
		if ( ! $porto_settings['catalog-cart'] ) {
			$show_cart_button = false;
			if ( ! $porto_settings['catalog-price'] && ! $porto_settings['catalog-readmore'] ) {
				$no_add_to_cart = true;
			} elseif ( $porto_settings['catalog-price'] && ! $porto_settings['catalog-readmore'] ) {
				$show_only_price = true;
			}
		}
	}
}

do_action( 'woocommerce_before_add_to_cart_form' );

?>

<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo ! $variations_attr ? '' : $variations_attr; // WPCS: XSS ok. ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
	<?php else : ?>
		<table class="variations" cellspacing="0">
			<tbody>
				<?php
				$loop = 0;
				foreach ( $attributes as $attribute_name => $options ) :
					$loop++;
					?>
					<tr>
						<td class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); // WPCS: XSS ok. ?></label></td>
						<?php if ( version_compare( $porto_woo_version, '2.4', '<' ) ) : ?>
							<td class="value"><select id="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>" name="attribute_<?php echo sanitize_title( $attribute_name ); ?>" data-attribute_name="attribute_<?php echo sanitize_title( $attribute_name ); ?>">
								<option value=""><?php esc_html_e( 'Choose an option', 'woocommerce' ); ?>&hellip;</option>
								<?php
								if ( is_array( $options ) ) {
									if ( isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) {
										$selected_value = $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ];
									} elseif ( isset( $selected_attributes[ sanitize_title( $attribute_name ) ] ) ) {
										$selected_value = $selected_attributes[ sanitize_title( $attribute_name ) ];
									} else {
										$selected_value = '';
									}

									if ( taxonomy_exists( $attribute_name ) ) {

										$terms = wc_get_product_terms( $post->ID, $attribute_name, array( 'fields' => 'all' ) );

										foreach ( $terms as $term ) {
											if ( ! in_array( $term->slug, $options ) ) {
												continue;
											}
											echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $term->slug ), false ) . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
										}
									} else {

										foreach ( $options as $option ) {
											echo '<option value="' . esc_attr( sanitize_title( $option ) ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $option ), false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
										}
									}
								}
								?>

							</select> 
							<?php
							if ( sizeof( $attributes ) === $loop ) {
								echo '<a class="reset_variations" href="#reset">' . esc_html__( 'Clear selection', 'woocommerce' ) . '</a>';
							}
							?>
							</td>

						<?php else : ?>

							<td class="value">
								<?php
								wc_dropdown_variation_attribute_options(
									array(
										'options'   => $options,
										'attribute' => $attribute_name,
										'product'   => $product,
									)
								);
								echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : '';
								?>
							</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php if ( ! isset( $no_add_to_cart ) || ! $no_add_to_cart ) : ?>
		<div class="single_variation_wrap<?php echo ! $show_only_price ? '' : ' py-0 border-0'; ?>"<?php echo version_compare( $porto_woo_version, '2.5', '<' ) ? ' style="display:none;"' : ''; ?>>
			<?php
			/**
			 * Hook: woocommerce_before_single_variation.
			 */
			do_action( 'woocommerce_before_single_variation' );
			?>

			<?php if ( version_compare( $porto_woo_version, '2.4', '<' ) ) : ?>
				<div class="single_variation"></div>
				<?php
				// hide add to cart button in catalog mode

				if ( $show_cart_button ) :
					?>
				<div class="variations_button">

					<?php
					woocommerce_quantity_input(
						array(
							'input_value' => ( isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 ),
						)
					);
					?>
					<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
				</div>

				<?php endif; ?>

				<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />
				<input type="hidden" name="product_id" value="<?php echo esc_attr( $post->ID ); ?>" />
				<input type="hidden" name="variation_id" class="variation_id" value="" />

			<?php else : ?>

				<?php
				/**
				 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
				 *
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				do_action( 'woocommerce_single_variation' );
				?>

			<?php endif; ?>

			<?php
			/**
			 * Hook: woocommerce_after_single_variation.
			 */
			do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php
do_action( 'woocommerce_after_add_to_cart_form' );
