<?php
/**
 * Smart Post Listing Helper Functions.
 */

namespace PremiumAddonsPro\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Smart_Post_Listing_Helper.
 */
class Smart_Post_Listing_Helper {

    /**
	 * Get Post Meta
	 *
	 * @since 3.4.4
	 * @access protected
	 *
	 * @param string $link_target target.
     * @param string $settings widget settings.
     * @param string|int $id post ID.
	 */
	public static function render_smart_post_meta( $post_type, $settings, $id ) {

		$source = $settings['post_type_filter'];

		$separator = $settings['meta_separator'];

		$author_meta = 'featured' === $post_type ? $settings['pa_featured_author_meta'] : $settings['author_meta'];

		$date_meta = 'featured' === $post_type ? $settings['pa_featured_date_meta'] : $settings['date_meta'];

		$comments_meta = 'featured' === $post_type ? $settings['pa_featured_comments_meta'] : $settings['comments_meta'];

        $tags_meta = 'featured' === $post_type ? $settings['pa_featured_tags_meta'] : $settings['tags_meta'];

		if ( 'yes' === $date_meta ) {
			$date_format = get_option( 'date_format' );
		}

		if ( 'yes' === $comments_meta ) {

			$comments_strings = array(
				'no-comments'       => __( 'No Comments', 'premium-addons-for-elementor' ),
				'one-comment'       => __( '1 Comment', 'premium-addons-for-elementor' ),
				'multiple-comments' => __( '% Comments', 'premium-addons-for-elementor' ),
			);
		}

		?>
			<?php if ( 'yes' === $author_meta ) : ?>
				<div class="premium-smart-listing__post-author premium-smart-listing__post-meta">
					<i class="fa fa-user fa-fw" aria-hidden="true"></i>
					<?php the_author_posts_link(); ?>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $date_meta ) { ?>
				<span class="premium-smart-listing__meta-separator"><?php echo esc_html( $separator ); ?></span>
				<div class="premium-smart-listing__post-time premium-smart-listing__post-meta">
					<i class="fa fa-clock-o" aria-hidden="true"></i>
					<span><?php the_time( $date_format ); ?></span>
				</div>
			<?php } ?>

			<?php if ( 'yes' === $tags_meta ) : ?>
				<span class="premium-smart-listing__meta-separator">•</span>
				<div class="premium-smart-listing__post-tags premium-smart-listing__post-meta">
					<i class="fa fa-align-left fa-fw" aria-hidden="true"></i>
                    <?php self::get_post_tags( $source, $id); ?>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $comments_meta ) : ?>
				<span class="premium-smart-listing__meta-separator"><?php echo esc_html( $separator ); ?></span>
				<div class="premium-smart-listing__post-comments premium-smart-listing__post-meta">
					<i class="fa fa-comments-o fa-fw" aria-hidden="true"></i>
					<?php comments_popup_link( $comments_strings['no-comments'], $comments_strings['one-comment'], $comments_strings['multiple-comments'], '', $comments_strings['no-comments'] ); ?>
				</div>
			<?php endif; ?>
		<?php

	}

    /**
	 * Get Post Categories
	 *
	 * @since 3.4.4
	 * @access protected
	 *
     * @param string $settings widget settings.
     * @param string|int $id post ID.
	 */
	public static function get_post_categories( $settings, $post_id ) { ?>
		<div class="premium-smart-listing__cat-container">
			<ul class="post-categories">
				<?php
                    $source = $settings['post_type_filter'];
					$post_cats     = 'product' === $source ? get_the_terms( $post_id, 'product_cat' ) : get_the_category();
					$class = 'premium-smart-listing__category ';
					$cats_repeater = $settings['categories_repeater'];

				if ( count( $post_cats ) ) {
					foreach ( $post_cats as $index => $cat ) {
						$repeater_class = isset( $cats_repeater[ $index ] ) ? 'elementor-repeater-item-' . $cats_repeater[ $index ]['_id'] : '';
						echo wp_kses_post( sprintf( '<li><a href="%s" class="%s %s">%s</a></li>', get_category_link( $cat ), $class, $repeater_class, $cat->name ) );
					}
				}

				?>
			</ul>
		</div>
		<?php
	}

    /**
	 * Get Post Tags
	 *
	 * @since 3.4.4
	 * @access protected
	 *
     * @param string $source query source.
     * @param string|int $id post ID.
	 */
    public static function get_post_tags( $source, $post_id ) {

        $post_tags     = 'product' === $source ? get_the_terms( $post_id, 'product_tag' ) : get_the_tags();

        if ( $post_tags && count( $post_tags ) ) {
            foreach ( $post_tags as $index => $tag ) {

                echo wp_kses_post( sprintf( '<a href="%s">%s</a>', get_tag_link( $tag ), $tag->name ) );
                if ( $index < count( $post_tags ) -1 )
                    echo ',';
            }
        }

	}


}
