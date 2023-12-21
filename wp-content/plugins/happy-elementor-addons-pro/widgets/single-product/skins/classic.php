<?php
namespace Happy_Addons_Pro\Widget\Skins\Single_Product;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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


	protected function cart_and_qv_button_style_controls_section() {

		$this->start_controls_section(
			'_section__qv_style_buttons',
			[
				'label' => __( 'Quick View', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		$this->qv_button_style_controls();

		$this->add_control(
			'qv_position',
			[
				'label' => __( 'Position', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'top-right',
				'options' => [
					'top-left' => __( 'Top Left', 'happy-addons-pro' ),
					'top-right' => __( 'Top Right', 'happy-addons-pro' ),
					'bottom-left' => __( 'Bottom Left', 'happy-addons-pro' ),
					'bottom-right' => __( 'Bottom Right', 'happy-addons-pro' ),
				],
				'prefix_class' => 'ha-single-product__qv_pos-',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'_section__cart_style_buttons',
			[
				'label' => __( 'Add To Cart', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
				],
			]
		);

		$this->cart_button_style_controls();

		$this->end_controls_section();

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
			'<a href="%s" data-quantity="%s" class="%s" title="%s" %s><i class="fa fa-shopping-cart"></i>%s</a>',
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

                    <?php if ( $this->get_instance_value( 'show_price' ) === 'yes' || $this->get_instance_value( 'show_cart_button' ) === 'yes' ) : ?>
						<div class="ha-single-product__price">
							<?php if ( $this->get_instance_value( 'show_price' ) === 'yes' ) : ?>
								<?php woocommerce_template_loop_price();?>
							<?php endif; ?>

							<?php if ( $this->get_instance_value( 'show_cart_button' ) === 'yes' ) : ?>
								<div class="ha-single-product__atc-btn">
									<?php woocommerce_template_loop_add_to_cart();?>
								</div>
							<?php endif; ?>
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
		$show_quick_view_button = $this->get_instance_value( 'show_quick_view_button' );
        ?>
			<?php if ( $show_quick_view_button === 'yes' ) : ?>
				<div class="ha-single-product__btns">
					<?php $this->print_quick_view_button( $product->get_id() ); ?>
				</div>
			<?php endif; ?>
		<?php
    }

}
