<?php
/**
 * Extension-Boilerplate
 *
 * @link https://github.com/ReduxFramework/extension-boilerplate
 *
 * GT3 Header Builder - Modified For ReduxFramework
 *
 * @package     GT3 Header Builder - Extension for building header
 * @author      gt3themes
 * @version     1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'ReduxFramework_gt3_section' ) ) {

    /**
     * Main ReduxFramework_custom_field class
     *
     * @since       1.0.0
     */
    class ReduxFramework_gt3_section extends ReduxFramework {

        /**
         * Field Constructor.
         *
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct( $field = array(), $value ='', $parent ) {


            $this->parent = $parent;
            $this->field  = $field;
            $this->value  = $value;

            if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
                $this->extension_url = plugin_dir_url(__FILE__);
            }

        }

        /**
         * Field Render Function.
         *
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {

                // No errors please
                $defaults    = array(
                    'indent'   => '',
                    'style'    => '',
                    'class'    => '',
                    'title'    => '',
                    'subtitle' => '',
                    'section_role' => ''
                );
                $this->field = wp_parse_args( $this->field, $defaults );

                $guid = uniqid();

                $add_class = '';
                if ( isset( $this->field['indent'] ) &&  true === $this->field['indent'] ) {
                    $add_class = ' form-table-section-indented';
                } elseif( !isset( $this->field['indent'] ) || ( isset( $this->field['indent'] ) && false !== $this->field['indent'] ) ) {
                    $add_class = " hide";
                }

                echo '<input type="hidden" id="' . esc_attr($this->field['id']) . '-marker"></td></tr></table><!-- end table -->';

                if ('end' == $this->field['section_role']) {
                    echo "</div>";
                    echo "</div><!-- end gt3_section_container -->";
                    echo "</div><!-- end redux section field -->";
                }

                if ('start' == $this->field['section_role']) {
                    echo "<div class='gt3_section_container' id='".esc_attr($this->field['id'])."'>";
                    echo "<div class='gt3_section_container__cover'></div>";
                    echo '<div id="section-' . esc_attr($this->field['id']) . '" class="redux-section-field redux-field ' . esc_attr($this->field['style']) . ' ' . esc_attr($this->field['class']) . ' ">';

                    // header start
                    echo "<div class='gt3_section__header'>";
                    if ( ! empty( $this->field['title'] ) ) {
                        echo '<h3>' . esc_html($this->field['title']) . '</h3>';
                    }
                    if ('start' == $this->field['section_role']) {
                        echo "<div class='gt3_section__close-icon'><i class='fa fa-times' aria-hidden='true'></i></div>";
                    }

                    if ( ! empty( $this->field['subtitle'] ) ) {
                        echo '<div class="redux-section-desc">' . esc_html($this->field['subtitle']) . '</div>';
                    }
                    echo "</div>";
                    // header end
                    echo '<div class="gt3_section__content">';
                }


                echo '<!--</div><div class="gt3_section__content">--><table id="section-table-' . esc_attr($this->field['id']) . '" data-id="' . esc_attr($this->field['id']) . '" class="form-table form-table-section no-border' . esc_attr($add_class) . '"><tbody><tr><th></th><td id="' . esc_attr($guid) . '">';

                // delete the tr afterwards
                ?>
                <script>
                    jQuery( document ).ready(
                        function() {
                            jQuery( '#<?php echo esc_js($this->field['id']); ?>-marker' ).parents( 'tr:first' ).css( {display: 'none'} ).prev('tr' ).css('border-bottom','none');;
                            var group = jQuery( '#<?php echo esc_js($this->field['id']); ?>-marker' ).parents( '.redux-group-tab:first' );
                            if ( !group.hasClass( 'sectionsChecked' ) ) {
                                group.addClass( 'sectionsChecked' );
                                var test = group.find( '.redux-section-indent-start h3' );
                                jQuery.each(
                                    test, function( key, value ) {
                                        jQuery( value ).css( 'margin-top', '20px' )
                                    }
                                );
                                if ( group.find( 'h3:first' ).css( 'margin-top' ) == "20px" ) {
                                    group.find( 'h3:first' ).css( 'margin-top', '0' );
                                }
                            }
                        }
                    );
                </script>
            <?php

            }

        /**
         * Enqueue Function.
         *
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue() {
            wp_enqueue_style(
                'redux-field-gt3_section-css',
                $this->extension_url  . 'field_gt3_section.css',
                time(),
                true
            );

            wp_enqueue_script(
                'redux-field-field_gt3_section-js',
                $this->extension_url . '/field_gt3_section.js',
                array( 'jquery' ),
                time(),
                true
            );

        }

    }
}
