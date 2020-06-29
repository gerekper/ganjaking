<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Compatibility Class
 *
 * @class   YITH_WCMV_Addons
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 *
 */
class YITH_WCMV_Addons {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCMV_Addons
     * @since 1.0.0
     */
    protected static $instance;

    /**
     * Plugins Supported Array
     *
     * @var array
     * @since 1.0.0
     */
    public $plugins;

    /**
     * Main Frontpage Instance
     *
     * @var YITH_WCMV_Addons_Compatibility
     * @since 1.0
     */
    public $compatibility;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCMV_Addons
     * @since 1.0.0
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Constructor
     *
     * @access public
     * @since  1.0.0
     */
    public function __construct() {
        /* Custom option type */
        add_action( 'woocommerce_admin_field_yith_premium_addons', array( $this, 'premium_addons_field' ) );

        add_action( 'init', array( $this, 'load_compatibility_class' ) );
        $this->plugins = require_once( 'compatibility/plugins-list.php' );
    }

    /**
     * Load YITH_WCMV_Addons_Compatibility Class
     */
    public function load_compatibility_class(){
        if( is_admin() ) {
            $this->compatibility = YITH_WCMV_Addons_Compatibility::get_instance();
        }
    }

    /**
     * Check if user has YITH XXX Premium plugin
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @since  1.0
     * @return bool
     */
    public function has_plugin( $plugin_name ) {
        $has_plugin = false;

        if( ! empty( $this->plugins[ $plugin_name ] ) ){
            $plugin = $this->plugins[ $plugin_name ];
            if(
                defined( $plugin['premium'] ) && constant( $plugin['premium'] ) &&
                defined( $plugin['installed_version'] ) && constant( $plugin['installed_version'] ) &&
                version_compare( constant( $plugin['installed_version'] ), $plugin['min_version'], $plugin['compare'] )
            ){
                $has_plugin = true;
            }
        }
        return $has_plugin;
    }

     /**
     * Get plughin option description
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @since  1.0
     * @return bool
     */
    public function get_option_description( $plugin_name ){
        $option_desc = isset( $this->plugins[ $plugin_name ] ) ? $this->plugins[ $plugin_name ]['option_desc'] : '';
        return call_user_func( '__', $option_desc, 'yith-woocommerce-product-vendors' );
    }

     /**
     * Get plugin landing page URI
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @since  1.0
     * @return bool
     */
    public function get_plugin_landing_uri( $plugin_name, $context = 'uri' ){
        $plugin_link = '';
        
        if( isset( $this->plugins[ $plugin_name ] ) ){
            if ( 'display' == $context ) {
	            $coming_soon   = ! empty( $this->plugins[ $plugin_name ]['coming_soon'] ) && $this->plugins[ $plugin_name ]['coming_soon'] ? sprintf( '<span class="yith-wcmv-add-ons coming-soon">%s</span>', __( 'Coming Soon', 'yith-woocommerce-product-vendors' ) ) : '';
	            $is_new        = ! empty( $this->plugins[ $plugin_name ]['is_new'] ) && $this->plugins[ $plugin_name ]['is_new'] ? sprintf( '<span class="yith-wcmv-add-ons is-new">%s</span>', __( 'New', 'yith-woocommerce-product-vendors' ) ) : '';
	            $is_deprecated = ! empty( $this->plugins[ $plugin_name ]['is_deprecated'] ) && $this->plugins[ $plugin_name ]['is_deprecated'] ? sprintf( '<span class="yith-wcmv-add-ons is-deprecated">%s</span>', __( 'Deprecated', 'yith-woocommerce-product-vendors' ) ) : '';
	            $is_label      = $is_deprecated;

	            if( empty( $is_label ) ){
		            $is_label = ! empty( $coming_soon ) ? $coming_soon : $is_new;
                }

                $plugin_link = $this->has_plugin( $plugin_name ) ? sprintf( '%s', ! empty( $is_deprecated ) ? $is_deprecated : $is_new )  : sprintf( '<span class="yith-wcmv-required-plugin">(%s <a href="%s" target="_blank">%s</a> %s - %s %s %s).</span>%s',
                    _x( 'Needs', 'Admin: means needs YITH xxx plugin to works', 'yith-woocommerce-product-vendors' ),
                    $this->plugins[$plugin_name]['landing_uri'],
                    $this->plugins[$plugin_name]['name'],
                    _x( 'plugin', 'Admin: means needs YITH xxx plugin to works', 'yith-woocommerce-product-vendors' ),
                    _x( 'version', 'means: plugin version', 'yith-woocommerce-product-vendors' ),
                    $this->plugins[$plugin_name]['min_version'],
                    _x( 'or greater', 'means: min version xxx or greater', 'yith-woocommerce-product-vendors' ),
	                $is_label
                );
            }

            elseif( 'uri' == $context ){
                $plugin_link = $this->plugins[ $plugin_name ]['landing_uri'];
            }
        }
        return $plugin_link;
    }

    /**
     * Premium addons fields
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @since  1.0
     * @return bool
     */
    public function premium_addons_field( $value ) {
        $array_class     = isset( $value['class'] ) ? $value['class'] : array();
        $visbility_class = is_array( $array_class ) ? implode( ' ', $array_class ) : $array_class;
        ?>
        <tr valign="top" class="<?php echo esc_attr( $visbility_class ); ?>">
            <th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?></th>
            <td class="forminp forminp-checkbox">
                <fieldset>
                    <?php if ( !empty( $value['title'] ) ) : ?>
                        <legend class="screen-reader-text"><span><?php echo esc_html( $value['title'] ) ?></span>
                        </legend>
                    <?php endif; ?>
                    <label for="<?php echo $value['id'] ?>">
                        <?php echo $value['desc'] ?>
                        <?php if ( isset( $value[ 'settings_tab' ][ 'uri' ] ) && isset( $value[ 'settings_tab' ][ 'plugin_name' ] ) && isset( $value[ 'settings_tab' ][ 'desc' ] ) ): ?>
                            <?php printf( '<a href="%s">%s > %s > %s</a>', $value[ 'settings_tab' ][ 'uri' ], __( 'YIT Plugins', 'yith-woocommerce-product-vendors' ), $value[ 'settings_tab' ][ 'plugin_name' ], $value[ 'settings_tab' ][ 'desc' ] ); ?>
                        <?php endif; ?>
                    </label>
                </fieldset>
            </td>
        </tr>
        <?php
    }
}