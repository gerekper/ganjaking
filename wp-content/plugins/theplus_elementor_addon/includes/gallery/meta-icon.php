<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	if(!empty($loopImageIcon) && $loopImageIcon == 'icon'){
		if(!empty($loopIconStyle) && $loopIconStyle == 'font_awesome'){
			$iconFontawesome = $loopIconFontawesome;
			$icon_content = '<i class="'.esc_attr($iconFontawesome).'" ></i>';
		}else if(!empty($loopIconStyle) && $loopIconStyle == 'icon_mind'){
			$iconsMind = $loopIconsMind;
			$icon_content = '<i class="'.esc_attr($iconsMind).'" ></i>';
		}else if(!empty($loopIconStyle) && $loopIconStyle == 'font_awesome_5'){
			ob_start();
			\Elementor\Icons_Manager::render_icon( $loopIconFontawesome5, [ 'aria-hidden' => 'true' ]);
			$icon_content = ob_get_contents();
			ob_end_clean();
		}
	}else{
		if(!empty($customImage)){
			$icon_content = tp_get_image_rander( $customImageId,'full');
		}else{
			$icon_content = '<i class="fas fa-search-plus" aria-hidden="true"></i>';
		}
	}
?>
<div class="meta-search-icon">
	<?php	
	 if(!empty($settings['display_box_link']) && $settings['display_box_link']=='yes'){ ?>
		<div <?php echo $popup_attr_icon; ?>><?php echo $icon_content; ?></div>
	<?php }else{ ?>
		<a href="<?php echo esc_url($full_image); ?>" <?php echo $popup_attr_icon; ?>><?php echo $icon_content; ?></a>
	<?php } ?>
</div>