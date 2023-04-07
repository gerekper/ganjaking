<?php

class WC_Dropshipping_Admin {

	public $orders = null;

	public $product = null;

	public $csv = null;

	public $ali_prod_filter = null;

	public function __construct() {
		 require_once( 'class-wc-dropshipping-product.php' );
		require_once( 'class-wc-dropshipping-csv-import.php' );
		$this->product = new WC_Dropshipping_Product();
		$this->csv = new WC_Dropshipping_CSV_Import();
		// admin menu
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'my_admin_scripts' ) );
		// admin dropship supplier
		$this->ali_prod_filter = new Ali_Product_Filter();

		add_action( 'create_dropship_supplier', array( $this, 'create_term' ), 5, 3 );
		add_action( 'delete_dropship_supplier', array( $this, 'delete_term' ), 5 );
		add_action( 'created_term', array( $this, 'save_category_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );
		add_action( 'dropship_supplier_add_form_fields', array( $this, 'add_category_fields' ) );
		add_action( 'dropship_supplier_edit_form_fields', array( $this, 'edit_category_fields' ), 10, 2 );
		add_action( 'wp_ajax_CSV_upload_form', array( $this, 'ajax_save_category_fields' ) );
		add_filter( 'manage_edit-dropship_supplier_columns', array( $this, 'manage_columns' ), 10, 1 );
		add_action( 'manage_dropship_supplier_custom_column', array( $this, 'column_content' ), 10, 3 );
		add_action( 'admin_menu', array( $this, 'my_remove_menu_pages' ) );
		add_action( 'admin_menu', array( $this, 'dropshipper_order_list_page' ) );
		add_action( 'wp_ajax_dropshipper_shipping_info_edited', array( $this, 'dropshipper_shipping_info_edited_callback' ) );
		add_filter(
			'woocommerce_order_item_get_formatted_meta_data',
			array( $this, 'mobilefolk_order_item_get_formatted_meta_data' ),
			10,
			1
		);

		// register the ajax action or generate api key callback function

		add_action( 'wp_ajax_email_ali_api_key', array( $this, 'email_ali_api_key' ) );
		add_action( 'wp_ajax_nopriv_email_ali_api_key', array( $this, 'email_ali_api_key' ) );
		add_action( 'wp_ajax_hide_cbe_message', array( $this, 'hide_cbe_message' ) );
		add_action( 'admin_menu', array( $this, 'unset_menus_dropshipper' ) );
		add_action( 'admin_init', array( $this, 'remove_jetpack_menu' ) );
	}

	public function unset_menus_dropshipper( $menu_order ) {
		global $menu;
		global $current_user;
		$user_roles = $current_user->roles;
		if ( in_array( 'dropshipper', $user_roles ) ) {
			$hMenu = $menu;
			foreach ( $hMenu as $nMenuIndex => $hMenuItem ) { // print_r($hMenuItem); echo '<br>';
				if ( $hMenuItem[2] == 'dropshipper-order-list' || $hMenuItem[2] == 'profile.php' ) {
				} else {
					unset( $menu[ $nMenuIndex ] );
				}
			}
		}
	}

	public function remove_jetpack_menu() {
		if ( class_exists( 'Jetpack' ) ) {
			global $current_user;
			$user_roles = $current_user->roles;
			if ( in_array( 'dropshipper', $user_roles ) ) {
					remove_menu_page( 'jetpack' );
			}
		}
	}

	public function hide_cbe_message() {
		update_option( 'cbe_hideoption', 'yes' ); // here

	}

	// ajax function for generate api key callback function

	public function email_ali_api_key() {
		$input = site_url();

		$input = trim( $input, '/' );

		if ( ! preg_match( '#^http(s)?://#', $input ) ) {
			$input = 'http://' . $input;
		}

		$urlParts = parse_url( $input );
		$domain = preg_replace( '/^www\./', '', $urlParts['host'] );
		$aliexpresskey = generate_aliexpress_key( $domain );
		$admin_email = get_bloginfo( 'admin_email' );

		$to = $admin_email;
		$subject = 'Your AliExpress Key';
		$message = 'Your aliexpress key for ' . $domain . ' is: ' . $aliexpresskey;

		wp_mail( $to, $subject, $message );

		wp_die( $aliexpresskey );
	}

	public function mobilefolk_order_item_get_formatted_meta_data( $formatted_meta ) {
		$options = get_option( 'wc_dropship_manager' );

		if ( isset( $options['hide_suppliername'] ) ) {
			$hide_suppliername = $options['hide_suppliername'];
		} else {
			$hide_suppliername = '';
		}

		if ( $hide_suppliername == '1' ) {
			$temp_metas = array();

			foreach ( $formatted_meta as $key => $meta ) {
				if ( isset( $meta->key ) && ! in_array( $meta->key, array( 'supplier' ) ) ) {
					$temp_metas[ $key ] = $meta;
				}
			}

			return $temp_metas;
		} else {
			return $formatted_meta;
		}
	}

	public function admin_styles() {
		$base_name = explode( '/', plugin_basename( __FILE__ ) );

		wp_enqueue_script(
			'wc_dropship_manager_scripts',
			plugins_url() . '/' . $base_name[0] . '/assets/js/wc_dropship_manager.js',
			array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' )
		);

		wp_enqueue_script( 'jquery-tiptip', plugins_url() . '/woocommerce/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ), true );

		wp_enqueue_style( 'woocommerce_admin_styles', plugins_url() . '/woocommerce/assets/css/admin.css', array() );
	}

	public function my_admin_scripts() {
		$base_name = explode( '/', plugin_basename( __FILE__ ) );

		if ( array_key_exists( 'success', $_GET ) && trim( $_GET['success'] ) == 'no' ) {
			wp_enqueue_script( 'my-jquery-min-script', plugins_url() . '/' . $base_name[0] . '/assets/js/jquery.min.js', array( 'jquery' ), true );
			wp_enqueue_script( 'popper.min.js.map', plugins_url() . '/' . $base_name[0] . '/assets/js/popper.min.js', array( 'jquery' ), true );
			wp_enqueue_script( 'my-bootstrap-script', plugins_url() . '/' . $base_name[0] . '/assets/js/bootstrap.min.js', array( 'jquery' ), true );
			wp_enqueue_script( 'my-custom-script', plugins_url() . '/' . $base_name[0] . '/assets/js/custom-modal.js', array( 'jquery' ), true );
		} else {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-blockui' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-widget' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-tiptip' );
			wp_enqueue_script( 'jquery-ui-dialog' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'my-great-script', plugins_url() . '/' . $base_name[0] . '/assets/js/myscript.js', array( 'jquery' ), '1.0.1', true );
		}
	}

	public function dropshipper_shipping_info_edited_callback() {
		global $wpdb;

		if ( isset( $_POST['id'] ) && isset( $_POST['info'] ) ) {
			$id = intval( $_POST['id'] );

			$info = $_POST['info'];

			update_post_meta( $_POST['id'], 'dropshipper_shipping_info_' . get_current_user_id(), $info );

			echo 'true';
		} else {
			echo 'false';
		}

		die(); // this is required to return a proper result

	}

	public function manage_columns( $cols ) {
		unset( $cols['description'] );
		unset( $cols['slug'] );
		unset( $cols['posts'] );
		unset( $cols['address_line1'] );
		unset( $cols['address_line2'] );
		unset( $cols['supplier_city'] );
		unset( $cols['country_state'] );
		unset( $cols['postcode_zip'] );
		// unset( $cols['specific_delivery_location'] );
		unset( $cols['contact_info'] );
		// $cols['account_number'] = 'Account Number';
		$cols['order_email_addresses'] = 'Email Addresses';
		$cols['inventory'] = 'Inventory';
		$cols['posts'] = 'Count';
		// $cols['contact_info'] = 'Contact Info';
		$cols['address_line1'] = 'Address 1';
		$cols['address_line2'] = 'Address 2';
		$cols['supplier_city'] = 'City';
		$cols['country_state'] = 'Country / State';
		$cols['postcode_zip'] = 'Postcode / ZIP';

		return $cols;
	}

	/*********************************************************************/
	/*
	  For Create supplier                                              *
	/*********************************************************************/



	public function column_content( $blank, $column_name, $term_id ) {
		$ds = wc_dropshipping_get_dropship_supplier( intval( $term_id ) );
		$options = get_option( 'wc_dropship_manager' );
		// print_r($options['packing_slip_customer_service_email'] );

		$address_line1 = '';

		if (isset($ds["address_line1"])) {
			$address_line1 = $ds["address_line1"];
		}else{
			$address_line1 = '';
		}

		$address_line2 = '';

		if (isset($ds["address_line2"])) {
			$address_line2 = $ds["address_line2"];
		}else{
			$address_line2 = '';
		}

		$supplier_city = '';

		if (isset($ds["supplier_city"])) {
			$supplier_city = $ds["supplier_city"];
		}else{
			$supplier_city = '';
		}

		$country_state = '';

		if (isset($ds["country_state"])) {
			$country_state = $ds["country_state"];
		}else{
			$country_state = '';
		}

		$postcode_zip = '';

		if (isset($ds["postcode_zip"])) {
			$postcode_zip = $ds["postcode_zip"];
		}else{
			$postcode_zip = '';
		}

		// print_r($ds);
		switch ( $column_name ) {
			case 'account_number':
				echo $ds['account_number'];

				break;

			/*
			case 'supplier_price':
				echo $ds['supplier_price'];
			break;
			*/

			case 'order_email_addresses':
				echo $ds['order_email_addresses'];

				break;

			case 'inventory':
				echo '<p><a title="Import ' . $ds['name'] . '&apos;s Inventory Status in .CSV Format" href="' . admin_url( 'admin-ajax.php' ) . '?action=get_CSV_upload_form&width=600&height=350&term_id=' . $term_id . '" class="thickbox button-primary csvwindow" term_id="' . $term_id . '" >Import Inventory .CSV</a></p>';

				break;

			// case 'contact_info':
			// echo $options['packing_slip_customer_service_email'];
			// break;

			case 'address_line1':
				echo $address_line1;
				break;

			case 'address_line2':
				echo $address_line2;
				break;

			case 'supplier_city':
				echo $supplier_city;
				break;

			case 'country_state':
				echo $country_state;
				break;

			case 'postcode_zip':
				echo $postcode_zip;
				break;
		}
		// print_r($ds);
	}

	public function get_dropship_supplier_fields() {
		$meta = array(

			'account_number' => '',

			// 'supplier_price' => '',

			'order_email_addresses' => '',
			'csv_delimiter' => ',',
			'csv_column_indicator' => '',
			'csv_column_sku' => '',
			'csv_column_qty' => '',
			'csv_type' => '',
			'csv_quantity' => '',
			'csv_indicator_instock' => '',
			'contact_info' => '',
			'address_line1' => '',
			'address_line2' => '',
			'supplier_city' => '',
			'country_state' => '',
			'postcode_zip' => '',

		);

		return $meta;
	}

	public function add_category_fields() {
		 $meta = $this->get_dropship_supplier_fields();

		$this->display_add_form_fields( $meta );
	}

	public function edit_category_fields( $term, $taxonomy ) {
		$meta = get_term_meta( $term->term_id, 'meta', true );

		$this->display_edit_form_fields( $meta );
	}

	//
	// Menu options : Products > Suppliers > Add New Dropshipping Supplier
	//

	public function display_add_form_fields( $data ) {
		// print_r($data);
		$options = get_option( 'wc_dropship_manager' );

		if ( $data['specific_delivery_location'] = '1' ) {

			$data['specific_delivery_location'] = ' checked="checked" ';
			$spec_deli_loc = 'style="display:table-row"';
		}

		add_thickbox();

		echo '
		<style>#col-right {
			float: right;
			width: 65%;
			margin-top: -720px;
		}

		#wpfooter {
			position: fixed;
		}

		#col-left {
			float: left;
			width: 25%;
		}

		#col-right {
			float: right;
			width: 75%;
			margin-top: -840px;
		}

		.wp-core-ui .button-primary {
			white-space: pre-wrap;
			font-size: 11px;
			padding: 0 5px;
		}

		.thickbox button-primary csvwindow{
			width: 50px;
		}

		.td_address{
			width: 95%;
		}
		.widefat th{
			font-size: 12px;
		}


		</style>

			<div class="form-field term-account_number-wrap">
				<label for="account_number" >Account #</label>
				<input type="text" size="40" name="account_number" value="' . $data['account_number'] . '" />
				<p>Your store&apos;s account number with this supplier. Leave blank if you don&apos;t have an account number</p>
			</div>
			<div class="form-field form-required term-order_email_addresses-wrap">
				<label for="order_email_addresses" >Email Addresses</label>
				<input type="text" size="40" name="order_email_addresses" value="' . $data['order_email_addresses'] . '"  />
				<p>When a customer purchases a product from you, the supplier will be sent an notification via email. List the supplier&apos;s email addresses that should be notified when a new order is placed.<p>
			</div>';

			echo '
				<div class="form-field form-required term_specific_delivery_location_wrap">
					<input name="specific_delivery_location" id="specific_delivery_location" data-id="specific_delivery_location" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" value="' . $data['specific_delivery_location'] . '"   />

					<label for="specific_delivery_location" style="margin-left: 25px; margin-top: -20px;
					margin-bottom: 7px;" > Enable specific delivery location for the supplier.</label>';

				echo '</div>

				<div class="inner-toggle specific_delivery_location" style="display:none;">

					<label for="address_line1" >Address line 1</label>
					<input type="text" class="td_address" size="40"  name="address_line1" value="' . $data['address_line1'] . '" /></br></br>

					<label for="address_line2" >Address line 2</label>
					<input type="text" class="td_address" size="40"  name="address_line2" value="' . $data['address_line2'] . '"  /></br></br>

					<label for="supplier_city" >City</label>
					<input type="text" class="td_address" size="40"  name="supplier_city" value="' . $data['supplier_city'] . '"  /></br></br>

					<label for="country_state" >Country/State</label>
					<input type="text" class="td_address" size="40"  name="country_state" value="' . $data['country_state'] . '" /></br></br>

					<label for="postcode_zip" >Postcode/ZIP</label>
					<input type="number" class="td_address" size="40"  name="postcode_zip" value="' . $data['postcode_zip'] . '"/></br></br>

				</div>';

			echo '</div>';
	}

	//
	// Menu options : Products > Suppliers > Edit Dropshipping Supplier
	//

	public function display_edit_form_fields( $data ) {
		// wc_dropshipping_get_dropship_supplier( $id );
			$options = get_option( 'wc_dropship_manager' );
			$speci_del_style_disply = 'style="display:none"';

			if (isset($data['specific_delivery_location'])) {
				$specific_delivery_location = $data['specific_delivery_location'];
			} else {
				$specific_delivery_location = '';
			}

		$csv_types = array(
			'quantity' => 'Quantity on Hand',
			'indicator' => 'In-Stock Indicator',
		);


		if ( $data['address_line1'] != '' ) {
			echo 'Hiii
			<script>
				window.onload = function() {
				var checkbox = document.getElementById("specific_delivery_location");
				checkbox.checked = true;

			  }
			</script>';
			$speci_del_style_disply = 'style="display:table-row"';

		}

		echo '
		<style>
		.validate{
			margin-top: -190px;
		}
		</style>

					<tr class="term-account_number-wrap">
						<th><label for="account_number" >Account #</label></th>
						<td><input type="text" size="40" name="account_number" value="' . $data['account_number'] . '" />
						<p>Your store&apos;s account number with this supplier. Leave blank if you don&apos;t have an account number</p></td>
					</tr>

					<tr  class="term-order_email_addresses-wrap">
						<th><label for="order_email_addresses" >Email Addresses</label></th>
						<td><input type="text" size="40" name="order_email_addresses" value="' . $data['order_email_addresses'] . '"  />
						<p>When a customer purchases a product from you, the supplier will be sent an notification via email. List the supplier&apos;s email addresses that should be notified when a new order is placed.<p></td>
					</tr>';

					echo '<tr class="term_specific_delivery_location_wrap">
						<td>
							<input name="specific_delivery_location" id="specific_delivery_location" data-id="specific_delivery_location" class="miscellaneous_packing_slip_options_checkbox" type="checkbox" value="' . $specific_delivery_location . '" ' . $specific_delivery_location . ' />
						</td>
						<th>
							<label for="specific_delivery_location" > Enable specific delivery location for the supplier.</label>
						</th>

					</tr>';

					// print_r($data['specific_delivery_location']);

					echo '
					<div class="hide_show_div_spe_del" style="display:none; background-color:red;">
						<tr class="inner-toggle specific_delivery_location" ' . $speci_del_style_disply . '>

							<th><label for="address_line1" >Address line 1</label></th>
							<td><input type="text" class="td_address" size="40" name="address_line1" value="' . $data['address_line1'] . '"  /></td></br></br>
						</tr>

						<tr class="inner-toggle specific_delivery_location" ' . $speci_del_style_disply . '>
							<th><label for="address_line2" >Address line 2</label></th>
							<td><input type="text" class="td_address" size="40" name="address_line2" value="' . $data['address_line2'] . '"  /></td></br></br>
						</tr>

						<tr class="inner-toggle specific_delivery_location" ' . $speci_del_style_disply . '>
							<th><label for="supplier_city" >City</label></th>
							<td><input type="text" class="td_address" size="40" name="supplier_city" value="' . $data['supplier_city'] . '"  /></td></br></br>
						</tr>

						<tr class="inner-toggle specific_delivery_location" ' . $speci_del_style_disply . '>
							<th><label for="country_state" >Country/State</label></th>
							<td><input type="text" class="td_address" size="40" name="country_state" value="' . $data['country_state'] . '"   /></td></br></br>
						</tr>

						<tr class="inner-toggle specific_delivery_location" ' . $speci_del_style_disply . '>
							<th><label for="postcode_zip" >Postcode/ZIP</label></th>
							<td><input type="number" class="td_address" size="40" name="postcode_zip" value="' . $data['postcode_zip'] . '"  /></td></br></br>

						</tr>';




				echo '</table>

				<h3>Supplier Inventory CSV Import Settings</h3>
				<p>(If you do not receive inventory statuses from your supplier in the form of a .CSV file, leave these settings blank)</p>
				<table class="form-table">
					<tr  class="term-csv_delimiter-wrap">
						<th><label for="csv_delimiter" >CSV File Column Delimiter</label></th>
						<td><input type="text" size="2" name="csv_delimiter" value="' . $data['csv_delimiter'] . '" />
						<p>Please indicate what character is used to separate fields in the CSV file. Normally this is a comma</p></td>
					</tr>



					<tr  class=" term-column_sku-wrap">
						<th><label for="csv_column_sku" >CSV SKU Column #</label></th>
						<td><input type="text" size="2" name="csv_column_sku" value="' . $data['csv_column_sku'] . '" />
						<p>Please indicate which column in the CSV file corresponds to product SKUs. Note that this should be the same SKU that the manufacturer uses. WooCommerce Dropshipping will automatically add the SKU code for products from this suppler when you upload a .CSV file</p></td>
					</tr>



					<tr  class=" term-csv_type-wrap">
						<th><label for="csv_type">CSV Type</label></th>
						<td><select name="csv_type" id="csv_type" >';

		foreach ( $csv_types as $csv_type => $description ) {
			$selected = '';

			if ( $data['csv_type'] === $csv_type ) {
				$selected = 'selected';
			}

			echo '<option value="' . $csv_type . '" ' . $selected . '>' . $description . '</option>';
		}

		echo '</select>
						<p>Please indicate how the .CSV file&apos;s data should be read. <br /><br /><b>Quantity on Hand </b>- If your supplier sends you a .CSV file that contains the quantity that they have remaining in their inventory, you should use this method. Any number above zero indicates that the product is still in stock.<br /><b>In-Stock Indicator </b> - Use this method if your supplier sends you a .CSV file that includes a column indicating whether or not a product is in stock.  This is typically in either a Y/N or 1/0 format to indicate whether or not the product is in stock.</p></td>
					</tr>
					<tr  class="csv_quantity csv_types">
						<th><label for="csv_column_qty" >CSV Inventory Quantity Column #</label></th>
						<td><input type="text" size="2" name="csv_column_qty" value="' . $data['csv_column_qty'] . '" />
						<p>Please indicate which column in the .CSV file corresponds to the quantity of inventory available</p></td>
					</tr>


					<tr  class="csv_indicator csv_types">
						<th><label for="csv_column_indicator" >In-Stock Indicator Column #</label></th>
						<td><input type="text" size="2" name="csv_column_indicator" value="' . $data['csv_column_indicator'] . '" />
						<p>Please indicate which column in the .CSV file indicates whether or not a product is in stock</p></td>
					</tr>

					<tr  class="csv_indicator csv_types">
						<th><label for="csv_indicator_instock" >In-Stock Indicator Value</label></th>
						<td><input type="text" size="2" name="csv_indicator_instock" value="' . $data['csv_indicator_instock'] . '" />
						<p>Please input the value (ie. Y or 1) in the column defined above that indicates whether or not a product is in stock </p></td>
					</tr>';
	}

	/*
	public function cloneUserRole()   {
		 global $wp_roles;
		 if (!isset($wp_roles))
		 $wp_roles = new WP_Roles();
		 $adm = $wp_roles->get_role('subscriber');
		 // Adding a new role with all admin caps.
		 $wp_roles->add_role('dropshipper', 'Dropshipper', $adm->capabilities);
	}*/

	public function my_remove_menu_pages() {
		global $user_ID;
		$user = wp_get_current_user();
		if ( in_array( 'dropshipper', (array) $user->roles ) ) {

			remove_menu_page( 'edit-comments.php' );
			remove_menu_page( 'index.php' );
			remove_menu_page( 'link-manager.php' ); // Links
			remove_menu_page( 'posts.php' );
			remove_menu_page( 'edit.php' );
			remove_menu_page( 'edit.php?post_type=elementor_library' ); // Elementor
			remove_menu_page( 'elementor' ); // Elementor
			// remove_menu_page('Posts.php');
			remove_menu_page( 'tools.php' ); // Tools
			remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' ); // Quick Press widget
			remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' ); // Recent Drafts
			remove_meta_box( 'dashboard_primary', 'dashboard', 'side' ); // WordPress.com Blog
			remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' ); // Other WordPress News
			remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' ); // Incoming Links
			remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' ); // Plugins
			remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' ); // Right Now
			remove_meta_box( 'rg_forms_dashboard', 'dashboard', 'normal' ); // Gravity Forms
			remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' ); // Recent Comments
			remove_meta_box( 'icl_dashboard_widget', 'dashboard', 'normal' ); // Multi Language Plugin
			remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' ); // Activity
			remove_meta_box( 'e-dashboard-overview', 'dashboard', 'normal' ); // Elementor Activity

		}
	}

	public function dropshipper_order_list_page() {
		 global $user_ID;

		$user = wp_get_current_user();
		if ( in_array( 'dropshipper', (array) $user->roles ) ) {
			$page_title = 'Order Lists';
			$menu_title = 'Order List';
			$capability = 'dropshipper';
			$menu_slug  = 'dropshipper-order-list';
			$function   = 'dropshipper_order_list';
			$icon_url   = 'dashicons-media-code';

			add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function );
		}
	}

	public function save_category_fields( $term_id, $tt_id, $taxonomy ) {
		$options = get_option( 'wc_dropship_manager' );

		if ( isset( $options['email_supplier'] ) ) {
			$email_supplier = $options['email_supplier'];
		}

		// check for uploaded csv

		if ( count( $_FILES ) > 0 && $_FILES['csv_file']['error'] == 0 ) {

			// we are saving an inventory form submit

			do_action( 'wc_dropship_manager_parse_csv' );
		} else {
			if ( $taxonomy == 'dropship_supplier' ) {

				// do update

				$meta = $this->get_dropship_supplier_fields();

				foreach ( $meta as $key => $val ) {
					if ( isset( $_POST[ $key ] ) ) {
						$meta[ $key ] = $_POST[ $key ];
					}
				}

				$cterm = update_term_meta( $term_id, 'meta', $meta );
			}

			/*Create New User When Create Term*/

			if ( $cterm != '' && $taxonomy == 'dropship_supplier' ) {
				$username = @$_POST['tag-name'];

				$email = @$_POST['order_email_addresses'];

				/*$password = wp_generate_password();*/

				$the_user = get_user_by( 'email', $email );
				$user_id = @$the_user->ID;

				update_user_meta( $user_id, 'supplier_id', $term_id );

				if ( ! empty( $username ) && ! empty( $email ) && ! $user_id && email_exists( $email ) == false ) {
					$random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
					$user_id = wp_create_user( $username, $random_password, $email );
					update_user_meta( $user_id, 'supplier_id', $term_id );
					$user_id_role = new WP_User( $user_id );
					$user_id_role->set_role( 'dropshipper' );
					$loginurl = wp_login_url();
					/*Send User Password*/

					if ( isset( $email_supplier ) ) {
						if ( $email_supplier == '1' ) {
							$to = $email;
							$subject = 'Registration Detail';
							$from = get_option( 'admin_email' );

							// To send HTML mail, the Content-type header must be set
							$headers  = 'MIME-Version: 1.0' . "\r\n";
							$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

							// Create email headers
							$headers .= 'From: ' . $from . "\r\n"
								. 'Reply-To: ' . $from . "\r\n"
								. 'X-Mailer: PHP/' . phpversion();

							// Compose a simple HTML email message
							$message = '<html><body>';
							$message .= '<h1 style="color:#f40;">Hi ' . $user_id_role->display_name . '!</h1>';
							$message .= '<p style="color:#080;font-size:15px;">Thanks For Registration</p>';

							// $message .= '<p style="disply:none">Your Email:&nbsp;'. $email .'</p>';
							$message .= '<p>Your User Name:&nbsp;' . $user_id_role->display_name . '</p>';
							$message .= '<p>Your Password:&nbsp;' . $random_password . '</p>';
							$message .= '<p>Change Your Password Once you login</p>';
							$message .= '<p>Login URL: ' . $loginurl . '</p>';
							$message .= '</body></html>';

							wp_mail( $to, $subject, $message, $headers );

							// mail($to, $subject, $message, $headers);

						}
					}
				} else {
					$random_password = __( 'User already exists.  Password inherited.' );
				}
			}
		}

	}

	public function ajax_save_category_fields() {
		$this->save_category_fields( $_POST['term_id'], '', $_POST['taxonomy'] );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			wp_die();
		}
	}

	/* Order term when created (put in position 0). */



	public function create_term( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( $taxonomy != 'dropship_supplier' && ! taxonomy_is_product_attribute( $taxonomy ) ) {
			return;
		}
		$meta_name = taxonomy_is_product_attribute( $taxonomy ) ? 'order_' . esc_attr( $taxonomy ) : 'order';
		update_term_meta( $term_id, $meta_name, 0 );
	}

	/* When a term is deleted, delete its meta. */



	public function delete_term( $term_id, $taxonomy = '' ) {
		if ( $taxonomy != 'dropship_supplier' && ! taxonomy_is_product_attribute( $taxonomy ) ) {
			return;
		}
		$meta_name = taxonomy_is_product_attribute( $taxonomy ) ? 'order_' . esc_attr( $taxonomy ) : 'order';
		$term_id = (int) $term_id;
		update_term_meta( $term_id, $meta_name, 0 );

		if ( ! $term_id ) {
			return;
		}
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->termmeta} WHERE `term_id` = " . $term_id );
	}
}


