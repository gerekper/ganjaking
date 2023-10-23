<?php
/**
 * Plugins Admin Functions
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_wcmap_admin_print_single_item' ) ) {
	/**
	 * Print single item field options
	 *
	 * @since  1.0.0
	 * @param array $args Template args array.
	 */
	function yith_wcmap_admin_print_single_item( $args ) {

		// Let third part filter template args.
		/**
		 * APPLY_FILTERS: yith_wcmap_admin_print_single_item_args
		 *
		 * Filters the array with the arguments needed to print the item in the plugin panel.
		 *
		 * @param array Array of arguments.
		 *
		 * @return array
		 */
		$args = apply_filters( 'yith_wcmap_admin_print_single_item_args', $args );

		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		$options['content'] = ! empty( $options['content'] ) ? stripslashes( $options['content'] ) : '';
		$is_default         = yith_wcmap_is_plugin_item( $item_key ) || yith_wcmap_is_default_item( $item_key );
		$item_key           = rawurldecode( $item_key );

		// Get item fields.
		$fields = yith_wcmap_admin_get_fields( $type );
		// If content position field is set and the endpoint is not a default one, hide this field option and force if to default value.
		if ( isset( $fields['content_position'] ) && ! $is_default ) {
			$fields['content_position']['type'] = 'hidden';
			$options['content_position']        = 'override';
		}

		/**
		 * DO_ACTION: yith_wcmap_admin_before_single_item
		 *
		 * Allows to render some content before printing the item in the plugin panel.
		 *
		 * @param string $item_key Item key.
		 * @param array  $options  Item options.
		 * @param string $type     Item type.
		 */
		do_action( 'yith_wcmap_admin_before_single_item', $item_key, $options, $type );

		include YITH_WCMAP_DIR . 'includes/admin/views/single-item.php';

		/**
		 * DO_ACTION: yith_wcmap_admin_after_single_item
		 *
		 * Allows to render some content after printing the item in the plugin panel.
		 *
		 * @param string $item_key Item key.
		 * @param array  $options  Item options.
		 * @param string $type     Item type.
		 */
		do_action( 'yith_wcmap_admin_after_single_item', $item_key, $options, $type );
	}
}

if ( ! function_exists( 'yith_wcmap_get_editable_roles' ) ) {
	/**
	 * Get editable roles for endpoints
	 *
	 * @since  2.0.0
	 * @return array
	 */
	function yith_wcmap_get_editable_roles() {
		$usr_roles = wp_cache_get( 'yith_wcmap_user_roles', 'yith_wcmap' );
		if ( empty( $usr_roles ) ) {
			// Get user role.
			$roles     = get_editable_roles();
			$usr_roles = array();
			foreach ( $roles as $key => $role ) {
				if ( empty( $role['capabilities'] ) ) {
					continue;
				}
				$usr_roles[ $key ] = $role['name'];
				wp_cache_set( 'yith_wcmap_user_roles', $usr_roles, 'yith_wcmap' );
			}
		}

		return $usr_roles;
	}
}

if ( ! function_exists( 'yith_wcmap_admin_get_fields' ) ) {
	/**
	 * Get admin items fields
	 *
	 * @since 3.0.0
	 * @param string $type The field type.
	 * @return array
	 */
	function yith_wcmap_admin_get_fields( $type ) {

		$fields = wp_cache_get( 'yith_wcmap_items_fields', 'yith_wcmap' );
		if ( empty( $fields ) && file_exists( YITH_WCMAP_DIR . 'plugin-options/items-options-fields.php' ) ) {
			$fields = include YITH_WCMAP_DIR . 'plugin-options/items-options-fields.php';

			wp_cache_set( 'yith_wcmap_items_fields', $fields, 'yith_wcmap' );
		}

		return isset( $fields[ $type ] ) ? $fields[ $type ] : array();
	}
}

if ( ! function_exists( 'yith_wcmap_admin_print_single_field' ) ) {
	/**
	 * Print single admin field
	 *
	 * @since 3.0.0
	 * @param array  $field_options The field options.
	 * @param string $field The field.
	 * @param string $item_key The item key.
	 * @param mixed  $value The value.
	 * @return void
	 */
	function yith_wcmap_admin_print_single_field( $field_options, $field, $item_key, $value = '' ) {

		$field_options['id']    = $item_key . "_{$field}";
		$field_options['name']  = $item_key . "[{$field}]";
		$field_options['value'] = $value ? $value : ( isset( $field_options['default'] ) ? $field_options['default'] : '' );

		if ( empty( $field_options['custom_attributes'] ) ) {
			$field_options['custom_attributes'] = array();
		}
		if ( empty( $field_options['type'] ) ) {
			$field_options['type'] = 'text';
		}

		if ( 'icon-select' === $field_options['type'] ) {
			$field_options['type']                                 = 'select';
			$field_options['custom_attributes']['data-icon_value'] = $field_options['value'];
		}

		$field_deps = '';
		if ( isset( $field_options['deps'] ) ) {
			$deps = explode( ',', $field_options['deps']['ids'] );
			foreach ( $deps as &$dep ) {
				$dep = $item_key . "\\[{$dep}\\]";
			}

			$field_deps = 'data-deps="' . esc_attr( implode( ',', $deps ) ) . '" data-deps_value="' . esc_attr( $field_options['deps']['values'] ) . '"';
		}

		$tr_class = ( 'hidden' === $field_options['type'] ) ? 'class="hidden"' : '';

		?>
		<tr <?php echo $tr_class; ?> <?php echo $field_deps; ?>>
			<th>
				<label for="<?php echo esc_attr( $field_options['id'] ); ?>">
					<?php echo esc_html( $field_options['label'] ); ?>
				</label>
			</th>
			<td>
				<?php yith_plugin_fw_get_field( $field_options, true, false ); ?>
				<?php if ( ! empty( $field_options['desc'] ) ) : ?>
					<span class="description"><?php echo wp_kses_post( $field_options['desc'] ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}
}
