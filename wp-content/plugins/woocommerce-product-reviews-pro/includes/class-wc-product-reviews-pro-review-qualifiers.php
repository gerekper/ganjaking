<?php
/**
 * WooCommerce Product Reviews Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Review Qualifiers class
 *
 * Handles stuff related to review qualifiers
 *
 * @since 1.0.0
 */
class WC_Product_Reviews_Pro_Review_Qualifiers {


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'create_review_qualifier_taxonomy' ) );

		add_action( 'product_review_qualifier_edit_form_fields', array( $this, 'edit_qualifier_options_field' ) );
		add_action( 'product_review_qualifier_add_form_fields',  array( $this, 'add_qualifier_options_field' ) );

		add_action( 'edited_product_review_qualifier',  array( $this, 'save_qualifier_options' ) );
		add_action( 'created_product_review_qualifier', array( $this, 'save_qualifier_options' ) );
		add_action( 'delete_product_review_qualifier',  array( $this, 'delete_qualifier_options' ) );

		// Save review qualifier data
		add_action( 'comment_post', array( $this, 'add_review_qualifier_data' ), 2 );
	}


	/**
	 * Create review qualifier taxonomy
	 *
	 * @since 1.0.0
	 */
	public function create_review_qualifier_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Review Qualifiers', 'taxonomy general name', 'woocommerce-product-reviews-pro' ),
			'singular_name'              => _x( 'Review Qualifier', 'taxonomy singular name', 'woocommerce-product-reviews-pro' ),
			'search_items'               => __( 'Search Review Qualifiers', 'woocommerce-product-reviews-pro' ),
			'all_items'                  => __( 'All Review Qualifiers', 'woocommerce-product-reviews-pro' ),
			'edit_item'                  => __( 'Edit Review Qualifier', 'woocommerce-product-reviews-pro' ),
			'view_item'                  => __( 'View Review Qualifier', 'woocommerce-product-reviews-pro' ),
			'update_item'                => __( 'Update Review Qualifier', 'woocommerce-product-reviews-pro' ),
			'add_new_item'               => __( 'Add New Review Qualifier', 'woocommerce-product-reviews-pro' ),
			'new_item_name'              => __( 'New Review Qualifier Name', 'woocommerce-product-reviews-pro' ),
			'separate_items_with_commas' => __( 'Separate qualifiers with commas', 'woocommerce-product-reviews-pro' ),
			'add_or_remove_items'        => __( 'Add or remove review qualifiers', 'woocommerce-product-reviews-pro' ),
			'choose_from_most_used'      => __( 'Choose from the most used review qualifiers', 'woocommerce-product-reviews-pro' ),
			'not_found'                  => __( 'No review qualifiers found.', 'woocommerce-product-reviews-pro' ),
			'menu_name'                  => __( 'Review Qualifiers', 'woocommerce-product-reviews-pro' ),
			'popular_items'              => __( 'Most used Qualifiers', 'woocommerce-product-reviews-pro' ),
		);

		/**
		 * Filter the review qualifier taxonomy arguments
		 *
		 * @since 1.0.0
		 * @param array $args The review qualifier taxonomy arguments.
		 */
		$args = apply_filters( 'wc_product_reviews_pro_review_qualifier_taxonomy_args', array(
			'hierarchical'      => false,
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => false,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
			'labels'            => $labels,
		) );

		register_taxonomy( 'product_review_qualifier', 'product', $args );
	}


	/**
	 * Output review qualifier options field HTML for edit form
	 *
	 * @since 1.0.0
	 * @param \WP_Term $term
	 */
	public function edit_qualifier_options_field( $term ) {

		$options = get_term_meta( $term->term_id, 'options', true );

		?>
		<tr class="form-field">
			<th scope="row"><label for="options"><?php echo esc_html_x( 'Options', 'Review qualifier options', 'woocommerce-product-reviews-pro' ); ?></label></th>
			<td><textarea name="options" id="options" rows="5" cols="50" class="large-text"><?php echo esc_textarea( $options ); ?></textarea><br>
			<span class="description"><?php esc_html_e( 'List review qualifier options above (1 per line).' ); ?></span></td>
		</tr>
		<?php
	}


	/**
	 * Output review qualifier options field HTML for add form
	 *
	 * @since 1.0.0
	 * @param \WP_Term $term
	 */
	public function add_qualifier_options_field( $term ) {

		$options = is_object( $term ) ? get_term_meta( $term->term_id, 'options', true ) : '';

		?>
		<div class="form-field">
			<label for="tag-options"><?php echo esc_html_x( 'Options', 'Review qualifier options', 'woocommerce-product-reviews-pro' ); ?></label>
			<textarea name="options" id="tag-options" rows="5" cols="40"><?php echo esc_textarea( $options ); ?></textarea>
			<p><?php esc_html_e( 'List review qualifier options above (1 per line).' ); ?></p>
		</div>
		<?php
	}


	/**
	 * Save review qualifier options
	 *
	 * @since 1.0.0
	 * @param int $term_id
	 */
	public function save_qualifier_options( $term_id ) {

		if ( $term_id && isset( $_POST['options'] ) ) {

			$sanitized_options = implode( "\n", array_map( 'wc_clean', explode( "\n", $_POST['options'] ) ) );

			update_term_meta( $term_id, 'options', $sanitized_options );
		}
	}


	/**
	 * Delete review qualifier options after a term has been deleted
	 *
	 * @since 1.0.0
	 * @param int $term_id
	 */
	public function delete_qualifier_options( $term_id ) {

		delete_term_meta( $term_id, 'options' );
	}


	/**
	 * Add review qualifier data when adding a comment from frontend
	 *
	 * @since 1.0.0
	 * @param int $comment_id
	 */
	public function add_review_qualifier_data( $comment_id ) {

		$comment = get_comment( $comment_id );

		// Bail out if not a review
		if ( 'review' !== $comment->comment_type ) {
			return;
		}

		// Look up if the comment post (product) has any review qualifiers
		$review_qualifiers = wp_get_post_terms( $comment->comment_post_ID, 'product_review_qualifier' );

		// Bail out if no review qualifiers
		if ( ! $review_qualifiers || empty( $review_qualifiers ) ) {
			return;
		}

		foreach ( $review_qualifiers as $review_qualifier ) {

			$key = 'wc_product_reviews_pro_review_qualifier_' . $review_qualifier->term_id;

			// Skip qualifier if no data posted
			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}

			// Save posted review qualifier data
			update_comment_meta( $comment_id, $key, wc_clean( $_POST[$key] ) );
		}
	}


}
