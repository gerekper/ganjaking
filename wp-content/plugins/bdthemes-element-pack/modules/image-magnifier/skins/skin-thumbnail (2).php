<?php
namespace ElementPack\Modules\ImageMagnifier\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;

use Elementor\Group_Control_Image_Size;     
use Elementor\Control_Media;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Thumbnail extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-thumbnail';
	}

	public function get_title() {
		return __( 'Thumbnail', 'bdthemes-element-pack' );
	}

	public function render_navigation($settings) {

		?>
		<div class="swiper-button-next"></div>
		<div class="swiper-button-prev"></div>
		<?php
	}

	public function render_header($settings) {
		?>
		<div class="bdt-image-magnifier-skin-thumbnail">

		<?php
	}

	public function render_footer($settings) {
			?>
        
        <?php $this->render_script($settings); ?>

		</div>
		<?php
	}

	public function render_script($settings) {
			?>
			<script>
			jQuery(document).ready(function($){
				var galleryThumbs = new Swiper('.gallery-thumbs', {
					spaceBetween: 10,
					slidesPerView: 4,
					loop: true,
					freeMode: true,
					loopedSlides: 5, //looped slides should be the same
					watchSlidesVisibility: true,
					watchSlidesProgress: true,
					autoHeight: true,
					navigation: {
						nextEl: '.swiper-button-next',
						prevEl: '.swiper-button-prev',
					},
				});
				var galleryTop = new Swiper('.gallery-top', {
					spaceBetween: 10,
					loop:true,
					autoHeight: true,
					loopedSlides: 5, //looped slides should be the same
					thumbs: {
						swiper: galleryThumbs,
					},
				});
			});
			</script>
		<?php
	}

	public function render_image($settings, $item) {
		$image_url = wp_get_attachment_image_src( $item['id'], 'full' );			

		 ?>
	      <div class="swiper-slide">
	      	<img src="<?php echo esc_url($image_url[0]); ?>" alt="<?php echo get_the_title(); ?>">
	      </div>
		  <?php
	}

	public function render_slider_thumbnail($settings) {		

		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'swiper-carousel gallery-top ' . $swiper_class);

		?>
		<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
			<div class="swiper-wrapper">
				<?php foreach ( $settings['image_magnifier_gallery'] as $index => $item ) : ?>
					<?php $this->render_image($settings, $item); ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	public function render_slidenav_thumbnail($settings) {
		
		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'swiper-carousel gallery-thumbs ' . $swiper_class);

		?>
		<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
		    <div class="swiper-wrapper">
				<?php foreach ( $settings['image_magnifier_gallery'] as $index => $item ) : ?>
				      <?php $this->render_image($settings, $item); ?>
				<?php endforeach; ?>
		    </div>
			<?php $this->render_navigation($settings); ?>
	  </div>
	  <?php
	}

	public function render() {
		$settings = $this->parent->get_settings_for_display();
		$id       = $this->parent->get_id();

		if ( empty( $settings['image_magnifier_gallery'] ) ) {
			return;
		}

		$this->render_header($settings);
		$this->render_slider_thumbnail($settings);
		$this->render_slidenav_thumbnail($settings);
		$this->render_footer($settings);
	}
}

