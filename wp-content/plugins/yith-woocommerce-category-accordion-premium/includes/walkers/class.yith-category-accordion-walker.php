<?php // phpcs:ignore WordPress.NamingConventions
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\CategoryAccordion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Category_Accordion_Walker' ) ) {
	/**
	 * YITH_Category_Accordion_Walker
	 */
	class YITH_Category_Accordion_Walker extends Walker_Category {

		/**
		 * Is the array ids to exclude
		 *
		 * @var array
		 */
		protected $exclude_ids;

		/**
		 * Construct
		 *
		 * @param array $exclude_ids The exclude ids.
		 */
		public function __construct( $exclude_ids = array() ) {
			$this->exclude_ids = array_map( 'intval', $exclude_ids );
		}

		/**
		 * Start the element output.
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $category Category data object.
		 * @param int    $depth Depth of category in reference to parents. Default 0.
		 * @param array  $args An array of arguments. @see wp_list_categories().
		 * @param int    $id ID of the current category.
		 *
		 * @since 2.1.0
		 *
		 * @see Walker::start_el()
		 *
		 */
		public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
			/** This filter is documented in wp-includes/category-template.php */

			$cat_name = apply_filters(
				'list_cats',
				esc_attr( $category->name ),
				$category
			);

			// Don't generate an element if the category name is empty.
			if ( ! $cat_name ) {
				return;
			}

			$link = '<a href="' . esc_url( get_term_link( $category ) ) . '" ';

			$link_class = ! empty( $args['style_count'] ) ? $args['style_count'] : '';

			$link .= 'class="' . $link_class . '"';
			if ( $args['use_desc_for_title'] && ! empty( $category->description ) ) {
				/**
				 * Filter the category description for display.
				 *
				 * @param string $description Category description.
				 * @param object $category Category object.
				 *
				 * @since 1.2.0
				 *
				 */
				$link .= ' title="' . esc_attr( strip_tags( apply_filters( 'yith_category_accordion_description', $category->description, $category ) ) ) . '"'; //phpcs:ignore
			}

			$link .= '>';

			$cat_name = apply_filters( 'yith_category_accordion_name', $cat_name, $category );
			$link     .= $cat_name . '</a>';

			if ( ! empty( $args['feed_image'] ) || ! empty( $args['feed'] ) ) {
				$link .= ' ';

				if ( empty( $args['feed_image'] ) ) {
					$link .= '(';
				}

				$link .= '<a href="' . esc_url( get_term_feed_link( $category->term_id, $category->taxonomy, $args['feed_type'] ) ) . '"';

				if ( empty( $args['feed'] ) ) {
					/* translators: % is the category accordion name */
					$alt = ' alt="' . sprintf( __( 'Feed for all posts filed under %s' ), $cat_name ) . '"';
				} else {
					$alt  = ' alt="' . $args['feed'] . '"';
					$name = $args['feed'];
					$link .= empty( $args['title'] ) ? '' : $args['title'];
				}

				$link .= '>';

				if ( empty( $args['feed_image'] ) ) {
					$link .= $name;
				} else {
					$link .= "<img src='" . $args['feed_image'] . "'$alt" . ' />';
				}
				$link .= '</a>';

				if ( empty( $args['feed_image'] ) ) {
					$link .= ')';
				}
			}

			if ( ! empty( $args['show_count'] ) ) {

				switch ( $args['style_count'] ) {

					case 'square_style':
						$link .= '<span class="rectangle_count">' . number_format_i18n( $category->count ) . '</span>';
						break;

					case 'circle_style':
						$link .= '<span class="round_count">' . number_format_i18n( $category->count ) . '</span>';
						break;

					default:
						$link .= '<span class="default_count"><span class="default_count_bracket">&#40;</span>' . number_format_i18n( $category->count ) . '<span class="default_count_bracket">&#41;</span></span>';
				}
			}

			if ( 'list' === $args['style'] ) {
				$output      .= "\t<li";
				$css_classes = array(
					'cat-item',
					'cat-item-' . $category->term_id,
				);

				if ( ! empty( $args['current_category'] ) ) {
					$_current_category = get_term( $args['current_category'], $category->taxonomy );
					if ( $category->term_id === $args['current_category'] ) {
						$css_classes[] = 'current-cat';
					} elseif ( $_current_category instanceof WP_Term && $category->term_id === $_current_category->parent ) {
						$css_classes[] = 'current-cat-parent';
					}
				}

				$category_image = '';

				if( 'yes' === $args['wc_image'] ) {
					$thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
					// get the image URL
					$image = wp_get_attachment_url($thumbnail_id);
					if ($image) {
						// get the IMG HTML
						$category_image = "<img src='{$image}' alt='' width='40' />";
					}
					array_push($css_classes,"cat-item-image");
				}

				/**
				 * Filter the list of CSS classes to include with each category in the list.
				 *
				 * @param array  $css_classes An array of CSS classes to be applied to each list item.
				 * @param object $category Category data object.
				 * @param int    $depth Depth of page, used for padding.
				 * @param array  $args An array of wp_list_categories() arguments.
				 *
				 * @since 4.2.0
				 *
				 * @see wp_list_categories()
				 *
				 */
				$css_classes = implode( ' ', apply_filters( 'category_css_class', $css_classes, $category, $depth, $args ) );

                $output .= ' class="' . $css_classes . '" data-cat_level="' . $depth . '"';
                $output .= "> $category_image $link\n";
            } else {
                $output .= "\t$link<br />\n";
            }

		}

		/**
		 * End_el
		 *
		 * @param mixed $output output.
		 * @param mixed $page page.
		 * @param int   $depth depth.
		 * @param array $args args.
		 *
		 * @return void
		 */
		public function end_el( &$output, $page, $depth = 0, $args = array() ) {

			if ( isset( $args['show_last_post'] ) && $args['show_last_post'] ) {
				global $wpdb;

				$limit = '';
				if ( - 1 !== $args['post_limit'] ) {
					$limit = 'LIMIT ' . $args['post_limit'];
				}

				$posts = $wpdb->get_results( //phpcs:ignore WordPress.DB.DirectDatabaseQuery
					"SELECT object_id as ID FROM {$wpdb->term_relationships} r "
					. "JOIN {$wpdb->posts} p on r.object_id = p.ID WHERE p.post_status = 'publish' and r.term_taxonomy_id = "
					. "(SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE taxonomy = '" . $args['taxonomy'] . "' and term_id = " . $page->term_id . ') ' //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					. 'ORDER BY p.post_date DESC ' . $limit //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				);

				global $post;

				if ( $posts ) {

					$sub_cont     = '<ul class="children">';
					$class_parent = '';
					foreach ( $posts as $this_post ) {

						$class = ! is_null( $post ) && $post->ID === $this_post->ID ? 'current-cat' : '';

						if ( ! is_null( $post ) && $post->ID === $this_post->ID ) {
							$class_parent = 'current-cat-parent';
						}

						$sub_cont .= ' <li class="' . $class . '" data-cat_level="2">';
						$sub_cont .= '<a title="' . get_the_title( $this_post->ID ) . '" href="' . get_permalink( $this_post->ID ) . '">' . get_the_title( $this_post->ID ) . '</a>';
						$sub_cont .= '</li>';
					}
					$sub_cont .= '</ul>';

					$output .= '<ul class="children">';
					$output .= '<li class="cat-item ' . $class_parent . '" data-cat_level="1"><a href="#">' . __( 'Last Posts', 'yith-woocommerce-category-accordion' ) . '</a>';
					$output .= $sub_cont;
					$output .= '</li></ul>';

				}
			}
			parent::end_el( $output, $page, $depth, $args );
		}

		/**
		 * Display_element
		 *
		 * @param mixed $element element.
		 * @param mixed $children_elements children_elements.
		 * @param mixed $max_depth max_depth.
		 * @param mixed $depth depth.
		 * @param mixed $args args.
		 * @param mixed $output output.
		 *
		 * @return void
		 */
		public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
			if ( ! $element || ( 0 === $element->count && ! empty( $args[0]['hide_empty'] ) ) ) {
				return;
			}

			if ( in_array( $element->term_id, $this->exclude_ids, true ) || in_array( $element->parent, $this->exclude_ids, true ) ) {
				return;
			}
			parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		}

		/** Starts the list before the elements are added.
		 *
		 * @param string $output Used to append additional content. Passed by reference.
		 * @param int    $depth Optional. Depth of category. Used for tab indentation. Default 0.
		 * @param array  $args Optional. An array of arguments. Will only append content if style argument
		 *                       value is 'list'. See wp_list_categories(). Default empty array.
		 *
		 * @see Walker::start_lvl()
		 *
		 * @since 2.1.0
		 *
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			if ( 'list' !== $args['style'] ) {
				return;
			}

			$indent = str_repeat( "\t", $depth );
			$output .= "$indent<ul class='yith-children'>\n";
		}
	}
}
