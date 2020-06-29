<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Maintenance_Addon
 * @subpackage Revslider_Maintenance_Addon/public/partials
 */
if(!defined('ABSPATH')) exit();

$revslider_maintenance_addon_values = Revslider_Maintenance_Addon_Public::return_mta_data();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<title><?php echo !empty($revslider_maintenance_addon_values['revslider-maintenance-addon-page-title']) && $revslider_maintenance_addon_values['revslider-maintenance-addon-type']=="slider" ? stripslashes($revslider_maintenance_addon_values['revslider-maintenance-addon-page-title']) : get_bloginfo( 'name' );?></title>
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
		if($revslider_maintenance_addon_values['revslider-maintenance-addon-type'] == 'slider'){
			$content = '[rev_slider alias="'.$revslider_maintenance_addon_values['revslider-maintenance-addon-slider'].'"]';
		}else{
			$content = get_post_field('post_content', $revslider_maintenance_addon_values['revslider-maintenance-addon-page']);
		}
		echo do_shortcode($content);
		
		if($revslider_maintenance_addon_values['revslider-maintenance-addon-countdown-active']){
			Revslider_Maintenance_Addon_Public::add_js($revslider_maintenance_addon_values);
		}
		?>
	</div><!-- .site-content -->
	<?php wp_footer(); ?>

	</body>
</html>