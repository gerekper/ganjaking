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

if ( ! class_exists( 'YWCTM_Vendors_Table' ) ) {

	/**
	 * Displays the exclusion table in YWCTM plugin admin tab
	 *
	 * @class   YWCTM_Vendors_Table
	 * @since   2.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWCTM_Vendors_Table {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			if ( ! isset( $_GET['sub_tab'] ) || ( isset( $_GET['sub_tab'] ) && 'exclusions-vendors' !== $_GET['sub_tab'] ) ) {
				return;
			}

			add_action( 'init', array( $this, 'init' ), 15 );

		}

		/**
		 * Init page
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function init() {
			add_action( 'ywctm_exclusions_vendors', array( $this, 'output' ) );
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
					'singular' => esc_html__( 'vendor', 'yith-woocommerce-catalog-mode' ),
					'plural'   => esc_html__( 'vendors', 'yith-woocommerce-catalog-mode' ),
					'id'       => 'vendor',
				)
			);

			$message     = array();
			$fields      = array();
			$object_name = '';

			if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], basename( __FILE__ ) ) ) {

				if ( ! empty( $_POST['item_ids'] ) ) {

					$exclusion_data = isset( $_POST['_ywctm_vendor_override_exclusion'] ) ? 'yes' : 'no';

					$object_ids = ( ! is_array( $_POST['item_ids'] ) ) ? explode( ',', $_POST['item_ids'] ) : $_POST['item_ids'];

					foreach ( $object_ids as $object_id ) {
						update_term_meta( $object_id, '_ywctm_vendor_override_exclusion', $exclusion_data );
					}

					if ( ! empty( $_POST['insert'] ) ) {

						$singular = esc_html__( '1 vendor added successfully', 'yith-woocommerce-catalog-mode' );
						/* translators: %s number of vendors */
						$plural  = sprintf( esc_html__( '%s vendors added successfully', 'yith-woocommerce-catalog-mode' ), count( $object_ids ) );
						$message = array(
							'text' => count( $object_ids ) === 1 ? $singular : $plural,
							'type' => 'success',
						);

					} elseif ( ! empty( $_POST['edit'] ) ) {

						$message = array(
							'text' => esc_html__( 'Vendor updated successfully', 'yith-woocommerce-catalog-mode' ),
							'type' => 'success',
						);

					}
				}
			}

			if ( ! empty( $_GET['action'] ) && 'delete' !== $_GET['action'] ) {

				$item = array(
					'ID'      => 0,
					'exclude' => 'yes',
				);

				if ( isset( $_GET['id'] ) && ( 'edit' === $_GET['action'] ) ) {

					$item        = array(
						'ID'      => $_GET['id'],
						'exclude' => get_term_meta( $_GET['id'], '_ywctm_vendor_override_exclusion', true ),
					);
					$vendor      = get_term( $_GET['id'], 'yith_shop_vendor' );
					$object_name = $vendor->name;

				}

				$fields = $this->get_fields( $item, $object_name, $_GET['action'] );

			}

			$table->options = array(
				'select_table'     => $wpdb->terms . ' a INNER JOIN ' . $wpdb->term_taxonomy . ' b ON a.term_id = b.term_id INNER JOIN ' . $wpdb->termmeta . ' c ON c.term_id = a.term_id',
				'select_columns'   => array(
					'a.term_id AS ID',
					'a.name',
					'MAX( CASE WHEN c.meta_key = "_ywctm_vendor_override_exclusion" THEN c.meta_value ELSE NULL END ) AS exclude',
				),
				'select_where'     => 'b.taxonomy = "yith_shop_vendor" AND ( c.meta_key = "_ywctm_vendor_override_exclusion" )',
				'select_group'     => 'a.term_id',
				'select_order'     => 'a.name',
				'select_order_dir' => 'ASC',
				'per_page_option'  => 'vendors_per_page',
				'search_where'     => array(
					'a.name',
				),
				'count_table'      => $wpdb->terms . ' a INNER JOIN ' . $wpdb->term_taxonomy . ' b ON a.term_id = b.term_id INNER JOIN ' . $wpdb->termmeta . ' c ON c.term_id = a.term_id',
				'count_where'      => 'b.taxonomy = "yith_shop_vendor" AND c.meta_key = "_ywctm_vendor_override_exclusion"',
				'key_column'       => 'ID',
				'view_columns'     => array(
					'cb'      => '<input type="checkbox" />',
					'vendor'  => esc_html__( 'Vendor', 'yith-woocommerce-catalog-mode' ),
					'exclude' => esc_html__( 'Exclusion', 'yith-woocommerce-catalog-mode' ),
				),
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'category' => array( 'name', true ),
				),
				'custom_columns'   => array(
					'column_vendor'  => function ( $item, $me ) {
						return ywctm_item_name_column( $item, $me );
					},
					'column_exclude' => function ( $item ) {
						ywctm_vendor_column( $item );
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

									delete_term_meta( $id, '_ywctm_vendor_override_exclusion' );
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
				$singular = esc_html__( '1 vendor removed successfully', 'yith-woocommerce-catalog-mode' );
				/* translators: %s number of vendors*/
				$plural  = sprintf( esc_html__( '%s vendors removed successfully', 'yith-woocommerce-catalog-mode' ), $deleted );
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
					<h1 class="wp-heading-inline"><?php esc_html_e( 'Vendor Exclusion list', 'yith-woocommerce-catalog-mode' ); ?></h1>
					<?php if ( empty( $_GET['action'] ) || ( 'insert' !== $_GET['action'] && 'edit' !== $_GET['action'] ) ) : ?>
						<?php
						$query_args   = array_merge( $list_query_args, array( 'action' => 'insert' ) );
						$add_form_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
						?>
						<a class="page-title-action yith-add-button" href="<?php echo $add_form_url; ?>"><?php echo esc_html__( 'Add vendor', 'yith-woocommerce-catalog-mode' ); ?></a>
					<?php endif; ?>
					<hr class="wp-header-end" />
					<?php if ( $message ) : ?>
						<div class="notice notice-<?php echo $message['type']; ?> is-dismissible"><p><?php echo $message['text']; ?></p></div>
					<?php endif; ?>
					<?php
					if ( ! empty( $_GET['action'] ) && ( 'insert' === $_GET['action'] || 'edit' === $_GET['action'] ) ) :
						$query_args = array_merge( $list_query_args, array() );
						?>

						<form id="form" method="POST" action="<?php echo esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) ); ?>">
							<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
							<table class="form-table">
								<tbody>
								<?php foreach ( $fields as $field ) : ?>
									<tr valign="top" class="yith-plugin-fw-panel-wc-row <?php echo $field['type']; ?> <?php echo $field['name']; ?>">
										<th scope="row" class="titledesc">
											<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
										</th>
										<td class="forminp forminp-<?php echo $field['type']; ?>">
											<?php yith_plugin_fw_get_field( $field, true ); ?>
										</td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
							<input id="<?php echo $_GET['action']; ?>" name="<?php echo $_GET['action']; ?>" type="submit" class="<?php echo( 'insert' === $_GET['action'] ? 'yith-save-button' : 'yith-update-button' ); ?>" value="<?php echo( ( 'insert' === $_GET['action'] ) ? esc_html__( 'Add vendor', 'yith-woocommerce-catalog-mode' ) : esc_html__( 'Update vendor', 'yith-woocommerce-catalog-mode' ) ); ?>" />
						</form>

					<?php else : ?>

						<form id="custom-table" method="GET" action="<?php echo $list_url; ?>">
							<?php $table->search_box( esc_html__( 'Search vendor', 'yith-woocommerce-catalog-mode' ), 'vendor' ); ?>
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
		 * @param  $item   array
		 * @param  $name   string
		 * @param  $action string
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function get_fields( $item, $name, $action ) {

			$fields = array(
				0 => array(
					'id'       => 'item_ids',
					'name'     => 'item_ids',
					'type'     => 'ajax-terms',
					'multiple' => true,
					'data'     => array(
						'placeholder' => esc_html__( 'Search vendors', 'yith-woocommerce-catalog-mode' ),
						'taxonomy'    => 'yith_shop_vendor',
					),
					'title'    => esc_html__( 'Select vendors', 'yith-woocommerce-catalog-mode' ),
				),
				1 => array(
					'id'    => '_ywctm_vendor_override_exclusion',
					'name'  => '_ywctm_vendor_override_exclusion',
					'type'  => 'onoff',
					'title' => esc_html__( 'Enable Exclusion', 'yith-woocommerce-catalog-mode' ),
					'value' => $item['exclude'],
				),
			);

			if ( 'edit' === $action ) {
				$fields[0] = array(
					'id'                => 'item_ids',
					'name'              => 'item_ids',
					'type'              => 'text',
					'custom_attributes' => 'disabled',
					'value'             => $name,
					'title'             => esc_html__( 'Vendor to edit', 'yith-woocommerce-catalog-mode' ),
				);
				$fields[8] = array(
					'id'    => 'item_id',
					'name'  => 'item_ids',
					'type'  => 'hidden',
					'value' => $item['ID'],
					'title' => '',
				);
			};

			ksort( $fields );

			return $fields;

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

			if ( ( 'yith-plugins_page_yith_wc_catalog_mode_panel' === $current_screen->id ) && ( isset( $_GET['tab'] ) && 'exclusions' === $_GET['tab'] ) && ( ! isset( $_GET['action'] ) || ( 'edit' !== $_GET['action'] && 'insert' !== $_GET['action'] ) ) ) {

				$option = 'per_page';

				$args = array(
					'label'   => esc_html__( 'Vendors', 'yith-woocommerce-catalog-mode' ),
					'default' => 10,
					'option'  => 'vendors_per_page',
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

			return ( 'vendors_per_page' === $option ) ? $value : $status;

		}

	}

	new YWCTM_Vendors_Table();
}
