<?php
/**
 * UAEL Base Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Posts\TemplateBlocks;

use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Base
 */
abstract class Skin_Style {


	/**
	 * Query object
	 *
	 * @since 1.7.0
	 * @var object $query
	 */
	public static $query;

	/**
	 * Query object
	 *
	 * @since 1.7.0
	 * @var object $query_obj
	 */
	public static $query_obj;

	/**
	 * Settings
	 *
	 * @since 1.7.0
	 * @var object $settings
	 */
	public static $settings;

	/**
	 * Skin
	 *
	 * @since 1.7.0
	 * @var object $skin
	 */
	public static $skin;

	/**
	 * Node ID of element
	 *
	 * @since 1.7.0
	 * @var object $node_id
	 */
	public static $node_id;

	/**
	 * Rendered Settings
	 *
	 * @since 1.7.0
	 * @var object $_render_attributes
	 */
	public $_render_attributes; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Get post title.
	 *
	 * Returns the post title HTML wrap.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_featured_title() {

		$settings = self::$settings;

		do_action( 'uael_single_post_before_title', get_the_ID(), $settings );
		?>
		<h3 class="uael-post__title">

		<?php if ( $this->get_instance_value( 'link_title' ) ) { ?>

			<?php $target = ( 'yes' === $this->get_instance_value( 'link_title_new' ) ) ? '_blank' : '_self'; ?>
			<a href="<?php echo wp_kses_post( apply_filters( 'uael_single_post_link', get_the_permalink(), get_the_ID(), $settings ) ); ?>" target="<?php echo esc_attr( $target ); ?>">
				<?php the_title(); ?>
			</a>

		<?php } else { ?>
			<?php the_title(); ?>
		<?php } ?>
		</h3>
		<?php

		do_action( 'uael_single_post_after_title', get_the_ID(), $settings );
	}

	/**
	 * Get post meta.
	 *
	 * Returns the post meta HTML wrap.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_featured_meta_data() {

		$settings       = self::$settings;
		$meta_tag_value = $this->get_instance_value( 'meta_tag' );
		$meta_tag       = UAEL_Helper::validate_html_tag( $meta_tag_value );

		if ( 'yes' !== $this->get_instance_value( 'show_meta' ) ) {
			return;
		}

		do_action( 'uael_single_post_before_meta', get_the_ID(), $settings );

		$_f_meta = $this->get_instance_value( '_f_meta' );

		$sequence = apply_filters( 'uael_post_meta_sequence', array( 'author', 'date', 'comments', 'cat', 'tag' ) );
		?>

		<<?php echo wp_kses_post( $meta_tag ); ?> class="uael-post__meta-data">

		<?php
		foreach ( $sequence as $key => $seq ) {

			$post_type = $settings['post_type_filter'];

			switch ( $seq ) {
				case 'author':
					if ( in_array( 'author', $_f_meta, true ) ) {
						$this->render_author();
					}
					break;

				case 'date':
					if ( in_array( 'date', $_f_meta, true ) ) {
						$this->render_date();
					}
					break;

				case 'comments':
					if ( in_array( 'comment', $_f_meta, true ) ) {
						$this->render_comments();
					}
					break;

				case 'cat':
					if ( 'custom' === $settings['query_type'] ) {
						if ( 'post' !== $post_type ) {
							break;
						}
					}

					if ( in_array( 'category', $_f_meta, true ) ) {
						$terms  = wp_get_post_terms( get_the_ID(), 'category' );
						$terms  = apply_filters( 'uael_posts_meta_category', $terms, $settings );
						$prefix = 'cat';
						$this->get_meta_html_by_prefix( $terms, $prefix );
					}
					break;

				case 'tag':
					if ( 'custom' === $settings['query_type'] ) {
						if ( 'post' !== $post_type ) {
							break;
						}
					}

					if ( in_array( 'tag', $_f_meta, true ) ) {
						$terms  = wp_get_post_terms( get_the_ID(), 'post_tag' );
						$prefix = 'tag';
						$this->get_meta_html_by_prefix( $terms, $prefix );
					}
					break;
			}
		}
		?>

		</<?php echo wp_kses_post( $meta_tag ); ?>>

		<?php

		do_action( 'uael_single_post_after_meta', get_the_ID(), $settings );
	}

	/**
	 * Get post excerpt length.
	 *
	 * Returns the length of post excerpt.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function uael_featured_excerpt_length_filter() {
		return $this->get_instance_value( '_f_excerpt_length' );
	}

	/**
	 * Get post excerpt.
	 *
	 * Returns the post excerpt HTML wrap.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_featured_excerpt() {

		$settings          = self::$settings;
		$_f_excerpt_length = $this->get_instance_value( '_f_excerpt_length' );

		if ( 0 === $_f_excerpt_length ) {
			return;
		}

		add_filter( 'excerpt_length', array( $this, 'uael_featured_excerpt_length_filter' ), 20 );
		add_filter( 'excerpt_more', array( $this, 'uael_excerpt_more_filter' ), 20 );

		do_action( 'uael_single_post_before_excerpt', get_the_ID(), $settings );
		?>
		<div class="uael-post__excerpt">
			<?php the_excerpt(); ?>
		</div>
		<?php

		remove_filter( 'excerpt_length', array( $this, 'uael_featured_excerpt_length_filter' ), 20 );
		remove_filter( 'excerpt_more', array( $this, 'uael_excerpt_more_filter' ), 20 );

		do_action( 'uael_single_post_after_excerpt', get_the_ID(), $settings );
	}

	/**
	 * Get no image class.
	 *
	 * Returns the no image class.
	 *
	 * @since 1.7.2
	 * @access public
	 */
	public function get_no_image_class() {

		if ( 'none' === $this->get_instance_value( 'image_position' ) ) {
			return 'uael-post__noimage';
		}

		return ( ! get_the_post_thumbnail_url() ) ? 'uael-post__noimage' : '';
	}

	/**
	 * Get no image class.
	 *
	 * Returns the no image class.
	 *
	 * @since 1.27.0
	 * @access public
	 */
	public function get_thumbnail_no_image_class() {

		if ( 'none' === $this->get_instance_value( 'image_position' ) ) {
			return 'uael-post-wrapper__noimage';
		}

		return ( ! get_the_post_thumbnail_url() ) ? 'uael-post-wrapper__noimage' : '';
	}

	/**
	 * Get featured image.
	 *
	 * Returns the featured image HTML wrap.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_featured_image() {

		$settings = self::$settings;

		if ( 'none' === $this->get_instance_value( 'image_position' ) ) {
			return;
		}

		$settings['image'] = array(
			'id' => get_post_thumbnail_id(),
		);

		$settings['image_size'] = $this->get_instance_value( 'image_size' );

		$settings['image_custom_dimension'] = $this->get_instance_value( 'image_custom_dimension' );

		$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings );

		if ( empty( $thumbnail_html ) ) {
			return;
		}

		do_action( 'uael_single_post_before_thumbnail', get_the_ID(), $settings );

		if ( 'yes' === $this->get_instance_value( 'link_img' ) ) {
			$href   = apply_filters( 'uael_single_post_link', get_the_permalink(), get_the_ID(), $settings );
			$target = ( 'yes' === $this->get_instance_value( 'link_new_tab' ) ) ? '_blank' : '_self';
			$this->add_render_attribute( 'img_link' . get_the_ID(), 'href', $href );
			$this->add_render_attribute( 'img_link' . get_the_ID(), 'target', $target );
		}

		$this->add_render_attribute( 'img_link' . get_the_ID(), 'title', get_the_title() );
		?>
		<div class="uael-post__thumbnail">
			<?php if ( $this->get_instance_value( 'link_img' ) ) { ?>

			<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'img_link' . get_the_ID() ) ); ?>><?php echo wp_kses_post( $thumbnail_html ); ?></a>
			<?php } else { ?>
				<?php echo wp_kses_post( $thumbnail_html ); ?>
				<?php
			}
			if ( 'background' !== $this->get_instance_value( 'image_position' ) ) {
				$this->render_terms( 'media' );
			}
			?>
		</div>
		<?php
		do_action( 'uael_single_post_after_thumbnail', get_the_ID(), $settings );
	}

	/**
	 * Get post title.
	 *
	 * Returns the post title HTML wrap.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_title() {

		$settings        = self::$settings;
		$title_tag_value = $this->get_instance_value( 'title_tag' );
		$title_tag       = UAEL_Helper::validate_html_tag( $title_tag_value );

		do_action( 'uael_single_post_before_title', get_the_ID(), $settings );
		?>
		<<?php echo wp_kses_post( $title_tag ); ?> class="uael-post__title">

		<?php if ( $this->get_instance_value( 'link_title' ) ) { ?>

			<?php $target = ( 'yes' === $this->get_instance_value( 'link_title_new' ) ) ? '_blank' : '_self'; ?>
			<a href="<?php echo wp_kses_post( apply_filters( 'uael_single_post_link', get_the_permalink(), get_the_ID(), $settings ) ); ?>" target="<?php echo esc_attr( $target ); ?>">
				<?php the_title(); ?>
			</a>

		<?php } else { ?>
			<?php the_title(); ?>
		<?php } ?>
		</<?php echo wp_kses_post( $title_tag ); ?>>
		<?php

		do_action( 'uael_single_post_after_title', get_the_ID(), $settings );
	}

	/**
	 * Get post meta.
	 *
	 * Returns the post meta HTML wrap.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_meta_data() {

		$settings       = self::$settings;
		$meta_tag_value = $this->get_instance_value( 'meta_tag' );
		$meta_tag       = UAEL_Helper::validate_html_tag( $meta_tag_value );

		if ( 'yes' === $this->get_instance_value( 'show_meta' ) ) {

			do_action( 'uael_single_post_before_meta', get_the_ID(), $settings );

			$sequence = apply_filters( 'uael_post_meta_sequence', array( 'author', 'date', 'comments', 'cat', 'tag' ) );
			?>
			<<?php echo wp_kses_post( $meta_tag ); ?> class="uael-post__meta-data">
			<?php
			if ( $this->get_instance_value( 'show_meta' ) ) {

				foreach ( $sequence as $key => $seq ) {

					$post_type = $settings['post_type_filter'];

					switch ( $seq ) {
						case 'author':
							if ( $this->get_instance_value( 'show_author' ) ) {
								$this->render_author();
							}
							break;

						case 'date':
							if ( $this->get_instance_value( 'show_date' ) ) {
								$this->render_date();
							}
							break;

						case 'comments':
							if ( $this->get_instance_value( 'show_comments' ) ) {
								$this->render_comments();
							}
							break;

						case 'cat':
							if ( 'custom' === $settings['query_type'] ) {
								if ( 'post' !== $post_type ) {
									break;
								}
							}

							if ( $this->get_instance_value( 'show_categories' ) === 'yes' ) {
								$terms  = wp_get_post_terms( get_the_ID(), 'category' );
								$terms  = apply_filters( 'uael_posts_meta_category', $terms, $settings );
								$prefix = 'cat';
								$this->get_meta_html_by_prefix( $terms, $prefix );
							}
							break;

						case 'tag':
							if ( 'custom' === $settings['query_type'] ) {
								if ( 'post' !== $post_type ) {
									break;
								}
							}

							if ( $this->get_instance_value( 'show_tags' ) === 'yes' ) {
								$terms  = wp_get_post_terms( get_the_ID(), 'post_tag' );
								$prefix = 'tag';
								$this->get_meta_html_by_prefix( $terms, $prefix );
							}
							break;
					}
				}
			}
		}
		?>
		</<?php echo wp_kses_post( $meta_tag ); ?>>
		<?php
		do_action( 'uael_single_post_after_meta', get_the_ID(), $settings );
	}

	/**
	 * Get post author.
	 *
	 * Returns the post author HTML wrap.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_author() {

		$settings = self::$settings;

		$unlink_meta = $this->get_instance_value( 'link_meta' );

		do_action( 'uael_single_post_before_author', get_the_ID(), $settings );
		?>
		<span class="uael-post__author">
			<?php
			$icon     = $this->get_instance_value( 'show_author_icon' );
			$new_icon = $this->get_instance_value( 'new_show_author_icon' );

			if ( UAEL_Helper::is_elementor_updated() ) {

				$author_migrated      = isset( $settings['__fa4_migrated'][ self::$skin . '_new_show_author_icon' ] );
				$author_icon_is_empty = ! isset( $icon );
				?>

					<?php if ( ! empty( $icon ) || ! empty( $new_icon ) ) { ?>
						<?php
						if ( $author_migrated || $author_icon_is_empty ) {
							\Elementor\Icons_Manager::render_icon( $new_icon, array( 'aria-hidden' => 'true' ) );
						} elseif ( ! empty( $icon ) ) {
							?>
							<i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
						<?php } ?>
					<?php } ?>
				<?php } elseif ( ! empty( $icon ) ) { ?>
					<i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
				<?php } ?>

			<?php
			if ( 'yes' === $this->get_instance_value( 'link_meta' ) ) {
				the_author_posts_link();
			} else {
				the_author();
			}
			?>
		</span>
		<?php
		do_action( 'uael_single_post_after_author', get_the_ID(), $settings );
	}

	/**
	 * Get post published date.
	 *
	 * Returns the post published date HTML wrap.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_date() {

		$settings = self::$settings;

		do_action( 'uael_single_post_before_date', get_the_ID(), $settings );
		?>
		<span class="uael-post__date">
			<?php
			$icon     = $this->get_instance_value( 'show_date_icon' );
			$new_icon = $this->get_instance_value( 'new_show_date_icon' );

			if ( UAEL_Helper::is_elementor_updated() ) {

				$date_icon_migrated = isset( $settings['__fa4_migrated'][ self::$skin . '_new_show_date_icon' ] );
				$date_icon_is_empty = ! isset( $icon );
				?>
				<?php if ( ! empty( $icon ) || ! empty( $new_icon ) ) { ?>
						<?php
						if ( $date_icon_migrated || $date_icon_is_empty ) {
							\Elementor\Icons_Manager::render_icon( $new_icon, array( 'aria-hidden' => 'true' ) );
						} elseif ( ! empty( $icon ) ) {
							?>
							<i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
						<?php } ?>
					<?php } ?>
				<?php } elseif ( ! empty( $icon ) ) { ?>
					<i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
				<?php } ?>

			<?php echo wp_kses_post( apply_filters( 'uael_post_the_date_format', get_the_date(), get_the_ID(), get_option( 'date_format' ), '', '' ) ); ?>
		</span>
		<?php
		do_action( 'uael_single_post_after_date', get_the_ID(), $settings );
	}

	/**
	 * Get post related comments.
	 *
	 * Returns the post related comments HTML wrap.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_comments() {

		$settings = self::$settings;

		$icon     = $this->get_instance_value( 'show_comments_icon' );
		$new_icon = $this->get_instance_value( 'new_show_comments_icon' );

		do_action( 'uael_single_post_before_comments', get_the_ID(), $settings );

		?>
		<span class="uael-post__comments">
			<?php if ( UAEL_Helper::is_elementor_updated() ) { ?>
				<?php if ( ! empty( $icon ) || ! empty( $new_icon ) ) { ?>
					<?php
					$comments_migrated      = isset( $settings['__fa4_migrated'][ self::$skin . '_new_show_comments_icon' ] );
					$comments_icon_is_empty = ! isset( $icon );
					if ( $comments_migrated || $comments_icon_is_empty ) {
						\Elementor\Icons_Manager::render_icon( $new_icon, array( 'aria-hidden' => 'true' ) );
					} elseif ( ! empty( $icon ) ) {
						?>
						<i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
					<?php } ?>
				<?php } ?>
			<?php } elseif ( ! empty( $icon ) ) { ?>
					<i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
			<?php } ?>
			<?php comments_number(); ?>
		</span>
		<?php
		do_action( 'uael_single_post_after_comments', get_the_ID(), $settings );
	}

	/**
	 * Get post related terms.
	 *
	 * Returns the post related terms HTML wrap.
	 *
	 * @param string $position Position value of term.
	 * @since 1.7.0
	 * @access public
	 */
	public function render_terms( $position ) {

		$settings = self::$settings;

		if ( $position !== $this->get_instance_value( 'terms_position' ) ) {
			return;
		}

		$this->render_term_html();
	}

	/**
	 * Get post related terms html.
	 *
	 * Returns the post related terms HTML wrap.
	 *
	 * @param array  $terms Terms array.
	 * @param string $prefix Prefix cat/tag.
	 * @since 1.7.0
	 * @access public
	 */
	public function get_meta_html_by_prefix( $terms, $prefix ) {

		$settings = self::$settings;

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return;
		}

		$num = $this->get_instance_value( $prefix . '_meta_max_terms' );

		if ( '' !== $num ) {
			$terms = array_slice( $terms, 0, $num );
		}

		$icon     = $this->get_instance_value( $prefix . '_meta_show_term_icon' );
		$new_icon = $this->get_instance_value( 'new_' . $prefix . '_meta_show_term_icon' );

		$link_meta = apply_filters( 'uael_link_taxomony_badge', $this->get_instance_value( 'link_meta' ) );

		if ( 'yes' === $link_meta ) {
			$format = ' <a href="%2$s" class="uael-listing__terms-link" id="uael-post-term-%3$s" aria-labelledby="uael-post-term-%3$s">%1$s</a>';
		} else {
			$format = ' <span class="uael-listing__terms-link">%1$s</span>';
		}

		$result = '';

		if ( UAEL_Helper::is_elementor_updated() ) {
			if ( ! empty( $icon ) || ! empty( $new_icon ) ) {

				$meta_migrated      = isset( $settings['__fa4_migrated'][ self::$skin . '_new_' . $prefix . '_meta_show_term_icon' ] );
				$meta_icon_is_empty = ! isset( $icon );

				if ( $meta_migrated || $meta_icon_is_empty ) {
					ob_start();
					\Elementor\Icons_Manager::render_icon( $new_icon, array( 'aria-hidden' => 'true' ) );
					$result .= ob_get_clean();
				} elseif ( ! empty( $icon ) ) {
					$result .= '<i class="' . $icon . '" aria-hidden="true"></i>';
				}
			}
		} elseif ( ! empty( $icon ) ) {
			$result .= '<i class="' . $icon . '" aria-hidden="true"></i>';
		}

		foreach ( $terms as $term ) {
			$term_name = $term->slug;
			$result   .= sprintf( $format, $term->name, get_term_link( (int) $term->term_id ), strtolower( $term_name ) );
		}

		do_action( 'uael_single_post_before_content_terms', get_the_ID(), $settings );

		printf( '<span class="uael-post__terms-meta uael-post__terms-meta-%s">%s</span>', esc_attr( $prefix ), $result );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'uael_single_post_after_content_terms', get_the_ID(), $settings );
	}



	/**
	 * Get post related terms.
	 *
	 * Returns the post related terms HTML wrap.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_term_html() {

		$settings   = self::$settings;
		$skin       = self::$skin;
		$terms_show = '';

		if ( 'feed' === $skin || 'news' === $skin ) {
			if ( 'yes' !== $this->get_instance_value( 'show_taxonomy' ) ) {
				return;
			}
		}

		$taxonomies_to_exclude = array( 'optional', 'translation_priority' );

		if ( 'post' === $settings['post_type_filter'] ) {
			$terms_show = $this->get_instance_value( 'terms_to_show' );
		} else {
			$terms_show = array_diff( get_taxonomies( '', 'names' ), $taxonomies_to_exclude );
		}

		$terms = wp_get_post_terms( get_the_ID(), $terms_show );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return;
		}

		$num = $this->get_instance_value( 'max_terms' );

		if ( '' !== $num ) {
			$terms = array_slice( $terms, 0, $num );
		}

		$terms = apply_filters( 'uael_posts_tax_filter', $terms );

		$link_meta = apply_filters( 'uael_link_taxomony_badge', $this->get_instance_value( 'link_meta' ) );

		if ( 'yes' === $link_meta ) {
			$format = '<a href="%2$s" class="uael-listing__terms-link" id="uael-post-term-%3$s" aria-labelledby="uael-post-term-%3$s">%1$s</a>';
		} else {
			$format = '<span class="uael-listing__terms-link">%1$s</span>';
		}

		$result = '';

		$icon     = $this->get_instance_value( 'show_term_icon' );
		$new_icon = $this->get_instance_value( 'new_show_term_icon' );

		if ( UAEL_Helper::is_elementor_updated() ) {

			$terms_migrated      = isset( $settings['__fa4_migrated'][ self::$skin . '_new_show_term_icon' ] );
			$terms_icon_is_empty = ! isset( $icon );

			if ( ! empty( $icon ) || ! empty( $new_icon ) ) {
				if ( $terms_migrated || $terms_icon_is_empty ) {
					ob_start();
					\Elementor\Icons_Manager::render_icon( $new_icon, array( 'aria-hidden' => 'true' ) );
					$result .= ob_get_clean();
				} elseif ( ! empty( $icon ) ) {
					$result .= '<i class="' . $icon . '" aria-hidden="true"></i>';
				}
			}
		} elseif ( ! empty( $icon ) ) {
			$result .= '<i class="' . $icon . '" aria-hidden="true"></i>';
		}

		foreach ( $terms as $term ) {
			$term_name = $term->slug;
			$result   .= sprintf( $format, $term->name, get_term_link( (int) $term->term_id ), strtolower( $term_name ) );
		}
		do_action( 'uael_single_post_before_terms', get_the_ID(), $settings );

		printf( '<span class="uael-post__terms">%s</span>', $result );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'uael_single_post_after_terms', get_the_ID(), $settings );
	}

	/**
	 * Get post excerpt length.
	 *
	 * Returns the length of post excerpt.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function uael_excerpt_length_filter() {
		return $this->get_instance_value( 'excerpt_length' );
	}

	/**
	 * Get post excerpt end text.
	 *
	 * Returns the string to append to post excerpt.
	 *
	 * @param string $more returns string.
	 * @since 1.7.0
	 * @access public
	 */
	public function uael_excerpt_more_filter( $more ) {
		return ' ...';
	}

	/**
	 * Get post excerpt.
	 *
	 * Returns the post excerpt HTML wrap.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_excerpt() {

		$settings        = self::$settings;
		$_excerpt_length = $this->get_instance_value( 'excerpt_length' );

		if ( 0 === $_excerpt_length ) {
			return;
		}

		add_filter( 'excerpt_length', array( $this, 'uael_excerpt_length_filter' ), 20 );
		add_filter( 'excerpt_more', array( $this, 'uael_excerpt_more_filter' ), 20 );

		do_action( 'uael_single_post_before_excerpt', get_the_ID(), $settings );
		?>

		<div class="uael-post__excerpt">
			<?php 
				$excerpt        = get_the_excerpt();
				$words          = preg_split( '/\s+/', $excerpt );
				$excerpt_length = (int) $_excerpt_length;
				$excerpt_length = max( $excerpt_length, 25 );
				$trim_excerpt   = implode( ' ', array_slice( $words, 0, $excerpt_length ) );
			if ( count( $words ) > $excerpt_length ) {
				$trim_excerpt .= apply_filters( 'excerpt_more', '...' );
			}
			$allowed_tags           = wp_kses_allowed_html( 'post' );
			$allowed_tags['iframe'] = array(
				'src'             => true,
				'width'           => true,
				'height'          => true,
				'frameborder'     => true,
				'allowfullscreen' => true,
			);
			echo wp_kses( $trim_excerpt, $allowed_tags );
			?>
		</div>

		<?php

		remove_filter( 'excerpt_length', array( $this, 'uael_excerpt_length_filter' ), 20 );
		remove_filter( 'excerpt_more', array( $this, 'uael_excerpt_more_filter' ), 20 );

		do_action( 'uael_single_post_after_excerpt', get_the_ID(), $settings );
	}

	/**
	 * Get post call to action.
	 *
	 * Returns the post call to action HTML wrap.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_read_more() {

		$settings = self::$settings;

		if ( 'yes' === $this->get_instance_value( 'show_cta' ) ) {

			do_action( 'uael_single_post_before_cta', get_the_ID(), $settings );

			$this->add_render_attribute(
				'icon' . get_the_ID(),
				'class',
				array(
					'elementor-button-icon',
					'elementor-align-icon-' . $this->get_instance_value( 'cta_icon_align' ),
				)
			);

			$this->add_render_attribute(
				'cta_link' . get_the_ID(),
				array(
					'class'           => array(
						'uael-post__read-more',
						'elementor-button',
					),
					'href'            => apply_filters( 'uael_single_post_link', get_the_permalink(), get_the_ID(), $settings ),
					'target'          => ( 'yes' === $this->get_instance_value( 'cta_new_tab' ) ) ? '_blank' : '_self',
					'aria-labelledby' => 'uael-post-' . get_the_ID(),
				)
			);

			?>
			<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'cta_link' . get_the_ID() ) ); ?>>
					<span class="elementor-button-content-wrapper">
						<?php
						$icon     = $this->get_instance_value( 'cta_icon' );
						$new_icon = $this->get_instance_value( 'new_cta_icon' );

						if ( UAEL_Helper::is_elementor_updated() ) {
							if ( ! empty( $icon ) || ! empty( $new_icon ) ) :
								$cta_icon_migrated = isset( $settings['__fa4_migrated'][ self::$skin . '_new_cta_icon' ] );
								$cta_icon_is_empty = empty( $icon );
								?>
								<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' . get_the_ID() ) ); ?>>

									<?php
									if ( $cta_icon_migrated || $cta_icon_is_empty ) {
										\Elementor\Icons_Manager::render_icon( $new_icon, array( 'aria-hidden' => 'true' ) );
									} else {
										?>
										<i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
									<?php } ?>

								</span>
							<?php endif; ?>
						<?php } elseif ( ! empty( $icon ) ) { ?>
							<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' . get_the_ID() ) ); ?>>
								<i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
							</span>
						<?php } ?>

						<span class="elementor-button-text" id=<?php echo esc_attr( 'uael-post-' . get_the_ID() ); ?>><?php echo wp_kses_post( apply_filters( 'uael_post_cta_text', $this->get_instance_value( 'cta_text' ), get_the_ID(), $settings ) ); ?></span>
					</span>
				</a>
			<?php
			do_action( 'uael_single_post_after_cta', get_the_ID(), $settings );
		}
	}

	/**
	 * Get masonry script.
	 *
	 * Returns the post masonry script.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_masonry_script() {

		$structure = $this->get_instance_value( 'post_structure' );

		if ( 'masonry' !== $structure || ! ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) ) {
			return;
		}

		$layout = 'masonry';

		?>
		<script type="text/javascript">

			jQuery( document ).ready( function( $ ) {

				$( '.uael-post-grid__inner' ).each( function() {

					var	scope 		= $( '[data-id="<?php echo esc_attr( self::$node_id ); ?>"]' );
					var selector 	= $(this);

					if ( selector.closest( scope ).length < 1 ) {
						return;
					}

					selector.imagesLoaded( function() {

						$isotopeObj = selector.isotope({
							layoutMode: '<?php echo esc_attr( $layout ); ?>',
							itemSelector: '.uael-post-wrapper',
						});

						selector.find( '.uael-post-wrapper' ).resize( function() {
							$isotopeObj.isotope( 'layout' );
						});
					});
				});
			});

		</script>
		<?php
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

		if ( ! in_array( $this->get_instance_value( 'post_structure' ), array( 'normal', 'featured', 'masonry' ), true ) ) {
			return;
		}

		// Get current page number.
		$paged = self::$query_obj->get_paged();

		$query = self::$query;

		$total_pages = $query->max_num_pages;

		if ( 'infinite' !== $this->get_instance_value( 'pagination' ) ) {

			if ( '' !== $this->get_instance_value( 'max_pages' ) && null !== $this->get_instance_value( 'max_pages' ) ) {
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
	 * Get Footer.
	 *
	 * Returns the Pagination HTML.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_footer() {

		$this->render_pagination();

		$this->render_masonry_script();
	}

	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_header() {

		$this->render_filters();
	}

	/**
	 * Get Filter taxonomy array.
	 *
	 * Returns the Filter array of objects.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_filter_values() {

		$settings = self::$settings;
		$skin     = self::$skin;

		$post_type = $settings['post_type_filter'];

		$filter_by = $this->get_instance_value( 'tax_masonry_' . $post_type . '_filter' );

		$filter_type = $settings[ $filter_by . '_' . $post_type . '_filter_rule' ];

		$filters = $settings[ 'tax_' . $filter_by . '_' . $post_type . '_filter' ];

		// Get the categories for post types.
		$taxs = get_terms( $filter_by );

		$filter_array = array();

		if ( is_wp_error( $taxs ) ) {
			return array();
		}

		if ( empty( $filters ) || '' === $filters ) {

			$filter_array = $taxs;
		} else {

			foreach ( $taxs as $key => $value ) {

				if ( 'IN' === $filter_type ) {

					if ( in_array( $value->slug, $filters, true ) ) {

						$filter_array[] = $value;
					}
				} else {

					if ( ! in_array( $value->slug, $filters, true ) ) {

						$filter_array[] = $value;
					}
				}
			}
		}

		return $filter_array;
	}

	/**
	 * Get Masonry classes array.
	 *
	 * Returns the Masonry classes array.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_masonry_classes() {

		$settings = self::$settings;

		$post_type = $settings['post_type_filter'];

		$filter_by = $this->get_instance_value( 'tax_masonry_' . $post_type . '_filter' );

		$taxonomies = wp_get_post_terms( get_the_ID(), $filter_by );
		$class      = array();

		if ( count( $taxonomies ) > 0 ) {

			foreach ( $taxonomies as $taxonomy ) {

				if ( is_object( $taxonomy ) ) {

					$class[] = $taxonomy->slug;
				}
			}
		}

		return implode( ' ', $class );
	}

	/**
	 * Get category name.
	 *
	 * Adds the category class.
	 *
	 * @since 1.20.0
	 * @access public
	 */
	public function get_category_name() {

		foreach ( get_the_category( get_the_ID() ) as $category ) {

			$category_name = str_replace( ' ', '-', $category->name );

			echo esc_attr( strtolower( $category_name ) ) . ' ';
		}
	}

	/**
	 * Get Wrapper Classes.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_slider_attr() {

		if ( 'carousel' !== $this->get_instance_value( 'post_structure' ) ) {
			return;
		}

		if ( ! wp_script_is( 'jquery', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery' );
		}

		$is_rtl      = is_rtl();
		$direction   = $is_rtl ? 'rtl' : 'ltr';
		$show_dots   = ( in_array( $this->get_instance_value( 'navigation' ), array( 'dots', 'both' ), true ) );
		$show_arrows = ( in_array( $this->get_instance_value( 'navigation' ), array( 'arrows', 'both' ), true ) );

		$slick_options = array(
			'slidesToShow'   => ( $this->get_instance_value( 'slides_to_show' ) ) ? absint( $this->get_instance_value( 'slides_to_show' ) ) : 4,
			'slidesToScroll' => ( $this->get_instance_value( 'slides_to_scroll' ) ) ? absint( $this->get_instance_value( 'slides_to_scroll' ) ) : 1,
			'autoplaySpeed'  => ( $this->get_instance_value( 'autoplay_speed' ) ) ? absint( $this->get_instance_value( 'autoplay_speed' ) ) : 5000,
			'autoplay'       => ( 'yes' === $this->get_instance_value( 'autoplay' ) ),
			'infinite'       => ( 'yes' === $this->get_instance_value( 'infinite' ) ),
			'pauseOnHover'   => ( 'yes' === $this->get_instance_value( 'pause_on_hover' ) ),
			'speed'          => ( $this->get_instance_value( 'transition_speed' ) ) ? absint( $this->get_instance_value( 'transition_speed' ) ) : 500,
			'arrows'         => $show_arrows,
			'dots'           => $show_dots,
			'rtl'            => $is_rtl,
			'prevArrow'      => '<button type="button" data-role="none" class="slick-prev" aria-label="Previous" tabindex="0" role="button"><i class="fa fa-angle-left"></i></button>',
			'nextArrow'      => '<button type="button" data-role="none" class="slick-next" aria-label="Next" tabindex="0" role="button"><i class="fa fa-angle-right"></i></button>',
		);

		if ( $this->get_instance_value( 'slides_to_show_tablet' ) || $this->get_instance_value( 'slides_to_show_mobile' ) ) {

			$slick_options['responsive'] = array();

			if ( $this->get_instance_value( 'slides_to_show_tablet' ) ) {

				$tablet_show   = absint( $this->get_instance_value( 'slides_to_show_tablet' ) );
				$tablet_scroll = ( $this->get_instance_value( 'slides_to_scroll_tablet' ) ) ? absint( $this->get_instance_value( 'slides_to_scroll_tablet' ) ) : $tablet_show;

				$slick_options['responsive'][] = array(
					'breakpoint' => 1024,
					'settings'   => array(
						'slidesToShow'   => $tablet_show,
						'slidesToScroll' => $tablet_scroll,
					),
				);
			}

			if ( $this->get_instance_value( 'slides_to_show_mobile' ) ) {

				$mobile_show   = absint( $this->get_instance_value( 'slides_to_show_mobile' ) );
				$mobile_scroll = ( $this->get_instance_value( 'slides_to_scroll_mobile' ) ) ? absint( $this->get_instance_value( 'slides_to_scroll_mobile' ) ) : $mobile_show;

				$slick_options['responsive'][] = array(
					'breakpoint' => 767,
					'settings'   => array(
						'slidesToShow'   => $mobile_show,
						'slidesToScroll' => $mobile_scroll,
					),
				);
			}
		}

		$this->add_render_attribute(
			'uael-post-slider',
			array(
				'data-post_slider'  => wp_json_encode( $slick_options ),
				'data-equal-height' => $this->get_instance_value( 'equal_height' ),
			)
		);

		return $this->get_render_attribute_string( 'uael-post-slider' );
	}

	/**
	 * Get Filters.
	 *
	 * Returns the Filter HTML.
	 *
	 * @since 1.7.0
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

		if ( ! in_array( $this->get_instance_value( 'post_structure' ), array( 'masonry', 'normal' ), true ) ) {
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
	 * Get Search Box HTML.
	 *
	 * Returns the Search Box HTML.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_search() {
		$settings = self::$settings;
		?>
		<div class="uael-post__grid-empty">
			<p><?php echo wp_kses_post( $settings['no_results_text'] ); ?></p>
			<?php if ( 'yes' === $settings['show_search_box'] ) { ?>
				<?php get_search_form(); ?>
			<?php } ?>
		</div>
		<?php
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
			'uael-post__columns-' . $this->get_instance_value( 'slides_to_show' ),
			'uael-post__columns-tablet-' . $this->get_instance_value( 'slides_to_show_tablet' ),
			'uael-post__columns-mobile-' . $this->get_instance_value( 'slides_to_show_mobile' ),
		);

		if ( 'masonry' === $this->get_instance_value( 'post_structure' ) ) {
			$classes[] = 'uael-post-masonry';
		}

		if ( 'infinite' === $this->get_instance_value( 'pagination' ) ) {
			$classes[] = 'uael-post-infinite-scroll';
			$classes[] = 'uael-post-infinite__event-' . $this->get_instance_value( 'infinite_event' );
		}

		return apply_filters( 'uael_wrapper_classes', $classes );
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

		$classes = array(
			'uael-blog-terms-position-' . $this->get_instance_value( 'terms_position' ),
			'uael-post-image-' . $this->get_instance_value( 'image_position' ),
			'uael-post-grid',
			'uael-posts',
		);

		if ( 'featured' === $this->get_instance_value( 'post_structure' ) ) {

			$classes[] = 'uael-post_structure-' . $this->get_instance_value( 'post_structure' );
			$classes[] = 'uael-featured_post_structure-' . $this->get_instance_value( 'featured_post' );
		}

		return apply_filters( 'uael_outer_wrapper_classes', $classes );
	}

	/**
	 * Get body.
	 *
	 * Returns body.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_body() {

		global $post;

		$settings            = self::$settings;
		$query               = self::$query;
		$skin                = self::$skin;
		$count               = 0;
		$is_featured         = false;
		$wrapper             = $this->get_wrapper_classes();
		$outer_wrapper       = $this->get_outer_wrapper_classes();
		$structure           = $this->get_instance_value( 'post_structure' );
		$layout              = '';
		$page_id             = '';
		$filter_default_text = $this->get_instance_value( 'filters_all_text' );

		if ( null !== \Elementor\Plugin::$instance->documents->get_current() ) {
			$page_id = \Elementor\Plugin::$instance->documents->get_current()->get_main_id();
		}

		if ( 'masonry' === $structure ) {

			$layout = 'masonry';
		}
		$offset_top = apply_filters( 'uael_post_offset_top', 30 );
		$this->add_render_attribute( 'wrapper', 'class', $wrapper );
		$this->add_render_attribute( 'outer_wrapper', 'class', $outer_wrapper );
		$this->add_render_attribute( 'outer_wrapper', 'data-query-type', $settings['query_type'] );
		$this->add_render_attribute( 'outer_wrapper', 'data-structure', $structure );
		$this->add_render_attribute( 'outer_wrapper', 'data-layout', $layout );
		$this->add_render_attribute( 'outer_wrapper', 'data-page', $page_id );
		$this->add_render_attribute( 'outer_wrapper', 'data-skin', $skin );
		$this->add_render_attribute( 'outer_wrapper', 'data-filter-default', $filter_default_text );
		$this->add_render_attribute( 'outer_wrapper', 'data-offset-top', $offset_top );

		if (
			'yes' === $this->get_instance_value( 'default_filter_switch' ) &&
			'' !== $this->get_instance_value( 'default_filter' )
		) {
			$this->add_render_attribute( 'outer_wrapper', 'data-default-filter', $this->get_instance_value( 'default_filter' ) );
		}

		?>

		<?php do_action( 'uael_posts_before_outer_wrap', $settings ); ?>

		<div <?php echo wp_kses_post( sanitize_text_field( $this->get_render_attribute_string( 'outer_wrapper' ) ) ); ?> <?php echo wp_kses_post( sanitize_text_field( $this->get_slider_attr() ) ); ?>>

			<?php do_action( 'uael_posts_before_wrap', $settings ); ?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?>>
			<?php

			while ( $query->have_posts() ) {

				$is_featured = false;

				if ( 0 === $count && 'featured' === $this->get_instance_value( 'post_structure' ) ) {
					$is_featured = true;
				}

				$query->the_post();

				include UAEL_MODULES_DIR . 'posts/templates/content-post-' . $skin . '.php';

				$count++;
			}

			wp_reset_postdata();
			?>
				</div>
			<?php if ( 'infinite' === $this->get_instance_value( 'pagination' ) ) { ?>
			<div class="uael-post-inf-loader">
				<div class="uael-post-loader-1"></div>
				<div class="uael-post-loader-2"></div>
				<div class="uael-post-loader-3"></div>
			</div>
			<?php } ?>
			<?php do_action( 'uael_posts_after_wrap', $settings ); ?>

		</div>

		<?php do_action( 'uael_posts_after_outer_wrap', $settings ); ?>
		<?php
	}

	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @param string $style Skin ID.
	 * @param array  $settings Settings Object.
	 * @param string $node_id Node ID.
	 * @since 1.7.0
	 * @access public
	 */
	public function render( $style, $settings, $node_id ) {

		self::$settings         = $settings;
		$schema_support         = $settings[ $style . '_schema_support' ];
		$publisher_name         = $settings[ $style . '_publisher_name' ];
		$publisher_logo         = isset( $settings[ $style . '_publisher_logo' ]['url'] ) ? ( $settings[ $style . '_publisher_logo' ]['url'] ) : 0;
		$content_schema_warning = false;
		self::$skin             = $style;
		self::$node_id          = $node_id;
		self::$query_obj        = new Build_Post_Query( $style, $settings, '' );
		self::$query_obj->query_posts();

		self::$query = self::$query_obj->get_query();

		$query     = self::$query;
		$is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$headline     = get_the_title();
				$image        = get_the_post_thumbnail_url();
				$publishdate  = get_the_date( 'Y-m-d' );
				$modifieddate = get_the_modified_date( 'Y-m-d' );
				if ( 'yes' === $schema_support && ( ( '' === $headline || '' === $publishdate || '' === $publisher_name || '' === $publisher_logo || '' === $modifieddate ) || ( ! $image ) ) ) {
					$content_schema_warning = true;
				}
			}
			if ( 'yes' === $schema_support && true === $content_schema_warning && $is_editor ) {
				?>
				<div class="uael-builder-msg elementor-alert elementor-alert-warning">
					<?php if ( ! $image ) { ?>
						<span class="elementor-alert-description"><?php esc_html_e( 'Some posts do not have featured images. Please fill in all required fields to display posts schema properly.', 'uael' ); ?></span>
					<?php } else { ?>
						<span class="elementor-alert-description">
							<?php esc_attr_e( 'Some fields are empty under the posts schema section. Please fill in all required fields.', 'uael' ); ?><br/>
							<?php esc_attr_e( 'Make sure all your posts do have a featured image.', 'uael' ); ?>
						</span>
					<?php } ?>
				</div>
				<?php
			}
		}

		// Get search box.
		if ( ! self::$query->have_posts() ) {
			$this->render_search();
			return;
		}

		?>
		<div class="uael-post__header">
			<?php $this->get_header(); ?>
		</div>
		<div class="uael-post__body">
			<?php $this->get_body(); ?>
		</div>
		<div class="uael-post__footer">
			<?php $this->get_footer(); ?>
		</div>
		<?php
	}

	/**
	 * Render settings array for selected skin
	 *
	 * @since 1.7.0
	 * @param string $control_base_id Skin ID.
	 * @access public
	 */
	public function get_instance_value( $control_base_id ) {
		if ( isset( self::$settings[ self::$skin . '_' . $control_base_id ] ) ) {
			return self::$settings[ self::$skin . '_' . $control_base_id ];
		} else {
			return null;
		}
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * Render post HTML via AJAX call.
	 *
	 * @param array|string $style_id  The style ID.
	 * @param array|string $widget    Widget object.
	 * @since 1.7.0
	 * @access public
	 */
	public function inner_render( $style_id, $widget ) {

		ob_start();

		check_ajax_referer( 'uael-posts-widget-nonce', 'nonce' );

		$category = ( isset( $_POST['category'] ) ) ? sanitize_text_field( $_POST['category'] ) : '';

		self::$settings  = $widget->get_settings_for_display();
		self::$query_obj = new Build_Post_Query( $style_id, self::$settings, $category );
		self::$query_obj->query_posts();
		self::$query = self::$query_obj->get_query();
		self::$skin  = $style_id;
		$query       = self::$query;
		$settings    = self::$settings;
		$is_featured = false;
		$count       = 0;

		while ( $query->have_posts() ) {

			$is_featured = false;

			if ( 0 === $count && 'featured' === $this->get_instance_value( 'post_structure' ) ) {
				$is_featured = true;
			}

			$query->the_post();
			include UAEL_MODULES_DIR . 'posts/templates/content-post-' . $style_id . '.php';

			$count++;
		}

		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Render post pagination HTML via AJAX call.
	 *
	 * @param array|string $style_id  The style ID.
	 * @param array|string $widget    Widget object.
	 * @since 1.7.0
	 * @access public
	 */
	public function page_render( $style_id, $widget ) {

		ob_start();

		check_ajax_referer( 'uael-posts-widget-nonce', 'nonce' );

		$category = ( isset( $_POST['category'] ) ) ? sanitize_text_field( $_POST['category'] ) : '';

		self::$settings  = $widget->get_settings_for_display();
		self::$query_obj = new Build_Post_Query( $style_id, self::$settings, $category );
		self::$query_obj->query_posts();
		self::$query = self::$query_obj->get_query();
		self::$skin  = $style_id;
		$query       = self::$query;
		$settings    = self::$settings;
		$is_featured = false;

		$this->render_pagination();

		return ob_get_clean();
	}
}
