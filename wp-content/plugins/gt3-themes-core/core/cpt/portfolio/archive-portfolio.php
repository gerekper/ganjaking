<?php
/**
 * The template for displaying archive pages
 *
 */

get_header();
if ( !post_password_required() ) {

	$layout = gt3_option('page_sidebar_layout');
	$sidebar = gt3_option('page_sidebar_def');
	$column = 12;

	if ( $layout == 'left' || $layout == 'right' ) {
		$column = apply_filters( 'gt3_column_width', 9 );
	}else{
		$sidebar = '';
	}
	$row_class = ' sidebar_'.$layout;


	global $wp_query;
    $query_args = array(
		'post_status' => 'publish',
		'posts_per_page' => 12,
		'post_type' => 'portfolio',
		'orderby' => 'date',
	);

	if (!empty($wp_query->tax_query)) {
		$tax_query_array = (array)$wp_query->tax_query;
		$query_args['tax_query'] = $tax_query_array['queries'];
	}

	wp_enqueue_style('gt3-elementor-core-frontend', plugins_url( '/core/elementor/assets/css/frontend.css', GT3_THEMES_CORE_PLUGIN_FILE ));
    wp_enqueue_script('gt3-elementor-core-frontend-core', plugins_url( '/core/elementor/assets/js/core-frontend.js', GT3_THEMES_CORE_PLUGIN_FILE ) , array(), null, true);

    wp_add_inline_script(
    	'gt3-elementor-core-frontend-core',
    	"document.addEventListener('DOMContentLoaded', function(){
    		var elementor_core_frontend = (typeof window.gt3Elementor.CoreFrontend === 'function') ? new window.gt3Elementor.CoreFrontend : window.gt3Elementor.CoreFrontend;
			elementor_core_frontend.Portfolio(jQuery('.elementor-element-gt3_portfolio_archive'));
	});");

?>


	<div class="container">
		<div class="row<?php echo esc_attr($row_class); ?>">
			<div class="content-container span<?php echo (int)esc_attr($column); ?>">
				<section id='main_content'>
					<?php

				        $port = new \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Portfolio(
				        	array(
				        		'id' => 'gt3_portfolio_archive'
				        	),
				        	array('settings'=>array(
				        		'query' => array(
				        			'query' => $query_args
				        		)
				        	)));
				        $port -> set_settings(apply_filters( 'gt3/core/cpt/portfolio/archive_portfolio/set_settings', array(
				        		'cols' => '3',
				        		'show_category' => 'yes',
				        		'show_description' => false,
				        		'show_view_all' => 'yes',
				        		'load_items' => 6,
				        		'query' => array(
				        			'query' => $query_args
				        		)
				        	), $query_args )
				    	);
					    $port->print_element();

					?>
				</section>
			</div>
			<?php
			if ($layout == 'left' || $layout == 'right') {
				?><div class="sidebar-container span<?php echo (12 - (int)esc_attr($column)); ?>"><?php
				if (is_active_sidebar( $sidebar )) {
					?><aside class='sidebar'><?php
					dynamic_sidebar( $sidebar );
					?></aside><?php
				}
				?></div><?php // end sidebar-container
			}
			?>
		</div>
	</div>

<?php
} else {
	?>
	<div class="pp_block">
        <div class="container_vertical_wrapper">
            <div class="container a-center pp_container">
                <h1><?php echo esc_html__('Password Protected', 'qudos'); ?></h1>
                <?php the_content(); ?>
            </div>
        </div>
	</div>
<?php
}
get_footer();
?>
