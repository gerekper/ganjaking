<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.redefiningtheweb.com
 * @since      1.0.0
 *
 * @package    Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai
 * @subpackage Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai
 * @subpackage Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai/admin
 * @author     RedefiningTheWeb <developer@redefiningtheweb.com>
 */
class Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $rtwwdpd_plugin_name    The ID of this plugin.
	 */
	private $rtwwdpd_plugin_name;

	public $rtwwdpd_set_rules;
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $rtwwdpd_version    The current version of this plugin.
	 */
	private $rtwwdpd_version;

	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $rtwwdpd_plugin_name       The name of this plugin.
	 * @param      string    $rtwwdpd_version    The version of this plugin.
	 */
	public function __construct( $rtwwdpd_plugin_name, $rtwwdpd_version ) {
	
		$this->rtwwdpd_plugin_name = $rtwwdpd_plugin_name;
		$this->rtwwdpd_version = $rtwwdpd_version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function rtwwdpd_enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the rtwwdpd_run() function
		 * defined in Woo_Dynamic_Pricing_Discounts_With_Ai_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Dynamic_Pricing_Discounts_With_Ai_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if(get_current_screen()->id == 'users' || get_current_screen()->id == 'edit-product' || get_current_screen()->id == 'product' || get_current_screen()->id == 'woocommerce_page_rtwwdpd'|| get_current_screen()->id == '%d9%88%d9%88%da%a9%d8%a7%d9%85%d8%b1%d8%b3_page_rtwwdpd' || get_current_screen()->id =='%d7%95%d7%95%d7%a7%d7%95%d7%9e%d7%a8%d7%a1_page_rtwwdpd')
		{
			if( get_current_screen()->id == 'woocommerce_page_rtwwdpd' ){

				wp_enqueue_style( "bootstrap", RTWWDPD_URL. 'assets/BootstrapDataTable/css/bootstrap.css', array(), $this->rtwwdpd_version, 'all' );
				// data table bootstrap css 
				wp_enqueue_style( "datatable-bootstrap", RTWWDPD_URL. 'assets/BootstrapDataTable/css/dataTables.bootstrap4.min.css', array(), $this->rtwwdpd_version, 'all' );
				// responsive bootstrap4 css
				wp_enqueue_style( "responsive-bootstrap4", RTWWDPD_URL. 'assets/BootstrapDataTable/css/responsive.bootstrap4.min.css', array(), $this->rtwwdpd_version, 'all' );
			}
			
			wp_enqueue_style( "select2", plugins_url( 'woocommerce/assets/css/select2.css' ), array(), $this->rtwwdpd_version, 'all' );
			wp_enqueue_style( $this->rtwwdpd_plugin_name, plugin_dir_url( __FILE__ ) . 'css/rtwwdpd-woo-dynamic-pricing-discounts-with-ai-admin.css', array(), $this->rtwwdpd_version, 'all' );
			wp_enqueue_style( 'woocommerce_admin_styles', plugins_url( 'woocommerce/assets/css/admin.css' ), array(), $this->rtwwdpd_version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function rtwwdpd_enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the rtwwdpd_run() function
		 * defined in Woo_Dynamic_Pricing_Discounts_With_Ai_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Dynamic_Pricing_Discounts_With_Ai_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if(get_current_screen()->id == 'users' || get_current_screen()->id == 'edit-product' || get_current_screen()->id == 'product' || get_current_screen()->id == 'woocommerce_page_rtwwdpd'|| get_current_screen()->id == '%d9%88%d9%88%da%a9%d8%a7%d9%85%d8%b1%d8%b3_page_rtwwdpd' || get_current_screen()->id =='%d7%95%d7%95%d7%a7%d7%95%d7%9e%d7%a8%d7%a1_page_rtwwdpd')
		{
			wp_enqueue_script( 'selectWoo', plugins_url( 'woocommerce/assets/js/selectWoo/selectWoo.full.min.js' ), array( 'jquery' ), $this->rtwwdpd_version, false );
			wp_enqueue_script( 'tipTip', plugins_url( 'woocommerce/assets/js/jquery-tiptip/jquery.tipTip.min.js' ), array( 'jquery' ), $this->rtwwdpd_version, false );

			wp_enqueue_script( 'wc-enhanced-select', plugins_url( 'woocommerce/assets/js/admin/wc-enhanced-select.min.js' ), array( 'jquery', 'selectWoo' ), $this->rtwwdpd_version, false );
			
			wp_enqueue_script( "datatable", RTWWDPD_URL. 'assets/Datatables/js/jquery.dataTables.min.js', array( 'jquery' ), $this->rtwwdpd_version, false );
			wp_enqueue_script( "datatable-responsive", RTWWDPD_URL. 'assets/Responsive_DT/js/dataTables.responsive.min.js', array( 'jquery' ), $this->rtwwdpd_version, false );
			// responsive-bootstrap4-js
			if(get_current_screen()->id == 'woocommerce_page_rtwwdpd'){

				wp_enqueue_script( "responsive-bootstrap4", RTWWDPD_URL. 'assets/BootstrapDataTable/js/responsive.bootstrap4.min.js', array( 'jquery' ), $this->rtwwdpd_version, false );
				// dataTables-bootstrap4-js
				wp_enqueue_script( "dataTables-bootstrap4", RTWWDPD_URL. 'assets/BootstrapDataTable/js/dataTables.bootstrap4.min.js', array( 'jquery' ), $this->rtwwdpd_version, false );
			}
			wp_enqueue_script( "select2", plugins_url( 'woocommerce/assets/js/select2/select2.full.min.js' ), array( 'jquery' ), $this->rtwwdpd_version, false );
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script( 'wc-enhanced-select' );
			wp_register_script( $this->rtwwdpd_plugin_name, plugin_dir_url( __FILE__ ) . 'js/rtwwdpd-woo-dynamic-pricing-discounts-with-ai-admin.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'wc-enhanced-select' ), $this->rtwwdpd_version, false );
			wp_enqueue_script( 'woocommerce_admin' );

			$rtwwdpd_ajax_nonce = wp_create_nonce( "rtwwdpd-ajax-seurity" );
			wp_localize_script($this->rtwwdpd_plugin_name, 'rtwwdpd_ajax', array( 'ajax_url' => esc_url(admin_url('admin-ajax.php')),
				'rtwwdpd_nonce' => $rtwwdpd_ajax_nonce));
			wp_enqueue_script( $this->rtwwdpd_plugin_name );

			wp_enqueue_script( "blockUI", plugins_url( 'woocommerce/assets/js/jquery-blockui/jquery.blockUI.min.js' ), array( 'jquery' ), $this->rtwwdpd_version, false );

			wp_enqueue_script( 'jquery.validate', RTWWDPD_URL . 'assets/jquery.validate/jquery.validate.js', array( 'jquery' ), $this->rtwwdpd_version, false );
		}
	}

	/**
	 * Function to add submenu in woocommerce menu tab.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_add_submenu()
	{
		add_submenu_page( 'woocommerce', esc_attr__( 'Woocommerce Dynamic Pricing & Discounts', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ), esc_html__( 'Woocommerce Dynamic Pricing & Discounts', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ), 'manage_woocommerce', 'rtwwdpd', array( $this, 'rtwwdpd_admin_setting' ) );
	}

	/**
	 * Function for display settings page.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_admin_setting()
	{
		include_once( RTWWDPD_DIR.'admin/partials/rtwwdpd-woo-dynamic-pricing-discounts-with-ai-admin-display.php');
	}

	/**
	 * Function to give setting on product edit page.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_variation($loop, $variation_data, $variation)
	{
		$rtwwdpd_variation = get_option('rtwwdpd_variation_rule');
		if(!empty($rtwwdpd_variation))
		{
			$rtwwdpd_variation_arr = array();

			foreach ($rtwwdpd_variation as $key => $value) {
				$rtwwdpd_variation_arr[$key] = $value['rtwwdpd_offer_name'];
			}
			$rtwwdpd_variation_arr 	= array_merge( array( '0' => 'Select Offer' ), $rtwwdpd_variation_arr );
			$rtwwdpd_variation_meta ='';

			if(get_post_meta( $variation->ID, 'rtwwdpd_variation' ) != '' && get_post_meta( $variation->ID, 'rtwwdpd_variation' ) != array())
			{
				$rtwwdpd_variation_meta = get_post_meta( $variation->ID, 'rtwwdpd_variation');
			}

			echo '<div class="form-row form-row-first">';
			if(isset($rtwwdpd_variation) && is_array($rtwwdpd_variation) && !empty($rtwwdpd_variation))
			{
				echo '<span class="rtwwdpd_variation_names"><b>Available Rules : ';
				foreach ($rtwwdpd_variation as $key => $value) {
					echo '['.$value['rtwwdpd_offer_name']. '], ';
				}
				echo '</b></span>';
			}
			woocommerce_wp_text_input( array(
					'id' => 'rtwwdpd_variation[' . $loop . ']',
					'class' => 'form-row short',
					'label' => esc_html__( 'Product Variation Offer : ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ),
					'value' => get_post_meta( $variation->ID, 'rtwwdpd_variation', true ),
					'description' 		=> esc_html__('Enter the above rule names in this textbox which you want to apply on this variation.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'),
					'desc_tip' 			=> true,
				)
			);
			
			echo '</div>';
		}
	}


	/**
	 * Function to save settings of edit product page.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_variation_save($variation_id, $i){
		
		if(isset($_POST['rtwwdpd_variation'][$i]))
		{
			$rtwwdpd_custom_field = $_POST['rtwwdpd_variation'][$i];
			
			if ( isset( $rtwwdpd_custom_field ) ) 
			{ 
				update_post_meta( $variation_id, 'rtwwdpd_variation', esc_attr( $rtwwdpd_custom_field ) );
			}
			
		}
		if(isset($_POST['custom_field'][$i]))
		{
			$rtwwdpd_custom_fields = $_POST['custom_field'][$i];
			if ( isset( $rtwwdpd_custom_fields ) ) 
			{ 
				update_post_meta( $variation_id, 'custom_field', esc_attr( $rtwwdpd_custom_fields ) );
			}
		}
	}

	/**
	 * Function to short products by price.
	 *
	 * @since    1.0.0
	 */
	public static function rtw_sort_by_price( $cart_item_a, $cart_item_b ) {
		return $cart_item_a['data']->get_price('edit') > $cart_item_b['data']->get_price('edit');
	}
	
	/**
	 * Function to update customer visit.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_update_customer_visit($rtwwdpd_user_login, $rtwwdpd_user) {
		$rtwwdpd_user_id = $rtwwdpd_user->ID;
		$rtwwdpd_meta_key = 'rtwwdpd_user_visit_count';
		$rtwwdpd_meta_value = 2;
		$rtwwdpd_array = get_user_meta($rtwwdpd_user_id, $key = '', $single = false);
		$rtwwdpd_meta_value = $rtwwdpd_array['rtwwdpd_user_visit_count'][0];
		$rtwwdpd_meta_value++;
		update_user_meta($rtwwdpd_user_id, $rtwwdpd_meta_key, $rtwwdpd_meta_value);
	}

	/**
	 * Function to add extra cloumn in user list table.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_new_colmn_user($rtwwdpd_columns)
	{
		$rtwwdpd_columns['rtw_plus_mem'] = 'Plus Member';
		return $rtwwdpd_columns;
	}

	/**
	 * Function to add extra cloumn in user list page.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_new_column_val( $rtwwdpd_column )
	{
		$rtwwdpd_column['rtw_plus_mem'] = 'Plus Member';
		return $rtwwdpd_column;
	}

	/**
	 * Function to check if a customer is plus member.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_user_data( $val, $rtwwdpd_column_name, $rtwwdpd_user_id )
	{
		$rtwwdpd_user_meta = get_user_meta($rtwwdpd_user_id, 'rtwwdpd_plus_member');
		$rtwwdpd_prev_opt = get_option('rtwwdpd_add_member');
		$rtwwdpd_user_data = get_userdata( $rtwwdpd_user_id );
		$rtwwdpd_today_date = current_time('Y-m-d');
		$rtwwdpd_registered_date = $rtwwdpd_user_data->user_registered;
		$rtwwdpd_user = wp_get_current_user();

		if($rtwwdpd_user_meta)
		{
			switch ($rtwwdpd_column_name) {
				case 'rtw_plus_mem' :
				if($rtwwdpd_user_meta[0]['check'] == 'checked')
				{
					return '<input class="rtw_plus_mem" value="'.$rtwwdpd_user_id.'" type="checkbox" checked="checked" name="rtw_plus_mem" />';
				}
				else{
					if(is_array($rtwwdpd_prev_opt) && !empty($rtwwdpd_prev_opt))
					{
						foreach ($rtwwdpd_prev_opt as $key => $value)
						{
							$rtwwdpd_no_oforders = wc_get_customer_order_count( $rtwwdpd_user_id);
							$rtwwdpd_args = array(
								'customer_id' => $rtwwdpd_user_id,
								'post_status' => 'cancelled',
								'post_type' => 'shop_order',
								'return' => 'ids',
							);
							$rtwwdpd_numordr_cancld = 0;
							$rtwwdpd_numordr_cancld = count( wc_get_orders( $rtwwdpd_args ) );
							$rtwwdpd_no_oforders = $rtwwdpd_no_oforders - $rtwwdpd_numordr_cancld;
							$rtwwdpd_ordrtotal = wc_get_customer_total_spent($rtwwdpd_user_id);
							$rtwwdpd_user_role = $value['rtwwdpd_roles'] ;
							if(is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
							{
								$rtwwdpd_role_matched = false;
								foreach ($rtwwdpd_user_role as $rol => $role) {
									if($role == 'all'){
										$rtwwdpd_role_matched = true;
									}
									if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
										$rtwwdpd_role_matched = true;
									}
								}
								if($rtwwdpd_role_matched == false)
								{
									return '<input class="rtw_plus_mem" value="'.$rtwwdpd_user_id.'" type="checkbox" name="rtw_plus_mem" />';
								}
							}

							if(isset($value['rtwwdpd_min_orders']) && $value['rtwwdpd_min_orders'] > $rtwwdpd_no_oforders)
							{
								return '<input class="rtw_plus_mem" value="'.$rtwwdpd_user_id.'" type="checkbox" name="rtw_plus_mem" />';
							}
							if(isset($value['rtwwdpd_purchase_amt']) && $value['rtwwdpd_purchase_amt'] > $rtwwdpd_ordrtotal)
							{
								return '<input class="rtw_plus_mem" value="'.$rtwwdpd_user_id.'" type="checkbox" name="rtw_plus_mem" />';
							}
							if(isset($value['rtwwdpd_purchase_prodt']) && $value['rtwwdpd_purchase_prodt'])
							{
								
							}
							if(isset($value['rtw_user_regis_for']))
							{
								$rtwtremnthbfre = date("d.m.Y", strtotime("-3 Months"));
								$rtwsixmnthbfre = date("d.m.Y", strtotime("-6 Months"));
								$rtwoneyrbfre = date("d.m.Y", strtotime("-1 Year"));

								if($value['rtw_user_regis_for'] == 'less3mnth')
								{
									if($rtwwdpd_registered_date < $rtwtremnthbfre)
									{
										return '<input class="rtw_plus_mem" value="'.$rtwwdpd_user_id.'" type="checkbox" name="rtw_plus_mem" />';
									}
								}
								elseif($value['rtw_user_regis_for'] == 'more3mnth')
								{
									if($rtwwdpd_registered_date > $rtwtremnthbfre)
									{
										return '<input class="rtw_plus_mem" value="'.$rtwwdpd_user_id.'" type="checkbox" name="rtw_plus_mem" />';
									}
								}
								elseif($value['rtw_user_regis_for'] == 'more6mnth')
								{
									if($rtwwdpd_registered_date > $rtwsixmnthbfre)
									{
										return '<input class="rtw_plus_mem" value="'.$rtwwdpd_user_id.'" type="checkbox" name="rtw_plus_mem" />';
									}
								}
								elseif ($value['rtw_user_regis_for'] == 'more1yr') 
								{
									if($rtwwdpd_registered_date > $rtwoneyrbfre)
									{
										return '<input class="rtw_plus_mem" value="'.$rtwwdpd_user_id.'" type="checkbox" name="rtw_plus_mem" />';
									}
								}
							}
							return '<input class="rtw_plus_mem" value="'.$rtwwdpd_user_id.'" checked="checked" type="checkbox" name="rtw_plus_mem" />';
						}
					}
					else{
						return '<input class="rtw_plus_mem" value="'.$rtwwdpd_user_id.'" type="checkbox" name="rtw_plus_mem" />';
					}
				}
				default:
			}
		}
		else{
			switch ($rtwwdpd_column_name) {
				case 'rtw_plus_mem' :
				return '<input class="rtw_plus_mem" value="'.$rtwwdpd_user_id.'" type="checkbox" name="rtw_plus_mem" />';
				default:
			}
		}
		return $val;
	}

	/**
	 * Function to update a customer is plus member.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_plus_member_callback()
	{
		if (!wp_verify_nonce($_POST['security_check'], 'rtwwdpd-ajax-seurity')){
			return;
		}
		$rtwwdpd_user_id = sanitize_text_field($_POST['user_id']);
		$rtwwdpd_checked = sanitize_text_field($_POST['checked']);
		$rtwwdpd_meta_val = array('check'=> $rtwwdpd_checked);
		update_user_meta($rtwwdpd_user_id, 'rtwwdpd_plus_member', $rtwwdpd_meta_val);
		$rtwwdpd_response = 'success';
		echo json_encode($rtwwdpd_response);
		die();
	}

	/**
	 * Function to check if plus member rule is enable.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_plus_enable_callback()
	{
		$rtwwdpd_checked = sanitize_text_field($_POST['enable']);
		update_option('rtwwdpd_plus_enable', $rtwwdpd_checked);
		echo json_encode('success');
		die();
	}

	/**
	 * Function to check if nth order rule is enable.
	 *
	 * @since    1.2.0
	 */
	function rtwwdpd_enable_nth_order_callback()
	{
		$rtwwdpd_checked = sanitize_text_field($_POST['enable']);
		update_option('rtwwdpd_enable_nth_order', $rtwwdpd_checked);
		echo json_encode('success');
		die();
	}
	
	/**
	 * Function to check if least free rule is enable.
	 *
	 * @since    2.0.0
	 */
	function rtwwdpd_enable_least_free_callback(){
		$rtwwdpd_checked = sanitize_text_field($_POST['enable']);
		update_option('rtwwdpd_enable_least_free', $rtwwdpd_checked);
		echo json_encode('success');
		die();
	}


	/**
	 * Function to check if specific customer rule is enable.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_enable_specific_callback()
	{
		$rtwwdpd_checked = sanitize_text_field($_POST['enable']);
		update_option('rtwwdpd_specific_enable', $rtwwdpd_checked);
		echo json_encode('success');
		die();
	}

	/**
	 * Function to check if next buy bonus rule is enable.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_enable_next_buy_bonus()
	{
		$rtwwdpd_checked = sanitize_text_field($_POST['enable']);
		update_option('rtwwdpd_next_buy', $rtwwdpd_checked);
		echo json_encode('success');
		die();
	}

	/**
	 * Function to update discount tables ordering.
	 *
	 * @since    1.0.0
	 */
	public function rtwwdpd_category_tbl_callback()
	{
		$rtwwdpd_tbl_nam = sanitize_text_field($_POST['table']);
		
		if($rtwwdpd_tbl_nam == 'category_tbl')
		{
			$rtwwdpd_products_option = get_option('rtwwdpd_single_cat_rule');
			$rtwwdpd_updated_array = array();
			foreach ($_POST['rtwarray'] as $key => $value) {
				$rtwwdpd_updated_array[sanitize_text_field($key)] = $rtwwdpd_products_option[sanitize_text_field($value)];
			}

			update_option('rtwwdpd_single_cat_rule', $rtwwdpd_updated_array);
		}
		elseif($rtwwdpd_tbl_nam == 'category_com_tbl')
		{
			$rtwwdpd_products_option = get_option('rtwwdpd_combi_cat_rule');
			$rtwwdpd_updated_array = array();
			foreach ($_POST['rtwarray'] as $key => $value) {
				$rtwwdpd_updated_array[sanitize_text_field($key)] = $rtwwdpd_products_option[sanitize_text_field($value)];
			}

			update_option('rtwwdpd_combi_cat_rule', $rtwwdpd_updated_array);
		}
		elseif($rtwwdpd_tbl_nam == 'prodct_tbl')
		{	
			$rtwwdpd_products_option = get_option('rtwwdpd_single_prod_rule');
			$rtwwdpd_updated_array = array();
			foreach ($_POST['rtwarray'] as $key => $value) {
				$rtwwdpd_updated_array[sanitize_text_field($key)] = $rtwwdpd_products_option[sanitize_text_field($value)];
			}

			update_option('rtwwdpd_single_prod_rule', $rtwwdpd_updated_array);
		}
		elseif($rtwwdpd_tbl_nam == 'prodct_com_tbl')
		{
			$rtwwdpd_products_option = get_option('rtwwdpd_combi_prod_rule');
			$rtwwdpd_updated_array = array();
			foreach ($_POST['rtwarray'] as $key => $value) {
				$rtwwdpd_updated_array[sanitize_text_field($key)] = $rtwwdpd_products_option[sanitize_text_field($value)];
			}

			update_option('rtwwdpd_combi_prod_rule', $rtwwdpd_updated_array);
		}
		elseif($rtwwdpd_tbl_nam == 'attr_tbl')
		{
			$rtwwdpd_products_option = get_option('rtwwdpd_att_rule');
			$rtwwdpd_updated_array = array();
			foreach ($_POST['rtwarray'] as $key => $value) {
				$rtwwdpd_updated_array[sanitize_text_field($key)] = $rtwwdpd_products_option[sanitize_text_field($value)];
			}

			update_option('rtwwdpd_att_rule', $rtwwdpd_updated_array);
		}
		elseif($rtwwdpd_tbl_nam == 'variation_tbl')
		{
			$rtwwdpd_products_option = get_option('rtwwdpd_variation_rule');
			$rtwwdpd_updated_array = array();
			foreach ($_POST['rtwarray'] as $key => $value) {
				$rtwwdpd_updated_array[sanitize_text_field($key)] = $rtwwdpd_products_option[sanitize_text_field($value)];
			}

			update_option('rtwwdpd_variation_rule', $rtwwdpd_updated_array);
		}
		elseif($rtwwdpd_tbl_nam == 'tier_pro_tbl')
		{
			$rtwwdpd_products_option = get_option('rtwwdpd_tiered_rule');
			$rtwwdpd_updated_array = array();
			foreach ($_POST['rtwarray'] as $key => $value) {
				$rtwwdpd_updated_array[sanitize_text_field($key)] = $rtwwdpd_products_option[sanitize_text_field($value)];
			}

			update_option('rtwwdpd_tiered_rule', $rtwwdpd_updated_array);
		}
		elseif($rtwwdpd_tbl_nam == 'tier_cat_tbl')
		{
			$rtwwdpd_products_option = get_option('rtwwdpd_tiered_cat');
			$rtwwdpd_updated_array = array();
			foreach ($_POST['rtwarray'] as $key => $value) {
				$rtwwdpd_updated_array[sanitize_text_field($key)] = $rtwwdpd_products_option[sanitize_text_field($value)];
			}

			update_option('rtwwdpd_tiered_cat', $rtwwdpd_updated_array);
		}
		elseif($rtwwdpd_tbl_nam == 'shipp_tbl')
		{
			$rtwwdpd_products_option = get_option('rtwwdpd_ship_method');
			$rtwwdpd_updated_array = array();
			foreach ($_POST['rtwarray'] as $key => $value) {
				$rtwwdpd_updated_array[sanitize_text_field($key)] = $rtwwdpd_products_option[sanitize_text_field($value)];
			}

			update_option('rtwwdpd_ship_method', $rtwwdpd_updated_array);
		}
		elseif($rtwwdpd_tbl_nam == 'pro_tag_tbl')
		{
			$rtwwdpd_products_option = get_option('rtwwdpd_tag_method');
			$rtwwdpd_updated_array = array();
			foreach ($_POST['rtwarray'] as $key => $value) {
				$rtwwdpd_updated_array[sanitize_text_field($key)] = $rtwwdpd_products_option[sanitize_text_field($value)];
			}

			update_option('rtwwdpd_tag_method', $rtwwdpd_updated_array);
		}
		elseif($rtwwdpd_tbl_nam == 'pay_tbl')
		{
			$rtwwdpd_products_option = get_option('rtwwdpd_pay_method');
			$rtwwdpd_updated_array = array();
			foreach ($_POST['rtwarray'] as $key => $value) {
				$rtwwdpd_updated_array[sanitize_text_field($key)] = $rtwwdpd_products_option[sanitize_text_field($value)];
			}

			update_option('rtwwdpd_pay_method', $rtwwdpd_updated_array);
		}
		elseif($rtwwdpd_tbl_nam == 'cart_tbl')
		{
			$rtwwdpd_products_option = get_option('rtwwdpd_cart_rule');
			$rtwwdpd_updated_array = array();
			foreach ($_POST['rtwarray'] as $key => $value) {
				$rtwwdpd_updated_array[sanitize_text_field($key)] = $rtwwdpd_products_option[sanitize_text_field($value)];
			}

			update_option('rtwwdpd_cart_rule', $rtwwdpd_updated_array);
		}
		elseif($rtwwdpd_tbl_nam == 'bogo_tbl')
		{
			$rtwwdpd_products_option = get_option('rtwwdpd_bogo_rule');
			$rtwwdpd_updated_array = array();
			foreach ($_POST['rtwarray'] as $key => $value) {
				$rtwwdpd_updated_array[sanitize_text_field($key)] = $rtwwdpd_products_option[sanitize_text_field($value)];
			}

			update_option('rtwwdpd_bogo_rule', $rtwwdpd_updated_array);
		}
		elseif($rtwwdpd_tbl_nam == 'bogo_cat_tbl')
		{
			$rtwwdpd_products_option = get_option('rtwwdpd_bogo_cat_rule');
			$rtwwdpd_updated_array = array();
			foreach ($_POST['rtwarray'] as $key => $value) {
				$rtwwdpd_updated_array[sanitize_text_field($key)] = $rtwwdpd_products_option[sanitize_text_field($value)];
			}

			update_option('rtwwdpd_bogo_cat_rule', $rtwwdpd_updated_array);
		}
		elseif($rtwwdpd_tbl_nam == 'specific_tbl')
		{
			$rtwwdpd_products_option = get_option('rtwwdpd_specific_c');
			$rtwwdpd_updated_array = array();
			foreach ($_POST['rtwarray'] as $key => $value) {
				$rtwwdpd_updated_array[sanitize_text_field($key)] = $rtwwdpd_products_option[sanitize_text_field($value)];
			}

			update_option('rtwwdpd_specific_c', $rtwwdpd_updated_array);
		}
		die;
	}

	/**
	 * Function to give discount on the next order of a customer.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_next_buy_bonus( $rtwwdpd_order_id )
	{
		$rtwwdpd_next_enable = get_option('rtwwdpd_next_buy');
		if( isset( $rtwwdpd_next_enable ) && $rtwwdpd_next_enable == 'enable' )
		{
			$rtwwdpd_cart_content = WC()->cart;
			$rtwwdpd_next_buy_option = get_option( 'rtwwdpd_next_buy_rule' );
			$rtwwdpd_user_id = get_current_user_id();
			$rtwwdpd_user_meta = get_user_meta( $rtwwdpd_user_id , 'rtwwdpd_next_buy_eligible' );
			$rtwwdpd_order = new WC_Order( $rtwwdpd_order_id );
			
			$rtwwdpd_order_total = $rtwwdpd_order->get_total();
			$rtwwdpd_order_subtotal = $rtwwdpd_order->get_subtotal();
			$rtwwdpd_user = wp_get_current_user();
			$rtwwdpd_today_date = current_time('Y-m-d');

			$rtwwdpd_no_oforders = wc_get_customer_order_count( $rtwwdpd_user_id );
			$rtwwdpd_user_id = $rtwwdpd_user_id;
			$rtwwdpd_args = array(
				'customer_id' => $rtwwdpd_user_id,
				'post_status' => 'cancelled',
				'post_type' => 'shop_order',
				'return' => 'ids',
			);

			$rtwwdpd_numordr_cancld = 0;
			$rtwwdpd_numordr_cancld = count( wc_get_orders( $rtwwdpd_args ) );
			$rtwwdpd_no_oforders = $rtwwdpd_no_oforders - $rtwwdpd_numordr_cancld;
			$rtwwdpd_ordrtotal = wc_get_customer_total_spent( $rtwwdpd_user_id );
			$remove_eligibility = false;
			foreach ( $rtwwdpd_next_buy_option as $next => $opt ) {

				$rtwwdpd_user_role = $opt['rtwwdpd_select_roles_com'] ;
				$rtwwdpd_role_matched = false;
				if( is_array( $rtwwdpd_user_role ) && !empty( $rtwwdpd_user_role ) )
				{
					foreach ( $rtwwdpd_user_role as $rol => $role ) {
						if( $role == 'all' ){
							$rtwwdpd_role_matched = true;
						}
						if ( in_array( $role, (array) $rtwwdpd_user->roles ) ) {
							$rtwwdpd_role_matched = true;
						}
					}
				}
				if( $rtwwdpd_role_matched == false )
				{
					continue 1;
				}

				$rtwwdpd_matched = true;
				if( $opt['rtwwdpd_combi_from_date'] > $rtwwdpd_today_date || $opt['rtwwdpd_combi_to_date'] < $rtwwdpd_today_date )
				{
					continue 1;
				}

				$rtwwdpd_opt_subtotl = isset( $opt['rtwwdpd_totl_sbtotl'] ) ? $opt['rtwwdpd_totl_sbtotl'] : 0;

				if( $rtwwdpd_opt_subtotl == 'rtw_sbtotl' )
				{
					if( $rtwwdpd_order_subtotal < $rtwwdpd_opt_subtotl )
					{
						continue 1;
					}
				}
				elseif( $rtwwdpd_opt_subtotl == 'rtw_totl' )
				{
					if( $rtwwdpd_order_total < $rtwwdpd_opt_subtotl )
					{
						continue 1;
					}
				}

				if( $opt['rtwwdpd_min_orders'] > $rtwwdpd_no_oforders )
				{
					continue 1;
				}

				$rtwwdpd_repeat = isset( $opt['rtwwdpd_repeat_discnt'] ) ? $opt['rtwwdpd_repeat_discnt'] : 'no';

				update_user_meta( $rtwwdpd_user_id, 'rtwwdpd_next_buy_eligible', 'rtwwdpd_eligible');

				if( $opt['rtwwdpd_repeat_discnt'] == 'yes' )
				{
					$remove_eligibility = true;
				}
			}
			
			foreach ($rtwwdpd_cart_content->cart_contents as $key => $value) {
				if( $value['discounts']['by'][0] == 'rtwwdpd_next_buy')
				{
					if( $remove_eligibility == true )
					{
						update_user_meta( $rtwwdpd_user_id, 'rtwwdpd_next_buy_eligible', 'rtwwdpd_noteligible');
					}
				}
			}
		}
	}
	 
	/**
	 * Function to make html of attribute values.
	 *
	 * @since    1.2.2
	 */
	function rtwwdpd_selected_attribute_callback(){
		if (!wp_verify_nonce($_POST['security_check'], 'rtwwdpd-ajax-seurity')){
			return;
		}
		$rtwwdpd_attriute_slug = sanitize_text_field($_POST['attriute_slug']);
		$html = '';
		$html .= '
			<td>
				<label class="rtwwdpd_label"> '. esc_html__('Attribute Value', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') .'</label>
			</td>
			<td>';
				
				$rtwwdpd_terms_size = get_terms( $rtwwdpd_attriute_slug );
				
				$rtwwdpd_attr_val_size = array();
			
				if(is_array($rtwwdpd_terms_size) && !empty($rtwwdpd_terms_size))
				{
					foreach ($rtwwdpd_terms_size as $key => $value) {
						$rtwwdpd_attr_val_size[$value->term_id] = $value->name;
					}
				}

		$html .=' <select multiple="multiple" class="rtwwdpd_payment_method rtwwdpd_att_value" name="rtwwdpd_attribute_val[]">';
					
				if(is_array($rtwwdpd_attr_val_size) && !empty($rtwwdpd_attr_val_size))
				{
					foreach ($rtwwdpd_attr_val_size as $colid => $col) { 

		$html .= ' <option value="'. esc_attr($colid) .'">'.
					esc_html__( $col, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) . '
						</option>';
					}
				}

		$html .= ' </select>
				<i class="rtwwdpd_description">'. esc_html__( 'Select attribute name on which discount should be given.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ).'
				</i>
			</td>';

		echo json_encode($html);
		die;

	}

	/** 
	* function to verify purchase code
	* @since 1.3.0
	*/
	function rtwwdpd_verify_purchase_code_callback(){

		if (!wp_verify_nonce($_POST['security_check'], 'rtwwdpd-ajax-seurity')){
			return;
		}
		$rtwwdpd_purchase_code = sanitize_text_field( $_POST['purchase_code'] );

		$rtwwdpd_site_url 		= get_site_url();
		$rtwwdpd_admin_email 	= get_option('admin_email');
		$wp_get_current_user 	= get_user_meta( get_current_user_id() );
		
		if( is_array($wp_get_current_user) && !empty( $wp_get_current_user ) )
		{
			if( isset( $wp_get_current_user['first_name'][0]))
			{
				$rtwwdpd_admin_name = $wp_get_current_user['first_name'][0] . ' '. $wp_get_current_user['last_name'][0];
			}
		}
		else{
			$wp_get_current_user 	= wp_get_current_user();
			$rtwwdpd_admin_name 	= $wp_get_current_user->data->user_nicename;
		}
		$rtwwdpd_plugin_name 	= 'WooCommerce Dynamic Pricing & Discounts with AI';
		$plugin_text_domain 	= 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai';
		$rtwwdpd_site_domain 	= preg_replace( "(^https?://)", "", $rtwwdpd_site_url );


		$rtwwdpd_post_array = array( 
								'site_domain' => $rtwwdpd_site_domain,
								'admin_email' => $rtwwdpd_admin_email,
								'admin_name' => $rtwwdpd_admin_name,
								'plugin_name' => $rtwwdpd_plugin_name,
								'text_domain' => $plugin_text_domain,
								'purchase_code' => $rtwwdpd_purchase_code,
								'plugin_id' => 24165502 
							);

		$args = array(
				    'method' => 'POST',
				    'headers'  => array(
				        'Content-type: application/x-www-form-urlencoded'
				    ),
				    'sslverify' => false,
				    'body' => $rtwwdpd_post_array
				);
				
		$response = wp_remote_post( 'https://demo.redefiningtheweb.com/license-verification/license-verification.php', $args );
		if(is_wp_error($response))
		{
			$rtwwdpd_result = array( 'status' => false,
								'message' => $response->get_error_message() );

			echo json_encode( $rtwwdpd_result );
			die;
		}

		$response_body = json_decode( $response['body'] );
		$response_status = $response_body->result;
		$response_message = $response_body->message;

		if( $response_status ){
			$rtwwdpd_update_array = array( 'purchase_code' => $rtwwdpd_purchase_code,
			'status' => true );

			update_site_option( 'rtwbma_verification_done', $rtwwdpd_update_array );

			$rtwwdpd_result = array( 'status' => true,
								'message' => $response_message );

			echo json_encode( $rtwwdpd_result );
			die;
		}else{
			$rtwwdpd_result = array( 'status' => false,
								'message' => $response_message );

			echo json_encode( $rtwwdpd_result );
			die;
		}
	}

	/*
	* Function to remove purchase code
	*/

	function rtwwdpd_delete_purchase_code()
	{
		$rtwwdpd_site_url = get_site_url();
		$rtwwdpd_admin_email = get_option('admin_email');
		$wp_get_current_user = get_user_meta( get_current_user_id() );

		if( is_array($wp_get_current_user) && !empty( $wp_get_current_user ) )
		{
			if( isset( $wp_get_current_user['first_name'][0]) )
			{
				$rtwwdpd_admin_name = $wp_get_current_user['first_name'][0] . ' '. $wp_get_current_user['last_name'][0];
			}
		}
		else{
			$wp_get_current_user = wp_get_current_user();
			$rtwwdpd_admin_name = $wp_get_current_user->data->user_nicename;
		}
		$rtwwdpd_plugin_name = 'WooCommerce Dynamic Pricing & Discounts with AI';
		$plugin_text_domain = 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai';
		$rtwwdpd_site_domain = preg_replace( "(^https?://)", "", $rtwwdpd_site_url );
		$rtwwdpd_purchase_code = get_site_option( 'rtwbma_verification_done', array() );

		$rtwwdpd_post_array = array(
			'site_domain' => $rtwwdpd_site_domain,
			'admin_email' => $rtwwdpd_admin_email,
			'admin_name' => $rtwwdpd_admin_name,
			'plugin_name' => $rtwwdpd_plugin_name,
			'text_domain' => $plugin_text_domain,
			'purchase_code' => $rtwwdpd_purchase_code['purchase_code'],
			'plugin_id' => 24165502
		);

		$args = array(
				'method' => 'POST',
				'headers' => array(
				'Content-type: application/x-www-form-urlencoded'
			),
				'sslverify' => false,
				'body' => $rtwwdpd_post_array
			);

		$response = wp_remote_post( 'https://demo.redefiningtheweb.com/license-verification/license-remove.php', $args );
		delete_site_option('rtwbma_verification_done');
		wp_redirect( esc_url( admin_url( 'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_discount_rules' ) ) );
		exit;
	}

	/**
	 * Function to get total discount on order.
	 *
	 * @since    1.3.0
	 */
	function rtwwdpd_order_get_total_discount( $order, $data )
	{
		$total_discount = 0;
		$meta_data_order = $data->get_meta_data();
		foreach( $meta_data_order as $meta => $dis )
		{
			$key = $dis->get_data()['key'];
			if( $key == 'total_discount' )
			{
				$total_discount = $dis->get_data()['value'];
			}
		}

		if( !empty( $total_discount ) )
		{
			return $total_discount;
		}
		else{
			return $order;
		}
	}

	/* 
	* Shipping customization
	*/
	function rtwwdpd_shipping_rule_on_callback(){
		if (!wp_verify_nonce($_POST['security_check'], 'rtwwdpd-ajax-seurity')){
			return;
		}
		$rtwwdpd_rul_on = sanitize_text_field( $_POST['rtw_val'] );
		update_option('rtwwdpd_shipping_discount_on', $rtwwdpd_rul_on);
		die;
	}

	
    function rtwwdpd_apply_shipping_discount_callback()
    {
        if (!wp_verify_nonce($_POST['security_check'], 'rtwwdpd-ajax-seurity')){
			return;
		}
		$rtwwdpd_rul_on = sanitize_text_field( $_POST['shipping_discount_on'] );
		update_option('rtwwdpd_apply_shipping_discount_on', $rtwwdpd_rul_on);
		die;
	}
	

	function rtwwdpd_plus_text_callback()
	{
		if (!wp_verify_nonce($_POST['security_check'], 'rtwwdpd-ajax-seurity')){
			return;
		}
		$rtwwdpd_rul_text = sanitize_text_field( $_POST['rtw_val'] );
		update_option('rtwwdpd_plus_member_text', $rtwwdpd_rul_text);
		die;
	}

	function rtwwdpd_show_ship_on_chkout_callback()
	{
        if (!wp_verify_nonce($_POST['security_check'], 'rtwwdpd-ajax-seurity')){
			return;
		}
		$rtwwdpd_rul_on = sanitize_text_field( $_POST['shipping_discount_on'] );
		update_option('rtwwdpd_show_ship_on_chkout', $rtwwdpd_rul_on);
		die;
	}
}
