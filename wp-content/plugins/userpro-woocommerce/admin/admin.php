<?php
class UPWoocommerceAdmin {

	var $options;
	function __construct() {
		
		/* Plugin slug and version */
		$this->slug = 'userpro';
		$this->subslug = 'userpro-woocommerce';
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->plugin_data = get_plugin_data( UPWPATH . 'userpro-woocommerce.php', false, false);
		$this->version = $this->plugin_data['Version'];
		
		/* Priority actions */
		add_action('userpro_admin_menu_hook', array(&$this, 'add_menu'), 9);
		add_action('admin_init', array(&$this, 'admin_init'), 9);
	}
	
	function admin_init() {
	
		$this->tabs = array(
			'settings' => __('Settings','userpro-woocommerce'),
			'purchases' => __('Licensing','userpro-woocommerce'),	
		);
		$this->default_tab = 'settings';
		$this->options = get_option('userpro_woocommerce');
	}
	
	
	function add_menu() {
		add_submenu_page( 'userpro', __('WooCommerce','userpro-woocommerce'), __('WooCommerce','userpro-woocommerce'), 'manage_options', 'userpro-woocommerce', array(&$this, 'admin_page') );
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
			require_once UPWPATH.'admin/panels/'.$tab.'.php';
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
		
		update_option('userpro_woocommerce', $this->options);
		echo '<div class="updated"><p><strong>'.__('Settings saved.','userpro-woocommerce').'</strong></p></div>';
	}

	function reset() {
		$upw_default_options = new UPWDefaultOptions();
		update_option('userpro_woocommerce', $upw_default_options->userpro_woocommerce_default_options() );
		$this->options = array_merge( $this->options, $upw_default_options->userpro_woocommerce_default_options() );
		echo '<div class="updated"><p><strong>'.__('Settings are reset to default.','userpro-woocommerce').'</strong></p></div>';
	}
        
        function checkWishList() {
            if($_POST['upw_show_wishlist'] == "y"){
                $activated_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
                if( !in_array('yith-woocommerce-wishlist/init.php', $activated_plugins) && !in_array('yith-woocommerce-wishlist-premium/init.php', $activated_plugins) && !in_array('woocommerce-wishlists/woocommerce-wishlists.php', $activated_plugins) ){
                   
                    $this->upw_activation_notice();
                    return 0;
                    
                }
            }
        }
        function upw_activation_notice(){
		echo '<div class="error" role="alert"><p>'.__('Attention: Wishlist requires YITH WooCommerce Wishlist or YITH WooCommerce Wishlist Premium or WooCommerce Wishlists to be installed and activated.').'</p></div>';
                $_POST['upw_show_wishlist'] = "n";
		return 0;
	}
	function admin_page() {

		if (isset($_POST['submit'])) {
                        $this->checkWishList();
			$this->save();
		}

		if (isset($_POST['reset-options'])) {
			$this->reset();
		}
		if (isset($_POST['verify-upw-license'])) {
			$this->verify_upw_license();
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
	
function verify_upw_license() {
		global $userpro;
		$code = $_POST['upw_purchases_code'];
		
		if ($code == ''){
			echo '<div class="error"><p><strong>'.__('Please enter a purchase code.','userpro-woocommerce').'</strong></p></div>';
		} else {
			if ( $userpro->verify_purchase($code, '13z89fdcmr2ia646kphzg3bbz0jdpdja', 'DeluxeThemes', '5958681') ){
				$userpro->validate_license($code);
				echo '<div class="updated fade"><p><strong>'.__('Thanks for activating UserPro Woocommerce Addon !','userpro-woocommerce').'</strong></p></div>';
			} else {
				$userpro->invalidate_license($code);
				echo '<div class="error"><p><strong>'.__('You have entered an invalid purchase code or the Envato API could be down at the moment.','userpro-woocommerce').'</strong></p></div>';
			}
		}
	}
	
}

$upw_admin = new UPWoocommerceAdmin();
?>
