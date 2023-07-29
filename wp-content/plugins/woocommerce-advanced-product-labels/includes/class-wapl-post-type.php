<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WAPL_Post_Type
 *
 * Initialize the WAPL post type
 *
 * @class       WAPL_Post_Type
 * @author     	Jeroen Sormani
 * @package		WooCommerce Advanced Product Labels
 * @version		1.0.0
 */
class WAPL_Post_Type {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Register post type
		add_action( 'init', array( $this, 'register_post_type' ) );

		// Edit user notices
		add_filter( 'post_updated_messages', array( $this, 'custom_post_type_messages' ) );

		// Add meta box
		add_action( 'add_meta_boxes', array( $this, 'post_type_meta_box' ) );
		// Save meta box
		add_action( 'save_post', array( $this, 'save_meta_boxes' ) );

		// Redirect after delete
		add_action( 'load-edit.php', array( $this, 'redirect_after_trash' ) );
	}


	/**
	 * Register post type.
	 *
	 * Register the WCAM post type.
	 *
	 * @since 1.0.0
	 */
	public function register_post_type() {

		$labels = array(
			'name'               => __( 'Global Labels', 'woocommerce-advanced-product-labels' ),
			'singular_name'      => __( 'Global Label', 'woocommerce-advanced-product-labels' ),
			'add_new'            => __( 'Add New', 'woocommerce-advanced-product-labels' ),
			'add_new_item'       => __( 'Add New Global Label', 'woocommerce-advanced-product-labels' ),
			'edit_item'          => __( 'Edit Global Label', 'woocommerce-advanced-product-labels' ),
			'new_item'           => __( 'New Global Label', 'woocommerce-advanced-product-labels' ),
			'view_item'          => __( 'View Global Label', 'woocommerce-advanced-product-labels' ),
			'search_items'       => __( 'Search Global Labels', 'woocommerce-advanced-product-labels' ),
			'not_found'          => __( 'No Global Labels', 'woocommerce-advanced-product-labels' ),
			'not_found_in_trash' => __( 'No Global Labels found in Trash', 'woocommerce-advanced-product-labels' ),
		);

		register_post_type( 'wapl', array(
			'label'           => 'wapl',
			'show_ui'         => true,
			'show_in_menu'    => false,
			'capability_type' => 'post',
			'map_meta_cap'    => true,
			'rewrite'         => array( 'slug' => 'wapl', 'with_front' => true ),
			'_builtin'        => false,
			'query_var'       => true,
			'supports'        => array( 'title' ),
			'labels'          => $labels,
		) );
	}


	/**
	 * Messages.
	 *
	 * Modify the notice messages text for the 'wapl' post type.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $messages Existing list of messages.
	 * @return array           Modified list of messages.
	 */
	function custom_post_type_messages( $messages ) {

		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		$messages['wapl'] = array(
			0  => '',
			1  => __( 'Global product label updated.', 'woocommerce-advanced-product-labels' ),
			2  => __( 'Custom field updated.', 'woocommerce-advanced-product-labels' ),
			3  => __( 'Custom field deleted.', 'woocommerce-advanced-product-labels' ),
			4  => __( 'Global product label updated.', 'woocommerce-advanced-product-labels' ),
			5  => isset( $_GET['revision'] ) ?
				sprintf( __( 'Product label restored to revision from %s', 'woocommerce-advanced-product-labels' ), wp_post_revision_title( (int) $_GET['revision'], false ) )
				: false,
			6  => __( 'Global product label published.', 'woocommerce-advanced-product-labels' ),
			7  => __( 'Global product label saved.', 'woocommerce-advanced-product-labels' ),
			8  => __( 'Global product label submitted.', 'woocommerce-advanced-product-labels' ),
			9  => sprintf(
				__( 'Global product label scheduled for: <strong>%1$s</strong>.', 'woocommerce-advanced-product-labels' ),
				date_i18n( __( 'M j, Y @ G:i', 'woocommerce-advanced-product-labels' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Global product label draft updated.', 'woocommerce-advanced-product-labels' )
		);

		$permalink = admin_url( 'admin.php?page=wc-settings&tab=labels' );

		$view_link            = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'Return to overview.', 'woocommerce-advanced-product-labels' ) );
		$messages['wapl'][1] .= $view_link;
		$messages['wapl'][6] .= $view_link;
		$messages['wapl'][9] .= $view_link;

		$preview_link          = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'Return to overview.', 'woocommerce-advanced-product-labels' ) );
		$messages['wapl'][8]  .= $preview_link;
		$messages['wapl'][10] .= $preview_link;

		return $messages;
	}


	/**
	 * Add meta boxes.
	 *
	 * Add two meta boxes to the 'wapl' posts.
	 *
	 * @since 1.0.0
	 */
	public function post_type_meta_box() {
		add_meta_box( 'wapl_conditions', __( 'Global Label conditions', 'woocommerce-advanced-product-labels' ), array( $this, 'render_conditions' ), 'wapl', 'normal' );
		add_meta_box( 'wapl_settings', __( 'Global Label settings', 'woocommerce-advanced-product-labels' ), array( $this, 'render_settings' ), 'wapl', 'normal' );
	}


	/**
	 * Render meta box.
	 *
	 * Get conditions meta box contents.
	 *
	 * @since 1.0.0
	 */
	public function render_conditions() {
		require_once plugin_dir_path( __FILE__ ) . 'admin/views/html-meta-box-conditions.php';
	}


	/**
	 * Render meta box.
	 *
	 * Get settings meta box contents.
	 *
	 * @since 1.0.0
	 */
	public function render_settings() {
		wp_enqueue_media();

		$preview_product_id = get_option( 'wapl_preview_product_id', 0 );

		if ( $preview_product_id ) {
			$preview_product = wc_get_product( $preview_product_id );
			$GLOBALS['product'] = $preview_product;
			ob_start();
				woocommerce_template_loop_product_thumbnail();
			$image = ob_get_clean();
		} else {
			$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
			$image      = wc_placeholder_img( $image_size, array() );
		}

		require_once plugin_dir_path( __FILE__ ) . 'admin/views/html-meta-box-settings.php';
	}


	/**
	 * Save meta.
	 *
	 * Validate and save post meta. This value contains all
	 * the normal fee settings (no conditions).
	 *
	 * @since 1.0.0
	 *
	 * @param  int      $post_id ID of the post being saved.
	 * @return int|void          Post ID when failing, not returning otherwise.
	 */
	public function save_meta_boxes( $post_id ) {

		// verify nonce
		if ( ! isset( $_POST['wapl_global_label_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['wapl_global_label_meta_box_nonce'], 'wapl_global_label_meta_box' ) ) {
			return $post_id;
		}

		// if autosave, don't save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// check capability
		if ( ! current_user_can( apply_filters( 'wapl_global_label_capability', 'manage_woocommerce' ) ) ) {
			return $post_id;
		}

		// check if post_type is wapl
		if ( $_POST['post_type'] != 'wapl' ) {
			return $post_id;
		}

		$postdata  = $_POST['_wapl_label'];
		$label     = array(
			'type'                          => in_array( $postdata['type'], array_keys( wapl_get_label_types() ) ) ? $postdata['type'] : '',
			'text'                          => wp_kses_post( $postdata['text'] ),
			'style'                         => in_array( $postdata['style'], array_keys( wapl_get_label_styles() ) ) ? $postdata['style'] : '',
			'align'                         => sanitize_key( $postdata['align'] ),
			'position'                      => wc_clean( $postdata['position'] ),
			'label_custom_background_color' => sanitize_text_field( $postdata['label_custom_background_color'] ),
			'label_custom_text_color'       => sanitize_text_field( $postdata['label_custom_text_color'] ),
			'custom_image'                  => absint( $postdata['custom_image'] ),
			'conditions'                    => wpc_sanitize_conditions( $_POST['conditions'] ),
		);

		if ( $label['type'] !== 'custom' ) {
			$label['custom_image'] = '';
		}

		update_post_meta( $post_id, '_wapl_global_label', $label );

		// Thumbnail ID
		if ( isset( $_POST['product_id'] ) ) {
			update_option( 'wapl_preview_product_id', absint( $_POST['product_id'] ), false );
		}

		// Clear cache
		wp_cache_delete( 'global_labels', 'woocommerce-advanced-product-labels' );
	}


	/**
	 * Redirect trash.
	 *
	 * Redirect user after trashing a post.
	 *
	 * @since 1.0.0
	 */
	public function redirect_after_trash() {
		$screen = get_current_screen();

		if ( 'edit-wapl' == $screen->id ) {
			if ( isset( $_GET['trashed'] ) &&  intval( $_GET['trashed'] ) > 0 ) {
				wp_redirect( admin_url( '/admin.php?page=wc-settings&tab=labels' ) );
				exit();
			}
		}
	}


}
require_once plugin_dir_path( __FILE__ ) . 'admin/class-wapl-condition.php';
