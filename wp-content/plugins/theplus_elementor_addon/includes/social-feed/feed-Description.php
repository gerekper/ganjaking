<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    $url = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';   
    if(!empty($txtLimt)){
        $ltn='';
        if($TextType == 'char'){
            $ltn = strlen($Description);
            $firstdesc = substr($Description, 0, $TextCount);
            $totaldesc = substr($Description, $TextCount, $ltn);
        }else if($TextType = 'word'){
            $words = explode(" ", $Description);
            $ltn = count($words);
            $firstdesc = implode(" ",array_splice($words, 0, $TextCount));
            $totaldesc = implode(" ",array_splice($words, 0));
        }
        // First text
            $Mantion = preg_replace('/(^|\s)@([\w.]+)/', '$1<span class="tp-mantion" >@$2</span>', $firstdesc);
            $HashTag = preg_replace("/#(\\S+)/", '<span class="tp-hashtag" >$0 </span>', $Mantion);
            $firstdesc = preg_replace($url, '<a href="$0" target="_blank" rel="noopener noreferrer" class="tp-feedurl" title="" >$0</a>', $HashTag);
        // Totle text
            $Mantion = preg_replace('/(^|\s)@([\w.]+)/', '$1<span class="tp-mantion" >@$2</span>', $totaldesc);
            $HashTag = preg_replace("/#(\\S+)/", '<span class="tp-hashtag" >$0 </span>', $Mantion);
            $totaldesc = preg_replace($url, '<a href="$0" target="_blank" rel="noopener noreferrer" class="tp-feedurl" title="" >$0</a>', $HashTag);
        ?> 
            <div class="tp-message">
                <div class="showtext"><?php echo wp_kses_post($firstdesc); ?>
                    <?php if(($TextType == 'char' && ($ltn > strlen($firstdesc))) || ($TextType == 'word' && ($ltn > count(explode(" ", $firstdesc))) )){ ?>
                        <span class="sf-dots"><?php echo esc_attr($TextDots); ?></span>
                        <div class="moreText" ><?php echo wp_kses_post($totaldesc); ?></div>
                        <a class="readbtn"><?php echo esc_attr($TextMore); ?> </a>
                    <?php } ?>
                </div>
            </div>
        <?php
    }else{
        $feedurl = preg_replace($url, '<a href="$0" target="_blank" rel="noopener noreferrer" class="tp-feedurl" title="" >$0</a>', $Description);
        $HashTag = preg_replace("/#(\\S+)/", '<span class="tp-hashtag" >$0 </span>', $feedurl);
        $Mantion = preg_replace('/(^|\s)@([\w.]+)/', '$1<span class="tp-mantion" >@$2</span>', $HashTag);
        
        ?> <div class="tp-message"><?php echo wp_kses_post($Mantion); ?></div> <?php
    }
?>