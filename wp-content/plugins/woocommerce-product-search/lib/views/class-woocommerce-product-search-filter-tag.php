<?php
/**
 * class-woocommerce-product-search-filter-tag.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !function_exists( 'woocommerce_product_search_filter_tag' ) ) {
	/**
	 * Renders a product tag filter which is returned as HTML and loads
	 * required resources.
	 *
	 * @param array $atts desired filter options
	 * @return string form HTML
	 */
	function woocommerce_product_search_filter_tag( $atts = array() ) {
		return WooCommerce_Product_Search_Filter_Tag::render( $atts );
	}
}

/**
 * Filter by tag.
 */
class WooCommerce_Product_Search_Filter_Tag {

	const DEFAULT_NUMBER = 10;

	const DEFAULT_THUMBNAIL_SIZING_FACTOR = 1.3;

	private static $instances = 0;

	/**
	 * Adds the shortcode.
	 */
	public static function init() {
		add_shortcode( 'woocommerce_product_filter_tag', array( __CLASS__, 'shortcode' ) );
	}

	/**
	 * Enqueues scripts and styles needed to render our search facility.
	 */
	public static function load_resources() {
		$options = get_option( 'woocommerce-product-search', array() );
		$enable_css = isset( $options[WooCommerce_Product_Search::ENABLE_CSS] ) ? $options[WooCommerce_Product_Search::ENABLE_CSS] : WooCommerce_Product_Search::ENABLE_CSS_DEFAULT;

		wp_enqueue_script( 'product-filter' );
		if ( $enable_css ) {
			wp_enqueue_style( 'product-search' );
		}
	}

	/**
	 * [woocommerce_product_filter_tag] shortcode handler.
	 *
	 * @param array $atts
	 * @param string $content not used
	 *
	 * @return mixed
	 */
	public static function shortcode( $atts = array(), $content = '' ) {
		return self::render( $atts );
	}

	/**
	 * Used to enable our service's get_terms_args filter during tag processing.
	 *
	 * @param boolean $apply
	 * @param array $args
	 * @param array $taxonomies
	 *
	 * @return boolean
	 */
	public static function get_terms_args_apply( $apply, $args, $taxonomies ) {
		return true;
	}

	/**
	 * Renders the tag filter.
	 *
	 * Using a taxonomy other than product_tag is currently NOT fully supported.
	 *
	 * @param array $atts
	 * @param array $results
	 *
	 * @return mixed
	 */
	public static function render( $atts = array(), &$results = null ) {
		self::load_resources();

		add_filter( 'woocommerce_product_search_get_terms_args_apply', array( __CLASS__, 'get_terms_args_apply' ), 10, 3 );

		$atts = shortcode_atts(
			array(
				'container_class'    => '',
				'container_id'       => null,
				'filter'             => 'yes',
				'format'             => 'flat', 
				'heading'            => null,
				'heading_class'      => null,
				'heading_element'    => 'div',
				'heading_id'         => null,
				'heading_no_results' => '',
				'hide_empty'         => 'yes',
				'multiple'           => 'no',
				'number'             => self::DEFAULT_NUMBER,
				'order'              => 'ASC',
				'orderby'            => 'name',
				'show'               => 'all',
				'show_count'         => 'no',
				'show_heading'       => 'yes',
				'show_names'         => 'yes',
				'show_thumbnails'    => 'no',
				'sizing'             => 'none', 
				'style'              => 'inline',
				'taxonomy'           => 'product_tag',
				'terms'              => null,
				'thumbnail_sizing_factor' => self::DEFAULT_THUMBNAIL_SIZING_FACTOR,
				'toggle'             => 'yes',
				'toggle_widget'      => 'yes'
			),
			$atts
		);

		$n               = self::$instances;
		$container_class = '';
		$container_id    = sprintf( 'product-search-filter-tag-%d', $n);
		$heading_class   = 'product-search-filter-terms-heading product-search-filter-tag-heading';
		$heading_id      = sprintf( 'product-search-filter-tag-heading-%d', $n );

		$taxonomy = get_taxonomy( trim( $atts['taxonomy'] ) );

		if ( $taxonomy === false ) {
			$taxonomy = 'product_tag';
		} else {
			if ( $atts['heading'] === null ) {
				if ( !empty( $taxonomy->labels ) && !empty( $taxonomy->labels->singular_name ) ) {
					$atts['heading'] = _x( $taxonomy->labels->singular_name, 'product category singular name', 'woocommerce-product-search' );
				} else {
					$atts['heading'] = _x( $taxonomy->label, 'product category label', 'woocommerce-product-search' );
				}
			}
			$taxonomy = $taxonomy->name;
		}
		$atts['taxonomy'] = $taxonomy;

		$params = array();
		foreach ( $atts as $key => $value ) {
			$is_param = true;
			if ( $value !== null ) {
				if ( is_string( $value ) ) {
					$value = strip_tags( trim( $value ) );
				}
				switch ( $key ) {
					case 'format' :

						$value = strtolower( $value );
						switch ( $value ) {
							case 'flat' :
							case 'list' :
								break;
							default :
								$value = 'flat';
						}
						break;
					case 'filter' :
					case 'hide_empty' :
					case 'multiple' :
					case 'show_count' :
					case 'show_heading' :
					case 'show_names' :
					case 'show_thumbnails' :
					case 'toggle' :
					case 'toggle_widget' :
						$value = strtolower( $value );
						$value = $value == 'true' || $value == 'yes' || $value == '1';
						break;
					case 'orderby' :
						switch ( $value ) {
							case 'count' :
							case 'name' :
								break;
							default :
								$value = 'name';
						}
						break;
					case 'order' :
						$value = strtoupper( trim( $value ) );
						switch ( $value ) {
							case 'ASC' :
							case 'DESC' :
								break;
							default :
								$value = 'ASC';
						}
						break;
					case 'number' :
						$value = intval( $value );
						break;
					case 'taxonomy' :

						break;

					case 'container_class' :
					case 'container_id' :
					case 'heading_class' :
					case 'heading_id' :
						$value = preg_replace( '/[^a-zA-Z0-9 _.#-]/', '', $value );
						$value = trim( $value );
						$containers[$key] = $value;
						$is_param = false;
						break;

					case 'heading_element' :
						if ( !in_array( $value, WooCommerce_Product_Search_Filter::get_allowed_filter_heading_elements() ) ) {
							$value = 'div';
						}
						break;

					case 'heading' :
					case 'heading_no_results' :
						$value = esc_html( $value );
						break;

					case 'show' :
						$value = trim( strtolower( $value ) );
						switch ( $value ) {
							case 'all' :
							case 'set' :
								break;
							default :
								$value = 'all';
						}
						break;

					case 'style' :
						$value = trim( strtolower( $value ) );
						switch( $value ) {
							case 'list' :
							case 'inline' :
								break;
							default :
								$value = 'inline';
						}
						break;

					case 'sizing' :
						$value = trim( strtolower( $value ) );
						switch( $value ) {
							case 'auto' :
							case 'none' :
								break;
							default :
								$value = 'auto';
						}
						break;

					case 'thumbnail_sizing_factor' :
						$value = trim( $value );
						if ( is_numeric( $value ) ) {
							$value = floatval( $value );
						} else {
							$value = self::DEFAULT_THUMBNAIL_SIZING_FACTOR;
						}
						break;
				}
			}
			if ( $is_param ) {
				$params[$key] = $value;
			}
		}

		if ( !empty( $containers['container_class'] ) ) {
			$container_class = $containers['container_class'];
		}
		if ( !empty( $containers['container_id'] ) ) {
			$container_id = $containers['container_id'];
		}
		if ( !empty( $containers['heading_class'] ) ) {
			$heading_class = $containers['heading_class'];
		}
		if ( !empty( $containers['heading_id'] ) ) {
			$heading_id = $containers['heading_id'];
		}

		$list_classes = array();
		switch( $params['style'] ) {
			case 'list' :
				$list_classes[] = 'style-list';
				break;
			case 'inline' :
				$list_classes[] = 'style-inline';
				break;
		}
		if ( $params['show_thumbnails'] ) {
			$list_classes[] = 'show-thumbnails';
		} else {
			$list_classes[] = 'hide-thumbnails';
		}
		if ( $params['show_names'] ) {
			$list_classes[] = 'show-names';
		} else {
			$list_classes[] = 'hide-names';
		}
		$list_class = implode( ' ', $list_classes );

		$params['echo'] = false;

		$parent_term_ids = array();
		if (
			isset( $_REQUEST['ixwpst'] ) &&
			isset( $_REQUEST['ixwpst'][$taxonomy] ) &&
			is_array( $_REQUEST['ixwpst'][$taxonomy] )
		) {
			$include_term_ids = array();
			foreach ( $_REQUEST['ixwpst'][$taxonomy] as $term_id ) {
				if ( $term = get_term( $term_id, $taxonomy ) ) {
					if ( ( $term !== null ) && !( $term instanceof WP_Error) ) {
						$include_term_ids[] = $term->term_id;
						$child_term_ids     = get_terms( array( 'taxonomy' => $term->taxonomy, 'fields' => 'ids', 'child_of' => $term->term_id, 'hierarchical' => false ) );
						$include_term_ids   = array_merge( $include_term_ids, $child_term_ids );
						$include_term_ids   = array_unique( $include_term_ids );

						$i = 0;
						if ( !empty( $term->parent ) ) {
							$parent = get_term( $term->parent, $taxonomy );
							if ( ( $parent !== null ) && !( $parent instanceof WP_Error) ) {
								while ( $parent && $i < 5 ) {
									$parent_term_ids[$term->term_id][] = $parent->term_id;
									if ( !empty( $parent->parent ) ) {
										$parent = get_term( $parent->parent, $taxonomy );
										if ( ( $parent === null ) || ( $parent instanceof WP_Error) ) {
											break;
										}
									} else {
										break;
									}
									$i++;
								}
								$parent_term_ids[$term->term_id] = array_reverse( $parent_term_ids[$term->term_id] );
							}
						}
					}
				}
			}
			if ( count( $include_term_ids ) > 0 ) {
				if ( !$params['multiple'] && $params['show'] === 'set' ) {
					$params['include'] = $include_term_ids;
				} else {
					$params['union'] = $include_term_ids;
				}
			}
		}

		$query_ixwpst = isset( $_GET['ixwpst'] ) ? $_GET['ixwpst'] : null;
		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$current_url = remove_query_arg( array( 'ixwpst' ), $current_url );
		if ( $query_ixwpst !== null ) {
			unset( $query_ixwpst[$taxonomy] );
			if ( count( $query_ixwpst ) > 0 ) {
				$current_url = add_query_arg( array( 'ixwpst' => $query_ixwpst ), $current_url );
			}
		}

		$parent_term_urls      = array();
		$added_parent_term_ids = array();
		foreach ( $parent_term_ids as $tid => $ids ) {
			foreach ( $ids as $id ) {
				if ( !in_array( $id, $added_parent_term_ids ) ) {
					$parent_term_url = add_query_arg( array( 'ixwpst' => array( $taxonomy => array( $id ) ) ), $current_url );
					$parent_term_urls[$id] = $parent_term_url;
				}
			}
		}

		$params['topic_count_text_callback'] = array( __CLASS__, 'topic_count_text_callback' );

		$output_prefix = apply_filters(
			"woocommerce_product_search_filter_{$taxonomy}_prefix",
			sprintf(
				'<div id="%s" class="product-search-filter-terms %s" data-multiple="%s">',
				esc_attr( $container_id ),
				esc_attr( $container_class ),
				$params['multiple'] ? '1' : ''
			)
		);
		$open_inside_output = sprintf(
			'<div class="tagcloud product-tags product-search-filter-items product-search-filter-tag product-search-filter-%s %s%s%s">',
			esc_attr( $taxonomy ),
			esc_attr( $list_class ),
			$params['toggle'] ? ' product-search-filter-toggle' : '',
			$params['toggle_widget'] ? ' product-search-filter-toggle-widget' : ''
		);
		$close_inside_output = '</div>'; 

		$clear_output = '';
		if ( isset( $_GET['ixwpst'] ) && isset( $_GET['ixwpst'][$taxonomy] ) ) {

			$clear_output .= sprintf(
				'<a class="tag-item-all nav-back tag-cloud-link product-search-%s-filter-item" data-term="" href="%s">%s</a>' . "\n",
				esc_attr( $taxonomy ), 
				esc_url( $current_url ),
				__( 'Clear', 'woocommerce-product-search' )
			);
		}

		$parent_terms_output = '';
		if ( count( $parent_term_urls ) > 0 ) {
			$parent_terms_output .= sprintf(
				'<ul class="%s-item-parents">',
				esc_attr( $taxonomy ) 
			);
			foreach ( $parent_term_urls as $parent_term_id => $parent_term_url ) {
				if ( $parent_term = get_term( $parent_term_id, $taxonomy ) ) {
					if ( ( $parent !== null ) && !( $parent instanceof WP_Error) ) {
						$parent_terms_output .= sprintf(
							'<li data-term="%s" class="%s-item-parent nav-back product-search-%s-filter-item"><a href="%s">%s</a></li>',
							esc_attr( $parent_term->term_id ), 
							esc_attr( $taxonomy ), 
							esc_attr( $taxonomy ), 
							esc_url( $parent_term_url ),
							esc_html( $parent_term->name )
						);
					}
				}
			}
			$parent_terms_output .= '</ul>';
		}

		global $woocommerce_product_search_tag_cloud_data_taxonomy;
		$woocommerce_product_search_tag_cloud_data_taxonomy = $taxonomy;
		add_filter( 'wp_generate_tag_cloud_data', array( __CLASS__, 'wp_generate_tag_cloud_data' ) );
		$terms_output = apply_filters(
			"woocommerce_product_search_filter_{$taxonomy}_content",
			self::tag_cloud( apply_filters( "woocommerce_product_search_filter_{$taxonomy}_args", $params ), $results ),
			$atts,
			$params
		);
		remove_filter( 'wp_generate_tag_cloud_data', array( __CLASS__, 'wp_generate_tag_cloud_data' ) );
		unset( $params['list_class'] );
		unset( $woocommerce_product_search_tag_cloud_data_taxonomy );

		$heading_output = '';
		if ( $params['show_heading'] ) {
			$heading_output .= sprintf(
				'<%s class="%s" id="%s">%s</%s>',
				esc_html( $params['heading_element'] ),
				esc_attr( $heading_class ),
				esc_attr( $heading_id ),
				isset( $results['elements_displayed'] ) && ( $results['elements_displayed'] > 0 ) ? esc_html( $params['heading'] ) : esc_html( $params['heading_no_results'] ),
				esc_html( $params['heading_element'] )
			);
		}

		$output = $output_prefix;
		$output .= $heading_output;
		$output .= $open_inside_output;
		$output .= $clear_output;
		$output .= $parent_terms_output;
		$output .= $terms_output;
		$output .= $close_inside_output;

		$output .= apply_filters(
			"woocommerce_product_search_filter_{$taxonomy}_suffix",
			'</div>' 
		);

		$js_object = sprintf( '{taxonomy:"%s"', esc_attr( $taxonomy ) );
		$js_object .= ',multiple:' . ( $params['multiple'] ? 'true' : 'false' );
		$js_object .= ',filter:' . ( $params['filter'] ? 'true' : 'false' );
		$js_object .= sprintf( ',show:"%s"', esc_attr( $params['show'] ) );
		$js_object .= sprintf( ',origin_id:"%s"', esc_attr( $container_id ) );
		$js_object .= '}';
		$output .= '<script type="text/javascript">';
		$output .= 'document.addEventListener( "DOMContentLoaded", function() {';
		$output .= 'if ( typeof jQuery !== "undefined" ) {';
		$output .= 'if ( typeof ixwpsf !== "undefined" && typeof ixwpsf.taxonomy !== "undefined" ) {';
		$output .= 'ixwpsf.taxonomy.push(' . $js_object . ');';
		$output .= '}';
		$output .= '}'; 
		$output .= '} );'; 
		$output .= '</script>';

		WooCommerce_Product_Search_Filter::filter_added();

		self::$instances++;

		remove_filter( 'woocommerce_product_search_get_terms_args_apply', array( __CLASS__, 'get_terms_args_apply' ), 10 );

		return $output;
	}

	/**
	 * Renderer
	 *
	 * @param string $args parameters
	 *
	 * @return string rendered
	 */
	public static function tag_cloud( $args = '', &$results = array() ) {


		$output = '';

		$results['elements_displayed'] = 0;

		$defaults = array(
			'smallest'   => 8,
			'largest'    => 22,
			'unit'       => 'pt',
			'number'     => self::DEFAULT_NUMBER,
			'format'     => 'flat',
			'separator'  => "\n",
			'orderby'    => 'name',
			'order'      => 'ASC',
			'exclude'    => '',
			'include'    => '',
			'link'       => 'view',
			'taxonomy'   => 'post_tag',
			'post_type'  => '',
			'echo'       => true,
			'show_count' => 0,
			'union'      => '',
			'show_names'      => true,
			'show_thumbnails' => false,
			'style'           => 'inline',
			'list_class'      => '',
			'sizing'          => 'auto',
			'thumbnail_sizing_factor' => self::DEFAULT_THUMBNAIL_SIZING_FACTOR,
			'hide_empty' => true
		);

		$args = wp_parse_args( $args, $defaults );

		$number = intval( $args['number'] );
		unset( $args['number'] );

		$tags = get_terms( $args['taxonomy'], array_merge( $args, array( 'orderby' => 'count', 'order' => 'DESC' ) ) );

		$union = array();
		if ( !empty( $args['union'] ) ) {
			$union = $args['union'];
			if ( !is_array( $union ) ) {
				if ( is_string( $union ) ) {
					$union = array_map( 'trim', explode( ',', $union ) );
				} else {
					$union = array();
				}
			}
		}
		if ( count( $union ) > 0 ) {

			$include = array();
			if ( !empty( $args['include'] ) ) {
				$include = $args['include'];
				if ( !is_array( $include ) ) {
					if ( is_string( $include ) ) {
						$include = array_map( 'trim', explode( ',', $include ) );
					} else {
						$include = array();
					}
				}
			}

			$term_ids = array();
			foreach ( $tags as $key => $tag ) {
				$term_ids[] = $tag->term_id;
			}

			$term_ids = array_unique( array_merge( $union, $include, $term_ids ) );
			if ( !empty( $args['number'] ) ) {
				$term_ids = array_slice( $term_ids, 0, intval( $args['number'] ) );
			}
			$union_args = $args;
			$union_args['include'] = $term_ids;
			$tags = get_terms( $args['taxonomy'], array_merge( $union_args, array( 'orderby' => 'count', 'order' => 'DESC' ) ) );
		}

		if ( ! ( empty( $tags ) || is_wp_error( $tags ) ) ) {

			$term_counts = WooCommerce_Product_Search_Service::get_term_counts( $args['taxonomy'] );
			foreach ( $tags as $key => $tag ) {
				if ( 'edit' == $args['link'] ) {
					$link = get_edit_term_link( $tag->term_id, $tag->taxonomy, $args['post_type'] );
				} else {
					$link = get_term_link( intval( $tag->term_id ), $tag->taxonomy );
				}

				if ( !is_wp_error( $link ) ) {
					$tags[ $key ]->link = $link;
					$tags[ $key ]->id = $tag->term_id;

					if ( isset( $term_counts[$tag->term_id] ) ) {
						$tags[ $key ]->count = $term_counts[$tag->term_id];
					}
				}
			}

			$results['elements_displayed'] = count( $tags );
			$args['format'] = $args['style'] === 'inline' ? 'flat' : 'list';

			$args['number'] = $number;
			$output = self::generate_tag_cloud( $tags, $args ); 

			/**
			 * Filters the tag cloud output.
			 *
			 * @since 2.3.0
			 *
			 * @param string $return HTML output of the tag cloud.
			 * @param array  $args   An array of tag cloud arguments.
			 */
			$output = apply_filters( 'wp_tag_cloud', $output, $args );
		}

		if ( !empty( $args['echo'] ) && $args['echo'] ) {

			echo $output; 
		}

		return $output;
	}

	/**
	 * Generate tag cloud
	 *
	 * @param array $tags
	 * @param string $args
	 *
	 * @return string HTML
	 */
	public static function generate_tag_cloud( $tags, $args = '' ) {


		$defaults = array(
			'smallest'        => 8,
			'largest'         => 22,
			'unit'            => 'pt',
			'number'          => 45,
			'format'          => 'flat',
			'separator'       => "\n",
			'orderby'         => 'name',
			'order'           => 'ASC',
			'topic_count_text' => null,
			'topic_count_text_callback' => null,
			'topic_count_scale_callback' => 'default_topic_count_scale',
			'filter'          => 1,
			'show_count'      => 0,
			'show_names'      => true,
			'show_thumbnails' => false,
			'list_class'      => '',
			'sizing'          => 'auto',
			'thumbnail_sizing_factor' => self::DEFAULT_THUMBNAIL_SIZING_FACTOR,
			'hide_empty'      => true
		);

		$args = wp_parse_args( $args, $defaults );

		$show_names      = $args['show_names'];
		$show_thumbnails = $args['show_thumbnails'];

		$return = ( 'array' === $args['format'] ) ? array() : '';

		if ( $args['hide_empty'] === true ) {
			$_tags = array();
			foreach ( $tags as $tag ) {
				if ( $tag->count > 0 ) {
					$_tags[] = $tag;
				}
			}
			$tags = $_tags;
			unset( $_tags );
		}

		if ( empty( $tags ) ) {
			return $return;
		}

		if ( isset( $args['topic_count_text'] ) ) {

			$translate_nooped_plural = $args['topic_count_text'];
		} elseif ( ! empty( $args['topic_count_text_callback'] ) ) {

			if ( $args['topic_count_text_callback'] === 'default_topic_count_text' ) {
				$translate_nooped_plural = _n_noop( '%s item', '%s items' );
			} else {
				$translate_nooped_plural = false;
			}
		} elseif ( isset( $args['single_text'] ) && isset( $args['multiple_text'] ) ) {

			$translate_nooped_plural = _n_noop( $args['single_text'], $args['multiple_text'] );
		} else {

			$translate_nooped_plural = _n_noop( '%s item', '%s items' );
		}

		/**
		 * Filters how the items in a tag cloud are sorted.
		 *
		 * @since 2.8.0
		 *
		 * @param array $tags Ordered array of terms.
		 * @param array $args An array of tag cloud arguments.
		 */
		$tags_sorted = apply_filters( 'tag_cloud_sort', $tags, $args );
		if ( empty( $tags_sorted ) ) {
			return $return;
		}

		if ( $tags_sorted !== $tags ) {
			$tags = $tags_sorted;
			unset( $tags_sorted );
		} else {
			if ( 'RAND' === $args['order'] ) {
				shuffle( $tags );
			} else {

				if ( 'name' === $args['orderby'] ) {
					uasort( $tags, '_wp_object_name_sort_cb' );
				} else {
					uasort( $tags, '_wp_object_count_sort_cb' );
				}

				if ( 'DESC' === $args['order'] ) {
					$tags = array_reverse( $tags, true );
				}
			}
		}

		if ( $args['number'] > 0 )
			$tags = array_slice( $tags, 0, $args['number'] );

			$counts = array();
			$real_counts = array(); 
			foreach ( (array) $tags as $key => $tag ) {
				$real_counts[ $key ] = $tag->count;
				$counts[ $key ] = call_user_func( $args['topic_count_scale_callback'], $tag->count );
			}

			$min_count = min( $counts );
			$spread = max( $counts ) - $min_count;
			if ( $spread <= 0 )
				$spread = 1;
				$font_spread = $args['largest'] - $args['smallest'];
				if ( $font_spread < 0 )
					$font_spread = 1;
					$font_step = $font_spread / $spread;

					$aria_label = false;

					if ( $args['show_count'] || 0 !== $font_spread ) {
						$aria_label = true;
					}

					$tags_data = array();
					foreach ( $tags as $key => $tag ) {
						$tag_id = isset( $tag->id ) ? $tag->id : $key;

						$count = $counts[ $key ];
						$real_count = $real_counts[ $key ];

						if ( $translate_nooped_plural ) {
							$formatted_count = sprintf( translate_nooped_plural( $translate_nooped_plural, $real_count ), number_format_i18n( $real_count ) );
						} else {
							$formatted_count = call_user_func( $args['topic_count_text_callback'], $real_count, $tag, $args );
						}

						$tags_data[] = array(
							'term_object'     => $tag,
							'id'              => $tag_id,
							'url'             => '#' != $tag->link ? $tag->link : '#',
							'role'            => '#' != $tag->link ? '' : ' role="button"',
							'name'            => $tag->name,
							'formatted_count' => $formatted_count,
							'slug'            => $tag->slug,
							'real_count'      => $real_count,
							'class'           => 'tag-cloud-link tag-link-' . $tag_id,
							'font_size'       => $args['smallest'] + ( $count - $min_count ) * $font_step,
							'aria_label'      => $aria_label ? sprintf( ' aria-label="%1$s (%2$s)"', esc_attr( $tag->name ), esc_attr( $formatted_count ) ) : '',
							'show_count'      => $args['show_count'] ? sprintf( ' (%s)', esc_html( $real_count ) ) : '',
						);
						if ( $args['sizing'] !== 'auto' ) {
							unset( $tags_data['font-size'] );
						}
					}

					/**
					 * Filters the data used to generate the tag cloud.
					 *
					 * @since 4.3.0
					 *
					 * @param array $tags_data An array of term data for term used to generate the tag cloud.
					 */
					$tags_data = apply_filters( 'wp_generate_tag_cloud_data', $tags_data );

					$a = array();

					foreach ( $tags_data as $key => $tag_data ) {
						$class = $tag_data['class'] . ' tag-link-position-' . ( $key + 1 );
						$thumbnail_params = array();
						if ( $args['sizing'] === 'auto' ) {
							$thumbnail_params['style'] =
								'width:' . ( floatval( str_replace( ',', '.', ( $tag_data['font_size'] ) ) ) * floatval( $args['thumbnail_sizing_factor'] ) ) .
								$args['unit'];
						}
						$thumbnail = '';
						if ( $show_thumbnails ) {
							$thumbnail = WooCommerce_Product_Search_Thumbnail::term_thumbnail( $tag_data['term_object'], $thumbnail_params );
						}
						$term_name = '';
						if ( $show_names ) {
							$term_name =
								'<span class="term-name">' .
								esc_html( $tag_data['name'] ) . $tag_data['show_count'] .
								'</span>';
						}
						$a[] = sprintf(
							'<a href="%1$s" %2$s class="%3$s" style="%4$s;" %5$s>%6$s</a>',
							esc_url( $tag_data['url'] ),
							$tag_data['role'],
							esc_attr( $class ),
							$args['sizing'] === 'auto' ? esc_attr( 'font-size: ' . str_replace( ',', '.', $tag_data['font_size'] ) . $args['unit'] ) : '',
							$tag_data['aria_label'],
							$thumbnail . $term_name
						);
					}

					switch ( $args['format'] ) {
						case 'array' :
							$return =& $a;
							break;
						case 'list' :
							
							$return = sprintf( "<ul class='wp-tag-cloud %s' role='list'>\n\t<li>", esc_attr( $args['list_class'] ) );
							$return .= join( "</li>\n\t<li>", $a );
							$return .= "</li>\n</ul>\n";
							break;
						default :
							$return = join( $args['separator'], $a );
							break;
					}

					if ( $args['filter'] ) {
						/**
						 * Filters the generated output of a tag cloud.
						 *
						 * The filter is only evaluated if a true value is passed
						 * to the $filter argument in wp_generate_tag_cloud().
						 *
						 * @since 2.3.0
						 *
						 * @see wp_generate_tag_cloud()
						 *
						 * @param array|string $return String containing the generated HTML tag cloud output
						 *                             or an array of tag links if the 'format' argument
						 *                             equals 'array'.
						 * @param array        $tags   An array of terms used in the tag cloud.
						 * @param array        $args   An array of wp_generate_tag_cloud() arguments.
						 */
						return apply_filters( 'wp_generate_tag_cloud', $return, $tags, $args );
					}

					else
						return $return;
	}

	/**
	 * Data generator
	 *
	 * @param array $tags_data holds data for each tag to be rendered in a tag cloud
	 *
	 * @return array
	 */
	public static function wp_generate_tag_cloud_data( $tags_data ) {


		global $woocommerce_product_search_tag_cloud_data_taxonomy;
		$current_tags = array();
		if ( isset( $_GET['ixwpst'] ) && isset( $_GET['ixwpst'][$woocommerce_product_search_tag_cloud_data_taxonomy] ) ) {
			$current_tags = $_GET['ixwpst'][$woocommerce_product_search_tag_cloud_data_taxonomy];
			if ( !is_array( $current_tags ) ) {
				$current_tags = array( $current_tags );
			}
		}
		foreach ( $tags_data as $key => $tag_data ) {
			$tag_data['class'] .= sprintf( ' product-search-%s-filter-item ', esc_attr( $woocommerce_product_search_tag_cloud_data_taxonomy ) );
			if ( in_array( $tag_data['id'], $current_tags ) ) {
				$tag_data['class'] .= ' current-tag ';
			}
			$tag_data['role']  .= sprintf( ' data-term="%s" ', esc_attr( $tag_data['id'] ) );
			$tags_data[$key]    = $tag_data;
		}
		return $tags_data;
	}

	/**
	 * Used as the topic_count_text_callback to wp_tag_cloud() to return the topic count text.
	 *
	 * @param int $count how many products
	 *
	 * @return string
	 */
	public static function topic_count_text_callback( $count ) {
		
		return sprintf( _n( '%s product', '%s products', $count, 'woocommerce-product-search' ), number_format_i18n( $count ) );
	}

}
WooCommerce_Product_Search_Filter_Tag::init();
