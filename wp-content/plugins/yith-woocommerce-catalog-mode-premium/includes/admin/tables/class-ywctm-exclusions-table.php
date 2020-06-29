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

if ( ! class_exists( 'YWCTM_Exclusions_Table' ) ) {

	/**
	 * Displays the exclusion table in YWCTM plugin admin tab
	 *
	 * @class   YWCTM_Exclusions_Table
	 * @since   2.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWCTM_Exclusions_Table {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			if ( ! isset( $_GET['sub_tab'] ) || ( isset( $_GET['sub_tab'] ) && 'exclusions-vendors' === $_GET['sub_tab'] ) ) {
				return;
			}

			add_action( 'init', array( $this, 'init' ), 25 );

		}

		/**
		 * Init page
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function init() {

			add_action( 'ywctm_exclusions_items', array( $this, 'output' ) );
			add_filter( 'set-screen-option', array( $this, 'set_options' ), 10, 3 );
			add_action( 'current_screen', array( $this, 'add_options' ) );

		}

		/**
		 * Outputs the exclusion table template with insert form in plugin options panel
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function output() {

			global $wpdb;

			$table = new YITH_Custom_Table(
				array(
					'singular' => esc_html__( 'item', 'yith-woocommerce-catalog-mode' ),
					'plural'   => esc_html__( 'items', 'yith-woocommerce-catalog-mode' ),
				)
			);

			$message     = array();
			$fields      = array();
			$object_name = '';

			if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], basename( __FILE__ ) ) ) {

				$item_type = $_POST['item_type'];
				$item_ids  = $_POST[ $item_type . '_ids' ];

				if ( ! empty( $item_ids ) ) {

					$exclusion_data = array(
						'enable_inquiry_form'         => isset( $_POST['ywctm_enable_inquiry_form'] ) ? 'yes' : 'no',
						'enable_atc_custom_options'   => isset( $_POST['ywctm_enable_atc_custom_options'] ) ? 'yes' : 'no',
						'atc_status'                  => $_POST['ywctm_atc_status'],
						'custom_button'               => $_POST['ywctm_custom_button'],
						'custom_button_url'           => $_POST['ywctm_custom_button_url'],
						'custom_button_loop'          => $_POST['ywctm_custom_button_loop'],
						'custom_button_loop_url'      => $_POST['ywctm_custom_button_loop_url'],
						'enable_price_custom_options' => isset( $_POST['ywctm_enable_price_custom_options'] ) ? 'yes' : 'no',
						'price_status'                => $_POST['ywctm_price_status'],
						'custom_price_text'           => $_POST['ywctm_custom_price_text'],
						'custom_price_text_url'       => $_POST['ywctm_custom_price_text_url'],
					);

					$object_ids = ( ! is_array( $item_ids ) ) ? explode( ',', $item_ids ) : $item_ids;

					foreach ( $object_ids as $object_id ) {

						if ( 'product' === $item_type ) {
							$product = wc_get_product( $object_id );
							$product->add_meta_data( '_ywctm_exclusion_settings' . ywctm_get_vendor_id(), $exclusion_data, true );
							$product->save();
						} else {
							update_term_meta( $object_id, '_ywctm_exclusion_settings' . ywctm_get_vendor_id(), $exclusion_data );
						}
					}

					if ( ! empty( $_POST['insert'] ) ) {

						$singular = esc_html__( '1 exclusion added successfully', 'yith-woocommerce-catalog-mode' );
						/* translators: %s: number of excluisions added */
						$plural  = sprintf( esc_html__( '%s exclusions added successfully', 'yith-woocommerce-catalog-mode' ), count( $object_ids ) );
						$message = array(
							'text' => count( $object_ids ) === 1 ? $singular : $plural,
							'type' => 'success',
						);

					} elseif ( ! empty( $_POST['edit'] ) ) {

						$message = array(
							'text' => esc_html__( 'Exclusion updated successfully', 'yith-woocommerce-catalog-mode' ),
							'type' => 'success',
						);

					}
				}
			}

			if ( ! empty( $_GET['action'] ) && 'delete' !== $_GET['action'] ) {

				$item = $this->get_default_values();

				if ( isset( $_GET['id'] ) && ( 'edit' === $_GET['action'] ) ) {

					switch ( $_GET['item_type'] ) {
						case 'category':
							$exclusion_data = get_term_meta( $_GET['id'], '_ywctm_exclusion_settings' . ywctm_get_vendor_id(), true );
							$item           = array_merge( array( 'ID' => $_GET['id'] ), $exclusion_data );
							$category       = get_term( $_GET['id'], 'product_cat' );
							$object_name    = $category->name;
							break;
						case 'tag':
							$exclusion_data = get_term_meta( $_GET['id'], '_ywctm_exclusion_settings' . ywctm_get_vendor_id(), true );
							$item           = array_merge( array( 'ID' => $_GET['id'] ), $exclusion_data );
							$tag            = get_term( $_GET['id'], 'product_tag' );
							$object_name    = $tag->name;
							break;
						default:
							$product        = wc_get_product( $_GET['id'] );
							$exclusion_data = $product->get_meta( '_ywctm_exclusion_settings' . ywctm_get_vendor_id() );
							$item           = array_merge( array( 'ID' => $_GET['id'] ), $exclusion_data );
							$object_name    = $product->get_formatted_name();
					}
				}

				$fields = $this->get_fields( isset( $_GET['item_type'] ) ? $_GET['item_type'] : '', $item, $object_name, $_GET['action'] );

			}

			$table->options = array(
				'select_table'     => "(
				                        (SELECT a.ID, a.post_title AS name, MAX(CASE WHEN b.meta_key = '_ywctm_exclusion_settings' THEN b.meta_value ELSE NULL END) AS exclusion, 'product' AS item_type
										FROM $wpdb->posts a INNER JOIN $wpdb->postmeta b ON a.ID = b.post_id
										WHERE a.post_type = 'product' AND b.meta_key = '_ywctm_exclusion_settings'
										GROUP BY a.ID)
										UNION
										(SELECT a.term_id AS ID, a.name, MAX(CASE WHEN c.meta_key = '_ywctm_exclusion_settings' THEN c.meta_value ELSE NULL END) AS exclusion, 'category' AS item_type
										FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
										WHERE b.taxonomy = 'product_cat' AND c.meta_key = '_ywctm_exclusion_settings'
										GROUP BY a.term_id)
										UNION
										(SELECT a.term_id AS ID, a.name, MAX(CASE WHEN c.meta_key = '_ywctm_exclusion_settings' THEN c.meta_value ELSE NULL END) AS exclusion, 'tag' AS item_type
										FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
										WHERE b.taxonomy = 'product_tag' AND c.meta_key = '_ywctm_exclusion_settings' 
										GROUP BY a.term_id)
										) AS items",
				'select_columns'   => array(
					'ID',
					'name',
					'exclusion',
					'item_type',
					'concat(ID, "-", item_type) AS idtype',
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
				                        (SELECT a.ID, a.post_title AS name, MAX(CASE WHEN b.meta_key = '_ywctm_exclusion_settings' THEN b.meta_value ELSE NULL END) AS exclusion, 'product' AS item_type
										FROM $wpdb->posts a INNER JOIN $wpdb->postmeta b ON a.ID = b.post_id
										WHERE a.post_type = 'product' AND b.meta_key = '_ywctm_exclusion_settings'
										GROUP BY a.ID)
										UNION
										(SELECT a.term_id AS ID, a.name, MAX(CASE WHEN c.meta_key = '_ywctm_exclusion_settings' THEN c.meta_value ELSE NULL END) AS exclusion, 'category' AS item_type
										FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
										WHERE b.taxonomy = 'product_cat' AND c.meta_key = '_ywctm_exclusion_settings'
										GROUP BY a.term_id)
										UNION
										(SELECT a.term_id AS ID, a.name, MAX(CASE WHEN c.meta_key = '_ywctm_exclusion_settings' THEN c.meta_value ELSE NULL END) AS exclusion, 'tag' AS item_type
										FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
										WHERE b.taxonomy = 'product_tag' AND c.meta_key = '_ywctm_exclusion_settings' 
										GROUP BY a.term_id)
										) AS items",
				'count_where'      => '',
				'key_column'       => 'idtype',
				'view_columns'     => ywctm_set_table_columns(),
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'item_name' => array( 'name', true ),
				),
				'custom_columns'   => array(

					'column_item_name'    => function ( $item, $me ) {
						return ywctm_item_name_column( $item, $me );
					},
					'column_item_type'    => function ( $item ) {
						return ywctm_item_type_column( $item['item_type'] );
					},
					'column_add_to_cart'  => function ( $item ) {
						return ywctm_add_to_cart_column( $item );
					},
					'column_show_price'   => function ( $item ) {
						return ywctm_price_column( $item );
					},
					'column_inquiry_form' => function ( $item ) {
						ywctm_inquiry_form_column( $item );
					},
				),
				'bulk_actions'     => array(
					'actions'   => array(
						'delete' => esc_html__( 'Remove from list', 'yith-woocommerce-catalog-mode' ),
					),
					'functions' => array(
						'function_delete' => function () {

							$ids = isset( $_GET['id'] ) ? $_GET['id'] : array();
							if ( ! is_array( $ids ) ) {
								$ids = explode( ',', $ids );
							}

							if ( ! empty( $ids ) ) {
								foreach ( $ids as $id ) {

									$data = explode( '-', $id );

									if ( ( isset( $data[1] ) && 'product' === $data[1] ) || ( isset( $_GET['item_type'] ) && 'product' === $_GET['item_type'] ) ) {
										$product = wc_get_product( $data[0] );
										$product->delete_meta_data( '_ywctm_exclusion_settings' . ywctm_get_vendor_id() );
										$product->save();
									} else {
										delete_term_meta( $data[0], '_ywctm_exclusion_settings' . ywctm_get_vendor_id() );
									}
								}
							}
						},
					),
				),
			);
			$table->prepare_items();

			if ( 'delete' === $table->current_action() ) {

				$ids      = $_GET['id'];
				$deleted  = count( is_array( $ids ) ? $ids : explode( ',', $ids ) );
				$singular = esc_html__( '1 exclusion removed successfully', 'yith-woocommerce-catalog-mode' );
				/* translators: number of excluisions deleted */
				$plural  = sprintf( esc_html__( '%s exclusions removed successfully', 'yith-woocommerce-catalog-mode' ), $deleted );
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
		 * @param   $table    YITH_Custom_Table
		 * @param   $fields   array
		 * @param   $message  array
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function print_template( $table, $fields, $message ) {

			$list_query_args = array(
				'page'    => $_GET['page'],
				'tab'     => $_GET['tab'],
				'sub_tab' => $_GET['sub_tab'],
			);

			$list_url = esc_url( add_query_arg( $list_query_args, admin_url( 'admin.php' ) ) );

			?>
			<div class="yith-plugin-fw-wp-page-wrapper ywctm-exclusions">
				<div class="wrap">
					<h1 class="wp-heading-inline"><?php esc_html_e( 'Exclusion list', 'yith-woocommerce-catalog-mode' ); ?></h1>
					<?php if ( empty( $_GET['action'] ) || ( 'insert' !== $_GET['action'] && 'edit' !== $_GET['action'] ) ) : ?>
						<?php
						$query_args   = array_merge( $list_query_args, array( 'action' => 'insert' ) );
						$add_form_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
						?>
						<a class="page-title-action yith-add-button" href="<?php echo $add_form_url; ?>"><?php esc_html_e( 'Add exclusion', 'yith-woocommerce-catalog-mode' ); ?></a>
					<?php endif; ?>
					<hr class="wp-header-end" />
					<?php if ( $message ) : ?>
						<div class="notice notice-<?php echo $message['type']; ?> is-dismissible"><p><?php echo $message['text']; ?></p></div>
					<?php endif; ?>
					<?php if ( ! empty( $_GET['action'] ) && ( 'insert' === $_GET['action'] || 'edit' === $_GET['action'] ) ) : ?>

						<?php
						$query_args = array_merge( $list_query_args, array() );

						if ( isset( $_GET['return_page'] ) ) {
							$query_args['paged'] = $_GET['return_page'];
						}

						$enabled = get_option( 'ywctm_inquiry_form_enabled' . ywctm_get_vendor_id(), 'hidden' );
						?>

						<form id="form" method="POST" action="<?php echo esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) ); ?>">
							<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
							<table class="form-table <?php echo( 'hidden' !== $enabled && ywctm_exists_inquiry_forms() ? '' : 'no-active-form' ); ?>">
								<tbody>
								<?php foreach ( $fields as $field ) : ?>
									<tr valign="top" class="yith-plugin-fw-panel-wc-row <?php echo $field['type']; ?> <?php echo $field['name']; ?>">
										<th scope="row" class="titledesc">
											<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
										</th>
										<td class="forminp forminp-<?php echo $field['type']; ?>">
											<?php yith_plugin_fw_get_field( $field, true ); ?>
											<?php if ( isset( $field['desc'] ) && '' !== $field['desc'] ) : ?>
												<span class="description"><?php echo $field['desc']; ?></span>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
							<input id="<?php echo $_GET['action']; ?>" name="<?php echo $_GET['action']; ?>" type="submit" class="<?php echo( 'insert' === $_GET['action'] ? 'yith-save-button' : 'yith-update-button' ); ?>" value="<?php echo( ( 'insert' === $_GET['action'] ) ? esc_html__( 'Add exclusion', 'yith-woocommerce-catalog-mode' ) : esc_html__( 'Update exclusion', 'yith-woocommerce-catalog-mode' ) ); ?>" />
						</form>

					<?php else : ?>

						<form id="custom-table" method="GET" action="<?php echo $list_url; ?>">
							<?php $table->search_box( esc_html__( 'Search item', 'yith-woocommerce-catalog-mode' ), 'item' ); ?>
							<input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
							<input type="hidden" name="tab" value="<?php echo $_GET['tab']; ?>" />
							<input type="hidden" name="sub_tab" value="<?php echo $_GET['sub_tab']; ?>" />
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
		 * @param  $type   string
		 * @param  $item   array
		 * @param  $name   string
		 * @param  $action string
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function get_fields( $type, $item, $name, $action ) {

			if ( 'edit' === $action ) {

				$item_type = ywctm_item_type_column( $type );
				$fields    = array(
					array(
						'id'                => 'item_ids',
						'name'              => 'item_ids',
						'type'              => 'text',
						'custom_attributes' => 'disabled',
						'value'             => $name,
						/* translators: %s item type */
						'title'             => sprintf( esc_html__( '%s to edit', 'yith-woocommerce-catalog-mode' ), $item_type ),
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
							'product'  => esc_html__( 'Products', 'yith-woocommerce-catalog-mode' ),
							'category' => esc_html__( 'Categories', 'yith-woocommerce-catalog-mode' ),
							'tag'      => esc_html__( 'Tags', 'yith-woocommerce-catalog-mode' ),
						),
						'title'   => esc_html__( 'Item type', 'yith-woocommerce-catalog-mode' ),
						'value'   => '',
						'desc'    => esc_html__( 'Choose whether to add specific products, categories or tags to the exclusion list.', 'yith-woocommerce-catalog-mode' ),
					),
					array(
						'id'       => 'product_ids',
						'name'     => 'product_ids',
						'type'     => 'ajax-products',
						'multiple' => true,
						'data'     => array(
							'placeholder' => esc_html__( 'Search products', 'yith-woocommerce-catalog-mode' ),
						),
						'title'    => esc_html__( 'Select products', 'yith-woocommerce-catalog-mode' ),
						'desc'     => esc_html__( 'Select which products to add in the exclusion list.', 'yith-woocommerce-catalog-mode' ),
					),
					array(
						'id'       => 'category_ids',
						'name'     => 'category_ids',
						'type'     => 'ajax-terms',
						'multiple' => true,
						'data'     => array(
							'placeholder' => esc_html__( 'Search categories', 'yith-woocommerce-catalog-mode' ),
							'taxonomy'    => 'product_cat',
						),
						'title'    => esc_html__( 'Select categories', 'yith-woocommerce-catalog-mode' ),
						'desc'     => esc_html__( 'Select which product categories to add in the exclusion list.', 'yith-woocommerce-catalog-mode' ),
					),
					array(
						'id'       => 'tag_ids',
						'name'     => 'tag_ids',
						'type'     => 'ajax-terms',
						'multiple' => true,
						'data'     => array(
							'placeholder' => esc_html__( 'Search tags', 'yith-woocommerce-catalog-mode' ),
							'taxonomy'    => 'product_tag',
						),
						'title'    => esc_html__( 'Select tags', 'yith-woocommerce-catalog-mode' ),
						'desc'     => esc_html__( 'Select which product tags to add in the exclusion list.', 'yith-woocommerce-catalog-mode' ),
					),
				);
			}

			return array_merge( $fields, ywctm_get_exclusion_fields( $item ) );

		}

		/**
		 * Get default values
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function get_default_values() {

			$atc_global         = get_option( 'ywctm_hide_add_to_cart_settings' . ywctm_get_vendor_id() );
			$button_global      = get_option( 'ywctm_custom_button_settings' . ywctm_get_vendor_id() );
			$button_loop_global = get_option( 'ywctm_custom_button_settings_loop' . ywctm_get_vendor_id() );
			$price_global       = get_option( 'ywctm_hide_price_settings' . ywctm_get_vendor_id() );
			$label_global       = get_option( 'ywctm_custom_price_text_settings' . ywctm_get_vendor_id() );

			return array(
				'ID'                          => 0,
				'enable_inquiry_form'         => 'yes',
				'enable_atc_custom_options'   => 'no',
				'atc_status'                  => $atc_global['action'],
				'custom_button'               => $button_global,
				'custom_button_loop'          => $button_loop_global,
				'enable_price_custom_options' => 'no',
				'price_status'                => $price_global['action'],
				'custom_price_text'           => $label_global,
			);

		}

		/**
		 * Add screen options for exclusions list table template
		 *
		 * @param   $current_screen WP_Screen
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_options( $current_screen ) {

			if ( ( 'yith-plugins_page_yith_wc_catalog_mode_panel' === $current_screen->id || 'toplevel_page_yith_vendor_ctm_settings' === $current_screen->id ) && ( isset( $_GET['tab'] ) && 'exclusions' === $_GET['tab'] ) && ( ! isset( $_GET['action'] ) || ( 'edit' !== $_GET['action'] && 'insert' !== $_GET['action'] ) ) ) {

				$option = 'per_page';

				$args = array(
					'label'   => esc_html__( 'Exclusions', 'yith-woocommerce-catalog-mode' ),
					'default' => 10,
					'option'  => 'items_per_page',
				);

				add_screen_option( $option, $args );

			}

		}

		/**
		 * Set screen options for exclusions list table template
		 *
		 * @param   $status string
		 * @param   $option string
		 * @param   $value  string
		 *
		 * @return  mixed
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function set_options( $status, $option, $value ) {

			return ( 'items_per_page' === $option ) ? $value : $status;

		}

	}

	new YWCTM_Exclusions_Table();
}
