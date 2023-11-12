<?php
/**
 * UAEL Post - Template.
 *
 * @package UAEL
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $post;

// Ensure visibility.
if ( empty( $post ) ) {
	return;
}

?>

<?php do_action( 'uael_single_post_before_wrap', get_the_ID(), $settings ); ?>

<div class="uael-post-wrapper <?php echo ( $is_featured ) ? 'uael-post-wrapper-featured' : ''; ?> <?php echo wp_kses_post( sanitize_text_field( $this->get_category_name() ) ); ?>">

	<div class="uael-post__bg-wrap">
		<?php if ( 'yes' === $this->get_instance_value( 'link_complete_box' ) ) { ?>
			<a href="<?php the_permalink(); ?>" target="<?php echo ( 'yes' === $this->get_instance_value( 'link_complete_box_tab' ) ) ? '_blank' : '_self'; ?>" class="uael-post__complete-box-overlay" aria-label="<?php esc_attr_e( 'Link Complete Box', 'uael' ); ?>"></a>
		<?php } ?>

		<?php do_action( 'uael_single_post_before_inner_wrap', get_the_ID(), $settings ); ?>

		<div class="uael-post__inner-wrap <?php echo wp_kses_post( $this->get_no_image_class() ); ?>">

		<?php $this->render_featured_image(); ?>

			<?php do_action( 'uael_single_post_before_content_wrap', get_the_ID(), $settings ); ?>

			<div class="uael-post__content-wrap">

			<?php
			$this->render_term_html();

			if ( $this->get_instance_value( 'show_title' ) ) {
				$this->render_title();
			}

			$this->render_separator();

			if ( $this->get_instance_value( 'show_excerpt' ) ) {
				$this->render_excerpt();
			}

			if ( $this->get_instance_value( 'show_meta' ) ) {
				$this->render_meta_data();
			}

			if ( $this->get_instance_value( 'show_cta' ) ) {
				$this->render_read_more();
			}
			?>
			</div>
			<?php do_action( 'uael_single_post_after_content_wrap', get_the_ID(), $settings ); ?>

		</div>
		<?php do_action( 'uael_single_post_after_inner_wrap', get_the_ID(), $settings ); ?>

	</div>

</div>
<?php do_action( 'uael_single_post_after_wrap', get_the_ID(), $settings ); ?>
