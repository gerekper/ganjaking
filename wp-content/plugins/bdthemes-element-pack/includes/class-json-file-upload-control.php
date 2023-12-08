<?php

namespace ElementPack\Includes;

use Elementor\Base_Data_Control;

if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly.
}

class ElementPack_JSON_File_Upload_Control extends Base_Data_Control {

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct();
        add_action('admin_enqueue_scripts', [$this, 'load_wp_media_files']);
        add_filter('upload_mimes', [$this, 'elementor_pack_allow_json_file_upload_mime_types'], 10, 1);
        add_filter('wp_check_filetype_and_ext', [$this, 'elementor_pack_allow_json_file_upload_ext'], 10, 4);

    }

    function load_wp_media_files() {
        wp_enqueue_media();
    }


    public function get_type() {
        return 'json-upload';
    }

    protected function get_default_settings() {
        return [
            'label'             => __('Upload JSON File', 'elementor-artbees-extension'),
            'description'       => '',
            'label_block'       => true,
            'show_label'        => true,
            'callback_selector' => ''
        ];
    }

    public function content_template() {

        $control_uid = $this->get_control_uid();

        ?>
        <div class="elementor-control-field">
            <label for="<?php echo $control_uid; ?>" class="elementor-control-title">{{ data.label }}</label>
            <div class="elementor-control-input-wrapper">
                <p class="ep-json-file-upload-notice" style="color:red"></p>
                <form action="" method="post" class="element-pack-json-file-upload-control">
                    <input type="button" data-callback-selector="elementor-control-{{ data.callback_selector }}"
                           name="upload-btn" class="element-pack-josn-file-upload-file" value="Upload Json">
                    <input id="<?php echo $control_uid; ?>" type="hidden" name="file-link"
                           class="elementor-control-tag-area element-pack-json-file-upload-control-hidden-field"
                           title="{{ data.title }}" data-setting="{{ data.name }}"/>
                </form>
            </div>
        </div>
        <div class="elementor-control-field-description">{{data.description}}</div>
        <?php
    }

    public function enqueue() {
        wp_register_script('element-pack-json-file-import-control', BDTEP_ASSETS_URL . 'js/controls/element-pack-json-file-upload-control.min.js');
        wp_enqueue_script('element-pack-json-file-import-control');
    }


    public function elementor_pack_allow_json_file_upload_ext($data, $file, $filename, $mimes) {

        if ( !empty($data['ext']) && !empty($data['type']) ) {
            return $data;
        }
        $filetype = wp_check_filetype($filename, $mimes);

        if ( 'json' === $filetype['ext'] ) {
            $data['ext']  = 'json';
            $data['type'] = 'application/json';
        }
        return $data;

    }

    public function elementor_pack_allow_json_file_upload_mime_types($mime_types = array()) {

        // The MIME types listed here will be allowed in the media library.
        // You can add as many MIME types as you want.
        $mime_types['json'] = 'application/json';
        return $mime_types;
    }

}




