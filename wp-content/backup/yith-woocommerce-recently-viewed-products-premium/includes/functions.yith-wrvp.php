<?php
/**
 * Plugin Utility Functions
 *
 * @author YITH
 * @package YITH WooCommerce Recently Viewed Products
 * @version 1.0.0
 */


if ( !defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if( ! function_exists( 'ywrvp_campaign_build_link' ) ) {
	/**
	 * Build link of the product with Google Analytics options
	 *
	 * @since 1.0.4
	 * @param $link
	 * @return string
	 * @author Francesco Licandro
	 */
	function ywrvp_campaign_build_link( $link ) {

		if ( get_option( 'yith-wrvp-enable-analytics' ) == 'yes' ) {

			$campaign_source  = str_replace( ' ', '%20', get_option( 'yith-wrvp-campaign-source' ) );
			$campaign_medium  = str_replace( ' ', '%20', get_option( 'yith-wrvp-campaign-medium' ) );
			$campaign_term    = str_replace( ',', '+', get_option( 'yith-wrvp-campaign-term' ) );
			$campaign_content = str_replace( ' ', '%20', get_option( 'yith-wrvp-campaign-content' ) );
			$campaign_name    = str_replace( ' ', '%20', get_option( 'yith-wrvp-campaign-name' ) );

			$query_args = array(
					'utm_source' => $campaign_source,
					'utm_medium' => $campaign_medium,
			);

			if ( $campaign_term != '' ) {

				$query_args['utm_term'] = $campaign_term;

			}

			if ( $campaign_content != '' ) {

				$query_args['utm_content'] = $campaign_content;

			}

			$query_args['utm_name'] = $campaign_name;

			$link = add_query_arg( $query_args, $link );

		}

		return apply_filters( 'yith_wrvp_campaign_build_link', $link );
	}
}

if( ! function_exists( 'ywrvp_parse_with_default' ) ) {
	/**
	 * Parse args with default options for options type
	 *
	 * @since 1.2.0
	 * @param array $data
	 * @return array
	 * @author Francesco Licandro
	 */
	function ywrvp_parse_with_default( $data ) {

		$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array()
		);

		return wp_parse_args( $data, $defaults );
	}
}

if( ! function_exists( 'ywrvp_email_select_products_html' ) ) {
	/**
	 * Print select products html for email options
	 *
	 * @since 1.2.0
	 * @param string $key
	 * @param array $data
	 * @param object $email
	 * @return string
	 * @author Francesco Licandro
	 */
	function ywrvp_email_select_products_html( $key, $data, $email ) {

		$field = $email->get_field_key( $key );
		$data  = ywrvp_parse_with_default( $data );

		$products = $email->get_option( $key );
		! is_array( $products ) && $products = explode( ',', $products );
		// remove empty
		$products = array_filter( $products );
		$json_ids = array();

		foreach ( $products as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( is_object( $product ) ) {
				$json_ids[ $product_id ] = wp_kses_post( html_entity_decode( $product->get_formatted_name() ) );
			}
		}

		version_compare( WC()->version, '3.0', '<' ) && $products = implode(',', $products);

		ob_start();
		?>

		<tr valign="top">
			<th scope="row" class="select_products">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $email->get_tooltip_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span>
					</legend>
					<div id="<?php echo esc_attr( $field ); ?>-container">
						<div class="option">
							<?php
							yit_add_select2_fields( array(
								'class'         => 'wc-product-search',
								'id'            => esc_attr( $field ),
								'name'          => esc_attr( $field ),
								'style'         => 'width:50%;',
								'data-multiple' => true,
								'data-selected' => $json_ids,
								'value'         => $products

							) );
							?>
							<?php echo wp_kses_post( $email->get_description_html( $data ) ); ?>
						</div>
					</div>
				</fieldset>
			</td>
		</tr>

		<?php

		return ob_get_clean();
	}
}

if( ! function_exists( 'ywrvp_email_textarea_editor_html') ) {
	/**
	 * Print textarea editor html for email options
	 *
	 * @since 1.2.0
	 * @param string $key
	 * @param array $data
	 * @param object $email
	 *
	 * @return string
	 * @author Francesco Licandro
	 */
	function ywrvp_email_textarea_editor_html( $key, $data, $email ) {

		$field = $email->get_field_key( $key );
		$data  = ywrvp_parse_with_default( $data );

		$editor_args = array(
			'wpautop'       => true,
			// use wpautop?
			'media_buttons' => true,
			// show insert/upload button(s)
			'textarea_name' => esc_attr( $field ),
			// set the textarea name to something different, square brackets [] can be used here
			'textarea_rows' => 20,
			// rows="..."
			'tabindex'      => '',
			'editor_css'    => '',
			// intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
			'editor_class'  => '',
			// add extra class(es) to the editor textarea
			'teeny'         => false,
			// output the minimal editor config used in Press This
			'dfw'           => false,
			// replace the default fullscreen with DFW (needs specific DOM elements and css)
			'tinymce'       => true,
			// load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
			'quicktags'     => true
			// load Quicktags, can be used to pass settings directly to Quicktags using an array()
		);

		ob_start();
		?>

		<tr valign="top">
			<th scope="row" class="select_categories">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo wp_kses_post( $email->get_tooltip_html( $data ));  ?>
			</th>
			<td class="forminp">
				<fieldset>
					<div id="<?php echo esc_attr( $field ); ?>-container">
						<div
							class="editor"><?php wp_editor( $email->get_option( $key ), esc_attr( $field ), $editor_args ); ?></div>
						<?php echo wp_kses_post( $email->get_description_html( $data ) ); ?>
					</div>
				</fieldset>
			</td>
		</tr>

		<?php

		return ob_get_clean();
	}
}

if( ! function_exists( 'ywrvp_email_upload_html' ) ) {
	/**
	 * Print upload type html for email options
	 *
	 * @since 1.2.0
	 *
	 * @param string $key
	 * @param array $data
	 * @param object $email
	 * @return string
	 * @author Francesco Licandro
	 */
	function ywrvp_email_upload_html( $key, $data, $email ) {

		$field = $email->get_field_key( $key );
		$data  = ywrvp_parse_with_default( $data );

		ob_start();
		?>

		<tr valign="top">
			<th scope="row">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo wp_kses_post( $email->get_tooltip_html( $data ) ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<div id="<?php echo esc_attr( $field ); ?>-container" class="plugin-option">
						<input type="text" name="<?php echo esc_attr( $field ); ?>"
						       id="<?php echo esc_attr( $field ); ?>" value="<?php echo esc_html( $email->get_option( $key ) ); ?>"
						       class="upload_img_url" style="width: 25em;"/>
						<input type="button" value="<?php esc_html_e( 'Upload', 'yith-plugin-fw' ) ?>"
						       id="<?php echo esc_attr( $field ); ?>-button" class="upload_button yith-plugin-fw-upload-button button"/>
						<?php echo wp_kses_post( $email->get_description_html( $data ) ); ?>
					</div>
					<div class="upload_img_preview yith-wrvp" style="margin-top:10px;">
						<?php
						$file = $email->get_option( $key );
						if ( preg_match( '/(jpg|jpeg|png|gif|ico)$/', $file ) ) {
							echo '<img src="' . $file . '" />'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</div>
				</fieldset>
			</td>
		</tr>

		<?php

		return ob_get_clean();
	}
}

if( ! function_exists( 'ywrvp_email_radio_html' ) ) {
	/**
	 * Print radio type html for email options
	 *
	 * @since 1.2.0
	 *
	 * @param string $key
	 * @param array $data
	 * @param object $email
	 * @return string
	 * @author Francesco Licandro
	 */
	function ywrvp_email_radio_html( $key, $data, $email ) {

		$field      = $email->get_field_key( $key );
		$data       = ywrvp_parse_with_default( $data );
		$current    = $email->get_option( $key );

		if( empty( $data['options'] ) ) {
		    return '';
        }

		ob_start();
		?>

		<tr valign="top">
			<th scope="row">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo wp_kses_post( $email->get_tooltip_html( $data )) ; ?>
			</th>
			<td class="forminp">
				<fieldset>
					<div id="<?php echo esc_attr( $field ); ?>-container" class="plugin-option">
                        <ul>
                        <?php foreach( $data['options'] as $key => $value ) : ?>
                            <li>
                                <input type="radio" id="<?php echo esc_attr( $field . '-' . $key ); ?>" name="<?php echo esc_attr( $field ); ?>"
                                       value="<?php echo esc_attr( $key ) ?>" <?php checked( $key, $current ); ?>>
                                <label for="<?php echo esc_attr( $field . '-' . $key ); ?>"><?php echo esc_html( $value ); ?></label>
                            </li>
                        <?php endforeach; ?>
                        </ul>
					    <?php echo wp_kses_post( $email->get_description_html( $data )); ?>
					</div>
				</fieldset>
			</td>
		</tr>

		<?php

		return ob_get_clean();
	}
}

if( ! function_exists( 'yith_wrvp_get_mail_copuon_code_html' ) ) {
	/**
	 * Get coupon code html
	 *
	 * @since 1.2.0
	 *
	 * @param string $coupon_code
	 * @return string
	 * @author Francesco Licandro
	 */
	function yith_wrvp_get_mail_copuon_code_html( $coupon_code ) {

		if ( ! $coupon_code ) {
			return '';
		}

		$coupon_image = apply_filters( 'yith_wrvp_coupon_code_image_email', YITH_WRVP_ASSETS_URL . '/images/coupon-code.png', $coupon_code );

		ob_start(); ?>
		<div id="coupon-code">
			<span style="background-image: url('<?php echo esc_url( $coupon_image ); ?>');"><?php echo esc_html( $coupon_code ) ?></span>
		</div>
		<?php $return = ob_get_clean();

		return apply_filters( 'yith_wrvp_coupon_code_html_email', $return, $coupon_code, $coupon_image );
	}
}

if( ! function_exists( 'yith_wrvp_get_mail_product_image')) {
	/**
	 * Get product image html for plugin email
	 * 
	 * @since 1.2.0
	 * @author Francesco Licandro
	 * @param object $product \WC_Product
	 * @param string $product_link
	 * @return string
	 */
	function yith_wrvp_get_mail_product_image( $product, $product_link = '' ){

		! $product_link && $product_link = $product->get_permalink();

		$size       = apply_filters( 'yith_wrvp_email_image_size', 'ywrvp_image_size' );
		$dimensions = ( $size == 'ywrvp_image_size' && get_option( 'yith-wrvp-image-size', '' ) ) ? get_option( 'yith-wrvp-image-size' ) : wc_get_image_size( $size );
		// set image attr
		$height     = esc_attr( $dimensions['height'] );
		$width      = esc_attr( $dimensions['width'] );
		// get image id
		$image_id   = is_callable( array( $product, 'get_image_id' ) ) ? $product->get_image_id() : get_post_thumbnail_id( $product );
		// build image html
		$src        = ( $image_id && wp_get_attachment_image_src( $image_id, $size ) ) ? current( wp_get_attachment_image_src( $image_id, $size ) ) : wc_placeholder_img_src();
		$image      = '<a href="' . $product_link .'"><img src="'. $src . '" height="' . $height . '" width="' . $width . '" /></a>';

		return apply_filters( 'yith_wrvp_get_mail_product_image_filter', $image, $product, $product_link );
	}
}

if( ! function_exists( 'yith_wrvp_set_transient' ) ) {
    /**
     * Set transient using custom function or WP function
     *
     * @since 1.5.0
     * @author Francesco Licandro
     * @param string $transient
     * @param mixed $value
     * @param string|integer $expire
     */
    function yith_wrvp_set_transient( $transient, $value, $expire ){
        class_exists( 'YITH_Toolkit_Transient' ) ? YITH_Toolkit_Transient::set_transient( $transient, $value, $expire ) : set_transient( $transient, $value, $expire );
    }
}

if( ! function_exists( 'yith_wrvp_get_transient' ) ) {
    /**
     * Get transient using custom function or WP function
     *
     * @since 1.5.0
     * @author Francesco Licandro
     * @param string $transient
     * @return mixed
     */
    function yith_wrvp_get_transient( $transient ){
        return class_exists( 'YITH_Toolkit_Transient' ) ? YITH_Toolkit_Transient::get_transient( $transient ) : get_transient( $transient );
    }
}

if( ! function_exists( 'yith_wrvp_get_categories_list' ) ) {
    /**
     * Get a list of product categories
     *
     * @since 1.4.5
     * @author Francesco Licandro
     * @return array
     */
    function yith_wrvp_get_categories_list(){

        $transient_name = 'yith_wrvp_categories_list';

        if( ( $categories = yith_wrvp_get_transient( $transient_name ) ) === false ) {
            $categories = array();
            $terms      = get_terms( array(
                'taxonomy' => 'product_cat',
                'hide_empty' => true,
            ) );

            if( ! empty( $terms ) ) {
                foreach( $terms as $term ) {
                    $categories[ $term->term_id ] = $term->name;
                }
            }

            yith_wrvp_set_transient( $transient_name, $categories, WEEK_IN_SECONDS );
        }

        return $categories;
    }
}