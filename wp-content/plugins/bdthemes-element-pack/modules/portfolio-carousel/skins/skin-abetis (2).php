<?php
namespace ElementPack\Modules\PortfolioCarousel\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Abetis extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-abetis';
	}

	public function get_title() {
		return __( 'Abetis', 'bdthemes-element-pack' );
	}

	public function render() {
		$settings = $this->parent->get_settings_for_display();

		// TODO need to delete after v6.5
        if (isset($settings['limit']) and $settings['posts_per_page'] == 6) {
            $limit = $settings['limit'];
        } else {
            $limit = $settings['posts_per_page'];
        }
		
		$this->parent->query_posts($limit);

		$wp_query = $this->parent->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		$this->parent->render_header('abetis');

		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();

			$this->parent->render_post();
		}

		$this->parent->render_footer();
		
		wp_reset_postdata();

	}
}

