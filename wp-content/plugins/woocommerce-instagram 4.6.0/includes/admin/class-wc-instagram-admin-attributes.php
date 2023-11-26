<?php
/**
 * Handles the WC Attributes in admin.
 *
 * @package WC_Instagram/Admin
 * @since   3.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_Admin_Attributes class.
 */
class WC_Instagram_Admin_Attributes {

	/**
	 * Constructor.
	 *
	 * @since 3.7.0
	 */
	public function __construct() {
		add_action( 'woocommerce_after_add_attribute_fields', array( $this, 'add_fields' ) );
		add_action( 'woocommerce_after_edit_attribute_fields', array( $this, 'edit_fields' ) );
		add_action( 'woocommerce_attribute_added', array( $this, 'save' ) );
		add_action( 'woocommerce_attribute_updated', array( $this, 'save' ) );
		add_action( 'woocommerce_attribute_deleted', array( $this, 'save' ) );

		$attributes = wc_get_attribute_taxonomies();

		foreach ( $attributes as $attribute ) {
			add_action( 'pa_' . $attribute->attribute_name . '_add_form_fields', array( $this, 'add_term_fields' ) );
			add_action( 'pa_' . $attribute->attribute_name . '_edit_form_fields', array( $this, 'edit_term_fields' ), 10, 2 );
		}

		add_action( 'created_term', array( $this, 'save_term' ), 10, 3 );
		add_action( 'edited_term', array( $this, 'save_term' ), 10, 3 );
	}

	/**
	 * Outputs custom fields when adding a new attribute.
	 *
	 * @since 3.7.0
	 */
	public function add_fields() {
		?>
		<div class="form-field">
			<label for="attribute_google_pa"><?php echo esc_html__( 'Google attribute', 'woocommerce-instagram' ); ?></label>
			<?php self::output_google_attribute_field(); ?>
			<p class="description"><?php esc_html_e( 'Associated Google attribute.', 'woocommerce-instagram' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Outputs custom fields when editing an attribute.
	 *
	 * @since 3.7.0
	 */
	public function edit_fields() {
		$attribute_id = ( isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0 ); // phpcs:ignore WordPress.Security.NonceVerification
		$value        = WC_Instagram_Attributes::get_meta( $attribute_id, 'google_pa' );
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="attribute_google_pa"><?php echo esc_html__( 'Google attribute', 'woocommerce-instagram' ); ?></label>
			</th>
			<td>
				<?php self::output_google_attribute_field( $value ); ?>
				<p class="description"><?php esc_html_e( 'Associated Google attribute.', 'woocommerce-instagram' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Saves an attribute.
	 *
	 * @since 3.7.0
	 *
	 * @param int $attribute_id Attribute ID.
	 */
	public function save( $attribute_id ) {
		$nonce = ( ! empty( $_REQUEST['_wpnonce'] ) ? wc_clean( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '' );

		// Not using the admin forms.
		if ( ! $nonce || (
			! wp_verify_nonce( $nonce, 'woocommerce-add-new_attribute' ) &&
			! wp_verify_nonce( $nonce, 'woocommerce-save-attribute_' . $attribute_id ) &&
			! wp_verify_nonce( $nonce, 'woocommerce-delete-attribute_' . $attribute_id )
		) ) {
			return;
		}

		$attribute = ( isset( $_POST['attribute_google_pa'] ) ? wc_clean( wp_unslash( $_POST['attribute_google_pa'] ) ) : '' );

		if ( $attribute ) {
			WC_Instagram_Attributes::set_meta( $attribute_id, 'google_pa', $attribute );
		} else {
			WC_Instagram_Attributes::delete_meta( $attribute_id, 'google_pa' );
		}
	}

	/**
	 * Outputs custom fields when adding a new attribute term.
	 *
	 * @since 3.7.0
	 *
	 * @param string $taxonomy Taxonomy slug.
	 */
	public function add_term_fields( $taxonomy ) {
		$attribute = $this->get_google_attribute( $taxonomy );

		if ( ! $attribute || empty( $attribute['options'] ) ) {
			return;
		}

		/* translators: %s Google attribute label */
		$label = sprintf( __( 'Google "%s" value', 'woocommerce-instagram' ), $attribute['label'] );
		?>
		<div class="form-field term-google-pa-value-wrap">
			<label for="google_pa_value"><?php echo esc_html( $label ); ?></label>
			<?php self::output_google_attribute_options_field( array( 'options' => $attribute['options'] ) ); ?>
			<p class="description"><?php esc_html_e( 'This Google attribute only accepts specific values.', 'woocommerce-instagram' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Outputs custom fields when editing an attribute term.
	 *
	 * @since 3.7.0
	 *
	 * @param WP_Term $term     Term object.
	 * @param string  $taxonomy Taxonomy slug.
	 */
	public function edit_term_fields( $term, $taxonomy ) {
		$attribute = $this->get_google_attribute( $taxonomy );

		if ( ! $attribute || empty( $attribute['options'] ) ) {
			return;
		}

		/* translators: %s Google attribute label */
		$label = sprintf( __( 'Google "%s" value', 'woocommerce-instagram' ), $attribute['label'] );

		$args = array(
			'options' => $attribute['options'],
			'value'   => get_term_meta( $term->term_id, 'google_pa_value', true ),
		);
		?>
		<tr class="form-field term-google-pa-value-wrap">
			<th scope="row">
				<label for="google_pa_value"><?php echo esc_html( $label ); ?></label>
			</th>
			<td>
				<?php self::output_google_attribute_options_field( $args ); ?>
				<p class="description"><?php esc_html_e( 'This Google attribute only accepts specific values.', 'woocommerce-instagram' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Saves an attribute term.
	 *
	 * @since 3.7.0
	 *
	 * @param int    $term_id  Term ID.
	 * @param int    $tt_id    Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	public function save_term( $term_id, $tt_id, $taxonomy ) {
		// Not an attribute term.
		if ( 0 !== strpos( $taxonomy, 'pa_' ) ) {
			return;
		}

		// Not using the admin forms.
		if (
			empty( $_POST ) ||
			( ! isset( $_POST['_wpnonce_add-tag'] ) && ! isset( $_POST['_wpnonce'] ) ) ||
			( isset( $_POST['_wpnonce_add-tag'] ) && ! wp_verify_nonce( wc_clean( wp_unslash( $_POST['_wpnonce_add-tag'] ) ), 'add-tag' ) ) ||
			( isset( $_POST['_wpnonce'] ) && ! wp_verify_nonce( wc_clean( wp_unslash( $_POST['_wpnonce'] ) ), 'update-tag_' . $term_id ) )
		) {
			return;
		}

		$option = ( isset( $_POST['google_pa_value'] ) ? wc_clean( wp_unslash( $_POST['google_pa_value'] ) ) : '' );

		if ( $option ) {
			update_term_meta( $term_id, 'google_pa_value', $option );
		} else {
			delete_term_meta( $term_id, 'google_pa_value' );
		}
	}

	/**
	 * Gets the Google attribute associated with the specified attribute.
	 *
	 * @since 3.7.0
	 *
	 * @param string $attribute_name Attribute name.
	 * @return array|false An array with the attribute data. False if not found.
	 */
	protected function get_google_attribute( $attribute_name ) {
		$attribute_id = wc_attribute_taxonomy_id_by_name( $attribute_name );
		$google_pa    = WC_Instagram_Attributes::get_meta( $attribute_id, 'google_pa' );

		return ( $google_pa ? WC_Instagram_Google_Product_Attributes::get_attribute( $google_pa ) : false );
	}

	/**
	 * Outputs the Google attribute select field.
	 *
	 * @since 3.7.0
	 *
	 * @param string $selected Optional. The selected value. Default empty.
	 */
	protected function output_google_attribute_field( $selected = '' ) {
		$attributes = WC_Instagram_Google_Product_Attributes::get_attributes();
		?>
		<select id="attribute_google_pa" name="attribute_google_pa">
			<option value=""><?php esc_html_e( 'Select an attribute&hellip;', 'woocommerce-instagram' ); ?></option>
			<?php
			foreach ( $attributes as $key => $attribute ) :
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $key ),
					selected( $key, $selected, false ),
					esc_html( $attribute['label'] )
				);
			endforeach;
			?>
		</select>
		<?php
	}

	/**
	 * Outputs a select field with the available Google attribute options.
	 *
	 * @since 3.7.0
	 *
	 * @param array $args Optional. The field arguments. Default empty.
	 */
	protected function output_google_attribute_options_field( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'options' => array(),
				'value'   => '',
			)
		);

		$args['options'] = array( '' => __( 'Select an option&hellip;', 'woocommerce-instagram' ) ) + $args['options'];
		?>
		<select id="google_pa_value" name="google_pa_value">
			<?php
			foreach ( $args['options'] as $value => $label ) :
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $value ),
					selected( $value, $args['value'], false ),
					esc_html( $label )
				);
			endforeach;
			?>
		</select>
		<?php
	}
}

return new WC_Instagram_Admin_Attributes();
