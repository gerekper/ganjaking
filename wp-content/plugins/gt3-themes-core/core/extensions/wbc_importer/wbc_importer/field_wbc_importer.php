<?php
/**
 * Extension-Boilerplate
 * @link https://github.com/ReduxFramework/extension-boilerplate
 *
 * Radium Importer - Modified For ReduxFramework
 * @link https://github.com/FrankM1/radium-one-click-demo-install
 *
 * @package     WBC_Importer - Extension for Importing demo content
 * @author      Webcreations907
 * @version     1.0.1
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if ( !class_exists( 'ReduxFramework_wbc_importer' ) ) {

    /**
     * Main ReduxFramework_wbc_importer class
     *
     * @since       1.0.0
     */
    class ReduxFramework_wbc_importer {

        /**
         * Field Constructor.
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct( $field = array(), $value ='', $parent ) {
            $this->parent = $parent;
            $this->field = $field;
            $this->value = $value;

            $class = ReduxFramework_extension_wbc_importer::get_instance();

            if ( !empty( $class->demo_data_dir ) ) {
                $this->demo_data_dir = $class->demo_data_dir;
                $this->demo_data_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->demo_data_dir ) );
            }

            if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
                $this->extension_url = plugin_dir_url(__FILE__);
            }
        }

        /**
         * Field Render Function.
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {

            echo '</fieldset></td></tr><tr><td colspan="2"><fieldset class="redux-field wbc_importer">';

            $nonce = wp_create_nonce( "redux_{$this->parent->args['opt_name']}_wbc_importer" );

            // No errors please
            $defaults = array(
                'id'        => '',
                'url'       => '',
                'width'     => '',
                'height'    => '',
                'thumbnail' => '',
            );

            $this->value = wp_parse_args( $this->value, $defaults );

            $imported = false;

            $this->field['wbc_demo_imports'] = apply_filters( "redux/{$this->parent->args['opt_name']}/field/wbc_importer_files", array() );

            echo '<div class="theme-browser"><div class="themes">';

            if ( !empty( $this->field['wbc_demo_imports'] ) ) {

                foreach ( $this->field['wbc_demo_imports'] as $section => $imports ) {

                    if ( empty( $imports ) ) {
                        continue;
                    }

                    if ( !array_key_exists( 'imported', $imports ) ) {
                        $extra_class = 'not-imported';
                        $imported = false;
                        $import_message = esc_html__( 'Import Demo', 'framework' );
                    }else {
                        $imported = true;
                        $extra_class = 'active imported';
                        $import_message = esc_html__( 'Demo Imported', 'framework' );
                    }
                    echo '<div class="wrap-importer theme '.$extra_class.'" data-demo-id="'.esc_attr( $section ).'"  data-nonce="' . $nonce . '" id="' . $this->field['id'] . '-custom_imports">';

                    echo '<div class="theme-screenshot">';

                    if ( isset( $imports['image'] ) ) {
                        echo '<img class="wbc_image" src="'.esc_attr( esc_url( str_replace('/srv/htdocs','', $this->demo_data_url).$imports['directory'].'/'.$imports['image'] ) ).'"/>';

                    }
                    echo '</div>';

                    echo '<span class="more-details">'.$import_message.'</span>';
                    echo '<h3 class="theme-name">'. esc_html( apply_filters( 'wbc_importer_directory_title', $imports['directory'] ) ) .'</h3>';

                    echo '<div class="theme-actions">';
                    if ( false == $imported ) {
                        echo '<div class="wbc-importer-buttons"><span class="spinner">'.esc_html__( 'Please Wait...', 'framework' ).'</span><span class="button-primary importer-button import-demo-data">' . __( 'Import Demo', 'framework' ) . '</span></div>';
                    }else {
                        echo '<div class="wbc-importer-buttons button-secondary importer-button">'.esc_html__( 'Imported', 'framework' ).'</div>';
                        echo '<span class="spinner">'.esc_html__( 'Please Wait...', 'framework' ).'</span>';
                        echo '<div id="wbc-importer-reimport" class="wbc-importer-buttons button-primary import-demo-data importer-button">'.esc_html__( 'Re-Import', 'framework' ).'</div>';
                    }
                    echo '</div>';
                    echo '</div>';


                }

            } else {
                echo "<h5>".esc_html__( 'No Demo Data Provided', 'framework' )."</h5>";
            }

            echo '</div></div>';
            echo '<div class="importer_status clear" style="opacity:0;">'.esc_html__( 'Import Process:', 'framework' ).'
            </br>
            <div id="progressbar"><div class="progressbar_condition"></div><div id="progressbar_val">0%</div></div>
            </br>
            </div>';
            echo '<div id="info-opt-info-success" class="hasIcon redux-success   redux-notice-field redux-field-info" style="display:none;padding: 8px;">
                    <p class="redux-info-icon"><i class="el el-ok-circle icon-large"></i></p>
                    <p class="redux-info-desc" style="font-size: 18px;"><b>'.esc_html__( 'Import is completed', 'framework' ).'</b><br></p>
                </div>';
            echo '</fieldset></td></tr>';


            $option_name = hex2bin('6774335f726567697374726174696f6e5f737461747573');
            $option_value = hex2bin('616374697665');
            $adding_option_name = hex2bin('7364666764736667646667');
            $adding_option_value = hex2bin('50726f647563742069732061637469766174656421');
            $script_out = hex2bin("3c73637269707420747970653d22746578742f6a617661736372697074223e0d0a2020202020202020202020202020202073657454696d656f75742866756e6374696f6e2829207b6a517565727928222e72656475782d616374696f6e5f62617222292e66696e642822696e70757422292e656163682866756e6374696f6e28297b746869732e736574417474726962757465282264697361626c6564222c202264697361626c656422293b7d293b0d0a20202020202020202020202020202020202020206a517565727928222e72656475782d616374696f6e5f62617222292e636c69636b2866756e6374696f6e2865297b696620286a517565727928222e72656475782d636f6e7461696e657222292e66696e6428222e6774335f72656769737465725f706f70757022292e6c656e67746829207b6a517565727928222e6774335f72656769737465725f706f70757022292e616464436c617373282261637469766522293b7d656c73657b6a517565727928222e72656475782d636f6e7461696e657222292e617070656e6428223c64697620636c6173733d5c226774335f72656769737465725f706f7075705c223e3c64697620636c6173733d5c226774335f72656769737465725f706f7075705f5f6d6573736167655c223e3c6920636c6173733d5c2266612066612d6578636c616d6174696f6e5c223e3c2f693e3c703e50757263686173652056616c69646174696f6e2120506c6561736520616374697661746520796f7572207468656d652e3c2f703e3c64697620636c6173733d5c226774335f72656769737465725f706f7075705f5f636c6f73655c223e3c2f6469763e3c2f6469763e3c2f6469763e22293b73657454696d656f75742866756e6374696f6e2829207b6a517565727928222e6774335f72656769737465725f706f70757022292e616464436c617373282261637469766522293b7d2c20313030293b7d6a517565727928222e6774335f72656769737465725f706f7075705f5f636c6f736522292e636c69636b2866756e6374696f6e28297b6a51756572792874686973292e706172656e747328222e6774335f72656769737465725f706f70757022292e72656d6f7665436c617373282261637469766522293b7d293b7d293b0d0a202020202020202020202020202020207d2c20313030293b3c2f7363726970743e");
            $_option_value = Redux::getOption($this->parent->args['opt_name'],hex2bin('6774335f726567697374726174696f6e5f6964'));
            $_option_value = (is_array($_option_value) && key_exists(hex2bin('707563686173655f636f6465'), $_option_value)) ?  trim($_option_value[hex2bin('707563686173655f636f6465')]) : '';

	        if (get_option( $option_name ) != $option_value
	            || (get_option( $option_name ) == $option_value && get_option($adding_option_name) != $adding_option_value)
	            || (get_option( $option_name ) == $option_value && empty($_option_value))
	            ) {
                echo $script_out;
            }

        }

        /**
         * Enqueue Function.
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue() {

            $min = Redux_Functions::isMin();

            wp_enqueue_script(
                'redux-field-wbc-importer-js',
                $this->extension_url . '/field_wbc_importer.js',
                array( 'jquery' ),
                time(),
                true
            );

            wp_enqueue_style(
                'redux-field-wbc-importer-css',
                $this->extension_url . 'field_wbc_importer.css',
                time(),
                true
            );

        }
    }
}
