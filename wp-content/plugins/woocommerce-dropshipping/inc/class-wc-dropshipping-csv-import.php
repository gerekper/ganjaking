<?php
class WC_Dropshipping_CSV_Import {
	public function __construct() {
		$this->init();
	}

	public function init() {
		add_action('wp_ajax_get_CSV_upload_form',array($this,'CSV_upload_form'));
		add_action('wc_dropship_manager_parse_csv', array($this,'admin_save_inventory_status'));
		add_action('wc_dropship_manager_out_of_stock',array($this,'display_out_of_stock'),10, 2);
		add_action('wc_dropship_manager_in_stock',array($this,'display_in_stock'),10, 2);
	}

	/* CSV Inventory */
	public function CSV_upload_form() {
		$term_id = $_GET['term_id'];
		$ds = wc_dropshipping_get_dropship_supplier( intval( $term_id ) );
		// Inventory Upload window
		echo '	<div id="CSVwindow" >
					<form class="csvupload_form" action="'.admin_url('admin-ajax.php').'" method="post" enctype="multipart/form-data" target="csvupload_iframe-'.$ds['slug'].'" >
						<input type="hidden" name="action" value="CSV_upload_form" />';
						//wp_nonce_field( 'editedtag');
						wp_nonce_field( 'CSV_upload_form');
		echo '			<p>If your supplier provides a spreadsheet in .CSV format indicating their inventory levels (Quantity on Hand) or whether or not their products are in stock (In-Stock Indicator) you can import the .CSV file here to update your inventory status.
						Before uploading a .CSV file, please configure which columns to use on the spreadsheet by mousing over the supplier&apos;s name and select "Edit"</p>
						<table>
							<tr>
									<th>CSV File Location:</th>
							</tr>
							<tr>
								<td>
									<input type="hidden" id="csv_upload_term_id" name="term_id" value="'.$term_id.'" />
									<input type="file" name="csv_file" value="" />
									<input type="hidden" value="'.$ds['slug'].'" name="slug">
									<input type="hidden" value="dropship_supplier" name="taxonomy">
									<input type="hidden" value="product" name="post_type">
								</td>
								<td><input class="button-primary csvupload_submit_btn" type="submit" name="submit" value="Update" /></td>
							</tr>
						</table>
					</form>
					<iframe style="display:none;width: 100%;height: 340px" id="csvupload_iframe-'.$ds['slug'].'" class="csv_upload_iframe" name="csvupload_iframe-'.$ds['slug'].'" src="#" ></iframe>
				</div>';
		wp_die();
	}

	// parses a supplier inventory csv and updates the SKUS
	public function admin_save_inventory_status() {
		global $wpdb;
		ini_set('auto_detect_line_endings',TRUE);
		$ds = wc_dropshipping_get_dropship_supplier( intval( $_POST['term_id'] ) ); // get the supplier data
		$options = get_option( 'wc_dropship_manager' );
		$instock = '';
		$outofstock = '';
		$supplier_info = '';
		$temp = array();

		$q_select_skus = " 	CREATE TEMPORARY TABLE %name AS (
								SELECT m.post_id
								FROM ".$wpdb->postmeta." m
								INNER JOIN ".$wpdb->term_relationships." tr ON ( m.post_id = tr.object_id )
								INNER JOIN ".$wpdb->term_taxonomy." tt ON ( tr.term_taxonomy_id = tt.term_taxonomy_id )
								AND tt.taxonomy = 'dropship_supplier'
								AND tt.term_id = ".$ds['id']."
								WHERE m.meta_key = '_sku'
								AND m.meta_value IN (%s)
                            );";

		$q_update_stockstatus = " UPDATE ".$wpdb->postmeta."
									SET meta_value = %s
									WHERE meta_key = '_stock_status'
									AND post_id IN (
									 SELECT post_id AS id
									 FROM %name );";

		$q_update_visibilitystatus = " 	UPDATE ".$wpdb->postmeta."
                                        SET meta_value = %s
                                        WHERE meta_key = '_visibility'
                                        AND post_id IN (
                                         SELECT post_id AS id
                                         FROM %name );";

      /*$q_update_quantitystatus = " 	UPDATE ".$wpdb->postmeta."
                                        SET meta_value = %s
                                        WHERE meta_key = '_stock'
                                        AND post_id IN (
                                         SELECT post_id AS id
                                         FROM %name );";*/

		// process uploaded CSV
		if(($_FILES['csv_file']['error'] == 0)&&(strlen($ds['csv_delimiter']) > 0)) {
			$name = $_FILES['csv_file']['name'];
			$ext_file = explode('.', $_FILES['csv_file']['name']);
			$ext = strtolower(end($ext_file));
			$type = $_FILES['csv_file']['type'];
			$tmpName = $_FILES['csv_file']['tmp_name'];
			// check the file is a csv
			if($ext === 'csv') {
				if(($handle = fopen($tmpName, 'r')) !== FALSE) {
					// necessary if a large csv file
					set_time_limit(0);
					// loop over CSV
					while(($data = fgetcsv($handle, 1000, $ds['csv_delimiter'])) !== FALSE) {
						$temp = array();
						// get the values from the csv
						$temp['sku'] = $data[$ds['csv_column_sku']-1];
						if($ds['csv_type'] === 'quantity') {
							$temp['qty_remaining'] = preg_replace('/[^0-9]/', '', $data[$ds['csv_column_qty']-1]);
							// get rid of anything that is not a number.
							$qty_remaining = $temp['qty_remaining'];
							// All we care about is if there is enough product in the warehouse to ship orders
							if (trim($temp['qty_remaining']) < $options['inventory_pad']) {
								// if the product has less than "inventory_pad" remaining then its out of stock
								$outofstock .= "'$temp[sku]',";
								$sku = $temp['sku'];
								$product_id = wc_get_product_id_by_sku($sku);
								update_post_meta($product_id,'_stock',$qty_remaining);
							} else {
								// product is active

								$instock .= "'$temp[sku]',";
								$sku = $temp['sku'];
								$product_id = wc_get_product_id_by_sku($sku);
								update_post_meta($product_id,'_stock',$qty_remaining);

							}
						} elseif ($ds['csv_type'] === 'indicator') {
							if (strcasecmp(trim($data[$ds['csv_column_indicator']-1]),$ds['csv_indicator_instock']) != 0 ) {
								// if the field does not equal the "in-stock" indicator then its out of stock
								$outofstock .= "'$temp[sku]',";
							} else {
								// product is active
								$instock .= "'$temp[sku]',";
							}
						}
						unset($temp);
					}
			        fclose($handle);
					// add empty data on the end so SQL doesnt get mad about the extra comma
					$outofstock .= "''";
					$instock .= "''";
					define( 'DIEONDBERROR', true );
					$wpdb->show_errors();
					// update all out of stock skus
					// create temp table
					if ( strlen($outofstock) > 0 ) {
						$sql = str_replace('%name','outofstock_skus', $q_select_skus );
						$sql = str_replace('%s',$outofstock, $sql );
						$wpdb->query($sql);
						//use temp table to update stock status on all skus that are oos
						$sql = str_replace('%name','outofstock_skus', $q_update_stockstatus );
						$sql = $wpdb->prepare( $sql , array('outofstock') );
						$wpdb->query($sql);
						// use temp table to update visibility on all skus that are oos
						$sql = str_replace('%name','outofstock_skus', $q_update_visibilitystatus );
						$sql = $wpdb->prepare( $sql , array('hidden') );
						$wpdb->query($sql);
					}
					if ( strlen($instock) > 0 ) {
						// update all now instock skus
						$sql = str_replace('%name','instock_skus', $q_select_skus );
						$sql = str_replace('%s',$instock, $sql );
						$wpdb->query($sql);
						$sql = str_replace('%name','instock_skus', $q_update_stockstatus );
						$sql = $wpdb->prepare( $sql , array('instock') );
						$wpdb->query($sql);
						// use temp table to update visibility on all skus that are in stock
						$sql = str_replace('%name','instock_skus', $q_update_visibilitystatus );
						$sql = $wpdb->prepare( $sql , array('visible') );
						$wpdb->query($sql);
					}
					do_action('wc_dropship_manager_out_of_stock',$outofstock,$supplier_info);
					do_action('wc_dropship_manager_in_stock',$instock,$supplier_info);
					//$wpdb->print_error();
		        	}
			}
		} else {
			echo '<p>There was an error processing the .CSV file.  If this error persists, please contact OPMC support.</p>';
		}
		// announce that we've finished
		do_action('wc_dropship_manager_inventory_status_update_completed');
	}

	// TODO: format this output
	public function display_out_of_stock($outofstock,$supplier_info) {
		$aSkus = explode(',',$outofstock);
		$new_skus = array();
		foreach( $aSkus as $sku ) {
			if (2 === strlen($sku)){
				break;
			}else{
				$new_skus[] = $sku;
			}
		}
		echo '<div style="float:left;"><b>OUT OF STOCK: '.count($new_skus).'</b>';
		echo '<ul>';
		foreach( $new_skus as $sku ) {
			if (2 === strlen($sku)){
				break;
			}
			echo '<li>'.$sku.'</li>';
		}
		echo '</ul></div>';
	}

	public function display_in_stock($instock,$supplier_info) {
		$aSkus = explode(',',$instock);
		$new_skus = array();
		foreach( $aSkus as $sku ) {
			if (2 === strlen($sku)){
				break;
			}else{
				$new_skus[] = $sku;
			}
		}
		echo '<div style="float:right;"><b>IN STOCK: '.count($new_skus).'</b>';
		echo '<ul>';
		foreach($new_skus as $sku)
		{
			echo '<li>'.$sku.'</li>';
		}
		echo '</ul></div>';
		echo '<br style="clear:both" />';
	}
}
