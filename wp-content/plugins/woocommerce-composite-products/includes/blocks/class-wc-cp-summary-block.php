<?php
/**
 * Class for handling the Configuration Summary Block.
 *
 * @package  WooCommerce Composite Products
 * @since    8.10.4
 * @version 8.10.4
 */
class WC_CP_Configuration_Summary_Block {

	/**
	 * The single instance of the class.
	 *
	 * @var WC_CP_Configuration_Summary_Block
	 */
	protected static $_instance = null;

	/**
	 * Main WC_CP_Configuration_Summary_Block instance. Ensures only one instance of WC_CP_Configuration_Summary_Block is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_CP_Configuration_Summary_Block
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-composite-products' ), '8.10.4' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-composite-products' ), '8.10.4' );
	}

	/**
	 * Construct.
	 */
	public function __construct() {
		add_action('init', array( $this, 'init' ) );
	}

	/**
	 * Init.
	 */
	public function init() {
		$this->register_block();
	}

	/**
	 * Register the block.
	 */
	private function register_block() {
		register_block_type( 
			WC_CP_ABSPATH . 'assets/dist/frontend/blocks/summary', 
			array(
				'render_callback' => array( $this, 'render' ),
				'uses_context'    => array( 'query', 'postId' )
			)
		);
	}

	/**
	 * Render the block.
	 *
	 * @param array    $attributes Block attributes. Default empty array.
	 * @param string   $content    Block content. Default empty string.
	 * @param WP_Block $block      Block instance.
	 * 
	 * @return string Rendered block type output.
	 */
	public function render( $attributes, $content, $block ) {

		if ( ! empty( $content ) ) {
			return $content;
		}

		$post_id = $block->context['postId'];
		if ( ! $post_id ) {
			return '';
		}

		$product = wc_get_product( $post_id );

		if ( ! $product ) {
			return '';
		}

		ob_start();

		// Defaults and fill-ins for widget-related missing attributes.
		$defaults = array(
			'title'         => __( 'Your Selections', 'woocommerce-composite-products' ),
			'before_title'  => '<h4>',
			'after_title'   => '</h4>',
			'before_widget' => '<div class="widget_composite_summary">',
			'after_widget'  => '</div>',
			'max_columns'   => absint( $attributes['maxColumns'] ),
			'display'       => 'default'
		);

		$args = wp_parse_args( $attributes, $defaults );

		// Render widget.
		WC_Widget_Composite::render( $product, $args );
		$html = ob_get_clean();

		// Render block.
		return sprintf(
			'<div class="widget woocommerce wp-block-woocommerce-composite-products-summary composite_summary widget_composite_summary">
				%s
			</div>',
			$html
		);
	}
}

new WC_CP_Configuration_Summary_Block();
