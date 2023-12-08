<?php 
    echo '<div class="tp-fcb-header">';
        if(!empty($UserImage)){
            echo '<img class="tp-fcb-profile" src="'.esc_url($UserImage).'"/>';
        } 
        echo '<div class="tp-fcb-usercontact">';
            if(!empty($UserName)){
                echo '<div class="tp-fcb-username">
                        <a href="'.esc_url($UserLink).'" target="_blank" rel="noopener noreferrer">'.wp_kses_post($UserName).'</a>
                     </div>';
            } 
            if(!empty($CreatedTime)){ 
                echo '<div class="tp-fcb-time">
                        <a href="'.esc_url($PostLink).'" target="_blank" rel="noopener noreferrer">'.wp_kses_post($CreatedTime).'</a>
                     </div>';
            } 
        echo '</div>';
        if(!empty($socialIcon)){ 
            echo '<div class="tp-fcb-logo">
                    <i class="'.esc_attr($socialIcon).'"></i>
                 </div>';
        } 
    echo '</div>';
?>