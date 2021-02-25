<?php
/**
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_login_Addon
 * @subpackage Revslider_login_Addon/public/partials
 */
require_once("../../../../../wp-load.php");
$revslider_login_addon_values = array();
parse_str(get_option('revslider_login_addon'), $revslider_login_addon_values);

//defaults
$revslider_login_addon_values['revslider-login-addon-type'] = isset($revslider_login_addon_values['revslider-login-addon-type']) ? $revslider_login_addon_values['revslider-login-addon-type'] : 'slider';
$revslider_login_addon_values['revslider-login-addon-slider'] = isset($revslider_login_addon_values['revslider-login-addon-slider']) ? $revslider_login_addon_values['revslider-login-addon-slider'] : '';
$revslider_login_addon_values['revslider-login-addon-page'] = isset($revslider_login_addon_values['revslider-login-addon-page']) ? $revslider_login_addon_values['revslider-login-addon-page'] : '';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<title><?php echo !empty($revslider_login_addon_values['revslider-login-addon-page-title'] && $revslider_login_addon_values['revslider-login-addon-type']=="slider") ? stripslashes($revslider_login_addon_values['revslider-login-addon-page-title']) : get_bloginfo( 'name' );?></title>
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
		if($revslider_login_addon_values['revslider-login-addon-type'] == 'slider'){
			$content = '[rev_slider alias="'.$revslider_login_addon_values['revslider-login-addon-slider'].'"]';
		}
		else {
			$content = get_post_field('post_content', $revslider_login_addon_values['revslider-login-addon-page']);
		}
		$content = do_shortcode($content);
		echo $content;

		if(strpos($content, 'input type="password" name="pwd" id="user_pass"') === false && strpos($content, 'form id="lostpasswordform"') === false){
			echo "<div style='width:300px;margin:40px auto;'>".__('There is no login form in the selected content, please check your settings.<br><br>') . do_shortcode('[revslider-login-form]') . "</div>";
		}
	?>
</div><!-- .site-content -->
<?php wp_footer(); ?>
</body>
</html>


