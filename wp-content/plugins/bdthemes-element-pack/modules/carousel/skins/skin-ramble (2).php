<?php
namespace ElementPack\Modules\Carousel\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Ramble extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-ramble';
	}

	public function get_title() {
		return __( 'Ramble', 'bdthemes-element-pack' );
    }
    
	public function render() {
		$settings = $this->parent->get_settings_for_display();

	    $posts_per_page = $settings['posts_per_page'];
	        
		$this->parent->query_posts($posts_per_page);
		
		$wp_query = $this->parent->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		$this->parent->get_posts_tags();

		$this->parent->render_header("ramble");

		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();

			$this->parent->render_post();
		}

		$this->parent->render_footer();

		wp_reset_postdata();
	}

}