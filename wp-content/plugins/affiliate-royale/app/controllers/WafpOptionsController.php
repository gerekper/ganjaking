<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpOptionsController
{
  public static function load_hooks()
  {
    add_action('wp_ajax_add_commission_level', 'WafpOptionsController::add_commission_level_callback');

    if(isset($_REQUEST['page']) and $_REQUEST['page'] == 'affiliate-royale-options')
    {
      add_action('admin_print_scripts', 'WafpOptionsController::add_tinymce_js' );
      add_action('admin_print_styles', 'WafpOptionsController::add_tinymce_css' );
    }
  }

  public static function add_tinymce_js()
  {
    wp_register_style('ea-settings-table', WAFP_CSS_URL.'/settings_table.css', array(), WAFP_VERSION);
    wp_enqueue_style('ea-options', WAFP_CSS_URL.'/admin-options.css', array('ea-settings-table'), WAFP_VERSION);

    wp_register_script('ea-settings-table', WAFP_JS_URL.'/settings-table.js', array('jquery'), WAFP_VERSION);
    wp_enqueue_script('ea-options', WAFP_JS_URL.'/admin-options.js', array('jquery','ea-settings-table'), WAFP_VERSION);
  }

  public static function add_tinymce_css()
  {
    $wp_scripts = new WP_Scripts();
    $ui = $wp_scripts->query('jquery-ui-core');
    $url = "//ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.css";
    wp_enqueue_style( 'jquery-ui', $url, $ui->ver );
  }

  public static function add_commission_level_callback() {
    global $wafp_options;
    $level = $_REQUEST['level'];
    ?>
    <li class="wafp-hidden" id="wafp-level-<?php echo $level; ?>"><?php printf(__('Level %d:', 'affiliate-royale', 'easy-affiliate'),$level); ?> <span class="wafp_commission_currency_symbol"><?php echo $wafp_options->currency_symbol; ?></span><input id="<?php echo $wafp_options->commission_str; ?>_<?php echo $level; ?>" class="form-field" size="6" value="<?php echo WafpUtils::format_float('0'); ?>" name="<?php echo $wafp_options->commission_str; ?>[]"><span class="wafp_commission_percentage_symbol">%</span></li>
    <?php
    die(); // this is required to return a proper result
  }

  public static function route()
  {
    $action = (isset($_REQUEST['action'])?$_REQUEST['action']:null);
    if($action=='process-form')
      return self::process_form();
    else
      return self::display_form();
  }

  public static function display_form()
  {
    global $wafp_options;
    if(WafpUtils::is_logged_in_and_an_admin())
    {
      //if(!get_option('users_can_register'))
      //  require(WAFP_VIEWS_PATH . '/shared/wp_cant_register.php');

      require(WAFP_VIEWS_PATH . '/options/form.php');
    }
  }

  public static function process_form()
  {
    global $wafp_options;

    if(WafpUtils::is_logged_in_and_an_admin())
    {
      $errors = array();

      $errors = apply_filters('wafp_validate_options', $wafp_options->validate($_POST,$errors));

      $wafp_options->update($_POST);

      if( count($errors) > 0 )
        require(WAFP_VIEWS_PATH . '/shared/errors.php');
      else
      {
        do_action('wafp_process_options');
        $wafp_options->store();
        $_REQUEST = array(); // Clear the request array now that we've got everything saved
        require(WAFP_VIEWS_PATH . '/options/options_saved.php');
      }

      //if(!get_option('users_can_register'))
      //  require(WAFP_VIEWS_PATH . '/shared/wp_cant_register.php');

      require(WAFP_VIEWS_PATH . '/options/form.php');
    }
  }
}
