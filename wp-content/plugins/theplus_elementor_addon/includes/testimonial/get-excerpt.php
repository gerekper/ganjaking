<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if($tlContentFrom == 'tlrepeater'){
    $testimonial_author_text = wpautop( $testiAuthor );
}else{
    $testimonial_author_text = get_post_meta(get_the_id(), 'theplus_testimonial_author_text', true);
    $testimonial_author_text = wpautop( $testimonial_author_text );
} 

if(!empty($testimonial_author_text)){ 
    if($tlContentFrom == 'tlrepeater'){ 
        $excerpt = ""; 
        if($descByLimit == 'default'){ 
?>      <div class="entry-content scroll-<?php echo esc_attr($cntscrollOn); ?>"><?php echo $testimonial_author_text; ?></div> <?php 
    }else{
        if( $descByLimit == 'words' ){ 
            $total = explode(' ', $testimonial_author_text);
            $remaining_words = implode(" " , array_slice($total, $descLimit-1)); 
            $words = explode(" ",$testimonial_author_text);
            $limit_words = implode(" ",array_splice($words,0,$descLimit-1));                     
            if (str_word_count($limit_words) >= $descLimit) {
                $excerpt = $limit_words.' <span class="testi-more-text" style = "display: none" >'.wp_kses_post($remaining_words).'</span><a '.$attr.' class="testi-readbtn"> '.esc_attr($redmorTxt).' </a>';
            }else {
                $excerpt = $limit_words;
            } 
        }else if( $descByLimit == 'letters' ){ 
            $ltn = strlen($testimonial_author_text);
            $limit_words = substr($testimonial_author_text,0,$descLimit); 
            $remaining_words = substr($testimonial_author_text, $descLimit, $ltn); 
            if(strlen($testimonial_author_text) > $descLimit){
                $excerpt = $limit_words.'<span class="testi-more-text" style = "display:none" >'.wp_kses_post($remaining_words).'</span><a '.$attr.' class="testi-readbtn"> '.esc_attr($redmorTxt).' </a>';
            }else{
                $excerpt = $limit_words;
            }
        } 
    } 
} 
?>
<div class="entry-content">
    <?php 
        if($tlContentFrom == 'tlrepeater'){
            echo $excerpt;
        }else{
            echo $testimonial_author_text; 
        }
    ?>
</div>
<?php } ?>