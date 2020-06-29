<?php
/**
 * WCOPC_Admin_Editor class.
 *
 * @since 2.0
 */
class WCOPC_Admin_Editor {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_head', array( $this, 'add_shortcode_button' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 99 );
		add_filter( 'tiny_mce_version', array( $this, 'refresh_mce' ), 20 );
		add_filter( 'mce_external_languages', array( $this, 'add_tinymce_lang' ), 20, 1 );

		add_action( 'wp_ajax_one_page_checkout_shortcode_iframe', array( $this, 'one_page_checkout_shortcode_iframe' ), 9 );

	}

	/**
	 * Add a button for the OPC shortcode to the WP editor.
	 */
	public function add_shortcode_button() {

		$screen = get_current_screen();

		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) || $screen->post_type == 'product' ) {
			return;
		}

		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_shortcode_tinymce_plugin' ), 20 );
			add_filter( 'mce_buttons', array( $this, 'register_shortcode_button' ), 20 );
		}
	}

	/**
	 * Enqueue scripts
	 */
	public static function enqueue_scripts() {

		global $pagenow, $typenow;

		/**
		 * Enqueue on post edit screens for all post types
		 */
		if ( $pagenow=='post-new.php' OR $pagenow=='post.php' ) {

			wp_enqueue_script( 'iframe-resizer', PP_One_Page_Checkout::$plugin_url . '/js/admin/iframeResizer.min.js', array(), '2.8.5' );

		}

	}

	/**
	 * woocommerce_add_tinymce_lang function.
	 *
	 * @param array $arr
	 * @return array
	 */
	public function add_tinymce_lang( $arr ) {
		$arr['wcopc_shortcode_button'] = PP_One_Page_Checkout::$plugin_path . '/js/admin/editor_plugin_lang.php';
		return $arr;
	}

	/**
	 * Register the shortcode button.
	 *
	 * @param array $buttons
	 * @return array
	 */
	public function register_shortcode_button( $buttons ) {
		array_push( $buttons, '|', 'wcopc_shortcode_button' );
		return $buttons;
	}

	/**
	 * Add the shortcode button to TinyMCE
	 *
	 * @param array $plugin_array
	 * @return array
	 */
	public function add_shortcode_tinymce_plugin( $plugin_array ) {
		$wp_version = get_bloginfo( 'version' );
		$suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$plugin_array['wcopc_shortcode_button'] = PP_One_Page_Checkout::$plugin_url . '/js/admin/editor_plugin.js';

		return $plugin_array;
	}

	/**
	 * Force TinyMCE to refresh.
	 *
	 * @param int $ver
	 * @return int
	 */
	public function refresh_mce( $ver ) {
		$ver += 3;
		return $ver;
	}


	/**
	 * Display the contents of the iframe used when the One Page Checkout
	 * TinyMCE button is clicked.
	 *
	 * @param int $ver
	 * @return int
	 */
	public static function one_page_checkout_shortcode_iframe() {
		global $wp_scripts;

		set_current_screen( 'wcopc' );

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

		// Determine whether to display category_ids search selector.
		$display_category_ids = ! PP_One_Page_Checkout::is_woocommerce_pre( '3.2' );

		wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
		wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), WC_VERSION );

		wp_enqueue_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
		wp_enqueue_script( 'wcopc_iframeresizer_contentwindow', PP_One_Page_Checkout::$plugin_url . '/js/admin/iframeResizer.contentWindow.min.js', array(), '2.8.5' );

		// Init the WooCommerce scripts as these aren't attached on iframe pages
		$admin_assets = new WC_Admin_Assets();
		$admin_assets->admin_scripts();

		wp_enqueue_script( 'wcopc_tinymce_dialog',
			PP_One_Page_Checkout::$plugin_url . '/js/admin/one-page-checkout-iframe.js',
			array(
				'wc-enhanced-select',
				'jquery-ui-datepicker',
				'jquery-ui-sortable',
				'wcopc_iframeresizer_contentwindow',
				'jquery-tiptip'
			),
			WC_VERSION );

		if ( $display_category_ids ) {
			wp_enqueue_script(
				'wcopc_selectwoo_override',
				PP_One_Page_Checkout::$plugin_url . '/js/admin/opc-selectwoo-init.js',
				array( 'wc-enhanced-select', 'jquery' ),
				WC_VERSION
			);
		}
		iframe_header(); ?>
<style>
/* Make sure select box doesn't extend below iframe */
.select2-results, .select2-results ul {
	max-height: 150px !important;
}

.select2-container .select2-selection--multiple .select2-selection__choice {
	max-width: 100%;
	box-sizing: border-box;
	white-space: normal;
	word-wrap: break-word;
}

#wcopc_settings {
	float: left;
	width: 100%;
}

#wcopc_settings fieldset {
	margin: 1em 0;
}

#wcopc_settings label {
	width: 70px;
	display: inline-block;
}

#wcopc_settings select {
	width: 75%;
}

#wcopc_template_fields label {
	width: 75%;
}

input.#wcopc_product_ids {
	width: 75%;
}

@media screen and (max-width: 782px) {
	/* Fix engorged radio buttons */
	#wcopc_settings input[type="radio"], input[type="checkbox"] {
		width: 16px;
		height: 16px;
	}
	#wcopc_settings input[type="radio"]:checked:before {
		width: 6px;
		height: 6px;
		margin: 4px;
	}
}
/* Enlarge Woo's tiny tooltips */
#tiptip_content {
	min-width: 260px;
}
</style>
<div class="wrap" style="margin: 1em;">
<form id="wcopc_settings">
	<?php do_action( 'wcopc_shortcode_iframe_before' ); ?>
	<fieldset id="wcopc_product_ids_fields">
		<label for="wcopc_product_ids"><strong><?php _e( 'Products:', 'wcopc' ); ?></strong></label>
		<?php if ( PP_One_Page_Checkout::is_woocommerce_pre( '3.0' ) ) { ?>
			<input type="hidden" id="wcopc_product_ids" name="wcopc_product_ids[]" data-multiple="true" class="wc-product-search" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'wcopc' ); ?>"/>
		<?php } else { ?>
			<select id="wcopc_product_ids" name="wcopc_product_ids[]" class="wc-product-search" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'wcopc' ); ?>"></select>
		<?php } ?>
	</fieldset>
	<?php if ( $display_category_ids ) :?>
		<fieldset id="wcopc_category_ids_fields">
			<label for="wcopc_category_ids">
				<strong><?php _e( 'Categories:', 'wcopc' ); ?></strong>
			</label>
			<select id="wcopc_category_ids"
					name="wcopc_category_ids[]"
					class="wc-category-search wcopc-category-search"
					multiple="multiple"
					data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'wcopc' ); ?>"></select>
		</fieldset>
	<?php endif; ?>
	<fieldset id="wcopc_template_fields">
		<div style="font-weight: bold;"><?php _e( 'Template:', 'wcopc' ); ?></div>
		<?php $first = true; ?>
		<?php foreach( PP_One_Page_Checkout::$templates as $id => $template_details ) : ?>
		<label for="<?php echo esc_attr( $id ); ?>">
			<input id="<?php echo esc_attr( $id ); ?>" name="wcopc_template" type="radio" value="<?php echo $id; ?>" style="width: 16px; height: 16px;" <?php checked( $first ); $first = false; ?>>
			<?php echo esc_html( $template_details['label'] ); ?>
			<?php if ( ! empty( $template_details['description'] ) ) : ?>
			<img data-tip="<?php echo wc_sanitize_tooltip( $template_details['description'] ); ?>" class="help_tip" src="<?php echo WC()->plugin_url() . '/assets/images/help.png'; ?>" height="16" width="16">
			<?php endif; ?>
		</label>
		<?php endforeach; ?>
	</fieldset>
	<?php do_action( 'wcopc_shortcode_iframe_after' ); ?>
	<fieldset>
		<input id="wcopc_submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Create Shortcode', 'wcopc' ); ?>" />
		<input id="wcopc_cancel" type="button" class="button" value="<?php esc_attr_e( 'Cancel', 'wcopc' ); ?>" />
	</fieldset>
</form>
</div>
<?php
		iframe_footer();
		exit();
	}
}

new WCOPC_Admin_Editor();
