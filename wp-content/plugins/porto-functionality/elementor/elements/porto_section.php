<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Porto Carousel Element
 *
 * @since 5.2.2
 */
use Elementor\Controls_Manager;

class Porto_Elementor_Section extends Elementor\Element_Section {

	public function before_render() {
		$settings = $this->get_settings_for_display();

		$items        = 0 < intval( $settings['items']['size'] ) ? $settings['items']['size'] : 1;
		$items_tablet = 0 < intval( $settings['items_tablet']['size'] ) ? $settings['items_tablet']['size'] : 1;
		$items_mobile = 0 < intval( $settings['items_mobile']['size'] ) ? $settings['items_mobile']['size'] : 1;

		$extra_class    = '';
		$extra_options  = '';
		$extra_cont_cls = '';

		if ( 'carousel' == $settings['as_param'] ) {
			$settings['gap'] = 'no';
			$extra_class     = array( 'porto-carousel', 'owl-carousel', 'has-ccols' );
			if ( 'yes' == $settings['show_nav'] ) {
				if ( $settings['nav_pos'] ) {
					$extra_class[] = esc_attr( $settings['nav_pos'] );
				}
				if ( $settings['nav_type'] ) {
					$extra_class[] = esc_attr( $settings['nav_type'] );
				}
				if ( 'yes' == $settings['show_nav_hover'] ) {
					$extra_class[] = 'show-nav-hover';
				}
			}

			if ( 'yes' == $settings['show_dots'] ) {
				if ( $settings['dots_style'] ) {
					$extra_class[] = esc_attr( $settings['dots_style'] );
				}
				if ( $settings['dots_pos'] ) {
					$extra_class[] = esc_attr( $settings['dots_pos'] . ' ' . $settings['dots_align'] );
				}
			}

			if ( (int) $items > 1 ) {
				$extra_class[] = 'ccols-xl-' . intval( $items );
			}
			if ( (int) $items_tablet > 1 ) {
				$extra_class[] = 'ccols-md-' . intval( $items_tablet );
			}
			if ( (int) $items_mobile > 1 ) {
				$extra_class[] = 'ccols-' . intval( $items_mobile );
			} else {
				$extra_class[] = 'ccols-1';
			}

			$extra_options                = array();
			$extra_options['margin']      = '' !== $settings['item_margin'] ? (int) $settings['item_margin'] : 0;
			$extra_options['items']       = $items;
			$extra_options['nav']         = 'yes' == $settings['show_nav'];
			$extra_options['dots']        = 'yes' == $settings['show_dots'];
			$extra_options['themeConfig'] = true;

			$breakpoints = Elementor\Core\Responsive\Responsive::get_breakpoints();
			if ( 1 !== intval( $items ) ) {
				$extra_options['responsive'] = array( $breakpoints['xs'] => (int) $items_mobile );

				$items_sm = $items_tablet - 1 >= $items_mobile ? $items_tablet - 1 : $items_mobile;
				if ( (int) $items_sm !== (int) $items_mobile ) {
					$extra_options['responsive'][ $breakpoints['sm'] ] = (int) $items_sm;
				}
				if ( (int) $items_tablet !== (int) $items_sm ) {
					$extra_options['responsive'][ $breakpoints['md'] ] = (int) $items_tablet;
				}
				if ( (int) $items !== (int) $items_tablet ) {
					if ( (int) $items > (int) $items_tablet + 1 ) {
						$extra_options['responsive'][ $breakpoints['lg'] ] = (int) $items - 1;
						$extra_options['responsive'][ $breakpoints['xl'] ] = (int) $items;
					} else {
						$extra_options['responsive'][ $breakpoints['lg'] ] = (int) $items;
					}
				}
			}
			if ( 'yes' == $settings['autoplay'] ) {
				$extra_options['autoplay']           = true;
				$extra_options['autoplayTimeout']    = (int) $settings['autoplay_timeout'];
				$extra_options['autoplayHoverPause'] = true;
			} else {
				$extra_options['autoplay'] = false;
			}
			if ( isset( $settings['fullscreen'] ) && 'yes' == $settings['fullscreen'] ) {
				$extra_class[]               = 'fullscreen-carousel';
				$extra_options['fullscreen'] = true;
			}
			if ( isset( $settings['center'] ) && 'yes' == $settings['center'] ) {
				$extra_options['center'] = true;
			}
			if ( ! empty( $settings['stage_padding'] ) ) {
				$extra_options['stagePadding'] = (int) $settings['stage_padding'];
			}

			$extra_class   = ' ' . implode( ' ', $extra_class );
			$extra_options = ' data-plugin-options="' . esc_attr( json_encode( $extra_options ) ) . '"';

		} elseif ( 'banner' == $settings['as_param'] ) {
			$settings['gap'] = 'no';
			$extra_cont_cls .= ' porto-ibanner';
		} elseif ( 'creative' == $settings['as_param'] ) {
			wp_enqueue_script( 'isotope' );
			$extra_options                    = array();
			$extra_options['layoutMode']      = 'masonry';
			$extra_options['masonry']         = array( 'columnWidth' => '.grid-col-sizer' );
			$extra_options['itemSelector']    = '.porto-grid-item';
			$extra_options['animationEngine'] = 'best-available';
			$extra_options['resizable']       = false;

			$extra_options = ' data-plugin-masonry data-plugin-options="' . esc_attr( json_encode( $extra_options ) ) . '"';

			if ( 'yes' == $settings['use_preset'] ) {
				$settings['gap'] = 'no';
			}
		}

		if ( isset( $settings['porto_el_cls'] ) && $settings['porto_el_cls'] ) {
			$extra_class .= ' ' . trim( $settings['porto_el_cls'] );
		}

		$before_html = '';

		?>
		<<?php echo esc_html( $this->get_html_tag() ); ?> <?php $this->print_render_attribute_string( '_wrapper' ); ?>>
			<?php
			if ( 'video' === $settings['background_background'] ) :
				if ( $settings['background_video_link'] ) :
					$video_properties = Elementor\Embed::get_video_properties( $settings['background_video_link'] );

					$this->add_render_attribute( 'background-video-container', 'class', 'elementor-background-video-container' );

					if ( ! $settings['background_play_on_mobile'] ) {
						$this->add_render_attribute( 'background-video-container', 'class', 'elementor-hidden-phone' );
					}
					?>
					<div <?php echo $this->get_render_attribute_string( 'background-video-container' ); ?>>
						<?php if ( $video_properties ) : ?>
							<div class="elementor-background-video-embed"></div>
							<?php
						else :
							$video_tag_attributes = 'autoplay muted playsinline';
							if ( 'yes' !== $settings['background_play_once'] ) :
								$video_tag_attributes .= ' loop';
							endif;
							?>
							<video class="elementor-background-video-hosted elementor-html5-video" <?php echo $video_tag_attributes; ?>></video>
						<?php endif; ?>
					</div>
					<?php
				endif;
			endif;

			$has_background_overlay = in_array( $settings['background_overlay_background'], [ 'classic', 'gradient' ], true ) ||
									in_array( $settings['background_overlay_hover_background'], [ 'classic', 'gradient' ], true );

			if ( $has_background_overlay ) :
				?>
				<div class="elementor-background-overlay"></div>
				<?php
			endif;

			if ( $settings['shape_divider_top'] ) {
				$this->print_shape_divider( 'top' );
			}

			if ( $settings['shape_divider_bottom'] ) {
				$this->print_shape_divider( 'bottom' );
			}
			?>

			<?php
			$legacy_enabled = ! method_exists( \Elementor\Plugin::instance(), 'get_legacy_mode' ) || \Elementor\Plugin::instance()->get_legacy_mode( 'elementWrappers' );
			if ( 'banner' == $settings['as_param'] ) {
				if ( ! empty( $settings['banner_image'] ) && ! empty( $settings['banner_image']['id'] ) ) {
					$attr    = array( 'class' => 'porto-ibanner-img' );
					$img_src = wp_get_attachment_image_src( $settings['banner_image']['id'], ! empty( $settings['banner_image_size'] ) ? $settings['banner_image_size'] : 'full' );
					// Generate 'srcset' and 'sizes'
					$image_meta          = wp_get_attachment_metadata( $settings['banner_image']['id'] );
					$settings_min_height = false;
					if ( ! empty( $settings['min_height']['size'] ) && 'px' == $settings['min_height']['unit'] ) {
						if ( ! $settings_min_height ) {
							$settings_min_height = (int) $settings['min_height']['size'];
						}
					}
					if ( ! empty( $settings['min_height_tablet']['size'] ) && 'px' == $settings['min_height_tablet']['unit'] ) {
						if ( ! $settings_min_height || (int) $settings['min_height_tablet']['size'] < $settings_min_height ) {
							$settings_min_height = (int) $settings['min_height_tablet']['size'];
						}
					}
					if ( ! empty( $settings['min_height_mobile']['size'] ) && 'px' == $settings['min_height_mobile']['unit'] ) {
						if ( ! $settings_min_height || (int) $settings['min_height_mobile']['size'] < $settings_min_height ) {
							$settings_min_height = (int) $settings['min_height_mobile']['size'];
						}
					}
					if ( $settings_min_height && is_array( $image_meta ) && is_array( $image_meta['sizes'] ) ) {
						$ratio = $image_meta['height'] / $image_meta['width'];
						foreach ( $image_meta['sizes'] as $key => $size ) {
							if ( $size['width'] * (float) $ratio < $settings_min_height ) {
								unset( $image_meta['sizes'][ $key ] );
							}
						}
					}
					$srcset = wp_get_attachment_image_srcset( $settings['banner_image']['id'], ! empty( $settings['banner_image_size'] ) ? $settings['banner_image_size'] : 'full', $image_meta );
					$sizes  = wp_get_attachment_image_sizes( $settings['banner_image']['id'], ! empty( $settings['banner_image_size'] ) ? $settings['banner_image_size'] : 'full', $image_meta );
					if ( $srcset && $sizes ) {
						$attr['srcset'] = $srcset;
						$attr['sizes']  = $sizes;
					}

					if ( is_array( $img_src ) ) {
						$attr_str_escaped = '';
						foreach ( $attr as $key => $val ) {
							$attr_str_escaped .= ' ' . esc_html( $key ) . '="' . esc_attr( $val ) . '"';
						}
						$before_html .= '<img src="' . esc_url( $img_src[0] ) . '" alt="' . esc_attr( trim( get_post_meta( $settings['banner_image']['id'], '_wp_attachment_image_alt', true ) ) ) . '" width="' . esc_attr( $img_src[1] ) . '" height="' . esc_attr( $img_src[2] ) . '"' . $attr_str_escaped . '>';
					}
				}
			} elseif ( 'creative' == $settings['as_param'] ) {
				if ( 'yes' == $settings['use_preset'] ) {
					$extra_class .= ' grid-creative porto-preset-layout';

					$grid_layout        = porto_creative_grid_layout( empty( $settings['grid_layout'] ) ? '1' : $settings['grid_layout'] );
					$grid_height_number = trim( preg_replace( '/[^0-9]/', '', $settings['grid_height'] ) );
					$unit               = trim( str_replace( $grid_height_number, '', $settings['grid_height'] ) );

					echo '<style scope="scope" data-id="' . esc_attr( $this->get_id() ) . '">';
					global $porto_settings;
					if ( 'boxed' == $settings['layout'] && ! $legacy_enabled && ! empty( $porto_settings ) ) {
						echo '.elementor-element.elementor-element-' . $this->get_id() . ' .grid-creative { max-width: ' . ( (int) $porto_settings['container-width'] - (int) $porto_settings['grid-gutter-width'] + (int) $settings['spacing']['size'] ) . 'px }';
						echo '@media (min-width: 992px) and (max-width: ' . ( (int) $porto_settings['container-width'] + $porto_settings['grid-gutter-width'] - 1 ) . 'px) {';
						echo '.elementor-element.elementor-element-' . $this->get_id() . ' .grid-creative { max-width: ' . ( 960 - (int) $porto_settings['grid-gutter-width'] + (int) $settings['spacing']['size'] ) . 'px }';
						echo '}';
					}
					porto_creative_grid_style( $grid_layout, $grid_height_number, '.elementor-element.elementor-element-' . $this->get_id(), '' === $settings['spacing']['size'] ? false : intval( $settings['spacing']['size'] ), false, $unit, '.porto-grid-item' );
					echo '</style>';
				} else {
					global $porto_settings;
					if ( 'boxed' == $settings['layout'] && ! $legacy_enabled && ! empty( $porto_settings ) && ( 0 === $settings['spacing1']['size'] || $settings['spacing1']['size'] ) ) {
						echo '<style scope="scope" data-id="' . esc_attr( $this->get_id() ) . '">';
						echo '@media (min-width: 992px) and (max-width: ' . ( (int) $porto_settings['container-width'] + $porto_settings['grid-gutter-width'] - 1 ) . 'px) {';
						echo '.elementor-element.elementor-element-' . $this->get_id() . ' > .elementor-container { max-width: ' . ( 960 - (int) $porto_settings['grid-gutter-width'] + (int) $settings['spacing1']['size'] ) . 'px }';
						echo '}';
						echo '</style>';
					}
				}
			}
			?>
			<?php if ( $legacy_enabled ) : ?>
			<div class="elementor-container elementor-column-gap-<?php echo esc_attr( $settings['gap'] ), esc_attr( $extra_cont_cls ); ?>">
				<?php echo porto_filter_output( $before_html ); ?>
				<div class="elementor-row<?php echo esc_attr( $extra_class ); ?>"<?php echo porto_filter_output( $extra_options ); ?>>
			<?php else : ?>
			<div class="elementor-container elementor-column-gap-<?php echo esc_attr( $settings['gap'] ), esc_attr( $extra_class ), esc_attr( $extra_cont_cls ); ?>"<?php echo porto_filter_output( $extra_options ); ?>>
				<?php echo porto_filter_output( $before_html ); ?>
			<?php endif; ?>
		<?php
	}

	public function after_render() {
		$settings = $this->get_settings_for_display();
		if ( 'banner' == $settings['as_param'] && ! empty( $settings['banner_link']['url'] ) ) {
			echo '<a class="porto-ibanner-link" href="' . esc_url( $settings['banner_link']['url'] ) . '"' . ( $settings['banner_link']['is_external'] ? ' target="_blank"' : '' ) . '></a>';
		} elseif ( 'creative' == $settings['as_param'] ) {
			$grid_sizer = '';
			global $porto_grid_layout;
			if ( ! empty( $porto_grid_layout ) && is_array( $porto_grid_layout ) ) {
				$fractions    = array();
				$denominators = array();
				$numerators   = array();
				$unit         = '%';
				foreach ( $porto_grid_layout as $item ) {
					$w = (float) $item['size'];
					if ( ! $w ) {
						continue;
					}
					if ( (float) ( (int) $w ) === $w ) { // integer
						$arr = array( $w, 1 );
					} else {
						for ( $index = 2; $index <= 100; $index++ ) {
							$r_w = round( $w * $index, 1 );
							if ( (float) ( (int) $r_w ) === $r_w ) { //integer
								$gcd = porto_gcd( $r_w, $index );
								$arr = array( $r_w / $gcd, $index / $gcd );
							}
						}

						if ( ! isset( $arr ) ) {
							$w   = floor( $w * 10 );
							$gcd = porto_gcd( $w, 10 );
							$arr = array( $w / $gcd, 10 / $gcd );
						}
					}
					if ( isset( $arr ) && ! in_array( $arr, $fractions ) ) {
						$fractions[]    = $arr;
						$numerators[]   = $arr[0];
						$denominators[] = $arr[1];
					}
				}
				if ( count( $fractions ) >= 1 ) {
					$deno_lcm = porto_lcm( $denominators );
					$num_gcd  = porto_gcd( $numerators );
					$unit_num = round( $num_gcd / $deno_lcm, 4 );
					if ( $unit_num >= 0.1 ) {
						$unit_num  .= esc_attr( $unit );
						$grid_sizer = ' style="width:' . $unit_num . '; flex: 0 0 ' . $unit_num . '"';
					}
				}
			}
			echo '<div class="grid-col-sizer"' . $grid_sizer . '></div>';
		}
		?>
				</div>
		<?php
		if ( 'creative' == $settings['as_param'] ) {
			unset( $GLOBALS['porto_grid_layout'], $GLOBALS['porto_item_count'], $GLOBALS['porto_grid_type'] );
		} elseif ( 'banner' == $settings['as_param'] && 'yes' == $settings['add_container'] ) {
			unset( $GLOBALS['porto_banner_add_container'] );
		}
		$legacy_enabled = ! method_exists( \Elementor\Plugin::instance(), 'get_legacy_mode' ) || \Elementor\Plugin::instance()->get_legacy_mode( 'elementWrappers' );
		if ( $legacy_enabled ) :
			?>
			</div>
		<?php endif; ?>
		</<?php echo esc_html( $this->get_html_tag() ); ?>>
		<?php
	}

	private function get_html_tag() {
		$html_tag = $this->get_settings( 'html_tag' );

		if ( empty( $html_tag ) ) {
			$html_tag = 'section';
		}

		return $html_tag;
	}

	private function print_shape_divider( $side ) {
		$settings         = $this->get_active_settings();
		$base_setting_key = "shape_divider_$side";
		$negative         = ! empty( $settings[ $base_setting_key . '_negative' ] );
		?>
		<div class="elementor-shape elementor-shape-<?php echo esc_attr( $side ); ?>" data-negative="<?php echo var_export( $negative ); ?>">
			<?php include Elementor\Shapes::get_shape_path( $settings[ $base_setting_key ], ! empty( $settings[ $base_setting_key . '_negative' ] ) ); ?>
		</div>
		<?php
	}
}

add_action( 'elementor/element/section/section_layout/after_section_end', 'porto_elementor_section_custom_control', 10, 2 );
add_action( 'elementor/element/column/layout/after_section_end', 'porto_elementor_column_custom_control', 10, 2 );
add_filter( 'elementor/section/print_template', 'porto_elementor_print_section_template', 10, 2 );
add_filter( 'elementor/column/print_template', 'porto_elementor_print_column_template', 10, 2 );
add_action( 'elementor/frontend/section/before_render', 'porto_elementor_section_add_custom_attrs', 10, 1 );
add_action( 'elementor/frontend/column/before_render', 'porto_elementor_column_add_custom_attrs', 10, 1 );

add_action( 'elementor/element/section/section_background/before_section_end', 'porto_elementor_element_add_parallax', 10, 2 );
add_action( 'elementor/element/column/section_style/before_section_end', 'porto_elementor_element_add_parallax', 10, 2 );

function porto_elementor_section_custom_control( $self, $args ) {
	$carousel_nav_types = porto_sh_commons( 'carousel_nav_types' );
	$carousel_nav_types = array_combine( array_values( $carousel_nav_types ), array_keys( $carousel_nav_types ) );
	$self->start_controls_section(
		'section_section_additional',
		array(
			'label' => __( 'Porto Additional Settings', 'porto-functionality' ),
			'tab'   => Controls_Manager::TAB_LAYOUT,
		)
	);

	$self->add_control(
		'as_param',
		array(
			'type'    => Controls_Manager::SELECT,
			'label'   => __( 'Use as', 'porto-functionality' ),
			'options' => array(
				''         => __( 'Default', 'porto-functionality' ),
				'carousel' => __( 'Carousel', 'porto-functionality' ),
				'banner'   => __( 'Banner', 'porto-functionality' ),
				'creative' => __( 'Creative Grid', 'porto-functionality' ),
			),
		)
	);

	$self->add_control(
		'stage_padding',
		array(
			'label'     => __( 'Stage Padding (px)', 'porto-functionality' ),
			'type'      => Controls_Manager::NUMBER,
			'min'       => 0,
			'max'       => 100,
			'step'      => 1,
			'condition' => array(
				'as_param' => 'carousel',
			),
		)
	);

	$self->add_responsive_control(
		'items',
		array(
			'label'     => __( 'Items', 'porto-functionality' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => array(
				'px' => array(
					'step' => 1,
					'min'  => 1,
					'max'  => 6,
				),
			),
			'condition' => array(
				'as_param' => 'carousel',
			),
		)
	);

	$self->add_control(
		'item_margin',
		array(
			'label'       => __( 'Item Margin(px)', 'porto-functionality' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0,
			'min'         => '0',
			'max'         => '100',
			'step'        => '1',
			'placeholder' => '0',
			'condition'   => array(
				'as_param' => 'carousel',
			),
		)
	);

	$self->add_control(
		'show_nav',
		array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => __( 'Show Nav', 'porto-functionality' ),
			'condition' => array(
				'as_param' => 'carousel',
			),
		)
	);

	$self->add_control(
		'show_nav_hover',
		array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => __( 'Show Nav on Hover', 'porto-functionality' ),
			'condition' => array(
				'as_param' => 'carousel',
				'show_nav' => 'yes',
			),
		)
	);

	$self->add_control(
		'nav_pos',
		array(
			'type'      => Controls_Manager::SELECT,
			'label'     => __( 'Nav Position', 'porto-functionality' ),
			'options'   => array(
				''                => __( 'Middle', 'porto-functionality' ),
				'nav-pos-inside'  => __( 'Middle Inside', 'porto-functionality' ),
				'nav-pos-outside' => __( 'Middle Outside', 'porto-functionality' ),
				'show-nav-title'  => __( 'Top', 'porto-functionality' ),
				'nav-bottom'      => __( 'Bottom', 'porto-functionality' ),
			),
			'condition' => array(
				'as_param' => 'carousel',
				'show_nav' => 'yes',
			),
		)
	);

	$self->add_control(
		'nav_type',
		array(
			'type'      => Controls_Manager::SELECT,
			'label'     => __( 'Nav Type', 'porto-functionality' ),
			'options'   => $carousel_nav_types,
			'condition' => array(
				'as_param' => 'carousel',
				'show_nav' => 'yes',
			),
		)
	);

	$self->add_control(
		'show_dots',
		array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => __( 'Show Dots', 'porto-functionality' ),
			'condition' => array(
				'as_param' => 'carousel',
			),
		)
	);

	$self->add_control(
		'dots_style',
		array(
			'type'      => Controls_Manager::SELECT,
			'label'     => __( 'Dots Style', 'porto-functionality' ),
			'options'   => array(
				''             => __( 'Default', 'porto-functionality' ),
				'dots-style-1' => __( 'Circle inner dot', 'porto-functionality' ),
			),
			'condition' => array(
				'as_param'  => 'carousel',
				'show_dots' => 'yes',
			),
		)
	);

	$self->add_control(
		'dots_pos',
		array(
			'type'      => Controls_Manager::SELECT,
			'label'     => __( 'Dots Position', 'porto-functionality' ),
			'options'   => array(
				''                => __( 'Outside', 'porto-functionality' ),
				'nav-inside'      => __( 'Inside', 'porto-functionality' ),
				'show-dots-title' => __( 'Top beside title', 'porto-functionality' ),
			),
			'condition' => array(
				'as_param'  => 'carousel',
				'show_dots' => 'yes',
			),
		)
	);

	$self->add_control(
		'dots_align',
		array(
			'type'      => Controls_Manager::SELECT,
			'label'     => __( 'Dots Align', 'porto-functionality' ),
			'options'   => array(
				''                  => __( 'Right', 'porto-functionality' ),
				'nav-inside-center' => __( 'Center', 'porto-functionality' ),
				'nav-inside-left'   => __( 'Left', 'porto-functionality' ),
			),
			'condition' => array(
				'as_param' => 'carousel',
				'dots_pos' => 'nav-inside',
			),
		)
	);

	$self->add_control(
		'autoplay',
		array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => __( 'Auto Play', 'porto-functionality' ),
			'condition' => array(
				'as_param' => 'carousel',
			),
		)
	);

	$self->add_control(
		'autoplay_timeout',
		array(
			'type'      => Controls_Manager::NUMBER,
			'label'     => __( 'Auto Play Timeout', 'porto-functionality' ),
			'default'   => 5000,
			'condition' => array(
				'as_param' => 'carousel',
				'autoplay' => 'yes',
			),
		)
	);

	$self->add_control(
		'fullscreen',
		array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => __( 'Full Screen', 'porto-functionality' ),
			'condition' => array(
				'as_param' => 'carousel',
			),
		)
	);

	$self->add_control(
		'center',
		array(
			'type'        => Controls_Manager::SWITCHER,
			'label'       => __( 'Center Item', 'porto-functionality' ),
			'description' => __( 'This will add "center" class to the center item.', 'porto-functionality' ),
			'condition'   => array(
				'as_param' => 'carousel',
			),
		)
	);

	/* banner controls */
	$self->add_control(
		'banner_image',
		array(
			'type'        => Controls_Manager::MEDIA,
			'label'       => __( 'Banner Image', 'porto-functionality' ),
			'description' => __( 'Upload the image for this banner', 'porto-functionality' ),
			'condition'   => array(
				'as_param' => 'banner',
			),
		)
	);

	$self->add_group_control(
		\Elementor\Group_Control_Image_Size::get_type(),
		array(
			'name'      => 'banner_image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
			'default'   => 'full',
			'separator' => 'none',
			'condition' => array(
				'as_param' => 'banner',
			),
		)
	);

	$self->add_control(
		'banner_color_bg2',
		array(
			'type'      => Controls_Manager::COLOR,
			'label'     => __( 'Background Color', 'porto-functionality' ),
			'selectors' => array(
				'{{WRAPPER}} > .porto-ibanner' => 'background-color: {{VALUE}};',
			),
			'condition' => array(
				'as_param' => 'banner',
			),
		)
	);

	$self->add_control(
		'banner_link',
		array(
			'type'        => Controls_Manager::URL,
			'label'       => __( 'Link ', 'porto-functionality' ),
			'description' => __( 'Add link / select existing page to link to this banner', 'porto-functionality' ),
			'condition'   => array(
				'as_param' => 'banner',
			),
		)
	);

	$self->add_control(
		'add_container',
		array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => __( 'Add Container', 'porto-functionality' ),
			'condition' => array(
				'as_param' => 'banner',
			),
		)
	);

	$self->add_responsive_control(
		'min_height',
		array(
			'label'      => __( 'Min Height', 'porto-functionality' ),
			'type'       => Controls_Manager::SLIDER,
			'range'      => array(
				'px' => array(
					'step' => 1,
					'min'  => 1,
					'max'  => 1000,
				),
				'%'  => array(
					'step' => 1,
					'min'  => 1,
					'max'  => 100,
				),
				'vh' => array(
					'step' => 1,
					'min'  => 1,
					'max'  => 100,
				),
				'vw' => array(
					'step' => 1,
					'min'  => 1,
					'max'  => 100,
				),
				'em' => array(
					'step' => 1,
					'min'  => 1,
					'max'  => 100,
				),
			),
			'size_units' => array(
				'%',
				'px',
				'vh',
				'vw',
				'em',
			),
			'selectors'  => array(
				'{{WRAPPER}} > .porto-ibanner' => 'min-height: {{SIZE}}{{UNIT}};',
			),
			'condition'  => array(
				'as_param' => 'banner',
			),
		)
	);

	/* creative grid controls */
	$self->add_control(
		'use_preset',
		array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => __( 'Use Preset', 'porto-functionality' ),
			'condition' => array(
				'as_param' => 'creative',
			),
		)
	);

	$self->add_control(
		'grid_layout',
		array(
			'label'     => __( 'Grid Layout', 'porto-functionality' ),
			'type'      => 'image_choose',
			'default'   => '1',
			'options'   => array_combine( array_values( porto_sh_commons( 'masonry_layouts' ) ), array_keys( porto_sh_commons( 'masonry_layouts' ) ) ),
			'condition' => array(
				'as_param'   => 'creative',
				'use_preset' => 'yes',
			),
		)
	);

	$self->add_control(
		'grid_height',
		array(
			'label'     => __( 'Grid Height', 'porto-functionality' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => '600px',
			'condition' => array(
				'as_param'   => 'creative',
				'use_preset' => 'yes',
			),
		)
	);

	$self->add_control(
		'spacing',
		array(
			'type'        => Controls_Manager::SLIDER,
			'label'       => __( 'Column Spacing (px)', 'porto-functionality' ),
			'description' => __( 'This will override "Columns Gap" value in "Layout" section.', 'porto-functionality' ),
			'range'       => array(
				'px' => array(
					'step' => 1,
					'min'  => 0,
					'max'  => 100,
				),
			),
			'condition'   => array(
				'as_param'   => 'creative',
				'use_preset' => 'yes',
			),
		)
	);

	$legacy_enabled = ! method_exists( \Elementor\Plugin::instance(), 'get_legacy_mode' ) || \Elementor\Plugin::instance()->get_legacy_mode( 'elementWrappers' );
	if ( $legacy_enabled ) {
		$spacing_selectors = array(
			'{{WRAPPER}} > .elementor-container > .elementor-row' => 'margin-left: calc(-{{SIZE}}{{UNIT}} / 2); margin-right: calc(-{{SIZE}}{{UNIT}} / 2); width: calc(100% + {{SIZE}}{{UNIT}});',
			'{{WRAPPER}} > .elementor-container > .elementor-row > .elementor-column > .elementor-element-populated' => 'padding: calc({{SIZE}}{{UNIT}} / 2);',
		);
	} else {
		global $porto_settings;
		$spacing_selectors = array(
			'.elementor-element.elementor-element-{{ID}} > .elementor-container' => 'margin-left: calc(-{{SIZE}}{{UNIT}} / 2); margin-right: calc(-{{SIZE}}{{UNIT}} / 2); width: calc(100% + {{SIZE}}{{UNIT}});',
			'{{WRAPPER}} > .elementor-container > .elementor-column > .elementor-element-populated' => 'padding: calc({{SIZE}}{{UNIT}} / 2);',
		);
		if ( ! empty( $porto_settings ) ) {
			$spacing_selectors['.elementor-section-boxed.elementor-element-{{ID}} > .elementor-container'] = 'max-width:calc(' . ( (int) $porto_settings['container-width'] - (int) $porto_settings['grid-gutter-width'] ) . 'px + {{SIZE}}{{UNIT}});';
		}
	}

	$self->add_control(
		'spacing1',
		array(
			'type'        => Controls_Manager::SLIDER,
			'label'       => __( 'Column Spacing (px)', 'porto-functionality' ),
			'description' => __( 'This will override "Columns Gap" value in "Layout" section.', 'porto-functionality' ),
			'range'       => array(
				'px' => array(
					'step' => 1,
					'min'  => 0,
					'max'  => 100,
				),
				'em' => array(
					'step' => 0.1,
					'min'  => 0,
					'max'  => 10,
				),
			),
			'size_units'  => array(
				'px',
				'em',
			),
			'default'     => array(
				'unit' => 'px',
			),
			'selectors'   => $spacing_selectors,
			'condition'   => array(
				'as_param'    => 'creative',
				'use_preset!' => 'yes',
			),
		)
	);

	$self->add_control(
		'porto_el_cls',
		array(
			'label'     => __( 'Extra Class', 'porto-functionality' ),
			'type'      => Controls_Manager::TEXT,
			'condition' => array(
				'as_param!' => '',
			),
		)
	);

	$self->end_controls_section();
}

function porto_elementor_column_custom_control( $self, $args ) {

	// removed required attribute for Column Width(_inline_size) field
	$self->update_control(
		'_inline_size',
		array(
			'required' => false,
		)
	);

	$self->start_controls_section(
		'section_column_additional',
		array(
			'label' => __( 'Porto Settings', 'porto-functionality' ),
			'tab'   => Controls_Manager::TAB_LAYOUT,
		)
	);

	$self->add_control(
		'as_banner_layer',
		array(
			'type'    => Controls_Manager::SELECT,
			'label'   => __( 'Use as', 'porto-functionality' ),
			'options' => array(
				''          => __( 'Default', 'porto-functionality' ),
				'carousel'  => __( 'Carousel', 'porto-functionality' ),
				'yes'       => __( 'Banner Layer', 'porto-functionality' ),
				'grid_item' => __( 'Creative Grid Item', 'porto-functionality' ),
			),
		)
	);

	/* start carousel controls */
	$self->add_control(
		'stage_padding',
		array(
			'label'     => __( 'Stage Padding (px)', 'porto-functionality' ),
			'type'      => Controls_Manager::NUMBER,
			'min'       => 0,
			'max'       => 100,
			'step'      => 1,
			'condition' => array(
				'as_param' => 'carousel',
			),
		)
	);

	$self->add_responsive_control(
		'items',
		array(
			'label'     => __( 'Items', 'porto-functionality' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => array(
				'px' => array(
					'step' => 1,
					'min'  => 1,
					'max'  => 6,
				),
			),
			'condition' => array(
				'as_banner_layer' => 'carousel',
			),
		)
	);

	$self->add_control(
		'item_margin',
		array(
			'label'       => __( 'Item Margin (px)', 'porto-functionality' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0,
			'min'         => '0',
			'max'         => '100',
			'step'        => '1',
			'placeholder' => '0',
			'condition'   => array(
				'as_banner_layer' => 'carousel',
			),
		)
	);

	$self->add_control(
		'show_nav',
		array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => __( 'Show Nav', 'porto-functionality' ),
			'condition' => array(
				'as_banner_layer' => 'carousel',
			),
		)
	);

	$self->add_control(
		'show_nav_hover',
		array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => __( 'Show Nav on Hover', 'porto-functionality' ),
			'condition' => array(
				'as_banner_layer' => 'carousel',
				'show_nav'        => 'yes',
			),
		)
	);

	$self->add_control(
		'nav_pos',
		array(
			'type'      => Controls_Manager::SELECT,
			'label'     => __( 'Nav Position', 'porto-functionality' ),
			'options'   => array(
				''                => __( 'Middle', 'porto-functionality' ),
				'nav-pos-inside'  => __( 'Middle Inside', 'porto-functionality' ),
				'nav-pos-outside' => __( 'Middle Outside', 'porto-functionality' ),
				'show-nav-title'  => __( 'Top', 'porto-functionality' ),
				'nav-bottom'      => __( 'Bottom', 'porto-functionality' ),
			),
			'condition' => array(
				'as_banner_layer' => 'carousel',
				'show_nav'        => 'yes',
			),
		)
	);

	$carousel_nav_types = porto_sh_commons( 'carousel_nav_types' );
	$carousel_nav_types = array_combine( array_values( $carousel_nav_types ), array_keys( $carousel_nav_types ) );

	$self->add_control(
		'nav_type',
		array(
			'type'      => Controls_Manager::SELECT,
			'label'     => __( 'Nav Type', 'porto-functionality' ),
			'options'   => $carousel_nav_types,
			'condition' => array(
				'as_banner_layer' => 'carousel',
				'show_nav'        => 'yes',
			),
		)
	);

	$self->add_control(
		'show_dots',
		array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => __( 'Show Dots', 'porto-functionality' ),
			'condition' => array(
				'as_banner_layer' => 'carousel',
			),
		)
	);

	$self->add_control(
		'dots_style',
		array(
			'type'      => Controls_Manager::SELECT,
			'label'     => __( 'Dots Style', 'porto-functionality' ),
			'options'   => array(
				''             => __( 'Default', 'porto-functionality' ),
				'dots-style-1' => __( 'Circle inner dot', 'porto-functionality' ),
			),
			'condition' => array(
				'as_banner_layer' => 'carousel',
				'show_dots'       => 'yes',
			),
		)
	);

	$self->add_control(
		'dots_pos',
		array(
			'type'      => Controls_Manager::SELECT,
			'label'     => __( 'Dots Position', 'porto-functionality' ),
			'options'   => array(
				''                => __( 'Outside', 'porto-functionality' ),
				'nav-inside'      => __( 'Inside', 'porto-functionality' ),
				'show-dots-title' => __( 'Top beside title', 'porto-functionality' ),
			),
			'condition' => array(
				'as_banner_layer' => 'carousel',
				'show_dots'       => 'yes',
			),
		)
	);

	$self->add_control(
		'dots_align',
		array(
			'type'      => Controls_Manager::SELECT,
			'label'     => __( 'Dots Align', 'porto-functionality' ),
			'options'   => array(
				''                  => __( 'Right', 'porto-functionality' ),
				'nav-inside-center' => __( 'Center', 'porto-functionality' ),
				'nav-inside-left'   => __( 'Left', 'porto-functionality' ),
			),
			'condition' => array(
				'as_banner_layer' => 'carousel',
				'dots_pos'        => 'nav-inside',
			),
		)
	);

	$self->add_control(
		'autoplay',
		array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => __( 'Auto Play', 'porto-functionality' ),
			'condition' => array(
				'as_banner_layer' => 'carousel',
			),
		)
	);

	$self->add_control(
		'autoplay_timeout',
		array(
			'type'      => Controls_Manager::NUMBER,
			'label'     => __( 'Auto Play Timeout', 'porto-functionality' ),
			'default'   => 5000,
			'condition' => array(
				'as_banner_layer' => 'carousel',
				'autoplay'        => 'yes',
			),
		)
	);

	$self->add_control(
		'fullscreen',
		array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => __( 'Full Screen', 'porto-functionality' ),
			'condition' => array(
				'as_banner_layer' => 'carousel',
			),
		)
	);

	$self->add_control(
		'center',
		array(
			'type'        => Controls_Manager::SWITCHER,
			'label'       => __( 'Center Item', 'porto-functionality' ),
			'description' => __( 'This will add "center" class to the center item.', 'porto-functionality' ),
			'condition'   => array(
				'as_banner_layer' => 'carousel',
			),
		)
	);

	/* end carousel controls */

	$self->add_responsive_control(
		'width',
		array(
			'type'       => Controls_Manager::SLIDER,
			'label'      => __( 'Width', 'porto-functionality' ),
			'range'      => array(
				'%'  => array(
					'step' => 1,
					'min'  => 1,
					'max'  => 100,
				),
				'px' => array(
					'step' => 1,
					'min'  => 1,
					'max'  => 1000,
				),
				'vw' => array(
					'step' => 1,
					'min'  => 1,
					'max'  => 100,
				),
			),
			'size_units' => array(
				'%',
				'px',
				'vw',
			),
			'default'    => array(
				'unit' => '%',
			),
			'selectors'  => array(
				'{{WRAPPER}} .porto-ibanner-layer' => 'width: {{SIZE}}{{UNIT}};',
			),
			'condition'  => array(
				'as_banner_layer' => 'yes',
			),
		)
	);

	$self->add_responsive_control(
		'width1',
		array(
			'type'                => Controls_Manager::SLIDER,
			'label'               => __( 'Width', 'porto-functionality' ),
			'description'         => __( 'This will not work if you use preset layout.', 'porto-functionality' ),
			'range'               => array(
				'%' => array(
					'step' => 1,
					'min'  => 1,
					'max'  => 100,
				),
			),
			'size_units'          => array(
				'%',
			),
			'default'             => array(
				'unit' => '%',
			),
			'selectors'           => array(
				'.elementor-element-{{ID}}.porto-grid-item' => 'width: {{SIZE}}%;',
			),
			'min_affected_device' => array(
				Elementor\Controls_Stack::RESPONSIVE_DESKTOP => Elementor\Controls_Stack::RESPONSIVE_TABLET,
				Elementor\Controls_Stack::RESPONSIVE_TABLET  => Elementor\Controls_Stack::RESPONSIVE_TABLET,
			),
			'condition'           => array(
				'as_banner_layer' => 'grid_item',
			),
		)
	);

	$self->add_responsive_control(
		'height',
		array(
			'type'       => Controls_Manager::SLIDER,
			'label'      => __( 'Height', 'porto-functionality' ),
			'range'      => array(
				'%'  => array(
					'step' => 1,
					'min'  => 1,
					'max'  => 100,
				),
				'px' => array(
					'step' => 1,
					'min'  => 1,
					'max'  => 1000,
				),
				'vw' => array(
					'step' => 1,
					'min'  => 1,
					'max'  => 100,
				),
			),
			'size_units' => array(
				'%',
				'px',
				'vw',
			),
			'default'    => array(
				'unit' => '%',
			),
			'selectors'  => array(
				'{{WRAPPER}} .porto-ibanner-layer' => 'height: {{SIZE}}{{UNIT}};',
			),
			'condition'  => array(
				'as_banner_layer' => 'yes',
			),
		)
	);

	$self->add_control(
		'horizontal',
		array(
			'type'        => Controls_Manager::SLIDER,
			'label'       => __( 'Horizontal Position', 'porto-functionality' ),
			'range'       => array(
				'%' => array(
					'step' => 1,
					'min'  => -50,
					'max'  => 150,
				),
			),
			'default'     => array(
				'unit' => '%',
				'size' => 50,
			),
			'description' => __( '50 is center, 0 is left and 100 is right.', 'porto-functionality' ),
			'condition'   => array(
				'as_banner_layer' => 'yes',
			),
		)
	);

	$self->add_control(
		'vertical',
		array(
			'type'        => Controls_Manager::SLIDER,
			'label'       => __( 'Vertical Position', 'porto-functionality' ),
			'range'       => array(
				'%' => array(
					'step' => 1,
					'min'  => -50,
					'max'  => 150,
				),
			),
			'default'     => array(
				'unit' => '%',
				'size' => 50,
			),
			'description' => __( '50 is middle, 0 is top and 100 is bottom.', 'porto-functionality' ),
			'condition'   => array(
				'as_banner_layer' => 'yes',
			),
		)
	);

	$self->add_responsive_control(
		'text_align1',
		array(
			'label'              => __( 'Text Align', 'elementor' ),
			'type'               => Controls_Manager::CHOOSE,
			'options'            => array(
				'left'    => array(
					'title' => __( 'Left', 'elementor' ),
					'icon'  => 'eicon-text-align-left',
				),
				'center'  => array(
					'title' => __( 'Center', 'elementor' ),
					'icon'  => 'eicon-text-align-center',
				),
				'right'   => array(
					'title' => __( 'Right', 'elementor' ),
					'icon'  => 'eicon-text-align-right',
				),
				'justify' => array(
					'title' => __( 'Justified', 'elementor' ),
					'icon'  => 'eicon-text-align-justify',
				),
			),
			'default'            => '',
			'selectors'          => array(
				'{{WRAPPER}} .porto-ibanner-layer' => 'text-align: {{VALUE}};',
			),
			'frontend_available' => true,
			'condition'          => array(
				'as_banner_layer' => 'yes',
			),
		)
	);

	$self->add_control(
		'css_anim_type',
		array(
			'label'     => __( 'CSS Animation', 'porto-functionality' ),
			'type'      => Controls_Manager::ANIMATION,
			'condition' => array(
				'as_banner_layer' => 'yes',
			),
		)
	);

	$self->add_control(
		'css_anim_delay',
		array(
			'label'     => __( 'CSS Animation Delay (ms)', 'porto-functionality' ),
			'type'      => Controls_Manager::NUMBER,
			'step'      => 50,
			'min'       => 0,
			'max'       => 8000,
			'condition' => array(
				'as_banner_layer' => 'yes',
				'css_anim_type!'  => '',
			),
		)
	);

	$self->add_control(
		'css_anim_duration',
		array(
			'label'     => __( 'CSS Animation Duration (ms)', 'porto-functionality' ),
			'type'      => Controls_Manager::NUMBER,
			'step'      => 100,
			'min'       => 100,
			'max'       => 4000,
			'condition' => array(
				'as_banner_layer' => 'yes',
				'css_anim_type!'  => '',
			),
		)
	);

	$self->add_control(
		'porto_el_cls',
		array(
			'label'     => __( 'Extra Class', 'porto-functionality' ),
			'type'      => Controls_Manager::TEXT,
			'condition' => array(
				'as_banner_layer!' => '',
			),
		)
	);

	$self->end_controls_section();

	$self->start_controls_section(
		'section_column_floating_fields',
		array(
			'label' => __( 'Floating Animation', 'porto-functionality' ),
			'tab'   => Controls_Manager::TAB_LAYOUT,
		)
	);

	$floating_options = porto_update_vc_options_to_elementor( porto_shortcode_floating_fields() );
	foreach ( $floating_options as $key => $opt ) {
		unset( $opt['condition']['animation_type'] );
		$self->add_control( $key, $opt );
	}

	$self->end_controls_section();
}

function porto_elementor_element_add_parallax( $self, $args ) {
	$self->add_control(
		'parallax_speed',
		array(
			'type'        => Controls_Manager::SLIDER,
			'label'       => __( 'Parallax Speed', 'porto-functionality' ),
			'range'       => array(
				'px' => array(
					'step' => 0.1,
					'min'  => 1,
					'max'  => 3,
				),
			),
			'description' => __( 'Enter parallax speed ratio if you want to use parallax effect. (Note: Standard value is 1.5, min value is 1. Leave empty if you don\'t want.)', 'porto-functionality' ),
			'condition'   => array(
				'background_background'  => array( 'classic' ),
				'background_image[url]!' => '',
			),
		)
	);
}

function porto_elementor_print_section_template( $content, $self ) {
	ob_start();
	?>
	<#
		let extra_container_cls = '',
			extra_before_html = '',
			extra_after_html = '',
			extra_attr = '',
			extra_class = '';
		if ( 'carousel' == settings.as_param || 'creative' == settings.as_param ) {
			let extra_options = {};
			if ( 'carousel' == settings.as_param ) {
				settings.gap = 'no';
				extra_class = ' owl-carousel porto-carousel has-ccols';

				if ( 'yes' == settings.show_nav ) {
					if ( settings.nav_pos ) {
						extra_class += ' ' + settings.nav_pos;
					}
					if ( settings.nav_type ) {
						extra_class += ' ' + settings.nav_type;
					}
					if ( 'yes' == settings.show_nav_hover ) {
						extra_class += ' show-nav-hover';
					}
				}

				if ( 'yes' == settings.show_dots ) {
					if ( settings.dots_style ) {
						extra_class +=  ' ' + settings.dots_style;
					}
					if ( settings.dots_pos ) {
						extra_class +=  ' ' + settings.dots_pos + ' ' + settings.dots_align;
					}
				}

				if ( Number( settings.items.size ) > 1 ) {
					extra_class += ' ccols-xl-' + Number( settings.items.size );
				}
				if ( Number( settings.items_tablet.size ) > 1 ) {
					extra_class += ' ccols-md-' + Number( settings.items_tablet.size );
				}
				if ( Number( settings.items_mobile.size ) > 1 ) {
					extra_class += ' ccols-' + Number( settings.items_mobile.size );
				} else {
					extra_class += ' ccols-1';
				}

				extra_options["nav"] = 'yes' == settings.show_nav;
				extra_options["dots"] = 'yes' == settings.show_dots;
				extra_options["items"] = Number( settings.items.size );
				extra_options["margin"] = Number( settings.item_margin );
				extra_options["themeConfig"] = true;
				extra_options["responsive"] = {};
				extra_options["responsive"][elementorFrontend.config.breakpoints['xs']] = Math.max(Number( settings.items_mobile.size ), 1);
				extra_options["responsive"][elementorFrontend.config.breakpoints['sm']] = Math.max(Number( settings.items_tablet.size ) - 1, Number( settings.items_mobile.size ), 1);
				extra_options["responsive"][elementorFrontend.config.breakpoints['md']] = Math.max(Number( settings.items_tablet.size ), 1);
				if (Math.max(Number( settings.items.size ), 1) > Math.max(Number( settings.items_tablet.size ), 1) + 1) {
					extra_options["responsive"][elementorFrontend.config.breakpoints['lg']] = Math.max(Number( settings.items.size ) - 1, 1);
					extra_options["responsive"][elementorFrontend.config.breakpoints['xl']] = Math.max(Number( settings.items.size ), 1);
				} else {
					extra_options["responsive"][elementorFrontend.config.breakpoints['lg']] = Math.max(Number( settings.items.size ), 1);
				}
				if (settings.stage_padding) {
					extra_options["stagePadding"] = Number(settings.stage_padding);
				}

				if ('yes' == settings.autoplay) {
					extra_options['autoplay']           = true;
					extra_options['autoplayTimeout']    = Number( settings.autoplay_timeout );
					extra_options['autoplayHoverPause'] = true;
				} else {
					extra_options['autoplay'] = false;
				}
				if ( 'yes' == settings.fullscreen ) {
					extra_class                += ' fullscreen-carousel';
					extra_options['fullscreen'] = true;
				}
				if ( 'yes' == settings.center ) {
					extra_options['center'] = true;
				}
				extra_attr += ' data-plugin-options=' + JSON.stringify( extra_options );
			} else {
				extra_class = '';
				if ('yes' == settings.use_preset) {
					extra_class += ' grid-creative porto-preset-layout';
					extra_attr += ' data-layout=' + Number( settings.grid_layout );
					extra_attr += ' data-grid-height=' + escape( settings.grid_height );
					settings.gap = 'no';
					if (0 === settings.spacing.size || settings.spacing.size) {
						extra_attr += ' data-spacing=' + Number( settings.spacing.size );
					}
				} else {
					if (0 === settings.spacing1.size || settings.spacing1.size) {
						extra_attr += ' data-spacing=' + Number( settings.spacing1.size );
					}
				}
				extra_options['layoutMode'] = 'masonry';
				extra_options['masonry'] = {'columnWidth': '.grid-col-sizer'};
				extra_options['itemSelector'] = '.porto-grid-item';
				extra_options['animationEngine'] = 'best-available';
				extra_options['resizable'] = false;
				extra_attr += ' data-plugin-masonry data-plugin-options=' + JSON.stringify( extra_options );

				extra_after_html += '<div class="grid-col-sizer"></div>';
			}
		} else if ('banner' == settings.as_param) {
			var image = {
					id: settings.banner_image.id,
					url: settings.banner_image.url,
					size: settings.banner_image_size,
					dimension: settings.banner_image_custom_dimension,
					model: view.getEditModel()
				},
				image_url = elementor.imagesManager.getImageUrl( image );

			settings.gap = 'no';
			if ( image_url ) {
				extra_before_html += '<img class="porto-ibanner-img" src="' + image_url + '" />';
			}

			if ('yes' == settings.add_container) {
				extra_attr += ' data-add_container=1';
			}
			extra_container_cls += ' porto-ibanner';
		}

		if (settings.parallax_speed.size) {
			extra_class += ' porto-parallax';
			extra_attr += ' data-parallax-speed=' + parseFloat(settings.parallax_speed.size);
		}

		if (settings.porto_el_cls) {
			extra_class += ' ' + settings.porto_el_cls;
		}
	#>
	<?php
		$legacy_enabled = ! method_exists( \Elementor\Plugin::instance(), 'get_legacy_mode' ) || \Elementor\Plugin::instance()->get_legacy_mode( 'elementWrappers' );
	if ( $legacy_enabled ) {
		$content = str_replace( '<div class="elementor-container', '<div class="elementor-container{{ extra_container_cls }}', $content );
		$content = str_replace( '<div class="elementor-row">', '{{{ extra_before_html }}}<div{{ extra_attr }} class="elementor-row{{ extra_class }}">{{{ extra_after_html }}}', $content );
	} else {
		$content = str_replace( '<div class="elementor-container elementor-column-gap-{{ settings.gap }}">', '<div{{ extra_attr }} class="elementor-container elementor-column-gap-{{ settings.gap }}{{ extra_container_cls }}{{ extra_class }}">{{{ extra_before_html }}}{{{ extra_after_html }}}', $content );
	}
		echo porto_filter_output( $content );
	return ob_get_clean();
}

function porto_elementor_print_column_template( $content, $self ) {
	$legacy_enabled = ! method_exists( \Elementor\Plugin::instance(), 'get_legacy_mode' ) || \Elementor\Plugin::instance()->get_legacy_mode( 'elementWrappers' );
	ob_start();
	?>
	<#
		let extra_class = '',
			extra_style = '',
			extra_class1 = '',
			extra_attr = '',
			before_html = '';
		if ( 'yes' == settings.as_banner_layer ) {
			extra_class += ' porto-ibanner-layer';
			if (50 == Number(settings.horizontal.size)) {
				if (50 == Number(settings.vertical.size)) {
					extra_style += 'left: 50%;top: 50%;transform: translate(-50%, -50%);';
				} else {
					extra_style += 'left: 50%;transform: translateX(-50%);';
				}
			} else if (50 > Number(settings.horizontal.size)) {
				extra_style += 'left:' + Number(settings.horizontal.size) + '%;';
			} else {
				extra_style += 'right:' + (100 - Number(settings.horizontal.size)) + '%;';
			}

			if (50 == Number(settings.vertical.size)) {
				if (50 != Number(settings.horizontal.size)) {
					extra_style += 'top: 50%;transform: translateY(-50%);';
				}
			} else if (50 > Number(settings.vertical.size)) {
				extra_style += 'top:' + Number(settings.vertical.size) + '%;';
			} else {
				extra_style += 'bottom:' + (100 - Number(settings.vertical.size)) + '%;';
			}

			if (extra_style) {
				extra_style = ' style="' + extra_style + '"';
			}

			if ( settings.css_anim_type ) {
				extra_style += ' data-appear-animation="' + settings.css_anim_type + '"';
				if ( settings.css_anim_type ) {
					extra_style += ' data-appear-animation-delay="' + Number( settings.css_anim_delay ) + '"';
				}
				if ( settings.css_anim_duration ) {
					extra_style += ' data-appear-animation-duration="' + Number( settings.css_anim_duration ) + '"';
				}
			}
		} else if ( 'grid_item' == settings.as_banner_layer && settings.width1 ) {
			extra_style += ' data-width=' + JSON.stringify( settings.width1 );
		} else if ( 'carousel' == settings.as_banner_layer ) {
			let extra_options = {};
			settings.gap = 'no';
			extra_class1 += ' owl-carousel porto-carousel has-ccols';

			if ( 'yes' == settings.show_nav ) {
				if ( settings.nav_pos ) {
					extra_class1 += ' ' + settings.nav_pos;
				}
				if ( settings.nav_type ) {
					extra_class1 += ' ' + settings.nav_type;
				}
				if ( 'yes' == settings.show_nav_hover ) {
					extra_class1 += ' show-nav-hover';
				}
			}

			if ( 'yes' == settings.show_dots ) {
				if ( settings.dots_style ) {
					extra_class1 +=  ' ' + settings.dots_style;
				}
				if ( settings.dots_pos ) {
					extra_class1 +=  ' ' + settings.dots_pos + ' ' + settings.dots_align;
				}
			}

			if ( Number( settings.items.size ) > 1 ) {
				extra_class1 += ' ccols-xl-' + Number( settings.items.size );
			}
			if ( Number( settings.items_tablet.size ) > 1 ) {
				extra_class1 += ' ccols-md-' + Number( settings.items_tablet.size );
			}
			if ( Number( settings.items_mobile.size ) > 1 ) {
				extra_class1 += ' ccols-' + Number( settings.items_mobile.size );
			} else {
				extra_class1 += ' ccols-1';
			}

			extra_options["nav"] = 'yes' == settings.show_nav;
			extra_options["dots"] = 'yes' == settings.show_dots;
			extra_options["items"] = Number( settings.items.size );
			extra_options["margin"] = Number( settings.item_margin );
			extra_options["themeConfig"] = true;
			extra_options["responsive"] = {};
			extra_options["responsive"][elementorFrontend.config.breakpoints['xs']] = Math.max(Number( settings.items_mobile.size ), 1);
			extra_options["responsive"][elementorFrontend.config.breakpoints['sm']] = Math.max(Number( settings.items_tablet.size ) - 1, Number( settings.items_mobile.size ), 1);
			extra_options["responsive"][elementorFrontend.config.breakpoints['md']] = Math.max(Number( settings.items_tablet.size ), 1);
			if (Math.max(Number( settings.items.size ), 1) > Math.max(Number( settings.items_tablet.size ), 1) + 1) {
				extra_options["responsive"][elementorFrontend.config.breakpoints['lg']] = Math.max(Number( settings.items.size ) - 1, 1);
				extra_options["responsive"][elementorFrontend.config.breakpoints['xl']] = Math.max(Number( settings.items.size ), 1);
			} else {
				extra_options["responsive"][elementorFrontend.config.breakpoints['lg']] = Math.max(Number( settings.items.size ), 1);
			}

			if ('yes' == settings.autoplay) {
				extra_options['autoplay']           = true;
				extra_options['autoplayTimeout']    = Number( settings.autoplay_timeout );
				extra_options['autoplayHoverPause'] = true;
			} else {
				extra_options['autoplay'] = false;
			}
			if ( 'yes' == settings.fullscreen ) {
				extra_class1               += ' fullscreen-carousel';
				extra_options['fullscreen'] = true;
			}
			if ( 'yes' == settings.center ) {
				extra_options['center'] = true;
			}
			if (settings.stage_padding) {
				extra_options["stagePadding"] = Number(settings.stage_padding);
			}

			extra_attr += ' data-plugin-options=' + JSON.stringify( extra_options );
		}
		if (settings.parallax_speed.size) {
			extra_class += ' porto-parallax';
			extra_style += ' data-parallax-speed=' + parseFloat(settings.parallax_speed.size);
		}
		extra_attr += porto_elementor_add_floating_options( settings );

		if (settings.as_banner_layer && settings.porto_el_cls) {
			if ('carousel' == settings.as_banner_layer) {
				extra_class1 += ' ' + settings.porto_el_cls;
			} else {
				extra_class += ' ' + settings.porto_el_cls;
			}
		}

		if (!settings.as_banner_layer) {
			before_html += '<div class="elementor-background-overlay"></div>';
		}
	#>
	<?php if ( $legacy_enabled ) : ?>
	<div class="elementor-column-wrap{{ extra_class }}"{{{ extra_style }}}>
		<div class="elementor-background-overlay"></div>
		<div class="elementor-widget-wrap{{ extra_class1 }}"{{ extra_attr }}></div>
	</div>
	<?php else : ?>
	<div class="elementor-widget-wrap{{ extra_class }}{{ extra_class1 }}"{{{ extra_style }}}{{ extra_attr }}>
		{{{ before_html }}}
	</div>
		<?php
	endif;
	return ob_get_clean();
}

function porto_elementor_section_add_custom_attrs( $self ) {
	$settings = $self->get_settings_for_display();

	if ( 'creative' == $settings['as_param'] ) {
		if ( 'yes' == $settings['use_preset'] ) {
			global $porto_grid_layout, $porto_item_count;
			$porto_grid_layout = porto_creative_grid_layout( empty( $settings['grid_layout'] ) ? '1' : $settings['grid_layout'] );
			$porto_item_count  = 0;
		} else {
			global $porto_grid_layout;
			$porto_grid_layout = array();
		}
		global $porto_grid_type;
		$porto_grid_type = $self->get_data( 'isInner' );
	} elseif ( 'banner' == $settings['as_param'] && 'yes' == $settings['add_container'] ) {
		global $porto_banner_add_container;
		$porto_banner_add_container = true;
	}
	if ( ! empty( $settings['parallax_speed']['size'] ) ) {
		$self->add_render_attribute( '_wrapper', 'data-plugin-parallax', '' );
		$self->add_render_attribute( '_wrapper', 'data-plugin-options', '{"speed": ' . floatval( $settings['parallax_speed']['size'] ) . '}' );

		wp_enqueue_script( 'skrollr' );
	}
}

function porto_elementor_column_add_custom_attrs( $self ) {
	$legacy_enabled = ! method_exists( \Elementor\Plugin::instance(), 'get_legacy_mode' ) || \Elementor\Plugin::instance()->get_legacy_mode( 'elementWrappers' );

	$settings = $self->get_settings_for_display();

	if ( 'yes' == $settings['as_banner_layer'] ) {
		global $porto_banner_add_container;
		if ( ! empty( $porto_banner_add_container ) ) {
			$self->add_render_attribute( '_wrapper', 'class', 'container' );
		}
		$extra_class = array( 'porto-ibanner-layer' );
		$extra_style = '';

		$x_pos = floatval( $settings['horizontal']['size'] );
		$y_pos = floatval( $settings['vertical']['size'] );

		if ( 50.0 === $x_pos ) {
			if ( 50.0 === $y_pos ) {
				$extra_style .= 'left: 50%;top: 50%;transform: translate(-50%, -50%);';
			} else {
				$extra_style .= 'left: 50%;transform: translateX(-50%);';
			}
		} elseif ( 50.0 > $x_pos ) {
			$extra_style .= 'left:' . $x_pos . '%;';
		} else {
			$extra_style .= 'right:' . ( 100 - $x_pos ) . '%;';
		}

		if ( 50.0 === $y_pos ) {
			if ( 50.0 !== $x_pos ) {
				$extra_style .= 'top: 50%;transform: translateY(-50%);';
			}
		} elseif ( 50.0 > $y_pos ) {
			$extra_style .= 'top:' . $y_pos . '%;';
		} else {
			$extra_style .= 'bottom:' . ( 100 - $y_pos ) . '%;';
		}

		if ( isset( $settings['porto_el_cls'] ) && $settings['porto_el_cls'] ) {
			$extra_class[] = esc_attr( $settings['porto_el_cls'] );
		}

		$wrapper_name = $legacy_enabled ? '_inner_wrapper' : '_widget_wrapper';
		$self->add_render_attribute( $wrapper_name, 'class', $extra_class );
		if ( $extra_style ) {
			$self->add_render_attribute( $wrapper_name, 'style', $extra_style );
		}

		if ( isset( $settings['css_anim_type'] ) && $settings['css_anim_type'] ) {
			$self->add_render_attribute( $wrapper_name, 'data-appear-animation', esc_attr( $settings['css_anim_type'] ) );
			if ( ! empty( $settings['css_anim_delay'] ) ) {
				$self->add_render_attribute( $wrapper_name, 'data-appear-animation-delay', absint( $settings['css_anim_delay'] ) );
			}
			if ( ! empty( $settings['css_anim_duration'] ) ) {
				$self->add_render_attribute( $wrapper_name, 'data-appear-animation-duration', absint( $settings['css_anim_duration'] ) );
			}
		}
	} if ( 'carousel' == $settings['as_banner_layer'] ) {
		$items        = 0 < intval( $settings['items']['size'] ) ? $settings['items']['size'] : 1;
		$items_tablet = 0 < intval( $settings['items_tablet']['size'] ) ? $settings['items_tablet']['size'] : 1;
		$items_mobile = 0 < intval( $settings['items_mobile']['size'] ) ? $settings['items_mobile']['size'] : 1;

		$settings['gap'] = 'no';
		$extra_class     = array( 'porto-carousel', 'owl-carousel', 'has-ccols' );
		if ( 'yes' == $settings['show_nav'] ) {
			if ( $settings['nav_pos'] ) {
				$extra_class[] = esc_attr( $settings['nav_pos'] );
			}
			if ( $settings['nav_type'] ) {
				$extra_class[] = esc_attr( $settings['nav_type'] );
			}
			if ( 'yes' == $settings['show_nav_hover'] ) {
				$extra_class[] = 'show-nav-hover';
			}
		}

		if ( 'yes' == $settings['show_dots'] ) {
			if ( $settings['dots_style'] ) {
				$extra_class[] = esc_attr( $settings['dots_style'] );
			}
			if ( $settings['dots_pos'] ) {
				$extra_class[] = esc_attr( $settings['dots_pos'] . ' ' . $settings['dots_align'] );
			}
		}

		if ( (int) $items > 1 ) {
			$extra_class[] = 'ccols-xl-' . intval( $items );
		}
		if ( (int) $items_tablet > 1 ) {
			$extra_class[] = 'ccols-md-' . intval( $items_tablet );
		}
		if ( (int) $items_mobile > 1 ) {
			$extra_class[] = 'ccols-' . intval( $items_mobile );
		} else {
			$extra_class[] = 'ccols-1';
		}

		$extra_options                = array();
		$extra_options['margin']      = '' !== $settings['item_margin'] ? (int) $settings['item_margin'] : 0;
		$extra_options['items']       = $items;
		$extra_options['nav']         = 'yes' == $settings['show_nav'];
		$extra_options['dots']        = 'yes' == $settings['show_dots'];
		$extra_options['themeConfig'] = true;

		$breakpoints = Elementor\Core\Responsive\Responsive::get_breakpoints();
		if ( 1 !== intval( $items ) ) {
			$extra_options['responsive'] = array( $breakpoints['xs'] => (int) $items_mobile );

			$items_sm = $items_tablet - 1 >= $items_mobile ? $items_tablet - 1 : $items_mobile;
			if ( (int) $items_sm !== (int) $items_mobile ) {
				$extra_options['responsive'][ $breakpoints['sm'] ] = (int) $items_sm;
			}
			if ( (int) $items_tablet !== (int) $items_sm ) {
				$extra_options['responsive'][ $breakpoints['md'] ] = (int) $items_tablet;
			}
			if ( (int) $items !== (int) $items_tablet ) {
				if ( (int) $items > (int) $items_tablet + 1 ) {
					$extra_options['responsive'][ $breakpoints['lg'] ] = (int) $items_tablet + 1;
					$extra_options['responsive'][ $breakpoints['xl'] ] = (int) $items;
				} else {
					$extra_options['responsive'][ $breakpoints['lg'] ] = (int) $items;
				}
			}
		}
		if ( 'yes' == $settings['autoplay'] ) {
			$extra_options['autoplay']           = true;
			$extra_options['autoplayTimeout']    = (int) $settings['autoplay_timeout'];
			$extra_options['autoplayHoverPause'] = true;
		} else {
			$extra_options['autoplay'] = false;
		}
		if ( isset( $settings['fullscreen'] ) && 'yes' == $settings['fullscreen'] ) {
			$extra_class[]               = 'fullscreen-carousel';
			$extra_options['fullscreen'] = true;
		}
		if ( isset( $settings['center'] ) && 'yes' == $settings['center'] ) {
			$extra_options['center'] = true;
		}
		if ( ! empty( $settings['stage_padding'] ) ) {
			$extra_options['stagePadding'] = (int) $settings['stage_padding'];
		}

		if ( isset( $settings['porto_el_cls'] ) && $settings['porto_el_cls'] ) {
			$extra_class[] = esc_attr( $settings['porto_el_cls'] );
		}

		$self->add_render_attribute( '_widget_wrapper', 'class', $extra_class );
		$self->add_render_attribute( '_widget_wrapper', 'data-plugin-options', esc_attr( json_encode( $extra_options ) ) );

	} else {
		global $porto_grid_layout, $porto_item_count, $porto_grid_type;
		$is_inner = $self->get_data( 'isInner' );
		if ( $is_inner === $porto_grid_type && isset( $porto_grid_layout ) && is_array( $porto_grid_layout ) ) {
			$extra_class = array();
			if ( isset( $settings['porto_el_cls'] ) && $settings['porto_el_cls'] ) {
				$extra_class[] = esc_attr( $settings['porto_el_cls'] );
			}
			if ( isset( $porto_item_count ) && ( 0 === $porto_item_count || ! empty( $porto_item_count ) ) ) {
				$grid_layout   = $porto_grid_layout[ $porto_item_count % count( $porto_grid_layout ) ];
				$extra_class[] = esc_attr( 'porto-grid-item grid-col-' . $grid_layout['width'] . ' grid-col-md-' . $grid_layout['width_md'] . ( isset( $grid_layout['width_lg'] ) ? ' grid-col-lg-' . $grid_layout['width_lg'] : '' ) . ( isset( $grid_layout['height'] ) ? ' grid-height-' . $grid_layout['height'] : '' ) );
				$porto_item_count++;
			} else {
				$extra_class[]       = 'porto-grid-item';
				$porto_grid_layout[] = $settings['width1'];
				if ( isset( $settings['width1_tablet'] ) && ! empty( $settings['width1_tablet']['size'] ) ) {
					$porto_grid_layout[] = $settings['width1_tablet'];
				}
				if ( isset( $settings['width1_mobile'] ) && ! empty( $settings['width1_mobile']['size'] ) ) {
					$porto_grid_layout[] = $settings['width1_mobile'];
				}
			}
			$self->add_render_attribute( '_wrapper', 'class', $extra_class );
		}
	}

	if ( ! empty( $settings['parallax_speed']['size'] ) ) {
		$wrapper_name = $legacy_enabled ? '_inner_wrapper' : '_widget_wrapper';
		$self->add_render_attribute( $wrapper_name, 'data-plugin-parallax', '' );
		$self->add_render_attribute( $wrapper_name, 'data-plugin-options', '{"speed": ' . floatval( $settings['parallax_speed']['size'] ) . '}' );
		wp_enqueue_script( 'skrollr' );
	}

	$floating_attrs = porto_shortcode_add_floating_options( $settings, true );
	if ( $floating_attrs ) {
		foreach ( $floating_attrs as $key => $val ) {
			$self->add_render_attribute( '_widget_wrapper', $key, $val );
		}
	}
}
