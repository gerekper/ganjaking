<?php
/**
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
?>

/**
 * Backgrounds *****
 */

html{
	background-color: <?php echo esc_attr(mfn_opts_get('background-html', '#FCFCFC')); ?>;
}

#Wrapper,#Content{
	background-color: <?php echo esc_attr(mfn_opts_get('background-body', '#FCFCFC')); ?>;
}

/**
 * Font | Family *****
 */

body, button, span.date_label, .timeline_items li h3 span, input[type="submit"], input[type="reset"], input[type="button"],
input[type="text"], input[type="password"], input[type="tel"], input[type="email"], textarea, select, .offer_li .title h3 {
	font-family: "<?php echo esc_attr(str_replace('#', '', mfn_opts_get('font-content', 'Roboto'))); ?>", Arial, Tahoma, sans-serif;
}

#menu > ul > li > a, a.action_button, #overlay-menu ul li a {
	font-family: "<?php echo esc_attr(str_replace('#', '', mfn_opts_get('font-menu', 'Roboto'))); ?>", Arial, Tahoma, sans-serif;
}

#Subheader .title {
	font-family: "<?php echo esc_attr(str_replace('#', '', mfn_opts_get('font-title', 'Lora'))); ?>", Arial, Tahoma, sans-serif;
}

h1, h2, h3, h4, .text-logo #logo {
	font-family: "<?php echo esc_attr(str_replace('#', '', mfn_opts_get('font-headings', 'Roboto'))); ?>", Arial, Tahoma, sans-serif;
}

h5, h6 {
	font-family: "<?php echo esc_attr(str_replace('#', '', mfn_opts_get('font-headings-small', 'Roboto'))); ?>", Arial, Tahoma, sans-serif;
}

blockquote {
	font-family: "<?php echo esc_attr(str_replace('#', '', mfn_opts_get('font-blockquote', 'Roboto'))); ?>", Arial, Tahoma, sans-serif;
}

.chart_box .chart .num, .counter .desc_wrapper .number-wrapper, .how_it_works .image .number,
.pricing-box .plan-header .price, .quick_fact .number-wrapper, .woocommerce .product div.entry-summary .price {
	font-family: "<?php echo esc_attr(str_replace('#', '', mfn_opts_get('font-decorative', 'Roboto'))); ?>", Arial, Tahoma, sans-serif;
}

/**
 * Font | Size & Style *****
 */

<?php

	$aFont = array(
		'content'	=> mfn_opts_get('font-size-content'),
		'big'			=> mfn_opts_get('font-size-big'),
		'menu'		=> mfn_opts_get('font-size-menu'),
		'title'		=> mfn_opts_get('font-size-title'),
		'h1'			=> mfn_opts_get('font-size-h1'),
		'h2'			=> mfn_opts_get('font-size-h2'),
		'h3'			=> mfn_opts_get('font-size-h3'),
		'h4'			=> mfn_opts_get('font-size-h4'),
		'h5'			=> mfn_opts_get('font-size-h5'),
		'h6'			=> mfn_opts_get('font-size-h6'),
		'intro'		=> mfn_opts_get('font-size-single-intro'),
	);

	$aFont['menu']['line_height'] = 0;

	$aFontInit = $aFont;
?>

/* Body */

body {
	font-size: <?php echo esc_attr($aFont['content']['size']); ?>px;
	line-height: <?php echo esc_attr($aFont['content']['line_height']); ?>px;
	font-weight: <?php echo esc_attr(str_replace('italic', '', $aFont['content']['weight_style'])); ?>;
	letter-spacing: <?php echo esc_attr($aFont['content']['letter_spacing']); ?>px;
	<?php
		if (strpos($aFont['content']['weight_style'], 'italic')) {
			echo 'font-style: italic;';
		}
	?>
}

.big {
	font-size: <?php echo esc_attr($aFont['big']['size']); ?>px;
	line-height: <?php echo esc_attr($aFont['big']['line_height']); ?>px;
	font-weight: <?php echo esc_attr(str_replace('italic', '', $aFont['big']['weight_style'])); ?>;
	letter-spacing: <?php echo esc_attr($aFont['big']['letter_spacing']); ?>px;
	<?php
		if (strpos($aFont['big']['weight_style'], 'italic')) {
			echo 'font-style: italic;';
		}
	?>
}

#menu > ul > li > a, a.action_button, #overlay-menu ul li a{
	font-size: <?php echo esc_attr($aFont['menu']['size']); ?>px;
	font-weight: <?php echo esc_attr(str_replace('italic', '', $aFont['menu']['weight_style'])); ?>;
	letter-spacing: <?php echo esc_attr($aFont['menu']['letter_spacing']); ?>px;
	<?php
		if (strpos($aFont['menu']['weight_style'], 'italic')) {
			echo 'font-style: italic;';
		}
	?>
}

#overlay-menu ul li a{
	line-height: <?php echo esc_attr($aFont['menu']['size'] + $aFont['menu']['size'] * 0.5); ?>px;
}

#Subheader .title {
	font-size: <?php echo esc_attr($aFont['title']['size']); ?>px;
	line-height: <?php echo esc_attr($aFont['title']['line_height']); ?>px;
	font-weight: <?php echo esc_attr(str_replace('italic', '', $aFont['title']['weight_style'])); ?>;
	letter-spacing: <?php echo esc_attr($aFont['title']['letter_spacing']); ?>px;
	<?php
		if (strpos($aFont['title']['weight_style'], 'italic')) {
			echo 'font-style: italic;';
		}
	?>
}

/* Headings */

h1, .text-logo #logo {
	font-size: <?php echo esc_attr($aFont['h1']['size']); ?>px;
	line-height: <?php echo esc_attr($aFont['h1']['line_height']); ?>px;
	font-weight: <?php echo esc_attr(str_replace('italic', '', $aFont['h1']['weight_style'])); ?>;
	letter-spacing: <?php echo esc_attr($aFont['h1']['letter_spacing']); ?>px;
	<?php
		if (strpos($aFont['h1']['weight_style'], 'italic')) {
			echo 'font-style: italic;';
		}
	?>
}
h2 {
	font-size: <?php echo esc_attr($aFont['h2']['size']); ?>px;
	line-height: <?php echo esc_attr($aFont['h2']['line_height']); ?>px;
	font-weight: <?php echo esc_attr(str_replace('italic', '', $aFont['h2']['weight_style'])); ?>;
	letter-spacing: <?php echo esc_attr($aFont['h2']['letter_spacing']); ?>px;
	<?php
		if (strpos($aFont['h2']['weight_style'], 'italic')) {
			echo 'font-style: italic;';
		}
	?>
}
h3 {
	font-size: <?php echo esc_attr($aFont['h3']['size']); ?>px;
	line-height: <?php echo esc_attr($aFont['h3']['line_height']); ?>px;
	font-weight: <?php echo esc_attr(str_replace('italic', '', $aFont['h3']['weight_style'])); ?>;
	letter-spacing: <?php echo esc_attr($aFont['h3']['letter_spacing']); ?>px;
	<?php
		if (strpos($aFont['h3']['weight_style'], 'italic')) {
			echo 'font-style: italic;';
		}
	?>
}
h4 {
	font-size: <?php echo esc_attr($aFont['h4']['size']); ?>px;
	line-height: <?php echo esc_attr($aFont['h4']['line_height']); ?>px;
	font-weight: <?php echo esc_attr(str_replace('italic', '', $aFont['h4']['weight_style'])); ?>;
	letter-spacing: <?php echo esc_attr($aFont['h4']['letter_spacing']); ?>px;
	<?php
		if (strpos($aFont['h4']['weight_style'], 'italic')) {
			echo 'font-style: italic;';
		}
	?>
}
h5 {
	font-size: <?php echo esc_attr($aFont['h5']['size']); ?>px;
	line-height: <?php echo esc_attr($aFont['h5']['line_height']); ?>px;
	font-weight: <?php echo esc_attr(str_replace('italic', '', $aFont['h5']['weight_style'])); ?>;
	letter-spacing: <?php echo esc_attr($aFont['h5']['letter_spacing']); ?>px;
	<?php
		if (strpos($aFont['h5']['weight_style'], 'italic')) {
			echo 'font-style: italic;';
		}
	?>
}
h6 {
	font-size: <?php echo esc_attr($aFont['h6']['size']); ?>px;
	line-height: <?php echo esc_attr($aFont['h6']['line_height']); ?>px;
	font-weight: <?php echo esc_attr(str_replace('italic', '', $aFont['h6']['weight_style'])); ?>;
	letter-spacing: <?php echo esc_attr($aFont['h6']['letter_spacing']); ?>px;
	<?php
		if (strpos($aFont['h6']['weight_style'], 'italic')) {
			echo 'font-style: italic;';
		}
	?>
}

/* Advanced */

#Intro .intro-title {
	font-size: <?php echo esc_attr($aFont['intro']['size']); ?>px;
	line-height: <?php echo esc_attr($aFont['intro']['line_height']); ?>px;
	font-weight: <?php echo esc_attr(str_replace('italic', '', $aFont['intro']['weight_style'])); ?>;
	letter-spacing: <?php echo esc_attr($aFont['intro']['letter_spacing']); ?>px;
	<?php
		if (strpos($aFont['intro']['weight_style'], 'italic')) {
			echo 'font-style: italic;';
		}
	?>
}

/**
 * Font | Size	Responsive *****
 */

<?php if (mfn_opts_get('responsive') && mfn_opts_get('font-size-responsive')): ?>

	<?php
		$min_size = 13;
		$min_line = 19;

		// Tablet (Landscape) |  768 - 959
		$multiplier = 0.85;

		foreach ($aFont as $key => $font) {
			$aFont[$key]['size'] = round($font['size'] * $multiplier);
			if ($aFont[$key]['size'] < $min_size) {
				$aFont[$key]['size'] = $min_size;
			}

			$aFont[$key]['line_height'] = round($font['line_height'] * $multiplier);
			if ($aFont[$key]['line_height'] < $min_line) {
				$aFont[$key]['line_height'] = $min_line;
			}

			$aFont[$key]['letter_spacing'] = round($font['letter_spacing'] * $multiplier);
		}
	?>

	@media only screen and (min-width: 768px) and (max-width: 959px){
		body {
			font-size: <?php echo esc_attr($aFont['content']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['content']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['content']['letter_spacing']); ?>px;
		}
		.big {
			font-size: <?php echo esc_attr($aFont['big']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['big']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['big']['letter_spacing']); ?>px;
		}
		#menu > ul > li > a, a.action_button, #overlay-menu ul li a {
			font-size: <?php echo esc_attr($aFont['menu']['size']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['menu']['letter_spacing']); ?>px;
		}
		#overlay-menu ul li a{
			line-height: <?php echo esc_attr($aFont['menu']['size'] + $aFont['menu']['size'] * 0.5); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['menu']['letter_spacing']); ?>px;
		}
		#Subheader .title {
			font-size: <?php echo esc_attr($aFont['title']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['title']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['title']['letter_spacing']); ?>px;
		}
		h1, .text-logo #logo {
			font-size: <?php echo esc_attr($aFont['h1']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h1']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h1']['letter_spacing']); ?>px;
		}
		h2 {
			font-size: <?php echo esc_attr($aFont['h2']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h2']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h2']['letter_spacing']); ?>px;
		}
		h3 {
			font-size: <?php echo esc_attr($aFont['h3']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h3']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h3']['letter_spacing']); ?>px;
		}
		h4 {
			font-size: <?php echo esc_attr($aFont['h4']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h4']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h4']['letter_spacing']); ?>px;
		}
		h5 {
			font-size: <?php echo esc_attr($aFont['h5']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h5']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h5']['letter_spacing']); ?>px;
		}
		h6 {
			font-size: <?php echo esc_attr($aFont['h6']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h6']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h6']['letter_spacing']); ?>px;
		}
		#Intro .intro-title {
			font-size: <?php echo esc_attr($aFont['intro']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['intro']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['intro']['letter_spacing']); ?>px;
		}

		blockquote { font-size: 15px;}

		.chart_box .chart .num { font-size: 45px; line-height: 45px; }

		.counter .desc_wrapper .number-wrapper { font-size: 45px; line-height: 45px;}
		.counter .desc_wrapper .title { font-size: 14px; line-height: 18px;}

		.faq .question .title { font-size: 14px; }

		.fancy_heading .title { font-size: 38px; line-height: 38px; }

		.offer .offer_li .desc_wrapper .title h3 { font-size: 32px; line-height: 32px; }
		.offer_thumb_ul li.offer_thumb_li .desc_wrapper .title h3 {  font-size: 32px; line-height: 32px; }

		.pricing-box .plan-header h2 { font-size: 27px; line-height: 27px; }
		.pricing-box .plan-header .price > span { font-size: 40px; line-height: 40px; }
		.pricing-box .plan-header .price sup.currency { font-size: 18px; line-height: 18px; }
		.pricing-box .plan-header .price sup.period { font-size: 14px; line-height: 14px;}

		.quick_fact .number { font-size: 80px; line-height: 80px;}

		.trailer_box .desc h2 { font-size: 27px; line-height: 27px; }

		.widget > h3 { font-size: 17px; line-height: 20px; }
	}

	<?php

		// Tablet (Portrait) & Mobile (Landscape) | 480 - 767
		$multiplier = 0.75;

		$aFont = $aFontInit;

		foreach ($aFont as $key => $font) {
			$aFont[$key]['size'] = round($font['size'] * $multiplier);
			if ($aFont[$key]['size'] < $min_size) {
				$aFont[$key]['size'] = $min_size;
			}

			$aFont[$key]['line_height'] = round($font['line_height'] * $multiplier);
			if ($aFont[$key]['line_height'] < $min_line) {
				$aFont[$key]['line_height'] = $min_line;
			}

			$aFont[$key]['letter_spacing'] = round($font['letter_spacing'] * $multiplier);
		}
	?>

	@media only screen and (min-width: 480px) and (max-width: 767px){
		body {
			font-size: <?php echo esc_attr($aFont['content']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['content']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['content']['letter_spacing']); ?>px;
		}
		.big {
			font-size: <?php echo esc_attr($aFont['big']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['big']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['big']['letter_spacing']); ?>px;
		}
		#menu > ul > li > a, a.action_button, #overlay-menu ul li a {
			font-size: <?php echo esc_attr($aFont['menu']['size']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['menu']['letter_spacing']); ?>px;
		}
		#overlay-menu ul li a{
			line-height: <?php echo esc_attr($aFont['menu']['size'] + $aFont['menu']['size'] * 0.5); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['menu']['letter_spacing']); ?>px;
		}
		#Subheader .title {
			font-size: <?php echo esc_attr($aFont['title']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['title']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['title']['letter_spacing']); ?>px;
		}
		h1, .text-logo #logo {
			font-size: <?php echo esc_attr($aFont['h1']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h1']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h1']['letter_spacing']); ?>px;
		}
		h2 {
			font-size: <?php echo esc_attr($aFont['h2']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h2']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h2']['letter_spacing']); ?>px;
		}
		h3 {
			font-size: <?php echo esc_attr($aFont['h3']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h3']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h3']['letter_spacing']); ?>px;
		}
		h4 {
			font-size: <?php echo esc_attr($aFont['h4']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h4']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h4']['letter_spacing']); ?>px;
		}
		h5 {
			font-size: <?php echo esc_attr($aFont['h5']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h5']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h5']['letter_spacing']); ?>px;
		}
		h6 {
			font-size: <?php echo esc_attr($aFont['h6']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h6']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h6']['letter_spacing']); ?>px;
		}
		#Intro .intro-title {
			font-size: <?php echo esc_attr($aFont['intro']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['intro']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['intro']['letter_spacing']); ?>px;
		}

		blockquote { font-size: 14px;}

		.chart_box .chart .num { font-size: 40px; line-height: 40px; }

		.counter .desc_wrapper .number-wrapper { font-size: 40px; line-height: 40px;}
		.counter .desc_wrapper .title { font-size: 13px; line-height: 16px;}

		.faq .question .title { font-size: 13px; }

		.fancy_heading .title { font-size: 34px; line-height: 34px; }

		.offer .offer_li .desc_wrapper .title h3 { font-size: 28px; line-height: 28px; }
		.offer_thumb_ul li.offer_thumb_li .desc_wrapper .title h3 {  font-size: 28px; line-height: 28px; }

		.pricing-box .plan-header h2 { font-size: 24px; line-height: 24px; }
		.pricing-box .plan-header .price > span { font-size: 34px; line-height: 34px; }
		.pricing-box .plan-header .price sup.currency { font-size: 16px; line-height: 16px; }
		.pricing-box .plan-header .price sup.period { font-size: 13px; line-height: 13px;}

		.quick_fact .number { font-size: 70px; line-height: 70px;}

		.trailer_box .desc h2 { font-size: 24px; line-height: 24px; }

		.widget > h3 { font-size: 16px; line-height: 19px; }
	}

	<?php

		// Mobile (Portrait) | < 479
		$multiplier = 0.6;

		$aFont = $aFontInit;

		foreach ($aFont as $key => $font) {
			$aFont[$key]['size'] = round($font['size'] * $multiplier);
			if ($aFont[$key]['size'] < $min_size) {
				$aFont[$key]['size'] = $min_size;
			}

			$aFont[$key]['line_height'] = round($font['line_height'] * $multiplier);
			if ($aFont[$key]['line_height'] < $min_line) {
				$aFont[$key]['line_height'] = $min_line;
			}

			$aFont[$key]['letter_spacing'] = round($font['letter_spacing'] * $multiplier);
		}
	?>

	@media only screen and (max-width: 479px){
		body {
			font-size: <?php echo esc_attr($aFont['content']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['content']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['content']['letter_spacing']); ?>px;
		}
		.big {
			font-size: <?php echo esc_attr($aFont['big']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['big']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['big']['letter_spacing']); ?>px;
		}
		#menu > ul > li > a, a.action_button, #overlay-menu ul li a {
			font-size: <?php echo esc_attr($aFont['menu']['size']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['menu']['letter_spacing']); ?>px;
		}
		#overlay-menu ul li a{
			line-height: <?php echo esc_attr($aFont['menu']['size'] + $aFont['menu']['size'] * 0.5); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['menu']['letter_spacing']); ?>px;
		}
		#Subheader .title {
			font-size: <?php echo esc_attr($aFont['title']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['title']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['title']['letter_spacing']); ?>px;
		}
		h1, .text-logo #logo {
			font-size: <?php echo esc_attr($aFont['h1']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h1']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h1']['letter_spacing']); ?>px;
		}
		h2 {
			font-size: <?php echo esc_attr($aFont['h2']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h2']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h2']['letter_spacing']); ?>px;
		}
		h3 {
			font-size: <?php echo esc_attr($aFont['h3']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h3']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h3']['letter_spacing']); ?>px;
		}
		h4 {
			font-size: <?php echo esc_attr($aFont['h4']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h4']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h4']['letter_spacing']); ?>px;
		}
		h5 {
			font-size: <?php echo esc_attr($aFont['h5']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h5']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h5']['letter_spacing']); ?>px;
		}
		h6 {
			font-size: <?php echo esc_attr($aFont['h6']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['h6']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['h6']['letter_spacing']); ?>px;
		}
		#Intro .intro-title {
			font-size: <?php echo esc_attr($aFont['intro']['size']); ?>px;
			line-height: <?php echo esc_attr($aFont['intro']['line_height']); ?>px;
			letter-spacing: <?php echo esc_attr($aFont['intro']['letter_spacing']); ?>px;
		}

		blockquote { font-size: 13px;}

		.chart_box .chart .num { font-size: 35px; line-height: 35px; }

		.counter .desc_wrapper .number-wrapper { font-size: 35px; line-height: 35px;}
		.counter .desc_wrapper .title { font-size: 13px; line-height: 26px;}

		.faq .question .title { font-size: 13px; }

		.fancy_heading .title { font-size: 30px; line-height: 30px; }

		.offer .offer_li .desc_wrapper .title h3 { font-size: 26px; line-height: 26px; }
		.offer_thumb_ul li.offer_thumb_li .desc_wrapper .title h3 {  font-size: 26px; line-height: 26px; }

		.pricing-box .plan-header h2 { font-size: 21px; line-height: 21px; }
		.pricing-box .plan-header .price > span { font-size: 32px; line-height: 32px; }
		.pricing-box .plan-header .price sup.currency { font-size: 14px; line-height: 14px; }
		.pricing-box .plan-header .price sup.period { font-size: 13px; line-height: 13px;}

		.quick_fact .number { font-size: 60px; line-height: 60px;}

		.trailer_box .desc h2 { font-size: 21px; line-height: 21px; }

		.widget > h3 { font-size: 15px; line-height: 18px; }
	}

<?php endif; ?>

/**
 * Sidebar | Width *****
 */

<?php
	$sidebarW = mfn_opts_get('sidebar-width', '23');
	$contentW = 100 - $sidebarW;
	$sidebar2W = $sidebarW - 5;
	$content2W = 100 - ($sidebar2W * 2);
	$sidebar2M = $content2W + $sidebar2W;
	$content2M = $sidebar2W;
?>

.with_aside .sidebar.columns {
	width: <?php echo esc_attr($sidebarW); ?>%;
}
.with_aside .sections_group {
	width: <?php echo esc_attr($contentW); ?>%;
}

.aside_both .sidebar.columns {
	width: <?php echo esc_attr($sidebar2W); ?>%;
}
.aside_both .sidebar.sidebar-1{
	margin-left: -<?php echo esc_attr($sidebar2M); ?>%;
}
.aside_both .sections_group {
	width: <?php echo esc_attr($content2W); ?>%;
	margin-left: <?php echo esc_attr($content2M); ?>%;
}

/**
 * Grid | Width *****
 */

<?php if (mfn_opts_get('responsive')): ?>

	<?php
		$gridW = mfn_opts_get('grid-width', 1240);
	?>

	@media only screen and (min-width:1240px){
		#Wrapper, .with_aside .content_wrapper {
			max-width: <?php echo esc_attr($gridW); ?>px;
		}
		.section_wrapper, .container {
			max-width: <?php echo esc_attr($gridW - 20); ?>px;
		}
		.layout-boxed.header-boxed #Top_bar.is-sticky{
			max-width: <?php echo esc_attr($gridW); ?>px;
		}
	}

	<?php
		if ($box_padding = mfn_opts_get('layout-boxed-padding')):
	?>

		@media only screen and (min-width:768px){

			.layout-boxed #Subheader .container,
			.layout-boxed:not(.with_aside) .section:not(.full-width),
			.layout-boxed.with_aside .content_wrapper,
			.layout-boxed #Footer .container { padding-left: <?php echo esc_attr($box_padding); ?>; padding-right: <?php echo esc_attr($box_padding); ?>;}

			.layout-boxed.header-modern #Action_bar .container,
			.layout-boxed.header-modern #Top_bar:not(.is-sticky) .container { padding-left: <?php echo esc_attr($box_padding); ?>; padding-right: <?php echo esc_attr($box_padding); ?>;}
		}

	<?php endif; ?>

	<?php
		$mobileGridW = mfn_opts_get('mobile-grid-width', 700);
	?>

	@media only screen and (max-width: 767px){
		.section_wrapper,
		.container,
		.four.columns .widget-area { max-width: <?php echo esc_attr($mobileGridW); ?>px !important; }
	}

<?php endif; ?>

/**
 * Other *****
 */

/* Logo Height */

<?php
	$aLogo = array(
		'height' => intval(mfn_opts_get('logo-height', 60)),
		'vertical_padding' => intval(mfn_opts_get('logo-vertical-padding', 15)),
	);

	$aLogo['top_bar_right_H'] = $aLogo['height'] + ($aLogo['vertical_padding'] * 2);
	$aLogo['top_bar_right_T'] = ($aLogo['top_bar_right_H'] / 2) - 20;

	$aLogo['menu_padding'] = ($aLogo['top_bar_right_H'] / 2) - 30;
	$aLogo['menu_margin'] = ($aLogo['top_bar_right_H'] / 2) - 25;
	$aLogo['responsive_menu_T'] = ($aLogo['height'] / 2) + 10; /* mobile logo | margin: 10px */

	$aLogo['header_fixed_LH'] = ($aLogo['top_bar_right_H'] - 30) / 2 ;
?>

#Top_bar #logo,
.header-fixed #Top_bar #logo,
.header-plain #Top_bar #logo,
.header-transparent #Top_bar #logo {
	height: <?php echo esc_attr($aLogo['height']); ?>px;
	line-height: <?php echo esc_attr($aLogo['height']); ?>px;
	padding: <?php echo esc_attr($aLogo['vertical_padding']); ?>px 0;
}
.logo-overflow #Top_bar:not(.is-sticky) .logo {
    height: <?php echo esc_attr($aLogo['top_bar_right_H']); ?>px;
}
#Top_bar .menu > li > a {
    padding: <?php echo esc_attr($aLogo['menu_padding']); ?>px 0;
}
.menu-highlight:not(.header-creative) #Top_bar .menu > li > a {
	margin: <?php echo esc_attr($aLogo['menu_margin']); ?>px 0;
}
.header-plain:not(.menu-highlight) #Top_bar .menu > li > a span:not(.description) {
    line-height: <?php echo esc_attr($aLogo['top_bar_right_H']); ?>px;
}
.header-fixed #Top_bar .menu > li > a {
    padding: <?php echo esc_attr($aLogo['header_fixed_LH']); ?>px 0;
}

#Top_bar .top_bar_right,
.header-plain #Top_bar .top_bar_right {
	height: <?php echo esc_attr($aLogo['top_bar_right_H']); ?>px;
}
#Top_bar .top_bar_right_wrapper {
	top: <?php echo esc_attr($aLogo['top_bar_right_T']); ?>px;
}
.header-plain #Top_bar a#header_cart,
.header-plain #Top_bar a#search_button,
.header-plain #Top_bar .wpml-languages,
.header-plain #Top_bar a.action_button {
	line-height: <?php echo esc_attr($aLogo['top_bar_right_H']); ?>px;
}

<?php if (! $aLogo['vertical_padding']): ?>
.logo-overflow #Top_bar.is-sticky #logo{padding:0!important;}
<?php endif; ?>

@media only screen and (max-width: 767px){
	#Top_bar a.responsive-menu-toggle {
		top: <?php echo esc_attr($aLogo['responsive_menu_T']); ?>px;
	}
	<?php if ($aLogo['vertical_padding']): ?>
	.mobile-header-mini #Top_bar #logo{
		height:50px!important;
		line-height:50px!important;
		margin:5px 0;
	}
	<?php endif; ?>
}

/* Before After Item */

<?php
	$translate['before'] = mfn_opts_get('translate') ? mfn_opts_get('translate-before', 'Before') : __('Before', 'betheme');
	$translate['after'] = mfn_opts_get('translate') ? mfn_opts_get('translate-after', 'After') : __('After', 'betheme');
?>

.twentytwenty-before-label::before { content: "<?php echo esc_attr($translate['before']); ?>";}
.twentytwenty-after-label::before { content: "<?php echo esc_attr($translate['after']); ?>";}

/* Form | Border width */

<?php $form_border_width = trim(mfn_opts_get('form-border-width')); ?>

<?php if ($form_border_width || ($form_border_width === '0')): ?>

	input[type="date"],input[type="email"],input[type="number"],input[type="password"],input[type="search"],
	input[type="tel"],input[type="text"],input[type="url"],select,textarea,.woocommerce .quantity input.qty{
		border-width: <?php echo esc_attr($form_border_width); ?>;
		<?php if ($form_border_width != '1px'): ?>
			box-shadow: unset;
			resize: none;
		<?php endif; ?>
	}

<?php endif; ?>

<?php
	$form_border_radius = trim(mfn_opts_get('form-border-radius'));
	if( is_numeric( $form_border_radius ) ){
		$form_border_radius .= 'px';
	}
?>

<?php if ($form_border_radius): ?>

	input[type="date"],input[type="email"],input[type="number"],input[type="password"],input[type="search"],
	input[type="tel"],input[type="text"],input[type="url"],select,textarea,.woocommerce .quantity input.qty{
		border-radius: <?php echo esc_attr($form_border_radius); ?>;
	}

<?php endif; ?>

/* Side Slide */

#Side_slide{
	right:-<?php echo esc_attr(mfn_opts_get('responsive-side-slide-width', 250)); ?>px;
	width:<?php echo esc_attr(mfn_opts_get('responsive-side-slide-width', 250)); ?>px;
}
#Side_slide.left{
	left:-<?php echo esc_attr(mfn_opts_get('responsive-side-slide-width', 250)); ?>px;
}

/* Other */

/* Blog teaser | Android phones 1pt line fix - do NOT move it somewhere else */

.blog-teaser li .desc-wrapper .desc{background-position-y:-1px;}
