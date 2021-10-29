<?php if( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
	<div class="icon32" id="icon-index"><br/></div>
	<h2>HMWP IDS Log <?php echo $search_title; ?></h2>

    <?php $allowed = array(
        'a' => array(
            'href' => array(),
            'title' => array()
        ),
        'br' => array(),
        'em' => array(),
        'strong' => array()
    );
    ?>
	<?php if ( $message ) : ?>

	<div id="message" class="updated"><p><?php echo $message; ?></p></div>

	<?php endif; ?>

	<div class="filter">
		<form method="get" action="" id="list-filter">
			<ul class="subsubsub">
				<li>&nbsp;</li>
			</ul>
		</form>
	</div>

	<form method="get" action="admin.php" class="search-form">
		<input type="hidden" value="<?php echo $page;?>" name="page"/>
		<p class="search-box">
			<label for="s" class="screen-reader-text"><?php _e( 'Search Intrusions', 'mute-screamer' ); ?></label>
			<input type="text" value="<?php echo esc_attr( $intrusions_search ); ?>" name="intrusions_search" id="hmwp_ms-intrusions-search-input"/>
			<input type="submit" class="button" value="<?php _e( 'Search Intrusions', 'mute-screamer' ); ?>"/>
		</p>
	</form>

	<?php if($intrusions) : 
        wp_enqueue_script('jquery');
        ?>        
        <script>
            jQuery(document).ready(function(){                
                jQuery('.filter-ips-data').click(function(e){
                    e.preventDefault();
                    jQuery('#hmwp_ms-intrusions-search-input').val(jQuery(this).data('ip'));
                    jQuery('.search-form').submit();
                });
            });
        </script>
	<form method="get" action="" id="posts-filter">
		<div class="tablenav">
			<div class="alignleft actions">
				<select name="action">
					<option selected="selected" value=""><?php _e( 'Bulk Actions', 'mute-screamer' ); ?></option>
					<option value="bulk_delete"><?php _e( 'Delete', 'mute-screamer' ); ?></option>
                    <option value="bulk_ban_ip"><?php _e( 'Ban IPs', 'mute-screamer' ); ?></option>
                    <option value="bulk_exclude"><?php _e( 'Exclude', 'mute-screamer' ); ?></option>
				</select>
				<input type="submit" class="button-secondary action" id="doaction" name="doaction" value="Apply"/>
                <a href="?page=hmwp_ms_intrusions&action=delete_all" onclick="return confirm('Are you sure to delete all logs?')" class="button" style="display:inline-block"><?php _e(' Delete All', 'mute-screamer' ); ?></a>
				<?php wp_nonce_field( 'hmwp_ms_action_intrusions_bulk' ); ?>

				<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />
			</div>

			<?php echo $pagination; ?>

			<br class="clear"/>
		</div>

		<table cellspacing="0" class="widefat fixed">
			<thead>
				<tr class="thead">
					<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"/></th>
					<?php foreach($columns as $key => $val) : ?>
						<th style="" class="manage-column column-<?php echo $key;?>" id="<?php echo $key;?>" scope="col"><?php echo $val; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>

			<tfoot>
				<tr class="thead">
					<th style="" class="manage-column column-cb check-column" id="cb_2" scope="col"></th>
					<?php foreach($columns as $key => $val) : ?>
						<th style="" class="manage-column column-<?php echo $key;?>" id="<?php echo $key;?>_2" scope="col"><?php echo $val; ?></th>
					<?php endforeach; ?>
				</tr>
			</tfoot>

			<tbody class="list:intrusion intrusion-list" id="hmwp_ms_intrusions">

					<?php foreach($intrusions as $intrusion) : ?>

						<?php $style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"'; ?>

						<tr<?php echo $style; ?> id="intrusion-<?php echo $intrusion->id; ?>">
							<th class="check-column" scope="row">
								<input type="checkbox" value="<?php echo $intrusion->id; ?>" class="<?php echo ''; ?>" id="intrusion_<?php echo $intrusion->id; ?>" name="intrusions[]"/>
							</th>
							<?php foreach($columns as $key => $val) : ?>
								<td class="<?php echo $key; ?> column-<?php echo $key; ?>">
									<?php switch ($key) :
										case 'name':
											$exclude_link = wp_nonce_url( admin_url( 'admin.php?page=hmwp_ms_intrusions&action=exclude&intrusion=' . $intrusion->id ), 'hmwp_ms_action_exclude_intrusion' );

                                            $ban_link = wp_nonce_url( admin_url( 'admin.php?page=hmwp_ms_intrusions&action=ban&ban_ip=' .  $intrusion->ip ), 'hmwp_ms_action_ban_ip' );

											$delete_link  = wp_nonce_url( admin_url( 'admin.php?page=hmwp_ms_intrusions&action=delete&intrusion=' . $intrusion->id ), 'hmwp_ms_action_delete_intrusion' );
?>
											<strong><a href="<?php echo $exclude_link; ?>" title="<?php echo esc_attr( sprintf( __( 'Exclude &#8220;%s&#8221;' ), sanitize_title($intrusion->name) ) ); ?>"><?php echo esc_html( sanitize_text_field($intrusion->name) ); ?></a></strong>
											<div class="row-actions">

												<span class="exclude"><a title="<?php echo esc_attr( __( 'Add this item to the exception fields list', 'mute-screamer' ) ); ?>" href="<?php echo $exclude_link; ?>"><?php _e( 'Exclude', 'mute-screamer' ); ?></a> | </span>

                                                <span class="ip_ban"><a title="<?php echo esc_attr( __( 'Ban this IP ', 'mute-screamer' ) ); ?>" href="<?php echo $ban_link; ?>" onclick="return confirm('Are you sure to ban this IP address?')" style="color:#ff7900"><?php _e( 'Ban IP', 'mute-screamer' ); ?></a> | </span>

                                                <span class="delete"><a title="<?php echo esc_attr( __( 'Delete this item', 'mute-screamer' ) ); ?>" class="delete submitdelete" href="<?php echo $delete_link; ?>"><?php _e( 'Delete', 'mute-screamer' ); ?></a></span>
											</div>
<?php
											break;

										case 'value':
                                            $v = wp_kses(esc_attr( $intrusion->value ), $allowed);
                                            echo "<div class='intrusion_content'>";
                                            if (strlen($v)>30) {
											    echo "<div class='int_short'>".substr($v,0 ,30) ."... <a class='intrusion_more'  href='#'>more</a></div>";

                                                echo "<div class='int_long' style='display:none'>".$v." <a class='intrusion_less' href='#'>less</a></div>";
                                            }else{
                                                echo $v;
                                            }
                                            echo "</div>";
											break;

										case 'page':
											echo "<div class='intrusion_content'>".wp_kses(esc_attr( $intrusion->page ), $allowed).'</div>';
											break;

										case 'tags':
											echo esc_html( $intrusion->tags );
											break;

										case 'ip':
											echo '<a target="_blank" href="http://whatismyipaddress.com/ip/'.sanitize_text_field( $intrusion->ip ). '" >'.sanitize_text_field( $intrusion->ip ).'</a> <br/> '; //hassan
                                            if (!$intrusion->user_id)
                                                echo 'Guest';
                                            else {
                                                $u = get_userdata( $intrusion->user_id );
                                                echo $u->user_login;
                                            }
                                            echo '<div class="row-actions">';
                                            echo '<span class="delete"><a href="#" class="filter-ips-data" data-ip="'. sanitize_text_field( $intrusion->ip ) .'">'. __('Filter','hide_my_wp') .'</a></span>';
                                            echo '</div>';

											if ($intrusion->origin && strpos($intrusion->origin,'.')===false)
                                                	echo '<br/><a target="_blank" href="http://whatismyipaddress.com/ip/'.sanitize_text_field( $intrusion->ip ). '" >'.$intrusion->origin.' <img src="'.HMW_URL.'/img/flags/'. strtolower($intrusion->origin).'.gif" title="Country Code: '.$intrusion->origin.'"></a> ';


											break;


										case 'impact':
											echo esc_html( $intrusion->impact )  .' / '.$intrusion->total_impact ;
											break;

										case 'date':
											echo date( "{$date_format} {$time_format}", strtotime( $intrusion->created ) + $time_offset );
											break;

										default:
											echo apply_filters( 'manage_hmwp_ms_intrusions_custom_column', '', $key, $intrusion->id );
									?>
									<?php endswitch;?>
								</td>
							<?php endforeach;?>
						</tr>

					<?php endforeach; ?>

			</tbody>
		</table>

		<div class="tablenav">
			<?php echo $pagination; ?>

			<div class="alignleft actions">
				<select name="action2">
					<option selected="selected" value=""><?php _e( 'Bulk Actions', 'mute-screamer' ); ?></option>
					<option value="bulk_delete"><?php _e( 'Delete', 'mute-screamer' ); ?></option>
					<option value="bulk_ban_ip"><?php _e( 'Ban IPs', 'mute-screamer' ); ?></option>
					<option value="bulk_exclude"><?php _e( 'Exclude', 'mute-screamer' ); ?></option>
				</select>
				<input type="submit" class="button-secondary action" id="doaction2" name="doaction2" value="Apply"/><a href="?page=hmwp_ms_intrusions&action=delete_all" onclick="return confirm('Are you sure to delete all logs?')" class="button" style="display:inline-block"><?php _e(' Delete All', 'mute-screamer' ); ?></a>
                <p><div style="font-style:italic">* Value is sanitized for your safety</div></p>
			</div>
			<br class="clear"/>
		</div>
	</form>

	<?php elseif( ! $search_title ) : ?>

	<p><?php _e( 'How good is that, no intrusions.', 'mute-screamer' ); ?></p>

	<?php else : ?>

	<p><?php _e( 'No intrusions found.', 'mute-screamer' ); ?></p>

	<?php endif; ?>

</div>
<script type='text/javascript'>
jQuery(function(){
	jQuery('.submitdelete').click(function() {
		return confirm(<?php _e( '"You are about to permanently delete this item.\n  \'Cancel\' to stop, \'OK\' to delete."', 'mute-screamer' ); ?>);
	});

	
	jQuery('.intrusion_more').click(function(){
		jQuery(this).parent().parent().find(".int_long").show();
		jQuery(this).parent().parent().find(".int_short").hide();
        return false;

	});
	
	jQuery('.intrusion_less').click(function(){
		jQuery(this).parent().parent().find(".int_long").hide();
		jQuery(this).parent().parent().find(".int_short").show();
        return false;
	});
});
</script>