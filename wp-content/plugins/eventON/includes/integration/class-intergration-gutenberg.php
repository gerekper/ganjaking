<?php
/**
 * Gutenberg Integration
 * @version  2.6.14
 */

class EVO_Gutenberg{
	private $namespace = 'eventon-blocks';
	private $blockname = 'eventon-main';
	function __construct(){
		if(!function_exists('register_block_type')) return false;
		add_action( 'init', array($this,'gutenberg_boilerplate_block') );
		add_filter( 'block_categories_all', array($this,'evo_category'), 10, 2);
	}
	function gutenberg_boilerplate_block(){

		if ( function_exists( 'register_block_type' ) && is_admin() ) {
			wp_register_script(
		        'evo-'. $this->blockname,
		        EVO()->assets_path. 'lib/gutenberg/evo_gutenberg.js',
		        array( 'wp-blocks', 'wp-element' )
		    );

		    //array( 'wp-blocks', 'wp-element','wp-editor' )

		    wp_localize_script(
		    	'evo-'. $this->blockname,
		    	'evoblock',
		    	array(
		    		'evoblock_prev' => plugins_url(EVENTON_BASE).'/assets/images/placeholder.png',
		    	)
		    );

			register_block_type( 
				$this->namespace .'/'. $this->blockname, 
				array(
		       		'editor_script' => 'evo-'.  $this->blockname,
		       		'prev'=> array(
		       			'type'=>'boolean','default'=> false,
		       		)
		    	) 
		    );
	    }	   
	}
	function evo_category( $categories, $post ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug' => 'eventon',
					'title' => __( 'EventON', 'eventon' ),
				),
			)
		);
	}
}

new EVO_Gutenberg();