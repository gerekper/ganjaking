<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ($selectFeed != 'Instagram') || ($selectFeed == 'Instagram' && $IGGP_Type == 'Instagram_Graph') ){
    echo '<div class="tp-sf-footer">';
        if($selectFeed == 'Facebook'){
            if(isset($Fblikes)){
                echo '<span class="tp-btn-like">';
                    if(!empty($showFooterIn) && $style == "style-2" ){
                        echo esc_html__("Like ","theplus");
                    }else{
                        echo '<img src="'.esc_url($likeImg).'"/>';
                        echo '<img src="'.esc_url($ReactionImg).'"/>';
                    }
                    echo tp_number_short($Fblikes);
                echo '</span>';
            }
            if(isset($comment)){
                echo '<span class="tp-btn-comment">';
                    if(!empty($showFooterIn) && $style == "style-2" ){
                        echo esc_html__('comment ','theplus');
                    }else{
                        echo '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="comment-alt" class="svg-inline--fa fa-comment-alt fa-w-16 tp-svg" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 7.1 5.8 12 12 12 2.4 0 4.9-.7 7.1-2.4L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64zm16 352c0 8.8-7.2 16-16 16H288l-12.8 9.6L208 428v-60H64c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16h384c8.8 0 16 7.2 16 16v288z"></path></svg> ';
                    }
                        echo esc_attr($comment); 
                echo '</span>';
            }
            if(isset($share)){
                echo '<span class="tp-btn-share">';
                    if(!empty($showFooterIn) && $style == "style-2" ){
                        echo esc_html__("share ","theplus");
                    }else{
                        echo '<svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="share" class="svg-inline--fa fa-share fa-w-18 tp-svg" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M564.907 196.35L388.91 12.366C364.216-13.45 320 3.746 320 40.016v88.154C154.548 130.155 0 160.103 0 331.19c0 94.98 55.84 150.231 89.13 174.571 24.233 17.722 58.021-4.992 49.68-34.51C100.937 336.887 165.575 321.972 320 320.16V408c0 36.239 44.19 53.494 68.91 27.65l175.998-184c14.79-15.47 14.79-39.83-.001-55.3zm-23.127 33.18l-176 184c-4.933 5.16-13.78 1.73-13.78-5.53V288c-171.396 0-295.313 9.707-243.98 191.7C72 453.36 32 405.59 32 331.19 32 171.18 194.886 160 352 160V40c0-7.262 8.851-10.69 13.78-5.53l176 184a7.978 7.978 0 0 1 0 11.06z"></path></svg> ';
                    }
                        echo esc_attr($share);
                echo '</span>';
            }
        }
        if($selectFeed == 'Twitter'){
            if(isset($TwRT)){
                echo '<span class="button-comment">';
                    echo '<a href="'.esc_url($TwRetweetURL).'">';
                        if(!empty($showFooterIn) && $style == "style-2" ){
                            echo esc_html__('Retweet ','theplus');
                        }else{
                            echo '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="retweet-alt" class="svg-inline--fa fa-retweet-alt fa-w-20 tp-svg" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M392.402 383.598C404.359 395.555 395.891 416 378.981 416H120c-13.255 0-24-10.745-24-24V192H48c-21.361 0-32.045-25.895-16.971-40.971l80-80c9.373-9.372 24.568-9.372 33.941 0l80 80C240.074 166.134 229.319 192 208 192h-48v160h202.056c7.82 0 14.874 4.783 17.675 12.084a55.865 55.865 0 0 0 12.671 19.514zM592 320h-48V120c0-13.255-10.745-24-24-24H261.019c-16.91 0-25.378 20.445-13.421 32.402a55.865 55.865 0 0 1 12.671 19.514c2.801 7.302 9.855 12.084 17.675 12.084H480v160h-48c-21.313 0-32.08 25.861-16.971 40.971l80 80c9.374 9.372 24.568 9.372 33.941 0l80-80C624.041 345.9 613.368 320 592 320z"></path></svg> ';
                        } 
                            echo esc_attr($TwRT);
                    echo '</a>';
                echo '</span>';
            }
            if(isset($TWLike)){
                echo '<span class="button-comment">';
                    echo '<a href="'.esc_url($TwlikeURL).'">';
                        if(!empty($showFooterIn) && $style == "style-2" ){
                            echo esc_html__('Like ','theplus');
                        }else{
                            echo '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="heart" class="svg-inline--fa fa-heart fa-w-16 tp-svg" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M458.4 64.3C400.6 15.7 311.3 23 256 79.3 200.7 23 111.4 15.6 53.6 64.3-21.6 127.6-10.6 230.8 43 285.5l175.4 178.7c10 10.2 23.4 15.9 37.6 15.9 14.3 0 27.6-5.6 37.6-15.8L469 285.6c53.5-54.7 64.7-157.9-10.6-221.3zm-23.6 187.5L259.4 430.5c-2.4 2.4-4.4 2.4-6.8 0L77.2 251.8c-36.5-37.2-43.9-107.6 7.3-150.7 38.9-32.7 98.9-27.8 136.5 10.5l35 35.7 35-35.7c37.8-38.5 97.8-43.2 136.5-10.6 51.1 43.1 43.5 113.9 7.3 150.8z"></path></svg> '; 
                        } 
                            echo esc_attr($TWLike);
                    echo '</a>';
                echo '</span>';
            }
        }
        if($selectFeed == 'Vimeo'){
            if(isset($likes)){
                echo '<span class="tp-btn-like">';
                    if(!empty($showFooterIn) && $style == "style-2" ){ 
                        echo esc_html__("Like ","theplus");
                    }else{
                        echo '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="heart" class="svg-inline--fa fa-heart fa-w-16 tp-svg" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M458.4 64.3C400.6 15.7 311.3 23 256 79.3 200.7 23 111.4 15.6 53.6 64.3-21.6 127.6-10.6 230.8 43 285.5l175.4 178.7c10 10.2 23.4 15.9 37.6 15.9 14.3 0 27.6-5.6 37.6-15.8L469 285.6c53.5-54.7 64.7-157.9-10.6-221.3zm-23.6 187.5L259.4 430.5c-2.4 2.4-4.4 2.4-6.8 0L77.2 251.8c-36.5-37.2-43.9-107.6 7.3-150.7 38.9-32.7 98.9-27.8 136.5 10.5l35 35.7 35-35.7c37.8-38.5 97.8-43.2 136.5-10.6 51.1 43.1 43.5 113.9 7.3 150.8z"></path></svg> ';    
                    }
                        echo esc_attr($likes);
                echo '</span>';
            }
            if(isset($comment)){
                echo '<span class="tp-btn-comment">';
                    if(!empty($showFooterIn) && $style == "style-2" ){ 
                        echo esc_html__("comment ","theplus");  
                    }else{ 
                        echo '<svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="comment" class="svg-inline--fa fa-comment fa-w-16 tp-svg" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 64c123.5 0 224 79 224 176S379.5 416 256 416c-28.3 0-56.3-4.3-83.2-12.8l-15.2-4.8-13 9.2c-23 16.3-58.5 35.3-102.6 39.6 12-15.1 29.8-40.4 40.8-69.6l7.1-18.7-13.7-14.6C47.3 313.7 32 277.6 32 240c0-97 100.5-176 224-176m0-32C114.6 32 0 125.1 0 240c0 47.6 19.9 91.2 52.9 126.3C38 405.7 7 439.1 6.5 439.5c-6.6 7-8.4 17.2-4.6 26 3.8 8.8 12.4 14.5 22 14.5 61.5 0 110-25.7 139.1-46.3 29 9.1 60.2 14.3 93 14.3 141.4 0 256-93.1 256-208S397.4 32 256 32z"></path></svg> ';
                    } 
                    echo esc_attr($comment);
                echo '</span>';
            }
            if(isset($share)){
                echo '<span class="tp-btn-share">';
                    if(!empty($showFooterIn) && $style == "style-2" ){ 
                        echo esc_html__("share ","theplus");
                    }else{
                        echo '<svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="share" class="svg-inline--fa fa-share fa-w-18 tp-svg" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M564.907 196.35L388.91 12.366C364.216-13.45 320 3.746 320 40.016v88.154C154.548 130.155 0 160.103 0 331.19c0 94.98 55.84 150.231 89.13 174.571 24.233 17.722 58.021-4.992 49.68-34.51C100.937 336.887 165.575 321.972 320 320.16V408c0 36.239 44.19 53.494 68.91 27.65l175.998-184c14.79-15.47 14.79-39.83-.001-55.3zm-23.127 33.18l-176 184c-4.933 5.16-13.78 1.73-13.78-5.53V288c-171.396 0-295.313 9.707-243.98 191.7C72 453.36 32 405.59 32 331.19 32 171.18 194.886 160 352 160V40c0-7.262 8.851-10.69 13.78-5.53l176 184a7.978 7.978 0 0 1 0 11.06z"></path></svg> '; 
                    } 
                    echo esc_attr($share);
                echo '</span>';
            }

        }
        if($selectFeed == 'Youtube'){ 
            if(isset($view)){
                echo '<span class="button-comment">';
                    if(!empty($showFooterIn) && $style == "style-2" ){
                        echo esc_html__("view ","theplus");
                    }else{
                        echo '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="eye-slash" class="svg-inline--fa fa-eye-slash fa-w-20 tp-svg" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M634 471L36 3.51A16 16 0 0 0 13.51 6l-10 12.49A16 16 0 0 0 6 41l598 467.49a16 16 0 0 0 22.49-2.49l10-12.49A16 16 0 0 0 634 471zM296.79 146.47l134.79 105.38C429.36 191.91 380.48 144 320 144a112.26 112.26 0 0 0-23.21 2.47zm46.42 219.07L208.42 260.16C210.65 320.09 259.53 368 320 368a113 113 0 0 0 23.21-2.46zM320 112c98.65 0 189.09 55 237.93 144a285.53 285.53 0 0 1-44 60.2l37.74 29.5a333.7 333.7 0 0 0 52.9-75.11 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64c-36.7 0-71.71 7-104.63 18.81l46.41 36.29c18.94-4.3 38.34-7.1 58.22-7.1zm0 288c-98.65 0-189.08-55-237.93-144a285.47 285.47 0 0 1 44.05-60.19l-37.74-29.5a333.6 333.6 0 0 0-52.89 75.1 32.35 32.35 0 0 0 0 29.19C89.72 376.41 197.08 448 320 448c36.7 0 71.71-7.05 104.63-18.81l-46.41-36.28C359.28 397.2 339.89 400 320 400z"></path></svg> ';
                    } echo esc_attr($view);
                echo '</span>';
            }
            if(isset($likes)){
                echo '<span class="button-comment">';
                    if(!empty($showFooterIn) && $style == "style-2" ){
                        echo esc_html__("likes ","theplus");
                    }else{
                        echo '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="heart" class="svg-inline--fa fa-heart fa-w-16 tp-svg" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M458.4 64.3C400.6 15.7 311.3 23 256 79.3 200.7 23 111.4 15.6 53.6 64.3-21.6 127.6-10.6 230.8 43 285.5l175.4 178.7c10 10.2 23.4 15.9 37.6 15.9 14.3 0 27.6-5.6 37.6-15.8L469 285.6c53.5-54.7 64.7-157.9-10.6-221.3zm-23.6 187.5L259.4 430.5c-2.4 2.4-4.4 2.4-6.8 0L77.2 251.8c-36.5-37.2-43.9-107.6 7.3-150.7 38.9-32.7 98.9-27.8 136.5 10.5l35 35.7 35-35.7c37.8-38.5 97.8-43.2 136.5-10.6 51.1 43.1 43.5 113.9 7.3 150.8z"></path></svg> ';
                    } echo esc_attr($likes);
                echo '</span>';
            } 
            if(isset($comment)){
                echo '<span class="button-comment">';
                    if(!empty($showFooterIn) && $style == "style-2" ){
                        echo esc_html__("Comment ","theplus");
                    }else{
                        echo '<svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="comment" class="svg-inline--fa fa-comment fa-w-16 tp-svg" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 64c123.5 0 224 79 224 176S379.5 416 256 416c-28.3 0-56.3-4.3-83.2-12.8l-15.2-4.8-13 9.2c-23 16.3-58.5 35.3-102.6 39.6 12-15.1 29.8-40.4 40.8-69.6l7.1-18.7-13.7-14.6C47.3 313.7 32 277.6 32 240c0-97 100.5-176 224-176m0-32C114.6 32 0 125.1 0 240c0 47.6 19.9 91.2 52.9 126.3C38 405.7 7 439.1 6.5 439.5c-6.6 7-8.4 17.2-4.6 26 3.8 8.8 12.4 14.5 22 14.5 61.5 0 110-25.7 139.1-46.3 29 9.1 60.2 14.3 93 14.3 141.4 0 256-93.1 256-208S397.4 32 256 32z"></path></svg> ';
                    }
                        echo esc_attr($comment);
                echo '</span>';
            }
            if(isset($Dislike)){
                echo '<span class="button-comment">';
                    if(!empty($showFooterIn) && $style == "style-2" ){
                        echo esc_html__("Dislike ","theplus");
                    }else{
                        echo '<svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="thumbs-down" class="svg-inline--fa fa-thumbs-down fa-w-16 tp-svg" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M496.656 226.317c5.498-22.336 2.828-49.88-9.627-69.405 4.314-23.768-3.099-49.377-18.225-67.105C470.724 35.902 437.75 0 378.468.014c-3.363-.03-35.508-.003-41.013 0C260.593-.007 195.917 40 160 40h-10.845c-5.64-4.975-13.042-8-21.155-8H32C14.327 32 0 46.327 0 64v256c0 17.673 14.327 32 32 32h96c17.673 0 32-14.327 32-32v-12.481c.85.266 1.653.549 2.382.856C184 320 219.986 377.25 243.556 400.82c9.9 9.9 13.118 26.44 16.525 43.951C265.784 474.082 276.915 512 306.91 512c59.608 0 82.909-34.672 82.909-93.08 0-30.906-11.975-52.449-20.695-69.817h70.15c40.654 0 72.726-34.896 72.727-72.571-.001-20.532-5.418-37.341-15.345-50.215zM128 320H32V64h96v256zm311.273-2.898H327.274c0 40.727 30.545 59.628 30.545 101.817 0 25.574 0 61.091-50.909 61.091-20.363-20.364-10.182-71.272-40.727-101.817-28.607-28.607-71.272-101.818-101.818-101.818H160V72.74h4.365c34.701 0 101.818-40.727 173.09-40.727 3.48 0 37.415-.03 40.727 0 38.251.368 65.505 18.434 57.212 70.974 16.367 8.78 28.538 39.235 15.015 61.996C472 176 472 224 456.017 235.648 472 240 480.1 256.012 480 276.375c-.1 20.364-17.997 40.727-40.727 40.727zM104 272c0 13.255-10.745 24-24 24s-24-10.745-24-24 10.745-24 24-24 24 10.745 24 24z"></path></svg> ';
                    }
                        echo esc_attr($Dislike);
                echo '</span>';
            } 
        }
        if($selectFeed == 'Instagram'){ 
            if(isset($likes)){
                echo '<span class="tp-btn-like">';
                    if(!empty($showFooterIn) && $style == "style-2" ){ 
                        echo esc_html__("Like ","theplus");
                    }else{
                        echo '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="heart" class="svg-inline--fa fa-heart fa-w-16 tp-svg" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M458.4 64.3C400.6 15.7 311.3 23 256 79.3 200.7 23 111.4 15.6 53.6 64.3-21.6 127.6-10.6 230.8 43 285.5l175.4 178.7c10 10.2 23.4 15.9 37.6 15.9 14.3 0 27.6-5.6 37.6-15.8L469 285.6c53.5-54.7 64.7-157.9-10.6-221.3zm-23.6 187.5L259.4 430.5c-2.4 2.4-4.4 2.4-6.8 0L77.2 251.8c-36.5-37.2-43.9-107.6 7.3-150.7 38.9-32.7 98.9-27.8 136.5 10.5l35 35.7 35-35.7c37.8-38.5 97.8-43.2 136.5-10.6 51.1 43.1 43.5 113.9 7.3 150.8z"></path></svg> ';    
                    }
                        echo esc_attr($likes);
                echo '</span>';
            }
            if(isset($comment)){
                echo '<span class="tp-btn-comment">';
                    if(!empty($showFooterIn) && $style == "style-2" ){ 
                        echo esc_html__("comment ","theplus");  
                    }else{ 
                        echo '<svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="comment" class="svg-inline--fa fa-comment fa-w-16 tp-svg" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 64c123.5 0 224 79 224 176S379.5 416 256 416c-28.3 0-56.3-4.3-83.2-12.8l-15.2-4.8-13 9.2c-23 16.3-58.5 35.3-102.6 39.6 12-15.1 29.8-40.4 40.8-69.6l7.1-18.7-13.7-14.6C47.3 313.7 32 277.6 32 240c0-97 100.5-176 224-176m0-32C114.6 32 0 125.1 0 240c0 47.6 19.9 91.2 52.9 126.3C38 405.7 7 439.1 6.5 439.5c-6.6 7-8.4 17.2-4.6 26 3.8 8.8 12.4 14.5 22 14.5 61.5 0 110-25.7 139.1-46.3 29 9.1 60.2 14.3 93 14.3 141.4 0 256-93.1 256-208S397.4 32 256 32z"></path></svg> ';
                    } 
                    echo esc_attr($comment);
                echo '</span>';
            }
        }
    echo '</div>';
}   

