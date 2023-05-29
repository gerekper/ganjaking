<?php
/**
 * Create metaboxes for the product post type
 *
 * This file is used to manage the metaboxes used in (product) post type.
 *
 * @link       https://wpali.com
 * @since      1.0.7
 *
 * @package    Wpali_Woocommerce_Order_Builder
 * @subpackage Wpali_Woocommerce_Order_Builder/admin/partials
 */

// Import CMB2-Trunk version 
if ( file_exists( dirname( __FILE__ ) . '/includes/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/includes/CMB2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/includes/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/includes/CMB2/init.php';
}
// Import CMB2 options-page metaboxes registration code and tabs
	if ( file_exists( dirname( __FILE__ ) . '/includes/wpali-woocommerce-order-builder-admin-options-tabs.php' ) ) {
		require_once dirname( __FILE__ ) . '/includes/wpali-woocommerce-order-builder-admin-options-tabs.php';
	}
	if ( file_exists( dirname( __FILE__ ) . '/includes/wpali-woocommerce-order-builder-admin-options.php' ) ) {
		require_once dirname( __FILE__ ) . '/includes/wpali-woocommerce-order-builder-admin-options.php';
	}
// Import metaboxes conditional logic
	if ( file_exists( dirname( __FILE__ ) . '/includes/wpali-woocommerce-order-builder-admin-metaboxes-condition.php' ) ) {
		require_once dirname( __FILE__ ) . '/includes/wpali-woocommerce-order-builder-admin-metaboxes-condition.php';
	}

/**
 * Function to create unique id
 */
function cmb2_get_id_number() {
	
	$random = md5(uniqid(rand(), true));

	return $random;
}
/**
 * Product field custom sanitization
 */
function give_random_id( $value, $field_args, $field ) {
		
		$i = 0;
		$len = count($value);
		$sanitized_value= array();
		$num = 1;
		foreach ($value as $product){
			if ($i !== $len - 1) {
				if (!empty($product['product-photo']) or !empty($product['product-name']) or !empty($product['product-price'])){
					$product['product-id'] = cmb2_get_id_number();	
					
					if (empty($product['product-name'])){
						$product['product-name'] = "item ". $num++;
					}
					if (empty($product['product-price'])){
						$product['product-price'] = "0.00";
					}

				$sanitized_value[] = $product;
				}
			}
		}

	return $sanitized_value;
}

/**
 * Create custom CMB2 field type 'product'
 */
function cmb2_render_product_field_callback( $field, $value, $object_id, $object_type, $field_type_object ) {

  // make sure we specify each part of the value we need.
  $value = wp_parse_args($value, array(
    'product-photo' => '',
    'product-name' => '',
    'product-price' => '',
    'product-id' => '',    

  ));
  ?>

   <div class="wwob-product-image wwob-product-field alignleft">
    <?php echo $field_type_object->file( array(
        'name'  => $field_type_object->_name( '[product-photo]' ),
        'id'    => $field_type_object->_id( '_product_photo' ),
        'value' => $value['product-photo'],
	    'placeholder' => 'Image',
	    'type'  => 'hidden',

		
    ) ); 
	?>
  </div>
  <div class="wwob-product-name wwob-product-field alignleft">
    <?php echo $field_type_object->input( array(
        'name'  => $field_type_object->_name( '[product-name]' ),
        'id'    => $field_type_object->_id( '_product_name' ),
        'value' => $value['product-name'],
	    'placeholder' => 'Name',
    ) ); ?>
  </div>
  <div class="wwob-product-price wwob-product-field alignleft"><div class="wwob-product-price-symbol"><?php echo get_woocommerce_currency_symbol(); ?></div>
  <div class="wwob-product-price-field">
    <?php echo $field_type_object->input( array(
        'name'  => $field_type_object->_name( '[product-price]' ),
        'id'    => $field_type_object->_id( '_product_price' ),
        'value' => $value['product-price'],
        'placeholder' => 'Price',
		'type'  => 'number',
		'step' => '.01',
		
		
    ) ); ?>
  </div>  
  </div>  

    <?php 
		echo $field_type_object->input( array(
        'name'  => $field_type_object->_name( '[product-id]' ),
        'id'    => $field_type_object->_id( '_product_id' ),
        'value' => $value['product-id'],
	    'type'  => 'hidden',

    ) ); 
	?>
	
  <?php
  do_action( 'wwob_add_items_field', $value, $field_type_object );
  
  echo $field_type_object->_desc( true );

}
add_filter( 'cmb2_render_product', 'cmb2_render_product_field_callback', 10, 5 );

/**
 * Optionally save the product values into separate fields
 */
function cmb2_split_product_values($override_value, $value, $object_id, $field_args) {
  if (!isset($field_args['split_values']) || ! $field_args['split_values']) {
    // Don't do the override
    return $override_value;
  }

  $product_keys = array('product-photo', 'product-name', 'product-price', 'product-id' );

  foreach ($product_keys as $key) {
    if (!empty($value[$key])) {
      update_post_meta($object_id, $field_args['id'] . 'product_'. $key, $value[ $key ]);
    }
  }

  // Tell CMB2 we already did the update
  return true;
}
add_filter('cmb2_sanitize_product', 'cmb2_split_product_values', 12, 4);

/**
 * The following snippets are required for allowing the product field
 * to work as a repeatable field, or in a repeatable group
 */

	add_filter( 'cmb2_sanitize_product', 'sanitize', 10, 5 );
	function sanitize( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {
		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'sanitize_text_field', $val ) );
		}

		return array_filter($meta_value);
	}

	add_filter('cmb2_types_esc_product', 'escape', 10, 4);
	function escape( $check, $meta_value, $field_args, $field_object ) {
		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}
		
		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'esc_attr', $val ) );
		}	  

		return array_filter($meta_value);
	}


/**
 * Set default (enable order builder) option value to disabled on first save if not already defined.
 */
function wwob_set_default_to_enable_disable() {
    return 'disabled';
}

/**
 * Register WooCommerce Order Builder on product post-type.
 */
function wwob_products_metabox() {

	$prefix = "wwob_";
	$prefix_field = "wwob_field_";
	$prefix_option = "wwob_option_";
	$prefix_logic = "wwob_logic_";
	
	$cmb_repeat = new_cmb2_box( array(
		'id'           => $prefix . 'metabox',
		'title'        =>  __( 'WPAli: WooCommerce Order Builder', 'wpali-woocommerce-order-builder' ),
		'object_types' => array( 'product' ),
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true,
	) );
	
	$cmb_repeat->add_field( array(
		'name'    => 'Enable Order Builder',
		'id'      => $prefix . 'enable_disable',
		'type'    => 'radio_inline',
		'options' => array(
			'enabled' => __( 'Yes', 'wpali-woocommerce-order-builder' ),
			'disabled'   => __( 'No', 'wpali-woocommerce-order-builder' ),
		),
		'default_cb' => 'wwob_set_default_to_enable_disable',
		'before_row'   => '
			<div class="cmb-tabs">
				<ul class="tabs-nav">
					<li class="current"><a href="#tab-content-1"><i class="dashicons-screenoptions dashicons"></i><span>Products</span></a></li>
					<li><a href="#tab-content-2"><i class="dashicons-plus-alt dashicons"></i><span>Extra Options</span></a></li>
				</ul>
				<div class="tab-content tab-content-1 current">
					<div class="tab-content-container">
		',
	) );


	// Product Group
	
	$group_repeat = $cmb_repeat->add_field( array(
		'id'          => $prefix . 'group',
		'type'        => 'group',
		'options'     => array(
			'group_title'   => __( 'Product', 'wpali-woocommerce-order-builder' ) . ' {#}', // {#} gets replaced by row number
			'add_button'    => __( 'Add Product', 'wpali-woocommerce-order-builder' ),
			'remove_button' => __( 'Remove Product', 'wpali-woocommerce-order-builder' ),
			'sortable'      => true, 
			'closed'     => true,
		),
		'after_group'    => '
					</div><!-- /.tab-content-container -->
				</div><!-- /.tab-content -->
		',

	) );

	$cmb_repeat->add_group_field( $group_repeat, array(
		'name' => 'Label',
		'id'   => $prefix_field.'label',
		'type' => 'text',
		'classes' => 'wwob-parameters', 
		'attributes' => array(
			'placeholder' => 'Title',
		),
	) );
	$cmb_repeat->add_group_field( $group_repeat, array(
		'name' => 'Desc',
		'id'   => $prefix_field.'description',
		'type' => 'text',
		'classes' => 'wwob-parameters', 
		'attributes' => array(
			'placeholder' => 'Optional',
		),
	) );
	$cmb_repeat->add_group_field( $group_repeat, array(
		'name' => 'Max Select',
		'id'   => $prefix_field.'maxSelect',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'placeholder' => 'Optional',
		),
		'classes' => 'wwob-parameters',
	) );
	$cmb_repeat->add_group_field( $group_repeat, array(
		'name' => 'Min Select',
		'id'   => $prefix_field.'minSelect',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'placeholder' => 'Optional',
		),
		'classes' => 'wwob-parameters',
	) );
	$cmb_repeat->add_group_field( $group_repeat, array(
		'name' => 'Quantity',
		'id'   => $prefix_field.'quantity',
		'type' => 'checkbox',
		'classes' => 'wwob-parameters',
	) );
	$cmb_repeat->add_group_field( $group_repeat, array(
		'name' => 'Quota',
		'id'   => $prefix_field.'quota',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'placeholder' => 'Optional',
		),
		'classes' => 'wwob-parameters',
	) );
	
	do_action( 'wwob_add_product_group_field', $cmb_repeat, $group_repeat, $prefix_field );
	
	$cmb_repeat->add_group_field( $group_repeat, array(
		'name' => 'Choices',
		'id'   => $prefix.'product_field',
		'type' => 'product',
		'repeatable' => true,
		'text' => array(
			'add_row_text' =>  __( '+', 'wpali-woocommerce-order-builder' ),
			'add_upload_file_text' => __( 'Upload Image', 'wpali-woocommerce-order-builder' ),
		),
		'classes' => 'wwob-sub-products', 
		'sanitization_cb' => 'give_random_id',

	) );
	
	
	// Extra options start
	
	$cmb_repeat->add_field( array(
		'name'    => 'Extra Options',
		'id'      => $prefix . 'extra_options_start_tab',
		'classes' => 'conditional-hidden hidden',
		'type'    => 'title',
		'before_row'   => '
			<div class="tab-content tab-content-2">
				<div class="tab-content-container">	
		',
	) );
	
	do_action( 'wwob_add_extra_option', $cmb_repeat, $prefix );

	$cmb_repeat->add_field( array(
		'name'    => 'Enable Product Options',
		'id'      => $prefix . 'extra_options',
		'type'    => 'checkbox',
		'options' => array(
			'enabled' => __( 'Enable', 'wpali-woocommerce-order-builder' ),
		),
	) );

	$cmb_repeat->add_field( array(
		'name'       => 'Product Options',
		'id'         => $prefix . 'additional_options',
		'type'       => 'multicheck_inline',
		'classes' => 'conditional-hidden',
		'options' => array(
			'color' => __( 'Color', 'wpali-woocommerce-order-builder' ),
			'size' => __( 'Size', 'wpali-woocommerce-order-builder' ),
			'custom' => __( 'Custom', 'wpali-woocommerce-order-builder' ),
		),
		'attributes' => array(
			'data-conditional-id' => $prefix . 'extra_options',
			'select_all_button' => false,
		),
	) );
	$cmb_repeat->add_field( array(
		'name'       => 'Available Colors',
		'id'         => $prefix . 'available_colors_options',
		'type'       => 'multicheck_inline',
		'classes' => 'conditional-hidden colors child',
		'options' => array(
			'white' => __( 'White', 'wpali-woocommerce-order-builder' ),
			'black' => __( 'Black', 'wpali-woocommerce-order-builder' ),
			'blue' => __( 'Blue', 'wpali-woocommerce-order-builder' ),
			'red' => __( 'Red', 'wpali-woocommerce-order-builder' ),
			'yellow' => __( 'Yellow', 'wpali-woocommerce-order-builder' ),
			'grey' => __( 'Grey', 'wpali-woocommerce-order-builder' ),
			'orange' => __( 'Orange', 'wpali-woocommerce-order-builder' ),
			'green' => __( 'Green', 'wpali-woocommerce-order-builder' ),
			'brown' => __( 'Brown', 'wpali-woocommerce-order-builder' ),
			'pink' => __( 'Pink', 'wpali-woocommerce-order-builder' ),
			'purple' => __( 'Purple', 'wpali-woocommerce-order-builder' ),
		),
		'attributes' => array(
			'data-conditional-id' => $prefix . 'additional_options',
			'data-conditional-value' => 'color',
		),
	) );
	$cmb_repeat->add_field( array(
		'name' => 'Make Color Required',
		'id'   => $prefix.'available_colors_isrequired',
		'classes' => 'conditional-hidden colors child',
		'type' => 'checkbox',
		'after_row'  => '<div class="clearfix"></div>',
		'attributes' => array(
			'data-conditional-id' => $prefix . 'additional_options',
			'data-conditional-value' => 'color',
		),
	) );
	$cmb_repeat->add_field( array(
		'name'       => 'Available Sizes',
		'id'         => $prefix . 'available_sizes_options',
		'type'       => 'multicheck_inline',
		'classes' => 'conditional-hidden size child',
		'options' => array(
			'small' => __( 'Small', 'wpali-woocommerce-order-builder' ),
			'meduim' => __( 'Medium', 'wpali-woocommerce-order-builder' ),
			'large' => __( 'Large', 'wpali-woocommerce-order-builder' ),
			'xl' => __( 'Extra Large', 'wpali-woocommerce-order-builder' ),
			'xxl' => __( 'Extra Extra Large', 'wpali-woocommerce-order-builder' ),
		),
		'attributes' => array(
			'data-conditional-id' => $prefix . 'additional_options',
			'data-conditional-value' => 'size',
		),
	) );
	$cmb_repeat->add_field( array(
		'name' => 'Make Size Required',
		'id'   => $prefix.'available_sizes_isrequired',
		'type' => 'checkbox',
		'classes' => 'conditional-hidden size child',
		'after_row'  => '<div class="clearfix"></div>',
		'attributes' => array(
			'data-conditional-id' => $prefix . 'additional_options',
			'data-conditional-value' => 'size',
		),
	) );
	
	$option_repeat = $cmb_repeat->add_field( array(
		'id'          => $prefix . 'extra_custom_option',
		'type'        => 'group',
		'classes' => 'conditional-hidden-group conditional-hidden child',
		'options'     => array(
			'group_title'   => __( 'Option', 'wpali-woocommerce-order-builder' ) . ' {#}', // {#} gets replaced by row number
			'add_button'    => __( 'Add Option', 'wpali-woocommerce-order-builder' ),
			'remove_button' => __( 'Remove Option', 'wpali-woocommerce-order-builder' ),
			'closed'     => true,
		),

	) );
	$cmb_repeat->add_group_field( $option_repeat, array(
		'name' => 'Label',
		'id'   => $prefix_option.'label',
		'type' => 'text',
		'classes' => 'wwob-options', 
		'attributes' => array(
			'placeholder' => 'Title',
		),
	) );
	$cmb_repeat->add_group_field( $option_repeat, array(
		'name'             => 'Type',
		'id'   => $prefix_option.'type',
		'type'             => 'select',
		'select'          => 'select',
		'show_option_none ' => true,
		'classes' => 'wwob-options ', 
		'options'          => array(
			'select'     => __( 'Select Dropdown', 'cmb2' ),
			'checkbox' => __( 'Checkbox', 'cmb2' ),
			'radio'   => __( 'Radio', 'cmb2' ),
			'text'   => __( 'Text', 'cmb2' ),
		),
	) );
	$cmb_repeat->add_group_field( $option_repeat, array(
		'name' => 'Required',
		'id'   => $prefix_option.'isrequired',
		'type' => 'checkbox',
		'classes' => 'wwob-options', 
		'after_row'  => '<div class="clearfix"></div>',
	) );
	$cmb_repeat->add_group_field( $option_repeat, array(
		'name'             => 'Choices',
		'id'   => $prefix_option.'choices',
		'type'             => 'text',
		'repeatable' => true,
		'text' => array(
			'add_row_text' =>  __( '+', 'wpali-woocommerce-order-builder' ),
		),
	) );
	
	
	$cmb_repeat->add_field( array(
		'name'    => 'Enable Product Instructions',
		'id'      => $prefix . 'product_instructions',
		'type'    => 'checkbox',
		'options' => array(
			'enabled' => __( 'Enable', 'wpali-woocommerce-order-builder' ),
		),
	) );
	$cmb_repeat->add_field( array(
		'name'    => 'Admin Instructions',
		'id'      => $prefix . 'admin_instructions',
		'type'    => 'checkbox',
		'desc'    => 'Give special instructions to your customer.',
		'classes' => 'conditional-hidden',
		'options' => array(
			'enabled' => __( 'Enable', 'wpali-woocommerce-order-builder' ),
		),
		'classes' => 'conditional-hidden',
		'attributes' => array(
			'data-conditional-id' => $prefix . 'product_instructions',
		),
	) );
	$cmb_repeat->add_field( array(
		'name'    => 'Label',
		'id'      => $prefix . 'admin_instructions_label',
		'type'    => 'text',
		'classes' => 'conditional-hidden child',
		'attributes' => array(
			'data-conditional-id' => $prefix . 'admin_instructions',
		),
	) );
	$cmb_repeat->add_field( array(
		'name' => 'Instructions',
		'id'      => $prefix . 'admin_instructions_text',
		'type' => 'textarea_small',
		'classes' => 'conditional-hidden child',
		'attributes' => array(
			'data-conditional-id' => $prefix . 'admin_instructions',
		),
	) );	
	$cmb_repeat->add_field( array(
		'name'    => 'Customer Instructions',
		'id'      => $prefix . 'customer_instructions',
		'desc'    => 'Take special instructions from your customer.',
		'type'    => 'checkbox',
		'classes' => 'conditional-hidden',
		'options' => array(
			'enabled' => __( 'Enable', 'wpali-woocommerce-order-builder' ),
		),
		'classes' => 'conditional-hidden',
		'attributes' => array(
			'data-conditional-id' => $prefix . 'product_instructions',
		),
	) );
	$cmb_repeat->add_field( array(
		'name'    => 'Required',
		'id'      => $prefix . 'customer_instructions_req',
		'type'    => 'checkbox',
		'desc'    => 'Do you want to make this field required?',
		'classes' => 'conditional-hidden child',
		'attributes' => array(
			'data-conditional-id' => $prefix . 'customer_instructions',
		),
	) );
	$cmb_repeat->add_field( array(
		'name'    => 'Label',
		'id'      => $prefix . 'customer_instructions_label',
		'type'    => 'text',
		'classes' => 'conditional-hidden child',
		'attributes' => array(
			'data-conditional-id' => $prefix . 'customer_instructions',
		),
	) );
	$cmb_repeat->add_field( array(
		'name' => 'Description',
		'id'      => $prefix . 'customer_instructions_text',
		'desc'    => 'Describe what the input for to your customer.',
		'type' => 'textarea_small',
		'classes' => 'conditional-hidden child',
		'attributes' => array(
			'data-conditional-id' => $prefix . 'customer_instructions',
		),

	) );	
	$cmb_repeat->add_field( array(
		'name'    => 'Disable items image',
		'id'      => $prefix . 'disable_images',
		'type'    => 'checkbox',
		'options' => array(
			'enabled' => __( 'Enable', 'wpali-woocommerce-order-builder' ),
		),
	) );
	
	$cmb_repeat->add_field( array(
		'name'    => 'Enable Image Preview',
		'id'      => $prefix . 'preview_image',
		'type'    => 'checkbox',
		'options' => array(
			'enabled' => __( 'Enable', 'wpali-woocommerce-order-builder' ),
		),
		'after_row'    => '
					</div><!-- /.tab-content-container -->
				</div><!-- /.tab-content -->
			</div><!-- /.cmb2-tabs -->
		',
	) );
	
	// Third Tab
	// $cmb_repeat->add_field( array(
		// 'name'    => __( 'tab start', 'wpali-woocommerce-order-builder' ),
		// 'id'          => $prefix . 'tab_start',
		// 'type'    => 'title',
		// 'before_row'   => '
			// <div class="tab-content tab-content-3">
				// <div class="tab-content-container">	
		// ',
	// ) );

	// $cmb_repeat->add_field( array(
		// 'name'    => __( 'tab end', 'wpali-woocommerce-order-builder' ),
		// 'id'          => $prefix . 'tab_end',
		// 'type'    => 'title',
		// 'after_row'    => '
					// </div><!-- /.tab-content-container -->
				// </div><!-- /.tab-content -->
			// </div><!-- /.cmb2-tabs -->
		// ',
	// ) );
	
}
add_action( 'cmb2_admin_init', 'wwob_products_metabox' );
