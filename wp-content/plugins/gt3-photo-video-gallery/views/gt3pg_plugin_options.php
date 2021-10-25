<?php
if(!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly
use GT3\PhotoVideoGallery\Settings;
use GT3\PhotoVideoGallery\Assets;

global $gt3_photo_gallery_defaults, $gt3_photo_gallery;
$plugin_info    = get_plugin_data(GT3PG_PLUGINPATH.'/gt3-photo-video-gallery.php');
$theme_list     = (array) gt3_banner_addon();
$plugin_title   = apply_filters('gt3pg_admin_title', GT3PG_ADMIN_TITLE);
$plugin_version = apply_filters('gt3pg_admin_version', $plugin_info['Version']);
$plugin_help    = apply_filters('gt3pg_admin_help', GT3PG_WORDPRESS_URL);
//wp_enqueue_script('editor');
wp_enqueue_script('gt3pg_admin_js');
?>
<script>
	var gt3pg_admin_ajax_url = "<?php echo admin_url("admin-ajax.php")?>";
</script>

<div class="gt3pg_optimized_notice" style="display: none">
	<?php esc_html_e('It requires GT3 Image Optimizer plugin', 'gt3pg'); ?>
</div>
<div class="gt3pg_admin_wrap">
	<div class="gt3pg_inner_wrap">
		<form action="" method="post" class="gt3pg_page_settings">
			<div class="gt3pg_main_line">
				<div class="gt3pg_themename">
					<?php echo $plugin_title ?>
					<span class="gt3pg_theme_ver"><?php echo $plugin_version ?></span>
				</div>
				<div class="gt3pg_links">
					<a href="<?php echo esc_attr($plugin_help) ?>" target="_blank"><?php esc_html_e('Need Help?', 'gt3pg') ?></a>
				</div>
				<div class="clear"></div>
				<?php
				$plugin            = 'gt3-photo-video-gallery-pro/gt3-photo-video-gallery-pro.php';
				$installed_plugins = get_plugins();

				if(!isset($installed_plugins[$plugin])) {
					?>
					<div class="get_pro">The <span class="gt3pg_theme_ver">Pro version</span> is now available. You can check it here -&gt;
						<a href="https://gt3themes.com/gt3-photo-video-gallery-pro-is-live-now/" style="color: #ffffff;">View Pro Version</a>
					</div>
					<?php
				}
				?>
			</div>
			<?php
				wp_enqueue_script('block-library');
				wp_enqueue_script('editor');
				wp_enqueue_script('wp-editor');
				wp_enqueue_script('wp-components');
				wp_enqueue_style('wp-components');
				wp_enqueue_style('wp-element');
				wp_enqueue_style('wp-blocks-library');
				wp_enqueue_media();
				wp_enqueue_script('media-grid');
				wp_enqueue_script('media');


				wp_enqueue_script('gt3pg_settings', GT3PG_PLUGINROOTURL.'/dist/js/admin/settings.js', null, GT3PG_PLUGIN_VERSION);
				wp_enqueue_style('gt3pg_settings', GT3PG_PLUGINROOTURL.'/dist/css/admin/settings.css', null, GT3PG_PLUGIN_VERSION);
				wp_enqueue_style('gt3pg_admin_css', GT3PG_PLUGINROOTURL.'/dist/css/admin/admin.css', null, GT3PG_PLUGIN_VERSION);

				$settings = Settings::instance();
				$assets = Assets::instance();

				wp_localize_script(
					'gt3pg_settings',
					'gt3pg_lite',
					array(
						'defaults' => $settings->getSettings(),
						'blocks'   => array_map('strtolower', $settings->getBlocks()),
						'plugins'  => $assets->getPlugins(),
					)
				);

				?>
				<div class="edit-post-layout">
					<div class="edit-post-sidebar">
						<div id="gt3_editor"></div>
					</div>
				</div>
		</form>
	</div>
</div>

