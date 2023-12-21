<?php
namespace Happy_Addons_Pro\Widget\Skins\Product_Grid;

use Elementor\Skin_Base;

defined( 'ABSPATH' ) || exit;

class Classic extends Skin_Base {

	public function get_id() {
		return 'classic';
	}

	public function get_title() {
		return _x( 'Classic', 'Product Grid widget skin', 'happy-addons-pro' );
	}

	public function __add_hooks() {
		$this->parent->__add_hooks();

		add_filter( 'woocommerce_loop_add_to_cart_link', [ $this, '__update_add_to_cart' ], 10, 3 );
	}

	public function __remove_hooks() {
		$this->parent->__remove_hooks();

		remove_filter( 'woocommerce_loop_add_to_cart_link', [ $this, '__update_add_to_cart' ], 10, 3 );
	}

	public function __update_add_to_cart( $html, $product, $args ) {
		return sprintf(
			'<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_html( $product->add_to_cart_text() )
		);
	}

	public function render() {
		if ( ! function_exists( 'WC' ) ) {
			$this->parent::show_wc_missing_alert();

			return;
		}

		// Add WC hooks
		$this->__add_hooks();

		$settings = $this->parent->get_settings_for_display();
		$products = (array) $this->parent->get_query();

		global $post;

		foreach ( $products as $post ) : setup_postdata( $post );

			global $product;

			// Ensure visibility.
			if ( empty( $product ) || ! $product->is_visible() ) {
				continue;
			}
			?>

			<article <?php wc_product_class( 'ha-product-grid__item', $product ); ?>>
				<div role="figure" class="ha-product-grid__img">
					<a href="<?php the_permalink(); ?>" rel="bookmark">
						<?php woocommerce_template_loop_product_thumbnail(); ?>
					</a>

					<?php if ( $settings['show_badge'] === 'yes' && $product->is_on_sale() ) : ?>
						<div class="ha-product-grid__badge"><?php woocommerce_show_product_loop_sale_flash(); ?></div>
					<?php endif; ?>
				</div>

				<?php if ( $settings['show_rating'] === 'yes' && $product->get_average_rating() ) : ?>
					<div class="ha-product-grid__rating"><?php woocommerce_template_loop_rating();  ?></div>
				<?php endif; ?>

				<h2 class="ha-product-grid__title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

				<?php if ( $settings['show_price'] === 'yes' ) : ?>
					<div class="ha-product-grid__price"><?php woocommerce_template_loop_price(); ?></div>
				<?php endif; ?>

				<?php if ( $settings['show_cart_button'] === 'yes' || $settings['show_quick_view_button'] === 'yes' ) : ?>
					<div class="ha-product-grid__btns">
						<?php
						if ( $settings['show_cart_button'] === 'yes' ) :
							woocommerce_template_loop_add_to_cart();
						endif;

						if ( $settings['show_quick_view_button'] === 'yes' ) :
							$this->print_quick_view_button( $product->get_id() );
						endif;
						?>
					</div>
				<?php endif; ?>

			</article>

			<?php
		endforeach;

		wp_reset_postdata();

		$this->parent->get_load_more_button();

		// Remove WC hooks
		$this->__remove_hooks();
	}

	protected function print_quick_view_button( $product_id ) {
		$url = add_query_arg(
			[
				'action'     => 'ha_show_product_quick_view',
				'product_id' => $product_id,
				'nonce'      => wp_create_nonce( 'ha_show_product_quick_view' ),
			],
			admin_url( 'admin-ajax.php' )
		);

		printf(
			'<a href="#" data-mfp-src="%s" class="ha-pqv-btn" data-modal-class="ha-pqv--%s">%s</a>',
			esc_url( $url ),
			$this->parent->get_id(),
			$this->parent->get_settings_for_display( 'quick_view_text' )
		);
	}
}
