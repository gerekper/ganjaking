<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    if($FancyStyle == 'style-1'){
        include THEPLUS_PATH. "includes/social-feed/fancybox-feed-style-1.php";
    }else if($FancyStyle == 'style-2'){
        include THEPLUS_PATH. "includes/social-feed/fancybox-feed-style-2.php";
    }else{
        if(empty($FbAlbum)){
            if( $PopupOption == "Donothing" ){
                $videoURL = '';
            }else if( $PopupOption == "GoWebsite" ){
                $PopupTarget = 'target=_blank rel="noopener noreferrer"';
                $PopupLink = 'href="'.esc_url($videoURL).'"';
            }else if( $PopupOption == "OnFancyBox" ){
                $PopupLink = 'href="'.esc_url($videoURL).'"';
            }
        }

        if( $selectFeed == 'Facebook' && !empty($FbAlbum) ){
            $ij=0;            
            foreach ($videoURL as $fdata){              
                $AImg = ( !empty($fdata['images']) && !empty($fdata['images'][0]['source']) ) ? $fdata['images'][0]['source'] : ''; 
   
                if($ij == 0){ ?>
                    <a href="<?php echo esc_url($AImg); ?>" <?php echo $FancyBoxJS; ?> >
                        <img class="reference-thumb" src="<?php echo esc_url($ImageURL); ?>" />
                    </a>
                <?php }else{ ?>
                    <a href="<?php echo esc_url($AImg); ?>" <?php echo $FancyBoxJS; ?> >
                        <img class="hidden-image" src="<?php echo esc_url($AImg); ?>" />
                    </a>
                <?php  }
                $ij++;
            }
        }else if( $selectFeed == 'Instagram' && $IGGP_Type == 'Instagram_Graph' ){
            
            if( !empty($ImageURL) ){
                if( $Type == "CAROUSEL_ALBUM" ){
                    if(!empty($IGGP_CAROUSEL)){
                        foreach ($IGGP_CAROUSEL as $key => $IGGP){
                            $IGGP_MediaType = !empty($IGGP['IGGPImg_Type']) ? $IGGP['IGGPImg_Type'] : 'IMAGE'; 
                            $IGGP_MediaURl = !empty($IGGP['IGGPURL_Media']) ? $IGGP['IGGPURL_Media'] : ''; 
                            $IGGP_CAROUSEL_Class="";
                            if($key != 0){
                                $IGGP_CAROUSEL_Class = "IGGP_CAROUSEL_Hidden";
                            }
                            echo "<a href='".esc_url($IGGP_MediaURl)."' $FancyBoxJS data-thumb='".esc_url($IGGP_MediaURl)."' class='tp-soc-img-cls $IGGP_CAROUSEL_Class' >";
                                if($key == 0){
                                    echo $IGGP_Icon;
                                    if($IGGP_MediaType == 'IMAGE'){
                                        echo "<img class='tp-post-thumb' src='".esc_url($IGGP_MediaURl)."' />";
                                    }else if($IGGP_MediaType == 'VIDEO'){
                                        echo "<img class='tp-post-thumb' src='".THEPLUS_ASSETS_URL.'images/tp-placeholder.jpg'."' />";
                                    }
                                }
                            echo "</a>";
                        }
                    }
                }else{
                    if($style == "style-1" || $style == "style-2"){ 
                        echo "<a $PopupLink $PopupTarget $FancyBoxJS class='tp-soc-img-cls'>";
                            echo $IGGP_Icon;
                            echo "<img class='tp-post-thumb' src='".esc_url($ImageURL)."' />";
                        echo '</a>';
                    }else if($style == "style-3" || $style == "style-4"){
                        echo $IGGP_Icon;
                        echo '<a '.$PopupLink . $PopupTarget . $FancyBoxJS.' class="tp-image-link"></a>';
                    }
                }
            }
        }else{
            if( ($Type == 'video' || $Type == 'photo') && (!empty($ImageURL)) ){
                if($style == "style-1" || $style == "style-2"){ ?> 
                    <a <?php echo $PopupLink . $PopupTarget . $FancyBoxJS; ?> class="tp-soc-img-cls" >
                        <?php echo $IGGP_Icon ?>
                        <img class="tp-post-thumb" src="<?php echo esc_url($ImageURL); ?>" />
                    </a>
                <?php }else if($style == "style-3" || $style == "style-4"){
                    echo $IGGP_Icon;
                    echo '<a '.$PopupLink . $PopupTarget . $FancyBoxJS.' class="tp-image-link"></a>';
                }
            } 
        }
    }
?>