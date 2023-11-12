<?php
/**
 * UAEL Feed Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Posts\TemplateBlocks;

use UltimateElementor\Modules\Posts\TemplateBlocks\Skin_Style;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Feed
 */
class Skin_Feed extends Skin_Style {


	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get Classes array for wrapper class.
	 *
	 * Returns the array for wrapper class.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_wrapper_classes() {

		$classes = array(
			'uael-post-grid__inner',
			'uael-post__columns-1',
			'uael-post__columns-tablet-1',
			'uael-post__columns-mobile-1',
		);

		if ( 'infinite' === $this->get_instance_value( 'pagination' ) ) {
			$classes[] = 'uael-post-infinite-scroll';
			$classes[] = 'uael-post-infinite__event-' . $this->get_instance_value( 'infinite_event' );
		}

		return $classes;
	}

	/**
	 * Get Classes array for outer wrapper class.
	 *
	 * Returns the array for outer wrapper class.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_outer_wrapper_classes() {

		return array(
			'uael-post-grid',
			'uael-posts',
		);
	}

	/**
	 * Get Pagination.
	 *
	 * Returns the Pagination HTML.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_pagination() {

		$settings = self::$settings;

		if ( 'none' === $this->get_instance_value( 'pagination' ) ) {
			return;
		}

		// Get current page number.
		$paged = self::$query_obj->get_paged();

		$query = self::$query;

		$total_pages = $query->max_num_pages;

		if ( 'infinite' !== $this->get_instance_value( 'pagination' ) ) {

			if ( '' !== $this->get_instance_value( 'max_pages' ) ) {
				$total_pages = min( $this->get_instance_value( 'max_pages' ), $total_pages );
			}
		}

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

			$next_page_number = $current_page + 1;
			$next_page_number = ( ( $next_page_number ) <= $total_pages ) ? $next_page_number : '';

			$class = (
					'infinite' === $this->get_instance_value( 'pagination' )
				) ? 'style="display:none;"' : '';
			?>
			<nav class="uael-grid-pagination" <?php echo wp_kses_post( $class ); ?> role="navigation" aria-label="<?php esc_attr_e( 'Pagination', 'uael' ); ?>" data-total="<?php echo esc_attr( $total_pages ); ?>" data-next-page="<?php echo esc_attr( $next_page_number ); ?>">
				<?php
				if ( 'infinite' !== $this->get_instance_value( 'pagination' ) ) {
					echo wp_kses_post( implode( PHP_EOL, $links ) );
				}
				?>

			</nav>

			<?php

			if (
				'infinite' === $this->get_instance_value( 'pagination' ) &&
				'click' === $this->get_instance_value( 'infinite_event' )
			) {
				?>
			<div class="uael-post__load-more-wrap">
				<a class="uael-post__load-more elementor-button" href="javascript:void(0);">
					<span><?php echo wp_kses_post( $this->get_instance_value( 'load_more_text' ) ); ?></span>
				</a>
			</div>
				<?php
			}
		}
	}

	/**
	 * Get Filters.
	 *
	 * Returns the Filter HTML.
	 *
	 * @since 1.7.1
	 * @access public
	 */
	public function render_filters() {

		$settings       = self::$settings;
		$skin           = self::$skin;
		$tab_responsive = '';

		if ( 'yes' === $this->get_instance_value( 'tabs_dropdown' ) ) {
			$tab_responsive = ' uael-posts-tabs-dropdown';
		}

		if ( 'yes' !== $this->get_instance_value( 'show_filters' ) || 'main' === $settings['query_type'] ) {
			return;
		}

		$filters = $this->get_filter_values();
		$filters = apply_filters( 'uael_posts_filterable_tabs', $filters, $settings );
		$all     = $this->get_instance_value( 'filters_all_text' );

		$all_text = ( 'All' === $all || '' === $all ) ? esc_attr__( 'All', 'uael' ) : $all;

		?>
		<div class="uael-post__header-filters-wrap<?php echo esc_attr( $tab_responsive ); ?>">
			<ul class="uael-post__header-filters" aria-label="<?php esc_attr_e( 'Taxonomy Filter', 'uael' ); ?>">
				<li class="uael-post__header-filter uael-filter__current" data-filter="*"><?php echo wp_kses_post( $all_text ); ?></li>
				<?php foreach ( $filters as $key => $value ) { ?>
				<li class="uael-post__header-filter" data-filter="<?php echo '.' . esc_attr( $value->slug ); ?>" tabindex="0"><?php echo esc_html( $value->name ); ?></li>
				<?php } ?>
			</ul>

			<?php if ( 'yes' === $this->get_instance_value( 'tabs_dropdown' ) ) { ?>
				<div class="uael-filters-dropdown">
					<div class="uael-filters-dropdown-button"><?php echo wp_kses_post( $all_text ); ?><i class="fa fa-angle-down"></i></div>
					<ul class="uael-filters-dropdown-list uael-post__header-filters">
						<li class="uael-filters-dropdown-item uael-post__header-filter uael-filter__current" data-filter="*"><?php echo wp_kses_post( $all_text ); ?></li>
						<?php foreach ( $filters as $key => $value ) { ?>
						<li class="uael-filters-dropdown-item uael-post__header-filter" data-filter="<?php echo '.' . esc_attr( $value->slug ); ?>"><?php echo esc_html( $value->name ); ?></li>
						<?php } ?>
					</ul>
				</div>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Render Separator HTML.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_separator() {

		$settings = self::$settings;

		do_action( 'uael_single_post/skin_feed/before_separator', get_the_ID(), $settings );

		printf( '<div class="uael-post__separator"></div>' );

		do_action( 'uael_single_post/skin_feed/after_separator', get_the_ID(), $settings );
	}
}

