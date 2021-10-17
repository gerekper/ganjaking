<?php
/**
 * Wishlist Admin
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evowi_admin{

	public function __construct(){

		$this->fnc = new evowi_fnc();

		add_filter('eventon_settings_lang_tab_content', array($this,'evowi_language_additions'), 10, 1);
		add_filter( 'eventon_custom_icons',array($this, 'custom_icons') , 10, 1);
		//add_filter('eventon_settings_tab1_arr_content', array($this,'evowi_settings'), 10, 1);
		
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

	function evowi_settings($array){
		$array[1]['fields'][] = array('id'=>'evowi', 'type'=>'subheader','name'=>'Event Map Addon Settings');
		$array[1]['fields'][] = array(
			'id'=>'evo_map_marker_type',
			'type'=>'dropdown',
			'name'=>'Map marker type',
			'options'=>array(
				'dynamic'=>'Dynamic number-ed markers',
				'default'=>'Default Google Markers',
				'custom'=>'Custom map marker icon (Only if set above)'
			),
			'legend'=>'If you have problems with dynamic marker, swtiching to default marker should resolve.'
		);
		$array[1]['fields'][] = array(
			'id'=>'evomap_map_style', 
			'type'=>'yesno',
			'name'=>'Use the same map style as above setting'
		);
		$array[1]['fields'][] = array('id'=>'evomap_def_latlon',
			'type'=>'text',
			'name'=>'Default Latitude and longitude for map (separate by comma)',
			'legend'=>'This will be the map location focused when there are no events on map.','default'=>'eg. 123,-234.83'
		);
		$array[1]['fields'][] = array('id'=>'evomap_clusters',
			'type'=>'yesno',
			'name'=>'Disable map clusters',
			'legend'=>'This will stop creating map clusters on the map for multiple map markers'
		);

		return $array;
	}
}