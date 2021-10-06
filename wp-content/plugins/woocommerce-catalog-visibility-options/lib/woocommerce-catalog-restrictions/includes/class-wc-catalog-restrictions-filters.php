<?php

class WC_Catalog_Restrictions_Filters {

	private static $instance;

	public static function instance() {
		if ( !self::$instance ) {
			self::$instance = new WC_Catalog_Restrictions_Filters();
		}

		return self::$instance;
	}

	private $cache_can_purchase = array();
	private $cache_can_view_prices = array();

	public $buffer_on = false;
	public $action_removed = false;
	public $did_after_cart_button = false;
    public $is_booking_product = false;

	public function __construct() {

		add_filter( 'woocommerce_get_price_html', array( $this, 'on_price_html' ), 99, 2 );
		add_filter( 'woocommerce_variable_subscription_price_html', array( $this, 'on_price_html' ), 100, 2 );
		add_filter( 'woocommerce_sale_flash', array( $this, 'on_sale_flash' ), 99, 3 );
		add_filter( 'woocommerce_cart_item_price', array( $this, 'on_cart_item_price' ), 999, 2 );
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'on_cart_item_subtotal' ), 999, 2 );
		add_filter( 'woocommerce_cart_subtotal', array( $this, 'on_cart_subtotal' ), 9999, 2 );
		add_filter( 'woocommerce_cart_totals_order_total_html', array( $this, 'on_cart_total' ), 9999 );

		add_filter( 'woocommerce_order_formatted_line_subtotal', array(
			$this,
			'on_order_formatted_line_subtotal'
		), 10, 2 );

		add_action( 'woocommerce_after_single_product', array( $this, 'on_woocommerce_after_single_product_bind' ), 9 );
		add_action( 'woocommerce_after_single_product', array(
			$this,
			'on_woocommerce_after_single_product_unbind'
		), 11 );

		add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'on_after_add_to_cart_form' ), 0 );

		add_action( 'woocommerce_init', array( $this, 'bind_filters_late' ), 99 );


		//Since 2.7.2
		//Hook into cart validation to disallow items getting added to the cart.
		add_filter( 'woocommerce_add_to_cart_validation', array(
			$this,
			'on_woocommerce_add_to_cart_validation'
		), 10, 2 );


		//Bulk variations compatibility
		add_filter( 'woocommerce_bv_render_form', array( $this, 'on_woocommerce_bv_render_form' ), 99, 2 );

		//Since 2.7.0 use the loop_add_to_cart link to filter the button, rather than before and after loop item.
		add_filter( 'woocommerce_product_add_to_cart_url', array(
			$this,
			'on_woocommerce_product_add_to_cart_url'
		), 99, 2 );
		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'on_loop_add_to_cart_link' ), 99, 2 );
		add_filter( 'woocommerce_product_add_to_cart_text', array(
			$this,
			'on_woocommerce_product_add_to_cart_text'
		), 99, 2 );

		add_action( 'template_redirect', array( $this, 'plugin_compatibility_filters' ), 11 );

		//Since 2.8.1 - reset the availability_html so stock information does not show up in WC 2.6+
		add_filter( 'woocommerce_available_variation', array(
			$this,
			'on_get_woocommerce_available_variation'
		), 10, 3 );

		add_filter( 'wc_get_template', array( $this, 'on_get_variation_template' ), 99, 2 );

		add_filter( 'woocommerce_structured_data_product', array(
			$this,
			'on_get_woocommerce_structured_data_product'
		), 10, 2 );


		add_action( 'woocommerce_email_order_details', array( $this, 'on_email_order_details' ), 10, 1 );

	}

	public function bind_filters_late() {
		 add_action( 'woocommerce_before_booking_form', array( $this, 'on_before_booking_form' ), 1 );

		if ( WC_Catalog_Visibility_Compatibility::is_wc_version_gt( '3.4' ) ) {
			add_action( 'woocommerce_before_single_variation', array( $this, 'on_before_single_variation' ), 0 );
			add_action( 'woocommerce_after_single_variation', array( $this, 'on_after_single_variation' ), 998 );

			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'on_before_add_to_cart_button' ), 0 );
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'on_after_add_to_cart_button' ), 998 );
		} else {
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'on_before_add_to_cart_button' ), 0 );
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'on_after_add_to_cart_button' ), 998 );
		}
	}

	public function on_email_order_details() {
		remove_filter( 'woocommerce_get_price_html', array( $this, 'on_price_html' ), 99 );
		remove_filter( 'woocommerce_variable_subscription_price_html', array( $this, 'on_price_html' ), 100 );
		remove_filter( 'woocommerce_sale_flash', array( $this, 'on_sale_flash' ), 99 );
		remove_filter( 'woocommerce_cart_item_price', array( $this, 'on_cart_item_price' ), 999 );
		remove_filter( 'woocommerce_cart_item_subtotal', array( $this, 'on_cart_item_subtotal' ), 999 );
		remove_filter( 'woocommerce_cart_subtotal', array( $this, 'on_cart_subtotal' ), 9999 );
		remove_filter( 'woocommerce_cart_totals_order_total_html', array( $this, 'on_cart_total' ), 9999 );

		remove_filter( 'woocommerce_order_formatted_line_subtotal', array(
			$this,
			'on_order_formatted_line_subtotal'
		), 10 );
	}

	public function on_before_booking_form() {
		remove_action( 'woocommerce_before_add_to_cart_button', array( $this, 'on_before_add_to_cart_button' ), 1 );
		global $product;
		if ( $product && !WC_Catalog_Restrictions_Filters::instance()->user_can_purchase( $product ) ) {
			$this->buffer_on = ob_start();
			$this->is_booking_product = true;
		}
	}

	public function on_after_add_to_cart_form() {
		global $product;

		//Paypal Express Handling

		if ( defined( 'WC_GATEWAY_PPEC_VERSION' ) ) {
			if ( $product && !$this->user_can_purchase( $product ) ) {
				remove_action( 'woocommerce_after_add_to_cart_form', array(
					wc_gateway_ppec()->cart,
					'display_paypal_button_product'
				), 1 );
			}
		}

	}

	public function on_woocommerce_after_single_product_bind() {
		//Filters the regular variation price

		add_filter( 'woocommerce_product_variation_get_price', array(
			$this,
			'on_get_price'
		), 10, 2 );

		//Filters the regular product get price.
		add_filter( 'woocommerce_product_get_price', array( $this, 'on_get_price' ), 10, 2 );
	}

	public function on_woocommerce_after_single_product_unbind() {
		//Filters the regular variation price

		remove_filter( 'woocommerce_product_variation_get_price', array(
			$this,
			'on_get_price'
		), 10 );

		//Filters the regular product get price.
		remove_filter( 'woocommerce_product_get_price', array( $this, 'on_get_price' ), 10 );
	}

	public function plugin_compatibility_filters() {
		if ( is_product() ) {
			if ( !$this->user_can_purchase( wc_get_product( get_the_ID() ) ) ) {
				add_filter( 'woocommerce_bv_render_form', '__return_false' );
			}
		}
	}

	/**
	 * Reset the availability_html so stock information does not show up in WC 2.6+
	 *
	 * @param array $variation_data
	 * @param WC_Product_Variable $variable
	 * @param WC_Product $variation
	 *
	 * @return array
	 * @since 2.8.1
	 *
	 */
	public function on_get_woocommerce_available_variation( $variation_data, $variable, $variation ) {

		if ( $variable && ( !$this->user_can_view_price( $variable ) ) ) {
			$variation_data['availability_html']     = '';
			$variation_data['display_price']         = '';
			$variation_data['display_regular_price'] = '';
		}

		return $variation_data;
	}

	public function on_get_price( $price, $product ) {
		global $wc_cvo;
		if ($product && !$this->user_can_view_price( $product ) ) {
			if ( !$this->user_can_view_price( $product ) ) {
				return '';
			}
		}

		return $price;
	}

	/*
	 * Replacement HTML
	 */

	public function on_price_html( $html, $_product ) {
		global $wc_cvo;

		if ( $_product && $_product->get_type() == 'variation' ) {
			$_product = wc_get_product( $_product->get_parent_id() );
		}

		if ( $_product && !$this->user_can_view_price( $_product ) ) {
			return apply_filters( 'catalog_visibility_alternate_price_html', do_shortcode( wptexturize( $wc_cvo->setting( 'wc_cvo_c_price_text' ) ) ), $_product );
		}

		return $html;
	}

	public function on_cart_item_price( $price, $cart_item ) {
		global $wc_cvo;
		$product = $cart_item['data'];

		if ( $product && !$this->user_can_view_price( $product ) ) {
			return apply_filters( 'catalog_visibility_alternate_cart_item_price_html', do_shortcode( wptexturize( $wc_cvo->setting( 'wc_cvo_c_price_text' ) ) ), $cart_item );
		}

		return $price;
	}

	public function on_cart_item_subtotal( $price, $cart_item ) {
		$product = $cart_item['data'];

		if ( $product && !$this->user_can_view_price( $product ) ) {
			return apply_filters( 'catalog_visibility_alternate_cart_item_subtotal_html', '', $cart_item );
		}

		return $price;
	}

	/**
	 * @param float $subtotal
	 * @param WC_Order_Item $item
	 *
	 * @return mixed|void
	 */
	public function on_order_formatted_line_subtotal( $subtotal, $item ) {
		global $wc_cvo;
		try {
			$product = $item->get_product();

			if ( $product && !$this->user_can_view_price( $product ) ) {
				return apply_filters( 'catalog_visibility_alternate_order_formatted_line_subtotal', '', $item );
			}
		} catch ( Exception $exception ) {
			return $subtotal;
		}

		return $subtotal;
	}

	public function on_cart_subtotal( $subtotal ) {
		global $wc_cvo;
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( !$this->user_can_view_price( $cart_item['data'] ) ) {
				return apply_filters( 'catalog_visibility_alternate_cart_subtotal', do_shortcode( wptexturize( $wc_cvo->setting( 'wc_cvo_c_price_text' ) ) ), $cart_item );
			}
		}

		return $subtotal;
	}

	public function on_cart_total( $total ) {
		global $wc_cvo;
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( !$this->user_can_view_price( $cart_item['data'] ) ) {
				return apply_filters( 'catalog_visibility_alternate_cart_total', do_shortcode( wptexturize( $wc_cvo->setting( 'wc_cvo_c_price_text' ) ) ), $cart_item );
			}
		}

		return $total;
	}


	public function on_sale_flash( $html, $post, $product ) {
		if ( empty( $product ) ) {
			return $html;
		}

		if ( $product->get_type() == 'variation' ) {
			$product = wc_get_product( $product->get_parent_id() );
		}

		if ( !$this->user_can_view_price( $product ) ) {
			return '';
		}

		return $html;
	}

	public function on_before_single_variation() {
		remove_action( 'woocommerce_before_add_to_cart_button', array( $this, 'on_before_add_to_cart_button' ), 0 );
		remove_action( 'woocommerce_after_add_to_cart_button', array( $this, 'on_after_add_to_cart_button' ), 998 );
		$this->on_before_add_to_cart_button();
	}

	public function on_before_add_to_cart_button() {
		global $product;

		if ( $product && !$this->user_can_purchase( $product ) ) {
			if ( !$this->buffer_on ) {
				$this->buffer_on = ob_start();
			}
		}
	}

	public function on_after_single_variation() {
		$this->on_after_add_to_cart_button();
	}

	public function on_after_add_to_cart_button() {
		global $wc_cvo, $product;

		if ( $this->did_after_cart_button ) {
			return;
		} else {
			$this->did_after_cart_button = true;
		}

		if ( $product && !$this->user_can_purchase( $product ) ) {
			if ( $this->buffer_on ) {
				ob_end_clean();
			}

			if ($this->is_booking_product) {
			    // close the <div id="wc-bookings-booking-form" class="wc-bookings-booking-form" style="display:none">
			    echo '</div>';
            }

		} else {
			return;
		}


		do_action( 'catalog_visibility_before_alternate_add_to_cart_button' );

		$html = apply_filters( 'catalog_visibility_alternate_add_to_cart_button', do_shortcode( wpautop( wptexturize( $wc_cvo->setting( 'wc_cvo_s_price_text' ) ) ) ), $product );


		// Variable product price handling
		if ( $product->is_type( 'variable' ) ) {
			?>

            <div class="single_variation woocommerce-variation"></div>
            <div class="variations_button">
				<?php echo $html; ?>
                <input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>"/>
                <input type="hidden" name="variation_id" class="variation_id" value="0"/>
            </div>
			<?php do_action( 'wc_cvo_after_single_variation', $product ); ?>

			<?php
		} else {
			echo $html;
		}

		do_action( 'catalog_visibility_after_alternate_add_to_cart_button' );
	}

	/**
	 * This is hooked when the user can not view prices.
	 *
	 * @param $located
	 * @param $template_name
	 * @param $args
	 * @param $template_path
	 * @param $default_path
	 *
	 * @return string
	 */
	public function on_get_variation_template( $located, $template_name ) {
		global $wc_cvo;

		$_product = wc_get_product();

		if ( $_product && $template_name == 'single-product/add-to-cart/variation.php' ) {

			if ( $_product->get_type() == 'variation' ) {
				$_product = wc_get_product( $_product->get_parent_id() );
			}

			if ( !$this->user_can_view_price( $_product ) ) {
				$located = $wc_cvo->plugin_dir() . '/templates/variation.php';
			}
		}

		return $located;
	}

	public function on_woocommerce_bv_render_form( $render, $product ) {
		return $this->user_can_purchase( $product );
	}

	/**
	 * Hooks into cart validation to disallow items from being added to the cart at all.
	 *
	 * @param bool $result
	 * @param int $product_id
	 *
	 * @return bool
	 * @since 2.7.2
	 *
	 */
	public function on_woocommerce_add_to_cart_validation( $result, $product_id ) {
		$product           = wc_get_product( $product_id );
		$user_can_purchase = $product && WC_Catalog_Restrictions_Filters::instance()->user_can_purchase( $product );

		//If the result was OK, but the user can not purchase the product the result of this function will be false.
		//When adding an item to a wishlist however we need the result to be true as long as the regular validation is true;
		if ( $result && !$user_can_purchase ) {
			add_filter( 'woocommerce_add_to_wishlist_validation', array(
				$this,
				'on_woocommerce_add_to_wishlist_validation'
			), 10, 1 );
		}

		return $result & $user_can_purchase;
	}

	/**
	 * Hook to override catalog visibility disallowing items from being added to a wishlist.
	 *
	 * @param bool $result
	 *
	 * @return boolean
	 * @since 2.7.2
	 *
	 */
	public function on_woocommerce_add_to_wishlist_validation( $result ) {
		remove_filter( 'woocommerce_add_to_wishlist_validation', array(
			$this,
			'on_woocommerce_add_to_wishlist_validation'
		), 10, 1 );

		//This function is only called in the event that catalog visibility options has disallowed purchases AND regular validation already passed.
		//Hardcode to true to allow adding the item to a wishlist
		$result = true;

		return $result;
	}

	public function on_loop_add_to_cart_link( $markup, $product ) {
		global $wc_cvo;
		if ( $product && !$this->user_can_purchase( $product ) ) {
			$label = wptexturize( $wc_cvo->setting( 'wc_cvo_atc_text' ) );
			if ( empty( $label ) ) {
				return "";
			}
			$link = get_permalink( $product->get_id() );

			return apply_filters( 'catalog_visibility_alternate_add_to_cart_link', sprintf( '<a href="%s" data-product_id="%s" class="button product_type_%s">%s</a>', $link, $product->get_id(), $product->get_type(), $label ) );
		} else {
			return $markup;
		}
	}

	public function on_woocommerce_product_add_to_cart_text( $text, $product ) {
		global $wc_cvo;
		if ( $product && !$this->user_can_purchase( $product ) ) {
			$label = wptexturize( $wc_cvo->setting( 'wc_cvo_atc_text' ) );
			if ( empty( $label ) ) {
				return "";
			}

			return apply_filters( 'catalog_visibility_alternate_product_add_to_cart_text', $label, $product );
		} else {
			return $text;
		}
	}

	public function on_woocommerce_product_add_to_cart_url( $url, $product ) {
		if ( $product && !$this->user_can_purchase( $product ) ) {
			$link = get_permalink( $product->get_id() );

			return apply_filters( 'catalog_visibility_alternate_add_to_cart_link_url', $link, $product );
		} else {
			return $url;
		}
	}


	public function on_get_woocommerce_structured_data_product( $markup, $product ) {

		if ( !$this->user_can_view_price( $product ) ) {
			$markup['offers'] = array();
		}

		return $markup;
	}


	public function user_can_purchase( $product ) {
		if ( empty( $product ) ) {
			return false;
		}

		if ( $product->get_type() == 'variation' ) {
			$product = wc_get_product( $product->get_parent_id() );
		}

		if ( isset( $this->cache_can_purchase[ $product->get_id() ] ) ) {
			return $this->cache_can_purchase[ $product->get_id() ];
		}

		$pfilter = get_post_meta( $product->get_id(), '_wc_restrictions_purchase', true );
		$result  = false;
		if ( $pfilter == 'public' ) {
			$result = true; //Everyone
		} elseif ( $pfilter == 'restricted' ) {
			$roles      = get_post_meta( $product->get_id(), '_wc_restrictions_purchase_roles', true );
			$user_roles = $this->get_roles_for_current_user();

			if ( $roles && is_array( $roles ) ) {
				if ( !is_user_logged_in() ) {
					if ( count( array_intersect( $roles, $user_roles ) ) > 0 ) {
						$result = true;
					}
				} else {
					foreach ( $roles as $role ) {
						if ( current_user_can( $role ) ) {
							$result = true;
							break;
						}
					}
				}
			}
		} elseif ( $pfilter == 'locations_allowed' || $pfilter == 'locations_restricted' ) {
			global $wc_catalog_restrictions;
			$t_loc = $wc_catalog_restrictions->get_location_for_current_user();

			if ( !is_array( $t_loc ) ) {
				$t_loc = (array) $t_loc;
			}

			$locations = $product->get_meta( '_wc_restrictions_purchase_locations', true );
			$result    = count( array_intersect( $t_loc, $locations ) ) > 0;

			if ( $pfilter == 'locations_restricted' ) {
				$result = !$result;
			}

		} else {
			$result = $this->user_can_purchase_in_category( $product );
		}

		$result                                         = apply_filters( 'catalog_visibility_user_can_purchase', $result, $product );
		$this->cache_can_purchase[ $product->get_id() ] = $result;

		return $result;
	}

	public function user_can_purchase_in_category( $product ) {
		global $wc_cvo;
		if ( empty( $product ) ) {
			return false;
		}

		if ( $product->get_type() == 'variation' ) {
			$product = wc_get_product( $product->get_parent_id() );
		}

		$default = $wc_cvo->setting( 'wc_cvo_atc' ) == 'enabled' | ( $wc_cvo->setting( 'wc_cvo_atc' ) == 'secured' && catalog_visibility_user_has_access() );

		$category_result = $default;
		$category_ids    = WC_Catalog_Visibility_Compatibility::get_product_category_ids( $product );
		if ( $category_ids && is_array( $category_ids ) ) {
			foreach ( $category_ids as $category_id ) {
				$result = $this->check_category_purchase_access( $category_id, $default );
				if ( $result ) {
					$category_result = true;
					if ( !$default ) {
						break;
					}
				} else {
					$category_result = false;
					if ( $default ) {
						break;
					}
				}
			}
		}

		return apply_filters( 'catalog_visibility_user_can_purchase_in_category', $category_result, $product );
	}

	private function check_category_purchase_access( $category, $default ) {
		$pfilter = get_term_meta( $category, '_wc_restrictions_purchase', true );
		$result  = false;
		if ( $pfilter == 'public' ) {
			$result = true;
		} elseif ( $pfilter == 'restricted' ) {
			$roles      = get_term_meta( $category, '_wc_restrictions_purchase_roles', true );
			$user_roles = $this->get_roles_for_current_user();

			if ( $roles && is_array( $roles ) ) {
				if ( !is_user_logged_in() ) {
					if ( count( array_intersect( $roles, $user_roles ) ) > 0 ) {
						$result = true;
					}
				} else {
					foreach ( $roles as $role ) {
						if ( current_user_can( $role ) ) {
							$result = true;
							break;
						}
					}
				}
			}
		} elseif ( $pfilter == 'locations_allowed' || $pfilter == 'locations_restricted' ) {
			global $wc_catalog_restrictions;
			$t_loc = $wc_catalog_restrictions->get_location_for_current_user();

			if ( !is_array( $t_loc ) ) {
				$t_loc = (array) $t_loc;
			}

			$locations = get_term_meta( $category, '_wc_restrictions_purchase_locations', true );
			$result    = count( array_intersect( $t_loc, $locations ) ) > 0;

			if ( $pfilter == 'locations_restricted' ) {
				$result = !$result;
			}
		} else {
			$result = $default;
		}

		return $result;
	}

	public function user_can_view_price( $product ) {

		if ( empty( $product ) ) {
			return false;
		}

		if ( $product->get_type() == 'variation' ) {
			$product = wc_get_product( $product->get_parent_id() );
		}

		if ( isset( $this->cache_can_view_prices[ $product->get_id() ] ) ) {
			return $this->cache_can_view_prices[ $product->get_id() ];
		}

		$pfilter = $product->get_meta( '_wc_restrictions_price', true );
		$result  = false;
		if ( $pfilter == 'public' ) {
			$result = true;
		} elseif ( $pfilter == 'restricted' ) {
			$roles      = $product->get_meta( '_wc_restrictions_price_roles', true );
			$user_roles = $this->get_roles_for_current_user();

			if ( $roles && is_array( $roles ) ) {
				if ( !is_user_logged_in() ) {
					if ( count( array_intersect( $roles, $user_roles ) ) > 0 ) {
						$result = true;
					}
				} else {
					foreach ( $roles as $role ) {
						if ( current_user_can( $role ) ) {
							$result = true;
							break;
						}
					}
				}
			}
		} elseif ( $pfilter == 'locations_allowed' || $pfilter == 'locations_restricted' ) {
			global $wc_catalog_restrictions;
			$t_loc = $wc_catalog_restrictions->get_location_for_current_user();

			if ( !is_array( $t_loc ) ) {
				$t_loc = (array) $t_loc;
			}

			$locations = $product->get_meta( '_wc_restrictions_price_locations', true );

			if ( !is_array( $locations ) ) {
				$locations = (array) $locations;
			}

			$result = count( array_intersect( $t_loc, $locations ) ) > 0;

			if ( $pfilter == 'locations_restricted' ) {
				$result = !$result;
			}

		} else {
			$result = $this->user_can_view_price_in_category( $product );
		}

		$result                                            = apply_filters( 'catalog_visibility_user_can_view_price', $result, $product );
		$this->cache_can_view_prices[ $product->get_id() ] = $result;

		return $result;
	}

	public function user_can_view_price_in_category( $product ) {
		global $wc_cvo;

		if ( empty( $product ) ) {
			return false;
		}

		if ( $product->get_type() == 'variation' ) {
			$product = wc_get_product( $product->get_parent_id() );
		}

		$default = ( ( $wc_cvo->setting( 'wc_cvo_prices' ) == 'secured' && catalog_visibility_user_has_access() ) | $wc_cvo->setting( 'wc_cvo_prices' ) == 'enabled' );

		$category_result = $default;
		$category_ids    = WC_Catalog_Visibility_Compatibility::get_product_category_ids( $product );
		if ( $category_ids && is_array( $category_ids ) ) {
			foreach ( $category_ids as $category_id ) {
				$result = $this->check_category_price_access( $category_id, $default );
				if ( $result ) {
					$category_result = true;
					if ( !$default ) {
						break;
					}
				} else {
					$category_result = false;
					if ( $default ) {
						break;
					}
				}
			}
		}

		return apply_filters( 'catalog_visibility_user_can_view_price_in_category', $category_result, $product );

	}

	private function check_category_price_access( $category, $default ) {
		$pfilter = get_term_meta( $category, '_wc_restrictions_price', true );
		$result  = false;
		if ( $pfilter == 'public' ) {
			$result = true;
		} elseif ( $pfilter == 'restricted' ) {
			$roles      = get_term_meta( $category, '_wc_restrictions_price_roles', true );
			$user_roles = $this->get_roles_for_current_user();

			if ( $roles && is_array( $roles ) ) {
				if ( !is_user_logged_in() ) {
					if ( count( array_intersect( $roles, $user_roles ) ) > 0 ) {
						$result = true;
					}
				} else {
					foreach ( $roles as $role ) {
						if ( current_user_can( $role ) ) {
							$result = true;
							break;
						}
					}
				}
			}
		} elseif ( $pfilter == 'locations_allowed' || $pfilter == 'locations_restricted' ) {
			global $wc_catalog_restrictions;
			$t_loc = $wc_catalog_restrictions->get_location_for_current_user();

			if ( !is_array( $t_loc ) ) {
				$t_loc = (array) $t_loc;
			}

			$locations = get_term_meta( $category, '_wc_restrictions_price_locations', true );
			$result    = count( array_intersect( $t_loc, $locations ) ) > 0;

			if ( $pfilter == 'locations_restricted' ) {
				$result = !$result;
			}
		} else {
			$result = $default;
		}

		return $result;
	}


	public function get_roles_for_current_user() {
		$roles = array( 'guest' => 'guest' );

		if ( is_user_logged_in() ) {
			$user = new WP_User( get_current_user_id() );
			if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
				foreach ( $user->roles as $role ) {
					$roles[ $role ] = $role;
				}
			}
		}

		return apply_filters( 'woocommerce_catalog_restrictions_get_roles_for_current_user', $roles );
	}

}
