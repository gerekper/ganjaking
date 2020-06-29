<?php
/**
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

// class

$intro_class = "";
$parallax = false;
$intro_style = "";

$intro_options = get_post_meta(get_the_ID(), 'mfn-post-intro', true);
if (is_array($intro_options)) {
	if (isset($intro_options['light'])) {
		$intro_class .= 'light';
	}
	if (isset($intro_options['full-screen'])) {
		$intro_class .= ' full-screen';
	}
	if (isset($intro_options['parallax'])) {
		$intro_class .= ' parallax';
		$parallax = true;
	}
	if (isset($intro_options['cover'])) {
		$intro_style .= 'background-size:cover;';
	}
}

// style

if ($bg_color = get_post_meta(get_the_ID(), 'mfn-post-bg', true)) {
	$intro_style .= 'background-color:'. esc_attr($bg_color) .';';
}
if ($bg_image = get_post_meta(get_the_ID(), 'mfn-post-header-bg', true)) {
	$intro_style .= 'background-image:url('. esc_url($bg_image) .');';
} else {
	$parallax = false;
}

// padding

if ($intro_padding = mfn_opts_get('single-intro-padding')) {
	$intro_padding = 'padding:'. $intro_padding;
}

// parallax
if ($parallax) {
	$parallax = mfn_parallax_data();

	if (mfn_parallax_plugin() == 'translate3d') {
		if (wp_is_mobile()) {
			$intro_style .= 'background-attachment:scroll;background-size:cover;-webkit-background-size:cover;';
		} else {
			$intro_style = false;
		}
	} else {
		$intro_style .= 'background-repeat:no-repeat;background-attachment:fixed;background-size:cover;-webkit-background-size:cover;';
	}
}

// IMPORTANT for post meta

while (have_posts()) {
	the_post();
}
wp_reset_query();
?>

<div id="Intro" class="<?php echo esc_attr($intro_class); ?>" style="<?php echo esc_attr($intro_style); ?>" <?php echo wp_kses_data($parallax);?>>

	<?php
		// parallax | translate3d -------
		if (! wp_is_mobile() && $parallax && mfn_parallax_plugin() == 'translate3d') {
			echo '<img class="mfn-parallax" src="'. esc_url($bg_image) .'" alt="parallax background" style="opacity:0" />';
		}
	?>

	<div class="intro-inner" style="<?php echo esc_attr($intro_padding); ?>">

		<?php
			$h = mfn_opts_get('title-heading', 1);
			echo '<h'. esc_attr($h) .' class="intro-title">'. wp_kses(get_the_title(), mfn_allowed_html()) .'</h'. esc_attr($h) .'>';
		?>

		<?php
			$show_meta = false;

			$single_meta = mfn_opts_get('blog-meta');
			if (get_post_type() == 'portfolio') {
				$single_meta = mfn_opts_get('portfolio-meta');
			}

			if (is_array($single_meta)) {
				if (isset($single_meta['author']) || isset($single_meta['date']) || isset($single_meta['categories'])) {
					$show_meta = true;
				}
			}
		?>

		<?php if ($show_meta): ?>
			<div class="intro-meta">

				<?php if (isset($single_meta['author'])): ?>
					<div class="author">
						<i class="icon-user"></i>
						<span><a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php the_author_meta('display_name'); ?></a></span>
					</div>
				<?php endif; ?>

				<?php if (isset($single_meta['date'])): ?>
					<div class="date">
						<i class="icon-clock"></i>
						<span><?php echo esc_html(get_the_date()); ?></span>
					</div>
				<?php endif; ?>

				<?php if (isset($single_meta['categories'])): ?>

					<?php if ($categories = get_the_category()): ?>
						<div class="categories">
							<i class="icon-docs"></i>
							<?php
								$string_cat = '';
								foreach ($categories as $cat) {
									$string_cat .= '<a href="'. esc_url(get_category_link($cat->term_id)) .'">'. esc_html($cat->name) .'</a>, ';
								}
								echo '<span>'. rtrim($string_cat, ", ") .'</span>';
							?>
						</div>
					<?php endif; ?>

					<?php
						$terms = get_the_terms(false, 'portfolio-types');
						if (is_array($terms)):
					?>
						<div class="categories">
							<i class="icon-docs"></i>
							<?php
								$string_term = '';
								foreach ($terms as $term) {
									$string_term .= '<a href="'. esc_url(get_term_link($term, 'post_tag')) .'">'. esc_html($term->name) .'</a>, ';
								}
								echo '<span>'. rtrim($string_term, ", ") .'</span>';
							?>
						</div>
					<?php endif; ?>

					<?php if ($terms = get_the_terms(false, 'post_tag')): ?>
						<div class="tags">
							<i class="icon-tag"></i>
							<?php
								$string_term = '';
								foreach ($terms as $term) {
									$string_term .= '<a href="'. esc_url(get_term_link($term, 'post_tag')) .'">'. esc_html($term->name) .'</a>, ';
								}
								echo '<span>'. rtrim($string_term, ", ") .'</span>';
							?>
						</div>
					<?php endif; ?>

				<?php endif; ?>

			</div>
		<?php endif; ?>

	</div>

	<div class="intro-next"><i class="icon-down-open-big"></i></div>

</div>
