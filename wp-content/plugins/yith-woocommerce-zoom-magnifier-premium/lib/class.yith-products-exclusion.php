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

if ( ! class_exists( 'YWZM_Products_Exclusion' ) ) {

	/**
	 * Displays the exclusions table in ywzm plugin admin tab
	 *
	 * @class   ywzm_Blocklist_Table
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWZM_Products_Exclusion {

		/**
		 * Outputs the exclusions table template with insert form in plugin options panel
		 *
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 * @return  string
		 */
		public static function output() {

			global $wpdb;

			$table = new YITH_YWZM_Custom_Table( array(
				'singular' => esc_html__( 'product', 'yith-woocommerce-zoom-magnifier' ),
				'plural'   => esc_html__( 'products', 'yith-woocommerce-zoom-magnifier' )
			) );

			$table->options = array(
				'select_table'     => $wpdb->prefix . 'posts a INNER JOIN ' . $wpdb->prefix . 'postmeta b ON a.ID = b.post_id',
				'select_columns'   => array(
					'a.ID',
					'a.post_title'
				),
				'select_where'     => 'a.post_type = "product" AND b.meta_key = "_ywzm_exclude" AND b.meta_value = "yes"',
				'select_group'     => 'a.ID',
				'select_order'     => 'a.post_title',
				'select_limit'     => 10,
				'count_table'      => '( SELECT COUNT(*) FROM ' . $wpdb->prefix . 'posts a INNER JOIN ' . $wpdb->prefix . 'postmeta b ON a.ID = b.post_id  WHERE a.post_type = "product" AND b.meta_key = "_ywzm_exclude" AND b.meta_value="yes" GROUP BY a.ID ) AS count_table',
				'count_where'      => '',
				'key_column'       => 'ID',
				'view_columns'     => $table->get_columns(),
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'product' => array( 'post_title', true )
				),
				'custom_columns'   => array(
					'column_product' => function ( $item, $me ) {

						$delete_query_args = array(
							'page'   => $_GET['page'],
							'tab'    => $_GET['tab'],
							'action' => 'delete',
							'id'     => $item['ID']
						);

						$delete_url = add_query_arg( $delete_query_args, admin_url( 'admin.php' ) );

						$product_query_args = array(
							'post'   => $item['ID'],
							'action' => 'edit'
						);
						$product_url        = add_query_arg( $product_query_args, admin_url( 'post.php' ) );

						$actions = array(
							'delete' => '<a href="' . esc_url( $delete_url ) . '">' . esc_html__( 'Remove from exclusions', 'yith-woocommerce-zoom-magnifier' ) . '</a>'
						);

						return sprintf( '<strong><a class="tips" target="_blank" href="%s" data-tip="%s">#%d %s </a></strong> %s', esc_url( $product_url ), esc_html__( 'Edit product', 'yith-woocommerce-zoom-magnifier' ), $item['ID'], $item['post_title'], $me->row_actions( $actions ) );
					}
				),
				'bulk_actions'     => array(
					'actions'   => array(
						'delete' => esc_html__( 'Remove from list', 'yith-woocommerce-zoom-magnifier' )
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
                                           WHERE ( meta_key = '_ywzm_exclude') AND post_id IN ( $ids )"
								);
							}
						}
					)
				),
			);

			$table->prepare_items();

			$message = '';
			$notice  = '';

			$default = array(
				'ID'         => 0,
				'post_title' => ''
			);

			$list_query_args = array(
				'page' => $_GET['page'],
				'tab'  => $_GET['tab']
			);

			$list_url = add_query_arg( $list_query_args, admin_url( 'admin.php' ) );

			if ( 'delete' === $table->current_action() ) {
				$message = sprintf( _n( '%s product removed successfully', '%s products removed successfully', count( $_GET['id'] ), 'yith-woocommerce-zoom-magnifier' ), count( $_GET['id'] ) );
			}

			if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], basename( __FILE__ ) ) ) {

				$item_valid = self::validate_fields( $_POST );

				if ( $item_valid !== true ) {

					$notice = $item_valid;

				} else {

					foreach ( $_POST['products'] as $product_id ) {
						yit_save_prop( wc_get_product( $product_id ), '_ywzm_exclude', 'yes' );
					}

					if ( ! empty( $_POST['insert'] ) ) {

						$message = sprintf( _n( '%s product added successfully', '%s products added successfully', count( $_POST['products'] ), 'yith-woocommerce-zoom-magnifier' ), count( $_POST['products'] ) );

					} elseif ( ! empty( $_POST['update'] ) ) {

						$message = esc_html__( 'Product updated successfully', 'yith-woocommerce-zoom-magnifier' );

					}
				}
			}

			$item = $default;

			if ( isset( $_GET['id'] ) ) {

				$select_table   = $table->options['select_table'];
				$select_columns = implode( ',', $table->options['select_columns'] );
				$item           = $wpdb->get_row( $wpdb->prepare( "SELECT $select_columns FROM $select_table WHERE a.id = %d", $_GET['id'] ), ARRAY_A );

			}

			?>
			<div class="wrap">
				<div class="icon32 icon32-posts-post" id="icon-edit"><br /></div>
				<h2><?php esc_html_e( 'Single Product Exclusion List', 'yith-woocommerce-zoom-magnifier' );

					if ( empty( $_GET['action'] ) || ( 'insert' !== $_GET['action'] ) ) : ?>
						<?php $query_args = array(
							'page'   => $_GET['page'],
							'tab'    => $_GET['tab'],
							'action' => 'insert'
						);
						$add_form_url     = add_query_arg( $query_args, admin_url( 'admin.php' ) );
						?>
						<a class="add-new-h2"
						   href="<?php echo esc_url( $add_form_url ); ?>"><?php esc_html_e( 'Add Products', 'yith-woocommerce-zoom-magnifier' ); ?></a>
					<?php endif; ?>
				</h2>
				<?php if ( ! empty( $notice ) ) : ?>
					<div id="notice" class="error below-h2"><p><?php echo $notice; ?></p></div>
				<?php endif;

				if ( ! empty( $message ) ) : ?>
					<div id="message" class="updated below-h2"><p><?php echo $message; ?></p></div>
				<?php endif;

				if ( ! empty( $_GET['action'] ) && ( 'insert' == $_GET['action'] || 'edit' == $_GET['action'] ) ) : ?>

					<form id="form" method="POST">
						<input type="hidden" name="nonce"
						       value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
						<table class="form-table">
							<tbody>
							<tr valign="top" class="titledesc">
								<th scope="row">
									<label for="product"><?php esc_html_e( 'Products to exclude', 'yith-woocommerce-zoom-magnifier' ); ?></label>
								</th>
								<td class="forminp">
                                    <?php
                                    yit_add_select2_fields( array(
                                    'class'         => 'wc-product-search',
                                    'id'            => '',
                                    'name'          => 'products[]',
                                    'style'         => 'width:50%;',
                                    'data-multiple' => true,
                                    'data-selected' => '',
                                    'value'         => '',
                                    ) );?>
								</td>
							</tr>
							</tbody>
						</table>
						<?php if ( 'insert' == $_GET['action'] ) : ?>

							<input type="submit" value="<?php esc_html_e( 'Add product exclusion', 'yith-woocommerce-zoom-magnifier' ); ?>" id="insert"
							       class="button-primary" name="insert">
						<?php endif; ?>
						<a class="button-secondary"
						   href="<?php echo esc_url( $list_url ); ?>"><?php esc_html_e( 'Return to exclusion list', 'yith-woocommerce-zoom-magnifier' ); ?></a>
					</form>
				<?php else : ?>
					<form id="custom-table" method="GET" action="<?php echo esc_url( $list_url ); ?>">
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
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 *
		 * @param   $item array POST data array
		 *
		 * @return  bool|string
		 */
		static function validate_fields( $item ) {
			$messages = array();

			if ( empty( $item['products'] ) ) {
				$messages[] = esc_html__( 'Select at least one product', 'yith-woocommerce-zoom-magnifier' );
			}


			if ( empty( $messages ) ) {
				return true;
			}

			return implode( '<br />', $messages );
		}

	}
}