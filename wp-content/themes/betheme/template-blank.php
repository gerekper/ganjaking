<?php
/**
 * Template Name: Blank Page
 *
 * @package Betheme
 * @author Muffin Group
 * @link https://muffingroup.com
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js<?php echo esc_attr(mfn_user_os()); ?>"<?php mfn_tag_schema(); ?>>

<head>

<meta charset="<?php bloginfo('charset'); ?>" />
<?php wp_head(); ?>

</head>

<body <?php body_class('template-blank'); ?>>

	<?php do_action('mfn_hook_top'); ?>

	<?php do_action('mfn_hook_content_before'); ?>

	<div id="Content">
		<div class="content_wrapper clearfix">

			<div class="sections_group">
				<?php
					while (have_posts()) {

						the_post();

						$mfn_builder = new Mfn_Builder_Front(get_the_ID());
						$mfn_builder->show();

					}
				?>
			</div>

			<?php get_sidebar(); ?>

		</div>
	</div>

	<?php do_action('mfn_hook_content_after'); ?>

	<?php do_action('mfn_hook_bottom'); ?>

<?php wp_footer(); ?>

</body>
</html>
