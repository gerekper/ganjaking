<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    if($selectFeed == 'Vimeo' || $selectFeed == 'Youtube'){ 
        echo '<div class="tp-fcb-container">
                <iframe class="responsive-iframe" src="'.esc_url($EmbedURL).'" ></iframe>
             </div>';
    }else if($selectFeed == 'Facebook'){
        if($EmbedURL == 'Alb' && !empty($FbAlbum)){ 
            $ij = 0;
            $albumSize = count($videoURL);
            $uniqId = uniqid('f-');
            if( $albumSize > 1 ){
                foreach ( $videoURL as $index => $fdata ){
                    $AImg = (!empty($fdata['images'])) ? $fdata['images'][0]['source'] : []; 
                    if( $ij == 0 ){
                        echo '<a href="'.esc_url($AImg).'" data-fancybox="'.esc_attr($uniqId).'" >
                                <img class="reference-thumb" src="'.esc_url($ImageURL).'" />
                            </a>';
                    }else{ 
                        echo '<a href="'.esc_url($AImg).'" data-fancybox="'.esc_attr($uniqId).'" >
                                <img class="hidden-image" src="'.esc_url($AImg).'" />
                            </a>';
                    }
                $ij++;
                }
            } else {
                echo '<img class="tp-post-thumb" src="'.esc_url($ImageURL).'" />';
            }
        }else if( $EmbedType == 'video' && empty($FbAlbum) ){
            echo '<div class="tp-fcb-container">
                    <iframe class="responsive-iframe" src="'.esc_url($videoURL).'" ></iframe>
                </div>';
        }else {
            echo '<img class="tp-post-thumb" src="'.esc_url($ImageURL).'" />';
        }
    }else if($selectFeed == 'Instagram'){
        if($IGGP_Type == 'Instagram_Graph'){
            if( $Type == "CAROUSEL_ALBUM" ){
                echo "<div id='IGGP-wrap'>
                        <div id='IGGP-slider'>";
                            if( !empty($IGGP_CAROUSEL) ){
                                foreach ($IGGP_CAROUSEL as $key => $IGGP){
                                    $IGGP_MediaType = !empty($IGGP['IGGPImg_Type']) ? $IGGP['IGGPImg_Type'] : 'IMAGE'; 
                                    $IGGP_MediaURl = !empty($IGGP['IGGPURL_Media']) ? $IGGP['IGGPURL_Media'] : ''; 
                                    echo "<div class='slide-item'>";
                                        if($IGGP_MediaType == 'IMAGE'){
                                            echo "<img src='".esc_url($IGGP_MediaURl)."' class='tp-fcb-thumb' data-lazy='".esc_url($IGGP_MediaURl)."' alt='".esc_attr($key)."' >";
                                        }else if($IGGP_MediaType == 'VIDEO'){
                                            echo "<iframe class='responsive-iframe' src='".esc_url($IGGP_MediaURl)."' frameborder='0'></iframe>";
                                        }
                                    echo "</div>";
                                }   
                            }
                    echo "</div>
                    </div>";
            }else if( $Type == 'IMAGE' ){
                echo '<img class="tp-fcb-thumb" src="'.esc_url($ImageURL).'" />';
            }else if( $Type == 'VIDEO' ){
                echo '<div class="tp-fcb-container">
                        <iframe class="responsive-iframe" src="'.esc_url($videoURL).'" frameborder="0"></iframe>
                    </div>';
            }
        }else{
            echo '<img class="tp-fcb-thumb" src="'.esc_url($ImageURL).'" />';
        }

    }else if(!empty($ImageURL)){
        echo '<img class="tp-fcb-thumb" src="'.esc_url($ImageURL).'" />';
    }
?>