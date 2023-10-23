<?php
/**
 * On Sale/In Stock filter
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Widgets
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Stock_On_Sale_Widget' ) ) {
	/**
	 * YITH_WCAN_Stock_On_Sale_Widget
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Stock_On_Sale_Widget extends WP_Widget {

		/**
		 * Widget id
		 *
		 * @const string
		 */
		const ID_BASE = 'yith-woo-ajax-navigation-stock-on-sale';

		/**
		 * Construct method
		 *
		 * @return void
		 */
		public function __construct() {
			$classname   = 'yith-woocommerce-ajax-product-filter yith-wcan-stock-on-sale';
			$classname  .= 'checkboxes' === yith_wcan_get_option( 'yith_wcan_ajax_shop_filter_style', 'standard' ) ? ' with-checkbox' : '';
			$widget_ops  = array(
				'classname'   => $classname,
				'description' => __( 'Display on sale and in stock WooCommerce products', 'yith-woocommerce-ajax-navigation' ),
			);
			$control_ops = array(
				'width'  => 400,
				'height' => 350,
			);

			parent::__construct( self::ID_BASE, __( 'YITH AJAX In Stock/On Sale Filters', 'yith-woocommerce-ajax-navigation' ), $widget_ops, $control_ops );

			if ( ! is_admin() ) {
				$sidebars_widgets = wp_get_sidebars_widgets();
				$regex            = '/^' . self::ID_BASE . '-\d+/';

				foreach ( $sidebars_widgets as $sidebar => $widgets ) {
					if ( is_array( $widgets ) ) {
						foreach ( $widgets as $widget ) {
							if ( preg_match( $regex, $widget ) ) {
								$this->actions();
								break;
							}
						}
					}
				}
			}
		}

		/**
		 * Performs actions required by this widget
		 *
		 * @return void
		 */
		public function actions() {
			add_action( 'woocommerce_product_query', array( $this, 'show_in_stock_products' ) );
			add_filter( 'woocommerce_layered_nav_link', array( $this, 'stock_on_sale_filter_args' ), 15 );
			add_filter( 'loop_shop_post_in', array( $this, 'show_on_sale_products' ) );

			/* === Dropdown === */
			add_filter( 'yith_widget_title_stock_onsale', array( $this, 'widget_title' ), 10, 3 );

			/* === WooCommerce Shop page Display Option Check === */
			add_filter( 'woocommerce_product_subcategories_args', array( $this, 'force_to_show_products_instead_of_cateories_in_shop_page' ), 99 );
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
			global $wp_query;
			if ( ! yith_wcan_can_be_displayed() ) {
				return;
			}

			if ( empty( $instance['onsale'] ) && empty( $instance['instock'] ) ) {
				return;
			}

			$_attributes_array = yit_wcan_get_product_taxonomy();
			$request           = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( apply_filters( 'yith_wcan_is_search', is_search() ) ) {
				return;
			}

			if ( apply_filters( 'yith_wcan_show_widget', ! is_post_type_archive( 'product' ) && ! is_tax( $_attributes_array ), $instance ) ) {
				return;
			}

			$found_onsale_products = false;
			$onsale_ids            = wc_get_product_ids_on_sale();

			$on_sale_products_in_current_selection = array_intersect( YITH_WCAN()->frontend->layered_nav_product_ids, $onsale_ids );

			if ( ! empty( $on_sale_products_in_current_selection ) ) {
				$found_onsale_products = true;
			}

			/**
			 * Extracted vars:
			 *
			 * @var $before_widget string
			 * @var $after_widget string
			 * @var $title string
			 * @var $before_title string
			 * @var $after_title string
			 */
			extract( $instance ); // phpcs:ignore WordPress.PHP.DontExtract
			extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract

			$shop_page_uri = yit_get_woocommerce_layered_nav_link();

			$filter_value_args = array(
				'queried_object' => $queried_object = $wp_query instanceof WP_Query ? $wp_query->get_queried_object() : false,
			);

			$filter_value = yit_get_filter_args( $filter_value_args );

			$shop_page_uri = add_query_arg( $filter_value, $shop_page_uri );
			$onsale_text   = apply_filters( 'yith_wcan_onsale_text', __( 'Show only "On Sale" products', 'yith-woocommerce-ajax-navigation' ) );
			$instock_text  = apply_filters( 'yith_wcan_instock_text', __( 'Show only "In Stock" products', 'yith-woocommerce-ajax-navigation' ) );

			$onsale_class  = apply_filters( 'yith_wcan_onsale_class', ! empty( $request['onsale_filter'] ) ? 'yith-wcan-onsale-button active' : 'yith-wcan-onsale-button' );
			$instock_class = apply_filters( 'yith_wcan_onsale_class', ! empty( $request['instock_filter'] ) ? 'yith-wcan-instock-button active' : 'yith-wcan-instock-button' );

			$rel_nofollow = yith_wcan_add_rel_nofollow_to_url( true );

			echo $before_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			$title = apply_filters( 'widget_title', $title );

			if ( $title ) {
				echo $before_title . apply_filters( 'yith_widget_title_stock_onsale', wp_kses_post( $title ), $instance, $this->number ) . $after_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			echo '<ul class="yith-wcan-stock-on-sale">';

			if ( $found_onsale_products && $instance['onsale'] && apply_filters( 'yith_wcms_show_onsale_filter', true ) ) {
				$filter_link = ! empty( $request['onsale_filter'] ) ? remove_query_arg( 'onsale_filter', $shop_page_uri ) : add_query_arg( array( 'onsale_filter' => 1 ), $shop_page_uri );
				$filter_link = preg_replace( '/page\/[0-9]*\//', '', $filter_link );
				echo '<li><a ' . $rel_nofollow . ' href="' . esc_url( $filter_link ) . '" class="' . esc_attr( $onsale_class ) . '">' . esc_html( $onsale_text ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			if ( $instance['instock'] && apply_filters( 'yith_wcms_show_instock_filter', true ) ) {
				$instock_link = ! empty( $request['instock_filter'] ) ? remove_query_arg( 'instock_filter', $shop_page_uri ) : add_query_arg( array( 'instock_filter' => 1 ), $shop_page_uri );
				$instock_link = preg_replace( '/page\/[0-9]*\//', '', $instock_link );
				echo '<li><a ' . $rel_nofollow . ' href="' . esc_url( $instock_link ) . '" class="' . esc_attr( $instock_class ) . '">' . esc_html( $instock_text ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			echo '</ul>';
			echo $after_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
				'title'         => _x( 'Stock/On sale', 'Product sorting', 'yith-woocommerce-ajax-navigation' ),
				'onsale'        => 1,
				'instock'       => 1,
				'dropdown'      => 0,
				'dropdown_type' => 'open',
			);

			$instance = wp_parse_args( (array) $instance, $defaults );
			?>

			<p>
				<label>
					<strong><?php esc_html_e( 'Title', 'yith-woocommerce-ajax-navigation' ); ?>:</strong><br/>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>"/>
				</label>
			</p>

			<p id="yit-wcan-onsale-<?php echo $instance['onsale'] ? 'enabled' : 'disabled'; ?>" class="yith-wcan-onsale">
				<label for="<?php echo esc_attr( $this->get_field_id( 'onsale' ) ); ?>"><?php esc_html_e( 'Show "On Sale" filter', 'yith-woocommerce-ajax-navigation' ); ?>:
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'onsale' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'onsale' ) ); ?>" value="1" <?php checked( $instance['onsale'], 1, true ); ?> class="yith-wcan-onsalen-check widefat"/>
				</label>
			</p>

			<p id="yit-wcan-instock-<?php echo $instance['instock'] ? 'enabled' : 'disabled'; ?>" class="yith-wcan-instock">
				<label for="<?php echo esc_attr( $this->get_field_id( 'instock' ) ); ?>"><?php esc_html_e( 'Show "In Stock" filter', 'yith-woocommerce-ajax-navigation' ); ?>:
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'instock' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'instock' ) ); ?>" value="1" <?php checked( $instance['instock'], 1, true ); ?> class="yith-wcan-instockn-check widefat"/>
				</label>
			</p>

			<p id="yit-wcan-dropdown" class="yith-wcan-dropdown">
				<label for="<?php echo esc_attr( $this->get_field_id( 'dropdown' ) ); ?>"><?php esc_html_e( 'Show widget dropdown', 'yith-woocommerce-ajax-navigation' ); ?>:
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'dropdown' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'dropdown' ) ); ?>" value="1" <?php checked( $instance['dropdown'], 1, true ); ?> class="yith-wcan-dropdown-check widefat"/>
				</label>
			</p>

			<p id="yit-wcan-dropdown-type" class="yit-wcan-dropdown-type-<?php echo esc_attr( $instance['dropdown_type'] ); ?>" style="display: <?php echo ! empty( $instance['dropdown'] ) ? 'block' : 'none'; ?>;">
				<label for="<?php echo esc_attr( $this->get_field_id( 'dropdown_type' ) ); ?>"><strong><?php echo esc_html_x( 'Dropdown style:', 'Select this if you want to show the widget as open or closed', 'yith-woocommerce-ajax-navigation' ); ?></strong></label>
				<select class="yith-wcan-dropdown-type widefat" id="<?php echo esc_attr( $this->get_field_id( 'dropdown_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'dropdown_type' ) ); ?>">
					<option value="open" <?php selected( 'open', $instance['dropdown_type'] ); ?>> <?php esc_html_e( 'Opened', 'yith-woocommerce-ajax-navigation' ); ?> </option>
					<option value="close" <?php selected( 'close', $instance['dropdown_type'] ); ?>>  <?php esc_html_e( 'Closed', 'yith-woocommerce-ajax-navigation' ); ?> </option>
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
		 * Append correct parameters to layered nav link
		 *
		 * @param string $link Layered nav link.
		 * @return string Filtered url
		 */
		public function stock_on_sale_filter_args( $link ) {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$onsale_filter  = ! empty( $_GET['onsale_filter'] ) ? (int) $_GET['onsale_filter'] : false;
			$instock_filter = ! empty( $_GET['instock_filter'] ) ? (int) $_GET['instock_filter'] : false;
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			if ( $onsale_filter ) {
				$link = add_query_arg( array( 'onsale_filter' => $onsale_filter ), $link );
			}

			if ( $instock_filter ) {
				$link = add_query_arg( array( 'instock_filter' => $instock_filter ), $link );
			}

			return $link;
		}

		/**
		 * Filters product query to filter by In Stock/On Sale
		 *
		 * @param WP_Query $q Query object.
		 */
		public function show_in_stock_products( $q ) {
			$current_widget_options = $this->get_settings();

			if ( ! empty( $_GET['instock_filter'] ) && ! empty( $current_widget_options[ $this->number ]['instock'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				// in stock products.
				$meta_query = apply_filters(
					'yith_wcan_instock_filter_meta_query_args',
					array(
						'relation' => 'AND',
						array(
							'key'     => '_stock_status',
							'value'   => 'instock',
							'compare' => '=',
						),
					)
				);

				$q->set( 'meta_query', array_merge( WC()->query->get_meta_query(), $meta_query ) );
			}
		}

		/**
		 * Filters post__in parameter of main products query, to show only products matching with current filters
		 *
		 * @param array $ids Post__in parameter.
		 * @return array Filtered array of post__in parameter.
		 */
		public function show_on_sale_products( $ids ) {
			$current_widget_options = $this->get_settings();

			if ( ! empty( $_GET['onsale_filter'] ) && ! empty( $current_widget_options[ $this->number ]['onsale'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$ids = array_merge( $ids, wc_get_product_ids_on_sale() );
			}

			return $ids;
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

			$instance = $old_instance;

			$instance['title']         = wp_strip_all_tags( $new_instance['title'] );
			$instance['onsale']        = ( isset( $new_instance['onsale'] ) && yith_plugin_fw_is_true( $new_instance['onsale'] ) ) ? 1 : 0;
			$instance['instock']       = ( isset( $new_instance['instock'] ) && yith_plugin_fw_is_true( $new_instance['instock'] ) ) ? 1 : 0;
			$instance['dropdown']      = ( isset( $new_instance['dropdown'] ) && yith_plugin_fw_is_true( $new_instance['dropdown'] ) ) ? 1 : 0;
			$instance['dropdown_type'] = $new_instance['dropdown_type'];

			return $instance;
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
			$span_class    = apply_filters( 'yith_wcan_dropdown_class', 'widget-dropdown' );
			$dropdown_type = apply_filters( 'yith_wcan_dropdown_type', $instance['dropdown_type'], $instance );
			$title         = ! empty( $instance['dropdown'] ) ? $title . '<span class="' . $span_class . '" data-toggle="' . $dropdown_type . '"></span>' : $title;

			return $title;
		}

		/**
		 * Remove subcategories when filtering for onsale products
		 *
		 * @param array $args Array of query arguments.
		 * @return array Array of filtered query arguments.
		 */
		public function force_to_show_products_instead_of_cateories_in_shop_page( $args ) {
			$yith_ajax_product_filter_enabled = function_exists( 'YITH_WCAN' );
			$show_categories_in_shop_page     = 'subcategories' === get_option( 'woocommerce_shop_page_display' );
			$stock_onsale_filter_enabled      = ( isset( $_GET['instock_filter'] ) || isset( $_GET['onsale_filter'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( $yith_ajax_product_filter_enabled && $show_categories_in_shop_page && is_shop() && $stock_onsale_filter_enabled ) {
				/**
				 * Get categories query will fails with this args.
				 * If the query fail, WooCommerce show the products list instead of categories
				 * in shop page
				 */
				$args['include'] = - 1;
			}

			return $args;
		}
	}
}
