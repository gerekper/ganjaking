<?php

class userpro_memberlists_admin {

	var $options;

	function __construct() {
	
		/* Plugin slug and version */
		$this->slug = 'userpro';
		$this->subslug = 'userpro-memberlists';
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->plugin_data = get_plugin_data( UPML_PLUGIN_DIR . '/class-userpro-memberlists-setup.php', false, false);
		$this->version = $this->plugin_data['Version'];
		
		/* Priority actions */
		add_action('userpro_admin_menu_hook', array(&$this, 'add_menu'), 9);
		add_action('admin_init', array(&$this, 'admin_init'), 9);
		
	}
	
	function admin_init() {
	
		$this->tabs = array(
			'settings' => __('Memberlist Layouts','userpro-memberlists'),
			'licensing' => __('Licensing','userpro-memberlists')
		);
		$this->default_tab = 'settings';
		
		$this->options = get_option('userpro_memberlists');
		if (!get_option('userpro_memberlists')) {
			update_option('userpro_memberlists', userpro_memberlists_default_options() );
		}
		
	}
		
	function add_menu() {
		add_submenu_page( 'userpro', __('Memberlist Layouts','userpro-memberlists'), __('Memberlist Layouts','userpro-memberlists'), 'manage_options', 'userpro-memberlists', array(&$this, 'admin_page') );
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
			require_once UPML_PLUGIN_DIR.'/admin/panels/'.$tab.'.php';
		}
	}
	
	function save() {
	
		/* other post fields */
		foreach($_POST as $key => $value) {

			if ($key != 'submit' || $key != 'verify-license-memberslists') {
				if (!is_array($_POST[$key])) {
					$this->options[$key] = stripslashes(esc_attr($_POST[$key]));
				} else {
					$this->options[$key] = $_POST[$key];
				}
			}
		}
                if( isset($_POST['verify-license-memberslists'] ) ){
                    $code = $_POST['userpro_memberlists_envato_code'];
                    global $userpro;

                    if ($code == ''){
                            echo '<div class="error"><p><strong>'.__('Please enter a purchase code.','userpro').'</strong></p></div>';
                    } else {
                            if ( $userpro->verify_purchase($code, '13z89fdcmr2ia646kphzg3bbz0jdpdja', 'DeluxeThemes', '5958681') ){
                                    echo '<div class="updated fade"><p><strong>'.__('Thanks for activating UserPro Memberlists Addon!','userpro-memberlists').'</strong></p></div>';
                            } else {
                                    echo '<div class="error"><p><strong>'.__('You have entered an invalid purchase code or the Envato API could be down at the moment.','userpro-memberlists').'</strong></p></div>';
                            }
                    }
                }
		
		update_option('userpro_memberlists', $this->options);
		echo '<div class="updated"><p><strong>'.__('Settings saved.','userpro-fav').'</strong></p></div>';
	}

	function reset() {
		update_option('userpro_memberlists', userpro_memberlists_default_options() );
		$this->options = array_merge( $this->options, userpro_memberlists_default_options() );
		echo '<div class="updated"><p><strong>'.__('Settings are reset to default.','userpro-fav').'</strong></p></div>';
	}

	function admin_page() {

		if (isset($_POST['submit']) || isset($_POST['verify-license-memberslists']) ) {
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
$userpro_memberlists_admin = new  userpro_memberlists_admin();
