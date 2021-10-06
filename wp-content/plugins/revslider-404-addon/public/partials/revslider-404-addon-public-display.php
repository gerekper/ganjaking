<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_404_Addon
 * @subpackage Revslider_404_Addon/public/partials
 */

$revslider_404_addon_values = array();
parse_str(get_option('revslider_404_addon'), $revslider_404_addon_values);

//defaults
$revslider_404_addon_values['revslider-404-addon-type'] = isset($revslider_404_addon_values['revslider-404-addon-type']) ? $revslider_404_addon_values['revslider-404-addon-type'] : 'slider';
// $revslider_404_addon_values['revslider-404-addon-active'] = isset($revslider_404_addon_values['revslider-404-addon-active']) ? $revslider_404_addon_values['revslider-404-addon-active'] : '0';
$revslider_404_addon_values['revslider-404-addon-slider'] = isset($revslider_404_addon_values['revslider-404-addon-slider']) ? $revslider_404_addon_values['revslider-404-addon-slider'] : '';
$revslider_404_addon_values['revslider-404-addon-page'] = isset($revslider_404_addon_values['revslider-404-addon-page']) ? $revslider_404_addon_values['revslider-404-addon-page'] : '';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<title><?php echo !empty($revslider_404_addon_values['revslider-404-addon-page-title']) && $revslider_404_addon_values['revslider-404-addon-type']=="slider" ? stripslashes($revslider_404_addon_values['revslider-404-addon-page-title']) : get_bloginfo( 'name' );?></title>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<!--[if lt IE 9]>
	<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/html5.js"></script>
	<![endif]-->
	<?php wp_head(); ?>
	<style>
		body { background: transparent; }
		body:before , body:after { height:0; }
	</style>
</head>

<body <?php body_class(); ?>>
<div>
	<?php
		if($revslider_404_addon_values['revslider-404-addon-type'] == 'slider'){
			$content = '[rev_slider alias="'.$revslider_404_addon_values['revslider-404-addon-slider'].'"]';
		}
		else {
			$content = get_post_field('post_content', $revslider_404_addon_values['revslider-404-addon-page']);
		}
		echo do_shortcode($content);
	?>
</div><!-- .site-content -->
<?php wp_footer(); ?>

</body>
</html>