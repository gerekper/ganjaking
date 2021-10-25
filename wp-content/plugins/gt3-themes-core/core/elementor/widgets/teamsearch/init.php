<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ElementorModal\Widgets\GT3_Core_Elementor_Widget_TeamSearch' ) ) {
	class GT3_Core_Elementor_Widget_TeamSearch extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name() {
			return 'gt3-core-team-search';
		}

		public function get_title() {
			return esc_html__( 'Team Search', 'gt3_themes_core' );
		}

		public function get_icon() {
			return 'eicon-search';
		}

		protected function construct() {
			$this->add_script_depends( 'gt3-core/isotope' );
			add_action('wp_ajax_gt3_core_get_team_search_data', array( $this, 'ajax_handler' ));
			add_action('wp_ajax_nopriv_gt3_core_get_team_search_data', array( $this, 'ajax_handler' ));
		}

		public function ajax_handler(){
			header('Content-Type: application/json');
			$search_inputs = $_GET['search_inputs'];

			$args = array(
			    'post_type'=> 'team',
				'posts_per_page' => -1,
				'post_status' => 'publish',
			);

			if (!empty($search_inputs['s']) && ($_GET['name'] != 's')) {
				$args['name'] = $search_inputs['s'];
			}

			if (!empty($search_inputs['specialty']) && ($_GET['name'] != 'specialty')) {
			    $tax_query[] = array(
			        'taxonomy' => 'team_category',
			        'field'    => 'slug',
			        'terms'    => $search_inputs['specialty']
			    );
			}

			if (!empty($search_inputs['location']) && ($_GET['name'] != 'location')) {
			    $tax_query[] = array(
			        'taxonomy' => 'team_location',
			        'field'    => 'slug',
			        'terms'    => $search_inputs['location']
			    );
			}

			if (!empty($tax_query)) {
			    $args['tax_query'] = $tax_query;
			    if (count($args['tax_query']) > 1) {
			    	$args['tax_query']['relation'] = 'AND';
			    }
			}

			$team_query = new \WP_Query( $args );

			if($team_query->have_posts()) {
				$post_ids = wp_list_pluck( $team_query->posts, 'ID' );
				$post_titles = wp_list_pluck( $team_query->posts, 'post_title' );
				$post_slugs = wp_list_pluck( $team_query->posts, 'post_name' );
				wp_reset_postdata();
			}

			if (!empty($post_ids)) {
				$cats = get_terms( 'team_category', array(
					'hide_empty' => true,
					'object_ids' => $post_ids
				));

				$locations = get_terms( 'team_location', array(
					'hide_empty' => true,
					'object_ids' => $post_ids
				));
			}else{
				$cats = '';
				$locations = '';
			}



			$select_options = array();


			foreach ($search_inputs as $name => $value) {

				switch ($name) {
					case apply_filters( "gt3_team_category_search_slug_filter", 's'):
						$select_options[$name] = array();
						foreach ($post_titles as $key => $post_title) {
							$post_slugs = array_values($post_slugs);
							$select_options[$name][] = array(
								"id" => $post_slugs[$key],
								"text" => $post_title,
								"selected" => $post_title === $value ? true : false
							);
						}
						break;

					case apply_filters( "gt3_team_category_search_slug_filter", 'specialty'):
						$select_options[$name] = array();
						foreach ($cats as $cat) {
							$select_options[$name][] = array(
								"id" => $cat->slug,
								"text" => $cat->name,
								"selected" => $cat->slug === $value ? true : false
							);
						}
						break;

					case apply_filters( "gt3_team_location_search_slug_filter", 'location'):
						$select_options[$name] = array();
						foreach ($locations as $location) {
							$select_options[$name][] = array(
								"id" => $location->slug,
								"text" => $location->name,
								"selected" => $location->slug === $value ? true : false
							);
						}
						break;

					default:
						break;
				}

			}

			die(wp_json_encode(array(
				'select_options' => $select_options,
			)));
		}

		public $POST_TYPE = 'team';
		public $TAXONOMY  = 'team_category';

		public $render_index = 1;

		public function get_tax_query_fields() {
			$terms  = get_terms( array(
				'taxonomy'   => $this->POST_TYPE,
				'hide_empty' => true,
			) );
			$return = array();
			if ( is_array( $terms ) && count( $terms ) ) {
				foreach ( $terms as $term ) {
					/* @var \WP_Term $term */
					$return[ $term->term_id ] = $term->name;
				}
			}

			return $return;
		}

		public function get_authors_fields() {
			$users = get_users();

			$return = array();
			foreach ( $users as $user ) {
				$return[ $user->ID ] = $user->display_name;
			}

			return $return;
		}

		function getSlugById( $taxonomy, $ids ) {
			$slugs = array();

			$terms = get_terms( array(
				'taxonomy' => $taxonomy,
				'include'  => $ids,
			) );
			if ( ! is_wp_error( $terms ) ) {
				if ( is_array( $terms ) && count( $terms ) ) {
					foreach ( $terms as $term ) {
						$slugs[] = $term->slug;
					}
				}
			}

			return $slugs;
		}

		function isIds( $ids ) {
			if ( is_array( $ids ) && count( $ids ) ) {
				foreach ( $ids as $id ) {
					if ( ! is_numeric( $id ) ) {
						return false;
					}
				}

				return true;
			}

			return false;
		}

		public function get_taxonomy( $args ) {
			if ( $this->isIds( $args ) ) {
				$args = $this->getSlugById( $this->TAXONOMY, $args );
			}

			$terms  = get_terms( array(
				'taxonomy'   => $this->TAXONOMY,
				'hide_empty' => false,
				'slug'       => $args,
			) );
			$return = array();
			if ( is_array( $terms ) && count( $terms ) ) {
				foreach ( $terms as $term ) {
					/* @var \WP_Term $term */
					$return[ $term->term_id ] = array( 'slug' => $term->slug, 'name' => $term->name );
				}
			}

			return $return;
		}

		public function render_team_item( $posts_per_line, $single_member = false, $grid_gap = '', $link_post = '', $custom_item_height = '', $settings = false ) {
			$compile               = "";
			$appointment_str       = get_post_meta( get_the_ID(), "appointment_member" );
			$positions_str         = get_post_meta( get_the_ID(), "position_member" );
			$url_array             = get_post_meta( get_the_id(), "social_url", true );
			$icon_array            = get_post_meta( get_the_id(), "icon_selection", true );
			$short_desc            = get_post_meta( get_the_id(), "member_short_desc", true );
			$taxonomy_objects      = get_object_taxonomies( $this->POST_TYPE, 'objects' );
			$post_excerpt          = ( gt3_smarty_modifier_truncate( get_the_excerpt(), 80 ) );
			$wp_get_attachment_url = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );
			$post_cats             = wp_get_post_terms( get_the_id(), $this->TAXONOMY );
			$signature             = get_post_meta( get_the_id(), 'mb_signature' );
			if ( is_array( $signature ) && count( $signature ) ) {
				$signature = aq_resize( wp_get_attachment_url( $signature[0] ), "140", "50", false, true, true );
			} else {
				$signature = '';
			}

			$post_cats_str = '';

			for ( $i = 0; $i < count( $post_cats ); $i ++ ) {
				$post_cat_term = $post_cats[ $i ];
				$post_cat_name = $post_cat_term->slug;
				$post_cats_str .= ' ' . $post_cat_name;
			}

			$url_str = "";
			if ( isset( $url_array ) && ! empty( $url_array ) ) {
				for ( $i = 0; $i < count( $url_array ); $i ++ ) {
					$url             = $url_array[ $i ];
					$url_name        = esc_html( $url['name'] );
					$url_address     = $url['address'];
					$url_description = ! empty( $url['description'] ) ? esc_html( $url['description'] ) : '';
					if ( $single_member && ! empty( $url_address ) && ! empty( $url_description ) ) {
						$url_str .= '<div class="team_field">' . ( ! empty( $url_name ) ? '<h5>' . $url_name . ':</h5>' : '' ) . '<a href="' . esc_url( $url_address ) . '" class="team-link">' . $url_description . '</a></div>';
					} else if ( $single_member && ! empty( $url_address ) && empty( $url_description ) ) {
						$url_str .= '<div class="team_field">' . ( ! empty( $url_name ) ? '<h5>' . $url_name . ':</h5>' : '' ) . '<a href="' . esc_url( $url_address ) . '" class="team-link"><i class="fa fa-link"></i></a></div>';
					} else if ( $single_member && empty( $url_address ) && ! empty( $url_description ) ) {
						$url_str .= '<div class="team_field">' . ( ! empty( $url_name ) ? '<h5>' . $url_name . ':</h5>' : '' ) . '<div class="team_info-detail">' . $url_description . '</div></div>';
					}
				}
			}

			$icon_str = "";
			if ( isset( $icon_array ) && ! empty( $icon_array ) && (bool)array_filter($icon_array[0]) ) {
				$icon_str .= '<div class="team-icons">';
				for ( $i = 0; $i < count( $icon_array ); $i ++ ) {
					$icon         = $icon_array[ $i ];
					$icon_text    = ! empty( $icon['text'] ) ? esc_html( $icon['text'] ) : '';
					$icon_name    = ! empty( $icon['select'] ) ? esc_attr( $icon['select'] ) : '';
					$icon_address = ! empty( $icon['input'] ) ? esc_url( $icon['input'] ) : '#';
					$icon_color   = ! empty( $icon['color'] ) ? ' style="color: ' . esc_attr( $icon['color'] ) . '" ' : '';
					$icon_str     .= ! empty( $icon['select'] ) || ! empty( $icon['text'] )
						? '<a href="' . $icon_address . '" class="member-icon ' . $icon_name . '" ' . $icon_color . '><span>' . $icon_text . '</span></a>' : '';
				}
				$icon_str .= '</div>';
			}

			if ( strlen( $wp_get_attachment_url ) ) {

				switch ( $settings['posts_per_line'] ) {
					case "1":
						$gt3_featured_image_size = "1200";
						break;
					case "2":
						$gt3_featured_image_size = "1140";
						break;
					case "3":
						$gt3_featured_image_size = "740";
						break;
					case "4":
						$gt3_featured_image_size = "540";
						break;
					case "5":
						$gt3_featured_image_size = "380";
						break;
					default:
						$gt3_featured_image_size = "1200";
				}

				$img_ratio = 1167 / 1140;
				$img_proportion = wp_kses_post( apply_filters( 'gt3/core/render/team/team_img_prop', $img_ratio ) );

				$gt3_featured_image_url = aq_resize( $wp_get_attachment_url, $gt3_featured_image_size, $gt3_featured_image_size * $img_proportion, true, true, true );
				$featured_image         = '<img  src="' . $gt3_featured_image_url . '" alt="' . get_the_title() . '" />';

				$image_id = get_post_thumbnail_id();

				if ( $settings ) {
					$image_src      = wp_get_attachment_image_src( $image_id, 'full' );
					$title          = get_the_title( $image_id );
					$image          = $this::get_img_url( $image_src, $settings, $title );
					$featured_image = $image;
				} else {
					$image = wp_get_attachment_image( $image_id, 'full' );
				}


				if ( $custom_item_height == 'yes' ) {
					$featured_image = '<span class="team_image_cover" style="background-image: url(' . $gt3_featured_image_url . ')"></span>';
				}
			} else {
				$featured_image = '';
			}

			if ((!empty($settings['type']) && $settings['type'] == 'type3') && (!empty($settings['type']) && $settings['link_post'] ==  'yes')) {
				$view_more = '<div class="team_link"><a href="'.get_permalink( get_the_ID() ).'">'.esc_html__( 'View More', 'gt3_themes_core' ).'</a></div>';
			}else{
				$view_more = '';
			}

			if ( ! $single_member ) {
				$compile .= '<li class="item-team-member' . $post_cats_str . ( empty( $featured_image ) ? ' item-team--no_image' : '' ) . '">';
					$compile .= '<div class="item_wrapper">';
						$compile .= '<div class="item">';
							if (! empty( $featured_image )) {
								$compile .= '<div class="team_img featured_img">' . ( $link_post == 'yes' ? '<a href="' . get_permalink( get_the_ID() ) . '">' . $featured_image . '</a>' : $featured_image ) . '</div>';
							}
							if (! empty( $icon_str ) && $settings['show_social'] == 'yes') {
								$compile .= '<div class="team_icons_wrapper"><div class="member-icons">' . $icon_str . '</div></div>';
							}
							$compile .= '<div class="team-infobox">';
								if ($settings['show_title'] == 'yes' || $settings['show_position'] == 'yes') {
									$compile .= '<div class="team_title">';
										$compile .= '<div class="team_title_wrapper">';
											if ($settings['show_title'] == 'yes') {
												$compile .= '<h3 class="team_title__text">' . ( $link_post == 'yes' ? '<a href="' . get_permalink( get_the_ID() ) . '">' . get_the_title() . '</a>' : get_the_title() ) . '</h3>';
											}

											if (! empty( $positions_str[0]) && $settings['show_position'] == 'yes') {
												$compile .= '<div class="team-positions">' . $positions_str[0] . '</div>';
											}

							            $compile .= '</div>';
			                        $compile .= '</div>';
			                    }

		                        if (! empty( $short_desc ) && $settings['show_description'] == 'yes') {
		                        	$compile .= '<div class="team_info"><div class="member-short-desc">' . $short_desc . '</div></div>';
		                        }

		                        if (! empty( $signature ) && ($settings['show_position'] == 'yes' || $settings['show_description'] == 'yes' || $settings['show_title'] == 'yes') ) {
		                        	$compile .= '<div class="team_signature"><img src="' . $signature . '" alt="' . get_the_title() . '" /></div>';
		                        }

		                        if (!empty($view_more)) {
		                        	$compile .= $view_more;
		                        }

		                    $compile .= '</div>';
		                $compile .= '</div>';
	                $compile .= '</div>';
            	$compile .= '</li>';
			} else {

				$page_title_conditional = ( ( gt3_option( 'page_title_conditional' ) == '1' || gt3_option( 'page_title_conditional' ) == true ) ) ? 'yes' : 'no';

				if ( class_exists( 'RWMB_Loader' ) && get_queried_object_id() !== 0 ) {
					$mb_page_title_conditional = rwmb_meta( 'mb_page_title_conditional' );
					if ( $mb_page_title_conditional == 'yes' ) {
						$page_title_conditional = 'yes';
					} else if ( $mb_page_title_conditional == 'no' ) {
						$page_title_conditional = 'no';
					}
				}

				$compile .= '<div class="row single-member-page">
                <div class="span7">
                    <div class="team_img featured_img">
                        ' . $featured_image . '
                    </div>
                </div>
                <div class="span5">
                    <div class="team-infobox">'
			            . ( $page_title_conditional != 'yes' ? '<div class="team_title"><h3>' . get_the_title() . '</h3></div>' : '' ) .

			            '<div class="team_info">' .
			            ( ! empty( $url_str ) ? $url_str : '' ) .
			            ( ! empty( $icon_str ) ? '<div class="member-icons">' . $icon_str . '</div>' : '' ) .
			            '</div>
                    </div>
                </div>
            </div>';
			}

			$this->render_index ++;

			return $compile;
		}

		public function get_img_url( $image_src = false, $settings = false, $title = false ) {
			if ( $settings ) {

				$grid_type = 'square';
				$cols      = $settings['posts_per_line'];


				if ( ! empty( $image_src[0] ) && strlen( $image_src[0] ) ) {
					$wp_get_attachment_url = $image_src[0];
					if ( ! empty( $grid_type ) && $grid_type != 'vertical' ) {
						switch ( $grid_type ) {
							case 'square':
								$ration = 1;
								break;
							case 'rectangle':
								$ration = 0.8;
								break;
							default:
								$ration = null;
								break;
						}
					} else {
						$ration = null;
					}
					switch ( $cols ) {
						case '1':
							if ( function_exists( 'gt3_get_image_srcset' ) ) {
								$responsive_dimensions = array(
									array( '1200', '1600' ),
									array( '992', '1200' ),
									array( '768', '992' ),
									array( '600', '768' ),
									array( '420', '600' )
								);
								array_unshift( $responsive_dimensions, array( '1600', '1920' ) );
								$gt3_featured_image_url = gt3_get_image_srcset( $wp_get_attachment_url, $ration, $responsive_dimensions );
							} else {
								$gt3_featured_image_url = 'src="' . aq_resize( $wp_get_attachment_url, "1170", null, true, true, true ) . '"';
							}
							break;
						case '2':
							if ( function_exists( 'gt3_get_image_srcset' ) ) {
								$responsive_dimensions = array(
									array( '1200', '800' ),
									array( '992', '500' ),
									array( '768', '496' ),
									array( '600', '384' ),
									array( '420', '600' )
								);
								array_unshift( $responsive_dimensions, array( '1920', '1200' ), array(
									'1600',
									'960'
								) );
								$gt3_featured_image_url = gt3_get_image_srcset( $wp_get_attachment_url, $ration, $responsive_dimensions );
							} else {
								$gt3_featured_image_url = 'src="' . aq_resize( $wp_get_attachment_url, "570", "570", true, true, true ) . '"';
							}
							break;
						case '3':
							if ( function_exists( 'gt3_get_image_srcset' ) ) {
								$responsive_dimensions = array(
									array( '1200', '540' ),
									array( '992', '400' ),
									array( '768', '496' ),
									array( '600', '384' ),
									array( '420', '600' )
								);
								array_unshift( $responsive_dimensions, array( '2000', '1200' ), array(
									'1920',
									'670'
								), array( '1620', '640' ) );
								$gt3_featured_image_url = gt3_get_image_srcset( $wp_get_attachment_url, $ration, $responsive_dimensions );
							} else {
								$gt3_featured_image_url = 'src="' . aq_resize( $wp_get_attachment_url, "400", "400", true, true, true ) . '"';
							}
							break;
						case '4':
							if ( function_exists( 'gt3_get_image_srcset' ) ) {
								$responsive_dimensions = array(
									array( '1200', '400' ),
									array( '992', '300' ),
									array( '768', '496' ),
									array( '600', '384' ),
									array( '420', '600' )
								);
								array_unshift( $responsive_dimensions, array( '2000', '800' ), array(
									'1921',
									'500'
								), array( '1600', '480' ) );

								$gt3_featured_image_url = gt3_get_image_srcset( $wp_get_attachment_url, $ration, $responsive_dimensions );
							} else {
								$gt3_featured_image_url = 'src="' . aq_resize( $wp_get_attachment_url, "300", "300", true, true, true ) . '"';
							}
							break;

						case '5':
							if ( function_exists( 'gt3_get_image_srcset' ) ) {
								$responsive_dimensions = array(
									array( '1200', '300' ),
									array( '992', '300' ),
									array( '768', '496' ),
									array( '600', '384' ),
									array( '420', '600' )
								);
								array_unshift( $responsive_dimensions, array( '2000', '800' ), array(
									'1921',
									'500'
								), array( '1600', '480' ) );

								$ration = 1.565;

								$gt3_featured_image_url = gt3_get_image_srcset( $wp_get_attachment_url, $ration, $responsive_dimensions );
							} else {
								$gt3_featured_image_url = 'src="' . aq_resize( $wp_get_attachment_url, "300", "300", true, true, true ) . '"';
							}
							break;

						default:
							$gt3_featured_image_url = 'src="' . aq_resize( $wp_get_attachment_url, "1170", $ration, true, true, true ) . '"';
					}

					$featured_image = '<img ' . $gt3_featured_image_url . ( ! empty( $title ) ? ' title="' . esc_attr( $title ) . '"' : '' ) . ' alt="" />';

				} else {
					$featured_image = '';
				}

				return $featured_image;
			}

			return false;
		}

		public function renderTextBlock( $block ) {

			$wrap = '
			<li class="item-team-member text_block">
				<div class="item_wrapper">
					<div class="item">
						<div class="title">' . esc_html( $block['title'] ) . '</div>
						<div class="content">' . $block['content'] . '</div>
					</div>
				</div>
			</li>
			';

			$this->render_index ++;

			return $wrap;
		}
	}
}



