<?php
/**
 * WooCommerce Product Retailers
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Retailers to newer
 * versions in the future. If you wish to customize WooCommerce Product Retailers for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-retailers/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Retailers Admin Class - handles admin UX.
 *
 * @since 1.0.0
 */
class WC_Product_Retailers_Admin {


	/**
	 * Sets up the admin class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// load styles/scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// load WC scripts on the edit retailers page
		add_filter( 'woocommerce_screen_ids', array( $this, 'load_wc_admin_scripts' ) );

		add_filter( 'woocommerce_product_settings', array( $this, 'add_global_settings' ) );

		// add product tab
		if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.6.0' ) ) {
			add_filter( 'woocommerce_product_data_tabs',        array( $this, 'add_product_tab' ), 20 );
		} else {
			add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_product_tab' ), 11 );
		}

		// add product tab data
		add_action( 'woocommerce_product_data_panels', array( $this, 'add_product_tab_options' ), 11 );

		// save product tab data
		add_action( 'woocommerce_process_product_meta_simple',                array( $this, 'save_product_tab_options' ) );
		add_action( 'woocommerce_process_product_meta_variable',              array( $this, 'save_product_tab_options' ) );
		add_action( 'woocommerce_process_product_meta_booking',               array( $this, 'save_product_tab_options' ) );
		add_action( 'woocommerce_process_product_meta_subscription',          array( $this, 'save_product_tab_options' ) );
		add_action( 'woocommerce_process_product_meta_variable-subscription', array( $this, 'save_product_tab_options' ) );

		// add AJAX retailer search
		add_action( 'wp_ajax_wc_product_retailers_search_retailers', array( $this, 'ajax_search_retailers' ) );
	}


	/**
	 * Load admin js/css
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix
	 */
	public function load_styles_scripts( $hook_suffix ) {
		global $post_type;

		if ( 'wc_product_retailer' === $post_type && 'edit.php' === $hook_suffix ) {
			ob_start();
			?>
			// get rid of the date filter and also the filter button itself, unless there are other filters added
			$( 'select[name="m"]' ).remove();
			if ( ! $('#post-query-submit').siblings('select').size() ) $('#post-query-submit').remove();
			<?php
			$js = ob_get_clean();
			wc_enqueue_js( $js );
		}

		// load admin css/js only on edit product/new product pages
		if ( 'product' === $post_type && ( 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix ) ) {

			// admin CSS
			wp_enqueue_style( 'wc-product-retailers-admin', wc_product_retailers()->get_plugin_url() . '/assets/css/admin/wc-product-retailers-admin.min.css', array( 'woocommerce_admin_styles' ), WC_Product_Retailers::VERSION );

			// admin JS
			wp_enqueue_script( 'wc-product-retailers-admin', wc_product_retailers()->get_plugin_url() . '/assets/js/admin/wc-product-retailers-admin.min.js', WC_Product_Retailers::VERSION );

			wp_enqueue_script( 'jquery-ui-sortable' );

			// add script data
			$wc_product_retailers_admin_params = [
				'search_retailers_nonce' => wp_create_nonce( 'search_retailers' ),
			];

			wp_localize_script( 'wc-product-retailers-admin', 'wc_product_retailers_admin_params', $wc_product_retailers_admin_params );
		}

		// load WC CSS on add/edit retailer page
		if ( 'wc_product_retailer' === $post_type ) {
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css' );
		}
	}


	/**
	 * Adds settings/export screen ID to the list of pages for WC to load its scripts on.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $screen_ids
	 * @return array
	 */
	public function load_wc_admin_scripts( $screen_ids ) {

		$screen_ids[] = 'wc_product_retailer';

		return $screen_ids;
	}


	/**
	 * Returns the global settings array for the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return array the global settings
	 */
	public static function get_global_settings() {

		return apply_filters( 'wc_product_retailers_settings', array(
			// section start
			array(
				'name' => __( 'Product Retailers', 'woocommerce-product-retailers' ),
				'type' => 'title',
				'desc' => '',
				'id' => 'wc_product_retailer_options',
			),

			// product button text
			array(
				'title'    => __( 'Product Button Text', 'woocommerce-product-retailers' ),
				'desc_tip' => __( 'This text will be shown on the dropdown linking to the external product, unless overridden at the product level.', 'woocommerce-product-retailers' ),
				'id'       => 'wc_product_retailers_product_button_text',
				'css'      => 'width:200px;',
				'default'  => __( 'Purchase from Retailer', 'woocommerce-product-retailers' ),
				'type'     => 'text',
			),

			// catalog button text
			array(
				'title'    => __( 'Catalog Button Text', 'woocommerce-product-retailers' ),
				'desc_tip' => __( 'This text will be shown on the catalog page "Add to Cart" button for simple products that are sold through retailers only, unless overridden at the product level.', 'woocommerce-product-retailers' ),
				'id'       => 'wc_product_retailers_catalog_button_text',
				'css'      => 'width:200px;',
				'default'  => __( 'View Retailers', 'woocommerce-product-retailers' ),
				'type'     => 'text',
			),

			// open in new tab
			array(
				'title'    => __( 'Open retailer links in a new tab', 'woocommerce-product-retailers' ),
				'desc'     => __( 'Enable this option to open links to other retailers in a new tab instead of the current one.', 'woocommerce-product-retailers' ),
				'id'       => 'wc_product_retailers_enable_new_tab',
				'default'  => '',
				'type'     => 'checkbox',
			),

			// section end
			array( 'type' => 'sectionend', 'id' => 'wc_product_retailer_options' ),
		) );
	}


	/**
	 * Injects global settings into the Settings > Catalog/Products page(s), immediately after the 'Product Data' section.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings associative array of WooCommerce settings
	 * @return array associative array of WooCommerce settings
	 */
	public function add_global_settings( $settings ) {

		$setting_id = 'catalog_options';

		$updated_settings = array();

		foreach ( $settings as $setting ) {

			$updated_settings[] = $setting;

			if ( isset( $setting['id'] ) && $setting_id === $setting['id']
				 && isset( $setting['type'] ) && 'sectionend' === $setting['type'] ) {
				$updated_settings = array_merge( $updated_settings, self::get_global_settings() );
			}
		}

		return $updated_settings;
	}


	/**
	 * Adds a 'Retailers' tab to product data writepanel.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $tabs tab data
	 * @return void|array
	 */
	public function add_product_tab( $tabs = [] ) {

		if ( 'woocommerce_product_write_panel_tabs' === current_action() ) :

			?>
			<li class="wc-product-retailers-tab wc-product-retailers-options hide_if_external hide_if_grouped">
				<a href="#wc-product-retailers-data"><span><?php esc_html_e( 'Retailers', 'woocommerce-product-retailers' ); ?></span></a>
			</li>
			<?php

		elseif ( 'woocommerce_product_data_tabs' === current_filter() ) :

			$tabs['wc-product-retailers-tab'] = [
				'label'    => __( 'Retailers', 'woocommerce-product-retailers' ),
				'target'   => 'wc-product-retailers-data',
				'priority' => 900,
			];

			return $tabs;

		endif;
	}


	/**
	 * Adds product retailers options to product writepanel.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_product_tab_options() {
		global $post_id;

		?>
		<div id="wc-product-retailers-data" class="panel woocommerce_options_panel">
			<div class="options_group">
				<?php

				do_action( 'wc_product_retailers_product_options_start' );

				// retailer availability
				woocommerce_wp_select(
					array(
						'id'          => '_wc_product_retailers_retailer_availability',
						'label'       => __( 'Retailer Availability', 'woocommerce-product-retailers' ),
						'description' => __( 'Choose when retailers are shown for the product.', 'woocommerce-product-retailers' ),
						'class'       => 'wc-enhanced-select',
						'options'     => array(
							'with_store'    => __( 'Always; Use both retailers and the store add-to-cart button.', 'woocommerce-product-retailers' ),
							'replace_store' => __( 'Always; Use retailers instead of the store add-to-cart button.', 'woocommerce-product-retailers' ),
							'out_of_stock'  => __( 'Only when the product is out of stock.', 'woocommerce-product-retailers' ),
						),
						'value'       => get_post_meta( $post_id, '_wc_product_retailers_retailer_availability', true ),
					)
				);

				// show buttons
				woocommerce_wp_checkbox(
					array(
						'id'          => '_wc_product_retailers_use_buttons',
						'label'       => __( 'Use Buttons', 'woocommerce-product-retailers' ),
						'description' => __( 'Enable this to use buttons rather than a dropdown for multiple retailers.', 'woocommerce-product-retailers' ),
					)
				);

				// product button text
				woocommerce_wp_text_input(
					array(
						'id'          => '_wc_product_retailers_product_button_text',
						'label'       => __( 'Product Button Text', 'woocommerce-product-retailers' ),
						'description' => __( 'This text will be shown on the dropdown linking to the external product, or before the buttons if "Use Buttons" is enabled.', 'woocommerce-product-retailers' ),
						'desc_tip'    => true,
						'placeholder' => wc_product_retailers()->get_product_button_text(),
					)
				);

				// product button text
				woocommerce_wp_text_input(
					array(
						'id'          => '_wc_product_retailers_catalog_button_text',
						'label'       => __( 'Catalog Button Text', 'woocommerce-product-retailers' ),
						'description' => __( 'This text will be shown on the catalog page "Add to Cart" button for simple products that are sold through retailers only.', 'woocommerce-product-retailers' ),
						'desc_tip'    => true,
						'placeholder' => wc_product_retailers()->get_catalog_button_text(),
					)
				);

				// show retailers element on product page
				woocommerce_wp_checkbox(
					array(
						'id'          => '_wc_product_retailers_hide',
						'label'       => __( 'Hide Product Retailers', 'woocommerce-product-retailers' ),
						'description' => __( 'Enable this to hide the default product retailers buttons/dropdown on the product page.  Useful if you want to display them elsewhere using the shortcode or widget.', 'woocommerce-product-retailers' ),
						'default'     => 'no',
					)
				);

				do_action( 'wc_product_retailers_product_options_end' );
				?>
			</div>
			<div class="options_group">
				<?php $this->add_retailers_table(); ?>
			</div>
		</div>
		<?php

		// hide "Catalog Button Text" if "Retailers Only Purchase" is enabled
		wc_enqueue_js( '

			var $retailer_availability_input  = $( "#_wc_product_retailers_retailer_availability" );
			    $catalog_button_field         = $( "._wc_product_retailers_catalog_button_text_field" );

			$retailer_availability_input.change( function() {
				if ( "with_store" == $( this ).find( "option:selected" ).val() ) {
					$catalog_button_field.hide();
				} else {
					$catalog_button_field.show();
				}
			} ).change();

		' );
	}


	/**
	 * Add product retailers add/remove table.
	 *
	 * @since 1.0.0
	 */
	private function add_retailers_table() {
		global $post;
		?>
		<table class="widefat wc-product-retailers">
			<thead>
				<tr>
					<td class="check-column"><input type="checkbox"></td>
					<th class="wc-product-retailer-name"><?php esc_html_e( 'Retailer', 'woocommerce-product-retailers' ); ?></th>
					<th class="wc-product-retailer-price"><?php esc_html_e( 'Product Price', 'woocommerce-product-retailers' ); ?></th>
					<th class="wc-product-retailer-product-url"><?php esc_html_e( 'Product URL', 'woocommerce-product-retailers' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$retailers = get_post_meta( $post->ID, '_wc_product_retailers', true );
				$index     = 0;

				if ( ! empty( $retailers) ) :

					foreach ( $retailers as $retailer ) :

						try {
							// build the retailer object and override the URL as needed
							$_retailer = new WC_Product_Retailers_Retailer( $retailer['id'] );

							// product URL for retailer
							if ( ! empty( $retailer['product_url'] ) ) {
								$_retailer->set_url( $retailer['product_url'] );
							}

							// product price for retailer
							if ( isset( $retailer['product_price'] ) ) {
								$_retailer->set_price( $retailer['product_price'] );
							}

							// if the retailer is not available (trashed) exclude it
							if ( ! $_retailer->is_available( true ) ) {
								continue;
							}

							?>
							<tr class="wc-product-retailer">
								<td class="check-column">
									<input type="checkbox" name="select" />
									<input type="hidden" name="_wc_product_retailer_id[<?php echo $index; ?>]" value="<?php echo esc_attr( $_retailer->get_id() ); ?>" />
								</td>
								<td class="wc-product-retailer_name"><?php echo esc_html( $_retailer->get_name() ); ?></td>
								<td class="wc-product-retailer-product-price">
									<input type="text" data-post-id="<?php echo esc_attr( $_retailer->get_id() ); ?>" id="wc-product-retailer-price-<?php echo esc_attr( $_retailer->get_id() ); ?>" name="_wc_product_retailer_product_price[<?php echo $index; ?>]" value="<?php echo esc_attr( $_retailer->get_price() ); ?>" />
								</td>
								<td class="wc-product-retailer-product-url">
									<input type="text" data-post-id="<?php echo esc_attr( $_retailer->get_id() ); ?>" id="wc-product-retailer-product-url-<?php echo esc_attr( $_retailer->get_id() ); ?>" name="_wc_product_retailer_product_url[<?php echo $index; ?>]" value="<?php echo esc_attr( $_retailer->get_url() ); ?>" />
								</td>
							</tr>
							<?php
							$index++;
						} catch ( \Exception $e ) { /* retailer does not exist */ }
					endforeach;
				endif;
				?>
			</tbody>
			<tfoot>
			<tr>
				<td>
					<?php echo wc_help_tip( __( 'Search for a retailer to add to this product. You may add multiple retailers by searching for them first.', 'woocommerce-product-retailers' ) ); ?>
				</td>
				<td colspan="3">
					<?php $placeholder = sprintf( esc_attr__( 'Search for a retailer to add%s', 'woocommerce-product-retailers' ), '&hellip;' ); ?>
					<select
						class="wc-retailers-search"
						multiple="multiple"
						style="width: 50%;"
						name="wc_product_retailers_retailer_search[]"
						data-placeholder="<?php echo $placeholder; ?>">
						<option></option>
					</select>
					<button
						type="button"
						class="button button-primary wc-product-retailers-add-retailer"
					><?php esc_html_e( 'Add Retailer', 'woocommerce-product-retailers' ); ?></button>
					<button
						type="button"
						class="button button-secondary wc-product-retailers-delete-retailer"
					><?php esc_html_e( 'Delete Selected', 'woocommerce-product-retailers' ); ?></button>
				</td>
			</tr>
			</tfoot>
		</table>
		<?php
	}


	/**
	 * Saves product retailers options at the product level.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id the ID of the product being saved
	 */
	public function save_product_tab_options( $post_id ) {

		// retailer availability
		if ( isset( $_POST['_wc_product_retailers_retailer_availability'] ) ) {
			update_post_meta( $post_id, '_wc_product_retailers_retailer_availability', $_POST['_wc_product_retailers_retailer_availability'] );
		}

		// use buttons rather than a dropdown?
		update_post_meta(
			$post_id,
			'_wc_product_retailers_use_buttons',
			isset( $_POST['_wc_product_retailers_use_buttons'] ) && 'yes' === $_POST['_wc_product_retailers_use_buttons'] ? 'yes' : 'no'
		);

		// product button text
		if ( isset( $_POST['_wc_product_retailers_product_button_text'] ) ) {
			update_post_meta( $post_id, '_wc_product_retailers_product_button_text', $_POST['_wc_product_retailers_product_button_text'] );
		}

		// catalog button text
		if ( isset( $_POST['_wc_product_retailers_catalog_button_text'] ) ) {
			update_post_meta( $post_id, '_wc_product_retailers_catalog_button_text', $_POST['_wc_product_retailers_catalog_button_text'] );
		}

		// whether to hide the product retailers
		update_post_meta(
			$post_id,
			'_wc_product_retailers_hide',
			isset( $_POST['_wc_product_retailers_hide'] ) && 'yes' === $_POST['_wc_product_retailers_hide'] ? 'yes' : 'no'
		);

		$retailers = array();

		// persist any retailers assigned to this product
		if ( ! empty( $_POST['_wc_product_retailer_product_url'] ) && is_array( $_POST['_wc_product_retailer_product_url'] ) ) {

			foreach ( $_POST['_wc_product_retailer_product_url'] as $index => $retailer_product_url ) {

				$retailer_id = $_POST['_wc_product_retailer_id'][ $index ];

				$retailer_price = $_POST['_wc_product_retailer_product_price'][ $index ];

				// only save the product URL if it's unique to the product
				$retailers[] = array(
					'id'            => $retailer_id,
					'product_price' => $retailer_price,
					'product_url'   => $retailer_product_url !== get_post_meta( $retailer_id, '_product_retailer_default_url', true ) ? esc_url_raw( $retailer_product_url ) : '',
				);
			}
		}

		update_post_meta( $post_id, '_wc_product_retailers', $retailers );
	}


	/**
	 * Processes the AJAX retailer search on the edit product page.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function ajax_search_retailers() {

		// security check
		check_ajax_referer( 'search_retailers', 'security' );

		// get search term
		$term = (string) urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );

		if ( empty( $term ) ) {
			die();
		}

		$args = array(
			'post_type'    => 'wc_product_retailer',
			'post_status'  => 'publish',
			'nopaging'     => true,
		);

		if ( is_numeric( $term ) ) {

			//search by retailer ID
			$args['p'] = $term;

		} else {

			// search by retailer name
			$args['s'] = $term;

		}

		$posts = get_posts( $args );

		$retailers = array();

		// build the set of found retailers
		if ( ! empty( $posts ) ) {

			foreach ( $posts as $post ) {

				$retailers[] = array(
					'id'          => $post->ID,
					'name'        => $post->post_title,
					'product_url' => get_post_meta( $post->ID, '_product_retailer_default_url', true )
				);
			}
		}

		wp_send_json( $retailers );
	}


}
