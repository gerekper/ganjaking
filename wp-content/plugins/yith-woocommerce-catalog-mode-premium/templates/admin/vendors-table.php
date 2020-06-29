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
	 * @package Yithemes
	 * @since   1.3.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWCTM_Vendors_Table {

		/**
		 * Single instance of the class
		 *
		 * @var \YWCTM_Vendors_Table
		 * @since 1.3.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWCTM_Vendors_Table
		 * @since 1.3.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self(  );

			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since   1.3.0
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_filter( 'set-screen-option', array( $this, 'set_options' ), 10, 3 );
			add_action( 'current_screen', array( $this, 'add_options' ) );
			add_action( 'wp_ajax_ywctm_json_search_vendors', array( $this, 'json_search_vendors' ), 10 );

		}

		/**
		 * Outputs the exclusion table template with insert form in plugin options panel
		 *
		 * @since   1.3.0
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function output() {

			global $wpdb;

			$label = __( 'Exclude from override', 'yith-woocommerce-catalog-mode' );

			if ( get_option( 'ywctm_admin_override_reverse' ) == 'yes' ) {
				$label = __( 'Apply override', 'yith-woocommerce-catalog-mode' );
			}

			$table = new YITH_Custom_Table( array(
				                                'singular' => __( 'vendor', 'yith-woocommerce-catalog-mode' ),
				                                'plural'   => __( 'vendors', 'yith-woocommerce-catalog-mode' )
			                                ) );

			$table->options = array(
				'select_table'     => $wpdb->prefix . 'terms a INNER JOIN ' . $wpdb->prefix . 'term_taxonomy b ON a.term_id = b.term_id INNER JOIN ' . $wpdb->prefix .  'termmeta c ON c.term_id = a.term_id',
				'select_columns'   => array(
					'a.term_id AS ID',
					'a.name',
					'MAX( CASE WHEN c.meta_key = "_ywctm_vendor_override_exclusion" THEN c.meta_value ELSE NULL END ) AS exclude',
				),
				'select_where'     => 'b.taxonomy = "yith_shop_vendor" AND ( c.meta_key = "_ywctm_vendor_override_exclusion" ) AND c.meta_value = "yes"',
				'select_group'     => 'a.term_id',
				'select_order'     => 'a.name',
				'select_order_dir' => 'ASC',
				'per_page_option'  => 'vendors_per_page',
				'search_where'     => array(
					'a.name'
				),
				'count_table'      => '( SELECT a.* FROM ' . $wpdb->prefix . 'terms a INNER JOIN ' . $wpdb->prefix . 'term_taxonomy b ON a.term_id = b.term_id INNER JOIN ' . $wpdb->prefix .  'termmeta c ON c.term_id = a.term_id WHERE b.taxonomy = "yith_shop_vendor" AND ( c.meta_key = "_ywctm_vendor_override_exclusion" ) AND c.meta_value = "yes" GROUP BY a.term_id ) AS a',
				'count_where'      => '',
				'key_column'       => 'ID',
				'view_columns'     => array(
					'cb'      => '<input type="checkbox" />',
					'vendor'  => __( 'Vendor', 'yith-woocommerce-catalog-mode' ),
					'exclude' => $label,
				),
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'category' => array( 'name', true )
				),
				'custom_columns'   => array(
					'column_vendor'  => function ( $item, $me ) {

						$edit_query_args = array(
							'page'   => $_GET['page'],
							'tab'    => $_GET['tab'],
							'action' => 'edit',
							'id'     => $item['ID']
						);
						$edit_url        = esc_url( add_query_arg( $edit_query_args, admin_url( 'admin.php' ) ) );

						$delete_query_args = array(
							'page'   => $_GET['page'],
							'tab'    => $_GET['tab'],
							'action' => 'delete',
							'id'     => $item['ID']
						);
						$delete_url        = esc_url( add_query_arg( $delete_query_args, admin_url( 'admin.php' ) ) );

						$category_query_args = array(
							'taxonomy'  => 'yith_shop_vendor',
							'post_type' => 'product',
							'tag_ID'    => $item['ID'],
							'action'    => 'edit'
						);
						$vendor_url          = esc_url( add_query_arg( $category_query_args, admin_url( 'edit-tags.php' ) ) );

						$actions = array(
							'edit'    => '<a href="' . $edit_url . '">' . __( 'Edit exclusion', 'yith-woocommerce-catalog-mode' ) . '</a>',
							'product' => '<a href="' . $vendor_url . '" target="_blank">' . __( 'Edit vendor', 'yith-woocommerce-catalog-mode' ) . '</a>',
							'delete'  => '<a href="' . $delete_url . '">' . __( 'Remove from list', 'yith-woocommerce-catalog-mode' ) . '</a>',
						);

						return sprintf( '<strong><a class="tips" href="%s" data-tip="%s">#%d %s </a></strong> %s', $edit_url, __( 'Edit exclusion', 'yith-woocommerce-catalog-mode' ), $item['ID'], $item['name'], $me->row_actions( $actions ) );
					},
					'column_exclude' => function ( $item, $me ) {

						if ( $item['exclude'] == 'yes' ) {
							$class = 'show';
							$tip   = __( 'Yes', 'yith-woocommerce-catalog-mode' );
						} else {
							$class = 'hide';
							$tip   = __( 'No', 'yith-woocommerce-catalog-mode' );
						}

						return sprintf( '<mark class="%s tips" data-tip="%s">%s</mark>', $class, $tip, $tip );

					},
				),
				'bulk_actions'     => array(
					'actions'   => array(
						'delete' => __( 'Remove from list', 'yith-woocommerce-catalog-mode' )
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
                                           SET meta_value = 'no'
                                           WHERE ( meta_key = '_ywctm_vendor_override_exclusion' ) AND term_id IN ( $ids )"
								);
							}

						}
					)
				),
			);

			$message = '';
			$notice  = '';

			$list_query_args = array(
				'page' => $_GET['page'],
				'tab'  => $_GET['tab'],
			);

			$list_url = esc_url( add_query_arg( $list_query_args, admin_url( 'admin.php' ) ) );

			if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], basename( __FILE__ ) ) ) {

				$item_valid = $this->validate_fields( $_POST );

				if ( $item_valid !== true ) {

					$notice = $item_valid;

				} else {

					$exclusion  = isset( $_POST['_ywctm_vendor_override_exclusion'] ) ? 'yes' : 'no';
					$vendor_ids = ( ! is_array( $_POST['vendor_ids'] ) ) ? explode( ',', $_POST['vendor_ids'] ) : $_POST['vendor_ids'];
					$count      = count( $vendor_ids );

					foreach ( $vendor_ids as $vendor_id ) {

						update_term_meta( $vendor_id, '_ywctm_vendor_override_exclusion', $exclusion );

					}

					if ( ! empty( $_POST['insert'] ) ) {

						$singular = __( '1 vendor added successfully', 'yith-woocommerce-catalog-mode' );
						$plural   = sprintf( __( '%s vendors added successfully', 'yith-woocommerce-catalog-mode' ), $count );
						$message  = $count > 1 ? $plural : $singular;

					} elseif ( ! empty( $_POST['edit'] ) ) {

						$message = __( 'Vendor updated successfully', 'yith-woocommerce-catalog-mode' );

					}

				}

			}

			$table->prepare_items();

			$data_selected = '';
			$value         = '';
			$item          = array(
				'ID'      => 0,
				'exclude' => '',
			);

			if ( isset( $_GET['id'] ) && ! empty( $_GET['action'] ) && ( 'edit' == $_GET['action'] ) ) {

				$item          = array(
					'ID'      => $_GET['id'],
					'exclude' => get_term_meta( $_GET['id'], '_ywctm_vendor_override_exclusion', true )
				);
				$vendor        = get_term( $_GET['id'], 'yith_shop_vendor' );
				$data_selected = wp_kses_post( $vendor->name );
				$value         = $_GET['id'];

			}

			if ( 'delete' === $table->current_action() ) {

				$singular = __( '1 vendor removed successfully', 'yith-woocommerce-catalog-mode' );
				$plural   = sprintf( __( '%s vendors removed successfully', 'yith-woocommerce-catalog-mode' ), count( $_GET['id'] ) );
				$message  = count( $_GET['id'] ) > 1 ? $plural : $singular;

			}

			?>
            <div class="wrap">
                <div class="icon32 icon32-posts-post" id="icon-edit"><br /></div>
                <h1><?php _e( 'Vendor Exclusion list', 'yith-woocommerce-catalog-mode' ); ?>

					<?php if ( empty( $_GET['action'] ) || ( 'insert' !== $_GET['action'] && 'edit' !== $_GET['action'] ) ) : ?>
						<?php
						$query_args   = array( 'page' => $_GET['page'], 'tab' => $_GET['tab'], 'action' => 'insert' );
						$add_form_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
						?>
                        <a class="page-title-action" href="<?php echo $add_form_url; ?>"><?php _e( 'Add vendors', 'yith-woocommerce-catalog-mode' ); ?></a>
					<?php endif; ?>
                </h1>

				<?php if ( ! empty( $notice ) ) : ?>
                    <div id="notice" class="error below-h2"><p><?php echo $notice; ?></p></div>
				<?php endif; ?>

				<?php if ( ! empty( $message ) ) : ?>
                    <div id="message" class="updated below-h2"><p><?php echo $message; ?></p></div>
				<?php endif; ?>

				<?php if ( ! empty( $_GET['action'] ) && ( 'insert' == $_GET['action'] || 'edit' == $_GET['action'] ) ) : ?>

                    <form id="form" method="POST" action="<?php echo $list_url; ?>">
                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
                        <table class="form-table">
                            <tbody>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="vendor_ids">
										<?php echo ( 'edit' == $_GET['action'] ) ? __( 'Vendor to edit', 'yith-woocommerce-catalog-mode' ) : __( 'Select vendors', 'yith-woocommerce-catalog-mode' ); ?>
                                    </label>
                                </th>
                                <td class="forminp">

									<?php if ( 'edit' == $_GET['action'] ) : ?>
                                        <input id="vendor_id" name="vendor_ids" type="hidden" value="<?php echo esc_attr( $item['ID'] ); ?>" />
									<?php endif; ?>

									<?php

									$select_args = array(
										'class'            => 'wc-product-search',
										'id'               => 'vendor_ids',
										'name'             => 'vendor_ids',
										'data-placeholder' => __( 'Search for a vendor&hellip;', 'yith-woocommerce-catalog-mode' ),
										'data-allow_clear' => false,
										'data-selected'    => $data_selected,
										'data-multiple'    => ( 'edit' == $_GET['action'] ) ? false : true,
										'data-action'      => 'ywctm_json_search_vendors',
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
                                    <label for="_ywctm_vendor_override_exclusion"><?php echo $label; ?></label>
                                </th>
                                <td class="forminp forminp-checkbox">
                                    <input id="_ywctm_vendor_override_exclusion" name="_ywctm_vendor_override_exclusion" type="checkbox" <?php echo ( esc_attr( $item['exclude'] ) == 'yes' ) ? 'checked="checked"' : ''; ?> />
                                </td>
                            </tr>

                            </tbody>
                        </table>
                        <input id="<?php echo $_GET['action'] ?>" name="<?php echo $_GET['action'] ?>" type="submit" class="button-primary"
                               value="<?php echo( ( 'insert' == $_GET['action'] ) ? __( 'Add exclusion', 'yith-woocommerce-catalog-mode' ) : __( 'Update exclusion', 'yith-woocommerce-catalog-mode' ) ); ?>"
                        />
                        <a class="button-secondary" href="<?php echo $list_url; ?>"><?php _e( 'Return to list', 'yith-woocommerce-catalog-mode' ); ?></a>
                    </form>

				<?php else : ?>

                    <p>
                        <i>
							<?php _e( 'If you want to use this table, firstly you have to enable the "Admin Override" option, then enable the "Exclusion" option and, if want, also enable the "Reverse Exclusion" option.', 'yith-woocommerce-catalog-mode' ); ?>
                        </i>
                    </p>
                    <form id="custom-table" method="GET" action="<?php echo $list_url; ?>">
						<?php $table->search_box( __( 'Search vendor' ), 'vendor' ); ?>
                        <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
                        <input type="hidden" name="tab" value="<?php echo $_GET['tab']; ?>" />
						<?php $table->display(); ?>
                    </form>

				<?php endif; ?>

            </div>
			<?php

		}

		/**
		 * Validate input fields
		 *
		 * @since   1.3.0
		 *
		 * @param   $item array POST data array
		 *
		 * @return  bool|string
		 * @author  Alberto Ruggiero
		 */
		public function validate_fields( $item ) {

			$messages = array();

			if ( ! empty( $item['insert'] ) ) {

				if ( empty( $item['vendor_ids'] ) ) {
					$messages[] = __( 'Select at least one category', 'yith-woocommerce-catalog-mode' );
				}

			}

			if ( empty( $messages ) ) {
				return true;
			}

			return implode( '<br />', $messages );
		}

		/**
		 * Add screen options for exclusions list table template
		 *
		 * @since   1.3.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_options() {

			if ( ( 'yith-plugins_page_yith_wc_catalog_mode_panel' == get_current_screen()->id ) && ( isset( $_GET['tab'] ) && $_GET['tab'] == 'vendors' ) && ( ! isset( $_GET['action'] ) || ( $_GET['action'] != 'edit' && $_GET['action'] != 'insert' ) ) ) {

				$option = 'per_page';

				$args = array(
					'label'   => __( 'Vendors', 'yith-woocommerce-catalog-mode' ),
					'default' => 10,
					'option'  => 'vendors_per_page'
				);

				add_screen_option( $option, $args );

			}

		}

		/**
		 * Set screen options for exclusions list table template
		 *
		 * @since   1.3.0
		 *
		 * @param   $status
		 * @param   $option
		 * @param   $value
		 *
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function set_options( $status, $option, $value ) {

			return ( 'vendors_per_page' == $option ) ? $value : $status;

		}

		/**
		 * Get category name
		 *
		 * @since   1.3.0
		 *
		 * @param   $x
		 * @param   $taxonomy_types
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function json_search_vendors( $x = '', $taxonomy_types = array( 'yith_shop_vendor' ) ) {

			global $wpdb;

			$term = (string) urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );
			$term = '%' . $term . '%';

			$query_cat = $wpdb->prepare( "SELECT {$wpdb->terms}.term_id,{$wpdb->terms}.name, {$wpdb->terms}.slug
                                   FROM {$wpdb->terms} INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
                                   WHERE {$wpdb->term_taxonomy}.taxonomy IN (%s) AND {$wpdb->terms}.slug LIKE %s", implode( ',', $taxonomy_types ), $term );

			$product_categories = $wpdb->get_results( $query_cat );

			$to_json = array();

			foreach ( $product_categories as $product_category ) {

				$to_json[ $product_category->term_id ] = sprintf( '#%s &ndash; %s', $product_category->term_id, $product_category->name );

			}

			wp_send_json( $to_json );

		}

	}

	/**
	 * Unique access to instance of YWCTM_Vendors_Table class
	 *
	 * @return \YWCTM_Vendors_Table
	 */
	function YWCTM_Vendors_Table() {

		return YWCTM_Vendors_Table::get_instance();

	}

	new YWCTM_Vendors_Table();
}