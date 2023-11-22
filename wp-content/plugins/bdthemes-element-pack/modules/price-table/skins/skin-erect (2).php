<?php
namespace ElementPack\Modules\PriceTable\Skins;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;

use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Erect extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-erect';
	}

	public function get_title() {
		return __( 'Erect', 'bdthemes-element-pack' );
    }
    
	public function render() {
		$settings = $this->parent->get_settings_for_display();

		?>
		<div class="bdt-price-table skin-erect">

            <div class="bdt-grid-collapse" data-bdt-grid data-bdt-height-match="target: > div > div > *">

                <div class="bdt-width-1-6@m">
                    <div class="bdt-pricing-column">
                        <?php $this->parent->render_header(); ?>
                    </div>
                </div>

                <div class="bdt-width-1-6@m bdt-hidden@m">
                    <div class="bdt-pricing-column">
                        <?php $this->parent->render_price(); ?>
                    </div>
                </div>

                <div class="bdt-width-expand@m">
                <?php $this->parent->render_features_list_column(); ?>
                </div>

                <div class="bdt-width-1-6@m bdt-visible@m">
                    <div class="bdt-pricing-column">
                        <?php $this->parent->render_price(); ?>
                    </div>
                </div>

                <div class="bdt-width-1-6@m">
                    <div class="bdt-pricing-column">
                        <?php $this->parent->render_footer(); ?>
                    </div>
                </div>

            </div>

            <?php $this->parent->render_ribbon(); ?>

        </div>
        


		<?php
	}
}

