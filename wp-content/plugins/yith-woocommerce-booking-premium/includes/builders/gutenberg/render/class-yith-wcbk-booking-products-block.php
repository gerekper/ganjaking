<?php
/**
 * Handle "Booking Products" Gutenberg block.
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Booking_Products_Block' ) ) {
	/**
	 * Booking products block class
	 *
	 * @since 3.0.0
	 */
	class YITH_WCBK_Booking_Products_Block extends YITH_WCBK_Render_Block {

		/**
		 * Block attributes
		 *
		 * @var array
		 */
		protected $attributes = array(
			'type'        => 'newest',
			'columns'     => 4,
			'rows'        => 1,
			'product_ids' => array(),
			'categories'  => array(),
		);

		/**
		 * Parse attributes.
		 *
		 * @param array $attributes Attributes.
		 *
		 * @return array
		 */
		protected function parse_attributes( $attributes ) {
			$attributes    = parent::parse_attributes( $attributes );
			$allowed_types = array( 'newest', 'hand-picked', 'categories', 'top-rated' );

			$attributes['type']    = in_array( $attributes['type'], $allowed_types, true ) ? $attributes['type'] : 'newest';
			$attributes['columns'] = max( 1, absint( $attributes['columns'] ) );
			$attributes['rows']    = max( 1, absint( $attributes['rows'] ) );

			if ( is_string( $attributes['product_ids'] ) ) {
				$attributes['product_ids'] = explode( ',', $attributes['product_ids'] );
			}

			if ( is_string( $attributes['categories'] ) ) {
				$attributes['categories'] = explode( ',', $attributes['categories'] );
			}

			$attributes['product_ids'] = is_array( $attributes['product_ids'] ) ? array_map( 'absint', $attributes['product_ids'] ) : array();
			$attributes['categories']  = is_array( $attributes['categories'] ) ? array_map( 'absint', $attributes['categories'] ) : array();

			return $attributes;
		}

		/**
		 * Get the type.
		 *
		 * @return string
		 */
		public function get_type() {
			return $this->attributes['type'];
		}

		/**
		 * Get the columns.
		 *
		 * @return int
		 */
		public function get_columns() {
			return $this->attributes['columns'];
		}

		/**
		 * Get the rows.
		 *
		 * @return int
		 */
		public function get_rows() {
			return $this->attributes['rows'];
		}

		/**
		 * Get the product_ids.
		 *
		 * @return int[]
		 */
		public function get_product_ids() {
			return $this->attributes['product_ids'];
		}

		/**
		 * Get the categories.
		 *
		 * @return int[]
		 */
		public function get_categories() {
			return $this->attributes['categories'];
		}

		/**
		 * Get the rows.
		 *
		 * @return int
		 */
		public function get_limit() {
			return $this->get_columns() * $this->get_rows();
		}

		/**
		 * Get product IDs.
		 *
		 * @return int[]
		 */
		private function get_product_ids_to_render() {
			$query_args = array(
				'status'     => 'publish',
				'type'       => YITH_WCBK_Product_Post_Type_Admin::$prod_type,
				'limit'      => $this->get_limit(),
				'visibility' => 'catalog',
				'category'   => array(),
				'include'    => array(),
				'return'     => 'ids',
			);

			switch ( $this->get_type() ) {
				case 'newest':
					$query_args['orderby'] = 'date';
					$query_args['order']   = 'DESC';
					break;
				case 'top-rated':
					add_filter( 'posts_clauses', array( $this, 'order_by_rating_post_clauses' ) );
					break;
				case 'hand-picked':
					$query_args['include'] = ! ! $this->get_product_ids() ? $this->get_product_ids() : array( 0 );
					break;
				case 'categories':
					// Use 'tax_query' instead of 'category' param, since it search for slugs instead of IDs.
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					$query_args['tax_query'] = array(
						array(
							'taxonomy' => 'product_cat',
							'field'    => 'term_id',
							'terms'    => $this->get_categories(),
						),
					);
					break;
			}

			$product_ids = wc_get_products( $query_args );

			if ( 'top-rated' === $this->get_type() ) {
				remove_filter( 'posts_clauses', array( $this, 'order_by_rating_post_clauses' ) );
			}

			return $product_ids;
		}

		/**
		 * Get allow_blank_state value.
		 *
		 * @return string
		 */
		public function get_allow_blank_state() {
			return $this->data['allow_blank_state'];
		}

		/**
		 * Is the empty state allowed?
		 *
		 * @return bool
		 */
		public function is_blank_state_allowed() {
			return 'yes' === $this->get_allow_blank_state();
		}

		/**
		 * Set the allow_blank_state value.
		 *
		 * @param bool|string $value The value to be set.
		 */
		public function set_allow_blank_state( $value ) {
			$this->data['allow_blank_state'] = wc_bool_to_string( $value );
		}

		/**
		 * Render
		 */
		public function render() {
			$columns     = $this->get_columns();
			$product_ids = $this->get_product_ids_to_render();

			$wrapper_classes = array(
				'woocommerce',
				'columns-' . $columns,
			);
			$wrapper_classes = implode( ' ', $wrapper_classes );

			if ( $product_ids ) {
				// Prime caches to reduce future queries.
				if ( is_callable( '_prime_post_caches' ) ) {
					_prime_post_caches( $product_ids );
				}

				wc_setup_loop(
					array(
						'columns'      => $columns,
						'name'         => 'yith-wcbk-booking-products-block-' . $this->get_type(),
						'is_shortcode' => false,
						'is_search'    => false,
						'is_paginated' => false,
						'total'        => count( $product_ids ),
						'total_pages'  => 1,
						'per_page'     => $this->get_limit(),
						'current_page' => 1,
					)
				);

				$original_post    = $GLOBALS['post'] ?? false;
				$original_product = $GLOBALS['product'] ?? false;

				echo '<div class="' . esc_attr( $wrapper_classes ) . '">';

				woocommerce_product_loop_start();

				if ( wc_get_loop_prop( 'total' ) ) {
					foreach ( $product_ids as $product_id ) {
						$GLOBALS['post']    = get_post( $product_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						$GLOBALS['product'] = wc_get_product( $product_id );
						setup_postdata( $GLOBALS['post'] );

						wc_get_template_part( 'content', 'product' );
					}
				}

				if ( $original_post ) {
					$GLOBALS['post'] = $original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				} elseif ( isset( $GLOBALS['post'] ) ) {
					unset( $GLOBALS['post'] );
				}

				if ( $original_product ) {
					$GLOBALS['product'] = $original_product;
				} elseif ( isset( $GLOBALS['product'] ) ) {
					unset( $GLOBALS['product'] );
				}

				woocommerce_product_loop_end();

				wp_reset_postdata();
				wc_reset_loop();
				echo '</div>';
			} else {
				if ( $this->is_blank_state_allowed() ) {
					$this->render_blank_state();
				}
			}
		}

		/**
		 * Order by rating.
		 *
		 * @param array $args Query args.
		 *
		 * @return array
		 */
		public function order_by_rating_post_clauses( $args ) {
			global $wpdb;

			$args['where'] .= " AND $wpdb->commentmeta.meta_key = 'rating' ";
			$args['join']  .= "LEFT JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID) LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)";

			$args['orderby'] = "$wpdb->commentmeta.meta_value DESC";
			$args['groupby'] = "$wpdb->posts.ID";

			return $args;
		}

		/**
		 * Retrieve blank state params.
		 *
		 * @return array
		 */
		public function get_blank_state_params() {
			$message = __( 'There are no bookable products to show!', 'yith-booking-for-woocommerce' );

			switch ( $this->get_type() ) {
				case 'newest':
					break;
				case 'top-rated':
					$message = __( 'There are no top-rated bookable products to show!', 'yith-booking-for-woocommerce' );
					break;
				case 'hand-picked':
					if ( ! $this->get_product_ids() ) {
						$message .= '<br />';
						$message .= __( 'Select products to be shown in the block settings.', 'yith-booking-for-woocommerce' );
					}
					break;
				case 'categories':
					if ( $this->get_categories() ) {
						$message = __( 'There are no bookable products in the categories you selected!', 'yith-booking-for-woocommerce' );
					} else {
						$message .= '<br />';
						$message .= __( 'Select product categories in the block settings.', 'yith-booking-for-woocommerce' );
					}
					break;
			}

			return array(
				'icon_url' => YITH_WCBK_ASSETS_URL . '/images/empty-calendar.svg',
				'message'  => $message,
			);
		}
	}
}
