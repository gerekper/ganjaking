<?php

/**
 * Class VI_WBOOSTSALES_Frontend_Single
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WBOOSTSALES_Frontend_Cross_Sells {
	protected $settings;

	public function __construct() {
		$this->settings = new VI_WBOOSTSALES_Data();
		/*Check global enable*/
		if ( $this->settings->enable() ) {
			/*Check cross-sell enable*/
			if ( $this->settings->get_option( 'crosssell_enable' ) ) {
				switch ( $this->settings->get_option( 'crosssell_display_on' ) ) {
					case 1:
						add_action( 'woocommerce_single_product_summary', array(
							$this,
							'show_crosssell_product'
						), 50 );
						break;
					case 2:
						add_action( 'woocommerce_after_single_product_summary', array(
							$this,
							'show_crosssell_product'
						), 9 );
						break;
					case 3:
						add_action( 'woocommerce_after_template_part', array(
							$this,
							'woocommerce_after_template_part'
						) );
						break;
				}
				add_action( 'wp_footer', array( $this, 'show_crosssell_popup' ) );
			}
		}
//		add_filter( 'woocommerce_get_price_html', array( $this, 'woocommerce_get_price_html' ), 10, 2 );
	}

	/**Show original price for WBS bundle products on single page
	 *
	 * @param $price_html
	 * @param $product WC_Product_Wbs_Bundle
	 *
	 * @return float|string
	 */
	public function woocommerce_get_price_html( $price_html, $product ) {
		$original_price = '';
		if ( is_product() && $product && $product->is_type( 'wbs_bundle' ) ) {
			$bundled_items = $product->get_bundled_items();
			if ( count( $bundled_items ) ) {
				$array_price = array();
				foreach ( $bundled_items as $bundled_item ) {
					/**
					 * @var WBS_WC_Bundled_Item $bundled_item
					 */
					$bundled_product = $bundled_item->get_product();
					$price           = wc_get_price_to_display( $bundled_product );
					$array_price[]   = $price;
				}
				$sum_pr               = array_sum( $array_price );
				$product_bundle_price = wc_get_price_to_display( $product );
				$save_price           = $sum_pr - $product_bundle_price;
				if ( $save_price > 0 ) {
					ob_start();
					?>
                    <del>
						<?php echo wc_price( $sum_pr ); ?>
                    </del>
					<?php
					$original_price = ob_get_clean();
				}
			}
		}
		$price_html = $original_price . $price_html;

		return $price_html;
	}

	public function woocommerce_after_template_part( $name ) {
		if ( is_product() && $name === 'single-product/tabs/description.php' ) {
			$this->show_crosssell_product();
		}
	}

	/**
	 * Show bundles below description
	 */
	public function show_crosssell_product() {
		if ( $this->settings->get_option( 'crosssells_hide_on_single_product_page' ) && is_product() ) {
			return;
		}
		$product_id      = get_the_ID();
		$other_bundle_id = get_post_meta( $product_id, '_wbs_crosssells_bundle', true );
		if ( $other_bundle_id ) {
			if ( get_post_status( $other_bundle_id ) == 'publish' ) {
				if ( $this->is_bundle_in_cart( $other_bundle_id ) ) {
					return;
				}
				if ( ( $this->settings->get_option( 'hide_out_of_stock' ) && ! $this->is_in_stock( $other_bundle_id ) ) ) {
					return;
				}
				$output = new VI_WBOOSTSALES_Cross_Sells( $other_bundle_id );
				echo $output->show_html( true );
			}
		} else {
			$crosssells = get_post_meta( $product_id, '_wbs_crosssells', true );
			if ( isset( $crosssells[0] ) ) {
				if ( get_post_status( $crosssells[0] ) == 'publish' ) {
					if ( $this->is_bundle_in_cart( $crosssells[0] ) ) {
						return;
					}
					if ( ( $this->settings->get_option( 'hide_out_of_stock' ) && ! $this->is_in_stock( $crosssells[0] ) ) ) {
						return;
					}
					$output = new VI_WBOOSTSALES_Cross_Sells( $crosssells[0] );
					echo $output->show_html( true );
				}
			}
		}

	}

	public function is_bundle_in_cart( $bundle_id ) {
		$return       = false;
		$cart_content = WC()->cart->cart_contents;
		if ( is_array( $cart_content ) && count( $cart_content ) ) {
			foreach ( $cart_content as $key => $value ) {
				if ( $value['product_id'] == $bundle_id && ! empty( $value['wbs_bundled_items'] ) ) {
					$return = true;
					break;
				}
			}
		}

		return $return;
	}

	/**
	 * Show HTML cross sells product
	 */
	public function show_crosssell_popup() {
		if ( ( $this->settings->get_option( 'crosssells_hide_on_single_product_page' ) || $this->settings->get_option( 'crosssell_display_on' ) ) && is_product() ) {
			return;
		}
		$product_id           = filter_input( INPUT_POST, 'add-to-cart', FILTER_SANITIZE_NUMBER_INT );
		$quantity             = filter_input( INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT );
		$enable_cart_page     = $this->settings->get_option( 'enable_cart_page' );
		$enable_checkout_page = $this->settings->get_option( 'enable_checkout_page' );
		$hide_out_of_stock    = $this->settings->get_option( 'hide_out_of_stock' );
		if ( is_product() ) {
			if ( $product_id && $quantity ) {
				$other_bundle_id = get_post_meta( $product_id, '_wbs_crosssells_bundle', true );
				if ( $other_bundle_id ) {
					if ( get_post_status( $other_bundle_id ) == 'publish' ) {
						if ( $this->is_bundle_in_cart( $other_bundle_id ) ) {
							return;
						}
						if ( ( $hide_out_of_stock && ! $this->is_in_stock( $other_bundle_id ) ) ) {
							return;
						}
						$output = new VI_WBOOSTSALES_Cross_Sells( $other_bundle_id );
						echo $output->show_html();
					}
				} else {
					$crosssells = get_post_meta( $product_id, '_wbs_crosssells', true );

					if ( isset( $crosssells[0] ) ) {
						if ( get_post_status( $crosssells[0] ) == 'publish' ) {
							if ( $this->is_bundle_in_cart( $crosssells[0] ) ) {
								return;
							}
							if ( ( $hide_out_of_stock && ! $this->is_in_stock( $crosssells[0] ) ) ) {
								return;
							}
							$output = new VI_WBOOSTSALES_Cross_Sells( $crosssells[0] );
							echo $output->show_html();
						}
					}
				}
			} else {
				$other_bundle_id = get_post_meta( get_the_ID(), '_wbs_crosssells_bundle', true );
				if ( $other_bundle_id ) {
					if ( get_post_status( $other_bundle_id ) == 'publish' ) {
						if ( $this->is_bundle_in_cart( $other_bundle_id ) ) {
							return;
						}
						if ( ( $hide_out_of_stock && ! $this->is_in_stock( $other_bundle_id ) ) ) {
							return;
						}
						$output = new VI_WBOOSTSALES_Cross_Sells( $other_bundle_id );
						echo $output->show_html();
					}
				} else {
					$crosssells = get_post_meta( get_the_ID(), '_wbs_crosssells', true );
					if ( isset( $crosssells[0] ) ) {
						if ( get_post_status( $crosssells[0] ) == 'publish' ) {
							if ( $this->is_bundle_in_cart( $crosssells[0] ) ) {
								return;
							}
							if ( ( $hide_out_of_stock && ! $this->is_in_stock( $crosssells[0] ) ) ) {
								return;
							}
							$output = new VI_WBOOSTSALES_Cross_Sells( $crosssells[0] );
							echo $output->show_html();
						}
					}
				}
			}
		}

		if ( ( is_checkout() && $enable_checkout_page ) || ( is_cart() && $enable_cart_page ) ) {

			$items = WC()->cart->get_cart_contents();
			if ( count( $items ) ) {
				$crosssells = array();
				$p_cart     = array();
				foreach ( $items as $item ) {
					if ( ! empty( $item['wbs_bundled_by'] ) ) {
						continue;
					}
					$data            = array();
					$p_cart[]        = $item['product_id'];
					$other_bundle_id = get_post_meta( $item['product_id'], '_wbs_crosssells_bundle', true );
					if ( $other_bundle_id ) {
						$data['quantity'] = $item['quantity'];
						$data['id']       = $other_bundle_id;
						$crosssells[]     = $data;
					} else {
						$cross_sell_id = get_post_meta( $item['product_id'], '_wbs_crosssells', true );

						if ( isset( $cross_sell_id[0] ) ) {
							$data['quantity'] = $item['quantity'];
							$data['id']       = $cross_sell_id[0];
							if ( get_post_status( $data['id'] ) == 'publish' ) {
								$crosssells[] = $data;
							}
						} else {
							continue;
						}
					}

				}
				$crosssells = array_filter( $crosssells );

				if ( count( $crosssells ) ) {
					$crosssells = array_values( $crosssells );

					$check_opt = 0;
					if ( is_checkout() ) {
						$check_opt = $this->settings->get_option( 'checkout_page_option' );
					} elseif ( is_cart() ) {
						$check_opt = $this->settings->get_option( 'cart_page_option' );
					}
					switch ( $check_opt ) {
						case  1:
							$bundle_id = $this->get_random_bundle_id( $crosssells, $hide_out_of_stock );
							break;
						case  2:
							$max       = 0;
							$bundle_id = '';
							foreach ( $crosssells as $crosssell ) {
								if ( $hide_out_of_stock && ! $this->is_in_stock( $crosssell['id'] ) ) {
									continue;
								}
								$price = wc_get_product( $crosssell['id'] )->get_price();
								if ( $max < $price ) {
									$max       = $price;
									$bundle_id = $crosssell['id'];
								}
							}

							break;
						default:
							$max       = 0;
							$bundle_id = '';
							foreach ( $crosssells as $crosssell ) {
								if ( $hide_out_of_stock && ! $this->is_in_stock( $crosssell['id'] ) ) {
									continue;
								}
								$quantity = $crosssell['quantity'];
								if ( $max < $quantity ) {
									$max       = $quantity;
									$bundle_id = $crosssell['id'];
								}
							}
					}
					if ( ! $this->settings->get_option( 'bundle_added' ) ) {
						if ( in_array( $bundle_id, $p_cart ) ) {
							return;
						}
					}
					if ( $bundle_id ) {
						if ( get_post_status( $bundle_id ) == 'publish' ) {
							$output = new VI_WBOOSTSALES_Cross_Sells( $bundle_id );
							echo $output->show_html();
						}
					}
				}
			}
		}

	}

	/**
	 * @param $bundle_id
	 *
	 * @return bool
	 */
	protected function is_in_stock( $bundle_id ) {
		$instock        = true;
		$product_bundle = wc_get_product( $bundle_id );
		if ( ! $product_bundle ) {
			return false;
		} elseif ( ! $product_bundle->is_type( 'wbs_bundle' ) ) {
			return $product_bundle->is_in_stock();
		}
		$bundled_items = $product_bundle->get_bundled_items();
		if ( ! count( $bundled_items ) ) {
			return false;
		}
		foreach ( $bundled_items as $bundled_item ) {
			if ( ! $bundled_item->is_in_stock() ) {
				$instock = false;
				break;
			}
		}

		return $instock;
	}

	protected function get_random_bundle_id( $crosssells, $hide_out_of_stock ) {
		$bundle_id = '';
		if ( count( $crosssells ) ) {
			$index     = rand( 0, count( $crosssells ) - 1 );
			$bundle_id = $crosssells[ $index ]['id'];
			if ( $hide_out_of_stock && ! $this->is_in_stock( $bundle_id ) ) {
				unset( $crosssells[ $index ] );
				$bundle_id = $this->get_random_bundle_id( $crosssells, $hide_out_of_stock );
			}
		}

		return $bundle_id;
	}
}