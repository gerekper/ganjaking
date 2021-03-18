<?php

class WC_Swatches_Product_Attribute_Images {

	private $taxonomy;
	private $meta_key;
	private $image_size = 'shop_thumb';
	private $image_width = 32;
	private $image_height = 32;

	/**
	 * Constructor.
	 *
	 * Sets up a new Product Attribute image type
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $attribute_image_key a meta key to store the custom image for
	 * @param string $image_size a registered image size to use for this product attribute image
	 *
	 * @return WC_Product_Attribute_Images
	 */
	public function __construct( $attribute_image_key = 'thumbnail_id', $image_size = 'shop_thumb' ) {
		$this->meta_key = $attribute_image_key;
		$this->image_size = $image_size;

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array(&$this, 'on_admin_scripts') );
			add_action( 'current_screen', array(&$this, 'init_attribute_image_selector') );

			add_action( 'created_term', array(&$this, 'woocommerce_attribute_thumbnail_field_save'), 10, 3 );
			add_action( 'edit_term', array(&$this, 'woocommerce_attribute_thumbnail_field_save'), 10, 3 );
		}

		add_action( 'admin_init', array($this, 'on_admin_init') );

		//Hook for when the actual product attribute itself is modified.
		add_action('woocommerce_attribute_updated', array($this, 'on_woocommerce_attribute_updated'), 10, 3);
	}

	public function on_admin_init() {

		if (isset($_REQUEST['taxonomy'])){
			$this->taxonomy = $_REQUEST['taxonomy'];
		}

		$attribute_taxonomies = WC_Swatches_Compatibility::wc_get_attribute_taxonomies();
		if ( $attribute_taxonomies ) {
			foreach ( $attribute_taxonomies as $tax ) {

				add_action( 'pa_' . $tax->attribute_name . '_add_form_fields', array(&$this, 'woocommerce_add_attribute_thumbnail_field') );
				add_action( 'pa_' . $tax->attribute_name . '_edit_form_fields', array(&$this, 'woocommerce_edit_attributre_thumbnail_field'), 10, 2 );

				add_filter( 'manage_edit-pa_' . $tax->attribute_name . '_columns', array(&$this, 'woocommerce_product_attribute_columns') );
				add_filter( 'manage_pa_' . $tax->attribute_name . '_custom_column', array(&$this, 'woocommerce_product_attribute_column'), 10, 3 );
			}
		}
	}

	//Enqueue the scripts if on a product attribute page
	public function on_admin_scripts() {
		$screen = get_current_screen();
		if ( strpos( $screen->id, 'pa_' ) !== false ) :
			wp_enqueue_media();
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_style( 'thickbox' );
			if ( function_exists( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
		endif;
	}

	//Initalize the actions for all product attribute taxonomoies
	public function init_attribute_image_selector() {
		global $_wp_additional_image_sizes;
		$screen = get_current_screen();

		if ( strpos( $screen->id, 'pa_' ) !== false ) :

			if ( taxonomy_exists( $_REQUEST['taxonomy'] ) ) {
				$term_id = term_exists( isset( $_REQUEST['tag_ID'] ) ? $_REQUEST['tag_ID'] : 0, $_REQUEST['taxonomy'] );
				$term = 0;
				if ( $term_id ) {
					$term = get_term( $term_id, $_REQUEST['taxonomy'] );
				}

				$this->image_size = apply_filters( 'woocommerce_get_swatches_image_size', $this->image_size, $_REQUEST['taxonomy'], $term_id );
			}

			$the_size = isset( $_wp_additional_image_sizes[$this->image_size] ) ? $_wp_additional_image_sizes[$this->image_size] : $_wp_additional_image_sizes['shop_thumbnail'];

			if ( isset( $the_size['width'] ) && isset( $the_size['height'] ) ) {
				$this->image_width = $the_size['width'];
				$this->image_height = $the_size['height'];
			} else {
				$this->image_width = 32;
				$this->image_height = 32;
			}




		endif;
	}

	//The field used when adding a new term to an attribute taxonomy
	public function woocommerce_add_attribute_thumbnail_field() {
		?>
		<div class="form-field ">
			<label for="product_attribute_swatchtype_<?php echo $this->meta_key; ?>">Swatch Type</label>
			<select name="product_attribute_meta[<?php echo $this->meta_key; ?>][type]" id="product_attribute_swatchtype_<?php echo $this->meta_key; ?>" class="postform">
				<option value="-1">None</option>
				<option value="color">Color Swatch</option>
				<option value="photo">Image</option>
			</select>

			<script type="text/javascript">
				jQuery(document).ready(function ($) {

					$('#product_attribute_swatchtype_<?php echo $this->meta_key; ?>').change(function () {
						$('.swatch-field-active').hide().removeClass('swatch-field-active');
						$('.swatch-field-' + $(this).val()).slideDown().addClass('swatch-field-active');
					});

				});

			</script>
		</div>

		<div class="form-field swatch-field swatch-field-color section-color-swatch" style="overflow:visible;display:none;">
			<div id="swatch-color" class="<?php echo sanitize_title( $this->meta_key ); ?>-color">
				<label><?php _e( 'Color', 'wc_swatches_and_photos' ); ?></label>
				<div id="product_attribute_color_<?php echo $this->meta_key; ?>_picker" class="colorSelector"><div></div></div>
				<input class="woo-color"
				       id="product_attribute_color_<?php echo $this->meta_key; ?>"
				       type="text" class="text"
				       name="product_attribute_meta[<?php echo $this->meta_key; ?>][color]"
				       value="#FFFFFF" />
			</div>
		</div>

		<div class="sub_field form-field swatch-field swatch-field-photo" style="overflow:visible;display:none;">
			<div id="swatch-photo" class="<?php echo sanitize_title( $this->meta_key ); ?>-photo">
				<label><?php _e( 'Thumbnail', 'woocommerce' ); ?></label>
				<div id="product_attribute_thumbnail_<?php echo $this->meta_key; ?>" style="float:left;margin-right:10px;">
					<img src="<?php echo apply_filters( 'woocommerce_placeholder_img_src', WC()->plugin_url() . '/assets/images/placeholder.png' ); ?>" width="<?php echo $this->image_width; ?>px" height="<?php echo $this->image_height; ?>px" />
				</div>
				<div style="line-height:60px;">
					<input type="hidden"  class="upload_image_id" id="product_attribute_<?php echo $this->meta_key; ?>" name="product_attribute_meta[<?php echo $this->meta_key; ?>][photo]" />
					<button type="submit" class="upload_swatch_image_button button"><?php _e( 'Upload/Add image', 'woocommerce' ); ?></button>
					<button type="submit" class="remove_swatch_image_button button"><?php _e( 'Remove image', 'woocommerce' ); ?></button>
				</div>

				<div class="clear"></div>
			</div>
		</div>
		<?php
	}

	//The field used when editing an existing proeuct attribute taxonomy term
	public function woocommerce_edit_attributre_thumbnail_field( $term, $taxonomy ) {
		$swatch_term = new WC_Swatch_Term( $this->meta_key, $term->term_id, $taxonomy, false, $this->image_size );
		?>

		<tr class="form-field ">
			<th scope="row" valign="top"><label><?php _e( 'Type', 'wc_swatches_and_photos' ); ?></label></th>
			<td>
				<label for="product_attribute_swatchtype_<?php echo $this->meta_key; ?>">Swatch Type</label>
				<select name="product_attribute_meta[<?php echo $this->meta_key; ?>][type]" id="product_attribute_swatchtype_<?php echo $this->meta_key; ?>" class="postform">
					<option <?php selected( 'none', $swatch_term->get_type() ); ?> value="-1"><?php _e( 'None', 'wc_swatches_and_photos' ); ?></option>
					<option <?php selected( 'color', $swatch_term->get_type() ); ?> value="color"><?php _e( 'Color Swatch', 'wc_swatches_and_photos' ); ?></option>
					<option <?php selected( 'photo', $swatch_term->get_type() ); ?> value="photo"><?php _e( 'Photo', 'wc_swatches_and_photos' ); ?></option>
				</select>


				<script type="text/javascript">
					jQuery(document).ready(function ($) {

						$('#product_attribute_swatchtype_<?php echo $this->meta_key; ?>').change(function () {
							$('.swatch-field-active').hide().removeClass('swatch-field-active');
							$('.swatch-field-' + $(this).val()).show().addClass('swatch-field-active');
						});

					});
				</script>
			</td>
		</tr>

		<?php $style = $swatch_term->get_type() != 'color' ? 'display:none;' : ''; ?>
		<tr class="form-field swatch-field swatch-field-color section-color-swatch" style="overflow:visible;<?php echo $style; ?>">
			<th scope="row" valign="top"><label><?php _e( 'Color', 'wc_swatches_and_photos' ); ?></label></th>
			<td>
				<div id="swatch-color" class="<?php echo sanitize_title( $this->meta_key ); ?>-color">

					<div id="product_attribute_color_<?php echo $this->meta_key; ?>_picker" class="colorSelector"><div></div></div>
					<input class="woo-color"
					       id="product_attribute_color_<?php echo $this->meta_key; ?>"
					       type="text" class="text"
					       name="product_attribute_meta[<?php echo $this->meta_key; ?>][color]"
					       value="<?php echo $swatch_term->get_color(); ?>" />
				</div>

			</td>
		</tr>

		<?php $style = $swatch_term->get_type() != 'photo' ? 'display:none;' : ''; ?>
		<tr class="form-field sub_field swatch-field swatch-field-photo" style="overflow:visible;<?php echo $style; ?>">
			<th scope="row" valign="top"><label><?php _e( 'Photo', 'wc_swatches_and_photos' ); ?></label></th>
			<td>
				<div id="product_attribute_thumbnail_<?php echo $this->meta_key; ?>" style="float:left;margin-right:10px;">
					<img src="<?php echo $swatch_term->get_image_src(); ?>"  width="<?php echo $swatch_term->get_width(); ?>px" height="<?php echo $swatch_term->get_height(); ?>px" />
				</div>
				<div style="line-height:60px;">
					<input class="upload_image_id" type="hidden" id="product_attribute_<?php echo $this->meta_key; ?>" name="product_attribute_meta[<?php echo $this->meta_key; ?>][photo]" value="<?php echo $swatch_term->get_image_id(); ?>" />
					<button type="submit" class="upload_swatch_image_button button"><?php _e( 'Upload/Add image', 'woocommerce' ); ?></button>
					<button type="submit" class="remove_swatch_image_button button"><?php _e( 'Remove image', 'woocommerce' ); ?></button>
				</div>

				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}

	//Saves the product attribute taxonomy term data
	public function woocommerce_attribute_thumbnail_field_save( $term_id, $tt_id, $taxonomy ) {
		if ( isset( $_POST['product_attribute_meta'] ) ) {

			$metas = $_POST['product_attribute_meta'];
			if ( isset( $metas[$this->meta_key] ) ) {
				$data = $metas[$this->meta_key];

				$photo = isset( $data['photo'] ) ? $data['photo'] : '';
				$color = isset( $data['color'] ) ? $data['color'] : '';
				$type = isset( $data['type'] ) ? $data['type'] : '';


				update_term_meta( $term_id, $taxonomy . '_' . $this->meta_key . '_type', $type );
				update_term_meta( $term_id, $taxonomy . '_' . $this->meta_key . '_photo', $photo );
				update_term_meta( $term_id, $taxonomy . '_' . $this->meta_key . '_color', $color );
			}
		}
	}

	//Registers a column for this attribute taxonomy for this image
	public function woocommerce_product_attribute_columns( $columns ) {
		$new_columns = array();
		$new_columns['cb'] = $columns['cb'];
		$new_columns[$this->meta_key] = __( 'Thumbnail', 'wc_swatches_and_photos' );
		unset( $columns['cb'] );
		$columns = array_merge( $new_columns, $columns );
		return $columns;
	}

	//Renders the custom column as defined in woocommerce_product_attribute_columns
	public function woocommerce_product_attribute_column( $columns, $column, $id ) {
		if ( $column == $this->meta_key ) :
			$swatch_term = new WC_Swatch_Term( $this->meta_key, $id, $this->taxonomy, false, $this->image_size );
			$columns .= $swatch_term->get_output();
		endif;
		return $columns;
	}


	/**
	 * When someone updates the actual product attribute itself.  Need to rename our hashes.
	 * @param $attribute_id
	 * @param $attribute
	 * @param $old_attribute_name
	 */
	public function on_woocommerce_attribute_updated($attribute_id, $attribute, $old_attribute_name){
		global $wpdb;

		$old_key =  md5( sanitize_title( 'pa_' . $old_attribute_name ) );
		$new_key = md5( sanitize_title( 'pa_' . $attribute['attribute_name'] ) );

		if ($old_key == $new_key){
			return;
		}

		$posts_to_update = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_swatch_type' AND meta_value = 'pickers'");

		if ($posts_to_update && !is_wp_error($posts_to_update)){
			foreach($posts_to_update as $post_id){
			    $product = wc_get_product($post_id);
				$swatch_type_options = $product->get_meta('_swatch_type_options', true );
				if (isset($swatch_type_options[$old_key])){
					$swatch_type_options[$new_key] = $swatch_type_options[$old_key];
					unset($swatch_type_options[$old_key]);
					$product->update_meta_data('_swatch_type_options', $swatch_type_options);
					$product->save_meta_data();
				}
			}
		}

		//Update term meta next:
		$old_meta_key = 'pa_' . $old_attribute_name . '_';
		$new_meta_key = 'pa_' . $attribute['attribute_name'] . '_';

		$sql = $wpdb->prepare("UPDATE $wpdb->termmeta SET meta_key = %s WHERE meta_key = %s", $new_meta_key . 'swatches_id_type', $old_meta_key . 'swatches_id_type');
		$wpdb->query($sql);
		$wpdb->query($wpdb->prepare("UPDATE $wpdb->termmeta SET meta_key = %s WHERE meta_key = %s", $new_meta_key . 'swatches_id_photo', $old_meta_key . 'swatches_id_photo'));
		$wpdb->query($wpdb->prepare("UPDATE $wpdb->termmeta SET meta_key = %s WHERE meta_key = %s", $new_meta_key . 'swatches_id_color', $old_meta_key . 'swatches_id_color'));

		return;
	}

}
