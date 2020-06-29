<?php
if (!defined('ABSPATH')) {
    exit;
}

class Theplus_plugin_options
{
    
    /**
     * Option key, and option page slug
     * @var string
     */
    private $key = 'theplus_options';
    
    /**
     * Array of metaboxes/fields
     * @var array
     */
    protected $option_metabox = array();
    
    /**
     * Options Page title
     * @var string
     */
    protected $title = '';
    
    /**
     * Options Page hook
     * @var string
     */
    protected $options_page = '';
    protected $options_pages = array();
    /**
     * Constructor
     * @since 0.1.0
     */
    public function __construct()
    {
        // Set our title
		add_action( 'admin_enqueue_scripts', array( $this,'pt_theplus_options_scripts') );
        $this->title = __('ThePlus Settings', 'pt_theplus');
        require_once THEPLUS_PLUGIN_PATH.'post-type/cmb2-conditionals.php';
        // Set our CMB fields
        $this->fields = array(
        );
    }
    
    /**
     * Initiate our hooks
     * @since 1.0.0
     */
	public function pt_theplus_options_scripts() {   
		wp_enqueue_script( 'pt-theplus-js', THEPLUS_PLUGIN_URL .'post-type/cmb2-conditionals.js', array() );
		wp_enqueue_script('thickbox', null, array('jquery'));
		wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
	}

    public function hooks()
    {
        add_action('admin_init', array(
            $this,
            'init'
        ));
        add_action('admin_menu', array(
            $this,
            'add_options_page'
        ));
    }
    
    /**
     * Register our setting to WP
     * @since  1.0.0
     */
    public function init()
    {
        //register_setting( $this->key, $this->key );
        $option_tabs = self::option_fields();
        foreach ($option_tabs as $index => $option_tab) {
            register_setting($option_tab['id'], $option_tab['id']);
        }
    }
    
    /**
     * Add menu options page
     * @since 1.0.0
     */
    public function add_options_page()
    {
        //$this->options_page = add_menu_page( $this->title, 'ThePlus Options', 'manage_options', $this->key, array( $this, 'admin_page_display' ) );
		
		$verify_api=pt_plus_check_api_status();
        $option_tabs = self::option_fields($verify_api);
        foreach ($option_tabs as $index => $option_tab) {
            if ($index == 0) {
                $this->options_pages[] = add_menu_page($this->title, $this->title, 'manage_options', $option_tab['id'], array(
                    $this,
                    'admin_page_display'
                )); //Link admin menu to first tab
                add_submenu_page($option_tabs[0]['id'], $this->title, $option_tab['title'], 'manage_options', $option_tab['id'], array(
                    $this,
                    'admin_page_display'
                )); //Duplicate menu link for first submenu page
            } else {
                $this->options_pages[] = add_submenu_page($option_tabs[0]['id'], $this->title, $option_tab['title'], 'manage_options', $option_tab['id'], array(
                    $this,
                    'admin_page_display'
                ));
            }
        }
    }
    
    /**
     * 
     * @since  1.0.0
     */
    public function admin_page_display()
    {
		$verify_api=pt_plus_check_api_status();
        $option_tabs = self::option_fields($verify_api); //get all option tabs
        $tab_forms   = array();
?>

		<div class="<?php  echo $this->key; ?>">
		<div id="ptplus-banner-wrap">
			<div id="ptplus-banner" class="ptplus-banner-sticky">
				<h2><?php echo esc_html('ThePlus Settings','pt_theplus'); ?><!--<span><img src="<?php echo THEPLUS_PLUGIN_URL .'vc_elements/images/thepluslogo.png'; ?>"></span>--></h2>
				<div class="theplus-current-version wp-badge"> <?php echo esc_html('Version','pt_theplus'); ?> <?php echo VERSION_THEPLUS; ?></div>
			</div>
		</div>
		<h2 class="nav-tab-wrapper">
            	<?php
	        foreach ($option_tabs as $option_tab):
	            $tab_slug  = $option_tab['id'];
	            $nav_class = 'nav-tab';
	            if ($tab_slug == $_GET['page']) {
	                $nav_class .= ' nav-tab-active'; //add active class to current tab
	                $tab_forms[] = $option_tab; //add current tab to forms to be rendered
	            } ?>            	
            	<a class="<?php echo $nav_class; ?>" href="<?php  menu_page_url($tab_slug); ?>"><?php esc_attr_e($option_tab['title']); ?></a>
           	<?php endforeach; ?>
        </h2>
		<?php foreach ($tab_forms as $tab_form): ?>
				<?php if($tab_form['id']=='theplus_purchase_code'){ ?>
						<div class="theplus_about-tab changelog" style="padding-bottom: 0;">
					<div class="feature-section">
						<h4 style="padding-left:15px;"><?php echo esc_html__('Verify your plugin in 4 easy steps :','pt_theplus');?></h4>					
						<p style="padding-left:15px;"><?php echo esc_html__('1. Visit this ','pt_theplus'); ?><?php echo '<i><a href="http://theplusaddons.com/theplus-verify/" target="_blank">URL</a></i> and enter your purchase code before clicking submit button.  <i> How to get purchase code :</i> visit this <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">URL</a> or check <a href="https://youtu.be/u-ymumiy4yU" target="_blank"> video</a>'; ?></p>
						<p style="padding-left:15px;"><?php echo esc_html__('2. Add  Your Website URL. Important : From your WordPress Backend go to Settings -> General -> WordPress Address (URL) and Enter exact URL from there.','pt_theplus'); ?></p>
						<p style="padding-left:15px;"><?php echo esc_html__('3. Your "ThePlus Key" will be generated automatically. You just need to copy paste that Key from that panel to your "Verify Key" section of ThePlus Settings Panel.','pt_theplus'); ?></p>
						<p style="padding-left:15px;"><?php echo esc_html__('4. Enter Your ThePlus key and Press "Save" Button. You are now able to use all functionalities of Plugin.','pt_theplus'); ?></p>
						
						<p style="padding-left:15px;padding-top:5px;"><?php echo '<span style="color:red;font-size:20px;">*</span> If you have any doubt, Please check our video tutorial : <i><a href="https://www.youtube.com/watch?v=MNlyAvPMscA" target="_blank">Watch Video</a></i>'; ?></p>
					</div>
					</div>
				<?PHP } ?>
				<?php if($tab_form['id']!='theplus_about_us' && $tab_form['id']!='theplus_import_data'){ ?>
					<div id="<?php esc_attr_e($tab_form['id']); ?>" class="group theplus_form_content">
						<?php cmb_metabox_form($tab_form, $tab_form['id']); ?>
					</div>
				<?php } ?>
				<?php if($tab_form['id']=='theplus_purchase_code'){
					echo pt_plus_message_display();
					} ?>
				<?php if($tab_form['id']=='theplus_about_us'){ ?>
				<div class="theplus_about-tab changelog">
					<div class="feature-section">
						<h4 style="padding-left:15px;"><?php echo esc_html('ThePlus Addons for WPBakery Page Builder (formerly Visual Composer) Collection of most beautiful and modern Visual composer addons made by POSIMYTH Themes.','pt_theplus'); ?></h4>
						<div class="col-xs-6">
							<h3><?php echo esc_html('Resources','pt_theplus'); ?></h3>							
							<p><?php echo esc_html('It have great features, which includes 50+ VC Elements, 500+ Prebuilt UI Blocks, Grid Builder for custom post types as well as prebuilt pages with amazing designers.','pt_theplus'); ?></p>
							<ul>
								<li>
									<a href="http://theplus.sagar-patel.com/" target="_blank"><?php echo esc_html('Visit Our Main Page : Live Demo','pt_theplus'); ?></a></li>
								<li><a href="http://theplus.sagar-patel.com/plus-elements/" target="_blank"><?php echo esc_html('Check our 50+ Elements : Plus Elements','pt_theplus'); ?></a></li>
								<li><a href="http://theplus.sagar-patel.com/plus-blocks/" target="_blank"><?php echo esc_html('Our 500+ UI Blocks : Plus Blocks','pt_theplus'); ?></a></li>
								<li><a href="http://theplus.sagar-patel.com/plus-listings/" target="_blank"><?php echo esc_html('Visit Grid Builder Options : Plus Listings','pt_theplus'); ?></a></li>
								<li><a href="#"><?php echo esc_html('Check premade pages : Plus Pages','pt_theplus'); ?></a></li>
							</ul>
						</div>
						
						<div class="col-xs-6">
							<ul style="padding-top: 40px;">
								<li><a href="http://theplus.sagar-patel.com/documentation/" target="_blank"><?php echo esc_html('Checkout our detailed documentation : Online Documentation','pt_theplus'); ?></a></li>
								<li><a href="https://www.youtube.com/playlist?list=PLI_V0tvObVZMgXkxXmI9xbNQ0QraJaJe9" target="_blank"><?php echo esc_html('Watch Our Video Tutorials : Video Library','pt_theplus'); ?></a></li>
								<li><a href="https://posimyththemes.ticksy.com/" target="_blank"><?php echo esc_html('Contact us for any queries : Support Forum','pt_theplus'); ?></a></li>
								<li><a href="https://codecanyon.net/item/theplus-visual-composer-addons/21346121?ref=posimyththemes" target="_blank"><?php echo esc_html('Purchase Another Licence : Buy Now','pt_theplus'); ?></a></li>								
							</ul>
						</div>
					</div>
				</div>
				<?php } ?>
				<?php if($tab_form['id']=='theplus_import_data'){ ?>
				<div class="theplus_about-tab changelog">
					<div class="feature-section">
						<div id="pt-plus-import-form">
							<div class="pt_plus_row table_row">
							<?php if(!empty($verify_api) && $verify_api==1){ ?>
								<div class="pt_col-md-6">
									<form method="post" action="" id="importContentForm">
									<h4 class="ptplus-import-title"><?php _e('Step 1 (Optional) : Import Demo Content', 'pt_theplus') ?></h4>
									<div class="pt-plus-page-form">
										<div class="pt-plus-page-form-section-holder clearfix">
											<div class="pt-plus-page-form-section">
												<div class="pt-plus-field-desc">
													<h4><?php esc_html_e('Import Demo Data', 'pt_theplus'); ?></h4>
													<p><?php esc_html_e('Import Demo content for custom post types.', 'pt_theplus'); ?></p>
												</div>
												<div class="pt-plus-section-content">
														<div class="pt_plus_row">
															<div class="pt_col-lg-3">
																<select name="posts_import_example" id="posts_import_example" class="form-control pt-plus-form-element">
																	<option value="all">All Demo Data</option>
																	<option value="blog-posts">Blog Posts</option>
																	<option value="clients">Clients</option>
																	<option value="portfolio">Portfolio</option>
																	<option value="testimonials">Testimonials</option>
																	<option value="team-member">Team Member</option>
																</select>
															</div>
														</div>														
														<div class="pt_plus_row next-row">
															<div class="pt_col-lg-3">
																<img id="posts_demo_site_img" src="<?php echo THEPLUS_PLUGIN_URL . 'vc_elements/import/images/all.jpg' ?>" alt="Posts Import" />
															</div>
														</div>													
												</div>
											</div>
											<div class="pt-plus-page-form-section" >
												<div class="pt-plus-field-desc">
													<h4><?php esc_html_e('Import attachments', 'pt_theplus'); ?></h4>
													<p><?php esc_html_e('Do you want to import media files?', 'pt_theplus'); ?></p>
												</div>
												<div class="pt-plus-section-content">
													<div class="container-fluid">
														<div class="pt_plus_row">
															<div class="pt_col-lg-3">
																<input type="checkbox" value="1" class="pt-plus-form-element" name="posts_import_attachments" id="posts_import_attachments" />
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="pt_plus_row">
												<div class="pt_col-lg-3">
													<div class="form-button-section clearfix">
													<input type="hidden" name="posts_import_option" id="posts_import_option" value="content" class="form-control pt-plus-form-element" />
														<input type="submit" class="btn btn-primary btn-sm " value="Import Demo Data" name="import_posts" id="posts_import_demo_data" />
													</div>
												</div>
											</div>
											<div class="import_load_posts"><span><?php _e('The import process may take some time. Please be patient.', 'pt_theplus') ?> </span><br />
												<div class="pt-plus-progress-bar-posts">
													<img src="<?php echo THEPLUS_PLUGIN_URL."vc_elements/images/lazy_load.gif" ?>" class="loading-bar-image">
													<div class="pt-plus-progress-bar-message"></div>
												</div>
											</div>
											<div class="alert alert-warning">
												<strong><?php _e('Important Notice :', 'pt_theplus') ?></strong>
												<ul>
													<li><?php _e('Install Demo content for custom post types. You need not to import if you already have your live blog posts or testimonials or client and so on.  Note: You can import these demo content only if from setting you have selected plugin default post types. You can not import this demo data if you have selected your theme\'s post types.', 'pt_theplus'); ?></li>
												</ul>
											</div>
										</div>
										</div>
									</form>
								</div>
								<div class="pt_col-md-6">
									<form method="post" action="" id="importContentForm">
									<h4 class="ptplus-import-title"><?php _e('Step 2 : Import Plus Templates', 'pt_theplus') ?></h4>
									<div class="pt-plus-page-form">
										<div class="pt-plus-page-form-section-holder clearfix">
											<div class="pt-plus-page-form-section">
												<div class="pt-plus-field-desc">
													<h4><?php esc_html_e('Import Plus Template', 'pt_theplus'); ?></h4>
													<p><?php esc_html_e('Choose prebuilt template you want to import', 'pt_theplus'); ?></p>
												</div>
												<div class="pt-plus-section-content">
														<div class="pt_plus_row">
															<div class="pt_col-lg-3">
																<select name="pages_import_example" id="pages_import_example" class="form-control pt-plus-form-element">
																	<option value="education">1. Education </option>
																	<option value="events">2. Events </option>
																	<option value="restaurant">3. Restaurant </option>
																	<option value="seo">4. SEO </option>
																	<option value="spa">5. Spa </option>
																	<option value="architecture">6. Architecture </option>
																	<option value="barbershop">7. Barber Shop </option>
																	<option value="beautysaloon">8. Beauty Saloon </option>
																	<option value="designer">9. Designer </option>
																	<option value="digitalagency">10. Digital Agency </option>
																	<option value="digital-agency-1">11. Digital Agency 1</option>
																	<option value="fitness">12. Fitness</option>
																	<option value="travel">13. Travel</option>
																	<option value="digitalagency3">14. Digital Agency 3</option>
																	<option value="gym">15. Gym</option>
																	<option value="health">16. Doctor</option>
																	<option value="interior-designer">17. Interior Designer</option>
																	<option value="lawyer">18. Lawyer</option>
																	<option value="modern-restaurant">19. Modern Restaurant</option>
																	<option value="tourism">20. Tourism</option>
																</select>
															</div>
														</div>
														<div class="pt_plus_row next-row">
															<div class="pt_col-lg-3">
																<img id="pages_demo_site_img" src="<?php echo THEPLUS_PLUGIN_URL . 'vc_elements/import/images/education.jpg' ?>" alt="demo site" />
															</div>
														</div>
												</div>
											</div>
											<div class="pt-plus-page-form-section" >
												<div class="pt-plus-field-desc">
													<h4><?php esc_html_e('Import attachments', 'pt_theplus'); ?></h4>
													<p><?php esc_html_e('Do you want to import media files?', 'pt_theplus'); ?></p>
												</div>
												<div class="pt-plus-section-content">
													<div class="container-fluid">
														<div class="pt_plus_row">
															<div class="pt_col-lg-3">
																<input type="checkbox" value="1" class="pt-plus-form-element" name="pages_import_attachments" id="pages_import_attachments" />
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="pt_plus_row">
												<div class="pt_col-lg-3">
													<div class="form-button-section clearfix">
													<input type="hidden" name="pages_import_option" id="pages_import_option" value="content" class="form-control pt-plus-form-element" />
														<input type="submit" class="btn btn-primary btn-sm " value="Import Template" name="import_page" id="pages_import_demo_data" />
													</div>
												</div>
											</div>
											<div class="import_load_pages"><span><?php _e('The import process may take some time. Please be patient.', 'pt_theplus') ?> </span><br />
												<div class="pt-plus-progress-bar-posts">
													<img src="<?php echo THEPLUS_PLUGIN_URL."vc_elements/images/lazy_load.gif" ?>" class="loading-bar-image">
													<div class="pt-plus-progress-bar-message"></div>
												</div>
											</div>
											<div class="alert alert-warning">
												<strong><?php _e('Important Notice :', 'pt_theplus') ?></strong>
												<ul>
													<li><?php _e('Some Images are not included in this import process. To use any images from import you need to have licence of image and which you can get from envato elements or You need to change image from your sources.', 'pt_theplus'); ?></li>
												</ul>
											</div>
										</div>
										</div>
									</form>
								</div>
								<?php }else{ ?>
									<div class="pt-plus-page-form">
									<div class="alert alert-warning">
										<strong><?php _e('Important Notice :', 'pt_theplus') ?></strong>
										<ul>
											<li><?php _e('Please follow simple 4 steps to ', 'pt_theplus'); echo '<b><a href="admin.php?page=theplus_purchase_code">Verify</a></b>'; _e(' your plugin and get access of all functionalities. Go to Verify section of settings to proceed further.', 'pt_theplus'); ?></li>
										</ul>
									</div>
									</div>
								<?php } ?>
							</div>
						</div>
						<script >
							jQuery(document).ready(function() {
								jQuery('#posts_import_example').on('change', function (e) {
									var optionSelected = jQuery("option:selected", this).val();
									jQuery('#posts_demo_site_img').attr('src', '<?php echo THEPLUS_PLUGIN_URL . 'vc_elements/import/images/' ?>' + optionSelected + '.jpg' );
								});
								jQuery(document).on('click', '#posts_import_demo_data', function(e) {
									e.preventDefault();
									if (confirm('Are you sure, you want to import Posts Data now?')) {
										jQuery('.import_load_posts').css('display','block');
										
										var import_opt = jQuery( "#posts_import_option" ).val();
										var import_expl = jQuery( "#posts_import_example" ).val();
										var p = 0;
										if(import_opt == 'content'){
											for(var i=1;i<=1;i++){
												var str;
												str = import_expl+'.xml';
												jQuery.ajax({
													type: 'POST',
													url: ajaxurl,
													data: {
														action: 'ptplus_posts_dataImport',
														xml: str,
														example: import_expl,
														import_attachments: (jQuery("#posts_import_attachments").is(':checked') ? 1 : 0)
													},
													success: function(data, textStatus, XMLHttpRequest){														
														jQuery('.import_load_posts .pt-plus-progress-bar-message').html('<div class="alert alert-success"><strong>Import is completed</strong></div>');
														jQuery('.import_load_posts .pt-plus-progress-bar-posts .loading-bar-image').css('display','none');
													},
													error: function(MLHttpRequest, textStatus, errorThrown){
													}
												});
											}
										}
									}
									return false;
								});
								jQuery('#pages_import_example').on('change', function (e) {
									var optionSelected = jQuery("option:selected", this).val();
									jQuery('#pages_demo_site_img').attr('src', '<?php echo THEPLUS_PLUGIN_URL . 'vc_elements/import/images/' ?>' + optionSelected + '.jpg' );
								});
								jQuery(document).on('click', '#pages_import_demo_data', function(e) {
									e.preventDefault();
									if (confirm('Are you sure, you want to import Page Data now?')) {
										jQuery('.import_load_pages').css('display','block');
										
										var import_opt = jQuery( "#pages_import_option" ).val();
										var import_expl = jQuery( "#pages_import_example" ).val();
										var p = 0;
										if(import_opt == 'content'){
											for(var i=1;i<=1;i++){
												var str;
												str = import_expl+'.xml';
												jQuery.ajax({
													type: 'POST',
													url: ajaxurl,
													data: {
														action: 'ptplus_dataImport',
														xml: str,
														example: import_expl,
														import_attachments: (jQuery("#pages_import_attachments").is(':checked') ? 1 : 0)
													},
													success: function(data, textStatus, XMLHttpRequest){														
														jQuery('.import_load_pages .pt-plus-progress-bar-message').html('<div class="alert alert-success"><strong>Import is completed</strong></div>');
														jQuery('.import_load_pages .pt-plus-progress-bar-posts .loading-bar-image').css('display','none');
													},
													error: function(MLHttpRequest, textStatus, errorThrown){
													}
												});
											}
										}
									}
									return false;
								});
							});
						</script>
					</div>
				</div>
				<?php } ?>
            	<?php  endforeach; ?>
		</div>
		<?php
    }
    
    /**
     * Defines the theme option metabox and field configuration
     * @since  1.0.0
     * @return array
     */
    public function option_fields($verify_api='')
    {
		$array_read=array();
        if(!empty($verify_api) && $verify_api==1){
			$array_read=array(
				'required'    => 'required'
			);
		}else{
			$array_read= array(
				'readonly' => 'readonly',
				'disabled' => 'disabled'
			);
		}
        // Only need to initiate the array once per page-load
        if (!empty($this->option_metabox)) {
            return $this->option_metabox;
        }
       
        $this->option_metabox[] = array(
            'id' => 'general_options',
            'title' => 'General Settings',
            'show_on' => array(
                'key' => 'options-page',
                'value' => array(
                    'general_options'
                )
            ),
            'show_names' => true,
            'fields' => array(
				array(
	                'name' => __('Elements On/Off', 'pt_theplus'),
	                'desc' => __('You can turn on/off elements using this option. If you don\'t need all elements, We suggest you to turn off them which help you to improve performance of website loading.', 'pt_theplus'),
	                'id' => 'check_elements',
	                'type' => 'multicheck',
					'select_all_button' => true,
					'default' =>array('tp_accordion','tp_tabs','tp_tours','tp_adv_gmap','tp_adv_text_block','tp_advertisement_banner','tp_animated_image','tp_animated_svg',
								'tp_before_after','tp_button','tp_contact_form','tp_countdown','tp_creative_info_box','tp_empty_space','tp_food_menu','tp_gallery_list',
								'tp_header_breadcrumbs','tp_heading_animation','tp_heading_title','tp_icon_counter','tp_info_banner','tp_info_box','tp_magic_box','tp_modern_separator',
								'tp_pin_point','tp_pop_up','tp_pricing_table','tp_progressbar','tp_row_carousel','tp_row_settings','tp_social_share','tp_smart_gallery','tp_stylish_list','tp_timeline',
								'tp_toggle','tp_unique_box','tp_video_player'),
	                'options' => array(
	                    'tp_accordion' => __('Accordation', 'pt_theplus'),
	                    'tp_tabs' => __('Tabs', 'pt_theplus'),
	                    'tp_tours' => __('Tours', 'pt_theplus'),
	                    'tp_adv_gmap' => __('Google Maps', 'pt_theplus'),
	                    'tp_adv_text_block' => __('Advanced Text Block', 'pt_theplus'),
	                    'tp_advertisement_banner' => __('Advertisement Banner', 'pt_theplus'),
	                    'tp_animated_image' => __('Animated Image', 'pt_theplus'),
	                    'tp_animated_svg' => __('Animated Svg', 'pt_theplus'),
	                    'tp_before_after' => __('Before After', 'pt_theplus'),
	                    'tp_button' => __('Button', 'pt_theplus'),
	                    'tp_contact_form' => __('Contact Form', 'pt_theplus'),
	                    'tp_countdown' => __('CountDown', 'pt_theplus'),
	                    'tp_creative_info_box' => __('Creative Info Box', 'pt_theplus'),
	                    'tp_empty_space' => __('Empty Space', 'pt_theplus'),
	                    'tp_food_menu' => __('Food Menu', 'pt_theplus'),
	                    'tp_gallery_list' => __('Gallery List', 'pt_theplus'),
	                    'tp_header_breadcrumbs' => __('Breadcrumbs', 'pt_theplus'),
	                    'tp_heading_animation' => __('Heading Animation', 'pt_theplus'),
	                    'tp_heading_title' => __('Heading Title', 'pt_theplus'),
	                    'tp_icon_counter' => __('Icon Counter', 'pt_theplus'),
	                    'tp_info_banner' => __('Info Banner', 'pt_theplus'),
	                    'tp_info_box' => __('Info Box', 'pt_theplus'),
	                    'tp_magic_box' => __('Magic Box', 'pt_theplus'),
	                    'tp_modern_separator' => __('Row Separator', 'pt_theplus'),
	                    'tp_pin_point' => __('Pin Point', 'pt_theplus'),
	                    'tp_pop_up' => __('Pop Up', 'pt_theplus'),
	                    'tp_pricing_table' => __('Pricing Table', 'pt_theplus'),
	                    'tp_progressbar' => __('Progress Bar', 'pt_theplus'),
	                    'tp_row_carousel' => __('Row Carousel', 'pt_theplus'),
	                    'tp_row_settings' => __('Row Background', 'pt_theplus'),
	                    'tp_social_share' => __('Social Share', 'pt_theplus'),
						'tp_smart_gallery' => __('Smart Gallery', 'pt_theplus'),
	                    'tp_stylish_list' => __('Style list', 'pt_theplus'),
	                    'tp_timeline' => __('TimeLine', 'pt_theplus'),
	                    'tp_toggle' => __('Toggle', 'pt_theplus'),
	                    'tp_unique_box' => __('Unique Box', 'pt_theplus'),
	                    'tp_video_player' => __('Video Player', 'pt_theplus'),
	                )	                
	            ),
				array(
						'name' => __('Google Map API Key', 'pt_theplus'),
						'desc' => __('<b>NOTE :</b> Turn Off this key If you theme already have google key option. So, It will not generate error in console for multiple google map keys.', 'pt_theplus'),
						'id' => 'gmap_api_switch',
						'type' => 'select',
						'show_option_none' => true,
						'default' => 'enable',
						'options' => array(
							'enable' => __('Show', 'pt_theplus'),
							'disable' => __('Hide', 'pt_theplus'),
						),
				),
	            array(
	                'name' => __('Google Map API Key', 'pt_theplus'),
	                'desc' => __('This field is required if you want to use Advance Google Map element. You can obtain your own Google Maps Key here: (<a href="https://developers.google.com/maps/documentation/javascript/get-api-key">Click Here</a>)', 'pt_theplus'),
	                'default' => '',
	                'id' => 'theplus_google_map_api',
	                'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'gmap_api_switch',
						'data-conditional-value' => 'enable',
					),
	            ),
				array(
						'name' => __('Plus Blocks On/Off ', 'pt_theplus'),
						'desc' => __('Use this option to turn on/off Plus Plus UI Blocks. We have lots of templates in the backend so, It might increase loading time of backend as it have good quality images. So, In case of Less or No use of plus blocks, We request you to keep it Turn off. <b>NOTE :</b> If this section not working, Do verify your plugin. <b><a href="admin.php?page=theplus_purchase_code" style="text-decoration:blink;" target="_blank">Verify Now</a></b>', 'pt_theplus'),
						
						'id' => 'templates_on_off',
						'type' => 'select',
						'show_option_none' => true,
						'default' => 'enable',
						'options' => array(
							'enable' => __('Show', 'pt_theplus'),
							'disable' => __('Hide', 'pt_theplus'),
						),
						'attributes'  => $array_read,
				),
				array(
						'name' => __('VC Clipboard', 'pt_theplus'),
						'desc' => __('Easily copy paste on stack based vc clipboard functionality. Using this options you can turn on or off that.', 'pt_theplus'),
						'id' => 'vc_clipboard_on_off',
						'type' => 'select',
						'show_option_none' => true,
						'default' => 'enable',
						'options' => array(
							'enable' => __('Show', 'pt_theplus'),
							'disable' => __('Hide', 'pt_theplus'),
						)
				),
				array(
						'name' => __('VC View Shortcode', 'pt_theplus'),
						'desc' => __('Easily copy your row/column shortcode using this functionality. Using this options you can turn on or off that.', 'pt_theplus'),
						'id' => 'vc_view_shortcode_on_off',
						'type' => 'select',
						'show_option_none' => true,
						'default' => 'enable',
						'options' => array(
							'enable' => __('Show', 'pt_theplus'),
							'disable' => __('Hide', 'pt_theplus'),
						)
				),	
				array(
						'name' => __('Minify CSS', 'pt_theplus'),
						'desc' => __('Enable Minified CSS to have faster performance of website. Disable it if it have any conflicts with your other plugins. If you are using cache plugins and do change status of this, do remove cache and test website. You need to do hard refresh.', 'pt_theplus'),
						'id' => 'compress_minify_css',
						'type' => 'select',
						'show_option_none' => true,
						'default' => 'disable',
						'options' => array(
							'disable' => __('Disable', 'pt_theplus'),
							'enable' => __('Enable', 'pt_theplus'),
						)
				),
				array(
						'name' => __('Minified JS', 'pt_theplus'),
						'desc' => __('Enable Minified JS to have faster performance of website. Disable it if it have any conflicts with your other plugins. If you are using cache plugins and do change status of this, do remove cache and test website. You need to do hard refresh.', 'pt_theplus'),
						'id' => 'compress_minify_js',
						'type' => 'select',
						'show_option_none' => true,
						'default' => 'disable',
						'options' => array(
							'disable' => __('Disable', 'pt_theplus'),
							'enable' => __('Enable', 'pt_theplus'),
						)
				),
            )
        );
        
        $this->option_metabox[] = array(
            'id' => 'post_type_options',
            'title' => 'Post Type Settings',
            'show_on' => array(
                'key' => 'options-page',
                'value' => array(
                    'post_type_options'
                )
            ),
            'show_names' => true,
            'fields' => array(
				/* Portfolio option start */
				array(
					'name' => '',
					'desc' => __('For Grid Builder functionality of ThePlus Plugin, You need some custom post types to make it working. We have given options below for them, You can disable them, You can use plugin\'s default post type settings or You can connect your already made theme based post types.', 'pt_theplus'),
					'type' => 'title',
					'id' => 'tp_desc_text'
				),
				array(
					'name' => __('Portfolio Post Type Settings', 'pt_theplus'),
					'desc' => '',
					'type' => 'title',
					'id' => 'portfolio_post_title'
				),
				array(
						'name' => __('Select Post Type Type', 'pt_theplus'),
						'desc' => '',
						'id' => 'portfolio_post_type',
						'type' => 'select',
						'show_option_none' => true,
						'default' => 'disable',
						'options' => array(
							'disable' => __('Disable', 'pt_theplus'),
							'plugin' => __('ThePlus Post Type', 'pt_theplus'),
							'themes' => __('Prebuilt Theme Based', 'pt_theplus'),
							'themes_pro' => __('The Plus Theme Pro Settings', 'pt_theplus')
						)
				),
				array(
				'name' => __('Post Name : (Keep Blank if you want to keep default Name)', 'pt_theplus'),
				'desc' => __('Enter value for portfolio custom post type name. Default: "theplus_portfolio"', 'pt_theplus'),
				'default' => '',
				'id' => 'portfolio_plugin_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'portfolio_post_type',
						'data-conditional-value' => 'plugin',
					),
				),
				array(
				'name' => __('Category Taxonomy Value : (Keep Blank if you want to keep default Name)', 'pt_theplus'),
				'desc' => __('Enter value for Category Taxonomy Value. Default : "theplus_portfolio_category"', 'pt_theplus'),
				'default' => '',
				'id' => 'portfolio_category_plugin_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'portfolio_post_type',
						'data-conditional-value' => 'plugin',
					),
				),
				array(
				'name' => __('Prebuilt Post Name : (You can find that from here) ', 'pt_theplus'),
				'desc' => __('Enter the value of your current post type name which is prebuilt with your theme. E.g.: "theplus_portfolio" <a href="'.THEPLUS_PLUGIN_URL.'vc_elements/images/post-type-screenshot.png" class="thickbox" title="Get the Post Name of Custom Post type as per above Screenshot.">Check screenshot</a> for how to get that value from URL of your current post type.', 'pt_theplus'),
				'default' => '',
				'id' => 'portfolio_theme_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'portfolio_post_type',
						'data-conditional-value' => 'themes',
					),
				),
				array(
				'name' => __('Prebuilt Category Taxonomy Value :  (You can find that from here)', 'pt_theplus'),
				'desc' => __('Enter the value of your current Category Taxonomy Value which is prebuilt with your theme.  E.g. : "theplus_portfolio_category" <a href="'.THEPLUS_PLUGIN_URL.'vc_elements/images/taxonomy-screenshot.png" class="thickbox" title="Get the Category Taxonomy Value as per above screenshot.">Check screenshot</a> for how to get that value from URL of your current taxonomy.', 'pt_theplus'),
				'default' => '',
				'id' => 'portfolio_category_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'portfolio_post_type',
						'data-conditional-value' => 'themes',
					),
				),
				/* Portfolio option start */
				/* client option start */
				array(
					'name' => __('Clients Post Type Settings', 'pt_theplus'),
					'desc' => '',
					'type' => 'title',
					'id' => 'client_post_title'
				),
				array(
						'name' => __('Select Post Type Type', 'pt_theplus'),
						'desc' => '',
						'id' => 'client_post_type',
						'type' => 'select',
						'show_option_none' => true,
						'default' => 'disable',
						'options' => array(
							'disable' => __('Disable', 'pt_theplus'),
							'plugin' => __('ThePlus Post Type', 'pt_theplus'),
							'themes' => __('Prebuilt Theme Based', 'pt_theplus'),
							'themes_pro' => __('The Plus Theme Pro Settings', 'pt_theplus')
						)
				),
				array(
				'name' => __('Post Name : (Keep Blank if you want to keep default Name)', 'pt_theplus'),
				'desc' => __('Enter value for clients custom post type name. Default: "theplus_clients"', 'pt_theplus'),
				'default' => '',
				'id' => 'client_plugin_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'client_post_type',
						'data-conditional-value' => 'plugin',
					),
				),
				array(
				'name' => __('Category Taxonomy Value : (Keep Blank if you want to keep default Name)', 'pt_theplus'),
				'desc' => __('Enter value for Category Taxonomy Value. Default : "theplus_clients_cat" ', 'pt_theplus'),
				'default' => '',
				'id' => 'client_category_plugin_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'client_post_type',
						'data-conditional-value' => 'plugin',
					),
				),
				array(
				'name' => __('Prebuilt Post Name : (You can find that from here)', 'pt_theplus'),
				'desc' => __('Enter the value of your current post type name which is prebuilt with your theme. E.g.: "theplus_clients" <a href="'.THEPLUS_PLUGIN_URL.'vc_elements/images/post-type-screenshot.png" class="thickbox" title="Get the Post Name of Custom Post type as per above Screenshot.">Check screenshot</a> for how to get that value from URL of your current post type.', 'pt_theplus'),
				'default' => '',
				'id' => 'client_theme_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'client_post_type',
						'data-conditional-value' => 'themes',
					),
				),
				array(
				'name' => __('Prebuilt Category Taxonomy Value : (You can find that from here)', 'pt_theplus'),
				'desc' => __('Enter the value of your current Category Taxonomy Value which is prebuilt with your theme.  E.g. : "theplus_clients_cat" <a href="'.THEPLUS_PLUGIN_URL.'vc_elements/images/taxonomy-screenshot.png" class="thickbox" title="Get the Category Taxonomy Value as per above screenshot.">Check screenshot</a> for how to get that value from URL of your current taxonomy.', 'pt_theplus'),
				'default' => '',
				'id' => 'client_category_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'client_post_type',
						'data-conditional-value' => 'themes',
					),
				),
				/* client option start */
				/* testimonial option start */
				array(
					'name' => __('Testimonial Post Type Settings', 'pt_theplus'),
					'desc' => '',
					'type' => 'title',
					'id' => 'testimonial_post_title'
				),
				array(
						'name' => __('Select Post type Type', 'pt_theplus'),
						'desc' => '',
						'id' => 'testimonial_post_type',
						'type' => 'select',
						'show_option_none' => true,
						'default' => 'disable',
						'options' => array(
							'disable' => __('Disable', 'pt_theplus'),
							'plugin' => __('ThePlus Post Type', 'pt_theplus'),
							'themes' => __('Prebuilt Theme Based', 'pt_theplus'),
							'themes_pro' => __('The Plus Theme Pro Settings', 'pt_theplus')
						)
				),
				array(
				'name' => __('Post Name : (Keep Blank if you want to keep default Name)', 'pt_theplus'),
				'desc' => __('Enter value for testimonial custom post type name. Default: "theplus_testimonial"', 'pt_theplus'),
				'default' => '',
				'id' => 'testimonial_plugin_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'testimonial_post_type',
						'data-conditional-value' => 'plugin',
					),
				),
				array(
				'name' => __('Category Taxonomy Value : (Keep Blank if you want to keep default Name)', 'pt_theplus'),
				'desc' => __('Enter value for Category Taxonomy Value. Default :"theplus_testimonial_cat"', 'pt_theplus'),
				'default' => '',
				'id' => 'testimonial_category_plugin_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'testimonial_post_type',
						'data-conditional-value' => 'plugin',
					),
				),
				array(
				'name' => __('Prebuilt Post Name : (You can find that from here)', 'pt_theplus'),
				'desc' => __('Enter the value of your current post type name which is prebuilt with your theme. E.g.: "theplus_testimonial" <a href="'.THEPLUS_PLUGIN_URL.'vc_elements/images/post-type-screenshot.png" class="thickbox" title="Get the Post Name of Custom Post type as per above Screenshot.">Check screenshot</a> for how to get that value from URL of your current post type.', 'pt_theplus'),
				'default' => '',
				'id' => 'testimonial_theme_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'testimonial_post_type',
						'data-conditional-value' => 'themes',
					),
				),
				array(
				'name' => __('Prebuilt Category Taxonomy Value : (You can find that from here)', 'pt_theplus'),
				'desc' => __('Enter the value of your current Category Taxonomy Value which is prebuilt with your theme.  E.g. : "theplus_testimonial_cat" <a href="'.THEPLUS_PLUGIN_URL.'vc_elements/images/taxonomy-screenshot.png" class="thickbox" title="Get the Category Taxonomy Value as per above screenshot.">Check screenshot</a> for how to get that value from URL of your current taxonomy.', 'pt_theplus'),
				'default' => '',
				'id' => 'testimonial_category_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'testimonial_post_type',
						'data-conditional-value' => 'themes',
					),
				),
				/* testimonial option start */
				/* Team Member option start */
				array(
					'name' => __('Team Member Post Type Settings','pt_theplus'),
					'desc' => '',
					'type' => 'title',
					'id' => 'testimonial_post_title'
				),
				array(
						'name' => __('Select Team Member Post Type', 'pt_theplus'),
						'desc' => '',
						'id' => 'team_member_post_type',
						'type' => 'select',
						'show_option_none' => true,
						'default' => 'disable',
						'options' => array(
							'disable' => __('Disable', 'pt_theplus'),
							'plugin' => __('ThePlus Post Type', 'pt_theplus'),
							'themes' => __('Prebuilt Theme Based', 'pt_theplus'),
							'themes_pro' => __('The Plus Theme Pro Settings', 'pt_theplus')
						)
				),
				array(
				'name' => __('Post Name : (Keep Blank if you want to keep default Name)', 'pt_theplus'),
				'desc' => __('Enter value for team member custom post type name. Default: "theplus_team_member"', 'pt_theplus'),
				'default' => '',
				'id' => 'team_member_plugin_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'team_member_post_type',
						'data-conditional-value' => 'plugin',
					),
				),
				array(
				'name' => __('Category Taxonomy Value (Keep Blank if you want to keep default Name)', 'pt_theplus'),
				'desc' => __('Enter value for Category Taxonomy Value. Default : "theplus_team_member_cat"', 'pt_theplus'),
				'default' => '',
				'id' => 'team_member_category_plugin_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'team_member_post_type',
						'data-conditional-value' => 'plugin',
					),
				),
				array(
				'name' => __('Prebuilt Post Name : (You can find that from here)', 'pt_theplus'),
				'desc' => __('Enter the value of your current post type name which is prebuilt with your theme. E.g.: "theplus_team_member" <a href="'.THEPLUS_PLUGIN_URL.'vc_elements/images/post-type-screenshot.png" class="thickbox" title="Get the Post Name of Custom Post type as per above Screenshot.">Check screenshot</a> for how to get that value from URL of your current post type.', 'pt_theplus'),
				'default' => '',
				'id' => 'team_member_theme_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'team_member_post_type',
						'data-conditional-value' => 'themes',
					),
				),
				array(
				'name' => __('Prebuilt Category Taxonomy Value (You can find that from here)', 'pt_theplus'),
				'desc' => __('Enter the value of your current Category Taxonomy Value which is prebuilt with your theme.  E.g. : "theplus_team_member_cat" <a href="'.THEPLUS_PLUGIN_URL.'vc_elements/images/taxonomy-screenshot.png" class="thickbox" title="Get the Category Taxonomy Value as per above screenshot.">Check screenshot</a> for how to get that value from URL of your current taxonomy.', 'pt_theplus'),
				'default' => '',
				'id' => 'team_member_category_name',
				'type' => 'text',
					'attributes' => array(
						'data-conditional-id'    => 'team_member_post_type',
						'data-conditional-value' => 'themes',
					),
				),
				/* Team Member option start */
            )
        );
		$this->option_metabox[] = array(
            'id' => 'theplus_purchase_code',
            'title' => 'Verify Plugin',
            'show_on' => array(
                'key' => 'options-page',
                'value' => array(
                    'theplus_purchase_code'
                )
            ),
            'show_names' => true,
            'fields' => array(				
				array(
				'name' => __('ThePlus Key', 'pt_theplus'),
				'desc' => __('', 'pt_theplus'),
				'default' => '',
				'id' => 'tp_api_key',
				'type' => 'text',
				),
			),
        );
		
        $this->option_metabox[] = array(
            'id' => 'theplus_import_data',
            'title' => 'Plus Templates',
            'show_on' => array(
                'key' => 'options-page',
                'value' => array(
                    'theplus_import_data'
                )
            ),
            'show_names' => true,
        );
		$this->option_metabox[] = array(
            'id' => 'theplus_about_us',
            'title' => 'About',
            'show_on' => array(
                'key' => 'options-page',
                'value' => array(
                    'theplus_about_us'
                )
            ),
            'show_names' => true,
        );
        return $this->option_metabox;
    }
   
    public function get_option_key($field_id)
    {
        $option_tabs = $this->option_fields();
        foreach ($option_tabs as $option_tab) { //search all tabs
            foreach ($option_tab['fields'] as $field) { //search all fields
                if ($field['id'] == $field_id) {
                    return $option_tab['id'];
                }
            }
        }
        return $this->key; //return default key if field id not found
    }
    /**
     * Public getter method for retrieving protected/private variables
     * @since  1.0.0
     * @param  string  $field Field to retrieve
     * @return mixed          Field value or exception is thrown
     */
    public function __get($field)
    {
        
        // Allowed fields to retrieve
        if (in_array($field, array('key','fields','title','options_page'), true)) {
            return $this->{$field};
        }
        if ('option_metabox' === $field) {
            return $this->option_fields();
        }
        
        throw new Exception('Invalid property: ' . $field);
    }
    
}


// Get it started
$Theplus_plugin_options = new Theplus_plugin_options();
$Theplus_plugin_options->hooks();

/**
 * Wrapper function around cmb_get_option
 * @since  1.0.0
 * @param  string  $key Options array key
 * @return mixed        Option value
 */
function pt_theplus_get_option($key = '')
{
    global $Theplus_plugin_options;
    return cmb_get_option($Theplus_plugin_options->key, $key);
}