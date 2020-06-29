<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WBOOSTSALES_Cross_Sells {
	private $settings;
	private $cross_sells;

	/**
	 * VI_WBOOSTSALES_Upsells constructor.
	 * Init setting
	 */
	public function __construct( $cross_sells ) {
		$this->settings    = new VI_WBOOSTSALES_Data();
		$this->cross_sells = $cross_sells;
	}

	/**
	 * Show Upsell front end
	 * @return string
	 */
	public function show_html( $layout = false ) {
		global $wbs_language;
		/*Check product bunles*/
		if ( ! $this->cross_sells ) {
			return false;
		}
		$product = wc_get_product( $this->cross_sells );

		if ( $product->get_type() != 'wbs_bundle' || $product->get_status() != 'publish' ) {
			return false;
		}

		$crosssell_description = $this->settings->get_option( 'crosssell_description', $wbs_language );

		if ( ! $layout ) {
			$icon_position = $this->settings->get_option( 'icon_position' );
			$init_delay    = $this->settings->get_option( 'init_delay' );
			$open          = $this->settings->get_option( 'enable_cross_sell_open' );
			$added         = filter_input( INPUT_POST, 'add-to-cart', FILTER_SANITIZE_NUMBER_INT );
			$quantity      = filter_input( INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT );
			if ( $open && is_product() && $added && $quantity ) {
				$open = 0;
			}
			$init_random = array();
			if ( $init_delay ) {
				$init_random = array_filter( explode( ',', $init_delay ) );
			}
			if ( count( $init_random ) == 2 ) {
				$init_delay = rand( $init_random[0], $init_random[1] );
			}

			if ( count( $product->bundle_data ) ) {
				/*Check turn off gift icon*/
				if ( ! $this->settings->get_option( 'hide_gift' ) ) {
					switch ( $this->settings->get_option( 'icon' ) ) {
						case 1:
							$icon = ' wbs-icon-gift';
							break;
						case 2:
							$icon = ' wbs-icon-custom';
							break;
						default:
							$icon = ' wbs-icon-font';
					}
					ob_start();
					?>
                    <div id="gift-button"
                         class="gift-button animated <?php echo $icon_position == 0 ? 'gift_right' : 'gift_left';
					     echo $icon; ?>" style="display: none;"">
                    </div>
					<?php
				}
				$class = 'woocommerce-boost-sales';
				if ( $this->settings->get_detect() === 'mobile' ) {
					$class .= ' woocommerce-boost-sales-mobile';
				}
				?>
                <div id="wbs-content-cross-sells" class="<?php esc_attr_e( $class ) ?>" style="display: none"
                     data-initial_delay="<?php echo esc_attr( $init_delay ); ?>"
                     data-open="<?php echo esc_attr( $open ); ?>">
                    <div class="wbs-overlay"></div>
                    <div class="wbs-wrapper">
                        <div class="wbs-content-crossell <?php echo $icon_position == 0 ? 'gift_right' : 'gift_left'; ?>">
                            <div class="wbs-content-inner wbs-content-inner-crs">
                                <span class="wbs-close" title="Close"><span>X</span></span>
                                <div class="wbs-added-to-cart-overlay">
                                    <div class="wbs-loading"></div>
                                    <div class="wbs-added-to-cart-overlay-content">
                                        <span class="wbs-icon-added"></span>
										<?php esc_html_e( 'Added to cart', 'woocommerce-boost-sales' ) ?>
                                    </div>
                                </div>
                                <div class="wbs-bottom">
									<?php
									if ( $crosssell_description ) {
										?>
                                        <div class="crosssell-title"><?php echo esc_html( $crosssell_description ) ?></div>
										<?php
									}
									?>
                                    <form class="woocommerce-boost-sales-cart-form" method="post"
                                          enctype='multipart/form-data'>
                                        <div class="wbs-crosssells">
											<?php
											$bundle_front_end = new VI_WBOOSTSALES_Frontend_Bundles();

											wc_setup_product_data( $product->get_id() );
											$return = $bundle_front_end->show_crossell_html();
											if ( false === $return ) {
												ob_end_clean();

												return '';
											} else {
												echo $return;
											}
											wp_reset_postdata();
											?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
				<?php
				return ob_get_clean();
			}
		} else {
			$class = 'wbs-content-cross-sells-product-single-container';
			if ( $this->settings->get_detect() === 'mobile' ) {
				$class .= ' woocommerce-boost-sales-mobile';
			}
			ob_start();
			?>
            <div class="<?php esc_attr_e( $class ) ?>">
				<?php
				$class = '';
				if ( $this->settings->get_option( 'crosssell_display_on_slide' ) ) {
					$class = 'crosssell-display-on-slide';
				}
				?>
                <div id="wbs-content-cross-sells-product-single" class="<?php esc_attr_e( $class ) ?>">
					<?php if ( $crosssell_description ) { ?>
                        <div class="crosssell-title"><?php echo esc_html( $crosssell_description ) ?></div>
					<?php } ?>
                    <form class="woocommerce-boost-sales-cart-form" method="post" enctype='multipart/form-data'>
                        <div class="wbs-crosssells">
							<?php
							$bundle_front_end = new VI_WBOOSTSALES_Frontend_Bundles();

							wc_setup_product_data( $product->get_id() );
							if ( $class ) {

								$return = $bundle_front_end->show_crossell_html();
								if ( false === $return ) {
									ob_end_clean();

									return '';
								} else {
									echo $return;
								}
							} else {
								$return = $bundle_front_end->show_crossell_html( $layout );
								if ( false === $return ) {
									ob_end_clean();

									return '';
								} else {
									echo $return;
								}
							}
							wp_reset_postdata();
							?>
                        </div>
                    </form>
                </div>
                <div class="woocommerce-message">
					<?php
					echo wc_add_to_cart_message( $product->get_id(), false, true )
					?>
                </div>
            </div>
			<?php
			return ob_get_clean();
		}
	}

}