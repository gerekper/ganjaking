<?php

class CT_Ultimate_GDPR_Shortcode_Block_Cookies {

    public function __construct()
    {
        add_action( 'wp_enqueue_scripts', array($this,'assets'));
    }

    public function render(){ 

        $options = array_merge(
			CT_Ultimate_GDPR::instance()->get_controller_by_id( CT_Ultimate_GDPR_Controller_Cookie::ID )->get_default_options(),
			CT_Ultimate_GDPR::instance()->get_admin_controller()->get_options( CT_Ultimate_GDPR_Controller_Cookie::ID )
		);
        
        $title = $options['cookie_withdrawal_cookies_agreement'];
        $content = $options['cookie_withdrawal_cookies_agreement_description'];

    ?>
    <h4><?php echo $title; ?></h4>

    <form id="ct-ultimate-gdpr-block-cookies">
        <div class="ct-ultimate-gdpr-cookie checkbox">
            <label for="ct-ultimate-gdpr-block-cookies">
                <input type="checkbox" name="ct-ultimate-gdpr-block-cookies" class="ct-ultimate-gdpr-block-cookies">
            </label>
        </div>
        <div class="ct-ultimate-gdpr-cookie description">
            <p><?php echo $content; ?></p>
        </div>
        <div class="ct-ultimate-gdpr-cookie-block-btn">
            <input type="hidden" name="level" class="level" value="1"/>
            <input type="submit" name="ct-ultimate-gdpr-block-cookies-submit" value="<?php echo esc_html__( "Save", 'ct-ultimate-gdpr' ); ?>" class="ct-ultimate-gdpr-cookie-modal-btn button" disabled>
        </div>
    </form>
    <?php }

    public function assets()
    {
        wp_enqueue_script( 'ct-ultimate-gdpr-shortcode-block-cookie', ct_ultimate_gdpr_url() . '/assets/js/shortcode-block-cookie.js', array( 'jquery' ), ct_ultimate_gdpr_get_plugin_version() );
    }

}

function block_cookies($atts){
    $obj = new CT_Ultimate_GDPR_Shortcode_Block_Cookies();
    ob_start();
    $obj->render();
    return ob_get_clean();
}

add_shortcode( 'block_cookies', 'block_cookies' );