<?php

/**
 * Description of A2W_Language
 *
 * @author Mikhail
 */
if (!class_exists('A2W_Language')) {

    class A2W_Language {
        private $languages = array("en" => "English", "ar" => "Arabic", "de" => "German", 
                                   "es" => "Spanish", "fr" => "French", "it" => "Italian",
                                   "pl" => "Polish", "ja" => "Japanese", "ko" => "Korean", 
                                   "nl" => "Notherlandish (Dutch)", "pt" => "Portuguese (Brasil)", "ru" => "Russian", 
                                   "th" => "Thai", "id" => "Indonesian", "he" => "Hebrew", 
                                   "tr" => "Turkish", "vi" => "Vietnamese");
        
        public function get_languages() {
 
            return $this->languages;
        }
        
        public function get_language($code) {
            $languages = $this->get_languages();
            foreach($languages as $c => $text){
                if($code === $c){
                    return $text;
                    break;
                }
            }
            return false;
        }

    }

}