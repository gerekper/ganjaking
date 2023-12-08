<?php
namespace ElementPack\Modules\Countdown\Skins;
use DateTime;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Schemes;
use Elementor\Schemes\Color;

use ElementPack\Utils;
use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Tiny_Countdown extends Elementor_Skin_Base {
	
	public function get_id() {
		return 'bdt-tiny-countdown';
	}

	public function get_title() {
		return __( 'Tiny Countdown', 'bdthemes-element-pack' );
    }
    
    public function render() {
		$settings      = $this->parent->get_settings_for_display();
		$due_date      = $settings['due_date'];
		$string        = $this->parent->get_strftime( $settings );
		
		$with_gmt_time = date( 'Y-m-d H:i', strtotime( $due_date ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );		
		$datetime      = new DateTime($with_gmt_time);

		$final_time    = $datetime->format('c');

		$this->parent->add_render_attribute(
			[
				'countdown' => [
					'id' 	=> 'bdt-countdown-' . $this->get_id() . '-timer',
					'data-bdt-countdown' => [
						isset($settings['loop_time']) && ($settings['loop_time'] == 'yes') ?  '' : 'date: ' . $final_time
					],
				],
			]
		);

		
		if(is_user_logged_in()){
			$is_logged = true;
		}else{
			$is_logged = false;
		}
		
		$msg_id = 'bdt-countdown-msg-' . $this->get_id() . '';

		$id       = $this->parent->get_id();
		$coupon_tricky_id  = !empty($settings['id_for_coupon_code']) ? 'bdt-sf-' . $settings['id_for_coupon_code'] :  'bdt-sf-' . $id;

		$this->parent->add_render_attribute(
			[
				'countdown_wrapper' => [
					'class' => 'bdt-countdown-skin-tiny bdt-countdown-wrapper',
					'data-settings' => [
						wp_json_encode([
							"id"             => '#bdt-countdown-' . $this->get_id(), 
							'msgId'			 => '#' . $msg_id,
							'adminAjaxUrl'   => admin_url("admin-ajax.php"),
							'endActionType'	 => $settings['end_action_type'],
							'redirectUrl'	 => $settings['end_redirect_link'],
							'redirectDelay'	 => (empty($settings['link_redirect_delay']['size'])) ? 1000 : ($settings['link_redirect_delay']['size']) * 1000,
							'finalTime'		 => isset($settings['loop_time']) && ($settings['loop_time'] == 'yes') ?  '' :  $final_time,
							'wpCurrentTime'		 => $this->parent->wp_current_time(),
							'endTime'		 => strtotime($final_time),
							'loopHours'    => $settings['loop_time'] == 'yes' ?  $settings['loop_hours'] : false,
							'isLogged'     => $is_logged,
							'couponTrickyId' => $coupon_tricky_id
						]),
					],
				],
			]
		);

		?>
		<div <?php echo $this->parent->get_render_attribute_string('countdown_wrapper'); ?>>
			<div <?php echo $this->parent->get_render_attribute_string( 'countdown' ); ?>>
				<?php echo wp_kses_post($string); ?>
			</div>
			<?php if ($settings['end_action_type'] == 'message') : ?>
			<div id="<?php echo $msg_id; ?>" class="bdt-countdown-end-message" style="display:none;">
				<?php echo esc_html($settings['end_message']); ?>
			</div>
			<?php endif; ?>
			
		</div>
		<?php
	}

}

