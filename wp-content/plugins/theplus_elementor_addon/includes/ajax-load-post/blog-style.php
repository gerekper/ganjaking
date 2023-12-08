<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	if($layout=='metro'){		
		$ij=theplus_load_metro_style_layout($ji,$metro_column,$metro_style);
		if(!empty($responsive_tablet_metro) && $responsive_tablet_metro=='yes'){
			$tablet_ij=theplus_load_metro_style_layout($ji,$tablet_metro_column,$tablet_metro_style);
			$tablet_metro_class ='tb-metro-item'.esc_attr($tablet_ij);
		}
	}
	
	//category filter
	$category_filter='';
	if($filter_category=='yes'){				
		 $terms = get_the_terms( $loop->ID,'category');
		if ( $terms != null ){
			foreach( $terms as $term ) {
				$category_filter .=' '.esc_attr($term->slug).' ';
				unset($term);
			}
		}
	}
	
	//grid item loop
	echo '<div class="grid-item metro-item'.esc_attr($ij).' '.esc_attr($tablet_metro_class).' '.$desktop_class.' '.$tablet_class.' '.$mobile_class.' '.$category_filter.' '.$animated_columns.'">';				
	if(!empty($style)){
		include THEPLUS_PATH. 'includes/blog/blog-'.sanitize_file_name($style).'.php'; 
	}
	echo '</div>';
?>