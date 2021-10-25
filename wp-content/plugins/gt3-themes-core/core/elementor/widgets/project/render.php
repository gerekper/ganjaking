<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Utils;
use Elementor\GT3_Core_Elementor_Control_Query;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Project $widget */

$settings = array(
	'show_type'         => 'grid',
	'grid_type'         => 'square',
	'packery_type'      => 2,
	'cols'              => 4,
	'grid_gap'          => 0,
	'hover'             => 'type1',
	'show_title'        => true,
	'show_category'     => false,
	'show_description'  => false,
	'use_filter'        => false,
	'filter_align'      => 'center',
	'all_title'         => esc_html__( 'All', 'gt3_themes_core' ),
	'show_view_all'     => false,
	'load_items'        => 4,
	'button_type'       => 'default',
	'button_border'     => true,
	'button_title'      => esc_html__( 'Load More', 'gt3_themes_core' ),
	'from_elementor'    => true,
	'static_info_block' => '',
	'title'             => esc_html__( 'Title', 'gt3_themes_core' ),
	'sub_title'         => esc_html__( 'Subtitle', 'gt3_themes_core' ),
	'content'           => esc_html__( 'Content', 'gt3_themes_core' ),
	'btn_block'         => '',
	'btn_title'         => esc_html__( 'Button Title', 'gt3_themes_core' ),
	'btn_link'          => array( 'url' => '#', 'is_external' => false, 'nofollow' => false, ),
	'enable_icon'       => 'yes',
	'pagination_en'     => false,
);

$settings = wp_parse_args( $widget->get_settings(), $settings );

if ( (bool) $settings['pagination_en'] ) {
	$settings['show_view_all'] = false;
}

if ( ! is_numeric( $settings['load_items'] ) || empty( $settings['load_items'] ) || $settings['load_items'] < 1 ) {
	$settings['load_items'] = 4;
}
if ( ! is_numeric( $settings['cols'] ) || empty( $settings['cols'] ) || $settings['cols'] < 1 || $settings['cols'] > 4 ) {
	$settings['cols'] = 4;
}
if ( !isset($settings['cols_tablet']) ) {
	$settings['cols_tablet'] = 2;
}
if ( !isset($settings['cols_mobile']) ) {
	$settings['cols_mobile'] = 1;
}
global $paged;
if ( empty( $paged ) ) {
	$paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
}

$query_args = $settings['query']['query'];
unset( $settings['query']['query'] );
$query_args['paged'] = $paged;


$project_query_arg = 'project_cat';

if ( function_exists( 'gt3_option' ) ) {
	$slug_option       = gt3_option( 'project_slug' );
	$project_query_arg = sanitize_title( $slug_option ) . '_cat';
}


$query_raw = $settings['query'];
if ( isset( $_REQUEST[ $project_query_arg ] ) && ! empty( $_REQUEST[ $project_query_arg ] ) ) {
	if ( isset( $query_args['tax_query'] ) ) {
		foreach ( $query_args['tax_query'] as $key => $value ) {
			if ( ! is_numeric( $key ) ) {
				continue;
			}
			if ( is_array( $value ) && isset( $value['field'] ) && $value['field'] == 'slug' ) {
				$query_args['tax_query'][ $key ]['terms'] = $_REQUEST[ $project_query_arg ];
			}
		}
	}
} else {
	$_REQUEST[ $project_query_arg ] = '';
}
$query = new WP_Query( $query_args );
if ( ! $query->post_count ) {
	$paged               = 1;
	$query_args['paged'] = 1;
	$query               = new WP_Query( $query_args );
}
$exclude = array();
foreach ( $query->posts as $_post ) {
	$exclude[] = $_post->ID;
}

if ( $query->have_posts() ) {
	if ( ! $settings['from_elementor'] ) {
		echo '<style>
			.project_wrapper .isotope_wrapper {
				 margin-right:-' . $settings['grid_gap'] . ';
				 margin-bottom:-' . $settings['grid_gap'] . ';
			}

			.project_wrapper .isotope_item {
				padding-right: ' . $settings['grid_gap'] . ';
				padding-bottom: ' . $settings['grid_gap'] . ';
			}
		 </style>';
	}

	$query_args['exclude']        = $exclude;
	$query_args['posts_per_page'] = $settings['load_items'];
	$dataSettings                 = array(
		'pagination_en'    => (bool) ( $settings['pagination_en'] ),
		'show_title'       => (bool) ( $settings['show_title'] ),
		'show_category'    => (bool) ( $settings['show_category'] ),
		'show_description' => (bool) ( $settings['show_description'] ),
		'use_filter'       => ( (bool) ( $settings['use_filter'] ) && count( $query_raw['taxonomy'] ) > 1 ),
		'load_items'       => $settings['load_items'],
		'gap_value'        => intval( $settings['grid_gap'] ),
		'gap_unit'         => substr( $settings['grid_gap'], - 1 ) == '%' ? '%' : 'px',
		'query'            => $query_args,
		'type'             => $settings['show_type'],
		'random'           => ( isset( $query_args['orderby'] ) && $query_args['orderby'] == 'rand' ),
		'render_index'     => $query->query['posts_per_page'],
		'settings'         => array(
			'grid_type'    => $settings['grid_type'],
			'cols'         => $settings['cols'],
			'cols_tablet'  => $settings['cols_tablet'],
			'cols_mobile'  => $settings['cols_mobile'],
			'show_type'    => $settings['show_type'],
			'packery_type' => $settings['packery_type']
		)
	);

	$class_wrapper = array(
		'project_wrapper',
		'show_type_' . $settings['show_type'],
		'hover_' . $settings['hover'],
		'packery_type_' . $settings['packery_type'],
		$settings['from_elementor'] ? 'elementor' : 'not_elementor',
	);

	switch ( $settings['show_type'] ) {
		case 'packery':
			if ( ! key_exists( $settings['packery_type'], $widget->packery_grids ) ) {
				$settings['packery_type'] = 2;
			}
			$dataSettings['packery'] = $widget->packery_grids[ $settings['packery_type'] ];
			break;
		case 'masonry':
			$dataSettings['cols']        = $settings['cols'];
			$dataSettings['cols_tablet'] = $settings['cols_tablet'];
			$dataSettings['cols_mobile'] = $settings['cols_mobile'];
			$class_wrapper[]             = 'items' . $settings['cols'];
			$class_wrapper[]             = 'items_tablet' . $settings['cols_tablet'];
			$class_wrapper[]             = 'items_mobile' . $settings['cols_mobile'];
			break;
		case 'grid':
			$class_wrapper[]             = 'items' . $settings['cols'];
			$class_wrapper[]             = 'items_tablet' . $settings['cols_tablet'];
			$class_wrapper[]             = 'items_mobile' . $settings['cols_mobile'];
			$class_wrapper[]             = 'grid_type_' . $settings['grid_type'];
			$dataSettings['cols']        = $settings['cols'];
			$dataSettings['cols_tablet'] = $settings['cols_tablet'];
			$dataSettings['cols_mobile'] = $settings['cols_mobile'];
			$dataSettings['grid_type']   = $settings['grid_type'];
			break;
	}

	$widget->add_render_attribute( 'wrapper', 'class', $class_wrapper );

	if ( empty( $settings['btn_link']['url'] ) ) {
		$settings['btn_link']['url'] = '#';
	}
	$widget->add_render_attribute( 'btn_link', 'class', 'static_info_link' );
	$widget->add_render_attribute( 'btn_link', 'href', esc_url( $settings['btn_link']['url'] ) );

	if ( $settings['btn_link']['is_external'] ) {
		$widget->add_render_attribute( 'btn_link', 'target', '_blank' );
	}

	if ( ! empty( $settings['btn_link']['nofollow'] ) ) {
		$widget->add_render_attribute( 'btn_link', 'rel', 'nofollow' );
	}

	?>
    <div <?php $widget->print_render_attribute_string( 'wrapper' ) ?>>
		<?php if ( (bool) ( $settings['use_filter'] ) && count( $query_raw['taxonomy'] ) > 1 ) {
			?>
            <div class="isotope-filter">
				<?php
				if ( $settings['pagination_en'] ) {
					echo '<a href="' . get_permalink() . '" data-filter="*" ' . ( $_REQUEST[ $project_query_arg ] == '' ? ' class="active"' : '' ) . '>' . esc_html( $settings['all_title'] ) . '</a>';
				} else {
					echo '<a href="#" class="active" data-filter="*">' . esc_html( $settings['all_title'] ) . '</a>';
				}
				foreach ( $widget->get_taxonomy( $query_raw['taxonomy'] ) as $cat_slug ) {
					if ( $settings['pagination_en'] ) {
						$url = add_query_arg( array(
							$project_query_arg => $cat_slug['slug'],
						) );
						echo '<a href="' . $url . '" data-filter=".' . esc_attr( $cat_slug['slug'] ) . '" ' . ( $_REQUEST[ $project_query_arg ] == $cat_slug['slug'] ? ' class="active"' : '' ) . '>' . esc_html( $cat_slug['name'] ) . '</a>';
					} else {
						echo '<a href="#" data-filter=".' . esc_attr( $cat_slug['slug'] ) . '">' . esc_html( $cat_slug['name'] ) . '</a>';
					}
				}
				?>
            </div>
		<?php } ?>
        <div class="isotope_wrapper items_list gt3_clear">
			<?php

			$render_index = 1;

			while ( $query->have_posts() ) {

				if ( (bool) $settings['static_info_block'] && $render_index === 1 ) {
					$render_index ++;
					echo '<div class="static_info_text_block isotope_item loading blog_post_preview element">
						    <div class="item_wrapper">
								<div class="item">
									<div class="title">' . esc_html( $settings['title'] ) . '</div>
									<div class="sub_title">' . esc_html( $settings['sub_title'] ) . '</div>
									<div class="content">' . $settings['content'] . '</div>';
					if ( (bool) $settings['btn_block'] ) {
						if ( (bool) $settings['enable_icon'] ) {
							$btn_icon = '<span class="static_info_icon"><i class="fa fa-angle-right"></i></span>';
						} else {
							$btn_icon = '';
						}
						echo '<a ' . $widget->get_render_attribute_string( 'btn_link' ) . '>' . esc_html( $settings['btn_title'] ) . $btn_icon . '</a>';
					}
					echo '</div>
							</div>
						  </div>';
				}

				$query->the_post();
				echo '' . $widget->renderItem( ( (bool) ( $settings['use_filter'] ) && count( $query_raw['taxonomy'] ) > 1 ), $settings['show_title'], $settings['show_category'], $settings['show_description'], $render_index, $settings );

				$render_index ++;
			}
			?>
        </div>
		<?php
		if ( (bool) ( $settings['show_view_all'] ) && $query->max_num_pages > 1 ) {
			if ( empty( $settings['view_all_link_text'] ) ) {
				$settings['view_all_link_text'] = esc_html__( 'More', 'gt3_themes_core' );
			}
			if ( (bool) $settings['button_border'] ) {
				$widget->add_render_attribute( 'view_more_button', 'class', 'bordered' );
			}
			$widget->add_render_attribute( 'view_more_button', 'href', 'javascript:void(0)' );
			$widget->add_render_attribute( 'view_more_button', 'class', 'project_view_more_link' );
			$widget->add_render_attribute( 'view_more_button', 'class', 'button_size_elementor_normal alignment_center border_icon_none hover_none btn_icon_position_right' );

			$widget->add_render_attribute( 'view_more_button', 'class', 'button_type_' . esc_attr( $settings['button_type'] ) );
			if ( $settings['button_type'] == 'icon' ) {
				$widget->add_render_attribute( 'button_icon', 'class', esc_attr( $settings['button_icon'] ) );
			}

			$widget->add_render_attribute( 'button_icon', 'class', 'elementor_gt3_btn_icon' );
			if ( ! empty( $settings['button_title'] ) ) {
				$widget->add_render_attribute( 'view_more_button', 'title', esc_attr( $settings['button_title'] ) );
			}
			echo '
			<div class="elementor-element  elementor-widget elementor-widget-gt3-core-button gt3_project_view_more_link_wrapper">
				<div class="elementor-widget-container">
					<div class="gt3_module_button_elementor size_normal alignment_center button_icon_' . $settings['button_type'] . ' hover_none">
						<a ' . $widget->get_render_attribute_string( 'view_more_button' ) . '>
							<span class="gt3_module_button__container">
								<span class="gt3_module_button__cover front">
									<span class="elementor_gt3_btn_text">' . esc_html( $settings['button_title'] ) . '</span>
									' . ( $settings['button_type'] == 'icon' ? '<span class="elementor_btn_icon_container"><span ' . $widget->get_render_attribute_string( 'button_icon' ) . '></span></span>' : '' ) . '
								</span>
							</span>
						</a>
					</div>
				</div>
			</div>';
		} // End button
		if ( (bool) $settings['pagination_en'] ) {
			echo gt3_get_theme_pagination( 5, "", $query->max_num_pages, $paged );
		}
		?>
    </div>
	<?php
	$widget->print_data_settings($dataSettings);
}

wp_reset_postdata();

