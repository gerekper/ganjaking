<?php
/**
 * Class to handle displaying of checkout fields on order details page and emails.
 *
 * @package WooCommerce/Checkout_Field_Editor
 */

/**
 * Class WC_Checkout_Field_Editor_Order_Details
 */
class WC_Checkout_Field_Editor_Order_Details {

	/**
	 * Register Hooks
	 */
	public function register_hooks() {
		add_filter( 'woocommerce_locate_template', array( $this, 'woocommerce_locate_template' ), 10, 3 );
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'after_order_table' ), 20, 1 );
		add_action( 'woocommerce_email_after_order_table', array( $this, 'email_after_order_table' ), 20, 4 );
	}

	/**
	 * Filter located template to load template from our plugin.
	 *
	 * @param string $template      Template found other places.
	 * @param string $template_name Name of template we are looking for.
	 * @param string $template_path Current path we are looking in.
	 *
	 * @return string
	 */
	public function woocommerce_locate_template( $template, $template_name, $template_path ) {
		if ( ! in_array(
			$template_name,
			array(
				'emails/email-addresses.php',
				'emails/plain/email-addresses.php',
				'order/order-details-customer.php',
			),
			true
		) ) {
			return $template;
		}

		// Look within passed path within the theme - this is priority.
		$template = locate_template(
			array(
				$template_path . $template_name,
				$template_name,
			)
		);

		if ( $template ) {
			return $template;
		}

		return WC_CHECKOUT_FIELD_EDITOR_PATH . '/templates/' . $template_name;
	}

	/**
	 * Display the additional fields.
	 *
	 * @param WC_Order $order Current Order.
	 */
	public function after_order_table( $order ) {
		self::display_custom_fields( $order, 'additional' );
	}

	/**
	 * Display the additional fields in emails.
	 *
	 * @param WC_Order $order Current Order.
	 * @param bool     $sent_to_admin If we are sending email to admin.
	 * @param bool     $plain_text If we are sending plain text email.
	 * @param string   $email Email address.
	 */
	public function email_after_order_table( $order, $sent_to_admin, $plain_text, $email ) {
		if ( $plain_text ) {
			self::display_custom_fields_plain( $order, 'additional' );
		} else {
			self::display_custom_fields_email( $order, 'additional' );
		}
	}

	/**
	 * Output the custom fields formatted for html page.
	 *
	 * @param WC_Order $order Current Order.
	 * @param string   $type Type of field we are displaying.
	 */
	public static function display_custom_fields( $order, $type ) {
		$fields = self::get_custom_checkout_fields( $order, $type, 'view_order' );

		if ( $fields ) {
			echo '<dl>';
			foreach ( $fields as $field ) {
				?>
				<dt><?php echo esc_html( $field['label'] ); ?></dt>
				<dd><?php echo wp_kses_post( nl2br( wptexturize( $field['value'] ) ) ); ?></dd>
				<?php
			}
			echo '</dl>';
		}
	}

	/**
	 * Display Custom fields formatted for email.
	 *
	 * @param WC_Order $order Current Order.
	 * @param string   $type Type of field we are displaying.
	 */
	public static function display_custom_fields_email( $order, $type ) {
		$fields = self::get_custom_checkout_fields( $order, $type, 'emails' );

		foreach ( $fields as $field ) {
			?>
			<p>
				<strong><?php echo esc_html( $field['label'] ); ?>: </strong>
				<?php echo wp_kses_post( nl2br( wptexturize( $field['value'] ) ) ); ?>
			</p>
			<?php
		}
	}

	/**
	 * Display Custom fields formatted for plain text email.
	 *
	 * @param WC_Order $order Current Order.
	 * @param string   $type Type of field we are displaying.
	 */
	public static function display_custom_fields_plain( $order, $type ) {
		$fields = self::get_custom_checkout_fields( $order, $type, 'emails' );

		foreach ( $fields as $field ) {
			echo esc_html( wp_strip_all_tags( $field['label'] ) ) . ': ' . esc_html( wp_strip_all_tags( $field['value'] ) ) . "\n";
		}
	}

	/**
	 * Fetch custom checkout fields filtered by type and context.
	 *
	 * @param WC_Order $order Current Order.
	 * @param string   $type Type to filter by.
	 * @param string   $context Context to filter by.
	 *
	 * @return array
	 */
	private static function get_custom_checkout_fields( $order, $type, $context ) {
		$fields = wc_get_custom_checkout_fields( $order, array( $type ) );

		$field_values = array();

		foreach ( $fields as $name => $options ) {
			$option_value = wc_get_checkout_field_value( $order, $name, $options );
			if ( isset( $options['display_options'] ) && in_array( $context, $options['display_options'], true ) && '' !== $option_value ) {
				$field_values[] = array(
					'label' => $options['label'],
					'value' => $option_value,
				);
			}
		}

		return $field_values;
	}
}
