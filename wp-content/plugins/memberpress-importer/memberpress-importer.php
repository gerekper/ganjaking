<?php
/*
Plugin Name: MemberPress Importer
Plugin URI: http://memberpress.com
Description: Allows you to import Users, Products, Subscriptions, Transactions, Coupons and Rules from a csv file
Version: 1.6.7
Author: Caseproof, LLC
Author URI: http://caseproof.com
Text Domain: memberpress-importer
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if( is_plugin_active('memberpress/memberpress.php') ) {

  define('MPIMP_PLUGIN_SLUG',plugin_basename(__FILE__));
  define('MPIMP_PLUGIN_NAME',dirname(MPIMP_PLUGIN_SLUG));
  define('MPIMP_PATH',WP_PLUGIN_DIR.'/'.MPIMP_PLUGIN_NAME);
  define('MPIMP_IMPORTERS_PATH',MPIMP_PATH . '/importers');
  define('MPIMP_URL',plugins_url($path = '/'.MPIMP_PLUGIN_NAME));
  define('MPIMP_DURATION',2); // number of seconds a processing request will last
  define('MPIMP_TIMEOUT',45); // number of seconds before this thing times out
  define('MPIMP_EDITION', 'memberpress-importer');

  // Autoload all the requisite classes
  function mpimp_autoloader($class_name) {
    // Only load Affiliate Royale classes here
    if(preg_match('/^Mpimp.+$/', $class_name))
    {
      if( preg_match('/^MpimpBaseImporter$/', $class_name) or
          preg_match('/^MpimpImporterFactory$/', $class_name) )
        $filepath = MPIMP_PATH . "/{$class_name}.php";
      else if(preg_match('/^.+PostsImporter$/', $class_name)) // load this first because others depend on it
        $filepath = MPIMP_IMPORTERS_PATH . "/{$class_name}.php";
      else if(preg_match('/^.+Importer$/', $class_name))
        $filepath = MPIMP_IMPORTERS_PATH . "/{$class_name}.php";
      else
        $filepath = MPIMP_PATH . "/{$class_name}.php";

      if(file_exists($filepath))
        require_once($filepath);
    }
  }

  // if __autoload is active, put it on the spl_autoload stack
  if( is_array(spl_autoload_functions()) and
      in_array('__autoload', spl_autoload_functions()) ) {
     spl_autoload_register('__autoload');
  }

  // Add the autoloader
  spl_autoload_register('mpimp_autoloader');

  /***** Define Exceptions *****/
  class MpimpStopImportException extends Exception { }

  class MpimpAppController {
    public $importers;

    public function __construct() {
      //$this->importers = MpimpImporterFactory::available();
      $this->load_hooks();
    }

    public function load_hooks() {
      add_action('mepr_menu', array($this,'menu'));
      add_action('admin_enqueue_scripts', array($this,'load_scripts'));
      add_action('wp_ajax_process_csv', array($this,'process_csv'));
      add_action('wp_ajax_delete_csv', array($this,'delete_csv'));
    }

    public function menu() {
      add_submenu_page( 'memberpress',
                        __('Import', 'memberpress-importer'),
                        __('Import', 'memberpress-importer'),
                        'administrator',
                        'memberpress-import',
                        array($this,'route') );
    }

    public function route() {
      $message = '';
      $errors  = array();
      $results = array();

      if( strtoupper( $_SERVER['REQUEST_METHOD'] ) == 'POST' ) {
        if(!current_user_can('import'))
          throw new Exception(__("You don't have sufficient privileges for this", 'memberpress-importer'));

        try {
          $args = ( isset($_REQUEST['args']) ? $_REQUEST['args'] : array() );

          $this->validate_importer();

          $importer = $_REQUEST['action'];
          $tmpname  = $_FILES['mepr-'.$importer.'-csv-file']['tmp_name'];
          $filename = MeprUtils::random_string(10,true,true) . '.csv';
          $filepath = $this->csv_file_dir() . '/' . $filename;

          if(!move_uploaded_file( $tmpname, $filepath ))
            throw new Exception(__('There was a system error processing your file. You may want to check the write permissions on your WordPress uploads directory', 'memberpress-importer'));

          $results = $this->import_from_csv($importer,$filepath,$args);
        }
        catch( Exception $e ) {
          $errors[] = $e->getMessage();
        }

        if(!empty($errors) || $results['status'] == 'complete') {
          @unlink($filepath);
        }

        if(empty($errors)) {
          /* <div class="mpimp-results"
                  data-status="<?php echo $results['status']; ?>"
                  data-importer="<?php echo $results['importer']; ?>"
                  data-filename="<?php echo $results['filename']; ?>"
                  data-wpnonce="<?php echo wp_create_nonce('MpimpAppController'); ?>"
                  data-row="<?php echo $results['row']; ?>">
          */
          $results['_wpnonce'] = $_REQUEST['_wpnonce'];
          $this->display_results($results);
        }
        else
          $this->display_importer('', $errors);
      }
      else
        $this->display_importer($message, $errors);

      // MpimpMagicMembersController::route();
    }

    public function load_scripts($hook) {
      if($hook=='memberpress_page_memberpress-import') {
        wp_enqueue_style('mpimp-css', MPIMP_URL.'/memberpress-importer.css', array(), MEPR_VERSION);
        wp_enqueue_script('mpimp-js', MPIMP_URL.'/memberpress-importer.js', array('jquery'), MEPR_VERSION);
      }
    }

    private function validate_importer() {
      if( !isset($_REQUEST['action']) or
          !in_array($_REQUEST['action'], array_keys($this->importers)) or
          !isset($_REQUEST['_wpnonce']) or
          !wp_verify_nonce($_REQUEST['_wpnonce'],'MpimpImporter') )
      {
        throw new Exception(__('Forbidden', 'memberpress-importer'));
      }

      if(empty($_FILES['mepr-'.$_REQUEST['action'].'-csv-file']['tmp_name'])) {
        throw new Exception(__('You must select a file before uploading', 'memberpress-importer'));
      }
    }

    private function human_readable_class($str) {
      $str = preg_replace('/^Mpimp(.*)Importer$/', '$1', $str);
      $str = preg_replace('/([A-Z])/', ' $1', $str);
      return $str;
    }

    private function display_importer($message,$errors) {
      $nonce = wp_create_nonce('MpimpImporter');
      ?>
        <div class="wrap mepr-importer">
          <div class="icon32"></div>
          <h2><?php _e('Import', 'memberpress-importer'); ?></h2>
          <div class="mepr-import">
            <?php require MEPR_VIEWS_PATH . '/shared/errors.php'; ?>

            <p><?php printf( __('Before importing for the first time go to the %1$s to get instructions.', 'memberpress-importer'), '<a href="http://www.memberpress.com/user-manual/importing">'.__('User Manual Page on Importing', 'memberpress-importer').'</a>' ); ?></p>
            <p><strong><?php printf( __('Note: Make sure your database is backed up before running any MemberPress import.', 'memberpress-importer') ); ?></p>

            <div>
              <?php _e('Select the type of file to import:', 'memberpress-importer'); ?>&nbsp;&nbsp;
              <select id="mpimp-import-file-type">
                <?php foreach( $this->importers as $importer => $impclass ): ?>
                  <?php $importer_param = isset($_REQUEST['importer']) ? $_REQUEST['importer'] : ''; ?>
                  <option value="<?php echo $importer; ?>" <?php selected($importer,$importer_param); ?>><?php echo $this->human_readable_class($impclass); ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>&nbsp;</div>

            <?php foreach( $this->importers as $importer => $impclass ): ?>
              <?php $obj = MpimpImporterFactory::fetch($this->importers[$importer]); ?>
              <div id="mpimp-<?php echo $importer; ?>-form" class="mepr_hidden mpimp-importer-form">
                <form action="" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="action" value="<?php echo $importer; ?>" />
                  <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
                  <label for="mepr-<?php echo $importer; ?>-csv-file"><?php printf(__('%s CSV File:', 'memberpress-importer'), $this->human_readable_class($impclass)); ?></label>
                  <div>&nbsp;</div>
                  <input type="file" name="mepr-<?php echo $importer; ?>-csv-file" id="mepr-<?php echo $importer; ?>-csv-file" />
                  <div>&nbsp;</div>
                  <?php $obj->form(); ?>
                  <div>&nbsp;</div>
                  <input type="submit" name="submit" class="button button-primary" value="<?php _e('Upload', 'memberpress-importer'); ?>" />
                </form>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php
    }

    private function display_results($results) {
      $errors = (isset($results['errors']) and !empty($results['errors'])) ? $results['errors'] : array();
      $messages = (isset($results['messages']) and !empty($results['messages'])) ? $results['messages'] : array();
      $failed_rows = (isset($results['failed_rows']) and !empty($results['failed_rows'])) ? $results['failed_rows'] : array();

      ?>
      <script type='text/javascript'>
      /* <![CDATA[ */
      var MpimpResults = <?php echo json_encode($results); ?>;
      /* ]]> */
      </script>
      <div class="wrap mepr-importer">
        <div class="icon32"></div>
        <h2><?php _e('Import', 'memberpress-importer'); ?></h2>
        <div class="mpimp-results">
          <h3 class="mpimp-loading-gif"><img src="<?php echo admin_url('images/loading.gif'); ?>" /> <?php printf(__('Processing %s CSV File...', 'memberpress-importer'), ucwords($results['importer'])); ?></h3>
          <h3 class="mpimp-processing-complete"><?php printf(__('%s CSV File Processing Complete', 'memberpress-importer'), ucwords($results['importer'])); ?></h3>
          <p><a href="<?php echo $_SERVER['REQUEST_URI']; ?>" class="mpimp-return-link">&laquo; <?php _e('Import Another File', 'memberpress-importer'); ?></a></p>
          <div class="mpimp-summary">
            <h3><span class="mepr-total-successful"><?php echo $results['successful']; ?></span> <?php _e('Rows Successfully Imported', 'memberpress-importer'); ?></h3>
            <h3><span class="mepr-total-failed"><?php echo $results['failed']; ?></span> <?php _e('Rows Failed to be Imported', 'memberpress-importer'); ?></h3>
            <h3><span class="mepr-total-processed"><?php echo $results['total']; ?></span> <?php _e('Total Rows Processed', 'memberpress-importer'); ?></h3>
          </div>
          <p><?php _e('Row Success Messages:', 'memberpress-importer'); ?><br/>
          <textarea class="mpimp-messages"><?php echo implode("\n",$messages); ?></textarea>
          </p>
          <p><?php _e('Row Errors:', 'memberpress-importer'); ?><br/>
          <textarea class="mpimp-errors"><?php echo implode("\n",$errors); ?></textarea>
          </p>
          <p><?php _e('CSV for failed rows ... copy these, fix and reimport:', 'memberpress-importer'); ?><br/>
          <textarea class="mpimp-error-rows"><?php echo implode(',',$results['headers']); ?></textarea>
          </p>
        </div>
      </div>
      <?php
    }

    public function process_csv() {
      if(!isset($_REQUEST['_wpnonce']) or !wp_verify_nonce($_REQUEST['_wpnonce'],'MpimpImporter'))
        die(json_encode(array('error' => __("Why You Creepin'?", 'memberpress-importer'))));

      if(!current_user_can('import'))
        die(json_encode(array('error' => __("You don't have sufficient privileges for this", 'memberpress-importer'))));

      $args = (isset($_REQUEST['args']) ? $_REQUEST['args'] : array());

      $filepath = $this->csv_file_dir() . '/' . $_REQUEST['filename'];
      $results = $this->import_from_csv( $_REQUEST['importer'],
                                         $filepath,
                                         $args,
                                         $_REQUEST['row'],
                                         $_REQUEST['offset'],
                                         $_REQUEST['headers'] );

      // Blast that file away before returing to the javascript
      if($results['status']=='complete') { @unlink($filepath); }

      die(json_encode($results));
    }

    public function delete_csv() {
      if(!isset($_REQUEST['_wpnonce']) or !wp_verify_nonce($_REQUEST['_wpnonce'],'MpimpImporter'))
        die(json_encode(array('error' => __("Why You Creepin'?", 'memberpress-importer'))));

      if(!current_user_can('import'))
        die(json_encode(array('error' => __("You don't have sufficient privileges for this", 'memberpress-importer'))));

      if(!isset($_REQUEST['filename']))
        die(json_encode(array('error' => __("No file was specified", 'memberpress-importer'))));

      $filepath = $this->csv_file_dir() . '/' . $_REQUEST['filename'];

      @unlink($filepath);

      die(json_encode(array('message' => 'Success')));
    }

    /** Crunches through a CSV file and mines for gold */
    public function import_from_csv($importer,$filepath,$args=array(),$row=0,$offset=0,$headers=array(),$cli=false) {
      if( !$cli ) { @set_time_limit(MPIMP_TIMEOUT); }

      //Let's make sure line endings are accounted for
      @ini_set("auto_detect_line_endings", true);

      $start_time = time();
      $action = 'process_csv'; // This is the ajax action for the importation
      $data = true; // can only become false after a trip through fgetcsv

      $fh = @fopen($filepath, "r");

      $delimiter = $this->get_file_delimeter($filepath);
      $total = 0;
      $successful = 0;
      $failed = 0;
      $failed_rows = array();
      $messages = array();
      $errors = array();

      // grab the headers on the first pass
      if( $row==0 ) {
        if(($headers=$data=fgetcsv($fh, 0, $delimiter))!==false) { $row++; }
      }
      else {
        // Set the file pointer to the right place
        if( $offset > 0 ) { @fseek( $fh, $offset ); }

        while( ($cli or (time()-$start_time)<=MPIMP_DURATION) and
               (($data=fgetcsv($fh, 0, $delimiter))!==false) )
        {
          $total++;
          $row_start_time = time();
          $row_msg = __('Row %1$d: %2$s', 'memberpress-importer');
          try {
            $rowdata = $this->get_assoc_array($headers, $data);
            $obj = MpimpImporterFactory::fetch($this->importers[$importer]);
            $msg = sprintf( $row_msg, $row+1, call_user_func(array($obj, "import"), $rowdata, $args));

            if($cli)
              echo $msg . " (time: " . (time() - $row_start_time) . " seconds)\n";
            else
              $messages[] = $msg;

            $successful++;
            do_action('mpimp-row-import-successful', $obj, $rowdata, $args);
          }
          catch( MpimpStopImportException $e ) {
            $err = sprintf( $row_msg, $row+1, $e->getMessage() );

            if($cli)
              echo $err . " (time: " . (time() - $row_start_time) . " seconds)\n";
            else
              $errors[] = $err;

            $failed++;
            $data=false;
            break; // Stop Processing
          }
          catch( Exception $e ) {
            $err = sprintf( $row_msg, $row+1, $e->getMessage() );

            if($cli)
              echo $err . " (time: " . (time() - $row_start_time) . " seconds)\n";
            else
              $errors[] = $err;

            // In csv format in all it's glory, bro
            $failed_rows[] = $this->sputcsv($data);
            $failed++;
          }

          $row++;
        }
      }

      // Indicates that there were no more lines to process
      $status = (($data==false)?'complete':'incomplete');

      $filename = basename($filepath);
      $offset   = @ftell($fh);

      fclose($fh);

      return compact( 'action', 'status', 'headers', 'importer', 'filename',
                      'total', 'successful', 'failed', 'messages',
                      'errors', 'row', 'offset', 'failed_rows', 'args' );
    }

    private function get_file_delimeter($filepath) {
      $delimiters = apply_filters(
        'mepr-importer-delimiters',
        array(
          ';' => 0,
          ',' => 0,
          "\t" => 0,
          "|" => 0
        ),
        $filepath
      );

      $handle = fopen($filepath, "r");

      if($handle) {
        $first_line = fgets($handle);
        fclose($handle);

        foreach ($delimiters as $delimiter => &$count) {
          $count = count(str_getcsv($first_line, $delimiter));
        }

        if(max($delimiters) > 0) {
          return array_search(max($delimiters), $delimiters);
        }
      }

      return ','; // Default to comma
    }

    private function get_assoc_array($headers, $row) {
      $new_row = array();

      foreach($headers as $i => $col) {
        $new_row[trim($col)] = trim($row[$i]);
      }

      return $new_row;
    }

    // Blatantly copied from http://www.php.net/manual/en/function.fputcsv.php#96937
    private function sputcsv($row, $delimiter = ',', $enclosure = '"'/*, $eol = "\n"*/)
    {
      static $fp = false;
      if ($fp === false) {
        $fp = fopen('php://temp', 'r+'); // see http://php.net/manual/en/wrappers.php.php - yes there are 2 '.php's on the end.
        // NB: anything you read/write to/from 'php://temp' is specific to this filehandle
      }
      else {
        rewind($fp);
      }

      if(fputcsv($fp, $row, $delimiter, $enclosure) === false) {
        return false;
      }

      rewind($fp);
      $csv = fgets($fp);

      //if ($eol != PHP_EOL) {
      //  $csv = substr($csv, 0, (0 - strlen(PHP_EOL))) . $eol;
      //}

      return $csv;
    }

    /** This returns the directory where csv files will be temporarily stored */
    private function csv_file_dir()
    {
      $csv_file_path_array = wp_upload_dir();
      $csv_file_path = $csv_file_path_array['basedir'];
      $csv_file_dir = "{$csv_file_path}/mepr/csv";

      if(!is_dir($csv_file_dir)) // Make sure it exists
      {
        @mkdir($csv_file_dir, 0777, true);
      }

      return $csv_file_dir;
    }
  }

  global $mpimp;
  $mpimp = new MpimpAppController();

  // need to load this up in init so custom importers can be added like a beast
  function mpimp_load_app() {
    global $mpimp;
    $mpimp->importers = MpimpImporterFactory::available();
  }
  add_action('admin_init', 'mpimp_load_app', 100);

  require_once(WP_PLUGIN_DIR.'/memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPIMP_EDITION,
    MPIMP_PLUGIN_SLUG,
    'mpimp_license_key',
    __('MemberPress Importer', 'memberpress-importer'),
    __('Import MemberPress data from a csv file.', 'memberpress-importer')
  );
}
