<?php
/**
 * Notes class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WOOCOMMERCE PRODUCT ADD-ONS & EXTRA OPTIONS
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WAPO_Sold_Individually_Product' ) ) {
	/**
	 * YITH_WAPO_Sold_Individually_Product
	 *
	 * @since 3.0.0
	 */
	class YITH_WAPO_Sold_Individually_Product {

		/**
		 * Single instance of the class
		 *
		 * @var   \YITH_WAPO_Sold_Individually_Product
		 * @since 3.0.0
		 */
		protected static $instance;
		/**
		 * Var Sold individually product creation
		 *
		 * @var int The default product for create sold individually addons.
		 */
		public $sold_individually_product_id = -1;
		/**
		 * Constructor
		 *
		 * @since  3.0.0
		 */
		private function __construct() {

			add_action( 'init', array( $this, 'create_sold_individually_product' ) );

			add_filter( 'woocommerce_is_purchasable', array( $this, 'sold_individually_product_is_purchasable' ), 10, 2 );

			// Cart section.
			add_action( 'woocommerce_add_to_cart', array( $this, 'add_to_cart_addons_sell_individually' ), 10, 6 );
			add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'remove_link_addons_individually' ), 10, 2 );
			add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'hide_product_thumbnail_addons_sell_individually' ), 10, 2 );
			add_filter( 'woocommerce_cart_item_name', array( $this, 'hide_product_name_on_addons_sell_individually' ), 10, 2 );
			add_action( 'woocommerce_cart_item_removed', array( $this, 'remove_items_from_cart' ), 10, 2 );
			add_filter( 'woocommerce_cart_item_quantity', array( $this, 'woocommerce_cart_item_quantity' ), 10, 2 );

			add_filter( 'woocommerce_cart_item_class', array( $this, 'add_cart_item_class_for_main_product' ), 10, 3 );

			// Order section.
			add_filter( 'woocommerce_checkout_create_order_line_item', array( $this, 'woocommerce_checkout_create_order_line_item' ), 10, 4 );
			add_filter( 'woocommerce_order_item_name', array( $this, 'woocommerce_order_item_name' ), 10, 2 );
			add_filter( 'woocommerce_order_item_quantity_html', array( $this, 'woocommerce_order_item_quantity_html' ), 10, 2 );
            add_filter( 'woocommerce_order_item_class', array( $this, 'woocommerce_order_item_class' ), 10, 3 );

			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_itemmeta' ) );

			/**
			 * Hide the default product for sold individually feature on the admin products list
			 * */
			add_action( 'pre_get_posts', array( $this, 'hide_default_sold_individually_product' ) );

		}
		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WAPO_Sold_Individually_Product
		 * @since  3.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Create sold individually product
		 *
		 * @param bool $force Force to create the product.
		 *
		 * @since 3.0.0
		 */
		public function create_sold_individually_product( $force = false ) {

			$this->sold_individually_product_id = get_option( 'yith_wapo_sold_individually_product_id', -1 );
			if ( $force || -1 === $this->sold_individually_product_id || ! wc_get_product( $this->sold_individually_product_id ) ) {
				// Create sold individually product.
				$args = array(
					'post_title'     => apply_filters( 'yith_wapo_sold_individually_product_title', 'Sold Individually -- YITH WooCommerce Product Addons' ),
					'post_name'      => 'yith_wapo_sold_individually_product',
                    // translators: string that is created as a description of a private product that the plugin creates.
                    'post_content'   => esc_html__( 'This product has been created automatically by the plugin YITH WOOCOMMERCE PRODUCT ADD-ONS & EXTRA OPTIONS. You must not edit it, or the plugin might not work properly. The main function of this product is to be used for the feature "Sold individually".', 'yith-woocommerce-product-add-ons' ),
					'post_status'    => 'private',
					'post_date'      => gmdate( 'Y-m-d H:i:s' ),
					'post_author'    => 0,
					'post_type'      => 'product',
					'comment_status' => 'closed',
				);

				$this->sold_individually_product_id = wp_insert_post( $args );
				update_option( 'yith_wapo_sold_individually_product_id', $this->sold_individually_product_id );
				wp_set_object_terms( $this->sold_individually_product_id, 'simple', 'product_type' );
				// set this default gift card product as virtual.
				$product = wc_get_product( $this->sold_individually_product_id );
				if ( $product ) {
					$product->set_virtual( 'yes' );
					$product->set_catalog_visibility( 'hidden' );
					$product->set_tax_class( 'zero-rate' );
					$product->save();
				}
			} else {
				$product = wc_get_product( $this->sold_individually_product_id );
				if ( $product && 'simple' !== $product->get_type() ) {
					wp_set_object_terms( $product->get_id(), 'simple', 'product_type' );
				}
			}
		}

		/**
		 * Check if it's sold individually product on cart to make it purchasable
		 *
		 * @param bool       $purchasable is product purchasable.
		 * @param WC_Product $product Product.
		 * @since 3.0.0
		 * @return bool
		 */
		public function sold_individually_product_is_purchasable( $purchasable, $product ) {

			if ( ( $product instanceof WC_Product_Simple ) && ( (int) $product->get_id() === (int) $this->sold_individually_product_id ) ) {
				return true;
			}

			return $purchasable;
		}

		/**
		 * Add to cart addons sell indivually and group their in the same product.
		 *
		 * @param string $cart_item_key Cart item key.
		 * @param int    $product_id Product ID.
		 * @param int    $quantity Quantity.
		 * @param int    $variation_id Variation ID.
		 * @param object $variation The variation object.
		 * @param array  $cart_item_data Cart item data.
		 */
		public function add_to_cart_addons_sell_individually( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

			// Avoid show addons of child products of YITH Composite Products.
			if ( isset( $cart_item_data['yith_wcp_child_component_data'] ) ) {
				return $cart_item_key;
			}

			if ( $product_id > 0 && isset( $_REQUEST['yith_wapo_sell_individually'] ) && isset( $_REQUEST['yith_wapo'] ) ) { //phpcs:ignore

				$cart_item_data_sold_individually = array();

				$addons_product           = $_REQUEST['yith_wapo']; //phpcs:ignore
				$addons_sell_individually = $_REQUEST['yith_wapo_sell_individually']; //phpcs:ignore

				foreach ( $addons_product as $index => $options ) {
					foreach ( $options as $key => $value ) {
						$split = YITH_WAPO::get_instance()->split_addon_and_option_ids( $key, $value );

						$addon_id  = $split['addon_id'];
						$option_id = $split['option_id'];

						$info = yith_wapo_get_option_info( $addon_id, $option_id );
						// Check if the addon is a selector and has the default value.
						if ( 'default' === $value && 'select' === $info['addon_type'] ) {
							continue;
						}

						if ( isset( $addons_sell_individually[ $key ] ) && isset( $value ) && '' !== $value ) {
							$cart_item_data_sold_individually['yith_wapo_options'][][ $key ] = $value;
						}
					}
				}

				if ( isset( $cart_item_data_sold_individually['yith_wapo_options'] ) && ! empty( $cart_item_data_sold_individually['yith_wapo_options'] ) ) {

					$cart_item_data_sold_individually['yith_wapo_individual_addons'] = true;
					$cart_item_data_sold_individually['yith_wapo_addons_parent_key'] = $cart_item_key;
					$cart_item_data_sold_individually['yith_wapo_product_id']        = $product_id;
					$cart_item_data_sold_individually['yith_wapo_variation_id']      = $variation_id;
					$cart_item_data_sold_individually['yith_wapo_qty_options']       = isset( $_REQUEST['yith_wapo_product_qty'] ) ? $_REQUEST['yith_wapo_product_qty'] : array(); //phpcs:ignore

					remove_action( 'woocommerce_add_to_cart', array( $this, 'add_to_cart_addons_sell_individually' ), 10, 6 );
					remove_filter( 'woocommerce_add_cart_item_data', array( YITH_WAPO_Cart::get_instance(), 'add_cart_item_data' ), 25, 2 );

					$sold_individually_product = get_option( 'yith_wapo_sold_individually_product_id', false );

					if ( $sold_individually_product ) {

						$quantity = apply_filters( 'yith_wapo_sold_individually_quantity', 1 );

						if ( apply_filters( 'yith_wapo_split_addons_individually_on_cart', false ) ) {
							$addons_individually = $cart_item_data_sold_individually['yith_wapo_options'];
							foreach ( $addons_individually as $addon ) {
								$cart_item_data_sold_individually['yith_wapo_options'] = array( $addon );
								WC()->cart->add_to_cart( $sold_individually_product, $quantity, 0, array(), $cart_item_data_sold_individually );
							}
						} else {
							WC()->cart->add_to_cart( $sold_individually_product, $quantity, 0, array(), $cart_item_data_sold_individually );
						}

						do_action('yith_wapo_after_add_to_cart_individually_product', $sold_individually_product, $cart_item_data_sold_individually, $product_id );
					}

					add_action( 'woocommerce_add_to_cart', array( $this, 'add_to_cart_addons_sell_individually' ), 10, 6 );
					add_filter( 'woocommerce_add_cart_item_data', array( YITH_WAPO_Cart::get_instance(), 'add_cart_item_data' ), 25, 2 );
				}
			}

		}
		/**
		 * Remove link for delete individual addons without remove parent
		 *
		 * @param string $remove_link_code Cart item key.
		 * @param string $cart_item_key Cart item key.
		 * @return string
		 */
		public function remove_link_addons_individually( $remove_link_code, $cart_item_key = '' ) {

			$cart_item = WC()->cart->get_cart_item( $cart_item_key );

			if ( $cart_item && isset( $cart_item['yith_wapo_addons_parent_key'] ) ) {
				$remove_link_code = '';
			}

			return $remove_link_code;
		}
		/**
		 * Remove product thumbnail individual addons
		 *
		 * @param string $thumbnail Thumbnail image.
		 * @param array  $cart_item Cart item.
		 * @return string
		 */
		public function hide_product_thumbnail_addons_sell_individually( $thumbnail, $cart_item = array() ) {

			if ( $cart_item && isset( $cart_item['yith_wapo_addons_parent_key'] ) ) {

				$thumbnail = '';
			}

			return $thumbnail;
		}
		/**
		 * Remove product name individual addons
		 *
		 * @param string $product_name $product_name.
		 * @param array  $cart_item Cart item.
		 * @return string
		 */
		public function hide_product_name_on_addons_sell_individually( $product_name, $cart_item = array() ) {
			if ( $cart_item && isset( $cart_item['yith_wapo_addons_parent_key'] ) ) {

				$product_name = '';
			}

			return $product_name;
		}
		/**
		 * Remove individual addons items from cart when original product is removed.
		 *
		 * @param string $cart_item_key Cart item key.
		 * @param object $cart WC Cart.
		 */
		public function remove_items_from_cart( $cart_item_key, $cart ) {

			remove_action( 'woocommerce_cart_item_removed', array( $this, 'remove_items_from_cart' ), 10 );

			foreach ( $cart->get_cart() as $cart_item_key_ass => $value ) {

				if ( isset( $value['yith_wapo_addons_parent_key'] ) && $value['yith_wapo_addons_parent_key'] === $cart_item_key ) {

					WC()->cart->remove_cart_item( $cart_item_key_ass );

				}
			}

			add_action( 'woocommerce_cart_item_removed', array( $this, 'remove_items_from_cart' ), 10, 2 );

		}
		/**
		 * Filter the cart item quantity field to show a fixed quantity instead of the numeric field for sold individually addons.
		 *
		 * @param int    $quantity      Product quantity field.
		 * @param string $cart_item_key Cart item key.
		 *
		 * @return int
		 */
		public function woocommerce_cart_item_quantity( $quantity, $cart_item_key ) {
			$cart_item = WC()->cart->get_cart_item( $cart_item_key );

			if ( $cart_item && isset( $cart_item['yith_wapo_addons_parent_key'] ) ) {
				$quantity = apply_filters( 'yith_wapo_cart_item_sold_individually_quantity', 1, $cart_item );
			}

			return $quantity;
		}

		/**
		 * Add cart item classes for product with addons sell individually
		 *
		 * @param string $class_name    The class name.
		 * @param array  $cart_item     The cart item.
		 * @param string $cart_item_key The cart item key.
		 * @since 3.0.0
		 * @return string
		 */
		public function add_cart_item_class_for_main_product( $class_name, $cart_item, $cart_item_key = '' ) {

			$product_has_individual_addons = isset( $cart_item['yith_wapo_product_has_individual_addons'] );
			$product_is_individual_addons  = isset( $cart_item['yith_wapo_individual_addons'] );
			if ( $product_has_individual_addons ) {
				$class_name .= ' yith-wapo-product-has-individual-addons ';
			} elseif ( $product_is_individual_addons ) {
				$class_name .= ' yith-wapo-product-is-individual-addons ';

			}
			return $class_name;
		}

		/**
		 * Add addons data to sold individually products and change the name.
		 *
		 * @param WC_Order_Item_Product $item          Order item.
		 * @param string                $cart_item_key Cart item key.
		 * @param array                 $cart_item     Cart item data.
		 * @param WC_Order              $order         The order.
		 *
		 * @since 3.0.0
		 */
		public function woocommerce_checkout_create_order_line_item( $item, $cart_item_key, $cart_item, $order ) {

			$is_product_with_addons_sold_individually = isset( $cart_item['yith_wapo_individual_addons'] );

			if ( $is_product_with_addons_sold_individually ) {
				$meta_to_store = array(
					'_yith_wapo_individual_addons' => $cart_item['yith_wapo_individual_addons'],
					'_yith_wapo_product_id_individual_addons' => $cart_item['yith_wapo_product_id'],
					'_yith_wapo_variation_id_individual_addons' => $cart_item['yith_wapo_variation_id'],
					'_yith_wapo_addons_parent_key' => $cart_item['yith_wapo_addons_parent_key'],
				);

				if ( $meta_to_store ) {
					foreach ( $meta_to_store as $key => $value ) {
						$item->add_meta_data( $key, $value );
					}
				}

				$parent_product_id = $cart_item['yith_wapo_product_id'];
				$parent_product    = wc_get_product( $parent_product_id );

				if ( $parent_product ) {
					/*
					 * APPLY_FILTER: yith_wapo_individually_product_addon_title
					 *
					 * Change individual addon title.
					 *
					 * @param WC_Order_Item_Product $item           Order item.
					 * @param string                $cart_item_key  Cart item key.
					 * @param array                 $cart_item      Cart item data.
					 * @param WC_Order              $order          The order.
					 * @param string				 $parent_product Parent product title.
					 *
					 * @return array
					 */
                    // translators: string that is printed after buying an add-on individually. You can see it in the order edit page.
					$title = apply_filters( 'yith_wapo_individually_product_addon_title', esc_html__( 'Individually sold add-on from', 'yith-woocommerce-product-add-ons' ) . ' ' . $parent_product->get_title(), $item, $cart_item_key, $cart_item, $order, $parent_product );
					$item->set_name( $title );
				}
			}

		}
		/**
		 * Change title of sold individually products in orders
		 *
		 * @param string        $title      The title.
		 * @param WC_Order_Item $order_item The order item.
		 *
		 * @return string
		 * @since  3.0.0
		 */
		public function woocommerce_order_item_name( $title, $order_item ) {

			if ( ! $order_item instanceof WC_Order_Item_Product || ! $order_item->get_meta( '_yith_wapo_individual_addons' ) ) {
				return $title;
			}

			return '';
		}

        /**
         * Change quantity of sold individually products in orders
         *
         * @param string        $title      The title.
         * @param WC_Order_Item $order_item The order item.
         *
         * @return string
         * @since  3.0.0
         */
        public function woocommerce_order_item_quantity_html( $quantity_html, $order_item ) {

            if ( ! $order_item instanceof WC_Order_Item_Product || ! $order_item->get_meta( '_yith_wapo_individual_addons' ) ) {
                return $quantity_html;
            }

            return '';
        }

        public function woocommerce_order_item_class( $classes, $order_item, $order ) {

            if ( $order_item instanceof WC_Order_Item_Product && $order_item->get_meta( '_yith_wapo_individual_addons' ) ) {
                $classes .= ' yith-wapo-sold-individually';
            }

            return $classes;
        }

		/**
		 * Hide individual meta keys.
		 *
		 * @param array $meta_array Order item meta to hide.
		 *
		 * @return array
		 * @since  3.0.0
		 */
		public function hidden_order_itemmeta( $meta_array ) {

			$meta_array[] = '_yith_wapo_individual_addons';
			$meta_array[] = '_yith_wapo_product_id_individual_addons';
			$meta_array[] = '_yith_wapo_variation_id_individual_addons';
			$meta_array[] = '_yith_wapo_addons_parent_key';

			return $meta_array;
		}


		/**
		 * Avoid to show the default sold individually product
		 *
		 * @param array $query The query.
		 *
		 * @since  3.0.0
		 */
		public function hide_default_sold_individually_product( $query ) {

			global $pagenow;

			if ( $query->is_admin && 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] && apply_filters( 'yith_wapo_pre_get_posts_hide_default_sold_individually_product', true, $query ) ) { // phpcs:ignore
				$query->set( 'post__not_in', array( get_option( 'yith_wapo_sold_individually_product_id' ) ) );
			}

		}
	}
}


/**
 * Unique access to instance of YITH_WAPO_Sold_Individually_Product class
 *
 * @return \YITH_WAPO_Sold_Individually_Product
 * @since  3.0.0
 */
function YITH_WAPO_Sold_Individually_Product() { //phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return YITH_WAPO_Sold_Individually_Product::get_instance();
}
