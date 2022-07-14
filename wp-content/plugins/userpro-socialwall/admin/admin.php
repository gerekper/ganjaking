<?php
class userpro_userwall_admin {

	var $options;

	function __construct() {
	
		/* Plugin slug and version */
		$this->slug = 'userpro';
		$this->subslug = 'userpro-userwall';
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->plugin_data = get_plugin_data( UPS_PLUGIN_DIR . 'userpro-userwall.php', false, false);
		$this->version = $this->plugin_data['Version'];
		
		/* Priority actions */
		add_action('userpro_admin_menu_hook', array(&$this, 'add_menu'), 9);
		
		add_action('admin_init', array(&$this, 'admin_init'), 9);
		
	}
	
	function admin_init() {
	
		$this->tabs = array(
			'options' => __('Userwall','userpro-userwall'),
			'report' => __('Report','userpro-userwall'),
			'deletepost' => __('Delete Social Wall Posts and Comments','userpro-userwall'),
			'email' => __('Email Notification','userpro-userwall'),
			'licensing' => __('Licensing','userpro-userwall'),
		);
		$this->default_tab = 'options';
		
		$this->options = get_option('userpro_userwall');
		if (!get_option('userpro_userwall')) {
			update_option('userpro_userwall', userpro_userwall_default_options() );
		}
		
	}
	
	
	function add_menu() {
		add_submenu_page( 'userpro', __('SocialWall','userpro-userwall'), __('SocialWall','userpro-userwall'), 'manage_options', 'userpro-userwall', array(&$this, 'admin_page') );
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
			require_once UPS_PLUGIN_DIR.'admin/panels/'.$tab.'.php';
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
		
		update_option('userpro_userwall', $this->options);
		echo '<div class="updated"><p><strong>'.__('Settings saved.','userpro-userwall').'</strong></p></div>';
	}

	function reset() {
		update_option('userpro_userwall', userpro_userwall_default_options() );
		$this->options = array_merge( $this->options, userpro_userwall_default_options() );
		echo '<div class="updated"><p><strong>'.__('Settings are reset to default.','userpro-userwall').'</strong></p></div>';
	}
	
	function rebuild_pages() {
		userpro_userwallsetup($rebuild=1);
		echo '<div class="updated"><p><strong>'.__('Your plugin pages have been rebuilt successfully.','userpro-userwall').'</strong></p></div>';
	}

	function verify_socialwall_license() {
			global $userpro;
			$code = $_POST['userpro_userwall_envato_code'];
			if ($code == ''){
				echo '<div class="error"><p><strong>'.__('Please enter a purchase code.','userpro-userwall').'</strong></p></div>';
			} else {
				if ( $userpro->verify_purchase($code, '13z89fdcmr2ia646kphzg3bbz0jdpdja', 'DeluxeThemes', '5958681') ){
					
					echo '<div class="updated fade"><p><strong>'.__('Thanks for activating UserPro Socialwall!','userpro-userwall').'</strong></p></div>';
				} else {
					
					echo '<div class="error"><p><strong>'.__('You have entered an invalid purchase code or the Envato API could be down at the moment.','userpro-dashboard').'</strong></p></div>';
				}
			}
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
		if (isset($_POST['userpro_userwall_envato_code'])) {
			$this->verify_socialwall_license();
			$this->save();
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

$userpro_userwall_admin = new userpro_userwall_admin();
?>
