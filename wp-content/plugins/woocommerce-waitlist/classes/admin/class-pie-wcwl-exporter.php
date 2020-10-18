<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Exporter' ) ) {
	/**
	 * Class Pie_WCWL_Exporter
	 */
	class Pie_WCWL_Exporter {

		/**
		 * Initialise exporter class
		 */
		public function init() {
			add_filter( 'woocommerce_account_settings', array( $this, 'add_waitlist_data_retention_settings' ) );
			add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ) );
			add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
		}

		/**
		 * Add setting to enable erasure of waitlist data after the default WooCommerce data settings
		 *
		 * @param $settings
		 *
		 * @return mixed
		 */
		public function add_waitlist_data_retention_settings( $settings ) {
			if ( ! is_array( $settings ) ) {
				return $settings;
			}
			$waitlist_setting = array(
				'desc'          => __( 'Remove personal data for waitlists', 'woocommerce-waitlist' ),
				'desc_tip'      => sprintf( __( 'When handling an %1$saccount erasure request%2$s, should personal data for waitlists be retained or removed?', 'woocommerce-waitlist' ), '<a href="' . esc_url( admin_url( 'tools.php?page=remove_personal_data' ) ) . '">', '</a>' ),
				'id'            => 'woocommerce_erasure_request_removes_waitlist_data',
				'type'          => 'checkbox',
				'default'       => 'no',
				'checkboxgroup' => '',
				'autoload'      => false,
			);
			$new_settings     = array();
			foreach ( $settings as $setting ) {
				$new_settings[] = $setting;
				if ( isset( $setting['id'] ) && 'woocommerce_erasure_request_removes_order_data' === $setting['id'] ) {
					$new_settings[] = $waitlist_setting;
				}
			}

			return $new_settings;
		}

		/**
		 * Register exporter for plugin
		 *
		 * @param $exporters
		 *
		 * @return mixed
		 */
		public function register_exporter( $exporters ) {
			$exporters['woocommerce-waitlist'] = array(
				'exporter_friendly_name' => __( 'Waitlist Data', 'woocommerce-waitlist' ),
				'callback'               => array( $this, 'generate_export_data' ),
			);

			return $exporters;
		}

		/**
		 * Register eraser for plugin
		 *
		 * @param $erasers
		 *
		 * @return mixed
		 */
		public function register_eraser( $erasers ) {
			$erasers['woocommerce-waitlist'] = array(
				'eraser_friendly_name' => __( 'Waitlist Data', 'woocommerce-waitlist' ),
				'callback'             => array( $this, 'generate_eraser_data' ),
			);

			return $erasers;
		}

		/**
		 * Generate data to add to the export file
		 *
		 * @param     $email
		 * @param int   $page
		 *
		 * @return array
		 */
		public function generate_export_data( $email, $page ) {
			$number       = 10;
			$products     = $this->get_waitlist_products( $number, $page );
			$export_items = array();
			foreach ( $products as $post ) {
				$waitlist = get_post_meta( $post->ID, 'woocommerce_waitlist', true );
				$archive  = get_post_meta( $post->ID, 'wcwl_waitlist_archive', true );
				if ( ! $this->user_is_on_waitlist( $email, $waitlist ) && ! $this->user_is_on_archive( $email, $archive ) ) {
					continue;
				}
				$product     = wc_get_product( $post->ID );
				$item_id     = "waitlist-{$post->ID}";
				$group_id    = 'waitlists';
				$group_label = __( 'Waitlist Data', 'woocommerce-waitlist' );
				$data        = $this->get_product_info( $product );
				if ( $this->user_is_on_waitlist( $email, $waitlist ) ) {
					$data = array_merge( $data, $this->get_waitlist_info( 'Yes', $waitlist, $email ) );
				} else {
					$data = array_merge( $data, $this->get_waitlist_info() );
				}
				if ( $this->user_is_on_archive( $email, $archive ) ) {
					$data = array_merge( $data, $this->get_archive_info( 'Yes' ) );
				} else {
					$data = array_merge( $data, $this->get_archive_info() );
				}
				$export_items[] = array(
					'group_id'    => $group_id,
					'group_label' => $group_label,
					'item_id'     => $item_id,
					'data'        => $data,
				);
			}

			return array(
				'data' => $export_items,
				'done' => count( $products ) < $number,
			);
		}

		/**
		 * Get the default product info for the export report
		 *
		 * @param $product
		 *
		 * @return array
		 */
		protected function get_product_info( $product ) {
			return array(
				array(
					'name'  => __( 'Product ID', 'woocommerce-waitlist' ),
					'value' => $product->get_id(),
				),
				array(
					'name'  => __( 'Product Name', 'woocommerce-waitlist' ),
					'value' => $product->get_name(),
				),
			);
		}

		/**
		 * Return required waitlist info for data export
		 *
		 * @param string $on_waitlist
		 * @param array  $waitlist
		 * @param int    $email
		 *
		 * @return array
		 */
		protected function get_waitlist_info( $on_waitlist = 'No', $waitlist = array(), $email = '' ) {
			$data      = array(
				array(
					'name'  => __( 'Currently on Waitlist', 'woocommerce-waitlist' ),
					'value' => __( $on_waitlist, 'woocommerce-waitlist' ),
				),
			);
			$join_text = __( 'Join Date', 'woocommerce-waitlist' );
			if ( 'Yes' === $on_waitlist ) {
				$data[] = array(
					'name'  => $join_text,
					'value' => $this->get_join_date( $waitlist, $email ),
				);
			} else {
				$data[] = array(
					'name'  => $join_text,
					'value' => '-',
				);
			}

			return $data;
		}

		/**
		 * Return required archive info for data export
		 *
		 * @param string $on_archive
		 *
		 * @return array
		 */
		protected function get_archive_info( $on_archive = 'No' ) {
			return array(
				array(
					'name'  => __( 'Email in Archives', 'woocommerce-waitlist' ),
					'value' => __( $on_archive, 'woocommerce-waitlist' ),
				),
			);
		}

		/**
		 * Return all products
		 *
		 * @param $number int how many to return at a time
		 * @param $page   int current page
		 *
		 * @return array
		 */
		protected function get_waitlist_products( $number, $page ) {
			$args  = array(
				'post_type'      => array( 'product', 'product_variation' ),
				'post_status'    => 'any',
				'posts_per_page' => $number,
				'meta_key'       => 'woocommerce_waitlist',
				'paged'          => $page,
			);
			$query = new WP_Query( $args );
			if ( ! isset( $query->posts ) || ! is_array( $query->posts ) ) {
				$query->posts = array();
			}

			return $query->posts;
		}

		/**
		 * Generate data to include when erasing users data
		 *
		 * @param $email
		 * @param $page
		 *
		 * @return array
		 */
		public function generate_eraser_data( $email, $page ) {
			if ( ! wc_string_to_bool( get_option( 'woocommerce_erasure_request_removes_waitlist_data', 'no' ) ) ) {
				return $this->get_retention_data_array();
			}
			$number       = 10;
			$products     = $this->get_waitlist_products( $number, $page );
			$data_removed = false;
			foreach ( $products as $post ) {
				$waitlist = get_post_meta( $post->ID, 'woocommerce_waitlist', true );
				$archive  = get_post_meta( $post->ID, 'wcwl_waitlist_archive', true );
				if ( ! $this->user_is_on_waitlist( $email, $waitlist ) && ! $this->user_is_on_archive( $email, $archive ) ) {
					continue;
				}
				$product = wc_get_product( $post->ID );
				if ( $product && $this->user_is_on_waitlist( $email, $waitlist ) ) {
					$this->remove_user_from_waitlist( $product, $email );
					$data_removed = true;
				}
				if ( $product && $this->user_is_on_archive( $email, $archive ) ) {
					$this->remove_user_from_archive( $archive, $email, $post->ID );
				}
			}

			return array(
				'items_removed'  => $data_removed,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => count( $products ) < $number,
			);
		}

		protected function get_retention_data_array() {
			return array(
				'items_removed'  => false,
				'items_retained' => true,
				'messages'       => array( __( 'Personal data for waitlists has been retained.', 'woocommerce-waitlist' ) ),
				'done'           => true,
			);
		}

		/**
		 * Removes given user from given product's waitlist
		 *
		 * @param $product
		 * @param $email
		 */
		protected function remove_user_from_waitlist( $product, $email ) {
			$waitlist = new Pie_WCWL_Waitlist( $product );
			$waitlist->unregister_user( $email );
			WC_Emails::instance();
			do_action( 'wcwl_left_mailout_send_email', $email, $product->get_id() );
		}

		/**
		 * Removes given user from given archive
		 *
		 * @param $archive
		 * @param $email
		 * @param $product_id
		 */
		protected function remove_user_from_archive( $archive, $email, $product_id ) {
			foreach ( $archive as $timestamp => $users ) {
				if ( empty( $users ) ) {
					unset( $archive[ $timestamp ] );
				} elseif ( isset( $archive[ $timestamp ][ $email ] ) ) {
					unset( $archive[ $timestamp ][ $email ] );
				} else {
					$user = get_user_by( 'email', $email );
					if ( isset( $archive[ $timestamp ][ $user->ID ] ) ) {
						unset( $archive[ $timestamp ][ $user->ID ] );
					}
				}
			}
			update_post_meta( $product_id, 'wcwl_waitlist_archive', $archive );
		}

		/**
		 * Check if the given user is on the given waitlist
		 *
		 * @param $email
		 * @param $waitlist
		 *
		 * @return bool
		 */
		protected function user_is_on_waitlist( $email, $waitlist ) {
			if ( ! $waitlist ) {
				return false;
			}
			$user = get_user_by( 'email', $email );
			if ( $user && key_exists( $user->ID, $waitlist ) ) {
				return true;
			}
			if ( key_exists( $email, $waitlist ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Check if given user is contained in the given archives
		 *
		 * @param $email
		 * @param $archive
		 *
		 * @return bool
		 */
		protected function user_is_on_archive( $email, $archive ) {
			if ( ! $archive ) {
				return false;
			}
			foreach ( $archive as $timestamp => $users ) {
				if ( key_exists( $email, $users ) ) {
					return true;
				}
				$user = get_user_by( 'email', $email );
				if ( $user && key_exists( $user->ID, $users ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Retrieve and format join date for user
		 *
		 * @param $waitlist
		 * @param $email
		 *
		 * @return bool|string
		 */
		protected function get_join_date( $waitlist, $email ) {
			if ( ! isset( $waitlist[ $email ] ) ) {
				$user      = get_user_by( 'email', $email );
				$timestamp = $waitlist[ $user->ID ];
			} else {
				$timestamp = $waitlist[ $email ];
			}

			return date( 'l jS \of F Y h:i:s A', $timestamp );
		}
	}
}
