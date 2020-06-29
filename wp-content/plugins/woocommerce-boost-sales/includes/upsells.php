<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WBOOSTSALES_Upsells {
	private $settings;
	private $quantity;
	private $product;
	private $upsells;
	private $variation_id;
	protected $language;

	/**
	 * VI_WBOOSTSALES_Upsells constructor.
	 * Init setting
	 */
	public function __construct( $product_id, $quantity, $upsells, $variation_id = false ) {
		$this->product      = $product_id;
		$this->quantity     = $quantity;
		$this->upsells      = $upsells;
		$this->variation_id = $variation_id;
		$this->language     = '';
		$this->settings     = new VI_WBOOSTSALES_Data();
	}

	/**Use this function to not get affected by filter of function $product->get_image()
	 *
	 * @param $product WC_Product
	 * @param string $size
	 * @param array $attr
	 * @param bool $placeholder
	 *
	 * @return string
	 */
	public static function get_product_image( $product, $size = 'woocommerce_thumbnail', $attr = array(), $placeholder = true ) {
		$image = '';
		if ( $product->get_image_id() ) {
			$image = wp_get_attachment_image( $product->get_image_id(), $size, false, $attr );
		} elseif ( $product->get_parent_id() ) {
			$parent_product = wc_get_product( $product->get_parent_id() );
			if ( $parent_product ) {
				$image = self::get_product_image( $parent_product, $size, $attr, $placeholder );
			}
		}

		if ( ! $image && $placeholder ) {
			$image = wc_placeholder_img( $size, $attr );
		}

		return $image;
	}

	/**
	 * @return false|string
	 */
	public function show_html() {
		global $wbs_language;
		/*Check Coupon*/
		$min_amount = $this->settings->get_coupon( 'min' );
		$cart       = WC()->cart;
		$total_cart = $cart->cart_contents_total;
//		if ( wc_tax_enabled() ) {
//			$tax        = $cart->get_cart_contents_tax();
//			$total_cart += $tax;
//		}
		$discount_bar_return_html = '';
		if ( $this->settings->get_option( 'enable_discount' ) ) {
			$coupon_id = $this->settings->get_option( 'coupon' );
			if ( $coupon_id ) {
				$coupon         = new WC_Coupon( $coupon_id );
				$coupon_code    = $coupon->get_code();
				$total_discount = WC()->cart->get_coupon_discount_amount( $coupon_code );
				$total_cart     += $total_discount;
				if ( $total_cart >= $min_amount ) {
					return $discount_bar_return_html;
				}
			}
		}
		/*Hide on single page*/
		if ( is_product() && $this->settings->get_option( 'hide_on_single_product_page' ) ) {
			return $discount_bar_return_html;
		}

		if ( ! is_array( $this->upsells ) || ! count( $this->upsells ) ) {
			return $discount_bar_return_html;
		}
		$total_cart_html          = $cart->get_cart_subtotal();
		$number_cart              = $cart->get_cart_contents_count();
		$cart_url                 = wc_get_cart_url();
		$checkout_url             = wc_get_checkout_url();
		$continue_shopping_action = $this->settings->get_option( 'continue_shopping_action' );
		switch ( $continue_shopping_action ) {
			case 'shop':
				$shop_url = wc_get_page_permalink( 'shop' );
				break;
			case 'home':
				$shop_url = wc_get_page_permalink( 'home' );
				break;
			case 'stay':
			default:
				$shop_url = '#';
		}
		$select_template = $this->settings->get_option( 'select_template' );
		$message_bought  = $this->settings->get_option( 'message_bought', $wbs_language );
		$item_per_row    = $this->settings->get_option( 'item_per_row' );

		$main_product = wc_get_product( $this->product );
//		$product_image    = woocommerce_get_product_thumbnail();
		$product_image    = self::get_product_image( $main_product );
		$product_title    = $main_product->get_name();
		$main_product_url = $main_product->get_permalink();
		$total_product    = wc_get_price_to_display( $main_product ) * $this->quantity;
		if ( $this->variation_id ) {
			$variation = wc_get_product( $this->variation_id );
			if ( $variation ) {
				$variation_image = self::get_product_image( $variation );
				if ( $variation_image ) {
					$product_image = $variation_image;
				}
				$product_title    = $variation->get_name();
				$main_product_url = $variation->get_permalink();
				$total_product    = wc_get_price_to_display( $variation ) * $this->quantity;
			}
		}
		$upsells_count = 0;
		ob_start();
		echo $discount_bar_return_html;
		if ( $this->settings->get_detect() === 'mobile' && $this->settings->get_option( 'upsell_mobile_template' ) === 'scroll' ) {
			?>
            <div class="wbs-upsells-items wbs-upsells-items-mobile">
				<?php
				foreach ( $this->upsells as $upsell_id ) {
					$upsell_product = wc_get_product( $upsell_id );
					if ( $upsell_product ) {
						if ( ! $upsell_product->is_in_stock() && $this->settings->get_option( 'hide_out_stock' ) ) {
							continue;
						}
						$upsells_count ++;
						?>
                        <div class="wbs-upsells-item">
                            <div class="wbs-upsells-item-main">
                                <div class="wbs-upsells-item-left">
									<?php
									$product_url = $upsell_product->get_permalink();
									if ( $product_url ) {
										?>
                                        <a href="<?php echo $product_url ?>" target="_blank"
                                           class="wbs-upsells-item-url">
											<?php
											do_action( 'woocommerce_boost_sales_before_shop_loop_item_title', $upsell_product );
											?>
                                        </a>
										<?php
									} else {
										do_action( 'woocommerce_boost_sales_before_shop_loop_item_title', $upsell_product );
									}
									?>
                                </div>
                                <div class="wbs-upsells-item-right">
									<?php if ( $product_url ) {
										?>
                                        <a href="<?php echo $product_url ?>" target="_blank"
                                           class="wbs-upsells-item-url">
											<?php
											do_action( 'woocommerce_boost_sales_shop_loop_item_title', $upsell_product );
											?>
                                        </a>
										<?php
									} else {
										do_action( 'woocommerce_boost_sales_shop_loop_item_title', $upsell_product );
									}
									do_action( 'woocommerce_boost_sales_after_shop_loop_item_title', $upsell_product );
									?>
                                    <div class="product-controls">
                                        <div class="wbs-cart">
											<?php do_action( 'woocommerce_boost_sales_single_product_summary_mobile', $upsell_product ) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wbs-upsells-item-added-to-cart">
                                <span class="wbs-icon-added"></span>
								<?php printf( __( '"%s" has been added to your cart', 'woocommerce-boost-sales' ), get_the_title(), 'woocommerce-boost-sales' ) ?>
                            </div>
                        </div>
						<?php
					}
				}
				?>
            </div>
			<?php
		} else {
			?>
            <div class="vi-flexslider" id="flexslider-up-sell"
                 data-rtl="<?php echo esc_attr( is_rtl() ? 1 : 0 ) ?>"
                 data-item-per-row="<?php echo esc_attr( $item_per_row ); ?>">
                <div class="wbs-upsells wbs-vi-slides">
					<?php
					foreach ( $this->upsells as $upsell_id ) {
						$upsell_product = wc_get_product( $upsell_id );
						if ( $upsell_product ) {
							if ( ! $upsell_product->is_in_stock() && $this->settings->get_option( 'hide_out_stock' ) ) {
								continue;
							}
							$upsells_count ++;
							$product_url = get_the_permalink();
							?>
                            <div class="vi-wbs-chosen wbs-variation wbs-product">
                                <div class="wbs-upsells-add-items"></div>
                                <div class="product-top">
									<?php
									do_action( 'woocommerce_boost_sales_before_shop_loop_item_title', $upsell_product );
									?>
                                </div>
                                <div class="product-desc">
									<?php if ( $product_url ) {
										?>
                                        <a href="<?php echo $product_url ?>" target="_blank"
                                           class="wbs-upsells-item-url">
											<?php
											do_action( 'woocommerce_boost_sales_shop_loop_item_title', $upsell_product );
											?>
                                        </a>
										<?php
									} else {
										do_action( 'woocommerce_boost_sales_shop_loop_item_title', $upsell_product );
									}
									do_action( 'woocommerce_boost_sales_after_shop_loop_item_title', $upsell_product );
									?>
                                </div>
                                <div class="product-controls">
                                    <div class="wbs-cart">
										<?php do_action( 'woocommerce_boost_sales_single_product_summary', $upsell_product ) ?>
                                    </div>
                                </div>
                            </div>
							<?php
						}
					}
					?>
                </div>
            </div>
			<?php
		}
		$upsells_list = ob_get_clean();
		if ( $upsells_count == 0 ) {
			return '';
		}
		ob_start();
		if ( $this->settings->get_option( 'enable_discount' ) ) {
			$discount_bar  = new VI_WBOOSTSALES_Discount_Bar();
			$discount_type = $discount_bar->get_discount_type();
			$this->head_line( $discount_type );
		}
		?>
        <div class="wbs-overlay"></div>
		<?php do_action( 'wbs_before_upsells' ) ?>
        <div class="wbs-wrapper wbs-archive-upsells wbs-upsell-template-<?php echo $select_template ?>"
             style="opacity: 0">
            <div class="wbs-content nktdhtn">
                <div class="wbs-content-inner">
                    <span class="wbs-close" title="Close"><span>X</span></span>

                    <div class="wbs-breadcrum">
						<?php
						if ( $select_template == 1 ) {
							?>
                            <p class="wbs-notify_added wbs-title_style1">
                                <span class="wbs-icon-added"></span> <?php $this->settings->get_option( 'ajax_button' ) ? esc_html_e( 'New item(s) have been added to your cart.', 'woocommerce-boost-sales' ) : printf( _n( '%s new item has been added to your cart', '%s new items have been added to your cart', $this->quantity, 'woocommerce-boost-sales' ), $this->quantity ); ?>
                            </p>
							<?php
						}
						?>
                        <div class="wbs-header-right">
                            <a href="<?php echo esc_url( $cart_url ) ?>"
                               class="wbs-button-view"><?php esc_html_e( 'View Cart', 'woocommerce-boost-sales' ) ?></a>
                            <a href="<?php echo esc_url( $shop_url ) ?>"
                               class="wbs-button-continue <?php esc_attr_e( 'wbs-button-continue-' . $continue_shopping_action ) ?> <?php if ( $total_cart < $min_amount ) {
								   echo 'goto';
							   } ?>"><?php esc_html_e( $this->settings->get_option( 'continue_shopping_title', $wbs_language ) ) ?></a>
                            <a href="<?php echo esc_url( $checkout_url ) ?>"
                               class="wbs-button-check <?php if ( $total_cart >= $min_amount ) {
								   echo 'goto';
							   } ?>">
								<?php
								$checkout_text = apply_filters( 'woocommerce_boost_sales_upsells_checkout_text', esc_html__( 'Checkout', 'woocommerce-boost-sales' ) );
								echo $checkout_text;
								?>
                            </a>
							<?php
							if ( $select_template == 2 ) {
								if ( $this->settings->get_option( 'ajax_button' ) && is_product() ) {
									?>
                                    <p class="wbs-current_total_cart"></p>
									<?php
								} else {
									$temporary_number = sprintf( _n( 'Your current cart(%s product): %s', 'Your current cart(%s products): %s', $number_cart, 'woocommerce-boost-sales' ), $number_cart, $total_cart_html );
									?>
                                    <p class="wbs-current_total_cart"><?php echo $temporary_number ?></p>
									<?php
								}
							}
							?>
                        </div>
                        <div class="wbs-product">
							<?php
							if ( $select_template == 2 ) { ?>
                                <p class="wbs-notify_added wbs-title_style2">
                                    <span class="wbs-icon-added"></span> <?php $this->settings->get_option( 'ajax_button' ) ? esc_html_e( 'New item(s) have been added to your cart.', 'woocommerce-boost-sales' ) : printf( _n( '%s new item has been added to your cart', '%s new items have been added to your cart', $this->quantity, 'woocommerce-boost-sales' ), $this->quantity ) ?>
                                </p>
								<?php
							}
							?>
                            <div class="wbs-p-image">
								<?php
								if ( $main_product_url ) {
									?>
                                    <a href="<?php echo $main_product_url ?>" class="wbs-p-url"
                                       target="_blank"><?php echo $product_image; ?></a>
									<?php
								} else {
									echo $product_image;
								}
								?>
                            </div>
							<?php
							if ( $select_template == 2 ) {
								echo '<div class="wbs-combo_popup_style2">';
							}
							?>
                            <div class="wbs-p-title">
								<?php
								if ( $main_product_url ) {
									?>
                                    <a href="<?php echo $main_product_url ?>" class="wbs-p-url"
                                       target="_blank"><?php echo $product_title; ?></a>
									<?php
								} else {
									echo $product_title;
								}
								?>
                            </div>
                            <div class="wbs-p-price">
                                <div class="wbs-p-quantity">
                                    <span class="wbs-p-quantity-text"><?php esc_html_e( 'Quantity:', 'woocommerce-boost-sales' ); ?></span>
                                    <span class="wbs-p-quantity-number"
                                          style="float: none;"><?php echo esc_html( $this->quantity ); ?></span>
                                </div>
                                <div class="wbs-price-total">
                                    <div class="wbs-total-price"><?php esc_html_e( 'Total', 'woocommerce-boost-sales' ) ?>
                                        <span class="wbs-money"
                                              style="float: none;"><?php echo wc_price( $total_product ); ?></span>
                                    </div>
                                </div>
                            </div>
							<?php
							if ( $select_template == 2 ) {
								echo '</div>';
							}
							?>
                        </div>
                    </div>

                    <div class="wbs-bottom">
						<?php if ( $message_bought ) {
							?>
                            <h3 class="upsell-title"><?php echo str_replace( '{name_product}', $product_title, strip_tags( $message_bought ) ); ?></h3>
                            <hr/>
							<?php
						}
						/*upsells list here*/
						echo $upsells_list;
						do_action( 'woocommerce_boost_sales_after_upsells_list', $main_product,$this->upsells );
						?>
                    </div>
                </div>
            </div>

        </div>
		<?php

		do_action( 'wbs_after_upsells' );

		return ob_get_clean();
	}

	/**
	 * @param $discount_type
	 */
	protected function head_line( $discount_type ) {
		global $wbs_language;
		$head_line   = $this->settings->get_option( 'coupon_desc', $wbs_language );
		$description = str_replace( '{discount_amount}', $discount_type, strip_tags( $head_line ) );
		if ( $description ) {
			?>
            <div class="vi-wbs-headline">
                <div class="vi-wbs-typo-container">
					<?php echo $description ?>
                </div>
            </div>
			<?php
		}
	}
}