<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Porto Section Element
 *
 * Carousel, Creative Grid and Banner
 *
 * @since 5.2.2
 */
use Elementor\Controls_Manager;

class Porto_Elementor_Section extends Elementor\Element_Section {
	private static $presets = array();

	public function before_render() {
		$settings = $this->get_settings_for_display();

		$items        = isset( $settings['items']['size'] ) ? ( 0 < intval( $settings['items']['size'] ) ? $settings['items']['size'] : 1 ) : 1;
		$items_tablet = isset( $settings['items_tablet'] ) && isset( $settings['items_tablet']['size'] ) ? ( 0 < intval( $settings['items_tablet']['size'] ) ? $settings['items_tablet']['size'] : 1 ) : 1;
		$items_mobile = isset( $settings['items_mobile'] ) && isset( $settings['items_mobile']['size'] ) ? ( 0 < intval( $settings['items_mobile']['size'] ) ? $settings['items_mobile']['size'] : 1 ) : 1;

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
			if ( ! empty( $settings['hover_effect'] ) ) {
				$extra_cont_cls .= ' ' . $settings['hover_effect'];
			}
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
		<<?php echo esc_html( $this->porto_html_tag() ); ?> <?php $this->print_render_attribute_string( '_wrapper' ); ?>>
			<?php
			if ( 'video' === $settings['background_background'] ) :
				if ( $settings['background_video_link'] ) :
					$video_properties = Elementor\Embed::get_video_properties( $settings['background_video_link'] );

					$this->add_render_attribute( 'background-video-container', 'class', 'elementor-background-video-container' );

					if ( ! $settings['background_play_on_mobile'] ) {
						$this->add_render_attribute( 'background-video-container', 'class', 'elementor-hidden-mobile' );
					}
					?>
					<div <?php $this->print_render_attribute_string( 'background-video-container' ); ?>>
						<?php if ( $video_properties ) : ?>
							<div class="elementor-background-video-embed"></div>
							<?php
						else :
							$video_tag_attributes = 'autoplay muted playsinline';
							if ( 'yes' !== $settings['background_play_once'] ) :
								$video_tag_attributes .= ' loop';
							endif;
							?>
							<video class="elementor-background-video-hosted elementor-html5-video" <?php
								// PHPCS - the variable $video_tag_attributes is a plain string.
								echo $video_tag_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>></video>
						<?php endif; ?>
					</div>
					<?php
				endif;
			endif;

			$has_background_overlay = in_array( $settings['background_overlay_background'], array( 'classic', 'gradient' ), true ) ||
									in_array( $settings['background_overlay_hover_background'], array( 'classic', 'gradient' ), true );

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
			$legacy_enabled = ! porto_elementor_if_dom_optimization();
			if ( 'banner' == $settings['as_param'] ) {
				if ( ! empty( $settings['banner_image'] ) && ! empty( $settings['banner_image']['id'] ) ) {
					$attr    = array( 'class' => 'porto-ibanner-img' . ( ! empty( $settings['banner_effect'] ) ? ' invisible' : '' ) );
					$img_src = wp_get_attachment_image_src( $settings['banner_image']['id'], ! empty( $settings['banner_image_size'] ) ? $settings['banner_image_size'] : 'full' );

					// Banner effect and parallax effect
					if ( '' !== $settings['banner_effect'] || '' !== $settings['particle_effect'] ) {
						// Background Effect
						$background_wrapclass = '';
						$background_class     = '';
						if ( $settings['banner_effect'] ) {
							$background_class = ' ' . $settings['banner_effect'];
						}
						// Particle Effect
						$particle_class = '';
						if ( $settings['particle_effect'] ) {
							$particle_class = ' ' . $settings['particle_effect'];
						}

						if ( ! empty( $img_src[0] ) ) {
							if ( '' == $settings['particle_effect'] || '' !== $settings['banner_effect'] ) {
								$banner_img = esc_url( $img_src[0] );
							}
							ob_start();
							echo '<div class="banner-effect-wrapper' . $background_wrapclass . '">';
							echo '<div class="banner-effect' . $background_class . '"' . ( empty( $banner_img ) ? '' : ' style="background-image: url(' . $banner_img . '); background-size: cover;background-position: center;"' ) . '>';

							if ( '' !== $settings['particle_effect'] ) {
								echo '<div class="particle-effect' . $particle_class . '"></div>';
							}
							echo '</div>';
							echo '</div>';
							$before_html .= ob_get_clean();
						}
					}
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
		$legacy_enabled = ! porto_elementor_if_dom_optimization();
		if ( $legacy_enabled ) :
			?>
			</div>
		<?php endif; ?>
		</<?php echo esc_html( $this->porto_html_tag() ); ?>>
		<?php
	}

	public static function get_presets( $columns_count = null, $preset_index = null ) {
		if ( ! self::$presets ) {
			self::init_presets();
		}

		$presets = self::$presets;

		if ( null !== $columns_count ) {
			$presets = $presets[ $columns_count ];
		}

		if ( null !== $preset_index ) {
			$presets = $presets[ $preset_index ];
		}

		return $presets;
	}
	public static function init_presets() {

		$additional_presets = array(
			2 => array(
				array(
					'preset' => array( 'flex-1', 'flex-auto' ),
				),
				array(
					'preset' => array( 33, 66 ),
				),
				array(
					'preset' => array( 66, 33 ),
				),
			),
			3 => array(
				array(
					'preset' => array( 'flex-1', 'flex-auto', 'flex-1' ),
				),
				array(
					'preset' => array( 'flex-auto', 'flex-1', 'flex-auto' ),
				),
				array(
					'preset' => array( 25, 25, 50 ),
				),
				array(
					'preset' => array( 50, 25, 25 ),
				),
				array(
					'preset' => array( 25, 50, 25 ),
				),
				array(
					'preset' => array( 16, 66, 16 ),
				),
			),
		);

		foreach ( range( 1, 10 ) as $columns_count ) {
			self::$presets[ $columns_count ] = array(
				array(
					'preset' => array(),
				),
			);

			$preset_unit = floor( 1 / $columns_count * 100 );

			for ( $i = 0; $i < $columns_count; $i++ ) {
				self::$presets[ $columns_count ][0]['preset'][] = $preset_unit;
			}

			if ( ! empty( $additional_presets[ $columns_count ] ) ) {
				self::$presets[ $columns_count ] = array_merge( self::$presets[ $columns_count ], $additional_presets[ $columns_count ] );
			}

			foreach ( self::$presets[ $columns_count ] as $preset_index => & $preset ) {
				$preset['key'] = $columns_count . $preset_index;
			}
		}
	}

	protected function get_initial_config() {
		global $post;
		if ( ( $post && PortoBuilders::BUILDER_SLUG == $post->post_type && ( 'header' == get_post_meta( $post->ID, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) || 'footer' == get_post_meta( $post->ID, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) ) ) {
			$config = parent::get_initial_config();

			$config['presets']       = self::get_presets();
			$config['controls']      = $this->get_controls();
			$config['tabs_controls'] = $this->get_tabs_controls();

			return $config;
		} else {
			return parent::get_initial_config();
		}
	}

	protected function print_shape_divider( $side ) {
		$settings         = $this->get_active_settings();
		$base_setting_key = "shape_divider_$side";
		$negative         = ! empty( $settings[ $base_setting_key . '_negative' ] );

		if ( 'custom' != $settings[ $base_setting_key ] ) {
			$shape_path = Elementor\Shapes::get_shape_path( $settings[ $base_setting_key ], $negative );
			if ( ! is_file( $shape_path ) || ! is_readable( $shape_path ) ) {
				return;
			}
		}
		?>
		<div class="elementor-shape elementor-shape-<?php echo esc_attr( $side ); ?>" data-negative="<?php
			// PHPCS - the variable $negative is getting a setting value with a strict structure.
			echo var_export( $negative ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>">
			<?php
			if ( 'custom' != $settings[ $base_setting_key ] ) {
				// PHPCS - The file content is being read from a strict file path structure.
				echo file_get_contents( $shape_path ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				if ( isset( $settings[ "shape_divider_{$side}_custom" ] ) && isset( $settings[ "shape_divider_{$side}_custom" ]['value'] ) ) {
					\ELEMENTOR\Icons_Manager::render_icon( $settings[ "shape_divider_{$side}_custom" ] );
				}
			}
			?>
		</div>
		<?php
	}

	private function porto_html_tag() {
		if ( is_callable( array( $this, 'get_html_tag' ) ) ) {
			return $this->get_html_tag();
		}

		$html_tag = $this->get_settings( 'html_tag' );

		if ( empty( $html_tag ) ) {
			$html_tag = 'section';
		}

		return Elementor\Utils::validate_html_tag( $html_tag );
	}
}

add_action( 'elementor/element/section/section_layout/after_section_end', 'porto_elementor_section_custom_control', 10, 2 );
add_action( 'elementor/element/section/section_advanced/after_section_end', 'porto_elementor_mpx_controls', 10, 2 );
add_action( 'elementor/element/section/section_shape_divider/after_section_end', 'porto_elementor_shape_divider', 10, 2 );
add_filter( 'elementor/section/print_template', 'porto_elementor_print_section_template', 10, 2 );
add_action( 'elementor/frontend/section/before_render', 'porto_elementor_section_add_custom_attrs', 10, 1 );

add_action( 'elementor/element/section/section_background/before_section_end', 'porto_elementor_element_add_parallax', 10, 2 );

/**
 * Add Shape divider option to elementor section.
 *
 * @since 6.1.0
 *
 * @param Object $self Object of elementor section
 * @param Array  $args
 */
function porto_elementor_shape_divider( $self, $args ) {

	$shapes_options = array(
		'' => esc_html__( 'None', 'elementor' ),
	);
	foreach ( Elementor\Shapes::get_shapes() as $shape_name => $shape_props ) {
		$shapes_options[ $shape_name ] = $shape_props['title'];
	}
	$shapes_options['custom'] = esc_html__( 'Custom', 'porto' );
	$self->update_control(
		'shape_divider_top',
		array(
			'label'              => esc_html__( 'Type', 'elementor' ),
			'type'               => Controls_Manager::SELECT,
			'options'            => $shapes_options,
			'render_type'        => 'none',
			'frontend_available' => true,
		),
		array(
			'overwrite' => true,
		)
	);
	$self->update_control(
		'shape_divider_bottom',
		array(
			'label'              => esc_html__( 'Type', 'elementor' ),
			'type'               => Controls_Manager::SELECT,
			'options'            => $shapes_options,
			'render_type'        => 'none',
			'frontend_available' => true,
		),
		array(
			'overwrite' => true,
		)
	);

	$self->update_control(
		'shape_divider_top_color',
		array(
			'label'     => esc_html__( 'Color', 'elementor' ),
			'type'      => Controls_Manager::COLOR,
			'condition' => [
				'shape_divider_top!' => '',
			],
			'selectors' => [
				'{{WRAPPER}} > .elementor-shape-top .elementor-shape-fill' => 'fill: {{UNIT}};',
				'{{WRAPPER}} > .elementor-shape-top svg' => 'fill: {{UNIT}};',
			],
		),
		array(
			'overwrite' => true,
		)
	);
	$self->update_control(
		'shape_divider_bottom_color',
		array(
			'label'     => esc_html__( 'Color', 'elementor' ),
			'type'      => Controls_Manager::COLOR,
			'condition' => [
				'shape_divider_bottom!' => '',
			],
			'selectors' => [
				'{{WRAPPER}} > .elementor-shape-bottom .elementor-shape-fill' => 'fill: {{UNIT}};',
				'{{WRAPPER}} > .elementor-shape-bottom svg' => 'fill: {{UNIT}};',
			],
		),
		array(
			'overwrite' => true,
		)
	);

	$self->add_control(
		'shape_divider_top_custom',
		array(
			'label'                  => esc_html__( 'Custom SVG', 'porto' ),
			'type'                   => Controls_Manager::ICONS,
			'label_block'            => false,
			'skin'                   => 'inline',
			'exclude_inline_options' => array( 'icon' ),
			'render_type'            => 'none',
			'frontend_available'     => true,
			'condition'              => array(
				'shape_divider_top' => 'custom',
			),
		),
		array(
			'position' => array(
				'of' => 'shape_divider_top',
			),
		)
	);
	$self->add_control(
		'shape_divider_bottom_custom',
		array(
			'label'                  => esc_html__( 'Custom SVG', 'porto' ),
			'type'                   => Controls_Manager::ICONS,
			'label_block'            => false,
			'skin'                   => 'inline',
			'exclude_inline_options' => array( 'icon' ),
			'render_type'            => 'none',
			'frontend_available'     => true,
			'condition'              => array(
				'shape_divider_bottom' => 'custom',
			),
		),
		array(
			'position' => array(
				'of' => 'shape_divider_bottom',
			),
		)
	);

	$self->update_control(
		'gap_columns_custom',
		array(
			'selectors' => [
				'{{WRAPPER}} .elementor-column-gap-custom .elementor-column > .elementor-element-populated, {{WRAPPER}} .elementor-column-gap-custom .elementor-column > .pin-wrapper > .elementor-element-populated' => 'padding: {{SIZE}}{{UNIT}};',
			],
		),
		array(
			'overwrite' => true,
		)
	);
}

function porto_elementor_section_custom_control( $self, $args ) {
	$carousel_nav_types = porto_sh_commons( 'carousel_nav_types' );
	$carousel_nav_types = array_combine( array_values( $carousel_nav_types ), array_keys( $carousel_nav_types ) );
	$self->start_controls_section(
		'section_section_additional',
		array(
			'label' => esc_html__( 'Porto Additional Settings', 'porto-functionality' ),
			'tab'   => Controls_Manager::TAB_LAYOUT,
		)
	);

	if ( is_singular( PortoBuilders::BUILDER_SLUG ) ) {
		$builder_type = get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
		if ( 'header' == $builder_type ) {
			$self->add_control(
				'is_main_header',
				array(
					'type'        => Controls_Manager::SWITCHER,
					'label'       => esc_html__( 'Is Main header?', 'porto-functionality' ),
					'description' => esc_html__( 'This section will be displayed in sticky header.', 'porto-functionality' ),
				)
			);
		} elseif ( 'shop' == $builder_type ) {
			$self->add_control(
				'is_toolbox',
				array(
					'type'        => Controls_Manager::SWITCHER,
					'label'       => esc_html__( 'Is Toolbox?', 'porto-functionality' ),
					'description' => esc_html__( 'Tools box is a container which contains "Sort By", "Display Count", "Grid/List Toggle", etc in Shop Builder.', 'porto-functionality' ),
				)
			);
		}
	}

	$self->add_control(
		'as_param',
		array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Use as', 'porto-functionality' ),
			'options' => array(
				''         => esc_html__( 'Default', 'porto-functionality' ),
				'carousel' => esc_html__( 'Carousel', 'porto-functionality' ),
				'banner'   => esc_html__( 'Banner', 'porto-functionality' ),
				'creative' => esc_html__( 'Creative Grid', 'porto-functionality' ),
			),
		)
	);

	$self->add_control(
		'stage_padding',
		array(
			'label'     => esc_html__( 'Stage Padding (px)', 'porto-functionality' ),
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
			'label'     => esc_html__( 'Items', 'porto-functionality' ),
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
			'label'       => esc_html__( 'Item Margin(px)', 'porto-functionality' ),
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
			'label'     => esc_html__( 'Show Nav', 'porto-functionality' ),
			'condition' => array(
				'as_param' => 'carousel',
			),
		)
	);

	$self->add_control(
		'show_nav_hover',
		array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => esc_html__( 'Show Nav on Hover', 'porto-functionality' ),
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
			'label'     => esc_html__( 'Nav Position', 'porto-functionality' ),
			'options'   => array(
				''                => esc_html__( 'Middle', 'porto-functionality' ),
				'nav-pos-inside'  => esc_html__( 'Middle Inside', 'porto-functionality' ),
				'nav-pos-outside' => esc_html__( 'Middle Outside', 'porto-functionality' ),
				'show-nav-title'  => esc_html__( 'Top', 'porto-functionality' ),
				'nav-bottom'      => esc_html__( 'Bottom', 'porto-functionality' ),
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
			'label'     => esc_html__( 'Nav Type', 'porto-functionality' ),
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
			'label'     => esc_html__( 'Show Dots', 'porto-functionality' ),
			'condition' => array(
				'as_param' => 'carousel',
			),
		)
	);

	$self->add_control(
		'dots_style',
		array(
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__( 'Dots Style', 'porto-functionality' ),
			'options'   => array(
				''             => esc_html__( 'Default', 'porto-functionality' ),
				'dots-style-1' => esc_html__( 'Circle inner dot', 'porto-functionality' ),
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
			'label'     => esc_html__( 'Dots Position', 'porto-functionality' ),
			'options'   => array(
				''                => esc_html__( 'Outside', 'porto-functionality' ),
				'nav-inside'      => esc_html__( 'Inside', 'porto-functionality' ),
				'show-dots-title' => esc_html__( 'Top beside title', 'porto-functionality' ),
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
			'label'     => esc_html__( 'Dots Align', 'porto-functionality' ),
			'options'   => array(
				''                  => esc_html__( 'Right', 'porto-functionality' ),
				'nav-inside-center' => esc_html__( 'Center', 'porto-functionality' ),
				'nav-inside-left'   => esc_html__( 'Left', 'porto-functionality' ),
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
			'label'     => esc_html__( 'Auto Play', 'porto-functionality' ),
			'condition' => array(
				'as_param' => 'carousel',
			),
		)
	);

	$self->add_control(
		'autoplay_timeout',
		array(
			'type'      => Controls_Manager::NUMBER,
			'label'     => esc_html__( 'Auto Play Timeout', 'porto-functionality' ),
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
			'label'     => esc_html__( 'Full Screen', 'porto-functionality' ),
			'condition' => array(
				'as_param' => 'carousel',
			),
		)
	);

	$self->add_control(
		'center',
		array(
			'type'        => Controls_Manager::SWITCHER,
			'label'       => esc_html__( 'Center Item', 'porto-functionality' ),
			'description' => esc_html__( 'This will add "center" class to the center item.', 'porto-functionality' ),
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
			'label'       => esc_html__( 'Banner Image', 'porto-functionality' ),
			'description' => esc_html__( 'Upload the image for this banner', 'porto-functionality' ),
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
			'label'     => esc_html__( 'Background Color', 'porto-functionality' ),
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
			'label'       => esc_html__( 'Link ', 'porto-functionality' ),
			'description' => esc_html__( 'Add link / select existing page to link to this banner', 'porto-functionality' ),
			'condition'   => array(
				'as_param' => 'banner',
			),
		)
	);

	$self->add_control(
		'add_container',
		array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => esc_html__( 'Add Container', 'porto-functionality' ),
			'condition' => array(
				'as_param' => 'banner',
			),
		)
	);

	$self->add_responsive_control(
		'min_height',
		array(
			'label'      => esc_html__( 'Min Height', 'porto-functionality' ),
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
	$self->add_control(
		'hover_effect',
		array(
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__( 'Hover Effect', 'porto-functionality' ),
			'options'   => array(
				''                   => esc_html__( 'None', 'porto-functionality' ),
				'porto-ibe-zoom'     => esc_html__( 'Zoom', 'porto-functionality' ),
				'porto-ibe-effect-1' => esc_html__( 'Effect 1', 'porto-functionality' ),
				'porto-ibe-effect-2' => esc_html__( 'Effect 2', 'porto-functionality' ),
				'porto-ibe-effect-3' => esc_html__( 'Effect 3', 'porto-functionality' ),
				'porto-ibe-effect-4' => esc_html__( 'Effect 4', 'porto-functionality' ),
			),
			'condition' => array(
				'as_param' => 'banner',
			),
		)
	);
	$self->add_control(
		'banner_effect',
		array(
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__( 'Backgrund Effect', 'porto-functionality' ),
			'options'   => array(
				''                   => esc_html__( 'No', 'porto-functionality' ),
				'kenBurnsToRight'    => esc_html__( 'kenBurnsRight', 'porto-functionality' ),
				'kenBurnsToLeft'     => esc_html__( 'kenBurnsLeft', 'porto-functionality' ),
				'kenBurnsToLeftTop'  => esc_html__( 'kenBurnsLeftToTop', 'porto-functionality' ),
				'kenBurnsToRightTop' => esc_html__( 'kenBurnsRightToTop', 'porto-functionality' ),
			),
			'condition' => array(
				'as_param' => 'banner',
			),
		)
	);

	$self->add_responsive_control(
		'banner_effect_duration',
		array(
			'label'      => esc_html__( 'Background Effect Duration (s)', 'porto-functionality' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array(
				's',
			),
			'default'    => array(
				'size' => 30,
				'unit' => 's',
			),
			'range'      => array(
				's' => array(
					'step' => 1,
					'min'  => 0,
					'max'  => 60,
				),
			),
			'selectors'  => array(
				'.elementor-element-{{ID}} .banner-effect' => 'animation-duration:{{SIZE}}s;',
			),
			'condition'  => array(
				'as_param'       => 'banner',
				'banner_effect!' => '',
			),
		)
	);

	$self->add_control(
		'particle_effect',
		array(
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__( 'Particle Effects', 'porto-functionality' ),
			'options'   => array(
				''         => esc_html__( 'No', 'porto-functionality' ),
				'snowfall' => esc_html__( 'Snowfall', 'porto-functionality' ),
				'sparkle'  => esc_html__( 'Sparkle', 'porto-functionality' ),
			),
			'condition' => array(
				'as_param' => 'banner',
			),
		)
	);

	/* creative grid controls */
	$self->add_control(
		'use_preset',
		array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => esc_html__( 'Use Preset', 'porto-functionality' ),
			'condition' => array(
				'as_param' => 'creative',
			),
		)
	);

	$self->add_control(
		'grid_layout',
		array(
			'label'     => esc_html__( 'Grid Layout', 'porto-functionality' ),
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
			'label'     => esc_html__( 'Grid Height', 'porto-functionality' ),
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
			'label'       => esc_html__( 'Column Spacing (px)', 'porto-functionality' ),
			'description' => esc_html__( 'This will override "Columns Gap" value in "Layout" section.', 'porto-functionality' ),
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

	$legacy_enabled = ! porto_elementor_if_dom_optimization();
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
			'label'       => esc_html__( 'Column Spacing (px)', 'porto-functionality' ),
			'description' => esc_html__( 'This will override "Columns Gap" value in "Layout" section.', 'porto-functionality' ),
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
			'label'     => esc_html__( 'Extra Class', 'porto-functionality' ),
			'type'      => Controls_Manager::TEXT,
			'condition' => array(
				'as_param!' => '',
			),
		)
	);

	$self->end_controls_section();
}

function porto_elementor_element_add_parallax( $self, $args ) {
	$self->add_control(
		'parallax_speed',
		array(
			'type'        => Controls_Manager::SLIDER,
			'label'       => esc_html__( 'Parallax Speed', 'porto-functionality' ),
			'range'       => array(
				'px' => array(
					'step' => 0.1,
					'min'  => 1,
					'max'  => 3,
				),
			),
			'description' => esc_html__( 'Enter parallax speed ratio if you want to use parallax effect. (Note: Standard value is 1.5, min value is 1. Leave empty if you don\'t want.)', 'porto-functionality' ),
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

			// Background and particle effect
			if( '' !== settings.banner_effect || '' !== settings.particle_effect ) {
				let banner_effectwrapClass = 'banner-effect-wrapper';
				let banner_effectClass = 'banner-effect ';
				let particle_effectClass   = 'particle-effect ';
				if ( '' !== settings.banner_effect ) {
					banner_effectClass += settings.banner_effect ;
				}
				if ( '' !== settings.particle_effect ) {
					particle_effectClass += settings.particle_effect;
				}
				if ( settings.banner_image.url ) {
					let banner_img = '';
					if ( ! settings.particle_effect || settings.banner_effect ) {
						banner_img = 'background-image: url(' + image_url + '); background-size: cover;background-position: center;';
					}
					extra_before_html += '<div class="' + banner_effectwrapClass + '">';
					extra_before_html += '<div class="' + banner_effectClass +'" style="' + banner_img + '">';
					if ( '' !== settings.particle_effect ) {
						extra_before_html += '<div class="' + particle_effectClass + '"></div>';
					}
					extra_before_html += '</div>';
					extra_before_html += '</div>';
				}
			}

			settings.gap = 'no';
			if ( image_url ) {
				extra_before_html += '<img class="porto-ibanner-img' + ( '' !== settings.banner_effect ? ' invisible' : '' ) + '" src="' + image_url + '" />';
			}

			if ('yes' == settings.add_container) {
				extra_attr += ' data-add_container=1';
			}
			extra_container_cls += ' porto-ibanner';
			if( settings.hover_effect ) {
				extra_container_cls += ' ' + settings.hover_effect;
			}
		}

		if (settings.parallax_speed.size) {
			extra_class += ' porto-parallax';
			extra_attr += ' data-parallax-speed=' + parseFloat(settings.parallax_speed.size);
		}

		if (settings.porto_el_cls) {
			extra_class += ' ' + settings.porto_el_cls;
		}

		if (settings.is_toolbox) {
			extra_container_cls += ' shop-loop-before';
		}
	#>
	<?php
		$legacy_enabled = ! porto_elementor_if_dom_optimization();
	if ( $legacy_enabled ) {
		$content = str_replace( '<div class="elementor-container', '<div class="elementor-container{{ extra_container_cls }}', $content );
		$content = str_replace( '<div class="elementor-row">', '{{{ extra_before_html }}}<div{{ extra_attr }} class="elementor-row{{ extra_class }}">{{{ extra_after_html }}}', $content );
	} else {
		$content = str_replace( '<div class="elementor-container elementor-column-gap-{{ settings.gap }}">', '<div{{ extra_attr }} class="elementor-container elementor-column-gap-{{ settings.gap }}{{ extra_container_cls }}{{ extra_class }}">{{{ extra_before_html }}}{{{ extra_after_html }}}', $content );
	}
		echo porto_filter_output( $content );
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
	if ( ! empty( $settings['is_main_header'] ) ) {
		$self->add_render_attribute( '_wrapper', 'class', 'header-main' );
	} elseif ( ! empty( $settings['is_toolbox'] ) ) {
		$self->add_render_attribute( '_wrapper', 'class', 'shop-loop-before' );
	}
	if ( ! empty( $settings['parallax_speed']['size'] ) ) {
		$self->add_render_attribute( '_wrapper', 'data-plugin-parallax', '' );
		$self->add_render_attribute( '_wrapper', 'data-plugin-options', '{"speed": ' . floatval( $settings['parallax_speed']['size'] ) . '}' );

		wp_enqueue_script( 'skrollr' );
	}

	$mpx_attrs = porto_get_mpx_options( $settings );
	if ( $mpx_attrs ) {
		foreach ( $mpx_attrs as $key => $val ) {
			$self->add_render_attribute( '_wrapper', $key, $val );
		}
	}
}
