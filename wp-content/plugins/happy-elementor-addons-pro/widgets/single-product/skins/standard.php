<?php
namespace Happy_Addons_Pro\Widget\Skins\Single_Product;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Standard extends Skin_Base {

	/**
	 * Get widget ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'standard';
	}

	/**
	 * Get widget title
	 *
	 * @return string widget title
	 */
	public function get_title() {
		return __( 'Standard', 'happy-addons-pro' );
	}

	/**
	 * Settings content Control
	 */
	protected function settings_content_controls() {

		parent::settings_content_controls();

		$this->update_control(
			'badge_text',
			[
				'default' => '50% off'
			]
		);

		$this->remove_control('discount_text');
	}

	protected function badge_discount_style_controls_section( $widget ) {

		$this->start_controls_section(
			'_section_style_badge',
			[
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'badge_text!' ) => '',
				],
			]
		);

		$this->badge__offset( $widget );

		$this->badge_style_controls();

		$this->remove_control('_heading_badge_style');

		$this->end_controls_section();
	}

	protected function cart_button_style_controls() {

		$this->add_responsive_control(
			'cart_btn_spacing',
			[
				'label' => __( 'Space Between', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .button' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .added_to_cart' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		parent::cart_button_style_controls();
	}

	/**
	 * adding woocommerce filter
	 *
	 * @return void
	 */
	public function __add_hooks() {
		parent::__add_hooks();

		add_filter( 'woocommerce_loop_add_to_cart_link', [ $this, '__update_add_to_cart' ], 10, 3 );
	}

	/**
	 * removing woocommerce filter
	 *
	 * @return void
	 */
	public function __remove_hooks() {
		parent::__remove_hooks();

		remove_filter( 'woocommerce_loop_add_to_cart_link', [ $this, '__update_add_to_cart' ], 10, 3 );
	}

	/**
	 * update add to cart button markup
	 *
	 * @param [string] $html
	 * @param [object] $product
	 * @param [array] $args
	 * @return void
	 */
	public function __update_add_to_cart( $html, $product, $args ) {
		return sprintf(
			'<a href="%s" data-quantity="%s" class="%s" title="%s" %s><i class="fas fa-shopping-cart"></i><span class="ha-screen-reader-text">%s</span></a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
			esc_html( $product->add_to_cart_text() ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_html( $product->add_to_cart_text() )
		);
	}

	/**
	 * render content
	 *
	 * @return void
	 */
    public function render() {

		// Show Alart
		// $this->parent->__alert_();
		if ( ! function_exists( 'WC' ) ) {
			$this->parent::show_wc_missing_alert();
			return;
		}elseif ( empty( $this->parent->get_query() ) ) {
			$this->parent::show_alert_to_add_product();
			return;
		}

		// Add WC hooks
		$this->__add_hooks();

		$products = (array) $this->parent->get_query();


		$this->parent->add_render_attribute(
			'wrapper',
			'class',
			[
				'ha-single-product__item',
				'ha-single-product__'. $this->get_id(),
			]
		);

		global $post;

		foreach ( $products as $post ) : setup_postdata( $post );

			global $product;
		?>
			<article <?php $this->parent->print_render_attribute_string( 'wrapper' ); ?>>

                <div class="ha-single-product__img">

					<?php $this->get_feature_image();?>

					<?php $this->get_badge();?>

					<?php $this->get_hover_button( $product );?>

                </div>

                <div class="ha-single-product__content">

					<?php if ( $this->get_instance_value( 'show_rating' ) === 'yes' && $product->get_average_rating() ) : ?>
						<div class="ha-single-product__ratings">
							<?php woocommerce_template_loop_rating();  ?>
						</div>
					<?php endif; ?>

					<?php if ( $this->get_instance_value( 'show_cat' ) === 'yes' ) : ?>
						<div class="ha-single-product__category">
							<?php echo ha_pro_the_first_taxonomy( $post->ID, 'product_cat', ['class'=>'ha-single-product__category_inner'] ); ?>
						</div>
					<?php endif; ?>

					<<?php echo ha_escape_tags( $this->get_instance_value( 'title_tag' ), 'h2' ).' class="ha-single-product__title"';?>>
						<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
					</<?php echo ha_escape_tags( $this->get_instance_value( 'title_tag' ), 'h2' );?>>

					<?php if ( !empty( $this->get_instance_value( 'excerpt_length' ) ) ) : ?>
						<p class="ha-single-product__desc">
							<?php echo ha_pro_get_excerpt( $post->ID, $this->get_instance_value( 'excerpt_length' ) ); ?>
						</p>
					<?php endif; ?>

					<?php if ( $this->get_instance_value( 'show_price' ) === 'yes' ) : ?>
						<div class="ha-single-product__price">
								<?php woocommerce_template_loop_price();?>
						</div>
					<?php endif; ?>
                </div>
            </article>
		<?php
		endforeach;

		wp_reset_postdata();

		// Remove WC hooks
		$this->__remove_hooks();
	}

	/**
	 * get hover button markup
	 *
	 * @param [object] $product
	 * @return void
	 */
    protected function get_hover_button( $product ) {
		$show_cart_button = $this->get_instance_value( 'show_cart_button' );
		$show_quick_view_button = $this->get_instance_value( 'show_quick_view_button' );
        ?>
			<?php if ( $show_cart_button === 'yes' || $show_quick_view_button === 'yes' ) : ?>
				<div class="ha-single-product__btns">
					<?php
						if ( $show_cart_button === 'yes' ){
							woocommerce_template_loop_add_to_cart();
						}
						if ( $show_quick_view_button === 'yes' ){
							$this->print_quick_view_button( $product->get_id() );
						}
					?>
				</div>
			<?php endif; ?>
		<?php
    }

	/**
	 * get badge markup
	 *
	 * @return void
	 */
    protected function get_badge() {
		$badge_text = $this->get_instance_value( 'badge_text' );
        ?>
			<?php if ( $badge_text ) : ?>
				<div class="ha-single-product__badge">
					<?php
						printf( '<span %1$s>%2$s</span>',
							'class="ha-single-product__badge-text"',
							esc_html( $badge_text )
						);
					?>
				</div>
			<?php endif; ?>
		<?php
    }

}
