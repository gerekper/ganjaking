<?php
/**
 * The template for displaying archive pages
 *
 */
wp_enqueue_style('gt3-elementor-core-frontend', plugins_url( '/core/elementor/assets/css/frontend.css', GT3_THEMES_CORE_PLUGIN_FILE ));
wp_enqueue_style('gt3-elementor');
    wp_enqueue_script('gt3-elementor-core-frontend-core', plugins_url( '/core/elementor/assets/js/core-frontend.js', GT3_THEMES_CORE_PLUGIN_FILE ) , array(), null, true);
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
		'posts_per_page' => -1,
		'post_type' => 'team',
		'orderby' => 'date',
	);

	if (!empty($wp_query->tax_query)) {
		$tax_query_array = (array)$wp_query->tax_query;
		$query_args['tax_query'] = $tax_query_array['queries'];
	}

?>


	<div class="container">
		<div class="row<?php echo esc_attr($row_class); ?>">
			<div class="content-container span<?php echo (int)esc_attr($column); ?>">
				<section id='main_content'>
					<?php

				        $port = new \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Team(
				        	array(
				        		'id' => 'gt3_team_archive'
				        	),
				        	array());
				        $port -> set_settings(array(
				        		'type' => 'type2',
				        		'grid_gap' => '30',
				        		'posts_per_line' => '3',
				        		'link_post' => 'yes',
				        		'custom_item_height' => '',
				        		'use_filter' => '',
				        		'show_title' => 'yes',
				        		'show_position' => 'yes',
				        		'query' => array(
				        			'query' => $query_args
				        		)
				        	)
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
