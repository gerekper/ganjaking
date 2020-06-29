<?php
/**
 * The Header for our theme.
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */
?><!DOCTYPE html>
<?php
	if ($_GET && key_exists('mfn-rtl', $_GET)):
		echo '<html class="no-js" lang="ar" dir="rtl">';
	else:
?>
<html <?php language_attributes(); ?> class="no-js<?php echo esc_attr(mfn_user_os()); ?>"<?php mfn_tag_schema(); ?>>
<?php endif; ?>

<head>

<meta charset="<?php bloginfo('charset'); ?>" />
<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

	<?php do_action('mfn_hook_top'); ?>

	<?php get_template_part('includes/header', 'sliding-area'); ?>

	<?php
		if (mfn_header_style(true) == 'header-creative') {
			get_template_part('includes/header', 'creative');
		}
	?>

	<div id="Wrapper">

		<?php

			// featured image: parallax

			$class = '';
			$data_parallax = array();

			if (mfn_opts_get('img-subheader-attachment') == 'parallax') {
				$class = 'bg-parallax';

				if (mfn_opts_get('parallax') == 'stellar') {
					$data_parallax['key'] = 'data-stellar-background-ratio';
					$data_parallax['value'] = '0.5';
				} else {
					$data_parallax['key'] = 'data-enllax-ratio';
					$data_parallax['value'] = '0.3';
				}
			}
		?>

		<?php
			$shop_id = wc_get_page_id('shop');

			if (mfn_header_style(true) == 'header-below') {
				if (is_shop() || (mfn_opts_get('shop-slider') == 'all')) {
					echo mfn_slider($shop_id);
				}
			}
		?>

		<div id="Header_wrapper" class="<?php echo esc_attr($class); ?>" <?php if ($data_parallax) {
			printf('%s="%.1f"', $data_parallax['key'], $data_parallax['value']);
		} ?>>

			<?php
				if ('mhb' == mfn_header_style()) {

					// mfn_header action for header builder plugin

					do_action('mfn_header');
					if (is_shop() || (mfn_opts_get('shop-slider') == 'all')) {
						echo mfn_slider($shop_id);
					}

				} else {

					echo '<header id="Header">';
					if (mfn_header_style(true) != 'header-creative') {
						get_template_part('includes/header', 'top-area');
					}
					if (mfn_header_style(true) != 'header-below') {
						if (is_shop() || (mfn_opts_get('shop-slider') == 'all')) {
							echo mfn_slider($shop_id);
						}
					}
					echo '</header>';

				}
			?>

			<?php
				function mfn_woocommerce_show_page_title()
				{
					return false;
				}
				add_filter('woocommerce_show_page_title', 'mfn_woocommerce_show_page_title');

				$subheader_advanced = mfn_opts_get('subheader-advanced');

				if (! mfn_slider_isset($shop_id) || is_product() || (is_array($subheader_advanced) && isset($subheader_advanced['slider-show']))) {

					// subheader

					$subheader_options = mfn_opts_get('subheader');

					if (is_array($subheader_options) && isset($subheader_options['hide-subheader'])) {
						$subheader_show = false;
					} elseif (get_post_meta(mfn_ID(), 'mfn-post-hide-title', true)) {
						$subheader_show = false;
					} else {
						$subheader_show = true;
					}

					// title

					if (is_array($subheader_options) && isset($subheader_options[ 'hide-title' ])) {
						$title_show = false;
					} else {
						$title_show = true;
					}

					// breadcrumbs

					if (is_array($subheader_options) && isset($subheader_options['hide-breadcrumbs'])) {
						$breadcrumbs_show = false;
					} else {
						$breadcrumbs_show = true;
					}

					// output

					if ($subheader_show) {

						echo '<div id="Subheader">';
							echo '<div class="container">';
								echo '<div class="column one">';

									if ($title_show) {

										$title_tag = mfn_opts_get('subheader-title-tag', 'h1');

										// single product can not use H1
										if (is_product() && $title_tag == 'h1') {
											$title_tag = 'h2';
										}

										echo '<'. esc_attr($title_tag) .' class="title">';
											if (is_product() && mfn_opts_get('shop-product-title')) {
												the_title();
											} else {
												woocommerce_page_title();
											}
										echo '</'. esc_attr($title_tag) .'>';
									}

									if ($breadcrumbs_show) {
										$home = mfn_opts_get('translate') ? mfn_opts_get('translate-home', 'Home') : __('Home', 'betheme');
										$woo_crumbs_args = apply_filters('woocommerce_breadcrumb_defaults', array(
											'delimiter' => false,
											'wrap_before' => '<ul class="breadcrumbs woocommerce-breadcrumb">',
											'wrap_after' => '</ul>',
											'before' => '<li>',
											'after' => '<span><i class="icon-right-open"></i></span></li>',
											'home' => esc_html($home),
										));

										woocommerce_breadcrumb($woo_crumbs_args);
									}

								echo '</div>';
							echo '</div>';
						echo '</div>';
					}
				}
			?>

		</div>

		<?php do_action('mfn_hook_content_before');
