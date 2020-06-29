<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YWMMQ_Products_Bulk_Ops' ) ) {

	/**
	 * Displays products bulk operations with summary table in plugin admin tab
	 *
	 * @class   YWMMQ_Products_Bulk_Ops
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWMMQ_Products_Bulk_Ops {

		/**
		 * Single instance of the class
		 *
		 * @var \YWMMQ_Products_Bulk_Ops
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWMMQ_Products_Bulk_Ops
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self();

			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_filter( 'set-screen-option', array( $this, 'set_options' ), 10, 3 );
			add_action( 'current_screen', array( $this, 'add_options' ) );

		}

		/**
		 * Outputs the table template with insert form in plugin options panel
		 *
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 * @return  void
		 */
		public function output() {

			global $wpdb;

			$table = new YITH_Custom_Table( array(
				                                'singular' => __( 'product', 'yith-woocommerce-minimum-maximum-quantity' ),
				                                'plural'   => __( 'products', 'yith-woocommerce-minimum-maximum-quantity' )
			                                ) );

			$table->options = array(
				'select_table'     => $wpdb->prefix . 'posts a INNER JOIN ' . $wpdb->prefix . 'postmeta b ON a.ID = b.post_id',
				'select_columns'   => array(
					'a.ID',
					'a.post_title',
					'MAX(CASE WHEN b.meta_key = "_ywmmq_product_exclusion" THEN b.meta_value ELSE NULL END) AS excluded',
					'MAX(CASE WHEN b.meta_key = "_ywmmq_product_quantity_limit_override" THEN b.meta_value ELSE NULL END) AS override_qty'
				),
				'select_where'     => 'a.post_type = "product" AND ( b.meta_key = "_ywmmq_product_exclusion" OR b.meta_key = "_ywmmq_product_quantity_limit_override" ) AND b.meta_value = "yes"',
				'select_group'     => 'a.ID',
				'select_order'     => 'a.post_title',
				'select_order_dir' => 'ASC',
				'per_page_option'  => 'items_per_page',
				'count_table'      => '( SELECT COUNT(*) FROM ' . $wpdb->prefix . 'posts a INNER JOIN ' . $wpdb->prefix . 'postmeta b ON a.ID = b.post_id  WHERE a.post_type = "product" AND (b.meta_key = "_ywmmq_product_exclusion" OR b.meta_key = "_ywmmq_product_quantity_limit_override") AND b.meta_value="yes" GROUP BY a.ID ) AS count_table',
				'count_where'      => '',
				'key_column'       => 'ID',
				'view_columns'     => array(
					'cb'           => '<input type="checkbox" />',
					'product'      => __( 'Product', 'yith-woocommerce-minimum-maximum-quantity' ),
					'excluded'     => __( 'Excluded', 'yith-woocommerce-minimum-maximum-quantity' ),
					'override_qty' => __( 'Override Quantity Restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				),
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'product' => array( 'post_title', true )
				),
				'custom_columns'   => array(
					'column_product'      => function ( $item, $me ) {

						$edit_query_args = array(
							'page'    => $_GET['page'],
							'tab'     => $_GET['tab'],
							'section' => ( isset( $_GET['section'] ) ? $_GET['section'] : 'products' ),
							'action'  => 'edit',
							'id'      => $item['ID']
						);
						$edit_url        = esc_url( add_query_arg( $edit_query_args, admin_url( 'admin.php' ) ) );

						$delete_query_args = array(
							'page'    => $_GET['page'],
							'tab'     => $_GET['tab'],
							'section' => ( isset( $_GET['section'] ) ? $_GET['section'] : 'products' ),

							'action' => 'delete',
							'id'     => $item['ID']
						);
						$delete_url        = esc_url( add_query_arg( $delete_query_args, admin_url( 'admin.php' ) ) );

						$product_query_args = array(
							'post'   => $item['ID'],
							'action' => 'edit'
						);
						$product_url        = esc_url( add_query_arg( $product_query_args, admin_url( 'post.php' ) ) );

						$actions = array(
							'edit'    => '<a href="' . $edit_url . '">' . __( 'Edit rule', 'yith-woocommerce-minimum-maximum-quantity' ) . '</a>',
							'product' => '<a href="' . $product_url . '" target="_blank">' . __( 'Edit product', 'yith-woocommerce-minimum-maximum-quantity' ) . '</a>',
							'delete'  => '<a href="' . $delete_url . '">' . __( 'Remove from list', 'yith-woocommerce-minimum-maximum-quantity' ) . '</a>',
						);

						return sprintf( '<strong><a class="tips" href="%s" data-tip="%s">#%d %s </a></strong> %s', $edit_url, __( 'Edit rule', 'yith-woocommerce-minimum-maximum-quantity' ), $item['ID'], $item['post_title'], $me->row_actions( $actions ) );
					},
					'column_excluded'     => function ( $item ) {

						if ( $item['excluded'] == 'yes' ) {
							$class = 'show';
							$tip   = __( 'Yes', 'yith-woocommerce-minimum-maximum-quantity' );
						} else {
							$class = 'hide';
							$tip   = __( 'No', 'yith-woocommerce-minimum-maximum-quantity' );
						}

						return sprintf( '<mark class="%s tips" data-tip="%s">%s</mark>', $class, $tip, $tip );

					},
					'column_override_qty' => function ( $item ) {

						if ( $item['override_qty'] == 'yes' ) {
							$class   = 'show';
							$tip     = __( 'Yes', 'yith-woocommerce-minimum-maximum-quantity' );
							$product = wc_get_product( $item['ID'] );
							$min     = $product->get_meta( '_ywmmq_product_minimum_quantity' );
							$max     = $product->get_meta( '_ywmmq_product_maximum_quantity' );
							$step    = $product->get_meta( '_ywmmq_product_step_quantity' );
							$limits  = sprintf( __( 'Min.: %d - Max.: %d', 'yith-woocommerce-minimum-maximum-quantity' ), $min, $max ) . ( $step <= 1 ? '' : ' - ' . sprintf( __( 'Group of: %d', 'yith-woocommerce-minimum-maximum-quantity' ), $step ) );


						} else {
							$class  = 'hide';
							$tip    = __( 'No', 'yith-woocommerce-minimum-maximum-quantity' );
							$limits = '';
						}

						return sprintf( '<mark class="%s tips" data-tip="%s">%s</mark> %s', $class, $tip, $tip, $limits );

					},
				),
				'bulk_actions'     => array(
					'actions'   => array(
						'delete' => __( 'Remove from list', 'yith-woocommerce-minimum-maximum-quantity' )
					),
					'functions' => array(
						'function_delete' => function () {
							global $wpdb;

							$ids = isset( $_GET['id'] ) ? $_GET['id'] : array();
							if ( is_array( $ids ) ) {
								$ids = implode( ',', $ids );
							}

							if ( ! empty( $ids ) ) {
								$wpdb->query( "UPDATE {$wpdb->prefix}postmeta
                                           SET meta_value='no'
                                           WHERE ( meta_key = '_ywmmq_product_exclusion' OR meta_key = '_ywmmq_product_quantity_limit_override' ) AND post_id IN ( $ids )"
								);
							}
						}
					)
				),
			);

			$message = '';
			$notice  = '';

			$list_query_args = array(
				'page'    => $_GET['page'],
				'tab'     => $_GET['tab'],
				'section' => ( isset( $_GET['section'] ) ? $_GET['section'] : 'products' )
			);

			$list_url = esc_url( add_query_arg( $list_query_args, admin_url( 'admin.php' ) ) );

			if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], basename( __FILE__ ) ) ) {

				$item_valid = $this->validate_fields( $_POST );

				if ( $item_valid !== true ) {

					$notice = $item_valid;

				} else {

					$product_ids  = ( ! is_array( $_POST['product_ids'] ) ) ? explode( ',', $_POST['product_ids'] ) : $_POST['product_ids'];
					$exclusion    = isset( $_POST['_ywmmq_product_exclusion'] ) ? 'yes' : 'no';
					$override_qty = isset( $_POST['_ywmmq_product_quantity_limit_override'] ) ? 'yes' : 'no';
					$minimum_qty  = isset( $_POST['_ywmmq_product_minimum_quantity'] ) ? $_POST['_ywmmq_product_minimum_quantity'] : 0;
					$maximum_qty  = isset( $_POST['_ywmmq_product_maximum_quantity'] ) ? $_POST['_ywmmq_product_maximum_quantity'] : 0;
					$step_qty     = isset( $_POST['_ywmmq_product_step_quantity'] ) ? $_POST['_ywmmq_product_step_quantity'] : 1;

					foreach ( $product_ids as $product_id ) {

						$product = wc_get_product( $product_id );
						$product->update_meta_data( '_ywmmq_product_exclusion', $exclusion );
						$product->update_meta_data( '_ywmmq_product_quantity_limit_override', $override_qty );
						$product->update_meta_data( '_ywmmq_product_minimum_quantity', $minimum_qty );
						$product->update_meta_data( '_ywmmq_product_maximum_quantity', $maximum_qty );
						$product->update_meta_data( '_ywmmq_product_step_quantity', $step_qty );
						$product->save();

					}

					if ( ! empty( $_POST['insert'] ) ) {

						$message = sprintf( _n( '%s product added successfully', '%s products added successfully', count( $product_ids ), 'yith-woocommerce-minimum-maximum-quantity' ), count( $product_ids ) );

					} elseif ( ! empty( $_POST['edit'] ) ) {

						$message = __( 'Product updated successfully', 'yith-woocommerce-minimum-maximum-quantity' );

					}

				}

			}

			$table->prepare_items();

			$data_selected = '';
			$value         = '';
			$item          = array(
				'ID'           => 0,
				'excluded'     => '',
				'override_qty' => '',
				'minimum_qty'  => 0,
				'maximum_qty'  => 0,
				'step_qty'     => 1
			);

			if ( 'delete' === $table->current_action() ) {
				$ids     = isset( $_GET['id'] ) ? ( ( ! is_array( $_GET['id'] ) ) ? explode( ',', $_GET['id'] ) : $_GET['id'] ) : '';
				$items   = $ids != '' ? count( $ids ) : 0;
				$message = sprintf( _n( '%s product removed successfully', '%s products removed successfully', $items, 'yith-woocommerce-minimum-maximum-quantity' ), $items );
			}

			if ( isset( $_GET['id'] ) && ! empty( $_GET['action'] ) && ( 'edit' == $_GET['action'] ) ) {

				$product       = wc_get_product( $_GET['id'] );
				$value         = $_GET['id'];
				$data_selected = wp_kses_post( $product->get_formatted_name() );
				$data_selected = array( $value => $data_selected );
				$item          = array(
					'ID'           => $_GET['id'],
					'excluded'     => $product->get_meta( '_ywmmq_product_exclusion' ),
					'override_qty' => $product->get_meta( '_ywmmq_product_quantity_limit_override' ),
					'minimum_qty'  => $product->get_meta( '_ywmmq_product_minimum_quantity' ),
					'maximum_qty'  => $product->get_meta( '_ywmmq_product_maximum_quantity' ),
					'step_qty'     => $product->get_meta( '_ywmmq_product_step_quantity' )
				);

			}

			?>
            <div class="wrap">
                <div class="icon32 icon32-posts-post" id="icon-edit"><br /></div>
                <h1><?php _e( 'Product Rule list', 'yith-woocommerce-minimum-maximum-quantity' ); ?>
					<?php if ( empty( $_GET['action'] ) || ( 'insert' !== $_GET['action'] && 'edit' !== $_GET['action'] ) ) : ?>
						<?php $query_args = array(
							'page'    => $_GET['page'],
							'tab'     => $_GET['tab'],
							'section' => ( isset( $_GET['section'] ) ? $_GET['section'] : 'products' ),
							'action'  => 'insert'
						);
						$add_form_url     = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
						?>
                        <a class="page-title-action" href="<?php echo $add_form_url; ?>"><?php _e( 'Add Products', 'yith-woocommerce-minimum-maximum-quantity' ); ?></a>
					<?php endif; ?>
                </h1>
				<?php if ( ! empty( $notice ) ) : ?>
                    <div id="notice" class="error below-h2">
                        <p><?php echo $notice; ?></p>
                    </div>
				<?php endif; ?>

				<?php if ( ! empty( $message ) ) : ?>
                    <div id="message" class="updated below-h2">
                        <p><?php echo $message; ?></p>
                    </div>
				<?php endif; ?>

				<?php if ( ! empty( $_GET['action'] ) && ( 'insert' == $_GET['action'] || 'edit' == $_GET['action'] ) ) : ?>

                    <form id="form" method="POST" action="<?php echo $list_url; ?>">
                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
                        <table class="form-table">
                            <tbody>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="product_ids"><?php echo ( 'edit' == $_GET['action'] ) ? __( 'Product to edit', 'yith-woocommerce-minimum-maximum-quantity' ) : __( 'Products to add', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
                                </th>
                                <td class="forminp">
									<?php if ( 'edit' == $_GET['action'] ) : ?>
                                        <input id="product_id" name="product_ids" type="hidden" value="<?php echo esc_attr( $item['ID'] ); ?>" />
									<?php endif; ?>

									<?php

									$select_args = array(
										'class'            => 'wc-product-search',
										'id'               => 'product_ids',
										'name'             => 'product_ids',
										'data-placeholder' => __( 'Search for a product&hellip;', 'yith-woocommerce-minimum-maximum-quantity' ),
										'data-allow_clear' => false,
										'data-selected'    => $data_selected,
										'data-multiple'    => ( 'edit' == $_GET['action'] ) ? false : true,
										'data-action'      => 'woocommerce_json_search_products',
										'value'            => $value,
										'style'            => 'width: 50%'
									);

									if ( 'edit' == $_GET['action'] ) {
										$select_args['custom-attributes'] = array( 'disabled' => 'disabled' );
									}

									yit_add_select2_fields( $select_args )

									?>

                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="_ywmmq_product_exclusion"><?php _e( 'Exclude product', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
                                </th>
                                <td class="forminp forminp-checkbox">
                                    <input
                                        id="_ywmmq_product_exclusion"
                                        name="_ywmmq_product_exclusion"
                                        type="checkbox"
										<?php echo ( esc_attr( $item['excluded'] ) == 'yes' ) ? 'checked="checked"' : ''; ?>
                                    />
                                    <span class="description"><?php echo ( 'edit' == $_GET['action'] ) ? __( 'Do not apply restrictions to this product', 'yith-woocommerce-minimum-maximum-quantity' ) : __( 'Do not apply restrictions to the selected products', 'yith-woocommerce-minimum-maximum-quantity' ) ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="_ywmmq_product_quantity_limit_override"><?php _e( 'Override product restrictions', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
                                </th>
                                <td class="forminp forminp-checkbox">
                                    <input
                                        id="_ywmmq_product_quantity_limit_override"
                                        name="_ywmmq_product_quantity_limit_override"
                                        type="checkbox"
										<?php echo ( esc_attr( $item['override_qty'] ) == 'yes' ) ? 'checked="checked"' : ''; ?>
                                    />
                                    <span class="description"><?php _e( 'Global product restrictions will be overridden by current ones. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ) ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="_ywmmq_product_minimum_quantity"><?php _e( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ) ?></label>
                                </th>
                                <td class="forminp forminp-number">
                                    <input
                                        id="_ywmmq_product_minimum_quantity"
                                        name="_ywmmq_product_minimum_quantity"
                                        type="number"
                                        value="<?php echo esc_attr( $item['minimum_qty'] ); ?>"
                                        min="0"
                                        required="required"
                                    />
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="_ywmmq_product_maximum_quantity"><?php _e( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ) ?></label>
                                </th>
                                <td class="forminp forminp-number">
                                    <input
                                        id="_ywmmq_product_maximum_quantity"
                                        name="_ywmmq_product_maximum_quantity"
                                        type="number"
                                        value="<?php echo esc_attr( $item['maximum_qty'] ); ?>"
                                        min="0"
                                        required="required"
                                    />
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="_ywmmq_product_step_quantity"><?php _e( 'Quantity groups of', 'yith-woocommerce-minimum-maximum-quantity' ) ?></label>
                                </th>
                                <td class="forminp forminp-number">
                                    <input
                                        id="_ywmmq_product_step_quantity"
                                        name="_ywmmq_product_step_quantity"
                                        type="number"
                                        value="<?php echo esc_attr( $item['step_qty'] ); ?>"
                                        min="1"
                                        required="required"
                                    />
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <input
                            id="<?php echo $_GET['action'] ?>"
                            name="<?php echo $_GET['action'] ?>"
                            type="submit"
                            value="<?php echo( ( 'insert' == $_GET['action'] ) ? __( 'Add product rule', 'yith-woocommerce-minimum-maximum-quantity' ) : __( 'Update product rule', 'yith-woocommerce-minimum-maximum-quantity' ) ); ?>"
                            class="button-primary"
                        />
                        <a class="button-secondary" href="<?php echo $list_url; ?>"><?php _e( 'Return to rule list', 'yith-woocommerce-minimum-maximum-quantity' ); ?></a>
                    </form>

				<?php else : ?>

                    <form id="custom-table" method="GET" action="<?php echo $list_url; ?>">
                        <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
                        <input type="hidden" name="tab" value="<?php echo $_GET['tab']; ?>" />
                        <input type="hidden" name="section" value="<?php echo( isset( $_GET['section'] ) ? $_GET['section'] : 'products' ); ?>" />
						<?php $table->display(); ?>
                    </form>

				<?php endif; ?>
            </div>
			<?php

		}

		/**
		 * Validate input fields
		 *
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 *
		 * @param   $item array POST data array
		 *
		 * @return  bool|string
		 */
		private function validate_fields( $item ) {

			$messages = array();

			if ( empty( $item['product_ids'] ) ) {
				$messages[] = __( 'Select at least one product', 'yith-woocommerce-minimum-maximum-quantity' );
			}

			if ( empty( $item['_ywmmq_product_quantity_limit_override'] ) && empty( $item['_ywmmq_product_exclusion'] ) ) {
				$messages[] = __( 'Select at least one option', 'yith-woocommerce-minimum-maximum-quantity' );
			}

			if ( empty( $messages ) ) {
				return true;
			}

			return implode( '<br />', $messages );

		}

		/**
		 * Add screen options for list table template
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_options() {

			if ( 'yith-plugins_page_yith-wc-min-max-qty' == get_current_screen()->id && ( isset( $_GET['tab'] ) && $_GET['tab'] == 'bulk' ) && ( ! isset( $_GET['section'] ) || $_GET['section'] == 'products' ) && ( ! isset( $_GET['action'] ) || ( $_GET['action'] != 'edit' && $_GET['action'] != 'insert' ) ) ) {

				$option = 'per_page';

				$args = array(
					'label'   => __( 'Products', 'yith-woocommerce-minimum-maximum-quantity' ),
					'default' => 10,
					'option'  => 'items_per_page'
				);

				add_screen_option( $option, $args );

			}

		}

		/**
		 * Set screen options for list table template
		 *
		 * @since   1.0.0
		 *
		 * @param   $status
		 * @param   $option
		 * @param   $value
		 *
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function set_options( $status, $option, $value ) {

			return ( 'items_per_page' == $option ) ? $value : $status;

		}

	}

	/**
	 * Unique access to instance of YWMMQ_Products_Bulk_Ops class
	 *
	 * @return \YWMMQ_Products_Bulk_Ops
	 */
	function YWMMQ_Products_Bulk_Ops() {

		return YWMMQ_Products_Bulk_Ops::get_instance();

	}

	new YWMMQ_Products_Bulk_Ops();
}