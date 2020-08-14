<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * WPML Compatibility Class
 *
 * @class   YITH_WCPB_Wpml_Compatibility
 * @package Yithemes
 * @since   1.1.2 Free
 * @author  Yithemes
 *
 */
class YITH_WCPB_Wpml_Compatibility {

    /** @var YITH_WCPB_Wpml_Compatibility */
    protected static $_instance;

    public $bundle_meta_to_copy = array( '_yith_wcpb_bundle_data' );

    public static function get_instance() {
        $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

        return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
    }

    /**
     * Constructor
     *
     * @access protected
     */
    protected function __construct() {
        add_action( 'wcml_after_duplicate_product_post_meta', array( $this, 'bundles_sync' ), 10, 2 );
    }

    public function bundles_sync( $original_product_id, $trnsl_product_id ) {
        foreach ( $this->bundle_meta_to_copy as $bundle_meta ) {
            $data = get_post_meta( $trnsl_product_id, $bundle_meta, true );
            if ( is_array( $data ) ) {
                $language = apply_filters( 'wpml_post_language_details', null, $trnsl_product_id );
                foreach ( $data as &$product ) {
                    $product[ 'product_id' ] = apply_filters(
                        'wpml_object_id',
                        $product[ 'product_id' ],
                        'product',
                        true,
                        $language[ 'language_code' ]
                    );
                }
                update_post_meta( $trnsl_product_id, $bundle_meta, $data );
            }
        }
    }

    /**
     * Retrieve the WPML parent product id
     *
     * @param $id
     *
     * @return mixed
     */
    public function get_parent_id( $id ) {
        /** @var WPML_Post_Translation $wpml_post_translations */
        global $wpml_post_translations;
        if ( $wpml_post_translations && $parent_id = $wpml_post_translations->get_original_element( $id ) )
            $id = $parent_id;

        return $id;
    }

    /**
     * Get id of post translation in current language
     *
     * @param int         $element_id
     * @param string      $element_type
     * @param bool        $return_original_if_missing
     * @param null|string $ulanguage_code
     *
     * @return int the translation id
     */
    public function wpml_object_id( $element_id, $element_type = 'post', $return_original_if_missing = false, $ulanguage_code = null ) {
        if ( function_exists( 'wpml_object_id_filter' ) ) {
            return wpml_object_id_filter( $element_id, $element_type, $return_original_if_missing, $ulanguage_code );
        } elseif ( function_exists( 'icl_object_id' ) ) {
            return icl_object_id( $element_id, $element_type, $return_original_if_missing, $ulanguage_code );
        } else {
            return $element_id;
        }
    }
}
