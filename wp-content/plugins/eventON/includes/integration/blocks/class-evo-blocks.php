<?php
/**
 * EventON Blocks Integration
 * @version  4.4
 */

class EVO_Blocks{
	private $namespace = 'eventon-blocks';
	private $blockname = 'eventon-main';
	function __construct(){

		if(!function_exists('register_block_type')) return false;
		add_action( 'init', array($this,'block_registering') );
		add_action( 'enqueue_block_editor_assets', array($this,'script_enqueue') );
		add_filter( 'block_categories_all', array($this,'evo_category'), 10, 2);
	}

	function script_enqueue(){
		wp_enqueue_script( 'evo-'. $this->blockname .'-sidebar' );
	}
	function block_registering(){


		wp_register_script(
	        'evo-'. $this->blockname,
	        EVO()->assets_path. 'lib/blocks/evo_blocks.js',
	        array( 'wp-blocks', 'wp-element','wp-plugins', 'wp-edit-post', 'react' )
	    );

	    wp_register_script(
	        'evo-'. $this->blockname .'-sidebar',
	        EVO()->assets_path. 'lib/blocks/evo_sidebar.js',
	        array( 'wp-plugins', 'wp-edit-post', 'react' )
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

		
	    // EventON Classic Template Block
	    register_block_type( 
	    	'eventon/classic-template',
	    	array(
	    		'render_callback'=> array($this,'evo_block_render_callback')
	    	)
	    );
	    
	}

	function evo_block_render_callback($attributes, $content){

		$template_slug = $attributes['template'];
		$classic_file = $template_slug .'.php';

		$template = locate_template( $classic_file );
		if( !$template){
			$template = EVO()->plugin_path() . '/templates/' . $classic_file;		
		}

		ob_start();
		
		load_template( $template );

		$template_content = ob_get_clean();
		

		return $template_content;
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

new EVO_Blocks();