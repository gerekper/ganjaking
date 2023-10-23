<?php
/**
 * This file belongs to the YITH Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\MinimumMaximumQuantity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWMMQ_Bulk_Table' ) ) {

	/**
	 * Displays the rule table in YWMMQ plugin admin tab
	 *
	 * @class   YWMMQ_Bulk_Table
	 * @since   2.0.0
	 * @author  YITH <plugins@yithemes.com>
	 *
	 * @package YITH
	 */
	class YWMMQ_Bulk_Table {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'init' ), 25 );
		}

		/**
		 * Init page
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function init() {
			add_action( 'ywmmq_bulk_operations', array( $this, 'output' ) );
			add_filter( 'set-screen-option', array( $this, 'set_options' ), 10, 3 );
			add_action( 'current_screen', array( $this, 'add_options' ) );
		}

		/**
		 * Outputs the rule table template with insert form in plugin options panel
		 *
		 * @return  void
		 * @since   1.5.4
		 */
		public function output() {

			global $wpdb;

			$table = new YITH_Custom_Table(
				array(
					'singular' => esc_html__( 'item', 'yith-woocommerce-minimum-maximum-quantity' ),
					'plural'   => esc_html__( 'items', 'yith-woocommerce-minimum-maximum-quantity' ),
				)
			);

			$message     = array();
			$fields      = array();
			$object_name = '';
			$posted      = $_POST;//phpcs:ignore
			$getted      = $_GET; //phpcs:ignore

			if ( ! empty( $posted['nonce'] ) && wp_verify_nonce( $posted['nonce'], basename( __FILE__ ) ) ) {

				$item_type = $posted['item_type'];
				$item_ids  = $posted[ $item_type . '_ids' ];

				if ( ! empty( $item_ids ) ) {

					$exclusion    = isset( $posted['_exclusion'] ) ? 'yes' : 'no';
					$override_qty = isset( $posted['_quantity_limit_override'] ) ? 'yes' : 'no';
					$minimum_qty  = isset( $posted['_minimum_quantity'] ) ? $posted['_minimum_quantity'] : 0;
					$maximum_qty  = isset( $posted['_maximum_quantity'] ) ? $posted['_maximum_quantity'] : 0;
					$step_qty     = isset( $posted['_step_quantity'] ) ? $posted['_step_quantity'] : 1;
					$override_val = '';
					$minimum_val  = '';
					$maximum_val  = '';

					if ( 'product' !== $item_type ) {
						$override_val = isset( $posted['_value_limit_override'] ) ? 'yes' : 'no';
						$minimum_val  = isset( $posted['_minimum_value'] ) ? $posted['_minimum_value'] : 0;
						$maximum_val  = isset( $posted['_maximum_value'] ) ? $posted['_maximum_value'] : 0;
					}

					$object_ids = ( ! is_array( $item_ids ) ) ? explode( ',', $item_ids ) : $item_ids;

					foreach ( $object_ids as $object_id ) {

						if ( 'product' === $item_type ) {
							$product = wc_get_product( $object_id );
							$product->update_meta_data( '_ywmmq_product_exclusion', $exclusion );
							$product->update_meta_data( '_ywmmq_product_quantity_limit_override', $override_qty );
							$product->update_meta_data( '_ywmmq_product_minimum_quantity', $minimum_qty );
							$product->update_meta_data( '_ywmmq_product_maximum_quantity', $maximum_qty );
							$product->update_meta_data( '_ywmmq_product_step_quantity', $step_qty );
							$product->save();
						} else {
							update_term_meta( $object_id, '_ywmmq_' . $item_type . '_exclusion', $exclusion );
							update_term_meta( $object_id, '_ywmmq_' . $item_type . '_quantity_limit_override', $override_qty );
							update_term_meta( $object_id, '_ywmmq_' . $item_type . '_minimum_quantity', $minimum_qty );
							update_term_meta( $object_id, '_ywmmq_' . $item_type . '_maximum_quantity', $maximum_qty );
							update_term_meta( $object_id, '_ywmmq_' . $item_type . '_step_quantity', $step_qty );
							update_term_meta( $object_id, '_ywmmq_' . $item_type . '_value_limit_override', $override_val );
							update_term_meta( $object_id, '_ywmmq_' . $item_type . '_minimum_value', $minimum_val );
							update_term_meta( $object_id, '_ywmmq_' . $item_type . '_maximum_value', $maximum_val );
						}
					}

					if ( ! empty( $_POST['insert'] ) ) {

						$singular = esc_html__( '1 rule added successfully', 'yith-woocommerce-minimum-maximum-quantity' );
						/* translators: %s: number of rules added */
						$plural  = sprintf( esc_html__( '%s rules added successfully', 'yith-woocommerce-minimum-maximum-quantity' ), count( $object_ids ) );
						$message = array(
							'text' => count( $object_ids ) === 1 ? $singular : $plural,
							'type' => 'success',
						);

					} elseif ( ! empty( $_POST['edit'] ) ) {

						$message = array(
							'text' => esc_html__( 'Rule updated successfully', 'yith-woocommerce-minimum-maximum-quantity' ),
							'type' => 'success',
						);

					}
				}
			}

			if ( ! empty( $getted['action'] ) && 'delete' !== $getted['action'] ) {

				$item = $this->get_default_values();

				if ( isset( $getted['id'] ) && ( 'edit' === $getted['action'] ) ) {

					switch ( $getted['item_type'] ) {
						case 'category':
						case 'tag':
							$item = array(
								'ID'                       => $getted['id'],
								'_exclusion'               => get_term_meta( $getted['id'], '_ywmmq_' . $getted['item_type'] . '_exclusion', true ),
								'_quantity_limit_override' => get_term_meta( $getted['id'], '_ywmmq_' . $getted['item_type'] . '_quantity_limit_override', true ),
								'_minimum_quantity'        => get_term_meta( $getted['id'], '_ywmmq_' . $getted['item_type'] . '_minimum_quantity', true ),
								'_maximum_quantity'        => get_term_meta( $getted['id'], '_ywmmq_' . $getted['item_type'] . '_maximum_quantity', true ),
								'_step_quantity'           => get_term_meta( $getted['id'], '_ywmmq_' . $getted['item_type'] . '_step_quantity', true ),
								'_value_limit_override'    => get_term_meta( $getted['id'], '_ywmmq_' . $getted['item_type'] . '_value_limit_override', true ),
								'_minimum_value'           => get_term_meta( $getted['id'], '_ywmmq_' . $getted['item_type'] . '_minimum_value', true ),
								'_maximum_value'           => get_term_meta( $getted['id'], '_ywmmq_' . $getted['item_type'] . '_maximum_value', true ),
							);

							if ( 'category' === $getted['item_type'] ) {
								$category    = get_term( $getted['id'], 'product_cat' );
								$object_name = $category->name;
							} else {
								$tag         = get_term( $getted['id'], 'product_tag' );
								$object_name = $tag->name;
							}
							break;
						default:
							$product     = wc_get_product( $getted['id'] );
							$item        = array(
								'ID'                       => $getted['id'],
								'_exclusion'               => $product->get_meta( '_ywmmq_product_exclusion' ),
								'_quantity_limit_override' => $product->get_meta( '_ywmmq_product_quantity_limit_override' ),
								'_minimum_quantity'        => $product->get_meta( '_ywmmq_product_minimum_quantity' ),
								'_maximum_quantity'        => $product->get_meta( '_ywmmq_product_maximum_quantity' ),
								'_step_quantity'           => $product->get_meta( '_ywmmq_product_step_quantity' ),
								'_value_limit_override'    => '',
								'_minimum_value'           => '',
								'_maximum_value'           => '',
							);
							$object_name = $product->get_formatted_name();
					}
				}

				$fields = $this->get_fields( isset( $getted['item_type'] ) ? $getted['item_type'] : '', $item, $object_name, $getted['action'] );

			}

			$table->options = array(
				'select_table'     => "(
				                         (SELECT 
				                        a.ID, 
				                        a.post_title AS name, 
				                        MAX(CASE WHEN b.meta_key = '_ywmmq_product_exclusion' THEN b.meta_value ELSE NULL END) AS excluded,
										MAX(CASE WHEN b.meta_key = '_ywmmq_product_quantity_limit_override' THEN b.meta_value ELSE NULL END) AS override_qty,
										'' AS override_val,
				                        'product' AS item_type
										FROM $wpdb->posts a INNER JOIN $wpdb->postmeta b ON a.ID = b.post_id
										WHERE a.post_type = 'product' AND ( b.meta_key = '_ywmmq_product_exclusion' OR b.meta_key = '_ywmmq_product_quantity_limit_override' ) AND b.meta_value = 'yes'
										GROUP BY a.ID)
										UNION
										(SELECT
										a.term_id AS ID, 
										a.name, 
										MAX(CASE WHEN c.meta_key = '_ywmmq_category_exclusion' THEN c.meta_value ELSE NULL END) AS excluded,
										MAX(CASE WHEN c.meta_key = '_ywmmq_category_quantity_limit_override' THEN c.meta_value ELSE NULL END) AS override_qty,
										MAX(CASE WHEN c.meta_key = '_ywmmq_category_value_limit_override' THEN c.meta_value ELSE NULL END) AS override_val,
										'category' AS item_type
										FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
										WHERE b.taxonomy = 'product_cat' AND ( c.meta_key = '_ywmmq_category_exclusion' OR c.meta_key = '_ywmmq_category_quantity_limit_override' OR c.meta_key = '_ywmmq_category_value_limit_override') AND c.meta_value = 'yes'
										GROUP BY a.term_id)
										UNION
										(SELECT
										a.term_id AS ID, 
										a.name, 
										MAX(CASE WHEN c.meta_key = '_ywmmq_tag_exclusion' THEN c.meta_value ELSE NULL END) AS excluded,
										MAX(CASE WHEN c.meta_key = '_ywmmq_tag_quantity_limit_override' THEN c.meta_value ELSE NULL END) AS override_qty,
										MAX(CASE WHEN c.meta_key = '_ywmmq_tag_value_limit_override' THEN c.meta_value ELSE NULL END) AS override_val,
 										'tag' AS item_type
										FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
										WHERE b.taxonomy = 'product_tag' AND ( c.meta_key = '_ywmmq_tag_exclusion' OR c.meta_key = '_ywmmq_tag_quantity_limit_override' OR c.meta_key = '_ywmmq_tag_value_limit_override') AND c.meta_value = 'yes' 
										GROUP BY a.term_id) 
										) AS items",
				'select_columns'   => array(
					'ID',
					'name',
					'item_type',
					'concat(ID, "-", item_type) AS idtype',
					'excluded',
					'override_qty',
					'override_val',
				),
				'select_where'     => '',
				'select_group'     => '',
				'select_order'     => 'name',
				'select_order_dir' => 'ASC',
				'per_page_option'  => 'items_per_page',
				'search_where'     => array(
					'name',
				),
				'count_table'      => "(
				                        (SELECT 
				                        a.ID, 
				                        a.post_title AS name, 
				                        MAX(CASE WHEN b.meta_key = '_ywmmq_product_exclusion' THEN b.meta_value ELSE NULL END) AS excluded,
										MAX(CASE WHEN b.meta_key = '_ywmmq_product_quantity_limit_override' THEN b.meta_value ELSE NULL END) AS override_qty,
										'' AS override_val,
				                        'product' AS item_type
										FROM $wpdb->posts a INNER JOIN $wpdb->postmeta b ON a.ID = b.post_id
										WHERE a.post_type = 'product' AND ( b.meta_key = '_ywmmq_product_exclusion' OR b.meta_key = '_ywmmq_product_quantity_limit_override' ) AND b.meta_value = 'yes'
										GROUP BY a.ID)
										UNION
										(SELECT
										a.term_id AS ID, 
										a.name, 
										MAX(CASE WHEN c.meta_key = '_ywmmq_category_exclusion' THEN c.meta_value ELSE NULL END) AS excluded,
										MAX(CASE WHEN c.meta_key = '_ywmmq_category_quantity_limit_override' THEN c.meta_value ELSE NULL END) AS override_qty,
										MAX(CASE WHEN c.meta_key = '_ywmmq_category_value_limit_override' THEN c.meta_value ELSE NULL END) AS override_val,
										'category' AS item_type
										FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
										WHERE b.taxonomy = 'product_cat' AND ( c.meta_key = '_ywmmq_category_exclusion' OR c.meta_key = '_ywmmq_category_quantity_limit_override' OR c.meta_key = '_ywmmq_category_value_limit_override') AND c.meta_value = 'yes'
										GROUP BY a.term_id)
										UNION
										(SELECT
										a.term_id AS ID, 
										a.name, 
										MAX(CASE WHEN c.meta_key = '_ywmmq_tag_exclusion' THEN c.meta_value ELSE NULL END) AS excluded,
										MAX(CASE WHEN c.meta_key = '_ywmmq_tag_quantity_limit_override' THEN c.meta_value ELSE NULL END) AS override_qty,
										MAX(CASE WHEN c.meta_key = '_ywmmq_tag_value_limit_override' THEN c.meta_value ELSE NULL END) AS override_val,
 										'tag' AS item_type
										FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
										WHERE b.taxonomy = 'product_tag' AND ( c.meta_key = '_ywmmq_tag_exclusion' OR c.meta_key = '_ywmmq_tag_quantity_limit_override' OR c.meta_key = '_ywmmq_tag_value_limit_override') AND c.meta_value = 'yes' 
										GROUP BY a.term_id)
										) AS items",
				'count_where'      => '',
				'key_column'       => 'idtype',
				'view_columns'     => array(
					'cb'           => '<input type="checkbox" />',
					'item_name'    => esc_html__( 'Item Name', 'yith-woocommerce-minimum-maximum-quantity' ),
					'item_type'    => esc_html__( 'Item Type', 'yith-woocommerce-minimum-maximum-quantity' ),
					'excluded'     => esc_html__( 'Excluded', 'yith-woocommerce-minimum-maximum-quantity' ),
					'override_qty' => esc_html__( 'Override Quantity Restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
					'override_val' => esc_html__( 'Override Spend Restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				),
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'item_name' => array( 'name', true ),
				),
				'custom_columns'   => array(
					'column_item_name'    => function ( $item, $me ) {
						return $this->item_name_column( $item, $me );
					},
					'column_item_type'    => function ( $item ) {
						return $this->item_type_column( $item['item_type'] );
					},
					'column_excluded'     => function ( $item ) {
						return $this->excluded_column( $item['excluded'] );
					},
					'column_override_qty' => function ( $item ) {
						return $this->override_qty_column( $item );
					},
					'column_override_val' => function ( $item ) {
						return $this->override_val_column( $item );
					},

				),
				'bulk_actions'     => array(
					'actions'   => array(
						'delete' => esc_html__( 'Remove from list', 'yith-woocommerce-minimum-maximum-quantity' ),
					),
					'functions' => array(
						'function_delete' => function () {

							$ids = isset( $_GET['id'] ) ? $_GET['id'] : array(); //phpcs:ignore
							if ( ! is_array( $ids ) ) {
								$ids = explode( ',', $ids );
							}

							if ( ! empty( $ids ) ) {
								foreach ( $ids as $id ) {

									$data      = explode( '-', $id );
									$item_type = isset( $_GET['item_type'] ) ? $_GET['item_type'] : '';//phpcs:ignore
									if ( ( isset( $data[1] ) && 'product' === $data[1] ) || ( 'product' === $item_type ) ) {
										$product = wc_get_product( $data[0] );
										$product->delete_meta_data( '_ywmmq_product_exclusion' );
										$product->delete_meta_data( '_ywmmq_product_quantity_limit_override' );
										$product->delete_meta_data( '_ywmmq_product_minimum_quantity' );
										$product->delete_meta_data( '_ywmmq_product_maximum_quantity' );
										$product->delete_meta_data( '_ywmmq_product_step_quantity' );
										$product->save();
									} else {
										if ( ! isset( $data[1] ) && '' === $item_type ) {
											return;
										}

										$item_type = '' === $item_type ? $data[1] : $item_type;

										delete_term_meta( $data[0], '_ywmmq_' . $item_type . '_exclusion' );
										delete_term_meta( $data[0], '_ywmmq_' . $item_type . '_quantity_limit_override' );
										delete_term_meta( $data[0], '_ywmmq_' . $item_type . '_minimum_quantity' );
										delete_term_meta( $data[0], '_ywmmq_' . $item_type . '_maximum_quantity' );
										delete_term_meta( $data[0], '_ywmmq_' . $item_type . '_step_quantity' );
										delete_term_meta( $data[0], '_ywmmq_' . $item_type . '_value_limit_override' );
										delete_term_meta( $data[0], '_ywmmq_' . $item_type . '_minimum_value' );
										delete_term_meta( $data[0], '_ywmmq_' . $item_type . '_maximum_value' );
									}
								}
							}
						},
					),
				),
				'wp_cache_option'  => 'ywmmq_rules',
			);
			$table->prepare_items();

			if ( 'delete' === $table->current_action() ) {

				$ids      = $getted['id'];
				$deleted  = count( is_array( $ids ) ? $ids : explode( ',', $ids ) );
				$singular = esc_html__( '1 rule removed successfully', 'yith-woocommerce-minimum-maximum-quantity' );
				/* translators: number of rules deleted */
				$plural  = sprintf( esc_html__( '%s rules removed successfully', 'yith-woocommerce-minimum-maximum-quantity' ), $deleted );
				$message = array(
					'text' => 1 === $deleted ? $singular : $plural,
					'type' => 'success',
				);

			}

			$this->print_template( $table, $fields, $message );

		}

		/**
		 * Print table template
		 *
		 * @param YITH_Custom_Table $table   The table object.
		 * @param array             $fields  Fields array.
		 * @param array             $message Messages.
		 *
		 * @return  void
		 * @since   1.5.4
		 */
		private function print_template( $table, $fields, $message ) {
			$getted          = $_GET;//phpcs:ignore
			$list_query_args = array(
				'page' => $getted['page'],
				'tab'  => $getted['tab'],
			);

			$list_url = esc_url( add_query_arg( $list_query_args, admin_url( 'admin.php' ) ) );

			?>
			<div class="yith-plugin-fw-wp-page-wrapper ywmmq-rules">
				<div class="wrap">
					<h1 class="wp-heading-inline"><?php esc_html_e( 'Rules list', 'yith-woocommerce-minimum-maximum-quantity' ); ?></h1>
					<?php if ( empty( $getted['action'] ) || ( 'insert' !== $getted['action'] && 'edit' !== $getted['action'] ) ) : ?>
						<?php
						$query_args   = array_merge( $list_query_args, array( 'action' => 'insert' ) );
						$add_form_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
						?>
						<a class="page-title-action yith-add-button" href="<?php echo esc_attr( $add_form_url ); ?>"><?php esc_html_e( 'Add rule', 'yith-woocommerce-minimum-maximum-quantity' ); ?></a>
					<?php endif; ?>
					<hr class="wp-header-end" />
					<?php if ( $message ) : ?>
						<div class="notice notice-<?php echo esc_attr( $message['type'] ); ?> is-dismissible"><p><?php echo esc_attr( $message['text'] ); ?></p></div>
					<?php endif; ?>
					<?php if ( ! empty( $getted['action'] ) && ( 'insert' === $getted['action'] || 'edit' === $getted['action'] ) ) : ?>

						<?php
						$query_args = array_merge( $list_query_args, array() );

						if ( isset( $getted['return_page'] ) ) {
							$query_args['paged'] = $getted['return_page'];
						}
						?>

						<form id="form" method="POST" action="<?php echo esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) ); ?>">
							<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( basename( __FILE__ ) ) ); ?>" />
							<table class="form-table ywmmq-table">
								<tbody>
								<?php foreach ( $fields as $field ) : ?>
									<tr valign="top" class="yith-plugin-fw-panel-wc-row <?php echo esc_attr( $field['type'] ); ?> <?php echo esc_attr( $field['name'] ); ?>">
										<th scope="row" class="titledesc">
											<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_attr( $field['title'] ); ?></label>
										</th>
										<td class="forminp forminp-<?php echo esc_attr( $field['type'] ); ?>">
											<?php yith_plugin_fw_get_field( $field, true ); ?>
											<?php if ( isset( $field['desc'] ) && '' !== $field['desc'] ) : ?>
												<span class="description"><?php echo wp_kses_post( $field['desc'] ); ?></span>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
							<input id="<?php echo esc_attr( $getted['action'] ); ?>" name="<?php echo esc_attr( $getted['action'] ); ?>" type="submit" class="<?php echo esc_attr( 'insert' === $getted['action'] ? 'yith-save-button' : 'yith-update-button' ); ?>" value="<?php echo( ( 'insert' === $getted['action'] ) ? esc_html__( 'Add rule', 'yith-woocommerce-minimum-maximum-quantity' ) : esc_html__( 'Update rule', 'yith-woocommerce-minimum-maximum-quantity' ) ); ?>" />
						</form>

					<?php else : ?>

						<form id="custom-table" method="GET" action="<?php esc_attr( $list_url ); ?>">
							<?php $table->search_box( esc_html__( 'Search item', 'yith-woocommerce-minimum-maximum-quantity' ), 'item' ); ?>
							<input type="hidden" name="page" value="<?php echo esc_attr( $getted['page'] ); ?>" />
							<input type="hidden" name="tab" value="<?php echo esc_attr( $getted['tab'] ); ?>" />
							<?php $table->display(); ?>
						</form>

					<?php endif; ?>
					<div class="clear"></div>
				</div>

			</div>
			<?php

		}

		/**
		 * Get field option for current screen
		 *
		 * @param string $type   Item type.
		 * @param array  $item   Data Array.
		 * @param string $name   Option Name.
		 * @param string $action Action name.
		 *
		 * @return  array
		 * @since   1.5.4
		 */
		private function get_fields( $type, $item, $name, $action ) {

			if ( 'edit' === $action ) {

				$item_type = $this->item_type_column( $type );
				$fields    = array(
					array(
						'id'                => 'item_ids',
						'name'              => 'item_ids',
						'type'              => 'text',
						'custom_attributes' => 'disabled',
						'value'             => $name,
						/* translators: %s item type */
						'title'             => sprintf( esc_html__( '%s to edit', 'yith-woocommerce-minimum-maximum-quantity' ), $item_type ),
					),
					array(
						'id'    => 'item_id',
						'name'  => $type . '_ids',
						'type'  => 'hidden',
						'value' => $item['ID'],
						'title' => '',
					),
					array(
						'id'    => 'item_type',
						'name'  => 'item_type',
						'type'  => 'hidden',
						'value' => $type,
						'title' => '',
					),
				);
			} else {
				$fields = array(
					array(
						'id'      => 'item_type',
						'name'    => 'item_type',
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'options' => array(
							'product'  => esc_html__( 'Products', 'yith-woocommerce-minimum-maximum-quantity' ),
							'category' => esc_html__( 'Categories', 'yith-woocommerce-minimum-maximum-quantity' ),
							'tag'      => esc_html__( 'Tags', 'yith-woocommerce-minimum-maximum-quantity' ),
						),
						'title'   => esc_html__( 'Item type', 'yith-woocommerce-minimum-maximum-quantity' ),
						'value'   => '',
						'desc'    => esc_html__( 'Choose whether to add specific products, categories or tags to the rules list.', 'yith-woocommerce-minimum-maximum-quantity' ),
					),
					array(
						'id'       => 'product_ids',
						'name'     => 'product_ids',
						'type'     => 'ajax-products',
						'multiple' => true,
						'data'     => array(
							'placeholder' => esc_html__( 'Search products', 'yith-woocommerce-minimum-maximum-quantity' ),
						),
						'title'    => esc_html__( 'Select products', 'yith-woocommerce-minimum-maximum-quantity' ),
						'desc'     => esc_html__( 'Select which products to add in the rules list.', 'yith-woocommerce-minimum-maximum-quantity' ),
					),
					array(
						'id'       => 'category_ids',
						'name'     => 'category_ids',
						'type'     => 'ajax-terms',
						'multiple' => true,
						'data'     => array(
							'placeholder' => esc_html__( 'Search categories', 'yith-woocommerce-minimum-maximum-quantity' ),
							'taxonomy'    => 'product_cat',
						),
						'title'    => esc_html__( 'Select categories', 'yith-woocommerce-minimum-maximum-quantity' ),
						'desc'     => esc_html__( 'Select which product categories to add in the rules list.', 'yith-woocommerce-minimum-maximum-quantity' ),
					),
					array(
						'id'       => 'tag_ids',
						'name'     => 'tag_ids',
						'type'     => 'ajax-terms',
						'multiple' => true,
						'data'     => array(
							'placeholder' => esc_html__( 'Search tags', 'yith-woocommerce-minimum-maximum-quantity' ),
							'taxonomy'    => 'product_tag',
						),
						'title'    => esc_html__( 'Select tags', 'yith-woocommerce-minimum-maximum-quantity' ),
						'desc'     => esc_html__( 'Select which product tags to add in the rules list.', 'yith-woocommerce-minimum-maximum-quantity' ),
					),
				);
			}
			$fields = array_merge( $fields, ywmmq_get_rules_fields( $item, $type ) );

			if ( 'product' === $type ) {
				unset( $fields[8] );
				unset( $fields[9] );
				unset( $fields[10] );
			}

			return $fields;

		}

		/**
		 * Get default values
		 *
		 * @return  array
		 * @since   1.5.4
		 */
		private function get_default_values() {

			return array(
				'ID'                       => 0,
				'_exclusion'               => 'no',
				'_quantity_limit_override' => 'no',
				'_minimum_quantity'        => 0,
				'_maximum_quantity'        => 0,
				'_step_quantity'           => 0,
				'_value_limit_override'    => 'no',
				'_minimum_value'           => 0,
				'_maximum_value'           => 0,
			);

		}

		/**
		 * Add screen options for rules list table template
		 *
		 * @param WP_Screen $current_screen The current screen.
		 *
		 * @return  void
		 * @since   1.5.4
		 */
		public function add_options( $current_screen ) {

			if ( ( 'yith-plugins_page_yith-wc-min-max-qty' === $current_screen->id ) && ( isset( $_GET['tab'] ) && 'bulk' === $_GET['tab'] ) && ( ! isset( $_GET['action'] ) || ( 'edit' !== $_GET['action'] && 'insert' !== $_GET['action'] ) ) ) { //phpcs:ignore

				$option = 'per_page';

				$args = array(
					'label'   => esc_html__( 'Rules', 'yith-woocommerce-minimum-maximum-quantity' ),
					'default' => 10,
					'option'  => 'items_per_page',
				);

				add_screen_option( $option, $args );

			}

		}

		/**
		 * Set screen options for rules list table template
		 *
		 * @param string $status Screen status.
		 * @param string $option Option name.
		 * @param string $value  Option value.
		 *
		 * @return  mixed
		 * @since   1.5.4
		 */
		public function set_options( $status, $option, $value ) {

			return ( 'items_per_page' === $option ) ? $value : $status;

		}

		/**
		 * Print item name with action links in the rules table
		 *
		 * @param array             $item  Rule item.
		 * @param YITH_Custom_Table $table Table object.
		 *
		 * @return  string
		 * @since   1.5.4
		 */
		private function item_name_column( $item, $table ) {

			$getter     = $_GET; //phpcs:ignore
			$query_args = array(
				'page' => $getter['page'],
				'tab'  => $getter['tab'],
				'id'   => $item['ID'],
			);

			if ( isset( $getter['paged'] ) ) {
				$query_args['return_page'] = $getter['paged'];
			}

			$query_args['item_type'] = $item['item_type'];
			$section                 = $item['item_type'];

			$items      = array(
				'product'  => array(
					'edit_label' => esc_html__( 'Edit product', 'yith-woocommerce-minimum-maximum-quantity' ),
					'edit_link'  => esc_url(
						add_query_arg(
							array(
								'post'   => $item['ID'],
								'action' => 'edit',
							),
							admin_url( 'post.php' )
						)
					),
					'view_link'  => get_permalink( $item['ID'] ),
				),
				'category' => array(
					'edit_label' => esc_html__( 'Edit category', 'yith-woocommerce-minimum-maximum-quantity' ),
					'edit_link'  => esc_url(
						add_query_arg(
							array(
								'taxonomy'  => 'product_cat',
								'post_type' => 'product',
								'tag_ID'    => $item['ID'],
								'action'    => 'edit',
							),
							admin_url( 'edit-tags.php' )
						)
					),
					'view_link'  => get_term_link( intval( $item['ID'] ), 'product_cat' ),
				),
				'tag'      => array(
					'edit_label' => esc_html__( 'Edit tag', 'yith-woocommerce-minimum-maximum-quantity' ),
					'edit_link'  => esc_url(
						add_query_arg(
							array(
								'taxonomy'  => 'product_tag',
								'post_type' => 'product',
								'tag_ID'    => $item['ID'],
								'action'    => 'edit',
							),
							admin_url( 'edit-tags.php' )
						)
					),
					'view_link'  => get_term_link( intval( $item['ID'] ), 'product_tag' ),
				),
			);
			$edit_url   = esc_url( add_query_arg( array_merge( $query_args, array( 'action' => 'edit' ) ), admin_url( 'admin.php' ) ) );
			$delete_url = esc_url( add_query_arg( array_merge( $query_args, array( 'action' => 'delete' ) ), admin_url( 'admin.php' ) ) );
			$actions    = array(
				'edit'   => sprintf( '<a href="%s">%s</a>', $edit_url, esc_html__( 'Edit rule', 'yith-woocommerce-minimum-maximum-quantity' ) ),
				'item'   => sprintf( '<a target="_blank" href="%s">%s</a>', $items[ $section ]['edit_link'], $items[ $section ]['edit_label'] ),
				'delete' => sprintf( '<a href="%s">%s</a>', $delete_url, esc_html__( 'Remove from list', 'yith-woocommerce-minimum-maximum-quantity' ) ),
				'view'   => sprintf( '<a target="_blank" href="%s">%s</a>', $items[ $section ]['view_link'], esc_html__( 'View', 'yith-woocommerce-minimum-maximum-quantity' ) ),
			);

			return sprintf( '<strong><a class="row-title" href="%s" data-tip="%s">#%d %s </a></strong> %s', $edit_url, esc_html__( 'Edit rule', 'yith-woocommerce-minimum-maximum-quantity' ), $item['ID'], $item['name'], $table->__call( 'row_actions', array( $actions ) ) );

		}

		/**
		 * Print the item type column in the rule table
		 *
		 * @param string $item_type Item type name.
		 *
		 * @return  string
		 * @since   1.5.4
		 */
		private function item_type_column( $item_type ) {

			$item_types = array(
				'product'  => esc_html__( 'Product', 'yith-woocommerce-minimum-maximum-quantity' ),
				'category' => esc_html__( 'Category', 'yith-woocommerce-minimum-maximum-quantity' ),
				'tag'      => esc_html__( 'Tag', 'yith-woocommerce-minimum-maximum-quantity' ),
			);

			return $item_types[ $item_type ];

		}

		/**
		 * Print the item exclusion column in the rule table
		 *
		 * @param string $excluded Item exclusion.
		 *
		 * @return  string
		 * @since   1.5.4
		 */
		private function excluded_column( $excluded ) {

			if ( 'yes' === $excluded ) {
				$class = 'show';
				$tip   = esc_html__( 'Yes', 'yith-woocommerce-minimum-maximum-quantity' );
			} else {
				$class = 'hide';
				$tip   = esc_html__( 'No', 'yith-woocommerce-minimum-maximum-quantity' );
			}

			return sprintf( '<mark class="%s tips" data-tip="%s">%s</mark>', $class, $tip, $tip );
		}

		/**
		 * Print the item quantity rule column in the rule table
		 *
		 * @param array $item Rule item.
		 *
		 * @return  string
		 * @since   1.5.4
		 */
		private function override_qty_column( $item ) {

			if ( 'yes' === $item['override_qty'] ) {
				$class = 'show';
				$tip   = esc_html__( 'Yes', 'yith-woocommerce-minimum-maximum-quantity' );

				if ( 'product' === $item['item_type'] ) {
					$product = wc_get_product( $item['ID'] );
					$min     = $product->get_meta( '_ywmmq_product_minimum_quantity' );
					$max     = $product->get_meta( '_ywmmq_product_maximum_quantity' );
					$step    = $product->get_meta( '_ywmmq_product_step_quantity' );
				} else {
					$min  = get_term_meta( $item['ID'], '_ywmmq_' . $item['item_type'] . '_minimum_quantity', true );
					$max  = get_term_meta( $item['ID'], '_ywmmq_' . $item['item_type'] . '_maximum_quantity', true );
					$step = get_term_meta( $item['ID'], '_ywmmq_' . $item['item_type'] . '_step_quantity', true );
				}

				/* translators: %1$d minimum value - %2$d maximum value  */
				$limits = sprintf( esc_html__( 'Min.: %1$d - Max.: %2$d', 'yith-woocommerce-minimum-maximum-quantity' ), $min, $max );
				if ( $step > 1 ) {
					/* translators: %d step quantity value  */
					$limits .= ' - ' . sprintf( esc_html__( 'Group of: %d', 'yith-woocommerce-minimum-maximum-quantity' ), $step );
				}
			} else {
				$class  = 'hide';
				$tip    = esc_html__( 'No', 'yith-woocommerce-minimum-maximum-quantity' );
				$limits = '';
			}

			return sprintf( '<mark class="%s tips" data-tip="%s">%s</mark> %s', $class, $tip, $tip, $limits );
		}

		/**
		 * Print the item amount rule column in the rule table
		 *
		 * @param array $item Rule item.
		 *
		 * @return  string
		 * @since   1.5.4
		 */
		private function override_val_column( $item ) {

			if ( 'product' === $item['item_type'] ) {
				return '';
			}

			if ( 'yes' === $item['override_val'] ) {
				$class = 'show';
				$tip   = esc_html__( 'Yes', 'yith-woocommerce-minimum-maximum-quantity' );
				$min   = get_term_meta( $item['ID'], '_ywmmq_' . $item['item_type'] . '_minimum_value', true );
				$max   = get_term_meta( $item['ID'], '_ywmmq_' . $item['item_type'] . '_maximum_value', true );

				/* translators: %1$d minimum value - %2$d maximum value  */
				$limits = sprintf( esc_html__( 'Min.: %1$d - Max.: %2$d', 'yith-woocommerce-minimum-maximum-quantity' ), $min, $max );
			} else {
				$class  = 'hide';
				$tip    = esc_html__( 'No', 'yith-woocommerce-minimum-maximum-quantity' );
				$limits = '';
			}

			return sprintf( '<mark class="%s tips" data-tip="%s">%s</mark> %s', $class, $tip, $tip, $limits );
		}

	}

	new YWMMQ_Bulk_Table();
}
