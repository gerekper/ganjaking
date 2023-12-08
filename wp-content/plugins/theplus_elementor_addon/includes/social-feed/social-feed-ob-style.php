<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	$Iconlogo = '<div class="tp-sf-logo">
					<a href="'.esc_url($PostLink).'" class="tp-sf-logo-link" target="_blank" rel="noopener noreferrer" >
						<i class="'.esc_attr($socialIcon).'"></i>
					</a>
				</div>';

	ob_start();
    	echo '<div class="tp-sf-header">';
    		if(!empty($UserImage)){
    			echo '<div class="tp-sf-profile"><img class="tp-sf-logo" src="'.esc_url($UserImage).'"/></div>';
    		} 
    		echo '<div class="tp-sf-usercontact">';
    			if(!empty($UserName)){
    				echo '<div class="tp-sf-username">
							<a href="'.esc_url($UserLink).'" target="_blank" rel="noopener noreferrer">'.wp_kses_post($UserName).'</a></div>';
    			} 
    			if(!empty($CreatedTime)){
    				echo '<div class="tp-sf-time">
							<a href="'.esc_url($PostLink).'" target="_blank" rel="noopener noreferrer">'.wp_kses_post($CreatedTime).'</a></div>';
    			}   
    		echo '</div>';
    		if( (!empty($socialIcon) && $style != "style-3") || (empty($ImageURL) && $style == "style-3") ){
    			echo $Iconlogo;
    		}
    	echo '</div>';
    $Header_html = ob_get_clean();

	// Title
	$Massage_html='';
	if(!empty($ShowTitle)){
		ob_start();
			echo '<div class="tp-title">'.wp_kses_post($Massage).'</div>';
		$Massage_html = ob_get_clean();
	}
	
	// Copy Id
	$Copyid_html='';
	if(\Elementor\Plugin::$instance->editor->is_edit_mode() && $ShowFeedId == 'yes' ){
		$Copyid_html = '<div class="tp-sf-copy-feed">
							<input type="text" id="tp-copy-feedid" class="tp-copy-feedid" value="'.esc_attr($PostId).'" disabled>
							<div class="tp-sf-copy-icon" data-CopyPostId="'.esc_html($PostId).'" >
								<i class="far fa-copy CopyLoading"></i>
							</div>	
						</div>';
	}
