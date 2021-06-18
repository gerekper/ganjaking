<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UPDBAdmin' ) ) :
	class UPDBAdmin{
		var $options;
		function __construct(){
			$this->slug = 'userpro';
			$this->subslug = 'userpro-dashboard';
			add_action('userpro_admin_menu_hook', array(&$this, 'add_menu'), 9);
			add_action('admin_init', array(&$this, 'admin_init'), 9);
		}

		function add_menu(){

			add_submenu_page( 'userpro', __('User Dashboard','userpro-dashboard'), __('User Dashboard','userpro-dashboard'), 'manage_options', 'userpro-dashboard', array(&$this, 'admin_page') );
		}

		function admin_init(){
			if( !isset( $updb_default_options ) ){
				$updb_default_options = new UPDBDefaultOptions();
			}
			$this->tabs = array(
				'settings' => __('Settings','userpro-dashboard'),
				'custom-widgets' => __('Custom Widgets','userpro-dashboard'),
				'admin-dashboard-layout' => __('Admin Dashboard Layout','userpro-dashboard'),
				'updb_licensing' => __('UserPro Dashboard Licensing','userpro-dashboard')
			);
			$this->default_tab = 'settings';
			$this->options = get_option('userpro_db');
						
			if (!get_option('userpro_db')) {
				update_option('userpro_db', $updb_default_options->updb_default_options() );
			}
		}

		function admin_page() {

			if (isset($_POST['submit'])) {
				$this->save();
			}

			if (isset($_POST['reset-options'])) {
				$this->reset();
			}

			if (isset($_POST['updb_verify'])){
				$this->verify_updb_license();
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
		<?php
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
			require_once UPDB_PATH.'/admin/panels/'.$tab.'.php';
			}
		}
		function save() {
			foreach($_POST as $key => $value) {
			if ($key != 'submit') {
				if (!is_array($_POST[$key])) {
					$this->options[$key] = esc_attr($_POST[$key]);
				} else {
					$this->options[$key] = $_POST[$key];
				}
			   }
			}
		
			update_option('userpro_db', $this->options);
			echo '<div class="updated"><p><strong>'.__('Settings saved.','userpro-dashboard').'</strong></p></div>';
		}

		function reset() {
			if( !isset( $updb_default_options ) ){
				$updb_default_options = new UPDBDefaultOptions();
			}
			update_option('userpro_db', $updb_default_options->updb_default_options() );
			$this->options = array_merge( $this->options, $updb_default_options->updb_default_options() );
			echo '<div class="updated"><p><strong>'.__('Settings are reset to default.','userpro-dashboard').'</strong></p></div>';
		}

		function verify_updb_license() {
			global $userpro;
			$code = $_POST['userpro_dashboard_code'];
			if ($code == ''){
				echo '<div class="error"><p><strong>'.__('Please enter a purchase code.','userpro').'</strong></p></div>';
			} else {
				$values = get_option('userpro_db');
				$values['userpro_dashboard_code'] = $code;
				update_option('userpro_db', $values);

				if ( $userpro->verify_purchase($code, '13z89fdcmr2ia646kphzg3bbz0jdpdja', 'DeluxeThemes', '15375277') ){
					$userpro->validate_license($code);
					echo '<div class="updated fade"><p><strong>'.__('Thanks for activating UserPro Dashboard!','userpro-dashboard').'</strong></p></div>';
				} else {
					$userpro->invalidate_license($code);
					echo '<div class="error"><p><strong>'.__('You have entered an invalid purchase code or the Envato API could be down at the moment.','userpro-dashboard').'</strong></p></div>';
				}
			}
		}

	}
	new UPDBAdmin();
endif;
