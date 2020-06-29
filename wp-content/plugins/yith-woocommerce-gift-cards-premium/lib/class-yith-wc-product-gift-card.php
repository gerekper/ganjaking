<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists ( 'WC_Product_Gift_Card' ) ) {

	/**
	 *
	 * @class   YITH_YWGC_Gift_Card
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class WC_Product_Gift_Card extends WC_Product {

		const YWGC_AMOUNTS = '_gift_card_amounts';
		const YWGC_MANUAL_AMOUNT_MODE = '_ywgc_manual_amount_mode';
		const YWGC_PRODUCT_IMAGE = '_ywgc_product_image';
		const YWGC_PRODUCT_TEMPLATE_DESIGN = '_ywgc_show_product_template_design';

		public $amounts = null;

		/**
		 * Initialize a gift card product.
		 *
		 * @param mixed $product
		 */
		public function __construct( $product ) {
			parent::__construct( $product );

			$this->downloadable = 'no';
			$this->product_type = YWGC_GIFT_CARD_PRODUCT_TYPE;
		}

		public function get_type() {
			return YWGC_GIFT_CARD_PRODUCT_TYPE;
		}

		/**
		 *
		 * @return bool
		 */
		public function is_downloadable() {
			return false;
		}

		/**
		 * Retrieve the number of current amounts for this product
		 *
		 * @return int
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function get_amounts_count() {
			$amounts = $this->get_product_amounts ();

			return count ( $amounts );
		}

		/**
		 * Retrieve the amounts set for the product
		 * @return array
		 */
		public function get_product_amounts() {

            if ( ! is_array ( $this->amounts ) ) {
                if ( $this->id ) {
                    $result = get_post_meta( $this->get_id(), self::YWGC_AMOUNTS, true );
                    $this->amounts = is_array ( $result ) ? $result : array();
                }
            }

			return apply_filters ( 'yith_ywgc_gift_card_amounts', $this->amounts, $this );
		}

		/**
		 * Returns false if the product cannot be bought.
		 *
		 * @return bool
		 */
		public function is_purchasable() {

			$purchasable = $this->get_amounts_count () || $this->is_manual_amount_enabled ();

			return apply_filters ( 'woocommerce_is_purchasable', $purchasable, $this );
		}

		/**
		 * Save current gift card amounts
		 *
		 * @param array $amounts
		 */
		public function set_amounts( $amounts = array() ) {
			$this->amounts = $amounts;
		}

		/**
		 * Save current gift card amounts
		 *
		 * @param array $amounts
		 */
		public function save_amounts( $amounts = array() ) {
			yit_save_prop ( $this, self::YWGC_AMOUNTS, $amounts );
		}

		/**
		 * Update the design status for the gift card
		 *
		 * @param $status
		 */
		public function set_design_status( $status ) {
			yit_save_prop ( $this, self::YWGC_PRODUCT_TEMPLATE_DESIGN, $status );
		}

		/**
		 * Retrieve the design status
		 *
		 * @return mixed
		 */
		public function get_design_status() {

			return yit_get_prop ( $this->get_product_instance (), self::YWGC_PRODUCT_TEMPLATE_DESIGN );
		}

		/**
		 * Update the manual amount status.
		 * Available values are "global", "accept" and "reject"
		 *
		 * @param string $status
		 */
		public function update_manual_amount_status( $status ) {
			yit_save_prop ( $this, self::YWGC_MANUAL_AMOUNT_MODE, $status );
		}

		/**
		 * Process the current product instance in order to let third party plugin
		 * change the reference(Useful for WPML and similar plugins)
		 *
		 * @return WC_Product
		 */
		protected function get_product_instance() {

			return apply_filters ( 'yith_ywgc_get_product_instance', $this );
		}

		/**
		 * Retrieve the manual amount status for this product.
		 *
		 * Available values are "global", "accept" and "reject"
		 * @return mixed
		 */
		public function get_manual_amount_status() {

			return yit_get_prop ( $this->get_product_instance (), self::YWGC_MANUAL_AMOUNT_MODE );
		}

		/**
		 * Retrieve if manual amount is enabled for this gift card
		 */
		public function is_manual_amount_enabled() {

			$status = $this->get_manual_amount_status ();

			return apply_filters ( 'yith_gift_cards_is_manual_amount_enabled', 'accept' == $status, $status, $this );
		}

		/**
		 * Returns the price in html format
		 *
		 * @access public
		 *
		 * @param string $price (default: '')
		 *
		 * @return string
		 */
		public function get_price_html( $price = '' ) {
			$amounts = $this->get_amounts_to_be_shown ();

			// No price for current gift card
			if ( ! count ( $amounts ) ) {
				$price = apply_filters ( 'yith_woocommerce_gift_cards_empty_price_html', '', $this );
			} else {
				ksort ( $amounts, SORT_NUMERIC );

				$min_price = current ( $amounts );
				$min_price = wc_price( $min_price['price'] );
				$max_price = end ( $amounts );
				$max_price = wc_price( $max_price['price'] );

				$price = $min_price !== $max_price ?
					sprintf ( _x ( '%1$s&ndash;%2$s', 'Price range: from-to', 'yith-woocommerce-gift-cards' ), $min_price, $max_price ) :
					$min_price;
				$price = apply_filters ( 'yith_woocommerce_gift_cards_amount_range', $price, $this );

			}

			return apply_filters ( 'woocommerce_get_price_html', $price, $this );
		}

		/**
		 * Retrieve an array of gift cards amounts with the corrected value to be shown(inclusive or not inclusive taxes)
		 *
		 * @return array
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function get_amounts_to_be_shown() {
			$amounts_to_show  = array();

			$tax_display_mode = get_option ( 'woocommerce_tax_display_shop' );

			$display_amounts = $this->get_product_amounts ();
			$index           = 0;

			$product_amounts = $this->get_product_amounts ();
			foreach ( $display_amounts as $amount ) {

				$amount = wc_format_decimal ( floatval( $amount ) );

				if ( 'incl' === $tax_display_mode ) {
					$price = yit_get_price_including_tax ( $this, 1, $amount );
				} else {
					$price = yit_get_price_excluding_tax ( $this, 1, $amount );
				}

				$original_amount = $product_amounts[ $index ];

				$negative        = $price < 0;
				$price_format    = get_woocommerce_price_format ();
				$formatted_price = ( $negative ? '-' : '' ) . sprintf ( $price_format, get_woocommerce_currency_symbol (), $price );

				$amounts_to_show[ $original_amount ] = array(
				    'amount'    => $amount,
					'price'     => $price,
					'wc-price'  => $formatted_price,
					'title'     => wc_price ( $price )
				);
				$index ++;
			}

			return apply_filters ( 'yith_ywgc_gift_cards_amounts', $amounts_to_show, $this->id );
		}

		/**
		 * Get the add to cart button text
		 *
		 * @return string
		 */
		public function add_to_cart_text() {
            $text = $this->is_purchasable () && $this->is_in_stock () ? apply_filters( 'yith_ywgc_select_amount_text' , esc_html__( 'Select amount', 'yith-woocommerce-gift-cards' ) ) : esc_html__( 'Read more', 'yith-woocommerce-gift-cards' );

			return apply_filters ( 'woocommerce_product_add_to_cart_text', $text, $this );
		}

		/**
		 * Add a new amount to the gift cards
		 *
		 * @param float $amount
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function add_amount( $amount ) {

			$product_amounts = $this->get_product_amounts ();
			if ( ! in_array ( $amount, $product_amounts ) ) {

				$product_amounts[] = $amount;
				sort ( $product_amounts, SORT_NUMERIC );
				$this->save_amounts ( $product_amounts );

				return true;
			}

			return false;
		}

		/**
		 * Remove an amount from the amounts list
		 *
		 * @param float $amount
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function remove_amount( $amount ) {

			$product_amounts = $this->get_product_amounts ();

			if ( in_array ( $amount, $product_amounts ) ) {
				if ( ( $key = array_search ( $amount, $product_amounts ) ) !== false ) {
					unset( $product_amounts[ $key ] );
				}

				$this->save_amounts ( $product_amounts );

				return true;
			}

			return false;
		}


		/**
		 * Retrieve the custom image set from the edit product page for a specific gift card product
		 *
		 * @param string $size
		 * @param string $return Choose whether to return url or id (url|id)
		 *
		 * @return mixed
		 */
		public function get_manual_header_image( $size = 'full', $return = 'url' ) {
            global $post;
            $image_url = '';

			$product = wc_get_product($post);

			if ( !is_object($product))
			    return;

			if ( $product  ) {
			    $image_id = yit_get_prop ( $product, self::YWGC_PRODUCT_IMAGE );
            }
            else{
                $image_id = '';
            }

            $image_id = ( isset( $image_id ) && $image_id ) ? $image_id : yit_get_prop ( $this->get_product_instance(), self::YWGC_PRODUCT_IMAGE );


			$image_id = ( $image_id != '' ) ? $image_id : get_post_thumbnail_id( $post->ID );

			if ( $return == 'id' ) {
				return $image_id;
			}

			if ( $image_id ) {
				$image     = wp_get_attachment_image_src ( $image_id, $size );
				$image_url = $image[0];
			}

			return $image_url;
		}

		/**
		 * Set the header image for a gift card product
		 *
		 * @param int $attachment_id
		 */
		public function set_header_image( $attachment_id ) {

			yit_save_prop ( $this, self::YWGC_PRODUCT_IMAGE, $attachment_id );
		}

		/**
		 * Unset the header image for a gift card product
		 *
		 */
		public function unset_header_image() {

			yit_delete_prop ( $this, self::YWGC_PRODUCT_IMAGE );
		}
	}
}
