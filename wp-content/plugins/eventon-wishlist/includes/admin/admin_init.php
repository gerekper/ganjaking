<?php
/**
 * Wishlist Admin
 * @version 1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evowi_admin{

	public function __construct(){

		$this->fnc = new evowi_fnc();

		add_filter('eventon_settings_lang_tab_content', array($this,'evowi_language_additions'), 10, 1);
		add_filter( 'eventon_custom_icons',array($this, 'custom_icons') , 10, 1);
		add_filter('evo_se_setting_fields', array($this,'evowi_settings'), 10, 1);
		
		$posttype = "ajde_events";
	    add_filter( "manage_edit-{$posttype}_columns", array($this, 'edit_columns'), 20, 1 );
	    add_action( "manage_{$posttype}_posts_custom_column", array($this, 'column_display'), 20, 2 ); 

	    // event edit post addition
	    add_action('eventon_event_submitbox_misc_actions', array($this, 'event_edit_adds'),10,1);
	}

	// custom columns
		function edit_columns( $columns ){
		    $columns['likes'] = "Likes";
		    return $columns;
		}
		function column_display( $column_name, $post_id ) {
		    if ( 'likes' != $column_name )
		        return;

		    $count = $this->fnc->get_wishlist_count($post_id, 'all');
		    echo "<b style='background-color: #d3edff; padding: 3px 5px; border-radius: 20px; color: #4e4e4e; font-size: 11px;'>". $count . "</b>";
		}

	// event edit post adds
		function event_edit_adds($EV){

			$count = $this->fnc->get_wishlist_count($EV->ID, 'all');
			?>
			<p>Wishlist Count <b style='background-color: #d3edff; padding: 3px 5px; border-radius: 20px; color: #4e4e4e; font-size: 11px;'><?php echo $count;?></b></p>
			<?php
		}

	// language settings additinos
	function evowi_language_additions($_existen){
		$new_ar = array(
				array('type'=>'togheader','name'=>'ADDON: WishList'),
				array('label'=>'In your wishlist','var'=>'1'),
				array('label'=>'Add to wishlist','var'=>'1'),
				array('label'=>'My Wishlist Events','var'=>'1'),
				array('label'=>'Login required to manage your wishlist events','var'=>'1'),
				array('label'=>'Login Now','var'=>'1'),
				array('label'=>'Hello','var'=>'1'),
				array('label'=>'You must login to add events to wish list!','var'=>'1'),
				array('label'=>'You do not have any wish list events','var'=>'1'),
				array('label'=>'You can view and manage the events you have added to your wishlist from here.','var'=>'1'),
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
	}

	// custom icons
	function custom_icons($array){
		$array[] = array(
			'id'=>'evcal_evowi_001',
			'type'=>'icon',
			'name'=>'Wishlist selected icon',
			'default'=>'fa-heart'
		);$array[] = array(
			'id'=>'evcal_evowi_002',
			'type'=>'icon',
			'name'=>'Wishlist not selected icon',
			'default'=>'fa-heart-o'
		);
		return $array;
	}

	function evowi_settings($data){
		$data[] = array('id'=>'evosm','type'=>'sub_section_open',
				'name'=>__('Wish List Settings','eventon'));
		$data[] = array('id'=>'evowi_on_sin_pg','type'=>'yesno',
				'name'=>__('Enable wishlist on single event page','eventon'), 
				'legend'=>__('This will allow users to add events to wish list from single event page.','eventon')
			);

		$data[] = array('type'=>'sub_section_close');

		

		return $data;
	}
}