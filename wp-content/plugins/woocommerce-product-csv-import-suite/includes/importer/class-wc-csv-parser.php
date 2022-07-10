<?php
/**
 * WooCommerce CSV Importer class for managing parsing of CSV files.
 */
class WC_CSV_Parser {

	var $row;
	var $post_type;
	var $reserved_fields;		// Fields we map/handle (not custom fields)
	var $post_defaults;			// Default post data
	var $postmeta_defaults;		// default post meta
	var $postmeta_allowed;		// post meta validation
	var $allowed_product_types;	// Allowed product types

	/**
	 * Constructor
	 */
	public function __construct( $post_type = 'product' ) {
		$this->post_type         = $post_type;
		$this->reserved_fields   = include( 'data/data-reserved-fields.php' );
		$this->post_defaults     = include( 'data/data-post-defaults.php' );
		$this->postmeta_defaults = include( 'data/data-postmeta-defaults.php' );
		$this->postmeta_allowed  = include( 'data/data-postmeta-allowed.php' );

		$simple_term 	= get_term_by( 'slug', 'simple', 'product_type' );
		$variable_term 	= get_term_by( 'slug', 'variable', 'product_type' );
		$grouped_term 	= get_term_by( 'slug', 'grouped', 'product_type' );
		$external_term 	= get_term_by( 'slug', 'external', 'product_type' );

		$this->allowed_product_types = array(
			'simple' 	=> $simple_term->term_id,
			'variable'	=> $variable_term->term_id,
			'grouped'	=> $grouped_term->term_id,
			'external'	=> $external_term->term_id
		);

		// Subscription product types
		if ( class_exists( 'WC_Subscriptions' ) ) {
			$subscription_term                                    = get_term_by( 'slug', 'subscription', 'product_type' );
			$variable_subscription_term                           = get_term_by( 'slug', 'variable-subscription', 'product_type' );

			$this->allowed_product_types['subscription']          = $subscription_term->term_id;
			$this->allowed_product_types['variable-subscription'] = $variable_subscription_term->term_id;
		}

		// Composite product type
		if ( class_exists( 'WC_Composite_Products' ) ) {
			$composite_term = get_term_by( 'name', 'composite', 'product_type' );

			if ( $composite_term ) {
				$this->allowed_product_types['composite'] = $composite_term->term_id;
			}
		}

		// Bundle product type
		if ( class_exists( 'WC_Bundles' ) ) {
			$bundle_term = get_term_by( 'name', 'bundle', 'product_type' );

			if ( $bundle_term ) {
				$this->allowed_product_types['bundle'] = $bundle_term->term_id;
			}
		}

		// Booking product types
		if ( class_exists( 'WC_Booking' ) ) {
			$booking_term                           = get_term_by( 'slug', 'booking', 'product_type' );

			if ( $booking_term ) {
				$this->allowed_product_types['booking'] = $booking_term->term_id;
			}
		}

		// Booking product types
		if ( class_exists( 'WC_Accommodation_Booking' ) ) {
			$accommodation_booking_term             = get_term_by( 'slug', 'accommodation-booking', 'product_type' );

			if ( $accommodation_booking_term ) {
				$this->allowed_product_types['accommodation-booking'] = $accommodation_booking_term->term_id;
			}
		}

		// Photography product types
		if ( class_exists( 'WC_Photography' ) ) {
			$photography_term			= get_term_by( 'slug', 'photography', 'product_type' );

			if ( $photography_term ) {
				$this->allowed_product_types['photography'] = $photography_term->term_id;
			}
		}
	}

	/**
	 * Format data from the csv file
	 * @param  string $data
	 * @param  string $enc
	 * @return string
	 */
	public function format_data_from_csv( $data, $enc ) {
		return ( $enc == 'UTF-8' ) ? $data : utf8_encode( $data );
	}

	/**
	 * Parse the data
	 * @param  string  $file      [description]
	 * @param  string  $delimiter [description]
	 * @param  array  $mapping   [description]
	 * @param  integer $start_pos [description]
	 * @param  integer  $end_pos   [description]
	 * @return array
	 */
	public function parse_data( $file, $delimiter, $mapping, $start_pos = 0, $end_pos = null ) {
		// Set locale
		$enc = wc_pcsvis_is_first_row_encoded_in( $file, 'UTF-8, ISO-8859-1' );
		if ( $enc )
			setlocale( LC_ALL, 'en_US.' . $enc );
		@ini_set( 'auto_detect_line_endings', true );

		$parsed_data = array();
		$raw_headers = array();

		// Put all CSV data into an associative array
		if ( ( $handle = fopen( $file, "r" ) ) !== FALSE ) {

			$header   = fgetcsv( $handle, 0, $delimiter );

			if ( $start_pos != 0 ) {
				fseek( $handle, $start_pos );
			}

		    while ( ( $postmeta = fgetcsv( $handle, 0, $delimiter ) ) !== FALSE ) {
	            $row = array();

	            foreach ( $header as $key => $heading ) {

	            	// Heading is the lowercase version of the column name
					$s_heading = strtolower( $heading );

	            	// Check if this heading is being mapped to a different field
            		if ( isset( $mapping[$s_heading] ) ) {
            			if ( $mapping[ $s_heading ] == 'import_as_meta' ) {

            				$s_heading = 'meta:' . $s_heading;

            			} elseif ( $mapping[ $s_heading ] == 'import_as_images' ) {

            				$s_heading = 'images';

            			} else {
            				$s_heading = esc_attr( $mapping[$s_heading] );
            			}
            		}

            		if ( $s_heading == '' )
            			continue;

	            	// Add the heading to the parsed data
					$row[ $s_heading ] = ( isset( $postmeta[$key] ) ) ? $this->format_data_from_csv( $postmeta[$key], $enc ) : '';

	               	// Raw Headers stores the actual column name in the CSV
					$raw_headers[ $s_heading ] = $heading;
	            }

	            $parsed_data[] = $row;

	            unset( $postmeta, $row );

	            $position = ftell( $handle );

	            if ( $end_pos && $position >= $end_pos ) {
	            	break;
				}
		    }
		    fclose( $handle );
		}

		return array( $parsed_data, $raw_headers, $position );
	}

	/**
	 * Parse product
	 * @param  array  $item
	 * @param  integer $merge_empty_cells
	 * @return array
	 */
	public function parse_product( $item, $merge_empty_cells = 0 ) {
		global $WC_CSV_Product_Import, $wpdb;

		$this->row++;

		$terms_array = $postmeta = $product = array();
		$attributes  = $default_attributes = $gpf_data = null;

		// Merging
		$merging = ( ! empty( $_GET['merge'] ) && $_GET['merge'] ) ? true : false;

		// Is variation
		$is_variation = false;
		if ( isset( $_GET['import_page'] ) && 'woocommerce_variation_csv' === $_GET['import_page'] ) {
			$is_variation = true;
		}

		// Post ID field mapping
		$post_id = ( ! empty( $item['id'] ) ) ? $item['id'] : 0;
		$post_id = ( ! empty( $item['post_id'] ) ) ? $item['post_id'] : $post_id;

		if ( $merging ) {

			$product['merging'] = true;
			WC_Product_CSV_Import_Suite::log( sprintf( __( '> Row %s - preparing for merge.', 'woocommerce-product-csv-import-suite' ), $this->row ) );

			// Required fields
			if ( ! $post_id && empty( $item['sku'] ) ) {
				WC_Product_CSV_Import_Suite::log( __( '> > Cannot merge without id or sku. Importing instead.', 'woocommerce-product-csv-import-suite') );
				$merging = false;
			} else {
				// Check product exists
				if ( ! $post_id ) {
					// Check product to merge exists
					$found_product_id = $wpdb->get_var( $wpdb->prepare("
						SELECT $wpdb->posts.ID
					    FROM $wpdb->posts
					    LEFT JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )
					    WHERE $wpdb->posts.post_type = '" . $this->post_type . "'
					    AND $wpdb->posts.post_status IN ( 'publish', 'private', 'draft', 'pending', 'future' )
					    AND $wpdb->postmeta.meta_key = '_sku' AND $wpdb->postmeta.meta_value = '%s'
					 ", $item['sku'] ) );

					if (  $is_variation && ! $found_product_id && empty( $item[ 'post_title' ] ) ) {
						$merging = false;
					} else if ( ! $found_product_id && empty( $item['post_title'] ) ) {
						WC_Product_CSV_Import_Suite::log( sprintf(__( '> > Skipped. Cannot find product with sku %s.', 'woocommerce-product-csv-import-suite'), $item['sku']) );
						return new WP_Error( 'parse-error', __( 'Skipped. Cannot find product with sku.', 'woocommerce-product-csv-import-suite' ) );
					} elseif ( ! $found_product_id ) {
						WC_Product_CSV_Import_Suite::log( sprintf(__( '> > Skipped. Cannot find product with sku %s. Importing instead.', 'woocommerce-product-csv-import-suite'), $item['sku']) );
						$merging = false;
					} else {
						$post_id = $found_product_id;
						WC_Product_CSV_Import_Suite::log( sprintf(__( '> > Found product with ID %s.', 'woocommerce-product-csv-import-suite'), $post_id) );
					}
				}
			}
		}

		if ( ! $merging ) {

			$product['merging'] = false;

			WC_Product_CSV_Import_Suite::log( sprintf( __('> Row %s - preparing for import.', 'woocommerce-product-csv-import-suite'), $this->row ) );

			// Required fields
			if ( $this->post_type == 'product' && empty( $item['post_title'] ) ) {
				WC_Product_CSV_Import_Suite::log( __( '> > Skipped. No post_title set for new product.', 'woocommerce-product-csv-import-suite') );
				return new WP_Error( 'parse-error', __( 'No post_title set for new product.', 'woocommerce-product-csv-import-suite' ) );
			}

		}

		$product['post_id'] = $post_id;

		// Get post fields
		foreach ( $this->post_defaults as $column => $default ) {
			if ( isset( $item[ $column ] ) ) $product[ $column ] = $item[ $column ];
		}

		// Get custom fields
		foreach ( $this->postmeta_defaults as $column => $default ) {
			if ( isset( $item[$column] ) )
				$postmeta[$column] = (string) $item[$column];
			elseif ( isset( $item['_' . $column] ) )
				$postmeta[$column] = (string) $item['_' . $column];

			// Check custom fields are valid
			if ( isset( $postmeta[$column] ) && isset( $this->postmeta_allowed[$column] ) && ! in_array( $postmeta[$column], $this->postmeta_allowed[$column] ) ) {
				$postmeta[$column] = $this->postmeta_defaults[$column];
			}
		}

		if ( ! $merging ) {
			// Merge post meta with defaults
			$product  = wp_parse_args( $product, $this->post_defaults );
			$postmeta = wp_parse_args( $postmeta, $this->postmeta_defaults );
		}

		// Handle special meta fields

		// price
		if ( $merging ) {
			if (
				! isset( $postmeta['regular_price'] ) ||
				( isset( $postmeta['regular_price'] ) && '' === trim( $postmeta['regular_price'] ) && ! $merge_empty_cells )
			) {
				$postmeta['regular_price'] = WC_Product_CSV_Import_Suite::get_meta_data( $post_id, '_regular_price' );
			}

			if (
				! isset( $postmeta['sale_price'] ) ||
				( isset( $postmeta['sale_price'] ) && '' === trim( $postmeta['sale_price'] ) && ! $merge_empty_cells )
			) {
				$postmeta['sale_price'] = WC_Product_CSV_Import_Suite::get_meta_data( $post_id, '_sale_price' );
			}
		}

		// Sale dates product
		if ( array_key_exists( 'sale_price_dates_from', $item ) ) {
			$sale_price_dates_from = ! empty( $item['sale_price_dates_from'] ) ? $item['sale_price_dates_from'] : '';

		// Sale dates variation
		} elseif ( array_key_exists( 'meta:_sale_price_dates_from', $item ) ) {
			$sale_price_dates_from = ! empty( $item['meta:_sale_price_dates_from'] ) ? $item['meta:_sale_price_dates_from'] : '';
		}

		// Sale dates product
		if ( array_key_exists( 'sale_price_dates_to', $item ) ) {
			$sale_price_dates_to = ! empty( $item['sale_price_dates_to'] ) ? $item['sale_price_dates_to'] : '';

		// Sale dates variation
		} elseif ( array_key_exists( 'meta:_sale_price_dates_to', $item ) ) {
			$sale_price_dates_to =  ! empty( $item['meta:_sale_price_dates_to'] ) ? $item['meta:_sale_price_dates_to'] : '';
		}

		// Set price to sale price
		if ( ! empty( $postmeta['regular_price'] ) ) {
			if ( ! empty( $postmeta['sale_price'] ) ) {
				// set sale price
				if (
					( ! empty( $sale_price_dates_from ) && current_time( 'timestamp' ) >= strtotime( $sale_price_dates_from ) ) &&
					( ! empty( $sale_price_dates_to ) && current_time( 'timestamp' ) < strtotime( $sale_price_dates_to ) ) ) {
						$price = min( $postmeta['sale_price'], $postmeta['regular_price']);
						$postmeta['price'] = $price;
				} elseif (
					// sale schedule hasn't started yet set regular price
					! empty( $sale_price_dates_from ) && current_time( 'timestamp' ) < strtotime( $sale_price_dates_from ) ||
					// scheduled sale time past set
					! empty( $sale_price_dates_from ) && current_time( 'timestamp' ) > strtotime( $sale_price_dates_to ) ) {
							$postmeta['price'] = $postmeta['regular_price'];
				} else {
					$postmeta['price'] = $postmeta['sale_price'];
				}
			} else {
				$postmeta['price'] = $postmeta['regular_price'];
			}
		} elseif ( '0' === $postmeta['regular_price'] ) {
			$postmeta['price'] = 0;
		} else {
			$postmeta['price'] = '';
		}

		// Format prices
		if ( isset( $postmeta['regular_price'] ) ) {
			$postmeta['regular_price'] = wc_format_decimal( $postmeta['regular_price'] );
		}
		if ( isset( $postmeta['sale_price'] ) ) {
			$postmeta['sale_price'] = wc_format_decimal( $postmeta['sale_price'] );
		}
		if ( isset( $postmeta['price'] ) ) {
			$postmeta['price'] = wc_format_decimal( $postmeta['price'] );
		}

		// Reset dynamically generated meta
		$postmeta['min_variation_price'] = $postmeta['max_variation_price']	= $postmeta['min_variation_regular_price'] = $postmeta['max_variation_regular_price'] = $postmeta['min_variation_sale_price'] = $postmeta['max_variation_sale_price'] = '';

		// upsells
		if ( isset( $postmeta['upsell_ids'] ) && ! is_array( $postmeta['upsell_ids'] ) ) {
			$ids = array_filter( array_map( 'trim', explode( '|', $postmeta['upsell_ids'] ) ) );
			$postmeta['upsell_ids'] = $ids;
		}

		// crosssells
		if ( isset( $postmeta['crosssell_ids'] ) && ! is_array( $postmeta['crosssell_ids'] ) ) {
			$ids = array_filter( array_map( 'trim', explode( '|', $postmeta['crosssell_ids'] ) ) );
			$postmeta['crosssell_ids'] = $ids;
		}

		// variation description
		if ( isset( $postmeta['variation_description'] ) ) {
			$postmeta['variation_description'] = wp_kses_post( $postmeta['variation_description'] );
		}

		// Relative stock updates
		if ( $merging ) {
			if ( isset( $postmeta['stock'] ) ) {

				$postmeta['stock'] = trim( $postmeta['stock'] );

				$mode = substr( $postmeta['stock'], 0, 3 );

				if ( $mode == '(+)' ) {
					$old_stock 	= (int) WC_Product_CSV_Import_Suite::get_meta_data( $post_id, '_stock' );
					$amount 	= absint( substr( $postmeta['stock'], 3 ) );
					$new_stock 	= $old_stock + $amount;
					$postmeta['stock'] = $new_stock;
				}

				if ( $mode == '(-)' ) {
					$old_stock 	= (int) WC_Product_CSV_Import_Suite::get_meta_data( $post_id, '_stock' );
					$amount 	= absint( substr( $postmeta['stock'], 3 ) );
					$new_stock 	= $old_stock - $amount;
					$postmeta['stock'] = $new_stock;
				}
			}
		}

		// Format post status
		if ( ! empty( $product['post_status'] ) ) {
			$product['post_status'] = strtolower( $product['post_status'] );

			if ( 'product' === $this->post_type ) {
				if ( ! in_array( $product['post_status'], array( 'publish', 'private', 'draft', 'pending', 'future', 'inherit', 'trash' ) ) ) {
					$product['post_status'] = 'publish';
				}
			} else {
				if ( ! in_array( $product['post_status'], array( 'private', 'publish' ) ) ) {
					$product['post_status'] = 'publish';
				}
			}
		}

		// Put set core product postmeta into product array
		foreach ( $postmeta as $key => $value ) {
			$product['postmeta'][] = array( 'key' 	=> '_' . esc_attr($key), 'value' => $value );
		}

		/**
		 * Handle other columns
		 */
		foreach ( $item as $key => $value ) {

			if ( $this->post_type == 'product' && ! $merge_empty_cells && $value == "" )
				continue;

			/**
			 * File path handling
			 */
			if ( $key == 'file_paths' || $key == 'downloadable_files' ) {

				$file_paths  = explode( '|', $value );
				$_file_paths = array();
				foreach ( $file_paths as $file_path ) {
					// 2.1
					if ( function_exists( 'wc_get_filename_from_url' ) ) {
						$file_path = array_map( 'trim', explode( '::', $file_path ) );
						if ( sizeof( $file_path ) === 2 ) {
							$file_name = $file_path[0];
							$file_path = $file_path[1];
						} else {
							$file_name = wc_get_filename_from_url( $file_path[0] );
							$file_path = $file_path[0];
						}
						$_file_paths[ md5( $file_path ) ] = array(
							'name' => $file_name,
							'file' => $file_path
						);
					} else {
						$file_path = trim( $file_path );
						$_file_paths[ md5( $file_path ) ] = $file_path;
					}
				}
				$value = $_file_paths;

				$product['postmeta'][] = array( 'key' 	=> '_' . esc_attr( $key ), 'value' => $value );
			}

			/**
			 * Handle meta: columns for variation attributes
			 */
			elseif ( strstr( $key, 'meta:attribute_pa_' ) ) {

				// Get meta key name
				$meta_key = ( isset( $WC_CSV_Product_Import->raw_headers[$key] ) ) ? $WC_CSV_Product_Import->raw_headers[$key] : $key;
				$meta_key = trim( str_replace( 'meta:', '', $meta_key ) );

				// Convert to slug
				$value = sanitize_title( $value );

				// Add to postmeta array
				$product['postmeta'][] = array(
					'key' 	=> esc_attr( $meta_key ),
					'value' => $value
				);

			}

			/**
			 * Handle meta: columns - import as custom fields
			 */
			elseif ( strstr( $key, 'meta:' ) ) {

				// Get meta key name
				$meta_key = ( isset( $WC_CSV_Product_Import->raw_headers[$key] ) ) ? $WC_CSV_Product_Import->raw_headers[$key] : $key;
				$meta_key = trim( str_replace( 'meta:', '', $meta_key ) );

				// Decode JSON
				$json = json_decode( $value, true );

				if ( is_array( $json ) || is_object( $json ) )
					$value = (array) $json;

				// Add to postmeta array
				$product['postmeta'][] = array(
					'key' 	=> esc_attr( $meta_key ),
					'value' => $value
				);
			}

			/**
			 * Handle meta: columns - import as custom fields
			 */
			elseif ( strstr( $key, 'tax:' ) ) {

				// Get taxonomy
				$taxonomy = trim( str_replace( 'tax:', '', $key ) );

				// Exists?
				if ( ! taxonomy_exists( $taxonomy ) ) {
					WC_Product_CSV_Import_Suite::log( sprintf( __('> > Skipping taxonomy "%s" - it does not exist.', 'woocommerce-product-csv-import-suite'), $taxonomy ) );
					continue;
				}

				// Product type check
				if ( 'product_type' === $taxonomy ) {
					$term = strtolower( $value );

					if ( ! array_key_exists( $term, $this->allowed_product_types ) ) {
						WC_Product_CSV_Import_Suite::log( sprintf( __('> > > Product type "%s" not allowed - using simple.', 'woocommerce-product-csv-import-suite'), $term ) );
						$term_id = $this->allowed_product_types['simple'];
					} else {
						$term_id = $this->allowed_product_types[ $term ];
					}

					// Add to array
					$terms_array[] = array(
						'taxonomy' 	=> $taxonomy,
						'terms'		=> array( $term_id )
					);

					continue;
				}

				// Get terms - ID => parent
				$terms 			= array();
				$raw_terms 		= explode( '|', $value );
				$raw_terms 		= array_map( 'trim', $raw_terms );

				// Handle term hierachy (>)
				foreach ( $raw_terms as $raw_term ) {

					if ( strstr( $raw_term, '>' ) ) {

						$raw_term = explode( '>', $raw_term );
						$raw_term = array_map( 'trim', $raw_term );
						$raw_term = array_map( 'esc_html', $raw_term );
						$raw_term = array_filter( $raw_term );

						$parent = 0;
						$loop   = 0;

						foreach ( $raw_term as $term ) {
							$loop ++;
							$term_id = '';

							if ( isset( $this->inserted_terms[ $taxonomy ][ $parent ][ $term ] ) ) {
								$term_id = $this->inserted_terms[ $taxonomy ][ $parent ][ $term ];
							} elseif ( $term ) {

								/**
								 * Check term existance
								 */
								$term_may_exist = term_exists( $term, $taxonomy, absint( $parent ) );

								WC_Product_CSV_Import_Suite::log( sprintf( __( '> > (' . __LINE__ . ') Term %s (%s) exists? %s', 'woocommerce-product-csv-import-suite' ), sanitize_text_field( $term ), esc_html( $taxonomy ), $term_may_exist ? print_r( $term_may_exist, true ) : '-' ) );

								if ( is_array( $term_may_exist ) ) {

									$possible_term = get_term( $term_may_exist['term_id'], $taxonomy );

									if ( $possible_term->parent == $parent ) {
										$term_id = $term_may_exist['term_id'];
									}
								}

								if ( ! $term_id ) {
									// Use last term from hierarchy to create slug.
									$slug = end( $raw_term );

									$slug = is_array( $slug ) ? sanitize_title( implode( '-', $slug ) ) : sanitize_title( $slug );

									$t = wp_insert_term( $term, $taxonomy, array( 'parent' => $parent, 'slug' => $slug ) );

									if ( ! is_wp_error( $t ) ) {
										$term_id = $t['term_id'];
									} else {
										WC_Product_CSV_Import_Suite::log( sprintf( __( '> > (' . __LINE__ . ') Failed to import term %s, parent %s - %s', 'woocommerce-product-csv-import-suite' ), sanitize_text_field( $term ), sanitize_text_field( $parent ), sanitize_text_field( $taxonomy ) ) );
										break;
									}
								}

								$this->inserted_terms[ $taxonomy ][$parent][$term] = $term_id;

							}

							if ( ! $term_id )
								break;

							// Add to product terms, ready to set if this is the final term
							if ( sizeof( $raw_term ) == $loop )
								$terms[] = $term_id;

							$parent = $term_id;
						}

					} else {

						$term_id  = '';
						$raw_term = esc_html( $raw_term );

						if ( isset( $this->inserted_terms[ $taxonomy ][0][$raw_term] ) ) {

							$term_id = $this->inserted_terms[ $taxonomy ][0][$raw_term];

						} elseif ( $raw_term ) {

							// Check term existance
							$term_exists 	= term_exists( $raw_term, $taxonomy, 0 );
							$term_id 		= is_array( $term_exists ) ? $term_exists['term_id'] : 0;

							if ( ! $term_id ) {
								$t = wp_insert_term( trim( $raw_term ), $taxonomy, array( 'parent' => 0 ) );

								if ( ! is_wp_error( $t ) ) {
									$term_id = $t['term_id'];
								} else {
									WC_Product_CSV_Import_Suite::log( sprintf( __( '> > Failed to import term %s %s', 'woocommerce-product-csv-import-suite' ), esc_html($raw_term), esc_html($taxonomy) ) );
									break;
								}
							}

							$this->inserted_terms[ $taxonomy ][0][$raw_term] = $term_id;

						}

						// Store terms for later insertion
						if ( $term_id )
							$terms[] = $term_id;

					}

				}

				// Any defined?
				if ( sizeof( $terms ) == 0 )
					continue;

				// Add to array
				$terms_array[] = array(
					'taxonomy' 	=> $taxonomy,
					'terms'		=> $terms
				);
			}

			/**
			 * Handle Attributes
			 */
			elseif ( strstr( $key, 'attribute:' ) ) {

				$attribute_key 	= sanitize_title( trim( str_replace( 'attribute:', '', $key ) ) );
				$attribute_name = str_replace( 'attribute:', '', $WC_CSV_Product_Import->raw_headers[ $key ] );

				if ( ! $attribute_key ) {
					continue;
				}

				// Taxonomy
				if ( substr( $attribute_key, 0, 3 ) == 'pa_' ) {

					$taxonomy = $attribute_key;

					// Exists?
					if ( ! taxonomy_exists( $taxonomy ) ) {

						$nicename = strtolower( sanitize_title( str_replace( 'pa_', '', $taxonomy ) ) );

						WC_Product_CSV_Import_Suite::log( sprintf( __('> > Attribute taxonomy "%s" does not exist. Adding it. Nicename: %s', 'woocommerce-product-csv-import-suite'), $taxonomy, $nicename ) );

						$exists_in_db = $wpdb->get_var( "SELECT attribute_id FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '" . $nicename . "';" );

						if ( ! $exists_in_db ) {
							// Create the taxonomy
							$wpdb->insert( $wpdb->prefix . "woocommerce_attribute_taxonomies", array( 'attribute_name' => $nicename, 'attribute_label' => $nicename, 'attribute_type' => 'select', 'attribute_orderby' => 'menu_order' ) );
						} else {
							WC_Product_CSV_Import_Suite::log( sprintf( __('> > Attribute taxonomy %s already exists in DB.', 'woocommerce-product-csv-import-suite'), $taxonomy ) );
						}

						// Register the taxonomy now so that the import works!
						register_taxonomy( $taxonomy,
					        array( 'product', 'product_variation' ),
					        array(
								'hierarchical' => true,
								'show_ui'      => false,
								'query_var'    => true,
								'rewrite'      => false,
					        )
					    );
					}

					// Get terms
					$terms     = array();
					$raw_terms = explode( '|', $value );
					$raw_terms = array_map( 'esc_html', $raw_terms );
					$raw_terms = array_map( 'trim', $raw_terms );

					if ( sizeof( $raw_terms ) > 0 ) {

						foreach ( $raw_terms as $raw_term ) {

							if ( empty( $raw_term ) && 0 != $raw_term ) {
								continue;
							}

							// Check term existance
							$term_exists 	= term_exists( $raw_term, $taxonomy, 0 );
							$term_id 		= is_array( $term_exists ) ? $term_exists['term_id'] : 0;

							if ( ! $term_id ) {
								$t = wp_insert_term( trim( $raw_term ), $taxonomy );

								if ( ! is_wp_error( $t ) ) {
									$term_id = $t['term_id'];

									WC_Product_CSV_Import_Suite::log( sprintf( __( '> > Inserted Raw Term %s ID = %s', 'woocommerce-product-csv-import-suite' ), esc_html( $raw_term ), $term_id ) );
								} else {
									WC_Product_CSV_Import_Suite::log( sprintf( __( '> > Failed to import term %s %s', 'woocommerce-product-csv-import-suite' ), esc_html($raw_term), esc_html($taxonomy) ) );
									break;
								}
							} else {
								WC_Product_CSV_Import_Suite::log( sprintf( __( '> > Raw Term %s ID = %s', 'woocommerce-product-csv-import-suite' ), esc_html( $raw_term ), $term_id ) );
							}

							if ( $term_id ) {
								$terms[] = $term_id;
							}
						}

					}

					// Add to array
					$terms_array[] = array(
						'taxonomy' 	=> $taxonomy,
						'terms'		=> $terms
					);

					// Ensure we have original attributes
					if ( is_null( $attributes ) && $merging ) {
						$attributes = array_filter( (array) WC_Product_CSV_Import_Suite::get_meta_data( $post_id, '_product_attributes' ) );
					} elseif ( is_null( $attributes ) ) {
						$attributes = array();
					}

					// Set attribute
					if ( ! isset( $attributes[ $taxonomy ] ) ) {
						$attributes[ $taxonomy ] = array();
					}

					$attributes[ $taxonomy ]['name']        = $taxonomy;
					$attributes[ $taxonomy ]['value']       = null;
					$attributes[ $taxonomy ]['is_taxonomy'] = 1;

					if ( ! isset( $attributes[ $taxonomy ]['position'] ) ) {
						$attributes[ $taxonomy ]['position'] = 0;
					}
					if ( ! isset( $attributes[ $taxonomy ]['is_visible'] ) ) {
						$attributes[ $taxonomy ]['is_visible'] = 1;
					}
					if ( ! isset( $attributes[ $taxonomy ]['is_variation'] ) ) {
						$attributes[ $taxonomy ]['is_variation'] = 0;
					}

				} else {

					if ( ! $attribute_key ) {
						continue;
					}

					// Set attribute
					if ( ! isset( $attributes[ $attribute_key ] ) ) {
						$attributes[ $attribute_key ] = array();
					}

					$attributes[ $attribute_key ]['name']        = $attribute_name;
					$attributes[ $attribute_key ]['value']       = $value;
					$attributes[ $attribute_key ]['is_taxonomy'] = 0;

					if ( ! isset( $attributes[ $attribute_key ]['position'] ) ) {
						$attributes[ $attribute_key ]['position'] = 0;
					}
					if ( ! isset( $attributes[ $attribute_key ]['is_visible'] ) ) {
						$attributes[ $attribute_key ]['is_visible'] = 1;
					}
					if ( ! isset( $attributes[ $attribute_key ]['is_variation'] ) ) {
						$attributes[ $attribute_key ]['is_variation'] = 0;
					}
				}

			}

			/**
			 * Handle Attributes Data - position|is_visible|is_variation
			 */
			elseif ( strstr( $key, 'attribute_data:' ) ) {

				$attribute_key = sanitize_title( trim( str_replace( 'attribute_data:', '', $key ) ) );

				if ( ! $attribute_key ) {
					continue;
				}

				$values 	= explode( '|', $value );
				$position 	= isset( $values[0] ) ? (int) $values[0] : 0;
				$visible 	= isset( $values[1] ) ? (int) $values[1] : 1;
				$variation 	= isset( $values[2] ) ? (int) $values[2] : 0;

				// Ensure we have original attributes
				if ( ! isset( $attributes[ $attribute_key ] ) ) {
					if ( $merging ) {
						$existing_attributes = array_filter( (array) WC_Product_CSV_Import_Suite::get_meta_data( $post_id, '_product_attributes' ) );
						$attributes[ $attribute_key ] = isset( $existing_attributes[ $attribute_key ] ) ? $existing_attributes[ $attribute_key ] : array();
					} else {
						$attributes[ $attribute_key ] = array();
					}
				}

				$attributes[ $attribute_key ]['position']     = $position;
				$attributes[ $attribute_key ]['is_visible']   = $visible;
				$attributes[ $attribute_key ]['is_variation'] = $variation;
			}

			/**
			 * Handle Attributes Default Values
			 */
			elseif ( strstr( $key, 'attribute_default:' ) ) {

				$attribute_key = sanitize_title( trim( str_replace( 'attribute_default:', '', $key ) ) );

				if ( ! $attribute_key ) continue;

				// Ensure we have original attributes
				if ( is_null( $default_attributes ) && $merging ) {
					$default_attributes = array_filter( (array) WC_Product_CSV_Import_Suite::get_meta_data( $post_id, '_default_attributes' ) );
				} elseif ( is_null( $default_attributes ) ) {
					$default_attributes = array();
				}

				$default_attributes[ $attribute_key ] = $value;
			}

			/**
			 * Handle gpf: google product feed columns
			 */
			elseif ( strstr( $key, 'gpf:' ) ) {

				$gpf_key = trim( str_replace( 'gpf:', '', $key ) );

				// Get original values
				if ( is_null( $gpf_data ) && $merging ) {
					$gpf_data = array_filter( (array) WC_Product_CSV_Import_Suite::get_meta_data( $post_id, '_woocommerce_gpf_data' ) );
				} elseif ( is_null( $gpf_data ) ) {
					$gpf_data = array(
						'availability'            => '',
						'condition'               => '',
						'brand'                   => '',
						'product_type'            => '',
						'google_product_category' => '',
						'bing_category'           => '',
						'gtin'                    => '',
						'mpn'                     => '',
						'gender'                  => '',
						'age_group'               => '',
						'color'                   => '',
						'size'                    => '',
						'size_type'               => '',
						'size_system'             => '',
						'material'                => '',
						'pattern'                 => '',
						'delivery_label'          => '',
						'size'                    => '',
						'custom_label_0'          => '',
						'custom_label_1'          => '',
						'custom_label_2'          => '',
						'custom_label_3'          => '',
						'custom_label_4'          => '',
						'promotion_id'            => '',
					);
				}

				$gpf_data[$gpf_key] = $value;

			}

			/**
			 * Handle parent_sku column for variations
			 */
			elseif ( strstr( $key, 'parent_sku' ) ) {
				if ( $value ) {
					$possible_parent_ids = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value = %s", $value ) );

					if ( $possible_parent_ids ) {
						foreach ( $possible_parent_ids as $possible_parent_id ) {
							if ( 'product' === get_post_type( $possible_parent_id ) && in_array( get_post_status( $possible_parent_id ), array( 'publish', 'private', 'draft' ) ) ) {
								$product['post_parent'] = $possible_parent_id;
								break;
							}
						}
					}
				}
			}

			/**
			 * Handle upsell SKUs which we cannot assign until we get IDs later on
			 */
			elseif ( strstr( $key, 'upsell_skus' ) ) {
				if ( $value ) {
					$skus                   = array_filter( array_map( 'trim', explode( '|', $value ) ) );
					$product['upsell_skus'] = $skus;
				}
			}

			/**
			 * Handle crosssells SKUs which we cannot assign until we get IDs later on
			 */
			elseif ( strstr( $key, 'crosssell_skus' ) ) {
				if ( $value ) {
					$skus                      = array_filter( array_map( 'trim', explode( '|', $value ) ) );
					$product['crosssell_skus'] = $skus;
				}
			}

		}

		// Remove empty attribues
		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $key => $value ) {
				if ( ! isset( $value['name'] ) ) unset( $attributes[$key] );
			}
		}

		/**
		 * Handle images
		 */
		if ( ! empty( $item['images'] ) ) {
			$images = array_map( 'trim', explode( '|', $item['images'] ) );
		} else {
			$images = '';
		}

		$product['postmeta'][] = array( 'key' 	=> '_default_attributes', 'value' => $default_attributes );
		$product['attributes'] = $attributes;
		$product['gpf_data']   = $gpf_data;
		$product['images']     = $images;
		$product['terms']      = $terms_array;
		$product['sku']        = ( ! empty( $item['sku'] ) ) ? $item['sku'] : '';
		$product['post_title'] = ( ! empty( $item['post_title'] ) ) ? $item['post_title'] : '';

		unset( $item, $terms_array, $postmeta, $attributes, $gpf_data, $images );

		return $product;
	}
}
