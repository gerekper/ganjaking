<?php

if(!defined('ABSPATH')) {
	exit;
}

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Sharing $widget */

$settings = array(
	'select_layout' => 'vertical',
	'select_alignment' => 'align_left',
	'sharing_type' => 'type_icon',
	'sharing_label' => '',
	'icon_facebook' => 'yes',
	'icon_twitter' => 'yes',
	'icon_pinterest' => 'yes',
	'icon_google' => '',
	'icon_email' => 'yes',
	'icon_linkedin' => '',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_sharing_core',
	$settings['select_layout'],
	$settings['select_alignment'],
));

	$meta_label = $sharing_links_block = $links_block = $sharing_content = $pinterest_img = '';

	if (!empty($settings['sharing_label'])) {
		$meta_label = '<span class="gt3_sharing_label_title">' . esc_html($settings['sharing_label']) . '</span>';
	}

	$featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail');
	if (strlen($featured_image[0]) > 0) {
		$pinterest_img = $featured_image[0];
	}

	// Facebook
	if ($settings['icon_facebook'] == 'yes') {
		if ($settings['sharing_type'] == 'type_icon') {
			$icon_fb = '<i class="link_type_icon fa fa-facebook"></i>';
		} else {
			$icon_fb = '<span class="link_type_text">' . esc_html__('Facebook', 'gt3_themes_core') . '</span>';
		}
		$links_block .= '<a target="_blank" href="'. esc_url('https://www.facebook.com/share.php?u='. get_permalink()) .'" class="core_sharing_fb">'. $icon_fb . '</a>';
	}
	// Twitter
	if ($settings['icon_twitter'] == 'yes') {
		if ($settings['sharing_type'] == 'type_icon') {
			$icon_twitter = '<i class="link_type_icon fa fa-twitter"></i>';
		} else {
			$icon_twitter = '<span class="link_type_text">' . esc_html__('Twitter', 'gt3_themes_core') . '</span>';
		}
		$links_block .= '<a target="_blank" href="'. esc_url('https://twitter.com/intent/tweet?text='. get_the_title() .'>&amp;url='. get_permalink()) .'" class="core_sharing_twitter">'. $icon_twitter . '</a>';
	}
	// Pinterest
	if ($settings['icon_pinterest'] == 'yes' && strlen($pinterest_img) > 0) {
		if ($settings['sharing_type'] == 'type_icon') {
			$icon_pinterest = '<i class="link_type_icon fa fa-pinterest"></i>';
		} else {
			$icon_pinterest = '<span class="link_type_text">' . esc_html__('Pinterest', 'gt3_themes_core') . '</span>';
		}
		$links_block .= '<a data-elementor-open-lightbox="no" target="_blank" href="'. esc_url('https://pinterest.com/pin/create/button/?url='. get_permalink() .'&media='. $featured_image[0]) .'" class="core_sharing_pinterest">'. $icon_pinterest . '</a>';
	}
	// Google+
	if ($settings['icon_google'] == 'yes') {
		if ($settings['sharing_type'] == 'type_icon') {
			$icon_google = '<i class="link_type_icon fa fa-google"></i>';
		} else {
			$icon_google = '<span class="link_type_text">' . esc_html__('Google+', 'gt3_themes_core') . '</span>';
		}
		$links_block .= '<a target="_blank" href="'. esc_url('https://plus.google.com/share?url='. get_permalink()) .'" class="core_sharing_google">'. $icon_google . '</a>';
	}
	// Email
	if ($settings['icon_email'] == 'yes') {
		ob_start();
		the_title('','',true);
		$email_title = ob_get_clean();
		ob_start();
		the_permalink();
		$email_permalink = ob_get_clean();

		$email_link = 'mailto:?subject='. $email_title . '&body='. $email_permalink;

		if ($settings['sharing_type'] == 'type_icon') {
			$icon_email = '<i class="link_type_icon fa fa-envelope"></i>';
		} else {
			$icon_email = '<span class="link_type_text">' . esc_html__('Email', 'gt3_themes_core') . '</span>';
		}
		$links_block .= '<a target="_blank" href="' . $email_link . '" class="core_sharing_email">'. $icon_email . '</a>';
	}
	// LinkedIn
	if ($settings['icon_linkedin'] == 'yes') {
		if ($settings['sharing_type'] == 'type_icon') {
			$icon_linkedin = '<i class="link_type_icon fa fa-linkedin"></i>';
		} else {
			$icon_linkedin = '<span class="link_type_text">' . esc_html__('LinkedIn', 'gt3_themes_core') . '</span>';
		}
		$links_block .= '<a target="_blank" href="'. esc_url('https://www.linkedin.com/shareArticle?mini=true&url='.get_permalink().'&title='.get_the_title().'&source='.get_bloginfo("name")) .'" class="core_sharing_linkedin">'. $icon_linkedin . '</a>';
	}

	if (!empty($links_block)) {
		$sharing_links_block = '<div class="gt3_sharing_links_block">' . $links_block . '</div>';
	}

	$sharing_content .= $meta_label . $sharing_links_block;

?>
	<div <?php echo ''.$widget->get_render_attribute_string('wrapper') ?>>
		<?php echo ''.$sharing_content ?>
	</div>
<?php

