<?php

class userpro_gmap_admin {

	var $options;

	function __construct() {
		$this->slug = 'userpro';
		$this->subslug = 'userpro-gmap';
		/* Priority actions */
		add_action('userpro_admin_menu_hook', array(&$this, 'add_menu'), 9);
		add_action('admin_init', array(&$this, 'admin_init'), 9);
	}
	
	function admin_init() {
		$this->tabs = array(
			'settings' => __('Google Map','userpro-gmap'),
		);
		$this->default_tab = 'settings';
		
		$this->options = get_option('userpro_gmap');
		if (!get_option('userpro_gmap')) {
			update_option('userpro_gmap', userpro_gmap_default_options() );
		}
		
	}
		
	function add_menu() {
		add_submenu_page( 'userpro', __('Google Map','userpro-gmap'), __('Google Map','userpro-gmap'), 'manage_options', 'userpro-gmap', array(&$this, 'admin_page') );
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
			require_once UPGMAP_PATH.'/admin/panels/'.$tab.'.php';
		}
	}
	
	function save() {
	
		$this->options['usepro_rating_roles_can_rate'] = '';
		
		/* other post fields */
		foreach($_POST as $key => $value) {

			if ($key != 'submit') {
				if (!is_array($_POST[$key])) {
					$this->options[$key] = stripslashes(esc_attr($_POST[$key]));
				} else {
					$this->options[$key] = $_POST[$key];
				}
			}
		}

		update_option('userpro_gmap', $this->options);
		echo '<div class="updated"><p><strong>'.__('Settings saved.','userpro-fav').'</strong></p></div>';
	}

	function reset() {
		update_option('userpro_gmap', userpro_gmap_default_options() );
		$this->options = array_merge( $this->options, userpro_gmap_default_options() );
		echo '<div class="updated"><p><strong>'.__('Settings are reset to default.','userpro-fav').'</strong></p></div>';
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
$userpro_gmap_admin = new  userpro_gmap_admin();
