<?php
/**
 * Class to handle feature Coupons By Product Quantity
 *
 * @category    Admin
 * @author      StoreApps
 * @package     woocommerce-smart-coupons/includes
 * @since       5.0.0
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Coupons_By_Product_Quantity' ) ) {

	/**
	 * Class WC_SC_Coupons_By_Product_Quantity
	 */
	class WC_SC_Coupons_By_Product_Quantity {

		/**
		 * Variable to hold instance of this class
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {
			add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'usage_restriction' ), 99, 2 );
			add_action( 'admin_footer', array( $this, 'styles_and_scripts' ) );
			add_action( 'save_post', array( $this, 'process_meta' ), 10, 2 );
			add_filter( 'woocommerce_coupon_is_valid', array( $this, 'validate' ), 11, 2 );
		}

		/**
		 * Get single instance of this class
		 *
		 * @return this class Singleton object of this class
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name = '', $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Display field for coupon by product quantity
		 *
		 * @param integer   $coupon_id The coupon id.
		 * @param WC_Coupon $coupon The coupon object.
		 */
		public function usage_restriction( $coupon_id = 0, $coupon = null ) {
			if ( ! is_a( $coupon, 'WC_Coupon' ) ) {
				return;
			}
			$product_quantity_restrictions = array();
			if ( ! empty( $coupon_id ) ) {
				$product_quantity_restrictions = is_callable( array( $coupon, 'get_meta' ) ) ? $coupon->get_meta( 'wc_sc_product_quantity_restrictions' ) : '';
				if ( empty( $product_quantity_restrictions ) ) {
					$product_quantity_restrictions = get_post_meta( $coupon_id, 'wc_sc_product_quantity_restrictions', true );
				}

				if ( ! is_array( $product_quantity_restrictions ) ) {
					$product_quantity_restrictions = array();
				}
			}
			$cart_min_quantity                  = isset( $product_quantity_restrictions['values']['cart']['min'] ) ? $product_quantity_restrictions['values']['cart']['min'] : '';
			$cart_max_quantity                  = isset( $product_quantity_restrictions['values']['cart']['max'] ) ? $product_quantity_restrictions['values']['cart']['max'] : '';
			$product_quantity_restrictions_type = isset( $product_quantity_restrictions['type'] ) ? $product_quantity_restrictions['type'] : 'cart';
			?>
			<div class="options_group smart-coupons-field">
				<h3 class="smart-coupons-field" style="padding-left: 10px;"><?php echo esc_html__( 'Product quantity based restrictions', 'woocommerce-smart-coupons' ); ?></h3>
				<p class="form-field">
					<span class='left_column'><label><?php echo esc_html__( 'Validate quantity of', 'woocommerce-smart-coupons' ); ?></label></span>
					<label for="wc_sc_product_quantity_type_cart" class="cart_quantity">
						<input type="radio" name="wc_sc_product_quantity_restrictions[type]" id="wc_sc_product_quantity_type_cart" class="wc_sc_product_quantity_type wc_sc_product_quantity_type_cart" value="cart" <?php checked( 'cart', $product_quantity_restrictions_type, true ); ?> />
						<?php echo esc_html__( 'Cart', 'woocommerce-smart-coupons' ); ?>
					</label>
					<label for="wc_sc_product_quantity_type_product" class="product_quantity">
						<input type="radio" name="wc_sc_product_quantity_restrictions[type]" id="wc_sc_product_quantity_type_product" class="wc_sc_product_quantity_type wc_sc_product_quantity_type_product" value="product" <?php checked( 'product', $product_quantity_restrictions_type, true ); ?>>
						<?php echo esc_html__( 'Product', 'woocommerce-smart-coupons' ); ?>
					</label>
					<?php
					$tooltip_text = esc_html__( 'Choose whether to validate the quantity, cart-wise or product-wise', 'woocommerce-smart-coupons' );
                    echo wc_help_tip($tooltip_text); // phpcs:ignore ?>
				</p>
				<p class="form-field wc_sc_cart_quantity" style="<?php echo ( 'cart' === $product_quantity_restrictions_type ) ? '' : 'display: none;'; ?>">
					<label for="wc_sc_min_product_quantity"><?php echo esc_html__( 'Minimum quantity', 'woocommerce-smart-coupons' ); ?></label>
					<input type="number" name="wc_sc_product_quantity_restrictions[values][cart][min]" id="wc_sc_min_product_quantity" class="wc_sc_min_product_quantity" placeholder="<?php echo esc_attr__( 'No minimum', 'woocommerce-smart-coupons' ); ?>" value="<?php echo esc_attr( $cart_min_quantity ); ?>" min="0">
				</p>
				<p class="form-field wc_sc_cart_quantity" style="<?php echo ( 'cart' === $product_quantity_restrictions_type ) ? '' : 'display: none;'; ?>">
					<label for="wc_sc_max_product_quantity"><?php echo esc_html__( 'Maximum quantity', 'woocommerce-smart-coupons' ); ?></label>
					<input type="number" name="wc_sc_product_quantity_restrictions[values][cart][max]" id="wc_sc_max_product_quantity" class="wc_sc_max_product_quantity" placeholder="<?php echo esc_attr__( 'No maximum', 'woocommerce-smart-coupons' ); ?>" value="<?php echo esc_attr( $cart_max_quantity ); ?>" min="0">
				</p>
				<div class=" wc_sc_product_quantity" style="<?php echo isset( $product_quantity_restrictions['type'] ) && ( 'product' === $product_quantity_restrictions['type'] ) ? '' : 'display: none;'; ?>">
					<?php
					$product_quantities = isset( $product_quantity_restrictions['values']['product'] ) ? $product_quantity_restrictions['values']['product'] : array();
					if ( ! empty( $product_quantities ) ) {
						$display_label = true;
						foreach ( $product_quantities as $product_id => $value ) {
							if ( 0 !== $product_id ) {
								$product = wc_get_product( $product_id );
								if ( ! empty( $product ) && is_object( $product ) ) {
									$product_name         = is_callable( array( $product, 'get_name' ) ) ? $product->get_name() : '';
									$product_max_quantity = isset( $value['max'] ) ? $value['max'] : '';
									$product_min_quantity = isset( $value['min'] ) ? $value['min'] : '';
									?>
									<p class="form-field" data-index="<?php echo esc_attr( $product_id ); ?>">
										<?php if ( true === $display_label ) { ?>
											<label><?php echo esc_html__( 'Products', 'woocommerce-smart-coupons' ); ?></label>
										<?php } ?>
										<span>
											<input type="text" name="wc_sc_product_quantity_restrictions[values][product][<?php echo esc_attr( $product_id ); ?>][product_id]" placeholder="<?php echo esc_attr( $product_id ); ?>" value="<?php echo esc_attr( $product_name ); ?>" disabled>
											<input type="number" name="wc_sc_product_quantity_restrictions[values][product][<?php echo esc_attr( $product_id ); ?>][min]" class="product_min_quantity_field" placeholder="<?php echo esc_attr__( 'No minimum', 'woocommerce-smart-coupons' ); ?>" value="<?php echo esc_attr( $product_min_quantity ); ?>" min="0">
											<input type="hidden" name="wc_sc_product_quantity_restrictions[values][product][<?php echo esc_attr( $product_id ); ?>][max]" class="product_max_quantity_field" placeholder="<?php echo esc_attr__( 'No maximum', 'woocommerce-smart-coupons' ); ?>" value="<?php echo esc_attr( $product_max_quantity ); ?>" min="0">
										</span>
									</p>
									<?php
								}
							}
							$display_label = false;
						}
					} else {
						?>
						<p class="form-field">
							<span class='left_column'><label><?php echo esc_html__( 'Products', 'woocommerce-smart-coupons' ); ?></label></span>
							<label class="hypertext_css wc_sc_click_select_product">
								<?php echo esc_html__( 'Please select some products', 'woocommerce-smart-coupons' ); ?>
							</label>
						</p>
						<?php
					}
					?>
				</div>
				<div class=" wc_sc_category_quantity" style="<?php echo isset( $product_quantity_restrictions['type'] ) && ( 'product' === $product_quantity_restrictions['type'] ) ? '' : 'display: none;'; ?>">
					<?php
					$product_category_quantities = isset( $product_quantity_restrictions['values']['product_category'] ) ? $product_quantity_restrictions['values']['product_category'] : array();
					if ( ! empty( $product_category_quantities ) ) {
						$i = 0;
						foreach ( $product_category_quantities as $category_id => $value ) {
							if ( 0 !== $category_id ) {
								$term = get_term_by( 'id', $category_id, 'product_cat', ARRAY_A );
								if ( ! empty( $term ) && is_array( $term ) ) {
									$category_name = isset( $term['name'] ) ? $term['name'] : '';
									?>
									<p class="form-field" data-index="<?php echo esc_attr( $category_id ); ?>">
										<?php if ( 0 === $i ) { ?>
											<label><?php echo esc_html__( 'Categories', 'woocommerce-smart-coupons' ); ?></label>
										<?php } ?>
										<span>
											<input type="text" name="wc_sc_product_quantity_restrictions[values][product_category][<?php echo esc_attr( $category_id ); ?>][category_id]" placeholder="<?php echo esc_attr( $category_id ); ?>" value="<?php echo esc_attr( $category_name ); ?>" disabled>
											<input type="number" name="wc_sc_product_quantity_restrictions[values][product_category][<?php echo esc_attr( $category_id ); ?>][min]" class="product_min_quantity_field" placeholder="<?php echo esc_attr__( 'No minimum', 'woocommerce-smart-coupons' ); ?>" value="<?php echo esc_attr( isset( $value['min'] ) ? $value['min'] : '' ); ?>" min="0">
											<input type="hidden" name="wc_sc_product_quantity_restrictions[values][product_category][<?php echo esc_attr( $category_id ); ?>][max]" class="product_max_quantity_field" placeholder="<?php echo esc_attr__( 'No maximum', 'woocommerce-smart-coupons' ); ?>" value="<?php echo esc_attr( isset( $value['max'] ) ? $value['max'] : '' ); ?>" min="0">
										</span>
									</p>
									<?php

								}
							}
							$i++;
						}
					} else {
						?>
						<p class="form-field">
							<span class='left_column'><label><?php echo esc_html__( 'Categories', 'woocommerce-smart-coupons' ); ?></label></span>
							<label for="product_categories" class="hypertext_css">
								<?php echo esc_html__( 'Please select some categories', 'woocommerce-smart-coupons' ); ?>
							</label>
						</p>
						<?php
					}
					?>
				</div>
			</div>


			<div class="wc_sc_auto_generate_product_quantity" style="display: none;">
				<p class="form-field" data-index="{i}">
					<label>{p}</label>
					<span>
						<input type="text" name="wc_sc_product_quantity_restrictions[values][product][{i}][product_id]" placeholder="{i}" value="{n}" disabled>
						<input type="number" name="wc_sc_product_quantity_restrictions[values][product][{i}][min]" class="product_min_quantity_field" placeholder="<?php echo esc_attr__( 'No minimum', 'woocommerce-smart-coupons' ); ?>" min="0">
						<input type="hidden" name="wc_sc_product_quantity_restrictions[values][product][{i}][max]" class="product_max_quantity_field" placeholder="<?php echo esc_attr__( 'No maximum', 'woocommerce-smart-coupons' ); ?>" min="0">
					</span>
				</p>
			</div>


			<div class="wc_sc_auto_generate_category_quantity" style="display: none;">
				<p class="form-field" data-index="{i}">
					<label>{c}</label>
					<span>
						<input type="text" name="wc_sc_product_quantity_restrictions[values][product_category][{i}][category_id]" placeholder="{i}" value="{n}" disabled>
						<input type="number" name="wc_sc_product_quantity_restrictions[values][product_category][{i}][min]" class="product_min_quantity_field" placeholder="<?php echo esc_attr__( 'No minimum', 'woocommerce-smart-coupons' ); ?>" min="0">
						<input type="hidden" name="wc_sc_product_quantity_restrictions[values][product_category][{i}][max]" class="product_max_quantity_field" placeholder="<?php echo esc_attr__( 'No maximum', 'woocommerce-smart-coupons' ); ?>" min="0">
					</span>
				</p>
			</div>

			<div class="wc_sc_empty_products" style="display: none;">
				<p class="form-field">
					<span class='left_column'><label><?php echo esc_html__( 'Products', 'woocommerce-smart-coupons' ); ?></label></span>
					<label class="hypertext_css wc_sc_click_select_product">
						<?php echo esc_html__( 'Please select some products', 'woocommerce-smart-coupons' ); ?>
					</label>
				</p>
			</div>
			<div class="wc_sc_empty_categories" style="display: none;">
				<p class="form-field">
					<span class='left_column'><label><?php echo esc_html__( 'Categories', 'woocommerce-smart-coupons' ); ?></label></span>
					<label for="product_categories" class="hypertext_css">
						<?php echo esc_html__( 'Please select some categories', 'woocommerce-smart-coupons' ); ?>
					</label>
				</p>
			</div>
			<div class="wc_sc_product_section_title" style="display: none;">
				<?php echo esc_html__( 'Products', 'woocommerce-smart-coupons' ); ?>
			</div>
			<div class="wc_sc_category_section_title" style="display: none;">
				<?php echo esc_html__( 'Categories', 'woocommerce-smart-coupons' ); ?>
			</div>
			<?php
		}


		/**
		 * Styles and scripts
		 */
		public function styles_and_scripts() {
			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}
			?>
			<style type="text/css">
				.wc_sc_product_quantity_type_product {
					margin-left: 10px !important;
				}

				.product_min_quantity_field {
					width: 15% !important;
					margin-left: 10px !important;
				}
				.hypertext_css {
					width: 50% !important;
					color: #1870f0 !important;
					text-decoration: underline !important;
					width: 275px !important;
					margin-left: 1px !important;
				}
				.width_32{
					width: 32% !important;
				}
				.padding_left_3{
					padding-left: 3px;
				}
				label.cart_quantity{
					width: 75px !important;
					margin-left: 1px !important;
				}

				label.product_quantity {
					margin-left: 0px !important;
				}
			</style>
			<script type="text/javascript">
				jQuery(document).ready(function ($) {
					/**
					 *Change quantity type cart or product.
					 */
					$(document).on('change', '.wc_sc_product_quantity_type', function () {
						let value = $('input[name="wc_sc_product_quantity_restrictions[type]"]:checked').val();
						if ("product" === value) {
							$('.wc_sc_cart_quantity').hide();
							$('.wc_sc_product_quantity, .wc_sc_category_quantity').show();
						} else {
							$('.wc_sc_cart_quantity').show();
							$('.wc_sc_product_quantity, .wc_sc_category_quantity').hide();
						}
					});
					/**
					 *Auto add product field.
					 */
					$(document).on('change', 'select[name="product_ids[]"]', function () {
						var product_element_count = 0;
						$('.wc_sc_product_quantity').empty();
						let options =  $("select[name='product_ids[]'] option:selected").val();
						if( options === undefined ){
							let product_empty = $('.wc_sc_empty_products').html();
							$('.wc_sc_product_quantity').append(product_empty);
						}

						$("select[name='product_ids[]'] option:selected").each(function () {
							var $this = $(this);
							if ($this.length) {
								product_element_count = parseInt(product_element_count) + parseInt(1);
								let product_name = $this.text();
								let product_id = $this.val();
								let section_title = $('.wc_sc_product_section_title').text();
								let wc_sc_product_quantity = $('.wc_sc_auto_generate_product_quantity').html();
								wc_sc_product_quantity = wc_sc_product_quantity.replace(new RegExp('{i}', 'g'), product_id);
								wc_sc_product_quantity = wc_sc_product_quantity.replace(new RegExp('{n}', 'g'), product_name);

								if (product_element_count >= 2) {
									wc_sc_product_quantity = wc_sc_product_quantity.replace(new RegExp('{p}', 'g'), '');
								} else {
									wc_sc_product_quantity = wc_sc_product_quantity.replace(new RegExp('{p}', 'g'), section_title);
								}
								$('.wc_sc_product_quantity').append(wc_sc_product_quantity);
							}
						});
					});

					/**
					 *Auto add category field.
					 */
					$(document).on('change', 'select[name="product_categories[]"]', function () {
						var category_element_count = 0;
						$('.wc_sc_category_quantity').empty();
						let options =  $("#product_categories option:selected").val();
						if( options === undefined ){
							let category_empty = $('.wc_sc_empty_categories').html();
							$('.wc_sc_category_quantity').append(category_empty);
						}
						$("#product_categories option:selected").each(function () {
							var $this = $(this);
							if ($this.length) {
								category_element_count = parseInt(category_element_count) + parseInt(1);
								let category_name = $this.text();
								let category_id = $this.val();
								let section_title = $('.wc_sc_category_section_title').text();
								let wc_sc_category_quantity = $('.wc_sc_auto_generate_category_quantity').html();
								wc_sc_category_quantity = wc_sc_category_quantity.replace(new RegExp('{i}', 'g'), category_id);
								wc_sc_category_quantity = wc_sc_category_quantity.replace(new RegExp('{n}', 'g'), category_name);

								if (category_element_count >= 2) {
									wc_sc_category_quantity = wc_sc_category_quantity.replace(new RegExp('{c}', 'g'), '');
								} else {
									wc_sc_category_quantity = wc_sc_category_quantity.replace(new RegExp('{c}', 'g'), section_title);
								}
								$('.wc_sc_category_quantity').append(wc_sc_category_quantity);
							}
						});
					});

					$("body").on('click', '.wc_sc_click_select_product', function() {
						$(".wc-product-search").parent('p:nth-child(1)').css('background-color', '#ffffcc').animate({
							backgroundColor: 'transparent'
						}, 2000);
						$('html, body').animate({
							'scrollTop' : $(".wc-product-search").parent('p:nth-child(1)').position().top,
						});
					});
				});
			</script>
			<?php
		}

		/**
		 * Save coupon by product quantity data in meta
		 *
		 * @param Integer $post_id The coupon post ID.
		 * @param WP_Post $post The coupon post.
		 */
		public function process_meta( $post_id = 0, $post = null ) {

			if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( is_int( wp_is_post_revision( $post ) ) ) {
				return;
			}
			if ( is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}
            if (empty($_POST['woocommerce_meta_nonce']) || !wp_verify_nonce(wc_clean(wp_unslash($_POST['woocommerce_meta_nonce'])), 'woocommerce_save_data')) { // phpcs:ignore
				return;
			}
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			if ( 'shop_coupon' !== $post->post_type ) {
				return;
			}

            $product_quantity_restrictions = (isset($_POST['wc_sc_product_quantity_restrictions'])) ? wc_clean(wp_unslash($_POST['wc_sc_product_quantity_restrictions'])) : array(); // phpcs:ignore

			if ( ! isset( $product_quantity_restrictions['condition'] ) ) {
				$product_quantity_restrictions['condition'] = 'any'; // Values: any, all.
			}

			if ( isset( $product_quantity_restrictions['values']['product']['{i}'] ) ) {
				unset( $product_quantity_restrictions['values']['product']['{i}'] );
			}

			if ( isset( $product_quantity_restrictions['values']['product_category']['{i}'] ) ) {
				unset( $product_quantity_restrictions['values']['product_category']['{i}'] );
			}

			if ( ! empty( $product_quantity_restrictions ) ) {
				foreach ( $product_quantity_restrictions as $restriction_key => $restrictions ) {
					if ( 'values' === $restriction_key ) {
						foreach ( $restrictions['product'] as $id => $restriction ) {
							if ( 0 !== $id && isset( $product_quantity_restrictions['values']['product'][ $id ]['max'] ) ) {
								$product_quantity_restrictions['values']['product'][ $id ]['max'] = isset( $restriction['min'] ) ? $restriction['min'] : '';
							}
						}

						foreach ( $restrictions['product_category'] as $id => $restriction ) {
							if ( 0 !== $id && isset( $product_quantity_restrictions['values']['product_category'][ $id ]['max'] ) ) {
								$product_quantity_restrictions['values']['product_category'][ $id ]['max'] = isset( $restriction['min'] ) ? $restriction['min'] : '';
							}
						}
					}
				}
			}
			$coupon = new WC_Coupon( $post_id );

			if ( is_object( $coupon ) && is_callable( array( $coupon, 'update_meta_data' ) ) ) {
				$coupon->update_meta_data( 'wc_sc_product_quantity_restrictions', $product_quantity_restrictions );
				$coupon->save();
			} else {
				update_post_meta( $post_id, 'wc_sc_product_quantity_restrictions', $product_quantity_restrictions );
			}
		}

		/**
		 * Validate the coupon based on product quantity
		 *
		 * @param boolean   $valid Is valid or not.
		 * @param WC_Coupon $coupon The coupon object.
		 *
		 * @return boolean           Is valid or not
		 * @throws Exception If the coupon is invalid.
		 */
		public function validate( $valid = false, $coupon = null ) {

			// If coupon is invalid already, no need for further checks.
			if ( false === $valid ) {
				return $valid;
			}

			if ( ! is_a( $coupon, 'WC_Coupon' ) ) {
				return $valid;
			}

			$coupon_id                     = ( $this->is_wc_gte_30() ) ? $coupon->get_id() : $coupon->id;
			$product_quantity_restrictions = is_callable( array( $coupon, 'get_meta' ) ) ? $coupon->get_meta( 'wc_sc_product_quantity_restrictions' ) : '';
			if ( empty( $product_quantity_restrictions ) ) {
				$product_quantity_restrictions = get_post_meta( $coupon_id, 'wc_sc_product_quantity_restrictions', true );
			}
			if ( is_array( $product_quantity_restrictions ) && ! empty( $product_quantity_restrictions ) ) {
				$type      = ! empty( $product_quantity_restrictions['type'] ) ? $product_quantity_restrictions['type'] : '';
				$values    = ! empty( $product_quantity_restrictions['values'] ) ? $product_quantity_restrictions['values'] : '';
				$condition = ! empty( $product_quantity_restrictions['condition'] ) ? $product_quantity_restrictions['condition'] : 'any';

				switch ( $type ) {
					case 'cart':
						$min           = ! empty( $product_quantity_restrictions['values']['cart']['min'] ) ? $product_quantity_restrictions['values']['cart']['min'] : 0;
						$max           = ! empty( $product_quantity_restrictions['values']['cart']['max'] ) ? $product_quantity_restrictions['values']['cart']['max'] : 0;
						$cart_quantity = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_cart_contents_count' ) ) ) ? WC()->cart->get_cart_contents_count() : 0;

						if ( empty( $min ) && empty( $max ) ) {
							return $valid;
						} elseif ( empty( $min ) && ! empty( $max ) && $cart_quantity <= $max ) {
							return $valid;
						} elseif ( empty( $max ) && ! empty( $min ) && $cart_quantity >= $min ) {
							return $valid;
						} elseif ( ! empty( $min ) && ! empty( $max ) && $cart_quantity >= $min && $cart_quantity <= $max ) {
							return $valid;
						} else {
							$messages = array(
								__( 'Your cart does not meet the quantity requirement.', 'woocommerce-smart-coupons' ),
							);
							if ( $cart_quantity > $max ) {
								/* translators: 1. Number of quantity 2. Singular or plural text based on number of quantities */
								$messages[] = sprintf( __( 'Your cart should have a maximum of %1$d %2$s in total.', 'woocommerce-smart-coupons' ), $max, _n( 'quantity', 'quantities', $max ) );
							}
							if ( $cart_quantity < $min ) {
								/* translators: 1. Number of quantity 2. Singular or plural text based on number of quantities */
								$messages[] = sprintf( __( 'Your cart should have a minimum of %1$d %2$s in total.', 'woocommerce-smart-coupons' ), $min, _n( 'quantity', 'quantities', $min ) );
							}
							throw new Exception( implode( ' ', $messages ) );
						}
						break;
					default:
					case 'product':
						$product_quantity_restrictions          = ! empty( $values['product'] ) ? $values['product'] : array();
						$product_category_quantity_restrictions = ! empty( $values['product_category'] ) ? $values['product_category'] : array();
						$product_condition                      = $this->process_product_quantities( $product_quantity_restrictions, $condition );
						$product_category_condition             = $this->process_category_quantities( $product_category_quantity_restrictions, $condition );

						if ( false === $product_condition && false === $product_category_condition ) {
							throw new Exception( __( 'Your cart does not meet the product quantity requirement.', 'woocommerce-smart-coupons' ) );
						} elseif ( 'empty' === $product_condition && false === $product_category_condition ) {
							throw new Exception( __( 'Your cart does not meet the product quantity requirement.', 'woocommerce-smart-coupons' ) );
						} elseif ( false === $product_condition && 'empty' === $product_category_condition ) {
							throw new Exception( __( 'Your cart does not meet the product quantity requirement.', 'woocommerce-smart-coupons' ) );
						}
						break;
				}
			}

			return $valid;

		}

		/**
		 * Process cart product quantities
		 *
		 * @param array  $product_quantity_restrictions values.
		 * @param string $condition any/all.
		 * @return bool
		 * @throws Exception If empty product quantities.
		 */
		public function process_product_quantities( $product_quantity_restrictions = array(), $condition = 'any' ) {
			if ( ! empty( $product_quantity_restrictions ) ) {
				$status = array();

				foreach ( $product_quantity_restrictions as $id => $restriction ) {
					if ( 0 === $id ) {
						continue;
					}
					$min_quantity            = isset( $restriction['min'] ) ? $restriction['min'] : 0;
					$max_quantity            = isset( $restriction['max'] ) ? $restriction['max'] : 0;
					$cart_content            = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_cart' ) ) ) ? WC()->cart->get_cart() : array();
					$cart_product_quantities = $this->cart_product_quantities( $cart_content );

					$product_quantity = isset( $cart_product_quantities[ $id ] ) ? $cart_product_quantities[ $id ] : 0;

					if ( empty( $min_quantity ) && empty( $max_quantity ) ) {
						$status[] = 'empty';
					} elseif ( empty( $min_quantity ) && ! empty( $max_quantity ) && $product_quantity <= $max_quantity ) {
						$status[] = 'true';
					} elseif ( empty( $max_quantity ) && ! empty( $min_quantity ) && $product_quantity >= $min_quantity ) {
						$status[] = 'true';
					} elseif ( ! empty( $min_quantity ) && ! empty( $max_quantity ) && $product_quantity >= $min_quantity && $product_quantity <= $max_quantity ) {
						$status[] = 'true';
					} else {
						$status[] = 'false';
					}
				}

				switch ( $condition ) {
					case 'all':
						if ( in_array( 'false', $status, true ) ) {
							return false;
						} elseif ( in_array( 'true', $status, true ) ) {
							return true;
						} else {
							return 'empty';
						}
					default:
					case 'any':
						if ( in_array( 'true', $status, true ) ) {
							return true;
						} elseif ( in_array( 'false', $status, true ) ) {
							return false;
						} else {
							return 'empty';
						}
				}
			}
			return 'empty';
		}

		/**
		 * Process cart category quantities
		 *
		 * @param array  $product_category_quantity_restrictions values.
		 * @param string $condition any/all.
		 * @return bool
		 * @throws Exception If empty cart category quantities.
		 */
		public function process_category_quantities( $product_category_quantity_restrictions, $condition ) {

			if ( ! empty( $product_category_quantity_restrictions ) ) {
				$status = array();

				$cart_content = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_cart' ) ) ) ? WC()->cart->get_cart() : array();

				$cart_product_categories_quantities = $this->cart_product_categories_quantities( $cart_content );
				foreach ( $product_category_quantity_restrictions as $id => $restriction ) {
					$min_quantity = isset( $restriction['min'] ) ? $restriction['min'] : '';
					$max_quantity = isset( $restriction['max'] ) ? $restriction['max'] : '';
					if ( 0 === $id ) {
						continue;
					}
					$category_quantity = isset( $cart_product_categories_quantities[ $id ] ) ? $cart_product_categories_quantities[ $id ] : 0;

					if ( empty( $min_quantity ) && empty( $max_quantity ) ) {
						$status[] = 'empty';
					} elseif ( empty( $min_quantity ) && ! empty( $max_quantity ) && $category_quantity <= $max_quantity ) {
						$status[] = 'true';
					} elseif ( empty( $max_quantity ) && ! empty( $min_quantity ) && $category_quantity >= $min_quantity ) {
						$status[] = 'true';
					} elseif ( ! empty( $min_quantity ) && ! empty( $max_quantity ) && $category_quantity >= $min_quantity && $category_quantity <= $max_quantity ) {
						$status[] = 'true';
					} else {
						$status[] = 'false';
					}
				}

				switch ( $condition ) {
					case 'all':
						if ( in_array( 'false', $status, true ) ) {
							return false;
						} elseif ( in_array( 'true', $status, true ) ) {
							return true;
						} else {
							return 'empty';
						}
					default:
					case 'any':
						if ( in_array( 'true', $status, true ) ) {
							return true;
						} elseif ( in_array( 'false', $status, true ) ) {
							return false;
						} else {
							return 'empty';
						}
				}
			}
			return 'empty';
		}

		/**
		 * Calculate product quantities
		 *
		 * @param array $cart_content cart contents.
		 * @return array
		 */
		public function cart_product_quantities( $cart_content ) {
			if ( empty( $cart_content ) ) {
				return $cart_content;
			}
			$cart_product_quantities = array();
			foreach ( $cart_content as $key => $cart_item ) {
				$quantity     = isset( $cart_item['quantity'] ) ? $cart_item['quantity'] : 0;
				$product_id   = isset( $cart_item['product_id'] ) ? $cart_item['product_id'] : 0;
				$variation_id = isset( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : 0;
				if ( ! empty( $variation_id ) ) {
					if ( isset( $cart_product_quantities[ $variation_id ] ) ) {
						$cart_product_quantities[ $variation_id ] = $cart_product_quantities[ $variation_id ] + $quantity;
					} else {
						$cart_product_quantities[ $variation_id ] = $quantity;
					}
				}

				if ( isset( $cart_product_quantities[ $product_id ] ) ) {
					$cart_product_quantities[ $product_id ] = $cart_product_quantities[ $product_id ] + $quantity;
				} else {
					$cart_product_quantities[ $product_id ] = $quantity;
				}
			}
			return $cart_product_quantities;
		}

		/**
		 * Calculate category quantities
		 *
		 * @param array $cart_content cart contents.
		 * @return array
		 */
		public function cart_product_categories_quantities( $cart_content ) {
			if ( empty( $cart_content ) ) {
				return $cart_content;
			}
			$categories_quantities = array();
			foreach ( $cart_content as $key => $cart_item ) {
				$product  = isset( $cart_item['data'] ) ? $cart_item['data'] : array();
				$quantity = isset( $cart_item['quantity'] ) ? $cart_item['quantity'] : 0;

				if ( is_object( $product ) && is_callable( array( $product, 'get_category_ids' ) ) ) {
					$product_variation = ( is_callable( array( $product, 'is_type' ) ) ) ? $product->is_type( 'variation' ) : false;
					if ( $product_variation ) {
						$parent_id = ( is_callable( array( $product, 'get_parent_id' ) ) ) ? $product->get_parent_id() : 0;
						if ( ! empty( $parent_id ) ) {
							$product = wc_get_product( $parent_id );
						}
					}
					$categories = $product->get_category_ids();
					if ( ! empty( $categories ) ) {
						foreach ( $categories as $category ) {
							if ( isset( $categories_quantities[ $category ] ) ) {
								$categories_quantities[ $category ] = $categories_quantities[ $category ] + $quantity;
							} else {
								$categories_quantities[ $category ] = $quantity;
							}
						}
					}
				}
			}
			return $categories_quantities;
		}

	}
}

WC_SC_Coupons_By_Product_Quantity::get_instance();
