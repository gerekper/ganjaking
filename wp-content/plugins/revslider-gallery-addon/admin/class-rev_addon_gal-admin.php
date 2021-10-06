<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Rev_addon_gal
 * @subpackage Rev_addon_gal/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rev_addon_gal
 * @subpackage Rev_addon_gal/admin
 * @author     ThemePunch <info@themepunch.com>
 */
class Rev_addon_gal_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rev_addon_gal_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rev_addon_gal_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		

		if(isset($_GET["page"]) && $_GET["page"]=="revslider"){
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/revslider-gallery-addon-admin.js', array( 'jquery','revbuilder-admin'), $this->version, false );
			wp_localize_script( $this->plugin_name, 'revslider_gallery_addon', $this->get_var() ); 				
		}
	}
	
	/**
	 * Enqueue gutenberg block assets
	 *
	 * @since    2.0.0
	 */
	 public function gutenberg_enqueue_scripts() {
	
		if(class_exists( 'RevSliderSlider' )) {
		
			$sliders = array();
			$slider = new RevSliderSlider();
			global $rs_do_init_action;
			$rs_do_init_action = false;
			$arrSliders = $slider->get_sliders();
			$rs_do_init_action = true;
			$defSlider = get_option( 'revslider_gallery_addon' );
			$defSlider = str_replace('revslider-gallery-addon-slider=', '', $defSlider);
			
			foreach($arrSliders as $sliderony) {
				
				$params = $sliderony->get_params();
				$sourcetype = $slider->get_val($params, 'sourcetype', 'gallery');
				
				if($sourcetype !== 'post') continue;
				$subtype = $slider->get_val($params, array('source', 'post', 'subType'), 'post');
				
				if($subtype !== 'specific_post') continue;
				$sliders[] = array('label' => $sliderony->get_title(), 'value' => $sliderony->get_alias());
				
			}
			
			if(empty($sliders)) {
				$sliders = array(array('label' => __('No Gallery Sliders Found', 'revslider-gallery-addon'), 'value' => ''));
				$defSlider = '';
			}
			
			// Enqueue our script
			wp_enqueue_script(
				'revslider-gallery-addon-gutenberg-extension-js',
				plugin_dir_url( __FILE__ ) . 'assets/js/revslider-gallery-addon-gutenberg-extension.js',
				array( 'wp-blocks', 'wp-element', 'wp-editor' ),
				'1.0.0',
				true // Enqueue the script in the footer.
			);
			
			wp_localize_script( 'revslider-gallery-addon-gutenberg-extension-js', 'RevSliderGalleryAddOnOptions', array('default' => $defSlider, 'sliders' => $sliders) );
			
		}
		
	}

	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public function get_var($var='',$slug='revslider-gallery-addon') {
		if($slug == 'revslider-gallery-addon'){
			return array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'enabled' => get_option('revslider_gallery_enabled'),
				'bricks' => array(
					'active'  =>  __('Active','revslider-gallery-addon'),
					'gallery'  =>  __('gallery','revslider-gallery-addon'),
					'biggallery'  =>  __('Gallery','revslider-gallery-addon'),
					'galleryimages'  =>  __('Gallery Images','revslider-gallery-addon'),
					'settings' =>  __('Settings','revslider-gallery-addon'),					
					'configuration' =>  __('Configuration','revslider-gallery-addon'),
					'gallerycontent' =>  __('Content from','revslider-gallery-addon'),
					'slider' => __('Gallery Slider','revslider-gallery-addon'),
					'save' => __('Save Configuration','revslider-gallery-addon'),					
					'loadvalues' => __('Loading gallery Add-On Configuration','revslider-gallery-addon'),
					'savevalues' => __('Saving gallery Add-On Configuration','revslider-gallery-addon'),
					'usetimer' => __('Use Timer','revslider-gallery-addon'),
					'enddate' => __('End Date','revslider-gallery-addon'),				
					'title' => __('Title','revslider-gallery-addon'),				
					'caption' => __('Caption','revslider-gallery-addon'),				
					'description' => __('Description','revslider-gallery-addon'),				
					'link' => __('Link','revslider-gallery-addon'),				
					'uploaded' => __('Uploaded','revslider-gallery-addon'),									
					'info' => __('Select a default slider for WP media galleries, Slider must be of the "Specific Posts" source type.','revslider-gallery-addon')				
				)
			);
		}
		else{
			return $var;
		}
	}

	/**
	 * Saves Values for this Add-On
	 *
	 * @since    1.0.0
	 */
	public function save_gallery() {
		if(isset($_REQUEST['data']['revslider_gallery_form'])){
			update_option( "revslider_gallery_addon", $_REQUEST['data']['revslider_gallery_form'] );
			return 1;
		}
		else{
			return 0;
		}
		
	}

	/**
	 * Load Values for this Add-On
	 *
	 * @since    1.0.0
	 */
	public function values_gallery() {
		$revslider_gallery_addon_values = array();
		parse_str(get_option('revslider_gallery_addon'), $revslider_gallery_addon_values);
		$return = json_encode($revslider_gallery_addon_values);
		return array("message" => "Data found", "data"=>$return);
	}

	/**
	 * Change Enable Status of this Add-On
	 *
	 * @since    1.0.0
	 */
	private function change_addon_status($enabled) {
		update_option( "revslider_gallery_enabled", $enabled );	
	}

	/**
	 * Ajax actions for RevSlider calls
	 *
	 * @since    1.0.0
	 */
	public function do_ajax($return,$action) {
		switch ($action) {
			case 'wp_ajax_enable_revslider-gallery-addon':
				$this->change_addon_status( 1 );
				return  __('gallery AddOn enabled', 'revslider-gallery-addon');
				break;
			
			case 'wp_ajax_disable_revslider-gallery-addon':
				$this->change_addon_status( 0 );
				return  __('gallery AddOn disabled', 'revslider-gallery-addon');
				break;

			case 'wp_ajax_get_values_revslider-gallery-addon':
				$return = $this->values_gallery();
				if(empty($return)) $return = true;
				return $return;
				break;
			case 'wp_ajax_save_values_revslider-gallery-addon':
				$return = $this->save_gallery();
				if(empty($return) || !$return){
					return  __('Configuration could not be saved', 'revslider-gallery-addon');
				} 
				else {
					return  __('gallery Configuration saved', 'revslider-gallery-addon');	
				}
				break;
			default:
				return $return;
				break;
		}
	}

	/**
	 * Add Slider Selection Form Field to the WP Gallery modal
	 *
	 * @since    1.0.0
	 */
	public function rev_addon_media_form(){
		$slider = new RevSliderSlider();
		global $rs_do_init_action;
		$rs_do_init_action = false;
		$arrSliders = $slider->get_sliders();
		$rs_do_init_action = true;
		$defSlider = get_option( 'revslider_gallery_addon' );
	?>
		<script type="text/html" id="tmpl-rev-addon-gallery-setting">
		    <h3 style="z-index: -1;">___________________________________________________________________________________________</h3>
		    <h3><?php _e("Gallery Slider Revolution (above settings are off)","rev_slider_addon_gal"); ?></h3>

		    <label class="setting">
		      <span><?php _e('Select'); ?></span>
		      <select class="specific_post_select" data-setting="rev_addon_gal_slider">
		        <?php
		        	foreach($arrSliders as $sliderony){
		        		if($sliderony->get_type()=="specific_posts"){
							echo '<option value="'.$sliderony->get_alias().'" '.selected( $defSlider, $sliderony->get_alias(), true ).'>'. $sliderony->get_title() . '</option>';
						}
				}
		        ?>
		      </select>
		    </label>
		</script>

		<script>
		    jQuery(document).ready(function(){

		        _.extend(wp.media.gallery.defaults, {
		        	rev_addon_gal_slider: '<?php echo $defSlider; ?>'
		        });

		        wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend({
			        template: function(view){
			          return wp.media.template('gallery-settings')(view)
			               + wp.media.template('rev-addon-gallery-setting')(view);
			        }
		        });

		    });

		</script>
		<?php

		}

}
