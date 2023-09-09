<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Warranty_Frontend {

	/**
	 * Setup the class
	 */
	public function __construct() {
		$this->register_hooks();
	}

	/**
	 * Register the frontend hooks
	 */
	public function register_hooks() {
		add_filter( 'body_class', array( $this, 'output_body_class' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_styles' ) );

		// My Account.
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'show_request_button' ), 10, 1 );
		add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'my_orders_request_button' ), 10, 2 );

		add_filter( 'woocommerce_available_variation', array( $this, 'add_variation_data' ), 10, 3 );

		// Frontend form processing.
		add_action( 'template_redirect', array( $this, 'process_form_submission' ) );

		// Hide warranty notes from Recent Comments widget.
		add_filter( 'widget_comments_args', array( $this, 'hide_warranty_comments' ), 10, 1 );

		// Remove warranty notes from `/comments/feed`.
		add_filter( 'comment_feed_where', array( $this, 'manipulate_feed_comment_query' ), 10, 2 );
	}

	/**
	 * Add woocommerce CSS classes to the <body> element of the Warranty pages
	 *
	 * @param array $classes Body classes.
	 * @return array
	 */
	public function output_body_class( $classes ) {
		if ( is_page( wc_get_page_id( 'warranty' ) ) ) {
			$classes[] = 'woocommerce';
			$classes[] = 'woocommerce-page';
		}

		return $classes;
	}

	/**
	 * Register JS and CSS files
	 */
	public function frontend_styles() {
		global $post;

		wp_enqueue_style( 'wc-form-builder', plugins_url( 'assets/css/form-builder.css', WooCommerce_Warranty::$plugin_file ) );

		if ( $post ) {
			$product = wc_get_product( $post->ID );

			if ( $product && $product->is_type( 'variable' ) ) {
				wp_enqueue_script( 'wc-warranty-variables', plugins_url( 'assets/js/variables.js', WooCommerce_Warranty::$plugin_file ), array( 'jquery' ) );
				wp_localize_script(
					'wc-warranty-variables',
					'WC_Warranty',
					array(
						'currency_symbol' => get_woocommerce_currency_symbol(),
						'lifetime'        => esc_html__( 'Lifetime', 'wc_warranty' ),
						'no_warranty'     => esc_html__( 'No Warranty', 'wc_warranty' ),
						'free'            => esc_html__( 'Free', 'wc_warranty' ),
						'durations'       => array(
							'day'    => esc_html__( 'Day', 'wc_warranty' ),
							'days'   => esc_html__( 'Days', 'wc_warranty' ),
							'week'   => esc_html__( 'Week', 'wc_warranty' ),
							'weeks'  => esc_html__( 'Weeks', 'wc_warranty' ),
							'month'  => esc_html__( 'Month', 'wc_warranty' ),
							'months' => esc_html__( 'Months', 'wc_warranty' ),
							'year'   => esc_html__( 'Year', 'wc_warranty' ),
							'years'  => esc_html__( 'Years', 'wc_warranty' ),
						),
					)
				);
			}
		}

	}

	/**
	 * Display the 'Request Warranty' button on the order view page if
	 * an order contains a product with a valid warranty
	 *
	 * @param WC_Order $order Order object.
	 */
	public function show_request_button( $order ) {
		if ( ! $order instanceof WC_Order || 'no' === get_option( 'warranty_show_rma_button', 'yes' ) ) {
			return;
		}

		if ( 'completed' === $order->get_status() && Warranty_Order::order_has_warranty( $order ) ) {
			// If there is an existing warranty request, show a different text.
			$requests = get_posts(
				array(
					'post_type'  => 'warranty_request',
					'meta_query' => array(
						array(
							'key'   => '_order_id',
							'value' => $order->get_id(),
						),
					),
				)
			);

			if ( ! $requests ) {
				$requests = array();
			}

			if ( count( $requests ) > 0 ) {
				$title = get_option( 'view_warranty_button_text', __( 'View Warranty Request', 'wc_warranty' ) );
			} else {
				$title = get_option( 'warranty_button_text', __( 'Request Warranty', 'wc_warranty' ) );
			}

			$page_id   = get_option( 'woocommerce_warranty_page_id' );
			$permalink = add_query_arg( 'order', $order->get_id(), get_permalink( $page_id ) );
			echo '<a class="warranty-button button" href="' . esc_url( $permalink ) . '">' . esc_html( $title ) . '</a>';
		}
	}

	/**
	 * Display the 'Request Warranty' button on the My Account page
	 *
	 * @param  array    $actions Warranty request actions.
	 * @param  WC_Order $order WooCommerce order object.
	 * @return array $actions
	 */
	public function my_orders_request_button( $actions, $order ) {
		if ( ! $order instanceof WC_order || 'no' === get_option( 'warranty_show_rma_button', 'yes' ) ) {
			return $actions;
		}

		if ( 'completed' === $order->get_status() && Warranty_Order::order_has_warranty( $order ) ) {
			// If there is an existing warranty request, show a different text.
			$requests = get_posts(
				array(
					'post_type'  => 'warranty_request',
					'meta_query' => array(
						array(
							'key'   => '_order_id',
							'value' => $order->get_id(),
						),
					),
				)
			);

			if ( ! $requests ) {
				$requests = array();
			}

			if ( count( $requests ) > 0 ) {
				$title = get_option( 'view_warranty_button_text', esc_html__( 'View Warranty Status', 'wc_warranty' ) );
			} else {
				$title = get_option( 'warranty_button_text', esc_html__( 'Request Warranty', 'wc_warranty' ) );
			}

			$page_id   = get_option( 'woocommerce_warranty_page_id' );
			$permalink = esc_url( add_query_arg( 'order', $order->get_id(), get_permalink( $page_id ) ) );

			$actions['request_warranty'] = array(
				'url'  => $permalink,
				'name' => $title,
			);
		}

		return $actions;
	}

	/**
	 * Add warranty data to all variations
	 *
	 * @param array                $data       Variation data.
	 * @param WC_Product           $product    WC_Product.
	 * @param WC_Product_Variation $variation  WC_Product_Variation.
	 *
	 * @return array
	 */
	public function add_variation_data( $data, $product, $variation ) {
		$variation_id = $variation->get_id();
		$warranty     = warranty_get_product_warranty( $variation_id );
		if ( is_object( $warranty ) || is_array( $warranty ) ) {
			array_walk_recursive( $warranty, 'esc_attr' );
		}
		$data['_warranty']       = $warranty;
		$data['_warranty_label'] = $warranty['label'];

		return $data;
	}

	/**
	 * Capture and process frontend form submissions.
	 *
	 * @todo Split into methods.
	 */
	public function process_form_submission() {
		global $woocommerce;
		$request_data = warranty_request_data();
		$get_data     = warranty_request_get_data();
		$post_data    = warranty_request_post_data();

		if ( isset( $request_data['req'] ) ) {
			$request = $request_data['req'];

			if ( 'new_warranty' === $request ) {
				if ( empty( $post_data['wc_new_warranty_nonce'] ) ) {
					return;
				}

				if ( ! wp_verify_nonce( $post_data['wc_new_warranty_nonce'], 'wc_warranty_new_warranty_nonce' ) ) {
					die( 'Nonce is not matched!' );
				}

				$order_id     = isset( $get_data['order'] ) ? intval( $get_data['order'] ) : false;
				$idxs         = isset( $get_data['idx'] ) && is_array( $get_data['idx'] ) ? $get_data['idx'] : array();
				$request_type = ! empty( $post_data['warranty_request_type'] ) ? $post_data['warranty_request_type'] : '';
				$order        = wc_get_order( $order_id );

				if ( $order instanceof WC_Order && ! empty( $idxs ) ) {
					if ( ! is_user_logged_in() || ! current_user_can( 'view_order', intval( $order_id ) ) ) {
						wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
					}

					$quantities = ! empty( $post_data['warranty_qty'] ) ? $post_data['warranty_qty'] : 0;
					$items      = $order->get_items();
					$errors     = array();

					if ( 'completed' === $order->get_status() ) {
						$products = array();

						foreach ( $idxs as $idx ) {
							$products[] = ! empty( $items[ $idx ]['variation_id'] )
								? $items[ $idx ]['variation_id']
								: $items[ $idx ]['product_id'];
						}

						$request_id = warranty_create_request(
							array(
								'type'       => $request_type,
								'order_id'   => $order_id,
								'product_id' => $products,
								'index'      => $idxs,
								'qty'        => $quantities,
							)
						);

						if ( 'yes' === get_option( 'warranty_show_tracking_field', 'no' ) ) {
							update_post_meta( $request_id, '_request_tracking_code', 'y' );

							if ( ! empty( $post_data['tracking_provider'] ) ) {
								update_post_meta( $request_id, '_tracking_provider', $post_data['tracking_provider'] );
							}

							if ( ! empty( $post_data['tracking_code'] ) ) {
								update_post_meta( $request_id, '_tracking_code', $post_data['tracking_code'] );
							}
						}

						// Save the custom forms.
						$result = WooCommerce_Warranty::process_warranty_form( $request_id );

						if ( is_wp_error( $result ) ) {
							wp_delete_post( $request_id, true );

							$errors = $result->get_error_messages();
						} else {
							// Set the initial status and send the emails.
							warranty_update_status( $request_id, 'new' );
						}

						if ( empty( $errors ) ) {
							$back = get_permalink( get_option( 'woocommerce_warranty_page_id' ) );
							$back = add_query_arg( 'order', $order_id, $back );
							$back = add_query_arg( 'updated', rawurlencode( esc_html__( 'Request(s) sent', 'wc_warranty' ) ), $back );

							wp_safe_redirect( $back );
							exit;
						} else {
							$back = get_permalink( wc_get_page_id( 'warranty' ) );
							$back = add_query_arg(
								array(
									'order'      => $order_id,
									'request_id' => $request_id,
									'errors'     => rawurlencode( wp_json_encode( $errors ) ),
								),
								$back
							);

							if ( ! empty( $idxs ) ) {
								foreach ( $idxs as $idx ) {
									$back = add_query_arg( 'idx[]', $idx, $back );
								}
							}

							wp_safe_redirect( $back );
							exit;
						}
					} else {
						$result = new WP_Error( 'wc_warranty', esc_html__( 'Order does not have a valid warranty', 'wc_warranty' ) );
						$error  = $result->get_error_message( 'wc_warranty' );
						$back   = get_permalink( wc_get_page_id( 'warranty' ) );
						$back   = add_query_arg(
							array(
								'order' => $order_id,
								'error' => rawurlencode( $error ),
							),
							$back
						);

						wp_safe_redirect( $back );
						exit;
					}
				}
			} elseif ( 'new_return' === $request ) {
				if ( empty( $post_data['wc_new_return_nonce'] ) ) {
					return;
				}

				if ( ! wp_verify_nonce( $post_data['wc_new_return_nonce'], 'wc_warranty_new_return_nonce' ) ) {
					die( 'Nonce is not matched!' );
				}

				$post_data = warranty_request_post_data();

				$return_id    = isset( $post_data['return'] ) ? $post_data['return'] : '';
				$order_id     = isset( $post_data['order_id'] ) ? $post_data['order_id'] : '';
				$product_name = isset( $post_data['product_name'] ) ? $post_data['product_name'] : '';
				$first_name   = isset( $post_data['first_name'] ) ? $post_data['first_name'] : '';
				$last_name    = isset( $post_data['last_name'] ) ? $post_data['last_name'] : '';
				$email        = isset( $post_data['email'] ) ? $post_data['email'] : '';

				$warranty = array(
					'post_content' => '',
					// translators: %1$s Order ID.
					'post_name'    => sprintf( esc_html__( 'Return Request for Order #%1$s', 'wc_warranty' ), $order_id ),
					'post_status'  => 'publish',
					'post_author'  => 1,
					'post_type'    => 'warranty_request',
				);

				$request_id = wp_insert_post( $warranty );

				$metas = array(
					'order_id'     => $order_id,
					'product_id'   => 0,
					'product_name' => $product_name,
					'answer'       => '',
					'attachment'   => '',
					'code'         => warranty_generate_rma_code(),
					'first_name'   => $first_name,
					'last_name'    => $last_name,
					'email'        => $email,
				);

				foreach ( $metas as $key => $value ) {
					add_post_meta( $request_id, '_' . $key, $value, true );
				}

				$status = WooCommerce_Warranty::process_warranty_form( $request_id );

				warranty_update_status( $request_id, 'new' );

				if ( is_wp_error( $status ) ) {
					wp_delete_post( $request_id, true );

					foreach ( $status->get_error_messages() as $error ) {
						wc_add_notice( $error, 'error' );
					}
				} else {
					if ( function_exists( 'wc_add_notice' ) ) {
						wc_add_notice( esc_html__( 'Return request submitted successfully', 'wc_warranty' ) );
					} else {
						$woocommerce->add_message( esc_html__( 'Return request submitted successfully', 'wc_warranty' ) );
					}

					wp_safe_redirect( get_permalink( $return_id ) );
					exit;
				}
			}
		}

		if ( isset( $request_data['action'] ) ) {
			if ( 'set_tracking_code' === $request_data['action'] ) {
				$request_id = ! empty( $request_data['request_id'] ) ? $request_data['request_id'] : '';
				$code       = ! empty( $request_data['tracking_code'] ) ? $request_data['tracking_code'] : '';
				$provider   = ! empty( $request_data['tracking_provider'] ) ? $request_data['tracking_provider'] : '';

				update_post_meta( $request_id, '_tracking_code', $code );

				if ( ! empty( $provider ) ) {
					update_post_meta( $request_id, '_tracking_provider', $provider );
				}

				$request = warranty_load( $request_id );

				$back = get_permalink( get_option( 'woocommerce_warranty_page_id' ) );
				$back = add_query_arg( 'order', $request['order_id'], $back );
				$back = add_query_arg( 'updated', rawurlencode( esc_html__( 'Tracking codes updated', 'wc_warranty' ) ), $back );

				wp_safe_redirect( $back );
				exit;
			}
		}
	}

	/**
	 * Adds to Recent Comments widget query to filter out warranty notes.
	 *
	 * @since 1.8.13
	 *
	 * @param array $comment_query_args An array of arguments used to retrieve the recent comments.
	 *
	 * @return array Modified array of arguments.
	 */
	public function hide_warranty_comments( $comment_query_args ) {
		if ( isset( $comment_query_args['type__not_in'] ) ) {
			$comment_query_args['type__not_in'][] = 'wc_warranty_note';
		} else {
			$comment_query_args['type__not_in'] = array( 'wc_warranty_note' );
		}
		return $comment_query_args;
	}

	/**
	 * Manipulate the comment feed to exclude the warranty_request notes.
	 *
	 * @param String           $cwhere Where clause in comment sql.
	 * @param WP_Comment_Query $comment_query Comment query object.
	 *
	 * @return String.
	 */
	public function manipulate_feed_comment_query( $cwhere, $comment_query ) {
		return $cwhere . ( $cwhere ? ' AND ' : '' ) . " comment_type != 'wc_warranty_note' ";
	}
}

new Warranty_Frontend();
