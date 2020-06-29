<?php

class userpro_dg_admin {

	var $options;

	function __construct() {
	
		/* Plugin slug and version */
		$this->slug = 'userpro';
		$this->subslug = 'userpro-badges';
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->plugin_data = get_plugin_data( userpro_dg_path . 'index.php', false, false);
		$this->version = $this->plugin_data['Version'];
		
		/* Priority actions */
		add_action('userpro_admin_menu_hook', array(&$this, 'add_menu'), 9);
		//add_action('admin_enqueue_scripts', array(&$this, 'add_styles'), 9);
		add_action('admin_print_scripts-userpro_page_userpro-badges' , array(&$this, 'add_admin_scripts'));
		add_action('admin_print_styles-userpro_page_userpro-badges' , array(&$this, 'add_admin_styles'));
		add_action('admin_head', array(&$this, 'admin_head'), 9 );
		add_action('admin_init', array(&$this, 'admin_init'), 9);
		
	}
	
	function add_admin_scripts(){
		wp_enqueue_media();
		wp_register_script( 'userpro_badges', userpro_dg_url . 'admin/scripts/admin.js', array( 
			'jquery'
		) );
		wp_enqueue_script( 'userpro_badges' );
	}
	
	function add_admin_styles(){
		wp_register_style('userpro_badges', userpro_dg_url . 'admin/css/admin.css');
		wp_enqueue_style('userpro_badges');
	}
	
	function admin_init() {
	
		$this->tabs = array(
			'manage' => __('Add Badges','userpro'),
			'user_badges' => __('Edit User Badges','userpro'),
			'achievement' => __('Edit Badges','userpro'),
			'envato' => __('Envato Customers','userpro'),
		);
		$this->default_tab = 'manage';
		
		$this->options = get_option('userpro_dg');
		if (!get_option('userpro_dg')) {
			update_option('userpro_dg', userpro_dg_default_options() );
		}
		
	}
	
	function admin_head(){

	}

	function add_styles(){
	
	}
	
	function add_menu() {
		add_submenu_page( 'userpro', __('Badges and Achievements','userpro'), __('Badges and Achievements','userpro'), 'manage_options', 'userpro-badges', array(&$this, 'admin_page') );
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
			require_once userpro_dg_path.'admin/panels/'.$tab.'.php';
		}
	}
	
	function save() {
	
		$this->options['exclude_post_types'] = '';
		
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
		update_option('userpro_dg', $this->options);
		echo '<div class="updated"><p><strong>'.__('Settings saved.','userpro').'</strong></p></div>';
	}

	function reset() {
		update_option('userpro_dg', userpro_dg_default_options() );
		$this->options = array_merge( $this->options, userpro_dg_default_options() );
		echo '<div class="updated"><p><strong>'.__('Settings are reset to default.','userpro').'</strong></p></div>';
	}
	
	function install_badge(){
		global $userpro_badges;
		$res = $userpro_badges->new_badge( $_POST );
		if (isset($res['error'])){
			echo '<div class="error"><p><strong>'.$res['error'].'</strong></p></div>';
		}
	}
	
	function find_badges(){
		global $userpro_badges;
		$res = $userpro_badges->find_badges( $_POST );
		if (isset($res['error'])){
			echo '<div class="error"><p><strong>'.$res['error'].'</strong></p></div>';
		}
	}

	function admin_page() {
	
		if (isset($_POST['find-user-badges'])){
			$this->find_badges();
		}
	
		if (isset($_POST['insert-badge'])){
			$this->install_badge();
		}

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

$userpro_badges_admin = new userpro_dg_admin();
