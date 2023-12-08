<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	if(!empty($template_id)){
?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="product-list-content">
		<?php		
		
				global $tp_render_loop, $wp_query,$tp_index;
				$tp_index++;
				
				$tp_old_query=$wp_query;				
				$new_query=new \WP_Query( array( 'p' => get_the_ID() ) );	
				
				$wp_query = $new_query;	
				
				$pid=get_the_ID();
				
				$template_id = apply_filters( 'Product_Listing_template', $template_id,$pid,$tp_index );
				$template_id = get_current_ID($template_id);
				
				$tp_render_loop=get_the_ID().",".$template_id;
				
				if (!$template_id) return;
				
				$return = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id );
				
				$tp_render_loop=false;
				$wp_query = $tp_old_query;		
				echo $return;

				include THEPLUS_INCLUDES_URL. 'dynamic-listing/blog-skeleton.php';
			?>
		</div> 
		
	</article>
<?php
	}else{
		echo esc_html__('Select a Template','theplus');
	}
?>