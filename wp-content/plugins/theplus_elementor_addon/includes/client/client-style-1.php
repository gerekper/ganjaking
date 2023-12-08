<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
if($clientContentFrom == 'clrepeater'){
	$client_url = $clientlink;
}

if($clientContentFrom != 'clrepeater'){ 
	$postid = get_the_ID();
	$client_url = get_post_meta(get_the_id(), 'theplus_clients_url', true);
?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php } ?>
		<div class="client-post-content">
			<div class="client-content-logo">
			<?php
				if(!empty($disable_link) && $disable_link=='yes'){
					echo '<div>';
				}else{ ?>
					<a href="<?php echo esc_url($client_url); ?>" target="_blank" rel="noopener noreferrer"> <?php
				}
					include THEPLUS_INCLUDES_URL. 'client/format-image.php';
				
				if(!empty($disable_link) && $disable_link=='yes'){
					echo '</div>';
				}else{ 
					echo '</a>';
				} ?>
			</div>
			<?php if(!empty($display_post_title) && $display_post_title=='yes'){ ?>
				<div class="post-content-bottom">
					<?php include THEPLUS_INCLUDES_URL. 'client/post-meta-title.php'; ?>
				</div>
			<?php } ?>
		</div>
<?php if($clientContentFrom != 'clrepeater'){ ?>
	</article>
<?php } ?>
