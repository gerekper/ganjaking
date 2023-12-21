<?php
namespace Happy_Addons_Pro\Widget\Skins\Product_Category_Carousel;

use Elementor\Utils;

defined( 'ABSPATH' ) || die();

class Classic extends Skin_Base {

	/**
	 * Get widget ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'classic';
	}

	/**
	 * Get widget title
	 *
	 * @return string widget title
	 */
	public function get_title() {
		return __( 'Classic', 'happy-addons-pro' );
	}

	/**
	 * render content
	 *
	 * @return void
	 */
	public function render() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			printf( '<div class="ha-cat-carousel-error">%s</div>', __( 'Please Install/Activate Woocommerce Plugin.', 'happy-addons-pro' ) );

			return;
		}

		$settings = $this->parent->get_settings_for_display();
		$product_cats = $this->parent->get_query();

		if ( empty( $product_cats ) ) {
			if ( is_admin() ) {
				return printf( '<div class="ha-cat-carousel-error">%s</div>', __( 'Nothing Found. Please Add Category.', 'happy-addons-pro' ) );
			}
		}

		$this->parent->add_render_attribute(
			'wrapper',
			'class',
			[
				'ha-product-cat-carousel',
				'ha-product-cat-carousel-'. $this->get_id(),
			]
		);
		?>

		<div <?php $this->parent->print_render_attribute_string( 'wrapper' ); ?>>
			<?php
			foreach ( $product_cats as $product_cat ) :

				$image_src = Utils::get_placeholder_image_src();
				$thumbnail_id = get_term_meta( $product_cat->term_id, 'thumbnail_id', true );
				$image = wp_get_attachment_image_src( $thumbnail_id, $this->get_instance_value( 'cat_image_size' ), false );
				if ( $image ) {
					$image_src = $image[0];
				}

				$has_image = '';
				if ( 'yes' == $this->get_instance_value( 'cat_image_show' ) ) {
					$has_image = esc_attr( ' ha-product-cat-carousel-has-image' );
				}
				?>
				<article class="ha-product-cat-carousel-item<?php echo esc_attr( ' ' . $has_image ); ?>">
					<div class="ha-product-cat-carousel-item-inner">
						<?php if ( $image_src && 'yes' == $this->get_instance_value( 'cat_image_show' ) ) : ?>
							<div class="ha-product-cat-carousel-thumbnail">
								<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $product_cat->name ); ?>">
							</div>
						<?php endif; ?>
						<div class="ha-product-cat-carousel-content">
							<div class="ha-product-cat-carousel-content-inner">
								<<?php echo ha_escape_tags( $this->get_instance_value( 'title_tag' ), 'h2' ).' class="ha-product-cat-carousel-title"';?>>
									<a href="<?php echo esc_url( get_term_link( $product_cat->term_id, 'product_cat' ) ); ?>">
										<?php echo esc_html( $product_cat->name ); ?>
									</a>
								</<?php echo ha_escape_tags( $this->get_instance_value( 'title_tag' ), 'h2' );?>>

								<?php if ( $this->get_instance_value( 'show_cats_count' ) == 'yes' ) : ?>
									<div class="ha-product-cat-carousel-count"><?php printf( '%s%s%s', '(', esc_html( $product_cat->count ), ')' ); ?></div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</article>
				<?php
			endforeach;
			?>
		</div>

		<?php
	}
}
