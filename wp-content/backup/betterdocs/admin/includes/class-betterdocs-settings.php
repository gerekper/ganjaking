<?php 
/**
 * This class is responsible for all settings things happening in Betterdocs Plugin
 * 
 * @link       https://wpdeveloper.net
 * @since      1.0.0
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/admin
 */
class BetterDocs_Settings {
    public static function init(){
        add_action( 'betterdocs_settings_header', array( __CLASS__, 'header_template' ), 10 );
        add_action( 'wp_ajax_betterdocs_general_settings', array( __CLASS__, 'general_settings_ac' ), 10 );
    }
    
    /**
     * This function is responsible for settings page header
     *
     * @hooked betterdocs_settings_header
     * @return void
     */
    public static function header_template(){
        ?>
            <div class="betterdocs-settings-header">
                <div class="betterdocs-header-full">
                    <img src="<?php echo BETTERDOCS_ADMIN_URL ?>assets/img/betterdocs-icon.svg" alt="">
                    <h2 class="title"><?php _e( 'BetterDocs Settings', 'betterdocs' ); ?></h2>
                </div>
            </div>
        <?php
    }
    /**
	 * Get all settings fields
	 *
	 * @param array $settings
	 * @return array
	 */
	public static function get_settings_fields( $settings ){
        $new_fields = [];

        foreach( $settings as $setting ) {

            // if( isset( $setting['fields'] ) ) {

            // }

            $sections = isset( $setting['sections'] ) ? $setting['sections'] : [];
            if( ! empty( $sections ) ) {
                foreach( $sections as $section ) {
                    $fields = isset( $section['fields'] ) ? $section['fields'] : [];
                    if( empty( $fields ) ) {
                        $tabs = isset( $section['tabs'] ) ? $section['tabs'] : [];
                        if( ! empty( $tabs ) ) {
                            foreach( $tabs as $id => $tab ) {
                                $fields = isset( $tab['fields'] ) ? $tab['fields'] : [];
                                if( ! empty( $fields ) ) {
                                    foreach( $fields as $id => $field ) {
                                        $new_fields[ $id ] = $field;
                                    }
                                }
                            }
                        }
                    } else {
                        if( ! empty( $fields ) ) {
                            foreach( $fields as $id => $field ) {
                                $new_fields[ $id ] = $field;
                            }
                        }
                    }
                }
            }
        }

        return apply_filters( 'betterdocs_settings_fields', $new_fields );
	}
	/**
	 * Get the whole settings array
	 *
	 * @return void
	 */
	public static function settings_args(){
        if( ! function_exists( 'betterdocs_settings_args' ) ) {
            require BETTERDOCS_ADMIN_DIR_PATH . 'includes/betterdocs-settings-page-helper.php';
        }
        do_action( 'betterdocs_before_settings_load' );
        return betterdocs_settings_args();
	}
	/**
     * Render the settings page
	 *
     * @return void
	 */
    public static function settings_page(){
        $settings_args = self::settings_args();
        $value = BetterDocs_DB::get_settings();

		include_once BETTERDOCS_ADMIN_DIR_PATH . 'partials/betterdocs-settings-display.php';
	}
    /**
     * This function is responsible for render settings field
     *
     * @param string $key
     * @param array $field
     * @return void
     */
    public static function render_field( $key = '', $field = [] ) {
        $post_id   = '';
        $name      = $key;
        $id        = BetterDocs_Metabox::get_row_id( $key );
        $file_name = isset( $field['type'] ) ? $field['type'] : 'text';
        
        if( 'template' === $file_name ) {
            $default = isset( $field['defaults'] ) ? $field['defaults'] : [];
        } else {
            $default = isset( $field['default'] ) ? $field['default'] : '';
        }

        $saved_value = BetterDocs_DB::get_settings( $name );

        if( ! empty( $saved_value ) ) {
            $value = $saved_value;
        } else {
            $value = $default;
        }
        
        $class  = 'betterdocs-settings-field';
        $row_class = BetterDocs_Metabox::get_row_class( $file_name );

        $attrs = '';

        if( isset( $field['toggle'] ) && in_array( $file_name, array( 'checkbox', 'select', 'toggle', 'theme' ) ) ) {
            $attrs .= ' data-toggle="' . esc_attr( json_encode( $field['toggle'] ) ) . '"';
        }

        if( isset( $field['hide'] ) && $file_name == 'select' ) {
            $attrs .= ' data-hide="' . esc_attr( json_encode( $field['hide'] ) ) . '"';
        }

        $field_id = $name;

        include BETTERDOCS_ADMIN_DIR_PATH . 'partials/betterdocs-field-display.php';
    }
    public static function save_default_settings(){
		$settings_args = self::settings_args();
        $fields = self::get_settings_fields( $settings_args );
        $data = [];
        $saved_settings = BetterDocs_DB::get_settings();
        if( ! empty( $saved_settings ) ) {
            return false;
        }
        if( ! empty( $fields ) ) {
            foreach( $fields as $name => $posted_field ) {
                $data[ $name ] = isset( $posted_field['default'] ) ? $posted_field['default'] : 0;
            }
            return BetterDocs_DB::update_settings( $data );
        }
        return false;
    }
    /**
     * This function is responsible for 
     * save all settings data, including checking the disable field to prevent
     * users manipulation.
     *
     * @param array $values
     * @return void
     */
    public static function save_settings( $posted_fields = [] ){
		$settings_args = self::settings_args();
        $fields = self::get_settings_fields( $settings_args );
        $data = [];
        if( ! empty( $posted_fields ) ) {
            $new_posted_fields = [];
            foreach( $posted_fields as $posted_field ) {
                preg_match("/(.*)\[(.*)\]/", $posted_field['name'], $matches);
                if( ! empty( $matches ) ) {
                    $name = $matches[1];
                    $sub_name = $matches[2];
                    if( ! empty( $sub_name ) ) {
                        $new_posted_fields[ $name ][ $sub_name ] = $posted_field['value'];
                    } else {
                        $new_posted_fields[ $name ][] = $posted_field['value'];
                    }
                } else {
                    $new_posted_fields[ $posted_field['name'] ] = $posted_field['value'];
                }
            }
        }
        $fields_keys = array_fill_keys( array_keys( $fields ), 'off' );

		foreach( $new_posted_fields as $key => $new_posted_field ) {
			if( array_key_exists( $key, $fields ) ) {
                unset( $fields_keys[ $key ] );
                if( empty( $new_posted_field ) ) {
					$posted_value = isset( $fields[ $key ]['default'] ) ? $fields[ $key ]['default'] : '';
                }
                if( isset( $fields[ $key ]['disable'] ) && $fields[ $key ]['disable'] === true ) {
                    $posted_value = isset( $fields[ $key ]['default'] ) ? $fields[ $key ]['default'] : '';
                }
                $posted_value = BetterDocs_Helper::sanitize_field( $fields[ $key ], $new_posted_field );
                if( isset( $data[ $key ] ) ) {
                    if( is_array( $data[ $key ] ) ) {
                        $data[ $key ][] = $posted_value;
                    } else {
                        $data[ $key ] = array( $posted_value, $data[ $key ] );
                    }
                } else {
                    $data[ $key ] = $posted_value;
                }
            }
        }

        $data = array_merge( $fields_keys, $data );
		BetterDocs_DB::update_settings( $data );
    }
    
    public static function general_settings_ac(){
        /**
         * Verify the Nonce
         */
        if ( ( ! isset( $_POST['nonce'] ) && ! isset( $_POST['key'] ) ) || ! 
        wp_verify_nonce( $_POST['nonce'], 'betterdocs_'. $_POST['key'] .'_nonce' ) ) {
            return;
        }

        if( isset( $_POST['form_data'] ) ) {
            self::save_settings( $_POST['form_data'] );
            wp_send_json_success("success");
        } else {
            wp_send_json_error("error");
        }

        die;
    }
    /**
     * Get All Roles
     * dynamically
     * @return array
     */
    public static function get_roles(){
        $roles = wp_roles()->role_names;
        unset( $roles['subscriber'] );
        return $roles;
    }
}