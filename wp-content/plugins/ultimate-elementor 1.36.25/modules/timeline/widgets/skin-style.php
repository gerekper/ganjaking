<?php
/**
 * UAEL Base Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Timeline\Widgets;

use Elementor\Group_Control_Image_Size;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Base
 */
class Skin_Style {

	/**
	 * Query object
	 *
	 * @since 1.5.2
	 * @var object $query
	 */
	public static $query;

	/**
	 * Query object
	 *
	 * @since 1.5.2
	 * @var object $query_obj
	 */
	public static $query_obj;

	/**
	 * Settings
	 *
	 * @since 1.5.2
	 * @var object $settings
	 */
	public static $settings;

	/**
	 * Node ID of element
	 *
	 * @since 1.5.2
	 * @var object $node_id
	 */
	public static $node_id;

	/**
	 * Rendered Settings
	 *
	 * @since 1.5.2
	 * @var object $_render_attributes
	 */
	public $_render_attributes; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Render settings array for selected skin
	 *
	 * @since 1.5.2
	 * @param string $control_base_id control ID.
	 * @access public
	 */
	public function get_instance_value( $control_base_id ) {
		if ( isset( $settings[ $control_base_id ] ) ) {
			return $settings[ $control_base_id ];
		} else {
			return null;
		}
	}

	/**
	 * Get featured image.
	 *
	 * Returns the featured image HTML wrap.
	 *
	 * @since 1.5.2
	 * @param array $settings object.
	 * @access public
	 */
	public function render_featured_image( $settings ) {

		$settings['post_image_size'] = array(
			'id' => get_post_thumbnail_id(),
		);

		$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'post_image_size' );

		if ( empty( $thumbnail_html ) ) {
			return;
		}
		echo wp_kses_post( $thumbnail_html );
	}

	/**
	 * Get post title.
	 *
	 * Returns the post title HTML wrap.
	 *
	 * @since 1.5.2
	 * @access public
	 */
	public function render_title() {

		echo esc_attr( the_title() ) . '</br>';
	}

	/**
	 * Get post excerpt length.
	 *
	 * Returns the length of Timeline post excerpt.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function uael_timeline_excerpt_length() {
		$settings = self::$settings;
		return $settings['excerpt_length'];
	}

	/**
	 * Get post excerpt end text.
	 *
	 * Returns the string to append to Timeline post excerpt.
	 *
	 * @param string $more returns string.
	 * @since 1.7.0
	 * @access public
	 */
	public function uael_timeline_excerpt_more( $more ) {
		return ' ...';
	}

	/**
	 * Get post excerpt.
	 *
	 * Returns the post excerpt HTML wrap.
	 *
	 * @since 1.5.2
	 * @param array $settings object.
	 * @access public
	 */
	public function render_excerpt( $settings ) {

		$excerpt_length = $settings['excerpt_length'];

		if ( 0 === $excerpt_length ) {
			return;
		}

		add_filter( 'excerpt_length', array( $this, 'uael_timeline_excerpt_length' ), 20 );
		add_filter( 'excerpt_more', array( $this, 'uael_timeline_excerpt_more' ), 20 );

		the_excerpt();

		remove_filter( 'excerpt_length', array( $this, 'uael_excerpt_length_filter' ), 20 );
		remove_filter( 'excerpt_more', array( $this, 'uael_excerpt_more_filter' ), 20 );
	}

	/**
	 * Get post published date.
	 *
	 * Returns the post published date HTML wrap.
	 *
	 * @since 1.5.2
	 * @param array $settings object.
	 * @access public
	 */
	public function render_date( $settings ) {

		echo wp_kses_post( apply_filters( 'uael_timeline_the_date_format', get_the_date(), get_option( 'date_format' ), '', '' ) );
	}

	/**
	 * Get Pagination.
	 *
	 * Returns the Pagination HTML.
	 *
	 * @since 1.5.2
	 * @param array $settings object.
	 * @param array $query object.
	 * @param array $query_obj object.
	 * @access public
	 */
	public function render_pagination( $settings, $query, $query_obj ) {

		if ( 'no' === $settings['timeline_infinite'] ) {
			return;
		}

		// Get current page number.
		$paged       = $query_obj->get_paged();
		$total_pages = $query->max_num_pages;

		// Users can change the limit of no. of pages in infinite load using this filter
		// default is 5.
		$total_pages = apply_filters( 'uael_timeline_infinite_limit', $total_pages = 5 );

		// Return pagination html.
		if ( $total_pages > 1 ) {

			$current_page = $paged;
			if ( ! $current_page ) {
				$current_page = 1;
			}
			$links = paginate_links(
				array(
					'current' => $current_page,
					'total'   => $total_pages,
					'type'    => 'array',
				)
			);
			$class = (
					'yes' === $settings['timeline_infinite']
				) ? 'style=display:none;' : '';

			$id = 'uael-timeline-' . self::$node_id;

			?>
			<nav class="uael-timeline-pagination" id="<?php echo esc_attr( $id ); ?>" <?php echo esc_attr( $class ); ?> role="navigation" aria-label="<?php esc_attr_e( 'Pagination', 'uael' ); ?>">
				<?php echo wp_kses_post( implode( PHP_EOL, $links ) ); ?>
			</nav>
			<?php
		}
	}

	/**
	 * Get Search Box HTML.
	 *
	 * Returns the Search Box HTML.
	 *
	 * @since 1.5.2
	 * @param array $settings object.
	 * @access public
	 */
	public function render_search( $settings ) {
		?>
		<div class="uael-timeline-post-empty">
			<p><?php echo wp_kses_post( $settings['no_results_text'] ); ?></p>
			<?php if ( 'yes' === $settings['show_search_box'] ) { ?>
				<?php get_search_form(); ?>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Get body.
	 *
	 * Returns body.
	 *
	 * @since 1.5.2
	 * @param array $settings object.
	 * @param array $query object.
	 * @param array $query_obj object.
	 * @param array $dynamic object.
	 * @access public
	 */
	public function get_body( $settings, $query, $query_obj, $dynamic ) {

		global $post;

		$count        = 0;
		$index        = 0;
		$is_featured  = false;
		$args         = $query_obj->get_query_posts( $settings );
		$page_id      = \Elementor\Plugin::$instance->documents->get_current()->get_main_id();
		$is_editor    = \Elementor\Plugin::instance()->editor->is_edit_mode();
		$dynamic_date = $settings['post_timeline_date_text'];
		$custom_meta  = '';

		if ( ! $query->have_posts() ) {

			$this->render_search( $settings );
			return;
		}

		if ( 'yes' === $settings['timeline_cards_box_shadow'] ) {
			$this->add_render_attribute( 'timeline_main', 'class', 'uael-timeline-shadow-yes' );
		}
		$this->add_render_attribute( 'timeline_main', 'class', 'uael-timeline-main' );
		$this->add_render_attribute( 'timeline_days', 'class', 'uael-days' );
		$this->add_render_attribute( 'line', 'class', 'uael-timeline__line' );
		$this->add_render_attribute( 'line-inner', 'class', 'uael-timeline__line__inner' );

		if ( ! $is_editor ) {
			if ( 'yes' === $settings['timeline_infinite'] ) {
				$this->add_render_attribute( 'timeline_days', 'class', 'uael-timeline-infinite-load' );
			}
		}
			$count        = 0;
			$current_side = '';
			$per_posts    = $settings['posts_per_page'];
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'timeline_main' ) ); ?>>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'timeline_days' ) ); ?>>
				<?php
				while ( $query->have_posts() ) {
					$query->the_post();
					$post_id = get_the_ID();
					$this->add_render_attribute(
						array(
							'timeline_single_content' => array( 'class' => 'uael-date' ),
						)
					);

					$this->add_render_attribute( 'heading_setting_key', 'class', 'uael-timeline-heading' );

					$this->add_render_attribute( 'card_' . $post_id, 'class', 'timeline-icon-new' );
					$this->add_render_attribute( 'card_' . $post_id, 'class', 'out-view-timeline-icon' );

					$this->add_render_attribute( 'current_' . $post_id, 'class', 'elementor-repeater-item-' . $post_id );
					$this->add_render_attribute( 'current_' . $post_id, 'class', 'uael-timeline-field animate-border' );
					$this->add_render_attribute( 'current_' . $post_id, 'class', 'out-view' );
					$this->add_render_attribute( 'timeline_alignment' . $post_id, 'class', 'uael-day-new' );

					$this->add_render_attribute( 'data_alignment' . $post_id, 'class', 'uael-timeline-widget' );

					$page_no = get_query_var( 'paged' );
					if ( 'yes' === $settings['timeline_infinite'] && 0 !== $page_no ) {
						if ( 0 !== (int) $per_posts % 2 && 0 === (int) $page_no % 2 ) {
							$current_side = ( 0 === $count % 2 ) ? 'Right' : 'Left';
						} else {
							$current_side = ( 0 === $count % 2 ) ? 'Left' : 'Right';
						}
					} else {
						$current_side = ( 0 === $count % 2 ) ? 'Left' : 'Right';
					}

					if ( 'Right' === $current_side ) {
						$this->add_render_attribute( 'timeline_alignment' . $post_id, 'class', 'uael-day-left' );
						$this->add_render_attribute( 'data_alignment' . $post_id, 'class', 'uael-timeline-left' );
					} else {
						$this->add_render_attribute( 'timeline_alignment' . $post_id, 'class', 'uael-day-right' );
						$this->add_render_attribute( 'data_alignment' . $post_id, 'class', 'uael-timeline-right' );
					}

					$this->add_render_attribute( 'timeline_events' . $post_id, 'class', 'uael-events-new' );
					$this->add_render_attribute( 'timeline_events_inner' . $post_id, 'class', 'uael-events-inner-new' );

					$this->add_render_attribute( 'timeline_content' . $post_id, 'class', 'uael-content' );
					?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'current_' . esc_attr( $post_id ) ) ); ?>>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'data_alignment' . esc_attr( $post_id ) ) ); ?>>
							<div class="uael-timeline-marker">
								<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'card_' . esc_attr( $post_id ) ) ); ?>>
									<?php
									if ( UAEL_Helper::is_elementor_updated() ) {
										if ( ! empty( $settings['timeline_all_icon'] ) || ! empty( $settings['new_timeline_all_icon'] ) ) {
											$icon_migrated = isset( $settings['__fa4_migrated']['new_timeline_all_icon'] );
											$icon_is_new   = ! isset( $settings['timeline_all_icon'] );

											if ( $icon_migrated || $icon_is_new ) {

												\Elementor\Icons_Manager::render_icon( $settings['new_timeline_all_icon'], array( 'aria-hidden' => 'true' ) );
											} elseif ( ! empty( $settings['timeline_all_icon'] ) ) {
												?>
												<i class="<?php echo esc_attr( $settings['timeline_all_icon'] ); ?>" aria-hidden="true"></i>
												<?php
											}
										}
									} elseif ( ! empty( $settings['timeline_all_icon'] ) ) {
										?>
										<i class="<?php echo esc_attr( $settings['timeline_all_icon'] ); ?>" aria-hidden="true"></i>
										<?php
									}
									?>
								</span>

							</div>

							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'timeline_alignment' . esc_attr( $post_id ) ) ); ?>>
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'timeline_events' . esc_attr( $post_id ) ) ); ?>>
									<?php if ( 'module' === $settings['post_timeline_cta_type'] ) { ?>
										<a href="<?php the_permalink(); ?>">
									<?php } ?>
										<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'timeline_events_inner' . esc_attr( $post_id ) ) ); ?>>

											<?php if ( 'yes' === $settings['post_thumbnail'] && '' !== get_the_post_thumbnail_url( $post_id ) ) { ?>
												<div class="uael-timeline-featured-img">
													<?php echo wp_kses_post( sanitize_text_field( $this->render_featured_image( $settings ) ) ); ?>
												</div>
											<?php } ?>
											<div class="uael-timeline-date-hide uael-date-inner">
												<div class="inner-date-new">
													<?php if ( '' === $settings['post_timeline_date_type'] ) { ?>
														<p><?php echo wp_kses_post( sanitize_text_field( $this->render_date( $settings ) ) ); ?></p>
													<?php } elseif ( 'updated' === $settings['post_timeline_date_type'] ) { ?>
														<p><?php echo wp_kses_post( get_the_modified_date( '', $post_id ) ); ?></p>
													<?php } elseif ( 'custom' === $settings['post_timeline_date_type'] ) { ?>
														<p>
														<?php
														if ( '' !== $dynamic_date ) {
															echo wp_kses_post( get_post_meta( $post_id, $dynamic_date, 'true' ) );
														} else {
															$custom_meta = apply_filters( 'uael_timeline_date_content', $post_id, $settings );
															echo esc_attr( $custom_meta );
														}
														?>
														</p>
													<?php } ?>
												</div>
											</div>
											<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'timeline_content' . esc_attr( $post_id ) ) ); ?>>
												<?php
												if ( 'yes' === $settings['post_title'] && '' !== get_the_title( $post_id ) ) {
													$heading_size_tag = UAEL_Helper::validate_html_tag( $settings['timeline_heading_tag'] );
													?>
													<div class="uael-timeline-heading-text">
														<<?php echo esc_attr( $heading_size_tag ); ?> <?php echo wp_kses_post( sanitize_text_field( $this->get_render_attribute_string( 'heading_setting_key' ) ) ); ?>><?php echo wp_kses_post( sanitize_text_field( $this->render_title() ) ); ?></<?php echo esc_attr( $heading_size_tag ); ?>>
													</div>
												<?php } ?>
												<?php if ( 'yes' === $settings['post_excerpt'] ) { ?>
													<div class="uael-timeline-desc-content"><?php echo wp_kses_post( sanitize_text_field( $this->render_excerpt( $settings ) ) ); ?></div>
												<?php } ?>

												<?php if ( 'link' === $settings['post_timeline_cta_type'] ) { ?>
													<div class="uael-timeline-link-style">
														<a href="<?php the_permalink(); ?>" class="uael-timeline-link">
															<span><?php echo wp_kses_post( $dynamic['post_timeline_link_text'] ); ?></span>
														</a>
													</div>
												<?php } ?>
											</div>
											<?php if ( 'yes' === $settings['show_card_arrow'] ) { ?>
												<div class="uael-timeline-arrow"></div>
											<?php } ?>
										</div>
									<?php if ( 'module' === $settings['post_timeline_cta_type'] ) { ?>
										</a>
									<?php } ?>
								</div>
							</div>
							<?php if ( 'center' === $settings['timeline_align'] ) { ?>
								<div class="uael-timeline-date-new">
									<div class="uael-date-new">
										<div class="inner-date-new">
											<?php if ( '' === $settings['post_timeline_date_type'] ) { ?>
												<p><?php echo wp_kses_post( sanitize_text_field( $this->render_date( $settings ) ) ); ?></p>
											<?php } elseif ( 'updated' === $settings['post_timeline_date_type'] ) { ?>
												<p><?php echo wp_kses_post( get_the_modified_date( '', $post_id ) ); ?></p>
											<?php } elseif ( 'custom' === $settings['post_timeline_date_type'] ) { ?>
												<p>
												<?php
												if ( '' !== $dynamic_date ) {
													echo wp_kses_post( get_post_meta( $post_id, $dynamic_date, 'true' ) );
												} else {
													$custom_meta = apply_filters( 'uael_timeline_date_content', $post_id, $settings );
													echo esc_attr( $custom_meta );
												}
												?>
												</p>
											<?php } ?>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<?php
					++$count;
					++$index;
					?>
					<?php
				}
				wp_reset_postdata();
				?>
			</div>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'line' ) ); ?>>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'line-inner' ) ); ?>></div>
			</div>
			<?php
			if ( 'yes' === $settings['timeline_infinite'] ) {
				$this->render_pagination( $settings, $query, $query_obj );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Add render attribute.
	 *
	 * Used to add attributes to a specific HTML element.
	 *
	 * The HTML tag is represented by the element parameter, then you need to
	 * define the attribute key and the attribute key. The final result will be:
	 * `<element attribute_key="attribute_value">`.
	 *
	 * Example usage:
	 *
	 * `$this->add_render_attribute( 'wrapper', 'class', 'custom-widget-wrapper-class' );`
	 * `$this->add_render_attribute( 'widget', 'id', 'custom-widget-id' );`
	 * `$this->add_render_attribute( 'button', [ 'class' => 'custom-button-class', 'id' => 'custom-button-id' ] );`
	 *
	 * @since 1.5.2
	 * @access public
	 *
	 * @param array|string $element   The HTML element.
	 * @param array|string $key       Optional. Attribute key. Default is null.
	 * @param array|string $value     Optional. Attribute value. Default is null.
	 * @param bool         $overwrite Optional. Whether to overwrite existing
	 *                                attribute. Default is false, not to overwrite.
	 *
	 * @return Element_Base Current instance of the element.
	 */
	public function add_render_attribute( $element, $key = null, $value = null, $overwrite = false ) {
		if ( is_array( $element ) ) {
			foreach ( $element as $element_key => $attributes ) {
				$this->add_render_attribute( $element_key, $attributes, null, $overwrite );
			}

			return $this;
		}

		if ( is_array( $key ) ) {
			foreach ( $key as $attribute_key => $attributes ) {
				$this->add_render_attribute( $element, $attribute_key, $attributes, $overwrite );
			}

			return $this;
		}

		if ( empty( $this->_render_attributes[ $element ][ $key ] ) ) {
			$this->_render_attributes[ $element ][ $key ] = array();
		}

		settype( $value, 'array' );

		if ( $overwrite ) {
			$this->_render_attributes[ $element ][ $key ] = $value;
		} else {
			$this->_render_attributes[ $element ][ $key ] = array_merge( $this->_render_attributes[ $element ][ $key ], $value );
		}

		return $this;
	}

	/**
	 * Get render attribute string.
	 *
	 * Used to retrieve the value of the render attribute.
	 *
	 * @since 1.5.2
	 * @access public
	 *
	 * @param array|string $element The element.
	 *
	 * @return string Render attribute string, or an empty string if the attribute
	 *                is empty or not exist.
	 */
	public function get_render_attribute_string( $element ) {
		if ( empty( $this->_render_attributes[ $element ] ) ) {
			return '';
		}

		$render_attributes = $this->_render_attributes[ $element ];

		$attributes = array();

		foreach ( $render_attributes as $attribute_key => $attribute_values ) {
			$attributes[] = sprintf( '%1$s="%2$s"', $attribute_key, esc_attr( implode( ' ', $attribute_values ) ) );
		}

		return implode( ' ', $attributes );
	}

	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @param array  $settings Settings Object.
	 * @param string $node_id Node ID.
	 * @param array  $dynamic object.
	 * @since 1.5.2
	 * @access public
	 */
	public function render( $settings, $node_id, $dynamic ) {

		self::$settings = $settings;
		$dynamic        = $dynamic;
		self::$node_id  = $node_id;
		$query_obj      = new Build_Post_Query( $settings, '' );
		$query_obj->query_posts();
		$query = $query_obj->get_query();

		$this->get_body( $settings, $query, $query_obj, $dynamic );
	}
}
