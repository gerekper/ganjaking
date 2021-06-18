<?php
class userpro_tags_admin{

	var $options;

	function __construct() {
	
	add_action('userpro_admin_menu_hook', array(&$this, 'add_menu'), 9);
	$this->slug = 'userpro';	
	$this->subslug = 'userpro-tags';
	add_action('admin_init', array(&$this, 'admin_init'), 9);
 }


function admin_init() {
	
		$this->tabs = array(
			'options' => __('Tags Setting','userpro-tags'),
			
		);
		$this->default_tab = 'options';
		
		
	}
	
function add_menu() {
		add_submenu_page( 'userpro', __('Create Tags','userpro-tags'), __('Create Tags','userpro-tags'), 'manage_options', '/edit-tags.php?taxonomy=userpro_tags' );
		add_submenu_page( 'userpro', __('Tags Settings','userpro-tags'), __('Tags Settings','userpro-tags'), 'manage_options', 'userpro-tags', array(&$this, 'admin_page') );
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
			require_once userpro_tags_path.'admin/panels/'.$tab.'.php';
		}
	}
	
	function save() {
		
		/* other post fields */
		$userpro_options = get_option('userpro');
		foreach($_POST as $key => $value) {
		
			if ($key != 'submit') {
				if (!is_array($_POST[$key])) {
					$userpro_options[$key] = esc_attr($_POST[$key]);
				} else {
					$userpro_options[$key] = $_POST[$key];
				}
				
			}
		}
		
		update_option('userpro',$userpro_options);
		echo '<div class="updated"><p><strong>'.__('Settings saved.','userpro_tags').'</strong></p></div>';
	}

	function reset() {
		update_option('userpro', userpro_tags_default_options() );
		$this->options = array_merge( $this->options,userpro_tags_default_options() );
		echo '<div class="updated"><p><strong>'.__('Settings are reset to default.','userpro_tags').'</strong></p></div>';
	}
	
	

	function admin_page() {

		if (isset($_POST['submit'])) {
			$this->save();
		}

		if (isset($_POST['reset-options'])) {
			$this->reset();
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

        /* add the tags fields to admin panel*/	

}
$userpro_tags_admin=new userpro_tags_admin();
?>