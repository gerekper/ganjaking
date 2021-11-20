<?php
/**
 * WooCommerce Order Status Manager
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Order Status Manager to newer
 * versions in the future. If you wish to customize WooCommerce Order Status Manager for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Order Status Manager Order Statuses Admin
 *
 * @since 1.0.0
 */
class WC_Order_Status_Manager_Admin_Order_Statuses {


	/**
	 * Setup admin class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_filter( 'views_edit-wc_order_status', array( $this, 'order_status_table_actions' ) );
		add_action( 'admin_head-edit.php',        array( $this, 'move_add_custom_statuses_action' ) );

		add_filter( 'manage_edit-wc_order_status_columns', array( $this, 'order_status_columns' ) );

		add_filter( 'post_row_actions', array( $this, 'order_status_actions' ), 10, 2 );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		add_action( 'wc_order_status_manager_process_wc_order_status_meta', array( $this, 'save_order_status_meta' ), 10, 2 );

		add_action( 'manage_wc_order_status_posts_custom_column', array( $this, 'custom_column_content' ), 10, 2 );

		add_action( 'admin_footer', array( $this, 'reassign_order_status_popup' ) );
	}


	/**
	 * Customize order status columns
	 *
	 * @since 1.0.0
	 * @param array $columns
	 * @return array
	 */
	public function order_status_columns( $columns ) {

		$columns['slug']        = __( 'Slug', 'woocommerce-order-status-manager' );
		$columns['description'] = __( 'Description', 'woocommerce-order-status-manager' );
		$columns['paid']        = __( 'Paid', 'woocommerce-order-status-manager' );
		$columns['reports']     = __( 'Reports', 'woocommerce-order-status-manager' );
		$columns['type']        = __( 'Type', 'woocommerce-order-status-manager' );

		$first_column = array( 'icon' => __( 'Icon', 'woocommerce-order-status-manager' ) );

		return $first_column + $columns;
	}


	/**
	 * Customize order status row actions
	 *
	 * @since 1.0.0
	 * @param array $actions
	 * @param WP_Post $post
	 * @return array
	 */
	public function order_status_actions( $actions, WP_Post $post ) {

		$status = new WC_Order_Status_Manager_Order_Status( $post->ID );

		// remove delete for core statuses
		if ( $status->is_core_status() ) {
			unset( $actions['delete'] );
		}

		return $actions;
	}

	/**
	 * Add meta boxes to the order status edit page
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {

		// Order Status data meta box
		add_meta_box(
			'woocommerce-order-status-data',
			__( 'Order Status Data', 'woocommerce-order-status-manager' ),
			array( $this, 'order_status_data_meta_box' ),
			'wc_order_status',
			'normal',
			'high'
		);

		// Order Status actions meta box
		add_meta_box(
			'woocommerce-order-status-actions',
			__( 'Order Status Actions', 'woocommerce-order-status-manager' ),
			array( $this, 'order_status_actions_meta_box' ),
			'wc_order_status',
			'side',
			'high'
		);

		remove_meta_box( 'slugdiv', 'wc_order_status', 'normal' );
	}


	/**
	 * Display the order status data meta box
	 *
	 * @since 1.0.0
	 */
	public function order_status_data_meta_box() {
		global $post;

		$status = new \WC_Order_Status_Manager_Order_Status( $post->ID );

		wp_nonce_field( 'wc_order_status_manager_save_data', 'wc_order_status_manager_meta_nonce' );

		?>
		<div id="order_status_options" class="panel woocommerce_options_panel">
			<div class="options_group">
				<?php

				// Status Name
				woocommerce_wp_text_input( array(
					'id'    => 'post_title',
					'label' => __( 'Name', 'woocommerce-order-status-manager' ),
					'value' => $post->post_title,
					'custom_attributes' => array(
						'maxlength' => 35,
					),
					'desc_tip'          => true,
					'description'       => __( 'Maximum 35 characters.', 'woocommerce-order-status-manager' ),
				) );

				// slugs must have maximum 17 chars and can't begin with "wc-"
				$slug_custom_attributes = [
					'maxlength' => 17,
					'pattern'   => '^(?!wc-).+',
				];

				// disable slug editing for core statuses
				if ( $status->is_core_status() ) {
					$slug_custom_attributes['disabled'] = 'disabled';
				}

				// Slug
				woocommerce_wp_text_input( array(
					'id'                => 'post_name',
					'label'             => __( 'Slug', 'woocommerce-order-status-manager' ),
					'value'             => $post->post_name,
					'custom_attributes' => $slug_custom_attributes,
					'desc_tip'          => true,
					'description'       => __( 'Optional. If left blank, the slug will be automatically generated from the name. Maximum: 17 characters, cannot contain leading numbers.', 'woocommerce-order-status-manager' ),
				) );

				// Description
				woocommerce_wp_textarea_input( array(
					'id'          => 'post_excerpt',
					'label'       => __( 'Description', 'woocommerce-order-status-manager' ),
					'desc_tip'    => true,
					'description' => __( 'Optional status description. If set, this will be shown to customers while viewing an order.', 'woocommerce-order-status-manager' ),
					'value'       => htmlspecialchars_decode( $post->post_excerpt, ENT_QUOTES ),
				) );

				?>
			</div><!-- // .options_group -->

			<div class="options_group">
				<?php

				// Color
				woocommerce_wp_text_input( array(
					'id'          => '_color',
					'label'       => __( 'Color', 'woocommerce-order-status-manager' ),
					'type'        => 'text',
					'class'       => 'colorpick',
					'default'     => '#000000',
					'description' => __( 'Color displayed behind the order status image or name', 'woocommerce-order-status-manager' ),
				) );

				// Status Icon
				$icon = $status->get_icon();
				$icon_attachment_src = '';

				if ( is_numeric( $icon ) ) {
					$icon_attachment_src = wp_get_attachment_image_src( $icon, 'wc_order_status_icon' );
				}

				?>
				<p class="form-field _icon_field">
					<label for="_icon"><?php esc_html_e( 'Icon', 'woocommerce-order-status-manager' ); ?></label>

					<input
						type="text"
						id="_icon"
						name="_icon"
						class="short"
						value="<?php echo esc_attr( $status->get_icon() ); ?>"
						data-icon-image="<?php echo esc_attr( $icon_attachment_src ? $icon_attachment_src[0] : '' ); ?>"
					/>

					<a href="#_icon" class="button button-small upload-icon upload-icon-image" data-uploader-button-text="<?php _e( 'Set as status icon', 'woocommerce-order-status-manager' ); ?>"><?php _e( "Select File", 'woocommerce-order-status-manager' ); ?></a>
					<a href="#_icon" class="button button-small remove-icon" ><?php esc_html_e( "Remove Icon", 'woocommerce-order-status-manager' ); ?></a>
					<?php echo wc_help_tip( __( 'Optional status icon. If not supplied, then Name will be displayed to represent the status', 'woocommerce-order-status-manager' ) ); ?>
				</p>

				<?php

				// Status Action Icon
				$action_icon = $status->get_action_icon();
				$action_icon_attachment_src = '';

				if ( is_numeric( $action_icon ) ) {
					$action_icon_attachment_src = wp_get_attachment_image_src( $action_icon, 'wc_order_status_icon' );
				}

				?>
				<p class="form-field _action_icon_field">
					<label for="_action_icon"><?php esc_html_e( 'Action Icon', 'woocommerce-order-status-manager' ); ?></label>

					<input
						type="text"
						id="_action_icon"
						name="_action_icon"
						class="short"
						value="<?php echo esc_attr( $status->get_action_icon() ); ?>"
						data-icon-image="<?php echo esc_attr( $action_icon_attachment_src ? $action_icon_attachment_src[0] : '' ); ?>"
					/>

					<a href="#_action_icon" class="button button-small upload-icon upload-icon-image" data-uploader-button-text="<?php esc_attr_e( 'Set as status icon', 'woocommerce-order-status-manager' ); ?>"><?php esc_html_e( "Select File", 'woocommerce-order-status-manager' ); ?></a>
					<a href="#_action_icon" class="button button-small remove-icon" ><?php esc_html_e( "Remove Icon", 'woocommerce-order-status-manager' ); ?></a>
					<?php echo wc_help_tip( __( 'Optional action icon displayed in the action buttons for the next statuses.', 'woocommerce-order-status-manager' ) ); ?>
				</p>
			</div><!-- // .options_group -->

			<div class="options_group">
				<?php

				// Next statuses
				$next_status_options = array();
				$selected = $status->get_next_statuses();
				$selected = $selected ? $selected : array();

				foreach ( wc_get_order_statuses() as $slug => $name ) {

					if ( $status->get_slug( true ) !== $slug ) {
						$next_status_options[ str_replace( 'wc-', '', $slug ) ] = $name;
					}
				}

				?>
				<p class="form-field _next_statuses_field">
					<label for="_next_statuses"><?php esc_html_e( 'Next Statuses', 'woocommerce-order-status-manager' ); ?></label>
					<select id="_next_statuses"
					        name="_next_statuses[]"
					        class="select short"
					        data-placeholder="<?php esc_html_e( 'Choose statuses that follow this one in your workflow.', 'woocommerce-order-status-manager' ); ?>"
					        multiple >
						<?php foreach ( $next_status_options as $slug => $name ) : ?>
							<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( in_array( $slug, $selected ), 1 ); ?>><?php echo esc_html( $name ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php echo wc_help_tip( __( 'Zero or more statuses that would be considered next during normal order status flow. Action buttons will be available to move an order with this custom status to these next statuses.', 'woocommerce-order-status-manager' ) ); ?>
				</p>
				<?php

				// Bulk action
				woocommerce_wp_checkbox( array(
					'id'          => '_bulk_action',
					'label'       => __( 'Bulk action', 'woocommerce-order-status-manager' ),
					'description' => __( 'Check this to add this order status to the Orders list table Bulk Actions list.', 'woocommerce-order-status-manager' ),
					'value'       => get_post_meta( $post->ID, '_bulk_action', true ),
				) );

				// Include in reports
				woocommerce_wp_checkbox( array(
					'id'          => '_include_in_reports',
					'label'       => __( 'Include in reports', 'woocommerce-order-status-manager' ),
					'description' => __( 'Check this to include orders with this order status in the order reports.', 'woocommerce-order-status-manager' ),
					'value'       => 'auto-draft' === $post->post_status ? 'yes' : get_post_meta( $post->ID, '_include_in_reports', true ),
				) );

				$is_paid_value             = get_post_meta( $post->ID, '_is_paid', true );
				$is_paid_desc_tip          = true;
				$is_paid_description       = __( 'Choose whether this status implies a payment that has been received or not.', 'woocommerce-order-status-manager' );
				$is_paid_custom_attributes = [];

				// for pending core status, this needs to be fixed to 'needs_payment' to prevent WooCommerce core issues with some payment gateways
				if ( 'pending' === $status->get_slug() && $status->is_core_status() ) {

					$is_paid_value        = 'needs_payment';
					$is_paid_desc_tip     = false;
					$is_paid_description  = '<span style="display: block; float: left; clear: both;"> ' . sprintf(
						/* translators: Placeholders: %s - 'Pending payment' order status name */
						__( 'The Paid setting for %s cannot be changed to avoid order processing issues.', 'woocommerce-order-status-manager' ), $status->get_name()
					) . '</span>';

					$is_paid_custom_attributes = [ 'custom_attributes' => [
						'readonly' => 'readonly',
						'disabled' => 'disabled',
					] ];
				}

				// Is Paid
				woocommerce_wp_select( array_merge( [
					'id'          => '_is_paid',
					'label'       => _x( 'Paid', 'Order status has had payment received', 'woocommerce-order-status-manager' ),
					'description' => $is_paid_description,
					'desc_tip'    => $is_paid_desc_tip,
					'value'       => $is_paid_value,
					'options'     => [
						'yes'           => __( 'Orders with this status have been paid.', 'woocommerce-order-status-manager' ),
						'needs_payment' => __( 'Orders with this status require payment (similar to "pending").', 'woocommerce-order-status-manager' ),
						'no'            => __( 'Orders are neither paid nor require payment (similar to "on-hold" or "refunded").', 'woocommerce-order-status-manager' ),
					],
				], $is_paid_custom_attributes ) );

				?>
			</div><!-- // .options_group -->
		</div><!-- // .woocommerce_options_panel -->
		<?php

	}


	/**
	 * Display the order status actions meta box
	 *
	 * @since 1.0.0
	 */
	public function order_status_actions_meta_box() {
		global $post;

		$status = new WC_Order_Status_Manager_Order_Status( $post->ID );

		?>
		<ul class="order_status_actions submitbox">
			<?php

			/**
			 * Fires at the start of the order status actions meta box
			 *
			 * @since 1.0.0
			 * @param int $post_id The post id of the wc_order_status post
			 */
			do_action( 'wc_order_status_manager_order_status_actions_start', $post->ID );

			?>
			<li class="wide">
				<div id="delete-action">
					<?php if ( ! $status->is_core_status() && current_user_can( "delete_post", $post->ID ) ) : ?>
						<a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID, '', true ) ); ?>"><?php esc_html_e( 'Delete', 'woocommerce-order-status-manager' ); ?></a>
					<?php endif; ?>
				</div>

				<input
					type="submit"
					class="button save_order_status save_action button-primary tips" name="publish"
					value="<?php esc_attr_e( 'Save Order Status', 'woocommerce-order-status-manager' ); ?>"
					data-tip="<?php esc_attr_e( 'Save/update the order status', 'woocommerce-order-status-manager' ); ?>"
				/>
			</li>
			<?php

			/**
			* Fires at the end of the order status actions meta box
			*
			* @since 1.0.0
			* @param int $post_id The post id of the wc_order_status post
			*/
			do_action( 'wc_order_status_manager_order_status_actions_end', $post->ID );

			?>
		</ul>
		<?php
	}


	/**
	 * Processes and saves order status meta.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id
	 */
	public function save_order_status_meta( $post_id ) {

		update_post_meta( $post_id, '_color',              $_POST['_color'] ?? '#000000' ); // provide a default color
		update_post_meta( $post_id, '_next_statuses',      $_POST['_next_statuses'] ?? '' );
		update_post_meta( $post_id, '_bulk_action',        wc_bool_to_string( ! empty( $_POST['_bulk_action'] ) ) );
		update_post_meta( $post_id, '_include_in_reports', wc_bool_to_string( ! empty( $_POST['_include_in_reports'] ) ) );
		update_post_meta( $post_id, '_icon',               $_POST['_icon'] );
		update_post_meta( $post_id, '_action_icon',        $_POST['_action_icon'] );

		$status = new \WC_Order_Status_Manager_Order_Status( $post_id );

		if ( 'pending' === $status->get_slug() && $status->is_core_status() ) {
			$is_paid = 'needs_payment';
		} else {
			$is_paid = $_POST['_is_paid'] ?? 'no';
		}

		update_post_meta( $post_id, '_is_paid', $is_paid );
	}


	/**
	 * Output actions on top of order status posts table
	 *
	 * @since 1.3.0
	 * @param array $actions Default actions
	 * @return array
	 */
	public function order_status_table_actions( $actions ) {

		return array(
			'import-custom-statuses' => ' <button id="import-custom-statuses" class="button help_tip" data-tip="' . esc_attr__( 'Add statuses that may have been added by other plugins or custom code.', 'woocommerce-order-status-manager' ) . '" >' .
			                            esc_html__( 'Import custom statuses', 'woocommerce-order-status-manager' ) .
			                            '</button> ',
		);
	}


	/**
	 * Moves the button to add existing custom statuses
	 * to bottom part of order status posts table
	 *
	 * @since 1.3.0
	 */
	public function move_add_custom_statuses_action() {
		global $current_screen;

		if ( 'wc_order_status' !== $current_screen->post_type ) {
			return;
		}

		?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				$( '#import-custom-statuses' ).prependTo( '.tablenav.bottom' );
			} );
		</script>
		<?php
	}


	/**
	 * Output custom column content
	 *
	 * @since 1.0.0
	 * @param string $column
	 * @param int $post_id
	 */
	public function custom_column_content( $column, $post_id ) {

		$status = new WC_Order_Status_Manager_Order_Status( $post_id );

		switch ( $column ) {

			case 'icon';

				$color = $status->get_color();
				$icon  = $status->get_icon();
				$style = '';

				if ( $color ) {

					if ( $icon ) {
						$style = 'color: ' . $color . ';';
					} else {
						$style = 'background-color: ' . $color . '; color: ' . wc_order_status_manager()->get_icons_instance()->get_contrast_text_color( $color ) . ';';
					}
				}

				if ( is_numeric( $icon ) ) {

					$icon_src = wp_get_attachment_image_src( $icon, 'wc_order_status_icon' );

					if ( $icon_src ) {
						$style .= 'background-image: url( ' . $icon_src[0] . ');';
					}
				}

				printf( '<mark class="%1$s %2$s tips" style="%3$s" data-tip="%4$s">%5$s</mark>', sanitize_title( $status->get_slug() ), ( $icon ? 'has-icon ' . $icon : '' ), $style, esc_attr( $status->get_name() ), esc_html( $status->get_name() ) );

			break;

			case 'slug':
				echo esc_html( $status->get_slug() );
			break;

			case 'description':
				echo esc_html( $status->get_description() );
			break;

			case 'reports' :
				echo esc_html( $status->include_in_reports() ? __( 'Yes', 'woocommerce-order-status-manager' ) : __( 'No', 'woocommerce-order-status-manager' ) );
			break;

			case 'paid':
				echo esc_html ( $status->is_paid() ? __( 'Yes', 'woocommerce-order-status-manager' ) : __( 'No', 'woocommerce-order-status-manager' ) );
			break;

			case 'type':

				if ( $status->is_core_status() ) : // $status->get_type() could be translated causing a CSS mismatch
					printf( '<span class="badge core">%s</span>', esc_html( $status->get_type() ) );
				else :
					printf( '<span class="badge %1$s">%2$s</span>', sanitize_title( $status->get_type() ), esc_html( $status->get_type() ) );
				endif;

			break;

		}
	}


	/**
	 * Popup to be shown when an order status is being deleted
	 * the user will be prompted to reassign existing orders with another status
	 * or confirm deletion and have order statuses reassigned automatically
	 *
	 * @since 1.3.0
	 */
	public function reassign_order_status_popup() {
		global $typenow;

		if ( 'wc_order_status' === $typenow ) :

			?>
			<div id="reassign-order-status-popup" style="display: none;">

				<h3><?php esc_html_e( 'Are you sure that you want to delete this order status?', 'woocommerce-order-status-manager' ); ?></h3>

				<p class="singular" style="display: none">
					<a class="order-status-link"  href=""><?php
						/* translators: singular order marked with status name - %1$s: orders count (1), %2$s: order status name */
						printf( __( 'There is currently %1$s order marked as %2$s.', 'woocommerce-order-status-manager' ), '<strong class="order-status-count"></strong>', '<strong class="order-status-name"></strong>' ); ?></a>
				</p>
				<p class="plural" style="display: none">
					<a class="order-status-link" href=""><?php
						/* translators: multiple orders marked with status name - %1$s orders count (n), %2$s: order status name */
						printf( __( 'There are currently %1$s orders marked as %2$s.', 'woocommerce-order-status-manager' ), '<strong class="order-status-count"></strong>', '<strong class="order-status-name"></strong>' ); ?></a>
				</p>

				<p><?php esc_html_e( 'You can choose to reassign the status of existing orders with another before deleting or have it reassigned automatically.', 'woocommerce-order-status-manager' ) ?></p>

				<label for="wc-order-status-manager-reassign-status">
					<select name="wc_order_status_manager_reassign_status" id="wc-order-status-manager-reassign-status"></select>
					<button class="button reassign"><?php esc_html_e( 'Reassign and delete', 'woocommerce-order-status-manager' ); ?></button>
				</label>

				<button class="button delete"><?php esc_html_e( 'Delete', 'woocommerce-order-status-manager' ); ?></button>

				<p>
					<em><?php esc_html_e( 'Either operation cannot be undone automatically.', 'woocommerce-order-status-manager' ); ?></em><br>
					<em><?php esc_html_e( 'Emails and other actions will not be triggered automatically.', 'woocommerce-order-status-manager' ); ?></em>
				</p>

			</div>

			<a href="#reassign-order-status-popup" id="reassign-order-status">&nbsp;</a>
			<?php

		endif;
	}


}
