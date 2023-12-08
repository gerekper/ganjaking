<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	/**category filter*/
	$category_filter='';
	if($filter_category=='yes'){				
		 $terms = get_the_terms( $loop->ID,$texonomy_category);
		if ( $terms != null ){
			foreach( $terms as $term ) {
				$category_filter .=' '.esc_attr($term->slug).' ';
				unset($term);
			}
		}
	}

	/**grid item loop*/
	echo '<div class="grid-item flex-column flex-wrap '.$desktop_class.' '.$tablet_class.' '.$mobile_class.' '.$category_filter.' '.$animated_columns.'">';				
	if(!empty($style)){
		include THEPLUS_PATH. 'includes/client/client-' . sanitize_file_name($style) . '.php'; 
	}
	echo '</div>';
?>