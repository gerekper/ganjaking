<?php
/**
 * class-woocommerce-product-search-admin-taxonomy.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles filter images for product attributes.
 */
class WooCommerce_Product_Search_Admin_Taxonomy {

	/**
	 * Adds our actions and filters.
	 */
	public static function init() {

		add_action( 'product_cat_add_form_fields', array( __CLASS__, 'add_form_fields' ) );
		add_action( 'product_cat_edit_form_fields', array( __CLASS__, 'edit_form_fields' ), 10, 2 );
		add_filter( 'manage_edit-product_cat_columns', array( __CLASS__, 'manage_edit_taxonomy_columns' ) );
		add_filter( 'manage_product_cat_custom_column', array( __CLASS__, 'manage_taxonomy_custom_column' ), 10, 3 );

		add_action( 'product_tag_add_form_fields', array( __CLASS__, 'add_form_fields' ) );
		add_action( 'product_tag_edit_form_fields', array( __CLASS__, 'edit_form_fields' ), 10, 2 );
		add_filter( 'manage_edit-product_tag_columns', array( __CLASS__, 'manage_edit_taxonomy_columns' ) );
		add_filter( 'manage_product_tag_custom_column', array( __CLASS__, 'manage_taxonomy_custom_column' ), 10, 3 );

		$attribute_taxonomies = wc_get_attribute_taxonomies();
		if ( !empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $attribute ) {
				add_action( 'pa_' . $attribute->attribute_name . '_add_form_fields', array( __CLASS__, 'add_form_fields' ) );
				add_action( 'pa_' . $attribute->attribute_name . '_edit_form_fields', array( __CLASS__, 'edit_form_fields' ), 10, 2 );
				add_filter( 'manage_edit-pa_' . $attribute->attribute_name . '_columns', array( __CLASS__, 'manage_edit_taxonomy_columns' ) );
				add_filter( 'manage_pa_' . $attribute->attribute_name . '_custom_column', array( __CLASS__, 'manage_taxonomy_custom_column' ), 10, 3 );
			}
		}

		add_action( 'created_term', array( __CLASS__, 'created_term' ), 10, 3 );
		add_action( 'edit_term', array( __CLASS__, 'edit_term' ), 10, 3 );
	}

	/**
	 * Adds the filter image field to the taxonomy form.
	 */
	public static function add_form_fields( $taxonomy ) {
		wp_enqueue_media();
		?>
		<div class="form-field term-thumbnail-wrap">
			<label><?php _e( 'Search Filter Thumbnail Image', 'woocommerce-product-search' ); ?></label>
			<div id="product_search_image" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
			<div style="line-height: 60px;">
				<input type="hidden" id="product_search_image_id" name="product_search_image_id" />
				<button type="button" class="upload_product_search_filter_image_button button"><?php _e( 'Set image', 'woocommerce-product-search' ); ?></button>
				<button type="button" class="remove_product_search_filter_image_button button"><?php _e( 'Remove image', 'woocommerce-product-search' ); ?></button>
			</div>
			<?php self::render_image_script(); ?>
			<script type="text/javascript">
				document.addEventListener( "DOMContentLoaded", function() {
					if ( typeof jQuery !== "undefined" ) {
						jQuery( document ).ajaxComplete( function( event, request, options ) {
							if ( request && 4 === request.readyState && 200 === request.status
								&& options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {
								var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
								if ( ! res || res.errors ) {
									return;
								}
								// Clear Thumbnail fields on submit
								jQuery( '#product_search_image' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
								jQuery( '#product_search_image_id' ).val( '' );
								jQuery( '.remove_product_search_filter_image_button' ).hide();
								// Clear Display type field on submit
								jQuery( '#display_type' ).val( '' );
								return;
							}
						} );
					}
				} );
			</script>
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Edit category thumbnail field.
	 *
	 * @param mixed $term Term (category) being edited
	 */
	public static function edit_form_fields( $term, $taxonomy ) {

		wp_enqueue_media();
		$product_search_image_id = intval( get_term_meta( $term->term_id, 'product_search_image_id', true ) );
		if ( $product_search_image_id ) {
			$image_url = wp_get_attachment_thumb_url( $product_search_image_id );
		} else {
			$image_url = wc_placeholder_img_src();
		}
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Search Filter Thumbnail Image', 'woocommerce-product-search' ); ?></label></th>
			<td>
				<div id="product_search_image" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $image_url ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="product_search_image_id" name="product_search_image_id" value="<?php echo esc_attr( $product_search_image_id ); ?>" />
					<button type="button" class="upload_product_search_filter_image_button button"><?php _e( 'Set image', 'woocommerce-product-search' ); ?></button>
					<button type="button" class="remove_product_search_filter_image_button button"><?php _e( 'Remove image', 'woocommerce-product-search' ); ?></button>
				</div>
				<?php self::render_image_script(); ?>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Renders the image upload script part.
	 */
	private static function render_image_script() {
		?>
		<script type="text/javascript">
			document.addEventListener( "DOMContentLoaded", function() {
				if ( typeof jQuery !== "undefined" ) {
					// Only show the "remove image" button when needed
					if ( jQuery( '#product_search_image_id' ).val().length === 0 ) {
						jQuery( '.remove_product_search_filter_image_button' ).hide();
					}

					// Uploading files - Important : this must not be named file_frame as it clashes in namespace with the default for categories!
					var product_search_filter_image_file_frame;

					jQuery( document ).on( 'click', '.upload_product_search_filter_image_button', function( event ) {

						event.preventDefault();

						// If the media frame already exists, reopen it.
						if ( product_search_filter_image_file_frame ) {
							product_search_filter_image_file_frame.open();
							return;
						}

						// Create the media frame.
						product_search_filter_image_file_frame = wp.media.frames.downloadable_file = wp.media({
							title: '<?php _e( "Choose an image", "woocommerce" ); ?>',
							button: {
								text: '<?php _e( "Use image", "woocommerce" ); ?>'
							},
							multiple: false
						});

						// When an image is selected, run a callback.
						product_search_filter_image_file_frame.on( 'select', function() {
							var attachment           = product_search_filter_image_file_frame.state().get( 'selection' ).first().toJSON();
							var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

							jQuery( '#product_search_image_id' ).val( attachment.id );
							jQuery( '#product_search_image' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
							jQuery( '.remove_product_search_filter_image_button' ).show();
						});

						// Finally, open the modal.
						product_search_filter_image_file_frame.open();
					});

					jQuery( document ).on( 'click', '.remove_product_search_filter_image_button', function() {
						jQuery( '#product_search_image' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
						jQuery( '#product_search_image_id' ).val( '' );
						jQuery( '.remove_product_search_filter_image_button' ).hide();
						return false;
					});
				}
			} );
		</script>
		<?php
	}

	/**
	 * Saves our filter image data when a new term is created.
	 *
	 * @uses self::edit_term()
	 *
	 * @param int $term_id Term ID
	 * @param int $tt_id Term taxonomy ID
	 * @param string $taxonomy Taxonomy slug
	 */
	public static function created_term( $term_id, $tt_id, $taxonomy ) {
		self::edit_term( $term_id, $tt_id, $taxonomy );
	}

	/**
	 * Saves our filter image data when a term is updated.
	 *
	 * @param int $term_id Term ID
	 * @param int $tt_id Term taxonomy ID
	 * @param string $taxonomy Taxonomy slug
	 */
	public static function edit_term( $term_id, $tt_id, $taxonomy ) {
		if ( isset( $_POST['product_search_image_id'] ) ) {
			update_term_meta( $term_id, 'product_search_image_id', absint( $_POST['product_search_image_id'] ) );
		}
	}

	/**
	 * Adds our Search Filter Thumbnail Image column.
	 *
	 * @param array $columns current columns
	 * @return array
	 */
	public static function manage_edit_taxonomy_columns( $columns ) {
		$columns['product_search_image'] = sprintf(
			'<span title="%s">%s</span>',
			esc_attr__( 'Search Filter Thumbnail Image', 'woocommerce-product-search' ),
			esc_html__( 'Thumbnail', 'woocommerce-product-search' )
		);
		return $columns;
	}

	/**
	 * Renders the filter image or placeholder image.
	 *
	 * @param string $content current rendered column content
	 * @param string $column_name which current column to render
	 * @param int $term_id the ID of the current term for which to render the column content
	 *
	 * @return string rendered column content
	 */
	public static function manage_taxonomy_custom_column( $content, $column_name, $term_id ) {
		if ( $column_name === 'product_search_image' ) {
			$product_search_image_id = get_term_meta( $term_id, 'product_search_image_id', true );
			if ( $product_search_image_id ) {
				$url = wp_get_attachment_thumb_url( $product_search_image_id );
				$title = get_the_title( $product_search_image_id );
				if ( empty( $title ) ) {
					$title = __( 'Search Filter Thumbnail Image', 'woocommerce-product-search' );
				}
			} else {
				$url = wc_placeholder_img_src();
				$title = __( 'No Search Filter Thumbnail Image', 'woocommerce-product-search' );
			}
			$content .= sprintf(
				'<img src="%s" alt="%s" title="%s" class="wp-post-image" height="48" width="48" />',
				esc_url( $url ),
				esc_attr( $title ),
				esc_attr( $title )
			);
		}
		return $content;
	}

}

WooCommerce_Product_Search_Admin_Taxonomy::init();
