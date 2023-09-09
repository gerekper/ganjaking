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
			'address' => esc_html__( 'Address', 'wc_shipping_multiple_address' ),
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
		// No need to escape. It's already escaped on `wcms_get_formatted_address()`.
		$out = '<div class="address">' . wcms_get_formatted_address( $item['address'] ) . '</div>';// phpcs:ignore

		// Get actions
		$delete_url = admin_url( 'admin-post.php?action=wcms_delete_address&index=' . $item['index'] . '&user_id=' . intval( $this->user->ID ) . '&_wpnonce=' . wp_create_nonce( 'delete_shipping_address' ) );
		$actions = array(
			'edit'  => '<a class="edit-address" data-index="' . esc_attr( $item['index'] ) . '" title="' . esc_attr__( 'Edit', 'wc_shipping_multiple_address' ) . '" href="#">' . esc_html__( 'Edit', 'wc_shipping_multiple_address' ) . '</a>',
			'trash' => '<a class="submitdelete" title="' . esc_attr__( 'Delete', 'wc_shipping_multiple_address' ) . '" href="' . esc_url( $delete_url ) . '">' . esc_html__( 'Delete', 'follow_up_emails' ) . '</a>',
		);

		$row_actions = array();

		foreach ( $actions as $action => $link ) {
			$row_actions[] = '<span class="' . esc_attr( $action ) . '">' . $link . '</span>';
		}

		$out .= '<div class="row-actions">' . implode( ' | ', $row_actions ) . '</div>';

		return $out;
	}

	public function single_row( $item ) {
		$address              = $item['address'];
		$default_address_keys = array_keys( WC()->countries->get_default_address_fields() );
		$fields               = WC()->countries->get_address_fields( $address['shipping_country'], 'wcms_shipping_' );
		?>
		<tr id="address-<?php echo esc_attr( $item['index'] ); ?>">
			<?php $this->single_row_columns( $item ); ?>
		</tr>
		<tr></tr>
		<tr style="display: none;" class="address-form" id="address-form-<?php echo esc_attr( $item['index'] ); ?>" data-index="<?php echo esc_attr( $item['index'] ); ?>">
			<td>
				<div class="address-column">
				<?php
				foreach ( $fields as $key => $field ) {
					$val         = ( isset( $address[ substr( $key, 5 ) ] ) ) ? $address[ substr( $key, 5 ) ] : '';
					$default_key = str_replace( array( 'wcms_shipping_', 'shipping_' ), '', $key );

					// Get the value for non default address fields.
					if ( empty( $val ) && isset( $address[ $key ] ) && ! in_array( $default_key, $default_address_keys ) ) {
						$val = $address[ $key ];
					}

					$val = ( empty( $val ) && ! empty( $_GET[ $key ] ) ) ? sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) : $val; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

					// No need to escape. `woocommerce_form_field()` has been escaped.
					echo woocommerce_form_field( $key, $field, $val ); //phpcs:ignore
				}
				?>
				</div>

				<p class="submit">
					<input type="button" class="button btn-cancel" value="<?php esc_html_e( 'Cancel', 'wc_shipping_multiple_address' ); ?>" />
					<input type="button" class="button-primary btn-save" value="<?php esc_html_e( 'Save Address', 'wc_shipping_multiple_address' ); ?>" />
				</p>
			</td>
		</tr>
		<?php
	}

	public function display_tablenav( $which ) {}
}
