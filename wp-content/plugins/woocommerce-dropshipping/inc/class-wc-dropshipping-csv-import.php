<?php
class WC_Dropshipping_CSV_Import {
	public function __construct() {
		$this->init();
	}

	public function init() {
		add_action( 'wp_ajax_get_CSV_upload_form', array( $this, 'CSV_upload_form' ) );
		add_action( 'wc_dropship_manager_parse_csv', array( $this, 'admin_save_inventory_status' ) );
		add_action( 'wc_dropship_manager_out_of_stock', array( $this, 'display_out_of_stock' ), 10, 2 );
		add_action( 'wc_dropship_manager_in_stock', array( $this, 'display_in_stock' ), 10, 2 );
		add_filter( 'woocommerce_product_export_column_names', array( $this, 'woo_dropship_add_columns' ) );
		add_filter( 'woocommerce_product_export_product_default_columns', array( $this, 'woo_dropship_add_columns' ) );
		add_filter( 'woocommerce_product_export_product_column_dropship_supplier_id', array( $this, 'woo_dropship_export_taxonomy_id' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_dropship_supplier_name', array( $this, 'woo_dropship_export_taxonomy_name' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_dropship_supplier_slug', array( $this, 'woo_dropship_export_taxonomy_slug' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_dropship_supplier_description', array( $this, 'woo_dropship_export_taxonomy_description' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_dropship_supplier_email', array( $this, 'woo_dropship_export_taxonomy_email' ), 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_dropship_supplier_account_number', array( $this, 'woo_dropship_export_taxonomy_account_number' ), 10, 2 );
		add_filter( 'woocommerce_csv_product_import_mapping_options', array( $this, 'woo_dropship_map_columns' ) );
		add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array( $this, 'woo_dropship_add_columns_to_mapping_screen' ) );
		add_filter( 'woocommerce_product_importer_parsed_data', array( $this, 'woo_dropship_parse_taxonomy_json_supplier_name' ), 10, 2 );
		add_filter( 'woocommerce_product_importer_parsed_data', array( $this, 'woo_dropship_parse_taxonomy_json_supplier_slug' ), 10, 2 );
		add_filter( 'woocommerce_product_importer_parsed_data', array( $this, 'woo_dropship_parse_taxonomy_json_supplier_description' ), 10, 2 );
		add_filter( 'woocommerce_product_importer_parsed_data', array( $this, 'woo_dropship_parse_taxonomy_json_supplier_id' ), 10, 2 );
		add_filter( 'woocommerce_product_importer_parsed_data', array( $this, 'woo_dropship_parse_taxonomy_json_supplier_email' ), 10, 2 );
		add_filter( 'woocommerce_product_importer_parsed_data', array( $this, 'woo_dropship_parse_taxonomy_json_supplier_account_number' ), 10, 2 );
		add_filter( 'woocommerce_product_import_inserted_product_object', array( $this, 'woo_dropship_set_taxonomy' ), 10, 2 );
		add_filter( 'woocommerce_product_importer_before_set_parsed_data', array( $this, 'woo_dropship_set_taxonomy_update' ), 10, 2 );
		add_filter( 'woocommerce_admin_reports', array( $this, 'woo_dropship_supplier_report' ), 10, 1 );
	}

	/* CSV Inventory */
	public function CSV_upload_form() {
		$term_id = $_GET['term_id'];
		$ds = wc_dropshipping_get_dropship_supplier( intval( $term_id ) );
		// Inventory Upload window
		echo '<div id="CSVwindow" >
			<form class="csvupload_form" action="' . admin_url( 'admin-ajax.php' ) . '" method="post" enctype="multipart/form-data" target="csvupload_iframe-' . $ds['slug'] . '" >';

		echo '<input type="hidden" name="action" value="CSV_upload_form" />';
		// wp_nonce_field( 'editedtag');
		wp_nonce_field( 'CSV_upload_form' );

		echo '<p>If your supplier provides a spreadsheet in .CSV format indicating their inventory levels (Quantity on Hand) or whether or not their products are in stock (In-Stock Indicator) you can import the .CSV file here to update your inventory status.Before uploading a .CSV file, please configure which columns to use on the spreadsheet by mousing over the supplier&apos;s name and select "Edit"</p>';

		echo '<table>
			<tr>
				<th>CSV File Location:</th>
			</tr>
			<tr>
				<td>';
				echo '	<input type="hidden" id="csv_upload_term_id" name="term_id" value="' . htmlspecialchars($term_id, ENT_QUOTES, 'UTF-8') . '" />
    					<input type="file" name="csv_file" value="" />';

				echo '<input type="hidden" value="' . $ds['slug'] . '" name="slug">';
				echo '<input type="hidden" value="dropship_supplier" name="taxonomy">';
				echo '<input type="hidden" value="product" name="post_type">
						</td>
						<td><input class="button-primary csvupload_submit_btn" type="submit" name="submit" value="Update" />
						</td>
					</tr>
				</table>
				</form>';

				echo '<iframe style="display:none;width: 100%;height: 340px" id="csvupload_iframe-' . $ds['slug'] . '" class="csv_upload_iframe" name="csvupload_iframe-' . $ds['slug'] . '" src="#" ></iframe>
				</div>';
		wp_die();
	}

	// parses a supplier inventory csv and updates the SKUS
	public function admin_save_inventory_status() {
		global $wpdb;
		ini_set( 'auto_detect_line_endings', '1' );
		// echo "<script>alert($true_auto_detect_line_endings)</script>";

		$ds = wc_dropshipping_get_dropship_supplier( intval( $_POST['term_id'] ) ); // get the supplier data
		$options = get_option( 'wc_dropship_manager' );
		$instock = '';
		$outofstock = '';
		$supplier_info = '';
		$temp = array();

		$q_select_skus = ' 	CREATE TEMPORARY TABLE %name AS (
								SELECT m.post_id
								FROM ' . $wpdb->postmeta . ' m
								INNER JOIN ' . $wpdb->term_relationships . ' tr ON ( m.post_id = tr.object_id )
								INNER JOIN ' . $wpdb->term_taxonomy . " tt ON ( tr.term_taxonomy_id = tt.term_taxonomy_id )
								AND tt.taxonomy = 'dropship_supplier'
								AND tt.term_id = " . $ds['id'] . "
								WHERE m.meta_key = '_sku'
								AND m.meta_value IN (%s)
                            );";

		$q_update_stockstatus = ' UPDATE ' . $wpdb->postmeta . "
									SET meta_value = %s
									WHERE meta_key = '_stock_status'
									AND post_id IN (
									 SELECT post_id AS id
									 FROM %name );";

		$q_update_visibilitystatus = ' 	UPDATE ' . $wpdb->postmeta . "
                                        SET meta_value = %s
                                        WHERE meta_key = '_visibility'
                                        AND post_id IN (
                                         SELECT post_id AS id
                                         FROM %name );";

		/*
		$q_update_quantitystatus = "  UPDATE ".$wpdb->postmeta."
										SET meta_value = %s
										WHERE meta_key = '_stock'
										AND post_id IN (
										 SELECT post_id AS id
										 FROM %name );";*/

		// process uploaded CSV
		if ( ( $_FILES['csv_file']['error'] == 0 ) && ( strlen( $ds['csv_delimiter'] ) > 0 ) ) {
			$name = $_FILES['csv_file']['name'];
			$ext_file = explode( '.', $_FILES['csv_file']['name'] );
			$ext = strtolower( end( $ext_file ) );
			$type = $_FILES['csv_file']['type'];
			$tmpName = $_FILES['csv_file']['tmp_name'];
			// check the file is a csv
			if ( $ext === 'csv' ) {
				if ( ( $handle = fopen( $tmpName, 'r' ) ) !== false ) {
					// necessary if a large csv file
					set_time_limit( 0 );
					// loop over CSV
					while ( ( $data = fgetcsv( $handle, 1000, $ds['csv_delimiter'] ) ) !== false ) {
						$temp = array();
						// get the values from the csv
						$temp['sku'] = $data[ $ds['csv_column_sku'] - 1 ];
						if ( $ds['csv_type'] === 'quantity' ) {
							$temp['qty_remaining'] = preg_replace( '/[^0-9]/', '', $data[ $ds['csv_column_qty'] - 1 ] );
							// get rid of anything that is not a number.
							$qty_remaining = $temp['qty_remaining'];
							// All we care about is if there is enough product in the warehouse to ship orders
							if ( trim( $temp['qty_remaining'] ) < $options['inventory_pad'] ) {
								// if the product has less than "inventory_pad" remaining then its out of stock
								$outofstock .= "'$temp[sku]',";
								$sku = $temp['sku'];
								$product_id = wc_get_product_id_by_sku( $sku );
								update_post_meta( $product_id, '_stock', $qty_remaining );
							} else {
								// product is active

								$instock .= "'$temp[sku]',";
								$sku = $temp['sku'];
								$product_id = wc_get_product_id_by_sku( $sku );
								update_post_meta( $product_id, '_stock', $qty_remaining );

							}
						} elseif ( $ds['csv_type'] === 'indicator' ) {
							if ( strcasecmp( trim( $data[ $ds['csv_column_indicator'] - 1 ] ), $ds['csv_indicator_instock'] ) != 0 ) {
								// if the field does not equal the "in-stock" indicator then its out of stock
								$outofstock .= "'$temp[sku]',";
							} else {
								// product is active
								$instock .= "'$temp[sku]',";
							}
						}
						unset( $temp );
					}
					fclose( $handle );
					// add empty data on the end so SQL doesnt get mad about the extra comma
					$outofstock .= "''";
					$instock .= "''";
					define( 'DIEONDBERROR', true );
					$wpdb->show_errors();
					// update all out of stock skus
					// create temp table
					if ( strlen( $outofstock ) > 0 ) {
						$sql = str_replace( '%name', 'outofstock_skus', $q_select_skus );
						$sql = str_replace( '%s', $outofstock, $sql );
						$wpdb->query( $sql );
						// use temp table to update stock status on all skus that are oos
						$sql = str_replace( '%name', 'outofstock_skus', $q_update_stockstatus );
						$sql = $wpdb->prepare( $sql, array( 'outofstock' ) );
						$wpdb->query( $sql );
						// use temp table to update visibility on all skus that are oos
						$sql = str_replace( '%name', 'outofstock_skus', $q_update_visibilitystatus );
						$sql = $wpdb->prepare( $sql, array( 'hidden' ) );
						$wpdb->query( $sql );
					}
					if ( strlen( $instock ) > 0 ) {
						// update all now instock skus
						$sql = str_replace( '%name', 'instock_skus', $q_select_skus );
						$sql = str_replace( '%s', $instock, $sql );
						$wpdb->query( $sql );
						$sql = str_replace( '%name', 'instock_skus', $q_update_stockstatus );
						$sql = $wpdb->prepare( $sql, array( 'instock' ) );
						$wpdb->query( $sql );
						// use temp table to update visibility on all skus that are in stock
						$sql = str_replace( '%name', 'instock_skus', $q_update_visibilitystatus );
						$sql = $wpdb->prepare( $sql, array( 'visible' ) );
						$wpdb->query( $sql );
					}
					do_action( 'wc_dropship_manager_out_of_stock', $outofstock, $supplier_info );
					do_action( 'wc_dropship_manager_in_stock', $instock, $supplier_info );
					// $wpdb->print_error();
				}
			}
		} else {
			echo '<p>There was an error processing the .CSV file.  If this error persists, please contact OPMC support.</p>';
		}
		// announce that we've finished
		do_action( 'wc_dropship_manager_inventory_status_update_completed' );
	}

	// TODO: format this output
	public function display_out_of_stock( $outofstock, $supplier_info ) {
		$aSkus = explode( ',', $outofstock );
		$new_skus = array();
		foreach ( $aSkus as $sku ) {
			if ( 2 === strlen( $sku ) ) {
				break;
			} else {
				$new_skus[] = $sku;
			}
		}
		echo '<div style="float:left;"><b>OUT OF STOCK: ' . count( $new_skus ) . '</b>';
		echo '<ul>';
		foreach ( $new_skus as $sku ) {
			if ( 2 === strlen( $sku ) ) {
				break;
			}
			echo '<li>' . $sku . '</li>';
		}
		echo '</ul></div>';
	}

	public function display_in_stock( $instock, $supplier_info ) {
		$aSkus = explode( ',', $instock );
		$new_skus = array();
		foreach ( $aSkus as $sku ) {
			if ( 2 === strlen( $sku ) ) {
				break;
			} else {
				$new_skus[] = $sku;
			}
		}
		echo '<div style="float:right;"><b>IN STOCK: ' . count( $new_skus ) . '</b>';
		echo '<ul>';
		foreach ( $new_skus as $sku ) {
			echo '<li>' . $sku . '</li>';
		}
		echo '</ul></div>';
		echo '<br style="clear:both" />';
	}

	/**
	 * Add CSV columns for exporting extra data.
	 *
	 * @param  array $columns
	 * @return array  $columns
	 */
	public function woo_dropship_add_columns( $columns ) {
		$columns['dropship_supplier_id'] = __( 'Supplier Id', 'your-text-domain' );
		$columns['dropship_supplier_name'] = __( 'Supplier Name', 'your-text-domain' );
		$columns['dropship_supplier_slug'] = __( 'Supplier Slug', 'your-text-domain' );
		$columns['dropship_supplier_description'] = __( 'Supplier Description', 'your-text-domain' );
		$columns['dropship_supplier_email'] = __( 'Supplier Email', 'your-text-domain' );
		$columns['dropship_supplier_account_number'] = __( 'Supplier Account Number', 'your-text-domain' );
		return $columns;
	}

	public function woo_dropship_export_taxonomy_id( $value, $product ) {

		$terms = get_terms(
			array(
				'object_ids' => $product->get_ID(),
				'taxonomy' => 'dropship_supplier',
			)
		);

		if ( ! is_wp_error( $terms ) ) {

			$data = array();

			foreach ( (array) $terms as $term ) {
				$data[] = $term->term_id;
			}

			$value = json_encode( $data );

		}

		return $value;
	}

	public function woo_dropship_export_taxonomy_name( $value, $product ) {

		$terms = get_terms(
			array(
				'object_ids' => $product->get_ID(),
				'taxonomy' => 'dropship_supplier',
			)
		);

		if ( ! is_wp_error( $terms ) ) {

			$data = array();

			foreach ( (array) $terms as $term ) {
				$data[] = $term->name;
			}

			$value = json_encode( $data );

		}

		return $value;
	}

	public function woo_dropship_export_taxonomy_slug( $value, $product ) {

		$terms = get_terms(
			array(
				'object_ids' => $product->get_ID(),
				'taxonomy' => 'dropship_supplier',
			)
		);

		if ( ! is_wp_error( $terms ) ) {

			$data = array();

			foreach ( (array) $terms as $term ) {
				$data[] = $term->slug;
			}

			$value = json_encode( $data );

		}

		return $value;
	}

	public function woo_dropship_export_taxonomy_description( $value, $product ) {

		$terms = get_terms(
			array(
				'object_ids' => $product->get_ID(),
				'taxonomy' => 'dropship_supplier',
			)
		);

		if ( ! is_wp_error( $terms ) ) {

			$data = array();

			foreach ( (array) $terms as $term ) {
				$data[] = $term->description;
			}

			$value = json_encode( $data );

		}

		return $value;
	}

	public function woo_dropship_export_taxonomy_email( $value, $product ) {

		$terms = get_terms(
			array(
				'object_ids' => $product->get_ID(),
				'taxonomy' => 'dropship_supplier',
			)
		);

		if ( ! is_wp_error( $terms ) ) {

			$data = array();

			foreach ( (array) $terms as $term ) {
				$term_vals = get_term_meta( $term->term_id );
				foreach ( $term_vals as $key => $val ) {
					$termMeta = unserialize( $val[0] );
					$email = $termMeta['order_email_addresses'];
				}
				$data[] = $email;

			}

			$value = json_encode( $data );

		}

		return $value;
	}

	public function woo_dropship_export_taxonomy_account_number( $value, $product ) {

		$terms = get_terms(
			array(
				'object_ids' => $product->get_ID(),
				'taxonomy' => 'dropship_supplier',
			)
		);

		if ( ! is_wp_error( $terms ) ) {

			$data = array();

			foreach ( (array) $terms as $term ) {
				$term_vals = get_term_meta( $term->term_id );
				foreach ( $term_vals as $key => $val ) {
					$termMeta = unserialize( $val[0] );
					$account_number = $termMeta['account_number'];
				}
				$data[] = $account_number;

			}

			$value = json_encode( $data );

		}

		return $value;
	}

	/**
	 * Register the 'Custom Column' column in the importer.
	 *
	 * @param  array $columns
	 * @return array  $columns
	 */
	function woo_dropship_map_columns( $columns ) {
		$columns['dropship_supplier_id'] = __( 'Supplier Id', 'your-text-domain' );
		$columns['dropship_supplier_name'] = __( 'Supplier Name', 'your-text-domain' );
		$columns['dropship_supplier_slug'] = __( 'Supplier Slug', 'your-text-domain' );
		$columns['dropship_supplier_description'] = __( 'Supplier Description', 'your-text-domain' );
		$columns['dropship_supplier_email'] = __( 'Supplier Email', 'your-text-domain' );
		$columns['dropship_supplier_account_number'] = __( 'Supplier Account Number', 'your-text-domain' );
		return $columns;
	}

	/**
	 * Add automatic mapping support for custom columns.
	 *
	 * @param  array $columns
	 * @return array  $columns
	 */
	function woo_dropship_add_columns_to_mapping_screen( $columns ) {

		$columns[ __( 'Supplier Id', 'your-text-domain' ) ]     = 'dropship_supplier_id';

		// Always add English mappings.
		$columns['Supplier Name'] = 'dropship_supplier_name';
		$columns['Supplier Slug'] = 'dropship_supplier_slug';
		$columns['Supplier Description']  = 'dropship_supplier_description';
		$columns['Supplier Email']    = 'dropship_supplier_email';
		$columns['Supplier Account Number']   = 'dropship_supplier_account_number';

		return $columns;
	}

	/**
	 * Decode data items and parse JSON IDs.
	 *
	 * @param  array                   $parsed_data
	 * @param  WC_Product_CSV_Importer $importer
	 * @return array
	 */
	function woo_dropship_parse_taxonomy_json_supplier_name( $parsed_data, $importer ) {

		if ( ! empty( $parsed_data['dropship_supplier_name'] ) ) {

			$data = json_decode( $parsed_data['dropship_supplier_name'], true );

			unset( $parsed_data['dropship_supplier_name'] );

			if ( is_array( $data ) ) {

				$parsed_data['dropship_supplier_name'] = array();

				foreach ( $data as $term_id ) {
					$parsed_data['dropship_supplier_name'][] = $term_id;
				}
			}
		}

		return $parsed_data;
	}

	function woo_dropship_parse_taxonomy_json_supplier_slug( $parsed_data, $importer ) {

		if ( ! empty( $parsed_data['dropship_supplier_slug'] ) ) {

			$data = json_decode( $parsed_data['dropship_supplier_slug'], true );

			unset( $parsed_data['dropship_supplier_slug'] );

			if ( is_array( $data ) ) {

				$parsed_data['dropship_supplier_slug'] = array();

				foreach ( $data as $term_id ) {
					$parsed_data['dropship_supplier_slug'][] = $term_id;
				}
			}
		}

		return $parsed_data;
	}

	function woo_dropship_parse_taxonomy_json_supplier_description( $parsed_data, $importer ) {

		if ( ! empty( $parsed_data['dropship_supplier_description'] ) ) {

			$data = json_decode( $parsed_data['dropship_supplier_description'], true );

			unset( $parsed_data['dropship_supplier_description'] );

			if ( is_array( $data ) ) {

				$parsed_data['dropship_supplier_description'] = array();

				foreach ( $data as $term_id ) {
					$parsed_data['dropship_supplier_description'][] = $term_id;
				}
			}
		}

		return $parsed_data;
	}

	function woo_dropship_parse_taxonomy_json_supplier_id( $parsed_data, $importer ) {

		if ( ! empty( $parsed_data['dropship_supplier_id'] ) ) {

			$data = json_decode( $parsed_data['dropship_supplier_id'], true );

			unset( $parsed_data['dropship_supplier_id'] );

			if ( is_array( $data ) ) {

				$parsed_data['dropship_supplier_id'] = array();

				foreach ( $data as $term_id ) {
					$parsed_data['dropship_supplier_id'][] = $term_id;
				}
			}
		}

		return $parsed_data;
	}

	function woo_dropship_parse_taxonomy_json_supplier_email( $parsed_data, $importer ) {

		if ( ! empty( $parsed_data['dropship_supplier_email'] ) ) {

			$data = json_decode( $parsed_data['dropship_supplier_email'], true );

			unset( $parsed_data['dropship_supplier_email'] );

			if ( is_array( $data ) ) {

				$parsed_data['dropship_supplier_email'] = array();

				foreach ( $data as $term_id ) {
					$parsed_data['dropship_supplier_email'][] = $term_id;
				}
			}
		}

		return $parsed_data;
	}

	function woo_dropship_parse_taxonomy_json_supplier_account_number( $parsed_data, $importer ) {

		if ( ! empty( $parsed_data['dropship_supplier_account_number'] ) ) {

			$data = json_decode( $parsed_data['dropship_supplier_account_number'], true );

			unset( $parsed_data['dropship_supplier_account_number'] );

			if ( is_array( $data ) ) {

				$parsed_data['dropship_supplier_account_number'] = array();

				foreach ( $data as $term_id ) {
					$parsed_data['dropship_supplier_account_number'][] = $term_id;
				}
			}
		}

		return $parsed_data;
	}

	/**
	 * Set taxonomy.
	 *
	 * @param  array $parsed_data
	 * @return array
	 */
	function woo_dropship_set_taxonomy( $product, $data ) {
		error_log( 'THere' );
		if ( is_a( $product, 'WC_Product' ) ) {
			if ( ! empty( $data['dropship_supplier_slug'] ) ) {

				$inc = 0;
				$bigArr = array();
				foreach ( $data['dropship_supplier_name'] as $dropship_supplier_name ) {
					$tempArr = array( 'name' => $dropship_supplier_name );
					$bigArr[] = $tempArr;
					$inc++;
				}

				$pq = 0;
				foreach ( $data['dropship_supplier_id'] as $dropship_supplier_id ) {
					$bigArr[ $pq ]['id'] = $dropship_supplier_id;
					$pq++;
				}

				$pq = 0;
				foreach ( $data['dropship_supplier_slug'] as $dropship_supplier_slug ) {
					$bigArr[ $pq ]['slug'] = $dropship_supplier_slug;
					$pq++;
				}

				$pq = 0;
				foreach ( $data['dropship_supplier_description'] as $dropship_supplier_description ) {
					$bigArr[ $pq ]['description'] = $dropship_supplier_description;
					$pq++;
				}

				$pq = 0;
				foreach ( $data['dropship_supplier_email'] as $dropship_supplier_email ) {
					$bigArr[ $pq ]['email'] = $dropship_supplier_email;
					$pq++;
				}

				$pq = 0;
				foreach ( $data['dropship_supplier_account_number'] as $dropship_supplier_account_number ) {
					$bigArr[ $pq ]['account_number'] = $dropship_supplier_account_number;
					$pq++;
				}

				// error_log(print_r($bigArr,true));
				if ( ! empty( $bigArr ) ) {
					foreach ( $bigArr as $arr ) {
						$term = term_exists( $arr['slug'], 'dropship_supplier' );
						if ( $term !== 0 && $term !== null ) {
							// wp_set_object_terms( $product->get_id(),  $arr['slug'], 'dropship_supplier' );
						} else {
							$term = wp_insert_term(
								$arr['name'],
								'dropship_supplier',
								array(
									'description' => $arr['description'],
									'slug'        => $arr['slug'],
								)
							);
							$term_meta = array(
								'account_number' => $arr['account_number'],
								'order_email_addresses' => $arr['email'],
								'csv_delimiter' => '',
								'csv_column_indicator' => '',
								'csv_column_sku' => '',
							);
							update_term_meta( $term['term_id'], 'meta', $term_meta );

							if ( false == email_exists( $arr['email'] ) ) {
								$random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
								$user_id = wp_create_user( $arr['email'], $random_password, $arr['email'] );
								$user = new WP_User( $user_id ); // create a new user object for this user
								$user->set_role( 'dropshipper' );
							} else {
								$random_password = __( 'User already exists.  Password inherited.', 'textdomain' );
							}
						}
					}
				}

				wp_set_object_terms( $product->get_id(), (array) $data['dropship_supplier_slug'], 'dropship_supplier' );

			}
		}

		return $product;
	}

	function woo_dropship_set_taxonomy_update( $row, $mapped_keys ) {

		$productSkuKey = array_search( 'sku', $mapped_keys );
		$dropship_supplier_slug_key = array_search( 'dropship_supplier_slug', $mapped_keys );
		$dropship_supplier_name_key = array_search( 'dropship_supplier_name', $mapped_keys );
		$dropship_supplier_description_key = array_search( 'dropship_supplier_description', $mapped_keys );
		$dropship_supplier_email_key = array_search( 'dropship_supplier_email', $mapped_keys );
		$dropship_supplier_account_number_key = array_search( 'dropship_supplier_account_number', $mapped_keys );

		$productSku = isset( $row[ $productSkuKey ] ) ? $row[ $productSkuKey ] : '';

		if ( ! empty( $productSku ) ) {

			error_log( 'productSku ' . $productSku );

			$pId = wc_get_product_id_by_sku( $productSku );

			if ( $pId == 0 || empty( $pId ) ) {
				$product = get_page_by_title( 'Supplier Product 2008 2', OBJECT, 'product' );
				$pId = $product->ID;
			}

			error_log( 'productId ' . $pId );

			if ( ( $pId > 0 ) && isset( $row[ $dropship_supplier_slug_key ] ) && ! empty( $row[ $dropship_supplier_slug_key ] ) ) {

				$dropship_supplier_slug = str_replace( array( '[', ']', '"' ), '', $row[ $dropship_supplier_slug_key ] );
				$dropship_supplier_name = str_replace( array( '[', ']', '"' ), '', $row[ $dropship_supplier_name_key ] );
				$dropship_supplier_description = str_replace( array( '[', ']', '"' ), '', $row[ $dropship_supplier_description_key ] );
				$dropship_supplier_email = str_replace( array( '[', ']', '"' ), '', $row[ $dropship_supplier_email_key ] );
				$dropship_supplier_account_number = str_replace( array( '[', ']', '"' ), '', $row[ $dropship_supplier_account_number_key ] );

				$dropship_supplier = get_term_by( 'slug', $dropship_supplier_slug, 'dropship_supplier' );

				if ( $dropship_supplier ) {

					error_log( 'updated termId ' . $dropship_supplier->term_id );

					$updateTerm = wp_update_term(
						$dropship_supplier->term_id,
						'dropship_supplier',
						array(
							'name' => $dropship_supplier_name,
							'slug' => $dropship_supplier_slug,
							'description' => $dropship_supplier_description,
						)
					);

					$term_meta = array(
						'account_number' => $dropship_supplier_account_number,
						'order_email_addresses' => $dropship_supplier_email,
						'csv_delimiter' => '',
						'csv_column_indicator' => '',
						'csv_column_sku' => '',
					);
					update_term_meta( $dropship_supplier->term_id, 'meta', $term_meta );

					wp_set_object_terms( $pId, (array) $dropship_supplier->term_id, 'dropship_supplier' );

				} else {

					$term = wp_insert_term(
						$dropship_supplier_name,
						'dropship_supplier',
						array(
							'description' => $dropship_supplier_description,
							'slug'        => $dropship_supplier_slug,
						)
					);

					error_log( 'created termId ' . $term['term_id'] );

					$term_meta = array(
						'account_number' => $dropship_supplier_account_number,
						'order_email_addresses' => $dropship_supplier_email,
						'csv_delimiter' => '',
						'csv_column_indicator' => '',
						'csv_column_sku' => '',
					);
					update_term_meta( $term['term_id'], 'meta', $term_meta );

					wp_set_object_terms( $pId, (array) $term['term_id'], 'dropship_supplier' );

				}
			}
		}

		return $row;
	}

	public function woo_dropship_supplier_report( $dropreports ) {
		$sales_by_supplier = array(
			'sales_by_supplier' => array(
				'title'         => 'Sales By Supplier',
				'description'   => '',
				'hide_title'    => true,
				'callback'      => array( $this, 'sales_by_supplier_callback' ),
			),
		);

		// This can be: orders, customers, stock or taxes, based on where we want to insert our new reports page
		if ( array_key_exists( 'orders', $dropreports ) ) {
			$dropreports['orders']['reports'] = array_merge( $dropreports['orders']['reports'], $sales_by_supplier );
		}

		return $dropreports;
	}

	public function sales_by_supplier_callback() {
		$report = new WC_Report_Sales_By_Supplier();
		$report->output_report();
	}
}
