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

if ( ! class_exists( 'YWMMQ_Tags_Bulk_Ops' ) ) {

	/**
	 * Displays tags bulk operations with summary table in plugin admin tab
	 *
	 * @class   YWMMQ_Tags_Bulk_Ops
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWMMQ_Tags_Bulk_Ops {

		/**
		 * Single instance of the class
		 *
		 * @var \YWMMQ_Tags_Bulk_Ops
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWMMQ_Tags_Bulk_Ops
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
			add_action( 'wp_ajax_ywmmq_json_search_product_tags', array( $this, 'json_search_product_tags' ), 10 );

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
				                                'singular' => __( 'tag', 'yith-woocommerce-minimum-maximum-quantity' ),
				                                'plural'   => __( 'tags', 'yith-woocommerce-minimum-maximum-quantity' )
			                                ) );

			$table->options = array(
				'select_table'     => $wpdb->prefix . 'terms a INNER JOIN ' . $wpdb->prefix . 'term_taxonomy b ON a.term_id = b.term_id INNER JOIN ' . $wpdb->prefix . 'termmeta c ON c.term_id = a.term_id',
				'select_columns'   => array(
					'a.term_id AS ID',
					'a.name',
					'MAX(CASE WHEN c.meta_key = "_ywmmq_tag_exclusion" THEN c.meta_value ELSE NULL END) AS excluded',
					'MAX(CASE WHEN c.meta_key = "_ywmmq_tag_quantity_limit_override" THEN c.meta_value ELSE NULL END) AS override_qty',
					'MAX(CASE WHEN c.meta_key = "_ywmmq_tag_value_limit_override" THEN c.meta_value ELSE NULL END) AS override_val'
				),
				'select_where'     => 'b.taxonomy = "product_tag" AND ( c.meta_key = "_ywmmq_tag_exclusion" OR c.meta_key = "_ywmmq_tag_quantity_limit_override" OR c.meta_key = "_ywmmq_tag_value_limit_override") AND c.meta_value = "yes"',
				'select_group'     => 'a.term_id',
				'select_order'     => 'a.name',
				'select_order_dir' => 'ASC',
				'per_page_option'  => 'items_per_page',
				'count_table'      => '( SELECT COUNT(*) FROM ' . $wpdb->prefix . 'terms a INNER JOIN ' . $wpdb->prefix . 'term_taxonomy b ON a.term_id = b.term_id INNER JOIN ' . $wpdb->prefix . 'termmeta c ON c.term_id = a.term_id WHERE b.taxonomy = "product_tag" AND ( c.meta_key = "_ywmmq_tag_exclusion" OR c.meta_key = "_ywmmq_tag_quantity_limit_override" OR c.meta_key = "_ywmmq_tag_value_limit_override") AND c.meta_value = "yes" GROUP BY a.term_id ) AS count_table',
				'count_where'      => '',
				'key_column'       => 'ID',
				'view_columns'     => array(
					'cb'           => '<input type="checkbox" />',
					'tag'          => __( 'Tag', 'yith-woocommerce-minimum-maximum-quantity' ),
					'excluded'     => __( 'Excluded', 'yith-woocommerce-minimum-maximum-quantity' ),
					'override_qty' => __( 'Override Quantity Restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
					'override_val' => __( 'Override Spend Restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				),
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'product' => array( 'post_title', true )
				),
				'custom_columns'   => array(
					'column_tag'          => function ( $item, $me ) {

						$edit_query_args = array(
							'page'    => $_GET['page'],
							'tab'     => $_GET['tab'],
							'section' => $_GET['section'],
							'action'  => 'edit',
							'id'      => $item['ID']
						);
						$edit_url        = esc_url( add_query_arg( $edit_query_args, admin_url( 'admin.php' ) ) );

						$delete_query_args = array(
							'page'    => $_GET['page'],
							'tab'     => $_GET['tab'],
							'section' => $_GET['section'],

							'action' => 'delete',
							'id'     => $item['ID']
						);
						$delete_url        = esc_url( add_query_arg( $delete_query_args, admin_url( 'admin.php' ) ) );

						$tag_query_args = array(
							'taxonomy'  => 'product_tag',
							'post_type' => 'product',
							'tag_ID'    => $item['ID'],
							'action'    => 'edit'
						);
						$tag_url        = esc_url( add_query_arg( $tag_query_args, admin_url( 'edit-tags.php' ) ) );

						$actions = array(
							'edit'    => '<a href="' . $edit_url . '">' . __( 'Edit rule', 'yith-woocommerce-minimum-maximum-quantity' ) . '</a>',
							'product' => '<a href="' . $tag_url . '" target="_blank">' . __( 'Edit tag', 'yith-woocommerce-minimum-maximum-quantity' ) . '</a>',
							'delete'  => '<a href="' . $delete_url . '">' . __( 'Remove from list', 'yith-woocommerce-minimum-maximum-quantity' ) . '</a>',
						);

						return sprintf( '<strong><a class="tips" href="%s" data-tip="%s">#%d %s </a></strong> %s', $edit_url, __( 'Edit rule', 'yith-woocommerce-minimum-maximum-quantity' ), $item['ID'], $item['name'], $me->row_actions( $actions ) );
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
							$class  = 'show';
							$tip    = __( 'Yes', 'yith-woocommerce-minimum-maximum-quantity' );
							$min    = get_term_meta( $item['ID'], '_ywmmq_tag_minimum_quantity', true );
							$max    = get_term_meta( $item['ID'], '_ywmmq_tag_maximum_quantity', true );
							$step   = get_term_meta( $item['ID'], '_ywmmq_tag_step_quantity', true );
							$limits = sprintf( __( 'Min.: %d - Max.: %d', 'yith-woocommerce-minimum-maximum-quantity' ), $min, $max ) . ( $step <= 1 ? '' : ' - ' . sprintf( __( 'Group of: %d', 'yith-woocommerce-minimum-maximum-quantity' ), $step ) );

						} else {
							$class  = 'hide';
							$tip    = __( 'No', 'yith-woocommerce-minimum-maximum-quantity' );
							$limits = '';
						}

						return sprintf( '<mark class="%s tips" data-tip="%s">%s</mark> %s', $class, $tip, $tip, $limits );

					},
					'column_override_val' => function ( $item ) {

						if ( $item['override_val'] == 'yes' ) {
							$class  = 'show';
							$tip    = __( 'Yes', 'yith-woocommerce-minimum-maximum-quantity' );
							$min    = get_term_meta( $item['ID'], '_ywmmq_tag_minimum_value', true );
							$max    = get_term_meta( $item['ID'], '_ywmmq_tag_maximum_value', true );
							$limits = sprintf( __( 'Min.: %d - Max.: %d', 'yith-woocommerce-minimum-maximum-quantity' ), $min, $max );
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
								$wpdb->query( "UPDATE {$wpdb->prefix}termmeta
                                           SET meta_value='no'
                                           WHERE ( meta_key = '_ywmmq_tag_exclusion' OR meta_key = '_ywmmq_tag_quantity_limit_override' OR meta_key = '_ywmmq_tag_value_limit_override') AND term_id IN ( $ids )"
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
				'section' => $_GET['section']
			);

			$list_url = esc_url( add_query_arg( $list_query_args, admin_url( 'admin.php' ) ) );

			if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], basename( __FILE__ ) ) ) {

				$item_valid = $this->validate_fields( $_POST );

				if ( $item_valid !== true ) {

					$notice = $item_valid;

				} else {

					$tag_ids      = ( ! is_array( $_POST['tag_ids'] ) ) ? explode( ',', $_POST['tag_ids'] ) : $_POST['tag_ids'];
					$exclusion    = isset( $_POST['_ywmmq_tag_exclusion'] ) ? 'yes' : 'no';
					$override_qty = isset( $_POST['_ywmmq_tag_quantity_limit_override'] ) ? 'yes' : 'no';
					$minimum_qty  = isset( $_POST['_ywmmq_tag_minimum_quantity'] ) ? $_POST['_ywmmq_tag_minimum_quantity'] : 0;
					$maximum_qty  = isset( $_POST['_ywmmq_tag_maximum_quantity'] ) ? $_POST['_ywmmq_tag_maximum_quantity'] : 0;
					$step_qty     = isset( $_POST['_ywmmq_tag_step_quantity'] ) ? $_POST['_ywmmq_tag_step_quantity'] : 1;
					$override_val = isset( $_POST['_ywmmq_tag_value_limit_override'] ) ? 'yes' : 'no';
					$minimum_val  = isset( $_POST['_ywmmq_tag_minimum_value'] ) ? $_POST['_ywmmq_tag_minimum_value'] : 0;
					$maximum_val  = isset( $_POST['_ywmmq_tag_maximum_value'] ) ? $_POST['_ywmmq_tag_maximum_value'] : 0;

					foreach ( $tag_ids as $tag_id ) {

						update_term_meta( $tag_id, '_ywmmq_tag_exclusion', $exclusion );
						update_term_meta( $tag_id, '_ywmmq_tag_quantity_limit_override', $override_qty );
						update_term_meta( $tag_id, '_ywmmq_tag_minimum_quantity', $minimum_qty );
						update_term_meta( $tag_id, '_ywmmq_tag_maximum_quantity', $maximum_qty );
						update_term_meta( $tag_id, '_ywmmq_tag_step_quantity', $step_qty );
						update_term_meta( $tag_id, '_ywmmq_tag_value_limit_override', $override_val );
						update_term_meta( $tag_id, '_ywmmq_tag_minimum_value', $minimum_val );
						update_term_meta( $tag_id, '_ywmmq_tag_maximum_value', $maximum_val );

					}

					if ( ! empty( $_POST['insert'] ) ) {

						$message = sprintf( _n( '%s tag added successfully', '%s tags added successfully', count( $tag_ids ), 'yith-woocommerce-minimum-maximum-quantity' ), count( $tag_ids ) );

					} elseif ( ! empty( $_POST['edit'] ) ) {

						$message = __( 'Tag updated successfully', 'yith-woocommerce-minimum-maximum-quantity' );

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
				'step_qty'     => 1,
				'override_val' => '',
				'minimum_val'  => 0,
				'maximum_val'  => 0,
			);

			if ( 'delete' === $table->current_action() ) {
				$ids     = isset( $_GET['id'] ) ? ( ( ! is_array( $_GET['id'] ) ) ? explode( ',', $_GET['id'] ) : $_GET['id'] ) : '';
				$items   = $ids != '' ? count( $ids ) : 0;
				$message = sprintf( _n( '%s tag removed successfully', '%s tags removed successfully', $items, 'yith-woocommerce-minimum-maximum-quantity' ), $items );
			}

			if ( isset( $_GET['id'] ) && ! empty( $_GET['action'] ) && ( 'edit' == $_GET['action'] ) ) {

				$item          = array(
					'ID'           => $_GET['id'],
					'excluded'     => get_term_meta( $_GET['id'], '_ywmmq_tag_exclusion', true ),
					'override_qty' => get_term_meta( $_GET['id'], '_ywmmq_tag_quantity_limit_override', true ),
					'minimum_qty'  => get_term_meta( $_GET['id'], '_ywmmq_tag_minimum_quantity', true ),
					'maximum_qty'  => get_term_meta( $_GET['id'], '_ywmmq_tag_maximum_quantity', true ),
					'step_qty'     => get_term_meta( $_GET['id'], '_ywmmq_tag_step_quantity', true ),
					'override_val' => get_term_meta( $_GET['id'], '_ywmmq_tag_value_limit_override', true ),
					'minimum_val'  => get_term_meta( $_GET['id'], '_ywmmq_tag_minimum_value', true ),
					'maximum_val'  => get_term_meta( $_GET['id'], '_ywmmq_tag_maximum_value', true )
				);
				$value         = $_GET['id'];
				$tag           = get_term( $_GET['id'], 'product_tag' );
				$data_selected = wp_kses_post( $tag->name );
				$data_selected = array( $value => $data_selected );

			}

			?>
            <div class="wrap">
                <div class="icon32 icon32-posts-post" id="icon-edit"><br /></div>
                <h1><?php _e( 'Tag Rule list', 'yith-woocommerce-minimum-maximum-quantity' ); ?>

					<?php if ( empty( $_GET['action'] ) || ( 'insert' !== $_GET['action'] && 'edit' !== $_GET['action'] ) ) : ?>
						<?php $query_args = array(
							'page'    => $_GET['page'],
							'tab'     => $_GET['tab'],
							'section' => $_GET['section'],
							'action'  => 'insert'
						);
						$add_form_url     = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
						?>
                        <a class="page-title-action" href="<?php echo $add_form_url; ?>"><?php _e( 'Add Tags', 'yith-woocommerce-minimum-maximum-quantity' ); ?></a>
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
                                    <label for="tag_ids"><?php echo ( 'edit' == $_GET['action'] ) ? __( 'Tag to edit', 'yith-woocommerce-minimum-maximum-quantity' ) : __( 'Tags to add', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
                                </th>
                                <td class="forminp">
									<?php if ( 'edit' == $_GET['action'] ) : ?>
                                        <input id="tag_id" name="tag_ids" type="hidden" value="<?php echo esc_attr( $item['ID'] ); ?>" />
									<?php endif; ?>

									<?php

									$select_args = array(
										'class'            => 'wc-product-search',
										'id'               => 'tag_ids',
										'name'             => 'tag_ids',
										'data-placeholder' => __( 'Search for a tag&hellip;', 'yith-woocommerce-minimum-maximum-quantity' ),
										'data-allow_clear' => false,
										'data-selected'    => $data_selected,
										'data-multiple'    => ( 'edit' == $_GET['action'] ) ? false : true,
										'data-action'      => 'ywmmq_json_search_product_tags',
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
                                    <label for="_ywmmq_tag_exclusion"><?php _e( 'Exclude tag', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
                                </th>
                                <td class="forminp forminp-checkbox">
                                    <input
                                        id="_ywmmq_tag_exclusion"
                                        name="_ywmmq_tag_exclusion"
                                        type="checkbox"
										<?php echo ( esc_attr( $item['excluded'] ) == 'yes' ) ? 'checked="checked"' : ''; ?>
                                    />
                                    <span class="description"><?php echo ( 'edit' == $_GET['action'] ) ? __( 'Do not apply restrictions to products with this tag', 'yith-woocommerce-minimum-maximum-quantity' ) : __( 'Do not apply restrictions to products with the selected tags', 'yith-woocommerce-minimum-maximum-quantity' ) ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="_ywmmq_tag_quantity_limit_override"><?php _e( 'Override tag restrictions', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
                                </th>
                                <td class="forminp forminp-checkbox">
                                    <input
                                        id="_ywmmq_tag_quantity_limit_override"
                                        name="_ywmmq_tag_quantity_limit_override"
                                        type="checkbox"
										<?php echo ( esc_attr( $item['override_qty'] ) == 'yes' ) ? 'checked="checked"' : ''; ?>
                                    />
                                    <span class="description"><?php _e( 'Global tag quantity restrictions will be overridden by current ones. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ) ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="_ywmmq_tag_minimum_quantity"><?php _e( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ) ?></label>
                                </th>
                                <td class="forminp forminp-number">
                                    <input
                                        id="_ywmmq_tag_minimum_quantity"
                                        name="_ywmmq_tag_minimum_quantity"
                                        type="number"
                                        value="<?php echo esc_attr( $item['minimum_qty'] ); ?>"
                                        min="0"
                                        required="required"
                                    />
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="_ywmmq_tag_maximum_quantity"><?php _e( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ) ?></label>
                                </th>
                                <td class="forminp forminp-number">
                                    <input
                                        id="_ywmmq_tag_maximum_quantity"
                                        name="_ywmmq_tag_maximum_quantity"
                                        type="number"
                                        value="<?php echo esc_attr( $item['maximum_qty'] ); ?>"
                                        min="0"
                                        required="required"
                                    />
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="_ywmmq_tag_step_quantity"><?php _e( 'Tag quantity groups of', 'yith-woocommerce-minimum-maximum-quantity' ) ?></label>
                                </th>
                                <td class="forminp forminp-number">
                                    <input
                                        id="_ywmmq_tag_step_quantity"
                                        name="_ywmmq_tag_step_quantity"
                                        type="number"
                                        value="<?php echo esc_attr( $item['step_qty'] ); ?>"
                                        min="1"
                                        required="required"
                                    />
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="_ywmmq_tag_value_limit_override"><?php _e( 'Override tag restrictions', 'yith-woocommerce-minimum-maximum-quantity' ); ?></label>
                                </th>
                                <td class="forminp forminp-checkbox">
                                    <input
                                        id="_ywmmq_tag_value_limit_override"
                                        name="_ywmmq_tag_value_limit_override"
                                        type="checkbox"
										<?php echo ( esc_attr( $item['override_val'] ) == 'yes' ) ? 'checked="checked"' : ''; ?>
                                    />
                                    <span class="description"><?php _e( 'Global tag spend restrictions will be overridden by current ones. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ) ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="_ywmmq_tag_minimum_value"><?php _e( 'Minimum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ) ?> (<?php echo get_woocommerce_currency_symbol(); ?>)</label>
                                </th>
                                <td class="forminp forminp-number">
                                    <input
                                        id="_ywmmq_tag_minimum_value"
                                        name="_ywmmq_tag_minimum_value"
                                        type="text"
                                        class="wc_input_price"
                                        value="<?php echo esc_attr( $item['minimum_val'] ); ?>"
                                        required="required"
                                    />
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="_ywmmq_tag_maximum_value"><?php _e( 'Maximum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ) ?> (<?php echo get_woocommerce_currency_symbol(); ?>)</label>
                                </th>
                                <td class="forminp forminp-number">
                                    <input
                                        id="_ywmmq_tag_maximum_value"
                                        name="_ywmmq_tag_maximum_value"
                                        type="text"
                                        class="wc_input_price"
                                        value="<?php echo esc_attr( $item['maximum_val'] ); ?>"
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
                            value="<?php echo( ( 'insert' == $_GET['action'] ) ? __( 'Add tag rule', 'yith-woocommerce-minimum-maximum-quantity' ) : __( 'Update tag rule', 'yith-woocommerce-minimum-maximum-quantity' ) ); ?>"
                            class="button-primary"
                        />
                        <a class="button-secondary" href="<?php echo $list_url; ?>"><?php _e( 'Return to rule list', 'yith-woocommerce-minimum-maximum-quantity' ); ?></a>
                    </form>

				<?php else : ?>

                    <form id="custom-table" method="GET" action="<?php echo $list_url; ?>">
                        <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
                        <input type="hidden" name="tab" value="<?php echo $_GET['tab']; ?>" />
                        <input type="hidden" name="section" value="<?php echo $_GET['section']; ?>" />
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

			if ( empty( $item['tag_ids'] ) ) {
				$messages[] = __( 'Select at least one tag', 'yith-woocommerce-minimum-maximum-quantity' );
			}

			if ( empty( $item['_ywmmq_tag_quantity_limit_override'] ) && empty( $item['_ywmmq_tag_value_limit_override'] ) && empty( $item['_ywmmq_tag_exclusion'] ) ) {
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

			if ( 'yith-plugins_page_yith-wc-min-max-qty' == get_current_screen()->id && ( isset( $_GET['tab'] ) && $_GET['tab'] == 'bulk' ) && ( isset( $_GET['section'] ) && $_GET['section'] == 'tags' ) && ( ! isset( $_GET['action'] ) || ( $_GET['action'] != 'edit' && $_GET['action'] != 'insert' ) ) ) {

				$option = 'per_page';

				$args = array(
					'label'   => __( 'Tags', 'yith-woocommerce-minimum-maximum-quantity' ),
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

		/**
		 * Get tag name
		 *
		 * @since   1.0.0
		 *
		 * @param   $x
		 * @param   $taxonomy_types
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function json_search_product_tags( $x = '', $taxonomy_types = array( 'product_tag' ) ) {

			global $wpdb;

			$term = (string) urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );
			$term = '%' . $term . '%';

			$query_tag = $wpdb->prepare( "SELECT {$wpdb->terms}.term_id,{$wpdb->terms}.name, {$wpdb->terms}.slug
                                   FROM {$wpdb->terms} INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
                                   WHERE {$wpdb->term_taxonomy}.taxonomy IN (%s) AND {$wpdb->terms}.slug LIKE %s", implode( ',', $taxonomy_types ), $term );

			$product_tags = $wpdb->get_results( $query_tag );

			$to_json = array();

			foreach ( $product_tags as $product_tag ) {

				$to_json[ $product_tag->term_id ] = sprintf( '#%s &ndash; %s', $product_tag->term_id, $product_tag->name );

			}

			wp_send_json( $to_json );

		}

	}

	/**
	 * Unique access to instance of YWMMQ_Tags_Bulk_Ops class
	 *
	 * @return \YWMMQ_Tags_Bulk_Ops
	 */
	function YWMMQ_Tags_Bulk_Ops() {

		return YWMMQ_Tags_Bulk_Ops::get_instance();

	}

	new YWMMQ_Tags_Bulk_Ops();
}