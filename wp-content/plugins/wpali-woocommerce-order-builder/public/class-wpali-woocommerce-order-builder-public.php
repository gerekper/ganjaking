<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpali.com
 * @since      1.0.7
 *
 * @package    Wpali_Woocommerce_Order_Builder
 * @subpackage Wpali_Woocommerce_Order_Builder/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wpali_Woocommerce_Order_Builder
 * @subpackage Wpali_Woocommerce_Order_Builder/public
 * @author     ALI KHALLAD <ali@wpali.com>
 */
class Wpali_Woocommerce_Order_Builder_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.5
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpali_Woocommerce_Order_Builder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpali_Woocommerce_Order_Builder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ($this->is_wwob_enabled() == true){
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpali-woocommerce-order-builder-public.css', array(), $this->version, 'all' );
		}
			wp_enqueue_style( 'wwob-general', plugin_dir_url( __FILE__ ) . 'css/wpali-woocommerce-order-builder-public-general.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.1.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpali_Woocommerce_Order_Builder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpali_Woocommerce_Order_Builder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ($this->is_wwob_enabled() == true){
			wp_enqueue_script( 'resizesensor-js', plugin_dir_url( __FILE__ ) . 'js/vendor/resizesensor.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( 'sticky-sidebar-js', plugin_dir_url( __FILE__ ) . 'js/vendor/sticky-sidebar.min.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpali-woocommerce-order-builder-public.js', array( 'jquery' ), $this->version, true );

			wp_register_script( 'wwob-zoom', plugin_dir_url( __FILE__ ) . 'js/jquery.zoom.min.js' );

			// Localize the script with new data
			$product_quantity_count = wwob_get_option( 'product_quantity_count' ) ? wwob_get_option( 'product_quantity_count' ) : 'no';
			$translation_array = array(
				'min_error' => __( 'Please select at least', 'wpali-woocommerce-order-builder' ),
				'product_quantity_count' => $product_quantity_count,
			);
			wp_localize_script( $this->plugin_name, 'LocalizedVar', $translation_array );
		}
	}
	/**
	 * Check if current page is a product and WWOB is enabled.
	 *
	 * @since    1.0.7
	 */
	public function is_wwob_enabled() {
		global $post;
		if (  isset( $post ) && is_singular( 'product' )  ) {

			$enabled_disabled = get_post_meta( $post->ID, 'wwob_enable_disable', 1 ) ;
			$product = wc_get_product( $post->ID );
			if ($enabled_disabled == 'enabled' and $product->is_type( 'simple' ) ){
				return true;
			}
		}
	}
	/**
	 * Add WWOB class where it's activated to body classes.
	 *
	 * @since    1.0.7
	 */
	public function add_wwob_slug_body_class( $classes ) {

		if ( $this->is_wwob_enabled() ) {
			$classes[] = 'wwob-product';
		}
		return $classes;
	}

	/**
	 * Change Product Page Template if Order Builder is Enabled.
	 *
	 * @since    1.0.5
	 */

	public function theme_customisations_wc_get_template( $located, $template_name, $args, $template_path, $default_path ) {
		if ($this->is_wwob_enabled() == true){
			$plugin_template_path = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/woocommerce/' . $template_name;
			if ( file_exists( $plugin_template_path ) ) {
				$located = $plugin_template_path;
			}
		}
		return $located;
	}

	/**
	 * Control product display.
	 *
	 * @since    1.0.5
	 */

	public function wwob_display_product_display_functions() {
		$product_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$post_id = url_to_postid($product_url);
		$post_type = get_post_type( $post_id );
		if (  $post_type == 'product'  ) {
			$enabled_disabled = get_post_meta( $post_id, 'wwob_enable_disable', 1 ) ;
			$product = wc_get_product( $post_id );
			if ($enabled_disabled == 'enabled' and $product->is_type( 'simple' ) ){

				$product_layout = wwob_get_option( 'product_layout' ) ? wwob_get_option( 'product_layout' ) : "wwob";
				if($product_layout == "woocommerce"){
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
				}else{
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
				}
			}
		}
	}

	/**
	 * Overwrite add options to product page before add to cart.
	 *
	 * @since    1.0.5
	 */

	public function wwob_woocommerce_extended_product() {
		if ($this->is_wwob_enabled() == true){
			global $post;
			include_once( 'partials/wpali-woocommerce-order-builder-public-display.php' );

			$builder_form_data = get_post_meta( $post->ID, 'wwob_group', true );

			if ( is_array( $builder_form_data ) ) {
				$product = wc_get_product( $post->ID );

				$product_form = new wwob_woocommerce_custom_product_form( $post->ID );
				$product_form->get_form( $builder_form_data );

				echo '<input type="hidden" name="add-to-cart" value="' . esc_attr( $product->get_id() ) . '" />';
				echo '<input type="hidden" name="total-price"  value="" />';

			}
			echo '<div class="clear"></div>';
		}
	}

	/**
	 * Add HTML closing tags after add to cart button.
	 *
	 * @since    1.0.5
	 */

	public function WWOB_add_html_tags_after_addtocart(  ) {
		if ($this->is_wwob_enabled() == true){
			?>
			<div class="wwob-clearfix"></div>
			</div> <!-- End of sticky div -->
			</div> <!-- End of <div class="right-price-calculation-area" > -->
			<?php
		}
	}

	/**
	 * Add total price after quantity.
	 *
	 * @since    1.0.5
	 */

	public function WWOB_add_total_after_quantity(  ) {
		if ($this->is_wwob_enabled() == true){
			global $post;
			$product = wc_get_product($post->ID);
			$price = apply_filters( 'wwob_product_total_price', number_format($product->get_price(), 2, '.', ''));
			do_action( 'wwob_before_total', $product );
			?>
			<ul id="wwobform_totals_<?php echo $post->ID; ?>" class="side_wwobform_totals wwobform_fields ">
				<li class="wwobfield side-total-price">
					<label class="wwobfield_label"><?php _e('Total', 'wpali-woocommerce-order-builder'); ?> </label>
					<div class="wwobinput_container">
						<span class="wwob_currency_symbol"><?php echo get_woocommerce_currency_symbol(); ?></span><span class="formattedTotalPrice wwobinput_total"><?php echo $price; ?></span>
					</div>

				</li>
			</ul>

			<?php
			do_action( 'wwob_after_total', $product );
		}
	}

	/**
	 * Function to get single item price based on ID.
	 *
	 * @since    1.0.0
	 */
	public function GetPriceById($arr, $id){

    foreach($arr as $items){
		foreach($items['wwob_product_field'] as $item){

			if ($item['product-id'] == $id){
				return $item['product-price'];
			}
		}
    }
    return "";
	}
	/**
	 * Function to get total price of requested items based on $_POST data.
	 *
	 * @since    1.0.2
	 */
	public function GetTotalByPostReq($RequestData, $form_data){
		$product = wc_get_product( $RequestData['product_id'] );
		$base_price = $product->get_price();

		$arr_result = array();
		foreach($RequestData as $key => $value){
			$exp_key = explode('_item_', $key);

			if($exp_key[0] == 'input'){

				$exp_value = explode('|', $value);

				 $arr_result[] = $this->GetPriceById($form_data, $exp_value[0]);
			}
		}
		$arr_result[] = $base_price;
		if(isset($arr_result)){
			$total = array_sum($arr_result);
		}else{
			$total = "";
		}

		return $total;
	}
	/**
	 * Get the names of items from a specific order using $_POST data.
	 *
	 * @since    1.0.0
	 */
	public function GetItemsIDsByPostReq($RequestData, $form_data){

		$arr_result = array();
		foreach($RequestData as $key => $value){
			$exp_key = explode('_item_', $key);

			if($exp_key[0] == 'input'){

				$exp_value = explode('|', $value);
				$item_id = $exp_value[0];
				 $arr_result[] = $item_id;

			}
		}


			$result = $arr_result;

		return $result;
	}

	/**
	 * Get the items details from a specific order using $_POST data.
	 *
	 * @since    1.0.7
	 */
	public function GetDetailsByPostReq($items, $form_data){
		if(empty($items)){
			return false;
		}
		$result = array();
		// get the count of items including duplicate values.
		$items_count= array_count_values($items);

		// Create an array for duplicate value only.
		$duplicate_product = array();
		foreach($items_count as $key => $val){
			if($val > 1){
				$duplicate_product[$key] = $val;
			}
		}

		// New array without duplicates retrieved from $items_count keys.
		$filtered_items = array_keys($items_count);

		// count of ordered items
		$elementsReq = count($filtered_items);


		$one = 1;
		foreach ($form_data as $value)
		{
			$num = $one++;

			$curProd = "";
			if(!empty($value['wwob_field_label'])){
				$curProd = $value['wwob_field_label'];
			}else{
				$curProd = "Product".$num;
			}
			$elem =  array();

			foreach ($value['wwob_product_field'] as $parent)
			{
				for ($i=0; $i < $elementsReq; $i++) {

					if(!empty($parent['product-id'])){
						$n = 1;
						if($parent['product-id'] ==  $filtered_items[$i])
						{
							if (array_key_exists($filtered_items[$i], $duplicate_product)) {
								$product_name = $duplicate_product[$filtered_items[$i]].' '.$parent['product-name'];
							}else{
								$product_name = $parent['product-name'];
							}
							array_push($elem, $product_name);
							break;
						}
					}
				}

			}

			if (!empty($elem)){
				$curProdArray = array
				(
					'product' => $curProd,
					'items' => $elem
				);
			array_push($result, $curProdArray);
			}

		}
		return $result;
	}

	/**
	 * Set Prices dynamically when order builder is present.
	 *
	 * @since    1.0.0
	 */

	public function set_wwob_prices( $wwob_data ) {

		if ( ! isset( $_POST['wc_wwobforms_form_id'] ) || empty ( $_POST['wc_wwobforms_form_id'] ) ) { return $wwob_data; }
		$product = wc_get_product( $_POST['wc_wwobforms_form_id'] );
		if( $product->is_type( 'simple' ) ){
			$form_data = get_post_meta( $_POST['wc_wwobforms_form_id'], 'wwob_group', true );

			$total = $this->GetTotalByPostReq($_POST, $form_data);

			$wwob_data['data']->set_price( $total );
			$wwob_data['wwob_price'] = $total;
		}
		return $wwob_data;

	}

	/**
	 * Add WWob custom data to the cart item.
	 *
	 * @since    1.1.2
	 */
	public function wwob_add_cart_item_data( $cart_item_data, $product_id ){

		$enabled_disabled = get_post_meta( $product_id, 'wwob_enable_disable', 1 ) ;
		$product = wc_get_product( $product_id );

		if ($enabled_disabled == 'enabled' and $product->is_type( 'simple' )){
				$unique_cart_item_key = md5( microtime().rand() );
				$cart_item_data['unique_key'] = $unique_cart_item_key;

				if( isset( $cart_item_data ) ) {

					$form_data = get_post_meta( $product_id, 'wwob_group', true );
					$data = "";
					if( isset( $_POST ) and !empty( $_POST ) ) {
						$items = $this->GetItemsIDsByPostReq($_POST, $form_data);
						$data = $this->GetDetailsByPostReq($items, $form_data);
						$cart_item_data['details'] = $data ;

						$selected_options = array();
						foreach($_POST as $key => $value){
							$exp_key = explode('_choice_', $key);
							if($exp_key[0] == 'wwob'){
								$selection = $value;
								if( !empty( $selection ) ) {
									$selected_options[$exp_key[1]] = $selection;
								}
							}
						}

						$cart_item_data['options'] = $selected_options ;

						if( isset( $_POST['wwob_color'] ) and !empty( $_POST['wwob_color'] ) ) {
							$cart_item_data['wwob_color'] = $_POST['wwob_color'] ;
						}
						if( isset( $_POST['wwob_size'] ) and !empty( $_POST['wwob_size'] ) ) {
							$cart_item_data['wwob_size'] = $_POST['wwob_size'] ;
						}
						if( isset( $_POST['special_instructions'] ) and !empty( $_POST['special_instructions'] ) ) {
							$cart_item_data['special_instructions'] = $_POST['special_instructions'] ;
						}
					}


				}

			return $cart_item_data;
		}else{
			return $cart_item_data;
		}

	}
	/**
	 * Load WWob cart data from session.
	 *
	 * @since    1.0.0
	 */
	public function  set_get_cart_item_from_session ( $wwob_data , $values , $key ) {
		if ( ! isset( $wwob_data['wwob_price'] ) || empty ( $wwob_data['wwob_price'] ) ) { return $wwob_data; }

		$wwob_data['data']->set_price( $wwob_data['wwob_price'] );

		if ( isset( $values['details'] ) ){
			$wwob_data['details'] = $values['details'];
		}
		if ( isset( $values['options'] ) ){
			$wwob_data['options'] = $values['options'];
		}
		if ( isset( $values['wwob_color'] ) ){
			$wwob_data['wwob_color'] = $values['wwob_color'];
		}
		if ( isset( $values['wwob_size'] ) ){
			$wwob_data['wwob_size'] = $values['wwob_size'];
		}
		if ( isset( $values['special_instructions'] ) ){
			$wwob_data['special_instructions'] = $values['special_instructions'];
		}
		return $wwob_data;
	}
	/**
	 * Add WWob meta to order item.
	 *
	 * @since    1.0.0
	 */
	public function wwob_add_order_item_meta( $item_id, $values ) {

		if ( ! empty( $values['details'] ) ) {
			woocommerce_add_order_item_meta( $item_id, __( 'Order Details:', 'wpali-woocommerce-order-builder' ), $values['details'] );

			foreach ($values['details'] as $details){

				$string = "";
				$string .= "<ul class='woob_item_details_display'>";

					foreach ($details['items'] as $item){
						$string .= "<li>". $item ."</li>";
					}
				$string .= "</ul>";

				woocommerce_add_order_item_meta( $item_id, $details['product'], $string );
			}

		}
		if ( ! empty( $values['options'] ) ) {

			$string = "";
			$string .= "<ul class='woob_item_options_display'>";
			$options = array();
			foreach ($values['options'] as $key => $value){
				$pieces = explode("|", $value);
				if (!empty($pieces[1]) and !empty($pieces[0])){
					$options[$pieces[0]][]= $pieces[1];
				}else{
					$key = explode("_", $key, 3);
					$string .= "<li><b>". ucfirst(str_replace("_"," ",$key[2])) .":</b> ". $value ."</li>";
				}
			}

			foreach ($options as $key => $value){
				if ( ! empty( $value ) ) {
					$string .= "<li><b>". $key .": </b>";
					$val_count = count($value);
					$i = 0;
					foreach ($value as $val){
						$i++;
						if($val_count == $i){
							$string .= $val;
						}else{
							$string .= ''. $val .', ';
						}
					}
					$string .= "</li>";
				}
			}
			$string .= "</ul>";

			woocommerce_add_order_item_meta( $item_id, __( 'Product Options', 'wpali-woocommerce-order-builder' ), $string );

		}
		if ( ! empty( $values['wwob_color'] ) ) {
			woocommerce_add_order_item_meta( $item_id, __( 'Color', 'wpali-woocommerce-order-builder' ), $values['wwob_color'] );
		}
		if ( ! empty( $values['wwob_size'] ) ) {
			woocommerce_add_order_item_meta( $item_id, __( 'Size', 'wpali-woocommerce-order-builder' ), $values['wwob_size'] );
		}
		if ( ! empty( $values['special_instructions'] ) ) {
			woocommerce_add_order_item_meta( $item_id, __( 'Instructions', 'wpali-woocommerce-order-builder' ), sanitize_text_field( $values['special_instructions']) );
		}
	}
	/**
	 * Get WWob item data to display in cart.
	 *
	 * @since    1.0.0
	 */
	public function wwob_get_item_data( $other_data, $cart_item ) {

		if ( isset( $cart_item['details'] ) and !empty($cart_item['details']) ){

			foreach ($cart_item['details'] as $details){

				$string = "";
				$string .= "<ul class='woob_item_details_display'>";

					foreach ($details['items'] as $item){
						$string .= "<li>". $item ."</li>";
					}
				$string .= "</ul>";

				$other_data[] = array(
					'name' => __( $details['product'], 'wpali-woocommerce-order-builder' ),
					'value' => $string
				);
			}
		}
		if ( isset( $cart_item['options'] ) and !empty($cart_item['options']) ){

			$string = "";
			$string .= "<ul class='woob_item_options_display'>";


			$options = array();
			foreach ($cart_item['options'] as $key => $value){
				$pieces = explode("|", $value);
				if (!empty($pieces[1]) and !empty($pieces[0])){
					$options[$pieces[0]][]= $pieces[1];
				}else{
					$key = explode("_", $key, 3);
					$string .= "<li><b>". ucfirst(str_replace("_"," ",$key[2])) .":</b> ". $value ."</li>";
				}
			}
			foreach ($options as $key => $value){
				if ( ! empty( $value ) ) {
					$string .= "<li><b>". $key .": </b>";
					$val_count = count($value);
					$i = 0;
					foreach ($value as $val){
						$i++;
						if($val_count == $i){
							$string .= $val;
						}else{
							$string .= ''. $val .', ';
						}
					}
					$string .= "</li>";
				}
			}

			$string .= "</ul>";

			$other_data[] = array(
				'name' => __( 'Product Options', 'wpali-woocommerce-order-builder' ),
				'value' => $string
			);
		}
		if ( isset( $cart_item['wwob_color'] ) and !empty($cart_item['wwob_color']) ){
			$other_data[] = array( 'name' => __( 'Color', 'wpali-woocommerce-order-builder' ), 'value' => $cart_item['wwob_color'] );
		}
		if ( isset( $cart_item['wwob_size'] ) and !empty($cart_item['wwob_size']) ){
			$other_data[] = array( 'name' => __( 'Size', 'wpali-woocommerce-order-builder' ), 'value' => $cart_item['wwob_size'] );
		}
		if ( isset( $cart_item['special_instructions'] ) and !empty($cart_item['special_instructions']) ){
			$other_data[] = array( 'name' => __( 'Instructions', 'wpali-woocommerce-order-builder' ), 'value' => $cart_item['special_instructions'] );
		}
		return $other_data;
	}
	/**
	 * Show WWob data in order overview.
	 *
	 * @since    1.0.7
	 */
	public function wwob_order_item_product( $cart_item, $order_item ){

		if( isset( $order_item['details'] ) and !empty($cart_item['details'] )) {
			$cart_item_meta['details'] = $order_item['details'];
		}
		if( isset( $order_item['options'] ) and !empty($cart_item['options'] )) {
			$cart_item_meta['options'] = $order_item['options'];
		}
		if( isset( $order_item['wwob_color'] ) and !empty($cart_item['wwob_color'] )) {
			$cart_item_meta['wwob_color'] = $order_item['wwob_color'];
		}
		if( isset( $order_item['wwob_size'] ) and !empty($cart_item['wwob_size'] )) {
			$cart_item_meta['wwob_size'] = $order_item['wwob_size'];
		}
		if( isset( $order_item['special_instructions'] ) and !empty($cart_item['special_instructions'] )) {
			$cart_item_meta['special_instructions'] = $order_item['special_instructions'];
		}
		return $cart_item;

	}
	/**
	 * Add WWob field to order emails .
	 *
	 * @since    1.0.7
	 */
	public function wwob_email_order_meta_fields( $fields ) {

		if( isset( $fields['details'] ) and !empty($fields['details'] ) ){
		$fields['details'] = __( 'Order details', 'wpali-woocommerce-order-builder' );
		}
		if( isset( $fields['options'] ) and !empty($fields['wwob_size'] ) ){
		$fields['options'] = __( 'Product Options', 'wpali-woocommerce-order-builder' );
		}
		if( isset( $fields['wwob_color'] ) and !empty($fields['wwob_color'] ) ){
		$fields['wwob_color'] = __( 'Color', 'wpali-woocommerce-order-builder' );
		}
		if( isset( $fields['wwob_size'] ) and !empty($fields['wwob_size'] ) ){
		$fields['wwob_size'] = __( 'Size', 'wpali-woocommerce-order-builder' );
		}
		if( isset( $fields['special_instructions'] ) and !empty($fields['special_instructions'] ) ){
		$fields['special_instructions'] = __( 'Instructions', 'wpali-woocommerce-order-builder' );
		}
		return $fields;

	}
	/**
	 * Extra Items Styles.
	 *
	 * @since    1.0.0
	 */
	public function wwob_extra_items_styles() {

		global $post;
		if( !is_object($post) )
        return;

		$enabled_disabled = get_post_meta( $post->ID, 'wwob_enable_disable', 1 );
		$product = wc_get_product( $post->ID );

		if ( is_singular( 'product' ) and $enabled_disabled == 'enabled' and $product->is_type( 'simple' ) ) {

			$items_display_style = wwob_get_option( 'items_display_style' );
			$primary_color = wwob_get_option( 'primary_color' ) ? wwob_get_option( 'primary_color' ) : "#03d99d";
			$secondary_color = wwob_get_option( 'secondary_color' ) ? wwob_get_option( 'secondary_color' ) : "#1c0055";

			if($items_display_style == 'first'){
				?>
<style>.woocommerce .extended-checkboxes ul.wwobfield_checkbox li{padding: 10px; background:0 0!important;border-radius:10px}.extended-checkboxes .wwob-checkbox-img img{border: 2px solid transparent; border-radius:10px;padding:10px;background:<?php echo $secondary_color ?>}.extended-checkboxes ul.wwobfield_checkbox li .label-meta-container{border: 2px solid transparent; background:<?php echo $primary_color ?>;border-radius:10px;padding-top:5px;padding-bottom:5px; margin-bottom: 10px;}.extended-checkboxes ul.wwobfield_checkbox li .label-meta-container p,.extended-checkboxes ul.wwobfield_checkbox li .label-meta-container span{color:<?php echo $secondary_color ?>!important}.extended-checkboxes ul.wwobfield_checkbox li.selected-item .selected-product{background:0 0; margin-top: 10px;}.extended-checkboxes ul.wwobfield_checkbox li.selected-item .label-meta-container{background:transparent; border: 2px solid <?php echo $primary_color ?>;}.extended-checkboxes ul.wwobfield_checkbox li.selected-item .label-meta-container p,.extended-checkboxes ul.wwobfield_checkbox li.selected-item .label-meta-container span{color:<?php echo $primary_color ?>!important}.extended-checkboxes ul.wwobfield_checkbox li.selected-item .wwob-checkbox-img img {background: transparent; border: 2px solid <?php echo $primary_color ?>;}</style>
<script>var checkInsideImg = true;</script>
				<?php
			}elseif($items_display_style == 'second'){
				?>
<style>.woocommerce .extended-checkboxes ul.wwobfield_checkbox li{padding-bottom: 15px; border-radius:7px;border: 2px solid <?php echo $secondary_color ?>!important;overflow:visible}.extended-checkboxes ul.wwobfield_checkbox li.selected-item .selected-product span.selected-product-checked{top:calc(100% - 30px)}.extended-checkboxes ul.wwobfield_checkbox li.selected-item .wwob-checkbox-label img{border-top-left-radius:5px;border-top-right-radius:5px}.woocommerce label.wwob-checkbox-label p.wwob-item-name, .woocommerce span.wwobinput_price {color: <?php echo $primary_color ?>;}.woocommerce .extended-checkboxes ul.wwobfield_checkbox li .wwob-checkbox-label {margin-bottom: 0px;}.woocommerce .extended-checkboxes ul.wwobfield_checkbox li .wwob-checkbox-label img {border-top-left-radius: 5px;border-top-right-radius: 5px;}.extended-checkboxes ul.wwobfield_checkbox li .selected-product {border: 2px solid <?php echo $secondary_color ?>; border-radius: 5px;}li.quantity-enabled.selected-item a.wwob-minus, a.wwob-plus {border: 2px solid <?php echo $secondary_color ?>; top: calc( 100% - 20px ); line-height: 36px;}</style>
				<?php
				}elseif($items_display_style == 'third'){
				?>
<style>.woocommerce .extended-checkboxes ul.wwobfield_checkbox li{padding: 10px;border-radius:10px;background:0 0!important;border:3px solid transparent}.woocommerce .extended-checkboxes ul.wwobfield_checkbox li .label-meta-container {padding-bottom: 0px;}.woocommerce .extended-checkboxes .wwob-checkbox-img img{border-radius:50%}.woocommerce .extended-checkboxes ul.wwobfield_checkbox li .wwob-checkbox-label{padding:10px}.woocommerce .extended-checkboxes ul.wwobfield_checkbox li.selected-item{border:3px solid <?php echo $primary_color ?>}.extended-checkboxes ul.wwobfield_checkbox li.selected-item .selected-product span.selected-product-checked{left:10px;top:10px}.extended-checkboxes ul.wwobfield_checkbox li.selected-item .selected-product{background:0 0;}.woocommerce .extended-checkboxes ul.wwobfield_checkbox li label.wwob-checkbox-label p.wwob-item-name{color:<?php echo $secondary_color ?>!important}.woocommerce .extended-checkboxes ul.wwobfield_checkbox li span.wwobinput_price{color:<?php echo $primary_color ?>!important}</style>
				<?php
			}
		}
	}

	/**
	 * Prevent add to cart without selection.
	 *
	 * @since    1.1.3
	 */
	public function wwob_prevent_items_add_to_cart($validation, $product_id) {

		$enabled_disabled = get_post_meta( $product_id, 'wwob_enable_disable', 1 );
		$product = wc_get_product( $product_id );
		if ($enabled_disabled == 'enabled' and $product->is_type( 'simple' ) ){

			$form_data = get_post_meta( $product_id, 'wwob_group', true );
			$total = $this->GetItemsIDsByPostReq($_POST, $form_data);
			$price = $product->get_price();

			if (empty($total)){
				wc_add_notice(__( 'Sorry, This product has options. To purchase, please select options first and try again.', 'wpali-woocommerce-order-builder' ), 'error');
				$validation = false;
			}

		}
		return $validation;
	}

}
