<?php
$url    = GT3PG_LITE_IMG_URL.'/thumbs/';
$themes = array(
	array(
		'url'  => 'https://gt3themes.com/wordpress/oni-photography-wordpress-theme-for-elementor/',
		'img'  => 'oni.jpg',
		'name' => 'Oni'
	),
	array(
		'url'  => 'https://gt3themes.com/wordpress/soho-fullscreen-photo-video-wordpress-theme/',
		'img'  => 'soho.jpg',
		'name' => 'Soho'
	),
	array(
		'url'  => 'https://gt3themes.com/wordpress/moone-creative-photography-portfolio-wordpress-theme/',
		'img'  => 'moone.jpg',
		'name' => 'Moone'
	),
	array(
		'url'  => 'https://gt3themes.com/wordpress/ewebot-seo-marketing-agency-wordpress-theme/',
		'img'  => 'ewebot.jpg',
		'name' => 'Ewebot'
	),
	array(
		'url'  => 'https://gt3themes.com/wordpress/odry-photography-portfolio-wordpress-theme/',
		'img'  => 'odry.jpg',
		'name' => 'Odry'
	),
	array(
		'url'  => 'https://gt3themes.com/wordpress/juris-law-consulting-services-wordpress-theme/',
		'img'  => 'juris.jpg',
		'name' => 'Juris'
	),
	array(
		'url'  => 'https://gt3themes.com/wordpress/qudos-multi-purpose-elementor-wordpress-theme/',
		'img'  => 'qudos.jpg',
		'name' => 'Qudos'
	),
	array(
		'url'  => 'https://gt3themes.com/wordpress/listingeasy-directory-wordpress-theme/',
		'img'  => 'listingeasy.jpg',
		'name' => 'Listingeasy'
	),
	array(
		'url'  => 'https://gt3themes.com/wordpress/optima-psychologist-psychology-center-wordpress-theme/',
		'img'  => 'optima.jpg',
		'name' => 'Optima'
	),
	array(
		'url'  => 'https://gt3themes.com/wordpress/zohar-business-consulting-elementor-wordpress-theme/',
		'img'  => 'zohar.jpg',
		'name' => 'Zohar'
	),
	array(
		'url'  => 'https://gt3themes.com/wordpress/woocommerce-multi-purpose-responsive-wordpress-theme-wizestore/',
		'img'  => 'wizestore.jpg',
		'name' => 'Wizestore'
	),
	array(
		'url'  => 'https://gt3themes.com/wordpress/sunergy-multi-purpose-green-energy-and-ecology-wordpress-theme/',
		'img'  => 'sunergy.jpg',
		'name' => 'Sunergy'
	),
)
?>

<div class="most_popular_wp_themes">
	<h2><?php echo esc_html__('Premium WordPress Themes', 'gt3pg_pro') ?></h2>
	<p><?php echo esc_html__('GT3themes provides high-quality WordPress themes which are easy to use and customize.', 'gt3pg_pro') ?></p>
	<div class="most_popular_wp_themes_wrap">
		<?php
		foreach($themes as $theme) {
			echo '<div class="wp_themes_item"><a href="'.$theme['url'].'" target="_blank"><img src="'.$url.$theme['img'].'" alt="'.$theme['name'].'" /><span class="purchase_wp_theme">'. esc_html__('View Demo', 'gt3pg_pro') .'</span></a></div>';
		}
		?>
	</div>
</div>
