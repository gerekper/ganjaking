<?php
/**
 * Handles taxonomies in admin.
 *
 * @package WC_Instagram/Admin
 * @since   3.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_Admin_Taxonomies class.
 */
class WC_Instagram_Admin_Taxonomies {

	/**
	 * Constructor.
	 *
	 * @since 3.6.0
	 */
	public function __construct() {
		add_action( 'product_cat_add_form_fields', array( $this, 'add_category_fields' ) );
		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_category_fields' ) );
		add_action( 'created_term', array( $this, 'save_category_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );
	}

	/**
	 * Outputs the product category fields when adding a new term.
	 *
	 * @since 3.6.0
	 */
	public function add_category_fields() {
		?>
		<div class="form-field term-google-product-category-wrap">
			<label for="google-product-category"><?php echo esc_html__( 'Google product category', 'woocommerce-instagram' ); ?></label>
			<input id="google-product-category" class="wc-instagram-gpc-field" type="hidden" name="google_product_category" value="0" />
			<p class="description"><?php echo esc_html_x( 'A product category value provided by Google feed.', 'product data setting desc', 'woocommerce-instagram' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Outputs the product category fields when editing the term.
	 *
	 * @since 3.6.0
	 *
	 * @param WP_Term $term Term being edited.
	 */
	public function edit_category_fields( $term ) {
		$category_id = get_term_meta( $term->term_id, 'instagram_google_product_category', true );
		?>
		<tr class="form-field term-google-product-category-wrap">
			<th scope="row">
				<label for="google-product-category"><?php echo esc_html__( 'Google product category', 'woocommerce-instagram' ); ?></label>
			</th>
			<td>
				<input id="google-product-category" class="wc-instagram-gpc-field" type="hidden" name="google_product_category" value="<?php echo esc_attr( $category_id ); ?>" />
				<p class="description"><?php echo esc_html_x( 'A product category value provided by Google feed.', 'product data setting desc', 'woocommerce-instagram' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Saves the product category fields.
	 *
	 * @since 3.6.0
	 *
	 * @param mixed  $term_id  Term ID being saved.
	 * @param mixed  $tt_id    Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( 'product_cat' !== $taxonomy ) {
			return;
		}

		$product_cat = ( isset( $_POST['google_product_category'] ) ? wc_clean( wp_unslash( $_POST['google_product_category'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification

		if ( $product_cat ) {
			update_term_meta( $term_id, 'instagram_google_product_category', $product_cat );
		} else {
			delete_term_meta( $term_id, 'instagram_google_product_category' );
		}
	}
}

return new WC_Instagram_Admin_Taxonomies();
