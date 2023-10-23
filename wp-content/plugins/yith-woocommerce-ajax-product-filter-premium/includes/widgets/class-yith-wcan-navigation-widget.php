<?php
/**
 * Ajax filter widget (legacy)
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Widgets
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Navigation_Widget' ) ) {
	/**
	 * YITH WooCommerce Ajax Navigation Widget
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Navigation_Widget extends WP_Widget {

		/**
		 * YITH Brands Taxonomy Name
		 *
		 * @var string
		 */
		public $brand_taxonomy = '';

		/**
		 * Use to print or not widget
		 *
		 * @var bool
		 */
		public $found = false;

		/**
		 * Construct method
		 *
		 * @return void
		 */
		public function __construct() {
			$classname   = 'yith-woocommerce-ajax-product-filter yith-woo-ajax-navigation woocommerce widget_layered_nav';
			$classname  .= defined( 'YITH_WCAN_PREMIUM' ) && 'checkboxes' === yith_wcan_get_option( 'yith_wcan_ajax_shop_filter_style', 'standard' ) ? ' with-checkbox' : '';
			$widget_ops  = array(
				'classname'   => $classname,
				'description' => __( 'Filter the list of products without reloading the page', 'yith-woocommerce-ajax-navigation' ),
			);
			$control_ops = array(
				'width'  => 400,
				'height' => 350,
			);
			add_action( 'wp_ajax_yith_wcan_select_type', array( $this, 'ajax_print_terms' ) );
			parent::__construct( 'yith-woo-ajax-navigation', _x( 'YITH AJAX Product Filter', '[Plugin Name] Admin: Widget Title', 'yith-woocommerce-ajax-navigation' ), $widget_ops, $control_ops );

			/**
			 * Deprecated Filters Map
			 *
			 * @param mixed|array $deprecated_filters_map Array of deprecated filters
			 * @since 3.11.7
			 * @ return void
			 */
			$deprecated_filters_map = array(
				"{$this->id}-show_product_count" => array(
					'since'  => '3.11.7',
					'use'    => "{$this->id}_show_product_count",
					'params' => 2,
				),
				"{$this->id}-li_style"           => array(
					'since'  => '3.11.7',
					'use'    => "{$this->id}_li_style",
					'params' => 2,
				),
				'yith_wcan_in_array_ignor_case'  => array(
					'since'  => '4.1.1',
					'use'    => 'yith_wcan_in_array_ignore_case',
					'params' => 1,
				),
			);

			yith_wcan_deprecated_filter( $deprecated_filters_map );
		}

		/**
		 * Prints the widget
		 *
		 * @param array $args General widget arguments.
		 * @param array $instance Current instance arguments.
		 *
		 * @return void
		 */
		public function widget( $args, $instance ) {
			global $wc_product_attributes, $wp_query;

			$_chosen_attributes = YITH_WCAN()->get_layered_nav_chosen_attributes();
			$_attributes_array  = yit_wcan_get_product_taxonomy();

			/**
			 * Extracted vars:
			 *
			 * @var $before_widget string
			 * @var $after_widget string
			 * @var $title string
			 * @var $before_title string
			 * @var $after_title string
			 */
			extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract

			if ( apply_filters( 'yith_wcan_is_search', is_search() ) ) {
				return;
			}

			if ( apply_filters( 'yith_wcan_show_widget', ! is_post_type_archive( 'product' ) && ! is_tax( $_attributes_array ), $instance ) ) {
				return;
			}

			if ( yith_wcan_brands_enabled() ) {
				$this->brand_taxonomy = YITH_WCBR::$brands_taxonomy;
			}

			$queried_object    = $wp_query instanceof WP_Query ? $wp_query->get_queried_object() : false;
			$filter_term_field = YITH_WCAN()->filter_term_field;
			$current_term      = $_attributes_array && is_tax( $_attributes_array ) && ! empty( $queried_object ) ? $queried_object->$filter_term_field : '';
			$title             = apply_filters( 'yith_widget_title_ajax_navigation', ( isset( $instance['title'] ) ? $instance['title'] : '' ), $instance, $this->id_base );
			$query_type        = isset( $instance['query_type'] ) ? $instance['query_type'] : 'and';
			$display_type      = isset( $instance['type'] ) ? $instance['type'] : 'list';
			$is_child_class    = 'yit-wcan-child-terms';
			$is_parent_class   = 'yit-wcan-parent-terms';
			$is_chosen_class   = 'chosen';
			$terms_type_list   = ( isset( $instance['display'] ) ) ? $instance['display'] : 'all';
			$in_array_function = apply_filters( 'yith_wcan_in_array_ignore_case', false ) ? 'yit_in_array_ignore_case' : 'in_array';
			$rel_nofollow      = yith_wcan_add_rel_nofollow_to_url( true );

			$instance['id']          = $this->id;
			$instance['attribute']   = empty( $instance['attribute'] ) ? '' : $instance['attribute'];
			$instance['extra_class'] = empty( $instance['extra_class'] ) ? '' : $instance['extra_class'];

			$taxonomy = function_exists( 'wc_attribute_taxonomy_name' ) ? wc_attribute_taxonomy_name( $instance['attribute'] ) : WC()->attribute_taxonomy_name( $instance['attribute'] );
			$taxonomy = apply_filters( 'yith_wcan_get_terms_params', $taxonomy, $instance, 'taxonomy_name' );

			$terms_type_list = apply_filters( 'yith_wcan_get_terms_params', $terms_type_list, $instance, 'terms_type' );

			if ( ! taxonomy_exists( $taxonomy ) ) {
				return;
			}

			$terms = yit_get_terms( $terms_type_list, $taxonomy, $instance );

			if ( count( $terms ) > 0 ) {
				ob_start();

				$this->found = false;

				echo $before_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

				$title = html_entity_decode( apply_filters( 'widget_title', $title ) );

				if ( ! empty( $title ) ) {
					echo $before_title . wp_kses_post( $title ) . $after_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				// Force found when option is selected - do not force found on taxonomy attributes.
				if ( ! $_attributes_array || ! is_tax( $_attributes_array ) ) {
					if ( is_array( $_chosen_attributes ) && array_key_exists( $taxonomy, $_chosen_attributes ) ) {
						$this->found = true;
					}
				}

				if ( in_array( $display_type, apply_filters( 'yith_wcan_display_type_list', array( 'list' ) ), true ) ) {

					$tree      = array();
					$ancestors = yith_wcan_wp_get_terms(
						array(
							'taxonomy'     => $taxonomy,
							'parent'       => 0,
							'hierarchical' => true,
							'hide_empty'   => false,
						)
					);

					if ( ! empty( $ancestors ) && ! is_wp_error( $ancestors ) ) {

						if ( 'product' === yith_wcan_get_option( 'yith_wcan_ajax_shop_terms_order', 'alphabetical' ) ) {
							usort( $ancestors, 'yit_terms_sort' );
						} elseif ( 'alphabetical' === yith_wcan_get_option( 'yith_wcan_ajax_shop_terms_order', 'alphabetical' ) ) {
							usort( $ancestors, 'yit_alphabetical_terms_sort' );
						}

						foreach ( $ancestors as $ancestor ) {
							$tree[ $ancestor->term_id ] = 'parent' === $terms_type_list ? array() : yit_reorder_hierachical_categories( $ancestor->term_id, $taxonomy );
						}
					}

					do_action( 'yith_wcan_before_print_list', $taxonomy );

					$this->add_reset_taxonomy_link( $taxonomy, $instance );

					// List display.
					echo '<ul class="yith-wcan-list yith-wcan ' . esc_attr( $instance['extra_class'] ) . '">';

					$this->get_list_html( $tree, $taxonomy, $query_type, $display_type, $instance, $terms_type_list, $current_term, $args, $is_child_class, $is_parent_class, $is_chosen_class, 0, $filter_term_field, $rel_nofollow );

					echo '</ul>';
				} elseif ( in_array( $display_type, apply_filters( 'yith_wcan_display_type_select', array( 'select' ) ), true ) ) {
					$dropdown_label = apply_filters( 'yith_wcan_dropdown_label', __( 'Filters:', 'yith-woocommerce-ajax-navigation' ), $this, $instance, $instance['attribute'] );
					?>

					<a class="yit-wcan-select-open" href="#"><?php echo wp_kses_post( apply_filters( 'yith_wcan_dropdown_default_label', $dropdown_label, $this ) ); ?></a>

					<?php
					// Select display.
					echo "<div class='yith-wcan-select-wrapper'>";

					echo '<ul class="yith-wcan-select yith-wcan ' . esc_attr( $instance['extra_class'] ) . '">';

					$this->found = false;
					foreach ( $terms as $term ) {

						$_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );
						$option_is_set     = ( isset( $_chosen_attributes[ $taxonomy ] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[ $taxonomy ]['terms'] ) );

						if ( 'and' === $query_type ) {
							// If this is an AND query, only show options with count > 0.
							$count = count( array_intersect( $_products_in_term, YITH_WCAN()->frontend->layered_nav_product_ids ) );

							if ( $count > 0 ) {
								$this->found = true;
							}

							if ( ( 'hierarchical' !== $terms_type_list || ! yit_term_has_child( $term, $taxonomy ) ) && ! $count && ! $option_is_set ) {
								continue;
							}
						} else {
							// If this is an OR query, show all options so search can be expanded.
							$count = count( array_intersect( $_products_in_term, YITH_WCAN()->frontend->unfiltered_product_ids ) );

							if ( $count > 0 ) {
								$this->found = true;
							}

							if ( apply_filters( 'yith_wcan_skip_no_products_dropdown', ! $count ) ) {
								continue;
							}
						}

						$current_filter = $this->get_current_filters( $term, $instance );

						list( $link, $class ) = $this->get_link_attributes( $term, $taxonomy, $current_filter, $instance );

						$hide_count = ! empty( $instance['show_count'] ) && $instance['show_count'];
						$show_count = apply_filters( "{$this->id}_show_product_count", ( $count && ! $hide_count ), $instance );

						echo '<li ' . $class . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						echo ( $this->found || $option_is_set ) ? '<a ' . $rel_nofollow . ' data-type="select" href="' . esc_url( $link ) . '">' : '<span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						echo wp_kses_post( $term->name );

						if ( $this->found && apply_filters( 'yith_wcan_force_show_count', $show_count ) ) {
							echo ' <small class="count">' . wp_kses_post( $count ) . '</small><div class="clear"></div>';
						}

						echo ( $this->found || $option_is_set ) ? '</a>' : '</span>';

						echo '</li>';
					}

					echo '</ul>';

					echo '</div>';
				} elseif ( 'color' === $display_type ) {
					// List display.
					echo '<ul class="yith-wcan-color yith-wcan yith-wcan-group ' . esc_attr( $instance['extra_class'] ) . '">';

					foreach ( $terms as $term ) {

						// Get count based on current view - uses transients.
						$_products_in_term = get_objects_in_term( apply_filters( 'yith_wcan_color_get_objects_in_term', $term->term_id, $taxonomy, $term ), $taxonomy );

						$option_is_set = ( isset( $_chosen_attributes[ $taxonomy ] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[ $taxonomy ]['terms'] ) );

						if ( 'and' === $query_type ) {
							// If this is an AND query, only show options with count > 0.
							$count = count( array_intersect( $_products_in_term, YITH_WCAN()->frontend->layered_nav_product_ids ) );

							if ( $count > 0 ) {
								$this->found = true;
							}

							if ( apply_filters( 'yith_wcan_skip_no_products_color', ! $count && ! $option_is_set ) ) {
								continue;
							}
						} else {
							// If this is an OR query, show all options so search can be expanded.
							$count = count( array_intersect( $_products_in_term, YITH_WCAN()->frontend->unfiltered_product_ids ) );

							if ( $count > 0 ) {
								$this->found = true;
							}

							if ( apply_filters( 'yith_wcan_skip_no_products_color', ! $count ) ) {
								continue;
							}
						}

						$current_filter = $this->get_current_filters( $term, $instance );

						list( $link, $class ) = $this->get_link_attributes( $term, $taxonomy, $current_filter, $instance );

						$term_id = yit_wcan_localize_terms( $term->term_id, $taxonomy );

						$color = '';

						if ( ! empty( $instance['colors'][ $term_id ] ) ) {
							$color = $instance['colors'][ $term_id ];
						} elseif ( apply_filters( 'yith_wcan_ywccl_support', function_exists( 'ywccl_get_term_meta' ) ) && ! empty( $wc_product_attributes[ $term->taxonomy ]->attribute_type ) && 'colorpicker' === $wc_product_attributes[ $term->taxonomy ]->attribute_type ) {
							$colors = ywccl_get_term_meta( $term->term_id, $term->taxonomy . '_yith_wccl_value' );

							if ( ! empty( $colors ) ) {
								$colors = explode( ',', $colors );
								$color  = $colors[0];
							}
						}

						if ( $color ) {
							$li_style = apply_filters( "{$this->id}_li_style", 'background-color:' . $color . ';', $instance );

							echo '<li ' . $class . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

							echo ( $count > 0 || $option_is_set ) ? '<a ' . $rel_nofollow . ' style="' . esc_attr( $li_style ) . '" href="' . esc_url( $link ) . '" title="' . esc_attr( $term->name ) . '" >' : '<span class="yith-wcan-color-not-available" style="' . esc_attr( $li_style ) . ';" >'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

							echo wp_kses_post( $term->name );

							echo ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';
						}
					}

					echo '</ul>';

				} elseif ( 'label' === $display_type ) {
					// List display.
					echo '<ul class="yith-wcan-label yith-wcan yith-wcan-group ' . esc_attr( $instance['extra_class'] ) . '">';

					foreach ( $terms as $term ) {
						$_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );
						$option_is_set     = ( isset( $_chosen_attributes[ $taxonomy ] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[ $taxonomy ]['terms'] ) );

						if ( 'and' === $query_type ) {
							// If this is an AND query, only show options with count > 0.
							$count = count( array_intersect( $_products_in_term, YITH_WCAN()->frontend->layered_nav_product_ids ) );

							if ( $count > 0 ) {
								$this->found = true;
							}

							if ( ! $count && ! $option_is_set ) {
								continue;
							}
						} else {
							// If this is an OR query, show all options so search can be expanded.
							$count = count( array_intersect( $_products_in_term, YITH_WCAN()->frontend->unfiltered_product_ids ) );

							if ( $count > 0 ) {
								$this->found = true;
							}

							if ( apply_filters( 'yith_wcan_skip_no_products_label', ! $count ) ) {
								continue;
							}
						}

						$current_filter = $this->get_current_filters( $term, $instance );

						list( $link, $class ) = $this->get_link_attributes( $term, $taxonomy, $current_filter, $instance );

						$term_id = yit_wcan_localize_terms( $term->term_id, $taxonomy );

						$label = '';

						if ( ! empty( $instance['labels'][ $term_id ] ) ) {
							$label = $instance['labels'][ $term_id ];
						} elseif ( function_exists( 'ywccl_get_term_meta' ) && ! empty( $wc_product_attributes[ $term->taxonomy ]->attribute_type ) && 'label' === $wc_product_attributes[ $term->taxonomy ]->attribute_type ) {
							$label = ywccl_get_term_meta( $term->term_id, $term->taxonomy . '_yith_wccl_value' );
						}

						if ( apply_filters( 'yith_wcan_filter_label_text', $label, $term, $taxonomy ) ) {

							echo '<li ' . $class . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

							echo ( $count > 0 || $option_is_set ) ? '<a ' . $rel_nofollow . ' title="' . esc_attr( $term->name ) . '" href="' . esc_url( $link ) . '">' : '<span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

							echo wp_kses_post( $label );

							echo ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';
						}
					}
					echo '</ul>';

				} else {
					do_action( "yith_wcan_widget_display_{$display_type}", $args, $instance, $display_type, $terms, $taxonomy, $filter_term_field );
				}

				echo $after_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

				if ( ! $this->found ) {
					ob_end_clean();
				} else {
					echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}

		/**
		 * Outputs the form to configure widget
		 *
		 * @param array $instance Current instance.
		 *
		 * @return void
		 */
		public function form( $instance ) {
			$defaults = array(
				'title'       => '',
				'attribute'   => '',
				'query_type'  => 'and',
				'type'        => 'list',
				'colors'      => '',
				'multicolor'  => array(),
				'labels'      => '',
				'display'     => 'all',
				'extra_class' => '',
			);
			$instance = wp_parse_args( (array) $instance, $defaults );

			$widget_types = apply_filters(
				'yith_wcan_widget_types',
				array(
					'list'   => __( 'List', 'yith-woocommerce-ajax-navigation' ),
					'color'  => __( 'Color', 'yith-woocommerce-ajax-navigation' ),
					'label'  => __( 'Label', 'yith-woocommerce-ajax-navigation' ),
					'select' => __( 'Dropdown', 'yith-woocommerce-ajax-navigation' ),
				)
			);
			?>

			<p>
				<label>
					<strong><?php esc_html_e( 'Title', 'yith-woocommerce-ajax-navigation' ); ?>:</strong><br/>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>"/>
				</label>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><strong><?php esc_html_e( 'Type:', 'yith-woocommerce-ajax-navigation' ); ?></strong></label>
				<select class="yith_wcan_type widefat" id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>">
					<?php foreach ( $widget_types as $type => $label ) : ?>
						<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $type, $instance['type'] ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<?php do_action( 'yith_wcan_after_widget_type' ); ?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'query_type' ) ); ?>"><?php esc_html_e( 'Query Type:', 'yith-woocommerce-ajax-navigation' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'query_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'query_type' ) ); ?>">
					<option value="and" <?php selected( $instance['query_type'], 'and' ); ?>><?php echo esc_html_x( 'AND', '[ADMIN] Ajax Filter Widget; logical operator that affects query behaviour', 'yith-woocommerce-ajax-navigation' ); ?></option>
					<option value="or" <?php selected( $instance['query_type'], 'or' ); ?>><?php esc_html_e( 'OR', 'yith-woocommerce-ajax-navigation' ); ?></option>
				</select>
			</p>

			<p class="yith-wcan-attribute-list" style="display: <?php echo in_array( $instance['type'], array( 'tags', 'brands', 'categories' ), true ) ? 'none' : 'block'; ?>;">
				<label for="<?php echo esc_attr( $this->get_field_id( 'attribute' ) ); ?>"><strong><?php esc_html_e( 'Attribute:', 'yith-woocommerce-ajax-navigation' ); ?></strong></label>
				<select class="yith_wcan_attributes widefat" id="<?php echo esc_attr( $this->get_field_id( 'attribute' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'attribute' ) ); ?>">
					<?php yith_wcan_dropdown_attributes( $instance['attribute'] ); ?>
				</select>
			</p>

			<p id="yit-wcan-display" class="yit-wcan-display-<?php echo esc_attr( $instance['type'] ); ?>">
				<label for="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>"><strong><?php esc_html_e( 'Display (default All):', 'yith-woocommerce-ajax-navigation' ); ?></strong></label>
				<select class="yith_wcan_type widefat" id="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display' ) ); ?>">
					<option value="all" <?php selected( 'all', $instance['display'] ); ?> ><?php esc_html_e( 'All (no hierarchical)', 'yith-woocommerce-ajax-navigation' ); ?></option>
					<option value="hierarchical" <?php selected( 'hierarchical', $instance['display'] ); ?> ><?php esc_html_e( 'All (hierarchical)', 'yith-woocommerce-ajax-navigation' ); ?></option>
					<option value="parent" <?php selected( 'parent', $instance['display'] ); ?> ><?php esc_html_e( 'Only Parent', 'yith-woocommerce-ajax-navigation' ); ?></option>
				</select>
			</p>

			<?php if ( defined( 'YITH_WCAN_PREMIUM' ) ) : ?>
				<p>
					<label>
						<strong><?php esc_html_e( 'CSS custom class', 'yith-woocommerce-ajax-navigation' ); ?>:</strong><br/>
						<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'extra_class' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'extra_class' ) ); ?>" value="<?php echo esc_attr( $instance['extra_class'] ); ?>"/>
					</label>
				</p>
			<?php endif; ?>

			<div class="yith_wcan_placeholder">
				<?php
				$values = array();

				if ( 'color' === $instance['type'] ) {
					$values = $instance['colors'];
				} elseif ( 'multicolor' === $instance['type'] ) {
					$values = $instance['multicolor'];
				} elseif ( 'label' === $instance['type'] ) {
					$values = $instance['labels'];
				}

				yith_wcan_attributes_table(
					$instance['type'],
					$instance['attribute'],
					'widget-' . $this->id . '-',
					'widget-' . $this->id_base . '[' . $this->number . ']',
					$values,
					$instance['display']
				);
				?>
			</div>

			<span class="spinner" style="display: none;"></span>

			<input type="hidden" name="widget_id" value="widget-<?php echo esc_attr( $this->id ); ?>-"/>
			<input type="hidden" name="widget_name" value="widget-<?php echo esc_attr( $this->id_base ); ?>[<?php echo esc_attr( $this->number ); ?>]"/>

			<script>jQuery(document).trigger('yith_colorpicker');</script>
			<?php
		}

		/**
		 * Update intance
		 *
		 * @param array $new_instance New instance.
		 * @param array $old_instance Old instance.
		 *
		 * @return array Formatted instance.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance                = $old_instance;
			$instance['title']       = wp_strip_all_tags( $new_instance['title'] );
			$instance['attribute']   = ! empty( $new_instance['attribute'] ) ? stripslashes( $new_instance['attribute'] ) : array();
			$instance['query_type']  = stripslashes( $new_instance['query_type'] );
			$instance['type']        = stripslashes( $new_instance['type'] );
			$instance['colors']      = ! empty( $new_instance['colors'] ) ? $new_instance['colors'] : array();
			$instance['multicolor']  = ! empty( $new_instance['multicolor'] ) ? $new_instance['multicolor'] : array();
			$instance['labels']      = ! empty( $new_instance['labels'] ) ? $new_instance['labels'] : array();
			$instance['display']     = $new_instance['display'];
			$instance['extra_class'] = ! empty( $new_instance['extra_class'] ) ? $new_instance['extra_class'] : '';

			return $instance;
		}

		/**
		 * Retrieves currently active filters
		 *
		 * @param WP_Term $term Current term.
		 * @param array   $args Array of parameters (most comes our ot widget instance).
		 *
		 * @return array Array of currently active filters, plus term passed as param
		 */
		public function get_current_filters( $term, $args = array() ) {
			$current_filters   = array();
			$filter_term_field = YITH_WCAN()->filter_term_field;
			$display_type      = isset( $args['type'] ) ? $args['type'] : 'list';
			$query_type        = isset( $args['query_type'] ) ? $args['query_type'] : 'and';
			$arg               = apply_filters( "yith_wcan_{$display_type}_type_query_arg", isset( $args['attribute'] ) ? 'filter_' . sanitize_title( $args['attribute'] ) : '', $display_type, $term );
			$in_array_function = apply_filters( 'yith_wcan_in_array_ignore_case', false ) ? 'yit_in_array_ignore_case' : 'in_array';
			$request           = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( isset( $request[ $arg ] ) ) {
				$separator            = apply_filters( "yith_wcan_{$display_type}_filter_operator", ',', $display_type, $query_type );
				$current_filter_param = sanitize_text_field( wp_unslash( $request[ $arg ] ) );
				$current_filter_param = str_replace( ' ', '+', $current_filter_param );
				$current_filter_param = apply_filters( "yith_wcan_{$display_type}_filter_query_{$arg}", $current_filter_param );

				$current_filters = explode( $separator, $current_filter_param );
			}

			if ( ! is_array( $current_filters ) ) {
				$current_filters = array();
			}

			$current_filters = array_map( 'esc_attr', $current_filters );

			if ( property_exists( $term, $filter_term_field ) && ! $in_array_function( $term->$filter_term_field, $current_filters ) ) {
				$current_filters[] = $term->$filter_term_field;
			}

			return apply_filters( "yith_wcan_{$arg}_current_filter", $current_filters );
		}

		/**
		 * Returns attributes (href and class) to be used for filter anchors
		 *
		 * @param WP_Term $term Current term.
		 * @param string  $taxonomy Current taxonomy.
		 * @param array   $current_filters Filters currently applied.
		 * @param array   $args Array of parameters (most comes our ot widget instance).
		 *
		 * @return array Array of attributes to print (href and class)
		 */
		public function get_link_attributes( $term, $taxonomy, $current_filters = array(), $args = array() ) {
			global $wp_query;
			$queried_object     = $wp_query instanceof WP_Query ? $wp_query->get_queried_object() : false;
			$_attributes_array  = yit_wcan_get_product_taxonomy();
			$_chosen_attributes = YITH_WCAN()->get_layered_nav_chosen_attributes();
			$filter_term_field  = YITH_WCAN()->filter_term_field;
			$current_term       = $_attributes_array && is_tax( $_attributes_array ) ? $queried_object->$filter_term_field : '';
			$in_array_function  = apply_filters( 'yith_wcan_in_array_ignore_case', false ) ? 'yit_in_array_ignore_case' : 'in_array';
			$query_type         = isset( $args['query_type'] ) ? $args['query_type'] : 'and';
			$display_type       = isset( $args['type'] ) ? $args['type'] : 'list';
			$terms_type_list    = apply_filters( 'yith_wcan_get_terms_params', isset( $args['display'] ) ? $args['display'] : 'all', $args, 'terms_type' );
			$arg                = apply_filters( "yith_wcan_{$display_type}_type_query_arg", isset( $args['attribute'] ) ? 'filter_' . sanitize_title( $args['attribute'] ) : '', $display_type, $term );
			$is_child_class     = 'yit-wcan-child-terms';
			$is_chosen_class    = 'chosen';
			$is_parent_class    = 'yit-wcan-parent-terms';
			$link               = yit_get_woocommerce_layered_nav_link();
			$request            = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			// All current filters.
			if ( $_chosen_attributes ) {
				foreach ( $_chosen_attributes as $name => $data ) {
					if ( $name !== $taxonomy ) {

						// Exclude query arg for current term archive term.
						while ( $in_array_function( $term->slug, $data['terms'] ) ) {
							$key = array_search( $current_term, $data ); // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
							unset( $data['terms'][ $key ] );
						}

						// Remove pa_ and sanitize.
						$filter_name = urldecode( sanitize_title( str_replace( 'pa_', '', $name ) ) );

						if ( ! empty( $data['terms'] ) ) {
							$link = add_query_arg( 'filter_' . $filter_name, implode( apply_filters( "yith_wcan_{$display_type}_filter_operator", ',', $display_type, $query_type ), $data['terms'] ), $link );
						}

						if ( 'or' === $data['query_type'] ) {
							$link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
						}
					}
				}
			}

			// Min price.
			if ( isset( $request['min_price'] ) ) {
				$link = add_query_arg( 'min_price', (float) $request['min_price'], $link );
			}

			// Max price.
			if ( isset( $request['max_price'] ) ) {
				$link = add_query_arg( 'max_price', (float) $request['max_price'], $link );
			}

			// Product tag.
			if ( isset( $request['product_tag'] ) ) {
				$link = add_query_arg( 'product_tag', urlencode( sanitize_text_field( wp_unslash( $request['product_tag'] ) ) ), $link ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
			} elseif ( is_product_tag() && $queried_object ) {
				$link = add_query_arg( array( 'product_tag' => $queried_object->slug ), $link );
			}

			// Brand.
			if ( isset( $request[ $this->brand_taxonomy ] ) ) {
				$brands = get_term_by( 'slug', sanitize_text_field( wp_unslash( $request[ $this->brand_taxonomy ] ) ), $this->brand_taxonomy );

				if ( $brands instanceof WP_Term && $brands->term_id !== $term->term_id ) {
					$link = add_query_arg( $this->brand_taxonomy, urlencode( $brands->slug ), $link ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
				}
			} elseif ( is_tax( $this->brand_taxonomy ) && $queried_object ) {
				$link = add_query_arg( array( $this->brand_taxonomy => $queried_object->slug ), $link );
			}

			// Product category.
			if ( isset( $request['product_cat'] ) ) {
				$categories_filter_operator = 'and' === $query_type ? '+' : ',';
				$_chosen_categories         = explode( $categories_filter_operator, urlencode( sanitize_text_field( wp_unslash( $request['product_cat'] ) ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
				$link                       = add_query_arg(
					'product_cat',
					implode( apply_filters( 'yith_wcan_categories_filter_operator', $categories_filter_operator, $display_type, $query_type ), $_chosen_categories ),
					$link
				);
			} elseif ( is_product_category() && $queried_object ) {
				$link = add_query_arg( array( 'product_cat' => $queried_object->slug ), $link );
			}

			// Current term.
			if ( is_product_taxonomy() && ! yit_is_filtered_uri() && $term->term_id !== $queried_object->term_id ) {
				$link = add_query_arg(
					array(
						'source_id'               => $queried_object->term_id,
						'source_tax'              => $queried_object->taxonomy,
						$queried_object->taxonomy => $queried_object->slug,
					),
					$link
				);
			}

			// Current term.
			if ( isset( $request['source_id'] ) && isset( $request['source_tax'] ) ) {
				$add_source_id = true;
				if ( property_exists( $term, 'term_id' ) && property_exists( $queried_object, 'term_id' ) && $term->term_id === $queried_object->term_id ) {
					if ( ! yit_is_filtered_uri() ) {
						$add_source_id = false;
					}
				}

				if ( $add_source_id ) {
					$query_args = array(
						'source_id'  => sanitize_text_field( wp_unslash( $request['source_id'] ) ),
						'source_tax' => sanitize_text_field( wp_unslash( $request['source_tax'] ) ),
					);
					if ( property_exists( $queried_object, 'taxonomy' ) && isset( $request[ $queried_object->taxonomy ] ) ) {
						$args[ $queried_object->taxonomy ] = sanitize_text_field( wp_unslash( $request[ $queried_object->taxonomy ] ) );
					}
					$link = add_query_arg( $query_args, $link );
				}
			}

			// Vendor.
			if ( isset( $request['yith_shop_vendor'] ) ) {
				$link = add_query_arg( array( 'yith_shop_vendor' => sanitize_text_field( wp_unslash( $request['yith_shop_vendor'] ) ) ), $link );
			}

			// Current Filter = this widget.
			$term_param               = apply_filters( 'yith_wcan_term_param_uri', $term->$filter_term_field, $display_type, $term );
			$check_for_current_widget = isset( $_chosen_attributes[ $taxonomy ] ) && is_array( $_chosen_attributes[ $taxonomy ]['terms'] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[ $taxonomy ]['terms'] );
			$check_for_current_widget = apply_filters( "yith_wcan_{$display_type}_type_current_widget_check", $check_for_current_widget, $current_filters, $display_type, $term_param, $query_type );

			if ( $check_for_current_widget ) {
				if ( ( 'hierarchical' === $terms_type_list || ( 'tags' === $terms_type_list && 'hierarchical' === $args['display'] ) ) ) {
					if ( yit_term_is_child( $term ) ) {
						$class = "class='{$is_chosen_class}  {$is_child_class}'";
					} elseif ( yit_term_is_parent( $term ) ) {
						$class = "class='{$is_chosen_class}  {$is_parent_class}'";
					} else {
						$class = '';
					}
				} else {
					$class = "class='{$is_chosen_class}'";
				}

				// Remove this term is $current_filter has more than 1 term filtered.
				if ( count( $current_filters ) > 1 ) {
					$current_filter_without_this = array_diff( $current_filters, array( $term->$filter_term_field ) );
					$link                        = add_query_arg( $arg, implode( apply_filters( "yith_wcan_{$display_type}_filter_operator", ',', $display_type, $query_type ), $current_filter_without_this ), $link );
				} else {
					$link = remove_query_arg( $arg, $link );
				}
			} else {
				if ( ( 'hierarchical' === $terms_type_list || 'tags' === $terms_type_list ) ) {
					if ( yit_term_is_child( $term ) ) {
						$class = "class='{$is_child_class}'";
					} elseif ( yit_term_is_parent( $term ) ) {
						$class = "class='{$is_parent_class}'";
					} else {
						$class = '';
					}
				} else {
					$class = '';
				}

				$link = add_query_arg( $arg, implode( apply_filters( "yith_wcan_{$display_type}_filter_operator", ',', $display_type, $query_type ), $current_filters ), $link );
			}

			// Search Arg.
			if ( get_search_query() ) {
				$link = add_query_arg( 's', urlencode( get_search_query() ), $link ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
			}

			// Post Type Arg.
			if ( isset( $request['post_type'] ) ) {
				$link = add_query_arg( 'post_type', sanitize_text_field( wp_unslash( $request['post_type'] ) ), $link );
			}

			// Query type Arg.
			$is_attribute = apply_filters( 'yith_wcan_is_attribute_check', true, $args );

			if ( $is_attribute && 'or' === $query_type && ! ( 1 === count( $current_filters ) && isset( $_chosen_attributes[ $taxonomy ]['terms'] ) && is_array( $_chosen_attributes[ $taxonomy ]['terms'] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[ $taxonomy ]['terms'] ) ) ) {
				$link = isset( $args['attribute'] ) ? add_query_arg( 'query_type_' . sanitize_title( $args['attribute'] ), 'or', $link ) : $link;
			}

			$link = esc_url( urldecode( apply_filters( 'woocommerce_layered_nav_link', $link ) ) );

			return array(
				$link,
				$class,
			);
		}

		/**
		 * Print terms for the element selected
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public function ajax_print_terms() {
			$unsanitize_posted_data = $_POST; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$posted_data            = array();

			foreach ( $unsanitize_posted_data as $k => $v ) {
				$posted_data[ $k ] = esc_html( $v );
			}

			$type      = $posted_data['value'];
			$attribute = $posted_data['attribute'];
			$post_id   = $posted_data['id'];
			$name      = $posted_data['name'];
			$return    = array(
				'message' => '',
				'content' => $posted_data,
			);

			$settings        = $this->get_settings();
			$widget_settings = $settings[ $this->number ];
			$value           = '';

			if ( 'label' === $type ) {
				$value = $widget_settings['labels'];
			} elseif ( 'color' === $type ) {
				$value = $widget_settings['colors'];
			} elseif ( 'multicolor' === $type ) {
				$value = $widget_settings['multicolor'];
			}

			if ( $type ) {
				$return['content'] = yith_wcan_attributes_table(
					$type,
					$attribute,
					$post_id,
					$name,
					$value,
					false
				);
			}

			echo wp_json_encode( $return );
			die();
		}

		/**
		 * Prints list of terms for current filter
		 *
		 * @param array  $terms List of terms to print.
		 * @param string $taxonomy Taxonomy for the list of terms.
		 * @param string $query_type One among AND/OR.
		 * @param string $display_type One among list/color/label/select.
		 * @param array  $instance Widget instance.
		 * @param string $terms_type_list Hierarchical status.
		 * @param string $current_term Current term.
		 * @param array  $args Array of parameters.
		 * @param string $is_child_class Class for child items.
		 * @param string $is_parent_class Class for parent items.
		 * @param string $is_chosen_class Class for chosen items.
		 * @param int    $level Current nesting level.
		 * @param string $filter_term_field Field of the term object used to filter selection.
		 * @param string $rel_nofollow Attributes to be used for links of the list.
		 *
		 * @return void
		 */
		public function get_list_html( $terms, $taxonomy, $query_type, $display_type, $instance, $terms_type_list, $current_term, $args, $is_child_class, $is_parent_class, $is_chosen_class, $level = 0, $filter_term_field = 'slug', $rel_nofollow = '' ) {
			$_chosen_attributes = YITH_WCAN()->get_layered_nav_chosen_attributes();
			$in_array_function  = apply_filters( 'yith_wcan_in_array_ignore_case', false ) ? 'yit_in_array_ignore_case' : 'in_array';
			$terms              = apply_filters( 'yith_wcan_get_list_html_terms', $terms, $taxonomy, $instance );

			foreach ( $terms as $parent_id => $term_ids ) {
				$term = get_term_by( 'id', $parent_id, $taxonomy );

				$exclude = apply_filters( 'yith_wcan_exclude_terms', array(), $instance );
				$include = apply_filters( 'yith_wcan_include_terms', array(), $instance );
				$echo    = false;

				if ( 'tags' === $instance['type'] ) {
					$term_id = yit_wcan_localize_terms( $term->term_id, $taxonomy );
					if ( 'exclude' === $instance['tags_list_query'] ) {
						$echo = ! $in_array_function( $term_id, $exclude );
					} elseif ( 'include' === $instance['tags_list_query'] ) {
						$echo = $in_array_function( $term_id, $include );
					}
				} else {
					$echo = true;
				}

				$filter_by_tags_hierarchical = ( 'tags' === $terms_type_list && 'hierarchical' === $instance['display'] );

				if ( $echo ) {

					// Get count based on current view - uses transients.
					$_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );
					$option_is_set     = ( isset( $_chosen_attributes[ $taxonomy ] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[ $taxonomy ]['terms'] ) );
					$term_param        = apply_filters( 'yith_wcan_term_param_uri', $term->$filter_term_field, $display_type, $term );

					if ( 'and' === $query_type ) {
						// If this is an AND query, only show options with count > 0.
						$count = count( array_intersect( $_products_in_term, YITH_WCAN()->frontend->layered_nav_product_ids ) );
					} else {
						// If this is an OR query, show all options so search can be expanded.
						$count = count( array_intersect( $_products_in_term, YITH_WCAN()->frontend->unfiltered_product_ids ) );
					}

					if ( $count > 0 ) {
						$this->found = true;
					}

					$current_filter = $this->get_current_filters( $term, $instance );

					list( $link, $class ) = $this->get_link_attributes( $term, $taxonomy, $current_filter, $instance );

					$li_printed = false;
					$term_name  = apply_filters( 'yith_wcan_term_name_to_show', $term->name, $term );

					if ( $count > 0 || $option_is_set ) {
						$to_print = true;
						printf( '<li %s><a %s href="%s">%s</a>', $class, $rel_nofollow, esc_url( $link ), wp_kses_post( $term_name ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						$li_printed = true;
					} else {
						$to_print = apply_filters( 'yith_wcan_show_no_products_attributes', ( ! $filter_by_tags_hierarchical && 'and' !== $query_type ), $count, $term );
						$class    = apply_filters( 'yith_wcan_list_type_empty_filter_class', $class );

						if ( $to_print ) {
							printf( '<li %s><span>%s</span>', $class, wp_kses_post( $term_name ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$li_printed = true;
						}
					}

					$hide_count = ! empty( $instance['show_count'] ) && $instance['show_count'];
					$show_count = apply_filters( "{$this->id}_show_product_count", ( $count && ! $hide_count ), $instance );

					if ( $to_print && apply_filters( 'yith_wcan_force_show_count', $show_count ) ) {
						echo ' <small class="count">' . wp_kses_post( $count ) . '</small><div class="clear"></div>';
					}

					if ( $li_printed ) {
						echo '</li>';
					}
				}

				if ( ! empty( $term_ids ) && is_array( $term_ids ) ) {
					echo '<ul class="yith-child-terms level-' . esc_attr( $level ) . '">';
					$temp_level = $level;
					$temp_level ++;
					$this->get_list_html( $term_ids, $taxonomy, $query_type, $display_type, $instance, $terms_type_list, $current_term, $args, $is_child_class, $is_parent_class, $is_chosen_class, $temp_level, $filter_term_field, $rel_nofollow );
					echo '</ul>';
				}
			}
		}

		/**
		 * Does nothing on this version of the plugin; overridden in Premium class
		 *
		 * @param string $taxonomy Taxonomy.
		 * @param array  $instance Current widget instance.
		 *
		 * @return void
		 */
		public function add_reset_taxonomy_link( $taxonomy, $instance ) {}
	}
}
