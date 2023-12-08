<?php

namespace ElementPack\Includes;

use Elementor\Base_Data_Control;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ElementPack_FB_Access_Token_Generator_Control extends Base_Data_Control {

	public function get_type() {
		return 'EP_FB_TOKEN';
	}

	protected function get_default_settings() {
		return [
			'label'        => '',
			'description'  => '',
			'label_block'  => true,
			'show_label'   => true,
			'button_label' => __( 'Generate Access Token', 'bdthemes-element-pack' ),
			'page_id'      => '',
			'permission'   => 'manage_pages'
		];
	}

	public function content_template() {

		$control_uid = $this->get_control_uid();
		$options     = get_option( 'element_pack_api_settings' );
		$app_id      = isset( $options['facebook_app_id'] ) ? $options['facebook_app_id'] : '';
		$app_id      = apply_filters( 'ep_facebook_page_access_token_generator_app_id', $app_id );
		?>
        <div class="elementor-control-field">
            <label for="<?php echo $control_uid; ?>" class="elementor-control-title">{{ data.label }}</label>
            <div class="elementor-control-input-wrapper">
                <form action="" method="post" class="ep-facebook-page-access-token-generator-control">
                    <input id="<?php echo $control_uid; ?>" type="text" name="token-access"
                           class="elementor-control-tag-area ep-facebook-page-access-token-field"
                           title="{{ data.title }}" data-setting="{{ data.name }}"/>
                    <input type="button" data-permisson="{{ data.permission }}"
                           data-appid="<?php echo esc_attr( $app_id ) ?>" data-page_id_field="{{data.page_id}}"
                           name="connect-btn" style="background-color:rgb(24, 119, 242); color:white; font-weight: 600"
                           class="ep-facebook-page-access-token-generator-button"
                           value="{{ data.button_label }}" />
                    <p class="ep-error-notice" style="color:red"></p>
                </form>
            </div>
        </div>
        <div class="elementor-control-field-description">{{data.description}}</div>
		<?php
	}

	public function enqueue() {

		$options = get_option( 'element_pack_api_settings' );
		$app_id  = isset( $options['facebook_app_id'] ) ? $options['facebook_app_id'] : '';
		$app_id  = apply_filters( 'ep_facebook_page_access_token_generator_app_id', $app_id );

		if ( $app_id ):
			?>
            <script>
                window.fbAsyncInit = function () {
                    FB.init({
                        appId: '<?php echo esc_attr( $app_id ) ?>',
                        autoLogAppEvents: true,
                        xfbml: true,
                        version: 'v5.0'
                    });
                };

                (function (d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) {
                        return;
                    }
                    js = d.createElement(s);
                    js.id = id;
                    js.src = "https://connect.facebook.net/en_US/sdk.js";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));
            </script>
		<?php
		endif;
		wp_register_script( 'ep-facebook-page-access-token-generator-control', BDTEP_ASSETS_URL . 'js/controls/ep-facebook-page-access-token-generator-control.min.js' );
		wp_enqueue_script( 'ep-facebook-page-access-token-generator-control' );
	}

}