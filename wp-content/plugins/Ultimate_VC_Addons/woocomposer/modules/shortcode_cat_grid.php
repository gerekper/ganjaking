<?php
/**
 * Woocomposer list
 *
 * @Module: Category Grid Layout
 * @Since: 1.0
 *  @package WooComposer
 */

if ( ! class_exists( 'WooComposer_Cat_Grid' ) ) {
	/**
	 * Class that initializes WooComposer Cat Grid
	 *
	 * @class WooComposer_Cat_Grid
	 */
	class WooComposer_Cat_Grid {
		/**
		 * Constructor function that constructs Cat Grid View.
		 *
		 * @method __construct
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'woocomposer_init_grid' ) );
			add_shortcode( 'woocomposer_grid_cat', array( $this, 'woocomposer_grid_shortcode' ) );
		} /* end constructor */
		/**
		 * Render function woocomposer init Cat grid.
		 *
		 * @access public
		 */
		public function woocomposer_init_grid() {
			if ( function_exists( 'vc_map' ) ) {
				$orderby_arr = array(
					'Date'       => 'date',
					'Title'      => 'title',
					'Product ID' => 'ID',
					'Name'       => 'name',
					'Price'      => 'price',
					'Sales'      => 'sales',
					'Random'     => 'rand',
				);
				vc_map(
					array(
						'name'                    => __( 'Categories Grid', 'ultimate_vc' ),
						'base'                    => 'woocomposer_grid_cat',
						'icon'                    => 'woo_grid',
						'class'                   => 'woo_grid',
						'category'                => __( 'WooComposer [ Beta ]', 'ultimate_vc' ),
						'description'             => __( 'Display categories in grid view', 'ultimate_vc' ),
						'controls'                => 'full',
						'wrapper_class'           => 'clearfix',
						'deprecated'              => '3.13.5',
						'show_settings_on_create' => true,
						'params'                  => array(
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Number of Categories', 'ultimate_vc' ),
								'param_name' => 'number',
								'value'      => '',
								'min'        => 1,
								'max'        => 500,
								'suffix'     => '',
								'group'      => 'Initial Settings',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Number of Columns', 'ultimate_vc' ),
								'param_name' => 'columns',
								'value'      => '',
								'min'        => 1,
								'max'        => 4,
								'suffix'     => '',
								'group'      => 'Initial Settings',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Orderby', 'ultimate_vc' ),
								'param_name'  => 'orderby',
								'admin_label' => true,
								'value'       => $orderby_arr,
								'group'       => 'Initial Settings',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Order', 'ultimate_vc' ),
								'param_name'  => 'order',
								'admin_label' => true,
								'value'       => array(
									__( 'Asending', 'ultimate_vc' ) => 'asc',
									__( 'Desending', 'ultimate_vc' ) => 'desc',
								),
								'group'       => 'Initial Settings',
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Options', 'ultimate_vc' ),
								'param_name'  => 'options',
								'admin_label' => true,
								'value'       => '',
								'options'     => array(
									'hide_empty' => array(
										'label' => __( 'Hide empty categories', 'ultimate_vc' ),
										'on'    => __( 'Yes', 'ultimate_vc' ),
										'off'   => __( 'No', 'ultimate_vc' ),
									),
									'parent'     => array(
										'label' => __( 'Display Child Categories if availabe in the loop', 'ultimate_vc' ),
										'on'    => __( 'Yes', 'ultimate_vc' ),
										'off'   => __( 'No', 'ultimate_vc' ),
									),
									'sel_cat'    => array(
										'label' => __( 'Select custom categories to display', 'ultimate_vc' ),
										'on'    => __( 'Yes', 'ultimate_vc' ),
										'off'   => __( 'No', 'ultimate_vc' ),
									),
								),
								'group'       => 'Initial Settings',
							),
							array(
								'type'       => 'product_categories',
								'class'      => '',
								'heading'    => __( 'Select Categories', 'ultimate_vc' ),
								'param_name' => 'ids',
								'value'      => '',
								'group'      => 'Initial Settings',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Category count text', 'ultimate_vc' ),
								'param_name' => 'cat_count',
								'value'      => '',
								'group'      => 'Initial Settings',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Design Style', 'ultimate_vc' ),
								'param_name'  => 'design_style',
								'admin_label' => true,
								'value'       => array(
									__( 'Style 1', 'ultimate_vc' ) => 'style01',
									__( 'Style 2', 'ultimate_vc' ) => 'style02',
									__( 'Style 3', 'ultimate_vc' ) => 'style03',
								),
								'group'       => 'Initial Settings',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Text Alignment', 'ultimate_vc' ),
								'param_name' => 'text_align',
								'value'      => array(
									__( 'Left', 'ultimate_vc' ) => 'left',
									__( 'Center', 'ultimate_vc' ) => 'center',
									__( 'Right', 'ultimate_vc' ) => 'right',
								),
								'group'      => 'Initial Settings',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Product Border Style', 'ultimate_vc' ),
								'param_name' => 'border_style',
								'value'      => array(
									__( 'None', 'ultimate_vc' ) => '',
									__( 'Solid', 'ultimate_vc' ) => 'solid',
									__( 'Dashed', 'ultimate_vc' ) => 'dashed',
									__( 'Dotted', 'ultimate_vc' ) => 'dotted',
									__( 'Double', 'ultimate_vc' ) => 'double',
									__( 'Inset', 'ultimate_vc' ) => 'inset',
									__( 'Outset', 'ultimate_vc' ) => 'outset',
								),
								'group'      => 'Initial Settings',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Border Color', 'ultimate_vc' ),
								'param_name' => 'border_color',
								'value'      => '#333333',
								'dependency' => array(
									'element'   => 'border_style',
									'not_empty' => true,
								),
								'group'      => 'Initial Settings',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Border Size', 'ultimate_vc' ),
								'param_name' => 'border_size',
								'value'      => 1,
								'min'        => 1,
								'max'        => 10,
								'suffix'     => 'px',
								'dependency' => array(
									'element'   => 'border_style',
									'not_empty' => true,
								),
								'group'      => 'Initial Settings',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Border Radius', 'ultimate_vc' ),
								'param_name' => 'border_radius',
								'value'      => 5,
								'min'        => 1,
								'max'        => 500,
								'suffix'     => 'px',
								'dependency' => array(
									'element'   => 'border_style',
									'not_empty' => true,
								),
								'group'      => 'Initial Settings',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Animation', 'ultimate_vc' ),
								'param_name' => 'product_animation',
								'value'      => array(
									__( 'No Animation', 'ultimate_vc' ) => '',
									__( 'Swing', 'ultimate_vc' ) => 'swing',
									__( 'Pulse', 'ultimate_vc' ) => 'pulse',
									__( 'Fade In', 'ultimate_vc' ) => 'fadeIn',
									__( 'Fade In Up', 'ultimate_vc' ) => 'fadeInUp',
									__( 'Fade In Down', 'ultimate_vc' ) => 'fadeInDown',
									__( 'Fade In Left', 'ultimate_vc' ) => 'fadeInLeft',
									__( 'Fade In Right', 'ultimate_vc' ) => 'fadeInRight',
									__( 'Fade In Up Long', 'ultimate_vc' ) => 'fadeInUpBig',
									__( 'Fade In Down Long', 'ultimate_vc' ) => 'fadeInDownBig',
									__( 'Fade In Left Long', 'ultimate_vc' ) => 'fadeInLeftBig',
									__( 'Fade In Right Long', 'ultimate_vc' ) => 'fadeInRightBig',
									__( 'Slide In Down', 'ultimate_vc' ) => 'slideInDown',
									__( 'Slide In Left', 'ultimate_vc' ) => 'slideInLeft',
									__( 'Slide In Left', 'ultimate_vc' ) => 'slideInLeft',
									__( 'Bounce In', 'ultimate_vc' ) => 'bounceIn',
									__( 'Bounce In Up', 'ultimate_vc' ) => 'bounceInUp',
									__( 'Bounce In Down', 'ultimate_vc' ) => 'bounceInDown',
									__( 'Bounce In Left', 'ultimate_vc' ) => 'bounceInLeft',
									__( 'Bounce In Right', 'ultimate_vc' ) => 'bounceInRight',
									__( 'Rotate In', 'ultimate_vc' ) => 'rotateIn',
									__( 'Light Speed In', 'ultimate_vc' ) => 'lightSpeedIn',
									__( 'Roll In', 'ultimate_vc' ) => 'rollIn',
								),
								'group'      => 'Initial Settings',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Categories Title Background Color', 'ultimate_vc' ),
								'param_name' => 'color_categories_bg',
								'value'      => '',
								'group'      => 'Style Settings',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Categories Title Color', 'ultimate_vc' ),
								'param_name' => 'color_categories',
								'value'      => '',
								'group'      => 'Style Settings',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Categories Title', 'ultimate_vc' ),
								'param_name' => 'size_cat',
								'value'      => '',
								'min'        => 10,
								'max'        => 72,
								'suffix'     => 'px',
								'group'      => 'Size Settings',
							),
						), /* vc_map params array */
					)/* vc_map parent array */
				); /* vc_map call */
			} /* vc_map function check */
		} /* end woocomposer_init_grid */
		/**
		 * Render function for woocomposer grid shortcode.
		 *
		 * @param array $atts represts module attribuits.
		 * @access public
		 */
		public function woocomposer_grid_shortcode( $atts ) {
			global $woocommerce_loop;
			$parent                             = '';
			$ult_woocomposer_cat_grid_shortcode =
				shortcode_atts(
					array(
						'number'                => null,
						'orderby'               => 'date',
						'order'                 => 'ASC',
						'columns'               => '4',
						'ids'                   => '',
						'options'               => '',
						'cat_count'             => '',
						'design_style'          => 'style01',
						'text_align'            => '',
						'border_style'          => '',
						'border_color'          => '',
						'border_size'           => '',
						'border_radius'         => '',
						'product_animation'     => '',
						'color_categories_bg'   => '',
						'color_categories'      => '',
						'color_cat_count_bg'    => '',
						'color_cat_count_color' => '',
						'size_cat'              => '',
						'img_animate'           => '',
					),
					$atts
				);

			$border      = '';
			$size        = '';
			$count_style = '';
			$opts        = explode( ',', $ult_woocomposer_cat_grid_shortcode['options'] );

			if ( '' !== $ult_woocomposer_cat_grid_shortcode['color_categories'] ) {
				$size .= 'color:' . $ult_woocomposer_cat_grid_shortcode['color_categories'] . ';';
			}
			if ( '' !== $ult_woocomposer_cat_grid_shortcode['color_categories_bg'] ) {
				$size .= 'background:' . $ult_woocomposer_cat_grid_shortcode['color_categories_bg'] . ';';
			}
			if ( '' !== $ult_woocomposer_cat_grid_shortcode['size_cat'] ) {
				$size .= 'font-size:' . $ult_woocomposer_cat_grid_shortcode['size_cat'] . 'px;';
			}

			if ( '' !== $ult_woocomposer_cat_grid_shortcode['color_cat_count_bg'] ) {
				$count_style .= 'background:' . $ult_woocomposer_cat_grid_shortcode['color_cat_count_bg'] . ';';
			}
			if ( '' !== $ult_woocomposer_cat_grid_shortcode['color_cat_count_color'] ) {
				$count_style .= 'color:' . $ult_woocomposer_cat_grid_shortcode['color_cat_count_color'] . ';';
			}

			if ( isset( $atts['ids'] ) ) {
				$ult_woocomposer_cat_grid_shortcode['ids'] = explode( ',', $atts['ids'] );
				$ult_woocomposer_cat_grid_shortcode['ids'] = array_map( 'trim', $ult_woocomposer_cat_grid_shortcode['ids'] );
			} else {
				$ult_woocomposer_cat_grid_shortcode['ids'] = array();
			}

			$hide_empty = in_array( 'hide_empty', $opts ) ? 1 : 0;
			$parent     = in_array( 'parent', $opts ) ? '' : 0;

			if ( '' !== $ult_woocomposer_cat_grid_shortcode['border_style'] ) {
				$border .= 'border:' . $ult_woocomposer_cat_grid_shortcode['border_size'] . 'px ' . $ult_woocomposer_cat_grid_shortcode['border_style'] . ' ' . $ult_woocomposer_cat_grid_shortcode['border_color'] . ';';
				$border .= 'border-radius:' . $ult_woocomposer_cat_grid_shortcode['border_radius'] . 'px;';
			}
			// get terms and workaround WP bug with parents/pad counts.
			$args = array(
				'orderby'    => $ult_woocomposer_cat_grid_shortcode['orderby'],
				'order'      => $ult_woocomposer_cat_grid_shortcode['order'],
				'hide_empty' => $hide_empty,
				'include'    => $ult_woocomposer_cat_grid_shortcode['ids'],
				'pad_counts' => true,
				'child_of'   => $parent,
			);

			$product_categories = get_terms( 'product_cat', $args );

			if ( '' !== $parent ) {
				$product_categories = wp_list_filter( $product_categories, array( 'parent' => $parent ) );
			}

			if ( $hide_empty ) {
				foreach ( $product_categories as $key => $category ) {
					if ( 0 == $category->count ) {
						unset( $product_categories[ $key ] );
					}
				}
			}

			if ( $ult_woocomposer_cat_grid_shortcode['number'] ) {
				$product_categories = array_slice( $product_categories, 0, $ult_woocomposer_cat_grid_shortcode['number'] );
			}

			$woocommerce_loop['columns'] = $ult_woocomposer_cat_grid_shortcode['columns'];

			ob_start();

			// Reset loop/columns globals when starting a new loop.
			$woocommerce_loop['loop']   = '';
			$woocommerce_loop['column'] = '';

			if ( $product_categories ) {

				echo '<ul class="wcmp-cat-grid products">';

				foreach ( $product_categories as $category ) {

					// Store loop count we're currently on.
					if ( empty( $woocommerce_loop['loop'] ) ) {
						$woocommerce_loop['loop'] = 0;
					}

					// Store column count for displaying the grid.
					if ( empty( $woocommerce_loop['columns'] ) ) {
						$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
					}

					// Increase loop count.
					$woocommerce_loop['loop']++;
					?>
					<li class="product-category product
					<?php
					if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] ) {
						echo ' first';
					}
					if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] ) {
						echo ' last';
					}
					?>
					">
						<div class="wcmp-product wcmp-img-<?php echo $ult_woocomposer_cat_grid_shortcode['img_animate']; ?> wcmp-cat-<?php echo $ult_woocomposer_cat_grid_shortcode['design_style'] . ' animated ' . $ult_woocomposer_cat_grid_shortcode['product_animation']; ?>" style="<?php echo $border; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
						<?php do_action( 'woocommerce_before_subcategory', $category ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a href="<?php echo get_term_link( $category->slug, 'product_cat' ); ?>" style="text-align:<?php echo $ult_woocomposer_cat_grid_shortcode['text_align']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>;">
							<div class="wcmp-product-image">
							<?php
								/**
								 * Woocommerce_before_subcategory_title hook
								 *
								 * @hooked woocommerce_subcategory_thumbnail - 10
								 */
								do_action( 'woocommerce_before_subcategory_title', $category );
							?>
							</div><!--.wcmp-product-image-->
							<h3 style="<?php echo $size; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
								<?php
									echo $category->name;  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

								if ( $category->count > 0 ) {
									echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count" style="' . $count_style . '">' . $category->count . ' ' . $ult_woocomposer_cat_grid_shortcode['cat_count'] . '</mark>', $category ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
							</h3>
							<?php
								/**
								 * Woocommerce_after_subcategory_title hook
								 */
								do_action( 'woocommerce_after_subcategory_title', $category );
							?>
						</a>
						<?php do_action( 'woocommerce_after_subcategory', $category ); ?>		
						</div><!--.wcmp-product-->
					</li>	
					<?php
				}

				woocommerce_product_loop_end();

			}

			woocommerce_reset_loop();

			return '<div class="woocommerce columns-' . $ult_woocomposer_cat_grid_shortcode['columns'] . '">' . ob_get_clean() . '</div>';
		}//end woocomposer_grid_shortcode()

	} /* end class WooComposer_Cat_Grid */
	new WooComposer_Cat_Grid();
}
