<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Pricing Table Widget
 *
 * Porto Elementor widget to display a pricing table.
 *
 * @since 5.4.1
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Pricing_Table_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_price_box';
	}

	public function get_title() {
		return __( 'Porto Pricing Table', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'pricing table', 'price', 'box', 'price box' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_pricing_table',
			array(
				'label' => __( 'Pricing Table', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Title', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'desc',
			array(
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Description', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'is_popular',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Popular Price Box', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'popular_label',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Popular Label', 'porto-functionality' ),
				'condition' => array(
					'is_popular' => 'yes',
				),
			)
		);

		$this->add_control(
			'price',
			array(
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Price', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'price_unit',
			array(
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Price Unit', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'price_label',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Price Label', 'porto-functionality' ),
				'description' => 'For example, "Per Month"',
			)
		);

		$this->add_control(
			'content',
			array(
				'type'  => Controls_Manager::WYSIWYG,
				'label' => __( 'Content', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'show_btn',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Show Button', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'btn_label',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Button Label', 'porto-functionality' ),
				'condition' => array(
					'show_btn' => 'yes',
				),
			)
		);

		$this->add_control(
			'btn_action',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Button Action', 'porto-functionality' ),
				'options' => array_combine( array_values( porto_sh_commons( 'popup_action' ) ), array_keys( porto_sh_commons( 'popup_action' ) ) ),
				'default' => 'open_link',
			)
		);

		$this->add_control(
			'btn_link',
			array(
				'label'     => __( 'Link', 'porto-functionality' ),
				'type'      => Controls_Manager::URL,
				'condition' => array(
					'btn_action' => 'open_link',
				),
			)
		);

		$this->add_control(
			'popup_iframe',
			array(
				'label'     => __( 'Video or Map URL (Link)', 'porto-functionality' ),
				'type'      => Controls_Manager::URL,
				'condition' => array(
					'btn_action' => 'popup_iframe',
				),
			)
		);

		$this->add_control(
			'popup_block',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Popup Block', 'porto-functionality' ),
				'description' => __( 'Please add block slug name.', 'porto-functionality' ),
				'condition'   => array(
					'btn_action' => 'popup_block',
				),
			)
		);

		$this->add_control(
			'popup_size',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Popup Size', 'porto-functionality' ),
				'options'   => array(
					'md' => __( 'Medium', 'porto-functionality' ),
					'lg' => __( 'Large', 'porto-functionality' ),
					'sm' => __( 'Small', 'porto-functionality' ),
					'xs' => __( 'Extra Small', 'porto-functionality' ),
				),
				'default'   => 'md',
				'condition' => array(
					'btn_action' => 'popup_block',
				),
			)
		);

		$this->add_control(
			'popup_animation',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Popup Animation', 'porto-functionality' ),
				'options'   => array(
					'mfp-fade'            => __( 'Fade', 'porto-functionality' ),
					'mfp-with-zoom'       => __( 'Zoom', 'porto-functionality' ),
					'my-mfp-zoom-in'      => __( 'Fade Zoom', 'porto-functionality' ),
					'my-mfp-slide-bottom' => __( 'Fade Slide', 'porto-functionality' ),
				),
				'default'   => 'mfp-fade',
				'condition' => array(
					'btn_action' => 'popup_block',
				),
			)
		);

		$this->add_control(
			'btn_style',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Button Style', 'porto-functionality' ),
				'options'   => array(
					''        => __( 'Default', 'porto-functionality' ),
					'borders' => __( 'Outline', 'porto-functionality' ),
				),
				'condition' => array(
					'show_btn' => 'yes',
				),
			)
		);

		$this->add_control(
			'btn_size',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Button Size', 'porto-functionality' ),
				'options'   => array_combine( array_values( porto_sh_commons( 'size' ) ), array_keys( porto_sh_commons( 'size' ) ) ),
				'condition' => array(
					'show_btn' => 'yes',
				),
			)
		);

		$this->add_control(
			'btn_pos',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Button Position', 'porto-functionality' ),
				'options'   => array(
					''       => __( 'Top', 'porto-functionality' ),
					'bottom' => __( 'Bottom', 'porto-functionality' ),
				),
				'condition' => array(
					'show_btn' => 'yes',
				),
			)
		);

		$this->add_control(
			'btn_skin',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Button Skin Color', 'porto-functionality' ),
				'options'   => array_combine( array_values( porto_sh_commons( 'colors' ) ), array_keys( porto_sh_commons( 'colors' ) ) ),
				'default'   => 'custom',
				'condition' => array(
					'show_btn' => 'yes',
				),
			)
		);

		$this->add_control(
			'style',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Style', 'porto-functionality' ),
				'options' => array_combine( array_values( porto_sh_commons( 'price_boxes_style' ) ), array_keys( porto_sh_commons( 'price_boxes_style' ) ) ),
			)
		);

		$this->add_control(
			'skin',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Skin Color', 'porto-functionality' ),
				'options' => array_combine( array_values( porto_sh_commons( 'colors' ) ), array_keys( porto_sh_commons( 'colors' ) ) ),
				'default' => 'custom',
			)
		);

		$this->add_control(
			'size',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Size', 'porto-functionality' ),
				'options' => array_combine( array_values( porto_sh_commons( 'price_boxes_size' ) ), array_keys( porto_sh_commons( 'price_boxes_size' ) ) ),
			)
		);

		$this->add_control(
			'border',
			array(
				'type'    => Controls_Manager::SWITCHER,
				'label'   => __( 'Show Border', 'porto-functionality' ),
				'default' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_price_box' ) ) {
			$this->add_inline_editing_attributes( 'title' );
			$this->add_inline_editing_attributes( 'desc' );
			$this->add_render_attribute( 'desc', 'class', 'desc' );
			$title_attrs_escaped = ' ' . $this->get_render_attribute_string( 'title' );
			$desc_attrs_escaped  = ' ' . $this->get_render_attribute_string( 'desc' );

			$classes = 'pricing-table';
			if ( ! isset( $atts['border'] ) || ! $atts['border'] ) {
				$classes .= ' no-borders';
			}

			if ( isset( $atts['size'] ) && 'sm' === $atts['size'] ) {
				$classes .= ' pricing-table-sm';
			}

			if ( ! empty( $atts['style'] ) ) {
				$classes .= ' pricing-table-' . $atts['style'];
			}
			echo '<div class="' . esc_attr( $classes ) . '">';

			if ( isset( $atts['content'] ) ) {
				$content = $atts['content'];
			}
			include $template;
			echo '</div>';
		}
	}

	protected function content_template() {
		?>
		<#
			view.addRenderAttribute( 'wrapper', 'class', 'pricing-table' );
			if ( ! settings.border ) {
				view.addRenderAttribute( 'wrapper', 'class', 'no-borders' );
			}
			if ( settings.size && 'sm' === settings.size ) {
				view.addRenderAttribute( 'wrapper', 'class', 'pricing-table-sm' );
			}
			if ( settings.style ) {
				view.addRenderAttribute( 'wrapper', 'class', 'pricing-table-' + settings.style );
			}

			view.addRenderAttribute( 'inner_wrapper', 'class', 'porto-price-box plan' );
			if ( settings.is_popular ) {
				view.addRenderAttribute( 'inner_wrapper', 'class', 'most-popular' );
			}
			if ( settings.skin ) {
				view.addRenderAttribute( 'inner_wrapper', 'class', 'plan-' + settings.skin );
			}

			let btn_class = 'btn btn-modern';
			if ( settings.btn_style ) {
				btn_class += ' btn-' + settings.btn_style;
			}
			let btn_html = '';
			if ( settings.btn_size ) {
				btn_class += ' btn-' + settings.btn_size;
			}
			if ( 'custom' !== settings.btn_skin ) {
				btn_class += ' btn-' + settings.btn_skin;
			} else {
				btn_class += ' btn-default';
			}
			if ( 'bottom' !== settings.btn_pos ) {
				btn_class += ' btn-top';
			} else {
				btn_class += ' btn-bottom';
			}
			view.addRenderAttribute( 'btn', 'class', btn_class );

			if ( 'open_link' === settings.btn_action ) {
				if ( settings.btn_link ) {
					view.addRenderAttribute( 'btn', 'href', settings.btn_link );
				}
				if ( settings.btn_link ) {
					btn_html += '<a ' + view.getRenderAttributeString( 'btn' ) + '>' + settings.btn_label + '</a>';
				} else {
					btn_html += '<span ' + view.getRenderAttributeString( 'btn' ) + '>' + settings.btn_label + '</span>';
				}
			} else if ( 'popup_iframe' === settings.btn_action && settings.popup_iframe ) {
				view.addRenderAttribute( 'btn', 'class', 'porto-popup-iframe' );
				view.addRenderAttribute( 'btn', 'href', settings.popup_iframe );
				btn_html += '<a ' + view.getRenderAttributeString( 'btn' ) + '>' + settings.btn_label + '</a>';
			} else if ( 'popup_block' === settings.btn_action && settings.popup_block ) {
				let uid = 'popup' + Math.floor(Math.random()*999999);
				view.addRenderAttribute( 'btn', 'class', 'porto-popup-content' );
				view.addRenderAttribute( 'btn', 'href', '#' + uid );
				view.addRenderAttribute( 'btn', 'data-animation', settings.popup_animation );
				btn_html += '<a ' + view.getRenderAttributeString( 'btn' ) + '>' + settings.btn_label + '</a>';
				btn_html += '<div id="' + uid + '" class="dialog dialog-' + settings.popup_size + ' zoom-anim-dialog mfp-hide">[porto_block name="' + settings.popup_block + '"]</div>';
			}

			if ( btn_html ) {
				if ( 'bottom' === settings.btn_pos ) {
					view.addRenderAttribute( 'inner_wrapper', 'class', 'plan-btn-bottom' );
				} else {
					view.addRenderAttribute( 'inner_wrapper', 'class', 'plan-btn-top' );
				}
			}

			view.addInlineEditingAttributes( 'title' );
			view.addInlineEditingAttributes( 'desc' );
			view.addRenderAttribute( 'desc', 'class', 'desc' );
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div {{{ view.getRenderAttributeString( 'inner_wrapper' ) }}}>
			<# if ( settings.is_popular && settings.popular_label ) { #>
				<div class="plan-ribbon-wrapper"><div class="plan-ribbon">{{ settings.popular_label }}</div></div>
			<# } #>
			<# if ( settings.title || settings.price || settings.desc ) { #>
				<h3>
				<# if ( settings.title ) { #>
					<strong {{{ view.getRenderAttributeString( 'title' ) }}}>{{ settings.title }}</strong>
				<# } #>
				<# if ( settings.desc ) { #>
					<em {{{ view.getRenderAttributeString( 'desc' ) }}}>{{ settings.desc }}</em>
				<# } #>
				<# if ( settings.price ) { #>
					<span class="plan-price"><span class="price">
					<# if ( settings.price_unit ) { #>
						<span class="price-unit">{{ settings.price_unit }}</span>
					<# } #>
					{{ settings.price }}
					</span>
					<# if ( settings.price_label ) { #>
						<label class="price-label">{{ settings.price_label }}</label>
					<# } #>
					</span>
				<# } #>
				</h3>
			<# } #>
			<#
				if ( settings.show_btn && 'bottom' !== settings.btn_pos ) {
					print( btn_html );
				}
				print( settings.content );
				if ( settings.show_btn && 'bottom' === settings.btn_pos ) {
					print( btn_html );
				}
			#>
			</div>
		</div>
		<?php
	}
}
