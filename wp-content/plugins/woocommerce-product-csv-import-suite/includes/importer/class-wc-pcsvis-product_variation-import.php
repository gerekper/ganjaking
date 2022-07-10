<?php
/**
 * WordPress Importer class for managing the import process of a CSV file
 *
 * @package WordPress
 * @subpackage Importer
 */
if ( ! class_exists( 'WC_PCSVIS_Product_Import' ) ) {
	return;
}

class WC_PCSVIS_Product_Variation_Import extends WC_PCSVIS_Product_Import {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->import_page = 'woocommerce_variation_csv';

		add_filter( 'import_post_meta_value', array( $this, 'filter_post_meta_value' ), 10, 2 );
	}

	/**
	 * Filter post meta values.
	 *
	 * @since 1.10.11
	 * @version 1.10.11
	 * @param mixed $value
	 * @param string $key
	 */
	public function filter_post_meta_value( $value, $key ) {
		// Format _sale_price_dates_from to timestamp
		if ( '_sale_price_dates_from' === $key ) {
			$value = strtotime( $value );
		}

		if ( '_sale_price_dates_to' === $key ) {
			$value = strtotime( $value );
		}

		return $value;
	}

	/**
	 * Create new posts based on import information
	 */
	public function process_product( $post ) {
		global $wpdb;

		wp_suspend_cache_invalidation( true );

		$merging               = ( ! empty( $post['merging'] ) && $post['merging'] ) ? true : false;
		$processing_product_id = absint( $post['post_id'] );
		$insert_meta_data      = array();

		if ( empty( $post['post_parent'] ) ) {
			$this->add_import_result( 'skipped', __( 'No product variation parent set', 'woocommerce-product-csv-import-suite' ), $processing_product_id, 'Not set', $post['sku'] );
			WC_Product_CSV_Import_Suite::log( __('> Skipping - no post parent set.', 'woocommerce-product-csv-import-suite') );
			return;
		}

		if ( ! empty( $processing_product_id ) && isset( $this->processed_posts[ $processing_product_id ] ) ) {
			$this->add_import_result( 'skipped', __( 'Product variation already processed', 'woocommerce-product-csv-import-suite' ), $processing_product_id, get_the_title( $post['post_parent'] ), $post['sku'] );
			WC_Product_CSV_Import_Suite::log( __('> Post ID already processed. Skipping.', 'woocommerce-product-csv-import-suite') );
			return;
		}

		if ( isset( $post['post_status'] ) && 'auto-draft' === $post['post_status'] ) {
			$this->add_import_result( 'skipped', __( 'Skipping auto-draft', 'woocommerce-product-csv-import-suite' ), $processing_product_id, get_the_title( $post['post_parent'] ), $post['sku'] );
			WC_Product_CSV_Import_Suite::log( __('> Skipping auto-draft.', 'woocommerce-product-csv-import-suite') );
			return;
		}

		$post_parent = (int) $post['post_parent'];
		$post_parent_exists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE ID = %d", $post_parent ) );

		if ( ! $post_parent_exists ) {
			$this->add_import_result( 'failed', __( 'Variation parent does not exist', 'woocommerce-product-csv-import-suite' ), $processing_product_id, 'Does not exist', $post['sku'] );
			WC_Product_CSV_Import_Suite::log( sprintf( __('> Variation parent does not exist! (#%d)', 'woocommerce-product-csv-import-suite'), $post_parent ) );
			return;
		}

		// Check post type to avoid conflicts with IDs
		if ( $merging && get_post_type( $processing_product_id ) !== 'product_variation' ) {
			$this->add_import_result( 'skipped', __( 'Post is not a product variation', 'woocommerce-product-csv-import-suite' ), $processing_product_id, 'Not a variation', $post['sku'] );
			WC_Product_CSV_Import_Suite::log( sprintf( __('> &#8220;%s&#8221; is not a product variation.', 'woocommerce-product-csv-import-suite'), $processing_product_id ), true );
			unset( $post );
			return;
		}

		if ( $merging ) {

			// Only merge fields which are set
			$post_id = $processing_product_id;

			WC_Product_CSV_Import_Suite::log( sprintf( __('> Merging post ID %s.', 'woocommerce-product-csv-import-suite'), $post_id ) );

			$postdata = array( 'ID' => $post_id );
			if (!empty($post['post_date'])) $postdata['post_date'] = date("Y-m-d H:i:s", strtotime( $post['post_date'] ) );
			if (!empty($post['post_date_gmt'])) $postdata['post_date_gmt'] = date("Y-m-d H:i:s", strtotime( $post['post_date_gmt'] ) );
			if (!empty($post['post_status'])) $postdata['post_status'] = $post['post_status'];
			if (!empty($post['menu_order'])) $postdata['menu_order'] = $post['menu_order'];
			$postdata['post_parent'] = $post_parent;

			if ( sizeof( $postdata ) ) {
				if ( wp_update_post( $postdata ) ) {
					WC_Product_CSV_Import_Suite::log( __( '> Merged post data: ', 'woocommerce-product-csv-import-suite' ) . print_r( $postdata, true ) );
				} else {
					WC_Product_CSV_Import_Suite::log( __( '> Failed to merge post data: ', 'woocommerce-product-csv-import-suite' ) . print_r( $postdata, true ) );
				}
			}

		} else {

			$processing_product_sku   = '';
			if ( ! empty( $post['sku'] ) ) {
				$processing_product_sku = $post['sku'];
			}

			if ( $this->variation_exists( $post_parent, $processing_product_id, $processing_product_sku ) ) {
				$this->add_import_result( 'skipped', __( 'Variation already exists', 'woocommerce-product-csv-import-suite' ), $processing_product_id, get_the_title( $post['post_parent'] ), $processing_product_sku );
				WC_Product_CSV_Import_Suite::log( sprintf( __( '> &#8220;%s&#8221; already exists.', 'woocommerce-product-csv-import-suite' ), esc_html( $post['post_title'] ) ), true );
				unset( $post );
				return;
			}

			// Insert product
			WC_Product_CSV_Import_Suite::log( __('> Inserting variation.', 'woocommerce-product-csv-import-suite') );

			$postdata = array(
				'import_id' 	=> $processing_product_id,
				'post_date' 	=> ( $post['post_date'] ) ? date( 'Y-m-d H:i:s', strtotime( $post['post_date'] )) : '',
				'post_date_gmt' => ( $post['post_date_gmt'] ) ? date( 'Y-m-d H:i:s', strtotime( $post['post_date_gmt'] )) : '',
				'post_status' 	=> $post['post_status'],
				'post_parent' 	=> $post_parent,
				'menu_order' 	=> $post['menu_order'],
				'post_type' 	=> 'product_variation',
			);

			$post_id = wp_insert_post( $postdata, true );

			if ( is_wp_error( $post_id ) ) {

				$this->add_import_result( 'failed', __( 'Failed to import product variation', 'woocommerce-product-csv-import-suite' ), $processing_product_id, get_the_title( $post['post_parent'] ), $post['sku'] );

				WC_Product_CSV_Import_Suite::log( sprintf( __( 'Failed to import product &#8220;%s&#8221;', 'woocommerce-product-csv-import-suite' ), esc_html($post['post_title']) ) );
				return;

			} else {
				WC_Product_CSV_Import_Suite::log( sprintf( __('> Inserted - post ID is %s.', 'woocommerce-product-csv-import-suite' ), $post_id ) );

				// Set post title now we have an ID
				$postdata['ID']         = $post_id;
				$postdata['post_title'] = sprintf( __( 'Variation #%s of %s', 'woocommerce' ), $post_id, get_the_title( $post_parent ) );
				wp_update_post( $postdata );
			}
		}

		// map pre-import ID to local ID
		if ( empty( $processing_product_id ) ) {
			$processing_product_id = (int) $post_id;
		}

		$this->processed_posts[ intval( $processing_product_id ) ] = (int) $post_id;
		$this->process_terms( $post_id, $post['terms'] );

		// Process post meta
		if ( ! empty( $post['postmeta'] ) && is_array( $post['postmeta'] ) ) {
			foreach ( $post['postmeta'] as $meta ) {
				if ( $key = apply_filters( 'import_post_meta_key', $meta['key'] ) ) {
					/**
					 * Filter import_post_meta_value.
					 *
					 * To manipulate the value from the import
					 * @since 1.10.11
					 */
					$insert_meta_data[ $key ] = apply_filters( 'import_post_meta_value', maybe_unserialize( $meta['value'] ), $key );
				}
			}
		}

		// Import images and add to post
		if ( ! empty( $post['images'] ) ) {

			$featured = true;

			if ( $merging ) {

				// Remove old
				delete_post_meta( $post_id, '_thumbnail_id' );

				// Delete old attachments
				$attachments = get_posts( 'post_parent=' . $post_id . '&post_type=attachment&fields=ids&post_mime_type=image&numberposts=-1' );

				foreach ( $attachments as $attachment ) {

					$url = wp_get_attachment_url( $attachment );

					if ( in_array( $url, $post['images'] ) ) {
						if ( $url == $post['images'][0] ) {
							$insert_meta_data['_thumbnail_id'] = $attachment;
						}
						unset( $post['images'][ array_search( $url, $post['images'] ) ] );
					} else {
						// Detach
						$attachment_post = array();
						$attachment_post['ID'] = $attachment;
						$attachment_post['post_parent'] = '';
						wp_update_post( $attachment_post );
					}
				}

				WC_Product_CSV_Import_Suite::log( __( '> > Old images processed', 'woocommerce-product-csv-import-suite' ) );

			}

			if ( $post['images'] ) foreach ( $post['images'] as $image ) {

				WC_Product_CSV_Import_Suite::log( sprintf( __( '> > Importing image "%s"', 'woocommerce-product-csv-import-suite' ), $image ) );

				$wp_filetype = wp_check_filetype( basename( $image ), null );
				$wp_upload_dir = wp_upload_dir();
				$filename = basename( $image );

				$attachment = array(
					 'post_mime_type' 	=> $wp_filetype['type'],
					 'post_title' 		=> preg_replace('/\.[^.]+$/', '', basename( $filename )),
					 'post_content' 	=> '',
					 'post_status' 		=> 'inherit'
				);

				$attachment_id = $this->process_attachment( $attachment, $image, $post_id );

				if ( ! is_wp_error( $attachment_id ) ) {
					if ( $featured ) {
						$insert_meta_data['_thumbnail_id'] = $attachment_id;
					}

					update_post_meta( $attachment_id, '_woocommerce_exclude_image', 0 );

					$featured = false;
				} else {
					WC_Product_CSV_Import_Suite::log( '> > ' . $attachment_id->get_error_message() );
				}
			}

			WC_Product_CSV_Import_Suite::log( __( '> > Images set', 'woocommerce-product-csv-import-suite' ) );
		}

		// Import GPF
		if ( ! empty( $post['gpf_data'] ) && is_array( $post['gpf_data'] ) ) {
			$insert_meta_data['_woocommerce_gpf_data'] = $post['gpf_data'];
		}

		// Delete existing meta first
		$wpdb->query( 'START TRANSACTION' );
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key IN ( '" . implode( "','", array_map( 'esc_sql', array_keys( $insert_meta_data ) ) ) . "' ) and post_id = %d", $post_id ) );

		$groups_active = class_exists( 'Groups_WS' );

		// When attributes come as uppercase, it cause variations combinations to bork.
		// See https://github.com/woocommerce/woocommerce-product-csv-import-suite/issues/68
		$insert_meta_data = array_change_key_case( $insert_meta_data, CASE_LOWER );

		// Format meta data
		foreach ( $insert_meta_data as $key => $value ) {
			$meta_key      = wp_unslash( $key );
			$meta_value    = wp_unslash( $value );
			$meta_value    = sanitize_meta( $meta_key, $meta_value, 'post' );

			if ( $groups_active && '_groups_variation_groups' === $key && ! empty( $value ) ) {
				foreach ( $value as $group ) {
					$meta_values[] = $wpdb->prepare( "( %d, %s, %s )", $post_id, $meta_key, $group );
				}

				continue;
			}

			if ( $groups_active && '_groups_variation_groups_remove' === $key && ! empty( $value ) ) {
				foreach ( $value as $group ) {
					$meta_values[] = $wpdb->prepare( "( %d, %s, %s )", $post_id, $meta_key, $group );
				}

				continue;
			}

			$meta_value    = maybe_serialize( $meta_value );
			$meta_values[] = $wpdb->prepare( "( %d, %s, %s )", $post_id, $meta_key, $meta_value );
		}

		// Then insert meta data
		$wpdb->query( "INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value ) VALUES " . implode( ',', $meta_values ) );
		$wpdb->query( 'COMMIT' );

		if ( $merging ) {
			$this->add_import_result( 'merged', 'Merge successful', $post_id, get_the_title( $post_parent ), $post['sku'] );
			WC_Product_CSV_Import_Suite::log( sprintf( __('> Finished merging variation ID %s.', 'woocommerce-product-csv-import-suite'), $post_id ) );
		} else {
			$this->add_import_result( 'imported', 'Import successful', $post_id, get_the_title( $post_parent ), $post['sku'] );
			WC_Product_CSV_Import_Suite::log( sprintf( __('> Finished importing variation ID %s.', 'woocommerce-product-csv-import-suite'), $post_id ) );
		}

		wp_suspend_cache_invalidation( false );
		clean_post_cache( $post_id );

		if ( ! isset( $this->data_store ) ) {
			$this->data_store = new WC_PCSVIS_Product_Data_Store();
		}

		$this->data_store->update_lookup( $post_id );

		unset( $post );
	}

	/**
	 * Checks to see if a variation exists for a specific parent based on ID or SKU
	 * @param  int $parent_id   The ID of the parent product
	 * @param  int $id          The ID for the variation
	 * @param  string $sku      The SKU for the variation
	 * @return bool             True if the variation exists, false if not
	 */
	public function variation_exists( $parent_id, $id, $sku = '' ) {
		global $wpdb;

		// SKU Check
		if ( $sku ) {
			$post_exists_sku = $wpdb->get_var( $wpdb->prepare( "
				SELECT $wpdb->posts.ID
				FROM $wpdb->posts
				LEFT JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )
				WHERE $wpdb->posts.post_status IN ( 'publish', 'private', 'draft', 'pending', 'future' )
				AND $wpdb->postmeta.meta_key = '_sku' AND $wpdb->postmeta.meta_value = '%s'
			", $sku ) );

			if ( $post_exists_sku ) {
				return true;
			}
		}

		// ID check
		$query = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'product_variation' AND post_parent = %d AND ID = %d AND post_status IN ( 'publish', 'private', 'draft', 'pending', 'future' )", $parent_id, $id );
		$posts_that_exist = $wpdb->get_col( $query );
		if ( $posts_that_exist ) {
			return true;
		}

		return false;
	}

	/**
	 * Parses the CSV file and prepares us for the task of processing parsed data
	 *
	 * @param string $file Path to the CSV file for importing
	 */
	function import_start( $file, $mapping, $start_pos, $end_pos ) {
		WC_Product_CSV_Import_Suite::log( __( 'Parsing product variations CSV.', 'woocommerce-product-csv-import-suite' ) );

		$this->parser = new WC_CSV_Parser( 'product_variation' );
		$this->data_store = new WC_PCSVIS_Product_Data_Store();

		list( $this->parsed_data, $this->raw_headers, $position ) = $this->parser->parse_data( $file, $this->delimiter, $mapping, $start_pos, $end_pos );

		WC_Product_CSV_Import_Suite::log( __( 'Finished parsing product variations CSV.', 'woocommerce-product-csv-import-suite' ) );

		unset( $import_data );

		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );

		return $position;
	}

	// Display import page title
	function header() {
		echo '<div class="wrap"><div class="icon32" id="icon-woocommerce-importer"><br></div>';
		echo '<h2>' . ( empty( $_GET['merge'] ) ? __( 'Import Product Variations', 'woocommerce-product-csv-import-suite' ) : __( 'Merge Product Variations', 'woocommerce-product-csv-import-suite' ) ) . '</h2>';
	}

	/**
	 * Display introductory text and file upload form
	 */
	public function greet() {
		$action     = 'admin.php?import=woocommerce_variation_csv&amp;step=1&amp;merge=' . ( ! empty( $_GET['merge'] ) ? 1 : 0 );
		$bytes      = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
		$size       = size_format( $bytes );
		$upload_dir = wp_upload_dir();

		include( 'views/html-import-greeting.php' );
	}
}
