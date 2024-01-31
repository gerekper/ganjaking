<?php
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * Worldpay payment method integration
 *
 */
final class WC_Worldpay_Blocks_Support extends AbstractPaymentMethodType {
    /**
     * Name of the payment method.
     *
     * @var string
     */
    protected $name = 'worldpay';
    public $icon;

    /**
     * Initializes the payment method type.
     */
    public function initialize() {
        $this->settings = get_option( 'woocommerce_worldpay_settings', [] );
        $this->icon = apply_filters( 'wc_worldpay_icon', '' );
    }

    /**
     * Returns if this payment method should be active. If false, the scripts will not be enqueued.
     *
     * @return boolean
     */
    public function is_active() {
        $payment_gateways_class   = WC()->payment_gateways();
        $payment_gateways         = $payment_gateways_class->payment_gateways();

        return $payment_gateways['worldpay']->is_available();
    }

    /**
     * Returns an array of scripts/handles to be registered for this payment method.
     *
     * @return array
     */
    public function get_payment_method_script_handles() {
        $asset_path   = WORLDPAYPLUGINURL . 'classes/blocks/js/index.asset.php';
        $version      = WORLDPAYPLUGINVERSION;
        $dependencies = [];
        if ( file_exists( $asset_path ) ) {
            $asset        = require $asset_path;
            $version      = is_array( $asset ) && isset( $asset['version'] )
                ? $asset['version']
                : $version;
            $dependencies = is_array( $asset ) && isset( $asset['dependencies'] )
                ? $asset['dependencies']
                : $dependencies;
        }
        wp_register_script(
            'wc-worldpay-blocks-integration',
            WORLDPAYPLUGINURL . 'classes/blocks/js/index.js',
            $dependencies,
            $version,
            true
        );
        wp_set_script_translations(
            'wc-worldpay-blocks-integration',
            'woocommerce-gateway-worldpay'
        );
        return [ 'wc-worldpay-blocks-integration' ];
    }

    /**
     * Returns an array of key=>value pairs of data made available to the payment methods script.
     *
     * @return array
     */
    public function get_payment_method_data() {
        return [
            'title'       => $this->get_setting( 'title' ),
            'description' => $this->get_setting( 'description' ),
            'supports'    => $this->get_supported_features(),
            'logo_url'    => WORLDPAYPLUGINURL . 'images/poweredByWorldPay.png',
        ];
    }

    /**
     * Returns an array of supported features.
     *
     * @return string[]
     */
    public function get_supported_features() {
        $payment_gateways = WC()->payment_gateways->payment_gateways();
        return $payment_gateways['worldpay']->supports;
    }

    private function get_icons() {

        $icons_src  = array();

        if ( $this->icon ) {

            $icons_src[ strtolower($this->settings['title']) ] = array(
                'src' => esc_url( $this->icon ),
                'alt' => $this->settings['title']
            );

        } elseif ( ! empty( $this->settings['cardtypes'] ) ) {
            foreach ( $this->settings['cardtypes'] as $card_type ) {

                $icons_src[esc_attr( strtolower( str_replace( ' ','-',$card_type ) ) )] = array(
                    'src' => esc_url( WORLDPAYPLUGINURL . 'images/card-' . strtolower( str_replace(' ','-',$card_type) ) . '.png' ),
                    'alt' => esc_attr( ucwords( $card_type ) )
                );

            }
        }

        /**
         * Add Payments V.me logo
         */
        if ( $this->settings['vmelogo'] == 'yes' ) {
            // $icons_src['vmelogo'] = esc_url( WORLDPAYPLUGINURL . 'images/vme.png' );
        }

        /**
         * Add Payments Powered By WorldPay logo
         */
        if ( $this->settings['wplogo'] == 'yes' ) {
            // $icons_src['Payments Powered By WorldPay'] = esc_url( WORLDPAYPLUGINURL . 'images/poweredByWorldPay.png' );   
        }

        return $icons_src;
    }

}
