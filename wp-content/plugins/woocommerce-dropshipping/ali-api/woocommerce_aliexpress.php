<?php
	add_action('pre_get_posts','aliexpress_status_filter_to_shop_order_page'); // Filter related aliexpress orders
	add_action('restrict_manage_posts','aliexpress_status_filter_to_shop_order_posts_administration');
	add_filter('manage_edit-shop_order_columns', 'custom_woo_columns_function'); // Extra column title
	add_action('manage_shop_order_posts_custom_column', 'custom_woo_admin_value', 2); // Extra column value
	add_action( 'add_meta_boxes', 'place_order_automatically_meta_boxes' ); // Add Meta Box for place order Auto..
	add_action('wp_ajax_get_order_data','get_order_data');	// Ajax callback for getting AliExpress Product URL.
	add_action('wp_ajax_nopriv_get_order_data','get_order_data');	// Ajax callback for getting AliExpress Product URL.
	add_action( 'add_meta_boxes', 'select_custom_order_status' ); //Select custome order status
	add_action('save_post', 'status_save_metabox');  //save custome order status
	add_action( 'admin_footer', 'insert_hidden_fields_for_check_cbe' ); // Checked CBE exist or not

	add_action( 'import_ali_product_in_woo', 'get_sku_in_woo' ); // Call SKU Function.
	add_action( 'rest_api_init', 'import_ali_product_in_woo_callback' );
	function import_ali_product_in_woo_callback() {
	    register_rest_route(
	    	'woo-aliexpress/v1',
	    	'product',
    		array(
                'methods' => WP_REST_Server::CREATABLE,
                'permission_callback' => '__return_true',
                'callback' => 'import_ali_product_in_woo',
            )
        );
	}
	// http://mytestsite.com/wp-json/woo-aliexpress/v1/product

	add_action( 'rest_api_init', 'get_list_of_product_category_from_woo_callback' );
	function get_list_of_product_category_from_woo_callback() {
	    register_rest_route(
	    	'woo-aliexpress/v1',
	    	'products-categories',
    		array(
                'methods' => WP_REST_Server::READABLE,
                'permission_callback' => '__return_true',
                'callback' => 'get_product_category_from_woo',
            )
        );
	}
	//http://testprey.avitinfotech.com/wp-json/woo-aliexpress/v1/products-categories

	add_action( 'rest_api_init', 'create_product_category_in_woo_callback' );
	function create_product_category_in_woo_callback() {
	    register_rest_route(
	    	'woo-aliexpress/v1',
	    	'create-categories',
    		array(
                'methods' => WP_REST_Server::CREATABLE,
                'permission_callback' => '__return_true',
                'callback' => 'create_product_category_in_woo',
            )
        );
	}
	// http://testprey.avitinfotech.com/wp-json/woo-aliexpress/v1/create-categories

	add_action( 'rest_api_init', 'get_order_details_from_woo_callback' );
	function get_order_details_from_woo_callback() {
	    register_rest_route(
	    	'woo-aliexpress/v1',
	    	'order-details',
    		array(
                'methods' => WP_REST_Server::CREATABLE,
                'permission_callback' => '__return_true',
                'callback' => 'get_order_detail_by_id',
            )
        );
	}
	// http://mytestsite.com/wp-json/woo-aliexpress/v1/order-details

	add_action( 'rest_api_init', 'authentication_with_CBE_and_woo_callback' );
	function authentication_with_CBE_and_woo_callback() {
	    register_rest_route(
	    	'woo-aliexpress/v1',
	    	'woo-authentication',
    		array(
                'methods' => WP_REST_Server::CREATABLE,
                'permission_callback' => '__return_true',
                'callback' => 'woo_authentication',
            )
        );
	}
	// http://mytestsite.com/wp-json/woo-aliexpress/v1/woo-authentication

	add_action( 'update_ali_product_in_woo', 'get_sku_in_woo' ); // Call SKU Function
	add_action( 'rest_api_init', 'update_ali_product_in_woo_callback' );
	function update_ali_product_in_woo_callback() {
	    register_rest_route(
	    	'woo-aliexpress/v1',
	    	'update-product',
    		array(
                'methods' => WP_REST_Server::CREATABLE,
                'permission_callback' => '__return_true',
                'callback' => 'update_ali_product_in_woo',
            )
        );
	}
	// http://localhost/mywpsite/wp-json/woo-aliexpress/v1/update-product

	add_action( 'rest_api_init', 'get_merchant_store_currency_code_in_woo_callback' );
	function get_merchant_store_currency_code_in_woo_callback() {
	    register_rest_route(
	    	'woo-aliexpress/v1',
	    	'store-currency-code',
    		array(
                'methods' => WP_REST_Server::READABLE,
                'permission_callback' => '__return_true',
                'callback' => 'get_merchant_store_currency_code_in_woo',
            )
        );
	}
	// http://localhost/mywpsite/wp-json/woo-aliexpress/v1/store-currency-code

	add_action( 'get_product_sku_from_woo', 'get_sku_in_woo' ); // Call SKU Function
	add_action( 'rest_api_init', 'get_product_sku_from_woo_callback' );
	function get_product_sku_from_woo_callback() {
	    register_rest_route(
	    	'woo-aliexpress/v1',
	    	'product-sku',
			array(
	            'methods' => WP_REST_Server::CREATABLE,
	            'permission_callback' => '__return_true',
	            'callback' => 'get_product_sku_from_woo',
	        )
	    );
	}
	// http://localhost/mywpsite/wp-json/woo-aliexpress/v1/product-sku

	add_action( 'rest_api_init', 'get_order_status_from_woo_callback' );

	function get_order_status_from_woo_callback() {

	    register_rest_route(

	    	'woo-aliexpress/v1',

	    	'order-status',

    		array(

                'methods' => WP_REST_Server::CREATABLE,
                'permission_callback' => '__return_true',
                'callback' => 'get_order_status_by_id',
            )
        );
	}
	// http://localhost/mywpsite/wp-json/woo-aliexpress/v1/order-status


	// -----------------------------------------------------------------------------------
	// ------------------------------ Start Ali CBE --------------------------------------
	// -----------------------------------------------------------------------------------


		add_action( 'rest_api_init', 'ali_cbe_price_rate' );
		function ali_cbe_price_rate() {
		    register_rest_route(
		    	'woo-aliexpress/v1',
		    	'price-rate',
	    		array(
	                'methods' => WP_REST_Server::READABLE,
	                'permission_callback' => '__return_true',
	                'callback' => 'getPriceRate',
	            )
	        );
		}
		// /wp-json/woo-aliexpress/v1/price-rate

		if (!function_exists('getPriceRate')) {

			function getPriceRate() {
				$options = get_option( 'wc_dropship_manager' );
				$ali_cbe_price_rate_type = $options['ali_cbe_price_rate_name'];
				$ali_cbe_price_rate_value = $options['ali_cbe_price_rate_value_name'];
				if($options['ali_cbe_enable_name'] == '0'){
					$ali_cbe_price_rate_value = 0;
				}
				$ali_cbe_get_option =  array(
					"price_rate_type" => $ali_cbe_price_rate_type,
				  "price_rate_value" => $ali_cbe_price_rate_value
				);

				if (!empty($ali_cbe_get_option)){
					$response = array(
						"code" => 'success',
						"message" => 'Success on getting price rate',
						"options" => array(
								'price_rate' => $ali_cbe_get_option
							),
						"data" => array(
							"status" => 200
						)
					);
				}else{
					return new WP_Error( 'fail', esc_html__( 'Setting for price rate is not set', 'my-text-domain' ), array( 'status' => 400 ) );
				}

			  return $response;

			}	// function getPriceRate()

		}


	// -----------------------------------------------------------------------------------
	// ------------------------------ End Ali CBE ----------------------------------------
	// -----------------------------------------------------------------------------------

	function import_ali_product_in_woo(WP_REST_Request $request) {

		// Following should be used whenever we want to return error:
		//return new WP_Error( 'fail', esc_html__( 'The id parameter is required.', 'my-text-domain' ), array( 'status' => 400 ) );

		//include_once('globals.php');

		$inputParams = $request->get_json_params();
		//return $inputParams;

		/**********PRODUCT VALIDATIONS STARTS HERE***/
        if( empty($inputParams['title']) ){
         	return new WP_Error( 'fail', esc_html__( 'The Title parameter is required!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( empty($inputParams['type']) || !in_array($inputParams['type'],array('simple','variable')) ){
         	return new WP_Error( 'fail', esc_html__( 'Please enter a valid Type (simple or variable)!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( empty($inputParams['sku']) ){
         	return new WP_Error( 'fail', esc_html__( 'The SKU parameter is required!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( empty($inputParams['price']) || !is_numeric($inputParams['price']) || $inputParams['price']<0 ){
         	return new WP_Error( 'fail', esc_html__( 'The Price parameter is required and must be a valid non-negative numeric value!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( empty($inputParams['regular_price']) || !is_numeric($inputParams['regular_price']) || $inputParams['regular_price']<0 ){
         	return new WP_Error( 'fail', esc_html__( 'The Regular Price parameter is required and must be a valid non-negative numeric value!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( !empty($inputParams['sale_price']) && (!is_numeric($inputParams['sale_price']) || $inputParams['sale_price']<0) ){
         	return new WP_Error( 'fail', esc_html__( 'The Sale Price parameter must be a valid non-negative numeric value!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        /* if(!isset($inputParams['description'])){
         	return new WP_Error( 'fail', esc_html__( 'The Description parameter is required!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if(!isset($inputParams['short_description'])){
         	return new WP_Error( 'fail', esc_html__( 'The Short Description parameter is required!', 'my-text-domain' ), array( 'status' => 400 ) );
        } */

        if( isset($inputParams['categories']) ){

			if( !is_array($inputParams['categories']) || empty($inputParams['categories']) ){
				return new WP_Error( 'fail', esc_html__( 'Please specify a valid category array format!', 'my-text-domain' ), array( 'status' => 400 ) );
			}

			$categoryFlag = false;

	    	foreach ($inputParams['categories'] as $value) {

	    		if( !is_array($value) || empty($value) || count($value) > 1 || !isset($value['id']) || !is_integer($value['id']) || $value['id']<0 ) {
	    			$categoryFlag = true;
	    		}

	    	}

	    	if($categoryFlag){
	    		return new WP_Error( 'fail', esc_html__( 'Please specify a valid category array format. Categories array must contain a valid numeric id parameter!', 'my-text-domain' ), array( 'status' => 400 ) );
	    	}

        }

        if( isset($inputParams['images']) ){

			if( !is_array($inputParams['images']) || empty($inputParams['images']) ){
				return new WP_Error( 'fail', esc_html__( 'Please specify valid URLs to images in array format!', 'my-text-domain' ), array( 'status' => 400 ) );
			}

			$imagesFlag = false;

	    	foreach ($inputParams['images'] as $value){

	    		if( !is_array($value) || empty($value) || count($value) > 1 || !isset($value['src']) ){
	    			$imagesFlag = true;
	    		}

				$exts = array('jpg', 'gif', 'png', 'jpeg');
	    		if( !( filter_var($value['src'], FILTER_VALIDATE_URL) && in_array(strtolower(pathinfo($value['src'], PATHINFO_EXTENSION)), $exts) ) )
	    		{
	    			$imagesFlag = true;
	    		}

	    	}

	    	if($imagesFlag){
	    		return new WP_Error( 'fail', esc_html__( 'Please specify valid URLs to images in array format. Images array must contain a valid image URL in src. Image type can be one of these: (jpg, gif, png, jpeg).', 'my-text-domain' ), array( 'status' => 400 ) );
	    	}

        }

        if( empty($inputParams['manage_stock']) || !is_bool($inputParams['manage_stock']) ){
         	return new WP_Error( 'fail', esc_html__( 'The Manage Stock parameter is required and must be a boolean (true/false) value!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( !isset($inputParams['stock_quantity']) || !is_integer($inputParams['stock_quantity']) || $inputParams['stock_quantity']<0 ){
         	return new WP_Error( 'fail', esc_html__( 'The Stock Quantity parameter is required and must be a valid non-negative number!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( !empty($inputParams['weight']) && (!is_numeric($inputParams['weight']) || $inputParams['weight']<0) ){
         	return new WP_Error( 'fail', esc_html__( 'The Weight parameter must be a valid non-negative numeric value!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( isset($inputParams['dimensions']) ){   // Remove Required validation

			if( !is_array($inputParams['dimensions']) || empty($inputParams['dimensions'])){
				//if( && is_array($inputParams['dimensions'])){
				return new WP_Error( 'fail', esc_html__( 'Please specify a valid list of Dimensions in array format. Dimensions array must contain a valid numeric length, width, and height parameters!', 'my-text-domain' ), array( 'status' => 400 ) );
				//}
			}

			if( isset($inputParams['dimensions']['length'])){

				if((!empty($inputParams['dimensions']['length']) && !is_numeric($inputParams['dimensions']['length'])) ||(!empty($inputParams['dimensions']['length']) &&  $inputParams['dimensions']['length']<=0 )){
					//return new WP_Error( 'fail', esc_html__( 'Please specify a valid non-negative numeric length parameter within Dimensions array!', 'my-text-domain' ), array( 'status' => 400 ) );
					return new WP_Error( 'fail', esc_html__( 'Please specify a valid non-negative numeric length parameter within Dimensions array!', 'my-text-domain' ), array( 'status' => 400 ) );
				}
			}

			if( isset($inputParams['dimensions']['width'])) {
				if((!empty($inputParams['dimensions']['width']) && !is_numeric($inputParams['dimensions']['width'])) ||(!empty($inputParams['dimensions']['width']) &&  $inputParams['dimensions']['width']<=0 )){
					return new WP_Error( 'fail', esc_html__( 'Please specify a valid non-negative numeric width parameter within Dimensions array!', 'my-text-domain' ), array( 'status' => 400 ) );
				}
			}

			if( isset($inputParams['dimensions']['height'])) {

				if((!empty($inputParams['dimensions']['height']) && !is_numeric($inputParams['dimensions']['height'])) ||(!empty($inputParams['dimensions']['height']) &&  $inputParams['dimensions']['height']<=0 )){
					return new WP_Error( 'fail', esc_html__( 'Please specify a valid non-negative numeric height parameter within Dimensions array!', 'my-text-domain' ), array( 'status' => 400 ) );
				}
			}
        }

        if( isset($inputParams['tags']) ){

			if( !is_array($inputParams['tags']) || empty($inputParams['tags']) ){
				return new WP_Error( 'fail', esc_html__( 'Please specify a valid list of Tags in array format. Tags array must contain a valid numeric id parameter!', 'my-text-domain' ), array( 'status' => 400 ) );
			}

			$tagsFlag = false;

	    	foreach ($inputParams['tags'] as $value) {

	    		if( !is_array($value) || empty($value) || count($value) > 1 || !isset($value['id']) || !is_integer($value['id']) || $value['id']<0 ) {
	    			$tagsFlag = true;
	    		}

	    	}

	    	if($tagsFlag){
	    		return new WP_Error( 'fail', esc_html__( 'Please specify a valid list of Tagsin array format. Tags array must contain a valid numeric id parameter!', 'my-text-domain' ), array( 'status' => 400 ) );
	    	}

        }
        if($inputParams['type'] == 'variable' ) {

	        if( isset($inputParams['attributes']) && ( empty($inputParams['attributes']) || !is_array($inputParams['attributes']) ) ){
	         	return new WP_Error( 'fail', esc_html__( 'The Attributes parameter must be in a valid array format!', 'my-text-domain' ), array( 'status' => 400 ) );
	        }

	        if( isset($inputParams['default_attributes']) && ( empty($inputParams['default_attributes']) || !is_array($inputParams['default_attributes']) ) ){
	         	return new WP_Error( 'fail', esc_html__( 'The Default Attributes parameter must be in a valid array format!', 'my-text-domain' ), array( 'status' => 400 ) );
	        }

	        if( isset($inputParams['variations']) && ( empty($inputParams['variations']) || !is_array($inputParams['variations']) ) ){
	         	return new WP_Error( 'fail', esc_html__( 'The Variations parameter must be in a valid array format!', 'my-text-domain' ), array( 'status' => 400 ) );
	        }
	   	}

        if( isset($inputParams['number_of_orders']) && (!is_numeric($inputParams['number_of_orders']) || $inputParams['number_of_orders']<0) ){
         	return new WP_Error( 'fail', esc_html__( 'The Number Of Orders parameter must be a valid non-negative numeric value!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        $store_currency_code = get_woocommerce_currency(); // Get store currency code
        //return $store_currency_code; // Remove Required validation
        /*if( isset($inputParams['ali_currency'])) {
	        if(!empty($inputParams['ali_currency']) && $store_currency_code != strtoupper($inputParams['ali_currency'])){
	         	return new WP_Error( 'fail', esc_html__( 'Currency code mismatch. Please enter a valid three digit ISO standard Currency Code matching with your woo store Currency Code setting!', 'my-text-domain' ), array( 'status' => 400 ) );
	        }
    	}*/

        if( empty($inputParams['ali_store_name']) ){
         	return new WP_Error( 'fail', esc_html__( 'Please enter AliExpress Store Name for this product!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( empty($inputParams['ali_product_url']) || !preg_match( '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' , $inputParams['ali_product_url']) ){
         	return new WP_Error( 'fail', esc_html__( 'Please enter a valid URL for this AliExpress Product!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( empty($inputParams['ali_store_url']) || !preg_match( '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' , $inputParams['ali_store_url']) ){
         	return new WP_Error( 'fail', esc_html__( 'Please enter a valid URL for this AliExpress Store!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( empty($inputParams['ali_store_price_range']) ){
         	return new WP_Error( 'fail', esc_html__( 'Please enter AliExpress Store Price Range for this product!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

		/**********PRODUCT VALIDATIONS ENDS HERE***/

		$title 				= $inputParams['title'];
		$type 				= $inputParams['type'];
		$sku 				= $inputParams['sku'];
		$price 				= $inputParams['price'];
		$regular_price 		= $inputParams['regular_price'];
		$sale_price 		= $inputParams['sale_price'];
		$description 		= $inputParams['description'];
		$short_description 	= $inputParams['short_description'];
		$categories 		= $inputParams['categories'];
		$images 			= $inputParams['images'];
		$manage_stock 		= $inputParams['manage_stock'];
		$stock_quantity 	= $inputParams['stock_quantity'];
		$tags 				= $inputParams['tags'];
		(isset($inputParams['weight'])) ? $weight	= $inputParams['weight'] : $weight = '';
		(isset($inputParams['dimensions'])) ? $dimensions	= $inputParams['dimensions'] : $dimensions = '';
		(isset($inputParams['is_ali_prod'])) ? $is_ali_prod	= $inputParams['is_ali_prod'] : $is_ali_prod = '';

		//Getting product attributes
        $product_attributes = $inputParams['attributes'];
        $default_attributes = $inputParams['default_attributes'];
        $variations 		= $inputParams['variations'];
        $number_of_orders 	= $inputParams['number_of_orders'];
        $ali_product_url 	= $inputParams['ali_product_url'];
        $ali_store_url 		= $inputParams['ali_store_url'];
        $ali_store_name 	= $inputParams['ali_store_name'];
        $ali_store_price_range 	= $inputParams['ali_store_price_range'];
        $ali_currency 		= $inputParams['ali_currency'];

        /*
	        $arrayLength = count($product_attributes);  // get attr option value
			for ($i = 0; $i < $arrayLength; $i++) {
			    foreach($product_attributes[$i] as $get_option) {
			    }
			}
		*/

	   	/** get product SKU **/

		$product_id = get_sku_in_woo($sku);

		if(!empty($product_id)) { /** if product already exits **/

			$response = new WP_Error( 'fail', esc_html__( 'Product already exists. Please select another product.', 'my-text-domain' ), array( 'status' => 400 ) );

		} else {

			$inputs_new = [
				'status'		=>'draft',
				'name'			=> $title,
				'type' 			=> $type,
				'sku' 			=> $sku,
				'price' 		=> $price,
			    'regular_price' => $regular_price ,
			    'sale_price' 	=> $sale_price,
				'description' 	=> $description,
				'short_description' => $short_description,
				'manage_stock' 	=> $manage_stock,
				'stock_quantity' => $stock_quantity,
				'weight' 		=> $weight,
				'dimensions' 	=> $dimensions,
				'categories' 	=> $categories,
				'images' 		=> $images,
				'attributes' 	=> $product_attributes,
				'tags' 			=> $tags,
				'default_attributes' => $default_attributes,
				'variations' 	=> $variations,
				'is_ali_product' => $is_ali_prod
			];

			$woorequest = new WP_REST_Request( 'POST' );
			$woorequest->set_body_params( $inputs_new );
			$products_controller = new WC_REST_Products_Controller;
			$response = $products_controller->create_item( $woorequest );
			$res = $response->data;
			//return $res;

			// The created product must have variations

		    if ( !isset( $res['variations'] ) ){
		        $res['variations'] = array();
		    }
		    if ( count( $res['variations'] ) == 0 && count( $inputs_new['variations'] ) > 0 ) {
		        if ( ! isset( $variations_controler ) ) {
		            $variations_controler = new WC_REST_Product_Variations_Controller();
		        }
		        foreach ( $inputs_new['variations'] as $variation ) {

		            $wp_rest_request = new WP_REST_Request( 'POST' );
		            $variation_rest = array(
		                'product_id' 	=> $res['id'],
		                'regular_price' => $variation['regular_price'],
		                'sale_price' 	=> $variation['sale_price'],
		                'manage_stock' 	=> $variation['manage_stock'],
		                'stock_quantity'=> $variation['stock_quantity'],
		                'attributes' 	=> $variation['attributes'],
		            );
		            $wp_rest_request->set_body_params( $variation_rest );
		            $new_variation = $variations_controler->create_item( $wp_rest_request );
		            $res['variations'][] = $new_variation->data;
		        }
		    }

		    // Add aditional fields
			$product_id = $res['id'];
			update_post_meta($product_id,'number_of_orders',$number_of_orders);
			update_post_meta($product_id,'ali_product_url',$ali_product_url);
			update_post_meta($product_id,'ali_store_url',$ali_store_url);
			update_post_meta($product_id,'ali_store_name',$ali_store_name);
			update_post_meta($product_id,'ali_store_price_range',$ali_store_price_range);
			update_post_meta($product_id,'ali_currency',$ali_currency);
			update_post_meta($product_id,'_is_ali_product',$is_ali_prod);

			/* $product = [
				'id' => '26',
				'product_id' => '26'
			];
			$woorequest = new WP_REST_Request( 'POST' );
			$woorequest->set_body_params( $product );
			$products_controller = new WC_REST_Products_Controller;
			$response = $products_controller->get_item( $woorequest ); */

			/* $fp = fopen("000AA.txt","a");
			fwrite($fp, print_r($response,1));
			fclose($fp); */

			if(isset($response->status) && $response->status <= 201) {

				// Following should be used whenever we want to return success:
				$response = array(
					"code" => 'success',
					"message" => 'Product created successfully.',
					"data" => array(
						"status" => 200
					)
				);

			} else {
				$response = new WP_Error( 'fail', esc_html__( 'Oops! Something went wrong. Please try again!', 'my-text-domain' ), array( 'status' => 400 ) );
			}
		}

	    return $response;

		//return rest_ensure_response($response);

	} // function import_ali_product_in_woo()
	// http://mytestsite.com/wp-json/woo-aliexpress/v1/product




	if (!function_exists('get_product_category_from_woo')) {

		function get_product_category_from_woo() {
			$get_featured_cats = array(
				'taxonomy'     => 'product_cat',
				'orderby'      => 'term_id',
				'hide_empty'   => '0'
			);

			$all_categories = get_categories( $get_featured_cats );
			//return $all_categories;
			$cat_list = array();
			foreach ($all_categories as $cat) {

				$cat_list[] = array(
					"id" => $cat->term_id,
					"name" => $cat->name,
					"slug" => $cat->slug,
					"category_parent" =>$cat->category_parent
				);
			}
			//return $cat_list;


			if(!empty($cat_list)) {

				// Following should be used whenever we want to return success:
				$response = array(
					"code" => 'success',
					"message" => 'Here are the woo categories list.',
					"data" => array(
						"status" => 200
					),
					"content" => $cat_list
				);

			} else {
				$response = new WP_Error( 'fail', esc_html__( 'No categories found!', 'my-text-domain' ), array( 'status' => 400 ) );
			}

		    return $response;

		}	// function product_category_from_woo()

	}
	//http://testprey.avitinfotech.com/wp-json/woo-aliexpress/v1/products-categories



	if (!function_exists('create_product_category_in_woo')) {

		function create_product_category_in_woo(WP_REST_Request $request) {

			$inputcat = $request->get_json_params();
			//return $inputcat;

			/*************PRODUCT CATEGORIES VALIDATIONS STARTS HERE*************/
            if(!isset($inputcat['name']) || empty($inputcat['name'])){
         		return new WP_Error( 'fail', esc_html__( 'The Name parameter is required!', 'my-text-domain' ), array( 'status' => 400 ) );
            }

            if(!isset($inputcat['description'])){
         		return new WP_Error( 'fail', esc_html__( 'The Description parameter is required!', 'my-text-domain' ), array( 'status' => 400 ) );
            }

            if(!isset($inputcat['category_parent']) || !is_numeric($inputcat['category_parent']) ){
         		return new WP_Error( 'fail', esc_html__( 'The Category Parent parameter is required and must be numeric value!', 'my-text-domain' ), array( 'status' => 400 ) );
            }
			/*************PRODUCT CATEGORIES VALIDATIONS ENDS HERE*************/
			$get_featured_cats = array(
				'taxonomy'     => 'product_cat',
				'orderby'      => 'term_id',
				'hide_empty'   => '0'
			);

			$all_categories = get_categories( $get_featured_cats );
			//return $all_categories;

			foreach ($all_categories as $cat) {

				$cat_id[] =  $cat->term_id;
				//print_r($cat_id);
			}

			$name 				= $inputcat['name'];
			$description 		= $inputcat['description'];
			$category_parent 	= $inputcat['category_parent'];

			$attr = array(
			    'description' => $description, // optional
			    'parent' => $category_parent // optional
			);

			if(in_array($category_parent, $cat_id)) {

				if(isset($name) && !empty($name)) {
					$response = wp_insert_term( $name, 'product_cat', $attr );

						// Following should be used whenever we want to return success:
					$response = array(
						"code" => 'success',
						"message" => 'Product category created successfully.',
						"data" => array(
							"status" => 200
						)
					);

				} else {
					$response = new WP_Error( 'fail', esc_html__( 'Name parameter required.', 'my-text-domain' ), array( 'status' => 400 ) );
				}
			} else if(isset($name) && !empty($name)) {

					$response = wp_insert_term( $name, 'product_cat', $attr );

					// Following should be used whenever we want to return success:
					$response = array(
						"code" => 'success',
						"message" => 'Product category created successfully.',
						"data" => array(
							"status" => 200
						)
					);

			} else {

				$response = new WP_Error( 'fail', esc_html__( 'Given parent categories id does not exist in product categories list.', 'my-text-domain' ), array( 'status' => 400 ) );
			}

			return $response;

		}	// function create_product_category_in_woo()
	}
	// http://testprey.avitinfotech.com/wp-json/woo-aliexpress/v1/create-categories



	if (!function_exists('get_order_detail_by_id')) {
		//to get full order details
	    function get_order_detail_by_id(WP_REST_Request $request) {

	        $inputParams = $request->get_json_params();

			/*************ORDER VALIDATIONS STARTS HERE*******************/
            if(!isset($inputParams['id']) || !is_integer($inputParams['id'])|| empty($inputParams['id'])){
         		return new WP_Error( 'fail', esc_html__( 'The Id parameter is required and must be a number!', 'my-text-domain' ), array( 'status' => 400 ) );
            }
            /**************ORDER VALIDATIONS ENDS HERE*************/

			$order_id = $inputParams['id'];

			$order_ids = [
				'id' => $order_id
			];

	        // Get the decimal precession
	        $dp = (isset($filter['dp'])) ? intval($filter['dp']) : 2;
	        // getting order Object
	        $order = wc_get_order($order_id);
	        /*echo '<pre>';
	        print_r($order);
	        die;*/
	        // If Order id exist in woo
	        if (!$order == "shop_order") {

	        	return new WP_Error( 'fail', esc_html__( 'The order id does not exist.', 'my-text-domain' ), array( 'status' => 400 ) );
	        }
	        //return gettype($order_id);
	        $order_data = array(
	            'id' => $order->get_id(),
	            'total' => wc_format_decimal($order->get_total(), $dp),
	            'total_line_items_quantity' => $order->get_item_count(),

	            /*'payment_details' => array(
	                'method_id' => $order->get_payment_method(),
	                'method_title' => $order->get_payment_method_title(),
	            ),*/

	            'billing_address' => array(
	                'first_name' => $order->get_billing_first_name(),
	                'last_name' => $order->get_billing_last_name(),
	                'address_1' => $order->get_billing_address_1(),
	                'address_2' => $order->get_billing_address_2(),
	                'city' => $order->get_billing_city(),
	                'state' => $order->get_billing_state(),
	                'postcode' => $order->get_billing_postcode(),
	                'country' => $order->get_billing_country(),
	                'phone' => $order->get_billing_phone()
	            ),
	            'shipping_address' => array(
	                'first_name' => $order->get_shipping_first_name(),
	                'last_name' => $order->get_shipping_last_name(),
	                 'company' => $order->get_shipping_company(),
	                'address_1' => $order->get_shipping_address_1(),
	                'address_2' => $order->get_shipping_address_2(),
	                'city' =>      $order->get_shipping_city(),
	                'state' =>     $order->get_shipping_state(),
	                'postcode' => $order->get_shipping_postcode(),
	                'country' => $order->get_shipping_country()
	                //'phone' => $order->get_shipping_phone()
	            ),


	            'line_items' => array(),
	            //'shipping_lines' => array(),
	        );
	        //getting all line items
	        foreach ($order->get_items() as $item_id => $item) {
	            $product = $item->get_product();
	            $product_id = null;
	            $product_sku = null;
	            //$get_variation_id = null;
	            // Check if the product exists.
	            if (is_object($product)) {
	                $product_id = $product->get_id();
	                $product_sku = $product->get_sku();
	                //$get_variation_id = $product->get_variation_id();
	                // return $get_variation_id;
	            }

	            $order_data['line_items'][] = array(
	                'id' => $item_id,
	                'total' => wc_format_decimal($order->get_line_total($item, false, false), $dp),
	                'quantity' => wc_stock_amount($item['qty']),
	                'name' => $item['name'],
	                'product_id' => (!empty($item->get_variation_id()) && ('product_variation' === $product->post_type )) ? $product->get_parent_id() : $product_id,
	                'variation_id' => (!empty($item->get_variation_id()) && ('product_variation' === $product->post_type )) ? $product_id : 0,
	                'product_url' => get_permalink($product_id),
	                'sku' => $product_sku,
	                'ali_product_url' => get_post_meta($product->get_parent_id(), 'ali_product_url', true),
	                'attributes' => $product->get_attributes(),
	            );


	        }
	        /*
	        // getting shipping
	        foreach ($order->get_shipping_methods() as $shipping_item_id => $shipping_item) {
	            $order_data['shipping_lines'][] = array(
	                'id' => $shipping_item_id,
	                'method_id' => $shipping_item['method_id'],
	                'method_title' => $shipping_item['name'],
	                'total' => wc_format_decimal($shipping_item['cost'], $dp),
	            );
	        }*/

	        if (!empty($order)) {

	            // Following should be used whenever we want to return success:
				$response = array(
					"code" => 'success',
					"message" => 'Here are the woo order details.',
					"data" => array(
						"status" => 200
					),
					"content" => $order_data
				);
	        }

	    	return $response;

	    } // function get_order_detail_by_id()

	}
	// http://mytestsite.com/wp-json/woo-aliexpress/v1/order-details

	if (!function_exists('woo_authentication')) {

		function woo_authentication(WP_REST_Request $request) {

			$inputauth = $request->get_json_params();
			/*************AUTHENTICATION VALIDATIONS STARTS HERE*************/

            if(!isset($inputauth['woo_url']) || empty($inputauth['woo_url'])){
         		return new WP_Error( 'fail', esc_html__( 'The woo url parameter is required!', 'my-text-domain' ), array( 'status' => 400 ) );
            }

            if(!isset($inputauth['woo_api_key']) || empty($inputauth['woo_api_key'])){
         		return new WP_Error( 'fail', esc_html__( 'The woo API key  parameter is required!', 'my-text-domain' ), array( 'status' => 400 ) );
            }

			/*************AUTHENTICATION VALIDATIONS ENDS HERE*************/
			/**************************************************************/
			/*************SITE URL VALIDATIONS START HERE******************/

		    $input_woo_url = site_url();
		    // in case scheme relative URI is passed, e.g., //www.google.com/
			$input_woo_url = trim($input_woo_url, '/');
			if (!preg_match('#^http(s)?://#', $input_woo_url)) {
			    $input_woo_url = 'http://' . $input_woo_url;
			}
			$urlParts = parse_url($input_woo_url);
			$domain = preg_replace('/^www\./', '', $urlParts['host']); // remove www
			//return $domain;

			/*************SITE URL VALIDATIONS ENDS HERE*************/
			/**************************************************************/
			/*************API URL VALIDATIONS START HERE******************/

			$woo_url 		= $inputauth['woo_url'];
			$woo_api_key 	= $inputauth['woo_api_key'];

			$url = filter_var($woo_url, FILTER_SANITIZE_URL);
			// Validate url
			if(filter_var($url, FILTER_VALIDATE_URL) !== false) {

				$auth = array(
				    'woo_url' => $url,
				    'woo_api_key' => $woo_api_key
				);

				$input_api_url = $auth['woo_url'];
				$input_api_url = trim($input_api_url, '/');
				if (!preg_match('#^http(s)?://#', $input_api_url)) {
				    $input_api_url = 'http://' . $input_api_url;
				}
				$urlParts = parse_url($input_api_url);
				$woo_api_domain = preg_replace('/^www\./', '', $urlParts['host']);
				//return $woo_api_domain;
				if($domain == $woo_api_domain) {
					//return $woo_api_domain;

					/*************API URL VALIDATIONS ENDS HERE*************/
					/**************************************************************/
					/*************API KEY VALIDATIONS START HERE******************/

					$woo_key = generate_aliexpress_key($domain);
					//return $woo_key;
					$woo_api_key = $auth['woo_api_key'];

					if($woo_api_key == $woo_key ){
						//$response = $auth;

						// Following should be used whenever we want to return success:
						$response = array(
							"code" => 'success',
							"message" => 'Woo Authentication success.',
							"data" => array(
								"status" => 200
							)
						);

					} else {

						$response = new WP_Error( 'fail', esc_html__( 'API Key Mismatch!!', 'my-text-domain' ), array( 'status' => 400 ) );
					}
				} else {

						$response = new WP_Error( 'fail', esc_html__( 'URL mismatch.', 'my-text-domain' ), array( 'status' => 400 ) );
					}

			} else {

				$response = new WP_Error( 'fail', esc_html__( 'Oops! Something went wrong. Please try again!', 'my-text-domain' ), array( 'status' => 400 ) );
			}
			return $response;

		}	// function woo_authentication()

	}
	// http://mytestsite.com/wp-json/woo-aliexpress/v1/woo-authentication


	// Public function for getting sku //
	function get_sku_in_woo( $sku ) {
		global $wpdb;

        $product_id = $wpdb->get_var('SELECT post_id FROM '.$wpdb->postmeta.' WHERE meta_key="_sku" AND meta_value LIKE "%'.$sku.'%"');
        if($product_id){

        	return $product_id;
    	} else {
    		return '';
    	}
	} // get_sku_in_woo


	// For update products

	function update_ali_product_in_woo(WP_REST_Request $request) {

		$inputParams = $request->get_json_params();
		//return $inputParams;

		/**********PRODUCT VALIDATIONS STARTS HERE***/
        if( empty($inputParams['title']) ){
         	return new WP_Error( 'fail', esc_html__( 'The Title parameter is required!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( empty($inputParams['type']) || !in_array($inputParams['type'],array('simple','variable')) ){
         	return new WP_Error( 'fail', esc_html__( 'Please enter a valid Type (simple or variable)!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( empty($inputParams['sku']) ){
         	return new WP_Error( 'fail', esc_html__( 'The SKU parameter is required!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( empty($inputParams['price']) || !is_numeric($inputParams['price']) || $inputParams['price']<0 ){
         	return new WP_Error( 'fail', esc_html__( 'The Price parameter is required and must be a valid non-negative numeric value!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( empty($inputParams['regular_price']) || !is_numeric($inputParams['regular_price']) || $inputParams['regular_price']<0 ){
         	return new WP_Error( 'fail', esc_html__( 'The Regular Price parameter is required and must be a valid non-negative numeric value!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( !empty($inputParams['sale_price']) && (!is_numeric($inputParams['sale_price']) || $inputParams['sale_price']<0) ){
         	return new WP_Error( 'fail', esc_html__( 'The Sale Price parameter must be a valid non-negative numeric value!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        /* if(!isset($inputParams['description'])){
         	return new WP_Error( 'fail', esc_html__( 'The Description parameter is required!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if(!isset($inputParams['short_description'])){
         	return new WP_Error( 'fail', esc_html__( 'The Short Description parameter is required!', 'my-text-domain' ), array( 'status' => 400 ) );
        } */

        if( isset($inputParams['categories']) ){

			if( !is_array($inputParams['categories']) || empty($inputParams['categories']) ){
				return new WP_Error( 'fail', esc_html__( 'Please specify a list of valid categories in array format!', 'my-text-domain' ), array( 'status' => 400 ) );
			}

			$categoryFlag = false;

	    	foreach ($inputParams['categories'] as $value) {

	    		if( !is_array($value) || empty($value) || count($value) > 1 || !isset($value['id']) || !is_integer($value['id']) || $value['id']<0 ) {
	    			$categoryFlag = true;
	    		}

	    	}

	    	if($categoryFlag){
	    		return new WP_Error( 'fail', esc_html__( 'Please specify a list of valid categories in array format. Categories array must contain a valid numeric id parameter!', 'my-text-domain' ), array( 'status' => 400 ) );
	    	}

        }

        if( isset($inputParams['images']) ){

			if( !is_array($inputParams['images']) || empty($inputParams['images']) ){
				return new WP_Error( 'fail', esc_html__( 'Please specify a list of valid images URLs in array format!', 'my-text-domain' ), array( 'status' => 400 ) );
			}

			$imagesFlag = false;

	    	foreach ($inputParams['images'] as $value){

	    		if( !is_array($value) || empty($value) || count($value) > 1 || !isset($value['src']) ){
	    			$imagesFlag = true;
	    		}

				$exts = array('jpg', 'gif', 'png', 'jpeg');
	    		if( !( filter_var($value['src'], FILTER_VALIDATE_URL) && in_array(strtolower(pathinfo($value['src'], PATHINFO_EXTENSION)), $exts) ) )
	    		{
	    			$imagesFlag = true;
	    		}

	    	}

	    	if($imagesFlag){
	    		return new WP_Error( 'fail', esc_html__( 'Please specify a list of valid image URLs in array format. Images array must contain a valid image URL in src. Image type can be one of these: (jpg, gif, png, jpeg).', 'my-text-domain' ), array( 'status' => 400 ) );
	    	}

        }

        if( empty($inputParams['manage_stock']) || !is_bool($inputParams['manage_stock']) ){
         	return new WP_Error( 'fail', esc_html__( 'The Manage Stock parameter is required and must be a boolean (true/false) value!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( !isset($inputParams['stock_quantity']) || !is_integer($inputParams['stock_quantity']) || $inputParams['stock_quantity']<0 ){
         	return new WP_Error( 'fail', esc_html__( 'The Stock Quantity parameter is required and must be a valid non-negative number!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( !empty($inputParams['weight']) && (!is_numeric($inputParams['weight']) || $inputParams['weight']<0) ){
         	return new WP_Error( 'fail', esc_html__( 'The Weight parameter must be a valid non-negative numeric value!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( isset($inputParams['dimensions']) ){   // Remove Required validation

			if( !is_array($inputParams['dimensions']) || empty($inputParams['dimensions'])){
				//if( && is_array($inputParams['dimensions'])){
				return new WP_Error( 'fail', esc_html__( 'Please specify a valid list of Dimensions in array format. Dimensions array must contain a valid numeric length, width, and height parameters!', 'my-text-domain' ), array( 'status' => 400 ) );
				//}
			}

			if( isset($inputParams['dimensions']['length'])){

				if((!empty($inputParams['dimensions']['length']) && !is_numeric($inputParams['dimensions']['length'])) ||(!empty($inputParams['dimensions']['length']) &&  $inputParams['dimensions']['length']<=0 )){
					//return new WP_Error( 'fail', esc_html__( 'Please specify a valid non-negative numeric length parameter within Dimensions array!', 'my-text-domain' ), array( 'status' => 400 ) );
					return new WP_Error( 'fail', esc_html__( 'Please specify a valid non-negative numeric length parameter within Dimensions array!', 'my-text-domain' ), array( 'status' => 400 ) );
				}
			}

			if( isset($inputParams['dimensions']['width'])) {
				if((!empty($inputParams['dimensions']['width']) && !is_numeric($inputParams['dimensions']['width'])) ||(!empty($inputParams['dimensions']['width']) &&  $inputParams['dimensions']['width']<=0 )){
					return new WP_Error( 'fail', esc_html__( 'Please specify a valid non-negative numeric width parameter within Dimensions array!', 'my-text-domain' ), array( 'status' => 400 ) );
				}
			}

			if( isset($inputParams['dimensions']['height'])) {

				if((!empty($inputParams['dimensions']['height']) && !is_numeric($inputParams['dimensions']['height'])) ||(!empty($inputParams['dimensions']['height']) &&  $inputParams['dimensions']['height']<=0 )){
					return new WP_Error( 'fail', esc_html__( 'Please specify a valid non-negative numeric height parameter within Dimensions array!', 'my-text-domain' ), array( 'status' => 400 ) );
				}
			}
        }

        if( isset($inputParams['tags']) ){

			if( !is_array($inputParams['tags']) || empty($inputParams['tags']) ){
				return new WP_Error( 'fail', esc_html__( 'Please specify a valid list of Tags in array format. Tags array must contain a valid numeric id parameter!', 'my-text-domain' ), array( 'status' => 400 ) );
			}

			$tagsFlag = false;

	    	foreach ($inputParams['tags'] as $value) {

	    		if( !is_array($value) || empty($value) || count($value) > 1 || !isset($value['id']) || !is_integer($value['id']) || $value['id']<0 ) {
	    			$tagsFlag = true;
	    		}

	    	}

	    	if($tagsFlag){
	    		return new WP_Error( 'fail', esc_html__( 'Please specify a valid list of Tags array format. Tags array must contain a valid numeric id parameter!', 'my-text-domain' ), array( 'status' => 400 ) );
	    	}

        }
        if($inputParams['type'] == 'variable' ) {

	        if( isset($inputParams['attributes']) && ( empty($inputParams['attributes']) || !is_array($inputParams['attributes']) ) ){
	         	return new WP_Error( 'fail', esc_html__( 'The Attributess parameter must be in a valid array format!', 'my-text-domain' ), array( 'status' => 400 ) );
	        }

	        if( isset($inputParams['default_attributes']) && ( empty($inputParams['default_attributes']) || !is_array($inputParams['default_attributes']) ) ){
	         	return new WP_Error( 'fail', esc_html__( 'The Default Attributess parameter must be in a valid array format!', 'my-text-domain' ), array( 'status' => 400 ) );
	        }

	        if( isset($inputParams['variations']) && ( empty($inputParams['variations']) || !is_array($inputParams['variations']) ) ){
	         	return new WP_Error( 'fail', esc_html__( 'The Variations parameter must be in a valid array format!', 'my-text-domain' ), array( 'status' => 400 ) );
	        }
	   	}

        if( isset($inputParams['number_of_orders']) && (!is_numeric($inputParams['number_of_orders']) || $inputParams['number_of_orders']<0) ){
         	return new WP_Error( 'fail', esc_html__( 'The Number Of Orders parameter must be a valid non-negative numeric value!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        $store_currency_code = get_woocommerce_currency(); // Get store currency code
        //return $store_currency_code; // Remove Required validation
        /*if( isset($inputParams['ali_currency'])) {
	        if(!empty($inputParams['ali_currency']) && $store_currency_code != strtoupper($inputParams['ali_currency'])){
	         	return new WP_Error( 'fail', esc_html__( 'Currency code mismatch. Please enter a valid three digit ISO standard Currency Code matching with your woo store Currency Code setting!', 'my-text-domain' ), array( 'status' => 400 ) );
	        }
    	}*/

        if( empty($inputParams['ali_store_name']) ){
         	return new WP_Error( 'fail', esc_html__( 'Please enter AliExpress Store Name for this product!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( empty($inputParams['ali_product_url']) || !preg_match( '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' , $inputParams['ali_product_url']) ){
         	return new WP_Error( 'fail', esc_html__( 'Please enter a valid URL for this AliExpress Product!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( empty($inputParams['ali_store_url']) || !preg_match( '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' , $inputParams['ali_store_url']) ){
         	return new WP_Error( 'fail', esc_html__( 'Please enter a valid URL for this AliExpress Store!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

        if( empty($inputParams['ali_store_price_range']) ){
         	return new WP_Error( 'fail', esc_html__( 'Please enter AliExpress Store Price Range for this product!', 'my-text-domain' ), array( 'status' => 400 ) );
        }

		/**********PRODUCT VALIDATIONS ENDS HERE***/

		$title 				= $inputParams['title'];
		$type 				= $inputParams['type'];
		$sku 				= $inputParams['sku'];
		$price 				= $inputParams['price'];
		$regular_price 		= $inputParams['regular_price'];
		$sale_price 		= $inputParams['sale_price'];
		$description 		= $inputParams['description'];
		$short_description 	= $inputParams['short_description'];
		$categories 		= $inputParams['categories'];
		$images 			= $inputParams['images'];
		$manage_stock 		= $inputParams['manage_stock'];
		$stock_quantity 	= $inputParams['stock_quantity'];
		$tags 				= $inputParams['tags'];
		(isset($inputParams['weight'])) ? $weight	= $inputParams['weight'] : $weight = '';
		(isset($inputParams['dimensions'])) ? $dimensions	= $inputParams['dimensions'] : $dimensions = '';
		(isset($inputParams['is_ali_prod'])) ? $is_ali_prod	= $inputParams['is_ali_prod'] : $is_ali_prod = '';

		//Getting product attributes
        $product_attributes = $inputParams['attributes'];
        $default_attributes = $inputParams['default_attributes'];
        $variations 		= $inputParams['variations'];
        $number_of_orders 	= $inputParams['number_of_orders'];
        $ali_product_url 	= $inputParams['ali_product_url'];
        $ali_store_url 		= $inputParams['ali_store_url'];
        $ali_store_name 	= $inputParams['ali_store_name'];
        $ali_store_price_range 	= $inputParams['ali_store_price_range'];
        $ali_currency 		= $inputParams['ali_currency'];


		/** get product SKU **/

		$product_id = get_sku_in_woo($sku);
		$product = wc_get_product( $product_id );
		if(empty($product)) { /** if product already exits **/

			$response = new WP_Error( 'fail', esc_html__( 'Error. Product does not exist in woocommerce store.', 'my-text-domain' ), array( 'status' => 400 ) );

		} else {

			$inputs_new = [
				'id'			=> $product->id,
				'name'			=> $title,
				'type' 			=> $type,
				'sku' 			=> $sku,
				'price' 		=> $price,
			    'regular_price' => $regular_price ,
			    'sale_price' 	=> $sale_price,
				'description' 	=> $description,
				'short_description' => $short_description,
				'manage_stock' 	=> $manage_stock,
				'stock_quantity' => $stock_quantity,
				'weight' 		=> $weight,
				'dimensions' 	=> $dimensions,
				'categories' 	=> $categories,
				'images' 		=> $images,
				'attributes' 	=> $product_attributes,
				'tags' 			=> $tags,
				'default_attributes' => $default_attributes,
				'variations' 	=> $variations,
				'is_ali_product' => $is_ali_prod
			];
			//return $inputs_new;
			$woorequest = new WP_REST_Request( 'POST' );
			$woorequest->set_body_params( $inputs_new );
			$products_controller = new WC_REST_Products_Controller;
			$response = $products_controller->update_item( $woorequest );
			$res = $response->data;

		    if ( count( $res['variations'] ) == 0 || count( $inputs_new['variations'] ) > 0 ) {
		    	//return $inputs_new['variations'] ;
		        if ( ! isset( $variations_controler ) ) {
		            $variations_controler = new WC_REST_Product_Variations_Controller();
		        }
		        $i= 0;
		        foreach ( $inputs_new['variations'] as $variation ) {

		            $wp_rest_request = new WP_REST_Request( 'POST' );

		            $variation_rest = array(
		                'product_id' 	=> $res['id'],
		                'regular_price' => $variation['regular_price'],
		                'sale_price' 	=> $variation['sale_price'],
		                'manage_stock' 	=> $variation['manage_stock'],
		                'stock_quantity'=> $variation['stock_quantity'],
		                'attributes' 	=> $variation['attributes'],

		            );

		            if(isset($res['variations'][$i]) && !empty($res['variations'][$i]))
		            {
		            	$variation_rest['id'] = $res['variations'][$i];
		            	$wp_rest_request->set_body_params( $variation_rest );
		            	$new_variation = $variations_controler->update_item( $wp_rest_request );
		            }
		            else
		            {
		            	$wp_rest_request->set_body_params( $variation_rest );
		            	$new_variation = $variations_controler->create_item( $wp_rest_request );
		            }

		            // $res['variations'][] = $new_variation->data;
		            $i++;

		        }

		    }

		    // Add aditional fields
			$product_id = $res['id'];
			update_post_meta($product_id,'number_of_orders',$number_of_orders);
			update_post_meta($product_id,'ali_product_url',$ali_product_url);
			update_post_meta($product_id,'ali_store_url',$ali_store_url);
			update_post_meta($product_id,'ali_store_name',$ali_store_name);
			update_post_meta($product_id,'ali_store_price_range',$ali_store_price_range);
			update_post_meta($product_id,'ali_currency',$ali_currency);
			update_post_meta($product_id,'_is_ali_product',$is_ali_prod);


			if(isset($response->status) && $response->status <= 201) {

				// Following should be used whenever we want to return success:
				$response = array(
					"code" => 'success',
					"message" => 'Product successfully updated.',
					"data" => array(
						"status" => 200
					)
				);

			} else {
				$response = new WP_Error( 'fail', esc_html__( 'Something went wrong. Please try again!', 'my-text-domain' ), array( 'status' => 400 ) );
			}
		}

	    return $response;

		//return rest_ensure_response($response);

	} // function import_ali_product_in_woo()
	// http://mytestsite.com/wp-json/woo-aliexpress/v1/update-product


	// Getting Merchant store currency code //
	function get_merchant_store_currency_code_in_woo() {

		$store_currency_code = get_woocommerce_currency(); // Get store currency code

		if(!empty($store_currency_code)) {

			// Following should be used whenever we want to return success:
			$response = array(
				"code" => 'success',
				"message" => 'Merchant store currency code found.',
				"data" => array(
					"status" => 200
				),
				"content" => $store_currency_code
			);

		} else {
			$response = new WP_Error( 'fail', esc_html__( 'Merchant store currency code not found!', 'my-text-domain' ), array( 'status' => 400 ) );
		}

		return $response;

	} // get_merchant_store_currency_code_in_woo
	// http://mytestsite.com/wp-json/woo-aliexpress/v1/store-currency-code


	/*******************************************************/
	/*    CUSTOM META BOX FOR Place Order Automatically    *
	/*******************************************************/
	function place_order_automatically_meta_boxes() {
		$options = get_option( 'wc_dropship_manager' );
		$ali_cbe_enable_setting = $options['ali_cbe_enable_name'];
		if (isset($ali_cbe_enable_setting)){
			if ($ali_cbe_enable_setting == '1'){
		    add_meta_box(
		        'woocommerce',
		        __( 'AliExpress Action' ),
		        'place_Order_automatically_meta_box_content',
		        'shop_order',
		        'side'
		    );
			}
		}
	}


function place_order_automatically_meta_box_content(){

	    global $post;
		setup_postdata( $post );
		$order_id = get_the_ID();
		$order = wc_get_order( $order_id );
		$url  = array();
		foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            if(isset($product) && !empty($product)){
	            if(!empty(get_post_meta($product->get_parent_id(), 'ali_product_url', true))){
	            	$url[] = get_post_meta($product->get_parent_id(), 'ali_product_url', true);
	        	}
          	}
	    }

	    $count = count($url);
	    if(!empty($url)) {
			//echo $order_id;
		    $button_text = __( 'Place Order', 'woocommerce' );
		    $url = admin_url("admin-ajax.php", null);

		    echo '<input type="button" style="display:none;" order_id="'.$order_id.'" class="button save_order button-primary" url="'.$url.'" id="opmc_ali_place_order" value="' . $button_text . '"/>';
		    echo '<input type="hidden" id="order_id" value="' . $order_id . '"/>';
		    // wp_die($order_id);
		}
	}

	//Getting AliExpress Product URL and open in new tab
	function get_order_data(){

		$id = $_REQUEST['id'];
		$order = wc_get_order( $id );
		$url  = array();
		foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();

            $checkurl= get_post_meta($product->get_parent_id(), 'ali_product_url', true);
            if(!empty($checkurl)){
            $url[] =  $checkurl;
            }
	    }

	    echo json_encode($url);
	    wp_die();

	}

	/***********/
	/*  END    *
	/***********/


	/*******************************************************/
	/*    CUSTOM META BOX FOR AliExpress Order Status      *
	/*******************************************************/



	function select_custom_order_status($post){
		$options = get_option( 'wc_dropship_manager' );
		$ali_cbe_enable_setting = $options['ali_cbe_enable_name'];
		if (isset($ali_cbe_enable_setting)){
			if ($ali_cbe_enable_setting == '1'){
  			add_meta_box('opmc-aliExpress-modal', 'AliExpress Order Status', 'status_of_aliexpress', 'shop_order', 'side');
			}
		}
	}


	function status_save_metabox(){
	    global $post;
	    if(isset($_POST["custom_aliexpress_class"])){
	         //UPDATE:
	        $meta_element_class = $_POST['custom_aliexpress_class'];
	        //END OF UPDATE

	        update_post_meta($post->ID, 'status_of_aliexpress', $meta_element_class);

	        // If you don't have the WC_Order object (from a dynamic $order_id)
			$order = wc_get_order( $post->ID );
			// The text for the note
			$note = __('AliExpress order status has been changed to "'.$meta_element_class.'"');
			// Add the note
			$order->add_order_note( $note );
	        //print_r($_POST);
	    }
	}

	function status_of_aliexpress($post){

		global $post;
	    $meta_element_class = get_post_meta($post->ID, 'status_of_aliexpress', true); //true ensures you get just one value instead of an array
	    setup_postdata( $post );
		$order_id = get_the_ID();
		$order = wc_get_order( $order_id );
		$url  = array();
		foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            if(isset($product) && !empty($product)){
	            if(!empty(get_post_meta($product->get_parent_id(), 'ali_product_url', true))){
	            	$url[] = get_post_meta($product->get_parent_id(), 'ali_product_url', true);
	        	}
          	}
	    }

	    $count = count($url);
	    if(!empty($url)) { ?>

		    <select name="custom_aliexpress_class" id="custom_aliexpress_class">
		    	<option value="Order Pending" <?php selected( $meta_element_class, 'Order Pending' ); ?>>Order Pending</option>
		      <option value="Order Placed" <?php selected( $meta_element_class, 'Order Placed' ); ?>>Order Placed</option>

		    </select>
	    	<?php
		}
	}

	/***********/
	/*  END    *
	/***********/

	/*************************************************************************************************/
	/*  AliExpress Order (List of AliExpress product placed or not) Show in column AliExpress Status *
	/*************************************************************************************************/

	function custom_woo_columns_function( $columns ) {

	    $new_columns = ( is_array( $columns ) ) ? $columns : array();
	    unset( $new_columns[ 'order_actions' ] );

	    // all of your columns will be added before the actions column
	    $new_columns['status_of_aliexpress'] = 'AliExpress Status';

	    //stop editing
	    @$new_columns[ 'order_actions' ] = @$columns[ 'order_actions' ];
	    return $new_columns;
	}

		// Change order of columns (working)

	function custom_woo_admin_value( $column ) {

		global $post;
	    $zip_value = get_post_meta($post->ID, 'status_of_aliexpress', true);
	    setup_postdata( $post );
		$order_id = get_the_ID();
		$order = wc_get_order( $order_id );
		$url  = array();
		foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            if(isset($product) && !empty($product)){
	            if(!empty(get_post_meta($product->get_parent_id(), 'ali_product_url', true))){
	            	$url[] = get_post_meta($product->get_parent_id(), 'ali_product_url', true);
	        	}
          	}
	    }

	    $count = count($url);
	    if ( $column == 'status_of_aliexpress' ) {

	    	if(empty($zip_value) && $count > 0 ){

	    		echo '<mark class="order-status status-refunded tips"><span>Order Pending</span></mark>';
	    	} else {
	    		echo '<mark class="order-status status-refunded tips"><span>'.$zip_value.'</span></mark>';
	    	}


	    }
	}

	/***********/
	/*  END    *
	/***********/


	/***********************************************************************/
	/* AliExpress Order Filter (List of AliExpress product placed or not)  *
	/***********************************************************************/

	function aliexpress_status_filter_to_shop_order_posts_administration(){

	    global $post_type;
	    if($post_type == 'shop_order'){

		    $values = array(
		        'Order Placed' => 'Order_Placed',
		        'Order Pending'    => 'Order_Pending',

		    );
	    	?>

		    <select name="aliexpress_status_filter">
		    <option value=""><?php _e('All Orders', 'pv-mag'); ?></option>
		    	<?php
		        $current_v = isset($_GET['aliexpress_status_filter']) ? $_GET['aliexpress_status_filter'] : '';
		        foreach ($values as $label => $value) {
		            printf
		                (
		                    '<option value="%s"%s>%s</option>',
		                    $value,
		                    $value == $current_v ? ' selected="selected"' : '',
		                    $label
		                );
		            }
		    	?>
		    </select>
		    <?php
	    }
	}


	function aliexpress_status_filter_to_shop_order_page($query){

		global $post_type, $pagenow;
		if($pagenow == 'edit.php' && $post_type == 'shop_order'){

		    if(isset($_GET['aliexpress_status_filter']) && $_GET['aliexpress_status_filter'] != ''){

		        $completed = 'Order Placed';
		        $pending = 'Order Pending';
		        $subs_type = $_GET['aliexpress_status_filter'];

		        switch ($subs_type) {
		            case 'Order_Placed':
		                $subs_typeA = 'Order Placed';
		                $date_comp = $completed;
		                break;

		            case 'Order_Pending':
		                $subs_typeA = 'Order Pending';
		                $date_comp = $pending;
		                break;

		            default:
		                break;
		        };

		        $meta_query = $query->get('meta_query');

		        if( empty($meta_query) ) {

		         	$meta_query = array();

		        }

		        $meta_query[] = array(
		            'relation' => 'OR',
		            array(
		                'key' => 'status_of_aliexpress',
		                'value' => $date_comp,
		                'compare' => '=',
		                'type' => 'shop_order'
		            ),
		            array(
		                'key' => 'status_of_aliexpress',
		                'value' => $date_comp,
		                'compare' => '=',
		                'type' => 'shop_order'
		            )
		        );

		        $query->set('meta_query',$meta_query);
		    }
		}
	}

	/***********/
	/*  END    *
	/***********/

	if (!function_exists('get_product_sku_from_woo')) {

		function get_product_sku_from_woo(WP_REST_Request $request) {

			$inputParams = $request->get_json_params();
			$sku 		 = $inputParams['sku'];
		   	/** get product SKU **/

			$product_id = get_sku_in_woo($sku);

			if(!empty($product_id)) { /** if product already exits **/

				$response = array(
					"code" => 'success',
					"message" => 'Product already exits. Please select another product.',
					"data" => array(
						"status" => 200
					),
					"content" => $product_id
				);

			} else {
					// Following should be used whenever we want to return success:

				$response = new WP_Error( 'fail', esc_html__( 'Product does not exist in your Woo store. Please add this product to your Woo store.', 'my-text-domain' ), array( 'status' => 400 ) );
			}

		    return $response;

		}	// function product_category_from_woo()

	}
	//http://testprey.avitinfotech.com/wp-json/woo-aliexpress/v1/product-sku

	// Checked CBE Installed or not

	if (!function_exists('insert_hidden_fields_for_check_cbe')) {

		function insert_hidden_fields_for_check_cbe()
		{
	    	echo '<input type="hidden" id="check_cbe_exist" value="0" />'; ?>
	    	<?php

             $options = get_option( 'cbe_hideoption', true );

            if(isset($options) && $options == 1){  ?>
	    	<script>
	    		setTimeout( function(e){

					var str = jQuery("#check_cbe_exist"). val();
					if(str == 0){
						jQuery( "#screen-meta-links" ).after( '<div id="cbe_message" class="notice notice-success is-dismissible" style="margin-top: 3%;"><p>To use WooCommerce Dropshipping with AliExpress, please install the <a href="https://chrome.google.com/webstore/detail/woocommerce-dropshipping/hfhghglengghapddjhheegmmpahpnkpo"> Chrome Browser Extension</a>&nbsp;&nbsp;&nbsp;&nbsp</p>  <button type="button" class="notice-dismiss hidecbe"><span class="screen-reader-text">Dismiss this notice.</span></button></div> ' );
					}
		   				 // Do something after 1 second
		  			}, 5000 );



					/* function myFunction() {
					  var x = document.getElementById("cbe_message");

					    x.style.display = "none";

					}*/



	    	</script>

	    <?php
	     }

		}
	}


	/*****************************************************/
	/* API for update AliExpress order status            *
	/*****************************************************/

	if (!function_exists('get_order_status_by_id')) {

		//to get full order details

	    function get_order_status_by_id(WP_REST_Request $request) {

	    	global $wpdb;

        $inputParams = $request->get_json_params();

        $orderid = $inputParams['id'];
	    	$status  = $inputParams['status'];

	        if( !empty($inputParams['id']) ) {

	         	//for check order id exist or not.
	        	$results = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE ID = $orderid");

	         	if( empty($results)) {

	         		return new WP_Error( 'fail', esc_html__( 'The Order Id does not exist!', 'my-text-domain' ), array( 'status' => 400 ) );
	         	}

        	} else {

        		return new WP_Error( 'fail', esc_html__( 'The Order Id parameter is required!', 'my-text-domain' ), array( 'status' => 400 ) );
        	}

        	if(empty($inputParams['status']) || ($inputParams['status'] != 'placed') && ($inputParams['status'] != 'pending') ) {

         		return new WP_Error( 'fail', esc_html__( 'The Order Status parameter is required!', 'my-text-domain' ), array( 'status' => 400 ) );
        	}


	    	//$alistatus = get_post_meta($orderid, 'status_of_aliexpress', true);

	    	if($status == 'placed') {

	    		update_post_meta($orderid, 'status_of_aliexpress', 'Order Placed');

				// Following should be used whenever we want to return success:

				$response = array(

					"code" => 'success',

					"message" => 'Order status updated successfully.',

					"data" => array(

						"status" => 200
					)
				);

			} else {

				update_post_meta($orderid, 'status_of_aliexpress', 'Order Pending');

				$response = array(

					"code" => 'success',

					"message" => 'Order status updated successfully.',

					"data" => array(

						"status" => 200

					)
				);
			}

	    return $response;

	    } // function get_order_status_by_id()
	}
	// http://localhost/mywpsite/wp-json/woo-aliexpress/v1/order-status

	/***********/
	/*  END    *
	/***********/

	?>
