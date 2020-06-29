<?php
/**
 * WooCommerce Product Documents
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Documents to newer
 * versions in the future. If you wish to customize WooCommerce Product Documents for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-documents/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Main admin handler.
 *
 * Loads / saves admin settings.
 *
 * @since 1.0
 */
class WC_Product_Documents_Admin {


	/**
	 * Sets up the admin class.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// add global settings
		add_filter( 'woocommerce_product_settings', array( $this, 'add_global_settings' ) );

		// load styles/scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// add product tab
		if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.6.0' ) ) {
			add_filter( 'woocommerce_product_data_tabs',        array( $this, 'add_product_tab' ), 20 );
		} else {
			add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_product_tab' ), 11 );
		}

		// add product tab data
		add_action( 'woocommerce_product_data_panels',  array( $this, 'add_product_tab_options' ), 11 );

		// save product tab data
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_tab_options' ) );
	}


	/**
	 * Injects global settings into the Settings > Catalog page, immediately after the 'Product Data' section.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings associative array of WooCommerce settings
	 * @return array associative array of WooCommerce settings
	 */
	public function add_global_settings( $settings ) {

		$updated_settings = array();

		foreach ( $settings as $setting ) {

			$updated_settings[] = $setting;

			if (    isset( $setting['id'], $setting['type'] )
			     && 'catalog_options' === $setting['id']
			     && 'sectionend'      === $setting['type'] ) {

				$updated_settings = array_merge( $updated_settings, $this->get_global_settings() );
			}
		}

		return $updated_settings;
	}


	/**
	 * Returns the global settings array for the plugin.
	 *
	 * @since 1.0
	 *
	 * @return array the global settings
	 */
	public function get_global_settings() {

		return apply_filters( 'wc_product_documents_settings', array(

			// section start
			array(
				'name' => __( 'Product Documents', 'woocommerce-product-documents' ),
				'type' => 'title',
				'desc' => '',
				'id' => 'wc_product_documents_catalog_options',
			),

			// documents title text
			array(
				'title'    => __( 'Product Documents Default Title', 'woocommerce-product-documents' ),
				'desc_tip' => __( 'This text will be shown above the product documents section unless overridden at the product level.', 'woocommerce-product-documents' ),
				'id'       => 'wc_product_documents_title',
				'css'      => 'width:200px;',
				'default'  => __( 'Product Documents', 'woocommerce-product-documents' ),
				'type'     => 'text',
			),

			// section end
			array(
				'type' => 'sectionend',
				'id' => 'wc_product_documents_catalog_options'
			),

		) );
	}


	/**
	 * Loads admin scripts and styles.
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * @param string $hook_suffix the current URL filename, ie edit.php, post.php, etc
	 */
	public function load_styles_scripts( $hook_suffix ) {
		global $post_type;

		// load admin css/js only on edit product/new product pages
		if ( 'product' === $post_type && ( 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix ) ) {

			// requires image upload capability
			wp_enqueue_media();

			// admin CSS
			wp_enqueue_style( 'wc-product-documents-admin', wc_product_documents()->get_plugin_url() . '/assets/css/admin/wc-product-documents.min.css', array( 'woocommerce_admin_styles' ), \WC_Product_Documents::VERSION );

			// admin JS
			wp_enqueue_script( 'wc-product-documents-admin', wc_product_documents()->get_plugin_url() . '/assets/js/admin/wc-product-documents.min.js', \WC_Product_Documents::VERSION );

			wp_enqueue_script( 'jquery-ui-sortable' );

			// add script data
			$new_section  = $this->document_section_markup( new \WC_Product_Documents_Section(), '{index}' );
			$new_document = $this->document_markup( new \WC_Product_Documents_Document(), '{index}', '{sub_index}' );

			$wc_product_documents_admin_params = array(
				'new_section'                  => str_replace( array( "\n", "\t" ), '', $new_section ),  // cleanup the markup a bit
				'new_document'                 => str_replace( array( "\n", "\t" ), '', $new_document ),
				'confirm_remove_section_text'  => __( 'Are you sure you want to remove this section?', 'woocommerce-product-documents' ),
				'confirm_remove_document_text' => __( 'Are you sure you want to remove this document?', 'woocommerce-product-documents' ),
				'select_file_text'             => __( 'Select a File', 'woocommerce-product-documents' ),
				'set_file_text'                => __( 'Set File', 'woocommerce-product-documents' ),
			);

			wp_localize_script( 'wc-product-documents-admin', 'wc_product_documents_admin_params', $wc_product_documents_admin_params );
		}
	}


	/**
	 * Adds 'Product Documents' tab to product data writepanel.
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * @param array $tabs tab data
	 * @return void|array
	 */
	public function add_product_tab( $tabs = [] ) {

		if ( 'woocommerce_product_write_panel_tabs' === current_action() ) :

			?>
			<li class="wc-product-documents-tab">
				<a href="#wc-product-documents-data"><span><?php esc_html_e( 'Product Documents', 'woocommerce-product-documents' ); ?></span></a>
			</li>
			<?php

		elseif ( 'woocommerce_product_data_tabs' === current_filter() ) :

			$tabs['wc-product-documents-tab'] = [
				'label'    => __( 'Product Documents', 'woocommerce-product-documents' ),
				'target'   => 'wc-product-documents-data',
				'priority' => 900,
			];

			return $tabs;

		endif;
	}


	/**
	 * Adds product documents options to product writepanel.
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function add_product_tab_options() {
		global $post;

		$documents = new \WC_Product_Documents_Collection( $post->ID );

		?>
		<div id="wc-product-documents-data" class="panel woocommerce_options_panel">
			<?php

			// documents title
			woocommerce_wp_text_input(
				array(
					'id'          => '_wc_product_documents_title',
					'label'       => __( 'Product Documents Title', 'woocommerce-product-documents' ),
					'description' => __( 'This text optional will be shown as the title for the product documents element.', 'woocommerce-product-documents' ),
					'desc_tip'    => true,
					'value'       => wc_product_documents()->get_documents_title_text( $post->ID ),
				)
			);

			// show documents element on product page
			woocommerce_wp_checkbox(
				array(
					'id'          => '_wc_product_documents_display',
					'label'       => __( 'Show Product Documents', 'woocommerce-product-documents' ),
					'description' => __( 'Enable this to automatically display any documents for this product on the product page.  Product documents can also be displayed anywhere via the widget or shortcode.', 'woocommerce-product-documents' ),
					'default'     => 'yes',
				)
			);

			?>
			<div class="wc-metaboxes-wrapper" style="width: 100%;">

				<div class="toolbar expand-close" style="text-align:right;">
					<a href="#" class="close_all"><?php esc_html_e( 'Close all', 'woocommerce-product-documents' ); ?></a>
					<a href="#" class="expand_all"><?php esc_html_e( 'Expand all', 'woocommerce-product-documents' ); ?></a>
				</div>

				<div class="wc-product-documents-sections wc-metaboxes">
					<?php // render all sections, even those without documents ?>
					<?php foreach ( $documents->get_sections( true ) as $index => $section ) : ?>
						<?php echo $this->document_section_markup( $section, $index ); ?>
					<?php endforeach; ?>
				</div>

				<div class="toolbar">
					<button type="button" class="button add-new-product-documents-section button-primary"><?php esc_html_e( 'New Section', 'woocommerce-product-documents' ); ?></button>
				</div>

			</div>

		</div>
		<?php
	}


	/**
	 * Returns the markup for a document section panel.
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * @param \WC_Product_Documents_Section $section the section data
	 * @param int $index the section index
	 * @return string the document section markup
	 */
	public function document_section_markup( $section, $index ) {

		ob_start();

		?>
		<div class="wc-product-documents-section wc-metabox closed">

			<h3>
				<button type="button" class="remove-wc-product-documents-section button"><?php esc_html_e( 'Remove', 'woocommerce-product-documents' ); ?></button>
				<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'woocommerce-product-documents' ); ?>"></div>
				<strong><?php esc_html_e( 'Name', 'woocommerce-product-documents' ); ?> &mdash;</strong> <input type="text" name="product_documents_section_name[<?php echo $index; ?>]" value="<?php echo esc_attr( $section->get_name() ); ?>" id="product_documents_section_name_<?php echo $index; ?>" class="product_documents_section_name" />
				<label for="product_documents_default_section_<?php echo $index; ?>" class="product-documents-default-section"><?php _e( 'Default', 'woocommerce-product-documents' ); ?></label><input type="radio" <?php checked( $section->is_default() ); ?> id="product_documents_default_section_<?php echo $index; ?>" class="product-documents-default-section" name="product_documents_default_section" value="<?php echo $index; ?>" />
				<input type="hidden" name="product_documents_section_position[<?php echo $index; ?>]" class="product-documents-section-position" value="<?php echo $index; ?>" />
				<input type="hidden" class="product-documents-section-index" value="<?php echo $index; ?>" />
			</h3>

			<div class="wc-metabox-content">

				<table class="widefat wc-product-documents">

					<thead>
						<tr>
							<th class="wc-product-document-draggable"></th>
							<th class="wc-product-document-label"><?php esc_html_e( 'Label', 'woocommerce-product-documents' ); ?></th>
							<th class="wc-product-document-file-location"><?php esc_html_e( 'Document Path/URL', 'woocommerce-product-documents' ); ?></th>
							<th class="wc-product-document-actions"><?php esc_html_e( 'Actions', 'woocommerce-product-documents' ); ?></th>
						</tr>
					</thead>

					<tbody>
						<?php // render all documents, even those without a file location configured ?>
						<?php foreach ( $section->get_documents( true ) as $sub_index => $document ) : ?>
							<?php echo $this->document_markup( $document, $index, $sub_index ); ?>
						<?php endforeach; ?>
					</tbody>

					<tfoot>
						<tr>
							<th colspan="4">
								<button type="button" class="button button-secondary wc-product-documents-add-document"><?php esc_html_e( 'Add Document', 'woocommerce-product-documents' ); ?></button>
							</th>
						</tr>
					</tfoot>

				</table>

			</div>

		</div>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the markup for a document row.
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * @param \WC_Product_Documents_Document $document the document data
	 * @param int $index the section index
	 * @param int $sub_index the document index
	 * @return string the document row markup
	 */
	public function document_markup( $document, $index, $sub_index ) {

		ob_start();

		?>
		<tr class="wc-product-document">
			<td class="wc-product-document-draggable">
				<img src="<?php echo wc_product_documents()->get_plugin_url() ?>/assets/images/draggable-handle.png" />
			</td>
			<td class="wc-product-document-label">
				<input type="text" name="wc_product_document_label[<?php echo $index; ?>][<?php echo $sub_index; ?>]" value="<?php echo esc_attr( $document->get_label( true ) ); ?>" id="wc_product_document_label_<?php echo $index; ?>_<?php echo $sub_index; ?>" class="wc-product-document-label" />
				<input type="hidden" name="wc_product_document_position[<?php echo $index; ?>][<?php echo $sub_index; ?>]" class="wc-product-document-position" value="<?php echo $sub_index; ?>" />
				<input type="hidden" class="wc-product-document-sub-index" value="<?php echo $sub_index; ?>" />
			</td>
			<td class="wc-product-document-file-location">
				<input type="text" name="wc_product_document_file_location[<?php echo $index; ?>][<?php echo $sub_index; ?>]" class="wc-product-document-file-location" id="wc_product_document_file_location_<?php echo $index; ?>_<?php echo $sub_index; ?>" value="<?php echo esc_attr( $document->get_file_location() ); ?>" />
			</td>
			<td class="wc-product-document-actions">
				<button type="button" class="button wc-product-documents-set-file"><?php esc_html_e( 'Set File', 'woocommerce-product-documents' ); ?></button>
				<button type="button" class="button wc-product-documents-remove-document"><?php esc_html_e( 'Remove', 'woocommerce-product-documents' ); ?></button>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}


	/**
	 * Saves product documents options at the product level.
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * @param int $product_id the ID of the product being saved
	 */
	public function save_product_tab_options( $product_id ) {

		$product = wc_get_product( $product_id );

		// first save the simple settings:

		// documents title
		if ( isset( $_POST['_wc_product_documents_title'] ) ) {
			$product->update_meta_data( '_wc_product_documents_title', $_POST['_wc_product_documents_title'] );
		}

		// render product documents on product page by default?
		$documents_display = isset( $_POST['_wc_product_documents_display'] ) && 'yes' === $_POST['_wc_product_documents_display'] ? 'yes' : 'no';

		$product->update_meta_data( '_wc_product_documents_display', $documents_display );

		// then take care of any documents:
		$documents_collection = new \WC_Product_Documents_Collection();
		$default_section      = isset( $_POST['product_documents_default_section'] ) ? (int) $_POST['product_documents_default_section'] : 0;
		$section_position     = $_POST['product_documents_section_position'];

		if ( ! empty( $section_position ) && is_array( $section_position ) ) {

			foreach ( $section_position as $index => $position ) {

				// create the section object
				$section_name = $_POST['product_documents_section_name'][ $index ];
				$section      = new \WC_Product_Documents_Section( $section_name, (int) $index === $default_section );
				$documents    = isset( $_POST['wc_product_document_position'][ $index ] ) ? $_POST['wc_product_document_position'][ $index ] : null;

				if ( ! empty( $documents ) && is_array( $documents ) ) {

					foreach ( $documents as $sub_index => $document_position ) {

						$document_label = $_POST['wc_product_document_label'][ $index ][ $sub_index ];
						$document_file  = $_POST['wc_product_document_file_location'][ $index ][ $sub_index ];

						// add the document object at the correct location
						$section->add_document( new \WC_Product_Documents_Document( $document_label, $document_file ), $document_position );
					}
				}

				// add the document section at the correct position
				$documents_collection->add_section( $section, $position );
			}
		}

		// persist the documents to the product
		$documents_collection->save_to_product( $product_id, $product );

		$product->save_meta_data();
	}


}
