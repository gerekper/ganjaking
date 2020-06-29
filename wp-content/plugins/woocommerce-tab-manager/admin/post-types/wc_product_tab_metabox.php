<?php
/**
 * WooCommerce Tab Manager
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Tab Manager to newer
 * versions in the future. If you wish to customize WooCommerce Tab Manager for your
 * needs please refer to http://docs.woocommerce.com/document/tab-manager/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_4_0 as Framework;

function wc_tab_manager_add_metabox() {
	new \WC_Tab_Manager_Categories_Metabox();
}

if ( is_admin() ) {
	add_action( 'load-post.php',     'wc_tab_manager_add_metabox' );
	add_action( 'load-post-new.php', 'wc_tab_manager_add_metabox' );
}


/**
 * Tab Manager Product Categories selector metabox
 *
 * @since 1.7.0
 */
class WC_Tab_Manager_Categories_Metabox {

	/**
	 * WC_Tab_Manager_Categories_Metabox constructor
	 *
	 * @since 1.7.0
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 100 );
		add_action( 'save_post', array( $this, 'save' ) );
	}


	/**
	 * Adds the meta box container
	 *
	 * @since 1.7.0
	 */
	public function add_meta_box() {

		add_meta_box(
			'tab_categories',
			__( 'Product Categories', 'textdomain' ),
			array( $this, 'render_meta_box_content' ),
			'wc_product_tab',
			'side',
			'default'
		);
	}


	/**
	 * Save the meta when the post is saved
	 *
	 * @since 1.7.0
	 * @param int $post_id The ID of the post being saved
	 */
	public function save( $post_id ) {

		// check if our nonce is set
		if ( ! isset( $_POST['wc_tab_manager_metabox_nonce'] ) ) {
			return;
		}

		$nonce = $_POST['wc_tab_manager_metabox_nonce'];

		// verify that the nonce is valid
		if ( ! wp_verify_nonce( $nonce, 'wc_tab_manager_metabox' ) ) {
			return;
		}

		// if this is an autosave, we don't want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// check the user's permissions
		if ( isset( $_POST['post_type'] ) && 'wc_product_tab' === $_POST['post_type'] && ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( ! empty( $_POST['_wc_tab_categories'] ) ) {

			// sanitize the user input & update the meta field
			$product_cats = sanitize_meta( '_wc_tab_categories', $_POST['_wc_tab_categories'], 'post' );

			update_post_meta( $post_id, '_wc_tab_categories', $product_cats );

		} else {

			delete_post_meta( $post_id, '_wc_tab_categories' );
		}
	}


	/**
	 * Render Meta Box content
	 *
	 * @since 1.7.0
	 * @param \WP_Post $post The post object
	 */
	public function render_meta_box_content( $post ) {

		// show a notice on a product tab
		if ( $post->post_parent ) {
			?><p><?php esc_html_e( 'Product-level tabs will always be shown on their assigned product.', 'woocommerce-tab-manager' ); ?></p><?php
			return;
		}

		// add an nonce field so we can check for it later.
		wp_nonce_field( 'wc_tab_manager_metabox', 'wc_tab_manager_metabox_nonce' );

		// display the form, using the current values
		?>
		<p class="form-field"><label for="product_categories"><?php _e( 'Product Categories', 'woocommerce-tab-manager' ); ?></label>

			<?php echo wc_help_tip( __( 'Select categories to restrict the display of this tab to certain products.', 'woocommerce-tab-manager' ) ); ?>

			<select
				id="_wc_tab_categories"
				name="_wc_tab_categories[]"
				style="width: 75%;"
				class="wc-enhanced-select"
				multiple="multiple"
				data-placeholder="<?php esc_attr_e( 'Any category', 'woocommerce-tab-manager' ); ?>">
				<?php $category_ids = (array) get_post_meta( $post->ID, '_wc_tab_categories', true ); ?>
				<?php $categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' ); ?>
				<?php if ( is_array( $categories ) ) : ?>
					<?php foreach ( $categories as $cat ) : ?>
						<option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( in_array( $cat->term_id, $category_ids, false ), true, true ); ?>><?php echo esc_html( $cat->name ); ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</p>
		<?php
	}


}
