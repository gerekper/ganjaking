<?php
/**
 * WooCommerce 360° Image Meta Boxes / Data
 *
 * @package WC_360_Image
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_360_Image_Meta' ) ) {
	/**
	 * WC360 Meta Class.
	 */
	class WC_360_Image_Meta {

		protected static $instance;

		private function __construct() {

			add_action( 'add_meta_boxes', array( $this, 'product_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 2 );
			add_action( 'admin_print_styles', array( $this, 'admin_styles' ) );
		}

		/**
		 * Start the Class when called.
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( ! self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Enqueues admin styles.
		 *
		 * @since 1.0.0
		 *
		 * @global string $typenow
		 */
		public function admin_styles() {
			global $typenow;

			if ( 'product' === $typenow ) {
				wp_enqueue_style( 'wc360_admin_meta_styles', WC_360_IMAGE_URL . 'assets/css/wc360-admin.css', array(), WC_360_IMAGE_VERSION );
			}
		}


		/**
		 * Register the metaboxes to be used for the team post type
		 *
		 * @since 0.1.0
		 */
		public function product_meta_boxes() {

			add_meta_box(
				'wc360_fields',
				'WC 360 Image Settings',
				array( $this, 'render_meta_boxes' ),
				'product',
				'side',
				'core'
			);
		}

		/**
		 * The HTML for the fields
		 *
		 * @since 0.1.0
		 */
		public function render_meta_boxes() {

			global $post;

			$meta = get_post_meta( $post->ID );

			wp_nonce_field( basename( __FILE__ ), 'wc360_fields' ); ?>

		<table id="wc360" class="form-table">

			<tr>
				<td class="wc360_meta_box_td">
					<label for="wc360_enable">
						<input type="checkbox" name="wc360_enable" id="wc360_enable" value="yes"
						<?php
						if ( isset( $meta['wc360_enable'] ) ) {
							checked( $meta['wc360_enable'][0], 'yes' ); }
						?>
						/>
							<?php esc_html_e( 'Replace Image with 360 Image', 'woocommerce-360-image' ); ?>
					</label>
				</td>
			</tr>

			<tr>
				<td>
						<?php echo '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=products&section=wc360' ) ) . '">' . esc_html__( 'WooCommerce 360 Image Settings', 'woocommerce-360-image' ) . '</a>'; ?>
				</td>
			</tr>

		</table>

			<?php
		}

		/**
		 * Save metaboxes
		 *
		 * @since 0.1.0
		 */
		public function save_meta_boxes( $post_id ) {

			global $post;

			// Verify nonce
			if ( ! isset( $_POST['wc360_fields'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wc360_fields'] ) ), basename( __FILE__ ) ) ) {
				return $post_id;
			}

			// Check Autosave
			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
				return $post_id;
			}

			// Don't save if only a revision
			if ( isset( $post->post_type ) && 'revision' === $post->post_type ) {
				return $post_id;
			}

			// Check permissions
			if ( ! current_user_can( 'edit_post', $post->ID ) ) {
				return $post_id;
			}

			// Checks for enable checkbox and saves
			if ( isset( $_POST['wc360_enable'] ) ) {
				update_post_meta( $post_id, 'wc360_enable', 'yes' );
			} else {
				update_post_meta( $post_id, 'wc360_enable', '' );
			}
		}
	}

}
