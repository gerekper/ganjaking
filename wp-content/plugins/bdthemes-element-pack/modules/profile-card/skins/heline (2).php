<?php
namespace ElementPack\Modules\ProfileCard\Skins;

use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;

use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Heline extends Elementor_Skin_Base {

	public function get_id() {
		return 'heline';
	}

	public function get_title() {
		return esc_html__( 'Heline', 'bdthemes-element-pack' );
    }

    public function render_social_icon() {
        $settings = $this->parent->get_settings_for_display();

        ?>

        <?php if ($settings['show_social_icon']) : ?>

        <div class="bdt-width-expand@s bdt-width-1-1 bdt-profile-card-share-wrapper">
            <div class="bdt-profile-card-share-link bdt-margin-medium-top">
                <?php 
                foreach ( $settings['social_link_list'] as $link ) :
                    $tooltip = ( 'yes' == $settings['social_icon_tooltip'] ) ? ' title="'.esc_attr( $link['social_link_title'] ).'" data-bdt-tooltip' : ''; ?>
                    
                    <a href="<?php echo esc_url( $link['social_link'] ); ?>" class="elementor-repeater-item-<?php echo esc_attr($link['_id']); ?>" target="_blank"<?php echo $tooltip; ?>>
                        <?php Icons_Manager::render_icon( $link['social_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] ); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php endif; 
    }
    
    public function render_instagram_card() {
		$settings = $this->parent->get_settings_for_display();
        $instagram = element_pack_instagram_card();


		?>

        <div class="bdt-profile-card skin-heline">
            <div class="bdt-profile-card-item bdt-flex bdt-flex-center">

                <div class="bdt-profile-card-header">
                    
                    <?php if ($settings['show_user_menu']) : ?>
                    <div class="bdt-profile-card-settings">
                        <a href="javascript:void(0);" ><i class="ep-icon-ellipsis-h" aria-hidden="true"></i></a>
                    </div>
                    
                    <?php $this->parent->user_dropdown_menu(); ?>

                    <?php endif; ?>

                    <?php if ($settings['show_image']) : ?>
                    <div class="bdt-profile-card-image">
                        <img src="<?php echo esc_url( $instagram['profile_picture'] ); ?>" alt="<?php echo $instagram['full_name']; ?>" />
                    </div>
                    <?php endif; ?>
                    
                </div>

                <div class="bdt-profile-card-inner">

                	<?php if ($settings['show_badge']) : ?>
                    <div class="bdt-profile-card-pro bdt-text-right">
                        <span><?php echo $settings['profile_badge_text']; ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="bdt-profile-card-name-info">

						<?php if ($settings['show_name']) : ?>
                            <h3 class="bdt-name">
                                <a class="" href="https://instagram.com/<?php echo esc_html($instagram['username']); ?>"><?php echo wp_kses_post($instagram['full_name']); ?></a>
                            </h3>
                        <?php endif; ?>

						<?php if ($settings['show_username']) : ?>
                            <span class="bdt-username"><?php echo $instagram['username']; ?></span>
                        <?php endif; ?>

                    </div>

					<?php if ($settings['show_text']) : ?>
                    <div class="bdt-profile-card-bio">
                        <?php echo wp_kses_post($instagram['bio']); ?>
                    </div>
                    <?php endif; ?>

					<?php if ($settings['show_status']) : ?>
                    <div class="bdt-profile-card-status">
                        <ul>
                            <li>
                                <span class="bdt-profile-stat">
                                    <?php echo esc_attr( $instagram['counts']['media'] ); ?>
								</span>
                                <span class="bdt-profile-label">
									<?php echo esc_html($settings['instagram_posts']); ?>
								</span>
                            </li>
                            <li>
								<span class="bdt-profile-stat">
									<?php echo esc_attr( $instagram['counts']['follows'] ); ?>
								</span>
                                <span class="bdt-profile-label">
									<?php echo esc_html($settings['instagram_followers']); ?>
								</span>
                            </li>
                            <li>
                                <span class="bdt-profile-stat">
									<?php echo esc_attr( $instagram['counts']['followed_by'] ); ?>
								</span>
                                <span class="bdt-profile-label">
									<?php echo esc_html($settings['instagram_following']); ?>
								</span>
                            </li>
                        </ul>
                    </div>
                    <?php endif; ?>
					
					<div class="bdt-grid">
						<?php if ($settings['show_button']) : ?>
	                    <div class="bdt-width-auto@s bdt-width-1-1 bdt-profile-card-button bdt-margin-medium-top">
                            <a class="bdt-button bdt-button-secondary" href="https://instagram.com/<?php echo esc_html($instagram['username']); ?>"><?php echo $settings['instagram_button_text']; ?></a>
	                    </div>
	                    <?php endif; ?>

						<?php $this->render_social_icon(); ?>

                	</div>
                </div>

            </div>
        </div>

		<?php
    }

	public function render_blog_card() {
		$settings = $this->parent->get_settings_for_display();

        ?>
        
        <div class="bdt-profile-card skin-heline">
            <div class="bdt-profile-card-item bdt-flex bdt-flex-center">

                <div class="bdt-profile-card-header">
                    
                    <?php if ($settings['show_user_menu']) : ?>
                    <div class="bdt-profile-card-settings">
                        <a href="javascript:void(0);" ><i class="ep-icon-ellipsis-h" aria-hidden="true"></i></a>
                    </div>
                    
                    <?php $this->parent->user_dropdown_menu(); ?>

                    <?php endif; ?>

                    <?php if ($settings['show_image']) : ?>
                    <div class="bdt-profile-card-image">
                        <img src="<?php echo esc_url( get_avatar_url( $settings['blog_user_id'], [ 'size' => 128 ] ) ); ?>" alt="<?php echo get_the_author_meta('first_name', $settings['blog_user_id']); ?>" />
                    </div>
                    <?php endif; ?>
                    
                </div>

                <div class="bdt-profile-card-inner">

                	<?php if ($settings['show_badge']) : ?>
                    <div class="bdt-profile-card-pro bdt-text-right">
                        <span><?php echo $settings['profile_badge_text']; ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="bdt-profile-card-name-info">

						<?php if ($settings['show_name']) : ?>
                            <h3 class="bdt-name"><?php echo get_the_author_meta('first_name', $settings['blog_user_id']); ?> <?php echo get_the_author_meta('last_name', $settings['blog_user_id']); ?></h3>
                        <?php endif; ?>

						<?php if ($settings['show_username']) : ?>
                            <span class="bdt-username"><?php echo get_the_author_meta('user_nicename', $settings['blog_user_id']); ?></span>
                        <?php endif; ?>

                    </div>

					<?php if ($settings['show_text']) : ?>
                    <div class="bdt-profile-card-bio">
                        <?php echo get_the_author_meta('description', $settings['blog_user_id']); ?>
                    </div>
                    <?php endif; ?>

					<?php if ($settings['show_status']) : ?>
                    <div class="bdt-profile-card-status">
                        <ul>
                            <li>
                                <span class="bdt-profile-stat">
                                    <?php echo count_user_posts( $settings['blog_user_id'] ); ?>
                                </span>
                                <span class="bdt-profile-label">
                                    <?php echo esc_html($settings['blog_posts']); ?>
                                </span>
                            </li>
                            <li>
                                <span class="bdt-profile-stat">
                                    <?php
                                    $comments_count = wp_count_comments();
                                    echo $comments_count->approved;
                                    ?>
                                </span>
                                <span class="bdt-profile-label">
                                    <?php echo esc_html($settings['blog_post_comments']); ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                    <?php endif; ?>
					
					<div class="bdt-grid">
						<?php if ($settings['show_button']) : ?>
	                    <div class="bdt-width-auto@s bdt-width-1-1 bdt-profile-card-button bdt-margin-medium-top">
                            <a class="bdt-button bdt-button-secondary" href="<?php echo get_author_posts_url($settings['blog_user_id']); ?>"><?php echo $settings['blog_button_text']; ?></a>
	                    </div>
	                    <?php endif; ?>

						<?php $this->render_social_icon(); ?>

                	</div>
                </div>

            </div>
        </div>

		<?php
	}

	public function render_custom_card() {
		$settings = $this->parent->get_settings_for_display();
		
		?>

        <div class="bdt-profile-card skin-heline">
            <div class="bdt-profile-card-item bdt-flex bdt-flex-center">

                <div class="bdt-profile-card-header">
                    
                    <?php if ($settings['show_user_menu']) : ?>
                    <div class="bdt-profile-card-settings">
                        <a href="javascript:void(0);" ><i class="ep-icon-ellipsis-h" aria-hidden="true"></i></a>
                    </div>
                    
                    <?php $this->parent->user_dropdown_menu(); ?>

                    <?php endif; ?>

                    <?php if ($settings['show_image']) : ?>
                    <div class="bdt-profile-card-image">
                        <?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'profile_image' ); ?>
                    </div>
                    <?php endif; ?>
                    
                </div>

                <div class="bdt-profile-card-inner">

                	<?php if ($settings['show_badge']) : ?>
                    <div class="bdt-profile-card-pro bdt-text-right">
                        <span><?php echo $settings['profile_badge_text']; ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="bdt-profile-card-name-info">

						<?php if ($settings['show_name']) : ?>
                        <h3 class="bdt-name"><?php echo $settings['profile_name']; ?></h3>
                        <?php endif; ?>

						<?php if ($settings['show_username']) : ?>
                        <span class="bdt-username"><?php echo $settings['profile_username']; ?></span>
                        <?php endif; ?>

                    </div>

					<?php if ($settings['show_text']) : ?>
                    <div class="bdt-profile-card-bio">
                        <?php echo $settings['profile_content']; ?>
                    </div>
                    <?php endif; ?>

					<?php if ($settings['show_status']) : ?>
                    <div class="bdt-profile-card-status">
                        <ul>
                            <li>
                                <span class="bdt-profile-stat"><?php echo $settings['profile_posts_number']; ?></span>
                                <span class="bdt-profile-label"><?php echo $settings['profile_posts']; ?></span>
                            </li>
                            <li>
                                <span class="bdt-profile-stat"><?php echo $settings['profile_followers_number']; ?></span>
                                <span class="bdt-profile-label"><?php echo $settings['profile_followers']; ?></span>
                            </li>
                            <li>
                                <span class="bdt-profile-stat"><?php echo $settings['profile_following_number']; ?></span>
                                <span class="bdt-profile-label"><?php echo $settings['profile_following']; ?></span>
                            </li>
                        </ul>
                    </div>
                    <?php endif; ?>
					
					<div class="bdt-grid">
						<?php if ($settings['show_button']) : ?>
	                    <div class="bdt-width-auto@s bdt-width-1-1 bdt-profile-card-button bdt-margin-medium-top">
	                        <a class="bdt-button bdt-button-secondary" href="<?php echo $settings['follow_link']['url'] ?>"><?php echo $settings['profile_button_text']; ?></a>
	                    </div>
	                    <?php endif; ?>

						<?php $this->render_social_icon(); ?>

                	</div>
                </div>

            </div>
        </div>

		<?php 
	}

	public function render() {
	    $settings = $this->parent->get_settings_for_display();

	    if ('blog' == $settings['profile']) {
		    $this->render_blog_card();
	   	} elseif ( 'instagram' == $settings['profile']) {
		    $this->render_instagram_card();
	   	} else {
		    $this->render_custom_card();
        }
	}
}

