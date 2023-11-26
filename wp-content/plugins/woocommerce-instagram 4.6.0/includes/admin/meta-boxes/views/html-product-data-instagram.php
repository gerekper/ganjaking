<?php
/**
 * Product Data - Instagram
 *
 * @package WC_Instagram/Admin/Meta Boxes
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="instagram_data" class="panel woocommerce_options_panel">
	<h3 class="options_group_heading"><?php esc_html_e( 'Catalog data', 'woocommerce-instagram' ); ?></h3>
	<div class="options_group">
		<?php
		woocommerce_wp_text_input(
			array(
				'id'          => '_instagram_brand',
				'label'       => _x( 'Brand', 'product data setting title', 'woocommerce-instagram' ),
				'description' => _x( 'The brand of the product.', 'product data setting desc', 'woocommerce-instagram' ),
				'desc_tip'    => true,
			)
		);

		woocommerce_wp_select(
			array(
				'id'          => '_instagram_condition',
				'label'       => _x( 'Condition', 'product data setting title', 'woocommerce-instagram' ),
				'description' => _x( 'The product condition.', 'product data setting desc', 'woocommerce-instagram' ),
				'desc_tip'    => true,
				'options'     => array( '' => _x( 'Default', 'setting option', 'woocommerce-instagram' ) ) + wc_instagram_get_product_conditions(),
			)
		);

		woocommerce_wp_select(
			array(
				'id'          => '_instagram_images_option',
				'label'       => _x( 'Images', 'product data setting title', 'woocommerce-instagram' ),
				'description' => _x( 'The product images to include in the catalog.', 'product data setting desc', 'woocommerce-instagram' ),
				'desc_tip'    => true,
				'options'     => array(
					''         => _x( 'Default', 'setting option', 'woocommerce-instagram' ),
					'all'      => _x( 'All the images', 'setting option', 'woocommerce-instagram' ),
					'featured' => _x( 'Featured image', 'setting option', 'woocommerce-instagram' ),
				),
			)
		);
		?>
	</div>

	<div class="options_group">
		<?php
		woocommerce_wp_text_input(
			array(
				'id'          => '_instagram_google_product_category',
				'label'       => _x( 'Product category', 'product data setting title', 'woocommerce-instagram' ),
				'description' => _x( 'A product category value provided by Google feed.', 'product data setting desc', 'woocommerce-instagram' ),
				'desc_tip'    => true,
				'type'        => 'hidden',
				'class'       => 'wc-instagram-gpc-field',
			)
		);
		?>
	</div>

	<h3 class="options_group_heading"><?php esc_html_e( 'Hashtag images', 'woocommerce-instagram' ); ?></h3>
	<div class="options_group">
		<?php
		if ( ! wc_instagram_has_business_account() ) :
			printf(
				'<p class="options_group_description"><span class="wc-instagram-notice warning feature-not-available">%1$s <a href="%2$s" target="_blank">%3$s</a></span></p>',
				esc_html__( 'This functionality requires a Facebook Page connected to your Instagram Business account.', 'woocommerce-instagram' ),
				esc_url( wc_instagram_get_settings_url() ),
				esc_html__( 'Go to settings', 'woocommerce-instagram' )
			);
		else :
			woocommerce_wp_text_input(
				array(
					'id'          => '_instagram_hashtag',
					'label'       => _x( 'Hashtag', 'product data setting title', 'woocommerce-instagram' ),
					'description' => _x( 'Display images for a given hashtag.', 'product data setting desc', 'woocommerce-instagram' ),
					'desc_tip'    => true,
				)
			);

			woocommerce_wp_select(
				array(
					'id'          => '_instagram_hashtag_images_type',
					'label'       => _x( 'Images to display', 'product data setting title', 'woocommerce-instagram' ),
					'description' => _x( 'Choose the images to display.', 'product data setting desc', 'woocommerce-instagram' ),
					'desc_tip'    => true,
					'options'     => array(
						''           => _x( 'Default', 'setting option', 'woocommerce-instagram' ),
						'recent_top' => _x( 'Recent images + Top images', 'setting option', 'woocommerce-instagram' ),
						'recent'     => _x( 'Recent images', 'setting option', 'woocommerce-instagram' ),
						'top'        => _x( 'Top images', 'setting option', 'woocommerce-instagram' ),
					),
				)
			);
		endif;
		?>
	</div>
</div>
