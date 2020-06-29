<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_quick_html extends WYSIJA_object {

    function __construct(){
        parent::__construct();
    }

    /**
     * quickly renders a row of 3 sales argument with a picture a title and description with an eventual link
     * pretty handy for the update page/welcome page and premium page
     * @param type $arguments the array includes 3 array with this format (key,title,desc,link,img)
     * @param type $cta_links the link in the descriptive text will be rendered as a button
     * @return string
     */
    function three_arguments($arguments, $cta_links = false){
        $html = $class_link = '';
        $count = 1;
        foreach ($arguments as $sale_argument) {
            if (isset($sale_argument['link'])) {
                if($cta_links)  $class_link = 'class="argument-cta"';
                $sale_argument['desc'] = str_replace(array('[link]', '[/link]'), array('<a '.$class_link.' href="' . $sale_argument['link'] . '" target="_blank">', '</a>'), $sale_argument['desc']);
            }
            if($count==3) $sale_argument['key'] .= ' last-feature';
            $html .= '<div class="col-'.$count.' '.$sale_argument['key'].'">';

            if(isset($sale_argument['img']))    $html .= '<img src="'.$sale_argument['img'].'">';
            $html .= '<h3>'.$sale_argument['title'].'</h3>
                            <p>'.$sale_argument['desc'].'</p>
                    </div>';
            $count++;
        }

        return $html;
    }
}
