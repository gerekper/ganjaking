<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	if($product->get_rating_count() > 0){
		wc_get_template( 'single-product/rating.php' ); 
	}	
?>