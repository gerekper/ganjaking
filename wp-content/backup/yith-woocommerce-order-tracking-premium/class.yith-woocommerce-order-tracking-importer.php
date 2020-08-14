<?php
/**
 * YITH WooCommerce Order Tracking CSV Importer class
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('YITH_WooCommerce_Order_Tracking_Importer')) {
    /**
     * YITH WooCommerce Order Tracking CSV Importer
     *
     * @since 1.0.0
     */
    class YITH_WooCommerce_Order_Tracking_Importer extends WP_Importer
    {

        /**
         * Importer id
         *
         * @var int
         * @since 1.0.0
         */
        public $id;

        /**
         * CSV file to import
         *
         * @var string
         * @since 1.0.0
         */
        public $file_url;

        /**
         * Importer page
         *
         * @var string
         * @since 1.0.0
         */
        public $import_page;

        /**
         * CSV delimiter
         *
         * @var string
         * @since 1.0.0
         */
        public $delimiter;

        /**
         * Single instance of the class
         *
         * @var \YITH_WooCommerce_Order_Tracking_Importer
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Constructor
         *
         * @return \YITH_WooCommerce_Order_Tracking_Importer
         * @since 1.0.0
         */
        public function __construct()
        {
            $this->import_page = 'ywot_import_tracking_codes';
            $this->delimiter = empty($_POST['delimiter']) ? ',' : wc_clean($_POST['delimiter']);

            /*
            /** @var Yith_WooCommerce_Order_Tracking_Premium $YWOT_Instance */
            global $YWOT_Instance;
            $YWOT_Instance->register_email_order_tracking_actions();
        }

        /**
         * Registered callback function for the WordPress Importer; manages the three separate stages of the CSV import process
         *
         * @return void
         * @since 1.0.0
         */
        public function dispatch()
        {

            $this->header();

            $step = empty($_GET['step']) ? 0 : (int)$_GET['step'];

            switch ($step) {

                case 0:
                    $this->greet();
                    break;

                case 1:
                    check_admin_referer('import-upload');

                    if ($this->handle_upload()) {

                        if ($this->id) {
                            $file = get_attached_file($this->id);
                        } else {
                            $file = ABSPATH . $this->file_url;
                        }

                        add_filter('http_request_timeout', array($this, 'bump_request_timeout'));

                        $this->import($file);
                    }
                    break;
            }

            $this->footer();
        }

        /**
         * format_data_from_csv function.
         *
         * @param mixed $data
         * @param string $enc
         *
         * @return string
         * @since 1.0.0
         */
        public function format_data_from_csv($data, $enc)
        {
            return ($enc == 'UTF-8') ? $data : utf8_encode($data);
        }

        /**
         * Import terms from CSV
         *
         * @param mixed $file
         *
         * @return void
         * @since 1.0.0
         */
        public function import($file)
        {
            if (!is_file($file)) {
                $this->import_error(esc_html__('The file does not exist, please try again.', 'yith-woocommerce-order-tracking'));
            }

            $this->import_start();

            $imported_codes = 0;
            $row_index = 0;
                $log_message = '';

            if (($handle = fopen($file, "r")) !== false) {

                $header = fgetcsv($handle, 0, $this->delimiter);

                $carriers = Carriers::get_instance()->get_carrier_list();

                if (5 === sizeof($header)) {

                    while (($row = fgetcsv($handle, 0, $this->delimiter)) !== false) {
                        $row_index++;
                        list($order_id, $carrier_code, $tracking_code, $shipping_date, $shipped_status) = $row;

                        $order_id = apply_filters('yith_ywot_import_order_id', $order_id );

                        //  Check if the $order_id belong to a valid order
                        $order = wc_get_order($order_id);
                        if (!$order) {
                            $log_message .= sprintf(esc_html__('Failed to import row %s. "%s" is not a valid order id.', 'yith-woocommerce-order-tracking'), $row_index, $order_id) . '<br />';
                            continue;
                        }

                        //  Check if the carrier code belong to a known carrier...
                        if (!isset($carriers[strtoupper($carrier_code)])) {
                            $log_message .= sprintf(esc_html__('Failed to import row %s: %s is not a known carrier code.', 'yith-woocommerce-order-tracking'), $row_index, $carrier_code) . '<br />';
                            continue;
                        }

                        $shipped_status = ('1' == $shipped_status) ? 'on' : '';

						$track = YITH_Tracking_Data::get($order);
						
						$track->set(array(
                            'ywot_carrier_id' => strtoupper($carrier_code),
                            'ywot_tracking_code' => $tracking_code,
                            'ywot_pick_up_date' => $shipping_date,
                            'ywot_picked_up' => $shipped_status,
                            ));
                            
                        $track->save();

                            
                        $imported_codes++;

                        //  Update the status of the order, according to the plugin option
                        $complete_order = "yes" == get_option ( 'ywot_set_completed_status', 'no' );
                        $update_status = $complete_order && $shipped_status;


                        if ($update_status) {
                            $order->update_status('completed', esc_html__('Completed order tracking details imported from CSV file', 'yith-woocommerce-order-tracking'));
                        }
                    }

                } else {
                    $this->import_error(esc_html__('The CSV file is invalid.', 'yith-woocommerce-order-tracking'));
                }

                fclose($handle);
            }

            // Show Result
            echo '<div class="updated settings-error below-h2"><p>' . sprintf(esc_html__('Operation completed - imported <strong>%s/%s</strong> valid tracking codes.', 'yith-woocommerce-order-tracking'), $imported_codes, $row_index) . '</p></div>';

            echo $log_message;
            $this->import_end();
        }

        /**
         * Attempt to create a new attachment from csv url
         *
         * @param string $url URL to fetch attachment from
         * @param string $base_url External site base url
         *
         * @return int|WP_Error Post ID on success, WP_Error otherwise
         * @since 1.0.0
         */
        public function process_attachment($url, $base_url)
        {
            $post = array();

            // if the URL is absolute, but does not contain address, then upload it assuming base_site_url
            if (preg_match('|^/[\w\W]+$|', $url)) {
                $url = rtrim($base_url, '/') . $url;
            }

            $upload = $this->fetch_remote_file($url);
            if (is_wp_error($upload)) {
                return $upload;
            }

            if ($info = wp_check_filetype($upload['file'])) {
                $post['post_mime_type'] = $info['type'];
            } else {
                return new WP_Error('attachment_processing_error', esc_html__('Invalid file type', 'wordpress-importer'));
            }

            $post['guid'] = $upload['url'];

            // as per wp-admin/includes/upload.php
            $post_id = wp_insert_attachment($post, $upload['file']);
            wp_update_attachment_metadata($post_id, wp_generate_attachment_metadata($post_id, $upload['file']));

            // remap resized image URLs, works by stripping the extension and remapping the URL stub.
            if (preg_match('!^image/!', $info['type'])) {
                $parts = pathinfo($url);
                $name = basename($parts['basename'], ".{$parts['extension']}"); // PATHINFO_FILENAME in PHP 5.2

                $parts_new = pathinfo($upload['url']);
                $name_new = basename($parts_new['basename'], ".{$parts_new['extension']}");

                $this->url_remap[$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
            }

            return $post_id;
        }

        /**
         * Attempt to download a remote file attachment
         *
         * @param string $url URL of item to fetch
         *
         * @return array|WP_Error Local file location details on success, WP_Error otherwise
         * @since 1.0.0
         */
        public function fetch_remote_file($url)
        {
            // extract the file name and extension from the url
            $file_name = basename($url);

            // get placeholder file in the upload dir with a unique, sanitized filename
            $upload = wp_upload_bits($file_name, 0, '');

            if ($upload['error']) {
                return new WP_Error('upload_dir_error', $upload['error']);
            }

            // fetch the remote url and write it to the placeholder file
            $headers = wp_get_http($url, $upload['file']);

            // request failed
            if (!$headers) {
                @unlink($upload['file']);

                return new WP_Error('import_file_error', esc_html__('Remote server did not respond', 'wordpress-importer'));
            }

            // make sure the fetch was successful
            if ($headers['response'] != '200') {
                @unlink($upload['file']);

                return new WP_Error('import_file_error', sprintf(esc_html__('Remote server returned error response %1$d %2$s', 'wordpress-importer'), esc_html($headers['response']), get_status_header_desc($headers['response'])));
            }

            $filesize = filesize($upload['file']);

            if (isset($headers['content-length']) && $filesize != $headers['content-length']) {
                @unlink($upload['file']);

                return new WP_Error('import_file_error', esc_html__('The size of the remote file is not valid', 'wordpress-importer'));
            }

            if (0 == $filesize) {
                @unlink($upload['file']);

                return new WP_Error('import_file_error', esc_html__('Zero size file downloaded', 'wordpress-importer'));
            }

            $max_size = (int)$this->max_attachment_size();
            if (!empty($max_size) && $filesize > $max_size) {
                @unlink($upload['file']);

                return new WP_Error('import_file_error', sprintf(esc_html__('Remote file is too large, limit is %s', 'wordpress-importer'), size_format($max_size)));
            }

            // keep track of the old and new urls so we can substitute them later
            $this->url_remap[$url] = $upload['url'];
            // keep track of the destination if the remote url is redirected somewhere else
            if (isset($headers['x-final-location']) && $headers['x-final-location'] != $url) {
                $this->url_remap[$headers['x-final-location']] = $upload['url'];
            }

            return $upload;
        }

        /**
         * Decide what the maximum file size for downloaded attachments is.
         * Default is 0 (unlimited), can be filtered via yith_ywot_import_attachment_size_limit
         *
         * @return int Maximum attachment file size to import
         * @since 1.0.0
         */
        public function max_attachment_size()
        {
            return apply_filters('yith_ywot_import_attachment_size_limit', 0);
        }

        /**
         * Performs post-import cleanup of files and the cache
         *
         * @return void
         * @since 1.0.0
         */
        public function import_end()
        {
            echo '<p>' . esc_html__('All done!', 'yith-woocommerce-order-tracking') . '</p>';

            do_action('import_end');
        }

        /**
         * Handles the CSV upload and initial parsing of the file to prepare for displaying author import options
         *
         * @return bool False if error uploading or invalid file, true otherwise
         * @since 1.0.0
         */
        public function handle_upload()
        {
            if (empty($_POST['file_url'])) {

                $file = wp_import_handle_upload();

                if (isset($file['error'])) {
                    $this->import_error($file['error']);
                }

                $this->id = absint($file['id']);

            } elseif (file_exists(ABSPATH . $_POST['file_url'])) {
                $this->file_url = esc_attr($_POST['file_url']);
            } else {
                $this->import_error();
            }

            return true;
        }

        /**
         * Print import page header
         *
         * @return void
         * @since 1.0.0
         */
        public function header()
        {
            echo '<div class="wrap"><div class="icon32 icon32-woocommerce-importer" id="icon-woocommerce"><br></div>';
            echo '<h2>' . esc_html__('YITH Order Tracking Importer', 'yith-woocommerce-order-tracking') . '</h2>';
        }

        /**
         * Print import page footer
         *
         * @return void
         * @since 1.0.0
         */
        public function footer()
        {
            echo '</div>';
        }

        /**
         * Print first step of import procedure
         *
         * @return void
         * @since 1.0.0
         */
        public function greet()
        {
            ?>
            <div class="narrow">
            <p><?php _e('Hi there! Upload a CSV file containing order\'s tracking codes to import the contents into your shop. Choose a .csv file to upload, then click "Upload file and import".', 'yith-woocommerce-order-tracking'); ?>
            </p>
            <p><?php _e('Please look at this. It is a sample of how the CSV file should look like.', 'yith-woocommerce-order-tracking'); ?>
                <a
                    href="<?php echo YITH_YWOT_ASSETS_URL . '/example.csv'; ?>"><?php _e('Click here to download a sample.', 'yith-woocommerce-order-tracking'); ?></a>
            </p>
            <p>
                <b><?php _e('Important note.', 'yith-woocommerce-order-tracking'); ?></b>
                <span><?php _e('The carrier\'s code should match one of the carrier codes supported. Before importing data, please, ensure that the carrier code in the CSV file matches one of the supported carriers. See the table below for further details on carrier codes.', 'yith-woocommerce-order-tracking'); ?></span>
            </p>
            <?php

            $action = 'admin.php?import=ywot_import_tracking_codes&step=1';

            $bytes = apply_filters('import_upload_size_limit', wp_max_upload_size());
            $size = size_format($bytes);
            $upload_dir = wp_upload_dir();

            $carriers = Carriers::get_instance()->get_carrier_list();

            if (!empty($upload_dir['error'])) :
                ?>
                <div class="error">
                <p><?php _e('Before uploading your import file, you need to fix the following error:', 'yith-woocommerce-order-tracking'); ?></p>

                <p><strong><?php echo $upload_dir['error']; ?></strong></p></div><?php
            else :
                ?>
                <form enctype="multipart/form-data" id="import-upload-form" method="post"
                      action="<?php echo esc_attr(wp_nonce_url($action, 'import-upload')); ?>">
                    <table class="form-table">
                        <tbody>
                        <tr>
                            <th>
                                <label
                                    for="upload"><?php _e('Choose a file from your computer:', 'yith-woocommerce-order-tracking'); ?></label>
                            </th>
                            <td>
                                <input type="file" id="upload" name="import" size="25"/>
                                <input type="hidden" name="action" value="save"/>
                                <input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>"/>
                                <small><?php printf(esc_html__('Maximum size: %s', 'yith-woocommerce-order-tracking'), $size); ?></small>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label
                                    for="file_url"><?php _e(' OR enter path to file:', 'yith-woocommerce-order-tracking'); ?></label>
                            </th>
                            <td>
                                <?php echo ' ' . ABSPATH . ' '; ?>
                                <input type="text" id="file_url" name="file_url" size="25"/>
                            </td>
                        </tr>
                        <tr>
                            <th><label><?php _e('Delimiter', 'yith-woocommerce-order-tracking'); ?></label><br/></th>
                            <td><input type="text" name="delimiter" placeholder="," size="2"/></td>
                        </tr>
                        </tbody>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button"
                               value="<?php esc_attr_e('Upload file to import', 'yith-woocommerce-order-tracking'); ?>"/>
                    </p>
                </form>

                <table class="widefat">
                    <thead>
                    <tr>
                        <th><?php _e('Carrier code', 'yith-woocommerce-order-tracking'); ?></th>
                        <th><?php _e('Carrier name', 'yith-woocommerce-order-tracking'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($carriers as $key => $value): ?>
                        <tr>
                            <td><?php echo $key; ?></td>
                            <td><?php echo $value['name']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            <?php
            endif;

            echo '</div>';
        }

        /**
         * Added to http_request_timeout filter to force timeout at 60 seconds during import
         *
         * @param  int $val
         *
         * @return int 60
         * @since 1.0.0
         */
        public function bump_request_timeout($val)
        {
            return 60;
        }

        /**
         * Show import error and quit
         *
         * @param  string $message
         *
         * @return void
         * @since 1.0.0
         */
        private function import_error($message = '')
        {
            echo '<p><strong>' . esc_html__('Sorry, an error has occurred. ', 'yith-woocommerce-order-tracking') . ' </strong ><br />';
            if ($message) {
                echo esc_html($message);
            }
            echo '</p>';
            $this->footer();
            die();
        }

        /**
         * Start import
         *
         * @return void
         * @since 1.0.0
         */
        private function import_start()
        {
            if (function_exists('gc_enable')) {
                gc_enable();
            }
            @set_time_limit(0);
            @ob_flush();
            @flush();
            @ini_set('auto_detect_line_endings', '1');
        }

        /**
         * Returns single instance of the class
         *
         * @since 1.0.0
         */
        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self;
            }

            return self::$instance;
        }
    }
}

/**
 * Unique access to instance of YITH_WooCommerce_Order_Tracking_Importer class
 *
 * @return \YITH_WooCommerce_Order_Tracking_Importer
 * @since 1.0.0
 */
function YWOT_Importer()
{
    return YITH_WooCommerce_Order_Tracking_Importer::get_instance();
}