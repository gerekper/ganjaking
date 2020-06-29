<?php
/**
 * Your Inspiration Themes Panel
 *
 * @author  Your Inspiration Themes
 * @version 0.1.0
 */

if ( ! class_exists ( 'YITH_Panel' ) ) {
    /*
     * The class manages the theme options for the Plugin
     */

    class YITH_Panel {

        /**
         * Parameters for add_submenu_page
         *
         *  add_submenu_page(
         *      'themes.php',        // The file name of a standard WordPress admin page
         *      'Theme Options',    // The text to be displayed in the title tags of the page when the menu is selected
         *      'Theme Options',    // The text to be used for the menu
         *      'administrator',    // The capability (or role) required for this menu to be displayed to the user.
         *      'theme-options',    // The slug name to refer to this menu by (should be unique for this menu).
         *      'theme_options_display_page' // The function to be called to output the content for this page.
         *  );
         *
         * @access protected
         */
        protected $_submenu = array ();

        /**
         * Initial Options definition:
         *   'tab' => array(
         *      'label',
         *      'sections' => array(
         *          'fields' => array(
         *             'option1',
         *             'option2',
         *              ...
         *          )
         *      )
         *   )
         *
         * @var array
         * @access public
         */
        public $options = array ();

        /**
         * Options group name
         *
         * @var string
         * @access public
         */
        public $option_group = 'panel_group';

        /**
         * Option name
         *
         * @var string
         * @access public
         */
        public $option_name = 'panel_options';

        /**
         * Banner links
         *
         * @var string
         * @access public
         */
        public $banner_url = 'https://yithemes.com/?ap_id=plugin';
        public $banner_img = '';

        /**
         * Constructor
         *
         * @param array $submenu Parameters for add_submenu_page
         * @param array $options Array of plugin options
         *
         */
        public function __construct ( $submenu, $options, $banner = array (), $option_group = false, $option_name = false ) {
            $this->_submenu = apply_filters ( 'yith_panel_submenu', $submenu );
            $this->options  = apply_filters ( 'yith_panel_options', $options );

            if ( ! empty( $banner ) ) {
                $this->banner_url = $banner[ 'url' ];
                $this->banner_img = $banner[ 'img' ];
            }

            if ( $option_group ) {
                $this->option_group = $option_group;
            }

            if ( $option_name ) {
                $this->option_name = $option_name;
            }

            //add new menu item
            //register new settings option group
            //include js and css files
            //print browser
            add_action ( 'admin_menu', array ( $this, 'add_submenu_page' ) );
            add_action ( 'admin_init', array ( $this, 'panel_register_setting' ) );
            add_action ( 'admin_enqueue_scripts', array ( $this, 'panel_enqueue' ) );

            // add the typography javascript vars
            add_action ( 'yith_panel_after_panel', array ( $this, 'js_typo_vars' ) );
        }

        /**
         * Create new submenu page
         *
         * @return void
         * @access public
         * @link   http://codex.wordpress.org/Function_Reference/add_submenu_page
         */
        public function add_submenu_page () {
            $submenu = $this->_submenu;
            add_submenu_page (
                $submenu[ 0 ],
                $submenu[ 1 ],
                $submenu[ 2 ],
                $submenu[ 3 ],
                $submenu[ 4 ],
                array ( $this, isset( $submenu[ 5 ] ) ? $submenu[ 5 ] : 'display_panel_page' )
            );
        }

        /**
         * Print the Panel page
         *
         * @return void
         * @access public
         */
        public function display_panel_page () {
            // Create a header in the default WordPress 'wrap' container
            $page = $this->_get_tab ();
            ?>
            <div id="icon-themes" class="icon32"><br/></div>
            <h2 class="nav-tab-wrapper">
                <?php foreach ( $this->options as $k => $tab ): ?>
                    <a class="nav-tab<?php if ( $page == $k ): ?> nav-tab-active<?php endif ?>"
                       href="<?php echo esc_url ( add_query_arg ( 'panel_page', $k ) ) ?>"><?php echo $tab[ 'label' ] ?></a>
                <?php endforeach ?>
                <?php do_action ( 'yith_panel_after_tabs' ); ?>
            </h2>

            <div class="wrap">
                <?php $this->printBanner () ?>
                <?php do_action ( 'yith_panel_before_panel' ); ?>
                <form action="options.php" method="post">

                    <?php do_settings_sections ( $this->option_name ); ?>
                    <?php settings_fields ( $this->option_group ) ?>

                    <p class="submit">
                        <input type="hidden" name="panel_page" value="<?php echo $page ?>"/>
                        <input class="button-primary" type="submit" name="save_options" value="Save Options"/>
                    </p>
                </form>
                <?php do_action ( 'yith_panel_after_panel' ); ?>
            </div>
            <?php
        }

        /**
         * Add the vars for the typography options
         */
        public function js_typo_vars () {
            global $yith_panel_if_typography;
            if ( ! isset( $yith_panel_if_typography ) || ! $yith_panel_if_typography ) return;

            $web_fonts = array (
                "Arial",
                "Arial Black",
                "Comic Sans MS",
                "Courier New",
                "Georgia",
                "Impact",
                "Lucida Console",
                "Lucida Sans Unicode",
                "Thaoma",
                "Trebuchet MS",
                "Verdana",
            );

            // http://niubbys.altervista.org/google_fonts.php
            $google_fonts = file_get_contents ( dirname ( __FILE__ ) . '/assets/js/google_fonts.json' );
            ?>
            <script type="text/javascript">
                var yit_google_fonts = '<?php echo $google_fonts ?>',
                    yit_web_fonts = '{"items":<?php echo json_encode ( $web_fonts ) ?>}',
                    yit_family_string = '';

            </script>
            <?php
        }

        /**
         * Register a new settings option group
         *
         * @return void
         * @access public
         * @link   http://codex.wordpress.org/Function_Reference/register_setting
         * @link   http://codex.wordpress.org/Function_Reference/add_settings_section
         * @link   http://codex.wordpress.org/Function_Reference/add_settings_field
         */
        public function panel_register_setting () {
            $page = $this->_get_tab ();
            $tab  = isset( $this->options[ $page ] ) ? $this->options[ $page ] : array ();

            if ( ! empty( $tab[ 'sections' ] ) ) {
                //add sections and fields
                foreach ( $tab[ 'sections' ] as $section_name => $section ) {
                    //add the section
                    add_settings_section (
                        $section_name,
                        $section[ 'title' ],
                        array ( $this, 'panel_section_content' ),
                        $this->option_name
                    );

                    //add the fields
                    foreach ( $section[ 'fields' ] as $option_name => $option ) {
                        $option[ 'id' ]        = $option_name;
                        $option[ 'label_for' ] = $option_name;

                        //register settings group
                        register_setting (
                            $this->option_group,
                            $option_name,
                            array ( $this, 'panel_sanitize' )
                        );

                        add_settings_field (
                            $option_name,
                            $option[ 'title' ],
                            array ( $this, 'panel_field_content' ),
                            $this->option_name,
                            $section_name,
                            $option
                        );
                    }
                }
            }
        }

        /**
         * Display sections content
         *
         * @return void
         * @access public
         */
        public function panel_section_content ( $section ) {
            $page = $this->_get_tab ();
            if ( isset( $this->options[ $page ][ 'sections' ][ $section[ 'id' ] ][ 'description' ] ) ) {
                echo "<p class='section-description'>" . $this->options[ $page ][ 'sections' ][ $section[ 'id' ] ][ 'description' ] . "</p>";
            }
        }

        /**
         * Sanitize the option's value
         *
         * @param array $input
         *
         * @return array
         * @access public
         */
        public function panel_sanitize ( $input ) {
            return apply_filters ( 'yith_panel_sanitize', $input );
        }

        /**
         * Get the active tab. If the page isn't provided, the function
         * will return the first tab name
         *
         * @return string
         * @access protected
         */
        public function _get_tab () {
            $panel_page = ! empty( $_REQUEST[ 'panel_page' ] ) ? sanitize_title_for_query ( $_REQUEST[ 'panel_page' ] ) : '';
            $tabs       = array_keys ( $this->options );

            return ! empty( $panel_page ) ? $panel_page : $tabs[ 0 ];
        }

        /**
         * Enqueue scripts and styles
         *
         * @return void
         * @access public
         */
        public function panel_enqueue ( $hook ) {
            global $pagenow;

            if ( $pagenow == $this->_submenu[ 0 ] && isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == $this->_submenu[ 4 ] ) {
                wp_enqueue_style ( 'wp-color-picker' );
                wp_enqueue_style ( 'jquery-ui', 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' );
                wp_enqueue_script ( 'jquery-ui-datepicker' );

                wp_enqueue_style ( 'yith-panel-css', plugin_dir_url ( __FILE__ ) . 'assets/css/yith-panel.css', array ( 'wp-color-picker' ), YITH_YWZM_VERSION );
                wp_enqueue_script ( 'yith-panel-js', plugin_dir_url ( __FILE__ ) . 'assets/js/yith-panel.js', array ( 'jquery', 'wp-color-picker' ), YITH_YWZM_VERSION, true );

                wp_enqueue_media ();

                do_action ( 'yith_panel_enqueue' );
            }
        }


        /**
         * Display field content
         *
         * @return void
         * @access public
         */
        public function panel_field_content ( $field ) {
            $value = get_option ( $field[ 'id' ], isset( $field[ 'std' ] ) ? $field[ 'std' ] : '' );
            $id    = $field[ 'id' ];
            $name  = $field[ 'id' ];

            $echo = '';

            switch ( $field[ 'type' ] ) {
                case 'text':
                    $echo = "<input type='text' id='{$id}' name='{$name}' value='{$value}' class='regular-text code' />";

                    if ( isset( $field[ 'description' ] ) && $field[ 'description' ] != '' ) {
                        $echo .= "<p class='description'>{$field['description']}</p>";
                    }
                    break;

                case 'textarea':
                    $echo = "<textarea name='{$name}' id='{$id}' class='large-text code' rows='10' cols='50'>{$value}</textarea>";
                    if ( isset( $field[ 'description' ] ) && $field[ 'description' ] != '' ) {
                        $echo .= "<p class='description'>{$field['description']}</p>";
                    }
                    break;

                case 'checkbox':
                    $echo = "<input type='checkbox' id='{$id}' name='{$name}' value='1' " . checked ( $value, true, false ) . " />";
                    if ( isset( $field[ 'description' ] ) && $field[ 'description' ] != '' ) {
                        $echo .= " <label for='{$id}'><span class='description'>{$field['description']}</span></label>";
                    }
                    break;

                case 'select':
                    $echo = "<select name='{$name}' id='{$id}'>";
                    foreach ( $field[ 'options' ] as $v => $label ) {
                        $echo .= "<option value='{$v}'" . selected ( $value, $v, false ) . ">{$label}</option>";
                    }
                    $echo .= "</select>";
                    if ( isset( $field[ 'description' ] ) && $field[ 'description' ] != '' ) {
                        $echo .= "<p class='description'>{$field['description']}</p>";
                    }
                    break;

                case 'skin':
                    $echo = "<select name='{$name}' id='{$id}' class='skin' data-path='{$field['path']}'>";
                    foreach ( $field[ 'options' ] as $v => $label ) {
                        $echo .= "<option value='{$v}'" . selected ( $value, $v, false ) . ">{$label}</option>";
                    }
                    $echo .= "</select>";
                    if ( isset( $field[ 'description' ] ) && $field[ 'description' ] != '' ) {
                        $echo .= "<p class='description'>{$field['description']}</p><div class='skin-preview'></div>";
                    }
                    break;

                case 'number':
                    $mms = '';
                    if ( isset( $field[ 'min' ] ) ) {
                        $mms .= " min='{$field['min']}'";
                    }

                    if ( isset( $field[ 'max' ] ) ) {
                        $mms .= " max='{$field['max']}'";
                    }

                    if ( isset( $field[ 'step' ] ) ) {
                        $mms .= " step='{$field['step']}'";
                    }

                    $echo = "<input type='number' id='{$id}' name='{$name}' value='{$value}' class='small-text' {$mms} />";
                    if ( isset( $field[ 'description' ] ) && $field[ 'description' ] != '' ) {
                        $echo .= "<p class='description'>{$field['description']}</p>";
                    }
                    break;

                case 'colorpicker':
                    $std = isset( $field[ 'std' ] ) ? $field[ 'std' ] : '';

                    $echo = "<input type='text' id='{$id}' name='{$name}' value='{$value}' class='medium-text code panel-colorpicker' data-default-color='{$std}' />";
                    if ( isset( $field[ 'description' ] ) && $field[ 'description' ] != '' ) {
                        $echo .= "<p class='description'>{$field['description']}</p>";
                    }
                    break;

                case 'datepicker':
                    $std   = isset( $field[ 'std' ] ) ? $field[ 'std' ] : array ( 'date' => '', 'hh' => 0, 'mm' => 0, 'ss' => 0 );
                    $value = ! empty( $value ) ? $value : array ( 'date' => '', 'hh' => 0, 'mm' => 0, 'ss' => 0 );

                    $echo = "<input type='text' id='{$id}_date' name='{$name}[date]' value='{$value['date']}' class='medium-text code panel-datepicker' colorpicker='" . esc_html__( 'Select a date', 'yith-woocommerce-zoom-magnifier' ) . "' /> - ";
                    $echo .= "<input type='text' id='{$id}_hh' name='{$name}[hh]' value='{$value['hh']}' class='small-text code' colorpicker='" . esc_html__( 'Hours', 'yith-woocommerce-zoom-magnifier' ) . "' /> : ";
                    $echo .= "<input type='text' id='{$id}_mm' name='{$name}[mm]' value='{$value['mm']}' class='small-text code' colorpicker='" . esc_html__( 'Minutes', 'yith-woocommerce-zoom-magnifier' ) . "' /> : ";
                    $echo .= "<input type='text' id='{$id}_ss' name='{$name}[ss]' value='{$value['ss']}' class='small-text code' colorpicker='" . esc_html__( 'Minutes', 'yith-woocommerce-zoom-magnifier' ) . "' />";
                    if ( isset( $field[ 'description' ] ) && $field[ 'description' ] != '' ) {
                        $echo .= "<p class='description'>{$field['description']}</p>";
                    }
                    break;

                case 'upload':
                    $echo = '<div class="uploader">';
                    $echo .= "  <input type='text' id='{$id}' name='{$name}' value='{$value}' class='regular-text code' /> <input type='button' name='' id='{$id}_button' class='button' value='" . esc_html__( 'Upload', 'yith-woocommerce-zoom-magnifier' ) . "'>";
                    $echo .= '</div>';
                    if ( isset( $field[ 'description' ] ) && $field[ 'description' ] != '' ) {
                        $echo .= "<p class='description'>{$field['description']}</p>";
                    }
                    break;

                case 'checkboxes':
                    $echo = '<div class="checkboxes">';
                    foreach ( $field[ 'options' ] as $check_value => $check_label ) {
                        $echo .= "<label><input type='checkbox' id='{$id}_{$check_value}' name='{$name}[]' value='$check_value' " . checked ( in_array ( $check_value, $value ), true, false ) . " /> {$check_label}</label><br />";
                    }

                    $echo .= " <p class='description'>{$field['description']}</p>";
                    break;

                case 'typography':
                    $value                    = wp_parse_args ( $value, $field[ 'std' ] ); ?>
                    <div class="typography_container typography">
                        <div class="option">
                            <!-- Size -->
                            <div class="spinner_container">
                                <input class="typography_size number small-text" type="number"
                                       name="<?php echo $name ?>[size]" id="<?php echo $id ?>-size"
                                       value="<?php echo $value[ 'size' ] ?>"
                                       data-min="<?php if ( isset( $field[ 'min' ] ) ) echo $field[ 'min' ] ?>"
                                       data-max="<?php if ( isset( $field[ 'max' ] ) ) echo $field[ 'max' ] ?>"/>
                            </div>

                            <!-- Unit -->
                            <div class="select-wrapper font-unit">
                                <select class="typography_unit" name="<?php echo $name ?>[unit]"
                                        id="<?php echo $id ?>-unit">
                                    <option
                                        value="px" <?php selected ( $value[ 'unit' ], 'px' ) ?>><?php esc_html_e( 'px', 'yith-woocommerce-zoom-magnifier' ) ?></option>
                                    <option
                                        value="em" <?php selected ( $value[ 'unit' ], 'em' ) ?>><?php esc_html_e( 'em', 'yith-woocommerce-zoom-magnifier' ) ?></option>
                                    <option
                                        value="pt" <?php selected ( $value[ 'unit' ], 'pt' ) ?>><?php esc_html_e( 'pt', 'yith-woocommerce-zoom-magnifier' ) ?></option>
                                    <option
                                        value="rem" <?php selected ( $value[ 'unit' ], 'rem' ) ?>><?php esc_html_e( 'rem', 'yith-woocommerce-zoom-magnifier' ) ?></option>
                                </select>
                            </div>

                            <!-- Family -->
                            <div class="select-wrapper font-family">
                                <select class="typography_family" name="<?php echo $name ?>[family]"
                                        id="<?php echo $id ?>-family" data-instance="false">
                                    <?php if ( $value[ 'family' ] ): ?>
                                        <option
                                            value="<?php echo stripslashes ( $value[ 'family' ] ) ?>"><?php echo $value[ 'family' ] ?></option>
                                    <?php else: ?>
                                        <option
                                            value=""><?php esc_html_e( 'Select a font family', 'yith-woocommerce-zoom-magnifier' ) ?></option>
                                    <?php endif ?>
                                </select>
                            </div>

                            <!-- Style -->
                            <div class="select-wrapper font-style">
                                <select class="typography_style" name="<?php echo $name ?>[style]"
                                        id="<?php echo $id ?>-style">
                                    <option
                                        value="regular" <?php selected ( $value[ 'style' ], 'regular' ) ?>><?php esc_html_e( 'Regular', 'yith-woocommerce-zoom-magnifier' ) ?></option>
                                    <option
                                        value="bold" <?php selected ( $value[ 'style' ], 'bold' ) ?>><?php esc_html_e( 'Bold', 'yith-woocommerce-zoom-magnifier' ) ?></option>
                                    <option
                                        value="extra-bold" <?php selected ( $value[ 'style' ], 'extra-bold' ) ?>><?php esc_html_e( 'Extra bold', 'yith-woocommerce-zoom-magnifier' ) ?></option>
                                    <option
                                        value="italic" <?php selected ( $value[ 'style' ], 'italic' ) ?>><?php esc_html_e( 'Italic', 'yith-woocommerce-zoom-magnifier' ) ?></option>
                                    <option
                                        value="bold-italic" <?php selected ( $value[ 'style' ], 'bold-italic' ) ?>><?php esc_html_e( 'Italic bold', 'yith-woocommerce-zoom-magnifier' ) ?></option>
                                </select>
                            </div>

                            <!-- Color -->
                            <input type='text' id='<?php echo $id ?>-color' name='<?php echo $name ?>[color]'
                                   value='<?php echo $value[ 'color' ] ?>'
                                   class='medium-text code panel-colorpicker typography_color'
                                   data-default-color='<?php echo $field[ 'std' ][ 'color' ] ?>'/>

                        </div>
                        <div class="clear"></div>
                        <div class="font-preview">
                            <p>The quick brown fox jumps over the lazy dog</p>
                            <!-- Refresh -->
                            <div class="refresh_container">
                                <button
                                    class="refresh"><?php esc_html_e( 'Click to preview', 'yith-woocommerce-zoom-magnifier' ) ?></button>
                            </div>
                        </div>
                    </div>
                    <?php
                    global $yith_panel_if_typography;
                    $yith_panel_if_typography = true;
                    break;

                default:
                    do_action ( 'yith_panel_field_' . $field[ 'type' ] );
                    break;
            }

            echo $echo;
        }

        /**
         * Print the banner
         *
         * @access protected
         * @return void
         */
        public function printBanner () {
            if ( ! $this->banner_url || ! $this->banner_img ) return;
            ?>
            <div class="yith_banner">
                <a href="<?php echo $this->banner_url ?>" target="_blank">
                    <img src="<?php echo $this->banner_img ?>" alt=""/>
                </a>
            </div>
            <?php
        }
    }
}