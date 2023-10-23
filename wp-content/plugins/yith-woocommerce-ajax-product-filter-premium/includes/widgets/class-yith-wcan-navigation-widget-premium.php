<?php
/**
 * Main class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Widgets
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Navigation_Widget_Premium' ) ) {
	/**
	 * YITH WooCommerce Ajax Navigation Widget
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Navigation_Widget_Premium extends YITH_WCAN_Navigation_Widget {

		/**
		 * Construct method
		 *
		 * @return void
		 */
		public function __construct() {
			add_filter( 'yith_wcan_get_terms_list', array( $this, 'reorder_terms_list' ), 10, 3 );
			parent::__construct();

			/**
			 * Deprecated Filters Map
			 *
			 * @param mixed|array $deprecated_filters_map Array of deprecated filters
			 * @since 3.11.7
			 * @ return void
			 */
			$deprecated_filters_map = array(
				"{$this->id}-a_style" => array(
					'since'  => '3.11.7',
					'use'    => "{$this->id}_a_style",
					'params' => 2,
				),
			);

			yith_wcan_deprecated_filter( $deprecated_filters_map );
		}

		/**
		 * Outputs the form to configure widget
		 *
		 * @param array $instance Current instance.
		 *
		 * @return void
		 */
		public function form( $instance ) {
			/* === Add Premium Widget Types === */
			add_filter( 'yith_wcan_widget_types', array( $this, 'premium_widget_types' ) );
			add_filter( 'yith-wcan-attribute-list-class', array( $this, 'set_attribute_style' ) );

			parent::form( $instance );

			$defaults = array(
				'type'             => 'list',
				'style'            => 'square',
				'show_count'       => 0,
				'dropdown'         => 0,
				'dropdown_type'    => 'open',
				'tags_list'        => array(),
				'tags_list_query'  => 'exclude',
				'see_all_tax_text' => '',
			);

			$instance = wp_parse_args( (array) $instance, $defaults );
			$terms    = yith_wcan_wp_get_terms(
				array(
					'taxonomy'   => 'product_tag',
					'hide_empty' => false,
				)
			);
			?>

			<p class="yit-wcan-see-all-taxonomies-text">
				<label for="<?php echo esc_attr( $this->get_field_id( 'see_all_tax_text' ) ); ?>">
					<?php esc_html_e( '"See all categories/tags" link text', 'yith-woocommerce-ajax-navigation' ); ?>:
					<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'see_all_tax_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'see_all_tax_text' ) ); ?>" value="<?php echo esc_attr( $instance['see_all_tax_text'] ); ?>" class="yith-wcan-see-all-text-field widefat"/>
					<span class="yith-wcan-see-all-taxonomies-text-description">
						<?php
						printf(
							'%s <a href="%s" target="_blank">%s</a> <span class="yith-wcan-see-all-taxonomies-text-default"> %s: <strong>%s</strong><br/>%s: <strong>%s</strong></span>',
							esc_html__( 'Leave it empty to use the default text available', 'yith-woocommerce-ajax-navigation' ),
							esc_url(
								add_query_arg(
									array(
										'page' => 'yith_wcan_panel',
										'tab'  => 'general',
									),
									admin_url( 'admin.php' )
								)
							),
							esc_html__( 'here', 'yith-woocommerce-ajax-navigation' ),
							esc_html__( 'current categories text', 'yith-woocommerce-ajax-navigation' ),
							esc_html( yith_wcan_get_option( 'yith_wcan_enable_see_all_categories_link_text' ) ),
							esc_html__( 'current tags text', 'yith-woocommerce-ajax-navigation' ),
							esc_html( yith_wcan_get_option( 'yith_wcan_enable_see_all_tags_link_text' ) )
						);
						?>
					</span>
				</label>
			</p>

			<div class="yit-wcan-widget-tag-list <?php echo esc_attr( $instance['type'] ); ?>">
				<?php

				if ( is_wp_error( $terms ) || empty( $terms ) ) {
					esc_html_e( 'No tags found.', 'yith-woocommerce-ajax-navigation' );
				} else {
					?>
					<strong><?php echo esc_html_x( 'Tag List', 'Admin: Section title', 'yith-woocommerce-ajax-navigation' ); ?></strong>
					<select class="yith_wcan_tags_query_type widefat" id="<?php echo esc_attr( $this->get_field_id( 'tags_list_query' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tags_list_query' ) ); ?>">
						<option value="include" <?php selected( 'include', $instance['tags_list_query'] ); ?>> <?php esc_html_e( 'Show Selected', 'yith-woocommerce-ajax-navigation' ); ?> </option>
						<option value="exclude" <?php selected( 'exclude', $instance['tags_list_query'] ); ?>>  <?php esc_html_e( 'Hide Selected', 'yith-woocommerce-ajax-navigation' ); ?> </option>
					</select>
					<div class="yith-wcan-select-option">
						<a href="#" class="select-all">
							<?php esc_html_e( 'Select all', 'yith-woocommerce-ajax-navigation' ); ?>
						</a>
						<a href="#" class="unselect-all">
							<?php esc_html_e( 'Unselect all', 'yith-woocommerce-ajax-navigation' ); ?>
						</a>
						<small class="yith-wcan-admin-note"><?php echo '* ' . esc_html_x( 'Note: tags with no products assigned will not be showed in the front end', 'Admin: user note', 'yith-woocommerce-ajax-navigation' ); ?></small>
					</div>
					<div class="yith_wcan_select_tag_wrapper">
						<table class="yith_wcan_select_tag">
							<thead>
							<tr>
								<td><?php esc_html_e( 'Tag name', 'yith-woocommerce-ajax-navigation' ); ?></td>
								<td><?php esc_html_e( 'Count', 'yith-woocommerce-ajax-navigation' ); ?>
									<small class="yith-wcan-admin-note-star">(*)</small>
								</td>
							</tr>
							</thead>
							<tbody>
							<?php foreach ( $terms as $term ) : ?>
								<tr>
									<td class="term_name">
										<label for="<?php echo esc_attr( $this->get_field_id( 'tags_list' ) ); ?>_<?php echo esc_attr( $term->term_id ); ?>">
											<input
												type="checkbox"
												value="<?php echo esc_attr( $term->slug ); ?>"
												name="<?php echo esc_attr( $this->get_field_name( 'tags_list' ) ); ?>[<?php echo esc_attr( $term->term_id ); ?>]"
												class="<?php echo esc_attr( $this->get_field_name( 'tags_list' ) ); ?> yith_wcan_tag_list_checkbox"
												id="<?php echo esc_attr( $this->get_field_id( 'tags_list' ) ); ?>_<?php echo esc_attr( $term->term_id ); ?>"
												<?php checked( is_array( $instance['tags_list'] ) && array_key_exists( $term->term_id, $instance['tags_list'] ) ); ?>
											/>
											<?php echo wp_kses_post( $term->name ); ?>
										</label>
									</td>
									<td class="term_count">
										<?php echo (int) $term->count; ?>
									</td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php } ?>
			</div>

			<p id="yit-wcan-style" class="yit-wcan-style-<?php echo esc_attr( $instance['type'] ); ?>">
				<label for="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>">
					<strong><?php echo esc_html_x( 'Color Style:', 'Select if you want to show round color box or square color box', 'yith-woocommerce-ajax-navigation' ); ?></strong>
				</label>
				<select class="yith_wcan_style widefat" id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>">
					<option value="square" <?php selected( 'square', $instance['style'] ); ?>><?php esc_html_e( 'Square', 'yith-woocommerce-ajax-navigation' ); ?></option>
					<option value="round" <?php selected( 'round', $instance['style'] ); ?>><?php esc_html_e( 'Round', 'yith-woocommerce-ajax-navigation' ); ?></option>
				</select>
			</p>

			<p id="yit-wcan-show-count" class="yit-wcan-show-count-<?php echo esc_attr( $instance['type'] ); ?>">
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>"><?php esc_html_e( 'Hide product count', 'yith-woocommerce-ajax-navigation' ); ?>:
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_count' ) ); ?>" value="1" <?php checked( $instance['show_count'], 1, true ); ?> class="widefat"/>
				</label>
			</p>

			<p id="yit-wcan-dropdown-<?php echo esc_attr( $instance['type'] ); ?>" class="yith-wcan-dropdown">
				<label for="<?php echo esc_attr( $this->get_field_id( 'dropdown' ) ); ?>"><?php esc_html_e( 'Show widget dropdown', 'yith-woocommerce-ajax-navigation' ); ?>:
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'dropdown' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'dropdown' ) ); ?>" value="1" <?php checked( $instance['dropdown'], 1, true ); ?> class="yith-wcan-dropdown-check widefat"/>
				</label>
			</p>

			<p id="yit-wcan-dropdown-type" class="yit-wcan-dropdown-type-<?php echo esc_attr( $instance['type'] ); ?>" style="display: <?php echo ! empty( $instance['dropdown'] ) ? 'block' : 'none'; ?>;">
				<label for="<?php echo esc_attr( $this->get_field_id( 'dropdown_type' ) ); ?>"><strong><?php echo esc_html_x( 'Dropdown style:', 'Select this if you want to show the widget as open or closed', 'yith-woocommerce-ajax-navigation' ); ?></strong></label>
				<select class="yith-wcan-dropdown-type widefat" id="<?php echo esc_attr( $this->get_field_id( 'dropdown_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'dropdown_type' ) ); ?>">
					<option value="open" <?php selected( 'open', $instance['dropdown_type'] ); ?>><?php esc_html_e( 'Opened', 'yith-woocommerce-ajax-navigation' ); ?></option>
					<option value="close" <?php selected( 'close', $instance['dropdown_type'] ); ?>><?php esc_html_e( 'Closed', 'yith-woocommerce-ajax-navigation' ); ?></option>
				</select>
			</p>

			<script type="text/javascript">
				jQuery(document).ready(function () {
					jQuery(document).on('change', '.yith-wcan-dropdown-check', function () {
						jQuery.select_dropdown(jQuery(this));
					});
				});
			</script>
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

			$instance = parent::update( $new_instance, $old_instance );

			$instance['style']            = $new_instance['style'];
			$instance['show_count']       = ( isset( $new_instance['show_count'] ) && yith_plugin_fw_is_true( $new_instance['show_count'] ) ) ? 1 : 0;
			$instance['dropdown']         = ( isset( $new_instance['dropdown'] ) && yith_plugin_fw_is_true( $new_instance['dropdown'] ) ) ? 1 : 0;
			$instance['dropdown_type']    = $new_instance['dropdown_type'];
			$instance['tags_list']        = ! empty( $new_instance['tags_list'] ) ? $new_instance['tags_list'] : array();
			$instance['tags_list_query']  = isset( $new_instance['tags_list_query'] ) ? $new_instance['tags_list_query'] : 'include';
			$instance['see_all_tax_text'] = $new_instance['see_all_tax_text'];

			return $instance;
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
			add_filter( "{$this->id}_li_style", array( $this, 'color_and_label_style' ), 10, 2 );
			add_filter( "{$this->id}-show_product_count", array( $this, 'show_product_count' ), 10, 2 );
			add_filter( 'yith_widget_title_ajax_navigation', array( $this, 'widget_title' ), 10, 3 );
			add_action( 'yith_wcan_widget_display_multicolor', array( $this, 'show_premium_widget' ), 10, 6 );
			add_action( 'yith_wcan_widget_display_categories', array( $this, 'show_premium_widget' ), 10, 6 );

			/* === Tag & Brand Filter === */
			add_filter( 'yith_wcan_get_terms_params', array( $this, 'get_terms_params' ), 10, 3 );
			add_filter( 'yith_wcan_display_type_list', array( $this, 'add_display_type_case' ) );
			add_filter( 'yith_wcan_list_type_query_arg', array( $this, 'type_query_args' ), 10, 3 );
			add_filter( 'yith_wcan_term_param_uri', array( $this, 'term_param_uri' ), 10, 3 );
			add_filter( 'yith_wcan_is_attribute_check', array( $this, 'filter_by_attributes_check' ), 10, 2 );
			add_filter( 'yith_wcan_tags_type_current_widget_check', array( $this, 'filter_current_widget' ), 10, 5 );
			add_filter( 'yith_wcan_categories_type_current_widget_check', array( $this, 'filter_current_widget' ), 10, 5 );
			add_filter( 'yith_wcan_brands_type_current_widget_check', array( $this, 'filter_current_widget' ), 10, 5 );
			add_filter( 'yith_wcan_tags_filter_operator', array( $this, 'tag_brands_filter_operator' ), 10, 3 );
			add_filter( 'yith_wcan_categories_filter_operator', array( $this, 'tag_brands_filter_operator' ), 10, 3 );
			add_filter( 'yith_wcan_brands_filter_operator', array( $this, 'tag_brands_filter_operator' ), 10, 3 );
			add_filter( 'yith_wcan_tags_type_query_arg', array( $this, 'tag_brands_query_arg' ), 10, 2 );
			add_filter( 'yith_wcan_categories_type_query_arg', array( $this, 'tag_brands_query_arg' ), 10, 2 );
			add_filter( 'yith_wcan_brands_type_query_arg', array( $this, 'tag_brands_query_arg' ), 10, 2 );

			if ( ! empty( $instance['type'] ) && 'tags' === $instance['type'] ) {
				$query_option = isset( $instance['tags_list_query'] ) ? $instance['tags_list_query'] : 'include';
				add_filter( "yith_wcan_{$query_option}_terms", array( $this, 'include_exclude_terms' ), 10, 2 );
			}

			if ( function_exists( 'yit_decode_title' ) ) {
				remove_filter( 'widget_title', 'yit_decode_title' );
			}

			parent::widget( $args, $instance );
		}

		/**
		 * Switch param to false when widget is of type tags or brands
		 *
		 * @param bool  $check Whether current type allows for check.
		 * @param array $instance Widget instance (contains current type).
		 *
		 * @return bool Filtered value
		 */
		public function filter_by_attributes_check( $check, $instance ) {
			if ( 'tags' === $instance['type'] || 'brands' === $instance['type'] ) {
				$check = false;
			}

			return $check;
		}

		/**
		 * Prints "See all terms" link
		 *
		 * @param string $taxonomy Current taxonomy.
		 * @param array  $instance Widget instance.
		 *
		 * @return void
		 */
		public function add_reset_taxonomy_link( $taxonomy, $instance ) {
			$rel_nofollow                  = yith_wcan_add_rel_nofollow_to_url( true );
			$in_array_function             = apply_filters( 'yith_wcan_in_array_ignore_case', false ) ? 'yit_in_array_ignore_case' : 'in_array';
			$allowed_taxonomies_reset_link = apply_filters( 'yit_wcan_allowed_taxonomies_reset_link', array( 'product_cat', 'product_tag' ) );

			if ( ( yit_is_filtered_uri() || is_product_category() || is_product_taxonomy() || is_product_tag() ) && $in_array_function( $taxonomy, $allowed_taxonomies_reset_link ) ) {

				if ( 'product_cat' === $taxonomy ) {
					$taxonomy = 'categories';
				} elseif ( 'product_tag' === $taxonomy ) {
					$taxonomy = 'tags';
				}

				$show = 'yes' === yith_wcan_get_option( "yith_wcan_enable_see_all_{$taxonomy}_link", 'no' ) ? true : false;
				$show = apply_filters( "yith_wcan_enable_see_all_{$taxonomy}_link", $show );

				if ( $show ) {
					$reset_categories_link = apply_filters( "yith_wcan_reset_{$taxonomy}_link", esc_url( get_the_permalink( wc_get_page_id( 'shop' ) ) ) );
					$default_value_option  = sprintf( '%s %s', __( 'See all', 'yith-woocommerce-ajax-navigation' ), $taxonomy );
					$see_all_text          = empty( $instance['see_all_tax_text'] ) ? yith_wcan_get_option( "yith_wcan_enable_see_all_{$taxonomy}_link_text", $default_value_option ) : $instance['see_all_tax_text'];

					printf(
						'<span id="yith-wcan-reset-all-%1$s" class="%2$s"><a class="yith-wcan-reset-%1$s-link" href="%3$s" %5$s>%4$s</a></span>',
						esc_attr( $taxonomy ),
						esc_attr( apply_filters( "yith_wcan_show_all_{$taxonomy}_classes", "yith-wcan-show-all-{$taxonomy}" ) ),
						esc_url( $reset_categories_link ),
						wp_kses_post( $see_all_text ),
						$rel_nofollow // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					);
				}
			}
		}

		/**
		 * Adds custom style to items of color swatches filters
		 *
		 * @param string $li_style Original style attribute.
		 * @param array  $instance Widget instance.
		 *
		 * @return string Filtered style attribute.
		 */
		public function color_and_label_style( $li_style, $instance ) {

			if ( ! empty( $instance['style'] ) && 'round' === $instance['style'] ) {
				$li_style .= 'border-radius: 50%;';
			}

			return $li_style;
		}

		/**
		 * Whether to show term count or not
		 *
		 * @param bool  $show Whether to show count or not.
		 * @param array $instance Widget instance.
		 *
		 * @return bool Whether to show count or not.
		 */
		public function show_product_count( $show, $instance ) {
			return empty( $instance['show_count'] ) ? true : false;
		}

		/**
		 * Returns formatted widget title
		 *
		 * @param string $title Widget title.
		 * @param array  $instance Current instance.
		 * @param string $id_base Widget id.
		 *
		 * @return string Formatted title.
		 */
		public function widget_title( $title, $instance, $id_base ) {
			$span_class                = apply_filters( 'yith_wcan_dropdown_class', 'widget-dropdown' );
			$instance['dropdown_type'] = isset( $instance['dropdown_type'] ) ? $instance['dropdown_type'] : 'open';
			$dropdown_type             = apply_filters( 'yith_wcan_dropdown_type', $instance['dropdown_type'], $instance );
			$span_html                 = sprintf( '<span class="%s" data-toggle="%s"></span>', $span_class, ! empty( $dropdown_type ) ? $dropdown_type : 'open' );
			$title                     = ! empty( $instance['dropdown'] ) ? $title . ' ' . $span_html : $title;

			return $title;
		}

		/**
		 * Adds types specific to premium version
		 *
		 * @param array $types Array of available types.
		 *
		 * @return array Array of filtered types.
		 */
		public function premium_widget_types( $types ) {
			$types['categories'] = __( 'Categories', 'yith-woocommerce-ajax-navigation' );
			$types['multicolor'] = __( 'BiColor', 'yith-woocommerce-ajax-navigation' );
			$types['tags']       = __( 'Tag', 'yith-woocommerce-ajax-navigation' );

			if ( yith_wcan_brands_enabled() ) {
				$types['brands'] = __( 'Brand', 'yith-woocommerce-ajax-navigation' );
			}

			return $types;
		}

		/**
		 * Prints templates for premium style of the widget
		 *
		 * @param array  $args Widget general args.
		 * @param array  $instance Widget specific instance.
		 * @param string $display_type One among list/color/label/select/bicolor/caterogy/tag.
		 * @param array  $terms List of terms to print.
		 * @param string $taxonomy Taxonomy for the list of terms.
		 * @param string $filter_term_field Field of the term object used to filter selection.
		 */
		public function show_premium_widget( $args, $instance, $display_type, $terms, $taxonomy, $filter_term_field = 'slug' ) {
			global $wc_product_attributes, $wp_query;
			$rel_nofollow       = yith_wcan_add_rel_nofollow_to_url( true );
			$_chosen_attributes = YITH_WCAN()->get_layered_nav_chosen_attributes();
			$in_array_function  = apply_filters( 'yith_wcan_in_array_ignore_case', false ) ? 'yit_in_array_ignore_case' : 'in_array';

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

			$_attributes_array = yit_wcan_get_product_taxonomy();

			if ( apply_filters( 'yith_wcan_is_search', is_search() ) ) {
				return;
			}

			if ( apply_filters( 'yith_wcan_show_widget', ! is_post_type_archive( 'product' ) && ! is_tax( $_attributes_array ), $instance ) ) {
				return;
			}

			$queried_object  = $wp_query instanceof WP_Query ? $wp_query->get_queried_object() : false;
			$current_term    = $_attributes_array && is_tax( $_attributes_array ) && ! empty( $queried_object ) ? $queried_object->term_id : '';
			$query_type      = isset( $instance['query_type'] ) ? $instance['query_type'] : 'and';
			$display_type    = isset( $instance['type'] ) ? $instance['type'] : 'list';
			$is_child_class  = 'yit-wcan-child-terms';
			$is_parent_class = 'yit-wcan-parent-terms';
			$is_chosen_class = 'chosen';
			$terms_type_list = ( isset( $instance['display'] ) && 'categories' === $display_type ) ? $instance['display'] : 'all';

			$instance['attribute'] = empty( $instance['attribute'] ) ? '' : $instance['attribute'];

			if ( 'multicolor' === $display_type ) {
				// List display.
				echo '<ul class="yith-wcan-color yith-wcan yith-wcan-group ' . esc_attr( $instance['extra_class'] ) . '">';

				foreach ( $terms as $term ) {

					// Get count based on current view - uses transients.
					$_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );

					$option_is_set = ( isset( $_chosen_attributes[ $taxonomy ] ) && $in_array_function( $term->$filter_term_field, $_chosen_attributes[ $taxonomy ]['terms'] ) );

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
						} elseif ( apply_filters( 'yith_wcan_skip_no_product_count_bicolor', false ) ) {
							continue 1;
						}
					}

					$current_filter = $this->get_current_filters( $term, $instance );

					list( $link, $class ) = $this->get_link_attributes( $term, $taxonomy, $current_filter, $instance );

					$term_id = yit_wcan_localize_terms( $term->term_id, $taxonomy );

					$colors = array();

					if ( ! empty( $instance['multicolor'][ $term_id ] ) && is_array( $instance['multicolor'][ $term_id ] ) && ! empty( $instance['multicolor'][ $term_id ][0] ) ) {
						$colors = $instance['multicolor'][ $term_id ];
					} elseif ( apply_filters( 'yith_wcan_ywccl_support', function_exists( 'ywccl_get_term_meta' ) ) && ! empty( $wc_product_attributes[ $term->taxonomy ]->attribute_type ) && 'colorpicker' === $wc_product_attributes[ $term->taxonomy ]->attribute_type ) {
						$colors = ywccl_get_term_meta( $term->term_id, $term->taxonomy . '_yith_wccl_value' );

						if ( ! empty( $colors ) ) {
							$colors = explode( ',', $colors );
						}
					}

					if ( $colors ) {

						$a_style   = '';
						$is_single = false;

						if ( empty( $colors[1] ) ) {
							$a_style   = apply_filters( "{$this->id}_a_style", 'background-color:' . $colors[0] . ';', $instance );
							$is_single = true;
							$a_class   = 'singlecolor';
						} else {
							$color_1_style = 'border-color: ' . $colors[0] . ' transparent;';
							$color_2_style = 'border-color: ' . $colors[1] . ' transparent;';
							$a_class       = 'multicolor ' . $instance['style'];
						}

						echo '<li ' . $class . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						echo ( $count > 0 || $option_is_set ) ? '<a ' . $rel_nofollow . ' class="' . esc_attr( $a_class ) . '" style="' . esc_attr( $a_style ) . '" href="' . esc_url( $link ) . '" title="' . esc_attr( $term->name ) . '" >' : '<span style="background-color:' . esc_attr( $instance['multicolor'][ $term_id ][0] ) . ';" >'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						if ( ! $is_single ) {
							echo '<span class="multicolor color-1 ' . esc_attr( $instance['style'] ) . '" style="' . esc_attr( $color_1_style ) . '"></span>';
							echo '<span class="multicolor color-2 ' . esc_attr( $instance['style'] ) . '" style="' . esc_attr( $color_2_style ) . '"></span>';
						}

						echo wp_kses_post( $term->name );

						echo ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';
					} else {
						$this->found = apply_filters( 'yith_wcan_found_with_no_colors_set', $this->found, $instance, $term, $taxonomy );
					}
				}

				echo '</ul>';
			} elseif ( 'categories' === $display_type ) {
				$tree = array();

				if ( 'hierarchical' === $instance['display'] ) {
					$ancestors = yith_wcan_wp_get_terms(
						array(
							'taxonomy'     => 'product_cat',
							'parent'       => 0,
							'hierarchical' => true,
							'hide_empty'   => false,
						)
					);

					$ancestors = apply_filters( 'yith_wcan_categories_ancestors', $ancestors, $instance );

					if ( ! empty( $ancestors ) && ! is_wp_error( $ancestors ) ) {
						if ( 'product' === yith_wcan_get_option( 'yith_wcan_ajax_shop_terms_order', 'alphabetical' ) ) {
							usort( $ancestors, 'yit_terms_sort' );
						} elseif ( 'alphabetical' === yith_wcan_get_option( 'yith_wcan_ajax_shop_terms_order', 'alphabetical' ) ) {
							usort( $ancestors, 'yit_alphabetical_terms_sort' );
						}

						foreach ( $ancestors as $ancestor ) {
							$tree[ $ancestor->term_id ] = yit_reorder_hierachical_categories( $ancestor->term_id );
						}
					}
				} else {
					foreach ( $terms as $term ) {
						$tree[ $term->term_id ] = array();
					}
				}

				$categories_filter_operator = 'and' === $query_type ? '+' : ',';

				$this->add_reset_taxonomy_link( $taxonomy, $instance );

				// List display.
				echo '<ul class="yith-wcan-list yith-wcan categories ' . esc_attr( $instance['extra_class'] ) . '">';

				$this->get_categories_list_html( $args, $tree, $taxonomy, $display_type, $query_type, $instance, $terms_type_list, $current_term, $categories_filter_operator, $is_chosen_class, $is_parent_class, $is_child_class, 0, $rel_nofollow );

				echo '</ul>';
			}
		}

		/**
		 * Prints list of categories for current filter
		 *
		 * @param array  $args Widget general args.
		 * @param array  $terms List of terms to print.
		 * @param string $taxonomy Taxonomy for the list of terms.
		 * @param string $display_type One among list/color/label/select.
		 * @param string $query_type One among AND/OR.
		 * @param array  $instance Widget instance.
		 * @param string $terms_type_list Hierarchical status.
		 * @param string $current_term Current term.
		 * @param string $categories_filter_operator Field of the term object used to filter selection.
		 * @param string $is_chosen_class Class for chosen items.
		 * @param string $is_parent_class Class for parent items.
		 * @param string $is_child_class Class for child items.
		 * @param int    $level Current nesting level.
		 * @param string $rel_nofollow Attributes to be used for links of the list.
		 *
		 * @return void
		 */
		public function get_categories_list_html( $args, $terms, $taxonomy, $display_type, $query_type, $instance, $terms_type_list, $current_term, $categories_filter_operator, $is_chosen_class, $is_parent_class, $is_child_class, $level = 0, $rel_nofollow = '' ) {
			global $wp_query;
			$queried_object     = $wp_query instanceof WP_Query ? $wp_query->get_queried_object() : false;
			$_chosen_attributes = YITH_WCAN()->get_layered_nav_chosen_attributes();
			$in_array_function  = apply_filters( 'yith_wcan_in_array_ignore_case', false ) ? 'yit_in_array_ignore_case' : 'in_array';

			foreach ( $terms as $parent_id => $term_ids ) {
				$term                   = get_term_by( 'id', $parent_id, 'product_cat' );
				$filter_is_hierarchical = 'hierarchical' === $instance['display'];
				$_products_in_term      = get_objects_in_term( $term->term_id, $taxonomy );
				$option_is_set          = ( isset( $_chosen_attributes[ $taxonomy ] ) && $in_array_function( $term->term_id, $_chosen_attributes[ $taxonomy ]['terms'] ) );
				$li_printed             = false;
				$skip                   = false;

				$term_param = apply_filters( 'yith_wcan_term_param_uri', $term->slug, $display_type, $term );

				if ( 'and' === $query_type ) {
					// If this is an AND query, only show options with count > 0.
					$product_selection = apply_filters( 'yith_wcan_products_filter_category_and', array_intersect( $_products_in_term, YITH_WCAN()->frontend->layered_nav_product_ids ), $_products_in_term, YITH_WCAN()->frontend->layered_nav_product_ids );
					$count             = count( $product_selection );

					if ( $count > 0 && $current_term !== $term_param ) {
						$this->found = true;
					}

					if ( apply_filters( 'yith_wcan_skip_no_products_in_category', ( ! yit_term_has_child( $term, $taxonomy ) ) && ! $count && ! $option_is_set, $terms, $term ) ) {
						continue;
					}
				} else {
					// If this is an OR query, show all options so search can be expanded
					// TODO: Temporary Fix.
					$to_exclude = YITH_WCAN_Cache_Helper::get( 'exclude_from_catalog_product_ids' );

					if ( false === $to_exclude ) {
						$unfiltered_args = array(
							'post_type'              => 'product',
							'numberposts'            => - 1,
							'post_status'            => 'publish',
							'fields'                 => 'ids',
							'no_found_rows'          => true,
							'update_post_meta_cache' => false,
							'update_post_term_cache' => false,
							'pagename'               => '',
							'wc_query'               => 'get_products_in_view', // Only for WC <= 2.6.x.
							'suppress_filters'       => true,
						);

						$wc_get_product_visibility_term_ids = function_exists( 'wc_get_product_visibility_term_ids' ) ? wc_get_product_visibility_term_ids() : array();

						if ( ! empty( $wc_get_product_visibility_term_ids['exclude-from-catalog'] ) ) {
							$unfiltered_args['tax_query'][] = array(
								'taxonomy' => 'product_visibility',
								'terms'    => $wc_get_product_visibility_term_ids['exclude-from-catalog'],
								'operator' => 'IN',
							);
						}

						$to_exclude = get_posts( $unfiltered_args );
						YITH_WCAN_Cache_Helper::set( 'exclude_from_catalog_product_ids', $to_exclude );
					}

					$product_selection = apply_filters( 'yith_wcan_products_filter_category_or', array_intersect( $_products_in_term, array_diff( $_products_in_term, $to_exclude ) ), $_products_in_term, $to_exclude );

					$count = count( $product_selection );

					$this->found = true;
				}

				$current_filter = $this->get_current_filters( $term, $instance );

				list( $link, $class ) = $this->get_link_attributes( $term, $taxonomy, $current_filter, $instance );

				$exclude = apply_filters( 'yith_wcan_exclude_category_terms', array(), $instance );

				if ( ! empty( $exclude ) && $in_array_function( $term->term_id, $exclude ) ) {
					$skip = true;
				}

				if ( ( is_product_category( $term->term_id ) && $queried_object->term_id === $term->term_id ) || ( is_post_type_archive( 'product' ) && isset( $_GET['source_id'] ) && $term->term_id === (int) $_GET['source_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$skip = apply_filters( 'yith_wcan_skip_current_category', 'yes' === yith_wcan_get_option( 'yith_wcan_show_current_categories_link', 'no' ) ? false : true );
				}

				if ( ! apply_filters( 'yith_wcan_skip_current_categories', $skip, $taxonomy, $terms, $count ) ) {
					$yith_wcan_skip_no_products_in_category = apply_filters( 'yith_wcan_skip_no_products_in_category', $filter_is_hierarchical, $terms, $term );
					$li_printed                             = $count > 0 || $option_is_set || 'or' === $query_type || ! $yith_wcan_skip_no_products_in_category;
					$term_name                              = apply_filters( 'yith_wcan_term_name_to_show', $term->name, $term );

					if ( $li_printed ) {
						echo '<li ' . apply_filters( 'yith_wcan_categories_item_class', $class, $term ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}

					if ( $count > 0 || $option_is_set || 'or' === $query_type ) {
						printf( '<a %s href="%s">%s</a>', $rel_nofollow, esc_url( $link ), wp_kses_post( $term_name ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} elseif ( ! $yith_wcan_skip_no_products_in_category ) {
						printf( '<span>%s</span>', wp_kses_post( $term_name ) );
					}

					$hide_count = ! empty( $instance['show_count'] ) && $instance['show_count'];

					if ( apply_filters( 'yith_wcan_force_show_count_in_category', ! empty( $count ) && ! $hide_count ) && apply_filters( "{$this->id}_show_product_count", true, $instance ) ) {
						echo ' <small class="count">' . esc_html( $count ) . '</small><div class="clear"></div>';
					}
				}

				if ( ! empty( $term_ids ) && is_array( $term_ids ) ) {
					echo '<ul class="yith-child-terms level-' . esc_attr( $level ) . '">';
					$temp_level = $level;
					$temp_level ++;
					$this->get_categories_list_html( $args, $term_ids, $taxonomy, $display_type, $query_type, $instance, $terms_type_list, $current_term, $categories_filter_operator, $is_chosen_class, $is_parent_class, $is_child_class, $temp_level, $rel_nofollow );
					echo '</ul>';
				}

				if ( $li_printed ) {
					do_action( 'yith_wcan_before_closing_list_item', $term );
					echo '</li>';
				}
			}
		}

		/**
		 * Returns taxonomy specific params for the widget
		 *
		 * @param array  $param Widget params.
		 * @param array  $instance Widget instance.
		 * @param string $type Filter type.
		 *
		 * @return array Filtered array of params
		 */
		public function get_terms_params( $param, $instance, $type ) {
			if ( empty( $instance['type'] ) ) {
				$instance['type'] = 'list';
			}

			if ( 'tags' === $instance['type'] ) {
				if ( 'taxonomy_name' === $type ) {
					$param = 'product_tag';
				}
			} elseif ( 'brands' === $instance['type'] && yith_wcan_brands_enabled() ) {
				if ( 'taxonomy_name' === $type ) {
					$param = YITH_WCBR::$brands_taxonomy;
				}
			} elseif ( 'categories' === $instance['type'] && 'taxonomy_name' === $type ) {
				$param = 'product_cat';
			}

			return $param;
		}

		/**
		 * Additional display cases
		 *
		 * @param array $args Display cases.
		 *
		 * @return array array of filtered arguments
		 */
		public function add_display_type_case( $args ) {
			$args[] = 'tags';
			$args[] = 'brands';

			return $args;
		}

		/**
		 * Filter query args, depending on current type
		 *
		 * @param string  $arg $query taxonomy.
		 * @param string  $type Filter type.
		 * @param WP_Term $term Not in use.
		 *
		 * @return string Filtered query args
		 */
		public function type_query_args( $arg, $type, $term = null ) {
			if ( 'tags' === $type ) {
				$arg = 'product_tag';
			} elseif ( 'brands' === $type && yith_wcan_brands_enabled() ) {
				$arg = YITH_WCBR::$brands_taxonomy;
			}

			return $arg;
		}

		/**
		 * Filter term's parameter to use in filter url
		 *
		 * @param string  $term_param Default term param.
		 * @param string  $type Type of filter.
		 * @param WP_Term $term Current term.
		 *
		 * @return string Filtered term param.
		 */
		public function term_param_uri( $term_param, $type, $term ) {
			if ( 'tags' === $type || 'brands' === $type ) {
				$term_param = $term->slug;
			}

			return $term_param;
		}

		/**
		 * Whether to show checkbox for current filter or not
		 *
		 * @param bool   $check_for_current_widget Whether to show checkbox.
		 * @param array  $current_filters Filters currently applied.
		 * @param string $type Filter type.
		 * @param string $term_param Term's parameter to use in filter url.
		 * @param string $query_type Query type.
		 *
		 * @return bool Whether to show checkbox or not.
		 */
		public function filter_current_widget( $check_for_current_widget, $current_filters, $type, $term_param, $query_type ) {
			$current_filters   = array();
			$taxonomy          = '';
			$brands_taxonomy   = yith_wcan_brands_enabled() ? YITH_WCBR::$brands_taxonomy : '';
			$in_array_function = apply_filters( 'yith_wcan_in_array_ignore_case', false ) ? 'yit_in_array_ignore_case' : 'in_array';
			$operator          = 'and' === $query_type ? '+' : ',';

			if ( 'categories' === $type && isset( $_GET['product_cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$taxonomy = 'product_cat';
			} elseif ( 'tags' === $type && isset( $_GET['product_tag'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$taxonomy = 'product_tag';
			} elseif ( 'brands' === $type && isset( $_GET[ $brands_taxonomy ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$taxonomy = $brands_taxonomy;
			}

			if ( isset( $_GET[ $taxonomy ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$current_filters = explode( $operator, sanitize_text_field( wp_unslash( $_GET[ $taxonomy ] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
				$current_filters = array_map( 'urlencode', $current_filters );

				if ( $in_array_function( $term_param, $current_filters ) ) {
					$check_for_current_widget = true;
				}
			}

			return $check_for_current_widget;
		}

		/**
		 * Filters operator to use to merge together filters
		 *
		 * @param string $operator Original operator.
		 * @param string $display_type Filter type.
		 * @param string $query_type Query type.
		 *
		 * @return string Filtered operator.
		 */
		public function tag_brands_filter_operator( $operator, $display_type, $query_type ) {
			if ( 'categories' === $display_type || 'tags' === $display_type || 'brands' === $display_type ) {
				$operator = 'and' === $query_type ? '+' : ',';
			}

			return $operator;
		}

		/**
		 * Filters operator to use to merge together filters
		 *
		 * @param string $query_arg Original query arg.
		 * @param string $display_type Filter type.
		 *
		 * @return string Filtered query arg.
		 */
		public function tag_brands_query_arg( $query_arg, $display_type ) {
			if ( 'categories' === $display_type ) {
				$query_arg = 'product_cat';
			} elseif ( 'tags' === $display_type ) {
				$query_arg = 'product_tag';
			} elseif ( 'brands' === $display_type ) {
				$query_arg = $this->brand_taxonomy;
			}

			return $query_arg;
		}

		/**
		 * Filters terms to include/exclude
		 *
		 * @param array $ids Array of terms to include/exclude.
		 * @param array $instance Widget instance.
		 *
		 * @return array Array of filtered terms.
		 */
		public function include_exclude_terms( $ids, $instance ) {
			$option_ids = empty( $instance['tags_list'] ) ? array() : $instance['tags_list'];

			if ( empty( $option_ids ) ) {
				if ( 'yith_wcan_include_terms' === current_filter() ) {
					$option_ids = array();
				} elseif ( 'yith_wcan_exclude_terms' === current_filter() ) {
					$option_ids = array();
				}
			} else {
				$option_ids = is_array( $option_ids ) ? array_keys( $option_ids ) : array();
			}

			return array_merge( $ids, $option_ids );
		}

		/**
		 * Filter tags to sort them
		 *
		 * @param array  $terms Array of terms.
		 * @param string $taxonomy Taxonomy for passed terms.
		 * @param array  $instance Widget instance.
		 *
		 * @return array Array of sorted terms.
		 */
		public function reorder_terms_list( $terms, $taxonomy, $instance ) {
			if ( 'product_tag' === $taxonomy && 'tags' === $instance['type'] ) {
				$terms = yit_reorder_terms_by_parent( $terms, $taxonomy );
			}

			return $terms;
		}
	}
}

// TODO: Remove this temporary fix and replace it with something better.
if ( ! function_exists( 'yith_wcan_exclude_from_catalog_product_ids' ) ) {
	/**
	 * Delete yith_wcan_exclude_from_catalog_product_ids transient when waving products
	 *
	 * @return void
	 */
	function yith_wcan_exclude_from_catalog_product_ids() {
		YITH_WCAN_Cache_Helper::delete( 'exclude_from_catalog_product_ids', true );
	}
}
add_action( 'save_post', 'yith_wcan_exclude_from_catalog_product_ids', 99 );
