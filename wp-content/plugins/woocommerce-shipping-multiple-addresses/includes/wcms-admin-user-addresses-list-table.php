<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WC_MS_Admin_User_Addresses_List_Table extends WP_List_Table {
	public $user;

	/**
	 * Create and instance of this list table.
	 * @param WP_User $user
	 */
	public function __construct( $user ) {
		$this->user = $user;
		parent::__construct( array(
			'singular'  => 'address',
			'plural'    => 'addresses',
			'ajax'      => false,
		) );
	}

	/**
	 * List of columns
	 * @return array
	 */
	public function get_columns() {
		return array(
			'address'   => __( 'Address', 'wc_shipping_multiple_address' ),
		);
	}

	/**
	 * List of sortable columns
	 * @return array
	 */
	public function get_sortable_columns() {
		return array();
	}

	public function prepare_items() {
		global $wcms;

		$wcms->cart->load_cart_files();

		$columns    = $this->get_columns();
		$hidden     = array();

		$sortable   = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$addresses  = $wcms->address_book->get_user_addresses( $this->user, false );
		$items      = array();

		foreach ( $addresses as $index => $address ) {
			$items[] = array( 'index' => $index, 'address' => $address );
		}

		$this->items = $items;
	}

	public function column_address( $item ) {
		$out = '<div class="address">' . wcms_get_formatted_address( $item['address'] ) . '</div>';

		// Get actions
		$actions = array(
			'edit'  => '<a class="edit-address" data-index="' . $item['index'] . '" title="' . esc_attr( __( 'Edit', 'wc_shipping_multiple_address' ) ) . '" href="#">' . __( 'Edit', 'wc_shipping_multiple_address' ) . '</a>',
			'trash' => '<a class="submitdelete" title="' . esc_attr( __( 'Delete', 'wc_shipping_multiple_address' ) ) . '" href="admin-post.php?action=wcms_delete_address&index=' . $item['index'] . '&user_id=' . $this->user->ID . '&_wpnonce=' . wp_create_nonce( 'delete_shipping_address' ) . '">' . __( 'Delete', 'follow_up_emails' ) . '</a>',
		);

		$row_actions = array();

		foreach ( $actions as $action => $link ) {
			$row_actions[] = '<span class="' . esc_attr( $action ) . '">' . $link . '</span>';
		}

		$out .= '<div class="row-actions">' . implode( ' | ', $row_actions ) . '</div>';

		return $out;
	}

	public function single_row( $item ) {
		$address    = $item['address'];
		$fields     = WC()->countries->get_address_fields( $address['shipping_country'], 'wcms_shipping_' );
		?>
		<tr id="address-<?php echo $item['index']; ?>">
			<?php $this->single_row_columns( $item ); ?>
		</tr>
		<tr></tr>
		<tr style="display: none;" class="address-form" id="address-form-<?php echo $item['index']; ?>" data-index="<?php echo $item['index']; ?>">
			<td>
				<div class="address-column">
				<?php
				foreach ( $fields as $key => $field ) {
					$val = ( isset( $address[ substr( $key, 5 ) ] ) ) ? $address[ substr( $key, 5 ) ] : '';

					if ( empty( $val ) && ! empty( $_GET[ $key ] ) ) {
						$val = $_GET[ $key ];
					}

					echo woocommerce_form_field( $key, $field, $val );
				}
				?>
				</div>

				<p class="submit">
					<input type="button" class="button btn-cancel" value="<?php _e( 'Cancel', 'wc_shipping_multiple_address' ); ?>" />
					<input type="button" class="button-primary btn-save" value="<?php _e( 'Save Address', 'wc_shipping_multiple_address' ); ?>" />
				</p>
			</td>
		</tr>
		<?php
	}

	public function display_tablenav( $which ) {}
}
