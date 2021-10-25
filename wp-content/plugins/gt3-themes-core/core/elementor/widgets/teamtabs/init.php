<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ElementorModal\Widgets\GT3_Core_Elementor_Widget_TeamTabs' ) ) {
	class GT3_Core_Elementor_Widget_TeamTabs extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name() {
			return 'gt3-core-teamtabs';
		}

		public function get_title() {
			return esc_html__( 'Team Tabs', 'gt3_themes_core' );
		}

		public function get_icon() {
			return 'eicon-person';
		}

		protected function construct() {
			$this->add_script_depends('slick');
			$this->add_style_depends('slick');
		}

		public $POST_TYPE = 'team';
		public $TAXONOMY  = 'team_category';

		public $render_index = 1;
		public $image_list = array();

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

		public function render_team_item( $settings = false ) {
			if (!$settings) {
				return;
			}


			$posts_per_line = !empty($query_args['posts_per_page']) ? $query_args['posts_per_page'] : 6;
			$single_member = false; 
			$link_post = !empty($settings['link_post']) ? $settings['link_post'] : false; 


			$compile               = "";
			$appointment_str       = get_post_meta( get_the_ID(), "appointment_member" );
			$positions_str         = get_post_meta( get_the_ID(), "position_member" );
			$url_array             = get_post_meta( get_the_id(), "social_url", true );
			$icon_array            = get_post_meta( get_the_id(), "icon_selection", true );
			$short_desc            = get_post_meta( get_the_id(), "member_short_desc", true );
			$extended_desc         = get_post_meta( get_the_id(), "member_extended_desc", true );
			$taxonomy_objects      = get_object_taxonomies( $this->POST_TYPE, 'objects' );
			$post_excerpt          = ( gt3_smarty_modifier_truncate( get_the_excerpt(), 80 ) );
			$post_cats             = wp_get_post_terms( get_the_id(), $this->TAXONOMY );

			$post_cats_str = '';

			for ( $i = 0; $i < count( $post_cats ); $i ++ ) {
				$post_cat_term = $post_cats[ $i ];
				$post_cat_name = $post_cat_term->slug;
				$post_cats_str .= ' ' . $post_cat_name;
			}


			$icon_str = "";
			if ( isset( $icon_array ) && ! empty( $icon_array ) ) {
				
				for ( $i = 0; $i < count( $icon_array ); $i ++ ) {
					$icon         = $icon_array[ $i ];
					$icon_text    = ! empty( $icon['text'] ) ? esc_html( $icon['text'] ) : '';
					$icon_name    = ! empty( $icon['select'] ) ? esc_attr( $icon['select'] ) : '';
					$icon_address = ! empty( $icon['input'] ) ? esc_url( $icon['input'] ) : '#';
					$icon_color   = ! empty( $icon['color'] ) ? ' style="color: ' . esc_attr( $icon['color'] ) . '" ' : '';
					$icon_str     .= ! empty( $icon['select'] ) || ! empty( $icon['text'] )
						? '<a href="' . $icon_address . '" class="member-icon ' . $icon_name . '" ' . $icon_color . '><span>' . $icon_text . '</span></a>' : '';
				}
				
				if (!empty($icon_str)) {
					$icon_str .= '<div class="team-icons">';
					$icon_str .= '</div>';
				}
			}

			$featured_image = $this::get_feature_image($settings);
			if (isset($this->image_list) && is_array($this->image_list)) {
				$this->image_list[] = '<div class="team_img_thumb"><div class="team_img_thumb_wrapper"><div class="team_img_thumb_container">'.$featured_image.'</div></div></div>';
			}
			

			$compile .= '<div class="item-team-member' . $post_cats_str . ( empty( $featured_image ) ? ' item-team--no_image' : '' ) . '">';
				$compile .= '<div class="item_wrapper">';
					$compile .= '<div class="item">';
						if (! empty( $featured_image )) {
							$compile .= '<div class="team_img featured_img">' . ( $link_post == 'yes' ? '<a href="' . get_permalink( get_the_ID() ) . '">' . $featured_image . '</a>' : $featured_image ) . '</div>';
						}
						$compile .= '<div class="team-content">';
							if (! empty( $icon_str )) {
								$compile .= '<div class="team_icons_wrapper"><div class="member-icons">' . $icon_str . '</div></div>';
							}
							$compile .= '<div class="team-infobox">';
						
								$compile .= '<div class="team_title">';
									$compile .= '<div class="team_title_wrapper">';
										$compile .= '<h3 class="team_title__text">' . ( $link_post == 'yes' ? '<a href="' . get_permalink( get_the_ID() ) . '">' . get_the_title() . '</a>' : get_the_title() ) . '</h3>';
									
										$compile .= '<div class="team-positions">' . $positions_str[0] . '</div>';

						            $compile .= '</div>';
		                        $compile .= '</div>';

		                        if(!empty($extended_desc)){
		                        	$compile .= '<div class="team_info"><div class="member-short-desc">' . $extended_desc . '</div></div>';
		                        }elseif (! empty( $short_desc )) {
		                        	$compile .= '<div class="team_info"><div class="member-short-desc">' . $short_desc . '</div></div>';
		                        }


		                    $compile .= '</div>';
		                $compile .= '</div>';
	                $compile .= '</div>';
                $compile .= '</div>';
        	$compile .= '</div>';

			$this->render_index ++;

			return $compile;
		}

		public function get_feature_image($settings){
			$wp_get_attachment_url = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );
			if ( strlen( $wp_get_attachment_url ) ) {		

				$image_id = get_post_thumbnail_id();

				if ( $settings ) {
					$image_src      = wp_get_attachment_image_src( $image_id, 'full' );
					$title          = get_the_title( $image_id );
					$image          = $this::get_img_url( $image_src, $settings, $title );
					$featured_image = $image;
				} else {
					$featured_image = wp_get_attachment_image( $image_id, 'full' );
				}

			} else {
				$featured_image = '';
			}
			return $featured_image;
		}

		public function get_img_url( $image_src = false, $settings = false, $title = false ) {
			if ( $settings ) {

				$grid_type = 'portrait';
				$cols      = 3;


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
							case 'portrait':
								$ration = 1.2;
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
			<div class="item-team-member text_block">
				<div class="item_wrapper">
					<div class="item">
						<div class="title">' . esc_html( $block['title'] ) . '</div>
						<div class="content">' . $block['content'] . '</div>
					</div>
				</div>
			</div>
			';

			$this->render_index ++;

			return $wrap;
		}
	}
}



