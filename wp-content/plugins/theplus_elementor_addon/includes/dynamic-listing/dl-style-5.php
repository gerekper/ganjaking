<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$link = get_field('page_url', get_the_ID());

?>
	<a class="tp-home-custom-listing" href="<?php echo esc_url( $link ); ?>">
	<div class="first-wrap">
		<?php			
			
			if( get_field('image', get_the_ID()) ){ ?>
				<img src="<?php the_field('image'); ?>" />
			<?php } 

			if( get_field('icon', get_the_ID()) ){ ?>
				<i class="im iconsmind-<?php the_field('icon'); ?>"></i>
			<?php } ?>
			
			<div class="tp-home-title"><?php echo get_the_title(); ?></div>
	</div>
	<div class="second-wrap"><?php $freepro = get_field_object( 'freepro' ); 
			$freepro_value = !empty($freepro['value']) ? $freepro['value'] : '';
			$standard = get_field_object( 'standard' );
			$standard_value = !empty($standard['value']) ? $standard['value'] : '';
			
			if((!empty($standard_value) && $standard_value!='None') || !empty($freepro_value)){ ?>
				<div class="freepro-standard-value-m"> <?php
			}
			if(!empty($freepro_value)){ ?>
				<span class="freepro freepro-<?php echo esc_attr($freepro_value); ?>"><?php echo esc_html($freepro_value); ?></span>
			<?php 
			}
						
			if(!empty($standard_value) && $standard_value!='None'){ ?>
				<span class="standard-value standard-value-<?php echo esc_attr($standard_value); ?>"><?php echo esc_html($standard_value); ?></span>	
			<?php 
			}
			if((!empty($standard_value) && $standard_value!='None') || !empty($freepro_value)){ ?>
				</div> <?php
			} ?> <span class="tp-home-view-demo">View Demo <i class="iconsmind-Right service-icon  icon-squre"></i></span></div>
	</a>
