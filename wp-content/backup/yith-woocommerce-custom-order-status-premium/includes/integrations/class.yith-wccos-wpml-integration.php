<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * WPML Compatibility Class
 *
 * @class   YITH_WCCOS_Wpml_Integration
 * @since   1.1.6
 */
class YITH_WCCOS_Wpml_Integration {

    /** @var YITH_WCCOS_Wpml_Integration */
    private static $_instance;

    /** @var SitePress */
    private $sitepress;

    private $_fields = array();

    public static function get_instance() {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
    }

    private function __construct() {
        global $sitepress;
        if ( $sitepress ) {
            $this->sitepress = $sitepress;
            $this->_init_fields();

            // Translate status titles
            add_filter( 'yith_wccos_order_status_title', array( $this, 'translate_status_title' ), 10, 2 );
            add_filter( 'yith_wccos_custom_message', array( $this, 'translate_email_fields' ), 10, 2 );
            add_filter( 'yith_wccos_email_heading', array( $this, 'translate_email_fields' ), 10, 2 );
            add_filter( 'yith_wccos_email_subject', array( $this, 'translate_email_fields' ), 10, 2 );

            add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
            add_action( 'save_post', array( $this, 'save_metabox' ) );
        }
    }

    /**
     * Init fields
     */
    private function _init_fields() {
        $this->_fields = array(
            'title'                => array(
                'name'  => '_yith_wccos_wpml_title_translations',
                'title' => __( 'Title', 'yith-woocommerce-custom-order-status' ),
            ),
            'email_custom_message' => array(
                'name'  => '_yith_wccos_wpml_custom_message_translations',
                'title' => __( 'Email Custom Message', 'yith-woocommerce-custom-order-status' ),
                'type'  => 'textarea'
            ),
            'email_heading'        => array(
                'name'  => '_yith_wccos_wpml_email_heading_translations',
                'title' => __( 'Email Heading', 'yith-woocommerce-custom-order-status' ),
            ),
            'email_subject'        => array(
                'name'  => '_yith_wccos_wpml_email_subject_translations',
                'title' => __( 'Email Subject', 'yith-woocommerce-custom-order-status' ),
            ),
        );
    }

    /**
     * Get the current language
     * @return string|null
     */
    public function get_current_language() {
        return $this->sitepress->get_current_language();
    }

    /**
     * Get the default language
     * @return string
     */
    public function get_default_language() {
        return $this->sitepress->get_default_language();
    }

    /**
     * Translate the status title
     * @param string $title
     * @param int $status_id
     * @return string
     */
    public function translate_status_title( $title, $status_id ) {
        $current_language   = $this->get_current_language();
        $title_translations = get_post_meta( $status_id, '_yith_wccos_wpml_title_translations', true );
        if ( !!$title_translations && !empty( $title_translations[ $current_language ] ) ) {
            $title = $title_translations[ $current_language ];
        }
        return $title;
    }

    /**
     * Add the metabox
     */
    public function add_metabox() {
        add_meta_box( 'yith-wccos-wpml-translations',
                      __( 'WPML Traslations', 'yith-woocommerce-custom-order-status' ),
                      array( $this, 'show_translations_metabox' ),
                      'yith-wccos-ostatus',
                      'side',
                      'default' );
    }

    /**
     * Show the translations metabox
     * @param WP_Post $post
     */
    public function show_translations_metabox( $post ) {
        $languages        = $this->sitepress->get_active_languages();
        $default_language = $this->get_default_language();
        if ( isset( $languages[ $default_language ] ) ) {
            unset( $languages[ $default_language ] );
        }

        foreach ( $languages as $language_code => $language ) {
            $language_name = isset( $language[ 'display_name' ] ) ? $language[ 'display_name' ] : $language_code;

            ?>
            <div class="yith-wccos-wpml-translation yith-wccos-wpml-translation__<?php echo $language_code; ?>">
                <div class="yith-wccos-wpml-translation__language"><?php echo $language_name ?></div>
                <?php
                foreach ( $this->_fields as $key => $field ) {
                    $field_name   = $field[ 'name' ] . "[{$language_code}]";
                    $field_id     = $field[ 'name' ] . '_' . $language_code;
                    $field_title  = $field[ 'title' ];
                    $translations = get_post_meta( $post->ID, $field[ 'name' ], true );
                    $field_value  = isset( $translations[ $language_code ] ) ? $translations[ $language_code ] : '';
                    $field_type   = isset( $field[ 'type' ] ) ? $field[ 'type' ] : 'input';
                    ?>
                    <div class="yith-wccos-wpml-translation__single yith-wccos-wpml-translation__single__<?php echo $key ?>">
                        <label for="<?php echo $field_id; ?>"><?php echo $field_title ?></label>

                        <?php if ( 'textarea' === $field_type ) : ?>
                            <textarea name="<?php echo $field_name; ?>"
                                      id="<?php echo $field_id; ?>"><?php echo $field_value ?></textarea>
                        <?php else: ?>
                            <input type="text" name="<?php echo $field_name; ?>"
                                   id="<?php echo $field_id; ?>"
                                   value="<?php echo $field_value ?>"/>
                        <?php endif; ?>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
    }

    /**
     * Save the metabox
     * @param int $post_id
     */
    public function save_metabox( $post_id ) {

        if ( isset( $_POST[ '_yith_wccos_wpml_title_translations' ] ) ) {
            update_post_meta( $post_id, '_yith_wccos_wpml_title_translations', $_POST[ '_yith_wccos_wpml_title_translations' ] );
            update_post_meta( $post_id, '_yith_wccos_wpml_custom_message_translations', $_POST[ '_yith_wccos_wpml_custom_message_translations' ] );
        }

        foreach ( $this->_fields as $key => $field ) {
            $name = $field[ 'name' ];
            if ( isset( $_POST[ $name ] ) ) {
                update_post_meta( $post_id, $name, $_POST[ $name ] );
            }
        }
    }

    /**
     * Translate email fields;
     *
     * @param string   $text
     * @param WC_Order $order
     * @return string
     * @since 1.1.19
     */
    public function translate_email_fields( $text, $order ) {
        $meta = false;
        switch ( current_action() ) {
            case 'yith_wccos_custom_message':
                $meta = '_yith_wccos_wpml_custom_message_translations';
                break;
            case 'yith_wccos_email_heading':
                $meta = '_yith_wccos_wpml_email_heading_translations';
                break;
            case 'yith_wccos_email_subject':
                $meta = '_yith_wccos_wpml_email_subject_translations';
                break;
        }

        if ( $meta ) {
            $order_language = $order->get_meta( 'wpml_language' );
            $status_args    = array( 'posts_per_page' => 1, 'meta_key' => 'slug', 'meta_value' => $order->get_status() );
            $status_ids     = yith_wccos_get_statuses( $status_args );
            if ( $status_ids ) {
                $translations = get_post_meta( current( $status_ids ), $meta, true );
                if ( !!$translations && !empty( $translations[ $order_language ] ) ) {
                    $text = $translations[ $order_language ];
                }
            }
        }
        return $text;
    }

}

return YITH_WCCOS_Wpml_Integration::get_instance();