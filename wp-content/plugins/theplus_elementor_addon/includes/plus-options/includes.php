<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	require_once THEPLUS_INCLUDES_URL.'plus-options/post-type.php';	
	
	$megamenu=theplus_get_option('general','check_elements');
	$check_category= get_option( 'theplus_api_connection_data' );
	if(isset($megamenu) && !empty($megamenu) && in_array("tp_dynamic_categories", $megamenu) && !empty($check_category['dynamic_category_thumb_check'])){
		require_once THEPLUS_INCLUDES_URL.'plus-options/custom-metabox/taxonomy_options.php';		
	}
	
	require_once THEPLUS_INCLUDES_URL.'plus-options/custom-metabox/custom_field_repeater_option.php';
	
	//product video url
	if(isset($megamenu) && !empty($megamenu) && in_array("tp_woo_single_image", $megamenu) && !empty($check_category['theplus_custom_field_video_switch'])){		
		require_once THEPLUS_INCLUDES_URL.'plus-options/custom-metabox/custom_field_video_in_product.php';
	}

	/*woo swatches*/
	if(isset($megamenu) && !empty($megamenu) && (in_array("tp_woo_single_basic", $megamenu) || in_array("tp_search_filter", $megamenu)) && !empty($check_category['theplus_woo_swatches_switch'])){
		require_once THEPLUS_INCLUDES_URL.'plus-options/custom-metabox/tp_custom_product_swatches.php';
		require_once THEPLUS_INCLUDES_URL.'plus-options/custom-metabox/tp_custom_product_swatches_meta.php';
		require_once THEPLUS_INCLUDES_URL.'plus-options/custom-metabox/tp_custom_product_swatches_front.php';
		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			add_action( 'init', array( 'TP_Woo_Variation_Swatches_Front', 'instance' ) );
		}
	}

?>