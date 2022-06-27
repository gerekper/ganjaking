<?php

class userpro_request_admin {

	var $options;

	function __construct() {
	
		/* Plugin slug and version */
		$this->slug = 'userpro';
		$this->subslug = 'userpro-request';
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->plugin_data = get_plugin_data( userpro_request_path . 'index.php', false, false);
		$this->version = $this->plugin_data['Version'];
		
		/* Priority actions */
		add_action('userpro_admin_menu_hook', array(&$this, 'add_menu'), 8);
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles'), 9);
		add_action('admin_head', array(&$this, 'admin_head'), 9 );
		add_action('admin_init', array(&$this, 'admin_init'), 9);
		
	}
	
	function admin_init() {
	
		$this->tabs = array(
			'settings' => __('User Requests','userpro')
		);
		$this->default_tab = 'settings';
		
		$this->options = get_option('userpro');
		
		if (!get_option('userpro')) {
			update_option('userpro', userpro_default_options() );
		}
		
	}
	
	function get_pending_verify_requests_count_only(){
		$count = 0;
		
		// verification status
		$pending = get_option('userpro_verify_requests');
		if (is_array($pending) && count($pending) > 0){
			$count = count($pending);
		}
		
		// waiting email approve
		$users = get_users(array(
			'meta_key'     => '_account_status',
			'meta_value'   => 'pending',
			'meta_compare' => '=',
		));
		if (isset($users)) {
			$count += count($users);
		}
		
		// waiting admin approve
		$users = get_users(array(
			'meta_key'     => '_account_status',
			'meta_value'   => 'pending_admin',
			'meta_compare' => '=',
		));
		if (isset($users)) {
			$count += count($users);
		}
		
		if ($count > 0){
			return $count;
		}
	}
	
	function admin_head(){

	}

	function add_styles(){
		
	}
	
	function add_menu() {
		$pending_count = $this->get_pending_verify_requests_count_only();
		$pending_title = esc_attr( sprintf(__( '%d new verification requests','userpro'), $pending_count ) );
		if ($pending_count > 0){
		$menu_label = sprintf( __( 'User Requests %s','userpro' ), "<span class='update-plugins count-$pending_count' title='$pending_title'><span class='update-count'>" . number_format_i18n($pending_count) . "</span></span>" );
		} else {
		$menu_label = __('User Requests','userpro');
		}
		add_submenu_page( 'userpro', $menu_label, $menu_label, 'manage_options', 'userpro-request', array(&$this, 'admin_page') );
	}

	function admin_tabs( $current = null ) {
			$tabs = $this->tabs;
			$links = array();
			if ( isset ( $_GET['tab'] ) ) {
				$current = $_GET['tab'];
			} else {
				$current = $this->default_tab;
			}
			foreach( $tabs as $tab => $name ) :
				if ( $tab == $current ) :
					$links[] = "<a class='nav-tab nav-tab-active' href='?page=".$this->subslug."&tab=$tab'>$name</a>";
				else :
					$links[] = "<a class='nav-tab' href='?page=".$this->subslug."&tab=$tab'>$name</a>";
				endif;
			endforeach;
			foreach ( $links as $link )
				echo $link;
	}

	function get_tab_content() {
		$screen = get_current_screen();
		if( strstr($screen->id, $this->subslug ) ) {
			if ( isset ( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			} else {
				$tab = $this->default_tab;
			}
			require_once userpro_request_path.'admin/panels/'.$tab.'.php';
		}
	}
	
	function save() {
		/* other post fields */
		foreach($_POST as $key => $value) {
			if ($key != 'submit') {
				if (!is_array($_POST[$key])) {
					$this->options[$key] = esc_attr($_POST[$key]);
				} else {
					$this->options[$key] = $_POST[$key];
				}
			}
		}
		
		update_option('userpro', $this->options);
		echo '<div class="updated"><p><strong>'.__('Settings saved.','userpro').'</strong></p></div>';
	}

	function reset() {
		update_option('userpro', userpro_default_options() );
		$this->options = array_merge( $this->options, userpro_default_options() );
		echo '<div class="updated"><p><strong>'.__('Settings are reset to default.','userpro').'</strong></p></div>';
	}
	
	function rebuild_pages() {
		userpro_ed_setup($rebuild=1);
		echo '<div class="updated"><p><strong>'.__('Your plugin pages have been rebuilt successfully.','userpro').'</strong></p></div>';
	}

	function admin_page() {

		if (isset($_POST['submit'])) {
			$this->save();
		}

		if (isset($_POST['reset-options'])) {
			$this->reset();
		}
		
		if (isset($_POST['rebuild-pages'])) {
			$this->rebuild_pages();
		}
		
	?>
	
		<div class="wrap <?php echo $this->slug; ?>-admin">
			
			<?php userpro_admin_bar(); ?>
			
			<h2 class="nav-tab-wrapper"><?php $this->admin_tabs(); ?></h2>

			<div class="<?php echo $this->slug; ?>-admin-contain">
				
				<?php $this->get_tab_content(); ?>
				
				<div class="clear"></div>
				
			</div>
			
		</div>

	<?php }

}

$GLOBALS['userpro_request_admin'] = new userpro_request_admin();
