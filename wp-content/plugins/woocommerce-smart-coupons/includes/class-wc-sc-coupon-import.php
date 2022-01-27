<?php
/**
 * Class to handle import of coupons
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.7.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_Importer' ) ) {
	include_once ABSPATH . '/wp-admin/includes/class-wp-importer.php';
}

if ( ! class_exists( 'WC_SC_Coupon_Import' ) ) {

	/**
	 * Class for handling coupon import
	 */
	class WC_SC_Coupon_Import extends WP_Importer {

		/**
		 * CSV attachment ID
		 *
		 * @var $id
		 */
		public $id;

		/**
		 * CSV attachment url
		 *
		 * @var $file_url
		 */
		public $file_url;

		/**
		 * The import page
		 *
		 * @var $import_page
		 */
		public $import_page;

		/**
		 * Posts
		 *
		 * @var $posts
		 */
		public $posts = array();

		/**
		 * Processed terms
		 *
		 * @var $processed_terms
		 */
		public $processed_terms = array();

		/**
		 * Processed Posts
		 *
		 * @var $processed_posts
		 */
		public $processed_posts = array();

		/**
		 * Post orphans
		 *
		 * @var $post_orphans
		 */
		public $post_orphans = array();

		/**
		 * Is fetch attchments
		 *
		 * @var $fetch_attachments
		 */
		public $fetch_attachments = false;

		/**
		 * The URL remap
		 *
		 * @var $url_remap
		 */
		public $url_remap = array();

		/**
		 * Logs
		 *
		 * @var $log
		 */
		public $log;

		/**
		 * Merged
		 *
		 * @var $merged
		 */
		public $merged;

		/**
		 * Skipped item count
		 *
		 * @var $skipped
		 */
		public $skipped = 0;

		/**
		 * Imported item count
		 *
		 * @var $imported
		 */
		public $imported = 0;

		/**
		 * Variable to hold instance of WC_SC_Coupon_Fields
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->import_page = 'wc-sc-coupons';
			add_filter( 'upload_dir', array( $this, 'upload_dir' ) );
			ob_start();
		}

		/**
		 * Get single instance of WC_SC_Coupon_Fields
		 *
		 * @return WC_SC_Coupon_Fields Singleton object of WC_SC_Coupon_Fields
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
		public function __call( $function_name, $arguments = array() ) {

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
		 * Registered callback function for the WordPress Importer
		 *
		 * Manages the three separate stages of the CSV import process
		 */
		public function dispatch() {
			global $woocommerce_smart_coupon;
			$step = ( empty( $_GET['step'] ) ) ? 0 : absint( wc_clean( wp_unslash( $_GET['step'] ) ) ); // phpcs:ignore

			switch ( $step ) {
				case 0:
					$this->greet();
					break;
				case 1:
					check_admin_referer( 'import-upload' );
					if ( $this->handle_upload() ) {
						$this->import_options();
					}
					break;
				case 2:
					$action_processed = $this->process_bulk_generate_action();
					$url              = admin_url( 'edit.php?post_type=shop_coupon' );

					ob_clean();
					wp_safe_redirect( $url );
					exit;
			}
		}

		/**
		 * Process bulk generation action
		 *
		 * @return bool $action_processed Is action processed
		 */
		public function process_bulk_generate_action() {
			global $woocommerce_smart_coupon;

			check_admin_referer( 'import-woocommerce-coupon' );

			$schedule_action  = '';
			$permission_error = false;
			$action_processed = false;

			$post_smart_coupons_generate_action = ( isset( $_POST['smart_coupons_generate_action'] ) ) ? wc_clean( wp_unslash( $_POST['smart_coupons_generate_action'] ) ) : ''; // phpcs:ignore
			$post_generate_and_import           = ( isset( $_POST['generate_and_import'] ) ) ? wc_clean( wp_unslash( $_POST['generate_and_import'] ) ) : ''; // phpcs:ignore

			if ( empty( $post_smart_coupons_generate_action ) && empty( $post_generate_and_import ) ) {

				$this->id       = ( ! empty( $_POST['import_id'] ) ) ? absint( wc_clean( wp_unslash( $_POST['import_id'] ) ) ) : 0; // phpcs:ignore
				$this->file_url = ( ! empty( $_POST['import_url'] ) ) ? wc_clean( wp_unslash( $_POST['import_url'] ) ) : ''; // phpcs:ignore

				if ( $this->id ) {
					$file = get_attached_file( $this->id );
				} else {
					$file = ( ! empty( $this->file_url ) ) ? ABSPATH . $this->file_url : '';
				}

				$upload_dir                     = wp_get_upload_dir();
				$csv_file_data['file_name']     = basename( $file );
				$csv_file_data['wp_upload_dir'] = $upload_dir['basedir'] . '/woocommerce_uploads/';

				$_POST['export_file'] = $csv_file_data;

				if ( ! isset( $_POST['no_of_coupons_to_generate'] ) ) {
					ini_set( 'auto_detect_line_endings', true ); // phpcs:ignore
					$fp = file( $file );
					if ( is_array( $fp ) && ! empty( $fp ) ) {
						$_POST['no_of_coupons_to_generate'] = count( $fp ) - 1;
					} else {
						$permission_error = true;
					}
				}

				$total_coupons_to_generate          = sanitize_text_field( wp_unslash( $_POST['no_of_coupons_to_generate'] ) );
				$_POST['total_coupons_to_generate'] = $total_coupons_to_generate;

				$_POST['action_stage']                  = 0;
				$_POST['smart_coupons_generate_action'] = 'import_from_csv';

				update_option( 'woo_sc_generate_coupon_posted_data', $_POST, 'no' );

				update_option( 'start_time_woo_sc', time(), 'no' );
				update_option( 'current_time_woo_sc', time(), 'no' );
				update_option( 'all_tasks_count_woo_sc', sanitize_text_field( wp_unslash( $_POST['no_of_coupons_to_generate'] ) ), 'no' );
				update_option( 'remaining_tasks_count_woo_sc', sanitize_text_field( wp_unslash( $_POST['no_of_coupons_to_generate'] ) ), 'no' );

				as_unschedule_action( 'woo_sc_generate_coupon_csv' );
				as_unschedule_action( 'woo_sc_import_coupons_from_csv' );

				$schedule_action = 'woo_sc_import_coupons_from_csv';
			} else {

				// If we are exporting coupons then create export file and save its path in options table.
				$coupon_column_headers   = $this->get_coupon_column_headers();
				$coupon_posts_headers    = $coupon_column_headers['posts_headers'];
				$coupon_postmeta_headers = $coupon_column_headers['postmeta_headers'];
				$coupon_term_headers     = $coupon_column_headers['term_headers'];
				$column_headers          = array_merge( $coupon_posts_headers, $coupon_postmeta_headers, $coupon_term_headers );
				$export_file = $woocommerce_smart_coupon->export_coupon_csv( $column_headers, array() ); // phpcs:ignore

				if ( is_array( $export_file ) && ! isset( $export_file['error'] ) ) {

					// Create CSV file.
					$csv_folder  = $export_file['wp_upload_dir'];
					$filename    = str_replace( array( '\'', '"', ',', ';', '<', '>', '/', ':' ), '', $export_file['file_name'] );
					$csvfilename = $csv_folder . $filename;
					$fp          = fopen( $csvfilename, 'w' ); // phpcs:ignore
					if ( false !== $fp ) {
						file_put_contents( $csvfilename, $export_file['file_content'] ); // phpcs:ignore
						fclose( $fp ); // phpcs:ignore
						$_POST['export_file'] = $export_file;

						$total_coupons_to_generate          = sanitize_text_field( wp_unslash( $_POST['no_of_coupons_to_generate'] ) );
						$_POST['total_coupons_to_generate'] = $total_coupons_to_generate;

						$_POST['action_stage'] = 0;

						update_option( 'woo_sc_generate_coupon_posted_data', $_POST, 'no' );
						update_option( 'start_time_woo_sc', time(), 'no' );
						update_option( 'current_time_woo_sc', time(), 'no' );
						update_option( 'all_tasks_count_woo_sc', sanitize_text_field( wp_unslash( $_POST['no_of_coupons_to_generate'] ) ), 'no' );
						update_option( 'remaining_tasks_count_woo_sc', sanitize_text_field( wp_unslash( $_POST['no_of_coupons_to_generate'] ) ), 'no' );

						as_unschedule_action( 'woo_sc_generate_coupon_csv' );
						as_unschedule_action( 'woo_sc_import_coupons_from_csv' );
						delete_option( 'woo_sc_action_data' );

						$schedule_action = 'woo_sc_generate_coupon_csv';
					} else {
						$permission_error = true;
					}
				} else {
					$permission_error = true;
				}
			}

			// Proceed only if there is no any file permission error.
			if ( true !== $permission_error ) {
				if ( ( ! empty( $post_smart_coupons_generate_action ) && ( 'woo_sc_is_email_imported_coupons' === $post_smart_coupons_generate_action || 'send_store_credit' === $post_smart_coupons_generate_action ) ) || ( isset( $_POST['woo_sc_is_email_imported_coupons'] ) ) ) {
					update_option( 'woo_sc_is_email_imported_coupons', 'yes', 'no' );
				}

				$generate_action = ( isset( $_POST['smart_coupons_generate_action'] ) ) ? wc_clean( wp_unslash( $_POST['smart_coupons_generate_action'] ) ) : ''; // phpcs:ignore
				$is_import_email = get_option( 'woo_sc_is_email_imported_coupons' );

				if ( 'yes' === $is_import_email && ( isset( $_POST['import_id'] ) || isset( $_POST['import_url'] ) ) ) { // phpcs:ignore
					$bulk_action = 'import_email';
				} elseif ( isset( $_POST['import_id'] ) || isset( $_POST['import_url'] ) ) { // phpcs:ignore
					$bulk_action = 'import';
				} elseif ( 'woo_sc_is_email_imported_coupons' === $generate_action ) {
					$bulk_action = 'generate_email';
				} elseif ( 'send_store_credit' === $generate_action ) {
					$bulk_action = 'send_store_credit';
				} else {
					$bulk_action = 'generate';
				}

				update_option( 'bulk_coupon_action_woo_sc', $bulk_action, 'no' );

				if ( ! empty( $schedule_action ) ) {
					do_action( $schedule_action );
					$action_processed = true;
				}
			}

			return $action_processed;
		}

		/**
		 * Create new posts based on import information
		 *
		 * @param array $post The post.
		 * @return int $global_coupon_id The imported coupon id
		 */
		public function process_coupon( $post ) {
			global $woocommerce_smart_coupon;

			$global_coupon_id = ''; // for handling global coupons.

			// Get parent.
			$post_parent = absint( $post['post_parent'] );

			if ( ! empty( $post_parent ) ) {
					// if we already know the parent, map it to the new local ID.
				if ( isset( $this->processed_posts[ $post_parent ] ) ) {
					$post_parent = $this->processed_posts[ $post_parent ];
					// otherwise record the parent for later.
				} else {
					$this->post_orphans[ intval( $post['post_id'] ) ] = $post_parent;
					$post_parent                                      = 0;
				}
			}

			$postdata = array(
				'import_id'      => $post['post_id'],
				'post_author'    => ( ! empty( $post['post_author'] ) ) ? absint( $post['post_author'] ) : get_current_user_id(),
				'post_date'      => ( $post['post_date'] ) ? gmdate( 'Y-m-d H:i:s', strtotime( $post['post_date'] ) ) : '',
				'post_date_gmt'  => ( $post['post_date_gmt'] ) ? gmdate( 'Y-m-d H:i:s', strtotime( $post['post_date_gmt'] ) ) : '',
				'post_content'   => $post['post_content'],
				'post_excerpt'   => $post['post_excerpt'],
				'post_title'     => $post['post_title'],
				'post_name'      => ( $post['post_name'] ) ? $post['post_name'] : sanitize_title( $post['post_title'] ),
				'post_status'    => $post['post_status'],
				'post_parent'    => $post_parent,
				'menu_order'     => $post['menu_order'],
				'post_type'      => 'shop_coupon',
				'post_password'  => $post['post_password'],
				'comment_status' => $post['comment_status'],
			);

			$post_id = wp_insert_post( $postdata, true );

			if ( is_wp_error( $post_id ) ) {

				$this->skipped++;
				unset( $post );
				return;

			}

			unset( $postdata );

			// map pre-import ID to local ID.
			if ( empty( $post['post_id'] ) ) {
				$post['post_id'] = absint( $post_id );
			}
			$this->processed_posts[ intval( $post['post_id'] ) ] = absint( $post_id );

			$coupon_code = strtolower( $post['post_title'] );

			// add/update post meta.
			if ( ! empty( $post['postmeta'] ) && is_array( $post['postmeta'] ) ) {

					$postmeta = array();
				foreach ( $post['postmeta'] as $meta ) {
					$postmeta[ $meta['key'] ] = $meta['value'];
				}

				foreach ( $postmeta as $meta_key => $meta_value ) {
					switch ( $meta_key ) {

						case 'customer_email':
							$customer_emails = maybe_unserialize( $meta_value );
							break;

						case 'coupon_amount':
							$coupon_amount = maybe_unserialize( $meta_value );
							break;

						case 'expiry_date':
							if ( empty( $meta_value ) && ! empty( $postmeta['sc_coupon_validity'] ) && ! empty( $postmeta['validity_suffix'] ) ) {
								$sc_coupon_validity = $postmeta['sc_coupon_validity'];
								$validity_suffix    = $postmeta['validity_suffix'];
								$meta_value         = gmdate( 'Y-m-d', strtotime( "+$sc_coupon_validity $validity_suffix" ) );
							}
							break;

						case 'discount_type':
							$discount_type = maybe_unserialize( $meta_value );
							break;

						case 'free_shipping':
							$allowed_free_shipping = maybe_unserialize( $meta_value );
							break;

						case '_used_by':
							if ( ! empty( $meta_value ) ) {
								$used_by = explode( '|', $meta_value );
								if ( ! empty( $used_by ) && is_array( $used_by ) ) {
									foreach ( $used_by as $_used_by ) {
										add_post_meta( $post_id, $meta_key, $_used_by );
									}
								}
							}
							break;

						case 'wc_sc_add_product_details':
							if ( ! empty( $meta_value ) ) {
								$add_product_details = array();
								$product_details     = explode( '|', $meta_value );
								if ( ! empty( $product_details ) ) {
									foreach ( $product_details as $index => $product_detail ) {
										$data = array_map( 'trim', explode( ',', $product_detail ) );
										if ( empty( $data[0] ) ) {
											continue;
										}
										$product_data['product_id']      = $data[0];
										$product_data['quantity']        = ( ! empty( $data[1] ) ) ? absint( $data[1] ) : 1;
										$product_data['discount_amount'] = ( ! empty( $data[2] ) ) ? $data[2] : '';
										$product_data['discount_type']   = ( ! empty( $data[3] ) ) ? $data[3] : 'percent';
										$add_product_details[]           = $product_data;
									}
								}
								$meta_value = $add_product_details;
							}
							break;

						default:
							$meta_value = apply_filters(
								'wc_sc_process_coupon_meta_value_for_import',
								$meta_value,
								array(
									'meta_key'        => $meta_key, // phpcs:ignore
									'postmeta'        => $postmeta,
									'post'            => $post,
									'coupon_importer' => $this,
								)
							);

					}

					if ( $meta_key ) {
						if ( 'customer_email' === $meta_key && ! empty( $postmeta['sc_disable_email_restriction'] ) && 'yes' === $postmeta['sc_disable_email_restriction'] ) {
							continue;
						}
						if ( '_used_by' === $meta_key ) {
							continue;
						}
						$is_import_meta = apply_filters(
							'wc_sc_is_import_meta',
							true,
							array(
								'meta_key'        => $meta_key, // phpcs:ignore
								'postmeta'        => $postmeta,
								'post'            => $post,
								'coupon_importer' => $this,
							)
						);
						if ( true === $is_import_meta ) {
							if ( $this->is_wc_gte_30() && 'expiry_date' === $meta_key ) {
								$meta_value = strtotime( $meta_value );
								if ( ! empty( $meta_value ) && is_numeric( $meta_value ) ) {
									$gmt_offset = get_option( 'gmt_offset', false );
									if ( false !== $gmt_offset && is_numeric( $gmt_offset ) ) {
										$gmt_offset_timestamp = $gmt_offset * HOUR_IN_SECONDS;
										$meta_value           = $meta_value - $gmt_offset_timestamp; // Substracts GMT offset from expiry date timestamp.
									}
								}
								update_post_meta( $post_id, 'date_expires', $meta_value );
							} else {
								update_post_meta( $post_id, $meta_key, maybe_unserialize( $meta_value ) );
							}
						}
					}
				}

				unset( $post['postmeta'] );
			}

			// Update term data.
			if ( ! empty( $post['term_data'] ) && is_array( $post['term_data'] ) ) {
				foreach ( $post['term_data'] as $data ) {
					if ( isset( $data['key'] ) && 'sc_coupon_category' === $data['key'] ) {
						if ( ! empty( $data['value'] ) ) {
							$coupon_cat_details = explode( '|', $data['value'] );
							wp_set_post_terms( $post_id, $coupon_cat_details, 'sc_coupon_category', true );
						}
					}
				}
				unset( $post['term_data'] );
			}

			$posted_data = get_option( 'woo_sc_generate_coupon_posted_data', true );
			if ( isset( $posted_data['sc_coupon_category'] ) && ! empty( $posted_data['sc_coupon_category'] ) ) {
				wp_set_post_terms( $post_id, $posted_data['sc_coupon_category'], 'sc_coupon_category', true );
			}

			$is_email_imported_coupons = get_option( 'woo_sc_is_email_imported_coupons' );

			if ( 'yes' === $is_email_imported_coupons && ! empty( $customer_emails ) && ( ! empty( $coupon_amount ) || 'yes' === $allowed_free_shipping ) && ! empty( $coupon_code ) && ! empty( $discount_type ) ) {
				$coupon       = array(
					'amount' => $coupon_amount,
					'code'   => $coupon_code,
				);
				$coupon_title = array();
				foreach ( $customer_emails as $customer_email ) {
					$coupon_title[ $customer_email ] = $coupon;
				}

				$message = '';
				if ( ! empty( $posted_data ) && is_array( $posted_data ) ) {
					$message = ( ! empty( $posted_data['smart_coupon_message'] ) ) ? $posted_data['smart_coupon_message'] : '';
				}

				$woocommerce_smart_coupon->sa_email_coupon( $coupon_title, $discount_type, 0, '', $message );
			}

			$this->imported++;

			// code for handling global coupons option.
			if ( ( ! empty( $post['post_status'] ) && 'publish' === $post['post_status'] )
					&& ( isset( $postmeta['customer_email'] ) && array() === $postmeta['customer_email'] )
					&& ( isset( $postmeta['sc_is_visible_storewide'] ) && 'yes' === $postmeta['sc_is_visible_storewide'] )
					&& ( isset( $postmeta['auto_generate_coupon'] ) && 'yes' !== $postmeta['auto_generate_coupon'] )
					&& ( isset( $postmeta['discount_type'] ) && 'smart_coupon' !== $postmeta['discount_type'] ) ) {

				$global_coupon_id = $post_id;
			}

			unset( $post );

			return $global_coupon_id;
		}

		/**
		 * Parses the CSV file and prepares us for the task of processing parsed data
		 *
		 * @param string $file Path to the CSV file for importing.
		 */
		public function import_start( $file ) {

			if ( ! is_file( $file ) ) {
				echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'woocommerce-smart-coupons' ) . '</strong><br />';
				echo esc_html__( 'The file does not exist, please try again.', 'woocommerce-smart-coupons' ) . '</p>';
				die();
			}

			$this->parser = new WC_SC_Coupon_Parser( 'shop_coupon' );
			$import_data  = $this->parser->parse_data( $file );

			$this->parsed_data = $import_data[0];
			$this->raw_headers = $import_data[1];

			unset( $import_data );

			wp_defer_term_counting( true );
			wp_defer_comment_counting( true );

		}

		/**
		 * Added to http_request_timeout filter to force timeout at 60 seconds during import
		 *
		 * @param int $val The current value.
		 * @return int 60
		 */
		public function bump_request_timeout( $val ) {
			return 60;
		}

		/**
		 * Performs post-import cleanup of files and the cache
		 */
		public function import_end() {

			wp_cache_flush();

			wp_defer_term_counting( false );
			wp_defer_comment_counting( false );

			do_action( 'import_end' );

		}

		/**
		 * Handles the CSV upload and initial parsing of the file to prepare for
		 * displaying author import options
		 *
		 * @return bool False if error uploading or invalid file, true otherwise
		 */
		public function handle_upload() {

			$post_file_url = ( ! empty( $_POST['file_url'] ) ) ? wc_clean( wp_unslash( $_POST['file_url'] ) ) : ''; // phpcs:ignore

			if ( empty( $post_file_url ) ) {
				$file = wp_import_handle_upload();

				if ( isset( $file['error'] ) ) {
					echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'woocommerce-smart-coupons' ) . '</strong><br />';
					echo esc_html( $file['error'] ) . '</p>';
					return false;
				}

				$this->id = absint( $file['id'] );

			} else {

				if ( file_exists( ABSPATH . $post_file_url ) ) {
					$this->file_url = esc_attr( $post_file_url );
				} else {
					echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'woocommerce-smart-coupons' ) . '</strong></p>';
					return false;
				}
			}

			return true;
		}

		/**
		 * Function to validate import CSV file is following correct format
		 *
		 * @param  array $header File headers.
		 * @return bool
		 */
		public function validate_file_header( $header = array() ) {

			$is_valid = true;

			if ( empty( $header ) || count( $header ) < 21 ) {
				$is_valid = false;
			} else {
				$default = array(
					'post_title',
					'post_excerpt',
					'post_status',
					'post_parent',
					'menu_order',
					'post_date',
					'discount_type',
					'coupon_amount',
					'free_shipping',
					'expiry_date',
					'minimum_amount',
					'maximum_amount',
					'individual_use',
					'exclude_sale_items',
					'product_ids',
					'exclude_product_ids',
					'product_categories',
					'exclude_product_categories',
					'customer_email',
					'usage_limit',
					'usage_limit_per_user',
				);

				foreach ( $default as $head ) {
					if ( ! in_array( $head, $header, true ) ) {
						$is_valid = false;
						break;
					}
				}
			}

			return $is_valid;
		}

		/**
		 * Display pre-import options
		 */
		public function import_options() {
			$j = 0;

			if ( $this->id ) {
				$file = get_attached_file( $this->id );
			} else {
				$file = ( ! empty( $this->file_url ) ) ? ABSPATH . $this->file_url : '';
			}

			// Set locale.
			$enc = mb_detect_encoding( $file, 'UTF-8, ISO-8859-1', true );
			if ( $enc ) {
				setlocale( LC_ALL, 'en_US.' . $enc );
			}
			ini_set( 'auto_detect_line_endings', true ); // phpcs:ignore

			$is_email_present = false;

			$handle = fopen( $file, 'r' ); // phpcs:ignore

			if ( false !== $handle ) {

				$row         = array();
				$raw_headers = array();

				$header = fgetcsv( $handle, 0 ); // gets header of the file.

				$is_valid = $this->validate_file_header( $header );

				if ( ! $is_valid ) {
					fclose( $handle ); // phpcs:ignore
					?>
					<div class="error">
						<p><?php echo '<strong>' . esc_html__( 'Coupon Import Error', 'woocommerce-smart-coupons' ) . '</strong>: ' . esc_html__( 'Invalid CSV file. Make sure your CSV file contains all columns, header row, and data in correct format.', 'woocommerce-smart-coupons' ) . ' <a href="' . esc_url( plugins_url( dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/sample.csv' ) ) . '">' . esc_html__( 'Download a sample.csv to confirm', 'woocommerce-smart-coupons' ) . '</a>.'; ?></p>
					</div>
					<?php
					$this->greet();
					return;
				}

				while ( false !== ( $postmeta = fgetcsv( $handle, 0 ) ) ) { // phpcs:ignore
					foreach ( $header as $key => $heading ) {
						if ( ! $heading ) {
							continue;
						}

						$s_heading         = strtolower( $heading );
						$row[ $s_heading ] = ( isset( $postmeta[ $key ] ) ) ? $this->format_data_from_csv( $postmeta[ $key ], $enc ) : '';
						if ( ! $is_email_present && 'customer_email' === $header[ $key ] ) {
							if ( ! empty( $row[ $s_heading ] ) && is_email( $row[ $s_heading ] ) ) {
								$is_email_present = true;
							}
						}
						$raw_headers[ $s_heading ] = $heading;
					}
					// Thanks to: Rohit Kumar.
					if ( true === $is_email_present ) {
						break;
					}
				}

				fclose( $handle ); // phpcs:ignore
			}
			?>
			<style type="text/css">
				.sc-import-outer-box {

				}
				.sc-import-outer-box h3,
				.sc-import-outer-box .button {
					text-align: center;
				}
				.sc-import-outer-box .button-primary {
					margin: 2em;
				}
				.sc-import-inner-box {
					padding: 0 1.5em;
				}
				.sc-import-inner-box .sc-import-ready {
					width: fit-content;
					margin: 0 auto;
				}
				.sc-import-outer-box .dashicons {
					display: inline-block;
					vertical-align: middle;
				}
				.sc-import-outer-box .dashicons-yes:before {
					color: #0dcc00;
				}
			</style>
			<?php
				$import_step_2_url = add_query_arg(
					array(
						'page' => 'wc-smart-coupons',
						'tab'  => 'import-smart-coupons',
						'step' => '2',
					),
					admin_url( 'admin.php' )
				);
			?>
			<form action="<?php echo esc_url( $import_step_2_url ); ?>" method="post">
				<?php wp_nonce_field( 'import-woocommerce-coupon' ); ?>
				<input type="hidden" name="import_id" value="<?php echo esc_attr( $this->id ); ?>" />
				<input type="hidden" name="import_url" value="<?php echo esc_attr( $this->file_url ); ?>" />

				<div class="postbox sc-import-outer-box">
					<div class="sc-import-inner-box">
						<h3><?php echo esc_html__( 'All set, Begin import?', 'woocommerce-smart-coupons' ); ?></h3>

						<div class="sc-import-ready">
							<ul class="sc-import-ready-list">
								<li><span class="dashicons dashicons-yes"></span> <?php echo esc_html__( 'File uploaded OK', 'woocommerce-smart-coupons' ); ?></li>
								<li><span class="dashicons dashicons-yes"></span> <?php echo esc_html__( 'File format seems OK', 'woocommerce-smart-coupons' ); ?></li>
							</ul>
							<?php $is_send_email = $this->is_email_template_enabled(); ?>
							<?php if ( 'yes' === $is_send_email && $is_email_present ) { ?>
							<p>
								<label for="woo_sc_is_email_imported_coupons"><input type="checkbox" name="woo_sc_is_email_imported_coupons" id="woo_sc_is_email_imported_coupons"  />
								<?php echo esc_html__( 'Email coupon to recipients?', 'woocommerce-smart-coupons' ) . ' <span class="woocommerce-help-tip" data-tip="' . esc_attr__( 'Enable this to send coupon to recipient\'s email addresses, provided in imported file.', 'woocommerce-smart-coupons' ) . '"></span>'; ?></label>
							</p>
							<?php } ?>
						</div>
						<center><input type="submit" class="button button-primary button-hero" value="<?php echo esc_attr__( 'Import', 'woocommerce-smart-coupons' ); ?>" /></center>
					</div>
				</div>

			</form>
			<?php
		}

		/**
		 * Format data passed from CSV
		 *
		 * @param array  $data The data to format.
		 * @param string $enc Encoding.
		 */
		public function format_data_from_csv( $data, $enc ) {
			return ( 'UTF-8' === $enc ) ? $data : utf8_encode( $data );
		}

		/**
		 * Display introductory text and file upload form
		 */
		public function greet() {
			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}
			?>
			<style type="text/css">
				.sc-file-container {
					overflow: hidden;
					position: relative;
				}
				.sc-file-container [type=file] {
					display: block;
					position: absolute;
					opacity: 0;
					font-size: 1em;
					filter: alpha(opacity=0);
					min-height: 100%;
					min-width: 100%;
					right: 0;
					text-align: right;
					top: 0;
				}
				.sc-import-input-file-container {
					text-align: center;
					margin: 5em 0;
				}
				.sc-import-input-file-container .dashicons-yes:before {
					color: #0dcc00;
				}
				.sc-import-input-file-container .dashicons {
					top: 50%;
					transform: translateY(50%);
				}
				.sc-import-server-file,
				.sc-import-server-file-input {
					margin: 1em;
				}
				.sc-import-input-file-container .button-primary {
					margin-top: 2em;
				}
			</style>
			<script type="text/javascript">
				jQuery(function(){
					jQuery('input#upload').on('change', function(){
						jQuery('.sc-file-container-label').html('<?php echo esc_html__( 'Chosen', 'woocommerce-smart-coupons' ); ?> <span class="dashicons dashicons-yes"></span>');
					});
					jQuery('.sc-import-server-file a').on('click', function(){
						jQuery('.sc-import-server-file-input').slideToggle();
					});
					jQuery('.sc-import-input-file-container').on('change keyup', 'input#upload, input#file_url', function(){
						var input_val = jQuery(this).val();
						if ( input_val != undefined && input_val != '' ) {
							jQuery('.sc-import-input-file-container').find('input.button').prop('disabled', false);
						} else {
							jQuery('.sc-import-input-file-container').find('input.button').prop('disabled', true);
						}
					});
				});
			</script>
			<div class="woo-sc-form-wrapper">
				<p><?php echo esc_html__( 'Hi there! Upload a CSV file with coupons details to import them into your shop.', 'woocommerce-smart-coupons' ); ?></p>
				<p><?php echo esc_html__( 'The CSV must adhere to a specific format and include a header row.', 'woocommerce-smart-coupons' ) . '&nbsp;' . '<a href="' . esc_url( plugins_url( dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/sample.csv' ) ) . '">' . esc_html__( 'Click here to download a sample', 'woocommerce-smart-coupons' ) . '</a>, ' . esc_html__( 'and create your CSV based on that.', 'woocommerce-smart-coupons' );  // phpcs:ignore ?></p>
				<p><?php echo esc_html__( 'Note: If any coupon from the CSV file already exists in the store, it will not update the existing coupon, instead a new coupon will be imported & the previous coupon with the same code will become inactive.', 'woocommerce-smart-coupons' );  // phpcs:ignore ?></p>
				<p><?php echo esc_html__( 'Ready to import? Choose a .csv file, then click "Upload file".', 'woocommerce-smart-coupons' ); ?></p>
				<div id="poststuff">
					<div class="postbox sc-import-outer-box">
						<div class="sc-import-inner-box">
							<?php

							$action = add_query_arg(
								array(
									'page' => 'wc-smart-coupons',
									'tab'  => 'import-smart-coupons',
									'step' => '1',
								),
								'admin.php'
							);

							$bytes      = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
							$size       = size_format( $bytes );
							$upload_dir = wp_get_upload_dir();

							if ( ! empty( $upload_dir['error'] ) ) {
								?>
									<div class="error">
										<p><?php echo esc_html__( 'Before you can upload your import file, you will need to fix the following error:', 'woocommerce-smart-coupons' ); ?></p>
										<p><strong><?php echo esc_html( $upload_dir['error'] ); ?></strong></p>
									</div>
								<?php
							} else {
								?>
								<form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo esc_url( wp_nonce_url( $action, 'import-upload' ) ); ?>">
									<div class="sc-import-input-file-container">
										<label for="upload" class="button button-hero sc-file-container">
											<span class="sc-file-container-label"><?php echo esc_html__( 'Choose a CSV file', 'woocommerce-smart-coupons' ); ?> <span class="dashicons dashicons-upload"></span></span>
											<input type="file" id="upload" name="import" accept=".csv" size="25" />
										</label>
										<input type="hidden" name="action" value="save" />
										<input type="hidden" name="max_file_size" value="<?php echo esc_attr( $bytes ); ?>" />
										<p><small><?php printf( esc_html__( 'Maximum file size', 'woocommerce-smart-coupons' ) . ': ' . esc_html( $size ) ); ?></small></p>
										<p><?php echo esc_html__( 'OR', 'woocommerce-smart-coupons' ); ?></p>
										<p class="sc-import-server-file"><small><?php echo esc_html__( 'Already uploaded CSV to the server?', 'woocommerce-smart-coupons' ) . ' <a href="javascript:void(0)">' . esc_html__( 'Enter location on the server', 'woocommerce-smart-coupons' ) . '</a>:'; ?></small></p>
										<p class="sc-import-server-file-input" style="display: none;"><?php echo esc_html( ' ' . ABSPATH . ' ' ); ?><input type="text" id="file_url" name="file_url" size="25" /></p>
										<input type="submit" class="button button-primary button-hero" value="<?php echo esc_attr__( 'Upload file', 'woocommerce-smart-coupons' ); ?>" disabled />&nbsp;
									</div>
								</form>
								<?php
							}

							?>
						</div>
					</div>
				</div>
			</div>
			<?php

		}

		/**
		 * Change upload dir for coupon import.
		 *
		 * @param array $pathdata Array of paths.
		 * @return array
		 */
		public function upload_dir( $pathdata = array() ) {

			$nonce = ( ! empty( $_GET['_wpnonce'] ) ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

			if ( ! wp_verify_nonce( $nonce, 'import-upload' ) ) {
				return $pathdata;
			}

			if ( empty( $_GET['page'] ) || 'wc-smart-coupons' !== $_GET['page'] ) {
				return $pathdata;
			}

			if ( empty( $_GET['tab'] ) || 'import-smart-coupons' !== $_GET['tab'] ) {
				return $pathdata;
			}

			if ( empty( $pathdata['subdir'] ) ) {
				$pathdata['path']   = $pathdata['path'] . '/woocommerce_uploads';
				$pathdata['url']    = $pathdata['url'] . '/woocommerce_uploads';
				$pathdata['subdir'] = '/woocommerce_uploads';
			} else {
				$new_subdir = '/woocommerce_uploads';

				$pathdata['path']   = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['path'] );
				$pathdata['url']    = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['url'] );
				$pathdata['subdir'] = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['subdir'] );
			}

			return $pathdata;
		}

	}

}

WC_SC_Coupon_Import::get_instance();
