<?php
namespace memberpress\courses\controllers;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses as base;
use memberpress\courses\lib as lib;
use memberpress\courses\helpers as helpers;
use memberpress\courses\controllers\admin as ctrl;
use memberpress\courses\models as models;

class App extends lib\BaseCtrl {
  public function load_hooks() {
    add_action( 'init', array( $this, 'maybe_flush_rewrite_rules' ), 99 );
    add_action( 'admin_notices', array( $this, 'courses_activated_admin_notice' ) );
    add_action( 'admin_notices', array( $this, 'required_wordpress_admin_notice' ) );
    add_action( 'admin_init', array($this,'install')); // DB upgrade is handled automatically here now
    add_action( 'mepr-process-options', array($this,'store_options'));
    add_action( 'mepr_display_options_tabs', array( $this, 'courses_tab' ), 99 );
    add_action( 'mepr_display_options', array( $this, 'courses_tab_content' ) );
    // add_action('custom_menu_order', array($this,'admin_menu_order'));
    // add_action('menu_order', array($this,'admin_menu_order'));
    // add_action('menu_order', array($this,'admin_submenu_order'));
    add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    add_action( 'in_admin_header', array($this, 'mp_admin_header'), 0);
    add_filter( 'mepr-extend-rules', array($this, 'protect_sections_lessons'), 10, 3);
    add_action( 'template_redirect', array($this, 'redirect_to_sales_page'));
    add_action( 'customize_register', array($this, 'register_customizer') );
    add_filter( 'post_type_link', array($this, 'lesson_permalink_replace'), 1, 2 );
    add_filter( 'rewrite_rules_array', array($this, 'lesson_permalink_rules') );
    add_filter( 'use_block_editor_for_post_type', array( $this, 'force_block_editor_for_courses' ), 999, 2 );
    add_filter( 'mepr-pre-run-rule-content', array($this, 'show_more_content_on_archive_page'), 10, 3 );
    add_filter( 'the_title', array($this, 'show_lock_icon'), 1000, 2);
  }

  /**
   * Make sure the rewrite rules are flushed to prevent issues with accessing the custom post types.
   * All custom post types should be registered by now.
   *
   * @return void
   */
  public function maybe_flush_rewrite_rules() {
    if ( empty( get_option( 'mepr_courses_flushed_rewrite_rules', '' ) ) ) {
      flush_rewrite_rules();
      update_option( 'mepr_courses_flushed_rewrite_rules', true );
    }
  }

  public function courses_activated_admin_notice() {
    if ( ! empty( $_GET['courses_activated'] ) && 'true' === $_GET['courses_activated'] ) : ?>
      <div class="notice notice-success is-dismissible">
        <p><?php esc_html_e( 'MemberPress Courses has been activated successfully!', 'memberpress-courses' ) ?></p>
      </div>
    <?php endif;
  }

  public function required_wordpress_admin_notice() {
    if(version_compare(get_bloginfo('version'),'5.0', '<') ) : ?>
      <div class="notice notice-warning is-dismissible">
        <p><?php esc_html_e( 'MemberPress Courses requires WordPress 5.0 and above to run smoothly. Please upgrade!', 'memberpress-courses' ) ?></p>
      </div>
    <?php endif;
  }

  public static function load_language() {
    $path_from_plugins_folder = \memberpress\courses\PLUGIN_NAME . '/i18n/';
    load_plugin_textdomain( \memberpress\courses\PLUGIN_NAME, false, $path_from_plugins_folder );
  }

  /**
   * Adds the "Courses" tab to the MemberPress settings page.
   *
   * @return void
   */
  public function courses_tab() {
    ?>
      <a class="nav-tab" id="courses" href="#"><?php _e( 'Courses', 'memberpress-courses' ); ?></a>
    <?php
  }

  /**
   * Renders the "Courses" tab content.
   *
   * @return void
   */
  public function courses_tab_content() {
    ?>
    <div id="courses" class="mepr-options-hidden-pane">
      <?php
        $options = \get_option('mpcs-options');
        require_once(base\VIEWS_PATH . '/admin/options/form.php');
        require_once(base\VIEWS_PATH . '/admin/options/general.php');
      ?>
    </div>
    <?php
  }

  /**
   * Saves the "Courses" data after Options page is updated
   *
   * @return void
   */
  public function store_options(){
    if(lib\Utils::is_post_request() && isset($_POST['mpcs-options'])) {

      if(isset($_POST['mpcs-options']['courses-slug'])){
        $_POST['mpcs-options']['courses-slug'] = preg_replace(
          array('/ +/', '/[^A-Za-z0-9_-]/'),
          array('-', '')
          , $_POST['mpcs-options']['courses-slug']);
      }

      if(!isset($_POST['mpcs-options']['classroom-mode'])) {
        $_POST['mpcs-options']['classroom-mode'] = 0;
      }

      // Maybe update courses slug in classroom menu
      $options = \get_option('mpcs-options');

      if($_POST['mpcs-options']['courses-slug'] !== $options['courses-slug']){
        $menu = wp_get_nav_menu_items('MemberPress Classroom');
        if($menu){
          foreach ($menu as $item){
            $old_slug = $options['courses-slug'];
            $slug = $_POST['mpcs-options']['courses-slug'];
            $data = array(
                'menu-item-object-id'   => $item->object_id,
                'menu-item-object'      => $item->object,
                'menu-item-parent-id'   => $item->menu_item_parent,
                'menu-item-position'    => $item->menu_order,
                'menu-item-type'        => $item->type,
                'menu-item-title'       => $item->title,
                'menu-item-url'         => str_replace('/'.$old_slug, '/'.$slug, $item->url),
                'menu-item-description' => $item->description,
                'menu-item-attr-title'  => $item->attr_title,
                'menu-item-target'      => $item->target,
                'menu-item-classes'     => implode(' ',$item->classes),
                'menu-item-xfn'        => $item->xfn,
            );
            wp_update_nav_menu_item('MemberPress Classroom', $item->db_id, $data);
          }
        }
      }

      \update_option('mpcs-options',$_POST['mpcs-options']);
    }
  }

  /**
  * Register custom post type for all CPTs
  * Called from activation.php
  * Hook: register_activation_hook
  */
  public function register_all_cpts() {
    $courses_ctrl = ctrl\Courses::fetch();
    $courses_ctrl->register_post_type();
    $lesson_ctrl = ctrl\Lessons::fetch();
    $lesson_ctrl->register_post_type();
  }

  public function toplevel_menu_route() {
    $courses_ctrl = ctrl\Courses::fetch();
    ?>
    <script>
      window.location.href="<?php echo $courses_ctrl->cpt_admin_url(); ?>";
    </script>
    <?php
  }

  public static function setup_menus() {
    $app = App::fetch();
    add_action('admin_menu', array($app,'menu'));
  }

  public function menu() {
    self::admin_separator();
    $courses_ctrl = ctrl\Courses::fetch();

    $menu_title = __('Courses', 'memberpress-courses');
    $menu_title .= sprintf( '<span style="background-color: #ed5a4c; color: #fff; font-weight: bold; display: inline-block; margin-left: 5px; padding: 2px 6px 3px; border-radius: 100px; font-size: 10px;">%s</span>', __('NEW', 'memberpress', 'memberpress-courses') );

    $courses_menu_hook = add_submenu_page(
      'memberpress',
      __('MemberPress Courses', 'memberpress-courses'),
      $menu_title,
      'manage_options',
      $courses_ctrl->cpt_admin_url(),
      array( $this, 'toplevel_menu_route' )
    );
  }

  /********* INSTALL PLUGIN ***********/
  public function install() {
    $db = lib\Db::fetch();
    $db->upgrade();
  }

  /**
   * Add a separator to the WordPress admin menus
   */
  public static function admin_separator() {
    global $menu;

    // Prevent duplicate separators when no core menu items exist
    if(!lib\Utils::is_user_admin()) { return; }

    $menu[] = array('', 'read', 'separator-'.base\PLUGIN_NAME, '', 'wp-menu-separator '.base\PLUGIN_NAME);
  }

  /*
   * Move our custom separator above our admin menu
   *
   * @param array $menu_order Menu Order
   * @return array Modified menu order
   */
  public static function admin_menu_order($menu_order) {
    if(!$menu_order) {
      return true;
    }

    if(!is_array($menu_order)) {
      return $menu_order;
    }

    // Initialize our custom order array
    $new_menu_order = array();

    // Menu values
    $second_sep   = 'separator2';
    $custom_menus = array('separator-'.base\PLUGIN_NAME, base\PLUGIN_NAME);

    // Loop through menu order and do some rearranging
    foreach($menu_order as $item) {
      // Position MemberPress Courses menu above appearance
      if($second_sep == $item) {
        // Add our custom menus
        foreach($custom_menus as $custom_menu) {
          if(array_search($custom_menu, $menu_order)) {
            $new_menu_order[] = $custom_menu;
          }
        }

        // Add the appearance separator
        $new_menu_order[] = $second_sep;

      // Skip our menu items down below
      }
      elseif(!in_array($item, $custom_menus)) {
        $new_menu_order[] = $item;
      }
    }

    // Return our custom order
    return $new_menu_order;
  }

  //Organize the CPT's in our submenu
  public static function admin_submenu_order($menu_order) {
    global $submenu;

    static $run = false;

    //no sense in running this everytime the hook gets called
    if($run) { return $menu_order; }

    //just return if there's no memberpress-courses menu available for the current screen
    if(!isset($submenu[base\PLUGIN_NAME])) { return $menu_order; }

    $run = true;
    $new_order = array();
    $i = 2;

    foreach($submenu[base\PLUGIN_NAME] as $sub) {
      if($sub[0] == __('Courses', 'memberpress-courses')) {
        $new_order[0] = $sub;
      }
      elseif($sub[0] == __('Lessons', 'memberpress-courses')) {
        $new_order[1] = $sub;
      }
      else {
        $new_order[$i++] = $sub;
      }
    }

    ksort($new_order);

    $submenu[base\PLUGIN_NAME] = $new_order;

    return $menu_order;
  }

  public static function mp_admin_header() {
    global $current_screen;

    if($current_screen->post_type === models\Course::$cpt && $current_screen->base == 'post') {
      require_once(base\VIEWS_PATH . '/admin/courses/curriculum_header.php');
    }

    if($current_screen->post_type === models\Lesson::$cpt && $current_screen->base == 'post') {
      require_once(base\VIEWS_PATH . '/admin/lessons/lesson_header.php');
    }

    if($current_screen->id === 'mp-courses_page_memberpress-courses-options') { ?>
      <div id="mp-admin-header"><img class="mp-logo" src="<?php echo base\IMAGES_URL . '/memberpress-logo-color.svg'; ?>" /></div>
      <?php
    }

  }

  public function enqueue_admin_scripts($hook) {
    \wp_enqueue_style('mpcs-simplegrid', base\CSS_URL . '/simplegrid.css', array(), base\VERSION);
    \wp_enqueue_style('mpcs-jquery-magnific-popup', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css');
    \wp_enqueue_style('mpcs-fontello-styles', base\FONTS_URL.'/fontello/css/mp-courses.css', array(), base\VERSION);
    \wp_enqueue_style('mpcs-admin-shared', base\CSS_URL . '/admin_shared.css', array('wp-pointer','mpcs-jquery-magnific-popup','mpcs-simplegrid','mpcs-fontello-styles'), base\VERSION);
    \wp_register_script('mpcs-jquery-magnific-popup', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js', array('jquery'));
    \wp_enqueue_script('mpcs-tooltip', base\JS_URL . '/tooltip.js', array('jquery','wp-pointer','mpcs-jquery-magnific-popup'), base\VERSION);
    if(strstr($hook, 'memberpress-options') !== false) {
      wp_enqueue_style( 'wp-color-picker' );
      \wp_enqueue_style('mpcs-settings-table', base\CSS_URL . '/settings_table.css', array(), base\VERSION);
      \wp_enqueue_script('mpcs-settings-table', base\JS_URL . '/settings_table.js', array('jquery', 'wp-color-picker'), base\VERSION);
      wp_enqueue_script('plupload-all');

      // Let's localize data for our drag and drop settings

      $plupload_init = array(
        'runtimes'            => 'html5,silverlight,flash,html4',
        'browse_button'       => 'plupload-browse-button',
        'container'           => 'plupload-upload-ui',
        'drop_element'        => 'drag-drop-area',
        'file_data_name'      => 'async-upload',
        'multiple_queues'     => true,
        'max_file_size'       => wp_max_upload_size().'b',
        'url'                 => admin_url('admin-ajax.php'),
        'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
        'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
        'filters'             => array(array('title' => __('Allowed Files', 'memberpress-courses'), 'extensions' => '*')),
        'multipart'           => true,
        'urlstream_upload'    => true,
        'multi_selection'     => false, // Limit selection to just one

        // additional post data to send to our ajax hook
        'multipart_params'    => array(
          '_ajax_nonce' => wp_create_nonce('photo-upload'),
          'action'      => 'logo_uploader',            // the ajax action name
        ),
      );

      // we should probably not apply this filter, plugins may expect wp's media uploader...
      $plupload_init = apply_filters('plupload_init', $plupload_init);
      \wp_localize_script( 'mpcs-settings-table', 'MPCS_Settings', $plupload_init );
    }
  }

  /**
  * Protect lessons associated with course based on rule
  * @see load_hooks(), add_filter('mepr-extend-rules')
  * @param array $post_rules All rules for post
  * @param MeprRule $rule Current rule
  * @param mixed $context We only handle WP_Post here
  * @return array $post_rules Modified post rules
  */
  public function protect_sections_lessons($post_rules, $rule, $context) {
    if(is_a($context, 'WP_Post') && $rule->mepr_type !== 'custom' && $context->post_type === models\Lesson::$cpt) {
      switch($rule->mepr_type) {
        case 'all_' . models\Course::$cpt:
          if(!\MeprRule::is_exception_to_rule($context, $rule))
              $post_rules[] = $rule;
          break;
        case 'single_' . models\Course::$cpt:
          $lesson = new models\Lesson($context->ID);
          if($course = $lesson->course()) {
            if($rule->mepr_content == $course->ID)
              $post_rules[] = $rule;
          }
          break;
        case 'all_tax_post_tag':
        case 'tax_'.ctrl\CourseTags::$tax.'||cpt_' . models\Course::$cpt:
        case 'tag':
          $lesson = new models\Lesson($context->ID);
          if($course = $lesson->course()) {
            if(has_term($rule->mepr_content, ctrl\CourseTags::$tax, $course->ID)){
              $post_rules[] = $rule;
            }
          }
          break;
        case 'all_tax_post_category':
        case 'tax_'.ctrl\CourseCategories::$tax.'||cpt_' . models\Course::$cpt:
        case 'category':
          $lesson = new models\Lesson($context->ID);
          if($course = $lesson->course()) {
            if(has_term($rule->mepr_content, ctrl\CourseCategories::$tax, $course->ID)){
              $post_rules[] = $rule;
            }
          }
          break;
      }
    }

    return $post_rules;
  }

  /**
  * Unauthorized User visit’s the course URL
  * If the Sales page URL is set for the course then the course URL will simply redirect to the Sales page.
  * @return void
  */
  public function redirect_to_sales_page(){
    global $wp_query;

    if(!is_single()){
      return;
    }

    if(current_user_can('memberpress_authorized')) {
      return;
    }

    if($wp_query->post->post_type == models\Course::$cpt){
      $course = new models\Course($wp_query->post->ID);
    }
    elseif ($wp_query->post->post_type == models\Lesson::$cpt) {
      $lesson = new models\Lesson($wp_query->post->ID);
      $course = $lesson->course();
    }

    if(!isset($course)) {
      return;
    }

    $sales_url = $course->sales_url;
    if(wp_http_validate_url($sales_url)){
      lib\Utils::wp_redirect($sales_url);
    }
  }

  /**
   * Add customizer section and settings
   *
   * @param  mixed $wp_customize
   * @return void
   */
  public function register_customizer($wp_customize){

    // Don't add these settings unless Classroom Mode is enabled.
    $options = \get_option('mpcs-options');
    $classroom_mode = helpers\Options::val($options,'classroom-mode', 1);
    if ( empty( $classroom_mode ) ) {
      return;
    }

    $sections = apply_filters( base\SLUG_KEY . '_customiser_sections', array() );
    $settings = apply_filters( base\SLUG_KEY . '_customiser_settings', array() );

    foreach ($sections as $section) {
      \extract($section);

      $wp_customize->add_section( $name,
        array(
          'title' => $title,
        )
      );
    }

    foreach ($settings as $setting) {
      \extract($setting);

      switch ($type) {
        case 'color':
          $wp_customize->add_setting( $name,
            array(
              'default' => $default,
              'transport' => 'refresh',
              'type' => 'option',
              'sanitize_callback' => $sanitize_callback
            )
          );
          $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize,  $name,
          array(
            'label' => $label,
            'section' => $section,
          ) ) );
          break;

          case 'image':
            $wp_customize->add_setting( $name,
              array(
                'default' => $default,
                'transport' => 'refresh',
                'type' => 'option',
                'sanitize_callback' => $sanitize_callback
              )
            );
            $wp_customize->add_control( new \WP_Customize_Media_Control( $wp_customize,  $name,
            array(
              'label' => $label,
              'section' => $section,
              'mime_type' => 'image',
            ) ) );
            break;

        default:
          $wp_customize->add_setting( $name,
            array(
              'default' => $default,
              'transport' => 'refresh',
              'type' => 'option',
              'sanitize_callback' => $sanitize_callback
            )
          );
          $wp_customize->add_control(
            $name,
            array(
              'label' => $label,
              'section' => $section,
              'type' => $type
            )
          );
          break;
      }
    }

  }



  /**
   * Replace tags in lesson permalink structure
   *
   * @param  mixed $post_link
   * @param  mixed $post
   * @return void
   */
  public function lesson_permalink_replace( $post_link, $post ){
    if ( is_object( $post ) && $post->post_type == models\Lesson::$cpt ){
      $lesson = new models\Lesson($post->ID);
      $course = $lesson->course();

      // Permalink if lesson is associated with a course
      if($course){
        $slug = $course->post_name;
        return str_replace( '%course_slug%', $slug, $post_link );
      }

      // Default lesson permalink
      return str_replace( '/'.helpers\Courses::get_permalink_base().'/%course_slug%/', '/', $post_link );
    }
    return $post_link;
  }


  /**
   * Ensure that courses and lessons are using the block editor.
   *
   * @param  boolean  $use        Whether to use the block editor in the admin.
   * @param  string   $post_type  Post type
   *
   * @return boolean
   */
  public function force_block_editor_for_courses( $use, $post_type ) {
    $post_types = array(
      models\Course::$cpt
    );
    if ( in_array( $post_type, $post_types ) ) {
      $use = true;
    }
    return $use;
  }

  /**
   * Run this if you want default lesson permalink to still work
   * For now, I think it's not necessary
   *
   * @param  mixed $rules
   * @return void
   */
  public function lesson_permalink_rules( $rules ) {
    $customRules = [];
    $customRules[ helpers\Courses::get_permalink_base() . '/([^/]+)/lessons/([^/]+)/?$' ] = 'index.php?'.models\Course::$cpt.'=$matches[2]&'.models\Lesson::$cpt.'=$matches[2]'; // makes /courses/coursename/lessons/lessonname/ resolves to lesson post
    $customRules[ 'lessons/([^/]+)/?$' ] = 'index.php?'.models\Lesson::$cpt.'=$matches[1]'; // Comment this line if you dont want lessons/lessonname to work alongside /courses/coursename/lessons/lessonname/

    return $customRules + $rules;
  }

  /**
   * Show course "more content" even if post is protected.
   *
   * @param mixed $show_unauth_message
   * @param mixed $current_post
   * @param mixed $uri
   *
   * @return bool
   */
  public function show_more_content_on_archive_page($show_unauth_message, $current_post, $uri){
    if(
      $current_post->post_type == models\Course::$cpt &&
      helpers\Courses::is_course_archive() &&
      true == $show_unauth_message
    ){
      $show_unauth_message = false;
    }
    return $show_unauth_message;
  }

  /**
   * SHow lock icon if course is locked
   * @param mixed $title
   * @param mixed $post_id
   *
   * @return [type]
   */
  public function show_lock_icon($title, $post_id) {
    $post = get_post($post_id);

    if(!class_exists('MeprRule')) { return $title; }

    if(is_admin() || defined('REST_REQUEST')) { return $title; }

    if(!isset($post->ID) || !$post->ID) { return $title; }

    if(strpos($title, 'mpcs-lock') !== false) { return $title; } //Already been here?

    if(\MeprRule::is_locked($post) && helpers\Courses::is_course_archive()) {
      $title = '<i class="mpcs-icon mpcs-lock"></i>' . " {$title}";
    }

    return $title;
  }
}
