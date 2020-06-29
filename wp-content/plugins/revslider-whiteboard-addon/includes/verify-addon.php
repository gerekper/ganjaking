<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class Revslider_Whiteboard_Addon_Verify {
	
	private $notice = false,
			$min_rev_slider = '6.0',
			$addon_title = 'Whiteboard',
			$text_domain = 'revslider-whiteboard-addon',
			$notice_slug = 'revslider_whiteboard_addon';
	
	public function __construct() {
		
		if(!class_exists('RevSliderFront')) {
			$this->notice = 'add_notice_plugin';
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, $this->min_rev_slider, '>=')) {
			$this->notice = 'add_notice_version';
		}
		else if(get_option('revslider-valid', 'false') == 'false') {
			$this->notice = 'add_notice_activation';
		}

		if($this->notice) {
			add_action('admin_enqueue_scripts', array($this, 'enqueue_notice_script'));
			add_action('admin_notices', array($this, 'add_notice'));
		}

	}
	
	public function is_verified() {
		
		return $this->notice === false;
		
	}
	
	public function enqueue_notice_script() {
	
		wp_enqueue_script($this->text_domain . '-notice', WHITEBOARD_PLUGIN_URL . 'admin/assets/js/dismiss-admin-notice.js', array('jquery'), WHITEBOARD_VERSION, true);
	
	}
	
	public function add_notice() {
		
		switch($this->notice) {
				
			case 'add_notice_activation':
				$id = md5($this->notice_slug . '_add_notice_activation');
				$this->notice = 'The <a href="?page=revslider">' . $this->addon_title . ' Add-On</a> requires an active ' . 
						        '<a href="//www.themepunch.com/slider-revolution/install-activate-and-update/#register-purchase-code" target="_blank">Purchase Code Registration</a>';
			break;
			
			case 'add_notice_plugin':
				$id = md5($this->notice_slug . '_add_notice_plugin');
				$this->notice = '<a href="//revolution.themepunch.com/" target="_blank">Slider Revolution</a> required to use the ' . $this->addon_title . ' Add-On';
			break;
			
			case 'add_notice_version':
				$id = md5($this->notice_slug . '_add_notice_version');
				$this->notice = 'The ' . $this->addon_title . ' Add-On requires Slider Revolution ' . $this->min_rev_slider . 
								' <a href="//www.themepunch.com/slider-revolution/install-activate-and-update/#plugin-updates" target="_blank">Update Slider Revolution</a>';
			break;
			
			default:
				$id = '';
				$this->notice = '';
			// end default
			
		}
		
		?>
		<div class="error below-h2 soc-notice-wrap revaddon-notice" style="display: none">
			<p><?php _e($this->notice, $this->text_domain); ?><span data-addon="<?php echo $this->text_domain; ?>-notice" data-noticeid="<?php echo $id; ?>" style="float: right; cursor: pointer" class="revaddon-dismiss-notice dashicons dashicons-dismiss"></span></p>
		</div>
		<?php
		
	}

}
?>