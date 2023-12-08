<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$postid = get_the_ID();

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="team-list-content">
		<div class="post-content-image">
			<?php if(empty($disable_link) && $disable_link!='yes'){ ?>
				<a rel="<?php echo esc_attr($member_urlNofollow); ?>" href="<?php echo esc_url($member_url); ?>" target="<?php echo esc_attr($member_urlBlank);?>">
			<?php }

				include THEPLUS_INCLUDES_URL . 'team-member/format-image.php';

				if(empty($disable_link) && $disable_link!='yes'){ ?> </a>

			<?php } ?>
		</div>		
		<div class="post-content-bottom">			
			<?php 
				include THEPLUS_INCLUDES_URL . 'team-member/post-meta-title.php';
				
				if( !empty($designation) && !empty($display_designation) && $display_designation == 'yes' ){
					echo $designation;
				} 
			?>
		</div>
	</div>
</article>