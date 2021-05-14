<?php

class WC_Dropshipping_Admin
{
	public $orders = null;

	public $product = null;

	public $csv = null;

	public $ali_prod_filter = null;

	public function __construct()
	{
		require_once('class-wc-dropshipping-product.php');
		require_once('class-wc-dropshipping-csv-import.php');

		$this->product = new WC_Dropshipping_Product();
		$this->csv = new WC_Dropshipping_CSV_Import();

		// admin menu

		add_action('admin_enqueue_scripts', array($this, 'admin_styles'));
		add_action('admin_enqueue_scripts', array($this, 'my_admin_scripts'));

		// admin dropship supplier

		$this->ali_prod_filter = new Ali_Product_Filter();

		add_action('create_dropship_supplier', array($this, 'create_term'), 5, 3);
		add_action('delete_dropship_supplier', array($this, 'delete_term'), 5);
		add_action('created_term', array($this, 'save_category_fields'), 10, 3);
		add_action('edit_term', array($this, 'save_category_fields'), 10, 3);
		add_action('dropship_supplier_add_form_fields', array($this, 'add_category_fields'));
		add_action('dropship_supplier_edit_form_fields', array($this, 'edit_category_fields'), 10, 2);
		add_action('wp_ajax_CSV_upload_form', array($this, 'ajax_save_category_fields'));
		add_filter('manage_edit-dropship_supplier_columns', array($this, 'manage_columns'), 10, 1);
		add_action('manage_dropship_supplier_custom_column', array($this, 'column_content'), 10, 3);
		add_filter('woocommerce_get_sections_email', array($this, 'add_settings_tab'), 50);
		add_action('woocommerce_settings_email', array($this, 'dropship_manager_settings_tab'), 10, 1);
		add_action('woocommerce_settings_save_email', array($this, 'update_settings'));
		add_action('admin_menu', array($this, 'my_remove_menu_pages'));
		add_action('admin_menu', array($this, 'dropshipper_order_list_page'));
		add_action('wp_ajax_dropshipper_shipping_info_edited', array($this, 'dropshipper_shipping_info_edited_callback'));
		add_filter(
			'woocommerce_order_item_get_formatted_meta_data',
			array($this, 'mobilefolk_order_item_get_formatted_meta_data'),
			10,
			1
		);

		// register the ajax action or generate api key callback function

		add_action('wp_ajax_email_ali_api_key', array($this, 'email_ali_api_key'));
		add_action('wp_ajax_nopriv_email_ali_api_key', array($this, 'email_ali_api_key'));
		add_action('wp_ajax_hide_cbe_message', array($this, 'hide_cbe_message'));
	}

	public function hide_cbe_message()
	{
		update_option('cbe_hideoption', 'yes'); //here

	}

	// ajax function for generate api key callback function

	public function email_ali_api_key()
	{
		$input = site_url();

		$input = trim($input, '/');

		if (!preg_match('#^http(s)?://#', $input)) {
			$input = 'http://' . $input;
		}

		$urlParts = parse_url($input);
		$domain = preg_replace('/^www\./', '', $urlParts['host']);
		$aliexpresskey = generate_aliexpress_key($domain);
		$admin_email = get_bloginfo('admin_email');

		$to = $admin_email;
		$subject = 'Your AliExpress Key';
		$message = "Your aliexpress key for " . $domain . " is: " . $aliexpresskey;

		wp_mail($to, $subject, $message);

		wp_die($aliexpresskey);
	}

	public function mobilefolk_order_item_get_formatted_meta_data($formatted_meta)
	{
		$options = get_option('wc_dropship_manager');

		if (isset($options['hide_suppliername'])) {
			$hide_suppliername = $options['hide_suppliername'];
		} else {
			$hide_suppliername = '';
		}

		if ($hide_suppliername == '1') {
			$temp_metas = [];

			foreach ($formatted_meta as $key => $meta) {
				if (isset($meta->key) && !in_array($meta->key, ['supplier'])) {
					$temp_metas[$key] = $meta;
				}
			}

			return $temp_metas;
		} else {
			return $formatted_meta;
		}
	}

	public function admin_styles()
	{
		$base_name = explode('/', plugin_basename(__FILE__));

		wp_enqueue_script(
			'wc_dropship_manager_scripts',
			plugins_url() . '/' . $base_name[0] . '/assets/js/wc_dropship_manager.js',
			array('jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip')
		);

		wp_enqueue_script('jquery-tiptip', plugins_url() . '/woocommerce/assets/js/jquery-tiptip/jquery.tipTip.min.js', array('jquery'), true);

		wp_enqueue_style('woocommerce_admin_styles', plugins_url() . '/woocommerce/assets/css/admin.css', array());
	}

	public function my_admin_scripts()
	{
		$base_name = explode('/', plugin_basename(__FILE__));

		if(array_key_exists("success", $_GET) && trim($_GET['success']) == 'no'){
			wp_enqueue_script('my-jquery-min-script', plugins_url() . '/' . $base_name[0] . '/assets/js/jquery.min.js', array('jquery'), true);
			wp_enqueue_script('popper.min.js.map', plugins_url() . '/' . $base_name[0] . '/assets/js/popper.min.js', array('jquery'), true);
			wp_enqueue_script('my-bootstrap-script', plugins_url() . '/' . $base_name[0] . '/assets/js/bootstrap.min.js', array('jquery'), true);
			wp_enqueue_script('my-custom-script', plugins_url() . '/' . $base_name[0] . '/assets/js/custom-modal.js', array('jquery'), true);
		} else {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script("jquery-ui-datepicker");
			wp_enqueue_script("jquery-blockui");
			wp_enqueue_script("jquery-ui-sortable");
			wp_enqueue_script("jquery-ui-widget");
			wp_enqueue_script("jquery-ui-core");
			wp_enqueue_script("jquery-tiptip");
			wp_enqueue_script("jquery-ui-dialog");
			wp_enqueue_script('my-great-script', plugins_url() . '/' . $base_name[0] . '/assets/js/myscript.js', array('jquery'), '1.0.1', true);
		}
	}

	public function dropshipper_shipping_info_edited_callback()
	{
		global $wpdb;

		if (isset($_POST['id']) && isset($_POST['info'])) {
			$id = intval($_POST['id']);

			$info = $_POST['info'];

			update_post_meta($_POST['id'], 'dropshipper_shipping_info_' . get_current_user_id(), $info);

			echo 'true';
		} else {
			echo 'false';
		}

		die(); // this is required to return a proper result

	}

	public function manage_columns($cols)
	{
		unset($cols['description']);
		unset($cols['slug']);
		unset($cols['posts']);
		//$cols['account_number'] = 'Account Number';
		$cols['order_email_addresses'] = 'Email Addresses';
		$cols['inventory'] = '';
		$cols['posts'] = 'Count';

		return $cols;
	}

	/*********************************************************************/
	/*	For Create supplier 											 *
	/*********************************************************************/



	public function column_content($blank, $column_name, $term_id)
	{
		$ds = wc_dropshipping_get_dropship_supplier(intval($term_id));

		switch ($column_name) {
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
				echo '<p><a title="Import ' . $ds['name'] . '&apos;s Inventory Status in .CSV Format" href="' . admin_url('admin-ajax.php') . '?action=get_CSV_upload_form&width=600&height=350&term_id=' . $term_id . '" class="thickbox button-primary csvwindow" term_id="' . $term_id . '" >Import Inventory .CSV</a></p>';

				break;
		}
	}

	public function get_dropship_supplier_fields()
	{
		$meta = array(

			'account_number' => '',

			//'supplier_price' => '',

			'order_email_addresses' => '',
			'csv_delimiter' => ',',
			'csv_column_indicator' => '',
			'csv_column_sku' => '',
			'csv_column_qty' => '',
			'csv_type' => '',
			'csv_quantity' => '',
			'csv_indicator_instock' => '',
		);

		return $meta;
	}

	public function add_category_fields()
	{
		$meta = $this->get_dropship_supplier_fields();

		$this->display_add_form_fields($meta);
	}

	public function edit_category_fields($term, $taxonomy)
	{
		$meta = get_term_meta($term->term_id, 'meta', true);

		$this->display_edit_form_fields($meta);
	}

	//
	// Menu options : Products > Suppliers > Add New Dropshipping Supplier
	//

	public function display_add_form_fields($data)
	{
		add_thickbox();

		echo '<div class="form-field term-account_number-wrap">
				<label for="account_number" >Account #</label>
				<input type="text" size="40" name="account_number" value="' . $data['account_number'] . '" />
				<p>Your store&apos;s account number with this supplier. Leave blank if you don&apos;t have an account number</p>
			</div>
			<div class="form-field term-order_email_addresses-wrap">
				<label for="order_email_addresses" >Email Addresses</label>
				<input type="text" size="40" name="order_email_addresses" value="' . $data['order_email_addresses'] . '" required />
				<p>When a customer purchases a product from you, the supplier will be sent an notification via email. List the supplier&apos;s email addresses that should be notified when a new order is placed.<p>
			</div>';
	}

	//
	// Menu options : Products > Suppliers > Edit Dropshipping Supplier
	//

	public function display_edit_form_fields($data)
	{
		$csv_types = array('quantity' => 'Quantity on Hand', 'indicator' => 'In-Stock Indicator');

		echo '<tr class="term-account_number-wrap">
						<th><label for="account_number" >Account #</label></th>
						<td><input type="text" size="40" name="account_number" value="' . $data['account_number'] . '" />
						<p>Your store&apos;s account number with this supplier. Leave blank if you don&apos;t have an account number</p></td>
					</tr>

					<tr  class="term-order_email_addresses-wrap">
						<th><label for="order_email_addresses" >Email Addresses</label></th>
						<td><input type="text" size="40" name="order_email_addresses" value="' . $data['order_email_addresses'] . '" required />
						<p>When a customer purchases a product from you, the supplier will be sent an notification via email. List the supplier&apos;s email addresses that should be notified when a new order is placed.<p></td>
					</tr>
				</table>

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

		foreach ($csv_types as $csv_type => $description) {
			$selected = '';

			if ($data['csv_type'] === $csv_type) {
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

	/*public function cloneUserRole()	{
		 global $wp_roles;
		 if (!isset($wp_roles))
		 $wp_roles = new WP_Roles();
		 $adm = $wp_roles->get_role('subscriber');
		 // Adding a new role with all admin caps.
		 $wp_roles->add_role('dropshipper', 'Dropshipper', $adm->capabilities);
	}*/

	public function my_remove_menu_pages()
	{
		global $user_ID;
		$user = wp_get_current_user();
		if ( in_array( 'dropshipper', (array) $user->roles ) ) {

			remove_menu_page('edit-comments.php');
			remove_menu_page('index.php');
			remove_menu_page('link-manager.php'); // Links
			remove_menu_page('posts.php');
			remove_menu_page('edit.php');
			remove_menu_page('edit.php?post_type=elementor_library'); // Elementor
			remove_menu_page('elementor'); // Elementor
			//remove_menu_page('Posts.php');
			remove_menu_page('tools.php'); // Tools
			remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); //Quick Press widget
			remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side'); //Recent Drafts
			remove_meta_box('dashboard_primary', 'dashboard', 'side'); //WordPress.com Blog
			remove_meta_box('dashboard_secondary', 'dashboard', 'side'); //Other WordPress News
			remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal'); //Incoming Links
			remove_meta_box('dashboard_plugins', 'dashboard', 'normal'); //Plugins
			remove_meta_box('dashboard_right_now', 'dashboard', 'normal'); //Right Now
			remove_meta_box('rg_forms_dashboard', 'dashboard', 'normal'); //Gravity Forms
			remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); //Recent Comments
			remove_meta_box('icl_dashboard_widget', 'dashboard', 'normal'); //Multi Language Plugin
			remove_meta_box('dashboard_activity', 'dashboard', 'normal'); //Activity
			remove_meta_box('e-dashboard-overview', 'dashboard', 'normal'); // Elementor Activity

		}
	}

	public function dropshipper_order_list_page()
	{
		global $user_ID;

		$user = wp_get_current_user();
		if ( in_array( 'dropshipper', (array) $user->roles ) ) {
			$page_title = 'Order Lists';
			$menu_title = 'Order List';
			$capability = 'dropshipper';
			$menu_slug  = 'dropshipper-order-list';
			$function   = 'dropshipper_order_list';
			$icon_url   = 'dashicons-media-code';

			add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function);
		}
	}

	public function save_category_fields($term_id, $tt_id, $taxonomy)
	{
		$options = get_option('wc_dropship_manager');


		if(isset($options['email_supplier'])){
			$email_supplier = $options['email_supplier'];
		}



		// check for uploaded csv

		if (count($_FILES) > 0 && $_FILES['csv_file']['error'] == 0) {



			// we are saving an inventory form submit

			do_action('wc_dropship_manager_parse_csv');
		} else {
			if ($taxonomy == 'dropship_supplier') {

				// do update

				$meta = $this->get_dropship_supplier_fields();

				foreach ($meta as $key => $val) {
					if (isset($_POST[$key])) $meta[$key] = $_POST[$key];
				}

				$cterm = update_term_meta($term_id, 'meta', $meta);
			}

			/*Create New User When Create Term*/

			if ($cterm != '' && $taxonomy == 'dropship_supplier') {
				$username = @$_POST['tag-name'];

				$email = @$_POST['order_email_addresses'];

				/*$password = wp_generate_password();*/

				$the_user = get_user_by('email', $email);
				$user_id = @$the_user->ID;

				update_user_meta($user_id, 'supplier_id', $term_id);

				if (!empty($username) && !empty($email) && !$user_id && email_exists($email) == false) {
					$random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
					$user_id = wp_create_user($username, $random_password, $email);
					update_user_meta($user_id, 'supplier_id', $term_id);
					$user_id_role = new WP_User($user_id);
					$user_id_role->set_role('dropshipper');
					$loginurl = wp_login_url();
					/*Send User Password*/

					if ( isset( $email_supplier ) ) {
						if ($email_supplier == '1') {
							$to = $email;
							$subject = 'Registration Detail';
							$from = get_option('admin_email');

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

						//$message .= '<p style="disply:none">Your Email:&nbsp;'. $email .'</p>';
							$message .= '<p>Your User Name:&nbsp;' . $user_id_role->display_name . '</p>';
							$message .= '<p>Your Password:&nbsp;' . $random_password . '</p>';
							$message .= '<p>Change Your Password Once you login</p>';
							$message .= '<p>Login URL: '.$loginurl.'</p>';
							$message .= '</body></html>';

							wp_mail($to, $subject, $message, $headers);

						//mail($to, $subject, $message, $headers);

						}
					}
				} else {
					$random_password = __('User already exists.  Password inherited.');
				}
			}
		}
	}

	public function ajax_save_category_fields()
	{
		$this->save_category_fields($_POST['term_id'], '', $_POST['taxonomy']);

		if (defined('DOING_AJAX') && DOING_AJAX) {
			wp_die();
		}
	}

	/* Order term when created (put in position 0). */



	public function create_term($term_id, $tt_id = '', $taxonomy = '')
	{
		if ($taxonomy != 'dropship_supplier' && !taxonomy_is_product_attribute($taxonomy)) return;
		$meta_name = taxonomy_is_product_attribute($taxonomy) ? 'order_' . esc_attr($taxonomy) : 'order';
		update_term_meta($term_id, $meta_name, 0);
	}

	/* When a term is deleted, delete its meta. */



	public function delete_term($term_id, $taxonomy = '')
	{
		if ($taxonomy != 'dropship_supplier' && !taxonomy_is_product_attribute($taxonomy)) return;
		$meta_name = taxonomy_is_product_attribute($taxonomy) ? 'order_' . esc_attr($taxonomy) : 'order';
		$term_id = (int)$term_id;
		update_term_meta($term_id, $meta_name, 0);

		if (!$term_id) return;
		global $wpdb;
		$wpdb->query("DELETE FROM {$wpdb->termmeta} WHERE `term_id` = " . $term_id);
	}

	/* Admin Settings Area */



	public function add_settings_tab($settings_tabs)
	{
		$settings_tabs['dropship_manager'] = __('Dropshipping Notifications', 'woocommerce-dropshipping');

		return $settings_tabs;
	}

	public function dropship_manager_settings_tab()
	{
		global $current_section;

		if ($current_section == 'dropship_manager') {
			$this->display_settings();
		}
	}

	public function update_settings()
	{
		global $current_section;

		if ($current_section == 'dropship_manager') {
			$options = get_option('wc_dropship_manager');

			foreach ($_POST as $key => $opt) {
				if ($key != 'submit') $options[$key] = $_POST[$key];
			}

			if (isset($_POST['supp_notification'])) {
				$options['supp_notification'] = '1';
			} else {
				$options['supp_notification'] = '0';
			}

			if (isset($_POST['csv_inmail'])) {
				$options['csv_inmail'] = '1';
			} else {
				$options['csv_inmail'] = '0';
			}

			if (isset($_POST['billing_phone'])) {
				$options['billing_phone'] = '1';
			} else {
				$options['billing_phone'] = '0';
			}

			if (isset($_POST['email_supplier'])) {
				$options['email_supplier'] = '1';
			} else {
				$options['email_supplier'] = '0';
			}

			if (isset($_POST['hide_suppliername'])) {
				$options['hide_suppliername'] = '1';
			} else {
				$options['hide_suppliername'] = '0';
			}

			if (isset($_POST['hide_suppliername_on_product_page'])) {
				$options['hide_suppliername_on_product_page'] = '1';
			} else {
				$options['hide_suppliername_on_product_page'] = '0';

			}

			if (isset($_POST['hideorderdetail_suppliername'])) {
				$options['hideorderdetail_suppliername'] = '1';
			} else {
				$options['hideorderdetail_suppliername'] = '0';
			}

			if (isset($_POST['full_information'])) {
				$options['full_information'] = '1';
			} else {
				$options['full_information'] = '0';
			}

			if (isset($_POST['show_logo'])) {
				$options['show_logo'] = '1';
			} else {
				$options['show_logo'] = '0';
			}

			if (isset($_POST['order_date'])) {
				$options['order_date'] = '1';
			} else {
				$options['order_date'] = '0';
			}

			if (isset($_POST['smtp_check'])) {
				$options['smtp_check'] = '1';
			} else {
				$options['smtp_check'] = '0';
			}

			if (isset($_POST['std_mail'])) {
				$options['std_mail'] = '1';
			} else {
				$options['std_mail'] = '0';
			}

			if (isset($_POST['checkout_order_number'])) {
				$options['checkout_order_number'] = '1';
			} else {
				$options['checkout_order_number'] = '0';
			}

			if (isset($_POST['show_pay_type'])) {
				$options['show_pay_type'] = '1';
			} else {
				$options['show_pay_type'] = '0';
			}

			if (isset($_POST['cnf_mail'])) {
				$options['cnf_mail'] = '1';
			} else {
				$options['cnf_mail'] = '0';
			}

			if (isset($_POST['cc_mail'])) {
				$options['cc_mail'] = '1';
			} else {
				$options['cc_mail'] = '0';
			}

			if (isset($_POST['from_name'])) {
				$options['from_name'] = $_POST['from_name'];
			} else {
				$options['from_name'] = '';
			}

			if (isset($_POST['from_email'])) {
				$options['from_email'] = $_POST['from_email'];
			} else {
				$options['from_email'] = '';
			}

			if (isset($_POST['hide_shipping_price'])) {
				$options['hide_shipping_price'] = '1';
			} else {
				$options['hide_shipping_price'] = '0';
			}

			if (isset($_POST['hide_tax'])) {
				$options['hide_tax'] = '1';
			} else {
				$options['hide_tax'] = '0';
			}

			if (isset($_POST['total_price'])) {
				$options['total_price'] = '1';
			} else {
				$options['total_price'] = '0';
			}

			if (isset($_POST['product_price'])) {
				$options['product_price'] = '1';
			} else {
				$options['product_price'] = '0';
			}

			if (isset($_POST['shipping'])) {
				$options['shipping'] = '1';
			} else {
				$options['shipping'] = '0';
			}

			if (isset($_POST['payment_method'])) {
				$options['payment_method'] = '1';
			} else {
				$options['payment_method'] = '0';
			}

			if (isset($_POST['cost_of_goods'])) {
				$options['cost_of_goods'] = '1';
			} else {
				$options['cost_of_goods'] = '0';
			}

			if (isset($_POST['billing_address'])) {
				$options['billing_address'] = '1';
			} else {
				$options['billing_address'] = '0';
			}

			if (isset($_POST['shipping_address'])) {
				$options['shipping_address'] = '1';
			} else {
				$options['shipping_address'] = '0';
			}

			if (isset($_POST['product_image'])) {
				$options['product_image'] = '1';
			} else {
				$options['product_image'] = '0';
			}

			if (isset($_POST['store_name'])) {
				$options['store_name'] = '1';
			} else {
				$options['store_name'] = '0';
			}

			if (isset($_POST['store_address'])) {
				$options['store_address'] = '1';
			} else {
				$options['store_address'] = '0';
			}

			if (isset($_POST['complete_email'])) {
				$options['complete_email'] = '1';
			} else {
				$options['complete_email'] = '0';
			}

			if (isset($_POST['order_complete_link'])) {
				$options['order_complete_link'] = '1';
			} else {
				$options['order_complete_link'] = '0';
			}

			if (isset($_POST['type_of_package'])) {
				$options['type_of_package'] = '1';
			} else {
				$options['type_of_package'] = '0';
			}

			if (isset($_POST['customer_note'])) {
				$options['customer_note'] = '1';
			} else {
				$options['customer_note'] = '0';
			}

			// Aliexpress Settings get POST
			if (isset($_POST['ali_cbe_enable_name'])) {
				$options['ali_cbe_enable_name'] = '1';
			} else {
				$options['ali_cbe_enable_name'] = '0';
			}

			if (isset($_POST['ali_cbe_price_rate_name'])) {
				$options['ali_cbe_price_rate_name'] = $_POST['ali_cbe_price_rate_name'];
			} else {
				$options['ali_cbe_price_rate'] = '';
			}

			if (isset($_POST['ali_cbe_price_rate_value_name'])) {
				if($options['ali_cbe_price_rate_value_name'] < 1 || !is_numeric($options['ali_cbe_price_rate_value_name'])){
					$options['ali_cbe_price_rate_value_name'] = 0;
				}else{
					$options['ali_cbe_price_rate_value_name'] = $_POST['ali_cbe_price_rate_value_name'];
				}
			} else {
				$options['ali_cbe_price_rate_value_name'] = 0;
			}

			update_option('wc_dropship_manager', $options);
		}
	}

	public function display_settings()
	{
		// Tab to update options

		$options = get_option('wc_dropship_manager');

		if (isset($options['supp_notification'])) {
			$supp_notification = $options['supp_notification'];
		} else {
			$supp_notification = '';
		}

		if (isset($options['csv_inmail'])) {
			$csvcheck = $options['csv_inmail'];
		} else {
			$csvcheck = '';
		}

		if ( isset($_POST['packing_slip_header']) ) {
			if ( '' != $_POST['packing_slip_header'] ) {
				$options['packing_slip_header'] = $_POST['packing_slip_header'];
			} else {
				$options['packing_slip_header'] = '';
			}
		}

		if (isset($options['full_information'])) {
			$full_information = $options['full_information'];
		} else {
			$full_information = '';
		}

		if (isset($options['show_logo'])) {
			$show_logo = $options['show_logo'];
		} else {
			$show_logo = '';
		}

		if (isset($options['order_date'])) {
			$order_date = $options['order_date'];
		} else {
			$order_date = '';
		}

		if (isset($options['smtp_check'])) {
			$smtp_check = $options['smtp_check'];
		} else {
			$smtp_check = '';
		}

		if (isset($options['std_mail'])) {
			$std_mail = $options['std_mail'];
		} else {
			$std_mail = '';
		}

		if (isset($options['checkout_order_number'])) {
			$checkout_order_number = $options['checkout_order_number'];
		} else {
			$checkout_order_number = 0;
		}

		if (isset($options['show_pay_type'])) {
			$show_pay_type = $options['show_pay_type'];
		} else {
			$show_pay_type = '';
		}

		if (isset($options['cnf_mail'])) {
			$cnf_mail = $options['cnf_mail'];
		} else {
			$cnf_mail = '';
		}

		if (isset($options['cc_mail'])) {
			$cc_mail = $options['cc_mail'];
		} else {
			$cc_mail = '';
		}

		if (isset($options['from_name'])) {
			$from_name = $options['from_name'];
		} else {
			$from_name = '';
		}

		if (isset($options['from_email'])) {
			$from_email = $options['from_email'];
		} else {
			$from_email = '';
		}

		if (isset($options['hide_shipping_price'])) {
			$hide_shipping_price = $options['hide_shipping_price'];
		} else {
			$hide_shipping_price = '';
		}

		if (isset($options['hide_tax'])) {
			$hide_tax = $options['hide_tax'];
		} else {
			$hide_tax = '';
		}

		if (isset($options['total_price'])) {
			$total_price = $options['total_price'];
		} else {
			$total_price = '';
		}

		if (isset($options['product_price'])) {
			$product_price = $options['product_price'];
		} else {
			$product_price = '';
		}

		if (isset($options['shipping'])) {
			$shipping = $options['shipping'];
		} else {
			$shipping = '';
		}

		if (isset($options['payment_method'])) {
			$payment_method = $options['payment_method'];
		} else {
			$payment_method = '';
		}

		if (isset($options['cost_of_goods'])) {
			$cost_of_goods = $options['cost_of_goods'];
		} else {
			$cost_of_goods = '';
		}

		if (isset($options['billing_address'])) {
			$billing_address = $options['billing_address'];
		} else {
			$billing_address = '';
		}

		if (isset($options['billing_phone'])) {
			$billing_phone = $options['billing_phone'];
		} else {
			$billing_phone = '';
		}

		if (isset($options['email_supplier'])) {
			$email_supplier = $options['email_supplier'];
		} else {
			$email_supplier = '';
		}

		if (isset($options['hide_suppliername'])) {
			$hide_suppliername = $options['hide_suppliername'];
		} else {
			$hide_suppliername = '';
		}

		if (isset($options['hide_suppliername_on_product_page'])) {
			$hide_suppliername_on_product_page = $options['hide_suppliername_on_product_page'];
		} else {
			$hide_suppliername_on_product_page = '';
		}

		if (isset($options['hideorderdetail_suppliername'])) {
			$hideorderdetail_suppliername = $options['hideorderdetail_suppliername'];
		} else {
			$hideorderdetail_suppliername = '';
		}

		if (isset($options['shipping_address'])) {
			$shipping_address = $options['shipping_address'];
		} else {
			$shipping_address = '';
		}

		if (isset($options['product_image'])) {
			$product_image = $options['product_image'];
		} else {
			$product_image = '';
		}

		if (isset($options['store_name'])) {
			$store_name = $options['store_name'];
		} else {
			$store_name = '';
		}

		if (isset($options['store_address'])) {
			$store_address = $options['store_address'];
		} else {
			$store_address = '';
		}

		if (isset($options['complete_email'])) {
			$complete_email = $options['complete_email'];
		} else {
			$complete_email = '';
		}

		if (isset($options['order_complete_link'])) {
			$order_complete_link = $options['order_complete_link'];
		} else {
			$order_complete_link = '';
		}

		if (isset($options['type_of_package'])) {
			$type_of_package = $options['type_of_package'];
		} else {
			$type_of_package = '';
		}

		if (isset($options['customer_note'])) {
			$customer_note = $options['customer_note'];
		} else {
			$customer_note = '';
		}

		// Aliexpress Settings for setting variable creation
		if (isset($options['ali_cbe_enable_name'])) {
			$ali_cbe_enable_setting = $options['ali_cbe_enable_name'];
		} else {
			$ali_cbe_enable_setting = '';
		}

		if (isset($options['ali_cbe_price_rate_name'])) {
			if ($options['ali_cbe_price_rate_name'] == 'ali_cbe_price_rate_percent_offset') {
				$ali_cbe_price_rate_selected_1 = 'selected';
				$ali_cbe_price_rate_selected_2 = '';
			} else {
				$ali_cbe_price_rate_selected_1 = '';
				$ali_cbe_price_rate_selected_2 = 'selected';
			}
		}

		// if (isset($options['ali_cbe_price_rate_value_name'])) {
		// 	$ali_cbe_price_rate_value_setting = $options['ali_cbe_price_rate_value_name'];
		// } else {
		// 	$ali_cbe_price_rate_value_setting = '';
		// }

		// For Checked Checkbox

		if ($csvcheck == '1') {
			$csvInMail = ' checked="checked" ';
		} else {
			$csvInMail = ' ';
		}

		if ($supp_notification == '1') {
			$supp_notification_attr = ' checked="checked" ';
		} else {
			$supp_notification_attr = ' ';
		}

		if ($full_information == '1') {
			$checkfull = ' checked="checked" ';
		} else {
			$checkfull = ' ';
		}

		if ($show_logo == '1') {
			$logoshow = ' checked="checked" ';
		} else {
			$logoshow = ' ';
		}

		if ($order_date == '1') {
			$date_order = ' checked="checked" ';
		} else {
			$date_order = ' ';
		}

		if ($smtp_check == '1') {
			$check_smtp = ' checked="checked" ';
		} else {
			$check_smtp = ' ';
		}

		if ($std_mail == '1' || $std_mail == '') {
			$std_mail = ' checked="checked" ';
		} else {
			$std_mail = ' ';
		}

		if ($checkout_order_number == '1') {
			$checkout_order_number = ' checked="checked" ';
		} else {
			$checkout_order_number = ' ';
		}

		if ($show_pay_type == '1' || $show_pay_type == '') {
			$show_pay_type = ' checked="checked" ';
		} else {
			$show_pay_type = ' ';
		}

		if ($cnf_mail == '1') {
			$cnf_mail = ' checked="checked" ';
		} else {
			$cnf_mail = ' ';
		}

		if ($cc_mail == '1' || $cc_mail == '') {
			$cc_mail = ' checked="checked" ';
		} else {
			$cc_mail = ' ';
		}

		if ($hide_shipping_price == '1') {
			$hide_shipping_price = ' checked="checked" ';
		} else {
			$hide_shipping_price = ' ';
		}

		if ($hide_tax == '1') {
			$hide_tax = ' checked="checked" ';
		} else {
			$hide_tax = ' ';
		}

		if ($total_price == '1') {
			$total_price = ' checked="checked" ';
		} else {
			$total_price = ' ';
		}


		if ($product_price == '1') {
			$price_product = ' checked="checked" ';
		} else {
			$price_product = ' ';
		}

		if ($shipping == '1') {
			$product_shipping = ' checked="checked" ';
		} else {
			$product_shipping = ' ';
		}

		if ($payment_method == '1') {
			$method_payment = ' checked="checked" ';
		} else {
			$method_payment = ' ';
		}

		if ($cost_of_goods == '1' || $cost_of_goods == '') {
			$cost_of_goods = ' checked="checked" ';
		} else {
			$cost_of_goods = ' ';
		}

		if ($billing_address == '1') {
			$address_billing = ' checked="checked" ';
		} else {
			$address_billing = ' ';
		}

		if ($billing_phone == '1') {
			$phone_billing = ' checked="checked" ';
		} else {
			$phone_billing = ' ';
		}

		if ($email_supplier == '1') {
			$supplier_email = ' checked="checked" ';
		} else {
			$supplier_email = ' ';
		}

		if ($hide_suppliername == '1') {
			$suppliername_hide = ' checked="checked" ';
		} else {
			$suppliername_hide = ' ';
		}

		if ($hide_suppliername_on_product_page == '1') {
			$hide_suppliername_on_product_page = ' checked="checked" ';
		} else {
			$hide_suppliername_on_product_page = ' ';
		}

		if ($hideorderdetail_suppliername == '1') {
			$suppliername_hideorderdetail = ' checked="checked" ';
		} else {
			$suppliername_hideorderdetail = ' ';
		}

		if ($shipping_address == '1') {
			$address_shipping = ' checked="checked" ';
		} else {
			$address_shipping = ' ';
		}

		if ($product_image == '1') {
			$image_product = ' checked="checked" ';
		} else {
			$image_product = ' ';
		}

		if ($store_name == '1') {
			$name_store = ' checked="checked" ';
		} else {
			$name_store = ' ';
		}

		if ($store_address == '1') {
			$address_store = ' checked="checked" ';
		} else {
			$address_store = ' ';
		}

		if ($complete_email == '1') {
			$email_complete = ' checked="checked" ';
		} else {
			$email_complete = ' ';
		}

		if ($order_complete_link == '1') {
			$link_complete_order = ' checked="checked" ';
		} else {
			$link_complete_order = ' ';
		}

		if ($type_of_package == '1') {
			$type_of_package = ' checked="checked" ';
		} else {
			$type_of_package = ' ';
		}

		if ($customer_note == '1') {
			$customer_note = ' checked="checked" ';
		} else {
			$customer_note = ' ';
		}

		// Aliexpress Settings for checkbox value
		if ($ali_cbe_enable_setting == '1') {
			$ali_cbe_enable_checkbox = ' checked="checked" ';
		} else {
			$ali_cbe_enable_checkbox = ' ';
		}

		if (isset($options['ali_cbe_price_rate_value_name'])){
			if($options['ali_cbe_price_rate_value_name'] < 1 || !is_numeric($options['ali_cbe_price_rate_value_name'])){
				$options['ali_cbe_price_rate_value_name'] = 0;
			}
		}

		$woocommerce_url = plugins_url() . '/woocommerce/';

		echo '<h3>AliExpress Chrome Browser Extension (CBE) Settings</h3>';
		echo '<table>
				<tr>
					<td><h4>Enable Support for the AliExpress CBE:</h4></td>
					<td>
						<span>
						<td><input name="ali_cbe_enable_name" type="checkbox" ' . $ali_cbe_enable_checkbox . ' /></td>
						</span>
					<td>
				</tr>
			</table>';
		if (isset($ali_cbe_enable_setting)){
			if ($ali_cbe_enable_setting == '1'){
				echo '<table>
						<tr>
						 	<td><h4>Generate AliExpress API Key: </h4></td>
							<td>
								<span>
								<button type="button" id="generate_ali_key" class="button-primary">Generate AliExpress API Key</button>
								</span>
							<td>
						</tr>
					</table>';

		echo '<table>
				<tr id="hide_key">
				 	<td id="ali_api_key"></td>
				</tr>
			</table>';


		echo '<table>
				<tr>
					<td><h4>Price Markup Method:</h4></td>
					<td><img class="help_tip" data-tip="This setting controls whether the prices listed for products on your WooCommerce store are marked up by a given percentage or by a fixed amount when compared to the AliExpress supplier&apos;s prices" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td>
					<td>
						<span>
							<td>
								<select name="ali_cbe_price_rate_name">
	  							<option value="ali_cbe_price_rate_percent_offset" '. @$ali_cbe_price_rate_selected_1 .'>Percentage Offset</option>
	  							<option value="ali_cbe_fixed_price_offset"'. @$ali_cbe_price_rate_selected_2 .'>Fixed Amount Offset</option>
								</select>

							</td>
						</span>
					<td>
				</tr>
			</table>';

			echo '<table>
					<tr>
						<td><h4>Markup Offset Value:</h4></td>
						<td><img class="help_tip" data-tip="This setting will either contain a percentage or fixed amount based on the chosen price markup method above" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td>
						<td>
							<span>
							<td><input name="ali_cbe_price_rate_value_name" value="' . @$options['ali_cbe_price_rate_value_name'] . '" size="5" /></td>
							</span>
						<td>
					</tr>
				</table>';
		}
	}
		echo '<h3>Supplier Email Notifications</h3>
			<p>When an order&apos;s status switches to processing, emails are sent to each supplier to notify them to ship their products. You can set a custom message for the suppliers in the box below to be included in these emails</p>
			<table>
				<tr>
					<td><label for="email_order_note">Email order note:</label></td>
					<td><img class="help_tip" data-tip="This note will appear on emails that suppliers will receive with your order notifications" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td>
					<td><textarea name="email_order_note" cols="45" >' . @$options['email_order_note'] . '</textarea></td>
				</tr>
			</table>';


		echo '<h3>.CSV File Inventory Update Settings</h3>
			<p>These options relate to how your store processes data imported from CSV spreadsheet files, if you receive them from your supplier</p>
			<table>
				<tr>
					<td><label for="inventory_pad">Inventory Padding:</label></td>
					<td><img class="help_tip" data-tip="If the supplier&apos;s stock falls below this number on an imported spreadsheet, the item will be considered out of stock in your store. <br>Set this to zero if you want to directly use the inventory numbers your supplier gives you, or higher if you want to ensure that they don&apos;t sell out of their products before you make a sale." src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td>
					<td><input name="inventory_pad" value="' . @$options['inventory_pad'] . '" size="1" /></td>
				</tr>

				<!--<tr>
					<td valign="top"><label for="url_product_feed">Url to product feed:</label></td>
					<td><img class="help_tip" data-tip="After updating the in-stock/out of stock status this url will be called to regenerate your product feed. <br />(Just leave blank if you don\'t have a product feed)" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td>
					<td>
						<input name="url_product_feed" value="' . @$options['url_product_feed'] . '" size="100" />
					</td>
				</tr>-->
			</table>';


		echo '<h3>Packing Slips</h3>
			<p>When the suppliers recieve the email notification to notify them to ship products, this plugin generates packing slips to be included with each order. These options control the information on the generated packing slips. <br />Talk to your supplier to make sure they print out and include the packing slips with each order so that your customers will see them</p>
			<table>
			<tr>
				<td><label for="packing_slip_header" >Packing Slip Title:</label></td>
				<td><img class="help_tip" data-tip="This will be the custom title of the packing slip" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td>
				<td><input name="packing_slip_header" value="' . @$options['packing_slip_header'] . '" size="100" /></td>
			</tr>
				<tr>
					<p><b>NOTE:</b> For best results, please keep logo dimensions within 200x60 px</p>
					<td><label for="packing_slip_url_to_logo" >Company Logo URL:</label></td>
					<td><img class="help_tip" data-tip="Please specify the URL where your company&apos;s logo can be found" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td>
					<td><input name="packing_slip_url_to_logo" value="' . @$options['packing_slip_url_to_logo'] . '" size="75" /></td>
				</tr>

				<tr>
					<td><label for="packing_slip_url_to_logo_width" >Company Logo Width:</label></td>
					<td><img class="help_tip" data-tip="Please specify the width of your company logo in pixels" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td>
					<td><input name="packing_slip_url_to_logo_width" value="' . @$options['packing_slip_url_to_logo_width'] . '" size="1" /></td>
				</tr>

				<tr>
					<td><label for="packing_slip_company_name" >Company Name:</label></td>
					<td><img class="help_tip" data-tip="Please enter the name of your company" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td>
					<td><input name="packing_slip_company_name" value="' . @$options['packing_slip_company_name'] . '" size="30" /></td>
				</tr>

				<tr>
					<td><label for="packing_slip_address" >Address:</label></td>
					<td><img class="help_tip" data-tip="Please enter your company&apos;s mailing address. This address will appear on your packing slips" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td>
					<td><input name="packing_slip_address" value="' . @$options['packing_slip_address'] . '" size="50" /></td>
				</tr>

				<tr>
					<td><label for="packing_slip_customer_service_email" >Customer Service Email:</label></td>
					<td><img class="help_tip" data-tip="Please enter the email address at which customers can reach your company if they have service issues" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td>
					<td><input name="packing_slip_customer_service_email" value="' . @$options['packing_slip_customer_service_email'] . '" size="30" /></td>
				</tr>

				<tr>
					<td><label for="packing_slip_customer_service_phone">Customer Service Phone Number:</label></td>
					<td><img class="help_tip"  data-tip="Please enter the phone number at which customers can reach your company if they have service issues" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td>
					<td><input name="packing_slip_customer_service_phone" value="' . @$options['packing_slip_customer_service_phone'] . '" size="10" /></td>
				</tr><tr>

					<td ><label for="packing_slip_thankyou">Footer Message:</label></td>
					<td><img class="help_tip" data-tip="This message will appear at the bottom of the packing slip" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></td>
					<td><textarea name="packing_slip_thankyou" cols="45" >' . @$options['packing_slip_thankyou'] . '</textarea></td>
				</tr>
			</table>';

		echo '<h3>Packing Slip Verbiage</h3>
                 <p>These options control the wording of the labels which are used on the generated packing slip that is sent to your supplier.  This can be used to create non-English packing slips, or to change the wording used on the packing slips <br/>
                 The default terms are listed on the left side, and you can specify a custom term or phrase to replace them with. These terms will be used in all of your packing slips. If you leave the given boxes empty then the default terms will appear in packing slip.</p>
			<table>
				<tr>
					<p><b>NOTE:</b> For best results, please make sure that any custom terms or phrases listed below are kept to a reasonable legnth. If your terms are too long, it may cause text wrapping and alignment issues with your packing slips.</p>
					<td><label for="dropship_chosen_shipping_method" >Chosen Shipping Method:</label></td>
					<td><input name="dropship_chosen_shipping_method" value="' . @$options['dropship_chosen_shipping_method'] . '" size="30" maxlength="50" /></td>
				</tr>
				<tr>
					<td><label for="dropship_payment_type" >Payment Type:</label></td>
					<td><input name="dropship_payment_type" value="' . @$options['dropship_payment_type'] . '" size="30" maxlength="50"/></td>
				</tr>

				<tr>
					<td><label for="dropship_image" >Image:</label></td>
					<td><input name="dropship_image" value="' . @$options['dropship_image'] . '" size="30" maxlength="50" /></td>
				</tr>

				<tr>
					<td><label for="dropship_sku" >SKU:</label></td>
					<td><input name="dropship_sku" value="' . @$options['dropship_sku'] . '" size="30" maxlength="50" /></td>
				</tr>

				<tr>
					<td><label for="dropship_product" >Product:</label></td>
					<td><input name="dropship_product" value="' . @$options['dropship_product'] . '" size="30" maxlength="50" /></td>
				</tr>

				<tr>
					<td><label for="dropship_quantity">Quantity:</label></td>
					<td><input name="dropship_quantity" value="' . @$options['dropship_quantity'] . '" size="30" maxlength="50"/></td>
				</tr>

				<tr>
					<td><label for="type_of_package_conversion">Type Of Package:</label></td>
					<td><input name="type_of_package_conversion" value="' . @$options['type_of_package_conversion'] . '" size="30" maxlength="50"/></td>
				</tr>

				<tr>
					<td ><label for="dropship_price">Price:</label></td>
					<td><input name="dropship_price" value="' . @$options['dropship_price'] . '" size="30" maxlength="50" /></td>
				</tr>

				<tr>
					<td ><label for="dropship_company_address">Company Address:</label></td>
					<td><input name="dropship_company_address" value="' . @$options['dropship_company_address'] . '" size="30" maxlength="50" /></td>
				</tr>

				<tr>
					<td ><label for="dropship_billing_address_email">Billing Address:</label></td>
					<td><input name="dropship_billing_address_email" value="' . @$options['dropship_billing_address_email'] . '" size="30" maxlength="50"/></td>
				</tr>

				<tr>
					<td ><label for="dropship_shipping_address_email">Shipping Address:</label></td>
					<td><input name="dropship_shipping_address_email" value="' . @$options['dropship_shipping_address_email'] . '" size="30" maxlength="50" /></td>
				</tr>
			</table>';

		echo '<h3>Additional Comments</h3>
				<p>Max length: 200 characters</p>
				<p>
					<b>NOTE:</b>
					This message appears below the shipper&apos;s address near the bottom left corner of the packing slip, but above the footer message.  If you add too much text, it will not display properly.
				</p>
			<table>
				<tr>
					<td>
						<label for="dropship_additional_comment" >Comments:</label>
					</td>
					<td>
						<textarea name="dropship_additional_comment" maxlength="200" rows="4" cols="30">' . @$options['dropship_additional_comment'] . '</textarea>
					</td>
				</tr>
	        </table>';


		echo '<h3>Send Order Details to Suppliers</h3>';
		echo '<p></p>
			<table>
				<tr>
					<td><label for="supp_notification">Do not send email notifications to supplier:</label></td>
					<td><input name="supp_notification" type="checkbox" ' . $supp_notification_attr . ' /></td>
				</tr>
			</table>';

		echo '<p>This option controls whether or not you want to send a .CSV spreadsheet file as an attachment with the regular order notification emails that are sent to your suppliers</p>
			<table>
				<tr>
					<td><label for="csv_inmail">Send CSV with Supplier Notifications:</label></td>
					<td><input name="csv_inmail" type="checkbox" ' . $csvInMail . ' /></td>
				</tr>
			</table>';

		echo '<h3>Miscellaneous Packing Slip Options</h3>
			<table>
				<tr>
					<td><label for="full_information"><b>Send full order information as a PDF to your supplier to use as a packing slip:</b></label></td>
					<td><input name="full_information" class="fullinfo" type="checkbox" ' . $checkfull . ' /></td>
				</tr>
			</table>';

		echo '<div class="slidesection_bkp">
			<p></p>
			<table>
				<tr>
					<td><label for="show_logo">Include your company logo in the header section of the packing slip:</label></td>
					<td><input name="show_logo" type="checkbox" ' . $logoshow . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="order_date">Include order date in the packing slip:</label></td>
					<td><input name="order_date" type="checkbox" ' . $date_order . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="product_price">Show product prices in the packing slip:</label></td>
					<td><input name="product_price" type="checkbox" ' . $price_product . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="total_price">Show the total price in the packing slip:</label></td>
					<td><input name="total_price" type="checkbox" ' . $total_price . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="hide_shipping_price">Hide the shipping cost in the packing slip:</label></td>
					<td><input name="hide_shipping_price" type="checkbox" ' . $hide_shipping_price . ' /></td>
				</tr>
			</table>';

			echo '<p></p>
				<table>
					<tr>
						<td><label for="hide_tax">Hide Tax in supplier email:</label></td>
						<td><input name="hide_tax" type="checkbox" ' . $hide_tax . ' /></td>
					</tr>
				</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="shipping">Include shipping information in the packing slip:</label></td>
					<td><input name="shipping" type="checkbox" ' . $product_shipping . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="shipping">Include the customer&apos;s phone number in the packing slip:</label></td>
					<td><input name="billing_phone" type="checkbox" ' . $phone_billing . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="hidesuppliername">Hide the supplier names on order confirmation emails:</label></td>
					<td><input name="hide_suppliername" type="checkbox" ' . $suppliername_hide . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="hidesuppliername">Show supplier names on product pages:</label></td>
					<td><input name="hide_suppliername_on_product_page" type="checkbox" ' . $hide_suppliername_on_product_page . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="type_of_package">Include an additional field in the "Add/Edit Product" menu to specify the "Type of Package"
					<img class="help_tip" data-tip="This will also be added as an additional column in the packing slip" style="margin: 0 0 0 0px;" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16">: </label></td>
					<td><input name="type_of_package" type="checkbox" ' . $type_of_package . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="hideorderdetail_suppliername">Hide supplier names on the Order Details page:</label></td>
					<td><input name="hideorderdetail_suppliername" type="checkbox" ' . $suppliername_hideorderdetail . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="payment_method">Show shipping method:</label></td>
					<td><input name="payment_method" type="checkbox" ' . $method_payment . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="show_pay_type">Show "Payment Type" in the notification email:</label></td>

					<td><input name="show_pay_type" type="checkbox" ' . $show_pay_type . ' /></td>
				</tr>

			</table>';

		echo '<p></p>

			<table>
				<tr>
					<td><label for="customer_note">Include the "Customer Note" into the Dropshipper packing slip:</label></td>
					<td><input name="customer_note" type="checkbox" ' . $customer_note . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="cost_of_goods">Show "Cost of Goods"  instead of the store&apos;s actual "Selling Price" of products in the packing slip:</label></td>
					<td><input name="cost_of_goods" type="checkbox" ' . $cost_of_goods . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="billing_address">Show billing address at the bottom:</label></td>
					<td><input name="billing_address" type="checkbox" ' . $address_billing . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="shipping_address">Show shipping address at the bottom:</label></td>
					<td><input name="shipping_address" type="checkbox" ' . $address_shipping . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="product_image">Show product thumbnail image:</label></td>
					<td><input name="product_image" type="checkbox" ' . $image_product . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="store_name">Include store name in the order notification CSV filename:</label></td>
					<td><input name="store_name" type="checkbox" ' . $name_store . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="store_address">Include the store&apos;s URL in the order notification CSV filename:</label></td>
					<td><input name="store_address" type="checkbox" ' . $address_store . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="complete_email">Send an additional email to the supplier when the order is completed:</label></td>
					<td><input name="complete_email" type="checkbox" ' . $email_complete . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="order_complete_link">Allow suppliers to mark their orders as shipped by clicking a link on the email, without logging in to your store:</label></td>
					<td><input name="order_complete_link" type="checkbox" ' . $link_complete_order . ' /></td>
				</tr>
			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="sendemail">When an admin creates a new supplier, send registration details to the supplier&apos;s email:</label></td>
					<td><input name="email_supplier" type="checkbox" ' . $supplier_email . ' /></td>
				</tr>

			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="cnf_mail">Notify via email when suppliers open order notification emails
					<img class="help_tip" data-tip="A notification will be sent to your store when a supplier opens order notification emails that you send out." style="margin: 0 0 0 0px;" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16">: </label></td>
					<td><input name="cnf_mail" type="checkbox" ' . $cnf_mail . ' /></td>
				</tr>

			</table>';

		echo '<p></p>
			<table>
				<tr>
					<td><label for="std_mail">Use the standard WooCommerce mail format for email notification:</label></td>
					<td><input name="std_mail" type="checkbox" ' . $std_mail . ' /></td>
				</tr>
			</table>';

			echo '<p></p>
				<table>
					<tr>
						<td><label for="checkout_order_number">Include order number field on checkout</label></td>
						<td><input name="checkout_order_number" type="checkbox" ' . $checkout_order_number . ' /></td>
					</tr>
				</table>';
			echo '<p></p>
				<tr>
					<td><label for="cc_mail">Don&apos;t cc: the store admin when sending order notification emails to suppliers</label></td>
					<td><input name="cc_mail" type="checkbox" ' . $cc_mail . ' /></td>
				</tr>
			</table>';

		echo '<h3>SMTP Options</h3>
			<p></p>
			<table>
				<tr>
					<td><label for="smtp_check">Check this option if you are using SMTP to send emails from your WooCommerce store:</label></td>
					<td><input name="smtp_check" type="checkbox" ' . $check_smtp . ' /></td>
				</tr>
			</table>';

		echo '<h2>Email Sender Information (if left empty, emails sent from the store will use default WooCommerce settings)</h2>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="from_name">Emails sent from the store should show this sender name: <img class="help_tip"  data-tip="This option will override default functionality of woocommerce" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></label>
						</th>
						<td class="forminp forminp-text">
							<input name="from_name" id="from_name" type="text" size="30" value="' . $from_name . '" class="" placeholder="">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="from_email">Emails sent from the store should show this sender email address:<img class="help_tip"  data-tip="This option will override default WooCommerce functionality" src="' . $woocommerce_url . 'assets/images/help.png" height="16" width="16"></label>
						</th>
						<td class="forminp forminp-email">
							<input name="from_email" id="from_email" type="email" size="30" value="' . $from_email . '" class="" placeholder="" multiple="multiple">
							<input type="hidden" name="show_admin_notice_option" value="0" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>';
	}
}
