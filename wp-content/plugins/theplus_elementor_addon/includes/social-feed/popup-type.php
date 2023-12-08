<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    if( $PopupOption == "GoWebsite" ){
        $PopupTarget = 'target=_blank" rel="noopener noreferrer"';
        $PopupLink = 'href="'.esc_url($videoURL).'"';
    }

    if(!empty($ImageURL)){ 
        if( $style == "style-1" || $style == "style-2" ){
            if( $PopupOption == "Donothing" || $PopupOption == "GoWebsite" ){
                echo '<a '.$PopupLink . $PopupTarget.' class="tp-soc-img-cls">';
                    echo $IGGP_Icon;
                    echo '<img class="tp-post-thumb" src="'.esc_url($ImageURL).'" />';
                echo '</a>';
            }else if( $PopupOption == "OnFancyBox" ){
                echo '<a href="javascript:;" '.$FancyBoxJS.' class="tp-soc-img-cls" data-src="#Fancy-'.esc_attr($PopupSylNum).'" >';
                    echo $IGGP_Icon;
                    echo '<img class="tp-post-thumb" src="'.esc_url($ImageURL).'" />';
                echo '</a>';
            }
        }else if( $style == "style-3" || $style == "style-4" ){ 
            if( $PopupOption == "Donothing" || $PopupOption == "GoWebsite" ){
                echo $IGGP_Icon;
                echo '<a '.$PopupLink . $PopupTarget.' class="tp-image-link tp-soc-img-cls" '.$FancyBoxJS.'></a>';
            }else if( $PopupOption == "OnFancyBox" ){
                echo $IGGP_Icon;
                echo '<a href="javascript:;" class="tp-image-link tp-soc-img-cls" '.$FancyBoxJS.' data-src="#Fancy-'.esc_attr($PopupSylNum).'" ></a>';
            }
        }
    }
